@extends('layouts.mahasiswa')

@section('title', 'Riwayat Pengajuan Surat')

@section('content')

{{-- Header Halaman --}}
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-history text-primary"></i> Riwayat Pengajuan Surat
    </h1>
</div>

{{-- Alert Success/Error --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-envelope-open-text"></i> Daftar Pengajuan Saya
        </h6>
        <span class="badge bg-info">Total: {{ $riwayatSurat->count() }} Pengajuan</span>
    </div>
    <div class="card-body">
        @if($riwayatSurat->isEmpty())
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle fa-2x mb-3"></i>
                <p class="mb-0">Belum ada pengajuan surat. Silakan ajukan surat baru!</p>
                <a href="{{ route('mahasiswa.pengajuan.create') }}" class="btn btn-primary btn-sm mt-3">
                    <i class="fas fa-plus"></i> Ajukan Surat Baru
                </a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="tableSurat" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">#</th>
                            <th>Tanggal Pengajuan</th>
                            <th>Jenis Surat</th>
                            <th>Judul/Keperluan</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($riwayatSurat as $index => $surat)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>
                                    <i class="fas fa-calendar text-muted"></i>
                                    {{ $surat->Tanggal_Diberikan_Tugas_Surat ? \Carbon\Carbon::parse($surat->Tanggal_Diberikan_Tugas_Surat)->format('d M Y') : '-' }}
                                </td>
                                <td>
                                    <strong>{{ $surat->jenisSurat->Nama_Surat ?? 'N/A' }}</strong>
                                </td>
                                <td>{{ \Illuminate\Support\Str::limit($surat->Judul_Tugas_Surat, 50) }}</td>
                                <td class="text-center">
                                    @php
                                        $status = strtolower(trim($surat->Status));
                                        $badgeClass = 'secondary';
                                        $icon = 'circle';
                                        
                                        if ($status === 'baru') {
                                            $badgeClass = 'info';
                                            $icon = 'clock';
                                        } elseif ($status === 'diterima admin' || $status === 'proses') {
                                            $badgeClass = 'primary';
                                            $icon = 'spinner';
                                        } elseif ($status === 'menunggu-ttd') {
                                            $badgeClass = 'warning';
                                            $icon = 'hourglass-half';
                                        } elseif ($status === 'selesai' || $status === 'telah ditandatangani dekan') {
                                            $badgeClass = 'success';
                                            $icon = 'check-circle';
                                        } elseif ($status === 'ditolak') {
                                            $badgeClass = 'danger';
                                            $icon = 'times-circle';
                                        }
                                    @endphp
                                    <span class="badge bg-{{ $badgeClass }}">
                                        <i class="fas fa-{{ $icon }}"></i> {{ ucfirst($surat->Status) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @php
                                        $statusLower = strtolower(trim($surat->Status));
                                        $isSelesai = ($statusLower === 'selesai' || $statusLower === 'telah ditandatangani dekan');
                                        $isDitolak = ($statusLower === 'ditolak');
                                    @endphp
                                    
                                    @if($isSelesai)
                                        {{-- Tombol Download PDF dengan QR Code --}}
                                        <a href="{{ route('mahasiswa.surat.download', $surat->Id_Tugas_Surat) }}" 
                                           class="btn btn-success btn-sm" 
                                           title="Download Surat dengan QR Code"
                                           target="_blank">
                                            <i class="fas fa-download"></i> Download
                                        </a>
                                        
                                        {{-- Tombol Verifikasi QR (jika ada verification) --}}
                                        @if($surat->verification)
                                            <a href="{{ route('surat.verify', $surat->verification->token) }}" 
                                               class="btn btn-info btn-sm mt-1" 
                                               title="Verifikasi Digital Signature"
                                               target="_blank">
                                                <i class="fas fa-qrcode"></i> Verifikasi
                                            </a>
                                        @endif
                                    @elseif($isDitolak)
                                        @php
                                            $dataSpesifik = $surat->data_spesifik ?? [];
                                        @endphp
                                        <button type="button" 
                                                class="btn btn-danger btn-sm btn-lihat-alasan"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modalAlasan"
                                                data-alasan="{{ $dataSpesifik['alasan_penolakan'] ?? 'Tidak ada alasan yang diberikan.' }}"
                                                data-penolak="{{ $dataSpesifik['ditolak_oleh'] ?? 'Admin' }}"
                                                data-tanggal="{{ $dataSpesifik['tanggal_penolakan'] ?? '-' }}">
                                            <i class="fas fa-eye"></i> Lihat Alasan
                                        </button>
                                    @else
                                        <button class="btn btn-secondary btn-sm" disabled>
                                            <i class="fas fa-hourglass-half"></i> Menunggu
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

{{-- Info Card --}}
<div class="row">
    <div class="col-md-12">
        <div class="alert alert-light border">
            <h6 class="alert-heading"><i class="fas fa-info-circle text-info"></i> Informasi Status Surat</h6>
            <hr>
            <ul class="mb-0">
                <li><span class="badge bg-info">Baru</span> - Surat baru diajukan, menunggu proses admin</li>
                <li><span class="badge bg-primary">Diterima Admin / Proses</span> - Sedang diproses oleh admin fakultas</li>
                <li><span class="badge bg-warning">Menunggu-TTD</span> - Menunggu persetujuan dan tanda tangan Dekan</li>
                <li><span class="badge bg-success">Selesai / Telah Ditandatangani Dekan</span> - Surat sudah ditandatangani dengan QR Code, siap didownload!</li>
                <li><span class="badge bg-danger">Ditolak</span> - Pengajuan surat ditolak</li>
            </ul>
        </div>
    </div>
</div>

{{-- Modal Lihat Alasan Penolakan --}}
<div class="modal fade" id="modalAlasan" tabindex="-1" aria-labelledby="modalAlasanLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalAlasanLabel"><i class="fas fa-exclamation-triangle"></i> Alasan Penolakan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="fw-bold">Ditolak Oleh:</label>
                    <p id="modalPenolak" class="text-dark"></p>
                </div>
                <div class="mb-3">
                    <label class="fw-bold">Tanggal Penolakan:</label>
                    <p id="modalTanggal" class="text-muted"></p>
                </div>
                <div class="mb-3">
                    <label class="fw-bold">Alasan:</label>
                    <div class="alert alert-light border border-danger text-danger p-3" id="modalAlasanText"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
{{-- Pastikan jQuery dimuat untuk DataTables dan Script Modal --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {
        // Initialize DataTable if exists
        if ($.fn.DataTable) {
            $('#tableSurat').DataTable({
                "order": [[1, "desc"]], // Sort by tanggal descending
                "pageLength": 10,
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json"
                }
            });
        }

        // Handle Modal Alasan Penolakan
        $(document).on('click', '.btn-lihat-alasan', function() {
            var alasan = $(this).attr('data-alasan');
            var penolak = $(this).attr('data-penolak');
            var tanggal = $(this).attr('data-tanggal');
            
            $('#modalAlasanText').text(alasan);
            $('#modalPenolak').text(penolak);
            $('#modalTanggal').text(tanggal);
        });
    });
</script>
@endpush