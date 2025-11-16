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
        // Tambah kolom Nama_Instansi jika belum ada
        if (!Schema::hasColumn('Surat_Magang', 'Nama_Instansi')) {
            Schema::table('Surat_Magang', function (Blueprint $table) {
                $table->string('Nama_Instansi', 255)->nullable()->after('Id_Tugas_Surat');
            });
        }

        // Update data existing dengan nama instansi acak
        $instansiList = [
            'PT Telkom Indonesia',
            'PT Bank Central Asia',
            'PT Google Indonesia',
            'PT Microsoft Indonesia',
            'PT Gojek Indonesia',
            'PT Tokopedia',
            'PT Shopee Indonesia',
            'PT Bukalapak',
            'Kementerian Komunikasi dan Informatika',
            'PT Pertamina (Persero)',
        ];

        $suratMagang = DB::table('Surat_Magang')->get();

        foreach ($suratMagang as $index => $surat) {
            // Pilih instansi secara acak atau berurutan
            $namaInstansi = $instansiList[$index % count($instansiList)];

            // Update Data_Mahasiswa untuk menambah semester
            $dataMahasiswa = json_decode($surat->Data_Mahasiswa, true);

            // Ubah menjadi array of mahasiswa (support multiple)
            if (!isset($dataMahasiswa[0])) {
                // Jika masih single object, convert ke array
                $dataMahasiswa = [
                    [
                        'nama' => $dataMahasiswa['nama'] ?? '',
                        'nim' => $dataMahasiswa['nim'] ?? '',
                        'jurusan' => $dataMahasiswa['jurusan'] ?? '',
                        'semester' => rand(6, 8), // Semester acak 6-8
                        'path_tanda_tangan' => $dataMahasiswa['path_tanda_tangan'] ?? null,
                    ]
                ];
            }

            // Update record
            DB::table('Surat_Magang')
                ->where('id_no', $surat->id_no)
                ->update([
                        'Nama_Instansi' => $namaInstansi,
                        'Data_Mahasiswa' => json_encode($dataMahasiswa, JSON_UNESCAPED_UNICODE),
                    ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('Surat_Magang', function (Blueprint $table) {
            $table->dropColumn('Nama_Instansi');
        });
    }
};
