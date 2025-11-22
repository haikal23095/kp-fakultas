@extends('layouts.dekan')

@section('title', 'Persetujuan Tanda Tangan Elektronik')

@section('content')
    <h1 class="h3 fw-bold mb-4">Persetujuan TTE Surat</h1>

    {{-- Flash Messages --}}
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

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 fw-bold text-primary">Antrian Surat Menunggu Tanda Tangan Anda</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th scope="col">Jenis Surat</th>
                            <th scope="col">Pemohon</th>
                            <th scope="col">Tanggal Diajukan</th>
                            <th scope="col" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($daftarSurat as $surat)
                            <tr>
                                <td>{{ optional($surat->jenisSurat)->Nama_Surat ?? '-' }}</td>
                                <td>
                                    {{ optional($surat->pemberiTugas)->Name_User ?? '-' }}
                                    @if(optional(optional($surat->pemberiTugas)->role)->Name_Role)
                                        <br><small class="text-muted">({{ optional($surat->pemberiTugas->role)->Name_Role }})</small>
                                    @endif
                                </td>
                                <td>{{ optional($surat->Tanggal_Diberikan_Tugas_Surat) ? $surat->Tanggal_Diberikan_Tugas_Surat->format('d M Y') : '-' }}</td>
                                <td class="text-center">
                                    <a href="{{ route('dekan.surat.detail', $surat->Id_Tugas_Surat) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye me-1"></i> Lihat Detail
                                    </a>
                                    <form action="{{ route('dekan.surat.approve', $surat->Id_Tugas_Surat) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menyetujui dan menandatangani surat ini?');">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm">
                                            <i class="fas fa-signature me-1"></i> Setujui (TTE)
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x mb-2"></i>
                                    <p class="mb-0">Tidak ada surat yang menunggu persetujuan TTE saat ini.</p>
                                    <small class="text-muted d-block mt-2">
                                        Surat akan muncul di sini setelah Admin Fakultas memproses dan mengajukan ke Dekan.
                                    </small>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection