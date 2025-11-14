<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ambil semua data Tugas_Surat yang merupakan Surat Pengantar KP/Magang
        // Asumsi: Id_Jenis_Surat untuk "Surat Pengantar KP/Magang" adalah 6
        $tugasSuratMagang = DB::table('Tugas_Surat')
            ->where('Id_Jenis_Surat', 6) // Sesuaikan dengan Id_Jenis_Surat Anda
            ->get();

        foreach ($tugasSuratMagang as $tugas) {
            // Parse data_spesifik (JSON) jika ada
            $dataSpesifik = null;
            if (!empty($tugas->data_spesifik)) {
                $dataSpesifik = json_decode($tugas->data_spesifik, true);
            }

            // Ambil data mahasiswa dari relasi
            $mahasiswa = DB::table('Mahasiswa')
                ->join('Users', 'Mahasiswa.Id_User', '=', 'Users.Id_User')
                ->join('Prodi', 'Mahasiswa.Id_Prodi', '=', 'Prodi.Id_Prodi')
                ->where('Users.Id_User', $tugas->Id_Pemberi_Tugas_Surat)
                ->select('Users.Name_User', 'Mahasiswa.NIM', 'Prodi.Nama_Prodi')
                ->first();

            // Siapkan Data_Mahasiswa (JSON)
            $dataMahasiswa = [
                'nama' => $mahasiswa->Name_User ?? 'Nama tidak ditemukan',
                'nim' => $mahasiswa->NIM ?? 'NIM tidak ditemukan',
                'jurusan' => $mahasiswa->Nama_Prodi ?? 'Jurusan tidak ditemukan',
                'path_tanda_tangan' => $dataSpesifik['path_tanda_tangan'] ?? null,
            ];

            // Siapkan Data_Dosen_pembiming (JSON)
            $dataDosenPembimbing = [
                'dosen_pembimbing_1' => $dataSpesifik['dosen_pembimbing_1'] ?? null,
                'dosen_pembimbing_2' => $dataSpesifik['dosen_pembimbing_2'] ?? null,
            ];

            // Insert ke tabel Surat_Magang
            DB::table('Surat_Magang')->insert([
                'Id_Tugas_Surat' => $tugas->Id_Tugas_Surat,
                'Nomor_Surat' => $tugas->Nomor_Surat ?? null,
                'Data_Mahasiswa' => json_encode($dataMahasiswa, JSON_UNESCAPED_UNICODE),
                'Data_Dosen_pembiming' => json_encode($dataDosenPembimbing, JSON_UNESCAPED_UNICODE),
                'Surat_Pengantar_Fakultas' => null, // Belum ada
                'Dokumen_Proposal' => $tugas->dokumen_pendukung ?? null,
                'Surat_Pengantar_Magang' => null, // Belum ada
            ]);
        }

        // OPTIONAL: Hapus kolom yang sudah tidak dipakai dari Tugas_Surat
        // Uncomment jika ingin menghapus kolom
        /*
        Schema::table('Tugas_Surat', function (Blueprint $table) {
            $table->dropColumn([
                'data_spesifik',
                'dokumen_pendukung',
                'Deskripsi_Tugas_Surat',
                'Nomor_Surat',
                'File_Surat'
            ]);
        });
        */
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Hapus semua data yang sudah dimigrasikan
        DB::table('Surat_Magang')->truncate();

        // OPTIONAL: Restore kolom jika sudah dihapus
        /*
        Schema::table('Tugas_Surat', function (Blueprint $table) {
            $table->text('Deskripsi_Tugas_Surat')->nullable()->after('Judul_Tugas_Surat');
            $table->json('data_spesifik')->nullable()->after('Deskripsi_Tugas_Surat');
            $table->string('dokumen_pendukung', 500)->nullable();
            $table->string('Nomor_Surat', 255)->nullable();
            $table->string('File_Surat', 500)->nullable();
        });
        */
    }
};
