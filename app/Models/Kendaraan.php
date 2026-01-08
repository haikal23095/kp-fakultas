<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kendaraan extends Model
{
    use HasFactory;

    protected $table = 'Kendaraan';

    protected $fillable = [
        'nama_kendaraan',
        'plat_nomor',
        'kapasitas',
        'status_kendaraan',
    ];

    /**
     * Relasi ke Surat_Peminjaman_Mobil
     */
    public function peminjamanMobil()
    {
        return $this->hasMany(SuratPeminjamanMobil::class, 'Id_Kendaraan');
    }

    /**
     * Scope untuk kendaraan yang tersedia
     */
    public function scopeTersedia($query)
    {
        return $query->where('status_kendaraan', 'Tersedia');
    }

    /**
     * Scope untuk kendaraan yang sedang maintenance
     */
    public function scopeMaintenance($query)
    {
        return $query->where('status_kendaraan', 'Maintenance');
    }
}
