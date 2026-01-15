<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MataKuliah extends Model
{
    use HasFactory;

    protected $table = 'Matakuliah';

    protected $primaryKey = 'Nomor';

    public $incrementing = true;

    public $timestamps = false;

    protected $fillable = [
        'Nama_Matakuliah',
        'Kelas',
        'SKS',
        'Id_Prodi',
    ];

    protected $casts = [
        'SKS' => 'integer',
        'Id_Prodi' => 'integer',
    ];

    /**
     * Relasi ke tabel Prodi
     */
    public function prodi()
    {
        return $this->belongsTo(Prodi::class, 'Id_Prodi', 'id');
    }

    /**
     * Scope untuk filter berdasarkan prodi
     */
    public function scopeByProdi($query, $prodiId)
    {
        return $query->where('Id_Prodi', $prodiId);
    }

    /**
     * Scope untuk mendapatkan mata kuliah unik (tanpa duplikasi nama)
     */
    public function scopeUniqueMataKuliah($query, $prodiId = null)
    {
        $query = $query->select('Nama_Matakuliah', 'SKS', 'Id_Prodi')
            ->groupBy('Nama_Matakuliah', 'SKS', 'Id_Prodi');

        if ($prodiId) {
            $query->where('Id_Prodi', $prodiId);
        }

        return $query;
    }

    /**
     * Mendapatkan semua kelas untuk mata kuliah tertentu
     */
    public static function getKelasByMataKuliah($namaMataKuliah, $prodiId = null)
    {
        $query = self::where('Nama_Matakuliah', $namaMataKuliah)
            ->select('Kelas', 'Nomor', 'SKS');

        if ($prodiId) {
            $query->where('Id_Prodi', $prodiId);
        }

        return $query->get();
    }
}
