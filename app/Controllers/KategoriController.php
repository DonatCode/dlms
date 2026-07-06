<?php
namespace App\Controllers;

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
        $data = $this->request->getJSON(true);
        if (empty($data['nama'])) {
            return $this->fail('Nama kategori wajib diisi', 400);
        }
        $this->model->insert($data);
        return $this->respondCreated($data);
    }

    public function update($id = null)
    {
        if (!$this->model->find($id)) {
            return $this->failNotFound('Kategori tidak ditemukan');
        }
        $data = $this->request->getJSON(true);
        $this->model->update($id, $data);
        return $this->respond($data);
    }

    public function delete($id = null)
    {
        if (!$this->model->find($id)) {
            return $this->failNotFound('Kategori tidak ditemukan');
        }
        $this->model->delete($id);
        return $this->respondDeleted(['id' => $id]);
    }
}