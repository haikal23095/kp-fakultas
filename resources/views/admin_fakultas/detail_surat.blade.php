@extends('layouts.admin_fakultas')

@section('title', 'Detail Surat')

@section('content')
@php 
    $status = trim(optional($surat)->Status ?? ''); 
    // Cek apakah surat magang ada
    $isMagang = $surat->suratMagang ? true : false;
    // Cek apakah surat aktif ada
    $isAktif = $surat->suratKetAktif ? true : false;
@endphp

<div class="mb-3">
    <a href="{{ route('admin_fakultas.surat.manage') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fa fa-arrow-left me-1"></i> Kembali ke Manajemen Surat
    </a>
</div>

<div class="card mb-4 shadow-sm">
	<div class="card-body d-flex align-items-center justify-content-between">
		<div>
			<h4 class="mb-1"><i class="fa fa-file-alt text-primary me-2"></i> Detail Surat</h4>
			<div class="text-muted small">Nomor: <strong>{{ optional($surat)->Nomor_Surat ?? 'N/A' }}</strong> &middot; ID: <strong>{{ optional($surat)->Id_Tugas_Surat ?? '-' }}</strong></div>
            
            @if($isAktif)
                <div class="mt-2 d-flex align-items-center flex-wrap gap-2">
                    @if($surat->suratKetAktif->is_urgent)
                        <span class="badge bg-danger blink-badge"><i class="fas fa-exclamation-circle me-1"></i> URGENT</span>
                        @if($surat->suratKetAktif->urgent_reason)
                            <small class="text-danger">Alasan: {{ $surat->suratKetAktif->urgent_reason }}</small>
                        @endif
                    @endif
                    
                    {{-- Tombol Toggle Urgent --}}
                    <button type="button" class="btn btn-sm {{ $surat->suratKetAktif->is_urgent ? 'btn-outline-secondary' : 'btn-outline-danger' }}" 
                            data-bs-toggle="modal" data-bs-target="#urgentModal"
                            title="{{ $surat->suratKetAktif->is_urgent ? 'Hapus status urgent' : 'Tandai sebagai urgent' }}">
                        <i class="fas {{ $surat->suratKetAktif->is_urgent ? 'fa-minus-circle' : 'fa-exclamation-circle' }}"></i>
                        {{ $surat->suratKetAktif->is_urgent ? 'Batalkan Prioritas' : 'Set Prioritas' }}
                    </button>
                </div>
            @endif
		</div>

		<div class="text-end">
			@if(strtolower($status) === 'selesai' || strtolower($status) === 'disetujui' || strtolower($status) === 'success')
				<span class="badge rounded-pill bg-success px-3 py-2"><i class="fas fa-check me-1"></i> {{ $status }}</span>
			@elseif(strtolower($status) === 'terlambat' || strtolower($status) === 'ditolak')
				<span class="badge rounded-pill bg-danger px-3 py-2"><i class="fas fa-times me-1"></i> {{ $status }}</span>
			@elseif(strtolower($status) === 'proses' || strtolower($status) === 'dikerjakan-admin')
				<span class="badge rounded-pill bg-primary px-3 py-2"><i class="fas fa-spinner fa-spin me-1"></i> {{ $status }}</span>
            @elseif(strtolower($status) === 'diajukan-ke-koordinator')
                <span class="badge rounded-pill bg-info text-dark px-3 py-2"><i class="fas fa-user-tie me-1"></i> Ke Koordinator</span>
            @elseif(strtolower($status) === 'diajukan-ke-dekan')
                <span class="badge rounded-pill bg-warning text-dark px-3 py-2"><i class="fas fa-signature me-1"></i> Ke Dekan</span>
			@else
				<span class="badge rounded-pill bg-secondary px-3 py-2">{{ $status ?? '-' }}</span>
			@endif

            {{-- Tombol Proses Draft (Hanya jika route ada dan role sesuai) --}}
			@if(auth()->check() && auth()->user()->Id_Role == 7 && (trim($surat->Status) == 'baru' || trim($surat->Status) == 'Diterima Admin' || trim($surat->Status) == 'Proses'))
                {{-- 
                    TODO: Pastikan route 'admin_fakultas.surat.process_draft' dibuat jika fitur ini diinginkan.
                    Saat ini disembunyikan atau dikomentari jika route belum ada.
                --}}
				{{-- 
                <form method="POST" action="{{ route('admin_fakultas.surat.process_draft', $surat->Id_Tugas_Surat) }}" class="mt-2 d-inline" enctype="multipart/form-data" onsubmit="return confirm('Apakah Anda yakin ingin memproses dan mengajukan surat ini ke Dekan?');">
					@csrf
					<input type="hidden" name="action" value="proses_ajukan_dekan">
					<button type="submit" class="btn btn-sm btn-warning"><i class="fa fa-paper-plane me-1"></i> Proses & Ajukan</button>
				</form>
                --}}
			@endif
		</div>
	</div>
	</div>

