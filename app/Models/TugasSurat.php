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

    // PASTIKAN TIDAK ADA BARIS "public $incrementing = false;" DI SINI
    // Jika ada, HAPUS baris itu.

    /**
     * SANGAT PENTING: Ini mengubah 'data_spesifik'
     * dari JSON string menjadi PHP array secara otomatis.
     */
    protected $casts = [
        'data_spesifik' => 'array',
    ];
}