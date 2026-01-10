@extends('layouts.admin_fakultas')

@section('title', 'Riwayat SK Penguji Skripsi')

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-0">Riwayat SK Penguji Skripsi</h1>
        <p class="mb-0 text-muted">Riwayat SK yang telah diproses oleh Admin</p>
    </div>
    <div>
        <a href="{{ route('admin_fakultas.sk.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>

<!-- SK List -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h6 class="m-0 fw-bold text-danger">
            <i class="fas fa-history me-2"></i>Riwayat Pengajuan SK Penguji Skripsi
        </h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Nomor SK</th>
                        <th>Program Studi</th>
                        <th>Semester</th>
                        <th>Tahun Akademik</th>
                        <th>Jumlah Mhs</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($skList as $index => $acc)
                    @php
                        $pengujiData = $acc->Data_Penguji_Skripsi;
                        if (is_string($pengujiData)) {
                            $pengujiData = json_decode($pengujiData, true);
                        }
                        $jumlahMahasiswa = is_array($pengujiData) ? count($pengujiData) : 0;
                        $sk = $acc->requestSK;
                    @endphp
                    <tr>
                        <td>{{ $skList->firstItem() + $index }}</td>
                        <td>
                            <strong class="text-danger">{{ $acc->Nomor_Surat ?? '-' }}</strong>
                        </td>
                        <td>{{ $sk->prodi->Nama_Prodi ?? '-' }}</td>
                        <td>
                            <span class="badge bg-{{ $acc->Semester == 'Ganjil' ? 'primary' : 'info' }}">
                                {{ $acc->Semester }}
                            </span>
                        </td>
                        <td>{{ $acc->Tahun_Akademik }}</td>
                        <td class="text-center">
                            <span class="badge bg-secondary">{{ $jumlahMahasiswa }}</span>
                        </td>
                        <td>
                            @php
                                $badgeClass = 'secondary';
                                switch($acc->Status) {
                                    case 'Menunggu-Persetujuan-Wadek-1': 
                                    case 'Menunggu-Persetujuan-Dekan': 
                                        $badgeClass = 'primary'; 
                                        break;
                                    case 'Selesai': 
                                        $badgeClass = 'success'; 
                                        break;
                                    case 'Ditolak-Wadek1':
                                    case 'Ditolak-Dekan':
                                        $badgeClass = 'danger'; 
                                        break;
                                }
                            @endphp
                            <span class="badge bg-{{ $badgeClass }}">{{ str_replace('-', ' ', $acc->Status) }}</span>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('admin_fakultas.sk.penguji-skripsi.detail-history', $acc->No) }}" 
                               class="btn btn-sm btn-info" title="Lihat Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if($acc->Status == 'Selesai' && $acc->QR_Code)
                            <a href="{{ route('admin_fakultas.sk.penguji-skripsi.download', $acc->No) }}" 
                               class="btn btn-sm btn-success" title="Download PDF">
                                <i class="fas fa-download"></i>
                            </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            Belum ada riwayat SK Penguji Skripsi
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($skList->hasPages())
    <div class="card-footer bg-white">
        {{ $skList->links() }}
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
    function applyFilters() {
        const semester = $('#filterSemester').val();
        let url = new URL(window.location.href);
        if (semester) url.searchParams.set('semester', semester);
        else url.searchParams.delete('semester');
        window.location.href = url.toString();
    }
</script>
@endpush
