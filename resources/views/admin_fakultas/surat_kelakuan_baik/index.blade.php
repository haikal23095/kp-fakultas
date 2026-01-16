@extends('layouts.admin_fakultas')

@section('title', 'Manajemen Surat Berkelakuan Baik')

@section('content')
<div class="container-fluid">
    {{-- Page Heading --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800">Surat Keterangan Berkelakuan Baik</h1>
            <p class="text-muted small mb-0">Kelola pengajuan surat keterangan berkelakuan baik mahasiswa</p>
        </div>
        <a href="{{ route('admin_fakultas.surat.manage') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>

    {{-- Alert Messages --}}
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

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Pengajuan Masuk</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 5%">No</th>
                            <th>Tanggal</th>
                            <th>Mahasiswa</th>
                            <th>Prodi</th>
                            <th>Semester/TA</th>
                            <th>Keperluan</th>
                            <th>Nomor Surat</th>
                            <th>Status</th>
                            <th class="text-center" style="width: 20%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($daftarPengajuan as $index => $tugas)
                            @php
                                $mhs = $tugas->pemberiTugas->mahasiswa ?? null;
                                $detail = $tugas->suratKelakuanBaik;
                                $statusClass = match(strtolower($tugas->Status)) {
                                    'baru', 'pending' => 'bg-warning text-dark',
                                    'selesai', 'success' => 'bg-success text-white',
                                    'ditolak' => 'bg-danger text-white',
                                    default => 'bg-secondary text-white'
                                };
                            @endphp
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ \Carbon\Carbon::parse($tugas->Tanggal_Diberikan_Tugas_Surat)->format('d M Y') }}</td>
                                <td>
                                    <div class="fw-bold">{{ $mhs->Nama_Mahasiswa ?? 'User Unknown' }}</div>
                                    <small class="text-muted">{{ $mhs->NIM ?? '-' }}</small>
                                </td>
                                <td>
                                    <small>{{ $mhs->prodi->Nama_Prodi ?? '-' }}</small>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $detail->Semester ?? '-' }} / {{ $detail->Tahun_Akademik ?? '-' }}</small>
                                </td>
                                <td>{{ \Illuminate\Support\Str::limit($detail->Keperluan ?? '-', 40) }}</td>
                                <td>
                                    @if($detail->Nomor_Surat)
                                        <span class="badge bg-info">{{ $detail->Nomor_Surat }}</span>
                                    @else
                                        <span class="badge bg-secondary">Belum ada</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $statusClass }}">{{ ucfirst($tugas->Status) }}</span>
                                </td>
                                <td class="text-center">
                                    @if(!$detail->Nomor_Surat)
                                        {{-- Tombol Beri Nomor Surat --}}
                                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalNomor{{ $tugas->Id_Tugas_Surat }}" title="Beri Nomor Surat">
                                            <i class="fas fa-hashtag"></i> Nomor
                                        </button>
                                    @elseif(strtolower($tugas->Status) == 'baru' || strtolower($tugas->Status) == 'pending')
                                        {{-- Tombol Kirim ke Wadek3 --}}
                                        <form action="{{ route('admin_fakultas.surat.kelakuan_baik.kirim_wadek3', $tugas->Id_Tugas_Surat) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Kirim surat ke Wadek3 untuk ditandatangani?')" title="Kirim ke Wadek3">
                                                <i class="fas fa-paper-plane"></i> Kirim
                                            </button>
                                        </form>
                                    @endif
                                    
                                    <a href="{{ route('admin_fakultas.surat.detail', $tugas->Id_Tugas_Surat) }}" class="btn btn-info btn-sm" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>

                            {{-- Modal Beri Nomor Surat --}}
                            <div class="modal fade" id="modalNomor{{ $tugas->Id_Tugas_Surat }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header bg-warning text-white">
                                            <h5 class="modal-title">Beri Nomor Surat</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="{{ route('admin_fakultas.surat.kelakuan_baik.beri_nomor', $tugas->Id_Tugas_Surat) }}" method="POST">
                                            @csrf
                                            <div class="modal-body">
                                                <p><strong>Mahasiswa:</strong> {{ $mhs->Nama_Mahasiswa ?? '-' }} ({{ $mhs->NIM ?? '-' }})</p>
                                                <p><strong>Keperluan:</strong> {{ $detail->Keperluan ?? '-' }}</p>
                                                <hr>
                                                <div class="mb-3">
                                                    <label class="form-label">Nomor Surat <span class="text-danger">*</span></label>
                                                    <input type="text" name="nomor_surat" class="form-control" placeholder="Contoh: 001/UN46.2/KM/2026" required>
                                                    <small class="text-muted">Format: nomor/UN46.2/KM/tahun</small>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-warning">Simpan Nomor</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">Belum ada pengajuan surat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
