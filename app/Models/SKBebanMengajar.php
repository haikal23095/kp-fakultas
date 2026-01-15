<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SKBebanMengajar extends Model
{
    use HasFactory;

    protected $table = 'Req_SK_Beban_Mengajar';

    protected $primaryKey = 'No';

    public $timestamps = false;

    protected $fillable = [
        'Id_Prodi',
        'Semester',
        'Tahun_Akademik',
        'Data_Beban_Mengajar',
        'Nomor_Surat',
        'Id_Acc_SK_Beban_Mengajar',
        'Tanggal-Pengajuan',
        'Tanggal-Tenggat',
        'Tanggal-Persetujuan-Dekan',
        'Id_Dosen_Kaprodi',
        'Status',
        'Alasan-Tolak',
        'QR_Code',
    ];

    protected $casts = [
        'Data_Beban_Mengajar' => 'array',
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
     * Relasi langsung ke User Kaprodi (melalui Dosen)
     * Ini memudahkan untuk mendapatkan user kaprodi tanpa perlu load nested relation
     */
    public function kaprodiUser()
    {
        return $this->hasOneThrough(
            User::class,           // Model tujuan
            Dosen::class,          // Model perantara
            'Id_Dosen',            // Foreign key di tabel Dosen
            'Id_User',             // Foreign key di tabel Users
            'Id_Dosen_Kaprodi',    // Local key di tabel Req_SK_Beban_Mengajar
            'Id_User'              // Local key di tabel Dosen
        );
    }

    /**
     * Relasi ke Acc_SK_Beban_Mengajar (SK yang sudah digabungkan)
     */
    public function accSKBebanMengajar()
    {
        return $this->belongsTo(\App\Models\AccSKBebanMengajar::class, 'Id_Acc_SK_Beban_Mengajar', 'No');
    }
}
