<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ruangan extends Model
{
    use HasFactory;

    protected $table = 'Ruangan';
    protected $primaryKey = 'Id_Ruangan';
    public $timestamps = false;

    protected $fillable = [
        'Nama_Ruangan',
        'Lokasi',
        'Kapasitas',
        'Fasilitas',
        'Status',
    ];

    /**
     * Relasi ke Surat_Peminjaman_Ruang
     */
    public function peminjamanRuang()
    {
        return $this->hasMany(SuratPeminjamanRuang::class, 'Id_Ruangan', 'Id_Ruangan');
    }
}
