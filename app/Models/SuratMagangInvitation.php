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
        'id_draft',
        'Id_Mahasiswa_Diundang',
        'Id_Mahasiswa_Pengundang',
        'status',
        'keterangan',
        'responded_at',
    ];

    protected $casts = [
        'invited_at' => 'datetime',
        'responded_at' => 'datetime',
    ];

    /**
     * Relasi ke Draft
     */
    public function draft()
    {
        return $this->belongsTo(SuratMagangDraft::class, 'id_draft', 'id_draft');
    }

    /**
     * Relasi ke Mahasiswa yang diundang
     */
    public function mahasiswaDiundang()
    {
        return $this->belongsTo(Mahasiswa::class, 'Id_Mahasiswa_Diundang', 'Id_Mahasiswa');
    }

    /**
     * Relasi ke Mahasiswa pengundang
     */
    public function mahasiswaPengundang()
    {
        return $this->belongsTo(Mahasiswa::class, 'Id_Mahasiswa_Pengundang', 'Id_Mahasiswa');
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
