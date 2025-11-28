<?php

namespace App\Modules\Gemini\Libraries;

use App\Models\UserModel;
use CodeIgniter\I18n\Time;

class MediaGenerationService
{
    protected $modelPayloadService;
    protected $userModel;
    protected $db;

    // Configuration for Media Models
    protected $mediaConfigs = [
        'imagen-4.0-generate-001' => [
            'type' => 'image',
            'cost' => 0.04,
            'name' => 'Imagen 4.0 Standard'
        ],
        'imagen-4.0-ultra-generate-001' => [
            'type' => 'image',
            'cost' => 0.06,
            'name' => 'Imagen 4.0 Ultra'
        ],
        'imagen-4.0-fast-generate-001' => [
            'type' => 'image',
            'cost' => 0.02,
            'name' => 'Imagen 4.0 Fast'
        ],
        'gemini-3-pro-image-preview' => [
            'type' => 'image_generation_content',
            'cost' => 0.05, // Estimate
            'name' => 'Gemini 3 Pro (Image & Text)'
        ],
        'gemini-2.5-flash-image' => [
            'type' => 'image_generation_content',
            'cost' => 0.03, // Estimate
            'name' => 'Gemini 2.5 Flash (Image & Text)'
        ],
        'veo-2.0-generate-001' => [
            'type' => 'video',
            'cost' => 0.10, // Estimated cost, adjust as needed
            'name' => 'Veo 2.0'
        ]
    ];

    public function __construct()
    {
        $this->modelPayloadService = service('modelPayloadService');
        $this->userModel = new UserModel(); // Assuming this handles user balance
        $this->db = \Config\Database::connect();
    }

    /**
     * Generates media (Image or Video) based on the model ID.
     */
    public function generateMedia(int $userId, string $prompt, string $modelId)
    {
        if (!isset($this->mediaConfigs[$modelId])) {
            return ['status' => 'error', 'message' => 'Invalid model ID.'];
        }

        $config = $this->mediaConfigs[$modelId];
        $cost = $config['cost'];

        // 1. Check Balance
        $user = $this->userModel->find($userId); // You might need to adjust this based on actual User Model

        // Convert cost to KSH if needed, assuming cost in config is USD
        // Using fixed rate from GeminiController for consistency, or just assume credits = USD/KSH
        // For now, assuming user->balance is in KSH and cost is USD.
        $usdToKsh = 129;
        $costKsh = $cost * $usdToKsh;

        if ($user->balance < $costKsh) {
            return ['status' => 'error', 'message' => 'Insufficient credits.'];
        }

        // 2. Prepare Payload
        $apiKey = getenv('GEMINI_API_KEY');
        $parts = [['text' => $prompt]];
        $payloadData = $this->modelPayloadService->getPayloadConfig($modelId, $apiKey, $parts);

        if (!$payloadData) {
            return ['status' => 'error', 'message' => 'Failed to generate payload configuration.'];
        }

        // 3. Execute Request
        $client = \Config\Services::curlrequest();

        try {
            $response = $client->post($payloadData['url'], [
                'headers' => ['Content-Type' => 'application/json'],
                'body'    => $payloadData['body'],
                'http_errors' => false // Don't throw exceptions for 4xx/5xx, handle manually
            ]);

            $httpCode = $response->getStatusCode();
            $responseBody = $response->getBody();

            if ($httpCode !== 200) {
                log_message('error', "Gemini Media API Error ({$httpCode}): " . $responseBody);
                $errData = json_decode($responseBody, true);
                $errMsg = $errData['error']['message'] ?? $responseBody;
                return ['status' => 'error', 'message' => 'API Error: ' . $errMsg];
            }

            $responseData = json_decode($responseBody, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                log_message('error', 'Gemini Media JSON Decode Error: ' . json_last_error_msg());
                return ['status' => 'error', 'message' => 'Failed to decode API response.'];
            }

            // 4. Handle Response based on Type
            if ($config['type'] === 'image') {
                return $this->handleImageResponse($userId, $modelId, $prompt, $responseData, $costKsh);
            } elseif ($config['type'] === 'video') {
                return $this->handleVideoResponse($userId, $modelId, $prompt, $responseData, $costKsh);
            } elseif ($config['type'] === 'image_generation_content') {
                return $this->handleImageGenerationContentResponse($userId, $modelId, $prompt, $responseData, $costKsh);
            }

            return ['status' => 'error', 'message' => 'Unknown media type.'];
        } catch (\Exception $e) {
            log_message('error', 'Gemini Media Exception: ' . $e->getMessage());
            return ['status' => 'error', 'message' => 'HTTP Request failed: ' . $e->getMessage()];
        }
    }

    protected function handleImageGenerationContentResponse($userId, $modelId, $prompt, $responseData, $cost)
    {
        // Response structure for generateContent with images:
        // candidates[0].content.parts[].inlineData (mimeType, data)

        if (isset($responseData['candidates'][0]['content']['parts'])) {
            $parts = $responseData['candidates'][0]['content']['parts'];
            $foundImage = false;
            $fileName = '';

            foreach ($parts as $part) {
                if (isset($part['inlineData']['data'])) {
                    $base64 = $part['inlineData']['data'];
                    $imageData = base64_decode($base64);

                    // Save to disk
                    $fileName = 'gen_' . time() . '_' . uniqid() . '.jpg'; // Assuming JPEG based on script
                    $uploadPath = WRITEPATH . 'uploads/generated/' . $userId . '/';

                    if (!is_dir($uploadPath)) {
                        mkdir($uploadPath, 0777, true);
                    }

                    if (file_put_contents($uploadPath . $fileName, $imageData) === false) {
                        log_message('error', "Failed to write generated image to: " . $uploadPath . $fileName);
                        continue;
                    }
                    $foundImage = true;
                    break; // Just handle the first image for now
                }
            }

            if ($foundImage) {
                // Deduct Balance
                $this->deductCredits($userId, $cost);

                // Record in DB
                $this->db->table('generated_media')->insert([
                    'user_id' => $userId,
                    'type' => 'image',
                    'model_id' => $modelId,
                    'prompt' => $prompt,
                    'local_path' => $fileName,
                    'status' => 'completed',
                    'cost' => $cost,
                    'created_at' => Time::now()->toDateTimeString(),
                    'updated_at' => Time::now()->toDateTimeString(),
                ]);

                return [
                    'status' => 'success',
                    'type' => 'image',
                    'url' => site_url('gemini/media/serve/' . $fileName)
                ];
            }
        }

        return ['status' => 'error', 'message' => 'No image data in response.'];
    }

