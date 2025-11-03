@extends('layouts.admin')

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
                        <th class="text-center">Kirim</th>
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

                        {{-- 6. Kirim (admin only): pilih role target dan upload file opsional --}}
                        <td class="text-center">
                            @if(auth()->check() && auth()->user()->Id_Role == 1)
                                <form method="POST" action="{{ route('admin.surat.assign', $tugas->Id_Tugas_Surat) }}" enctype="multipart/form-data" class="d-flex justify-content-center align-items-center">
                                    @csrf
                                    <select name="role_id" class="form-select form-select-sm me-2" style="width:150px;">
                                        @foreach($roles as $role)
                                            <option value="{{ $role->Id_Role }}">{{ $role->Name_Role }}</option>
                                        @endforeach
                                    </select>
                                    <input type="file" name="file" class="form-control form-control-sm me-2" style="width:160px;">
                                    <button type="submit" class="btn btn-sm btn-success">Kirim</button>
                                </form>
                            @else
                                <span class="text-muted">-</span>
                            @endif
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


