<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileArsip extends Model
{
    use HasFactory;

    // Sesuaikan dengan nama tabel di DB Anda
    protected $table = 'File_Arsip';

    // Sesuaikan dengan primary key di DB Anda
    protected $primaryKey = 'Id_File_Arsip';

    /**
     * Matikan timestamps (created_at, updated_at)
     * karena tidak ada di tabel 'File_Arsip' Anda.
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
        'Id_Tugas_Surat',
        'Keterangan',
        'Path_File',
        'Id_Penerima_Tugas_Surat',
        'Id_Pemberi_Tugas_Surat',
    ];
}