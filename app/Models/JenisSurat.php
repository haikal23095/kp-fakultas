<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisSurat extends Model
{
    use HasFactory;

    // Beri tahu Laravel nama tabel & primary key Anda
    protected $table = 'Jenis_Surat';
    protected $primaryKey = 'Id_Jenis_Surat';

    // Matikan fitur-fitur default Laravel yang tidak Anda gunakan
    public $incrementing = false;
    public $timestamps = false;
}