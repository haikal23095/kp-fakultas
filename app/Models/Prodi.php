<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prodi extends Model
{
    use HasFactory;

    // Beri tahu Laravel nama tabel & primary key Anda
    protected $table = 'Prodi';
    protected $primaryKey = 'Id_Prodi';

    // Matikan fitur-fitur default Laravel yang tidak Anda gunakan
    public $incrementing = false;
    public $timestamps = false;
}