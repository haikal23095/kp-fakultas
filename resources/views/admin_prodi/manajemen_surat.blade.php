@extends('layouts.admin_prodi')

@section('title', 'Manajemen Surat')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center my-4">
        <div>
            <h1 class="h3 fw-bold text-gray-800">Manajemen Surat</h1>
            <p class="text-muted mb-0">Proses surat yang telah disetujui Kaprodi dan tambahkan nomor surat.</p>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.admin_prodi') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Manajemen Surat</li>
            </ol>
        </nav>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- KP/Magang Tabs --}}
    <ul class="nav nav-tabs" id="magangTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="magang-pending-tab" data-bs-toggle="tab" data-bs-target="#magang-pending" type="button" role="tab" aria-controls="magang-pending" aria-selected="true">
                Perlu Nomor (KP/Magang)
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="magang-semua-tab" data-bs-toggle="tab" data-bs-target="#magang-semua" type="button" role="tab" aria-controls="magang-semua" aria-selected="false">
                Semua (Belum Selesai)
            </button>
        </li>
    </ul>

    <div class="tab-content border border-top-0 p-3 bg-white shadow-sm rounded-bottom" id="magangTabsContent">
        {{-- Tab: Perlu Nomor --}}
        <div class="tab-pane fade show active" id="magang-pending" role="tabpanel" aria-labelledby="magang-pending-tab">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-secondary text-uppercase small fw-bold">
                        <tr>
                            <th class="px-4 py-3">Mahasiswa</th>
                            <th class="py-3">Instansi</th>
                            <th class="py-3">Tanggal Pengajuan</th>
                            <th class="py-3 text-center">Status</th>
                            <th class="py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($suratMagangPending as $surat)
                            @php
                                $mahasiswa = $surat->tugasSurat->pemberiTugas->mahasiswa ?? null;
                                $tugas = $surat->tugasSurat;
                                $statusTugas = strtolower(trim($tugas->Status ?? ''));
                                $nomor = $tugas->Nomor_Surat ?? null;
                                $statusUi = $statusTugas;
                                if($nomor && $statusTugas === 'dikerjakan-admin') { $statusUi = 'menunggu-ttd'; }
                            @endphp
                            <tr>
                                <td class="px-4">
                                    <div class="fw-bold text-dark">{{ $mahasiswa?->Nama_Mahasiswa ?? 'N/A' }}</div>
                                    <div class="small text-muted">NIM: {{ $mahasiswa?->NIM ?? 'N/A' }}</div>
                                </td>
                                <td>{{ $surat->Nama_Instansi ?? '-' }}</td>
                                <td>{{ $tugas->Tanggal_Diberikan_Tugas_Surat?->format('d M Y') ?? '-' }}</td>
                                <td class="text-center">
                                    @if($statusUi === 'dikerjakan-admin')
                                        <span class="badge bg-warning text-dark">Perlu Nomor</span>
                                    @elseif($statusUi === 'menunggu-ttd')
                                        <span class="badge bg-info text-white">Menunggu TTD Dekan</span>
                                    @else
                                        <span class="badge bg-secondary text-white">{{ $tugas->Status ?? '-' }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin_prodi.surat.preview_magang', $surat->id_no) }}" target="_blank" rel="noopener" class="btn btn-outline-info btn-sm">
                                            <i class="fas fa-eye me-1"></i> Preview
                                        </a>
                                        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#nomorMagangModal{{ $tugas->Id_Tugas_Surat }}">
                                            <i class="fas fa-plus-circle me-1"></i> Tambah Nomor
                                        </button>
                                    </div>
                                    {{-- Modal Tambah Nomor Surat --}}
                                    <div class="modal fade" id="nomorMagangModal{{ $tugas->Id_Tugas_Surat }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header bg-success text-white">
                                                    <h5 class="modal-title fw-bold">Tambah Nomor Surat KP/Magang</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form action="{{ route('admin_prodi.surat.add_nomor', $tugas->Id_Tugas_Surat) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">Mahasiswa</label>
                                                            <input type="text" class="form-control" value="{{ $mahasiswa?->Nama_Mahasiswa }}" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">Nomor Surat <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control" name="nomor_surat" placeholder="Contoh: 456/UN46.FT/KM/2025" required>
                                                            <div class="form-text">Format: [No]/UN46.FT/KM/[Tahun]</div>
                                                        </div>
                                                        <div class="alert alert-info mb-0">
                                                            <i class="fas fa-info-circle me-2"></i>
                                                            Setelah nomor disimpan: Mahasiswa akan mendapat notifikasi dan surat diteruskan ke Dekan untuk tanda tangan.
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer bg-light">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-success fw-bold">
                                                            <i class="fas fa-check me-2"></i>Simpan & Teruskan ke Mahasiswa
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                    <p>Tidak ada surat KP/Magang yang perlu diberi nomor.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Tab: Semua (Belum Selesai) --}}
        <div class="tab-pane fade" id="magang-semua" role="tabpanel" aria-labelledby="magang-semua-tab">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-secondary text-uppercase small fw-bold">
                        <tr>
                            <th class="px-4 py-3">Mahasiswa</th>
                            <th class="py-3">Instansi</th>
                            <th class="py-3">Nomor Surat</th>
                            <th class="py-3" style="width: 160px;">Status</th>
                            <th class="py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($suratMagangSemua as $surat)
                            @php
                                $mahasiswa = $surat->tugasSurat->pemberiTugas->mahasiswa ?? null;
                                $tugas = $surat->tugasSurat;
                                $statusTugas = strtolower(trim($tugas->Status ?? ''));
                                $nomor = $tugas->Nomor_Surat ?? null;
                                $statusUi = $statusTugas;
                                if($nomor && $statusTugas === 'dikerjakan-admin') { $statusUi = 'menunggu-ttd'; }
                            @endphp
                            <tr>
                                <td class="px-4">
                                    <div class="fw-bold text-dark">{{ $mahasiswa?->Nama_Mahasiswa ?? 'N/A' }}</div>
                                    <div class="small text-muted">NIM: {{ $mahasiswa?->NIM ?? 'N/A' }}</div>
                                </td>
                                <td>{{ $surat->Nama_Instansi ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-light text-dark border">{{ $nomor ?? 'Belum ada nomor' }}</span>
                                </td>
                                <td>
                                    @if($statusUi === 'dikerjakan-admin')
                                        <span class="badge bg-warning text-dark">Perlu Nomor</span>
                                    @elseif($statusUi === 'menunggu-ttd')
                                        <span class="badge bg-info text-white">Menunggu TTD Dekan</span>
                                    @else
                                        <span class="badge bg-secondary text-white">{{ $tugas->Status ?? '-' }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin_prodi.surat.preview_magang', $surat->id_no) }}" target="_blank" rel="noopener" class="btn btn-outline-info btn-sm">
                                            <i class="fas fa-eye me-1"></i> Preview
                                        </a>
                                        @if(!$nomor)
                                        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#nomorMagangModal{{ $tugas->Id_Tugas_Surat }}">
                                            <i class="fas fa-plus-circle me-1"></i> Tambah Nomor
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <p>Tidak ada surat KP/Magang.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<style>
    .paper-preview {
        background: white;
        width: 100%;
        max-width: 210mm;
        min-height: 297mm;
        padding: 20mm;
        margin: 0 auto;
        border: 1px solid #d3d3d3;
        font-family: 'Times New Roman', Times, serif;
    }
</style>
@endsection



