<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratMagang extends Model
{
    use HasFactory;

    protected $table = 'Surat_Magang';
    protected $primaryKey = 'id_no';
    public $timestamps = false;

    protected $fillable = [
        'Id_Tugas_Surat',
        'Nama_Instansi',
        'Alamat_Instansi',
        'Tanggal_Mulai',
        'Tanggal_Selesai',
        'Foto_ttd',
        'Data_Mahasiswa',
        'Data_Dosen_pembiming',
        'Dokumen_Proposal',
        'Surat_Pengantar_Magang',
        'Acc_Koordinator',
        'Nama_Koordinator',
        'Status',
        'Komentar',
    ];

    /**
     * Cast kolom JSON
     */
    protected $casts = [
        'Data_Mahasiswa' => 'array',
        'Data_Dosen_pembiming' => 'array',
    ];

    /**
     * Relasi ke TugasSurat
     */
    public function tugasSurat()
    {
        return $this->belongsTo(TugasSurat::class, 'Id_Tugas_Surat', 'Id_Tugas_Surat');
    }

    /**
     * Relasi ke Dosen (Koordinator KP/Magang)
     */
    public function koordinator()
    {
        return $this->belongsTo(Dosen::class, 'Nama_Koordinator', 'Id_Dosen');
    }
}
