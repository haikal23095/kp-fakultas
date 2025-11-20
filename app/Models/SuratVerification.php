<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratVerification extends Model
{
    protected $table = 'surat_verifications';

    protected $fillable = [
        'id_tugas_surat',
        'token',
        'signed_by',
        'signed_by_user_id',
        'signed_at',
        'qr_path',
    ];

    protected $casts = [
        'signed_at' => 'datetime',
    ];

    /**
     * Relasi ke TugasSurat
     */
    public function tugasSurat()
    {
        return $this->belongsTo(TugasSurat::class, 'id_tugas_surat', 'Id_Tugas_Surat');
    }

    /**
     * Relasi ke User (Penandatangan)
     */
    public function penandatangan()
    {
        return $this->belongsTo(User::class, 'signed_by_user_id', 'Id_User');
    }
}
