<?php

namespace App\Controllers;

/**
 * Semua method di sini hanya menyajikan "cangkang" HTML. Data buku/kategori/
 * penulis diambil di sisi browser lewat JS (public/assets/site/site.js) yang
 * memanggil endpoint GET api/buku, api/kategori, api/penulis — endpoint ini
 * memang publik (tanpa filter jwt), jadi bisa diakses tanpa login sama sekali.
 *
 * Mode "tanpa login" hanya bisa menjelajah/membaca (browse & baca online di
 * /buku/{id}/baca). Tombol "Unduh PDF" tetap tampil, tapi kalau diklik tanpa
 * token login, site.js akan mengarahkan ke halaman /login terlebih dahulu
 * (lihat requireLoginOrRedirect() di site.js) — penegakan sebenarnya tetap
 * di server, karena endpoint api/buku/{id}/download dipasangi filter 'jwt'.
 */
class SiteController extends BaseController
{
    public function koleksi()
    {
        return view('site/koleksi');
    }

    public function detail($id)
    {
        return view('site/detail-buku', ['id' => (int) $id]);
    }

    public function baca($id)
    {
        return view('site/baca-buku', ['id' => (int) $id]);
    }

    public function kategori()
    {
        return view('site/kategori');
    }

    public function penulis()
    {
        return view('site/penulis');
    }

    public function login()
    {
        return view('site/login');
    }

    public function register()
    {
        return view('site/register');
    }
}
