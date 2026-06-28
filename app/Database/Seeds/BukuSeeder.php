<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class BukuSeeder extends Seeder
{
    public function run()
    {
    $data = [
        [
            'kategori_id'  => 1,
            'penulis_id'   => 1,
            'judul'        => 'Pemrograman PHP',
            'deskripsi'    => 'Belajar PHP dari dasar.',
            'cover'        => 'php.jpg',
            'file_pdf'     => 'php.pdf',
            'tahun_terbit' => 2024,
        ],
        [
            'kategori_id'  => 2,
            'penulis_id'   => 4,
            'judul'        => 'Laskar Pelangi',
            'deskripsi'    => 'Novel karya Andrea Hirata.',
            'cover'        => 'laskar.jpg',
            'file_pdf'     => 'laskar.pdf',
            'tahun_terbit' => 2005,
        ],
        [
            'kategori_id'  => 3,
            'penulis_id'   => 2,
            'judul'        => 'Algoritma dan Pemrograman',
            'deskripsi'    => 'Materi algoritma.',
            'cover'        => 'algo.jpg',
            'file_pdf'     => 'algo.pdf',
            'tahun_terbit' => 2023,
        ],
    ];

    $this->db->table('buku')->insertBatch($data);
    }
}
