@extends('layouts.dekan')

@section('title', 'Detail Surat Magang')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-0">Detail Surat Pengantar Magang</h1>
        <p class="mb-0 text-muted">
            <a href="{{ route('dekan.surat_magang.index') }}" class="text-decoration-none">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
            </a>
        </p>
    </div>
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
    {{-- Informasi Surat --}}
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Informasi Surat</h6>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Nomor Surat:</div>
                    <div class="col-md-8">
                        <span class="badge bg-primary">{{ $surat->Nomor_Surat ?? '-' }}</span>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Mahasiswa:</div>
                    <div class="col-md-8">
                        @foreach($dataMahasiswa as $idx => $mhs)
                        <div class="mb-2">
                            {{ $idx + 1 }}. <strong>{{ $mhs['nama'] ?? '' }}</strong><br>
                            <small class="text-muted">NIM: {{ $mhs['nim'] ?? '' }} | Angkatan: {{ $mhs['angkatan'] ?? '-' }}</small>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Program Studi:</div>
                    <div class="col-md-8">{{ $surat->tugasSurat?->pemberiTugas?->mahasiswa?->prodi?->Nama_Prodi ?? 'N/A' }}</div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Dosen Pembimbing:</div>
                    <div class="col-md-8">
                        @if($dataDosenPembimbing)
                            @if(isset($dataDosenPembimbing['dosen_pembimbing_1']))
                                <div>1. {{ $dataDosenPembimbing['dosen_pembimbing_1'] }}</div>
                            @endif
                            @if(isset($dataDosenPembimbing['dosen_pembimbing_2']) && $dataDosenPembimbing['dosen_pembimbing_2'])
                                <div>2. {{ $dataDosenPembimbing['dosen_pembimbing_2'] }}</div>
                            @endif
                        @endif
                    </div>
                </div>

                <hr>

                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Nama Instansi:</div>
                    <div class="col-md-8">{{ $surat->Nama_Instansi ?? '-' }}</div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Alamat Instansi:</div>
                    <div class="col-md-8">{{ $surat->Alamat_Instansi ?? '-' }}</div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Judul Penelitian:</div>
                    <div class="col-md-8">{{ $surat->Judul_Penelitian ?? '-' }}</div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Periode Magang:</div>
                    <div class="col-md-8">
                        @if($surat->Tanggal_Mulai && $surat->Tanggal_Selesai)
                            {{ \Carbon\Carbon::parse($surat->Tanggal_Mulai)->format('d M Y') }} - 
                            {{ \Carbon\Carbon::parse($surat->Tanggal_Selesai)->format('d M Y') }}
                        @else
                            -
                        @endif
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Koordinator yang Menyetujui:</div>
                    <div class="col-md-8">
                        {{ $surat->koordinator?->Nama_Dosen ?? 'N/A' }}
                        @if($surat->koordinator)
                            <br><small class="text-muted">NIP: {{ $surat->koordinator->NIP }}</small>
                        @endif
                    </div>
                </div>

                <hr>

                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Dokumen Proposal:</div>
                    <div class="col-md-8">
                        @if($surat->Dokumen_Proposal)
                            <a href="{{ route('dekan.surat_magang.download', $surat->id_no) }}" 
                               class="btn btn-sm btn-primary">
                                <i class="fas fa-download"></i> Unduh Proposal
                            </a>
                        @else
                            <span class="text-muted">Tidak ada dokumen</span>
                        @endif
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Preview Surat:</div>
                    <div class="col-md-8">
                        <button type="button" class="btn btn-sm btn-success" onclick="togglePreviewSurat()">
                            <i class="fas fa-file-contract"></i> Lihat Surat Pengantar Magang
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Preview Surat Pengantar Magang --}}
        <div id="previewSurat" style="display: none;" class="card shadow mb-4">
            <div class="card-header py-3 bg-success text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-file-contract me-2"></i>Preview Surat Pengantar Magang
                </h6>
            </div>
            <div class="card-body">
                <div class="preview-document" style="border: 1px solid #ddd; padding: 30px; background: white; font-family: 'Times New Roman', serif; font-size: 12pt; line-height: 1.6; max-width: 800px; margin: 0 auto;">
                    {{-- Header --}}
                    <div style="text-align: center; margin-bottom: 20px; border-bottom: 3px solid #000; padding-bottom: 10px;">
                        <img src="{{ asset('images/logo_unijoyo.png') }}" alt="Logo UTM" style="height: 60px; float: left;">
                        <div style="margin-left: 70px;">
                            <strong style="display: block; font-size: 11pt;">KEMENTERIAN PENDIDIKAN TINGGI, SAINS, DAN TEKNOLOGI</strong>
                            <strong style="display: block; font-size: 12pt;">UNIVERSITAS TRUNODJOYO</strong>
                            <strong style="display: block; font-size: 13pt;">FAKULTAS TEKNIK</strong>
                            <div style="font-size: 9pt; margin-top: 5px;">
                                Jl. Raya Telang, PO.Box. 2 Kamal, Bangkalan – Madura<br>
                                Telp : (031) 3011146, Fax. (031) 3011506<br>
                                Laman : www.trunojoyo.ac.id
                            </div>
                        </div>
                        <div style="clear: both;"></div>
                    </div>

                    {{-- Nomor Surat --}}
                    <div style="margin: 20px 0;">
                        <table style="width: 100%; font-size: 11pt;">
                            <tr>
                                <td style="width: 25%;">Nomor</td>
                                <td style="width: 2%;">:</td>
                                <td><strong>{{ $surat->Nomor_Surat ?? '[Nomor Surat]' }}</strong></td>
                            </tr>
                            <tr>
                                <td>Perihal</td>
                                <td>:</td>
                                <td><strong>Permohonan Izin Magang Mandiri</strong></td>
                            </tr>
                        </table>
                    </div>

                    {{-- Tanggal Surat --}}
                    <div style="text-align: right; margin: 20px 0 30px 0;">
                        {{ \Carbon\Carbon::now()->format('d F Y') }}
                    </div>

                    {{-- Kepada --}}
                    <div style="margin: 20px 0;">
                        <p style="margin: 0;">Yth. Pimpinan {{ $surat->Nama_Instansi ?? '[Nama Instansi]' }}</p>
                        <p style="margin: 0;">{{ $surat->Alamat_Instansi ?? '[Alamat Instansi]' }}</p>
                    </div>

                    {{-- Isi Surat --}}
                    <p style="text-align: justify; text-indent: 50px; margin: 20px 0; line-height: 1.8;">
                        Sehubungan dalam memperkenalkan mahasiswa pada dunia kerja sesuai bidang masing-masing, maka 
                        sesuai ketentuan Program Merdeka Belajar - Kampus Merdeka (MBKM) mahasiswa diperkenankan 
                        melaksanakan magang. Guna memperlancar kegiatan tersebut, kami mohon Bapak/Ibu untuk memberikan 
                        izin kepada mahasiswa kami untuk dapat melaksanakan kegiatan magang di perusahaan tersebut pada 
                        tanggal {{ $surat->Tanggal_Mulai ? \Carbon\Carbon::parse($surat->Tanggal_Mulai)->format('d F') : '[Tanggal Mulai]' }} s.d. 
                        {{ $surat->Tanggal_Selesai ? \Carbon\Carbon::parse($surat->Tanggal_Selesai)->format('d F Y') : '[Tanggal Selesai]' }}.
                    </p>

                    <p style="margin: 15px 0;">Adapun mahasiswa tersebut adalah:</p>

                    {{-- Tabel Mahasiswa --}}
                    <table style="width: 100%; border-collapse: collapse; margin: 15px 0;">
                        <thead>
                            <tr style="background-color: #f0f0f0;">
                                <th style="border: 1px solid #000; padding: 8px; text-align: center; width: 5%;">No</th>
                                <th style="border: 1px solid #000; padding: 8px; text-align: left; width: 40%;">Nama</th>
                                <th style="border: 1px solid #000; padding: 8px; text-align: left; width: 35%;">Program Studi</th>
                                <th style="border: 1px solid #000; padding: 8px; text-align: left; width: 20%;">No. WA</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dataMahasiswa as $idx => $mhs)
                            <tr>
                                <td style="border: 1px solid #000; padding: 8px; text-align: center;">{{ $idx + 1 }}.</td>
                                <td style="border: 1px solid #000; padding: 8px;">
                                    <strong>{{ $mhs['nama'] ?? '' }}</strong><br>
                                    <small>NIM {{ $mhs['nim'] ?? '' }}</small>
                                </td>
                                <td style="border: 1px solid #000; padding: 8px;">
                                    @php
                                        $prodiName = $surat->tugasSurat?->pemberiTugas?->mahasiswa?->prodi?->Nama_Prodi 
                                            ?? ($mhs['program-studi'] ?? $mhs['jurusan'] ?? 'Teknik Industri');
                                    @endphp
                                    {{ $prodiName }}
                                </td>
                                <td style="border: 1px solid #000; padding: 8px;">{{ $mhs['no_wa'] ?? '-' }}</td>
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

                    {{-- Tanda Tangan Dekan --}}
                    <div style="margin-top: 50px;">
                        <table style="width: 100%;">
                            <tr>
                                <td style="width: 50%; vertical-align: top;">
                                    {{-- Kosong --}}
                                </td>
                                <td style="width: 50%; text-align: center; vertical-align: top;">
                                    <p style="margin: 0 0 5px 0;">Dekan Fakultas Teknik,</p>
                                    
                                    @if($surat->Acc_Dekan && $surat->Qr_code_dekan)
                                        {{-- Tampilkan QR Code jika sudah disetujui --}}
                                        <div style="margin: 10px 0;">
                                            <img src="{{ asset('storage/' . $surat->Qr_code_dekan) }}" 
                                                 alt="QR Code Dekan" 
                                                 style="width: 100px; height: 100px; display: inline-block;">
                                        </div>
                                    @else
                                        {{-- Placeholder jika belum disetujui --}}
                                        <div style="height: 100px;"></div>
                                    @endif
                                    
                                    @php
                                        // Ambil Dekan dari fakultas mahasiswa yang mengajukan
                                        $mahasiswaPengaju = $surat->tugasSurat?->pemberiTugas?->mahasiswa;
                                        $fakultas = $mahasiswaPengaju?->prodi?->fakultas;
                                        
                                        // Ambil data Dekan langsung dari Id_Dekan di tabel Fakultas
                                        $dekan = null;
                                        if ($fakultas && $fakultas->Id_Dekan) {
                                            $dekan = \App\Models\Dosen::find($fakultas->Id_Dekan);
                                        }
                                        
                                        $namaDekan = $dekan?->Nama_Dosen ?? 'Dr. Budi Hartono, S.Kom., M.Kom.';
                                        $nipDekan = $dekan?->NIP ?? '198503152010121001';
                                    @endphp
                                    <p style="margin: 0;"><strong><u>{{ $namaDekan }}</u></strong></p>
                                    <p style="margin: 0;">NIP {{ $nipDekan }}</p>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="text-center mt-3">
                    <button type="button" class="btn btn-secondary" onclick="togglePreviewSurat()">
                        <i class="fas fa-times"></i> Tutup Preview
                    </button>
                    <button type="button" class="btn btn-primary" onclick="window.print()">
                        <i class="fas fa-print"></i> Cetak Surat
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Form Persetujuan --}}
    <div class="col-lg-4">
        <div class="card shadow mb-4 border-success">
            <div class="card-header py-3 bg-success text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-check-circle me-2"></i>Keputusan Dekan
                </h6>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <small>Setelah disetujui, QR Code akan digenerate dan status berubah menjadi <strong>"Success"</strong></small>
                </div>

                @if($surat->Acc_Dekan && $surat->Status === 'Success')
                    {{-- Surat sudah disetujui --}}
                    <div class="alert alert-success">
                        <h6 class="alert-heading fw-bold"><i class="fas fa-check-circle me-2"></i>Surat Telah Disetujui</h6>
                        <hr>
                        <p class="mb-0 small">Surat ini telah Anda setujui dan ditandatangani secara digital dengan QR Code.</p>
                        @if($surat->Qr_code_dekan)
                            <div class="text-center mt-3">
                                <img src="{{ asset('storage/' . $surat->Qr_code_dekan) }}" alt="QR Code Dekan" style="width: 150px; height: 150px; border: 2px solid #198754; padding: 10px; background: white;">
                                <p class="mt-2 mb-0 small text-muted">QR Code Tanda Tangan Digital Dekan</p>
                            </div>
                        @endif
                    </div>
                @else
                    {{-- Form Persetujuan --}}
                <form action="{{ route('dekan.surat_magang.approve', $surat->id_no) }}" method="POST">
                    @csrf
                    <div class="d-grid gap-2 mb-3">
                        <button type="submit" class="btn btn-success btn-lg" onclick="return confirm('Apakah Anda yakin ingin menyetujui surat ini? QR Code akan digenerate otomatis.')">
                            <i class="fas fa-check-circle me-2"></i>SETUJUI & TTD
                        </button>
                    </div>
                </form>

                <button type="button" class="btn btn-outline-danger w-100" data-bs-toggle="modal" data-bs-target="#rejectModal">
                    <i class="fas fa-times-circle me-2"></i>TOLAK
                </button>
                @endif

                <div class="alert alert-warning mt-3 mb-0 small">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    Menyetujui akan membubuhkan Tanda Tangan Digital (QR Code) pada surat.
                </div>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-secondary">
                    <i class="fas fa-clipboard-list me-2"></i>Status Surat
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <small class="text-muted">Status Saat Ini:</small>
                    <br>
                    <span class="badge bg-warning">
                        <i class="fas fa-clock me-1"></i> {{ $surat->Status }}
                    </span>
                </div>
                <div class="mb-2">
                    <small class="text-muted">Acc Koordinator:</small>
                    <br>
                    @if($surat->Acc_Koordinator)
                        <span class="badge bg-success"><i class="fas fa-check"></i> Disetujui</span>
                    @else
                        <span class="badge bg-secondary"><i class="fas fa-clock"></i> Belum</span>
                    @endif
                </div>
                <div>
                    <small class="text-muted">Acc Dekan:</small>
                    <br>
                    @if($surat->Acc_Dekan)
                        <span class="badge bg-success"><i class="fas fa-check"></i> Disetujui</span>
                    @else
                        <span class="badge bg-warning"><i class="fas fa-clock"></i> Menunggu</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Tolak --}}
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold"><i class="fas fa-times-circle me-2"></i>Tolak Pengajuan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('dekan.surat_magang.reject', $surat->id_no) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Anda akan menolak pengajuan surat magang ini.</p>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="komentar" rows="4" placeholder="Contoh: Dokumen kurang lengkap, format salah, dll..." required minlength="10"></textarea>
                        <div class="form-text">Alasan ini akan dikirimkan kepada mahasiswa.</div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger fw-bold">Tolak Pengajuan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function togglePreviewSurat() {
    var preview = document.getElementById('previewSurat');
    if (preview.style.display === 'none') {
        preview.style.display = 'block';
        preview.scrollIntoView({ behavior: 'smooth', block: 'start' });
    } else {
        preview.style.display = 'none';
    }
}
</script>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    #previewSurat, #previewSurat * {
        visibility: visible;
    }
    #previewSurat {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
    #previewSurat .text-center.mt-3 {
        display: none !important;
    }
    #previewSurat.card {
        border: none !important;
        box-shadow: none !important;
    }
    .card-header {
        display: none !important;
    }
}
</style>

@endsection
