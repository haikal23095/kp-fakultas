<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratDispensasi extends Model
{
    use HasFactory;

    protected $table = 'Surat_Dispensasi';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'Id_Tugas_Surat',
        'Id_User',
        'Id_Pejabat_Wadek3',
        'nama_kegiatan',
        'instansi_penyelenggara',
        'tempat_pelaksanaan',
        'tanggal_mulai',
        'tanggal_selesai',
        'file_lampiran',
        'nomor_surat',
        'verifikasi_admin_by',
        'verifikasi_admin_at',
        'acc_wadek3_by',
        'acc_wadek3_at',
        'file_surat_selesai',
        'keterangan_status',
    ];

    // Relasi ke TugasSurat
    public function tugasSurat()
    {
        return $this->belongsTo(TugasSurat::class, 'Id_Tugas_Surat', 'Id_Tugas_Surat');
    }

    // Relasi ke User (pengaju)
    public function user()
    {
        return $this->belongsTo(User::class, 'Id_User', 'Id_User');
    }

    // Relasi ke Pejabat Wadek3
    public function pejabatWadek3()
    {
        return $this->belongsTo(Pejabat::class, 'Id_Pejabat_Wadek3', 'Id_Pejabat');
    }

    // Relasi ke User admin yang verifikasi
    public function verifikasiAdmin()
    {
        return $this->belongsTo(User::class, 'verifikasi_admin_by', 'Id_User');
    }

    // Relasi ke User yang acc sebagai Wadek3
    public function accWadek3()
    {
        return $this->belongsTo(User::class, 'acc_wadek3_by', 'Id_User');
    }
}
