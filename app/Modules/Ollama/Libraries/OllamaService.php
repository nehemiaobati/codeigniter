<?php

namespace App\Modules\Ollama\Libraries;

use App\Modules\Ollama\Config\Ollama as OllamaConfig;
use CodeIgniter\HTTP\CURLRequest;
use Config\Services;

class OllamaService
{
    protected OllamaConfig $config;
    protected OllamaPayloadService $payloadService;
    protected CURLRequest $client;

    public function __construct()
    {
        $this->config = new OllamaConfig();
        $this->payloadService = new OllamaPayloadService();
        $this->client = Services::curlrequest([
            'timeout' => $this->config->timeout,
            'connect_timeout' => 10,
        ]);
    }

    /**
     * Checks if the Ollama instance is reachable.
     *
     * @return bool
     */
    public function checkConnection(): bool
    {
        try {
            $url = rtrim($this->config->baseUrl, '/') . '/'; // Root endpoint usually returns status
            $response = $this->client->get($url);
            return $response->getStatusCode() === 200;
        } catch (\Exception $e) {
            log_message('error', 'Ollama Connection Check Failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Fetches available models from the Ollama instance.
     *
     * @return array List of model names.
     */
    public function getModels(): array
    {
        try {
            $url = rtrim($this->config->baseUrl, '/') . '/api/tags';
            $response = $this->client->get($url);

            if ($response->getStatusCode() !== 200) {
                return [];
            }

            $data = json_decode($response->getBody(), true);
            $models = [];

            if (isset($data['models']) && is_array($data['models'])) {
                foreach ($data['models'] as $model) {
                    $models[] = $model['name'];
                }
            }

            return $models;
        } catch (\Exception $e) {
            log_message('error', 'Ollama Get Models Failed: ' . $e->getMessage());
            return []; // Return empty on failure
        }
    }

    /**
     * Generates a chat response from Ollama.
     *
     * @param string $model The model to use.
     * @param array $messages The conversation history.
     * @return array ['result' => string, 'usage' => array, 'error' => string|null]
     */
    public function generateChat(string $model, array $messages): array
    {
        // 1. Prepare Payload
        $config = $this->payloadService->getPayloadConfig($model, $messages, false); // stream=false for now

        try {
            // 2. Send Request
            $response = $this->client->post($config['url'], [
                'body' => $config['body'],
                'headers' => ['Content-Type' => 'application/json']
            ]);

            $statusCode = $response->getStatusCode();
            $body = $response->getBody();

            if ($statusCode !== 200) {
                $error = json_decode($body, true)['error'] ?? 'Unknown API error';
                log_message('error', "Ollama API Error ({$statusCode}): {$error}");
                return ['error' => "Ollama Error: {$error}"];
            }

            // 3. Parse Response
            $data = json_decode($body, true);

            if (isset($data['message']['content'])) {
                return [
                    'result' => $data['message']['content'],
                    'usage' => [
                        'total_duration' => $data['total_duration'] ?? 0,
                        'load_duration' => $data['load_duration'] ?? 0,
                        'prompt_eval_count' => $data['prompt_eval_count'] ?? 0,
                        'eval_count' => $data['eval_count'] ?? 0,
                    ]
                ];
            }

            return ['error' => 'Invalid response format from Ollama.'];
        } catch (\Exception $e) {
            log_message('error', 'Ollama Generate Failed: ' . $e->getMessage());
            return ['error' => 'Failed to connect to Ollama. Is it running?'];
        }
    }
    /**
     * Wrapper for chat generation to match MemoryService expectation.
     *
     * @param array $messages
     * @param string|null $model
     * @return array
     */
    public function chat(array $messages, ?string $model = null): array
    {
        $model = $model ?? $this->config->defaultModel;
        $response = $this->generateChat($model, $messages);

        if (isset($response['error'])) {
            return ['success' => false, 'error' => $response['error']];
        }

        return [
            'success'  => true,
            'response' => $response['result'],
            'model'    => $model,
            'usage'    => $response['usage'] ?? []
        ];
    }

    /**
     * Generates embeddings for the given text.
     *
     * @param string $input
     * @return array
     */
    public function embed(string $input): array
    {
        $url = rtrim($this->config->baseUrl, '/') . '/api/embeddings';
        // Note: 'nomic-embed-text' or 'mxbai-embed-large' are better for embeddings than llama3,
        // but we'll default to the configured model if not specified.
        // Ideally, add a specific embeddingModel to config.
        $payload = [
            'model'  => $this->config->defaultModel,
            'prompt' => $input
        ];

        try {
            $response = $this->client->post($url, [
                'body'    => json_encode($payload),
                'headers' => ['Content-Type' => 'application/json']
            ]);

            if ($response->getStatusCode() !== 200) {
                log_message('error', 'Ollama Embed Error: ' . $response->getBody());
                return [];
            }

            $data = json_decode($response->getBody(), true);
            return $data['embedding'] ?? [];
        } catch (\Exception $e) {
            log_message('error', 'Ollama Embed Failed: ' . $e->getMessage());
            return [];
        }
    }
}
