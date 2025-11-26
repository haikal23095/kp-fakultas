<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    use HasFactory;

    protected $table = 'Notifikasi';
    protected $primaryKey = 'Id_Notifikasi';
    public $timestamps = true; // Aktifkan timestamps untuk created_at dan updated_at

    const CREATED_AT = 'created_at';

    protected $fillable = [
        'Tipe_Notifikasi',
        'Pesan',
        'Dest_user', // Nama kolom di database pakai lowercase 'u'
        'Source_User',
        'Is_Read',
        'Data_Tambahan',
    ];

    protected $casts = [
        'Is_Read' => 'boolean',
        'created_at' => 'datetime', // Cast ke Carbon untuk diffForHumans()
        'Data_Tambahan' => 'array', // Cast JSON ke array
    ];

    /**
     * Relasi ke User tujuan (penerima notifikasi)
     */
    public function destinationUser()
    {
        return $this->belongsTo(User::class, 'Dest_user', 'Id_User');
    }

    /**
     * Relasi ke User sumber (pengirim notifikasi)
     */
    public function sourceUser()
    {
        return $this->belongsTo(User::class, 'Source_User', 'Id_User');
    }

    /**
     * Scope untuk notifikasi yang belum dibaca
     */
    public function scopeUnread($query)
    {
        return $query->where('Is_Read', false);
    }

    /**
     * Scope untuk notifikasi user tertentu
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('Dest_user', $userId);
    }

    /**
     * Mark notifikasi sebagai sudah dibaca
     */
    public function markAsRead()
    {
        $this->Is_Read = true;
        $this->save();
    }
}
