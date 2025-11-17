@extends('layouts.kaprodi')

@section('title', 'Permintaan Pengantar KP/Magang')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-0">Permintaan Pengantar KP/Magang</h1>
        <p class="mb-0 text-muted">Daftar surat pengantar magang yang menunggu persetujuan Anda</p>
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

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Permintaan Pengantar KP/Magang - Menunggu Persetujuan</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Tgl. Masuk</th>
                        <th>Pengaju</th>
                        <th>NIM</th>
                        <th>Jenis Surat</th>
                        <th>Nama Instansi</th>
                        <th>Periode Magang</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($daftarSurat as $index => $surat)
                    @php
                        $mahasiswa = $surat->tugasSurat->pemberiTugas->mahasiswa ?? null;
                        $dataMahasiswa = $surat->Data_Mahasiswa;
                        $namaMahasiswa = $mahasiswa?->Nama_Mahasiswa ?? ($dataMahasiswa[0]['nama'] ?? 'N/A');
                        $nimMahasiswa = $mahasiswa?->NIM ?? ($dataMahasiswa[0]['nim'] ?? 'N/A');
                    @endphp
                    <tr>
                        {{-- 1. No --}}
                        <td>{{ $index + 1 }}</td>

                        {{-- 2. Tgl. Masuk --}}
                        <td>
                            @if($surat->tugasSurat && $surat->tugasSurat->Tanggal_Diberikan_Tugas_Surat)
                                {{ \Carbon\Carbon::parse($surat->tugasSurat->Tanggal_Diberikan_Tugas_Surat)->format('d M Y') }}
                            @else
                                -
                            @endif
                        </td>

                        {{-- 3. Pengaju --}}
                        <td>{{ $namaMahasiswa }}</td>

                        {{-- 4. NIM --}}
                        <td>{{ $nimMahasiswa }}</td>

                        {{-- 5. Jenis Surat --}}
                        <td>
                            {{ $surat->tugasSurat->jenisSurat->Nama_Surat ?? 'Surat Pengantar Magang' }}
                        </td>

                        {{-- 6. Nama Instansi --}}
                        <td>{{ $surat->Nama_Instansi ?? '-' }}</td>

                        {{-- 7. Periode Magang --}}
                        <td>
                            @if($surat->Tanggal_Mulai && $surat->Tanggal_Selesai)
                                {{ \Carbon\Carbon::parse($surat->Tanggal_Mulai)->format('d M Y') }} - 
                                {{ \Carbon\Carbon::parse($surat->Tanggal_Selesai)->format('d M Y') }}
                            @else
                                -
                            @endif
                        </td>

                        {{-- 8. Status --}}
                        <td class="text-center">
                            @if($surat->Acc_Koordinator)
                                <span class="badge bg-success">Disetujui</span>
                            @else
                                <span class="badge bg-warning text-dark">Menunggu Persetujuan</span>
                            @endif
                        </td>

                        {{-- 9. Aksi --}}
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#detailModal{{ $surat->id_no }}">
                                <i class="fas fa-eye"></i> Detail
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Tidak ada surat yang menunggu persetujuan.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modals - Outside the table --}}
@foreach($daftarSurat as $index => $surat)
@php
    $mahasiswa = $surat->tugasSurat->pemberiTugas->mahasiswa ?? null;
    $dataMahasiswa = $surat->Data_Mahasiswa;
    $namaMahasiswa = $mahasiswa?->Nama_Mahasiswa ?? ($dataMahasiswa[0]['nama'] ?? 'N/A');
    $nimMahasiswa = $mahasiswa?->NIM ?? ($dataMahasiswa[0]['nim'] ?? 'N/A');
