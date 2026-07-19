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
$routes->group('api/kategori', ['filter' => ['jwt', 'admin']], function ($routes) {
    $routes->post('/', 'KategoriController::create');
    $routes->put('(:num)', 'KategoriController::update/$1');
    $routes->delete('(:num)', 'KategoriController::delete/$1');
});

// Penulis
$routes->get('api/penulis', 'PenulisController::index');
$routes->get('api/penulis/(:num)', 'PenulisController::show/$1');
$routes->group('api/penulis', ['filter' => ['jwt', 'admin']], function ($routes) {
    $routes->post('/', 'PenulisController::create');
    $routes->put('(:num)', 'PenulisController::update/$1');
    $routes->delete('(:num)', 'PenulisController::delete/$1');
});

// Buku
$routes->get('api/buku', 'BukuController::index');
$routes->get('api/buku/(:num)', 'BukuController::show/$1');
$routes->group('api/buku', ['filter' => ['jwt', 'admin']], function ($routes) {
    $routes->post('/', 'BukuController::create');
    $routes->post('(:num)', 'BukuController::update/$1');
    $routes->delete('(:num)', 'BukuController::delete/$1');
});
// Unduh PDF buku: siapa saja yang sudah LOGIN (bukan cuma admin) boleh mengunduh,
// jadi sengaja hanya filter 'jwt' saja, tanpa 'admin'.
$routes->group('api/buku', ['filter' => 'jwt'], function ($routes) {
    $routes->get('(:num)/download', 'BukuController::download/$1');
});

// Bookmark (butuh login, role apa saja)
$routes->group('api/bookmark', ['filter' => 'jwt'], function ($routes) {
    $routes->get('/', 'BookmarkController::index');
    $routes->post('/', 'BookmarkController::create');
    $routes->delete('(:num)', 'BookmarkController::delete/$1');
});

// Riwayat unduh & koleksi "Buku Saya" (butuh login, role apa saja)
$routes->group('api', ['filter' => 'jwt'], function ($routes) {
    $routes->get('riwayat-unduh', 'PengunduhanController::riwayat');
    $routes->get('buku-saya', 'PengunduhanController::koleksi');
});

// Admin panel (halaman HTML). Halaman ini hanya "cangkang" tampilan;
// proteksi sebenarnya tetap di filter jwt+admin pada endpoint api/* di atas,
// dan dibantu pengecekan token di sisi client (lihat app/Views/admin/layout.php)
$routes->get('admin', 'AdminController::dashboard');
$routes->get('admin/login', 'AdminController::login');
$routes->get('admin/kategori', 'AdminController::kategori');
$routes->get('admin/penulis', 'AdminController::penulis');
$routes->get('admin/buku', 'AdminController::buku');

// ==== Halaman publik (bisa diakses tanpa login) ====
// Home::index diganti isinya jadi halaman Beranda (bukan welcome page default CI4 lagi).
$routes->get('koleksi', 'SiteController::koleksi');
$routes->get('buku/(:num)', 'SiteController::detail/$1');
$routes->get('buku/(:num)/baca', 'SiteController::baca/$1');
$routes->get('kategori', 'SiteController::kategori');
$routes->get('penulis', 'SiteController::penulis');
$routes->get('login', 'SiteController::login');
$routes->get('register', 'SiteController::register');

// ==== Halaman akun (perlu login; proteksi via token di client + filter jwt di API) ====
$routes->get('dashboard', 'AccountController::dashboard');
$routes->get('buku-saya', 'AccountController::bukuSaya');
$routes->get('bookmark', 'AccountController::bookmark');
$routes->get('riwayat-unduh', 'AccountController::riwayatUnduh');