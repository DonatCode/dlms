<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBuku extends Migration
{
    public function up()
    {
    $this->forge->addField([
        'id' => [
            'type'           => 'INT',
            'constraint'     => 11,
            'unsigned'       => true,
            'auto_increment' => true,
        ],
        'kategori_id' => [
            'type'       => 'INT',
            'constraint' => 11,
            'unsigned'   => true,
        ],
        'penulis_id' => [
            'type'       => 'INT',
            'constraint' => 11,
            'unsigned'   => true,
        ],
        'judul' => [
            'type'       => 'VARCHAR',
            'constraint' => 255,
        ],
        'deskripsi' => [
            'type' => 'TEXT',
            'null' => true,
        ],
        'cover' => [
            'type'       => 'VARCHAR',
            'constraint' => 255,
        ],
        'file_pdf' => [
            'type'       => 'VARCHAR',
            'constraint' => 255,
        ],
        'tahun_terbit' => [
            'type' => 'YEAR',
        ],
        'created_at' => [
            'type' => 'DATETIME',
            'null' => true,
        ],
        'updated_at' => [
            'type' => 'DATETIME',
            'null' => true,
        ],
    ]);

    $this->forge->addKey('id', true);

    $this->forge->addForeignKey('kategori_id', 'kategori', 'id', 'CASCADE', 'CASCADE');
    $this->forge->addForeignKey('penulis_id', 'penulis', 'id', 'CASCADE', 'CASCADE');

    $this->forge->createTable('buku');
    }

    public function down()
    {
    $this->forge->dropTable('buku');
    }
}
