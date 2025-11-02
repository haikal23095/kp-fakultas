<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisPekerjaan extends Model
{
    use HasFactory;
    protected $table = 'Jenis_Pekerjaan';
    protected $primaryKey = 'Id_Jenis_Pekerjaan';
    public $timestamps = false;
}