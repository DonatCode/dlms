<?php
namespace App\Controllers;

use App\Models\BukuModel;
use App\Models\KategoriModel;
use CodeIgniter\RESTful\ResourceController;

class KategoriController extends ResourceController
{
    protected $modelName = KategoriModel::class;
    protected $format    = 'json';

    public function index()
    {
        return $this->respond($this->model->findAll());
    }

    public function show($id = null)
    {
        $kategori = $this->model->find($id);
        if (!$kategori) return $this->failNotFound('Kategori tidak ditemukan');
        return $this->respond($kategori);
    }

    public function create()
    {
        // getJSON(true) mengembalikan null kalau body kosong/bukan JSON valid,
        // dan empty(null['nama']) memicu warning "trying to access array offset
        // on null" di PHP 8. Guard dulu supaya errornya jadi respons JSON yang jelas.
        $data = $this->request->getJSON(true) ?? [];
        if (empty($data['nama'])) {
            return $this->fail('Nama kategori wajib diisi', 400);
        }
        $this->model->insert($data);
        $data['id'] = $this->model->getInsertID();
        return $this->respondCreated($data);
    }

    public function update($id = null)
    {
        if (!$this->model->find($id)) {
            return $this->failNotFound('Kategori tidak ditemukan');
        }
        $data = $this->request->getJSON(true) ?? [];
        if (empty($data['nama'])) {
            return $this->fail('Nama kategori wajib diisi', 400);
        }
        $this->model->update($id, $data);
        return $this->respond($data);
    }

    public function delete($id = null)
    {
        if (!$this->model->find($id)) {
            return $this->failNotFound('Kategori tidak ditemukan');
        }

        // Migrasi buku mendefinisikan FK kategori_id dengan ON DELETE CASCADE,
        // artinya menghapus kategori akan MENGHAPUS DIAM-DIAM semua buku terkait.
        // Ini berbahaya untuk aksi klik-hapus di panel admin, jadi diblok di sini
        // dan admin diminta memindahkan/menghapus buku itu dulu secara sadar.
        $jumlahBuku = (new BukuModel())->where('kategori_id', $id)->countAllResults();
        if ($jumlahBuku > 0) {
            return $this->fail("Kategori masih dipakai oleh {$jumlahBuku} buku, pindahkan atau hapus buku tersebut terlebih dahulu", 409);
        }

        $this->model->delete($id);
        return $this->respondDeleted(['id' => $id]);
    }
}