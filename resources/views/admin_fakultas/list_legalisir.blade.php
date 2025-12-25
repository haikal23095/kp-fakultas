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
            <p class="text-muted small">Kelola pembayaran dan progres berkas fisik mahasiswa</p>
        </div>
        <a href="{{ route('admin_fakultas.surat_legalisir.create') }}" class="btn btn-primary btn-action shadow-sm">
            <i class="fas fa-plus me-1"></i> Input Pengajuan Baru
        </a>
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
                                @endphp
                                <span class="badge badge-status bg-{{ $bg }}">
                                    {{ strtoupper(str_replace('_', ' ', $status)) }}
                                </span>
                            </td>
                            <td>
                                @if($surat->Status == 'menunggu_pembayaran')
                                    {{-- Tombol Pemicu Modal --}}
                                    <button type="button" class="btn btn-sm btn-success btn-action" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalBayar{{ $surat->id_no }}">
                                        <i class="fas fa-cash-register me-1"></i>Bayar
                                    </button>
                                @elseif($surat->Status != 'selesai')
                                    <form action="{{ route('admin_fakultas.surat_legalisir.progress', $surat->id_no) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-primary btn-action" onclick="return confirm('Update progres ke tahap selanjutnya?')">
                                            <i class="fas fa-arrow-right me-1"></i>Lanjut
                                        </button>
                                    </form>
                                @else
                                    <i class="fas fa-check-double text-success"></i> <small class="text-muted">Selesai</small>
                                @endif
                            </td>
                        </tr>

                        {{-- MODAL KONFIRMASI PEMBAYARAN (Harus di dalam loop @foreach) --}}
                        @if($surat->Status == 'menunggu_pembayaran')
                        <div class="modal fade" id="modalBayar{{ $surat->id_no }}" tabindex="-1" aria-hidden="true">
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
                        {{-- END MODAL --}}

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
    $(document).ready(function() {
        // Inisialisasi DataTable jika tersedia
        if ($.fn.DataTable) {
            $('#tableLegalisir').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
                },
                "pageLength": 10
            });
        }
    });
</script>
@endpush