<?php
namespace App\Controllers;

use App\Models\BukuModel;
use App\Models\KategoriModel;
use App\Models\PenulisModel;
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

        // Sebelumnya kategori_id/penulis_id langsung dipakai tanpa dicek keberadaannya,
        // sehingga id yang tidak valid akan lolos ke query INSERT dan baru gagal di level
        // database dengan pesan error SQL mentah (bocor detail internal, bukan JSON rapi).
        if (!(new KategoriModel())->find($kategori_id)) {
            return $this->fail('kategori_id tidak ditemukan', 400);
        }
        if (!(new PenulisModel())->find($penulis_id)) {
            return $this->fail('penulis_id tidak ditemukan', 400);
        }

        $coverFile = $this->request->getFile('cover');
        $pdfFile   = $this->request->getFile('file_pdf');

        // Kolom `cover` dan `file_pdf` di migrasi bersifat NOT NULL, jadi kedua file
        // wajib diunggah saat membuat buku baru, kalau tidak INSERT akan gagal di DB.
        if (!$coverFile || !$coverFile->isValid()) {
            return $this->fail('File cover wajib diunggah', 400);
        }
        if (!$pdfFile || !$pdfFile->isValid()) {
            return $this->fail('File PDF wajib diunggah', 400);
        }

        $coverName = $coverFile->getRandomName();
        $coverFile->move(FCPATH . 'uploads/covers', $coverName);

        $pdfName = $pdfFile->getRandomName();
        $pdfFile->move(FCPATH . 'uploads/pdf', $pdfName);

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
        $data['id'] = $this->model->getInsertID();
        return $this->respondCreated($data);
    }

    public function update($id = null)
    {
        $existing = $this->model->find($id);
        if (!$existing) {
            return $this->failNotFound('Buku tidak ditemukan');
        }

        $data = $this->request->getPost(['judul', 'deskripsi', 'kategori_id', 'penulis_id', 'tahun_terbit']);

        // BUG lama: getPost() mengembalikan null untuk key yang tidak dikirim form,
        // lalu null itu ikut di-update ke DB sehingga data lama bisa tertimpa kosong
        // hanya karena admin lupa mengisi salah satu field di form edit.
        // Solusi: buang key yang nilainya null (field yang memang tidak dikirim),
        // supaya update bersifat parsial dan tidak menghapus data yang sudah ada.
        $data = array_filter($data, fn ($v) => $v !== null);

        if (isset($data['kategori_id']) && !(new KategoriModel())->find($data['kategori_id'])) {
            return $this->fail('kategori_id tidak ditemukan', 400);
        }
        if (isset($data['penulis_id']) && !(new PenulisModel())->find($data['penulis_id'])) {
            return $this->fail('penulis_id tidak ditemukan', 400);
        }

        $coverFile = $this->request->getFile('cover');
        if ($coverFile && $coverFile->isValid() && !$coverFile->hasMoved()) {
            $coverName = $coverFile->getRandomName();
            $coverFile->move(FCPATH . 'uploads/covers', $coverName);
            $data['cover'] = $coverName;

            // File lama tidak pernah dihapus saat diganti -> sampah menumpuk di server.
            if (!empty($existing['cover']) && file_exists(FCPATH . 'uploads/covers/' . $existing['cover'])) {
                unlink(FCPATH . 'uploads/covers/' . $existing['cover']);
            }
        }

        $pdfFile = $this->request->getFile('file_pdf');
        if ($pdfFile && $pdfFile->isValid() && !$pdfFile->hasMoved()) {
            $pdfName = $pdfFile->getRandomName();
            $pdfFile->move(FCPATH . 'uploads/pdf', $pdfName);
            $data['file_pdf'] = $pdfName;

            if (!empty($existing['file_pdf']) && file_exists(FCPATH . 'uploads/pdf/' . $existing['file_pdf'])) {
                unlink(FCPATH . 'uploads/pdf/' . $existing['file_pdf']);
            }
        }

        if (empty($data)) {
            return $this->fail('Tidak ada data untuk diperbarui', 400);
        }

        $this->model->update($id, $data);
        return $this->respond(array_merge($existing, $data));
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