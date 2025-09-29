<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// Public Routes (no authentication required)
$routes->group('', static function ($routes) {
    $routes->get('/', 'Home::landing', ['as' => 'welcome']);
    $routes->get('register', 'Auth::register', ['as' => 'register']);
    $routes->post('register/store', 'Auth::store', ['as' => 'register.store']);
    $routes->get('login', 'Auth::login', ['as' => 'login']);
    $routes->post('login/authenticate', 'Auth::authenticate', ['as' => 'login.authenticate']);
    $routes->get('contact', 'Contact::form', ['as' => 'contact.form']);
    $routes->post('contact/send', 'Contact::send', ['as' => 'contact.send']);
    $routes->get('portfolio', 'Portfolio::index', ['as' => 'portfolio.index']);
    $routes->post('portfolio/send', 'Portfolio::sendEmail', ['as' => 'portfolio.sendEmail']);
});

// Authenticated Routes
$routes->group('', ['filter' => 'auth'], static function ($routes) {
    $routes->get('logout', 'Auth::logout', ['as' => 'logout']);
    $routes->get('home', 'Home::index', ['as' => 'home']);
    
    // Payment Routes (already grouped, but now nested under the main auth group for clarity)
    $routes->group('payment', static function ($routes) {
        $routes->get('/', 'Payments::index', ['as' => 'payment.index']);
        $routes->get('initiate', 'Payments::initiate', ['as' => 'payment.initiate']); // Added GET route
        $routes->post('initiate', 'Payments::initiate', ['as' => 'payment.initiate']);
        $routes->get('verify', 'Payments::verify', ['as' => 'payment.verify']);

});

    // Crypto Routes
    $routes->group('crypto', ['filter' => 'balance'], static function ($routes) { // Apply balance filter
        $routes->get('/', 'Crypto::index', ['as' => 'crypto.index']);
        $routes->post('query', 'Crypto::query', ['as' => 'crypto.query']);
    });
});
