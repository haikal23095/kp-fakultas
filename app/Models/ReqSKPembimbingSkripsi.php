<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReqSKPembimbingSkripsi extends Model
{
    use HasFactory;

    protected $table = 'Req_SK_Pembimbing_Skripsi';

    protected $primaryKey = 'No';

    public $timestamps = false;

    protected $fillable = [
        'Id_Prodi',
        'Semester',
        'Tahun_Akademik',
        'Data_Pembimbing_Skripsi',
        'Id_Dosen_Kaprodi',
        'Nomor_Surat',
        'Status',
        'Id_Acc_SK_Pembimbing_Skripsi',
        'Alasan-Tolak',
        'Tanggal-Pengajuan',
        'Tanggal-Tenggat',
    ];

    protected $casts = [
        // Disable auto-cast untuk menghindari double-encoding issues
        // 'Data_Pembimbing_Skripsi' => 'array',
        'Tanggal-Pengajuan' => 'datetime',
        'Tanggal-Tenggat' => 'datetime',
    ];

    /**
     * Accessor untuk mapping data hyphen ke underscore (backward compatibility)
     */
    public function getTanggalPengajuanAttribute()
    {
        $value = $this->attributes['Tanggal-Pengajuan'] ?? null;
        return $value ? $this->asDateTime($value) : null;
    }

    public function getTanggalTenggatAttribute()
    {
        $value = $this->attributes['Tanggal-Tenggat'] ?? null;
        return $value ? $this->asDateTime($value) : null;
    }

    /**
     * Relationships to be loaded by default (optional - uncomment if needed)
     * protected $with = ['accSKPembimbingSkripsi'];
     */

    /**
     * Relasi ke tabel Prodi
     */
    public function prodi()
    {
        return $this->belongsTo(Prodi::class, 'Id_Prodi', 'Id_Prodi');
    }

    /**
     * Relasi ke Dosen Kaprodi
     */
    public function kaprodi()
    {
        return $this->belongsTo(Dosen::class, 'Id_Dosen_Kaprodi', 'Id_Dosen');
    }

    /**
     * Relasi ke Acc_SK_Pembimbing_Skripsi (One to One)
     * Satu request akan memiliki satu approval
     */
    public function accSKPembimbingSkripsi()
    {
        return $this->belongsTo(AccSKPembimbingSkripsi::class, 'Id_Acc_SK_Pembimbing_Skripsi', 'No');
    }

    /**
     * Alias untuk accSKPembimbingSkripsi
     */
    public function approval()
    {
        return $this->accSKPembimbingSkripsi();
    }

    /**
     * Scope untuk filter berdasarkan status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('Status', $status);
    }

    /**
     * Scope untuk filter berdasarkan semester
     */
    public function scopeBySemester($query, $semester)
    {
        return $query->where('Semester', $semester);
    }

    /**
     * Scope untuk filter berdasarkan prodi
     */
    public function scopeByProdi($query, $idProdi)
    {
        return $query->where('Id_Prodi', $idProdi);
    }

    /**
     * Accessor untuk mendapatkan status dalam format yang lebih readable
     */
    public function getStatusReadableAttribute()
    {
        $statusMap = [
            'Dikerjakan admin' => 'Dikerjakan Admin',
            'Menunggu-Persetujuan-Wadek-1' => 'Menunggu Persetujuan Wadek 1',
            'Menunggu-Persetujuan-Dekan' => 'Menunggu Persetujuan Dekan',
            'Selesai' => 'Selesai',
            'Ditolak-Admin' => 'Ditolak Admin',
            'Ditolak-Wadek1' => 'Ditolak Wadek 1',
            'Ditolak-Dekan' => 'Ditolak Dekan',
        ];

        return $statusMap[$this->Status] ?? $this->Status;
    }

    /**
     * Check apakah request sudah selesai
     */
    public function isSelesai()
    {
        return $this->Status === 'Selesai';
    }

    /**
     * Check apakah request ditolak
     */
    public function isDitolak()
    {
        return in_array($this->Status, [
            'Ditolak-Admin',
            'Ditolak-Wadek1',
            'Ditolak-Dekan'
        ]);
    }

    /**
     * Check apakah request masih pending
     */
    public function isPending()
    {
        return in_array($this->Status, [
            'Dikerjakan admin',
            'Menunggu-Persetujuan-Wadek-1',
            'Menunggu-Persetujuan-Dekan'
        ]);
    }
}
