<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SKDosenWali extends Model
{
    use HasFactory;

    protected $table = 'Req_SK_Dosen_Wali';
    protected $primaryKey = 'No';

    public $timestamps = false;

    protected $fillable = [
        'Id_Prodi',
        'Semester',
        'Tahun_Akademik',
        'Data_Dosen_Wali',
        'Nomor_Surat',
        'Status',
        'Tanggal-Pengajuan',
        'Tanggal-Tenggat',
        'Id_Dosen_Kaprodi'
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
        return $this->belongsTo(Prodi::class, 'Id_Prodi', 'Id_Prodi');
    }

    /**
     * Relasi ke Dosen (Kaprodi yang mengajukan)
     */
    public function kaprodi()
    {
        return $this->belongsTo(Dosen::class, 'Id_Dosen_Kaprodi', 'Id_Dosen');
    }
}
