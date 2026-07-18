<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
    $data = [
        [
            'nama'       => 'Administrator',
            'email'      => 'admin@perpustakaan.com',
            'password'   => password_hash('admin123', PASSWORD_DEFAULT),
            'role'       => 'admin',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ],
        [
            'nama'       => 'Kevin',
            'email'      => 'kevin@gmail.com',
            'password'   => password_hash('12345678', PASSWORD_DEFAULT),
            'role'       => 'user',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]
    ];

    $this->db->table('users')->insertBatch($data);
    }
}
