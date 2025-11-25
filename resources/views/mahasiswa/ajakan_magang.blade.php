@extends('layouts.mahasiswa')

@section('title', 'Ajakan Magang')

@section('content')

{{-- Header Halaman --}}
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-handshake text-primary"></i> Ajakan Magang
    </h1>
</div>

{{-- Alert Success/Error/Warning --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('warning'))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle"></i> {{ session('warning') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Stats Cards --}}
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Undangan</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $invitations->count() }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-envelope fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Menunggu Respon</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $invitations->where('status', 'pending')->count() }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Diterima</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $invitations->where('status', 'accepted')->count() }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Ditolak</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $invitations->where('status', 'rejected')->count() }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Invitations List --}}
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-list"></i> Daftar Undangan Magang
        </h6>
    </div>
    <div class="card-body">
        @if($invitations->isEmpty())
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle fa-2x mb-3"></i>
                <p class="mb-0">Belum ada undangan magang dari teman Anda.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">#</th>
                            <th>Tanggal Undangan</th>
                            <th>Diundang Oleh</th>
                            <th>Perusahaan</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invitations as $index => $invitation)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>
                                    <i class="fas fa-calendar text-muted"></i>
                                    {{ $invitation->invited_at ? \Carbon\Carbon::parse($invitation->invited_at)->format('d M Y H:i') : '-' }}
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-user-circle fa-2x text-primary me-2"></i>
                                        <div>
                                            <strong>{{ $invitation->mahasiswaPengundang->user->Name_User ?? 'N/A' }}</strong><br>
                                            <small class="text-muted">NIM: {{ $invitation->mahasiswaPengundang->NIM ?? '-' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <strong>{{ $invitation->suratMagang->Nama_Instansi ?? 'N/A' }}</strong><br>
                                        <small class="text-muted">
                                            <i class="fas fa-map-marker-alt"></i> {{ $invitation->suratMagang->Alamat_Instansi ?? '-' }}
                                        </small>
                                    </div>
                                </td>
                                <td class="text-center">
                                    @php
                                        $status = strtolower(trim($invitation->status));
                                        $badgeClass = 'secondary';
                                        $icon = 'circle';
                                        
                                        if ($status === 'pending') {
                                            $badgeClass = 'warning';
                                            $icon = 'clock';
                                        } elseif ($status === 'accepted') {
                                            $badgeClass = 'success';
                                            $icon = 'check-circle';
                                        } elseif ($status === 'rejected') {
                                            $badgeClass = 'danger';
                                            $icon = 'times-circle';
                                        }
                                    @endphp
                                    <span class="badge bg-{{ $badgeClass }}">
                                        <i class="fas fa-{{ $icon }}"></i> {{ ucfirst($invitation->status) }}
                                    </span>
                                    @if($invitation->responded_at)
                                        <br><small class="text-muted">{{ \Carbon\Carbon::parse($invitation->responded_at)->format('d M Y') }}</small>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($invitation->status === 'pending')
                                        <div class="btn-group" role="group">
                                            <form action="{{ route('mahasiswa.ajakan-magang.accept', ['id' => $invitation->id_no]) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm" 
                                                    onclick="return confirm('Apakah Anda yakin ingin menerima undangan magang ini?')">
                                                    <i class="fas fa-check"></i> Terima
                                                </button>
                                            </form>
                                            <button type="button" class="btn btn-danger btn-sm" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#rejectModal{{ $invitation->id_no }}">
                                                <i class="fas fa-times"></i> Tolak
                                            </button>
                                        </div>

                                        {{-- Modal Tolak --}}
                                        <div class="modal fade" id="rejectModal{{ $invitation->id_no }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Tolak Undangan Magang</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form action="{{ route('mahasiswa.ajakan-magang.reject', ['id' => $invitation->id_no]) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-body">
                                                            <p>Apakah Anda yakin ingin menolak undangan magang dari <strong>{{ $invitation->mahasiswaPengundang->user->Name_User ?? 'N/A' }}</strong>?</p>
                                                            <div class="mb-3">
                                                                <label for="keterangan{{ $invitation->id_no }}" class="form-label">Alasan Penolakan (Opsional)</label>
                                                                <textarea class="form-control" id="keterangan{{ $invitation->id_no }}" name="keterangan" rows="3" placeholder="Tuliskan alasan penolakan..."></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-danger">
                                                                <i class="fas fa-times"></i> Tolak Undangan
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted">
                                            <i class="fas fa-check"></i> Sudah Direspons
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

@endsection
