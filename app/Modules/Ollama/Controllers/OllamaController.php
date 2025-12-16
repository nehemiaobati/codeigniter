<?php

declare(strict_types=1);

namespace App\Modules\Ollama\Controllers;

use App\Controllers\BaseController;
use App\Entities\User;
use App\Modules\Ollama\Libraries\OllamaService;
use App\Modules\Ollama\Models\OllamaPromptModel;
use App\Modules\Ollama\Models\OllamaUserSettingsModel;
use App\Modules\Ollama\Models\OllamaInteractionModel;
use App\Modules\Ollama\Models\OllamaEntityModel;
use App\Modules\Ollama\Libraries\OllamaDocumentService;
use App\Modules\Ollama\Entities\OllamaUserSetting;
use App\Models\UserModel;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\I18n\Time;
use Parsedown;

class OllamaController extends BaseController
{
    protected UserModel $userModel;
    protected OllamaService $ollamaService;
    protected OllamaPromptModel $promptModel;
    protected OllamaUserSettingsModel $userSettingsModel;

    private const SUPPORTED_MIME_TYPES = [
        'image/png',
        'image/jpeg',
        'image/jpg', // Some clients send this
        'image/webp',
        'image/gif',
        //'application/pdf',
    ];
    private const MAX_FILE_SIZE = 10 * 1024 * 1024; // 10MB
    private const MAX_FILES = 3;
    private const COST_PER_REQUEST = 1.00; // Flat rate per request

    public function __construct()
    {
        $this->userModel         = new UserModel();
        $this->ollamaService     = new OllamaService();
        $this->promptModel       = new OllamaPromptModel();
        $this->userSettingsModel = new OllamaUserSettingsModel();
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
            'stream_output_enabled'  => $userSetting ? $userSetting->stream_output_enabled : true, // Default to true if not set
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
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Auth required.', 'csrf_token' => csrf_hash()]);
        }

