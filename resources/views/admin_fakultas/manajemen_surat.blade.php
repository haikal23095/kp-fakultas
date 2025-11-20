@extends('layouts.admin_fakultas')

@section('title', 'Manajemen Surat')

@section('content')
<h1 class="h3 mb-4 text-gray-800">Manajemen Surat Fakultas</h1>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Semua Surat Aktif</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                <thead class="table-light">
                    <tr>
                        <th>Tgl. Masuk</th>
                        <th>Nomor Surat</th>
                        <th>Pengaju</th>
                        <th>Jenis Surat</th>
                        <th>Proses Saat Ini</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        // Sembunyikan tugas yang sudah selesai dari tampilan manajemen
                        $visibleTugas = $daftarTugas->filter(function($t) {
                            return strtolower(trim($t->Status)) !== 'selesai';
                        });
                    @endphp

                    @forelse($visibleTugas as $tugas)
                    <tr>
                        {{-- 1. Tgl. Masuk --}}
                        <td>{{ $tugas->Tanggal_Diberikan_Tugas_Surat->format('d M Y') }}</td>

                        {{-- 2. Nomor Surat --}}
                        <td>{{ $tugas->Nomor_Surat }}</td>

                        {{-- 3. Pengaju (DIPERBAIKI) --}}
                        <td>
                            {{-- Menggunakan 'Name_User' dari ERD --}}
                            {{ $tugas->pemberiTugas->Name_User ?? 'User Dihapus' }}
                            
                            {{-- MENGGANTI 'Nama_Pekerjaan' menjadi 'Jenis_Pekerjaan' --}}
                            {{-- Asumsi tabel Jenis_Pekerjaan punya kolom 'Jenis_Pekerjaan' untuk nama role --}}
                            ({{ optional($tugas->pemberiTugas->role)->Name_Role ?? 'Role N/A' }})
                        </td>

                        {{-- 4. Jenis Surat (DIPERBAIKI) --}}
                        <td>
                            {{-- Menggunakan 'Nama_Surat' dari ERD --}}
                            {{ $tugas->jenisSurat->Nama_Surat ?? 'Jenis Dihapus' }}
                        </td>

                        {{-- 5. Status (read-only untuk admin) --}}
                        <td class="align-middle text-center">
                            @php $status = trim($tugas->Status ?? ''); @endphp
                            @if(strtolower($status) === 'selesai' || strtolower($status) === 'disetujui')
                                <span class="badge bg-success">{{ $tugas->Status }}</span>
                            @elseif(strtolower($status) === 'terlambat')
                                <span class="badge bg-danger">{{ $tugas->Status }}</span>
                            @elseif(strtolower($status) === 'proses')
                                <span class="badge bg-primary">{{ $tugas->Status }}</span>
                            @else
                                <span class="badge bg-secondary">{{ $tugas->Status }}</span>
                            @endif
                        </td>

                        {{-- 6. Aksi --}}
                        <td class="text-center">
                            <a href="{{ route('admin.surat.detail', $tugas->Id_Tugas_Surat) }}" class="btn btn-sm btn-info">Lihat Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center">Tidak ada data surat aktif.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection


