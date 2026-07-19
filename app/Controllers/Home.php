<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        // Sebelumnya route '/' menampilkan halaman welcome bawaan CI4
        // (app/Views/welcome_message.php). Sekarang diganti menjadi
        // halaman Beranda perpustakaan yang sebenarnya.
        return view('site/beranda');
    }
}
