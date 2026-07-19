<?php

namespace App\Controllers;

use App\Models\BukuModel;
use App\Models\PengunduhanModel;
use CodeIgniter\RESTful\ResourceController;

/**
 * Endpoint terkait pengunduhan buku milik user yang sedang login.
 * Semua method di sini WAJIB dipasangi filter 'jwt' di Routes.php,
 * karena datanya spesifik per user ($this->request->user->id).
 */
class PengunduhanController extends ResourceController
{
    protected $modelName = PengunduhanModel::class;
    protected $format     = 'json';

    /**
     * GET /api/riwayat-unduh
     * Log lengkap setiap kali user mengunduh (bisa ada judul yang sama
     * berkali-kali kalau diunduh ulang), diurutkan dari yang terbaru.
     */
    public function riwayat()
    {
        $userId = $this->request->user->id;

        $data = $this->model
            ->select('pengunduhan.id, pengunduhan.tanggal_unduh, buku.id as buku_id, buku.judul, buku.cover, penulis.nama as penulis')
            ->join('buku', 'buku.id = pengunduhan.buku_id')
            ->join('penulis', 'penulis.id = buku.penulis_id')
            ->where('pengunduhan.user_id', $userId)
            ->orderBy('pengunduhan.tanggal_unduh', 'DESC')
            ->findAll();

        return $this->respond($data);
    }

    /**
     * GET /api/buku-saya
     * Daftar buku UNIK yang pernah diunduh user ini (dedup by buku_id),
     * dipakai untuk halaman "Buku Saya" (koleksi pribadi).
     */
    public function koleksi()
    {
        $userId = $this->request->user->id;

        $bukuIds = $this->model
            ->select('buku_id')
            ->where('user_id', $userId)
            ->groupBy('buku_id')
            ->findColumn('buku_id');

        if (empty($bukuIds)) {
            return $this->respond([]);
        }

        $data = (new BukuModel())->whereIn('id', $bukuIds)->findAll();
        return $this->respond($data);
    }
}
