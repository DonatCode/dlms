<?php

namespace App\Controllers;

use App\Models\BookmarkModel;
use App\Models\BukuModel;
use CodeIgniter\RESTful\ResourceController;

/**
 * Semua method di sini WAJIB dipasangi filter 'jwt' di Routes.php,
 * karena bookmark spesifik milik user yang sedang login.
 */
class BookmarkController extends ResourceController
{
    protected $modelName = BookmarkModel::class;
    protected $format     = 'json';

    /** GET /api/bookmark - daftar buku yang di-bookmark user ini */
    public function index()
    {
        $userId = $this->request->user->id;

        $bukuIds = $this->model->where('user_id', $userId)->findColumn('buku_id');
        if (empty($bukuIds)) {
            return $this->respond([]);
        }

        $data = (new BukuModel())->whereIn('id', $bukuIds)->findAll();
        return $this->respond($data);
    }

    /** POST /api/bookmark  body: { "buku_id": 1 } */
    public function create()
    {
        $userId = $this->request->user->id;
        $bukuId = $this->request->getJSON(true)['buku_id'] ?? null;

        if (empty($bukuId)) {
            return $this->fail('buku_id wajib diisi', 400);
        }
        if (!(new BukuModel())->find($bukuId)) {
            return $this->fail('Buku tidak ditemukan', 404);
        }

        $sudahAda = $this->model->where(['user_id' => $userId, 'buku_id' => $bukuId])->first();
        if ($sudahAda) {
            // Idempotent: kalau sudah pernah di-bookmark, jangan buat duplikat baru.
            return $this->respond($sudahAda);
        }

        $this->model->insert(['user_id' => $userId, 'buku_id' => $bukuId]);
        return $this->respondCreated(['user_id' => $userId, 'buku_id' => $bukuId]);
    }

    /** DELETE /api/bookmark/{buku_id} - hapus bookmark berdasarkan id buku (bukan id baris bookmark) */
    public function delete($bukuId = null)
    {
        $userId = $this->request->user->id;

        $row = $this->model->where(['user_id' => $userId, 'buku_id' => $bukuId])->first();
        if (!$row) {
            return $this->failNotFound('Bookmark tidak ditemukan');
        }

        $this->model->delete($row['id']);
        return $this->respondDeleted(['buku_id' => $bukuId]);
    }
}
