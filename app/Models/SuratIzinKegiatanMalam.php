<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratIzinKegiatanMalam extends Model
{
    use HasFactory;

    // Nama tabel eksplisit
    protected $table = 'surat_izin_kegiatan_malams';
    
    // Primary Key
    protected $primaryKey = 'id';

    // Matikan timestamps karena tabel tidak memiliki created_at/updated_at
    public $timestamps = false;

    protected $fillable = [
        'id_tugas_surat',
        'id_user',
        'id_pejabat',
        'nama_kegiatan',
        'waktu_mulai',
        'waktu_selesai',
        'lokasi_kegiatan',
        'jumlah_peserta',
        'alasan',
        'nomor_surat',
    ];

    protected $casts = [
        'waktu_mulai' => 'datetime',
        'waktu_selesai' => 'datetime',
    ];

    /**
     * Relasi ke TugasSurat
     */
    public function tugasSurat()
    {
        return $this->belongsTo(TugasSurat::class, 'id_tugas_surat', 'Id_Tugas_Surat');
    }

    /**
     * Relasi ke User (Mahasiswa Pemohon)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'Id_User');
    }

    /**
     * Relasi ke Pejabat (Wakil Dekan III)
     */
    public function pejabat()
    {
        return $this->belongsTo(Pejabat::class, 'id_pejabat', 'Id_Pejabat');
    }
}
