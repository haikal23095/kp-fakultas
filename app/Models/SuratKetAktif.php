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
        'Id_Tugas_Surat',
        'Nomor_Surat',
        'Tahun_Akademik',
        'KRS',
        'is_urgent',
        'urgent_reason',
    ];

    /**
     * Relasi ke TugasSurat
     */
    public function tugasSurat()
    {
        return $this->belongsTo(TugasSurat::class, 'Id_Tugas_Surat', 'Id_Tugas_Surat');
    }
}
