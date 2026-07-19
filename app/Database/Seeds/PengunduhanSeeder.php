<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PengunduhanSeeder extends Seeder
{
    public function run()
    {
    $data = [
        [
            'user_id' => 2,
            'buku_id' => 1,
            'tanggal_unduh' => date('Y-m-d H:i:s'),
        ],
        [
            'user_id' => 2,
            'buku_id' => 2,
            'tanggal_unduh' => date('Y-m-d H:i:s'),
        ]
    ];

    $this->db->table('pengunduhan')->insertBatch($data);
    }
}
