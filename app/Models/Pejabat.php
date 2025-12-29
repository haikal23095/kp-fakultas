<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pejabat extends Model
{
    use HasFactory;

    protected $table = 'Pejabat';
    protected $primaryKey = 'Id_Pejabat';
    public $timestamps = false;

    protected $fillable = [
        'Id_Pejabat',
        'Nama_Jabatan',
    ];

    /**
     * Relasi ke Dosen
     */
    public function dosen()
    {
        return $this->hasOne(Dosen::class, 'Id_Pejabat', 'Id_Pejabat');
    }
}
