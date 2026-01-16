@extends('layouts.dekan')

@section('title', 'SK Pembimbing Skripsi - Dekan')

@push('styles')
<style>
    .page-header {
        background: #ffffff;
        border-bottom: 2px solid #e9ecef;
        padding: 1.5rem 0;
        margin-bottom: 2rem;
    }

    .card-sk {
        border: 1px solid #e9ecef;
        border-radius: 8px;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
    }

    .card-sk:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }

    .badge-status {
        font-size: 0.85rem;
        padding: 0.5rem 0.75rem;
    }

    .info-box {
        background: #f8f9fa;
        border-left: 4px solid #4e73df;
        padding: 1rem;
        border-radius: 4px;
        margin-bottom: 1rem;
    }

    .preview-document {
        font-family: 'Times New Roman', Times, serif;
        background: #ffffff;
        color: #000;
        border: 1px solid #000;
        padding: 2cm 2.5cm;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        font-size: 11pt;
        line-height: 1.5;
        min-height: 500px;
        width: 21cm;
        max-width: 100%;
        margin: 0 auto;
    }
    
    .preview-header {
        text-align: center;
        margin-bottom: 20px;
        border-bottom: 3px double #000;
        padding-bottom: 10px;
    }
    
    .preview-header img {
        width: 80px;
        float: left;
        margin-top: -5px;
    }
    
    .preview-header strong {
        display: block;
        text-transform: uppercase;
    }
    
    .preview-header .line-1 { font-size: 14pt; font-weight: bold; }
    .preview-header .line-2 { font-size: 16pt; font-weight: bold; }
    .preview-header .line-3 { font-size: 14pt; font-weight: bold; }
    .preview-header .address {
        font-size: 10pt;
        margin-top: 5px;
        font-weight: normal;
    }
    
    .preview-table-mahasiswa {
        width: 100%;
        border-collapse: collapse;
        margin: 15px 0;
        font-size: 10pt;
        border: 1px solid #000;
    }
    
    .preview-table-mahasiswa th,
    .preview-table-mahasiswa td {
        border: 1px solid #000;
        padding: 5px 8px;
        vertical-align: middle;
        line-height: 1.3;
        color: #000;
    }
    
    .preview-table-mahasiswa thead th {
        background-color: #ffffff;
        font-weight: bold;
        text-align: center;
        text-transform: capitalize;
    }
    
    .preview-table-mahasiswa tbody td {
        font-size: 9pt;
        vertical-align: top;
    }
    
    .preview-table-mahasiswa tbody td:nth-child(1) {
        text-align: center;
        vertical-align: top;
    }

    @media (max-width: 768px) {
        .page-header {
            padding: 1rem 0;
        }
        .page-header h3 {
            font-size: 1.1rem;
        }
        .table {
            font-size: 0.85rem;
        }
    }
</style>
@endpush

@section('content')

{{-- Header Halaman --}}
<div class="page-header">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h3 class="mb-1 fw-bold text-dark">SK Pembimbing Skripsi</h3>
            <p class="mb-0 text-muted small">Daftar SK Pembimbing Skripsi yang menunggu tanda tangan Dekan</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-warning" onclick="showHistory()">
                <i class="fas fa-history me-2"></i>History
            </button>
            <a href="{{ route('dekan.persetujuan.sk_dosen') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>
</div>

{{-- Alert Success/Error --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Info Box --}}
<div class="row mb-3">
    <div class="col-md-6">
        <div class="info-box">
            <div class="d-flex align-items-start">
                <i class="fas fa-info-circle text-primary me-3 mt-1"></i>
                <div>
                    <strong>Total SK:</strong> {{ $daftarSK->count() }} SK Pembimbing Skripsi
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="d-flex justify-content-end">
            <select class="form-select" id="filterStatus" onchange="applyFilter()" style="max-width: 250px;">
                <option value="">Semua Status</option>
                <option value="Menunggu-Persetujuan-Dekan">Menunggu Persetujuan</option>
                <option value="Selesai">Selesai</option>
                <option value="Ditolak-Dekan">Ditolak</option>
            </select>
        </div>
    </div>
</div>

