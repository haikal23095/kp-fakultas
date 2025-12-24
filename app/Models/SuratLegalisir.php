<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratLegalisir extends Model
{
    use HasFactory;

    protected $table = 'Surat_Legalisir';
    protected $primaryKey = 'id_no';
    public $timestamps = false;

    protected $fillable = [
        'Id_Tugas_Surat',
        'Id_User',
        'Id_Pejabat',
        'Jenis_Dokumen',
        'Path_File',
        'Jumlah_Salinan',
        'Biaya',
        'Status',
    ];

    /**
     * Relasi ke TugasSurat
     */
    public function tugasSurat()
    {
        return $this->belongsTo(TugasSurat::class, 'Id_Tugas_Surat', 'Id_Tugas_Surat');
    }

    /**
     * Relasi ke User (Pemohon)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'Id_User', 'Id_User');
    }

    /**
     * Alias untuk relasi user (agar lebih jelas)
     */
    public function pemohon()
    {
        return $this->belongsTo(User::class, 'Id_User', 'Id_User');
    }

    /**
     * Relasi ke Pejabat
     */
    public function pejabat()
    {
        return $this->belongsTo(Pejabat::class, 'Id_Pejabat', 'Id_Pejabat');
    }
}
