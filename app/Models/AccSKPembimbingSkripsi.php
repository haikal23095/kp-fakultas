<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccSKPembimbingSkripsi extends Model
{
    use HasFactory;

    protected $table = 'Acc_SK_Pembimbing_Skripsi';

    protected $primaryKey = 'No';

    public $timestamps = false;

    protected $fillable = [
        'Semester',
        'Tahun_Akademik',
        'Data_Pembimbing_Skripsi',
        'Nomor_Surat',
        'Status',
        'Alasan_Tolak',
        'Alasan-Tolak', // Alias dengan hyphen untuk backward compatibility
        'QR_Code',
        'Tanggal_Persetujuan_Dekan',
        'Id_Dekan',
        'Tanggal_Pengajuan',
        'Tanggal_Tenggat',
    ];

    protected $casts = [
        'Data_Pembimbing_Skripsi' => 'array',
        'Tanggal_Persetujuan_Dekan' => 'datetime',
        'Tanggal_Pengajuan' => 'datetime',
        'Tanggal_Tenggat' => 'datetime',
    ];

    /**
     * Relasi ke Dekan (Pejabat)
     */
    public function dekan()
    {
        return $this->belongsTo(Pejabat::class, 'Id_Dekan', 'Id_Pejabat');
    }

    /**
     * Relasi balik ke Request SK Pembimbing Skripsi (One to Many)
     * Satu approval bisa terkait dengan banyak request
     */
    public function reqSKPembimbingSkripsi()
    {
        return $this->hasMany(ReqSKPembimbingSkripsi::class, 'Id_Acc_SK_Pembimbing_Skripsi', 'No');
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
     * Scope untuk filter berdasarkan tahun akademik
     */
    public function scopeByTahunAkademik($query, $tahunAkademik)
    {
        return $query->where('Tahun_Akademik', $tahunAkademik);
    }

    /**
     * Accessor untuk mendapatkan status dalam format yang lebih readable
     */
    public function getStatusReadableAttribute()
    {
        $statusMap = [
            'Menunggu-Persetujuan-Wadek-1' => 'Menunggu Persetujuan Wadek 1',
            'Menunggu-Persetujuan-Dekan' => 'Menunggu Persetujuan Dekan',
            'Selesai' => 'Selesai',
            'Ditolak-Wadek1' => 'Ditolak Wadek 1',
            'Ditolak-Dekan' => 'Ditolak Dekan',
        ];

        return $statusMap[$this->Status] ?? $this->Status;
    }

    /**
     * Check apakah sudah disetujui dekan
     */
    public function isSelesai()
    {
        return $this->Status === 'Selesai';
    }

    /**
     * Check apakah ditolak
     */
    public function isDitolak()
    {
        return in_array($this->Status, [
            'Ditolak-Wadek1',
            'Ditolak-Dekan'
        ]);
    }

    /**
     * Check apakah masih pending approval
     */
    public function isPending()
    {
        return in_array($this->Status, [
            'Menunggu-Persetujuan-Wadek-1',
            'Menunggu-Persetujuan-Dekan'
        ]);
    }
}
