@extends('layouts.admin_fakultas')

@section('title', 'Surat Pengantar Magang')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center my-4">
        <div>
            <h1 class="h3 fw-bold text-gray-800">Surat Pengantar KP/Magang</h1>
            <p class="text-muted mb-0">Kelola pengajuan surat pengantar magang mahasiswa.</p>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.admin_fakultas') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin_fakultas.surat.manage') }}">Manajemen Surat</a></li>
                <li class="breadcrumb-item active" aria-current="page">Surat Magang</li>
            </ol>
        </nav>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm border-start-success" role="alert">
        <div class="d-flex align-items-center">
            <div class="icon-circle bg-success text-white me-3">
                <i class="fas fa-check"></i>
            </div>
            <div>
                <strong>Berhasil!</strong> {{ session('success') }}
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm border-start-danger" role="alert">
        <div class="d-flex align-items-center">
            <div class="icon-circle bg-danger text-white me-3">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div>
                <strong>Gagal!</strong> {{ session('error') }}
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card shadow mb-4 border-0">
        <div class="card-header py-3 bg-white d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-clipboard-list me-2"></i>Daftar Surat Magang yang Perlu Diproses
            </h6>
            <span class="badge bg-primary text-white">{{ count($daftarSurat) }} Surat</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" width="100%" cellspacing="0">
                    <thead class="bg-light text-uppercase text-secondary text-xs font-weight-bolder opacity-7">
                        <tr>
                            <th class="ps-4">ID Pengajuan</th>
                            <th>Mahasiswa</th>
                            <th>Program Studi</th>
                            <th>Instansi Tujuan</th>
                            <th>Periode Magang</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($daftarSurat as $index => $surat)
                        @php
                            $mahasiswa = $surat->tugasSurat?->pemberiTugas?->mahasiswa ?? null;
                            $dataMahasiswa = is_array($surat->Data_Mahasiswa) ? $surat->Data_Mahasiswa : json_decode($surat->Data_Mahasiswa, true);
                            $namaMahasiswa = $mahasiswa?->Nama_Mahasiswa ?? ($dataMahasiswa[0]['nama'] ?? 'N/A');
                            $nimMahasiswa = $mahasiswa?->NIM ?? ($dataMahasiswa[0]['nim'] ?? 'N/A');
                            $prodiMahasiswa = $mahasiswa?->prodi?->Nama_Prodi ?? 'N/A';
                        @endphp
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-primary">#SM-{{ str_pad($surat->id_no, 4, '0', STR_PAD_LEFT) }}</div>
                                <small class="text-muted">
                                    @if($surat->tugasSurat && $surat->tugasSurat->Tanggal_Diberikan_Tugas_Surat)
                                        {{ \Carbon\Carbon::parse($surat->tugasSurat->Tanggal_Diberikan_Tugas_Surat)->format('d M Y') }}
                                    @else
                                        {{ \Carbon\Carbon::parse($surat->created_at ?? now())->format('d M Y') }}
                                    @endif
                                </small>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm bg-gradient-primary rounded-circle me-3 d-flex align-items-center justify-content-center text-white fw-bold" style="width: 40px; height: 40px;">
                                        {{ substr($namaMahasiswa, 0, 1) }}
                                    </div>
                                    <div>
                                        <h6 class="mb-0 text-sm fw-bold text-dark">{{ $namaMahasiswa }}</h6>
                                        <p class="text-xs text-secondary mb-0">{{ $nimMahasiswa }}</p>
                                        @if(count($dataMahasiswa) > 1)
                                        <span class="badge bg-info text-white" style="font-size: 0.6rem;">
                                            +{{ count($dataMahasiswa) - 1 }} lainnya
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="text-sm text-dark">{{ $prodiMahasiswa }}</span>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="text-sm fw-bold text-dark">{{ $surat->Nama_Instansi ?? '-' }}</span>
                                    <span class="text-xs text-muted">{{ Str::limit($surat->Alamat_Instansi ?? '-', 30) }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="text-xs text-secondary">
                                    @if($surat->Tanggal_Mulai && $surat->Tanggal_Selesai)
                                        {{ \Carbon\Carbon::parse($surat->Tanggal_Mulai)->format('d M Y') }}<br>s/d<br>
                                        {{ \Carbon\Carbon::parse($surat->Tanggal_Selesai)->format('d M Y') }}
                                    @else
                                        -
                                    @endif
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info text-white border border-info">
                                    <i class="fas fa-clock me-1"></i> Dikerjakan Admin
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-info btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#reviewModal{{ $surat->id_no }}">
                                        <i class="fas fa-eye me-1"></i> Review
                                    </button>
                                    <button type="button" class="btn btn-primary btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#prosesModal{{ $surat->id_no }}">
                                        <i class="fas fa-edit me-1"></i> Proses
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center justify-content-center">
                                    <div class="bg-light rounded-circle p-4 mb-3">
                                        <i class="fas fa-inbox fa-3x text-muted"></i>
                                    </div>
                                    <h5 class="text-muted fw-bold">Tidak ada permintaan baru</h5>
                                    <p class="text-muted mb-0">Semua surat magang telah diproses.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Modal Review - Lihat Dokumen --}}
