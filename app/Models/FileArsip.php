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
}