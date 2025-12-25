<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratLegalisir extends Model
{
    use HasFactory;

    // Nama tabel sesuai di database Anda
    protected $table = 'Surat_Legalisir';

    // Primary Key bukan 'id', jadi harus dideklarasikan
    protected $primaryKey = 'id_no';

    // Set ke false jika tabel Anda tidak memiliki kolom created_at & updated_at
    public $timestamps = false;

    /**
     * Kolom yang dapat diisi melalui mass assignment.
     * Pastikan nama kolom di sini SAMA PERSIS dengan di database (Case Sensitive).
     */
    protected $fillable = [
        'Id_Tugas_Surat',
        'Id_User',
        'Id_Pejabat',
        'Jenis_Dokumen',
        'Jumlah_Salinan',
        'Biaya',
        'Tanggal_Bayar',
        'Status',
    ];

    /**
     * Relasi ke Model User (Pemilik Pengajuan/Mahasiswa)
     */
    public function user()
    {
        // 'Id_User' adalah foreign key di tabel Surat_Legalisir
        // 'Id_User' (param ke-3) adalah primary key di tabel Users
        return $this->belongsTo(User::class, 'Id_User', 'Id_User');
    }

    /**
     * Relasi ke Model TugasSurat (Parent Table)
     */
    public function tugasSurat()
    {
        return $this->belongsTo(TugasSurat::class, 'Id_Tugas_Surat', 'Id_Tugas_Surat');
    }

    /**
     * Relasi ke Model Pejabat (Opsional)
     */
    public function pejabat()
    {
        return $this->belongsTo(Pejabat::class, 'Id_Pejabat', 'Id_Pejabat');
    }
}