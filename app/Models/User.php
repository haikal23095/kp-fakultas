<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * ======================================================
     * PENGATURAN DARI FILE ANDA (PENTING!)
     * ======================================================
     */
    protected $table = 'Users';
    protected $primaryKey = 'Id_User';
    
    // Matikan timestamps jika Anda tidak punya created_at/updated_at
    // Jika Anda punya, hapus baris ini
    public $timestamps = false; 

    protected $fillable = [
        'Username',
        'password',
        'Name_User',
        'email',
        'Id_Role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getNameAttribute()
    {
        return $this->Name_User;
    }

    /**
     * ======================================================
     * FUNGSI RELASI ROLE (YANG KITA BUTUHKAN)
     * ======================================================
     * Menghubungkan ke Model 'Role' (tabel 'Roles')
     */
    public function role()
    {
        // Foreign Key: 'Id_Role' (di tabel Users)
        // Owner Key: 'Id_Role' (di tabel Roles)
        return $this->belongsTo(Role::class, 'Id_Role', 'Id_Role');
    }
}

