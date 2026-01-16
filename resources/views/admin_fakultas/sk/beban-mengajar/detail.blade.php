@extends('layouts.admin_fakultas')

@section('title', 'Detail SK Beban Mengajar')

@push('styles')
<style>
    .preview-document {
        font-family: 'Times New Roman', Times, serif;
        background: #ffffff;
        color: #000;
        border: 1px solid #dee2e6;
        padding: 2cm 2.5cm;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        font-size: 11pt;
        line-height: 1.5;
        min-height: 500px;
        width: 21cm;
        max-width: 100%;
        margin: 0 auto;
    }
    .preview-table-beban {
        width: 100%;
        border-collapse: collapse;
        margin: 15px 0;
        font-size: 10pt;
        border: 1px solid #000;
    }
    .preview-table-beban th,
    .preview-table-beban td {
        border: 1px solid #000;
        padding: 8px 10px;
        vertical-align: middle;
        line-height: 1.3;
        color: #000;
    }
    .preview-table-beban thead th {
        background-color: #f8f9fa;
        font-weight: bold;
        text-align: center;
    }
    .preview-table-beban tbody td {
        font-size: 10pt;
    }
    .preview-table-beban tbody td:nth-child(1) {
        text-align: center;
        width: 5%;
    }
    .preview-table-beban tbody td:nth-child(4),
    .preview-table-beban tbody td:nth-child(5) {
        text-align: center;
    }
    .info-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 10px;
        color: white;
        padding: 20px;
        margin-bottom: 20px;
    }
    .info-card .label {
        font-size: 0.85rem;
        opacity: 0.8;
    }
    .info-card .value {
        font-size: 1.2rem;
        font-weight: bold;
    }
    .status-badge {
        font-size: 0.9rem;
        padding: 8px 15px;
        border-radius: 20px;
    }
</style>
@endpush

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-0">Detail SK Beban Mengajar</h1>
        <p class="mb-0 text-muted">Informasi lengkap pengajuan SK Beban Mengajar</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin_fakultas.sk.beban-mengajar') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>

