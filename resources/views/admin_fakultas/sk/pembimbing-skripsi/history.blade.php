@extends('layouts.admin_fakultas')

@section('title', 'History SK Pembimbing Skripsi')

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
        <h1 class="h3 fw-bold mb-0">History SK Pembimbing Skripsi</h1>
        <p class="mb-0 text-muted">Riwayat SK Pembimbing Skripsi yang telah diproses</p>
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
        <h6 class="m-0 fw-bold text-warning">
            <i class="fas fa-history me-2"></i>Riwayat SK Pembimbing Skripsi
        </h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="5%">No</th>
                        <th width="20%">Nomor SK</th>
                        <th width="12%">Semester</th>
                        <th width="13%">Tahun Akademik</th>
                        <th width="12%" class="text-center">Jumlah Mahasiswa</th>
                        <th width="15%">Tanggal Dibuat</th>
                        <th width="13%">Status</th>
                        <th width="10%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($skList as $index => $sk)
                    <tr>
                        @php
                            $dataPembimbing = $sk->Data_Pembimbing_Skripsi;
                            if (is_string($dataPembimbing)) {
                                $dataPembimbing = json_decode($dataPembimbing, true);
                            }
                            $jumlahMahasiswa = is_array($dataPembimbing) ? count($dataPembimbing) : 0;
                        @endphp
                        <td>{{ $skList->firstItem() + $index }}</td>
                        <td>
                            <strong class="text-warning">{{ $sk->Nomor_Surat ?? '-' }}</strong>
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
                            @php
                                $tanggalPengajuan = $sk->Tanggal_Pengajuan;
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
                                        $badgeClass = 'danger';
                                        break;
                                    case 'Ditolak-Dekan':
                                        $badgeClass = 'danger';
                                        break;
                                }
                                $statusText = str_replace('-', ' ', $sk->Status);
                            @endphp
                            <span class="badge bg-{{ $badgeClass }}">{{ $statusText }}</span>
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm" role="group">
                                <button class="btn btn-outline-warning" 
                                        onclick="showDetail({{ $sk->No }})"
                                        title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </button>
                                @if($sk->Status == 'Selesai' && $sk->QR_Code)
                                <a href="{{ route('admin_fakultas.sk.pembimbing-skripsi.download', $sk->No) }}" 
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
                        <td colspan="8" class="text-center text-muted py-5">
                            <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                            <p class="mb-0">Belum ada riwayat SK Pembimbing Skripsi</p>
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
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="modalDetailLabel">
                    <i class="fas fa-file-contract me-2"></i>Detail SK Pembimbing Skripsi
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalDetailContent">
                <div class="text-center py-5">
                    <div class="spinner-border text-warning" role="status">
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

    function showDetail(skNo) {
        const modal = new bootstrap.Modal(document.getElementById('modalDetail'));
        modal.show();

        // Fetch detail from server
        fetch(`{{ url('/admin-fakultas/sk/pembimbing-skripsi') }}/${skNo}/detail-history`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderDetail(data.sk);
            } else {
                document.getElementById('modalDetailContent').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
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

    function renderDetail(sk) {
        const mahasiswaList = sk.Data_Pembimbing_Skripsi || [];
        
        // Group by prodi
        const groupedByProdi = {};
        mahasiswaList.forEach(mhs => {
            const prodiName = (mhs.prodi_data && mhs.prodi_data.nama_prodi) ? mhs.prodi_data.nama_prodi : (mhs.prodi || 'Tidak ada prodi');
            if (!groupedByProdi[prodiName]) {
                groupedByProdi[prodiName] = [];
            }
            groupedByProdi[prodiName].push(mhs);
        });

        let prodiTablesHtml = '';
        Object.keys(groupedByProdi).forEach(prodiName => {
            const mahasiswaProdi = groupedByProdi[prodiName];
            let mahasiswaRowsHtml = '';
            mahasiswaProdi.forEach((mhs, idx) => {
                mahasiswaRowsHtml += `
                    <tr>
                        <td class="text-center">${idx + 1}</td>
                        <td>${mhs.nim || '-'}</td>
                        <td>${mhs.nama_mahasiswa || '-'}</td>
                        <td class="small">${mhs.judul_skripsi || '-'}</td>
                        <td class="small">
                            ${mhs.pembimbing_1 ? mhs.pembimbing_1.nama_dosen : '-'}<br>
                            <small class="text-muted">NIP: ${mhs.pembimbing_1 ? mhs.pembimbing_1.nip : '-'}</small>
                        </td>
                        <td class="small">
                            ${mhs.pembimbing_2 ? mhs.pembimbing_2.nama_dosen : '-'}<br>
                            <small class="text-muted">NIP: ${mhs.pembimbing_2 ? mhs.pembimbing_2.nip : '-'}</small>
                        </td>
                    </tr>
                `;
            });

            prodiTablesHtml += `
                <div class="mb-4">
                    <h6 class="fw-bold text-warning mb-3">
                        <i class="fas fa-graduation-cap me-2"></i>${prodiName}
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" width="50">No</th>
                                    <th width="120">NIM</th>
                                    <th width="180">Nama Mahasiswa</th>
                                    <th>Judul Skripsi</th>
                                    <th width="200">Pembimbing 1</th>
                                    <th width="200">Pembimbing 2</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${mahasiswaRowsHtml}
                            </tbody>
                        </table>
                    </div>
                </div>
            `;
        });

        const statusClass = {
            'Menunggu-Persetujuan-Wadek-1': 'bg-info',
            'Menunggu-Persetujuan-Dekan': 'bg-primary',
            'Selesai': 'bg-success',
            'Ditolak-Wadek1': 'bg-danger',
            'Ditolak-Dekan': 'bg-danger'
        }[sk.Status] || 'bg-secondary';

        const statusText = sk.Status.replace(/-/g, ' ');

        const html = `
            <div class="row mb-4">
                <div class="col-md-6">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td width="150" class="text-muted">Nomor SK</td>
                            <td><strong class="text-warning">${sk.Nomor_Surat || '-'}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Semester</td>
                            <td><strong>${sk.Semester}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tahun Akademik</td>
                            <td><strong>${sk.Tahun_Akademik}</strong></td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td width="150" class="text-muted">Tanggal Dibuat</td>
                            <td><strong>${new Date(sk.Tanggal_Pengajuan).toLocaleString('id-ID')}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Total Mahasiswa</td>
                            <td><strong class="text-warning">${mahasiswaList.length} Mahasiswa</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Status</td>
                            <td><span class="badge ${statusClass}">${statusText}</span></td>
                        </tr>
                    </table>
                </div>
            </div>

            ${(sk.Status === 'Ditolak-Wadek1' || sk.Status === 'Ditolak-Dekan') && sk.Alasan_Tolak ? `
            <div class="alert alert-danger mb-4">
                <div class="d-flex align-items-start">
                    <i class="fas fa-exclamation-circle fa-2x me-3"></i>
                    <div>
                        <h6 class="alert-heading fw-bold mb-2">SK Ditolak ${sk.Status === 'Ditolak-Wadek1' ? 'oleh Wadek 1' : 'oleh Dekan'}</h6>
                        <p class="mb-0"><strong>Alasan Penolakan:</strong></p>
                        <p class="mb-0">${sk.Alasan_Tolak}</p>
                    </div>
                </div>
            </div>
            ` : ''}

            <hr>

            <h6 class="fw-bold mb-3">Daftar Mahasiswa dan Dosen Pembimbing Per Program Studi</h6>
            ${prodiTablesHtml}
        `;

        document.getElementById('modalDetailContent').innerHTML = html;
    }
</script>
@endpush

@endsection
