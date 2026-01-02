@extends('layouts.kaprodi')

@section('title', 'Riwayat SK Beban Mengajar')

@section('content')

<div class="mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.kaprodi') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kaprodi.sk.index') }}">Ajukan SK</a></li>
            <li class="breadcrumb-item active">Riwayat SK Beban Mengajar</li>
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
                        <i class="fas fa-history fa-lg me-3"></i>
                        <div>
                            <h5 class="mb-0 fw-bold">Riwayat SK Beban Mengajar</h5>
                            <small>Daftar pengajuan SK beban mengajar yang telah diajukan</small>
                        </div>
                    </div>
                    <a href="{{ route('kaprodi.sk.beban-mengajar.create') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-plus-circle me-2"></i>Ajukan SK Baru
                    </a>
                </div>
            </div>
            <div class="card-body p-4">
                <!-- Filter Section -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Semester</label>
                        <select class="form-select" id="filterSemester">
                            <option value="">Semua Semester</option>
                            <option value="Ganjil">Ganjil</option>
                            <option value="Genap">Genap</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Tahun Akademik</label>
                        <select class="form-select" id="filterTahun">
                            <option value="">Semua Tahun</option>
                            <option value="2025/2026">2025/2026</option>
                            <option value="2024/2025">2024/2025</option>
                            <option value="2023/2024">2023/2024</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Status</label>
                        <select class="form-select" id="filterStatus">
                            <option value="">Semua Status</option>
                            <option value="pending">Menunggu</option>
                            <option value="approved">Disetujui</option>
                            <option value="rejected">Ditolak</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">&nbsp;</label>
                        <button type="button" class="btn btn-primary w-100">
                            <i class="fas fa-filter me-2"></i>Terapkan Filter
                        </button>
                    </div>
                </div>

                <!-- Table Section -->
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="tableSK">
                        <thead class="table-light">
                            <tr>
                                <th width="5%" class="text-center">No</th>
                                <th width="15%">Nomor SK</th>
                                <th width="15%">Semester</th>
                                <th width="15%">Tahun Akademik</th>
                                <th width="10%" class="text-center">Total SKS</th>
                                <th width="12%">Tanggal Ajuan</th>
                                <th width="13%" class="text-center">Status</th>
                                <th width="15%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Sample Data 1 -->
                            <tr>
                                <td class="text-center">1</td>
                                <td><strong>SK/BM/001/2025</strong></td>
                                <td>Ganjil</td>
                                <td>2025/2026</td>
                                <td class="text-center"><span class="badge bg-info">45 SKS</span></td>
                                <td>15 Januari 2026</td>
                                <td class="text-center">
                                    <span class="badge bg-success">
                                        <i class="fas fa-check-circle me-1"></i>Disetujui
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button type="button" class="btn btn-info" onclick="lihatDetail(1)" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button type="button" class="btn btn-success" onclick="downloadSK(1)" title="Download SK">
                                            <i class="fas fa-download"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Sample Data 2 -->
                            <tr>
                                <td class="text-center">2</td>
                                <td><strong>SK/BM/002/2025</strong></td>
                                <td>Genap</td>
                                <td>2024/2025</td>
                                <td class="text-center"><span class="badge bg-info">38 SKS</span></td>
                                <td>10 Januari 2026</td>
                                <td class="text-center">
                                    <span class="badge bg-warning text-dark">
                                        <i class="fas fa-clock me-1"></i>Menunggu
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button type="button" class="btn btn-info" onclick="lihatDetail(2)" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button type="button" class="btn btn-warning" onclick="editSK(2)" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger" onclick="hapusSK(2)" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Sample Data 3 -->
                            <tr>
                                <td class="text-center">3</td>
                                <td><strong>SK/BM/003/2024</strong></td>
                                <td>Ganjil</td>
                                <td>2024/2025</td>
                                <td class="text-center"><span class="badge bg-info">42 SKS</span></td>
                                <td>5 Januari 2026</td>
                                <td class="text-center">
                                    <span class="badge bg-danger">
                                        <i class="fas fa-times-circle me-1"></i>Ditolak
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button type="button" class="btn btn-info" onclick="lihatDetail(3)" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button type="button" class="btn btn-primary" onclick="ajukanUlang(3)" title="Ajukan Ulang">
                                            <i class="fas fa-redo"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="text-muted">
                        Menampilkan 1 - 3 dari 3 data
                    </div>
                    <nav>
                        <ul class="pagination pagination-sm mb-0">
                            <li class="page-item disabled">
                                <a class="page-link" href="#" tabindex="-1">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>
                            <li class="page-item active"><a class="page-link" href="#">1</a></li>
                            <li class="page-item disabled">
                                <a class="page-link" href="#">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
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
            <div class="modal-body">
                <!-- Identitas SK -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <th width="40%">Nomor SK</th>
                                <td>: <strong>SK/BM/001/2025</strong></td>
                            </tr>
                            <tr>
                                <th>Program Studi</th>
                                <td>: S1 Teknik Informatika</td>
                            </tr>
                            <tr>
                                <th>Semester</th>
                                <td>: Ganjil</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <th width="40%">Tahun Akademik</th>
                                <td>: 2025/2026</td>
                            </tr>
                            <tr>
                                <th>Tanggal Ajuan</th>
                                <td>: 15 Januari 2026</td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>: <span class="badge bg-success">Disetujui</span></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <hr>

                <!-- Detail Beban Mengajar -->
                <h6 class="fw-bold mb-3">Daftar Beban Mengajar</h6>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="table-light">
                            <tr>
                                <th width="5%" class="text-center">No</th>
                                <th width="30%">Nama Dosen</th>
                                <th width="30%">Mata Kuliah</th>
                                <th width="20%">Kelas</th>
                                <th width="15%" class="text-center">SKS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center">1</td>
                                <td>Dr. Ahmad Fauzi, M.Kom</td>
                                <td>Pemrograman Web</td>
                                <td>IF 5A</td>
                                <td class="text-center">3</td>
                            </tr>
                            <tr>
                                <td class="text-center">2</td>
                                <td>Dr. Ahmad Fauzi, M.Kom</td>
                                <td>Basis Data Lanjut</td>
                                <td>IF 5B</td>
                                <td class="text-center">3</td>
                            </tr>
                            <tr>
                                <td class="text-center">3</td>
                                <td>Dr. Siti Nurhaliza, M.T</td>
                                <td>Rekayasa Perangkat Lunak</td>
                                <td>IF 6A</td>
                                <td class="text-center">4</td>
                            </tr>
                        </tbody>
                        <tfoot class="table-secondary">
                            <tr class="fw-bold">
                                <td colspan="4" class="text-end">Total SKS:</td>
                                <td class="text-center">10</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Catatan -->
                <div class="mt-3">
                    <h6 class="fw-bold">Catatan:</h6>
                    <p class="text-muted mb-0">SK ini telah disetujui oleh Admin Fakultas pada tanggal 16 Januari 2026.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Tutup
                </button>
                <button type="button" class="btn btn-success" onclick="downloadSK(1)">
                    <i class="fas fa-download me-2"></i>Download PDF
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function lihatDetail(id) {
        // Show modal with detail
        const modal = new bootstrap.Modal(document.getElementById('detailModal'));
        modal.show();
    }
    
    function downloadSK(id) {
        alert('Download SK ID: ' + id + ' (Fitur akan segera tersedia)');
    }
    
    function editSK(id) {
        if (confirm('Apakah Anda yakin ingin mengedit SK ini?')) {
            window.location.href = '{{ route("kaprodi.sk.beban-mengajar.create") }}?edit=' + id;
        }
    }
    
    function hapusSK(id) {
        if (confirm('Apakah Anda yakin ingin menghapus pengajuan SK ini? Tindakan ini tidak dapat dibatalkan.')) {
            alert('Hapus SK ID: ' + id + ' (Fitur akan segera tersedia)');
        }
    }
    
    function ajukanUlang(id) {
        if (confirm('Apakah Anda yakin ingin mengajukan ulang SK ini?')) {
            window.location.href = '{{ route("kaprodi.sk.beban-mengajar.create") }}?resubmit=' + id;
        }
    }
</script>
@endpush

@endsection
