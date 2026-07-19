<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class KategoriSeeder extends Seeder
{
    public function run()
    {
    $data = [
        ['nama' => 'Teknologi'],
        ['nama' => 'Novel'],
        ['nama' => 'Pendidikan'],
        ['nama' => 'Agama'],
        ['nama' => 'Sejarah'],
    ];

    $this->db->table('kategori')->insertBatch($data);
    }
}
