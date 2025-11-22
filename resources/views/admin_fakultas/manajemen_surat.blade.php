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
                        <td>
                            {{ $tugas->Tanggal_Diberikan_Tugas_Surat->format('d M Y') }}
                            @if(optional($tugas->suratKetAktif)->is_urgent)
                                <br><span class="badge bg-danger blink-badge"><i class="fas fa-exclamation-circle me-1"></i> URGENT</span>
                            @endif
                        </td>

                        {{-- 2. Nomor Surat --}}
                        <td>
                            @if($tugas->Nomor_Surat)
                                <span class="fw-bold text-dark">{{ $tugas->Nomor_Surat }}</span>
                            @else
                                <span class="text-muted fst-italic small">Belum ada nomor</span>
                            @endif
                        </td>

                        {{-- 3. Pengaju (DIPERBAIKI) --}}
                        <td>
                            {{-- Menggunakan 'Name_User' dari ERD --}}
                            <div class="fw-bold">{{ $tugas->pemberiTugas->Name_User ?? 'User Dihapus' }}</div>
                            
                            {{-- MENGGANTI 'Nama_Pekerjaan' menjadi 'Jenis_Pekerjaan' --}}
                            {{-- Asumsi tabel Jenis_Pekerjaan punya kolom 'Jenis_Pekerjaan' untuk nama role --}}
                            <small class="text-muted">
                                {{ optional($tugas->pemberiTugas->role)->Name_Role ?? 'Role N/A' }}
                                @if(optional($tugas->pemberiTugas->mahasiswa)->prodi)
                                    - {{ $tugas->pemberiTugas->mahasiswa->prodi->Nama_Prodi }}
                                @endif
                            </small>
                        </td>

                        {{-- 4. Jenis Surat (DIPERBAIKI) --}}
                        <td>
                            {{-- Menggunakan 'Nama_Surat' dari ERD --}}
                            {{ $tugas->jenisSurat->Nama_Surat ?? 'Jenis Dihapus' }}
                            @if(optional($tugas->suratKetAktif)->is_urgent && optional($tugas->suratKetAktif)->urgent_reason)
                                <div class="mt-1 small text-danger border-start border-danger ps-2">
                                    <strong>Alasan:</strong> {{ \Illuminate\Support\Str::limit($tugas->suratKetAktif->urgent_reason, 50) }}
                                </div>
                            @endif
                        </td>

                        {{-- 5. Status (read-only untuk admin) --}}
                        <td class="align-middle text-center">
                            @php 
                                // Prioritas: status dari parent table (Tugas_Surat) 
                                $status = $tugas->Status ?? 'baru';
                                $status = trim($status);
                            @endphp
                            
                            @if(strtolower($status) === 'selesai' || strtolower($status) === 'disetujui' || strtolower($status) === 'success')
                                <span class="badge rounded-pill bg-success px-3 py-2"><i class="fas fa-check me-1"></i> {{ $status }}</span>
                            @elseif(strtolower($status) === 'terlambat' || strtolower($status) === 'ditolak')
                                <span class="badge rounded-pill bg-danger px-3 py-2"><i class="fas fa-times me-1"></i> {{ $status }}</span>
                            @elseif(strtolower($status) === 'proses' || strtolower($status) === 'dikerjakan-admin')
                                <span class="badge rounded-pill bg-primary px-3 py-2"><i class="fas fa-spinner fa-spin me-1"></i> {{ $status }}</span>
                            @elseif(strtolower($status) === 'diajukan-ke-koordinator')
                                <span class="badge rounded-pill bg-info text-dark px-3 py-2"><i class="fas fa-user-tie me-1"></i> Ke Koordinator</span>
                            @elseif(strtolower($status) === 'diajukan-ke-dekan')
                                <span class="badge rounded-pill bg-warning text-dark px-3 py-2"><i class="fas fa-signature me-1"></i> Ke Dekan</span>
                            @else
                                <span class="badge rounded-pill bg-secondary px-3 py-2">{{ $status }}</span>
                            @endif
                        </td>

                        {{-- 6. Aksi --}}
                        <td class="text-center">
                            <a href="{{ route('admin_fakultas.surat.detail', $tugas->Id_Tugas_Surat) }}" class="btn btn-sm btn-outline-primary shadow-sm">
                                <i class="fas fa-eye me-1"></i> Detail
                            </a>
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


