<?php declare(strict_types=1);

namespace App\Modules\Gemini\Libraries;

/**
 * Service responsible for generating model-specific configurations and payloads.
 * Decouples configuration complexity from the execution service.
 */
class ModelPayloadService
{
    /**
     * Returns the specific API Endpoint URL and JSON Request Body for a given model.
     *
     * @param string $modelId The specific model ID (e.g., gemini-3-pro-preview).
     * @param string $apiKey The API Key.
     * @param array $parts The content parts (user input/images).
     * @return array ['url' => string, 'body' => string]
     */
    public function getPayloadConfig(string $modelId, string $apiKey, array $parts): array
    {
        // Base URL construction - allows for switching between stream and standard if needed in future
        // Keeping 'generateContent' for compatibility with existing GeminiService parsing logic
        // If streaming is strictly required by the model, this string can be changed to 'streamGenerateContent'
        $apiMethod = 'generateContent'; 
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$modelId}:{$apiMethod}?key=" . urlencode($apiKey);

        $payload = [];

        switch ($modelId) {
            case 'gemini-3-pro-preview':
                // Configuration: Thinking Level HIGH, Google Search Tools
                $payload = [
                    "contents" => [
                        [
                            "role" => "user",
                            "parts" => $parts
                        ]
                    ],
                    "generationConfig" => [
                        "thinkingConfig" => [
                            "thinkingLevel" => "HIGH",
                        ],
                    ],
                    "tools" => [
                        [
                            "googleSearch" => new \stdClass() // Empty object for JSON
                        ]
                    ],
                ];
                break;

            case 'gemini-2.5-pro':
            case 'gemini-flash-latest':
            //case 'gemini-flash-lite-latest':
            case 'gemini-2.5-flash':
            //case 'gemini-2.5-flash-lite':
                // Configuration: Thinking Budget specific, Google Search Tools
                $payload = [
                    "contents" => [
                        [
                            "role" => "user",
                            "parts" => $parts
                        ]
                    ],
                    "generationConfig" => [
                        "thinkingConfig" => [
                            "thinkingBudget" => -1,
                        ],
                    ],
                    "tools" => [
                        [
                            "googleSearch" => new \stdClass()
                        ]
                    ],
                ];
                break;

            case 'gemini-2.0-flash':
            case 'gemini-2.0-flash-lite':
                // Configuration: Standard Flash configuration, Google Search Tools
                $payload = [
                    "contents" => [
                        [
                            "role" => "user",
                            "parts" => $parts
                        ]
                    ],
                    "generationConfig" => [
                        // Flash models typically don't use thinkingConfig, standard parameters apply
                    ],
                    "tools" => [
                        [
                            "googleSearch" => new \stdClass()
                        ]
                    ],
                ];
                break;

            default:
                // Fallback / Deprecated / Generic Configuration for older models
                $payload = [
                    "contents" => [
                        [
                            "role" => "user",
                            "parts" => $parts
                        ]
                    ],
                    "generationConfig" => [
                        "maxOutputTokens" => 64192,
                    ],
                    "tools" => [
                        [
                            "googleSearch" => new \stdClass()
                        ]
                    ],
                ];
                break;
        }

        return [
            'url'  => $url,
            'body' => json_encode($payload)
        ];
    }
}