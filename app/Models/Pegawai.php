<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    protected $table = 'Pegawai';
    protected $primaryKey = 'Id_Pegawai';
    public $timestamps = false;

    protected $fillable = [
        'NIP',
        'Nama_Pegawai',
        'Jenis_Kelamin_Pegawai',
        'Alamat_Pegawai',
        'Id_User',
        'Id_Prodi',
    ];

    /**
     * Relasi ke User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'Id_User', 'Id_User');
    }

    /**
     * Relasi ke Prodi
     */
    public function prodi()
    {
        return $this->belongsTo(Prodi::class, 'Id_Prodi', 'Id_Prodi');
    }
}
