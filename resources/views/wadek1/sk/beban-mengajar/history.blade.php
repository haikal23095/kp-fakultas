@extends('layouts.wadek1')

@section('title', 'Riwayat SK Beban Mengajar - Wadek 1')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-0">Riwayat SK Beban Mengajar</h1>
        <p class="mb-0 text-muted">Daftar SK Beban Mengajar yang telah diproses</p>
    </div>
    <div>
        <a href="{{ route('wadek1.sk.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>

<!-- Filter Section -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('wadek1.sk.beban-mengajar.history') }}">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="status" class="form-label">Filter Status</label>
                    <select name="status" id="status" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="Menunggu-Persetujuan-Dekan" {{ request('status') == 'Menunggu-Persetujuan-Dekan' ? 'selected' : '' }}>
                            Menunggu Persetujuan Dekan
                        </option>
                        <option value="Selesai" {{ request('status') == 'Selesai' ? 'selected' : '' }}>
                            Selesai
                        </option>
                        <option value="Ditolak-Wadek1" {{ request('status') == 'Ditolak-Wadek1' ? 'selected' : '' }}>
                            Ditolak Wadek1
                        </option>
                        <option value="Ditolak-Dekan" {{ request('status') == 'Ditolak-Dekan' ? 'selected' : '' }}>
                            Ditolak Dekan
                        </option>
                    </select>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Table Section -->
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 50px;">No</th>
                        <th>No. SK</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Periode</th>
                        <th class="text-center">Status</th>
                        <th class="text-center" style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($skList as $index => $sk)
                    <tr>
                        <td class="text-center">{{ $skList->firstItem() + $index }}</td>
                        <td>
                            <strong>{{ $sk->Nomor_Surat ?? 'Belum ada nomor' }}</strong>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($sk->{'Tanggal-Pengajuan'})->format('d M Y') }}</td>
                        <td>{{ $sk->Tahun_Akademik ?? '-' }} ({{ $sk->Semester ?? '-' }})</td>
                        <td class="text-center">
                            @if($sk->Status == 'Menunggu-Persetujuan-Dekan')
                                <span class="badge bg-warning text-dark">
                                    <i class="fas fa-clock me-1"></i>Menunggu Dekan
                                </span>
                            @elseif($sk->Status == 'Selesai')
                                <span class="badge bg-success">
                                    <i class="fas fa-check-circle me-1"></i>Selesai
                                </span>
                            @elseif($sk->Status == 'Ditolak-Wadek1')
                                <span class="badge bg-danger">
                                    <i class="fas fa-times-circle me-1"></i>Ditolak Wadek1
                                </span>
                            @elseif($sk->Status == 'Ditolak-Dekan')
                                <span class="badge bg-danger">
                                    <i class="fas fa-times-circle me-1"></i>Ditolak Dekan
                                </span>
                            @else
                                <span class="badge bg-secondary">{{ $sk->Status }}</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('wadek1.sk.beban-mengajar.detail', $sk->{'Id-SK'} ?? $sk->No) }}" 
                               class="btn btn-sm btn-info text-white">
                                <i class="fas fa-eye me-1"></i>Lihat Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                            Belum ada riwayat SK Beban Mengajar
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($skList->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $skList->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Info Section -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm bg-light">
            <div class="card-body">
                <div class="d-flex align-items-start">
                    <div class="me-3">
                        <i class="fas fa-info-circle fa-2x text-primary"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-2">Keterangan Status</h6>
                        <ul class="mb-0 small text-muted">
                            <li><strong>Menunggu Persetujuan Dekan:</strong> SK telah disetujui oleh Wadek 1 dan menunggu persetujuan Dekan</li>
                            <li><strong>Selesai:</strong> SK telah disetujui oleh Dekan dan proses selesai</li>
                            <li><strong>Ditolak Wadek1:</strong> SK ditolak oleh Wadek 1</li>
                            <li><strong>Ditolak Dekan:</strong> SK ditolak oleh Dekan</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
