<?php
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */


$routes->group('ollama', ['namespace' => 'App\Modules\Ollama\Controllers', 'filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'OllamaController::index', ['as' => 'ollama.index']);
    $routes->post('chat', 'OllamaController::chat', ['as' => 'ollama.chat']);
    $routes->post('clear', 'OllamaController::clearHistory', ['as' => 'ollama.clear']);
});
