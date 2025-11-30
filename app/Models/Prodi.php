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

    protected $fillable = [
        'Id_Prodi',
        'Nama_Prodi',
        'Id_Jurusan',
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
     * Relasi ke Jurusan
     */
    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class, 'Id_Jurusan', 'Id_Jurusan');
    }

    /**
     * Relasi ke Mahasiswa
     */
    public function mahasiswa()
    {
        return $this->hasMany(Mahasiswa::class, 'Id_Prodi', 'Id_Prodi');
    }

    /**
     * Relasi ke Dosen
     */
    public function dosen()
    {
        return $this->hasMany(Dosen::class, 'Id_Prodi', 'Id_Prodi');
    }

    /**
     * Relasi ke Pegawai Prodi
     */
    public function pegawai()
    {
        return $this->hasMany(Pegawai::class, 'Id_Prodi', 'Id_Prodi');
    }
}