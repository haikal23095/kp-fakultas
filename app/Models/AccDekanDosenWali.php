<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccDekanDosenWali extends Model
{
    use HasFactory;

    // Mengikuti struktur tabel Acc_SK_Dosen_Wali di database
    protected $table = 'Acc_SK_Dosen_Wali';
    protected $primaryKey = 'No';
    public $timestamps = false;

    protected $fillable = [
        'Semester',
        'Tahun_Akademik',
        'Data_Dosen_Wali',
        'Nomor_Surat',
        'Status',
        'Tanggal-Pengajuan',
        'Tanggal-Tenggat',
    ];

    protected $casts = [
        'Data_Dosen_Wali' => 'array',
        'Tanggal-Pengajuan' => 'datetime',
        'Tanggal-Tenggat' => 'datetime',
    ];

    /**
     * Relasi ke Req_SK_Dosen_Wali (Request SK yang sudah digabungkan)
     */
    public function reqSKDosenWali()
    {
        return $this->hasMany(SKDosenWali::class, 'Id_Acc_SK_Dosen_Wali', 'No');
    }
}
