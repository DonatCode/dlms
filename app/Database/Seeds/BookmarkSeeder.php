<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class BookmarkSeeder extends Seeder
{
    public function run()
    {
    $data = [
        [
            'user_id' => 2,
            'buku_id' => 1,
        ],
        [
            'user_id' => 2,
            'buku_id' => 3,
        ]
    ];

    $this->db->table('bookmark')->insertBatch($data);
    }
}
