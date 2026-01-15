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

    // Matikan fitur-fitur default Laravel yang tidak Anda gunakan
    public $incrementing = false;
    public $timestamps = false;

    // Kolom yang bisa diisi (mass assignment)
    protected $fillable = [
        'Id_Dosen',
        'NIP',
        'Nama_Dosen',
        'Alamat_Dosen',
        'Id_User',
        'Id_Prodi',
        'Id_Fakultas',
        'Id_Pejabat'
    ];

    // Relasi ke Fakultas
    public function fakultas()
    {
        return $this->belongsTo(Fakultas::class, 'Id_Fakultas', 'Id_Fakultas');
    }

    // Relasi ke Prodi
    public function prodi()
    {
        return $this->belongsTo(Prodi::class, 'Id_Prodi', 'Id_Prodi');
    }

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class, 'Id_User', 'Id_User');
    }

    // Relasi ke Pejabat (misal Dekan, Kajur, dsb.)
    public function pejabat()
    {
        return $this->belongsTo(Pejabat::class, 'Id_Pejabat', 'Id_Pejabat');
    }

    // Relasi ke Acc_SK_Dosen_Wali sebagai Dekan yang menyetujui
    public function accSkDosenWaliAsDekan()
    {
        return $this->hasMany(AccDekanDosenWali::class, 'Id_Dekan', 'Id_Dosen');
    }

    // Relasi ke Acc_SK_Beban_Mengajar sebagai Dekan yang menyetujui
    public function accSkBebanMengajarAsDekan()
    {
        return $this->hasMany(AccSKBebanMengajar::class, 'Id_Dekan', 'Id_Dosen');
    }
}