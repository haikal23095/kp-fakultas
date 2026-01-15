<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccSKPengujiSkripsi extends Model
{
    use HasFactory;

    protected $table = 'Acc_SK_Penguji_Skripsi';

    protected $primaryKey = 'No';

    public $timestamps = false;

    protected $fillable = [
        'Semester',
        'Tahun_Akademik',
        'Data_Penguji_Skripsi',
        'Nomor_Surat',
        'Status',
        'Alasan-Tolak',
        'Alasan_Tolak',
        'QR_Code',
        'Tanggal_Persetujuan_Dekan',
        'Id_Dekan',
        'Tanggal-Pengajuan',
        'Tanggal-Tenggat',
    ];

    protected $casts = [
        'Data_Penguji_Skripsi' => 'array',
        'Tanggal_Persetujuan_Dekan' => 'datetime',
        'Tanggal-Pengajuan' => 'datetime',
        'Tanggal-Tenggat' => 'datetime',
    ];

    /**
     * Relasi ke Dekan (Pejabat/Dosen)
     */
    public function dekan()
    {
        return $this->belongsTo(Dosen::class, 'Id_Dekan', 'Id_Dosen');
    }

    /**
     * Accessor untuk Tanggal_Pengajuan (mapping data dari Tanggal-Pengajuan)
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
     * Relasi balik ke Request SK Penguji Skripsi (One to Many)
     */
    public function reqSKPengujiSkripsi()
    {
        return $this->hasMany(ReqSKPengujiSkripsi::class, 'Id_Acc_SK_Penguji_Skripsi', 'No');
    }

    /**
     * Relasi balik ke Request SK Penguji Skripsi (Alias)
     * @deprecated Use reqSKPengujiSkripsi() instead
     */
    public function requestSK()
    {
        return $this->hasMany(ReqSKPengujiSkripsi::class, 'Id_Acc_SK_Penguji_Skripsi', 'No');
    }
}
