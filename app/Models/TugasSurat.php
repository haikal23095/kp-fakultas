<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TugasSurat extends Model
{
    // use the trait inside the class body
    use HasFactory;

    // Sesuaikan dengan nama tabel di DB Anda
    protected $table = 'Tugas_Surat';
    // Sesuaikan dengan primary key di DB Anda
    protected $primaryKey = 'Id_Tugas_Surat';

    /**
     * PENTING: Matikan timestamps (created_at, updated_at)
     * karena Anda menggunakan nama kolom tanggal sendiri.
     */
    public $timestamps = false;

    /**
     * Primary key adalah auto-increment
     */
    public $incrementing = true;

    /**
     * Tipe data primary key
     */
    protected $keyType = 'int';

    /**
     * Kolom-kolom yang boleh diisi secara mass assignment
     */
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
     * SANGAT PENTING: Ini mengubah 'data_spesifik'
     * dari JSON string menjadi PHP array secara otomatis.
     */
    protected $casts = [
        'data_spesifik' => 'array',
    ];
}