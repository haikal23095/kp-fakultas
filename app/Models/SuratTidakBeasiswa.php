<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratTidakBeasiswa extends Model
{
    use HasFactory;

    protected $table = 'Surat_Tidak_Beasiswa';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'Id_Tugas_Surat',
        'Id_User',
        'Id_Pejabat',
        'Nama_Orang_Tua',
        'Pekerjaan_Orang_Tua',
        'Pendapatan_Orang_Tua',
        'NIP_Orang_Tua',
        'Keperluan',
        'File_Pernyataan',
        'Nomor_Surat',
    ];

    public function tugasSurat()
    {
        return $this->belongsTo(TugasSurat::class, 'Id_Tugas_Surat', 'Id_Tugas_Surat');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'Id_User', 'Id_User');
    }

    public function pejabat()
    {
        return $this->belongsTo(Pejabat::class, 'Id_Pejabat', 'Id_Pejabat');
    }
}
