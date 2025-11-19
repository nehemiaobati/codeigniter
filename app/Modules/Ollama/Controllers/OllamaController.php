<?php declare(strict_types=1);

namespace App\Modules\Ollama\Controllers;

use App\Controllers\BaseController;
use App\Modules\Ollama\Libraries\OllamaService;
use App\Modules\Ollama\Libraries\OllamaMemoryService;
use App\Modules\Ollama\Models\OllamaInteractionModel;
use Parsedown;

class OllamaController extends BaseController
{
    private OllamaService $ollama;

    public function __construct()
    {
        $this->ollama = new OllamaService();
    }

    public function index(): string
    {
        $userId = (int) session()->get('userId');
        $model  = new OllamaInteractionModel();
        
        // Fetch recent chat history for display
        $history = $model->where('user_id', $userId)
                         ->orderBy('created_at', 'DESC')
                         ->limit(20)
                         ->findAll();

        $isOnline = $this->ollama->isOnline();
        if (!$isOnline) {
            session()->setFlashdata('error', 'Local Ollama service is unreachable. Is it running?');
        }

        $data = [
            'pageTitle'    => 'Local AI | Ollama & DeepSeek',
            'isOnline'     => $isOnline,
            'history'      => $history,
            'canonicalUrl' => url_to('ollama.index')
        ];

        return view('App\Modules\Ollama\Views\ollama\chat', $data);
    }

    public function chat()
    {
        $userId = (int) session()->get('userId');
        $prompt = (string) $this->request->getPost('prompt');

        if (empty(trim($prompt))) {
            return redirect()->back()->with('error', 'Please enter a prompt.');
        }

        if (!$this->ollama->isOnline()) {
            return redirect()->back()->withInput()->with('error', 'Ollama service is offline.');
        }

        // Initialize Memory Service
        $memory = new OllamaMemoryService($userId);

        // Build Context
        $messages = $memory->buildContext($prompt);

        // Call API
        $result = $this->ollama->chat($messages);

        if (!$result['success']) {
            return redirect()->back()->withInput()->with('error', $result['error']);
        }

        // Process Response
        $rawResponse = $result['response'];
        log_message('info','rawresponse:'. json_encode($rawResponse));
        
        // Save to DB (Memory)
        $memory->saveInteraction($prompt, $rawResponse, $result['model']);

        // Parse Markdown for Display
        $parsedown = new Parsedown();
        $parsedown->setSafeMode(true);
        
        // Handle DeepSeek <think> tags for UI presentation
        // We escape them to prevent HTML stripping, then wrap in a styled div
        $displayHtml = htmlspecialchars($rawResponse);
        $displayHtml = str_replace('<think>', '<div class="think-block"><div class="think-label">Reasoning Process</div>', $displayHtml);
        $displayHtml = str_replace('</think>', '</div>', $displayHtml);
        
        // Since we manually escaped, we use Parsedown on the parts *after* processing, 
        // but for simplicity in this block, we pass the raw content to view and let JS or CSS handle it, 
        // or we just return the raw text and let the View sanitize.
        
        return redirect()->back()->with('success_response', $rawResponse);
    }

    public function clearHistory()
    {
        $userId = (int) session()->get('userId');
        $model = new OllamaInteractionModel();
        $model->where('user_id', $userId)->delete();
        
        return redirect()->back()->with('success', 'Conversation history cleared.');
    }
}
