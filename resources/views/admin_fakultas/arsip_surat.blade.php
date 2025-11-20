@extends('layouts.admin_fakultas')

@section('title', 'Arsip Surat')

@section('content')
<h1 class="h3 mb-4 text-gray-800">Arsip Surat Fakultas</h1>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Arsip & Riwayat Surat</h6>
    </div>
    <div class="card-body">
        {{-- Flash messages --}}
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        {{-- Ringkasan cepat --}}
        <div class="mb-3">
            <strong>Total Arsip:</strong> {{ isset($arsipTugas) ? $arsipTugas->count() : 0 }}
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                <thead class="table-light">
                    <tr>
                        <th>Tgl. Selesai</th>
                        <th>No. Surat</th>
                        <th>Pengaju</th>
                        <th>Jenis Surat</th>
                        <th class="text-center">Status Akhir</th>
                        <th class="text-center">File</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($arsipTugas) && $arsipTugas->count())
                        @foreach($arsipTugas as $t)
                        <tr>
                            <td>{{ optional($t->Tanggal_Diselesaikan)->format('d M Y') ?? '-' }}</td>
                            <td>{{ $t->Nomor_Surat ?? '-' }}</td>
                            <td>{{ optional($t->pemberiTugas)->Name_User ?? 'User Dihapus' }}</td>
                            <td>{{ optional($t->jenisSurat)->Nama_Surat ?? 'Jenis Dihapus' }}</td>
                            <td class="text-center">
                                @php
                                    $status = trim($t->Status ?? '');
                                @endphp
                                @if(strtolower($status) === 'selesai' || strtolower($status) === 'disetujui')
                                    <span class="badge bg-success">{{ $t->Status }}</span>
                                @elseif(strtolower($status) === 'ditolak')
                                    <span class="badge bg-danger">{{ $t->Status }}</span>
                                @else
                                    <span class="badge bg-secondary">{{ $t->Status }}</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if(!empty($t->File_Surat))
                                    <a href="{{ asset('storage/' . ltrim($t->File_Surat, '/')) }}" target="_blank" class="btn btn-primary btn-sm" title="Unduh File"><i class="fas fa-download"></i></a>
                                @elseif(!empty($t->dokumen_pendukung))
                                    <a href="{{ asset('storage/' . ltrim($t->dokumen_pendukung, '/')) }}" target="_blank" class="btn btn-primary btn-sm" title="Unduh Dokumen"><i class="fas fa-download"></i></a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="text-center">Belum ada arsip tersedia.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection