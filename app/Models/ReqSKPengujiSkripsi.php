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
        'Alasan-Tolak',
        'Tanggal-Pengajuan',
        'Tanggal-Tenggat',
    ];

    protected $casts = [
        'Data_Penguji_Skripsi' => 'array',
        'Tanggal-Pengajuan' => 'datetime',
        'Tanggal-Tenggat' => 'datetime',
    ];

    /**
     * Accessor untuk mapping data hyphen ke underscore (backward compatibility)
     */
    public function getTanggalPengajuanAttribute()
    {
        $value = $this->attributes['Tanggal-Pengajuan'] ?? null;
        return $value ? $this->asDateTime($value) : null;
    }

    public function getTanggalTenggatAttribute()
    {
        $value = $this->attributes['Tanggal-Tenggat'] ?? null;
        return $value ? $this->asDateTime($value) : null;
    }

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