@foreach($daftarSurat as $surat)
@php
    $mahasiswa = $surat->tugasSurat?->pemberiTugas?->mahasiswa ?? null;
    $dataMahasiswa = is_array($surat->Data_Mahasiswa) ? $surat->Data_Mahasiswa : json_decode($surat->Data_Mahasiswa, true);
    $dataDosenPembimbing = is_array($surat->Data_Dosen_pembiming) ? $surat->Data_Dosen_pembiming : json_decode($surat->Data_Dosen_pembiming, true);
@endphp
<div class="modal fade" id="reviewModal{{ $surat->id_no }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-file-alt me-2"></i>Review Dokumen Pengajuan - #SM-{{ str_pad($surat->id_no, 4, '0', STR_PAD_LEFT) }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    {{-- Kolom Kiri: Informasi dan Aksi --}}
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm mb-3">
                            <div class="card-header bg-white fw-bold">
                                <i class="fas fa-info-circle me-2"></i>Informasi Pengajuan
                            </div>
                            <div class="card-body">
                                <div class="mb-2">
                                    <small class="text-muted">Mahasiswa:</small>
                                    <div class="fw-bold">{{ $dataMahasiswa[0]['nama'] ?? 'N/A' }}</div>
                                    @if(count($dataMahasiswa) > 1)
                                        <small class="text-info">+{{ count($dataMahasiswa) - 1 }} mahasiswa lainnya</small>
                                    @endif
                                </div>
                                <div class="mb-2">
                                    <small class="text-muted">Program Studi:</small>
                                    <div>{{ $mahasiswa?->prodi?->Nama_Prodi ?? 'N/A' }}</div>
                                </div>
                                <div class="mb-2">
                                    <small class="text-muted">Instansi:</small>
                                    <div class="fw-bold">{{ $surat->Nama_Instansi }}</div>
                                </div>
                                <div class="mb-2">
                                    <small class="text-muted">Periode:</small>
                                    <div>{{ \Carbon\Carbon::parse($surat->Tanggal_Mulai)->format('d M Y') }} - {{ \Carbon\Carbon::parse($surat->Tanggal_Selesai)->format('d M Y') }}</div>
                                </div>
                                <div class="mb-2">
                                    <small class="text-muted">Koordinator:</small>
                                    <div>{{ $surat->koordinator?->Nama_Dosen ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white fw-bold">
                                <i class="fas fa-download me-2"></i>Dokumen
                            </div>
                            <div class="card-body">
                                @if($surat->Dokumen_Proposal)
                                    <a href="{{ route('admin_fakultas.surat.magang.download_proposal', $surat->id_no) }}" 
                                       class="btn btn-outline-primary w-100 mb-2" target="_blank">
                                        <i class="fas fa-file-pdf me-1"></i> Download Proposal
                                    </a>
                                @else
                                    <div class="alert alert-warning mb-2 small">Proposal tidak tersedia</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Kolom Kanan: Preview Surat Pengantar (Form) --}}
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-light">
                                <h6 class="mb-0 fw-bold text-info">
                                    <i class="fas fa-file-alt me-2"></i>Surat Pengantar (Form Pengajuan)
                                </h6>
                            </div>
                            <div class="card-body p-0" style="max-height: 70vh; overflow-y: auto;">
                                <div class="preview-document" style="border: 1px solid #ddd; padding: 30px; background: white; font-family: 'Times New Roman', serif; font-size: 11pt; line-height: 1.6;">
                                    {{-- Header --}}
                                    <div style="text-align: center; margin-bottom: 20px; border-bottom: 3px solid #000; padding-bottom: 10px;">
                                        <img src="{{ asset('images/logo_unijoyo.png') }}" alt="Logo UTM" style="height: 60px; float: left;">
                                        <div style="margin-left: 70px;">
                                            <strong style="display: block; font-size: 10pt;">KEMENTERIAN PENDIDIKAN TINGGI, SAINS, DAN TEKNOLOGI</strong>
                                            <strong style="display: block; font-size: 11pt;">UNIVERSITAS TRUNOJOYO MADURA</strong>
                                            <strong style="display: block; font-size: 12pt;">FAKULTAS TEKNIK</strong>
                                            <div style="font-size: 8pt; margin-top: 5px;">
                                                Jl. Raya Telang, PO.Box. 2 Kamal, Bangkalan – Madura<br>
                                                Telp : (031) 3011146, Fax. (031) 3011506<br>
                                                Laman : www.trunojoyo.ac.id
                                            </div>
                                        </div>
                                        <div style="clear: both;"></div>
                                    </div>

                                    <h5 style="text-align: center; font-weight: bold; text-decoration: underline; margin: 30px 0;">FORM PENGAJUAN SURAT PENGANTAR</h5>

                                    <table style="width: 100%; font-size: 10pt; margin: 20px 0;">
                                        <tr>
                                            <td style="width: 30%; padding: 5px 0;">Nama</td>
                                            <td style="width: 2%;">:</td>
                                            <td>
                                                @foreach($dataMahasiswa as $idx => $mhs)
                                                    <div style="margin-bottom: 5px;">
                                                        <strong>{{ $idx + 1 }}. {{ $mhs['nama'] ?? '' }}</strong> (NIM: {{ $mhs['nim'] ?? '' }})
                                                    </div>
                                                @endforeach
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 5px 0;">Jurusan</td>
                                            <td>:</td>
                                            <td>{{ $mahasiswa?->prodi?->Nama_Prodi ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 5px 0;">Dosen Pembimbing</td>
                                            <td>:</td>
                                            <td>{{ $dataDosenPembimbing['dosen_pembimbing_1'] ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 5px 0;">Instansi Tujuan</td>
                                            <td>:</td>
                                            <td><strong>{{ $surat->Nama_Instansi }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 5px 0;">Periode Magang</td>
                                            <td>:</td>
                                            <td>{{ \Carbon\Carbon::parse($surat->Tanggal_Mulai)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($surat->Tanggal_Selesai)->format('d M Y') }}</td>
                                        </tr>
                                    </table>

                                    {{-- TTD Koordinator dan Mahasiswa --}}
                                    <div style="margin-top: 50px;">
                                        <table style="width: 100%;">
                                            <tr>
                                                <td style="width: 50%; text-align: center; vertical-align: top;">
                                                    <p style="margin: 0 0 10px 0;">Menyetujui,<br>Koordinator KP/TA</p>
                                                    @if($surat->Qr_code)
                                                        <div style="margin: 10px 0;">
                                                            <img src="{{ asset('storage/' . $surat->Qr_code) }}" style="width: 80px; height: 80px;">
                                                        </div>
                                                    @endif
                                                    <p style="margin: 10px 0;"><strong><u>{{ $surat->koordinator?->Nama_Dosen ?? '[Koordinator]' }}</u></strong></p>
                                                    <p style="margin: 0; font-size: 9pt;">NIP {{ $surat->koordinator?->NIP ?? '...' }}</p>
                                                </td>
                                                <td style="width: 50%; text-align: center; vertical-align: top;">
                                                    <p style="margin: 0 0 10px 0;">Bangkalan, {{ \Carbon\Carbon::now()->format('d M Y') }}<br>Pemohon</p>
                                                    @if($surat->Foto_ttd)
                                                        <img src="{{ asset('storage/' . $surat->Foto_ttd) }}" style="height: 60px; margin: 10px 0;">
                                                    @else
                                                        <div style="height: 60px; margin: 10px 0;"></div>
                                                    @endif
                                                    <p style="margin: 10px 0;"><strong><u>{{ $dataMahasiswa[0]['nama'] ?? 'N/A' }}</u></strong></p>
                                                    <p style="margin: 0; font-size: 9pt;">NIM {{ $dataMahasiswa[0]['nim'] ?? '...' }}</p>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Proses - Generate Surat Pengantar Magang --}}
<div class="modal fade" id="prosesModal{{ $surat->id_no }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-file-contract me-2"></i>Proses Surat Pengantar Magang - #SM-{{ str_pad($surat->id_no, 4, '0', STR_PAD_LEFT) }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    {{-- Kolom Kiri: Form Input Nomor Surat --}}
                    <div class="col-lg-4">
                        <div class="card border-primary mb-3">
                            <div class="card-header bg-primary text-white fw-bold">
                                <i class="fas fa-file-signature me-2"></i>Assign Nomor Surat
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin_fakultas.surat.magang.assign_nomor', $surat->id_no) }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="nomor_surat_proses_{{ $surat->id_no }}" class="form-label fw-bold">Nomor Surat <span class="text-danger">*</span></label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="nomor_surat_proses_{{ $surat->id_no }}" 
                                               name="nomor_surat" 
                                               placeholder="Contoh: 123/UN16.FT/TU/2025"
                                               oninput="updatePreviewNomorProses{{ $surat->id_no }}(this.value)"
                                               required>
                                        <small class="text-muted">Format: [Nomor]/UN16.FT/TU/[Tahun]</small>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100 fw-bold" onclick="return confirm('Anda yakin nomor surat sudah benar dan akan meneruskan ke Dekan?')">
                                        <i class="fas fa-paper-plane me-2"></i>TERUSKAN KE DEKAN
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm mb-3">
                            <div class="card-header bg-white fw-bold">
                                <i class="fas fa-info-circle me-2"></i>Informasi Surat
                            </div>
                            <div class="card-body">
                                <div class="mb-2">
                                    <small class="text-muted">Mahasiswa:</small>
                                    <div class="fw-bold">{{ $dataMahasiswa[0]['nama'] ?? 'N/A' }}</div>
                                    @if(count($dataMahasiswa) > 1)
                                        <small class="text-info">+{{ count($dataMahasiswa) - 1 }} mahasiswa lainnya</small>
                                    @endif
                                </div>
                                <div class="mb-2">
                                    <small class="text-muted">Instansi:</small>
                                    <div class="fw-bold">{{ $surat->Nama_Instansi }}</div>
                                </div>
                                <div class="mb-2">
                                    <small class="text-muted">Periode:</small>
                                    <div>{{ \Carbon\Carbon::parse($surat->Tanggal_Mulai)->format('d M Y') }} - {{ \Carbon\Carbon::parse($surat->Tanggal_Selesai)->format('d M Y') }}</div>
                                </div>
                                <div class="mb-2">
                                    <small class="text-muted">Koordinator:</small>
                                    <div>{{ $surat->koordinator?->Nama_Dosen ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white fw-bold">
                                <i class="fas fa-print me-2"></i>Aksi
                            </div>
                            <div class="card-body">
                                <button type="button" class="btn btn-outline-success w-100" onclick="window.print()">
                                    <i class="fas fa-print me-1"></i> Cetak Surat Pengantar Magang
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Kolom Kanan: Preview Surat Pengantar Magang --}}
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-light">
                                <h6 class="mb-0 fw-bold text-primary">
                                    <i class="fas fa-file-contract me-2"></i>Preview Surat Pengantar Magang
                                </h6>
                            </div>
                            <div class="card-body p-0" style="max-height: 70vh; overflow-y: auto;">
                                <div id="previewContainerProses{{ $surat->id_no }}" class="preview-document" style="border: 1px solid #ddd; padding: 30px; background: white; font-family: 'Times New Roman', serif; font-size: 11pt; line-height: 1.6;">
                                    {{-- Header --}}
                                    <div style="text-align: center; margin-bottom: 20px; border-bottom: 3px solid #000; padding-bottom: 10px;">
                                        <img src="{{ asset('images/logo_unijoyo.png') }}" alt="Logo UTM" style="height: 60px; float: left;">
                                        <div style="margin-left: 70px;">
                                            <strong style="display: block; font-size: 10pt;">KEMENTERIAN PENDIDIKAN TINGGI, SAINS, DAN TEKNOLOGI</strong>
                                            <strong style="display: block; font-size: 11pt;">UNIVERSITAS TRUNOJOYO MADURA</strong>
                                            <strong style="display: block; font-size: 12pt;">FAKULTAS TEKNIK</strong>
                                            <div style="font-size: 8pt; margin-top: 5px;">
                                                Jl. Raya Telang, PO.Box. 2 Kamal, Bangkalan – Madura<br>
                                                Telp : (031) 3011146, Fax. (031) 3011506<br>
                                                Laman : www.trunojoyo.ac.id
                                            </div>
                                        </div>
                                        <div style="clear: both;"></div>
                                    </div>

                                    {{-- Nomor Surat --}}
                                    <div style="margin: 20px 0;">
                                        <table style="width: 100%; font-size: 10pt;">
                                            <tr>
                                                <td style="width: 25%;">Nomor</td>
                                                <td style="width: 2%;">:</td>
                                                <td><strong id="preview_nomor_proses_{{ $surat->id_no }}">[Nomor Surat]</strong></td>
                                            </tr>
                                            <tr>
                                                <td>Perihal</td>
                                                <td>:</td>
                                                <td><strong>Permohonan Izin Magang Mandiri</strong></td>
                                            </tr>
                                        </table>
                                    </div>

                                    {{-- Tanggal --}}
                                    <div style="text-align: right; margin: 20px 0 30px 0;">
                                        {{ \Carbon\Carbon::now()->format('d F Y') }}
                                    </div>

                                    {{-- Kepada --}}
                                    <div style="margin: 20px 0;">
                                        <p style="margin: 0;">Yth. Pimpinan {{ $surat->Nama_Instansi }}</p>
                                        <p style="margin: 0;">{{ $surat->Alamat_Instansi }}</p>
                                    </div>

                                    {{-- Isi Surat --}}
                                    <p style="text-align: justify; text-indent: 50px; margin: 20px 0; line-height: 1.8;">
                                        Sehubungan dalam memperkenalkan mahasiswa pada dunia kerja sesuai bidang masing-masing, maka 
                                        sesuai ketentuan Program Merdeka Belajar - Kampus Merdeka (MBKM) mahasiswa diperkenankan 
                                        melaksanakan magang. Guna memperlancar kegiatan tersebut, kami mohon Bapak/Ibu untuk memberikan 
                                        izin kepada mahasiswa kami untuk dapat melaksanakan kegiatan magang di perusahaan tersebut pada 
                                        tanggal {{ \Carbon\Carbon::parse($surat->Tanggal_Mulai)->format('d F') }} s.d. 
                                        {{ \Carbon\Carbon::parse($surat->Tanggal_Selesai)->format('d F Y') }}.
                                    </p>

                                    <p style="margin: 15px 0;">Adapun mahasiswa tersebut adalah:</p>

                                    {{-- Tabel Mahasiswa --}}
                                    <table style="width: 100%; border-collapse: collapse; margin: 15px 0; font-size: 9pt;">
                                        <thead>
                                            <tr style="background-color: #f0f0f0;">
                                                <th style="border: 1px solid #000; padding: 6px; text-align: center; width: 5%;">No</th>
                                                <th style="border: 1px solid #000; padding: 6px; text-align: left; width: 40%;">Nama</th>
                                                <th style="border: 1px solid #000; padding: 6px; text-align: left; width: 35%;">Program Studi</th>
                                                <th style="border: 1px solid #000; padding: 6px; text-align: left; width: 20%;">No. WA</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($dataMahasiswa as $idx => $mhs)
                                            <tr>
                                                <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $idx + 1 }}.</td>
                                                <td style="border: 1px solid #000; padding: 6px;">
                                                    <strong>{{ $mhs['nama'] ?? '' }}</strong><br>
                                                    <small>NIM {{ $mhs['nim'] ?? '' }}</small>
                                                </td>
                                                <td style="border: 1px solid #000; padding: 6px;">{{ $mahasiswa?->prodi?->Nama_Prodi ?? 'N/A' }}</td>
                                                <td style="border: 1px solid #000; padding: 6px;">{{ $mhs['no_wa'] ?? '-' }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>

                                    <p style="text-align: justify; margin: 20px 0; line-height: 1.8;">
                                        Besar harapan kami dapat menerima konfirmasi kesediaan menerima atau menolak pengajuan Magang 
                                        Mandiri ini maksimal 14 (empat belas) hari dari tanggal surat ini dikeluarkan.
                                    </p>

                                    <p style="text-align: justify; margin: 20px 0; line-height: 1.8;">
                                        Demikian, atas perhatian dan bantuannya kami ucapkan terima kasih.
                                    </p>

                                    {{-- TTD Dekan (belum ada) --}}
                                    <div style="margin-top: 40px;">
                                        <table style="width: 100%;">
                                            <tr>
                                                <td style="width: 50%; vertical-align: top;"></td>
                                                <td style="width: 50%; text-align: center; vertical-align: top;">
                                                    <p style="margin: 0 0 5px 0;">Dekan Fakultas Teknik,</p>
                                                    <div style="height: 100px; border: 2px dashed #ccc; display: inline-block; padding: 30px; margin: 10px 0; background: #f9f9f9;">
                                                        <small style="color: #999;">TTD & QR Code Dekan<br>akan muncul setelah<br>disetujui</small>
                                                    </div>
                                                    @php
                                                        $fakultas = $mahasiswa?->prodi?->fakultas;
                                                        $dekan = null;
                                                        if ($fakultas && $fakultas->Id_Dekan) {
                                                            $dekan = \App\Models\Dosen::find($fakultas->Id_Dekan);
                                                        }
                                                        $namaDekan = $dekan?->Nama_Dosen ?? 'Dr. Budi Hartono, S.Kom., M.Kom.';
                                                        $nipDekan = $dekan?->NIP ?? '198503152010121001';
                                                    @endphp
                                                    <p style="margin: 5px 0;"><strong><u>{{ $namaDekan }}</u></strong></p>
                                                    <p style="margin: 0; font-size: 9pt;">NIP {{ $nipDekan }}</p>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updatePreviewNomorProses{{ $surat->id_no }}(value) {
    const previewElement = document.getElementById('preview_nomor_proses_{{ $surat->id_no }}');
    if (previewElement) {
        previewElement.textContent = value || '[Nomor Surat]';
    }
}
</script>
@endforeach

<style>
    .border-start-success {
        border-left: 4px solid #198754 !important;
    }
    .border-start-danger {
        border-left: 4px solid #dc3545 !important;
    }
    .icon-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>
@endsection