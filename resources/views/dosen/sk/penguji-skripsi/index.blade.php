@extends('layouts.dosen')

@section('title', 'SK Penguji Skripsi')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-0">SK Penguji Skripsi</h1>
        <p class="mb-0 text-muted">Daftar SK penguji skripsi yang melibatkan Anda</p>
    </div>
    <a href="{{ route('dosen.sk.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Kembali
    </a>
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
            <div class="card-body p-4">
                @if($filteredSK->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="5%" class="text-center">No</th>
                                <th width="15%">Program Studi</th>
                                <th width="15%">Nomor SK</th>
                                <th width="10%">Semester</th>
                                <th width="12%">Tahun Akademik</th>
                                <th width="10%" class="text-center">Jumlah Mahasiswa</th>
                                <th width="15%">Tanggal SK</th>
                                <th width="10%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($filteredSK as $index => $sk)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>{{ $sk->prodi->Nama_Prodi ?? '-' }}</td>
                                <td>
                                    @if($sk->accSKPengujiSkripsi && $sk->accSKPengujiSkripsi->Nomor_Surat)
                                        <strong class="text-primary">{{ $sk->accSKPengujiSkripsi->Nomor_Surat }}</strong>
                                    @else
                                        <span class="text-muted">Belum ada nomor</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $sk->Semester == 'Ganjil' ? 'info' : 'warning' }}">
                                        {{ $sk->Semester }}
                                    </span>
                                </td>
                                <td>{{ $sk->Tahun_Akademik }}</td>
                                <td class="text-center">
                                    <span class="badge bg-primary">{{ $sk->myMahasiswa ?? 0 }} dari {{ $sk->totalMahasiswa ?? 0 }}</span>
                                </td>
                                <td>
                                    <i class="fas fa-calendar me-1 text-muted"></i>
                                    {{ $sk->accSKPengujiSkripsi ? \Carbon\Carbon::parse($sk->accSKPengujiSkripsi->Tanggal_Persetujuan_Dekan)->format('d M Y') : '-' }}
                                </td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-primary" 
                                                onclick="lihatDetail({{ $sk->No }})"
                                                title="Lihat Detail Preview">
                                            <i class="fas fa-eye me-1"></i> Detail
                                        </button>
                                        <a href="{{ route('dosen.sk.penguji-skripsi.download', $sk->No) }}" 
                                           class="btn btn-sm btn-outline-primary" 
                                           target="_blank" 
                                           title="Download PDF">
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
                    <h5 class="text-muted">Belum Ada SK Penguji Skripsi</h5>
                    <p class="text-muted">Anda belum terdaftar dalam SK penguji skripsi yang telah disetujui</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Informasi Dosen -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm bg-light">
            <div class="card-body">
                <div class="d-flex align-items-start">
                    <div class="me-3">
                        <i class="fas fa-user-circle fa-2x text-primary"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-2">Informasi Dosen</h6>
                        <p class="mb-0 small text-muted">
                            <strong>Nama:</strong> {{ $dosen->Nama_Dosen ?? '-' }}<br>
                            <strong>NIP:</strong> {{ $dosen->NIP ?? '-' }}<br>
                            <strong>Prodi:</strong> {{ $dosen->prodi->Nama_Prodi ?? '-' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .document-page {
        background-color: white;
        width: 100%;
        max-width: 800px;
        min-height: 1122px;
        padding: 2cm 2.5cm;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        font-family: 'Times New Roman', Times, serif;
        color: black;
        line-height: 1.5;
        font-size: 11pt;
    }

    .doc-header {
        text-align: center;
        border-bottom: 3px double #000;
        padding-bottom: 10px;
        margin-bottom: 20px;
        position: relative;
    }

    .doc-header img {
        position: absolute;
        left: 0;
        top: 0;
        width: 80px;
    }

    .doc-header .h-title {
        text-transform: uppercase;
        font-weight: bold;
        margin: 0;
        line-height: 1.2;
    }

    .doc-address {
        font-size: 10pt;
        margin-top: 5px;
    }

    .doc-title {
        text-align: center;
        font-weight: bold;
        margin: 20px 0;
        text-decoration: none;
    }

    .doc-content {
        text-align: justify;
        font-size: 11pt;
    }

    .doc-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
    }

    .doc-table-border {
        border: 1px solid black;
    }

    .doc-table-border th, 
    .doc-table-border td {
        border: 1px solid black;
        padding: 5px 8px;
    }

    .signature-container {
        margin-top: 40px;
        float: right;
        width: 300px;
        text-align: left;
    }

    .qr-code {
        width: 100px;
        height: 100px;
        border: 1px solid #000;
        margin: 10px 0;
    }

    .lampiran-page {
        margin-top: 40px;
        border-top: 2px dashed #ccc;
        padding-top: 40px;
    }
</style>
@endpush

@push('scripts')
<div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-file-alt me-2"></i>Preview SK Penguji Skripsi</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0" style="background-color: #525659;">
                <div class="d-flex justify-content-center align-items-center p-3" style="background-color: #323639;">
                    <a href="#" id="btnDownload" class="btn btn-light me-2" target="_blank">
                        <i class="fas fa-download me-2"></i>Download PDF
                    </a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Tutup
                    </button>
                </div>
                <div class="d-flex justify-content-center p-4">
                    <div id="previewContent" style="background: white; box-shadow: 0 0 20px rgba(0,0,0,0.3);">
                        <div class="text-center p-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2 text-muted">Memuat dokumen...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function lihatDetail(id) {
        const modal = new bootstrap.Modal(document.getElementById('modalDetail'));
        const container = document.getElementById('previewContent');
        const downloadBtn = document.getElementById('btnDownload');
        
        container.innerHTML = `
            <div class="text-center p-5 font-sans" style="width: 21cm; min-height: 29.7cm; background: white;">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 text-muted">Memuat dokumen...</p>
            </div>
        `;
        
        modal.show();

        fetch(`/dosen/sk/penguji-skripsi/${id}/detail`)
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    const sk = data.sk;
                    const dekanName = data.dekanName;
                    const dekanNip = data.dekanNip;
                    const mhsList = sk.Data_Penguji_Skripsi || [];
                    
                    // Set download link
                    downloadBtn.href = `/dosen/sk/penguji-skripsi/${id}/download`;

                    let html = `
                        <div class="document-page">
                            <div class="doc-header">
                                <img src="{{ asset('images/logo_unijoyo.png') }}" alt="Logo">
                                <div class="h-title" style="font-size: 11pt;">KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, RISET, DAN TEKNOLOGI</div>
                                <div class="h-title" style="font-size: 14pt;">UNIVERSITAS TRUNODJOYO MADURA</div>
                                <div class="h-title" style="font-size: 12pt;">FAKULTAS TEKNIK</div>
                                <div class="doc-address">
                                    Jl. Raya Telang, PO. Box. 2 Kamal, Bangkalan – Madura<br>
                                    Telp : (031) 3011146, Fax. (031) 3011506<br>
                                    Laman : www.trunodjoyo.ac.id
                                </div>
                            </div>

                            <div class="doc-title">
                                KEPUTUSAN DEKAN FAKULTAS TEKNIK<br>
                                UNIVERSITAS TRUNODJOYO MADURA<br>
                                NOMOR : ${sk.Nomor_Surat || '-'}
                            </div>

                            <div class="text-center fw-bold mb-3">TENTANG</div>

                            <div class="doc-title">
                                PENETAPAN DOSEN PENGUJI SKRIPSI<br>
                                PROGRAM STUDI S1 ${sk.prodi ? sk.prodi.Nama_Prodi.toUpperCase() : 'FAKULTAS TEKNIK'}<br>
                                FAKULTAS TEKNIK UNIVERSITAS TRUNODJOYO MADURA<br>
                                SEMESTER ${(sk.Semester || '').toUpperCase()} TAHUN AKADEMIK ${sk.Tahun_Akademik || ''}
                            </div>

                            <div class="fw-bold mb-3">DEKAN FAKULTAS TEKNIK UNIVERSITAS TRUNODJOYO MADURA,</div>

                            <div class="doc-content">
                                <table class="w-100">
                                    <tr>
                                        <td style="width: 100px; vertical-align: top;">Menimbang</td>
                                        <td style="width: 20px; vertical-align: top;">:</td>
                                        <td>
                                            <ol type="a" class="ps-3 mb-0">
                                                <li>Bahwa untuk memperlancar pelaksanaan Ujian Skripsi mahasiswa, perlu menugaskan dosen sebagai penguji Skripsi;</li>
                                                <li>Bahwa untuk melaksanakan butir a di atas, perlu ditetapkan dalam Keputusan Dekan Fakultas Teknik;</li>
                                            </ol>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="vertical-align: top;">Mengingat</td>
                                        <td style="vertical-align: top;">:</td>
                                        <td>
                                            <ol class="ps-3 mb-0">
                                                <li>Undang-Undang Nomor 20 tahun 2003, tentang Sistem Pendidikan Nasional;</li>
                                                <li>Peraturan Pemerintah Nomor 4 Tahun 2012 Tentang Penyelenggaraan Pendidikan Tinggi;</li>
                                                <li>Peraturan Presiden RI Nomor 4 Tahun 2014 Tentang Perubahan Penyelenggaraan dan Pengelolaan Perguruan Tinggi;</li>
                                                <li>Keputusan Rektor Universitas Trunodjoyo Nomor 1357/UNM3/KP/2023 tentang Pengangkatan Pejabat Struktural Dekan Fakultas Teknik;</li>
                                            </ol>
                                        </td>
                                    </tr>
                                </table>

                                <div class="text-center fw-bold my-3">MEMUTUSKAN :</div>

                                <table class="w-100">
                                    <tr>
                                        <td style="width: 100px; vertical-align: top;">Menetapkan</td>
                                        <td style="width: 20px; vertical-align: top;">:</td>
                                        <td class="fw-bold">KEPUTUSAN DEKAN FAKULTAS TEKNIK UNIVERSITAS TRUNODJOYO MADURA TENTANG PENETAPAN DOSEN PENGUJI SKRIPSI SEMESTER ${(sk.Semester || '').toUpperCase()} TA ${sk.Tahun_Akademik || ''}.</td>
                                    </tr>
                                    <tr>
                                        <td style="vertical-align: top;">KESATU</td>
                                        <td style="vertical-align: top;">:</td>
                                        <td>Dosen Penguji Skripsi sebagaimana tercantum dalam lampiran Keputusan ini;</td>
                                    </tr>
                                    <tr>
                                        <td style="vertical-align: top;">KEDUA</td>
                                        <td style="vertical-align: top;">:</td>
                                        <td>Keputusan ini berlaku sejak tanggal ditetapkan.</td>
                                    </tr>
                                </table>
                            </div>

                            <div class="signature-container">
                                <div>Ditetapkan di Bangkalan</div>
                                <div>Pada tanggal: ${formatDate(sk.Tanggal_Persetujuan_Dekan || sk['Tanggal-Persetujuan-Dekan'] || new Date())}</div>
                                <div class="fw-bold mt-2">DEKAN,</div>
                                ${data.qrCodePath ? `<img src="${data.qrCodePath}" class="qr-code">` : '<div style="height: 100px;"></div>'}
                                <div><strong style="text-decoration: underline;">${dekanName}</strong></div>
                                <div>NIP. ${dekanNip}</div>
                            </div>
                            
                            <div class="clearfix"></div>

                            <!-- Lampiran Logic rendered inline for preview -->
                            <div class="lampiran-page">
                                <div class="small mb-3">
                                    SALINAN LAMPIRAN KEPUTUSAN DEKAN FAKULTAS TEKNIK<br>
                                    NOMOR : ${sk.Nomor_Surat || '-'}<br>
                                    TANGGAL : ${formatDate(sk.Tanggal_Persetujuan_Dekan || sk['Tanggal-Persetujuan-Dekan'] || new Date())}
                                </div>
                                <div class="text-center fw-bold mb-3">
                                    DAFTAR DOSEN PENGUJI SKRIPSI<br>
                                    PROGRAM STUDI S1 ${sk.prodi ? sk.prodi.Nama_Prodi.toUpperCase() : 'FAKULTAS TEKNIK'}
                                </div>
                                <table class="doc-table doc-table-border" style="font-size: 9pt;">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>NIM</th>
                                            <th>Nama Mahasiswa</th>
                                            <th>Judul</th>
                                            <th>Penguji 1</th>
                                            <th>Penguji 2</th>
                                            <th>Penguji 3</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${mhsList.map((m, i) => `
                                            <tr>
                                                <td class="text-center">${i+1}</td>
                                                <td>${m.nim || '-'}</td>
                                                <td>${m.nama_mahasiswa || '-'}</td>
                                                <td><small>${m.judul_skripsi || '-'}</small></td>
                                                <td><small>${m.nama_penguji_1 || '-'}</small></td>
                                                <td><small>${m.nama_penguji_2 || '-'}</small></td>
                                                <td><small>${m.nama_penguji_3 || '-'}</small></td>
                                            </tr>
                                        `).join('')}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    `;
                    container.innerHTML = html;
                } else {
                    container.innerHTML = `<div class="alert alert-danger mx-auto mt-5" style="max-width: 500px;">Gagal memuat detail SK: ${data.message}</div>`;
                }
            })
            .catch(err => {
                console.error(err);
                container.innerHTML = `<div class="alert alert-danger mx-auto mt-5" style="max-width: 500px;">Terjadi kesalahan saat menghubungi server.</div>`;
            });
    }

    function formatDate(dateStr) {
        if(!dateStr) return '-';
        const options = { year: 'numeric', month: 'long', day: 'numeric' };
        return new Date(dateStr).toLocaleDateString('id-ID', options);
    }
</script>
@endpush
