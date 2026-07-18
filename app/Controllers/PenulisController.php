<?php
namespace App\Controllers;

use App\Models\BukuModel;
use App\Models\PenulisModel;
use CodeIgniter\RESTful\ResourceController;

class PenulisController extends ResourceController
{
    protected $modelName = PenulisModel::class;
    protected $format    = 'json';

    public function index()
    {
        return $this->respond($this->model->findAll());
    }

    public function show($id = null)
    {
        $penulis = $this->model->find($id);
        if (!$penulis) return $this->failNotFound('Penulis tidak ditemukan');
        return $this->respond($penulis);
    }

    public function create()
    {
        $data = $this->request->getJSON(true) ?? [];
        if (empty($data['nama'])) {
            return $this->fail('Nama penulis wajib diisi', 400);
        }
        $this->model->insert($data);
        $data['id'] = $this->model->getInsertID();
        return $this->respondCreated($data);
    }

    public function update($id = null)
    {
        if (!$this->model->find($id)) {
            return $this->failNotFound('Penulis tidak ditemukan');
        }
        $data = $this->request->getJSON(true) ?? [];
        if (empty($data['nama'])) {
            return $this->fail('Nama penulis wajib diisi', 400);
        }
        $this->model->update($id, $data);
        return $this->respond($data);
    }

    public function delete($id = null)
    {
        if (!$this->model->find($id)) {
            return $this->failNotFound('Penulis tidak ditemukan');
        }

        // Sama seperti kategori: FK buku.penulis_id ON DELETE CASCADE akan
        // menghapus diam-diam semua buku terkait kalau tidak dicegah di sini.
        $jumlahBuku = (new BukuModel())->where('penulis_id', $id)->countAllResults();
        if ($jumlahBuku > 0) {
            return $this->fail("Penulis masih dipakai oleh {$jumlahBuku} buku, pindahkan atau hapus buku tersebut terlebih dahulu", 409);
        }

        $this->model->delete($id);
        return $this->respondDeleted(['id' => $id]);
    }
}