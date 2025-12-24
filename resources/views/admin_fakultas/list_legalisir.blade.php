@extends('layouts.admin_fakultas')

@section('title', 'Daftar Pengajuan Legalisir')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-1 text-gray-800">Daftar Pengajuan Legalisir</h1>
        <p class="text-muted small mb-0">Kelola pengajuan legalisir ijazah dan transkrip nilai</p>
    </div>
    <a href="{{ route('admin_fakultas.surat.manage') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Kembali
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert" style="border-radius: 12px; border-left: 4px solid #1cc88a;">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert" style="border-radius: 12px; border-left: 4px solid #e74a3b;">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card shadow mb-4" style="border-radius: 12px; border: none;">
    <div class="card-header py-3" style="background: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%); border-radius: 12px 12px 0 0;">
        <h6 class="m-0 font-weight-bold text-white">
            <i class="fas fa-stamp me-2"></i>Tabel Pengajuan Legalisir
        </h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" width="100%" cellspacing="0">
                <thead style="background-color: #f8f9fc;">
                    <tr>
                        <th>No</th>
                        <th>Nama Mahasiswa</th>
                        <th>Jenis Dokumen</th>
                        <th>Jumlah</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($daftarSurat as $index => $surat)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <div class="fw-bold">{{ $surat->user->Name_User ?? 'N/A' }}</div>
                        </td>
                        <td>{{ $surat->Jenis_Dokumen }}</td>
                        <td>{{ $surat->Jumlah_Salinan }}</td>
                        <td>{{ \Carbon\Carbon::parse($surat->tugasSurat->Tanggal_Diberikan_Tugas_Surat)->format('d M Y') }}</td>
                        <td class="text-center">
                            @php
                                $status = $surat->Status ?? 'pending';
                                $badgeClass = 'secondary';
                                $icon = 'circle';
                                
                                switch(strtolower($status)) {
                                    case 'selesai':
                                    case 'siap_diambil':
                                        $badgeClass = 'success';
                                        $icon = 'check';
                                        break;
                                    case 'ditolak':
                                        $badgeClass = 'danger';
                                        $icon = 'times';
                                        break;
                                    case 'menunggu_pembayaran':
                                    case 'menunggu_ttd_pimpinan':
                                        $badgeClass = 'warning';
                                        $icon = 'clock';
                                        break;
                                    case 'pembayaran_lunas':
                                    case 'proses_stempel_paraf':
                                        $badgeClass = 'info';
                                        $icon = 'spinner fa-spin';
                                        break;
                                    default:
                                        $badgeClass = 'secondary';
                                        $icon = 'circle';
                                }
                            @endphp
                            <span class="badge rounded-pill bg-{{ $badgeClass }} px-3 py-2">
                                <i class="fas fa-{{ $icon }} me-1"></i>
                                {{ ucfirst(str_replace('_', ' ', $status)) }}
                            </span>
                        </td>
                        <td>
                            {{-- Tombol Aksi Berdasarkan Status --}}
                            @if($surat->Status == 'pending')
                                <form action="{{ route('admin_fakultas.surat_legalisir.verifikasi', $surat->id_no) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-primary" onclick="return confirm('Verifikasi berkas ini?')">
                                        <i class="fas fa-check me-1"></i>Verifikasi
                                    </button>
                                </form>
                            @elseif($surat->Status == 'menunggu_pembayaran')
                                <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#modalBayar{{ $surat->id_no }}">
                                    <i class="fas fa-money-bill me-1"></i>Konfirmasi Bayar
                                </button>
                                
                                {{-- Modal Pembayaran --}}
                                <div class="modal fade" id="modalBayar{{ $surat->id_no }}" tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content" style="border-radius: 12px;">
                                            <form action="{{ route('admin_fakultas.surat_legalisir.bayar', $surat->id_no) }}" method="POST">
                                                @csrf
                                                <div class="modal-header" style="background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%); border-radius: 12px 12px 0 0;">
                                                    <h5 class="modal-title text-white">
                                                        <i class="fas fa-money-bill-wave me-2"></i>Konfirmasi Pembayaran
                                                    </h5>
                                                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label class="fw-bold">Masukkan Nominal Biaya (Rp)</label>
                                                        <input type="number" name="biaya" class="form-control" required min="0" placeholder="Contoh: 50000">
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                                        <i class="fas fa-times me-1"></i>Batal
                                                    </button>
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="fas fa-save me-1"></i>Simpan
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @elseif(in_array($surat->Status, ['pembayaran_lunas', 'proses_stempel_paraf', 'menunggu_ttd_pimpinan', 'siap_diambil']))
                                <form action="{{ route('admin_fakultas.surat_legalisir.progress', $surat->id_no) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-info" onclick="return confirm('Lanjutkan ke tahap berikutnya?')">
                                        <i class="fas fa-arrow-right me-1"></i>Lanjut Proses
                                    </button>
                                </form>
                            @endif
                            
                            <a href="{{ Storage::url($surat->Path_File) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-file-pdf me-1"></i>Lihat PDF
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
