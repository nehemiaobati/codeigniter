<?php

namespace App\Modules\Ollama\Config;

use CodeIgniter\Config\BaseConfig;

class Ollama extends BaseConfig
{
    /**
     * The base URL for the Ollama instance.
     * Default: http://localhost:11434
     */
    public string $baseUrl = 'http://localhost:11434';

    /**
     * The default model to use for generation.
     * Default: llama3
     */
    public string $defaultModel = 'llama3';

    /**
     * Request timeout in seconds.
     * Default: 120
     */
    public int $timeout = 120;
}
