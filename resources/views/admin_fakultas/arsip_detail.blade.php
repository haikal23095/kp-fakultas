@extends('layouts.admin_fakultas')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('admin_fakultas.surat.archive') }}" class="btn btn-outline-secondary me-3">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
        <h1 class="h3 mb-0 text-gray-800">Arsip {{ $jenisSurat->Nama_Surat }}</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Arsip</h6>
            <span class="badge bg-primary">{{ $arsipTugas->count() }} Arsip</span>
        </div>
        <div class="card-body">
            <div class="filter-section mb-4">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted"><i class="fas fa-calendar-day me-1"></i>Filter Bulan</label>
                        <select class="form-select" id="filter-bulan">
                            <option value="">Semua Bulan</option>
                            <option value="01">Januari</option>
                            <option value="02">Februari</option>
                            <option value="03">Maret</option>
                            <option value="04">April</option>
                            <option value="05">Mei</option>
                            <option value="06">Juni</option>
                            <option value="07">Juli</option>
                            <option value="08">Agustus</option>
                            <option value="09">September</option>
                            <option value="10">Oktober</option>
                            <option value="11">November</option>
                            <option value="12">Desember</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted"><i class="fas fa-calendar-alt me-1"></i>Filter Tahun</label>
                        <select class="form-select" id="filter-tahun">
                            <option value="">Semua Tahun</option>
                            @php
                                $years = $arsipTugas->pluck('Tanggal_Diselesaikan')
                                              ->filter()
                                              ->map(fn($date) => $date->format('Y'))
                                              ->unique()
                                              ->sort()
                                              ->reverse();
                            @endphp
                            @foreach($years as $year)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted"><i class="fas fa-search me-1"></i> Cari Nomor Surat</label>
                        <input type="text" class="form-control" id="filter-search" placeholder="Ketik nomor surat...">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="button" class="btn btn-outline-secondary w-100" id="btn-reset-filter">
                            <i class="fas fa-redo me-1"></i> Reset
                        </button>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover" id="table-arsip">
                    <thead>
                        <tr>
                            <th><i class="fas fa-calendar me-2"></i>Tanggal</th>
                            <th><i class="fas fa-file-alt me-2"></i>No. Surat</th>
                            <th><i class="fas fa-user me-2"></i>Pengaju</th>
                            <th class="text-center"><i class="fas fa-check-circle me-2"></i>Status</th>
                            <th class="text-center"><i class="fas fa-download me-2"></i>File</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($arsipTugas->sortByDesc('Tanggal_Diselesaikan') as $t)
                        <tr data-tanggal="{{ optional($t->Tanggal_Diselesaikan)->format('Y-m-d') ?? '' }}"
                            data-bulan="{{ optional($t->Tanggal_Diselesaikan)->format('m') ?? '' }}"
                            data-tahun="{{ optional($t->Tanggal_Diselesaikan)->format('Y') ?? '' }}"
                            data-nomor="{{ strtolower($t->Nomor_Surat ?? '') }}">
                            <td>
                                <div class="fw-bold">{{ optional($t->Tanggal_Diselesaikan)->format('d M Y') ?? '-' }}</div>
                                <small class="text-muted"><i class="far fa-clock me-1"></i>{{ optional($t->Tanggal_Diselesaikan)->format('H:i') ?? '' }}</small>
                            </td>
                            <td>
                                @if($t->Nomor_Surat)
                                    <span class="badge bg-light text-dark fw-bold">{{ $t->Nomor_Surat }}</span>
                                @else
                                    <span class="text-muted fst-italic small">Belum ada nomor</span>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold">{{ $t->pemberiTugas?->Name_User ?? 'User Dihapus' }}</div>
                                <small class="text-muted">
                                    <i class="fas fa-id-badge me-1"></i>{{ $t->pemberiTugas?->role?->Name_Role ?? 'Role N/A' }}
                                    @if($t->pemberiTugas?->mahasiswa?->prodi)
                                        <br><i class="fas fa-building me-1"></i>{{ $t->pemberiTugas->mahasiswa->prodi->Nama_Prodi }}
                                    @endif
                                </small>
                            </td>
                            <td class="text-center">
                                @php $status = strtolower(trim($t->Status ?? '')); @endphp
                                @if(in_array($status, ['selesai', 'disetujui', 'success']))
                                    <span class="badge bg-success text-white"><i class="fas fa-check me-1"></i>{{ ucfirst($status) }}</span>
                                @elseif($status === 'ditolak')
                                    <span class="badge bg-danger text-white"><i class="fas fa-times me-1"></i>{{ ucfirst($status) }}</span>
                                @else
                                    <span class="badge bg-secondary text-white">{{ ucfirst($status) }}</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if(!empty($t->File_Surat))
                                    <a href="{{ asset('storage/' . ltrim($t->File_Surat, '/')) }}" target="_blank" class="btn btn-primary btn-sm">
                                        <i class="fas fa-download me-1"></i>Unduh
                                    </a>
                                @elseif(!empty($t->dokumen_pendukung))
                                    <a href="{{ asset('storage/' . ltrim($t->dokumen_pendukung, '/')) }}" target="_blank" class="btn btn-primary btn-sm">
                                        <i class="fas fa-download me-1"></i>Unduh
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="text-center py-5" id="no-results" style="display: none;">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Tidak ada arsip ditemukan</h5>
                    <p class="text-muted">Coba ubah filter atau kata kunci pencarian</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterBulan = document.getElementById('filter-bulan');
    const filterTahun = document.getElementById('filter-tahun');
    const filterSearch = document.getElementById('filter-search');
    const btnReset = document.getElementById('btn-reset-filter');
    const table = document.getElementById('table-arsip').querySelector('tbody');
    const noResults = document.getElementById('no-results');
    const tableElement = document.getElementById('table-arsip');

    function applyFilter() {
        const bulan = filterBulan.value;
        const tahun = filterTahun.value;
        const search = filterSearch.value.toLowerCase();

        let visibleCount = 0;

        table.querySelectorAll('tr').forEach(function(row) {
            let show = true;

            if(bulan && row.dataset.bulan !== bulan) {
                show = false;
            }

            if(tahun && row.dataset.tahun !== tahun) {
                show = false;
            }

            if(search && !row.dataset.nomor.includes(search)) {
                show = false;
            }

            row.style.display = show ? '' : 'none';
            if(show) visibleCount++;
        });

        if(visibleCount === 0) {
            tableElement.style.display = 'none';
            noResults.style.display = 'block';
        } else {
            tableElement.style.display = 'table';
            noResults.style.display = 'none';
        }
    }

    filterBulan.addEventListener('change', applyFilter);
    filterTahun.addEventListener('change', applyFilter);
    filterSearch.addEventListener('keyup', applyFilter);

    btnReset.addEventListener('click', function() {
        filterBulan.value = '';
        filterTahun.value = '';
        filterSearch.value = '';
        applyFilter();
    });
});
</script>
@endpush