@extends('layouts.dosen')

@section('title', 'SK Beban Mengajar')

@section('content')

<div class="mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.dosen') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('dosen.sk.index') }}">SK Dosen</a></li>
            <li class="breadcrumb-item active">SK Beban Mengajar</li>
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
            <div class="card-header bg-primary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-chalkboard-teacher fa-lg me-3"></i>
                        <div>
                            <h5 class="mb-0 fw-bold">SK Beban Mengajar Saya</h5>
                            <small>Daftar SK beban mengajar yang melibatkan Anda</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-4">
                @if($filteredSK->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="5%" class="text-center">No</th>
                                <th width="15%">Nomor SK</th>
                                <th width="12%">Semester</th>
                                <th width="15%">Tahun Akademik</th>
                                <th width="15%">Tanggal SK</th>
                                <th width="10%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($filteredSK as $index => $sk)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>
                                    <strong class="text-primary">{{ $sk->Nomor_Surat }}</strong>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $sk->Semester == 'Ganjil' ? 'info' : 'warning' }}">
                                        {{ $sk->Semester }}
                                    </span>
                                </td>
                                <td>{{ $sk->Tahun_Akademik }}</td>
                                <td>
                                    <i class="fas fa-calendar me-1 text-muted"></i>
                                    {{ $sk->{'Tanggal-Persetujuan-Dekan'} ? \Carbon\Carbon::parse($sk->{'Tanggal-Persetujuan-Dekan'})->format('d M Y') : '-' }}
                                </td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-outline-info" onclick="lihatDetail({{ $sk->No }})" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <a href="{{ route('dosen.sk.beban-mengajar.download', $sk->No) }}" class="btn btn-sm btn-outline-primary" target="_blank" title="Download PDF">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Belum Ada SK Beban Mengajar</h5>
                    <p class="text-muted">Anda belum terdaftar dalam SK beban mengajar yang telah disetujui</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail SK -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="detailModalLabel">
                    <i class="fas fa-file-alt me-2"></i>Detail SK Beban Mengajar
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
    function lihatDetail(skId) {
        const modal = new bootstrap.Modal(document.getElementById('detailModal'));
        modal.show();
        
        fetch(`/dosen/sk/beban-mengajar/${skId}/detail`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayDetail(data.sk, data.dekanName, data.dekanNip, data.qrCodePath);
                } else {
                    document.getElementById('modalDetailContent').innerHTML = `
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i>${data.message}
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('modalDetailContent').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>Gagal memuat detail SK
                    </div>
                `;
            });
    }
    
    function displayDetail(sk, dekanName, dekanNip, qrCodePath) {
        let bebanData = sk.Data_Beban_Mengajar;
        if (typeof bebanData === 'string') {
            bebanData = JSON.parse(bebanData);
        }

        // Filter hanya beban mengajar untuk dosen ini
        const dosenName = '{{ $dosen->Nama_Dosen }}';
        const myBeban = bebanData.filter(item => 
            item.nama_dosen && item.nama_dosen.toLowerCase().includes(dosenName.toLowerCase())
        );

        const semesterUpper = sk.Semester ? sk.Semester.toUpperCase() : 'GANJIL';
        const tahunAkademik = sk.Tahun_Akademik || '2023/2024';
        const nomorSurat = sk.Nomor_Surat || '-';

        let html = `
            <div class="card mb-3">
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="fas fa-info-circle me-2 text-primary"></i>Informasi SK</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tr><td class="fw-semibold">Nomor SK:</td><td>${nomorSurat}</td></tr>
                                <tr><td class="fw-semibold">Semester:</td><td>${semesterUpper}</td></tr>
                                <tr><td class="fw-semibold">Tahun Akademik:</td><td>${tahunAkademik}</td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tr><td class="fw-semibold">Dekan:</td><td>${dekanName}</td></tr>
                                <tr><td class="fw-semibold">NIP:</td><td>${dekanNip}</td></tr>
                                <tr><td class="fw-semibold">Tanggal:</td><td>${sk['Tanggal-Persetujuan-Dekan'] || '-'}</td></tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="fas fa-chalkboard-teacher me-2 text-primary"></i>Beban Mengajar Saya</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%" class="text-center">No</th>
                                    <th width="35%">Mata Kuliah</th>
                                    <th width="15%" class="text-center">Kelas</th>
                                    <th width="10%" class="text-center">SKS</th>
                                    <th width="35%">Program Studi</th>
                                </tr>
                            </thead>
                            <tbody>
        `;

        if (myBeban.length > 0) {
            myBeban.forEach((item, idx) => {
                const mataKuliah = item.nama_mata_kuliah || item.mata_kuliah || item.Nama_Matakuliah || '-';
                const kelas = item.kelas || item.Kelas || '-';
                const sks = item.sks || item.SKS || 0;
                const prodi = item.Nama_Prodi || item.prodi || '-';
                
                html += `
                    <tr>
                        <td class="text-center">${idx + 1}</td>
                        <td>${mataKuliah}</td>
                        <td class="text-center">${kelas}</td>
                        <td class="text-center">${sks}</td>
                        <td>${prodi}</td>
                    </tr>
                `;
            });
        } else {
            html += `
                <tr>
                    <td colspan="5" class="text-center text-muted">Tidak ada data beban mengajar</td>
                </tr>
            `;
        }

        html += `
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        `;

        document.getElementById('modalDetailContent').innerHTML = html;
    }
</script>
@endpush

@endsection
