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
            @php
                $totalArsip = $arsipTugas->count();
                if(isset($arsipLegalisir)) {
                    $totalArsip += $arsipLegalisir->count();
                }
            @endphp
            <span class="badge bg-primary">{{ $totalArsip }} Arsip</span>
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
                            @if($jenisSurat->Id_Jenis_Surat != 3)
                                <th><i class="fas fa-file-alt me-2"></i>No. Surat</th>
                            @endif
                            <th><i class="fas fa-user me-2"></i>Pengaju</th>
                            @if($jenisSurat->Id_Jenis_Surat == 3)
                                <th class="text-center"><i class="fas fa-print me-2"></i>Jumlah Cetak</th>
                                <th class="text-center"><i class="fas fa-money-bill me-2"></i>Biaya</th>
                            @endif
                            <th class="text-center"><i class="fas fa-check-circle me-2"></i>Status</th>
                            @if($jenisSurat->Id_Jenis_Surat != 3)
                                <th class="text-center"><i class="fas fa-download me-2"></i>File</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Gabungkan Tugas_Surat dan Surat_Legalisir --}}
                        @php
                            // Untuk legalisir (ID=3): HANYA gunakan $arsipLegalisir
                            // Untuk Peminjaman Mobil (ID=13): HANYA gunakan $arsipTugas (dari Surat_Peminjaman_Mobil)
                            // Untuk surat lain: HANYA gunakan $arsipTugas
                            if($jenisSurat->Id_Jenis_Surat == 3) {
                                $allArsip = isset($arsipLegalisir) ? $arsipLegalisir : collect();
                            } else {
                                $allArsip = $arsipTugas;
                            }
                            
                            $allArsip = $allArsip->sortByDesc(function($item) use ($jenisSurat) {
                                // Untuk legalisir
                                if($jenisSurat->Id_Jenis_Surat == 3 && isset($item->tugasSurat)) {
                                    return $item->tugasSurat->Tanggal_Diselesaikan ?? $item->Tanggal_Bayar;
                                }
                                // Untuk peminjaman mobil
                                if($jenisSurat->Id_Jenis_Surat == 13) {
                                    return $item->updated_at ?? $item->created_at;
                                }
                                // Untuk tugas surat biasa
                                return $item->Tanggal_Diselesaikan ?? null;
                            });
                        @endphp

                        @foreach($allArsip as $t)
                        @php
                            // Deteksi jenis data
                            $isLegalisir = isset($t->Jenis_Dokumen);
                            $isPeminjamanMobil = isset($t->status_pengajuan) && isset($t->tujuan);
                            
                            if($isLegalisir) {
                                // Data dari Surat_Legalisir
                                $tanggal = $t->tugasSurat ? $t->tugasSurat->Tanggal_Diselesaikan : ($t->Tanggal_Bayar ?? $t->created_at);
                                $namaPengaju = $t->user->Name_User ?? 'N/A';
                                $roleJabatan = $t->user->role->Name_Role ?? 'N/A';
                                $namaProdi = $t->user->mahasiswa->prodi->Nama_Prodi ?? 'N/A';
                                $status = $t->Status;
                                $jumlahCetak = $t->Jumlah_Salinan ?? 0;
                                $harga = $t->Biaya ?? 0;
                            } elseif($isPeminjamanMobil) {
                                // Data dari Surat_Peminjaman_Mobil
                                $tanggal = $t->tugasSurat->Tanggal_Diselesaikan ?? $t->updated_at;
                                $nomorSurat = $t->tugasSurat->Nomor_Surat ?? '';
                                $namaPengaju = $t->user->Name_User ?? 'N/A';
                                $roleJabatan = $t->user->role->Name_Role ?? 'N/A';
                                $namaProdi = $t->user->mahasiswa->prodi->Nama_Prodi ?? null;
                                $status = $t->status_pengajuan;
                            } else {
                                // Data dari Tugas_Surat
                                $tanggal = $t->Tanggal_Diselesaikan;
                                $nomorSurat = $t->Nomor_Surat;
                                $namaPengaju = $t->pemberiTugas?->Name_User ?? 'User Dihapus';
                                $roleJabatan = $t->pemberiTugas?->role?->Name_Role ?? 'Role N/A';
                                $namaProdi = $t->pemberiTugas?->mahasiswa?->prodi?->Nama_Prodi ?? null;
                                $status = $t->Status ?? '';
                            }
                        @endphp
                        <tr data-tanggal="{{ optional($tanggal)->format('Y-m-d') ?? '' }}"
                            data-bulan="{{ optional($tanggal)->format('m') ?? '' }}"
                            data-tahun="{{ optional($tanggal)->format('Y') ?? '' }}"
                            data-nomor="{{ $nomorSurat ?? '' }}">
                            <td>
                                <div class="fw-bold">{{ optional($tanggal)->format('d M Y') ?? '-' }}</div>
                                <small class="text-muted"><i class="far fa-clock me-1"></i>{{ optional($tanggal)->format('H:i') ?? '' }}</small>
                            </td>
                            @if(!$isLegalisir)
                            <td>
                                @if(isset($nomorSurat) && $nomorSurat)
                                    <span class="badge bg-light text-dark fw-bold">{{ $nomorSurat }}</span>
                                @else
                                    <span class="text-muted fst-italic small">Belum ada nomor</span>
                                @endif
                            </td>
                            @endif
                            <td>
                                <div class="fw-bold">{{ $namaPengaju }}</div>
                                <small class="text-muted">
                                    <i class="fas fa-id-badge me-1"></i>{{ $roleJabatan }}
                                    @if($namaProdi)
                                        <br><i class="fas fa-building me-1"></i>{{ $namaProdi }}
                                    @endif
                                </small>
                            </td>
                            @if($jenisSurat->Id_Jenis_Surat == 3)
                            <td class="text-center">
                                <span class="badge bg-info">{{ $jumlahCetak ?? 0 }} Berkas</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success">Rp {{ number_format($harga ?? 0, 0, ',', '.') }}</span>
                            </td>
                            @endif
                            <td class="text-center">
                                @php $statusLower = strtolower(trim($status)); @endphp
                                @if(in_array($statusLower, ['selesai', 'disetujui', 'success']))
                                    <span class="badge bg-success text-white"><i class="fas fa-check me-1"></i>{{ ucfirst($statusLower) }}</span>
                                @elseif($statusLower === 'ditolak')
                                    <span class="badge bg-danger text-white"><i class="fas fa-times me-1"></i>{{ ucfirst($statusLower) }}</span>
                                @else
                                    <span class="badge bg-secondary text-white">{{ ucfirst($statusLower) }}</span>
                                @endif
                            </td>
                            @if($jenisSurat->Id_Jenis_Surat != 3)
                            <td class="text-center">
                                @if($t->suratMagang)
                                    {{-- Untuk Surat Magang: Download Surat Pengantar dan Proposal --}}
                                    <div class="btn-group" role="group">
                                        @if($t->suratMagang->Acc_Dekan || $t->suratMagang->Acc_Koordinator)
                                            <a href="{{ route('admin_fakultas.surat_magang.download_surat', $t->Id_Tugas_Surat) }}" 
                                               target="_blank" 
                                               class="btn btn-success btn-sm"
                                               title="Download Surat Pengantar">
                                                <i class="fas fa-file-pdf me-1"></i>Surat
                                            </a>
                                        @endif
                                        @if($t->suratMagang->File_proposal_kegiatan)
                                            <a href="{{ asset('storage/' . $t->suratMagang->File_proposal_kegiatan) }}" 
                                               target="_blank" 
                                               class="btn btn-info btn-sm"
                                               title="Download Proposal">
                                                <i class="fas fa-file-alt me-1"></i>Proposal
                                            </a>
                                        @endif
                                    </div>
                                    @if(!$t->suratMagang->Acc_Dekan && !$t->suratMagang->Acc_Koordinator && !$t->suratMagang->File_proposal_kegiatan)
                                        <span class="text-muted">-</span>
                                    @endif
                                @elseif($isPeminjamanMobil)
                                    {{-- Untuk Peminjaman Mobil Dinas --}}
                                    <a href="{{ route('admin_fakultas.peminjaman_mobil.download_surat', $t->id) }}" 
                                       target="_blank" 
                                       class="btn btn-primary btn-sm"
                                       title="Download Surat Peminjaman Mobil">
                                        <i class="fas fa-download me-1"></i>Unduh
                                    </a>
                                @elseif(!empty($t->File_Surat))
                                    {{-- Untuk surat lain yang punya File_Surat --}}
                                    <a href="{{ asset('storage/' . ltrim($t->File_Surat, '/')) }}" target="_blank" class="btn btn-primary btn-sm">
                                        <i class="fas fa-download me-1"></i>Unduh
                                    </a>
                                @elseif(!empty($t->dokumen_pendukung))
                                    {{-- Fallback ke dokumen_pendukung --}}
                                    <a href="{{ asset('storage/' . ltrim($t->dokumen_pendukung, '/')) }}" target="_blank" class="btn btn-primary btn-sm">
                                        <i class="fas fa-download me-1"></i>Unduh
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            @endif
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