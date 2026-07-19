<?php
namespace App\Models;
use CodeIgniter\Model;

class BukuModel extends Model
{
    protected $table = 'buku';
    protected $primaryKey = 'id';
    protected $allowedFields = ['kategori_id', 'penulis_id', 'judul', 'deskripsi', 'cover', 'file_pdf', 'tahun_terbit'];
    protected $useTimestamps = true;
}