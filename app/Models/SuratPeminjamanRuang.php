<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratPeminjamanRuang extends Model
{
    use HasFactory;

    protected $table = 'Surat_Peminjaman_Ruang';

    protected $fillable = [
        'Id_Tugas_Surat',
        'Id_Ruangan',
        'nama_kegiatan',
        'penyelenggara',
        'tanggal_mulai',
        'tanggal_selesai',
        'jumlah_peserta',
        'file_lampiran',
        'keterangan',
        'rekomendasi_admin',
        'catatan_wadek2',
        'alasan_penolakan',
        'nomor_surat',
        'file_surat_final',
        'qr_code_path',
        'status_pengajuan',
    ];

    protected $casts = [
        'tanggal_mulai' => 'datetime',
        'tanggal_selesai' => 'datetime',
    ];

    /**
     * Relasi ke Tugas_Surat
     */
    public function tugasSurat()
    {
        return $this->belongsTo(TugasSurat::class, 'Id_Tugas_Surat', 'Id_Tugas_Surat');
    }

    /**
     * Relasi ke Ruangan
     */
    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'Id_Ruangan', 'Id_Ruangan');
    }

    /**
     * Relasi ke SuratVerification (untuk QR Code)
     */
    public function verification()
    {
        return $this->hasOne(SuratVerification::class, 'id_tugas_surat', 'Id_Tugas_Surat');
    }

    /**
     * Scope untuk status Diajukan (untuk Admin)
     */
    public function scopeDiajukan($query)
    {
        return $query->where('status_pengajuan', 'Diajukan');
    }

    /**
     * Scope untuk status Diverifikasi_Admin (untuk Wadek2)
     */
    public function scopeDiverifikasiAdmin($query)
    {
        return $query->where('status_pengajuan', 'Diverifikasi_Admin');
    }

    /**
     * Scope untuk status Selesai (untuk Arsip)
     */
    public function scopeSelesai($query)
    {
        return $query->where('status_pengajuan', 'Selesai')
                     ->whereNotNull('nomor_surat');
    }

    /**
     * Scope untuk status Ditolak
     */
    public function scopeDitolak($query)
    {
        return $query->where('status_pengajuan', 'Ditolak');
    }
}
