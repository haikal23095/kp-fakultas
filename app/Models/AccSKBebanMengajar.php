<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccSKBebanMengajar extends Model
{
    use HasFactory;

    // Mengikuti struktur tabel Acc_SK_Beban_Mengajar di database
    protected $table = 'Acc_SK_Beban_Mengajar';
    protected $primaryKey = 'No';
    public $timestamps = false;

    protected $fillable = [
        'Semester',
        'Tahun_Akademik',
        'Data_Beban_Mengajar',
        'Nomor_Surat',
        'Status',
        'Alasan-Tolak',
        'QR_Code',
        'Tanggal-Persetujuan-Dekan',
        'Id_Dekan',
        'Tanggal-Pengajuan',
        'Tanggal-Tenggat',
    ];

    protected $casts = [
        'Data_Beban_Mengajar' => 'array',
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
     * Relasi ke SK_Beban_Mengajar (Request SK yang sudah digabungkan)
     */
    public function skBebanMengajar()
    {
        return $this->hasMany(SKBebanMengajar::class, 'Id_Acc_SK_Beban_Mengajar', 'No');
    }

    /**
     * Get prodi from first dosen in Data_Beban_Mengajar
     * Since Acc_SK can contain multiple prodi, we extract from data
     */
    public function getProdiAttribute()
    {
        if (!empty($this->Data_Beban_Mengajar) && is_array($this->Data_Beban_Mengajar)) {
            $firstItem = reset($this->Data_Beban_Mengajar);
            return $firstItem['Id_Prodi'] ?? null;
        }
        return null;
    }

    /**
     * Scope untuk filter berdasarkan semester dan tahun akademik
     */
    public function scopeByPeriode($query, $semester, $tahunAkademik)
    {
        return $query->where('Semester', $semester)
            ->where('Tahun_Akademik', $tahunAkademik);
    }

    /**
     * Scope untuk filter berdasarkan status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('Status', $status);
    }

    /**
     * Scope untuk menunggu persetujuan wadek 1
     */
    public function scopeMenungguWadek1($query)
    {
        return $query->where('Status', 'Menunggu-Persetujuan-Wadek-1');
    }

    /**
     * Scope untuk menunggu persetujuan dekan
     */
    public function scopeMenungguDekan($query)
    {
        return $query->where('Status', 'Menunggu-Persetujuan-Dekan');
    }

    /**
     * Scope untuk yang sudah selesai
     */
    public function scopeSelesai($query)
    {
        return $query->where('Status', 'Selesai');
    }

    /**
     * Scope untuk yang ditolak
     */
    public function scopeDitolak($query)
    {
        return $query->whereIn('Status', ['Ditolak-Wadek1', 'Ditolak-Dekan']);
    }
}
