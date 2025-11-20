@extends('layouts.admin_prodi')

@section('title', 'Manajemen Surat')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800">Manajemen Surat Fakultas</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.admin') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Manajemen Surat</li>
            </ol>
        </nav>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-start border-success border-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fa fa-check-circle text-success me-2 fa-lg"></i>
                <div>{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-start border-danger border-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fa fa-exclamation-circle text-danger me-2 fa-lg"></i>
                <div>{{ session('error') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3 border-bottom">
            <ul class="nav nav-pills card-header-pills" id="suratTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fw-bold" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab">
                        <i class="fa fa-clock me-2"></i>Perlu Diproses 
                        @if($suratPending->count() > 0)
                            <span class="badge bg-danger rounded-pill ms-2">{{ $suratPending->count() }}</span>
                        @endif
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold" id="semua-tab" data-bs-toggle="tab" data-bs-target="#semua" type="button" role="tab">
                        <i class="fa fa-folder-open me-2"></i>Semua Surat Aktif
                    </button>
                </li>
            </ul>
        </div>
        
        <div class="card-body p-0">
            <div class="tab-content" id="suratTabsContent">
                
                {{-- TAB 1: SURAT PENDING --}}
                <div class="tab-pane fade show active" id="pending" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" width="100%" cellspacing="0">
                            <thead class="bg-light text-secondary text-uppercase small fw-bold">
                                <tr>
                                    <th class="px-4 py-3" width="5%">No</th>
                                    <th class="py-3" width="15%">Tgl. Masuk</th>
                                    <th class="py-3" width="25%">Pengaju</th>
                                    <th class="py-3" width="25%">Jenis Surat</th>
                                    <th class="py-3 text-center" width="15%">Status</th>
                                    <th class="py-3 text-center" width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($suratPending as $index => $tugas)
                                    @php
                                        $pengaju = $tugas->pemberiTugas;
                                        $mahasiswa = optional($pengaju)->mahasiswa;
                                        $status = trim($tugas->Status ?? '');
                                    @endphp
                                    <tr>
                                        <td class="px-4 text-center text-muted">{{ $index + 1 }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="far fa-calendar-alt text-muted me-2"></i>
                                                {{ $tugas->Tanggal_Diberikan_Tugas_Surat ? $tugas->Tanggal_Diberikan_Tugas_Surat->format('d M Y') : '-' }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $pengaju->Name_User ?? 'User Dihapus' }}</div>
                                            <div class="small text-muted">
                                                @if($mahasiswa) NIM: {{ $mahasiswa->NIM }} @endif
                                                ({{ optional($pengaju->role)->Name_Role ?? 'N/A' }})
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-primary fw-medium">{{ optional($tugas->jenisSurat)->Nama_Surat ?? 'N/A' }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge rounded-pill bg-warning text-dark border border-warning">
                                                <i class="fa fa-hourglass-half me-1"></i> {{ $tugas->Status }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-info" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#previewModal{{ $tugas->Id_Tugas_Surat }}"
                                                        title="Preview Dokumen">
                                                    <i class="fa fa-eye"></i>
                                                </button>
                                                <a href="{{ route('admin.surat.detail', $tugas->Id_Tugas_Surat) }}" 
                                                   class="btn btn-sm btn-primary" 
                                                   title="Proses & Verifikasi">
                                                    Proses <i class="fa fa-arrow-right ms-1"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <div class="text-muted">
                                                <i class="fa fa-check-circle fa-3x mb-3 text-success opacity-50"></i>
                                                <p class="mb-0 fw-medium">Tidak ada surat yang menunggu verifikasi.</p>
                                                <small>Semua pengajuan baru telah diproses.</small>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- TAB 2: SEMUA SURAT AKTIF --}}
                <div class="tab-pane fade" id="semua" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" width="100%" cellspacing="0">
                            <thead class="bg-light text-secondary text-uppercase small fw-bold">
                                <tr>
                                    <th class="px-4 py-3" width="5%">No</th>
                                    <th class="py-3" width="12%">Tgl. Masuk</th>
                                    <th class="py-3" width="20%">Pengaju</th>
                                    <th class="py-3" width="20%">Jenis Surat</th>
                                    <th class="py-3 text-center" width="15%">Status</th>
                                    <th class="py-3" width="18%">Penerima Saat Ini</th>
                                    <th class="py-3 text-center" width="10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($daftarTugas as $index => $tugas)
                                    @php
                                        $status = trim($tugas->Status ?? '');
                                        $pengaju = $tugas->pemberiTugas;
                                        $penerima = $tugas->penerimaTugas;
                                        
                                        // Status Badge Logic
                                        $badgeClass = 'bg-secondary';
                                        $icon = 'fa-circle';
                                        
                                        if(in_array(strtolower($status), ['selesai', 'telah ditandatangani dekan'])) {
                                            $badgeClass = 'bg-success';
                                            $icon = 'fa-check-circle';
                                        } elseif(in_array(strtolower($status), ['ditolak', 'terlambat'])) {
                                            $badgeClass = 'bg-danger';
                                            $icon = 'fa-times-circle';
                                        } elseif(strtolower($status) === 'menunggu-ttd') {
                                            $badgeClass = 'bg-info text-dark';
                                            $icon = 'fa-pen-nib';
                                        } elseif(in_array(strtolower($status), ['diterima admin', 'baru'])) {
                                            $badgeClass = 'bg-warning text-dark';
                                            $icon = 'fa-clock';
                                        } elseif(strtolower($status) === 'proses') {
                                            $badgeClass = 'bg-primary';
                                            $icon = 'fa-spinner fa-spin';
                                        }
                                    @endphp
                                    <tr>
                                        <td class="px-4 text-center text-muted">{{ $index + 1 }}</td>
                                        <td>{{ $tugas->Tanggal_Diberikan_Tugas_Surat ? $tugas->Tanggal_Diberikan_Tugas_Surat->format('d M Y') : '-' }}</td>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $pengaju->Name_User ?? 'User Dihapus' }}</div>
                                            <div class="small text-muted">({{ optional($pengaju->role)->Name_Role ?? 'N/A' }})</div>
                                        </td>
                                        <td>{{ optional($tugas->jenisSurat)->Nama_Surat ?? 'N/A' }}</td>
                                        <td class="text-center">
                                            <span class="badge rounded-pill {{ $badgeClass }}">
                                                <i class="fa {{ $icon }} me-1"></i> 
                                                @if(strtolower($status) === 'menunggu-ttd')
                                                    Menunggu TTE
                                                @else
                                                    {{ $tugas->Status }}
                                                @endif
                                            </span>
                                        </td>
                                        <td>
                                            @if($penerima)
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm bg-light rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                                        <i class="fa fa-user text-secondary small"></i>
                                                    </div>
                                                    <div>
                                                        <div class="small fw-bold">{{ $penerima->Name_User }}</div>
                                                        <div class="small text-muted" style="font-size: 0.75rem;">{{ optional($penerima->role)->Name_Role }}</div>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-secondary" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#previewModalAll{{ $tugas->Id_Tugas_Surat }}"
                                                        title="Preview">
                                                    <i class="fa fa-file-pdf"></i>
                                                </button>
                                                <a href="{{ route('admin.surat.detail', $tugas->Id_Tugas_Surat) }}" 
                                                   class="btn btn-sm btn-outline-primary" 
                                                   title="Detail">
                                                    <i class="fa fa-info-circle"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-muted">
                                            <i class="fa fa-inbox fa-3x mb-3 opacity-50"></i>
                                            <p>Tidak ada data surat aktif.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODALS (Keep logic but improve design) --}}
    @foreach($daftarTugas as $tugas)
        <div class="modal fade" id="previewModal{{ $tugas->Id_Tugas_Surat }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="fa fa-file-pdf me-2"></i>Preview Dokumen
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-0 bg-light" style="height: 80vh;">
                        <div class="p-3 bg-white border-bottom shadow-sm">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="small text-muted text-uppercase fw-bold">Pengaju</label>
                                    <div class="fw-bold">{{ optional($tugas->pemberiTugas)->Name_User ?? '-' }}</div>
                                </div>
                                <div class="col-md-4">
                                    <label class="small text-muted text-uppercase fw-bold">Jenis Surat</label>
                                    <div>{{ optional($tugas->jenisSurat)->Nama_Surat ?? '-' }}</div>
                                </div>
                                <div class="col-md-4">
                                    <label class="small text-muted text-uppercase fw-bold">Tanggal</label>
                                    <div>{{ $tugas->Tanggal_Diberikan_Tugas_Surat ? $tugas->Tanggal_Diberikan_Tugas_Surat->format('d M Y') : '-' }}</div>
                                </div>
                            </div>
                        </div>
                        
                        <iframe 
                            src="{{ route('admin.surat.preview', $tugas->Id_Tugas_Surat) }}" 
                            width="100%" 
                            height="100%" 
                            style="border: none;"
                            class="bg-secondary">
                        </iframe>
                    </div>
                    <div class="modal-footer bg-white">
                        <a href="{{ route('admin.surat.download', $tugas->Id_Tugas_Surat) }}" 
                           class="btn btn-outline-secondary" target="_blank">
                            <i class="fa fa-download me-1"></i> Download PDF
                        </a>
                        <a href="{{ route('admin.surat.detail', $tugas->Id_Tugas_Surat) }}" class="btn btn-primary px-4">
                            Proses Surat <i class="fa fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Duplicate modal ID for "All" tab to avoid ID conflicts if needed, or just use same ID --}}
        {{-- Actually, in the loop above I used previewModal{{id}} for pending and previewModalAll{{id}} for all. --}}
        {{-- Let's create the second one --}}
        <div class="modal fade" id="previewModalAll{{ $tugas->Id_Tugas_Surat }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-dark text-white">
                        <h5 class="modal-title">
                            <i class="fa fa-file-pdf me-2"></i>Preview Dokumen
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-0 bg-light" style="height: 80vh;">
                        <iframe 
                            src="{{ route('admin.surat.preview', $tugas->Id_Tugas_Surat) }}" 
                            width="100%" 
                            height="100%" 
                            style="border: none;"
                            class="bg-secondary">
                        </iframe>
                    </div>
                    <div class="modal-footer bg-white">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <a href="{{ route('admin.surat.detail', $tugas->Id_Tugas_Surat) }}" class="btn btn-info text-white">
                            <i class="fa fa-info-circle me-1"></i> Lihat Detail
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

</div>

<style>
    .nav-pills .nav-link {
        color: #6c757d;
        border-radius: 0.5rem;
        padding: 0.75rem 1.25rem;
        transition: all 0.2s;
    }
    .nav-pills .nav-link:hover {
        background-color: #f8f9fa;
        color: #212529;
    }
    .nav-pills .nav-link.active {
        background-color: #0d6efd;
        color: white;
        box-shadow: 0 2px 4px rgba(13, 110, 253, 0.3);
    }
    .table-hover tbody tr:hover {
        background-color: rgba(0,0,0,.02);
    }
</style>
@endsection



