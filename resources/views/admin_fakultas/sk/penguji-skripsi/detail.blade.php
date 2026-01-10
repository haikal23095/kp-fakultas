@extends('layouts.admin_fakultas')

@section('title', 'Detail SK Penguji Skripsi')

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-0">Detail SK Penguji Skripsi</h1>
        <p class="mb-0 text-muted">Informasi lengkap pengajuan SK</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin_fakultas.sk.penguji-skripsi') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
        @if($sk->Status == 'Dikerjakan admin')
        <button type="button" 
                class="btn btn-danger" 
                onclick="showRejectModal()">
            <i class="fas fa-times me-2"></i>Tolak
        </button>
        @endif
    </div>
</div>

<!-- SK Information Card -->
<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-danger text-white">
                <h6 class="m-0"><i class="fas fa-info-circle me-2"></i>Informasi SK</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless table-sm mb-0">
                    <tr>
                        <th width="40%">Nomor SK</th>
                        <td>: {{ $sk->Nomor_Surat ?: '-' }}</td>
                    </tr>
                    <tr>
                        <th>Program Studi</th>
                        <td>: {{ $sk->prodi->Nama_Prodi ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Semester</th>
                        <td>: 
                            <span class="badge bg-{{ $sk->Semester == 'Ganjil' ? 'primary' : 'info' }}">
                                {{ $sk->Semester }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Tahun Akademik</th>
                        <td>: {{ $sk->Tahun_Akademik }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>: 
                            @php
                                $badgeClass = 'secondary';
                                $statusText = $sk->Status;
                                
                                switch($sk->Status) {
                                    case 'Dikerjakan admin':
                                        $badgeClass = 'warning';
                                        $statusText = 'Dikerjakan Admin';
                                        break;
                                    case 'Menunggu Persetujuan Dekan':
                                        $badgeClass = 'primary';
                                        $statusText = 'Menunggu Dekan';
                                        break;
                                    case 'Selesai':
                                        $badgeClass = 'success';
                                        $statusText = 'Selesai';
                                        break;
                                    case 'Ditolak-Admin':
                                        $badgeClass = 'danger';
                                        $statusText = 'Ditolak Admin';
                                        break;
                                    case 'Ditolak':
                                        $badgeClass = 'danger';
                                        $statusText = 'Ditolak Dekan';
                                        break;
                                }
                            @endphp
                            <span class="badge bg-{{ $badgeClass }}">{{ $statusText }}</span>
                        </td>
                    </tr>
                    <tr>
                        <th>Tanggal Pengajuan</th>
                        <td>: {{ $sk->{'Tanggal-Pengajuan'} ? \Carbon\Carbon::parse($sk->{'Tanggal-Pengajuan'})->format('d F Y H:i') : '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-primary text-white">
                <h6 class="m-0"><i class="fas fa-user-tie me-2"></i>Informasi Pengaju</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless table-sm mb-0">
                    <tr>
                        <th width="40%">Kaprodi</th>
                        <td>: {{ $sk->kaprodi->Nama_Dosen ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>NIP</th>
                        <td>: {{ $sk->kaprodi->NIP ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Rejection Info (if rejected) -->
@if(in_array($sk->Status, ['Ditolak-Admin', 'Ditolak']))
<div class="alert alert-danger border-0 shadow-sm">
    <div class="d-flex align-items-start">
        <i class="fas fa-exclamation-circle fa-2x me-3"></i>
        <div class="flex-grow-1">
            <h5 class="alert-heading mb-2">SK Ditolak</h5>
            <p class="mb-0"><strong>Alasan:</strong></p>
            <p class="mb-0">{{ $sk->{'Alasan-Tolak'} ?: 'Tidak ada alasan spesifik' }}</p>
        </div>
    </div>
</div>
@endif

<!-- Data Mahasiswa & Penguji -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h6 class="m-0 fw-bold text-danger">
            <i class="fas fa-users me-2"></i>Daftar Mahasiswa dan Penguji Skripsi
        </h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Mahasiswa</th>
                        <th>Judul Skripsi</th>
                        <th>Dosen Penguji</th>
                    </tr>
                </thead>
                <tbody>
                    @if(is_array($dataPenguji) && count($dataPenguji) > 0)
                        @foreach($dataPenguji as $index => $data)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <strong>{{ $data['nama_mahasiswa'] ?? '-' }}</strong><br>
                                <small class="text-muted">{{ $data['nim'] ?? '-' }}</small>
                            </td>
                            <td>
                                <span class="d-inline-block text-truncate" style="max-width: 300px;" 
                                      title="{{ $data['judul_skripsi'] ?? '-' }}">
                                    {{ $data['judul_skripsi'] ?? '-' }}
                                </span>
                            </td>
                            <td>
                                <ul class="list-unstyled mb-0">
                                    @if(isset($data['nama_penguji_1']))
                                        <li><i class="fas fa-check-circle text-success me-1"></i> {{ $data['nama_penguji_1'] }}</li>
                                    @endif
                                    @if(isset($data['nama_penguji_2']))
                                        <li><i class="fas fa-check-circle text-success me-1"></i> {{ $data['nama_penguji_2'] }}</li>
                                    @endif
                                    @if(isset($data['nama_penguji_3']))
                                        <li><i class="fas fa-check-circle text-success me-1"></i> {{ $data['nama_penguji_3'] }}</li>
                                    @endif
                                    @if(!isset($data['nama_penguji_1']) && !isset($data['nama_penguji_2']) && !isset($data['nama_penguji_3']))
                                        <li class="text-muted">-</li>
                                    @endif
                                </ul>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">
                                Tidak ada data penguji.
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tolak SK -->
<div class="modal fade" id="modalTolakSK" tabindex="-1" aria-labelledby="modalTolakSKLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalTolakSKLabel">
                    <i class="fas fa-times-circle me-2"></i>Tolak SK Penguji Skripsi
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formTolakSK">
                    <div class="mb-3">
                        <label for="reject-alasan" class="form-label fw-semibold">
                            Alasan Penolakan <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" 
                                  id="reject-alasan" 
                                  name="alasan" 
                                  rows="4" 
                                  placeholder="Masukkan alasan penolakan secara detail..."
                                  required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" onclick="submitRejection()">Tolak SK</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function showRejectModal() {
        const modal = new bootstrap.Modal(document.getElementById('modalTolakSK'));
        modal.show();
    }

    function submitRejection() {
        const alasan = $('#reject-alasan').val();
        
        if (!alasan || alasan.length < 10) {
            Swal.fire('Error', 'Alasan penolakan minimal 10 karakter', 'error');
            return;
        }

        Swal.fire({
            title: 'Konfirmasi Penolakan',
            text: 'Apakah Anda yakin ingin menolak pengajuan ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Ya, Tolak!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("admin_fakultas.sk.penguji-skripsi.reject") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        sk_id: '{{ $sk->No }}',
                        alasan: alasan
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Berhasil', response.message, 'success').then(() => {
                                window.location.href = '{{ route("admin_fakultas.sk.penguji-skripsi") }}';
                            });
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    }
                });
            }
        });
    }
</script>
@endpush
