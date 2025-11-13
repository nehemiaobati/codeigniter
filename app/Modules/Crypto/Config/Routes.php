<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Public Routes for Crypto Module
// Use the namespace option for the group.
$routes->group('', ['namespace' => 'App\Modules\Crypto\Controllers'], static function ($routes) {
    $routes->get('crypto-query', 'CryptoController::publicPage', ['as' => 'crypto.public']);
});

// Authenticated User Routes for Crypto Module
// Use the namespace option for the group.
$routes->group('crypto', ['namespace' => 'App\Modules\Crypto\Controllers', 'filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'CryptoController::index', ['as' => 'crypto.index']);
    $routes->post('query', 'CryptoController::query', ['as' => 'crypto.query', 'filter' => 'balance']);
});
