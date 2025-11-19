<?php declare(strict_types=1);

namespace App\Modules\Ollama\Libraries;

use App\Modules\Ollama\Config\Ollama;
use App\Modules\Ollama\Models\OllamaInteractionModel;
use App\Modules\Ollama\Models\OllamaEntityModel;
use App\Modules\Ollama\Libraries\OllamaService;
use App\Modules\Ollama\Libraries\OllamaTokenService;

class OllamaMemoryService
{
    private Ollama $config;
    private OllamaInteractionModel $interactionModel;
    private OllamaEntityModel $entityModel;
    private OllamaService $api;
    private OllamaTokenService $tokenizer;
    private int $userId;

    // Tuning Parameters
    private float $hybridAlpha = 0.5;
    private float $decayRate = 0.05;
    private float $boostRate = 0.5;

    public function __construct(int $userId)
    {
        $this->userId = $userId;
        $this->config = config(Ollama::class);
        $this->interactionModel = new OllamaInteractionModel();
        $this->entityModel = new OllamaEntityModel();
        $this->api = new OllamaService();
        $this->tokenizer = new OllamaTokenService();
    }

    public function buildContext(string $userInput): array
    {
        // 1. Get Embedding
        $inputVector = $this->api->embed($userInput);
        
        // 2. Extract Keywords
        $keywords = $this->tokenizer->processText($userInput);

        // 3. Hybrid Search
        $relevantIds = $this->performHybridSearch($inputVector, $keywords);

        // 4. Construct System Prompt with Memories
        $contextText = "";
        if (!empty($relevantIds)) {
            $memories = $this->interactionModel
                ->whereIn('id', $relevantIds)
                ->findAll();
            
            foreach ($memories as $mem) {
                $contextText .= "- [Memory]: User asked '{$mem->user_input}'. AI answered: '{$mem->ai_response}'\n";
            }
        }

        // 5. Assemble Messages
        $messages = [];
        
        $systemPrompt = "You are DeepSeek R1. " .
            "When faced with complex queries, you MUST use your internal reasoning process " .
            "wrapped in <think></think> tags before answering.\n\n" .
            "Relevant Context from Memory:\n" . ($contextText ?: "None available.");
            
        $messages[] = ['role' => 'system', 'content' => $systemPrompt];

        // 6. Add recent conversational history
        $recent = $this->interactionModel
            ->where('user_id', $this->userId)
            ->orderBy('created_at', 'DESC')
            ->limit(3)
            ->findAll();
        
        $recent = array_reverse($recent);
        foreach ($recent as $r) {
            $messages[] = ['role' => 'user', 'content' => $r->user_input];
            $messages[] = ['role' => 'assistant', 'content' => $r->ai_response];
        }

        $messages[] = ['role' => 'user', 'content' => $userInput];

        return $messages;
    }

    public function saveInteraction(string $input, string $response, string $modelName): void
    {
        // 1. Prepare Data
        $fullText = "User: $input | AI: $response";
        $embedding = $this->api->embed($fullText);
        $keywords = $this->tokenizer->processText($input);
        
        // 2. Save Interaction
        $interactionId = $this->interactionModel->insert([
            'user_id'     => $this->userId,
            'prompt_hash' => hash('sha256', $input),
            'user_input'  => $input,
            'ai_response' => $response,
            'ai_model'    => $modelName,
            'embedding'   => $embedding ? json_encode($embedding) : null,
            'keywords'    => json_encode($keywords),
            'relevance_score' => 1.0
        ]);

        // 3. Update Knowledge Graph (Entities)
        foreach ($keywords as $word) {
            $entity = $this->entityModel
                ->where('user_id', $this->userId)
                ->where('entity_key', $word)
                ->first();

            if ($entity) {
                // Update existing entity
                // FIX: Ensure mentioned_in is an array before merging
                $mentionedIn = $entity->mentioned_in ?? [];
                if (!in_array($interactionId, $mentionedIn)) {
                    $mentionedIn[] = $interactionId;
                }
                
                $this->entityModel->update($entity->id, [
                    'access_count' => $entity->access_count + 1,
                    'relevance_score' => $entity->relevance_score + $this->boostRate,
                    'mentioned_in' => json_encode($mentionedIn) // FIX: Manually JSON encode
                ]);
            } else {
                // Create new entity
                $this->entityModel->insert([
                    'user_id' => $this->userId,
                    'entity_key' => $word,
                    'name' => ucfirst($word),
                    'access_count' => 1,
                    'relevance_score' => 1.0,
                    'mentioned_in' => json_encode([$interactionId]) // FIX: Manually JSON encode
                ]);
            }
        }

        // 4. Apply Decay (The "Forgetting" Curve)
        // FIX: Use builder() to bypass Model validation rules for bulk updates
        $this->interactionModel->builder()
             ->where('user_id', $this->userId)
             ->set('relevance_score', "relevance_score - {$this->decayRate}", false)
             ->update();
    }

    private function performHybridSearch(?array $vector, array $keywords): array
    {
        $scores = [];

        // A. Vector Search
        if ($vector) {
            $candidates = $this->interactionModel
                ->where('user_id', $this->userId)
                ->where('embedding IS NOT NULL')
                ->orderBy('created_at', 'DESC')
                ->limit(50)
                ->findAll();

            foreach ($candidates as $c) {
                $dbVector = is_string($c->embedding) ? json_decode($c->embedding, true) : $c->embedding;
                if (!$dbVector) continue;

                $sim = $this->cosineSimilarity($vector, $dbVector);
                $scores[$c->id] = ($scores[$c->id] ?? 0) + ($sim * $this->hybridAlpha);
            }
        }

        // B. Keyword Search
        if (!empty($keywords)) {
            $entities = $this->entityModel
                ->where('user_id', $this->userId)
                ->whereIn('entity_key', $keywords)
                ->findAll();

            foreach ($entities as $entity) {
                $linkedInteractions = $entity->mentioned_in ?? [];
                // Handle case where mentioned_in might be returned as string depending on environment
                if (is_string($linkedInteractions)) {
                    $linkedInteractions = json_decode($linkedInteractions, true);
                }

                if (is_array($linkedInteractions)) {
                    foreach ($linkedInteractions as $intId) {
                        $scores[$intId] = ($scores[$intId] ?? 0) + ((1 - $this->hybridAlpha) * ($entity->relevance_score / 10));
                    }
                }
            }
        }

        arsort($scores);
        return array_keys(array_slice($scores, 0, 5, true));
    }

    private function cosineSimilarity(array $vecA, array $vecB): float
    {
        $dot = 0.0; $magA = 0.0; $magB = 0.0;
        foreach ($vecA as $i => $val) {
            if (!isset($vecB[$i])) continue;
            $dot += $val * $vecB[$i];
            $magA += $val * $val;
            $magB += $vecB[$i] * $vecB[$i];
        }
        return ($magA * $magB) == 0 ? 0.0 : $dot / (sqrt($magA) * sqrt($magB));
    }
}