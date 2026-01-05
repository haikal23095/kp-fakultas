@extends('layouts.mahasiswa')

@section('title', $title ?? 'Riwayat Surat Dispensasi')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800">{{ $title ?? 'Riwayat Surat Dispensasi' }}</h1>
            <p class="text-muted mb-0">Daftar pengajuan surat dispensasi yang telah Anda ajukan</p>
        </div>
        <a href="{{ route('mahasiswa.riwayat') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>

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

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Riwayat</h6>
            <a href="{{ route('mahasiswa.pengajuan.dispen.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-2"></i>Ajukan Dispensasi Baru
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Tanggal Pengajuan</th>
                            <th>Nomor Surat</th>
                            <th>Keperluan</th>
                            <th>Tanggal Kegiatan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($riwayatSurat as $index => $tugas)
                            @php
                                $surat = $tugas->suratDispensasi;
                                $verification = $tugas->verification;
                            @endphp
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ \Carbon\Carbon::parse($tugas->Tanggal_Diberikan_Tugas_Surat)->format('d M Y') }}</td>
                                <td>
                                    @if($surat && $surat->nomor_surat)
                                        <span class="badge bg-info text-dark">{{ $surat->nomor_surat }}</span>
                                    @else
                                        <span class="badge bg-secondary">Belum Terbit</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $surat->nama_kegiatan ?? 'Permohonan Surat Dispensasi' }}</div>
                                    @if($surat && $surat->instansi_penyelenggara)
                                        <small class="text-muted">{{ $surat->instansi_penyelenggara }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($surat)
                                        <small>
                                            {{ \Carbon\Carbon::parse($surat->tanggal_mulai)->format('d M Y') }} - 
                                            {{ \Carbon\Carbon::parse($surat->tanggal_selesai)->format('d M Y') }}
                                        </small>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($surat && $surat->acc_wadek3_by)
                                        <span class="badge bg-success">
                                            <i class="fas fa-check-circle me-1"></i>Selesai
                                        </span>
                                    @elseif($surat && $surat->nomor_surat)
                                        <span class="badge bg-warning text-dark">
                                            <i class="fas fa-clock me-1"></i>Menunggu ACC Wadek3
                                        </span>
                                    @elseif($surat && $surat->verifikasi_admin_by)
                                        <span class="badge bg-info">
                                            <i class="fas fa-hourglass-half me-1"></i>Diproses Admin
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            <i class="fas fa-paper-plane me-1"></i>Baru
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($surat && $surat->acc_wadek3_by && $surat->file_surat_selesai)
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-info btn-sm" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#previewModal{{ $tugas->Id_Tugas_Surat }}">
                                                <i class="fas fa-eye me-1"></i>Preview
                                            </button>
                                            <a href="{{ route('mahasiswa.download.dispensasi', $tugas->Id_Tugas_Surat) }}" 
                                               class="btn btn-success btn-sm" target="_blank">
                                                <i class="fas fa-download me-1"></i>Download
                                            </a>
                                        </div>
                                    @else
                                        <button class="btn btn-secondary btn-sm" disabled>
                                            <i class="fas fa-hourglass me-1"></i>Belum Selesai
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                    <p class="text-muted mb-0">Belum ada riwayat pengajuan surat dispensasi.</p>
                                    <a href="{{ route('mahasiswa.pengajuan.dispen.create') }}" class="btn btn-primary btn-sm mt-2">
                                        <i class="fas fa-plus me-2"></i>Ajukan Sekarang
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal Preview untuk setiap surat --}}
    @foreach($riwayatSurat as $tugas)
        @php
            $surat = $tugas->suratDispensasi;
            $mahasiswa = Auth::user()->mahasiswa;
            $verification = $tugas->verification;
        @endphp
        
        @if($surat && $surat->acc_wadek3_by)
        <div class="modal fade" id="previewModal{{ $tugas->Id_Tugas_Surat }}" tabindex="-1" aria-labelledby="previewModalLabel{{ $tugas->Id_Tugas_Surat }}" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="previewModalLabel{{ $tugas->Id_Tugas_Surat }}">
                            <i class="fas fa-file-alt me-2"></i>Preview Surat Dispensasi
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4" style="background: #f8f9fc;">
                        
                        {{-- Preview Surat dalam Box Putih (Mirip Kertas) --}}
                        <div class="surat-preview-container" style="background: white; padding: 40px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); max-width: 800px; margin: 0 auto;">
                            
                            {{-- Header Surat --}}
                            <div class="surat-header" style="text-align: center; border-bottom: 3px double #000; padding-bottom: 15px; margin-bottom: 20px; position: relative;">
                                <img src="{{ asset('images/logo_unijoyo.png') }}" 
                                     alt="Logo UTM" 
                                     style="height: 80px; position: absolute; left: 0; top: 0;">
                                
                                <div style="margin-left: 100px;">
                                    <div style="font-weight: bold; font-size: 14pt;">KEMENTERIAN PENDIDIKAN, KEBUDAYAAN,</div>
                                    <div style="font-weight: bold; font-size: 14pt;">RISET, DAN TEKNOLOGI</div>
                                    <div style="font-weight: bold; font-size: 14pt; margin-top: 5px;">UNIVERSITAS TRUNOJOYO MADURA</div>
                                    <div style="font-weight: bold; font-size: 16pt; margin-top: 5px;">FAKULTAS TEKNIK</div>
                                    <div style="font-size: 10pt; margin-top: 8px;">
                                        Jl. Raya Telang PO BOX 2 Kamal, Bangkalan - Madura<br>
                                        Telp : (031) 3011146, Fax. (031) 3011506<br>
                                        Laman : <span style="color: blue;">ft.trunojoyo.ac.id</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Nomor Surat --}}
                            <div class="surat-nomor" style="text-align: center; margin: 25px 0;">
                                <span style="font-weight: bold; font-size: 12pt;">
                                    Nomor: {{ $surat->nomor_surat ?? '-' }}
                                </span>
                            </div>

                            {{-- Judul Surat --}}
                            <div class="surat-judul" style="text-align: center; font-weight: bold; text-decoration: underline; font-size: 14pt; margin: 20px 0; text-transform: uppercase;">
                                SURAT DISPENSASI
                            </div>

                            {{-- Isi Pembuka --}}
                            <div class="surat-isi" style="text-align: justify; line-height: 1.8; font-size: 11pt;">
                                <p style="margin: 15px 0;">
                                    Yang bertanda tangan di bawah ini, Wakil Dekan III Bidang Kemahasiswaan Fakultas Teknik Universitas Trunojoyo Madura, dengan ini menerangkan bahwa:
                                </p>
                            </div>

                            {{-- Data Mahasiswa --}}
                            <table style="width: 100%; margin: 20px 0; font-size: 11pt;">
                                <tr>
                                    <td style="width: 30%; padding: 5px 0;">Nama</td>
                                    <td style="width: 5%;">:</td>
                                    <td style="width: 65%; font-weight: bold;">{{ $mahasiswa->Nama_Mahasiswa ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 5px 0;">NIM</td>
                                    <td>:</td>
                                    <td style="font-weight: bold;">{{ $mahasiswa->NIM ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 5px 0;">Program Studi</td>
                                    <td>:</td>
                                    <td>{{ $mahasiswa->prodi->Nama_Prodi ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 5px 0;">Fakultas</td>
                                    <td>:</td>
                                    <td>Fakultas Teknik</td>
                                </tr>
                            </table>

                            {{-- Isi Dispensasi --}}
                            <div class="surat-isi" style="text-align: justify; line-height: 1.8; font-size: 11pt;">
                                <p style="margin: 15px 0;">
                                    Mahasiswa tersebut memerlukan <strong>dispensasi perkuliahan</strong> pada tanggal 
                                    <strong>{{ \Carbon\Carbon::parse($surat->tanggal_mulai)->translatedFormat('d F Y') }}</strong> 
                                    sampai dengan 
                                    <strong>{{ \Carbon\Carbon::parse($surat->tanggal_selesai)->translatedFormat('d F Y') }}</strong>, 
                                    dikarenakan:
                                </p>
                                
                                <p style="margin: 15px 0 15px 40px;">
                                    <strong>{{ $surat->nama_kegiatan }}</strong>
                                </p>

                                @if($surat->instansi_penyelenggara && $surat->instansi_penyelenggara !== '-')
                                <p style="margin: 15px 0;">
                                    Yang diselenggarakan oleh <strong>{{ $surat->instansi_penyelenggara }}</strong>
                                    @if($surat->tempat_pelaksanaan && $surat->tempat_pelaksanaan !== '-')
                                        di <strong>{{ $surat->tempat_pelaksanaan }}</strong>
                                    @endif.
                                </p>
                                @endif

                                <p style="margin: 15px 0;">
                                    Demikian surat dispensasi ini dibuat untuk dapat dipergunakan sebagaimana mestinya. Atas perhatian dan kerjasamanya, kami ucapkan terima kasih.
                                </p>
                            </div>

                            {{-- Tanda Tangan & Stempel --}}
                            <div class="surat-ttd" style="margin-top: 40px; position: relative;">
                                <div style="float: right; width: 45%; text-align: center;">
                                    <p style="margin: 0;">Bangkalan, {{ \Carbon\Carbon::parse($surat->acc_wadek3_at ?? now())->translatedFormat('d F Y') }}</p>
                                    <p style="margin: 5px 0 10px 0; font-weight: bold;">Wakil Dekan III,</p>
                                    
                                    {{-- QR Code & Stempel --}}
                                    <div style="position: relative; margin: 20px auto; height: 140px;">
                                        {{-- QR Code dulu (di bawah) --}}
                                        @if($verification && $verification->qr_path && file_exists(storage_path('app/public/' . $verification->qr_path)))
                                            <img src="{{ asset('storage/' . $verification->qr_path) }}" 
                                                 alt="QR Code" 
                                                 style="width: 100px; height: 100px; position: absolute; left: 50%; transform: translateX(-50%); top: 10px; z-index: 1;">
                                        @endif
                                        {{-- Stempel di atas (numpuk) --}}
                                        @if(file_exists(public_path('images/stempel.png')))
                                            <img src="{{ asset('images/stempel.png') }}" 
                                                 alt="Stempel" 
                                                 style="opacity: 0.75; width: 140px; position: absolute; left: 50%; transform: translateX(-50%); top: 0px; z-index: 2;">
                                        @endif
                                    </div>
                                    
                                    @php
                                        $wadek3 = $surat->accWadek3;
                                    @endphp
                                    <p style="margin: 10px 0 0 0; font-weight: bold; text-decoration: underline;">
                                        {{ $wadek3->Name_User ?? '-' }}
                                    </p>
                                    <p style="margin: 0;">NIP. {{ $wadek3->dosen->NIP ?? $wadek3->pegawaiFakultas->NIP ?? '-' }}</p>
                                </div>
                                <div style="clear: both;"></div>
                            </div>

                            {{-- Footer Note --}}
                            <div style="margin-top: 60px; font-size: 9pt; font-style: italic; color: #666;">
                                <p><em>Catatan: Surat ini dicetak secara otomatis melalui Sistem Manajemen Surat Fakultas Teknik UTM</em></p>
                            </div>

                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Tutup
                        </button>
                        <a href="{{ route('mahasiswa.download.dispensasi', $tugas->Id_Tugas_Surat) }}" 
                           class="btn btn-success" target="_blank">
                            <i class="fas fa-download me-2"></i>Download PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endif
    @endforeach

</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#dataTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json',
        },
        order: [[1, 'desc']],
    });
});
</script>
@endpush
