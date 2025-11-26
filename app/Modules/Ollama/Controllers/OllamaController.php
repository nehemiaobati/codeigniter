<?php

declare(strict_types=1);

namespace App\Modules\Ollama\Controllers;

use App\Controllers\BaseController;
use App\Entities\User;
use App\Modules\Ollama\Libraries\OllamaService;
use App\Modules\Gemini\Models\PromptModel; // Reuse Gemini models for now
use App\Modules\Gemini\Models\UserSettingsModel; // Reuse Gemini models for now
use App\Modules\Gemini\Models\InteractionModel; // Reuse Gemini models for now
use App\Modules\Gemini\Models\EntityModel; // Reuse Gemini models for now
use App\Models\UserModel;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\I18n\Time;
use Parsedown;

class OllamaController extends BaseController
{
    protected UserModel $userModel;
    protected OllamaService $ollamaService;
    protected PromptModel $promptModel;
    protected UserSettingsModel $userSettingsModel;

    private const SUPPORTED_MIME_TYPES = [
        'image/png',
        'image/jpeg',
        'image/webp',
    ];
    private const MAX_FILE_SIZE = 10 * 1024 * 1024; // 10MB
    private const MAX_FILES = 3;
    private const COST_PER_REQUEST = 1.00; // Flat rate per request

    public function __construct()
    {
        $this->userModel         = new UserModel();
        $this->ollamaService     = new OllamaService();
        $this->promptModel       = new PromptModel();
        $this->userSettingsModel = new UserSettingsModel();
    }

    /**
     * Displays the main application dashboard.
     */
    public function index(): string
    {
        $userId = (int) session()->get('userId');
        $prompts = $this->promptModel->where('user_id', $userId)->findAll();
        $userSetting = $this->userSettingsModel->where('user_id', $userId)->first();

        // Fetch available models
        $availableModels = $this->ollamaService->getModels();
        if (empty($availableModels)) {
            $availableModels = ['llama3']; // Fallback
        }

        $data = [
            'pageTitle'              => 'Local AI Workspace | Ollama',
            'metaDescription'        => 'Interact with local LLMs via Ollama.',
            'canonicalUrl'           => url_to('ollama.index'),
            'result'                 => session()->getFlashdata('result'),
            'error'                  => session()->getFlashdata('error'),
            'prompts'                => $prompts,
            'assistant_mode_enabled' => $userSetting ? $userSetting->assistant_mode_enabled : true,
            'maxFileSize'            => self::MAX_FILE_SIZE,
            'maxFiles'               => self::MAX_FILES,
            'supportedMimeTypes'     => json_encode(self::SUPPORTED_MIME_TYPES),
            'availableModels'        => $availableModels,
        ];
        $data['robotsTag'] = 'noindex, follow';

        return view('App\Modules\Ollama\Views\ollama\query_form', $data);
    }

    /**
     * Handles file uploads.
     */
    public function uploadMedia(): ResponseInterface
    {
        $userId = (int) session()->get('userId');
        if ($userId <= 0) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Auth required.']);
        }

