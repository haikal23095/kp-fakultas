@extends('layouts.kaprodi')

@section('title', 'Riwayat SK Penguji Skripsi')

@section('content')

<div class="mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.kaprodi') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kaprodi.sk.index') }}">Ajukan SK</a></li>
            <li class="breadcrumb-item active">Riwayat SK Penguji Skripsi</li>
        </ol>
    </nav>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-danger text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-history fa-lg me-3"></i>
                        <div>
                            <h5 class="mb-0 fw-bold">Riwayat SK Penguji Skripsi</h5>
                            <small>Daftar pengajuan SK penguji skripsi yang telah diajukan</small>
                        </div>
                    </div>
                    <a href="{{ route('kaprodi.sk.penguji-skripsi.create') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-plus-circle me-2"></i>Ajukan SK Baru
                    </a>
                </div>
            </div>
            <div class="card-body p-4">
                <!-- Filter Section -->
                <form method="GET" action="{{ route('kaprodi.sk.penguji-skripsi.history') }}">
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Semester</label>
                            <select class="form-select" name="semester">
                                <option value="">Semua Semester</option>
                                <option value="Ganjil" {{ request('semester') == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                                <option value="Genap" {{ request('semester') == 'Genap' ? 'selected' : '' }}>Genap</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Tahun Akademik</label>
                            <input type="text" class="form-control" name="tahun_akademik" placeholder="Contoh: 23/24" value="{{ request('tahun_akademik') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Status</label>
                            <select class="form-select" name="status">
                                <option value="">Semua Status</option>
                                <option value="Dikerjakan admin" {{ request('status') == 'Dikerjakan admin' ? 'selected' : '' }}>Dikerjakan Admin</option>
                                <option value="Menunggu-Persetujuan-Wadek-1" {{ request('status') == 'Menunggu-Persetujuan-Wadek-1' ? 'selected' : '' }}>Menunggu Wadek 1</option>
                                <option value="Menunggu-Persetujuan-Dekan" {{ request('status') == 'Menunggu-Persetujuan-Dekan' ? 'selected' : '' }}>Menunggu Dekan</option>
                                <option value="Selesai" {{ request('status') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                                <option value="Ditolak-Admin" {{ request('status') == 'Ditolak-Admin' ? 'selected' : '' }}>Ditolak Admin</option>
                                <option value="Ditolak-Wadek1" {{ request('status') == 'Ditolak-Wadek1' ? 'selected' : '' }}>Ditolak Wadek 1</option>
                                <option value="Ditolak-Dekan" {{ request('status') == 'Ditolak-Dekan' ? 'selected' : '' }}>Ditolak Dekan</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">&nbsp;</label>
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="fas fa-filter me-2"></i>Terapkan Filter
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Table Section -->
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="5%" class="text-center">No</th>
                                <th width="15%">Program Studi</th>
                                <th width="12%">Nomor SK</th>
                                <th width="10%">Semester</th>
                                <th width="12%">Tahun Akademik</th>
                                <th width="10%" class="text-center">Jumlah Mahasiswa</th>
                                <th width="12%">Tanggal Ajuan</th>
                                <th width="12%" class="text-center">Status</th>
                                <th width="12%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($skList as $index => $sk)
                            <tr>
                                @php
                                    $pengujiData = $sk->Data_Penguji_Skripsi;
                                    // Sudah di-cast ke array di model, tapi jaga-jaga jika null
                                    $jumlahMahasiswa = is_array($pengujiData) ? count($pengujiData) : 0;
                                @endphp
                                <td class="text-center">{{ $skList->firstItem() + $index }}</td>
                                <td>{{ $sk->prodi->Nama_Prodi ?? '-' }}</td>
                                <td>
                                    @if($sk->accSKPengujiSkripsi && $sk->accSKPengujiSkripsi->Nomor_Surat)
                                        <strong class="text-danger">{{ $sk->accSKPengujiSkripsi->Nomor_Surat }}</strong>
                                    @else
                                        <span class="text-muted">Belum ada nomor</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $sk->Semester == 'Ganjil' ? 'primary' : 'info' }}">
                                        {{ $sk->Semester }}
                                    </span>
                                </td>
                                <td>{{ $sk->Tahun_Akademik }}</td>
                                <td class="text-center">
                                    <span class="badge bg-secondary">
                                        <i class="fas fa-user-graduate me-1"></i>{{ $jumlahMahasiswa }}
                                    </span>
                                </td>
                                <td>
                                    <small>{{ isset($sk->{'Tanggal-Pengajuan'}) ? \Carbon\Carbon::parse($sk->{'Tanggal-Pengajuan'})->format('d M Y H:i') : '-' }}</small>
                                </td>
                                <td class="text-center">
                                    @php
                                        $badgeClass = 'secondary';
                                        $statusText = $sk->Status;
                                        switch($sk->Status) {
                                            case 'Dikerjakan admin':
                                                $badgeClass = 'warning text-dark';
                                                break;
                                            case 'Menunggu-Persetujuan-Wadek-1':
                                                $badgeClass = 'info';
                                                $statusText = 'Menunggu Wadek 1';
                                                break;
                                            case 'Menunggu-Persetujuan-Dekan':
                                                $badgeClass = 'primary';
                                                $statusText = 'Menunggu Dekan';
                                                break;
                                            case 'Selesai':
                                                $badgeClass = 'success';
                                                break;
                                            case 'Ditolak-Admin':
                                            case 'Ditolak-Wadek1':
                                            case 'Ditolak-Dekan':
                                                $badgeClass = 'danger';
                                                break;
                                        }
                                    @endphp
                                    <span class="badge bg-{{ $badgeClass }}">{{ $statusText }}</span>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-danger btn-detail" 
                                        title="Detail"
                                        data-id="{{ $sk->Id_Req }}"
                                        data-nomor="{{ $sk->accSKPengujiSkripsi->Nomor_Surat ?? 'Belum ada nomor' }}"
                                        data-prodi="{{ $sk->prodi->Nama_Prodi ?? '-' }}"
                                        data-semester="{{ $sk->Semester }}"
                                        data-tahun="{{ $sk->Tahun_Akademik }}"
                                        data-status="{{ $sk->Status }}"
                                        data-tanggal="{{ $sk->{'Tanggal-Pengajuan'} ? \Carbon\Carbon::parse($sk->{'Tanggal-Pengajuan'})->format('d M Y H:i') : '-' }}"
                                        data-tenggat="{{ $sk->{'Tanggal-Tenggat'} ? \Carbon\Carbon::parse($sk->{'Tanggal-Tenggat'})->format('d M Y') : '-' }}"
                                        data-alasan="{{ $sk->{'Alasan-Tolak'} ?? '-' }}"
                                        data-penguji='@json($sk->Data_Penguji_Skripsi ?? [])'
                                        data-bs-toggle="modal" 
                                        data-bs-target="#detailModal">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">Belum ada riwayat pengajuan SK Penguji Skripsi.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($skList->hasPages())
                <div class="mt-4">
                    {{ $skList->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="detailModalLabel">
                    <i class="fas fa-file-alt me-2"></i>Detail SK Penguji Skripsi
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr>
                                <td width="40%" class="fw-semibold">Nomor SK</td>
                                <td id="detail-nomor">-</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Program Studi</td>
                                <td id="detail-prodi">-</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Semester</td>
                                <td id="detail-semester">-</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Tahun Akademik</td>
                                <td id="detail-tahun">-</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr>
                                <td width="40%" class="fw-semibold">Status</td>
                                <td id="detail-status">-</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Tanggal Pengajuan</td>
                                <td id="detail-tanggal">-</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Tanggal Tenggat</td>
                                <td id="detail-tenggat">-</td>
                            </tr>
                            <tr id="alasan-tolak-row" style="display: none;">
                                <td class="fw-semibold">Alasan Ditolak</td>
                                <td id="detail-alasan" class="text-danger">-</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="mb-3">
                    <h6 class="fw-bold border-bottom pb-2">
                        <i class="fas fa-user-check me-2 text-danger"></i>Daftar Mahasiswa & Penguji
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%" class="text-center">No</th>
                                    <th width="12%">NIM</th>
                                    <th width="23%">Nama Mahasiswa</th>
                                    <th width="20%">Penguji 1</th>
                                    <th width="20%">Penguji 2</th>
                                    <th width="20%">Penguji 3</th>
                                </tr>
                            </thead>
                            <tbody id="detail-penguji-list">
                                <tr>
                                    <td colspan="6" class="text-center text-muted">Tidak ada data</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Tutup
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const detailModal = document.getElementById('detailModal');
    
    detailModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        
        // Populate basic information
        document.getElementById('detail-nomor').textContent = button.dataset.nomor;
        document.getElementById('detail-prodi').textContent = button.dataset.prodi;
        document.getElementById('detail-semester').innerHTML = '<span class="badge bg-' + 
            (button.dataset.semester === 'Ganjil' ? 'primary' : 'info') + '">' + 
            button.dataset.semester + '</span>';
        document.getElementById('detail-tahun').textContent = button.dataset.tahun;
        document.getElementById('detail-tanggal').textContent = button.dataset.tanggal;
        document.getElementById('detail-tenggat').textContent = button.dataset.tenggat;
        
        // Status badge
        const status = button.dataset.status;
        let badgeClass = 'secondary';
        let statusText = status;
        
        switch(status) {
            case 'Dikerjakan admin':
                badgeClass = 'warning text-dark';
                break;
            case 'Menunggu-Persetujuan-Wadek-1':
                badgeClass = 'info';
                statusText = 'Menunggu Wadek 1';
                break;
            case 'Menunggu-Persetujuan-Dekan':
                badgeClass = 'primary';
                statusText = 'Menunggu Dekan';
                break;
            case 'Selesai':
                badgeClass = 'success';
                break;
            case 'Ditolak-Admin':
            case 'Ditolak-Wadek1':
            case 'Ditolak-Dekan':
                badgeClass = 'danger';
                break;
        }
        
        document.getElementById('detail-status').innerHTML = '<span class="badge bg-' + badgeClass + '">' + statusText + '</span>';
        
        // Show/hide alasan tolak
        if (status.includes('Ditolak')) {
            document.getElementById('alasan-tolak-row').style.display = '';
            document.getElementById('detail-alasan').textContent = button.dataset.alasan;
        } else {
            document.getElementById('alasan-tolak-row').style.display = 'none';
        }
        
        // Populate penguji list
        const pengujiData = JSON.parse(button.dataset.penguji);
        const pengujiList = document.getElementById('detail-penguji-list');
        pengujiList.innerHTML = '';
        
        if (pengujiData && pengujiData.length > 0) {
            pengujiData.forEach((item, index) => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td class="text-center">${index + 1}</td>
                    <td>${item.nim || '-'}</td>
                    <td>${item.nama_mahasiswa || '-'}</td>
                    <td><small>${item.nama_penguji_1 || '-'}</small></td>
                    <td><small>${item.nama_penguji_2 || '-'}</small></td>
                    <td><small>${item.nama_penguji_3 || '-'}</small></td>
                `;
                pengujiList.appendChild(row);
            });
        } else {
            pengujiList.innerHTML = '<tr><td colspan="6" class="text-center text-muted">Tidak ada data mahasiswa</td></tr>';
        }
    });
});
</script>
@endpush

@endsection
