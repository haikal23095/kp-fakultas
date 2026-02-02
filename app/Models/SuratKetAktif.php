<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratKetAktif extends Model
{
    use HasFactory;

    protected $table = 'Surat_Ket_Aktif';
    protected $primaryKey = 'id_no';
    public $timestamps = false;

    protected $fillable = [
        'Id_Pemberi_Tugas',
        'Id_Penerima_Tugas',
        'Nomor_Surat',
        'Status',
        'Tanggal_Diberikan',
        'Tanggal_Diselesaikan',
        'Id_Tugas_Surat',
        'Tahun_Akademik',
        'KRS',
        'is_urgent',
        'urgent_reason',
        'Deskripsi',
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
     * @deprecated Relasi ke TugasSurat sudah tidak digunakan
     */
    public function tugasSurat()
    {
        return $this->belongsTo(TugasSurat::class, 'Id_Tugas_Surat', 'Id_Tugas_Surat');
    }
}