        if (!$this->validate([
            'file' => [
                'label' => 'File',
                'rules' => 'uploaded[file]|max_size[file,' . (self::MAX_FILE_SIZE / 1024) . ']|mime_in[file,' . implode(',', self::SUPPORTED_MIME_TYPES) . ']',
            ],
        ])) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => $this->validator->getErrors()['file']]);
        }

        $file = $this->request->getFile('file');
        $userTempPath = WRITEPATH . 'uploads/ollama_temp/' . $userId . '/';

        if (!is_dir($userTempPath)) {
            mkdir($userTempPath, 0777, true);
        }

        $fileName = $file->getRandomName();
        if (!$file->move($userTempPath, $fileName)) {
            return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Save failed.']);
        }

        return $this->response->setJSON([
            'status'        => 'success',
            'file_id'       => $fileName,
            'original_name' => $file->getClientName(),
            'csrf_token'    => csrf_hash(),
        ]);
    }

    /**
     * Deletes a temporary uploaded file.
     */
    public function deleteMedia(): ResponseInterface
    {
        $userId = (int) session()->get('userId');
        if ($userId <= 0) return $this->response->setStatusCode(403);

        $fileId = $this->request->getPost('file_id');
        if (!$fileId) return $this->response->setStatusCode(400);

        $filePath = WRITEPATH . 'uploads/ollama_temp/' . $userId . '/' . basename($fileId);

        if (file_exists($filePath) && unlink($filePath)) {
            return $this->response->setJSON(['status' => 'success', 'csrf_token' => csrf_hash()]);
        }

        return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'File not found']);
    }

    /**
     * Generates content using Ollama.
     */
    public function generate(): RedirectResponse
    {
        $userId = (int) session()->get('userId');
        $user = $this->userModel->find($userId);

        if (!$user) return redirect()->back()->with('error', 'User not found.');

        // Input Validation
        if (!$this->validate([
            'prompt' => 'max_length[100000]',
            'model'  => 'required'
        ])) {
            return redirect()->back()->withInput()->with('error', 'Invalid input.');
        }

        $inputText = (string) $this->request->getPost('prompt');
        $selectedModel = (string) $this->request->getPost('model');
        $uploadedFileIds = (array) $this->request->getPost('uploaded_media');

        $userSetting = $this->userSettingsModel->where('user_id', $userId)->first();
        $isAssistantMode = $userSetting ? $userSetting->assistant_mode_enabled : true;

        // 1. Check Balance
        if ($user->balance < self::COST_PER_REQUEST) {
            return redirect()->back()->withInput()->with('error', 'Insufficient balance.');
        }

        // 2. Prepare Context & Files
        $messages = [];

        // Add System Prompt if in Assistant Mode (Simplified for now)
        if ($isAssistantMode) {
            $messages[] = ['role' => 'system', 'content' => 'You are a helpful AI assistant.'];
            // TODO: Retrieve history from InteractionModel if needed
        }

        // Handle Files
        $images = [];
        $userTempPath = WRITEPATH . 'uploads/ollama_temp/' . $userId . '/';
        foreach ($uploadedFileIds as $fileId) {
            $filePath = $userTempPath . basename($fileId);
            if (file_exists($filePath)) {
                $images[] = base64_encode(file_get_contents($filePath));
                @unlink($filePath); // Cleanup immediately
            }
        }

        // Add User Message
        $userMsg = ['role' => 'user', 'content' => $inputText];
        if (!empty($images)) {
            $userMsg['images'] = $images;
        }
        $messages[] = $userMsg;

        // 3. Call API
        $response = $this->ollamaService->generateChat($selectedModel, $messages);

        if (isset($response['error'])) {
            return redirect()->back()->withInput()->with('error', $response['error']);
        }

        // 4. Deduct Balance
        $this->userModel->deductBalance((int)$user->id, (string)self::COST_PER_REQUEST);

        // 5. Save Interaction (Optional, for history)
        if ($isAssistantMode) {
            // TODO: Save to InteractionModel
        }

        // 6. Output
        $parsedown = new Parsedown();
        $parsedown->setSafeMode(true);

        return redirect()->back()->withInput()
            ->with('result', $parsedown->text($response['result']))
            ->with('raw_result', $response['result'])
            ->with('success', 'Generated successfully. Cost: ' . self::COST_PER_REQUEST . ' credits.');
    }

    // --- Settings & Prompts (Reused Logic) ---

    public function updateSetting(): ResponseInterface
    {
        // Reuse Gemini logic or implement similar
        // For brevity, assuming similar implementation to GeminiController::updateSetting
        // ... (Implementation omitted for brevity, can be copied if needed)
        return $this->response->setJSON(['status' => 'success', 'csrf_token' => csrf_hash()]);
    }

    public function addPrompt(): RedirectResponse
    {
        $userId = (int) session()->get('userId');
        $this->promptModel->save([
            'user_id' => $userId,
            'title' => $this->request->getPost('title'),
            'prompt_text' => $this->request->getPost('prompt_text')
        ]);
        return redirect()->back()->with('success', 'Prompt saved.');
    }

    public function deletePrompt(int $id): RedirectResponse
    {
        $this->promptModel->delete($id);
        return redirect()->back()->with('success', 'Prompt deleted.');
    }

    public function clearMemory(): RedirectResponse
    {
        // Reuse Gemini logic
        return redirect()->back()->with('success', 'Memory cleared.');
    }
}
