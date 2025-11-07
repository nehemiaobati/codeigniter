<?php declare(strict_types=1);

namespace App\Libraries;

/**
 * Service layer for interacting with the Google Gemini API.
 */
class GeminiService
{
    /**
     * The API key for authenticating with the Gemini API.
     * @var string|null
     */
    protected $apiKey;

    /**
     * An ordered list of Gemini model IDs to try, from most preferred to least preferred.
     * The service will attempt to use these models in order, falling back to the next
     * if a quota error (429) is encountered for the current model.
     * @var array<string>
     */
    protected array $modelPriorities = [
        //"gemini-2.5-pro",
        "gemini-flash-latest",
        "gemini-flash-lite-latest",
        "gemini-2.5-flash",
        "gemini-2.5-flash-lite",
        "gemini-2.0-flash",
        "gemini-2.0-flash-lite",
    ];

    /**
     * Constructor.
     * Initializes the service and retrieves the Gemini API key from environment variables.
     */
    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY') ?? getenv('GEMINI_API_KEY');
    }

    /**
     * Sends a request to the Gemini API with a retry mechanism and model fallback for quota errors.
     *
     * @param array $parts An array of content parts (text and/or inlineData for files).
     * @return array An associative array with either a 'result' string and 'usage' data on success, or an 'error' string on failure.
     */
    public function generateContent(array $parts): array
    {
        if (!$this->apiKey) {
            return ['error' => 'GEMINI_API_KEY not set in .env file.'];
        }

        $generateContentApi = "generateContent";
        $lastError = ['error' => 'An unexpected error occurred after multiple retries.']; // Default error

        if (empty($this->modelPriorities)) {
            return ['error' => 'No Gemini models configured in modelPriorities.'];
        }

        foreach ($this->modelPriorities as $model) {
            $currentModel = $model;
            $apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/{$currentModel}:{$generateContentApi}?key={$this->apiKey}";

            // [RE-INTEGRATED] Advanced request payload with tools and thinking configuration.
            $requestPayload = [
                "contents" => [["role" => "user", "parts" => $parts]],
                "generationConfig" => [
                    "maxOutputTokens" => 64192,
                    "thinkingConfig" => [
                        "thinkingBudget" => -1
                    ]
                ],
                "tools" => [
                    ["googleSearch" => new \stdClass()]
                ],
            ];

            $maxRetries = 3;
            $initialDelay = 1; // seconds

            for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
                try {
                    $client = \Config\Services::curlrequest();
                    $response = $client->request('POST', $apiUrl, [
                        'body' => json_encode($requestPayload),
                        'headers' => ['Content-Type' => 'application/json'],
                        'timeout' => 90,
                        'connect_timeout' => 15,
                    ]);

                    $statusCode = $response->getStatusCode();
                    $responseBody = $response->getBody();

                    if ($statusCode === 429) {
                        log_message('warning', "Gemini API Quota Exceeded (429) for model '{$currentModel}' on attempt {$attempt}.");
                        $lastError = ['error' => "Quota exceeded for model '{$currentModel}'."];
                        if ($attempt < $maxRetries) {
                            sleep($initialDelay * pow(2, $attempt - 1));
                            continue;
                        } else {
                            break; // Max retries for this model, break to try the next model.
                        }
                    }

                    if ($statusCode !== 200) {
                        $errorData = json_decode($responseBody, true);
                        $errorMessage = $errorData['error']['message'] ?? 'Unknown API error';
                        log_message('error', "Gemini API Error: Status {$statusCode} - {$errorMessage} | Model: {$currentModel} | Response: {$responseBody}");
                        $lastError = ['error' => $errorMessage];
                        return $lastError; // Non-429 error, fail immediately without fallback.
                    }

                    $responseData = json_decode($responseBody, true);
                    // [RE-INTEGRATED] Robust check for JSON decoding errors.
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        log_message('error', 'Gemini API Response JSON Decode Error: ' . json_last_error_msg() . ' | Response: ' . $responseBody);
                        $lastError = ['error' => 'Failed to decode API response.'];
                        return $lastError; // Fail immediately.
                    }

                    // [RE-INTEGRATED] Loop to process all parts of the response.
                    $processedText = '';
                    if (isset($responseData['candidates'][0]['content']['parts'])) {
                        foreach ($responseData['candidates'][0]['content']['parts'] as $part) {
                            $processedText .= $part['text'] ?? '';
                        }
                    }

                    $usageMetadata = $responseData['usageMetadata'] ?? null;

                    // [RE-INTEGRATED] Stricter check for a valid response.
                    if (empty($processedText) && $usageMetadata === null) {
                        $lastError = ['error' => 'Received an empty or invalid response from the AI.'];
                        return $lastError; // Fail immediately.
                    }

                    // Success! Return the result.
                    return ['result' => $processedText, 'usage' => $usageMetadata];

                } catch (\Exception $e) {
                    log_message('error', "Gemini API Request Attempt {$attempt} failed for model '{$currentModel}': " . $e->getMessage());
                    $lastError = ['error' => 'The AI service is currently unavailable or the request timed out. Please try again in a few moments.'];
                    if ($attempt < $maxRetries) {
                        sleep($initialDelay * pow(2, $attempt - 1));
                    }
                }
            }
        }

        // [RE-INTEGRATED] Specific error message if all models failed due to quota issues.
        $finalErrorMsg = $lastError['error'] ?? 'An unexpected error occurred after multiple retries across all models.';
        if (str_contains($finalErrorMsg, 'Quota exceeded')) {
            return ['error' => 'All available AI models have exceeded their quota. Please wait and try again later. To increase your limits, request a quota increase through AI Studio, or switch to another /auth method.'];
        }
        
        return $lastError;
    }

    /**
     * [NEW] Converts a given text string into speech using a dedicated TTS API call.
     *
     * @param string $textToSpeak The text to be converted to speech.
     * @return array An associative array with 'status' (bool) and 'audioData' (string|null) or 'error' (string).
     */
    public function generateSpeech(string $textToSpeak): array
    {
        if (!$this->apiKey) {
            return ['status' => false, 'error' => 'GEMINI_API_KEY not set in .env file.'];
        }

        $ttsModel = 'gemini-2.5-flash-preview-tts';
        $apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/{$ttsModel}:generateContent?key={$this->apiKey}";

        $requestPayload = [
            "contents" => [
                ["parts" => [["text" => $textToSpeak]]]
            ],
            "generationConfig" => [
                "responseModalities" => ["audio"],
            ],
            "speech_config" => [
                "voice_config" => [
                    "prebuilt_voice_config" => [
                        "voice_name" => "Zephyr",
                    ]
                ]
            ],
        ];

        try {
            $client = \Config\Services::curlrequest();
            $response = $client->request('POST', $apiUrl, [
                'body' => json_encode($requestPayload),
                'headers' => ['Content-Type' => 'application/json'],
                'timeout' => 60,
            ]);

            $statusCode = $response->getStatusCode();
            $responseBody = $response->getBody();

            if ($statusCode !== 200) {
                $errorData = json_decode($responseBody, true);
                $errorMessage = $errorData['error']['message'] ?? 'Unknown API error during speech generation.';
                log_message('error', "Gemini TTS Error: Status {$statusCode} - {$errorMessage}");
                return ['status' => false, 'error' => $errorMessage];
            }

            $responseData = json_decode($responseBody, true);
            $audioData = $responseData['candidates'][0]['content']['parts'][0]['audioData'] ?? null;

            if ($audioData === null) {
                log_message('error', 'Gemini TTS Error: Audio data not found in response.');
                return ['status' => false, 'error' => 'Failed to retrieve audio data from the AI service.'];
            }

            return ['status' => true, 'audioData' => $audioData];

        } catch (\Exception $e) {
            log_message('error', 'Gemini TTS Exception: ' . $e->getMessage());
            return ['status' => false, 'error' => 'Could not connect to the speech synthesis service.'];
        }
    }

    /**
     * Counts the number of tokens in a given set of content parts.
     * This method will use the highest priority model and does not implement fallback.
     *
     * @param array $parts An array of content parts (text and/or inlineData for files).
     * @return array An associative array with 'status' (bool) and 'totalTokens' (int) or 'error' (string).
     */
    public function countTokens(array $parts): array
    {
        if (!$this->apiKey) {
            return ['status' => false, 'error' => 'GEMINI_API_KEY not set in .env file.'];
        }

        $currentModel = $this->modelPriorities[0] ?? "gemini-1.5-flash-latest";
        $countTokensApi = "countTokens";
        $apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/{$currentModel}:{$countTokensApi}?key={$this->apiKey}";

        $requestPayload = ["contents" => [["parts" => $parts]]];
        $requestBody = json_encode($requestPayload);
        $client = \Config\Services::curlrequest();

        try {
            $response = $client->request('POST', $apiUrl, [
                'body' => $requestBody,
                'headers' => ['Content-Type' => 'application/json'],
                'timeout' => 10,
            ]);

            $statusCode = $response->getStatusCode();
            $responseBody = $response->getBody();

            if ($statusCode !== 200) {
                $errorData = json_decode($responseBody, true);
                $errorMessage = $errorData['error']['message'] ?? 'Unknown API error during token count.';
                log_message('error', "Gemini API countTokens Error: Status {$statusCode} - {$errorMessage}");
                return ['status' => false, 'error' => $errorMessage];
            }

            $responseData = json_decode($responseBody, true);
            $totalTokens = $responseData['totalTokens'] ?? 0;

            return ['status' => true, 'totalTokens' => $totalTokens];

        } catch (\Exception $e) {
            log_message('error', 'Gemini API countTokens Exception: ' . $e->getMessage());
            return ['status' => false, 'error' => 'Could not connect to the AI service to estimate cost.'];
        }
    }
}