<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\PromptModel;
use App\Libraries\GeminiService;
use App\Libraries\MemoryService;
use CodeIgniter\HTTP\RedirectResponse;
use App\Entities\User; // Import the User entity

class GeminiController extends BaseController
{
    protected UserModel $userModel;
    protected GeminiService $geminiService;
    protected PromptModel $promptModel;

    /**
     * Constructor.
     * Initializes the UserModel and GeminiService via the services container.
     */
    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->geminiService = service('geminiService');
        $this->promptModel = new PromptModel();
    }

    public function index(): string
    {
        $userId = (int) session()->get('userId');
        $prompts = $this->promptModel->where('user_id', $userId)->findAll();

        $data = [
            'title'   => 'Gemini AI Query',
            'result'  => session()->getFlashdata('result'),
            'error'   => session()->getFlashdata('error'),
            'prompts' => $prompts,
        ];
        return view('gemini/query_form', $data);
    }

    public function generate(): RedirectResponse
    {
        $userId = (int) session()->get('userId');
        if ($userId <= 0) {
            return redirect()->back()->withInput()->with('error', ['User not logged in or invalid user ID.']);
        }

        $inputText = $this->request->getPost('prompt');
        $isAssistantMode = $this->request->getPost('assistant_mode') === '1';
        $finalPrompt = $inputText;
        $usedInteractionIds = [];
        $memoryService = null;

        if ($isAssistantMode && !empty(trim($inputText))) {
            $memoryService = service('memory', $userId);
            $recalled = $memoryService->getRelevantContext($inputText);
            $context = $recalled['context'];
            $usedInteractionIds = $recalled['used_interaction_ids'];

            $systemPrompt = $memoryService->getTimeAwareSystemPrompt();
            $currentTime = "CURRENT_TIME: " . date('Y-m-d H:i:s T');
            $finalPrompt = "{$systemPrompt}\n\n---RECALLED CONTEXT---\n{$context}---END CONTEXT---\n\n{$currentTime}\n\nUser query: \"{$inputText}\"";
        }

        // Handle file uploads
        $uploadedFiles = $this->request->getFileMultiple('media') ?: [];
        $supportedMimeTypes = [
            'image/png',
            'image/jpeg',
            'image/webp',
            'audio/mpeg',
            'audio/mp3',
            'audio/wav',
            'video/mov',
            'video/mpeg',
            'video/mp4',
            'video/mpg',
            'video/avi',
            'video/wmv',
            'video/mpegps',
            'video/flv',
            'application/pdf',
            'text/plain'
        ];

        // Individual file size limit
        $maxFileSize = 10 * 1024 * 1024; 
        // Define the maximum total file size allowed (e.g., 20MB)
        $totalMaxFileSize = 50 * 1024 * 1024;
        $totalFileSize = 0; // Initialize total file size
        $parts = [];

        if ($finalPrompt) {
            $parts[] = ['text' => $finalPrompt];
        }

        foreach ($uploadedFiles as $file) {
            if ($file->isValid()) {
                $mimeType = $file->getMimeType();
                if (!in_array($mimeType, $supportedMimeTypes)) {
                    return redirect()->back()->withInput()->with('error', "Unsupported file type: {$mimeType}.");
                }
                // Check individual file size
                if ($file->getSize() > $maxFileSize) {
                    return redirect()->back()->withInput()->with('error', 'File size exceeds 10 MB limit.');
                }
                // Add the size of the current file to the total
                $totalFileSize += $file->getSize();
                $base64Content = base64_encode(file_get_contents($file->getTempName()));
                $parts[] = ['inlineData' => ['mimeType' => $mimeType, 'data' => $base64Content]];
            }
        }

        // Check if the total file size exceeds the limit
        if ($totalFileSize > $totalMaxFileSize) {
            return redirect()->back()->withInput()->with('error', 'Total file size exceeds the 50 MB limit.');
        }

        if (empty($parts)) {
            return redirect()->back()->withInput()->with('error', ['Prompt or supported media is required.']);
        }

        $apiResponse = $this->geminiService->generateContent($parts);

        if (isset($apiResponse['error'])) {
            return redirect()->back()->withInput()->with('error', ['error' => $apiResponse['error']]);
        }

        // --- Token-based Pricing Logic ---
        $deductionAmount = 10.00; // Default fallback cost in KSH
        $costMessage = "A default charge of KSH " . number_format($deductionAmount, 2) . " has been applied.";
        $usdToKshRate = 129; // Define rate directly

        if (isset($apiResponse['usage']['totalTokenCount'])) {
            $inputTokens = (int) ($apiResponse['usage']['promptTokenCount'] ?? 0);
            $outputTokens = (int) ($apiResponse['usage']['candidatesTokenCount'] ?? 0);

            // Pricing for Gemini 2.5 Pro
            $inputPricePerMillion = 5.50;  // Standard price
            $outputPricePerMillion = 12.50; // Standard price

            $inputCostUSD = ($inputTokens / 1000000) * $inputPricePerMillion;
            $outputCostUSD = ($outputTokens / 1000000) * $outputPricePerMillion;
            $totalCostUSD = $inputCostUSD + $outputCostUSD;
            $costInKSH = $totalCostUSD * $usdToKshRate;

            $deductionAmount = max(0.01, $costInKSH);
            $costMessage = "KSH " . number_format($deductionAmount, 3) . " deducted for your AI query.";
        }

        /** @var User|null $user */
        $user = $this->userModel->find($userId);
        if (bccomp((string) $user->balance, (string) $deductionAmount, 2) < 0) {
            session()->setFlashdata('error', 'Query processed, but your balance was insufficient to cover the cost.');
        } else {
            $newBalance = bcsub((string) $user->balance, (string) $deductionAmount, 2);
            if ($this->userModel->update($userId, ['balance' => $newBalance])) {
                session()->setFlashdata('success', $costMessage);
            } else {
                session()->setFlashdata('error', 'Could not update your balance after the query.');
            }
        }

        // Update memory if assistant mode was used
        if ($isAssistantMode && $memoryService) {
            $aiResponseText = $apiResponse['result'];
            $memoryService->updateMemory($inputText, $aiResponseText, $usedInteractionIds);
        }

        return redirect()->back()->withInput()->with('result', $apiResponse['result']);
    }

    public function addPrompt(): RedirectResponse
    {
        $userId = (int) session()->get('userId');
        if ($userId <= 0) {
            return redirect()->to(url_to('gemini.index'))->with('error', 'You must be logged in to add a prompt.');
        }

        $validation = $this->validate([
            'title'       => 'required|max_length[255]',
            'prompt_text' => 'required',
        ]);

        if (!$validation) {
            return redirect()->to(url_to('gemini.index'))->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'user_id'     => $userId,
            'title'       => $this->request->getPost('title'),
            'prompt_text' => $this->request->getPost('prompt_text'),
        ];

        if ($this->promptModel->save($data)) {
            return redirect()->to(url_to('gemini.index'))->with('success', 'Prompt saved successfully.');
        }

        return redirect()->to(url_to('gemini.index'))->with('error', 'Failed to save the prompt.');
    }

    public function deletePrompt($id): RedirectResponse
    {
        $userId = (int) session()->get('userId');
        if ($userId <= 0) {
            return redirect()->to(url_to('gemini.index'))->with('error', 'You must be logged in to delete a prompt.');
        }

        $prompt = $this->promptModel->find($id);

        if (!$prompt || (int) $prompt->user_id !== $userId) {
            return redirect()->to(url_to('gemini.index'))->with('error', 'You are not authorized to delete this prompt.');
        }

        if ($this->promptModel->delete($id)) {
            return redirect()->to(url_to('gemini.index'))->with('success', 'Prompt deleted successfully.');
        }

        return redirect()->to(url_to('gemini.index'))->with('error', 'Failed to delete the prompt.');
    }
}
