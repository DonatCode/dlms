<?php

namespace App\Models;

use CodeIgniter\Model;

class BookmarkModel extends Model
{
    protected $table            = 'bookmark';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['user_id', 'buku_id'];
    protected $useTimestamps    = false;
    protected $returnType       = 'array';
}
