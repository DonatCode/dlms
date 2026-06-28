<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PenulisSeeder extends Seeder
{
    public function run()
    {
    $data = [
        ['nama' => 'Abdul Kadir'],
        ['nama' => 'Rosa A.S'],
        ['nama' => 'Budi Raharjo'],
        ['nama' => 'Andrea Hirata'],
        ['nama' => 'Tere Liye'],
    ];

    $this->db->table('penulis')->insertBatch($data);
    }
}
