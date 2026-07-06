<?php
use CodeIgniter\Router\RouteCollection;
/** @var RouteCollection $routes */

$routes->get('/', 'Home::index');

// Auth
$routes->post('api/register', 'AuthController::register');
$routes->post('api/login', 'AuthController::login');

$routes->group('api', ['filter' => 'jwt'], function ($routes) {
    $routes->post('logout', 'AuthController::logout');
    $routes->get('profile', 'AuthController::profile');
});

// Kategori
$routes->get('api/kategori', 'KategoriController::index');
$routes->get('api/kategori/(:num)', 'KategoriController::show/$1');
$routes->group('api/kategori', ['filter' => 'jwt'], function ($routes) {
    $routes->post('/', 'KategoriController::create');
    $routes->put('(:num)', 'KategoriController::update/$1');
    $routes->delete('(:num)', 'KategoriController::delete/$1');
});

// Penulis
$routes->get('api/penulis', 'PenulisController::index');
$routes->get('api/penulis/(:num)', 'PenulisController::show/$1');
$routes->group('api/penulis', ['filter' => 'jwt'], function ($routes) {
    $routes->post('/', 'PenulisController::create');
    $routes->put('(:num)', 'PenulisController::update/$1');
    $routes->delete('(:num)', 'PenulisController::delete/$1');
});

// Buku
$routes->get('api/buku', 'BukuController::index');
$routes->get('api/buku/(:num)', 'BukuController::show/$1');
$routes->group('api/buku', ['filter' => 'jwt'], function ($routes) {
    $routes->post('/', 'BukuController::create');
    $routes->post('(:num)', 'BukuController::update/$1');
    $routes->delete('(:num)', 'BukuController::delete/$1');
});