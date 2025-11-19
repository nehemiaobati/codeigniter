<?php declare(strict_types=1);

namespace App\Modules\Ollama\Models;

use CodeIgniter\Model;

class OllamaEntityModel extends Model
{
    protected $table = 'ollama_entities';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'user_id', 'entity_key', 'name', 'access_count', 
        'relevance_score', 'mentioned_in'
    ];
    protected $useTimestamps = true;
    
}