<!-- Info Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                        <i class="fas fa-university text-primary fa-lg"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-0 small">Program Studi</p>
                        <h6 class="mb-0 fw-bold">{{ $sk->prodi->Nama_Prodi ?? '-' }}</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-info bg-opacity-10 p-3 me-3">
                        <i class="fas fa-calendar-alt text-info fa-lg"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-0 small">Semester</p>
                        <h6 class="mb-0 fw-bold">{{ $sk->Semester }} {{ $sk->Tahun_Akademik }}</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3">
                        <i class="fas fa-users text-success fa-lg"></i>
                    </div>
                    <div>
                        @php
                            $bebanData = $sk->Data_Beban_Mengajar;
                            if (is_string($bebanData)) {
                                $bebanData = json_decode($bebanData, true);
                            }
                            $jumlahDosen = is_array($bebanData) ? count($bebanData) : 0;
                        @endphp
                        <p class="text-muted mb-0 small">Jumlah Beban</p>
                        <h6 class="mb-0 fw-bold">{{ $jumlahDosen }} Data</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3">
                        <i class="fas fa-info-circle text-warning fa-lg"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-0 small">Status</p>
                        @php
                            $badgeClass = 'secondary';
                            switch($sk->Status) {
                                case 'Dikerjakan admin':
                                    $badgeClass = 'warning';
                                    break;
                                case 'Menunggu-Persetujuan-Wadek-1':
                                    $badgeClass = 'info';
                                    break;
                                case 'Menunggu-Persetujuan-Dekan':
                                    $badgeClass = 'primary';
                                    break;
                                case 'Selesai':
                                    $badgeClass = 'success';
                                    break;
                                case 'Ditolak-Admin':
                                case 'Ditolak-Wadek1':
                                case 'Ditolak-Dekan':
                                case 'Ditolak':
                                    $badgeClass = 'danger';
                                    break;
                            }
                        @endphp
                        <span class="badge bg-{{ $badgeClass }}">{{ $sk->Status }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Detail Info -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <h6 class="m-0 fw-bold text-primary">
            <i class="fas fa-info-circle me-2"></i>Informasi Pengajuan
        </h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr>
                        <th width="40%">Nomor Surat</th>
                        <td>: {{ $sk->Nomor_Surat ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal Pengajuan</th>
                        <td>: {{ $sk->{'Tanggal-Pengajuan'} ? $sk->{'Tanggal-Pengajuan'}->format('d F Y H:i') : '-' }}</td>
                    </tr>
                    <tr>
                        <th>Program Studi</th>
                        <td>: {{ $sk->prodi->Nama_Prodi ?? '-' }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr>
                        <th width="40%">Semester</th>
                        <td>: {{ $sk->Semester }}</td>
                    </tr>
                    <tr>
                        <th>Tahun Akademik</th>
                        <td>: {{ $sk->Tahun_Akademik }}</td>
                    </tr>
                    @if($sk->{'Alasan-Tolak'})
                    <tr>
                        <th>Alasan Penolakan</th>
                        <td>: <span class="text-danger">{{ $sk->{'Alasan-Tolak'} }}</span></td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Data Beban Mengajar -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h6 class="m-0 fw-bold text-primary">
            <i class="fas fa-chalkboard-teacher me-2"></i>Data Beban Mengajar
        </h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="preview-table-beban">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Dosen</th>
                        <th>NIP</th>
                        <th>Mata Kuliah</th>
                        <th>Kelas</th>
                        <th>SKS</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $bebanData = $sk->Data_Beban_Mengajar;
                        if (is_string($bebanData)) {
                            $bebanData = json_decode($bebanData, true);
                        }
                        $totalSKS = 0;
                    @endphp
                    @if(is_array($bebanData) && count($bebanData) > 0)
                        @foreach($bebanData as $index => $item)
                            @php
                                $namaDosen = $item['nama_dosen'] ?? $item['Nama_Dosen'] ?? '-';
                                $nip = $item['nip'] ?? $item['NIP'] ?? '-';
                                $mataKuliah = $item['nama_mata_kuliah'] ?? $item['mata_kuliah'] ?? $item['Nama_Matakuliah'] ?? '-';
                                $kelas = $item['kelas'] ?? $item['Kelas'] ?? '-';
                                $sks = $item['sks'] ?? $item['SKS'] ?? 0;
                                $totalSKS += (int)$sks;
                            @endphp
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $namaDosen }}</td>
                                <td>{{ $nip }}</td>
                                <td>{{ $mataKuliah }}</td>
                                <td>{{ $kelas }}</td>
                                <td>{{ $sks }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                Tidak ada data beban mengajar
                            </td>
                        </tr>
                    @endif
                </tbody>
                @if(is_array($bebanData) && count($bebanData) > 0)
                <tfoot>
                    <tr class="table-secondary fw-bold">
                        <td colspan="5" class="text-end">Total SKS:</td>
                        <td class="text-center">{{ $totalSKS }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>

<!-- Action Buttons -->
@if($sk->Status == 'Dikerjakan admin')
<div class="card border-0 shadow-sm mt-4">
    <div class="card-body">
        <div class="d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-danger" onclick="showRejectModal()">
                <i class="fas fa-times me-2"></i>Tolak Pengajuan
            </button>
        </div>
    </div>
</div>
@endif

<!-- Modal Tolak SK -->
<div class="modal fade" id="modalTolakSK" tabindex="-1" aria-labelledby="modalTolakSKLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalTolakSKLabel">
                    <i class="fas fa-times-circle me-2"></i>Tolak SK Beban Mengajar
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
    function showRejectModal() {
        const modal = new bootstrap.Modal(document.getElementById('modalTolakSK'));
        modal.show();
    }

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
        fetch('{{ route("admin_fakultas.sk.beban-mengajar.reject") }}', {
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
                window.location.href = '{{ route("admin_fakultas.sk.beban-mengajar") }}';
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
