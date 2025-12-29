@extends('layouts.admin_fakultas')

@section('title', 'Manajemen Izin Kegiatan Malam')

@section('content')
<div class="container-fluid">
    {{-- Page Heading --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800">Surat Izin Kegiatan Malam</h1>
            <p class="text-muted small mb-0">Kelola pengajuan izin kegiatan mahasiswa di luar jam operasional</p>
        </div>
        <a href="{{ route('admin_fakultas.surat.manage') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert" style="border-radius: 12px; border-left: 4px solid #1cc88a;">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert" style="border-radius: 12px; border-left: 4px solid #e74a3b;">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Pengajuan Masuk</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 5%">No</th>
                            <th>Tanggal Ajuan</th>
                            <th>Mahasiswa</th>
                            <th>Nama Kegiatan</th>
                            <th>Waktu Kegiatan</th>
                            <th>Lokasi</th>
                            <th>Peserta</th>
                            <th>Status</th>
                            <th class="text-center" style="width: 12%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($daftarPengajuan as $index => $tugas)
                            @php
                                $mhs = $tugas->pemberiTugas->mahasiswa ?? null;
                                $detail = $tugas->suratIzinKegiatanMalam;
                                $statusClass = match(strtolower($tugas->Status)) {
                                    'baru', 'pending' => 'bg-warning text-dark',
                                    'selesai', 'success' => 'bg-success text-white',
                                    'ditolak' => 'bg-danger text-white',
                                    default => 'bg-secondary text-white'
                                };
                            @endphp
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ \Carbon\Carbon::parse($tugas->Tanggal_Diberikan_Tugas_Surat)->format('d M Y') }}</td>
                                <td>
                                    <div class="fw-bold">{{ $mhs->Nama_Mahasiswa ?? 'User Unknown' }}</div>
                                    <small class="text-muted">{{ $mhs->NIM ?? '-' }}</small><br>
                                    <small class="text-muted">{{ $mhs->prodi->Nama_Prodi ?? '-' }}</small>
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $detail->nama_kegiatan ?? '-' }}</div>
                                    <small class="text-muted">{{ \Illuminate\Support\Str::limit($detail->alasan ?? '-', 60) }}</small>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <strong>Mulai:</strong><br>
                                        {{ $detail ? \Carbon\Carbon::parse($detail->waktu_mulai)->format('d/m/Y H:i') : '-' }}<br>
                                        <strong>Selesai:</strong><br>
                                        {{ $detail ? \Carbon\Carbon::parse($detail->waktu_selesai)->format('d/m/Y H:i') : '-' }}
                                    </small>
                                </td>
                                <td>{{ $detail->lokasi_kegiatan ?? '-' }}</td>
                                <td class="text-center">{{ $detail->jumlah_peserta ?? 0 }} orang</td>
                                <td class="text-center">
                                    <span class="badge {{ $statusClass }}">{{ ucfirst($tugas->Status) }}</span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin_fakultas.surat.detail', $tugas->Id_Tugas_Surat) }}" 
                                       class="btn btn-info btn-sm" 
                                       title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">
                                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                    Belum ada pengajuan izin kegiatan malam.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Info Card --}}
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-info border-0" role="alert">
                <div class="d-flex align-items-start">
                    <i class="fas fa-info-circle fa-2x me-3 mt-1"></i>
                    <div>
                        <h6 class="alert-heading fw-bold mb-2">Informasi</h6>
                        <p class="mb-0">Surat izin kegiatan malam digunakan untuk memberikan izin kepada mahasiswa/organisasi yang ingin melaksanakan kegiatan di kampus di luar jam operasional. Pastikan untuk melakukan verifikasi terhadap jadwal dan lokasi sebelum menyetujui.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
            },
            "order": [[1, "desc"]]
        });
    });
</script>
@endpush
@endsection
