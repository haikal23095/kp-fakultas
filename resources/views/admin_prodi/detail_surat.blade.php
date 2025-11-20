@extends('layouts.admin_prodi')

@section('title', 'Detail Surat')

@section('content')
@php $status = trim(optional($surat)->Status ?? ''); @endphp

<div class="mb-3">
    <a href="{{ route('admin.surat.manage') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fa fa-arrow-left me-1"></i> Kembali ke Manajemen Surat
    </a>
</div>

<div class="card mb-4 shadow-sm">
	<div class="card-body d-flex align-items-center justify-content-between">
		<div>
			<h4 class="mb-1"><i class="fa fa-file-alt text-primary me-2"></i> Detail Surat</h4>
			<div class="text-muted small">Nomor: <strong>{{ optional($surat)->Nomor_Surat ?? 'N/A' }}</strong> &middot; ID: <strong>{{ optional($surat)->Id_Tugas_Surat ?? '-' }}</strong></div>
		</div>

		<div class="text-end">
			@if(strtolower($status) === 'selesai' || strtolower($status) === 'disetujui')
				<span class="badge bg-success fs-6">{{ $surat->Status }}</span>
			@elseif(strtolower($status) === 'terlambat' || strtolower($status) === 'ditolak')
				<span class="badge bg-danger fs-6">{{ $surat->Status }}</span>
			@elseif(strtolower($status) === 'proses')
				<span class="badge bg-primary fs-6">{{ $surat->Status }}</span>
			@else
				<span class="badge bg-secondary fs-6">{{ $surat->Status ?? '-' }}</span>
			@endif

			@if(auth()->check() && auth()->user()->Id_Role == 1 && (trim($surat->Status) == 'baru' || trim($surat->Status) == 'Diterima Admin'))
				<form method="POST" action="{{ route('admin.surat.process_draft', $surat->Id_Tugas_Surat) }}" class="mt-2 d-inline" enctype="multipart/form-data" onsubmit="return confirm('Apakah Anda yakin ingin memproses dan mengajukan surat ini ke Dekan?');">
					@csrf
					<input type="hidden" name="action" value="proses_ajukan_dekan">
					<button type="submit" class="btn btn-sm btn-warning"><i class="fa fa-paper-plane me-1"></i> Proses & Ajukan</button>
				</form>
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
					@if(!empty($surat->dokumen_pendukung))
						<a href="{{ route('admin.surat.download', $surat->Id_Tugas_Surat) }}" class="btn btn-outline-primary btn-sm" title="Lihat / Unduh Dokumen Pendukung"><i class="fa fa-download me-1"></i> Lihat / Unduh</a>
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

				@if(auth()->check() && auth()->user()->Id_Role == 1 && (trim($surat->Status) == 'Diterima Admin' || trim($surat->Status) == 'Proses'))
					<hr />
					<form method="POST" action="{{ route('admin.surat.process_draft', $surat->Id_Tugas_Surat) }}" enctype="multipart/form-data" onsubmit="return confirm('Apakah Anda yakin ingin mengupload draft final dan mengajukan ke Dekan?');">
						@csrf
						<div class="mb-3">
							<label for="draft_surat" class="form-label"><i class="fa fa-upload me-1"></i> Upload Draft Final (PDF)</label>
							<input type="file" name="draft_surat" id="draft_surat" class="form-control form-control-sm" accept="application/pdf" required>
							<div class="form-text">Maks: 5MB. File PDF saja.</div>
						</div>
						<button type="submit" class="btn btn-success btn-sm"><i class="fa fa-paper-plane me-1"></i> Submit & Ajukan ke Dekan</button>
					</form>
				@endif
			</div>
		</div>
	</div>
</div>

@endsection
