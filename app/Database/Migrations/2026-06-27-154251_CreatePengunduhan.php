<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePengunduhan extends Migration
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
        'user_id' => [
            'type'       => 'INT',
            'constraint' => 11,
            'unsigned'   => true,
        ],
        'buku_id' => [
            'type'       => 'INT',
            'constraint' => 11,
            'unsigned'   => true,
        ],
        'tanggal_unduh' => [
            'type' => 'DATETIME',
        ],
    ]);

    $this->forge->addKey('id', true);

    $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
    $this->forge->addForeignKey('buku_id', 'buku', 'id', 'CASCADE', 'CASCADE');

    $this->forge->createTable('pengunduhan');
    }

    public function down()
    {
    $this->forge->dropTable('pengunduhan');
    }
}
