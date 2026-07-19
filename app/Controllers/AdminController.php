<?php

namespace App\Controllers;

/**
 * AdminController hanya bertugas menyajikan "cangkang" halaman HTML.
 * Semua pengambilan/pengubahan data dilakukan di sisi browser lewat
 * JavaScript (public/admin/admin.js) yang memanggil REST API yang sudah
 * ada (api/kategori, api/penulis, api/buku) dengan token JWT.
 *
 * Proteksi akses admin dilakukan dua lapis:
 * 1. Client-side: admin.js mengecek token+role di localStorage, kalau
 *    tidak ada / bukan admin maka langsung redirect ke /admin/login.
 *    Ini hanya untuk kenyamanan UX (mencegah tampilan kosong berkedip).
 * 2. Server-side (yang sebenarnya menegakkan keamanan): setiap endpoint
 *    tulis (create/update/delete) di api/kategori, api/penulis, api/buku
 *    dipasangi filter ['jwt', 'admin'] di app/Config/Routes.php, jadi
 *    walaupun seseorang membuka halaman HTML admin secara langsung,
 *    dia tetap tidak akan bisa mengubah data tanpa token admin yang valid.
 */
class AdminController extends BaseController
{
    public function login()
    {
        return view('admin/login');
    }

    public function dashboard()
    {
        return view('admin/dashboard', ['active' => 'dashboard']);
    }

    public function kategori()
    {
        return view('admin/kategori', ['active' => 'kategori']);
    }

    public function penulis()
    {
        return view('admin/penulis', ['active' => 'penulis']);
    }

    public function buku()
    {
        return view('admin/buku', ['active' => 'buku']);
    }
}
