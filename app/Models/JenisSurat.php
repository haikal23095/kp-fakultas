<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisSurat extends Model
{
    use HasFactory;

    // WAJIB: Tentukan nama tabel
    protected $table = 'Jenis_Surat';
    
    // WAJIB: Tentukan Primary Key yang benar
    protected $primaryKey = 'Id_Jenis_Surat';

    // Matikan auto-increment jika PK Anda bukan integer
    // public $incrementing = false;
    
    // Matikan timestamps jika Anda tidak punya created_at/updated_at
    public $timestamps = false;
}
