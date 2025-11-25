<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratMagangInvitation extends Model
{
    protected $table = 'Surat_Magang_Invitations';
    protected $primaryKey = 'id_invitation';
    public $timestamps = false;

    const CREATED_AT = 'invited_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'id_surat_magang',
        'id_mahasiswa_diundang',
        'id_mahasiswa_pengundang',
        'status',
        'keterangan',
        'responded_at',
        'invited_at',
    ];

    protected $casts = [
        'invited_at' => 'datetime',
        'responded_at' => 'datetime',
    ];

    /**
     * Relasi ke Surat Magang
     */
    public function suratMagang()
    {
        return $this->belongsTo(SuratMagang::class, 'id_surat_magang', 'id_no');
    }

    /**
     * Relasi ke Mahasiswa yang diundang
     */
    public function mahasiswaDiundang()
    {
        return $this->belongsTo(Mahasiswa::class, 'id_mahasiswa_diundang', 'Id_Mahasiswa');
    }

    /**
     * Relasi ke Mahasiswa pengundang
     */
    public function mahasiswaPengundang()
    {
        return $this->belongsTo(Mahasiswa::class, 'id_mahasiswa_pengundang', 'Id_Mahasiswa');
    }

    /**
     * Scope untuk invitation yang pending
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope untuk invitation yang diterima
     */
    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }

    /**
     * Scope untuk invitation yang ditolak
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
}