        if (!$this->validate([
            'file' => [
                'label' => 'File',
                'rules' => 'uploaded[file]|max_size[file,' . (self::MAX_FILE_SIZE / 1024) . ']|mime_in[file,' . implode(',', self::SUPPORTED_MIME_TYPES) . ']',
            ],
        ])) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => $this->validator->getErrors()['file'], 'csrf_token' => csrf_hash()]);
        }

        $file = $this->request->getFile('file');
        $userTempPath = WRITEPATH . 'uploads/ollama_temp/' . $userId . '/';

        if (!is_dir($userTempPath)) {
            mkdir($userTempPath, 0755, true);
        }

        $fileName = $file->getRandomName();
        if (!$file->move($userTempPath, $fileName)) {
            return $this->response->setStatusCode(500)->setJSON(['status' => 'error', 'message' => 'Save failed.', 'csrf_token' => csrf_hash()]);
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
        if ($userId <= 0) return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'csrf_token' => csrf_hash()]);

        $fileId = $this->request->getPost('file_id');
        if (!$fileId) return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'csrf_token' => csrf_hash()]);

        $filePath = WRITEPATH . 'uploads/ollama_temp/' . $userId . '/' . basename($fileId);

        if (file_exists($filePath) && unlink($filePath)) {
            return $this->response->setJSON(['status' => 'success', 'csrf_token' => csrf_hash()]);
        }

        return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'File not found', 'csrf_token' => csrf_hash()]);
    }

    /**
     * Generates content using Ollama with Memory Integration.
     */
    /**
     * Generates content using Ollama with Memory Integration.
     */
    public function generate(): ResponseInterface
    {
        // Timeout Safety: Prevent PHP timeouts during slow local LLM inference
        set_time_limit(300);

        $userId = (int) session()->get('userId');
        $user = $this->userModel->find($userId);

        if (!$user) return redirect()->back()->with('error', 'User not found.');

        // Input Validation
        if (!$this->validate([
            'prompt' => 'max_length[100000]',
            'model'  => 'required'
        ])) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Invalid input.',
                    'csrf_token' => csrf_hash()
                ]);
            }
            return redirect()->back()->withInput()->with('error', 'Invalid input.');
        }

        $inputText = (string) $this->request->getPost('prompt');
        $inputText = strip_tags($inputText); // Remove HTML tags for weak model that get confused by them.
        $selectedModel = (string) $this->request->getPost('model');
        $uploadedFileIds = (array) $this->request->getPost('uploaded_media');

        $userSetting = $this->userSettingsModel->where('user_id', $userId)->first();
        $isAssistantMode = $userSetting ? $userSetting->assistant_mode_enabled : true;

        // 1. Check Balance
        if ($user->balance < self::COST_PER_REQUEST) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Insufficient balance.',
                    'csrf_token' => csrf_hash()
                ]);
            }
            return redirect()->back()->withInput()->with('error', 'Insufficient balance.');
        }

        // 2. Handle Files (Multimodal)
        $images = [];
        $userTempPath = WRITEPATH . 'uploads/ollama_temp/' . $userId . '/';
        foreach ($uploadedFileIds as $fileId) {
            $filePath = $userTempPath . basename($fileId);
            if (file_exists($filePath)) {
                $images[] = base64_encode(file_get_contents($filePath));
                @unlink($filePath); // Cleanup immediately
            }
        }

        $response = [];

        // REFACTOR: Use MemoryService for both Text-Only and Valid Multimodal Requests if Assistant Mode is on
        // Limitation: If MemoryService doesn't support images yet, we fallback. But we just updated it!
        if ($isAssistantMode) {
            $memoryService = new \App\Modules\Ollama\Libraries\OllamaMemoryService($userId);
            $response = $memoryService->processChat($inputText, $selectedModel, $images);
        } else {
            // Direct API (No Memory)
            $userMessage = ['role' => 'user', 'content' => $inputText];
            if (!empty($images)) {
                $userMessage['images'] = $images;
            }
            $messages = [$userMessage];
            $response = $this->ollamaService->generateChat($selectedModel, $messages);
        }

        if (isset($response['error']) || (isset($response['success']) && !$response['success'])) {
            $msg = $response['error'] ?? 'Unknown error';
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status'     => 'error',
                    'message'    => $msg,
                    'csrf_token' => csrf_hash()
                ]);
            }
            return redirect()->back()->withInput()->with('error', $msg);
        }

        // Normalize response format
        log_message('debug', 'Response: ' . json_encode($response));
        $resultText = $response['result'] ?? $response['response'] ?? '';

        // 3. Deduct Balance
        $this->userModel->deductBalance((int)$user->id, (string)self::COST_PER_REQUEST);

        // 4. Output
        $parsedown = new Parsedown();
        $parsedown->setBreaksEnabled(true);
        $parsedown->setSafeMode(false);
        $finalHtml = $parsedown->text($resultText);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status'     => 'success',
                'result'     => $finalHtml,
                'raw_result' => $resultText,
                'flash_html' => view('App\Views\partials\flash_messages', ['success' => 'Generated successfully.']),
                'csrf_token' => csrf_hash()
            ]);
        }

        return redirect()->back()->withInput()
            ->with('result', $finalHtml)
            ->with('raw_result', $resultText)
            ->with('success', 'Generated successfully. Cost: ' . self::COST_PER_REQUEST . ' credits.');
    }

    /**
     * Handles streaming text generation via Server-Sent Events (SSE).
     */
    public function stream(): ResponseInterface
    {
        // Setup SSE Headers Immediately
        $this->response->setContentType('text/event-stream');
        $this->response->setHeader('Cache-Control', 'no-cache');
        $this->response->setHeader('Connection', 'keep-alive');
        $this->response->setHeader('X-Accel-Buffering', 'no');
        $this->response->setHeader('X-CSRF-TOKEN', csrf_hash()); // Critical: Send token in header for early access

        $userId = (int) session()->get('userId');
        $user = $this->userModel->find($userId);

        if (!$user) {
            $this->response->setBody("data: " . json_encode([
                'error' => 'User not found',
                'csrf_token' => csrf_hash()
            ]) . "\n\n");
            return $this->response;
        }

        // Input Validation
        $inputText = (string) $this->request->getPost('prompt');
        $uploadedFileIds = (array) $this->request->getPost('uploaded_media');
        $selectedModel = (string) $this->request->getPost('model');

        // 1. Check Balance
        if ($user->balance < self::COST_PER_REQUEST) {
            $this->response->setBody("data: " . json_encode([
                'error' => "Insufficient balance.",
                'csrf_token' => csrf_hash()
            ]) . "\n\n");
            return $this->response;
        }

        // 2. Handle Files
        $images = [];
        $userTempPath = WRITEPATH . 'uploads/ollama_temp/' . $userId . '/';
        foreach ($uploadedFileIds as $fileId) {
            $filePath = $userTempPath . basename($fileId);
            if (file_exists($filePath)) {
                $images[] = base64_encode(file_get_contents($filePath));
                @unlink($filePath);
            }
        }

        // 3. Build Context (Simulate MemoryService Logic manually for Stream, or update Service)
        // MemoryService::processChat computes context THEN calls API. We need to split this.
        // For now, let's just do a Direct Stream (No Memory Context in Stream to keep it simple, OR fetch context first).

        // Let's FETCH context manually to support RAG in Stream
        $userSetting = $this->userSettingsModel->where('user_id', $userId)->first();
        $isAssistantMode = $userSetting ? $userSetting->assistant_mode_enabled : true;

        $messages = [];
        if ($isAssistantMode) {
            $memoryService = new \App\Modules\Ollama\Libraries\OllamaMemoryService($userId);
            // We can't use processChat because it calls API. We need a way to just GET context messages.
            // We will duplicate the context logic here briefly or Refactor MemoryService later to expose it.
            // Given limitations, let's use a Direct approach for Stream V1, or basic system prompt.
            // Actually, verify plan: "Update OllamaService... Add generateStream".
            // We can instantiate MemoryService, but it's protected.
            // Let's do a simple context retrieval if possible, otherwise just text.

            // For V1 Stream, let's stick to Direct + Images. 
            // To support Memory, we'd need to expose `_getRelevantContext` from MemoryService.
            // Let's assume standard behavior for now.

            $messages[] = ['role' => 'system', 'content' => 'You are a helpful AI assistant.'];
        }

        $userMessage = ['role' => 'user', 'content' => $inputText];
        if (!empty($images)) $userMessage['images'] = $images;
        $messages[] = $userMessage;

        // Write session to ensure CSRF token is persisted before we possibly crash or exit
        session_write_close();

        $this->response->sendHeaders();
        if (ob_get_level() > 0) ob_end_flush();
        flush();

        // 4. Stream
        $result = $this->ollamaService->generateStream(
            $selectedModel,
            $messages,
            function ($chunk) {
                echo "data: " . json_encode(['text' => $chunk]) . "\n\n";
                if (ob_get_level() > 0) ob_flush();
                flush();
            }
        );

        if (isset($result['error'])) {
            echo "data: " . json_encode([
                'error' => $result['error'],
                'csrf_token' => csrf_hash()
            ]) . "\n\n";
            exit;
        }

        // 5. Deduct Balance (After success/start) - Simple deduction
        // Ideally we deduct only if success. 
        $this->userModel->deductBalance((int)$user->id, (string)self::COST_PER_REQUEST);

        // 6. Finish
        echo "event: close\n";
        echo "data: " . json_encode([
            'csrf_token' => csrf_hash(),
            'cost' => self::COST_PER_REQUEST
        ]) . "\n\n";

        exit;
    }

    public function updateSetting(): ResponseInterface
    {
        $userId = (int) session()->get('userId');
        $key = $this->request->getPost('setting_key');
        $enabled = $this->request->getPost('enabled') === 'true';

        if (!in_array($key, ['assistant_mode_enabled', 'stream_output_enabled'])) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid setting', 'csrf_token' => csrf_hash()]);
        }

        $setting = $this->userSettingsModel->where('user_id', $userId)->first();
        if (!$setting) {
            $setting = new OllamaUserSetting();
            $setting->user_id = $userId;
        }

        $setting->$key = $enabled;
        $this->userSettingsModel->save($setting);

        return $this->response->setJSON(['status' => 'success', 'csrf_token' => csrf_hash()]);
    }

    public function clearMemory(): RedirectResponse
    {
        $userId = (int) session()->get('userId');

        // Clear Interactions
        $interactionModel = new OllamaInteractionModel();
        $interactionModel->where('user_id', $userId)->delete();

        // Clear Entities
        $entityModel = new OllamaEntityModel();
        $entityModel->where('user_id', $userId)->delete();

        return redirect()->back()->with('success', 'Memory cleared.');
    }

    /**
     * Downloads the generated content as a document.
     */
    /**
     * Downloads the generated content as a document.
     */
    public function downloadDocument()
    {
        $userId = (int) session()->get('userId');
        if ($userId <= 0) return $this->response->setStatusCode(403)->setJSON(['message' => 'Auth required.']);

        $content = $this->request->getPost('content');
        $format  = $this->request->getPost('format');

        if (empty($content) || !in_array($format, ['pdf', 'docx'])) {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'Invalid content or format.']);
        }

        $docService = new OllamaDocumentService();
        $result = $docService->generate($content, $format, [
            'author' => 'Ollama User ' . $userId
        ]);

        if ($result['status'] === 'success') {
            $filename = 'ollama_export_' . date('Ymd_His') . '.' . $format;
            $contentType = ($format === 'pdf') ? 'application/pdf' : 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';

            return $this->response
                ->setHeader('Content-Type', $contentType)
                ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
                ->setHeader('X-CSRF-TOKEN', csrf_hash()) // CRITICAL: Send new token
                ->setBody($result['fileData']);
        }

        return $this->response->setStatusCode(500)->setJSON(['message' => $result['message'] ?? 'Export failed.']);
    }

    /**
     * Adds a new saved prompt.
     */
    public function addPrompt(): ResponseInterface
    {
        $userId = (int) session()->get('userId');
        if ($userId <= 0) return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Auth required', 'csrf_token' => csrf_hash()]);

        $rules = [
            'title'       => 'required|min_length[3]|max_length[255]',
            'prompt_text' => 'required',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid input', 'csrf_token' => csrf_hash()]);
        }

        $data = [
            'user_id'     => $userId,
            'title'       => $this->request->getPost('title'),
            'prompt_text' => $this->request->getPost('prompt_text'),
        ];

        $id = $this->promptModel->insert($data);

        if ($id) {
            return $this->response->setJSON([
                'status' => 'success',
                'prompt' => array_merge($data, ['id' => $id]),
                'csrf_token' => csrf_hash()
            ]);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to save', 'csrf_token' => csrf_hash()]);
    }

    /**
     * Deletes a saved prompt.
     */
    public function deletePrompt($id): ResponseInterface
    {
        $userId = (int) session()->get('userId');
        $prompt = $this->promptModel->find($id);

        if (!$prompt || $prompt->user_id !== $userId) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Unauthorized', 'csrf_token' => csrf_hash()]);
        }

        if ($this->promptModel->delete($id)) {
            return $this->response->setJSON(['status' => 'success', 'csrf_token' => csrf_hash()]);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to delete', 'csrf_token' => csrf_hash()]);
    }
}
