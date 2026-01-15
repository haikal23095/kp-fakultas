<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReqSKPengujiSkripsi extends Model
{
    use HasFactory;

    protected $table = 'Req_SK_Penguji_Skripsi';

    protected $primaryKey = 'No';

    public $timestamps = false;

    protected $fillable = [
        'Id_Prodi',
        'Semester',
        'Tahun_Akademik',
        'Data_Penguji_Skripsi',
        'Id_Dosen_Kaprodi',
        'Nomor_Surat',
        'Status',
        'Id_Acc_SK_Penguji_Skripsi',
        'Alasan_Tolak',
        'Tanggal_Pengajuan',
        'Tanggal_Tenggat',
    ];

    protected $casts = [
        'Data_Penguji_Skripsi' => 'array',
        'Tanggal_Pengajuan' => 'datetime',
        'Tanggal_Tenggat' => 'datetime',
    ];

    /**
     * Relasi ke tabel Prodi
     */
    public function prodi()
    {
        return $this->belongsTo(Prodi::class, 'Id_Prodi', 'Id_Prodi');
    }

    /**
     * Relasi ke Kaprodi (Dosen)
     */
    public function kaprodi()
    {
        return $this->belongsTo(Dosen::class, 'Id_Dosen_Kaprodi', 'Id_Dosen');
    }

    /**
     * Relasi ke Approval SK Penguji Skripsi
     */
    public function accSKPengujiSkripsi()
    {
        return $this->belongsTo(AccSKPengujiSkripsi::class, 'Id_Acc_SK_Penguji_Skripsi', 'No');
    }

    /**
     * Alias untuk relasi approval
     */
    public function approval()
    {
        return $this->accSKPengujiSkripsi();
    }
}
