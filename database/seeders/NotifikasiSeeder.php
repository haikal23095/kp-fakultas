<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Notifikasi;
use App\Models\User;
use Carbon\Carbon;

class NotifikasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil beberapa user untuk contoh
        $users = User::limit(5)->get();

        if ($users->count() < 2) {
            echo "Tidak cukup user untuk membuat notifikasi contoh\n";
            return;
        }

        $notifications = [
            [
                'Tipe_Notifikasi' => 'surat',
                'Pesan' => 'Pengajuan Surat Pengantar Magang Anda telah diterima dan sedang diproses.',
                'Dest_User' => $users[0]->Id_User,
                'Source_User' => $users[1]->Id_User,
                'Is_Read' => false,
                'created_at' => Carbon::now()->subMinutes(5),
            ],
            [
                'Tipe_Notifikasi' => 'approval',
                'Pesan' => 'Surat Keterangan Aktif Kuliah Anda telah disetujui oleh Kaprodi.',
                'Dest_User' => $users[0]->Id_User,
                'Source_User' => $users[1]->Id_User,
                'Is_Read' => false,
                'created_at' => Carbon::now()->subHours(2),
            ],
            [
                'Tipe_Notifikasi' => 'rejection',
                'Pesan' => 'Mohon maaf, pengajuan surat Anda ditolak. Silakan periksa kembali dokumen yang Anda upload.',
                'Dest_User' => $users[0]->Id_User,
                'Source_User' => $users[1]->Id_User,
                'Is_Read' => true,
                'created_at' => Carbon::now()->subDays(1),
            ],
            [
                'Tipe_Notifikasi' => 'info',
                'Pesan' => 'Selamat datang di Sistem Informasi Fakultas. Silakan lengkapi profil Anda.',
                'Dest_User' => $users[0]->Id_User,
                'Source_User' => null, // Notifikasi sistem
                'Is_Read' => true,
                'created_at' => Carbon::now()->subDays(3),
            ],
        ];

        foreach ($notifications as $notification) {
            Notifikasi::create($notification);
        }

        echo "Notifikasi contoh berhasil dibuat!\n";
    }
}
