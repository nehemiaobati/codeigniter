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
        $config = $this->payloadService->getPayloadConfig($model, $messages, false); // stream=false

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
     * Generates a streaming chat response from Ollama.
     *
     * @param string $model
     * @param array $messages
     * @param callable $callback Function to handle each chunk string.
     * @return array ['usage' => array] or ['error' => string]
     */
    public function generateStream(string $model, array $messages, callable $callback): array
    {
        $config = $this->payloadService->getPayloadConfig($model, $messages, true); // stream=true
        $usage = [];

        try {
            // Using CURL directly for streaming as CI4 CurlRequest doesn't support stream callbacks easily in all versions
            // or to have finer control. However, CI4 does support 'debug' or 'on_progress' but reading body chunks is cleaner with raw curl or a custom handler.
            // Let's stick to a robust implementation using a reading loop if possible, or a callback.
            // CI4's CurlRequest supports 'stream' option which returns a resource, but parsing JSON chunks from a stream is tricky.
            // EASIEST WAY: Use a custom curl execution loop.

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $config['url']);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $config['body']);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, false); // Don't return, write to callback
            curl_setopt($ch, CURLOPT_WRITEFUNCTION, function ($ch, $chunk) use ($callback, &$usage) {
                // Ollama sends multiple JSON objects in one chunk sometimes, or partials.
                // Each line is a JSON object.
                // We need to buffer partial lines if they occur (though curl usually delivers full packets).
                // For simplicity, let's assume line-delimited JSON.

                $lines = explode("\n", $chunk);
                foreach ($lines as $line) {
                    $line = trim($line);
                    if (empty($line)) continue;

                    $data = json_decode($line, true);
                    if ($data) {
                        if (isset($data['message']['content'])) {
                            $callback($data['message']['content']);
                        }
                        if (isset($data['done']) && $data['done'] === true) {
                            $usage = [
                                'total_duration' => $data['total_duration'] ?? 0,
                                'load_duration' => $data['load_duration'] ?? 0,
                                'prompt_eval_count' => $data['prompt_eval_count'] ?? 0,
                                'eval_count' => $data['eval_count'] ?? 0,
                            ];
                        }
                    }
                }
                return strlen($chunk);
            });

            curl_exec($ch);

            if (curl_errno($ch)) {
                $error = curl_error($ch);
                curl_close($ch);
                return ['error' => "Curl Error: $error"];
            }

            $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($statusCode !== 200) {
                return ['error' => "Ollama API returned status $statusCode"];
            }

            return ['success' => true, 'usage' => $usage];
        } catch (\Throwable $e) {
            log_message('error', 'Ollama Stream Failed: ' . $e->getMessage());
            return ['error' => 'Streaming failed: ' . $e->getMessage()];
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
        // Use the new /api/embed endpoint (Ollama 0.1.26+)
        $url = rtrim($this->config->baseUrl, '/') . '/api/embed';

        $payload = [
            'model'  => $this->config->embeddingModel,
            'input'  => $input // 'input' instead of 'prompt' for /api/embed
        ];

        try {
            log_message('info', 'Ollama Embed Request: ' . json_encode($payload));

            $response = $this->client->post($url, [
                'body'        => json_encode($payload),
                'headers'     => ['Content-Type' => 'application/json'],
                'http_errors' => false // Prevent exception on 4xx/5xx to capture body
            ]);

            if ($response->getStatusCode() !== 200) {
                log_message('error', 'Ollama Embed Error: ' . $response->getBody());
                return [];
            }

            $data = json_decode($response->getBody(), true);

            // /api/embed returns 'embeddings' (array of arrays)
            $embedding = [];
            if (isset($data['embeddings']) && is_array($data['embeddings'])) {
                $embedding = $data['embeddings'][0] ?? [];
            } elseif (isset($data['embedding'])) {
                // Fallback for older versions or different response shapes
                $embedding = $data['embedding'];
            }

            if (empty($embedding)) {
                log_message('error', 'Ollama Embed Empty Response: ' . json_encode($data));
            } else {
                log_message('info', 'Ollama Embed Success. Vector Size: ' . count($embedding));
            }

            return $embedding;
        } catch (\Exception $e) {
            log_message('error', 'Ollama Embed Failed: ' . $e->getMessage());
            return [];
        }
    }
}
