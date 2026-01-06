<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SKPembimbingSkripsi extends Model
{
    use HasFactory;

    protected $table = 'Req_SK_Pembimbing_Skripsi';

    protected $primaryKey = 'No';

    public $timestamps = false;

    protected $fillable = [
        'Id_Prodi',
        'Semester',
        'Tahun_Akademik',
        'Data_Pembimbing',
        'Nomor_Surat',
        'Id_Acc_SK_Pembimbing_Skripsi',
        'Tanggal-Pengajuan',
        'Tanggal-Tenggat',
        'Id_Dosen_Kaprodi',
        'Status',
        'Alasan-Tolak',
    ];

    protected $casts = [
        'Data_Pembimbing' => 'array',
        'Tanggal-Pengajuan' => 'datetime',
        'Tanggal-Tenggat' => 'datetime',
    ];

    /**
     * Relasi ke tabel Prodi
     */
    public function prodi()
    {
        return $this->belongsTo(Prodi::class, 'Id_Prodi', 'Id_Prodi');
    }

    /**
     * Relasi ke Dosen Kaprodi
     */
    public function kaprodi()
    {
        return $this->belongsTo(Dosen::class, 'Id_Dosen_Kaprodi', 'Id_Dosen');
    }

    /**
     * Relasi ke Acc_SK_Pembimbing_Skripsi
     */
    public function accSKPembimbingSkripsi()
    {
        return $this->belongsTo(AccSKPembimbingSkripsi::class, 'Id_Acc_SK_Pembimbing_Skripsi', 'No');
    }
}
