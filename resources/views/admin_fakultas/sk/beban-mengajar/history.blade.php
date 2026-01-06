@extends('layouts.admin_fakultas')

@section('title', 'History SK Beban Mengajar')

@section('content')

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

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-0">History SK Beban Mengajar</h1>
        <p class="mb-0 text-muted">Riwayat SK Beban Mengajar yang telah diproses</p>
    </div>
    <div>
        <a href="{{ route('admin_fakultas.sk.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>

<!-- Filter Section -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <select class="form-select" id="filterStatus">
                    <option value="">Semua Status</option>
                    <option value="Menunggu-Persetujuan-Wadek-1" {{ request('status') == 'Menunggu-Persetujuan-Wadek-1' ? 'selected' : '' }}>Menunggu Wadek 1</option>
                    <option value="Menunggu-Persetujuan-Dekan" {{ request('status') == 'Menunggu-Persetujuan-Dekan' ? 'selected' : '' }}>Menunggu Dekan</option>
                    <option value="Selesai" {{ request('status') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="Ditolak-Wadek1" {{ request('status') == 'Ditolak-Wadek1' ? 'selected' : '' }}>Ditolak Wadek 1</option>
                    <option value="Ditolak-Dekan" {{ request('status') == 'Ditolak-Dekan' ? 'selected' : '' }}>Ditolak Dekan</option>
                    <option value="Ditolak-Admin" {{ request('status') == 'Ditolak-Admin' ? 'selected' : '' }}>Ditolak Admin</option>
                </select>
            </div>
            <div class="col-md-4">
                <select class="form-select" id="filterSemester">
                    <option value="">Semua Semester</option>
                    <option value="Ganjil" {{ request('semester') == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                    <option value="Genap" {{ request('semester') == 'Genap' ? 'selected' : '' }}>Genap</option>
                </select>
            </div>
            <div class="col-md-4">
                <button class="btn btn-primary w-100" onclick="applyFilters()">
                    <i class="fas fa-filter me-2"></i>Filter
                </button>
            </div>
        </div>
    </div>
</div>

<!-- SK List -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h6 class="m-0 fw-bold text-primary">
            <i class="fas fa-history me-2"></i>Riwayat SK Beban Mengajar
        </h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="5%">No</th>
                        <th width="18%">Program Studi</th>
                        <th width="15%">Nomor SK</th>
                        <th width="10%">Semester</th>
                        <th width="12%">Tahun Akademik</th>
                        <th width="10%" class="text-center">Jumlah Dosen</th>
                        <th width="12%">Tanggal Dibuat</th>
                        <th width="10%">Status</th>
                        <th width="8%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($skList as $index => $sk)
                    <tr>
                        @php
                            $bebanData = $sk->Data_Beban_Mengajar;
                            if (is_string($bebanData)) {
                                $bebanData = json_decode($bebanData, true);
                            }
                            $jumlahDosen = is_array($bebanData) ? count($bebanData) : 0;
                        @endphp
                        <td>{{ $skList->firstItem() + $index }}</td>
                        <td>{{ $sk->prodi->Nama_Prodi ?? '-' }}</td>
                        <td>
                            <strong class="text-primary">{{ $sk->Nomor_Surat ?? '-' }}</strong>
                        </td>
                        <td>
                            <span class="badge bg-{{ $sk->Semester == 'Ganjil' ? 'primary' : 'info' }}">
                                {{ $sk->Semester }}
                            </span>
                        </td>
                        <td>{{ $sk->Tahun_Akademik }}</td>
                        <td class="text-center">
                            <span class="badge bg-secondary">
                                <i class="fas fa-chalkboard-teacher me-1"></i>{{ $jumlahDosen }}
                            </span>
                        </td>
                        <td>
                            @php
                                $tanggalPengajuan = $sk->{'Tanggal-Pengajuan'};
                            @endphp
                            <small>{{ $tanggalPengajuan ? $tanggalPengajuan->format('d M Y H:i') : '-' }}</small>
                        </td>
                        <td>
                            @php
                                $badgeClass = 'secondary';
                                switch($sk->Status) {
                                    case 'Menunggu-Persetujuan-Wadek-1':
                                        $badgeClass = 'info';
                                        break;
                                    case 'Menunggu-Persetujuan-Dekan':
                                        $badgeClass = 'primary';
                                        break;
                                    case 'Selesai':
                                        $badgeClass = 'success';
                                        break;
                                    case 'Ditolak-Wadek1':
                                    case 'Ditolak-Dekan':
                                    case 'Ditolak-Admin':
                                        $badgeClass = 'danger';
                                        break;
                                }
                                $statusText = str_replace('-', ' ', $sk->Status);
                            @endphp
                            <span class="badge bg-{{ $badgeClass }}">{{ $statusText }}</span>
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm" role="group">
                                <button class="btn btn-outline-primary" 
                                        onclick="showDetail({{ $sk->No }})"
                                        title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </button>
                                @if($sk->Status == 'Selesai')
                                <a href="{{ route('admin_fakultas.sk.beban-mengajar.download', $sk->No) }}" 
                                   class="btn btn-outline-success" 
                                   title="Download SK"
                                   target="_blank">
                                    <i class="fas fa-download"></i>
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-5">
                            <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                            <p class="mb-0">Belum ada riwayat SK Beban Mengajar</p>
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

<!-- Modal Detail -->
<div class="modal fade" id="modalDetail" tabindex="-1" aria-labelledby="modalDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalDetailLabel">
                    <i class="fas fa-file-contract me-2"></i>Detail SK Beban Mengajar
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalDetailContent">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Memuat data...</p>
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
    function applyFilters() {
        const status = document.getElementById('filterStatus').value;
        const semester = document.getElementById('filterSemester').value;
        
        let url = new URL(window.location.href);
        
        if (status) url.searchParams.set('status', status);
        else url.searchParams.delete('status');
        
        if (semester) url.searchParams.set('semester', semester);
        else url.searchParams.delete('semester');
        
        window.location.href = url.toString();
    }

    function showDetail(skId) {
        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('modalDetail'));
        modal.show();
        
        // Load detail
        fetch(`/admin-fakultas/sk/beban-mengajar/${skId}/detail-history`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayDetail(data.sk);
                } else {
                    document.getElementById('modalDetailContent').innerHTML = `
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            ${data.message || 'Gagal memuat data'}
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('modalDetailContent').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        Terjadi kesalahan saat memuat data
                    </div>
                `;
            });
    }

    function displayDetail(sk) {
        // Parse beban mengajar data
        let bebanData = sk.Data_Beban_Mengajar;
        if (typeof bebanData === 'string') {
            try {
                bebanData = JSON.parse(bebanData);
            } catch (e) {
                bebanData = [];
            }
        }

        // Build beban mengajar table
        let bebanTableRows = '';
        if (Array.isArray(bebanData) && bebanData.length > 0) {
            bebanData.forEach((item, index) => {
                bebanTableRows += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${item.nama_dosen || '-'}</td>
                        <td>${item.nip || '-'}</td>
                        <td>${item.mata_kuliah || '-'}</td>
                        <td class="text-center">${item.sks || 0}</td>
                    </tr>
                `;
            });
        } else {
            bebanTableRows = `
                <tr>
                    <td colspan="5" class="text-center text-muted">Tidak ada data beban mengajar</td>
                </tr>
            `;
        }

        // Get status badge
        let badgeClass = 'secondary';
        switch(sk.Status) {
            case 'Menunggu-Persetujuan-Wadek-1':
                badgeClass = 'info';
                break;
            case 'Menunggu-Persetujuan-Dekan':
                badgeClass = 'primary';
                break;
            case 'Selesai':
                badgeClass = 'success';
                break;
            case 'Ditolak-Wadek1':
            case 'Ditolak-Dekan':
            case 'Ditolak-Admin':
                badgeClass = 'danger';
                break;
        }

        // Alasan tolak
        let alasanTolakHtml = '';
        if ((sk.Status.includes('Ditolak')) && sk['Alasan-Tolak']) {
            alasanTolakHtml = `
                <div class="alert alert-danger">
                    <strong><i class="fas fa-exclamation-triangle me-2"></i>Alasan Penolakan:</strong>
                    <p class="mb-0 mt-2">${sk['Alasan-Tolak']}</p>
                </div>
            `;
        }

        const content = `
            <div class="row mb-4">
                <div class="col-md-6">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th width="40%">Program Studi</th>
                            <td>: ${sk.prodi ? sk.prodi.Nama_Prodi : '-'}</td>
                        </tr>
                        <tr>
                            <th>Nomor SK</th>
                            <td>: <strong class="text-primary">${sk.Nomor_Surat || '-'}</strong></td>
                        </tr>
                        <tr>
                            <th>Semester</th>
                            <td>: <span class="badge bg-${sk.Semester == 'Ganjil' ? 'primary' : 'info'}">${sk.Semester}</span></td>
                        </tr>
                        <tr>
                            <th>Tahun Akademik</th>
                            <td>: ${sk.Tahun_Akademik}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th width="40%">Tanggal Pengajuan</th>
                            <td>: ${sk['Tanggal-Pengajuan'] || '-'}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Tenggat</th>
                            <td>: ${sk['Tanggal-Tenggat'] || '-'}</td>
                        </tr>
                        <tr>
                            <th>Jumlah Dosen</th>
                            <td>: <span class="badge bg-secondary">${bebanData.length} Dosen</span></td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>: <span class="badge bg-${badgeClass}">${sk.Status}</span></td>
                        </tr>
                    </table>
                </div>
            </div>

            ${alasanTolakHtml}

            <h6 class="fw-bold mb-3">
                <i class="fas fa-list me-2"></i>Daftar Beban Mengajar
            </h6>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th width="25%">Nama Dosen</th>
                            <th width="20%">NIP</th>
                            <th width="35%">Mata Kuliah</th>
                            <th width="15%" class="text-center">SKS</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${bebanTableRows}
                    </tbody>
                </table>
            </div>
        `;

        document.getElementById('modalDetailContent').innerHTML = content;
    }
</script>
@endpush

@endsection
