<?php

namespace App\Models;

use CodeIgniter\Model;

class PengunduhanModel extends Model
{
    protected $table            = 'pengunduhan';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['user_id', 'buku_id', 'tanggal_unduh'];
    protected $useTimestamps    = false;
    protected $returnType       = 'array';
}
