<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratMagangDraft extends Model
{
    protected $table = 'Surat_Magang_Draft';
    protected $primaryKey = 'id_draft';
    public $timestamps = true;

    protected $fillable = [
        'Id_Mahasiswa_Pembuat',
        'Id_Jenis_Surat',
        'Nama_Instansi',
        'Alamat_Instansi',
        'Tanggal_Mulai',
        'Tanggal_Selesai',
        'Judul_Penelitian',
        'Dosen_Pembimbing_1',
        'Dosen_Pembimbing_2',
        'File_Proposal',
        'File_TTD',
        'Data_Mahasiswa_Confirmed',
        'Data_Mahasiswa_Pending',
    ];

    protected $casts = [
        'Data_Mahasiswa_Confirmed' => 'array',
        'Data_Mahasiswa_Pending' => 'array',
        'Tanggal_Mulai' => 'date',
        'Tanggal_Selesai' => 'date',
    ];

    /**
     * Relasi ke Mahasiswa pembuat
     */
    public function mahasiswaPembuat()
    {
        return $this->belongsTo(Mahasiswa::class, 'Id_Mahasiswa_Pembuat', 'Id_Mahasiswa');
    }

    /**
     * Relasi ke Jenis Surat
     */
    public function jenisSurat()
    {
        return $this->belongsTo(JenisSurat::class, 'Id_Jenis_Surat', 'Id_Jenis_Surat');
    }

    /**
     * Relasi ke Invitations
     */
    public function invitations()
    {
        return $this->hasMany(SuratMagangInvitation::class, 'id_draft', 'id_draft');
    }

    /**
     * Get pending invitations
     */
    public function pendingInvitations()
    {
        return $this->invitations()->where('status', 'pending');
    }

    /**
     * Get accepted invitations
     */
    public function acceptedInvitations()
    {
        return $this->invitations()->where('status', 'accepted');
    }
}
