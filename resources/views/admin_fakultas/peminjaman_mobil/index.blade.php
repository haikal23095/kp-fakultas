@extends('layouts.admin_fakultas')

@section('title', 'Manajemen Peminjaman Mobil Dinas')

@push('styles')
<style>
    .page-header {
        background: #ffffff;
        border-bottom: 2px solid #f0f0f0;
        padding: 2rem 0;
        margin-bottom: 2rem;
    }
    
    .badge-status {
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        font-weight: 600;
        border-radius: 8px;
    }
    
    .table-responsive {
        border-radius: 12px;
        overflow: hidden;
    }
    
    .table thead th {
        background: #f8f9fc;
        color: #5a5c69;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        border: none;
    }
    
    .table tbody tr {
        transition: background-color 0.2s ease;
    }
    
    .table tbody tr:hover {
        background-color: #f8f9fc;
    }
    
    .btn-action {
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        border-radius: 8px;
        font-weight: 600;
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h3 class="mb-2 fw-bold text-dark">Manajemen Peminjaman Mobil Dinas</h3>
            <p class="mb-0 text-muted">Kelola pengajuan peminjaman mobil dinas fakultas</p>
        </div>
        <div>
            <a href="{{ route('admin_fakultas.kendaraan.index') }}" class="btn btn-primary me-2">
                <i class="fas fa-car me-2"></i>Kelola Kendaraan
            </a>
            <a href="{{ route('admin_fakultas.surat.manage') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert" style="border-radius: 12px;">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert" style="border-radius: 12px;">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Header Section Only --}}
<div class="mb-4">
    <h5 class="text-primary">
        <i class="fas fa-clock me-2"></i>Pengajuan Baru
        <span class="badge bg-warning text-dark ms-2">{{ $pengajuan->count() }}</span>
    </h5>
</div>

{{-- Content Section --}}
<div class="card shadow-sm">
            <div class="card-header py-3 bg-white">
                <h6 class="m-0 font-weight-bold text-primary">Daftar Pengajuan Menunggu Verifikasi</h6>
            </div>
            <div class="card-body">
                @if($pengajuan->isEmpty())
                    <div class="text-center py-5">
                        <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">Tidak ada pengajuan baru</h5>
                        <p class="text-muted">Semua pengajuan sudah diverifikasi</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Tanggal Pengajuan</th>
                                    <th>Peminjam</th>
                                    <th>Tujuan</th>
                                    <th>Tanggal Pemakaian</th>
                                    <th>Penumpang</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pengajuan as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <div class="fw-bold">{{ $item->created_at->format('d M Y') }}</div>
                                            <small class="text-muted">{{ $item->created_at->format('H:i') }} WIB</small>
                                        </td>
                                        <td>
                                            <div class="fw-bold">{{ $item->user->Name_User ?? 'N/A' }}</div>
                                            <small class="text-muted">
                                                {{ $item->user->role->Name_Role ?? '' }}
                                                @if($item->user->mahasiswa)
                                                    - {{ $item->user->mahasiswa->NIM ?? '' }}
                                                @endif
                                            </small>
                                        </td>
                                        <td>{{ Str::limit($item->tujuan, 30) }}</td>
                                        <td>
                                            <small>
                                                <strong>Mulai:</strong><br>
                                                {{ \Carbon\Carbon::parse($item->tanggal_pemakaian_mulai)->format('d M Y H:i') }}<br>
                                                <strong>Selesai:</strong><br>
                                                {{ \Carbon\Carbon::parse($item->tanggal_pemakaian_selesai)->format('d M Y H:i') }}
                                            </small>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-info">{{ $item->jumlah_penumpang }} orang</span>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-info btn-action me-1" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#detailModal{{ $item->id }}">
                                                <i class="fas fa-eye me-1"></i>Detail
                                            </button>
                                            <button type="button" class="btn btn-sm btn-primary btn-action" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#prosesModal{{ $item->id }}">
                                                <i class="fas fa-tasks me-1"></i>Proses
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Modal Section - Outside Table --}}
                    @foreach($pengajuan as $item)
                        {{-- Modal Detail --}}
                                    <div class="modal fade" id="detailModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-xl">
                                            <div class="modal-content">
                                                <div class="modal-header bg-info text-white">
                                                    <h5 class="modal-title">
                                                        <i class="fas fa-file-alt me-2"></i>Detail & Preview Pengajuan Peminjaman Mobil
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        {{-- Kolom Kiri: Info Peminjam --}}
                                                        <div class="col-lg-4">
                                                            {{-- Info Peminjam --}}
                                                            <div class="card border-0 shadow-sm mb-3">
                                                                <div class="card-header bg-light">
                                                                    <h6 class="mb-0"><i class="fas fa-user me-2"></i>Info Peminjam</h6>
                                                                </div>
                                                                <div class="card-body">
                                                                <hr>
                                                                <div class="row mb-2">
                                                                    <div class="col-5 fw-bold">Peminjam:</div>
                                                                    <div class="col-7">{{ $item->user->Name_User }}</div>
                                                                </div>
                                                                <div class="row mb-2">
                                                                    <div class="col-5 fw-bold">NIM/NIP:</div>
                                                                    <div class="col-7">{{ $item->user->mahasiswa->NIM ?? $item->user->Id_User }}</div>
                                                                </div>
                                                                <div class="row mb-2">
                                                                    <div class="col-5 fw-bold">Tujuan:</div>
                                                                    <div class="col-7">{{ $item->tujuan }}</div>
                                                                </div>
                                                                <div class="row mb-2">
                                                                    <div class="col-5 fw-bold">Keperluan:</div>
                                                                    <div class="col-7">{{ $item->keperluan }}</div>
                                                                </div>
                                                                <div class="row mb-2">
                                                                    <div class="col-5 fw-bold">Tanggal Mulai:</div>
                                                                    <div class="col-7">{{ \Carbon\Carbon::parse($item->tanggal_pemakaian_mulai)->format('d M Y H:i') }}</div>
                                                                </div>
                                                                <div class="row mb-2">
                                                                    <div class="col-5 fw-bold">Tanggal Selesai:</div>
                                                                    <div class="col-7">{{ \Carbon\Carbon::parse($item->tanggal_pemakaian_selesai)->format('d M Y H:i') }}</div>
                                                                </div>
                                                                    <div class="row">
                                                                        <div class="col-5 fw-bold">Penumpang:</div>
                                                                        <div class="col-7">{{ $item->jumlah_penumpang }} orang</div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            {{-- Status --}}
                                                            <div class="card border-0 shadow-sm">
                                                                <div class="card-header bg-light">
                                                                    <h6 class="mb-0"><i class="fas fa-flag me-2"></i>Status Pengajuan</h6>
                                                                </div>
                                                                <div class="card-body">
                                                                    <div class="d-flex align-items-center">
                                                                        <span class="badge bg-warning text-dark fs-6 px-3 py-2">
                                                                            <i class="fas fa-clock me-2"></i>{{ $item->status_pengajuan }}
                                                                        </span>
                                                                    </div>
                                                                    <small class="text-muted d-block mt-2">
                                                                        <i class="fas fa-calendar me-1"></i>Diajukan: {{ $item->created_at->format('d M Y H:i') }}
                                                                    </small>
                                                                </div>
                                                            </div>

                                                            {{-- Catatan Admin (jika ada) --}}
                                                            @if($item->rekomendasi_admin)
                                                            <div class="card border-0 shadow-sm mt-3">
                                                                <div class="card-header bg-light">
                                                                    <h6 class="mb-0"><i class="fas fa-comment-alt me-2"></i>Catatan Admin</h6>
                                                                </div>
                                                                <div class="card-body">
                                                                    <p class="mb-0 text-muted small">{{ $item->rekomendasi_admin }}</p>
                                                                </div>
                                                            </div>
                                                            @endif
                                                        </div>

                                                        {{-- Kolom Kanan: Preview Draft Surat --}}
                                                        <div class="col-lg-8">
                                                            <div class="card border-0 shadow-sm">
                                                                <div class="card-header bg-light">
                                                                    <h6 class="mb-0">
                                                                        <i class="fas fa-file-alt me-2"></i>Preview Draft Surat
                                                                        <span class="badge bg-warning text-dark ms-2">DRAFT</span>
                                                                    </h6>
                                                                </div>
                                                                <div class="card-body p-4" style="max-height: 600px; overflow-y: auto;">
                                                                    @php
                                                                        // Ambil data Wadek2 dari fakultas yang sama
                                                                        $fakultasId = $item->user->mahasiswa->prodi->jurusan->fakultas->id ?? null;
                                                                        $wadek2 = null;
                                                                        if ($fakultasId) {
                                                                            $wadek2 = \App\Models\Pejabat::where('Jabatan', 'Wakil Dekan II')
                                                                                ->where('Id_Fakultas', $fakultasId)
                                                                                ->first();
                                                                        }
                                                                    @endphp
                                                                    
                                                                    {{-- Kop Surat --}}
                                                                    <div class="text-center mb-4">
                                                                        <img src="{{ asset('images/logo-ubhara.png') }}" alt="Logo" style="width: 80px; height: auto;">
                                                                        <h6 class="fw-bold mb-0 mt-2">UNIVERSITAS BHAYANGKARA SURABAYA</h6>
                                                                        <p class="small mb-0">Fakultas {{ $item->user->mahasiswa->prodi->jurusan->fakultas->Nama_Fakultas ?? 'Nama Fakultas' }}</p>
                                                                        <p class="small mb-0">Jl. Ahmad Yani 114 Surabaya</p>
                                                                        <p class="small">Telp: (031) 8285491, Fax: (031) 8285492</p>
                                                                        <hr style="border: 2px solid #000; margin-top: 0.5rem;">
                                                                    </div>

                                                                    {{-- Judul Surat --}}
                                                                    <div class="text-center mb-3">
                                                                        <h6 class="fw-bold text-decoration-underline">SURAT PEMINJAMAN MOBIL DINAS</h6>
                                                                        <p class="mb-0">Nomor: <span class="text-muted fst-italic">[Akan diisi setelah persetujuan]</span></p>
                                                                    </div>

                                                                    {{-- Isi Surat --}}
                                                                    <div class="mb-3">
                                                                        <p class="mb-2">Yang bertanda tangan di bawah ini:</p>
                                                                        <table class="table table-sm table-borderless" style="font-size: 0.9rem;">
                                                                            <tr>
                                                                                <td width="150">Nama</td>
                                                                                <td width="10">:</td>
                                                                                <td>{{ $item->user->Name_User }}</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>NIM/NIP</td>
                                                                                <td>:</td>
                                                                                <td>{{ $item->user->mahasiswa->NIM ?? $item->user->Id_User }}</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>Program Studi</td>
                                                                                <td>:</td>
                                                                                <td>{{ $item->user->mahasiswa->prodi->Nama_Prodi ?? '-' }}</td>
                                                                            </tr>
                                                                        </table>
                                                                    </div>

                                                                    <p class="mb-2">Dengan ini mengajukan permohonan peminjaman mobil dinas dengan rincian sebagai berikut:</p>
                                                                    
                                                                    <table class="table table-sm table-borderless" style="font-size: 0.9rem;">
                                                                        <tr>
                                                                            <td width="180">Tujuan Perjalanan</td>
                                                                            <td width="10">:</td>
                                                                            <td><strong>{{ $item->tujuan }}</strong></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Keperluan</td>
                                                                            <td>:</td>
                                                                            <td>{{ $item->keperluan }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Tanggal Pemakaian</td>
                                                                            <td>:</td>
                                                                            <td>{{ \Carbon\Carbon::parse($item->tanggal_pemakaian_mulai)->format('d F Y, H:i') }} WIB<br>
                                                                                s/d {{ \Carbon\Carbon::parse($item->tanggal_pemakaian_selesai)->format('d F Y, H:i') }} WIB</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Jumlah Penumpang</td>
                                                                            <td>:</td>
                                                                            <td>{{ $item->jumlah_penumpang }} orang</td>
                                                                        </tr>
                                                                        @if($item->kendaraan)
                                                                        <tr>
                                                                            <td>Kendaraan yang Dipinjam</td>
                                                                            <td>:</td>
                                                                            <td><strong>{{ $item->kendaraan->nama_kendaraan }} ({{ $item->kendaraan->nomor_polisi }})</strong></td>
                                                                        </tr>
                                                                        @endif
                                                                    </table>

                                                                    {{-- Catatan Admin --}}
                                                                    @if($item->rekomendasi_admin)
                                                                    <div class="mt-3 p-3" style="background-color: #f8f9fa; border-left: 4px solid #0d6efd; border-radius: 4px;">
                                                                        <p class="mb-1 small fw-bold"><i class="fas fa-comment-dots me-2"></i>Catatan Admin:</p>
                                                                        <p class="mb-0 small">{{ $item->rekomendasi_admin }}</p>
                                                                    </div>
                                                                    @endif

                                                                    {{-- TTD Section --}}
                                                                    <div class="mt-4">
                                                                        <div class="row">
                                                                            <div class="col-6">
                                                                                <p class="mb-1 small">Diajukan oleh,</p>
                                                                                <p class="mb-5 small">Peminjam</p>
                                                                                <p class="mb-0 small fw-bold">{{ $item->user->Name_User }}</p>
                                                                                <p class="small">{{ $item->user->mahasiswa->NIM ?? $item->user->Id_User }}</p>
                                                                            </div>
                                                                            <div class="col-6 text-end">
                                                                                <p class="mb-1 small">Surabaya, {{ \Carbon\Carbon::now()->format('d F Y') }}</p>
                                                                                <p class="mb-1 small">Disetujui oleh,</p>
                                                                                <p class="mb-5 small">Wakil Dekan II</p>
                                                                                @if($wadek2)
                                                                                    <p class="mb-0 small fw-bold">{{ $wadek2->pegawai->Nama_Pegawai ?? $wadek2->Nama_Pejabat }}</p>
                                                                                    <p class="small">NIP. {{ $wadek2->pegawai->NIP ?? '-' }}</p>
                                                                                @else
                                                                                    <p class="mb-0 small fw-bold text-muted">[Nama Wakil Dekan II]</p>
                                                                                    <p class="small text-muted">NIP. [NIP]</p>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    {{-- Watermark Draft --}}
                                                                    <div class="text-center mt-3 p-2" style="background-color: #fff3cd; border: 2px dashed #ffc107; border-radius: 8px;">
                                                                        <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                                                                        <small class="text-muted fst-italic">Dokumen ini adalah DRAFT dan belum memiliki nomor surat resmi</small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                        <i class="fas fa-times me-2"></i>Tutup
                                                    </button>
                                                    <button type="button" class="btn btn-primary" 
                                                            data-bs-dismiss="modal"
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#prosesModal{{ $item->id }}">
                                                        <i class="fas fa-tasks me-2"></i>Proses Verifikasi
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Modal Proses Verifikasi --}}
                                    <div class="modal fade" id="prosesModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header bg-primary text-white">
                                                    <h5 class="modal-title">
                                                        <i class="fas fa-clipboard-check me-2"></i>Verifikasi Peminjaman Mobil
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    {{-- Info Singkat --}}
                                                    <div class="alert alert-info">
                                                        <h6><i class="fas fa-info-circle me-2"></i>Info Pengajuan</h6>
                                                        <hr>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <small><strong>Peminjam:</strong></small><br>
                                                                {{ $item->user->Name_User }}<br>
                                                                <small><strong>Tujuan:</strong></small><br>
                                                                {{ $item->tujuan }}
                                                            </div>
                                                            <div class="col-md-6">
                                                                <small><strong>Tanggal:</strong></small><br>
                                                                {{ \Carbon\Carbon::parse($item->tanggal_pemakaian_mulai)->format('d M Y H:i') }}<br>
                                                                s/d<br>
                                                                {{ \Carbon\Carbon::parse($item->tanggal_pemakaian_selesai)->format('d M Y H:i') }}
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- Form Verifikasi --}}
                                                    <form action="{{ route('admin_fakultas.peminjaman_mobil.verifikasi', $item->id) }}" method="POST" id="formVerifikasi{{ $item->id }}">
                                                        @csrf
                                                        @method('PUT')

                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">
                                                                <i class="fas fa-car me-2"></i>Pilih Kendaraan yang Tersedia
                                                            </label>
                                                            @php
                                                                $kendaraanTersedia = \App\Models\Kendaraan::tersedia()
                                                                    ->get()
                                                                    ->filter(function($kendaraan) use ($item) {
                                                                        return !\App\Models\SuratPeminjamanMobil::isTanggalBentrok(
                                                                            $kendaraan->id,
                                                                            $item->tanggal_pemakaian_mulai,
                                                                            $item->tanggal_pemakaian_selesai
                                                                        );
                                                                    });
                                                            @endphp

                                                            @if($kendaraanTersedia->isEmpty())
                                                                <div class="alert alert-danger">
                                                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                                                    <strong>Tidak ada kendaraan tersedia!</strong>
                                                                    <p class="mb-0 mt-2">Semua kendaraan sedang digunakan atau maintenance pada tanggal yang dipilih. Silakan tolak pengajuan atau minta peminjam untuk mengubah tanggal.</p>
                                                                </div>
                                                            @else
                                                                <select class="form-select" name="Id_Kendaraan" id="kendaraan{{ $item->id }}" required>
                                                                    <option value="">-- Pilih Kendaraan --</option>
                                                                    @foreach($kendaraanTersedia as $kendaraan)
                                                                        <option value="{{ $kendaraan->id }}">
                                                                            {{ $kendaraan->nama_kendaraan }} ({{ $kendaraan->plat_nomor }}) - Kapasitas: {{ $kendaraan->kapasitas }} orang
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                                <small class="text-muted">
                                                                    <i class="fas fa-info-circle me-1"></i>
                                                                    Hanya menampilkan kendaraan yang tersedia dan tidak bertabrakan jadwal
                                                                </small>
                                                            @endif
                                                        </div>

                                                        {{-- Input Nomor Surat --}}
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">
                                                                <i class="fas fa-file-signature me-2"></i>Nomor Surat <span class="text-danger">*</span>
                                                            </label>
                                                            @php
                                                                // Auto generate nomor surat
                                                                $tahun = date('Y');
                                                                $bulan = date('m');
                                                                $lastSurat = \App\Models\SuratPeminjamanMobil::whereNotNull('nomor_surat')
                                                                    ->whereYear('created_at', $tahun)
                                                                    ->whereMonth('created_at', $bulan)
                                                                    ->orderBy('id', 'desc')
                                                                    ->first();
                                                                
                                                                $urutan = 1;
                                                                if ($lastSurat && $lastSurat->nomor_surat) {
                                                                    $parts = explode('/', $lastSurat->nomor_surat);
                                                                    if (count($parts) > 0 && is_numeric($parts[0])) {
                                                                        $urutan = intval($parts[0]) + 1;
                                                                    }
                                                                }
                                                                
                                                                $fakultas = $item->user->mahasiswa->prodi->jurusan->fakultas ?? null;
                                                                $kodeFakultas = $fakultas ? strtoupper(substr($fakultas->Nama_Fakultas, 0, 2)) : 'FK';
                                                                $nomorSuratSuggestion = sprintf('%03d/SPM/%s/%s/%s', $urutan, $kodeFakultas, $bulan, $tahun);
                                                            @endphp
                                                            <input type="text" 
                                                                   class="form-control" 
                                                                   name="nomor_surat" 
                                                                   id="nomorSurat{{ $item->id }}"
                                                                   value="{{ $nomorSuratSuggestion }}"
                                                                   required
                                                                   placeholder="001/SPM/TE/01/2026">
                                                            <small class="text-muted">
                                                                <i class="fas fa-lightbulb me-1"></i>
                                                                Format: Nomor/SPM/Kode Fakultas/Bulan/Tahun
                                                            </small>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">Rekomendasi/Catatan Admin (Opsional)</label>
                                                            <textarea class="form-control" name="rekomendasi_admin" rows="3" 
                                                                      placeholder="Tambahkan catatan atau rekomendasi jika diperlukan"></textarea>
                                                        </div>

                                                        <input type="hidden" name="action" id="action{{ $item->id }}" value="">
                                                    </form>

                                                    {{-- Form Penolakan (Hidden by default) --}}
                                                    <form action="{{ route('admin_fakultas.peminjaman_mobil.tolak', $item->id) }}" method="POST" id="formTolak{{ $item->id }}" style="display: none;">
                                                        @csrf
                                                        @method('PUT')
                                                        
                                                        <div class="alert alert-warning">
                                                            <h6><i class="fas fa-exclamation-triangle me-2"></i>Form Penolakan</h6>
                                                        </div>
                                                        
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">Alasan Penolakan <span class="text-danger">*</span></label>
                                                            <textarea class="form-control" name="alasan_penolakan" rows="4" 
                                                                      placeholder="Jelaskan alasan penolakan pengajuan ini" required></textarea>
                                                        </div>
                                                    </form>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="button" class="btn btn-danger" onclick="showFormTolak({{ $item->id }})">
                                                        <i class="fas fa-times me-2"></i>Tolak
                                                    </button>
                                                    @if(!$kendaraanTersedia->isEmpty())
                                                        <button type="submit" form="formVerifikasi{{ $item->id }}" class="btn btn-success">
                                                            <i class="fas fa-check me-2"></i>Verifikasi & Teruskan
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                @endif
            </div>
        </div>

@endsection

@push('scripts')
<script>
function showFormTolak(id) {
    document.getElementById('formVerifikasi' + id).style.display = 'none';
    document.getElementById('formTolak' + id).style.display = 'block';
    
    // Update modal footer
    const modalFooter = document.querySelector('#prosesModal' + id + ' .modal-footer');
    modalFooter.innerHTML = `
        <button type="button" class="btn btn-secondary" onclick="hideFormTolak(${id})">Kembali</button>
        <button type="submit" form="formTolak${id}" class="btn btn-danger">
            <i class="fas fa-times me-2"></i>Konfirmasi Tolak
        </button>
    `;
}

function hideFormTolak(id) {
    document.getElementById('formVerifikasi' + id).style.display = 'block';
    document.getElementById('formTolak' + id).style.display = 'none';
    
    // Restore modal footer
    location.reload(); // Simple way to restore
}
</script>
@endpush
