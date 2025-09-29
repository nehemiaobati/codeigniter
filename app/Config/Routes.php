<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

//--------------------------------------------------------------------
// Public Routes (No Authentication Required)
//--------------------------------------------------------------------
// These routes are accessible to everyone.
$routes->group('', static function ($routes) {
    // Home & Welcome Page
    $routes->get('/', 'Home::landing', ['as' => 'welcome']);

    // Authentication Routes
    $routes->get('register', 'Auth::register', ['as' => 'register']);
    $routes->post('register/store', 'Auth::store', ['as' => 'register.store']);
    $routes->get('login', 'Auth::login', ['as' => 'login']);
    $routes->post('login/authenticate', 'Auth::authenticate', ['as' => 'login.authenticate']);
    $routes->get('logout', 'Auth::logout', ['as' => 'logout']); // Moved logout here as it's often accessible before full auth

    // Contact Routes
    $routes->get('contact', 'Contact::form', ['as' => 'contact.form']);
    $routes->post('contact/send', 'Contact::send', ['as' => 'contact.send']);

    // Portfolio Routes
    $routes->get('portfolio', 'Portfolio::index', ['as' => 'portfolio.index']);
    $routes->post('portfolio/send', 'Portfolio::sendEmail', ['as' => 'portfolio.sendEmail']);

    // Legal Routes
    $routes->get('terms', 'Home::terms', ['as' => 'terms']);
    $routes->get('privacy', 'Home::privacy', ['as' => 'privacy']);
});

//--------------------------------------------------------------------
// Authenticated User Routes
//--------------------------------------------------------------------
// These routes require the user to be logged in.
$routes->group('', ['filter' => 'auth'], static function ($routes) {
    // Dashboard/Home for logged-in users
    $routes->get('home', 'Home::index', ['as' => 'home']);

    // Admin Panel Routes
    $routes->group('admin', static function ($routes) {
        $routes->get('/', 'Admin::index', ['as' => 'admin.index']);
        $routes->get('users/(:num)', 'Admin::show/$1', ['as' => 'admin.users.show']);
        $routes->post('users/update_balance/(:num)', 'Admin::updateBalance/$1', ['as' => 'admin.users.update_balance']);
        $routes->post('admin/users/delete/(:num)', 'Admin::delete/$1', ['as' => 'admin.users.delete']); // Corrected path to 'admin/users/delete'
    });

    // Payment Routes
    $routes->group('payment', static function ($routes) {
        $routes->get('/', 'Payments::index', ['as' => 'payment.index']);
        //$routes->get('initiate', 'Payments::initiate', ['as' => 'payment.initiate']); // Added GET route
        $routes->post('initiate', 'Payments::initiate', ['as' => 'payment.initiate']);
        $routes->get('verify', 'Payments::verify', ['as' => 'payment.verify']);
    });

    // Crypto Routes (with balance filter)
    $routes->group('crypto', ['filter' => 'balance'], static function ($routes) {
        $routes->get('/', 'Crypto::index', ['as' => 'crypto.index']);
        $routes->post('query', 'Crypto::query', ['as' => 'crypto.query']);
    });
});
