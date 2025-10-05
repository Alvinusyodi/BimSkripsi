<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pertemuan extends Model
{

    protected $fillable = [
        'bimbingan_id',
        'topik',
        'isi',
        'tanggal',
        'komentar',
    ];    
}
