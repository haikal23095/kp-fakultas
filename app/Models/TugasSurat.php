<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TugasSurat extends Model
{
    use HasFactory;

    protected $table = 'Tugas_Surat';
    protected $primaryKey = 'Id_Tugas_Surat';
    public $timestamps = false;
    public $incrementing = false; // ID di-set manual, bukan auto-increment
    protected $keyType = 'int';

    protected $fillable = [
        'Id_Pemberi_Tugas_Surat',
        'Id_Penerima_Tugas_Surat',
        'Id_Jenis_Surat',
        'Id_Jenis_Pekerjaan',
        'Judul_Tugas_Surat',
        'Status',
        'Tanggal_Diberikan_Tugas_Surat',
        'Tanggal_Tenggat_Tugas_Surat',
        'Tanggal_Diselesaikan',
        'data_spesifik', // Tambahkan ini
    ];

    /**
     * Mengubah kolom Tanggal secara otomatis.
     */
    protected $casts = [
        'Tanggal_Diberikan_Tugas_Surat' => 'date',
        'Tanggal_Tenggat_Tugas_Surat' => 'date',
        'Tanggal_Diselesaikan' => 'date',
        'data_spesifik' => 'array', // Cast JSON ke array
    ];

    /**
     * Relasi ke PEMBERI TUGAS (Pengaju).
     * Menghubungkan 'Id_Pemberi_Tugas_Surat' ke 'Id_User' di tabel Users.
     */
    public function pemberiTugas()
    {
        return $this->belongsTo(User::class, 'Id_Pemberi_Tugas_Surat', 'Id_User');
    }

    /**
     * Relasi ke PENERIMA TUGAS.
     * Menghubungkan 'Id_Penerima_Tugas_Surat' ke 'Id_User' di tabel Users.
     */
    public function penerimaTugas()
    {
        return $this->belongsTo(User::class, 'Id_Penerima_Tugas_Surat', 'Id_User');
    }

    /**
     * Relasi ke JENIS SURAT.
     */
    public function jenisSurat()
    {
        return $this->belongsTo(JenisSurat::class, 'Id_Jenis_Surat', 'Id_Jenis_Surat');
    }

    /**
     * Update status tugas yang melewati tenggat menjadi 'Terlambat'
     */
    public static function updateStatusTerlambat()
    {
        return self::whereNotIn('Status', ['Selesai', 'Terlambat'])
            ->whereDate('Tanggal_Tenggat_Tugas_Surat', '<', Carbon::now()->toDateString())
            ->update(['Status' => 'Terlambat']);
    }

    /**
     * Ambil daftar tugas berdasarkan prodi
     * Filter berdasarkan PEMBERI tugas (yang mengajukan surat)
     */
    public static function getByProdi($prodiId)
    {
        return self::with([
            'pemberiTugas.role',
            'pemberiTugas.mahasiswa',
            'pemberiTugas.dosen',
            'pemberiTugas.pegawai',
            'jenisSurat'
        ])
            ->where(function ($q) use ($prodiId) {
                // Filter surat yang diajukan oleh mahasiswa dari prodi yang sama
                $q->whereHas('pemberiTugas.mahasiswa', function ($subQ) use ($prodiId) {
                    $subQ->where('Id_Prodi', $prodiId);
                })
                    // ATAU filter surat yang diajukan oleh dosen dari prodi yang sama
                    ->orWhereHas('pemberiTugas.dosen', function ($subQ) use ($prodiId) {
                    $subQ->where('Id_Prodi', $prodiId);
                })
                    // ATAU filter surat yang diajukan oleh pegawai dari prodi yang sama
                    ->orWhereHas('pemberiTugas.pegawai', function ($subQ) use ($prodiId) {
                    $subQ->where('Id_Prodi', $prodiId);
                });
            })
            ->orderBy('Tanggal_Diberikan_Tugas_Surat', 'desc')
            ->get();
    }

    /**
     * Update status tugas berdasarkan ID
     */
    public static function updateStatusById($id, $status)
    {
        $tugas = self::find($id);

        if (!$tugas) {
            return null;
        }

        $tugas->Status = $status;

        // Jika status selesai, set tanggal diselesaikan
        if (strtolower($status) === 'selesai') {
            $tugas->Tanggal_Diselesaikan = Carbon::now();
        } else {
            // Jika status bukan selesai, kosongkan tanggal diselesaikan
            $tugas->Tanggal_Diselesaikan = null;
        }

        $tugas->save();

        return $tugas;
    }

    /**
     * Relasi ke JENIS PEKERJAAN.
     */
    public function jenisPekerjaan()
    {
        // ASUMSI: Primary Key di tabel JenisPekerjaan adalah 'Id_Jenis_Pekerjaan'
        // Jika PK-nya 'id', ganti parameter ketiga menjadi 'id'
        return $this->belongsTo(JenisPekerjaan::class, 'Id_Jenis_Pekerjaan', 'Id_Jenis_Pekerjaan');
    }

    /**
     * Relasi ke FileArsip (one-to-one)
     */

    public static function getArsipSelesai()
    {
        return self::with(['pemberiTugas.role', 'jenisSurat'])
            ->whereRaw("LOWER(TRIM(Status)) = 'selesai'")
            ->orderBy('Tanggal_Diselesaikan', 'desc')
            ->get();
    }

    public function fileArsip()
    {
        return $this->hasOne(FileArsip::class, 'Id_Tugas_Surat', 'Id_Tugas_Surat');
    }

    /**
     * Relasi ke SuratMagang (one-to-one)
     */
    public function suratMagang()
    {
        return $this->hasOne(SuratMagang::class, 'Id_Tugas_Surat', 'Id_Tugas_Surat');
    }

    /**
     * Relasi ke SuratVerification (one-to-one)
     * Untuk mengambil data QR Code dan tanda tangan digital
     */
    public function verification()
    {
        return $this->hasOne(SuratVerification::class, 'id_tugas_surat', 'Id_Tugas_Surat');
    }
}