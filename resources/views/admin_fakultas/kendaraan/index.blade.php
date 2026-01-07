@extends('layouts.admin_fakultas')

@section('title', 'Kelola Kendaraan Dinas')

@push('styles')
<style>
    .page-header {
        background: #ffffff;
        border-bottom: 2px solid #f0f0f0;
        padding: 2rem 0;
        margin-bottom: 2rem;
    }
    
    .table-responsive {
        border-radius: 12px;
        overflow: hidden;
    }
    
    .table thead th {
        background: #f8f9fc;
        color: #5a5c69;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        border: none;
    }
    
    .table tbody tr {
        transition: background-color 0.2s ease;
    }
    
    .table tbody tr:hover {
        background-color: #f8f9fc;
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h3 class="mb-2 fw-bold text-dark">Kelola Kendaraan Dinas</h3>
            <p class="mb-0 text-muted">Manajemen data kendaraan dinas fakultas</p>
        </div>
        <a href="{{ route('admin_fakultas.surat.mobil_dinas') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert" style="border-radius: 12px;">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert" style="border-radius: 12px;">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card shadow-sm">
    <div class="card-header py-3 bg-white d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Kendaraan</h6>
        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addKendaraanModal">
            <i class="fas fa-plus me-2"></i>Tambah Kendaraan
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Kendaraan</th>
                        <th>Plat Nomor</th>
                        <th>Kapasitas</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kendaraan as $index => $k)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $k->nama_kendaraan }}</td>
                            <td><span class="badge bg-secondary">{{ $k->plat_nomor }}</span></td>
                            <td>{{ $k->kapasitas }} orang</td>
                            <td>
                                @if($k->status_kendaraan == 'Tersedia')
                                    <span class="badge bg-success">Tersedia</span>
                                @else
                                    <span class="badge bg-warning text-dark">Maintenance</span>
                                @endif
                            </td>
                            <td>
                                <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#editKendaraanModal{{ $k->id }}">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                Belum ada data kendaraan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal Add Kendaraan --}}
<div class="modal fade" id="addKendaraanModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Tambah Kendaraan Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin_fakultas.kendaraan.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Kendaraan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama_kendaraan" required placeholder="Contoh: Toyota Avanza">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Plat Nomor <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="plat_nomor" required placeholder="Contoh: N 1234 AB">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kapasitas <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="kapasitas" required min="1" placeholder="Jumlah penumpang">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select" name="status_kendaraan" required>
                            <option value="Tersedia">Tersedia</option>
                            <option value="Maintenance">Maintenance</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Edit Kendaraan untuk setiap kendaraan --}}
@foreach($kendaraan as $k)
<div class="modal fade" id="editKendaraanModal{{ $k->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Kendaraan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin_fakultas.kendaraan.update', $k->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Kendaraan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama_kendaraan" value="{{ $k->nama_kendaraan }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Plat Nomor <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="plat_nomor" value="{{ $k->plat_nomor }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kapasitas <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="kapasitas" value="{{ $k->kapasitas }}" required min="1">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select" name="status_kendaraan" required>
                            <option value="Tersedia" {{ $k->status_kendaraan == 'Tersedia' ? 'selected' : '' }}>Tersedia</option>
                            <option value="Maintenance" {{ $k->status_kendaraan == 'Maintenance' ? 'selected' : '' }}>Maintenance</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

@endsection
