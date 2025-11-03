<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    use HasFactory;

    /**
     * Nama tabel di database Anda adalah 'Mahasiswa' (M besar),
     * bukan 'mahasiswas' (default Laravel).
     */
    protected $table = 'Mahasiswa';

    /**
     * Primary key tabel Anda adalah 'Id_Mahasiswa' (bukan 'id').
     */
    protected $primaryKey = 'Id_Mahasiswa';

    /**
     * Kita matikan auto-incrementing karena di DB Anda
     * 'Id_Mahasiswa' BUKAN auto_increment.
     */
    public $incrementing = false;

    /**
     * Kita matikan timestamps (created_at, updated_at)
     * karena tidak ada di tabel 'Mahasiswa' Anda.
     */
    public $timestamps = false;

    /**
     * Definisikan relasi ke tabel User
     * Ini akan menghubungkan 'Id_User' di tabel Mahasiswa
     * ke 'Id_User' di tabel Users.
     */
    public function user()
    {
        // 'Id_User' (foreign key di Mahasiswa), 'Id_User' (primary key di Users)
        return $this->belongsTo(User::class, 'Id_User', 'Id_User');
    }
    
    /**
     * Definisikan relasi ke tabel Prodi
     */
    public function prodi()
    {
        return $this->belongsTo(Prodi::class, 'Id_Prodi', 'Id_Prodi');
    }
}