<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TugasSurat extends Model
{
    use HasFactory;

    protected $table = 'Tugas_Surat';
    protected $primaryKey = 'Id_Tugas_Surat';
    public $timestamps = false;
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'Id_Penerima_Tugas_Surat',
        'Id_Jenis_Surat',
        'Judul_Tugas_Surat',
        'Deskripsi_Tugas_Surat',
        'data_spesifik',
        'Status',
        'Tanggal_Diberikan_Tugas_Surat',
        'Id_Pemberi_Tugas_Surat',
        'Id_Jenis_Pekerjaan',
        'dokumen_pendukung',
        'File_Surat',
        'Nomor_Surat',
        'Tanggal_Tenggat_Tugas_Surat',
        'Tanggal_Diselesaikan',
    ];

    /**
     * Mengubah kolom JSON dan Tanggal secara otomatis.
     */
    protected $casts = [
        'data_spesifik' => 'array',
        'Tanggal_Diberikan_Tugas_Surat' => 'date',
        'Tanggal_Tenggat_Tugas_Surat' => 'date',
        'Tanggal_Diselesaikan' => 'date',
    ];

    // ===================================================================
    //  FUNGSI RELASI (SUDAH DIPERBAIKI)
    // ===================================================================

    /**
     * Relasi ke PEMBERI TUGAS (Pengaju).
     * Menghubungkan 'Id_Pemberi_Tugas_Surat' ke 'Id_User' di tabel Users.
     */
    public function pemberiTugas()
    {
        return $this->belongsTo(User::class, 'Id_Pemberi_Tugas_Surat', 'Id_User');
    }

    /**
     * Relasi ke PENERIMA TUGAS.
     * Menghubungkan 'Id_Penerima_Tugas_Surat' ke 'Id_User' di tabel Users.
     */
    public function penerimaTugas()
    {
        return $this->belongsTo(User::class, 'Id_Penerima_Tugas_Surat', 'Id_User');
    }

    /**
     * Relasi ke JENIS SURAT.
     */
    public function jenisSurat()
    {
        // ASUMSI: Primary Key di tabel JenisSurat adalah 'Id_Jenis_Surat'
        // Jika PK-nya 'id', ganti parameter ketiga menjadi 'id'
        return $this->belongsTo(JenisSurat::class, 'Id_Jenis_Surat', 'Id_Jenis_Surat');
    }

    /**
     * Relasi ke JENIS PEKERJAAN.
     */
    public function jenisPekerjaan()
    {
        // ASUMSI: Primary Key di tabel JenisPekerjaan adalah 'Id_Jenis_Pekerjaan'
        // Jika PK-nya 'id', ganti parameter ketiga menjadi 'id'
        return $this->belongsTo(JenisPekerjaan::class, 'Id_Jenis_Pekerjaan', 'Id_Jenis_Pekerjaan');
    }

    /**
     * Relasi ke FileArsip (one-to-one)
     */
    public function fileArsip()
    {
        return $this->hasOne(FileArsip::class, 'Id_Tugas_Surat', 'Id_Tugas_Surat');
    }
}