<div class="row g-4">
	<div class="col-lg-4">
		<div class="card h-100 shadow-sm">
			<div class="card-header bg-white border-bottom d-flex align-items-center">
				<i class="fa fa-user-circle fa-lg text-secondary me-2"></i>
				<strong>Detail Pengaju</strong>
			</div>
			<div class="card-body">
				<h5 class="mb-1">{{ optional($surat->pemberiTugas)->Name_User ?? '-' }}</h5>
				<div class="text-muted mb-3">{{ optional(optional($surat->pemberiTugas)->role)->Name_Role ?? '-' }}</div>

				<p class="mb-1"><small class="text-muted">Tanggal Pengajuan</small><br>{{ optional($surat->Tanggal_Diberikan_Tugas_Surat) ? optional($surat->Tanggal_Diberikan_Tugas_Surat)->format('d M Y H:i') : '-' }}</p>

				@if(!empty($detailPengaju))
					<hr />
					<p class="mb-1"><small class="text-muted">NIM</small><br>{{ $detailPengaju->NIM ?? '-' }}</p>
					<p class="mb-0"><small class="text-muted">Alamat</small><br>{{ $detailPengaju->Alamat_Mahasiswa ?? '-' }}</p>
				@endif
			</div>
		</div>
	</div>

	<div class="col-lg-4">
		<div class="card h-100 shadow-sm">
			<div class="card-header bg-white border-bottom d-flex align-items-center">
				<i class="fa fa-info-circle fa-lg text-secondary me-2"></i>
				<strong>Informasi Surat</strong>
			</div>
			<div class="card-body">
				<p class="mb-2"><small class="text-muted">Jenis Surat</small><br>{{ optional($surat->jenisSurat)->Nama_Surat ?? '-' }}</p>
				<p class="mb-2"><small class="text-muted">Judul</small><br>{{ $surat->Judul_Tugas_Surat ?? '-' }}</p>
				<p class="mb-0"><small class="text-muted">Deskripsi</small><br>{{ $surat->Deskripsi_Tugas_Surat ?? $surat->Deskripsi_Tugas ?? '-' }}</p>
			</div>
		</div>
	</div>

	<div class="col-lg-4">
		<div class="card h-100 shadow-sm">
			<div class="card-header bg-white border-bottom d-flex align-items-center">
				<i class="fa fa-folder-open fa-lg text-secondary me-2"></i>
				<strong>Dokumen & Proses</strong>
			</div>
			<div class="card-body">
				<p class="mb-2"><small class="text-muted">Ditujukan ke</small><br>{{ optional($surat->penerimaTugas)->Name_User ?? '-' }} <br><span class="text-muted">({{ optional(optional($surat->penerimaTugas)->role)->Name_Role ?? '-' }})</span></p>

				<div class="mb-3">
					<small class="text-muted d-block mb-1">Dokumen Pendukung (Mahasiswa)</small>
					@if($isMagang && !empty($surat->suratMagang->Dokumen_Proposal))
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('admin_fakultas.surat.preview', ['id' => $surat->Id_Tugas_Surat, 'type' => 'proposal']) }}" target="_blank" class="btn btn-outline-info" title="Preview Dokumen"><i class="fa fa-eye me-1"></i> Preview</a>
						    <a href="{{ route('admin_fakultas.surat.download', ['id' => $surat->Id_Tugas_Surat, 'type' => 'proposal']) }}" class="btn btn-outline-primary" title="Unduh Dokumen"><i class="fa fa-download me-1"></i> Unduh</a>
                        </div>
					@elseif($isAktif && !empty($surat->suratKetAktif->KRS))
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('admin_fakultas.surat.preview', ['id' => $surat->Id_Tugas_Surat]) }}" target="_blank" class="btn btn-outline-info" title="Preview KRS"><i class="fa fa-eye me-1"></i> Preview KRS</a>
						    <a href="{{ route('admin_fakultas.surat.download', ['id' => $surat->Id_Tugas_Surat]) }}" class="btn btn-outline-primary" title="Unduh KRS"><i class="fa fa-download me-1"></i> Unduh KRS</a>
                        </div>
                    @else
						<span class="text-muted">-</span>
					@endif
				</div>

				<div class="mb-3">
					<small class="text-muted d-block mb-1">Draft Final (Arsip)</small>
					@if(optional($surat->fileArsip)->Path_File)
						<a href="{{ asset('storage/' . ltrim(optional($surat->fileArsip)->Path_File, '/')) }}" target="_blank" class="btn btn-outline-success btn-sm"><i class="fa fa-file-pdf me-1"></i> Lihat Draft</a>
					@else
						<span class="text-muted">-</span>
					@endif
				</div>

                {{-- Form Proses Surat (Beri Nomor & Teruskan ke Dekan) --}}
				@if(auth()->check() && auth()->user()->Id_Role == 7 && (empty(trim($surat->Status)) || in_array(strtolower(trim($surat->Status)), ['baru', 'proses', 'diterima admin'])))
					<hr />
                    <h6 class="fw-bold text-primary"><i class="fas fa-cog me-1"></i> Tindakan Admin</h6>
                    
                    <div class="card bg-light border-0 mb-3">
                        <div class="card-body">
                            <form method="POST" action="{{ route('admin_fakultas.surat.forward', $surat->Id_Tugas_Surat) }}" onsubmit="return confirm('Apakah nomor surat sudah benar? Surat akan diteruskan ke Dekan.');">
                                @csrf
                                <div class="mb-3">
                                    <label for="nomor_surat" class="form-label fw-bold">Nomor Surat</label>
                                    <input type="text" name="nomor_surat" id="nomor_surat" class="form-control" placeholder="Masukkan Nomor Surat (Wajib)" value="{{ $surat->Nomor_Surat }}" required>
                                    <div class="form-text">Nomor surat wajib diisi sebelum diteruskan ke Dekan.</div>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-paper-plane me-1"></i> Simpan & Teruskan ke Dekan
                                    </button>
                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                        <i class="fas fa-times-circle me-1"></i> Tolak Surat
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
				@endif
			</div>
		</div>
	</div>
