<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SKDosenWali extends Model
{
    use HasFactory;

    protected $table = 'SK_Dosen_Wali';
    protected $primaryKey = 'No';

    public $timestamps = false;

    protected $fillable = [
        'Prodi',
        'Semester',
        'Tahun_Akademik',
        'Data_Dosen_Wali',
        'Nomor_Surat',
        'Status',
        'Tanggal-Pengajuan',
        'Tanggal-Tenggat'
    ];

    protected $casts = [
        'Data_Dosen_Wali' => 'array',
        'Tanggal-Pengajuan' => 'datetime',
        'Tanggal-Tenggat' => 'datetime',
    ];

    /**
     * Relasi ke Prodi
     */
    public function prodi()
    {
        return $this->belongsTo(Prodi::class, 'Prodi', 'Id_Prodi');
    }
}
