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
        'Alasan-Tolak',
        'QR_Code',
        'Tanggal-Persetujuan-Dekan',
        'Id_Dekan',
    ];

    protected $casts = [
        'Data_Dosen_Wali' => 'array',
        'Tanggal-Pengajuan' => 'datetime',
        'Tanggal-Tenggat' => 'datetime',
        'Tanggal-Persetujuan-Dekan' => 'datetime',
    ];

    /**
     * Relasi ke Dosen (Dekan yang menyetujui)
     */
    public function dekan()
    {
        return $this->belongsTo(Dosen::class, 'Id_Dekan', 'Id_Dosen');
    }

    /**
     * Relasi ke Req_SK_Dosen_Wali (Request SK yang sudah digabungkan)
     */
    public function reqSKDosenWali()
    {
        return $this->hasMany(SKDosenWali::class, 'Id_Acc_SK_Dosen_Wali', 'No');
    }

    /**
     * Get prodi from first dosen in Data_Dosen_Wali
     * Since Acc_SK can contain multiple prodi, we extract from data
     */
    public function getProdiAttribute()
    {
        if (!empty($this->Data_Dosen_Wali) && is_array($this->Data_Dosen_Wali)) {
            $firstDosen = $this->Data_Dosen_Wali[0] ?? null;
            if ($firstDosen && isset($firstDosen['prodi'])) {
                return (object) ['Nama_Prodi' => $firstDosen['prodi']];
            }
        }
        return null;
    }
}
