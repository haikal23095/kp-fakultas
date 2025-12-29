<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fakultas extends Model
{
    protected $table = 'Fakultas';
    protected $primaryKey = 'Id_Fakultas';
    public $timestamps = false;

    protected $fillable = [
        'Nama_Fakultas',
    ];

    /**
     * Relasi ke Prodi
     */
    public function prodi()
    {
        return $this->hasMany(Prodi::class, 'Id_Fakultas', 'Id_Fakultas');
    }

    /**
     * Relasi ke Pegawai Fakultas
     */
    public function pegawaiFakultas()
    {
        return $this->hasMany(PegawaiFakultas::class, 'Id_Fakultas', 'Id_Fakultas');
    }
}

