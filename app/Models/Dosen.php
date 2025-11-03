<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dosen extends Model
{
    use HasFactory;

    // Beri tahu Laravel nama tabel & primary key Anda
    protected $table = 'Dosen';
    protected $primaryKey = 'Id_Dosen';

    // Matikan fitur-jfitur default Laravel yang tidak Anda gunakan
    public $incrementing = false;
    public $timestamps = false;
}