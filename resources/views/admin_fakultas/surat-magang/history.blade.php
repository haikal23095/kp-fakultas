@extends('layouts.admin_fakultas')

@section('title', 'History Surat Pengantar KP/Magang')

@push('styles')
<style>
    .modal-xl-custom {
        max-width: 900px;
    }
    .detail-label {
        font-weight: 600;
        color: #5a5c69;
        font-size: 0.85rem;
    }
    .detail-value {
        color: #2c3e50;
    }
    .preview-document {
        border: 1px solid #ddd;
        padding: 30px;
        background: white;
        font-family: 'Times New Roman', serif;
        font-size: 11pt;
        line-height: 1.6;
    }
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
@endpush

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center my-4">
        <div>
            <h1 class="h3 fw-bold text-gray-800">History Surat Pengantar KP/Magang</h1>
            <p class="text-muted mb-0">Riwayat pengajuan surat pengantar magang mahasiswa.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin_fakultas.surat.magang') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>
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
        <div class="card-header py-3" style="background: linear-gradient(135deg, #36b9cc 0%, #1a8a9c 100%); border-radius: 12px 12px 0 0;">
            <h6 class="m-0 font-weight-bold text-white">
                <i class="fas fa-history me-2"></i>Riwayat Surat Pengantar KP/Magang
            </h6>
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
                        @forelse($historyData as $index => $surat)
                        @php
                            $mahasiswa = $surat->pemberiTugas?->mahasiswa ?? null;
                            $dataMahasiswa = is_array($surat->Data_Mahasiswa) ? $surat->Data_Mahasiswa : json_decode($surat->Data_Mahasiswa, true);
                            $dataDosenPembimbing = is_array($surat->Data_Dosen_pembiming) ? $surat->Data_Dosen_pembiming : json_decode($surat->Data_Dosen_pembiming, true);
                            $namaMahasiswa = $mahasiswa?->Nama_Mahasiswa ?? ($dataMahasiswa[0]['nama'] ?? 'N/A');
                            $nimMahasiswa = $mahasiswa?->NIM ?? ($dataMahasiswa[0]['nim'] ?? 'N/A');
                            $prodiMahasiswa = $mahasiswa?->prodi?->Nama_Prodi ?? 'N/A';
                        @endphp
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-primary">#SM-{{ str_pad($surat->id_no, 4, '0', STR_PAD_LEFT) }}</div>
                                <small class="text-muted">
                                    @if($surat->Tanggal_Diberikan)
                                        {{ \Carbon\Carbon::parse($surat->Tanggal_Diberikan)->format('d M Y') }}
                                    @else
                                        -
                                    @endif
                                </small>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm bg-gradient-primary rounded-circle me-3 d-flex align-items-center justify-content-center text-white fw-bold" style="width: 40px; height: 40px;">
                                        {{ strtoupper(substr($namaMahasiswa, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="text-sm fw-bold text-dark">{{ $namaMahasiswa }}</div>
                                        <div class="text-xs text-muted">{{ $nimMahasiswa }}</div>
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
                                        {{ \Carbon\Carbon::parse($surat->Tanggal_Mulai)->format('d M Y') }} -
                                        {{ \Carbon\Carbon::parse($surat->Tanggal_Selesai)->format('d M Y') }}
                                    @else
                                        -
                                    @endif
                                </span>
                            </td>
                            <td class="text-center">
                                @php $status = $surat->Status ?? '-'; @endphp
                                @if(strtolower($status) === 'success')
                                    <span class="badge bg-success text-white">
                                        <i class="fas fa-check me-1"></i>Selesai
                                    </span>
                                @elseif(str_contains(strtolower($status), 'ditolak'))
                                    <span class="badge bg-danger text-white">
                                        <i class="fas fa-times me-1"></i>{{ $status }}
                                    </span>
                                @elseif(strtolower($status) === 'diajukan-ke-dekan')
                                    <span class="badge bg-warning text-dark">
                                        <i class="fas fa-signature me-1"></i>Ke Dekan
                                    </span>
                                @else
                                    <span class="badge bg-secondary text-white">{{ $status }}</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <button type="button" 
                                        class="btn btn-sm btn-outline-info shadow-sm"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#detailModal{{ $surat->id_no }}">
                                    <i class="fas fa-eye me-1"></i> Detail
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center justify-content-center">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Tidak ada data history surat pengantar magang.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    @if($historyData->hasPages())
    <div class="mt-3">
        {{ $historyData->links() }}
    </div>
    @endif
</div>

{{-- Modal Detail untuk setiap surat --}}
@foreach($historyData as $surat)
@php
    $mahasiswa = $surat->pemberiTugas?->mahasiswa ?? null;
    $dataMahasiswa = is_array($surat->Data_Mahasiswa) ? $surat->Data_Mahasiswa : json_decode($surat->Data_Mahasiswa, true);
    $dataDosenPembimbing = is_array($surat->Data_Dosen_pembiming) ? $surat->Data_Dosen_pembiming : json_decode($surat->Data_Dosen_pembiming, true);
    $namaMahasiswa = $mahasiswa?->Nama_Mahasiswa ?? ($dataMahasiswa[0]['nama'] ?? 'N/A');
    $nimMahasiswa = $mahasiswa?->NIM ?? ($dataMahasiswa[0]['nim'] ?? 'N/A');
    $prodiMahasiswa = $mahasiswa?->prodi?->Nama_Prodi ?? 'N/A';
@endphp
<div class="modal fade" id="detailModal{{ $surat->id_no }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content" style="border-radius: 16px; border: none;">
            <div class="modal-header" style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); border-radius: 16px 16px 0 0;">
                <h5 class="modal-title text-white fw-bold">
                    <i class="fas fa-file-alt me-2"></i>Detail Surat Pengantar KP/Magang - #SM-{{ str_pad($surat->id_no, 4, '0', STR_PAD_LEFT) }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="row g-0">
                    {{-- Kolom Kiri: Informasi Detail --}}
                    <div class="col-lg-4 border-end p-4">
                        <h6 class="text-primary fw-bold mb-3"><i class="fas fa-info-circle me-2"></i>Informasi Pengajuan</h6>
                        
                        <div class="mb-3">
                            <p class="detail-label mb-1">ID Pengajuan</p>
                            <p class="detail-value">#SM-{{ str_pad($surat->id_no, 4, '0', STR_PAD_LEFT) }}</p>
                        </div>
                        
                        <div class="mb-3">
                            <p class="detail-label mb-1">Nomor Surat</p>
                            <p class="detail-value fw-bold">{{ $surat->Nomor_Surat ?? '-' }}</p>
                        </div>
                        
                        <div class="mb-3">
                            <p class="detail-label mb-1">Status</p>
                            @php $status = $surat->Status ?? '-'; @endphp
                            @if(strtolower($status) === 'success')
                                <span class="badge bg-success"><i class="fas fa-check me-1"></i>Selesai</span>
                            @elseif(str_contains(strtolower($status), 'ditolak'))
                                <span class="badge bg-danger"><i class="fas fa-times me-1"></i>{{ $status }}</span>
                            @elseif(strtolower($status) === 'diajukan-ke-dekan')
                                <span class="badge bg-warning text-dark"><i class="fas fa-signature me-1"></i>Ke Dekan</span>
                            @else
                                <span class="badge bg-secondary">{{ $status }}</span>
                            @endif
                        </div>

                        <hr>

                        <h6 class="text-primary fw-bold mb-3"><i class="fas fa-user-graduate me-2"></i>Data Mahasiswa</h6>
                        
                        @if(is_array($dataMahasiswa) && count($dataMahasiswa) > 0)
                            @foreach($dataMahasiswa as $idx => $mhs)
                            <div class="mb-2 p-2 bg-light rounded">
                                <div class="fw-bold">{{ $mhs['nama'] ?? 'N/A' }}</div>
                                <small class="text-muted">NIM: {{ $mhs['nim'] ?? '-' }}</small>
                            </div>
                            @endforeach
                        @else
                            <p class="text-muted">Data mahasiswa tidak tersedia</p>
                        @endif

                        <hr>

                        <h6 class="text-primary fw-bold mb-3"><i class="fas fa-chalkboard-teacher me-2"></i>Dosen Pembimbing</h6>
                        
                        @if(is_array($dataDosenPembimbing) && count($dataDosenPembimbing) > 0)
                            @foreach($dataDosenPembimbing as $idx => $dosen)
                            <div class="mb-2 p-2 bg-light rounded">
                                <div class="fw-bold">{{ $dosen['nama'] ?? 'N/A' }}</div>
                                <small class="text-muted">NIP: {{ $dosen['nip'] ?? '-' }}</small>
                            </div>
                            @endforeach
                        @else
                            <p class="text-muted">Data dosen pembimbing tidak tersedia</p>
                        @endif

                        <hr>

                        <h6 class="text-primary fw-bold mb-3"><i class="fas fa-building me-2"></i>Instansi Tujuan</h6>
                        <div class="mb-3">
                            <p class="detail-label mb-1">Nama Instansi</p>
                            <p class="detail-value">{{ $surat->Nama_Instansi ?? '-' }}</p>
                        </div>
                        <div class="mb-3">
                            <p class="detail-label mb-1">Alamat Instansi</p>
                            <p class="detail-value">{{ $surat->Alamat_Instansi ?? '-' }}</p>
                        </div>

                        <hr>

                        <h6 class="text-primary fw-bold mb-3"><i class="fas fa-calendar-alt me-2"></i>Periode Magang</h6>
                        <div class="mb-3">
                            <p class="detail-label mb-1">Tanggal Mulai</p>
                            <p class="detail-value">{{ $surat->Tanggal_Mulai ? \Carbon\Carbon::parse($surat->Tanggal_Mulai)->format('d M Y') : '-' }}</p>
                        </div>
                        <div class="mb-3">
                            <p class="detail-label mb-1">Tanggal Selesai</p>
                            <p class="detail-value">{{ $surat->Tanggal_Selesai ? \Carbon\Carbon::parse($surat->Tanggal_Selesai)->format('d M Y') : '-' }}</p>
                        </div>

                        @if($surat->Komentar)
                        <hr>
                        <h6 class="text-primary fw-bold mb-3"><i class="fas fa-comment me-2"></i>Komentar</h6>
                        <p class="detail-value">{{ $surat->Komentar }}</p>
                        @endif
                    </div>

                    {{-- Kolom Kanan: Preview Surat --}}
                    <div class="col-lg-8 p-4" style="background-color: #f8f9fc;">
                        <h6 class="text-primary fw-bold mb-3"><i class="fas fa-file-contract me-2"></i>Preview Surat Pengantar Magang</h6>
                        
                        <div class="preview-document" style="max-height: 60vh; overflow-y: auto;">
                            {{-- Kop Surat --}}
                            <div style="text-align: center; border-bottom: 3px solid #000; padding-bottom: 10px; margin-bottom: 20px;">
                                <img src="{{ asset('images/logo_unijoyo.png') }}" alt="Logo UTM" style="height: 60px; float: left;">
                                <div style="margin-left: 70px;">
                                    <strong style="display: block; font-size: 10pt;">KEMENTERIAN PENDIDIKAN TINGGI, SAINS, DAN TEKNOLOGI</strong>
                                    <strong style="display: block; font-size: 11pt;">UNIVERSITAS TRUNODJOYO</strong>
                                    <strong style="display: block; font-size: 12pt;">FAKULTAS TEKNIK</strong>
                                    <div style="font-size: 8pt; margin-top: 5px;">
                                        Jl. Raya Telang, PO.Box. 2 Kamal, Bangkalan – Madura<br>
                                        Telp : (031) 3011146, Fax. (031) 3011506<br>
                                        Laman : www.trunojoyo.ac.id
                                    </div>
                                </div>
                                <div style="clear: both;"></div>
                            </div>

                            {{-- Nomor dan Perihal --}}
                            <div style="margin-bottom: 20px;">
                                <table style="width: 100%;">
                                    <tr>
                                        <td style="width: 80px;">Nomor</td>
                                        <td style="width: 10px;">:</td>
                                        <td><strong>{{ $surat->Nomor_Surat ?? '[Nomor Surat]' }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td>Lampiran</td>
                                        <td>:</td>
                                        <td>-</td>
                                    </tr>
                                    <tr>
                                        <td>Perihal</td>
                                        <td>:</td>
                                        <td><strong>Permohonan Kerja Praktek/Magang</strong></td>
                                    </tr>
                                </table>
                            </div>

                            {{-- Tujuan --}}
                            <div style="margin-bottom: 20px;">
                                <p style="margin: 0;">Kepada Yth.</p>
                                <p style="margin: 0; font-weight: bold;">{{ $surat->Nama_Instansi ?? '[Nama Instansi]' }}</p>
                                <p style="margin: 0;">{{ $surat->Alamat_Instansi ?? '[Alamat Instansi]' }}</p>
                            </div>

                            {{-- Isi Surat --}}
                            <div style="margin-bottom: 20px; text-align: justify;">
                                <p style="text-indent: 50px; line-height: 1.8;">
                                    Sehubungan dalam memperkenalkan mahasiswa pada dunia kerja sesuai bidang masing-masing, maka 
                                    sesuai ketentuan Program Merdeka Belajar - Kampus Merdeka (MBKM) mahasiswa diperkenankan 
                                    melaksanakan magang. Guna memperlancar kegiatan tersebut, kami mohon Bapak/Ibu untuk memberikan 
                                    izin kepada mahasiswa kami untuk dapat melaksanakan kegiatan magang di perusahaan tersebut pada 
                                    tanggal {{ $surat->Tanggal_Mulai ? \Carbon\Carbon::parse($surat->Tanggal_Mulai)->format('d F') : '[Tanggal Mulai]' }} s.d. 
                                    {{ $surat->Tanggal_Selesai ? \Carbon\Carbon::parse($surat->Tanggal_Selesai)->format('d F Y') : '[Tanggal Selesai]' }}.
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
                                        @if(is_array($dataMahasiswa) && count($dataMahasiswa) > 0)
                                            @foreach($dataMahasiswa as $idx => $mhs)
                                            <tr>
                                                <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $idx + 1 }}.</td>
                                                <td style="border: 1px solid #000; padding: 6px;">
                                                    <strong>{{ $mhs['nama'] ?? '' }}</strong><br>
                                                    <small>NIM {{ $mhs['nim'] ?? '' }}</small>
                                                </td>
                                                <td style="border: 1px solid #000; padding: 6px;">{{ $prodiMahasiswa }}</td>
                                                <td style="border: 1px solid #000; padding: 6px;">{{ $mhs['no_wa'] ?? '-' }}</td>
                                            </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="4" style="border: 1px solid #000; padding: 6px; text-align: center;">Data mahasiswa tidak tersedia</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>

                                <p style="text-align: justify; margin: 20px 0; line-height: 1.8;">
                                    Besar harapan kami dapat menerima konfirmasi kesediaan menerima atau menolak pengajuan Magang 
                                    Mandiri ini maksimal 14 (empat belas) hari dari tanggal surat ini dikeluarkan.
                                </p>

                                <p style="text-align: justify; margin: 20px 0; line-height: 1.8;">
                                    Demikian, atas perhatian dan bantuannya kami ucapkan terima kasih.
                                </p>
                            </div>

                            {{-- Tanda Tangan --}}
                            <div style="margin-top: 40px;">
                                <table style="width: 100%;">
                                    <tr>
                                        <td style="width: 50%; vertical-align: top;"></td>
                                        <td style="width: 50%; text-align: center; vertical-align: top;">
                                            <p style="margin: 0 0 5px 0;">Dekan Fakultas Teknik,</p>
                                            @if($surat->Qr_code_dekan)
                                                <div style="margin: 10px 0;">
                                                    <img src="{{ asset('storage/' . $surat->Qr_code_dekan) }}" style="width: 80px; height: 80px;">
                                                </div>
                                            @else
                                                <div style="height: 80px; margin: 10px 0;"></div>
                                            @endif
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
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Tutup
                </button>
            </div>
@endforeach

@endsection
