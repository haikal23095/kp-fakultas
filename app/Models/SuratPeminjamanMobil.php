<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratPeminjamanMobil extends Model
{
    use HasFactory;

    protected $table = 'Surat_Peminjaman_Mobil';

    protected $fillable = [
        'Id_Tugas_Surat',
        'Id_User',
        'Id_Kendaraan',
        'Id_Pejabat',
        'tujuan',
        'keperluan',
        'tanggal_pemakaian_mulai',
        'tanggal_pemakaian_selesai',
        'jumlah_penumpang',
        'rekomendasi_admin',
        'catatan_wadek2',
        'alasan_penolakan',
        'nomor_surat',
        'file_surat_final',
        'qr_code_path',
        'status_pengajuan',
    ];

    protected $casts = [
        'tanggal_pemakaian_mulai' => 'date',
        'tanggal_pemakaian_selesai' => 'date',
    ];

    /**
     * Relasi ke Tugas_Surat
     */
    public function tugasSurat()
    {
        return $this->belongsTo(TugasSurat::class, 'Id_Tugas_Surat', 'Id_Tugas_Surat');
    }

    /**
     * Relasi ke User (Pengaju)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'Id_User', 'Id_User');
    }

    /**
     * Relasi ke Kendaraan
     */
    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class, 'Id_Kendaraan');
    }

    /**
     * Relasi ke Pejabat (Wadek2)
     */
    public function pejabat()
    {
        return $this->belongsTo(Pejabat::class, 'Id_Pejabat', 'Id_Pejabat');
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
     * Scope untuk status Disetujui atau Selesai (untuk Arsip)
     */
    public function scopeArsip($query)
    {
        return $query->whereIn('status_pengajuan', ['Disetujui_Wadek2', 'Selesai'])
                     ->whereNotNull('nomor_surat');
    }

    /**
     * Scope untuk status Ditolak
     */
    public function scopeDitolak($query)
    {
        return $query->where('status_pengajuan', 'Ditolak');
    }

    /**
     * Cek apakah tanggal bentrok dengan peminjaman lain
     */
    public static function isTanggalBentrok($kendaraanId, $mulai, $selesai, $excludeId = null)
    {
        $query = self::where('Id_Kendaraan', $kendaraanId)
                     ->whereIn('status_pengajuan', ['Diverifikasi_Admin', 'Disetujui_Wadek2'])
                     ->where(function($q) use ($mulai, $selesai) {
                         $q->whereBetween('tanggal_pemakaian_mulai', [$mulai, $selesai])
                           ->orWhereBetween('tanggal_pemakaian_selesai', [$mulai, $selesai])
                           ->orWhere(function($q2) use ($mulai, $selesai) {
                               $q2->where('tanggal_pemakaian_mulai', '<=', $mulai)
                                  ->where('tanggal_pemakaian_selesai', '>=', $selesai);
                           });
                     });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }
}