    protected function handleImageResponse($userId, $modelId, $prompt, $responseData, $cost)
    {
        if (isset($responseData['predictions'][0]['bytesBase64Encoded'])) {
            $base64 = $responseData['predictions'][0]['bytesBase64Encoded'];
            $imageData = base64_decode($base64);

            // Save to disk
            $fileName = 'gen_' . time() . '_' . uniqid() . '.jpg';
            $uploadPath = WRITEPATH . 'uploads/generated/' . $userId . '/';

            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            if (file_put_contents($uploadPath . $fileName, $imageData) === false) {
                log_message('error', "Failed to write generated image to: " . $uploadPath . $fileName);
                return ['status' => 'error', 'message' => 'Failed to save generated image.'];
            }

            // Deduct Balance
            $this->deductCredits($userId, $cost);

            // Record in DB
            $this->db->table('generated_media')->insert([
                'user_id' => $userId,
                'type' => 'image',
                'model_id' => $modelId,
                'prompt' => $prompt,
                'local_path' => $fileName,
                'status' => 'completed',
                'cost' => $cost,
                'created_at' => Time::now()->toDateTimeString(),
                'updated_at' => Time::now()->toDateTimeString(),
            ]);

            return [
                'status' => 'success',
                'type' => 'image',
                'url' => site_url('gemini/media/serve/' . $fileName) // We will need a route for this
            ];
        }

        return ['status' => 'error', 'message' => 'No image data in response.'];
    }

    protected function handleVideoResponse($userId, $modelId, $prompt, $responseData, $cost)
    {
        if (isset($responseData['name'])) {
            $opName = $responseData['name']; // "projects/.../operations/..."

            // Deduct Balance (Deduct on initiation or completion? Usually initiation to prevent abuse, refund on fail)
            $this->deductCredits($userId, $cost);

            // Record in DB
            $this->db->table('generated_media')->insert([
                'user_id' => $userId,
                'type' => 'video',
                'model_id' => $modelId,
                'prompt' => $prompt,
                'remote_op_id' => $opName,
                'status' => 'pending',
                'cost' => $cost,
                'created_at' => Time::now()->toDateTimeString(),
                'updated_at' => Time::now()->toDateTimeString(),
            ]);

            return [
                'status' => 'pending',
                'type' => 'video',
                'op_id' => $opName
            ];
        }

        return ['status' => 'error', 'message' => 'No operation ID in response.'];
    }

    public function pollVideoStatus($opId)
    {
        $apiKey = getenv('GEMINI_API_KEY');
        $url = "https://generativelanguage.googleapis.com/v1beta/{$opId}?key=" . urlencode($apiKey);

        $client = \Config\Services::curlrequest();

        try {
            $response = $client->get($url, ['http_errors' => false]);
            $responseBody = $response->getBody();
            $data = json_decode($responseBody, true);

            if (isset($data['done']) && $data['done'] === true) {
                // Video is ready
                if (isset($data['response']['generatedSamples'][0]['video']['uri'])) {
                    $videoUri = $data['response']['generatedSamples'][0]['video']['uri'];

                    // Download Video
                    $downloadUrl = $videoUri . '&key=' . urlencode($apiKey);

                    // Use client to download
                    $videoResponse = $client->get($downloadUrl, ['http_errors' => false]);

                    if ($videoResponse->getStatusCode() === 200) {
                        $videoContent = $videoResponse->getBody();

                        // Get DB Record
                        $record = $this->db->table('generated_media')->where('remote_op_id', $opId)->get()->getRow();

                        if ($record) {
                            $fileName = 'vid_' . time() . '_' . uniqid() . '.mp4';
                            $uploadPath = WRITEPATH . 'uploads/generated/' . $record->user_id . '/';

                            if (!is_dir($uploadPath)) {
                                mkdir($uploadPath, 0777, true);
                            }

                            if (file_put_contents($uploadPath . $fileName, $videoContent) === false) {
                                log_message('error', "Failed to write generated video to: " . $uploadPath . $fileName);
                                return ['status' => 'failed', 'message' => 'Failed to save video file.'];
                            }

                            // Update DB
                            $this->db->table('generated_media')->where('id', $record->id)->update([
                                'status' => 'completed',
                                'local_path' => $fileName,
                                'updated_at' => Time::now()->toDateTimeString(),
                            ]);

                            return [
                                'status' => 'completed',
                                'url' => site_url('gemini/media/serve/' . $fileName)
                            ];
                        }
                    }
                } else {
                    return ['status' => 'failed', 'message' => 'Generation failed or no video URI found.'];
                }
            }

            return ['status' => 'pending'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Polling failed: ' . $e->getMessage()];
        }
    }

    protected function deductCredits($userId, $amount)
    {
        // Use UserModel to deduct balance
        $this->userModel->deductBalance($userId, $amount);
    }

    public function getMediaConfig()
    {
        return $this->mediaConfigs;
    }
}
