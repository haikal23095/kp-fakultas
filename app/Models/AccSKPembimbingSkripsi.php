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
        'Id_Prodi',
        'Semester',
        'Tahun_Akademik',
        'Data_Pembimbing',
        'Nomor_Surat',
        'Tanggal-Persetujuan-Dekan',
        'QR_Code',
    ];

    protected $casts = [
        'Data_Pembimbing' => 'array',
        'Tanggal-Persetujuan-Dekan' => 'datetime',
    ];

    /**
     * Relasi ke tabel Prodi
     */
    public function prodi()
    {
        return $this->belongsTo(Prodi::class, 'Id_Prodi', 'Id_Prodi');
    }
}
