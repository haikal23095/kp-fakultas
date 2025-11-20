<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PegawaiFakultas extends Model
{
    protected $table = 'Pegawai_Fakultas';
    protected $primaryKey = 'Id_Pegawai';
    public $timestamps = false;

    protected $fillable = [
        'NIP',
        'Nama_Pegawai',
        'Jenis_Kelamin_Pegawai',
        'Alamat_Pegawai',
        'Id_User',
        'Id_Fakultas',
    ];

    /**
     * Relasi ke User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'Id_User', 'Id_User');
    }

    /**
     * Relasi ke Fakultas
     */
    public function fakultas()
    {
        return $this->belongsTo(Fakultas::class, 'Id_Fakultas', 'Id_Fakultas');
    }
}
