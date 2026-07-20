<?php

namespace App\Controllers;

/**
 * Sama seperti AdminController: hanya menyajikan cangkang HTML. Proteksi
 * client-side dilakukan oleh siteRequireLogin() di site.js (redirect ke
 * /login kalau tidak ada token), sementara proteksi sesungguhnya ada di
 * filter 'jwt' pada endpoint api/riwayat-unduh, api/buku-saya, api/bookmark,
 * dan api/profile.
 */
class AccountController extends BaseController
{
    public function dashboard()
    {
        return view('account/dashboard');
    }

    public function bukuSaya()
    {
        return view('account/buku-saya');
    }

    public function bookmark()
    {
        return view('account/bookmark');
    }

    public function riwayatUnduh()
    {
        return view('account/riwayat-unduh');
    }
}