</div>

<!-- Modal Tolak Surat -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin_fakultas.surat.reject', $surat->Id_Tugas_Surat) }}" method="POST">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="rejectModalLabel">Tolak Pengajuan Surat</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="alasan_penolakan" class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="alasan_penolakan" name="alasan_penolakan" rows="4" required placeholder="Jelaskan mengapa surat ini ditolak..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Tolak Surat</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Urgent --}}
@if($isAktif)
<div class="modal fade" id="urgentModal" tabindex="-1" aria-labelledby="urgentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin_fakultas.surat.toggle_urgent', $surat->Id_Tugas_Surat) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="urgentModalLabel">
                        {{ $surat->suratKetAktif->is_urgent ? 'Batalkan Prioritas' : 'Set Prioritas Surat' }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if($surat->suratKetAktif->is_urgent)
                        <p>Apakah Anda yakin ingin membatalkan status prioritas (urgent) untuk surat ini?</p>
                        <input type="hidden" name="urgent_reason" value="">
                    @else
                        <div class="mb-3">
                            <label for="urgent_reason" class="form-label">Alasan Prioritas</label>
                            <textarea class="form-control" id="urgent_reason" name="urgent_reason" rows="3" placeholder="Contoh: Dibutuhkan segera untuk beasiswa..." required></textarea>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn {{ $surat->suratKetAktif->is_urgent ? 'btn-danger' : 'btn-primary' }}">
                        {{ $surat->suratKetAktif->is_urgent ? 'Ya, Batalkan' : 'Simpan Prioritas' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection
