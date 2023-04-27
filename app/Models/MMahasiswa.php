<?php

namespace App\Models;

use CodeIgniter\Model;

class MMahasiswa extends Model
{
    protected $table            = 'mahasiswa';
    protected $primaryKey       = 'nim';
    protected $allowedFields    = [
        'nim',
        'nama',
        'ets',
        'eas',
        'final',
    ];
}
