<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jurusan extends Model
{
    use HasFactory;

    protected $table = 'Jurusan';
    protected $primaryKey = 'Id_Jurusan';

    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'Id_Jurusan',
        'Nama_Jurusan',
        'Id_Fakultas',
    ];

    /**
     * Relasi ke Fakultas
     */
    public function fakultas()
    {
        return $this->belongsTo(Fakultas::class, 'Id_Fakultas', 'Id_Fakultas');
    }

    /**
     * Relasi ke Prodi
     */
    public function prodi()
    {
        return $this->hasMany(Prodi::class, 'Id_Jurusan', 'Id_Jurusan');
    }
}
