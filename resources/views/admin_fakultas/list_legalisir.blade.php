@extends('layouts.admin_fakultas')

@section('title', 'Daftar Pengajuan Legalisir')

@push('styles')
<style>
    /* Desain Card & Tabel Modern */
    .card-legalisir {
        border: none;
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }
    
    .header-gradient {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        padding: 1.2rem;
        color: white;
    }

    .table-custom {
        font-size: 0.9rem;
    }
    
    .table-custom thead th {
        background-color: #f8f9fc;
        color: #4e73df;
        font-weight: 700;
        text-transform: uppercase;
        border: none;
        padding: 12px;
    }

    .badge-status {
        padding: 0.5rem 0.8rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.75rem;
    }

    .btn-action {
        border-radius: 8px;
        padding: 0.4rem 0.8rem;
        font-weight: 600;
        transition: all 0.2s;
    }

    .btn-action:hover {
        transform: translateY(-2px);
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800 fw-bold">Antrean Legalisir</h1>
            <p class="text-muted small">Kelola verifikasi file, pembayaran dan progres berkas fisik mahasiswa</p>
        </div>
    </div>

    {{-- Pesan Sukses --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-radius: 10px;">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card card-legalisir">
        <div class="header-gradient">
            <h6 class="m-0 fw-bold"><i class="fas fa-list me-2"></i>Daftar Dokumen Masuk</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-custom mb-0" id="tableLegalisir">
                    <thead class="text-center">
                        <tr>
                            <th>No</th>
                            <th class="text-start">Mahasiswa</th>
                            <th>Dokumen</th>
                            <th>Jumlah</th>
                            <th>File Scan</th>
                            <th>Biaya</th>
                            <th>Tgl Bayar</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($daftarSurat as $index => $surat)
                        <tr class="text-center align-middle">
                            <td>{{ $index + 1 }}</td>
                            <td class="text-start">
                                <div class="fw-bold text-primary">{{ $surat->user->Name_User ?? 'N/A' }}</div>
                                <small class="text-muted">{{ $surat->user->mahasiswa->NIM ?? '-' }}</small>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $surat->Jenis_Dokumen }}</span>
                            </td>
                            <td>{{ $surat->Jumlah_Salinan }} Copy</td>
                            <td>
                                @if($surat->isSigned() && $surat->File_Signed_Path)
                                    {{-- Tampilkan PDF Signed (sudah TTD) --}}
                                    <button type="button" class="btn btn-sm btn-success" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalPreview_{{ $surat->id_no }}">
                                        <i class="fas fa-check-circle me-1"></i>PDF Signed (TTD)
                                    </button>
                                    <br><small class="text-success fw-bold" style="font-size: 0.7rem;">✓ Ditandatangani</small>
                                @elseif($surat->File_Scan_Path)
                                    {{-- Tampilkan PDF Scan (belum TTD) --}}
                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalPreview_{{ $surat->id_no }}">
                                        <i class="fas fa-file-pdf me-1"></i>Lihat PDF
                                    </button>
                                    <br><small class="text-muted" style="font-size: 0.7rem;">{{ basename($surat->File_Scan_Path) }}</small>
                                @else
                                    <small class="text-muted fst-italic">Tidak ada file</small>
                                @endif
                            </td>
                            <td class="fw-bold text-success">
                                {{ $surat->Biaya ? 'Rp '.number_format($surat->Biaya, 0, ',', '.') : '-' }}
                            </td>
                            <td>
                                <small>
                                    {{ $surat->Tanggal_Bayar ? \Carbon\Carbon::parse($surat->Tanggal_Bayar)->format('d/m/Y') : 'Belum Bayar' }}
                                </small>
                            </td>
                            <td>
                                @php
                                    $status = $surat->Status;
                                    $bg = 'secondary';
                                    if($status == 'menunggu_pembayaran') $bg = 'warning text-dark';
                                    elseif($status == 'pembayaran_lunas') $bg = 'info';
                                    elseif($status == 'siap_diambil' || $status == 'selesai') $bg = 'success';
                                    elseif($status == 'ditolak') $bg = 'danger';
                                @endphp
                                <span class="badge badge-status bg-{{ $bg }}">
                                    {{ strtoupper(str_replace('_', ' ', $status)) }}
                                </span>
                            </td>
                            <td>
                                @if($surat->Status == 'menunggu_pembayaran' && !$surat->Is_Verified)
                                    {{-- Tombol Lihat & Verifikasi File --}}
                                    @if($surat->File_Scan_Path)
                                        <button type="button" class="btn btn-sm btn-info btn-action me-1" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modalVerifikasi_{{ $surat->id_no }}">
                                            <i class="fas fa-eye me-1"></i>Lihat & Verifikasi
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger btn-action" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modalTolak_{{ $surat->id_no }}">
                                            <i class="fas fa-times me-1"></i>Tolak
                                        </button>
                                    @else
                                        <span class="text-muted small">File tidak tersedia</span>
                                    @endif
                                @elseif($surat->Status == 'menunggu_pembayaran' && $surat->Is_Verified)
                                    {{-- Tombol Pemicu Modal Pembayaran --}}
                                    <button type="button" class="btn btn-sm btn-success btn-action" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalBayar_{{ $surat->id_no }}">
                                        <i class="fas fa-cash-register me-1"></i>Bayar
                                    </button>
                                @elseif($surat->Status == 'pembayaran_lunas')
                                    {{-- Tombol Kirim ke Pimpinan --}}
                                    <form action="{{ route('admin_fakultas.surat_legalisir.kirim_pimpinan', $surat->id_no) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-warning btn-action" onclick="return confirm('Kirim ke Dekan dan Wadek1 untuk TTD?')">
                                            <i class="fas fa-paper-plane me-1"></i>Kirim TTD
                                        </button>
                                    </form>
                                @elseif($surat->Status == 'menunggu_ttd_pimpinan')
                                    <span class="badge bg-warning text-dark"><i class="fas fa-hourglass-half me-1"></i>Menunggu TTD Pimpinan</span>
                                @elseif($surat->Status == 'siap_diambil')
                                    <form action="{{ route('admin_fakultas.surat_legalisir.progress', $surat->id_no) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success btn-action" onclick="return confirm('Apakah mahasiswa sudah mengambil berkas?')">
                                            <i class="fas fa-hand-holding me-1"></i>Sudah Diambil
                                        </button>
                                    </form>
                                @elseif($surat->Status == 'selesai')
                                    <i class="fas fa-check-double text-success"></i> <small class="text-muted">Selesai</small>
                                @elseif($surat->Status == 'ditolak')
                                    <span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i>Ditolak</span>
                                @endif
                            </td>
                        </tr>

                        {{-- MODAL KONFIRMASI PEMBAYARAN (Harus di dalam loop @foreach) --}}
                        @if($surat->Status == 'menunggu_pembayaran')
                        <div class="modal fade" id="modalBayar_{{ $surat->id_no }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content" style="border-radius: 15px;">
                                    <form action="{{ route('admin_fakultas.surat_legalisir.bayar', $surat->id_no) }}" method="POST">
                                        @csrf
                                        <div class="modal-header bg-success text-white" style="border-radius: 15px 15px 0 0;">
                                            <h5 class="modal-title fw-bold">Konfirmasi Pembayaran</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body p-4 text-center">
                                            <p class="mb-1 text-muted">Aksi ini akan menandai tagihan mahasiswa berikut sebagai <strong>LUNAS</strong>:</p>
                                            <h5 class="fw-bold mb-3">{{ $surat->user->Name_User }}</h5>
                                            
                                            <div class="p-3 bg-light rounded-3 border mb-3">
                                                <small class="text-muted d-block">Total Pembayaran:</small>
                                                <h3 class="fw-bold text-success mb-0">Rp {{ number_format($surat->Biaya, 0, ',', '.') }}</h3>
                                            </div>
                                            <p class="small text-danger italic">*Tanggal bayar akan tercatat secara otomatis hari ini.</p>
                                        </div>
                                        <div class="modal-footer border-0">
                                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm">Konfirmasi Lunas</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endif
                        {{-- END MODAL PEMBAYARAN --}}

                        {{-- MODAL PENOLAKAN --}}
                        @if($surat->Status == 'menunggu_pembayaran' && !$surat->Is_Verified)
                        <div class="modal fade" id="modalTolak_{{ $surat->id_no }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content" style="border-radius: 15px;">
                                    <form action="{{ route('admin_fakultas.surat_legalisir.tolak', $surat->id_no) }}" method="POST">
                                        @csrf
                                        <div class="modal-header bg-danger text-white" style="border-radius: 15px 15px 0 0;">
                                            <h5 class="modal-title fw-bold"><i class="fas fa-times-circle me-2"></i>Tolak Pengajuan Legalisir</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body p-4 text-center">
                                            <div class="alert alert-warning">
                                                <i class="fas fa-exclamation-triangle me-2"></i>
                                                Pengajuan dari <strong>{{ $surat->user->Name_User }}</strong> akan ditolak.
                                            </div>
                                            <p class="text-muted mb-0">Mahasiswa akan mendapat notifikasi bahwa pengajuannya ditolak.</p>
                                        </div>
                                        <div class="modal-footer border-0">
                                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold shadow-sm">
                                                <i class="fas fa-times me-1"></i>Ya, Tolak
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endif
                        {{-- END MODAL PENOLAKAN --}}

                        {{-- MODAL VERIFIKASI FILE (Untuk yang belum terverifikasi) --}}
                        @if($surat->Status == 'menunggu_pembayaran' && !$surat->Is_Verified && $surat->File_Scan_Path)
                        <div class="modal fade" id="modalVerifikasi_{{ $surat->id_no }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-xl modal-dialog-centered">
                                <div class="modal-content" style="border-radius: 15px;">
                                    <div class="modal-header bg-info text-white" style="border-radius: 15px 15px 0 0;">
                                        <h5 class="modal-title fw-bold">Verifikasi File Scan - {{ $surat->user->Name_User ?? 'N/A' }}</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body p-0">
                                        <div class="alert alert-warning m-3 mb-0">
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            <strong>Perhatian:</strong> Pastikan file scan sesuai dengan dokumen asli ({{ $surat->Jenis_Dokumen }}) yang dibawa mahasiswa.
                                        </div>
                                        <iframe src="{{ asset('storage/' . $surat->File_Scan_Path) }}" 
                                                style="width:100%; height:500px; border:none;"></iframe>
                                    </div>
                                    <div class="modal-footer border-0">
                                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                        <form action="{{ route('admin_fakultas.surat_legalisir.verifikasi', $surat->id_no) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm">
                                                <i class="fas fa-check-circle me-1"></i>Verifikasi File Ini
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                        {{-- END MODAL VERIFIKASI --}}

                        {{-- MODAL PREVIEW PDF (Untuk yang sudah terverifikasi, hanya lihat) --}}
                        @if($surat->File_Scan_Path || $surat->File_Signed_Path)
                        <div class="modal fade" id="modalPreview_{{ $surat->id_no }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-xl modal-dialog-centered">
                                <div class="modal-content" style="border-radius: 15px;">
                                    <div class="modal-header {{ $surat->isSigned() ? 'bg-success' : 'bg-primary' }} text-white" style="border-radius: 15px 15px 0 0;">
                                        <h5 class="modal-title fw-bold">
                                            @if($surat->isSigned())
                                                <i class="fas fa-check-circle me-2"></i>Preview File Signed (TTD) - {{ $surat->user->Name_User ?? 'N/A' }}
                                            @else
                                                Preview File Scan - {{ $surat->user->Name_User ?? 'N/A' }}
                                            @endif
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body p-0">
                                        @if($surat->isSigned() && $surat->File_Signed_Path)
                                            <iframe src="{{ asset('storage/' . $surat->File_Signed_Path) }}" 
                                                    style="width:100%; height:600px; border:none;"></iframe>
                                        @else
                                            <iframe src="{{ asset('storage/' . $surat->File_Scan_Path) }}" 
                                                    style="width:100%; height:600px; border:none;"></iframe>
                                        @endif
                                    </div>
                                    <div class="modal-footer border-0">
                                        <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                        {{-- END MODAL PREVIEW --}}

                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Modal Bootstrap 5 sudah otomatis initialize via data-bs-toggle
    // TIDAK perlu DataTables karena data tidak banyak dan bikin conflict dengan Bootstrap Modal
    
    document.addEventListener('DOMContentLoaded', function() {
        console.log('✅ Legalisir list loaded - Modals ready');
    });
</script>
@endpush