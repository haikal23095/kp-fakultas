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
        'Id_Pemberi_Tugas',
        'Id_Penerima_Tugas',
        'Nomor_Surat',
        'Tanggal_Diberikan',
        'Tanggal_Diselesaikan',
        'Nama_Instansi',
        'Alamat_Instansi',
        'Judul_Penelitian',
        'Tanggal_Mulai',
        'Tanggal_Selesai',
        'Foto_ttd',
        'Qr_code',
        'Qr_code_dekan',
        'Data_Mahasiswa',
        'Data_Dosen_pembiming',
        'Dokumen_Proposal',
        'Surat_Pengantar_Magang',
        'Acc_Koordinator',
        'Acc_Dekan',
        'Nama_Koordinator',
        'Nama_Dekan',
        'Nip_Dekan',
        'Status',
        'Komentar',
    ];

    /**
     * Cast kolom JSON
     */
    protected $casts = [
        'Data_Mahasiswa' => 'array',
        'Data_Dosen_pembiming' => 'array',
        'Tanggal_Diberikan' => 'date',
        'Tanggal_Diselesaikan' => 'date',
    ];

    /**
     * Relasi ke User Pemberi Tugas (Mahasiswa yang mengajukan)
     */
    public function pemberiTugas()
    {
        return $this->belongsTo(User::class, 'Id_Pemberi_Tugas', 'Id_User');
    }

    /**
     * Relasi ke User Penerima Tugas
     */
    public function penerimaTugas()
    {
        return $this->belongsTo(User::class, 'Id_Penerima_Tugas', 'Id_User');
    }

    /**
     * Relasi ke Dosen (Koordinator KP/Magang)
     */
    public function koordinator()
    {
        return $this->belongsTo(Dosen::class, 'Nama_Koordinator', 'Id_Dosen');
    }

    /**
     * Relasi ke Dosen (Dekan)
     */
    public function dekan()
    {
        return $this->belongsTo(Dosen::class, 'Nama_Dekan', 'Id_Dosen');
    }
}
