<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;
    protected $table = 'Roles';
    protected $primaryKey = 'Id_Role';

    public $timestamps = false;
    protected $fillable = ['Id_Role', 'Name_Role'];

    public function users()
    {
        return $this->hasMany(User::class, 'Id_Role', 'Id_Role');
    }

    /**
     * Ambil semua role yang diurutkan berdasarkan nama
     */
    public static function getAllOrdered()
    {
        return self::orderBy('Name_Role')->get();
    }
}


