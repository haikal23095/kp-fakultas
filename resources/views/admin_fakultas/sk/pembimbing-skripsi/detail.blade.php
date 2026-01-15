@extends('layouts.admin_fakultas')

@section('title', 'Detail SK Pembimbing Skripsi')

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-0">Detail SK Pembimbing Skripsi</h1>
        <p class="mb-0 text-muted">Informasi lengkap pengajuan SK</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin_fakultas.sk.pembimbing-skripsi') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
        @if($sk->Status == 'Dikerjakan admin' || $sk->Status == 'Ditolak-Wadek1' || $sk->Status == 'Ditolak-Dekan')
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
            <div class="card-header bg-warning text-white">
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
                                        $statusText = 'Selesai';
                                        break;
                                    case 'Ditolak-Admin':
                                        $badgeClass = 'danger';
                                        $statusText = 'Ditolak Admin';
                                        break;
                                    case 'Ditolak-Wadek1':
                                        $badgeClass = 'danger';
                                        $statusText = 'Ditolak Wadek 1';
                                        break;
                                    case 'Ditolak-Dekan':
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
                        <td>: {{ $sk->{'Tanggal-Pengajuan'} ? $sk->{'Tanggal-Pengajuan'}->format('d F Y H:i') : '-' }}</td>
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
                    <tr>
                        <th>Email</th>
                        <td>: {{ $sk->kaprodi->user->email ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>No. Telepon</th>
                        <td>: {{ $sk->kaprodi->user->No_WA ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Rejection Info (if rejected) -->
@if(in_array($sk->Status, ['Ditolak-Admin', 'Ditolak-Wadek1', 'Ditolak-Dekan']))
<div class="alert alert-danger border-0 shadow-sm">
    <div class="d-flex align-items-start">
        <i class="fas fa-exclamation-circle fa-2x me-3"></i>
        <div class="flex-grow-1">
            <h5 class="alert-heading mb-2">SK Ditolak</h5>
            @if($sk->approval && $sk->approval->{'Alasan-Tolak'})
            <p class="mb-0"><strong>Alasan:</strong></p>
            <p class="mb-0">{{ $sk->approval->{'Alasan-Tolak'} }}</p>
            @endif
        </div>
    </div>
</div>
@endif

<!-- Data Mahasiswa & Pembimbing -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h6 class="m-0 fw-bold text-warning">
            <i class="fas fa-users me-2"></i>Daftar Mahasiswa dan Pembimbing Skripsi
        </h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>NIM</th>
                        <th>Nama Mahasiswa</th>
                        <th>Judul Skripsi</th>
                        <th>Pembimbing 1</th>
                        <th>Pembimbing 2</th>
                    </tr>
                </thead>
                <tbody>
                    @if(is_array($dataPembimbing) && count($dataPembimbing) > 0)
                        @foreach($dataPembimbing as $index => $data)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $data['nim'] ?? '-' }}</td>
                            <td>{{ $data['nama_mahasiswa'] ?? '-' }}</td>
                            <td>
                                <span class="d-inline-block text-truncate" style="max-width: 300px;" 
                                      title="{{ $data['judul_skripsi'] ?? '-' }}">
                                    {{ $data['judul_skripsi'] ?? '-' }}
                                </span>
                            </td>
                            <td>
                                @if(isset($data['pembimbing_1']) && is_array($data['pembimbing_1']))
                                <div>
                                    <strong>{{ $data['pembimbing_1']['nama_dosen'] ?? '-' }}</strong>
                                    @if(isset($data['pembimbing_1']['nip']))
                                    <br><small class="text-muted">NIP: {{ $data['pembimbing_1']['nip'] }}</small>
                                    @endif
                                </div>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if(isset($data['pembimbing_2']) && is_array($data['pembimbing_2']))
                                <div>
                                    <strong>{{ $data['pembimbing_2']['nama_dosen'] ?? '-' }}</strong>
                                    @if(isset($data['pembimbing_2']['nip']))
                                    <br><small class="text-muted">NIP: {{ $data['pembimbing_2']['nip'] }}</small>
                                    @endif
                                </div>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    @else
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                            Tidak ada data mahasiswa
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    @if(is_array($dataPembimbing) && count($dataPembimbing) > 0)
    <div class="card-footer bg-light">
        <small class="text-muted">
            <i class="fas fa-info-circle me-1"></i>
            Total: <strong>{{ count($dataPembimbing) }}</strong> mahasiswa
        </small>
    </div>
    @endif
</div>

<!-- Modal Tolak SK -->
<div class="modal fade" id="modalTolakSK" tabindex="-1" aria-labelledby="modalTolakSKLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalTolakSKLabel">
                    <i class="fas fa-times-circle me-2"></i>Tolak SK Pembimbing Skripsi
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Anda akan menolak pengajuan SK berikut:
                </div>
                
                <div class="mb-3">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th width="40%">Program Studi</th>
                            <td>: {{ $sk->prodi->Nama_Prodi ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Semester</th>
                            <td>: {{ $sk->Semester }}</td>
                        </tr>
                        <tr>
                            <th>Tahun Akademik</th>
                            <td>: {{ $sk->Tahun_Akademik }}</td>
                        </tr>
                    </table>
                </div>

                <form id="formTolakSK">
                    <input type="hidden" id="reject-sk-id" name="sk_id" value="{{ $sk->No }}">
                    
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
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i>
                            Alasan ini akan dikirimkan sebagai notifikasi ke Kaprodi
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Batal
                </button>
                <button type="button" class="btn btn-danger" onclick="submitRejection()">
                    <i class="fas fa-ban me-1"></i>Tolak SK
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Show reject modal
    function showRejectModal() {
        const modal = new bootstrap.Modal(document.getElementById('modalTolakSK'));
        modal.show();
    }

    // Submit rejection
    function submitRejection() {
        const skId = document.getElementById('reject-sk-id').value;
        const alasan = document.getElementById('reject-alasan').value.trim();
        
        if (!alasan) {
            alert('Alasan penolakan harus diisi');
            return;
        }
        
        if (!confirm('Apakah Anda yakin ingin menolak SK ini? Tindakan ini tidak dapat dibatalkan.')) {
            return;
        }
        
        // Submit to server
        fetch('{{ route("admin_fakultas.sk.pembimbing-skripsi.reject") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                sk_id: skId,
                alasan: alasan
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Close modal and redirect
                const modal = bootstrap.Modal.getInstance(document.getElementById('modalTolakSK'));
                modal.hide();
                window.location.href = '{{ route("admin_fakultas.sk.pembimbing-skripsi") }}';
            } else {
                alert('Gagal: ' + (data.message || 'Terjadi kesalahan'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat mengirim data');
        });
    }
</script>
@endpush

@endsection
