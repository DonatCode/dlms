<?php
namespace App\Controllers;

use App\Models\BukuModel;
use CodeIgniter\RESTful\ResourceController;

class BukuController extends ResourceController
{
    protected $modelName = BukuModel::class;
    protected $format    = 'json';

    public function index()
    {
        return $this->respond($this->model->findAll());
    }

    public function show($id = null)
    {
        $buku = $this->model->find($id);
        if (!$buku) return $this->failNotFound('Buku tidak ditemukan');
        return $this->respond($buku);
    }

    public function create()
    {
        $judul        = $this->request->getPost('judul');
        $deskripsi    = $this->request->getPost('deskripsi');
        $kategori_id  = $this->request->getPost('kategori_id');
        $penulis_id   = $this->request->getPost('penulis_id');
        $tahun_terbit = $this->request->getPost('tahun_terbit');

        if (empty($judul) || empty($kategori_id) || empty($penulis_id)) {
            return $this->fail('Judul, kategori_id, dan penulis_id wajib diisi', 400);
        }

        $coverFile = $this->request->getFile('cover');
        $pdfFile   = $this->request->getFile('file_pdf');

        $coverName = null;
        $pdfName   = null;

        if ($coverFile && $coverFile->isValid() && !$coverFile->hasMoved()) {
            $coverName = $coverFile->getRandomName();
            $coverFile->move(FCPATH . 'uploads/covers', $coverName);
        }

        if ($pdfFile && $pdfFile->isValid() && !$pdfFile->hasMoved()) {
            $pdfName = $pdfFile->getRandomName();
            $pdfFile->move(FCPATH . 'uploads/pdf', $pdfName);
        }

        $data = [
            'judul'        => $judul,
            'deskripsi'    => $deskripsi,
            'kategori_id'  => $kategori_id,
            'penulis_id'   => $penulis_id,
            'tahun_terbit' => $tahun_terbit,
            'cover'        => $coverName,
            'file_pdf'     => $pdfName,
        ];

        $this->model->insert($data);
        return $this->respondCreated($data);
    }

    public function update($id = null)
    {
        if (!$this->model->find($id)) {
            return $this->failNotFound('Buku tidak ditemukan');
        }

        $data = $this->request->getPost(['judul', 'deskripsi', 'kategori_id', 'penulis_id', 'tahun_terbit']);

        $coverFile = $this->request->getFile('cover');
        if ($coverFile && $coverFile->isValid() && !$coverFile->hasMoved()) {
            $coverName = $coverFile->getRandomName();
            $coverFile->move(FCPATH . 'uploads/covers', $coverName);
            $data['cover'] = $coverName;
        }

        $pdfFile = $this->request->getFile('file_pdf');
        if ($pdfFile && $pdfFile->isValid() && !$pdfFile->hasMoved()) {
            $pdfName = $pdfFile->getRandomName();
            $pdfFile->move(FCPATH . 'uploads/pdf', $pdfName);
            $data['file_pdf'] = $pdfName;
        }

        $this->model->update($id, $data);
        return $this->respond($data);
    }

    public function delete($id = null)
    {
        $buku = $this->model->find($id);
        if (!$buku) return $this->failNotFound('Buku tidak ditemukan');

        if (!empty($buku['cover']) && file_exists(FCPATH . 'uploads/covers/' . $buku['cover'])) {
            unlink(FCPATH . 'uploads/covers/' . $buku['cover']);
        }
        if (!empty($buku['file_pdf']) && file_exists(FCPATH . 'uploads/pdf/' . $buku['file_pdf'])) {
            unlink(FCPATH . 'uploads/pdf/' . $buku['file_pdf']);
        }

        $this->model->delete($id);
        return $this->respondDeleted(['id' => $id]);
    }
}