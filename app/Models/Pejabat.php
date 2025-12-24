<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pejabat extends Model
{
    use HasFactory;

    protected $table = 'Pejabat';
    protected $primaryKey = 'Id_Pejabat';
    public $timestamps = false;

    protected $fillable = [
        'Id_Pejabat',
        'Nama_Jabatan',
    ];
}