@endphp

                    {{-- Modal Detail --}}
                    <div class="modal fade" id="detailModal{{ $surat->id_no }}" tabindex="-1" aria-labelledby="detailModalLabel{{ $surat->id_no }}" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="detailModalLabel{{ $surat->id_no }}">Detail Surat Pengantar Magang</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row mb-3">
                                        <div class="col-md-4 fw-bold">Tanggal Pengajuan:</div>
                                        <div class="col-md-8">
                                            @if($surat->tugasSurat && $surat->tugasSurat->Tanggal_Diberikan_Tugas_Surat)
                                                {{ \Carbon\Carbon::parse($surat->tugasSurat->Tanggal_Diberikan_Tugas_Surat)->format('d M Y H:i') }}
                                            @else
                                                -
                                            @endif
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-4 fw-bold">Nama Mahasiswa:</div>
                                        <div class="col-md-8">{{ $namaMahasiswa }}</div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-4 fw-bold">NIM:</div>
                                        <div class="col-md-8">{{ $nimMahasiswa }}</div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-4 fw-bold">Prodi:</div>
                                        <div class="col-md-8">{{ $mahasiswa?->prodi->Nama_Prodi ?? 'N/A' }}</div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-4 fw-bold">Nama Instansi:</div>
                                        <div class="col-md-8">{{ $surat->Nama_Instansi ?? '-' }}</div>
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
                                        <div class="col-md-4 fw-bold">Koordinator KP:</div>
                                        <div class="col-md-8">{{ $surat->Nama_Koordinator_KP ?? '-' }}</div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-4 fw-bold">Foto Tanda Tangan:</div>
                                        <div class="col-md-8">
                                            @if($surat->Foto_ttd && !empty(trim($surat->Foto_ttd)))
                                                <div class="mb-2">
                                                    <img src="{{ asset('storage/' . $surat->Foto_ttd) }}" 
                                                         alt="Tanda Tangan" 
                                                         style="max-height: 80px; border: 1px solid #ddd; padding: 5px;"
                                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                                    <div style="display: none; color: red;">
                                                        <i class="fas fa-exclamation-triangle"></i> Gambar tidak dapat dimuat
                                                    </div>
                                                </div>
                                                <small class="text-muted">Path: {{ $surat->Foto_ttd }}</small>
                                            @else
                                                <span class="text-muted">Tidak ada foto tanda tangan</span>
                                            @endif
                                        </div>
                                    </div>

                                    @if($surat->Data_Dosen_pembiming)
                                    <div class="row mb-3">
                                        <div class="col-md-4 fw-bold">Dosen Pembimbing:</div>
                                        <div class="col-md-8">
                                            @php
                                                $dosenPembimbing = is_array($surat->Data_Dosen_pembiming) 
                                                    ? $surat->Data_Dosen_pembiming 
                                                    : json_decode($surat->Data_Dosen_pembiming, true);
                                            @endphp
                                            @if($dosenPembimbing)
                                                @if(isset($dosenPembimbing['dosen_pembimbing_1']))
                                                    <div>1. {{ $dosenPembimbing['dosen_pembimbing_1'] }}</div>
                                                @endif
                                                @if(isset($dosenPembimbing['dosen_pembimbing_2']) && $dosenPembimbing['dosen_pembimbing_2'])
                                                    <div>2. {{ $dosenPembimbing['dosen_pembimbing_2'] }}</div>
                                                @endif
                                            @endif
                                            <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="togglePreview{{ $surat->id_no }}()">
                                                <i class="fas fa-file-alt"></i> Form Surat Pengantar
                                            </button>
                                        </div>
                                    </div>
                                    @endif

                                    {{-- Preview Surat Pengantar (Hidden by default) --}}
                                    <div id="previewSurat{{ $surat->id_no }}" style="display: none;" class="mt-4">
                                        <hr>
                                        <h6 class="text-center mb-3"><i class="fas fa-eye me-1"></i> Preview Surat Pengantar</h6>
                                        
                                        <div class="preview-document" style="border: 1px solid #ddd; padding: 20px; background: white; font-family: 'Times New Roman', serif; font-size: 11pt; line-height: 1.6; max-width: 700px; margin: 0 auto;">
                                            {{-- Header --}}
                                            <div style="text-align: center; margin-bottom: 20px; border-bottom: 3px solid #000; padding-bottom: 10px;">
                                                <img src="{{ asset('images/logo_unijoyo.png') }}" alt="Logo UTM" style="height: 60px; float: left;">
                                                <div style="margin-left: 70px;">
                                                    <strong style="display: block; font-size: 11pt;">KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, RISET, DAN TEKNOLOGI</strong>
                                                    <strong style="display: block; font-size: 12pt;">UNIVERSITAS TRUNOJOYO MADURA</strong>
                                                    <strong style="display: block; font-size: 13pt;">FAKULTAS TEKNIK</strong>
                                                    <div style="font-size: 9pt; margin-top: 5px;">
                                                        Sekretariat: Kampus Unijoyo PO Box 2 Telang Kamal Telp 031 7011147 Fax. 031 7011506
                                                    </div>
                                                </div>
                                                <div style="clear: both;"></div>
                                            </div>

                                            {{-- Judul --}}
                                            <p style="text-align: center; font-weight: bold; margin: 20px 0; text-decoration: underline;">FORM PENGAJUAN SURAT PENGANTAR</p>

                                            {{-- Tabel Data --}}
                                            <table style="width: 100%; margin-bottom: 15px; border-collapse: collapse;">
                                                <tr>
                                                    <td style="width: 30%; vertical-align: top; padding: 3px 0;">Nama</td>
                                                    <td style="width: 5%; vertical-align: top; padding: 3px 0;">:</td>
                                                    <td style="padding: 3px 0;">
                                                        @foreach($dataMahasiswa as $idx => $mhs)
                                                        <div style="margin-bottom: 5px;">
                                                            <strong>{{ $idx + 1 }}. {{ $mhs['nama'] ?? '' }}</strong><br>
                                                            <small>NIM: {{ $mhs['nim'] ?? '' }} | Semester: {{ $mhs['semester'] ?? '-' }}</small>
                                                        </div>
                                                        @endforeach
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="padding: 3px 0;">Jurusan</td>
                                                    <td style="padding: 3px 0;">:</td>
                                                    <td style="padding: 3px 0;">{{ $mahasiswa?->prodi->Nama_Prodi ?? 'N/A' }}</td>
                                                </tr>
                                                <tr>
                                                    <td style="padding: 3px 0;">Dosen Pembimbing</td>
                                                    <td style="padding: 3px 0;">:</td>
                                                    <td style="padding: 3px 0;">{{ $dosenPembimbing['dosen_pembimbing_1'] ?? '-' }}</td>
                                                </tr>
                                                @if(isset($dosenPembimbing['dosen_pembimbing_2']) && $dosenPembimbing['dosen_pembimbing_2'])
                                                <tr>
                                                    <td style="padding: 3px 0;">Dosen Pembimbing 2</td>
                                                    <td style="padding: 3px 0;">:</td>
                                                    <td style="padding: 3px 0;">{{ $dosenPembimbing['dosen_pembimbing_2'] }}</td>
                                                </tr>
                                                @endif
                                                <tr>
                                                    <td style="vertical-align: top; padding: 3px 0;">Surat Pengantar*</td>
                                                    <td style="vertical-align: top; padding: 3px 0;">:</td>
                                                    <td style="padding: 3px 0;">
                                                        1. Pengantar Kerja Praktek<br>
                                                        2. Pengantar TA<br>
                                                        3. Pengantar Dosen Pembimbing I TA<br>
                                                        4. Magang
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="padding: 3px 0;">Instansi/Perusahaan</td>
                                                    <td style="padding: 3px 0;">:</td>
                                                    <td style="padding: 3px 0;">{{ $surat->Nama_Instansi ?? '-' }}</td>
                                                </tr>
                                            </table>

                                            {{-- Bagian Khusus Magang --}}
                                            <div style="margin-top: 15px;">
                                                <strong><u>Isian berikut utk pengantar Magang</u></strong>
                                                <table style="width: 100%; margin-top: 5px; border-collapse: collapse;">
                                                    <tr>
                                                        <td style="width: 30%; padding: 3px 0;">Judul Penelitian</td>
                                                        <td style="width: 5%; padding: 3px 0;">:</td>
                                                        <td style="padding: 3px 0;">-</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="padding: 3px 0;">Jangka waktu penelitian</td>
                                                        <td style="padding: 3px 0;">:</td>
                                                        <td style="padding: 3px 0;">
                                                            @if($surat->Tanggal_Mulai && $surat->Tanggal_Selesai)
                                                                {{ \Carbon\Carbon::parse($surat->Tanggal_Mulai)->format('d M Y') }} s/d 
                                                                {{ \Carbon\Carbon::parse($surat->Tanggal_Selesai)->format('d M Y') }}
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="padding: 3px 0;">Identitas Surat Balasan**</td>
                                                        <td style="padding: 3px 0;">:</td>
                                                        <td style="padding: 3px 0;"></td>
                                                    </tr>
                                                </table>
                                            </div>

                                            {{-- Tanda Tangan --}}
                                            <div style="margin-top: 30px;">
                                                <table style="width: 100%;">
                                                    <tr>
                                                        <td style="width: 50%; vertical-align: top;">
                                                            <p style="margin: 0 0 5px 0;">Menyetujui<br>Koordinator KP/TA</p>
                                                            <div style="height: 60px;"></div>
                                                            <p style="margin: 0;">( {{ $surat->Nama_Koordinator_KP ?? '[Nama Kaprodi]' }} )</p>
                                                            <p style="margin: 0;">NIP. {{ $kaprodiNIP ?? '...' }}</p>
                                                        </td>
                                                        <td style="width: 50%; text-align: center; vertical-align: top;">
                                                            <p style="margin: 0 0 5px 0;">Bangkalan, {{ \Carbon\Carbon::now()->format('d M Y') }}</p>
                                                            <p style="margin: 0 0 5px 0;">Pemohon</p>
                                                            @if($surat->Foto_ttd && !empty(trim($surat->Foto_ttd)))
                                                                @php
                                                                    // Debug: Check if file exists
                                                                    $ttdPath = $surat->Foto_ttd;
                                                                    $fullPath = storage_path('app/public/' . $ttdPath);
                                                                    $fileExists = file_exists($fullPath);
                                                                @endphp
                                                                <img src="{{ asset('storage/' . $surat->Foto_ttd) }}" 
                                                                     alt="TTD" 
                                                                     style="max-height: 60px; max-width: 150px; display: block; margin: 0 auto;"
                                                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                                                <small style="display: none; color: red;">Gambar tidak ditemukan</small>
                                                            @else
                                                            <div style="height: 60px;"></div>
                                                            @endif
                                                            <p style="margin: 0;">( {{ $namaMahasiswa }} )</p>
                                                            <p style="margin: 5px 0 0 0;">NIM. {{ $nimMahasiswa }}</p>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>

                                            <hr style="border-top: 1px dashed #000; margin-top: 15px;">
                                            <small style="font-size: 9pt;">
                                                Cat: *Tulis alamat Instansi/perusahaan yg dituju<br>
                                                **Diisi untuk permohonan kedua dan seterusnya
                                            </small>
                                        </div>
                                    </div>

                                    @if($surat->Dokumen_Proposal)
                                    <div class="row mb-3">
                                        <div class="col-md-4 fw-bold">Dokumen Proposal:</div>
                                        <div class="col-md-8">
                                            <a href="{{ route('kaprodi.surat.download', $surat->id_no) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-download"></i> Unduh Proposal
                                            </a>
                                        </div>
                                    </div>
                                    @endif

                                    {{-- Mahasiswa yang ikut (jika lebih dari 1) --}}
                                    @if(count($dataMahasiswa) > 1)
                                    <div class="row mb-3">
                                        <div class="col-md-4 fw-bold">Mahasiswa yang Ikut:</div>
                                        <div class="col-md-8">
                                            <ol class="mb-0">
                                                @foreach($dataMahasiswa as $mhs)
                                                <li>{{ $mhs['nama'] ?? '' }} ({{ $mhs['nim'] ?? '' }}) - Semester {{ $mhs['semester'] ?? '' }}</li>
                                                @endforeach
                                            </ol>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                    @if(!$surat->Acc_Koordinator)
                                    <form action="{{ route('kaprodi.surat.reject', $surat->id_no) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menolak surat ini? Status Tugas akan diubah menjadi Ditolak.')">
                                            <i class="fas fa-times"></i> Tolak
                                        </button>
                                    </form>
                                    <form action="{{ route('kaprodi.surat.approve', $surat->id_no) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success" onclick="return confirm('Apakah Anda yakin ingin menyetujui surat ini?')">
                                            <i class="fas fa-check"></i> Setujui
                                        </button>
                                    </form>
                                    @else
                                    <span class="badge bg-success">Surat ini sudah disetujui</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <script>
                    function togglePreview{{ $surat->id_no }}() {
                        var preview = document.getElementById('previewSurat{{ $surat->id_no }}');
                        if (preview.style.display === 'none') {
                            preview.style.display = 'block';
                        } else {
                            preview.style.display = 'none';
                        }
                    }
                    </script>
@endforeach

@endsection
