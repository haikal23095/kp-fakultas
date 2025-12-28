<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratKelakuanBaik extends Model
{
    use HasFactory;

    // Nama tabel eksplisit
    protected $table = 'Surat_Kelakuan_Baik';
    
    // Primary Key
    protected $primaryKey = 'id';

    // Matikan timestamps karena tabel tidak memiliki created_at/updated_at
    public $timestamps = false;

    protected $fillable = [
        'Id_Tugas_Surat',
        'Id_User',
        'Id_Pejabat',
        'Keperluan',
        'Semester',
        'Tahun_Akademik',
        'Nomor_Surat',
    ];

    /**
     * Relasi ke TugasSurat
     */
    public function tugasSurat()
    {
        return $this->belongsTo(TugasSurat::class, 'Id_Tugas_Surat', 'Id_Tugas_Surat');
    }

    /**
     * Relasi ke User (Mahasiswa Pemohon)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'Id_User', 'Id_User');
    }

    /**
     * Relasi ke Pejabat (Wakil Dekan III)
     */
    public function pejabat()
    {
        return $this->belongsTo(Pejabat::class, 'Id_Pejabat', 'Id_Pejabat');
    }
}
