<?php declare(strict_types=1);

namespace App\Modules\Ollama\Libraries;

use App\Modules\Ollama\Config\Ollama;
use App\Modules\Ollama\Models\OllamaInteractionModel;
use App\Modules\Ollama\Libraries\OllamaService;

/**
 * Manages context retrieval using local vector similarity and recent history.
 */
class OllamaMemoryService
{
    private Ollama $config;
    private OllamaInteractionModel $model;
    private OllamaService $api;
    private int $userId;

    public function __construct(int $userId)
    {
        $this->userId = $userId;
        $this->config = config(Ollama::class);
        $this->model  = new OllamaInteractionModel();
        $this->api    = new OllamaService();
    }

    /**
     * Builds the message array for the API, including system prompt and retrieved context.
     */
    public function buildContext(string $userInput): array
    {
        $messages = [];
        $systemContext = "You are a helpful local AI assistant.";
        
        // 1. Generate embedding for current input
        $inputVector = $this->api->embed($userInput);

        // 2. Find semantically similar past interactions (Long Term Memory)
        if ($inputVector) {
            $relevantMemories = $this->findSimilarInteractions($inputVector);
            if (!empty($relevantMemories)) {
                $systemContext .= "\n\nRelevant Context from Memory:\n" . implode("\n", $relevantMemories);
            }
        }

        // 3. Add System Prompt
        $messages[] = ['role' => 'system', 'content' => $systemContext];

        // 4. Add Recent History (Short Term Memory)
        $recentHistory = $this->model
            ->where('user_id', $this->userId)
            ->orderBy('created_at', 'DESC')
            ->limit($this->config->historyDepth)
            ->findAll();
            
        $recentHistory = array_reverse($recentHistory); // Chronological order

        foreach ($recentHistory as $chat) {
            $messages[] = ['role' => 'user', 'content' => $chat->user_input];
            $messages[] = ['role' => 'assistant', 'content' => $chat->ai_response];
        }

        // 5. Add Current User Input
        $messages[] = ['role' => 'user', 'content' => $userInput];

        return $messages;
    }

    /**
     * Saves the interaction and its embedding to the database.
     */
    public function saveInteraction(string $input, string $response, string $modelName): void
    {
        // Embed the combined text for better context retrieval later
        $fullText = "User: $input | AI: $response";
        $embedding = $this->api->embed($fullText);
        $embeddingJson = $embedding ? json_encode($embedding) : null;        

        $this->model->insert([
            'user_id'     => $this->userId,
            'prompt_hash' => hash('sha256', $input),
            'user_input'  => $input,
            'ai_response' => $response,
            'ai_model'    => $modelName,
            'embedding'   => $embeddingJson 
        ]);
    }

    /**
     * Cosine Similarity Search logic (PHP Implementation).
     */
    private function findSimilarInteractions(array $targetVector): array
    {
        // Fetch rows with embeddings. 
        // Performance Note: For huge datasets, use a vector DB (pgvector/milvus). 
        // For local personal usage, PHP array iteration is surprisingly fast up to ~5k records.
        $candidates = $this->model
            ->where('user_id', $this->userId)
            ->where('embedding IS NOT NULL')
            ->orderBy('created_at', 'DESC')
            ->limit(100) // Optimization: Only check last 100 messages for deep context
            ->findAll();

        $results = [];

        foreach ($candidates as $candidate) {
            $score = $this->cosineSimilarity($targetVector, $candidate->embedding);
            
            if ($score >= $this->config->similarityThreshold) {
                $results[$score] = "- User asked: \"{$candidate->user_input}\". You answered: \"{$candidate->ai_response}\"";
            }
        }

        krsort($results); // Sort by highest score
        return array_slice($results, 0, 3); // Return top 3 matches
    }

    private function cosineSimilarity(array $vecA, array $vecB): float
    {
        $dot = 0.0;
        $magA = 0.0;
        $magB = 0.0;

        foreach ($vecA as $i => $val) {
            if (!isset($vecB[$i])) continue;
            $dot += $val * $vecB[$i];
            $magA += $val * $val;
            $magB += $vecB[$i] * $vecB[$i];
        }

        return ($magA * $magB) == 0 ? 0.0 : $dot / (sqrt($magA) * sqrt($magB));
    }
}