{{-- Daftar SK Pembimbing Skripsi --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold">
            <i class="fas fa-list me-2 text-warning"></i>
            Daftar SK Pembimbing Skripsi
        </h5>
    </div>
    <div class="card-body p-0">
        @if($daftarSK->isEmpty())
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <p class="text-muted">Tidak ada SK Pembimbing Skripsi yang menunggu persetujuan</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Semester</th>
                            <th>Tahun Akademik</th>
                            <th>Nomor Surat</th>
                            <th>Jumlah Mahasiswa</th>
                            <th>Tanggal Pengajuan</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($daftarSK as $index => $sk)
                            @php
                                $dataPembimbing = $sk->Data_Pembimbing_Skripsi;
                                if (is_string($dataPembimbing)) {
                                    $dataPembimbing = json_decode($dataPembimbing, true);
                                }
                                $jumlahMahasiswa = is_array($dataPembimbing) ? count($dataPembimbing) : 0;
                            @endphp
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <span class="badge bg-{{ $sk->Semester == 'Ganjil' ? 'primary' : 'info' }}">
                                        {{ $sk->Semester }}
                                    </span>
                                </td>
                                <td>{{ $sk->Tahun_Akademik }}</td>
                                <td>{{ $sk->Nomor_Surat ?? '-' }}</td>
                                <td class="text-center">
                                    <span class="badge bg-secondary">{{ $jumlahMahasiswa }} Mahasiswa</span>
                                </td>
                                <td>{{ $sk->Tanggal_Pengajuan ? $sk->Tanggal_Pengajuan->format('d M Y') : '-' }}</td>
                                <td>
                                    @php
                                        $badgeClass = 'secondary';
                                        $statusText = $sk->Status;
                                        
                                        switch($sk->Status) {
                                            case 'Menunggu-Persetujuan-Dekan':
                                                $badgeClass = 'warning text-dark';
                                                $statusText = 'Menunggu Persetujuan';
                                                break;
                                            case 'Selesai':
                                                $badgeClass = 'success';
                                                $statusText = 'Selesai';
                                                break;
                                            case 'Ditolak-Dekan':
                                                $badgeClass = 'danger';
                                                $statusText = 'Ditolak';
                                                break;
                                        }
                                    @endphp
                                    <span class="badge bg-{{ $badgeClass }}">{{ $statusText }}</span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button class="btn btn-primary" onclick="showDetail({{ $sk->No }})" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @if($sk->Status === 'Menunggu-Persetujuan-Dekan')
                                            <button class="btn btn-success" onclick="approveSK({{ $sk->No }})" title="Setujui & TTD">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button class="btn btn-danger" onclick="rejectSK({{ $sk->No }})" title="Tolak SK">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @else
                                            <button class="btn btn-secondary" disabled title="Sudah Diproses">
                                                <i class="fas fa-check-double"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

{{-- Info Tambahan --}}
<div class="row mt-4">
    <div class="col-12">
        <div class="alert alert-info border-0" role="alert">
            <div class="d-flex align-items-start">
                <i class="fas fa-info-circle fa-2x me-3 mt-1"></i>
                <div>
                    <h6 class="alert-heading fw-bold mb-2">Informasi</h6>
                    <ul class="mb-0 small">
                        <li>SK Pembimbing Skripsi yang muncul di sini adalah yang sudah disetujui oleh Wadek 1.</li>
                        <li>Anda dapat melihat detail SK dan memberikan tanda tangan elektronik.</li>
                        <li>Setelah ditandatangani, SK akan dikirimkan ke Admin Fakultas dan Kaprodi terkait.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail SK -->
<div class="modal fade" id="modalDetail" tabindex="-1" aria-labelledby="modalDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="modalDetailLabel">
                    <i class="fas fa-file-alt me-2"></i>Preview SK Pembimbing Skripsi
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div style="max-height: 750px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 4px;">
                    <div class="preview-document" id="previewContent">
                        <div class="text-center">
                            <div class="spinner-border text-warning" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Tutup
                </button>
                <button type="button" class="btn btn-success" onclick="approveFromModal()" id="btnApprove">
                    <i class="fas fa-check me-1"></i>Setujui & TTD
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal History -->
<div class="modal fade" id="modalHistory" tabindex="-1" aria-labelledby="modalHistoryLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalHistoryLabel">
                    <i class="fas fa-history me-2"></i>History SK Pembimbing Skripsi
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="historyContent">
                    <div class="text-center py-4">
                        <div class="spinner-border text-success" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Memuat data history...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Rejection -->
<div class="modal fade" id="modalReject" tabindex="-1" aria-labelledby="modalRejectLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalRejectLabel">
                    <i class="fas fa-times-circle me-2"></i>Tolak SK Pembimbing Skripsi
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Perhatian!</strong> SK yang ditolak akan dikembalikan untuk diperbaiki.
                </div>
                
                <!-- Pilih Target Penolakan -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Kembalikan SK ke: <span class="text-danger">*</span></label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="rejectTarget" id="targetAdmin" value="admin" checked>
                        <label class="form-check-label" for="targetAdmin">
                            <strong>Admin Fakultas</strong><br>
                            <small class="text-muted">Untuk revisi teknis (format, penomoran, dll)</small>
                        </label>
                    </div>
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="radio" name="rejectTarget" id="targetKaprodi" value="kaprodi">
                        <label class="form-check-label" for="targetKaprodi">
                            <strong>Kaprodi</strong><br>
                            <small class="text-muted">Untuk revisi substantif (data mahasiswa, dosen, dll)</small>
                        </label>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="alasanPenolakan" class="form-label fw-bold">Alasan Penolakan <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="alasanPenolakan" rows="4" placeholder="Tuliskan alasan penolakan (minimal 10 karakter)" required></textarea>
                    <small class="text-muted">Minimal 10 karakter</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Batal
                </button>
                <button type="button" class="btn btn-danger" onclick="submitRejection()">
                    <i class="fas fa-paper-plane me-1"></i>Kirim Penolakan
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let currentSKId = null;
    let currentDekanName = '';
    let currentDekanNip = '';
    let currentQRCode = '';
    let isHistoryMode = false;

    function showHistory() {
        const modal = new bootstrap.Modal(document.getElementById('modalHistory'));
        modal.show();

        // Fetch history data
        fetch('{{ url('/dekan/sk-pembimbing-skripsi/history') }}', {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderHistory(data.history);
            } else {
                document.getElementById('historyContent').innerHTML = `
                    <div class="alert alert-warning">
                        <i class="fas fa-info-circle me-2"></i>${data.message || 'Tidak ada history'}
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('historyContent').innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>Terjadi kesalahan saat memuat history
                </div>
            `;
        });
    }

    function renderHistory(historyList) {
        if (historyList.length === 0) {
            document.getElementById('historyContent').innerHTML = `
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Belum ada SK Pembimbing Skripsi yang ditandatangani</p>
                </div>
            `;
            return;
        }

        let html = `
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Semester/TA</th>
                            <th>Nomor Surat</th>
                            <th>Jumlah Mahasiswa</th>
                            <th>Tanggal TTD</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

        historyList.forEach((sk, index) => {
            let dataPembimbing = sk.Data_Pembimbing_Skripsi || [];
            
            // Handle JSON string or object
            if (typeof dataPembimbing === 'string') {
                try {
                    dataPembimbing = JSON.parse(dataPembimbing);
                } catch (e) {
                    dataPembimbing = [];
                }
            }
            
            const jumlahMahasiswa = Array.isArray(dataPembimbing) ? dataPembimbing.length : 
                                   (dataPembimbing && typeof dataPembimbing === 'object' ? Object.keys(dataPembimbing).length : 0);
            
            const tanggalTTD = sk.Tanggal_Persetujuan_Dekan || '-';
            
            const statusBadge = sk.Status === 'Selesai' 
                ? '<span class="badge bg-success">Selesai</span>' 
                : '<span class="badge bg-danger">Ditolak Dekan</span>';

            html += `
                <tr>
                    <td>${index + 1}</td>
                    <td>${sk.Semester || '-'} / ${sk.Tahun_Akademik || '-'}</td>
                    <td>${sk.Nomor_Surat || '-'}</td>
                    <td><span class="badge bg-primary">${jumlahMahasiswa} Mahasiswa</span></td>
                    <td>${tanggalTTD}</td>
                    <td>${statusBadge}</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-primary" onclick="showHistoryDetail(${sk.No})">
                            <i class="fas fa-eye"></i> Lihat
                        </button>
                    </td>
                </tr>
            `;
        });

        html += `
                    </tbody>
                </table>
            </div>
        `;

        document.getElementById('historyContent').innerHTML = html;
    }

    function showHistoryDetail(skId) {
        isHistoryMode = true;
        currentSKId = skId;
        
        // Close history modal
        bootstrap.Modal.getInstance(document.getElementById('modalHistory')).hide();
        
        // Show detail modal
        const modal = new bootstrap.Modal(document.getElementById('modalDetail'));
        modal.show();

        // Hide approve button for history items
        document.getElementById('btnApprove').style.display = 'none';

        // Fetch detail
        fetch(`{{ url('/dekan/sk-pembimbing-skripsi') }}/${skId}`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                currentDekanName = data.dekanName;
                currentDekanNip = data.dekanNip;
                // Render with QR code from database
                const qrCodeUrl = data.sk.QR_Code ? `{{ asset('storage') }}/${data.sk.QR_Code}` : null;
                renderPreview(data.sk, data.dekanName, data.dekanNip, qrCodeUrl);
            } else {
                alert('Gagal memuat detail SK: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat memuat detail: ' + error.message);
        });
    }

    function showDetail(skId) {
        isHistoryMode = false;
        currentSKId = skId;
        
        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('modalDetail'));
        modal.show();

        // Show approve button for pending items
        document.getElementById('btnApprove').style.display = 'inline-block';

        // Fetch detail
        fetch(`{{ url('/dekan/sk-pembimbing-skripsi') }}/${skId}`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            console.log('Response data:', data);
            if (data.success) {
                currentDekanName = data.dekanName;
                currentDekanNip = data.dekanNip;
                
                // Check if SK already has QR Code (Status Selesai)
                const qrCodeUrl = data.sk.QR_Code ? `{{ asset('storage') }}/${data.sk.QR_Code}` : null;
                
                // Debug QR Code path
                console.log('SK Status:', data.sk.Status);
                console.log('QR_Code from database:', data.sk.QR_Code);
                console.log('Full QR Code URL:', qrCodeUrl);
                
                // Hide approve button if SK already completed
                if (data.sk.Status === 'Selesai') {
                    document.getElementById('btnApprove').style.display = 'none';
                }
                
                renderPreview(data.sk, data.dekanName, data.dekanNip, qrCodeUrl);
            } else {
                console.error('Error details:', data);
                alert('Gagal memuat detail SK: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat memuat detail: ' + error.message);
        });
    }

    function renderPreview(sk, dekanNameParam, dekanNipParam, qrCode) {
        const finalDekanName = dekanNameParam || currentDekanName || '-';
        const finalDekanNip = dekanNipParam || currentDekanNip || '-';
        
        let dataPembimbing = sk.Data_Pembimbing_Skripsi || [];
        
        // Jika string, parse JSON
        if (typeof dataPembimbing === 'string') {
            try {
                dataPembimbing = JSON.parse(dataPembimbing);
            } catch (e) {
                console.error('Error parsing Data_Pembimbing_Skripsi:', e);
                dataPembimbing = [];
            }
        }
        
        // Jika object bukan array, convert ke array
        if (dataPembimbing && typeof dataPembimbing === 'object' && !Array.isArray(dataPembimbing)) {
            dataPembimbing = Object.values(dataPembimbing);
        }
        
        // Pastikan dataPembimbing adalah array
        if (!Array.isArray(dataPembimbing)) {
            console.error('Data_Pembimbing_Skripsi bukan array:', dataPembimbing);
            dataPembimbing = [];
        }
        
        const semesterUpper = (sk.Semester || 'GANJIL').toUpperCase();
        const semesterText = sk.Semester || 'Ganjil';
        const tahunAkademik = sk.Tahun_Akademik || '2023/2024';
        const nomorSurat = sk.Nomor_Surat || '-';

        // Kelompokkan mahasiswa per jurusan
        const groupedByJurusan = {};
        dataPembimbing.forEach(mhs => {
            let jurusanName = '-';
            if (mhs.prodi_data && mhs.prodi_data.jurusan && mhs.prodi_data.jurusan.Nama_Jurusan) {
                jurusanName = mhs.prodi_data.jurusan.Nama_Jurusan;
            } else if (mhs.prodi && mhs.prodi !== '-') {
                jurusanName = mhs.prodi;
            } else if (mhs.prodi_data && mhs.prodi_data.nama_prodi) {
                jurusanName = mhs.prodi_data.nama_prodi;
            }
            
            const prodiName = (mhs.prodi_data && mhs.prodi_data.nama_prodi) ? mhs.prodi_data.nama_prodi : (mhs.prodi || '-');
            
            if (!groupedByJurusan[jurusanName]) {
                groupedByJurusan[jurusanName] = { jurusan: jurusanName, prodi: [] };
            }
            if (!groupedByJurusan[jurusanName].prodi.includes(prodiName)) {
                groupedByJurusan[jurusanName].prodi.push(prodiName);
            }
            if (!groupedByJurusan[jurusanName].mahasiswa) {
                groupedByJurusan[jurusanName].mahasiswa = [];
            }
            groupedByJurusan[jurusanName].mahasiswa.push(mhs);
        });

        // Debug QR Code
        if (qrCode) {
            console.log('QR Code URL:', qrCode);
        }
        
        // Buat HTML untuk tanda tangan dengan atau tanpa QR code
        const ttdHtml = qrCode 
            ? `<p style="margin: 0 0 3px 0;">Ditetapkan di Bangkalan</p>
               <p style="margin: 0 0 10px 0;">pada tanggal ${new Date().toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'})}</p>
               <p style="margin: 0 0 10px 0;"><strong>DEKAN,</strong></p>
               <div style="text-align: right; margin: 10px 0;">
                   <img src="${qrCode}" alt="QR Code" style="width: 100px; height: 100px; border: 1px solid #000;" 
                        onerror="console.error('Failed to load QR Code:', '${qrCode}'); this.style.border='2px solid red'; this.alt='QR Code gagal dimuat: ${qrCode}';">
               </div>
               <p style="margin: 10px 0 0 0;">
                   <strong><u>${finalDekanName}</u></strong><br>
                   NIP. ${finalDekanNip}
               </p>`
            : `<p style="margin: 0 0 3px 0;">Ditetapkan di Bangkalan</p>
               <p style="margin: 0 0 30px 0;">pada tanggal ${new Date().toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'})}</p>
               <p style="margin: 0 0 70px 0;"><strong>DEKAN,</strong></p>
               <p style="margin: 0 0 0 0;">
                   <strong><u>${finalDekanName}</u></strong><br>
                   NIP. ${finalDekanNip}
               </p>`;

        let lampiranHtml = '';
        Object.keys(groupedByJurusan).forEach((jurusanName, index) => {
            const jurusanData = groupedByJurusan[jurusanName];
            const mahasiswaProdi = jurusanData.mahasiswa;
            const prodiName = jurusanData.prodi[0] || jurusanName;
            lampiranHtml += `
                <div class="lampiran-prodi" style="margin-top: ${index === 0 ? '30px' : '60px'}; page-break-before: ${index === 0 ? 'auto' : 'always'};">
                    <div style="font-size: 11pt; text-align: left; margin-bottom: 10px;">
                        <p style="margin: 0 0 3px 0; font-weight: normal; font-size: 9pt;">SALINAN</p>
                        <p style="margin: 0 0 3px 0; font-weight: normal; font-size: 9pt;">LAMPIRAN KEPUTUSAN DEKAN FAKULTAS TEKNIK UNIVERSITAS TRUNODJOYO</p>
                        <p style="margin: 0 0 3px 0; font-weight: normal; font-size: 9pt;">NOMOR ${nomorSurat}</p>
                        <p style="margin: 0 0 10px 0; font-weight: normal; font-size: 9pt;">TENTANG</p>
                        <p style="margin: 0 0 10px 0; font-weight: normal; font-size: 9pt;">PENETAPAN DOSEN PEMBIMBING SKRIPSI PROGRAM STUDI ${prodiName.toUpperCase()} FAKULTAS TEKNIK UNIVERSITAS TRUNODJOYO SEMESTER ${semesterUpper} TAHUN AKADEMIK ${tahunAkademik}</p>
                        <p style="margin: 0 0 10px 0; text-align: center; font-weight: bold;">DAFTAR MAHASISWA DAN DOSEN PEMBIMBING SKRIPSI</p>
                        <p style="margin: 0 0 10px 0; text-align: center; font-weight: bold;">PROGRAM STUDI ${prodiName.toUpperCase()} FAKULTAS TEKNIK</p>
                        <p style="margin: 0 0 15px 0; text-align: center; font-weight: bold;">UNIVERSITAS TRUNODJOYO</p>
                        <p style="margin: 0 0 15px 0; text-align: center; font-weight: bold;">SEMESTER ${semesterUpper} TAHUN AKADEMIK ${tahunAkademik}</p>
                    </div>
                    <table class="preview-table-mahasiswa">
                        <colgroup>
                            <col style="width: 4%;">
                            <col style="width: 8%;">
                            <col style="width: 15%;">
                            <col style="width: 23%;">
                            <col style="width: 25%;">
                            <col style="width: 25%;">
                        </colgroup>
                        <thead>
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
                            ${mahasiswaProdi.map((mhs, idx) => `
                                <tr>
                                    <td style="text-align: center;">${idx + 1}</td>
                                    <td style="text-align: center;">${mhs.nim || '-'}</td>
                                    <td>${mhs.nama_mahasiswa || '-'}</td>
                                    <td style="font-size: 9pt;">${mhs.judul_skripsi || '-'}</td>
                                    <td style="font-size: 9pt;">
                                        ${mhs.pembimbing_1 ? mhs.pembimbing_1.nama_dosen : '-'}<br>
                                        <small>NIP: ${mhs.pembimbing_1 ? mhs.pembimbing_1.nip : '-'}</small>
                                    </td>
                                    <td style="font-size: 9pt;">
                                        ${mhs.pembimbing_2 ? mhs.pembimbing_2.nama_dosen : '-'}<br>
                                        <small>NIP: ${mhs.pembimbing_2 ? mhs.pembimbing_2.nip : '-'}</small>
                                    </td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                    <div style="margin-top: 50px; font-size: 10pt;">
                        <div style="text-align: right;">
                            ${ttdHtml}
                        </div>
                    </div>
                </div>
            `;
        });

        const html = `
            <div class="preview-header">
                <img src="{{ asset('images/logo_unijoyo.png') }}" alt="Logo UTM">
                <strong class="line-1">KEMENTERIAN PENDIDIKAN, KEBUDAYAAN,</strong>
                <strong class="line-1">RISET DAN TEKNOLOGI</strong>
                <strong class="line-2">UNIVERSITAS TRUNODJOYO</strong>
                <strong class="line-3">FAKULTAS TEKNIK</strong>
                <div class="address">
                    Jl. Raya Telang PO BOX 2 Kamal, Bangkalan - Madura<br>
                    Telp: (031) 3011146, Fax. (031) 3011506<br>
                    Laman: www.trunojoyo.ac.id
                </div>
                <div style="clear: both;"></div>
            </div>

            <div style="text-align: center; margin: 20px 0; font-weight: bold; font-size: 14pt; text-decoration: underline;">
                KEPUTUSAN DEKAN FAKULTAS TEKNIK<br>
                UNIVERSITAS TRUNODJOYO
            </div>

            <div style="text-align: center; margin: 15px 0; font-size: 12pt;">
                NOMOR: ${nomorSurat}
            </div>

            <div style="text-align: center; margin: 15px 0; font-weight: bold; font-size: 11pt;">
                TENTANG
            </div>

            <div style="text-align: center; margin: 15px 0; font-weight: bold; font-size: 11pt;">
                PENETAPAN DOSEN PEMBIMBING SKRIPSI<br>
                FAKULTAS TEKNIK UNIVERSITAS TRUNODJOYO<br>
                SEMESTER ${semesterUpper} TAHUN AKADEMIK ${tahunAkademik}
            </div>

            <div style="margin: 20px 0; font-weight: bold; font-size: 11pt;">
                DEKAN FAKULTAS TEKNIK UNIVERSITAS TRUNODJOYO,
            </div>

            <div style="text-align: justify; margin-bottom: 20px; font-size: 10pt;">
                <table style="width: 100%; margin-bottom: 15px;">
                    <tr>
                        <td style="width: 120px; vertical-align: top;"><strong>Menimbang</strong></td>
                        <td style="width: 20px; vertical-align: top;">:</td>
                        <td style="vertical-align: top;">
                            <table style="width: 100%; border: none;">
                                <tr>
                                    <td style="width: 30px; vertical-align: top; border: none;">a.</td>
                                    <td style="border: none;">Bahwa untuk memperlancar penyusunan Skripsi mahasiswa, perlu menugaskan dosen sebagai pembimbing Skripsi;</td>
                                </tr>
                                <tr>
                                    <td style="vertical-align: top; border: none; padding-top: 5px;">b.</td>
                                    <td style="border: none; padding-top: 5px;">Bahwa untuk melaksanakan butir a di atas, perlu ditetapkan dalam Keputusan Dekan Fakultas Teknik;</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

                <table style="width: 100%; margin-bottom: 15px;">
                    <tr>
                        <td style="width: 120px; vertical-align: top;"><strong>Mengingat</strong></td>
                        <td style="width: 20px; vertical-align: top;">:</td>
                        <td style="vertical-align: top;">
                            <ol style="margin: 0; padding-left: 20px;">
                                <li style="margin-bottom: 5px;">Undang-Undang Nomor 20 tahun 2003, tentang Sistem Pendidikan Nasional;</li>
                                <li style="margin-bottom: 5px;">Peraturan Pemerintah Nomor 4 Tahun 2012 Tentang Penyelenggaraan Pendidikan Tinggi;</li>
                                <li style="margin-bottom: 5px;">Peraturan Presiden RI Nomor 4 Tahun 2014 Tentang Perubahan Penyelenggaraan dan Pengelolaan Perguruan Tinggi;</li>
                                <li style="margin-bottom: 5px;">Keputusan RI Nomor 85 tahun 2001, tentang Statuta Universitas Trunodjoyo;</li>
                                <li style="margin-bottom: 5px;">Keputusan Menteri Pendidikan dan Kebudayaan RI Nomor 232/ U/ 2000, tentang pedoman Penyusunan Kurikulum Pendidikan Tinggi dan Penilaian Hasil Belajar Mahasiswa;</li>
                                <li style="margin-bottom: 5px;">Peraturan Menteri Pendidikan, Kebudayaan, Riset, dan Teknologi RI Nomor 79/M/MPK.A/ KP.09.02/ 2022 tentang pengangkatan Rektor UTM periode 2022-2026;</li>
                                <li>Keputusan Rektor Universitas Trunodjoyo Nomor 1357/UNM3/KP/ 2023 tentang Pengangkatan Pejabat Struktural Dekan Fakultas Teknik;</li>
                            </ol>
                        </td>
                    </tr>
                </table>

                <p><strong>Memperhatikan:</strong> ${Object.keys(groupedByJurusan).map(jurusanName => `Surat dari Ketua Jurusan ${jurusanName} tentang permohonan SK Dosen Pembimbing Skripsi`).join('; ')};</p>

                <div style="text-align: center; margin: 30px 0 20px 0; font-weight: bold;">
                    MEMUTUSKAN
                </div>

                <table style="width: 100%; margin-bottom: 15px;">
                    <tr>
                        <td style="width: 15%; vertical-align: top; font-weight: bold;">Menetapkan</td>
                        <td style="width: 3%; vertical-align: top;">:</td>
                        <td>PENETAPAN DOSEN PEMBIMBING SKRIPSI FAKULTAS TEKNIK UNIVERSITAS TRUNODJOYO SEMESTER ${semesterUpper} TAHUN AKADEMIK ${tahunAkademik}.</td>
                    </tr>
                </table>

                <table style="width: 100%; margin-bottom: 10px;">
                    <tr>
                        <td style="width: 15%; vertical-align: top; font-weight: bold;">Kesatu</td>
                        <td style="width: 3%; vertical-align: top;">:</td>
                        <td>Dosen Pembimbing Skripsi sebagaimana tercantum dalam lampiran Keputusan ini;</td>
                    </tr>
                </table>

                <table style="width: 100%;">
                    <tr>
                        <td style="width: 15%; vertical-align: top; font-weight: bold;">Kedua</td>
                        <td style="width: 3%; vertical-align: top;">:</td>
                        <td>Keputusan ini berlaku sejak tanggal ditetapkan.</td>
                    </tr>
                </table>
            </div>

            <div style="font-size: 10pt; margin: 40px 0 30px 0; text-align: right;">
                ${ttdHtml}
            </div>

            ${lampiranHtml}
        `;

        document.getElementById('previewContent').innerHTML = html;
    }

    function approveSK(skId, fromModal = false) {
        if (!confirm('Apakah Anda yakin ingin menyetujui dan menandatangani SK Pembimbing Skripsi ini?')) {
            return;
        }

        currentSKId = skId;
        
        fetch(`{{ url('/dekan/sk-pembimbing-skripsi') }}/${skId}/approve`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                currentQRCode = data.qr_code;
                
                if (fromModal) {
                    // Jika dari modal, render ulang preview dengan QR code
                    fetch(`{{ url('/dekan/sk-pembimbing-skripsi') }}/${skId}`)
                    .then(response => response.json())
                    .then(detailData => {
                        if (detailData.success) {
                            const qrCodeUrl = `{{ asset('storage') }}/${currentQRCode}`;
                            renderPreview(detailData.sk, detailData.dekanName, detailData.dekanNip, qrCodeUrl);
                            alert(data.message);
                            document.getElementById('btnApprove').style.display = 'none';
                        }
                    })
                    .catch(error => {
                        console.error('Error reloading detail:', error);
                        window.location.reload();
                    });
                } else {
                    alert(data.message);
                    window.location.reload();
                }
            } else {
                alert('Gagal: ' + (data.message || 'Terjadi kesalahan'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menyetujui SK: ' + error.message);
        });
    }

    function approveFromModal() {
        if (!currentSKId) {
            alert('ID SK tidak ditemukan');
            return;
        }
        approveSK(currentSKId, true);
    }

    // Function untuk reject SK
    let currentRejectSKId = null;

    function rejectSK(skId) {
        currentRejectSKId = skId;
        document.getElementById('alasanPenolakan').value = '';
        document.getElementById('targetAdmin').checked = true; // Default ke admin
        
        const modal = new bootstrap.Modal(document.getElementById('modalReject'));
        modal.show();
    }

    function submitRejection() {
        const alasan = document.getElementById('alasanPenolakan').value.trim();
        const targetAdmin = document.getElementById('targetAdmin').checked;
        const targetKaprodi = document.getElementById('targetKaprodi').checked;
        
        // Validasi
        if (alasan.length < 10) {
            alert('Alasan penolakan harus minimal 10 karakter');
            return;
        }
        
        if (!targetAdmin && !targetKaprodi) {
            alert('Silakan pilih target penolakan');
            return;
        }
        
        const target = targetAdmin ? 'admin' : 'kaprodi';
        
        // Konfirmasi
        const targetText = target === 'admin' ? 'Admin Fakultas' : 'Kaprodi';
        if (!confirm(`Yakin ingin menolak SK ini dan mengembalikan ke ${targetText}?`)) {
            return;
        }
        
        // Kirim request
        fetch(`{{ url('/dekan/sk-pembimbing-skripsi') }}/${currentRejectSKId}/reject`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                alasan: alasan,
                target: target
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                window.location.reload();
            } else {
                alert('Gagal: ' + (data.message || 'Terjadi kesalahan'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menolak SK: ' + error.message);
        });
    }

    // Function untuk apply filter
    function applyFilter() {
        const status = document.getElementById('filterStatus').value;
        const url = new URL(window.location.href);
        
        if (status) {
            url.searchParams.set('status', status);
        } else {
            url.searchParams.delete('status');
        }
        
        window.location.href = url.toString();
    }

    // Set filter value dari URL saat page load
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const status = urlParams.get('status');
        if (status) {
            document.getElementById('filterStatus').value = status;
        }
    });

    // Add hover effect
    document.querySelectorAll('.card-sk').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.borderColor = '#4e73df';
        });
        card.addEventListener('mouseleave', function() {
            this.style.borderColor = '#e9ecef';
        });
    });

</script>
@endpush
