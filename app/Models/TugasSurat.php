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
    public $incrementing = true; // Auto-increment enabled
    protected $keyType = 'int';

    protected $fillable = [
        'Id_Pemberi_Tugas_Surat',
        'Id_Penerima_Tugas_Surat',
        'Id_Jenis_Surat',
        'Id_Jenis_Pekerjaan',
        'Judul_Tugas_Surat',
        'Nomor_Surat',
        'data_spesifik',
        'signature_qr_data',
        'qr_image_path',
        // 'Status', // Kolom ini sudah dipindah ke tabel spesifik (Surat_Magang, dll)
        'Tanggal_Diberikan_Tugas_Surat',
        'Tanggal_Tenggat_Tugas_Surat',
        'Tanggal_Diselesaikan',
    ];

    /**
     * Mengubah kolom Tanggal secara otomatis.
     */
    protected $casts = [
        'Tanggal_Diberikan_Tugas_Surat' => 'date',
        'Tanggal_Tenggat_Tugas_Surat' => 'date',
        'Tanggal_Diselesaikan' => 'date',
        'data_spesifik' => 'array',
        'signature_qr_data' => 'array',
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
     * CATATAN: Setelah normalisasi, Status ada di tabel spesifik seperti Surat_Magang
     * Method ini perlu disesuaikan per jenis surat jika masih digunakan
     */
    public static function updateStatusTerlambat()
    {
        // TODO: Implementasi per jenis surat (Surat_Magang, dll)
        // Untuk sementara tidak melakukan update karena Status sudah dipindah
        return 0;
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
     * CATATAN: Setelah normalisasi, Status ada di tabel spesifik (Surat_Magang, dll)
     * Method ini perlu memanggil update pada tabel spesifik yang sesuai
     */
    public static function updateStatusById($id, $status)
    {
        $tugas = self::find($id);

        if (!$tugas) {
            return null;
        }

        // Update status di tabel spesifik berdasarkan jenis surat
        // Contoh untuk Surat Magang:
        if ($tugas->suratMagang) {
            $tugas->suratMagang->Status = $status;
            $tugas->suratMagang->save();
        }

        // Jika status selesai, set tanggal diselesaikan
        if (strtolower($status) === 'success' || strtolower($status) === 'selesai') {
            $tugas->Tanggal_Diselesaikan = Carbon::now();
        } else {
            // Jika status bukan selesai, kosongkan tanggal diselesaikan
            $tugas->Tanggal_Diselesaikan = null;
        }

        $tugas->save();

        return $tugas;
    }

    /**
     * Accessor untuk mendapatkan Status dari tabel child (Surat_Magang, dll)
     * Ini memungkinkan pemanggilan $tugasSurat->Status di view/controller
     */
    public function getStatusAttribute()
    {
        // Prioritaskan status di tabel utama (Tugas_Surat)
        if (array_key_exists('Status', $this->attributes) && !is_null($this->attributes['Status']) && $this->attributes['Status'] !== '') {
            return $this->attributes['Status'];
        }

        // Cek status dari tabel child jika ada
        if ($this->relationLoaded('suratMagang') && $this->suratMagang) {
            return $this->suratMagang->Status;
        }

        // if ($this->suratKetAktif) { return $this->suratKetAktif->Status; }

        return null;
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
        return self::with(['pemberiTugas.role', 'jenisSurat', 'suratMagang'])
            ->whereHas('suratMagang', function ($query) {
                $query->where('Status', 'Success');
            })
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
     * Relasi ke SuratKetAktif (one-to-one)
     */
    public function suratKetAktif()
    {
        return $this->hasOne(SuratKetAktif::class, 'Id_Tugas_Surat', 'Id_Tugas_Surat');
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