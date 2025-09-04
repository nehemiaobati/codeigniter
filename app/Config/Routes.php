<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', static function () {
    if (session()->get('isLoggedIn')) {
        return redirect()->to('/dashboard');
    }

    return redirect()->to('/login');
});

$routes->get('register', 'Auth::register');
$routes->post('register/store', 'Auth::store');
$routes->get('login', 'Auth::login');
$routes->post('login/authenticate', 'Auth::authenticate');
$routes->get('logout', 'Auth::logout');
$routes->get('dashboard', 'Home::index'); // Placeholder for dashboard
