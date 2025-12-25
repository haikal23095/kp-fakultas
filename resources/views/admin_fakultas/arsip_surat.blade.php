@extends('layouts.admin_fakultas')

@section('title', 'Arsip Surat')

@push('styles')
<style>
    .page-header {
        background: #ffffff;
        border-bottom: 2px solid #f0f0f0;
        padding: 2rem 0;
        margin-bottom: 2.5rem;
    }

    .card-jenis-surat {
        border: 1px solid #e3e6f0;
        border-radius: 16px;
        transition: all 0.25s ease;
        cursor: pointer;
        height: 100%;
        background: #ffffff;
        box-shadow: 0 4px 16px rgba(76, 87, 125, 0.12);
    }

    .card-jenis-surat:hover {
        transform: translateY(-6px);
        box-shadow: 0 14px 28px rgba(76, 87, 125, 0.18);
        border-color: #4e73df;
    }

    .card-jenis-surat .card-body {
        padding: 2.5rem 2rem;
        text-align: center;
    }

    .card-icon {
        width: 90px;
        height: 90px;
        margin: 0 auto 1.5rem;
        border-radius: 22px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        color: #fff;
    }

    .card-icon.blue {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        box-shadow: 0 6px 16px rgba(78, 115, 223, 0.35);
    }

    .card-icon.green {
        background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);
        box-shadow: 0 6px 16px rgba(28, 200, 138, 0.35);
    }

    .card-icon.orange {
        background: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%);
        box-shadow: 0 6px 16px rgba(246, 194, 62, 0.35);
    }

    .card-icon.purple {
        background: linear-gradient(135deg, #8e54e9 0%, #4776e6 100%);
        box-shadow: 0 6px 16px rgba(142, 84, 233, 0.35);
    }

    .badge-count {
        position: absolute;
        top: 18px;
        right: 18px;
        font-size: 0.85rem;
        padding: 0.5rem 0.9rem;
        border-radius: 999px;
        font-weight: 600;
    }

    .card-jenis-surat h5 {
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 0.5rem;
        font-size: 1.25rem;
    }

    .card-jenis-surat p {
        color: #6c757d;
        font-size: 0.95rem;
        margin-bottom: 0;
    }

    .stats-row {
        display: flex;
        justify-content: space-around;
        margin-top: 1.5rem;
        padding-top: 1.25rem;
        border-top: 1px solid #e3e6f0;
    }

    .stat-item {
        text-align: center;
    }

    .stat-label {
        font-size: 0.75rem;
        color: #858796;
        text-transform: uppercase;
        font-weight: 600;
        margin-bottom: 0.25rem;
    }

    .stat-value {
        font-size: 1.4rem;
        font-weight: 700;
        color: #5a5c69;
    }

    .info-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #fff;
        border-radius: 16px;
        padding: 2rem;
        border: none;
        box-shadow: 0 12px 24px rgba(102, 126, 234, 0.35);
    }

    .info-card h6 {
        color: #fff;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .filter-section {
        background: #f8f9fc;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        border: 1px solid #e3e6f0;
    }

    .accordion-card {
        border-radius: 16px;
        box-shadow: 0 10px 25px rgba(76, 87, 125, 0.12);
        border: 1px solid #e3e6f0;
        margin-bottom: 1.5rem;
        overflow: hidden;
    }

    .collapse-header {
        background: #f8f9fc;
        padding: 1.25rem 1.5rem;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: background 0.2s ease;
    }

    .collapse-header:hover {
        background: #eef1f8;
    }

    .collapse-header h6 {
        margin: 0;
        font-weight: 700;
        color: #4e73df;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .collapse-header i.fa-chevron-down {
        transition: transform 0.2s ease;
        color: #4e73df;
    }

    .collapse.show .collapse-header i.fa-chevron-down {
        transform: rotate(180deg);
    }

    .table-arsip thead th {
        background: #f1f3f9;
        color: #4e5d78;
        font-weight: 700;
        border: none;
        padding: 1rem 0.75rem;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
    }

    .table-arsip tbody tr {
        transition: background 0.2s ease;
    }

    .table-arsip tbody tr:hover {
        background: #f8f9fc;
    }

    .badge-status {
        padding: 0.45rem 1rem;
        font-size: 0.85rem;
        border-radius: 999px;
        font-weight: 600;
    }

    .btn-download {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        color: white;
        border: none;
        border-radius: 10px;
        padding: 0.45rem 1rem;
        font-weight: 600;
        transition: transform 0.2s ease;
    }

    .btn-download:hover {
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 8px 18px rgba(78, 115, 223, 0.4);
    }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        color: #6c757d;
    }

    .empty-state i {
        font-size: 4rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    @media (max-width: 768px) {
        .card-jenis-surat .card-body {
            padding: 2rem 1.5rem;
        }
        .card-icon {
            width: 70px;
            height: 70px;
            font-size: 2rem;
            margin-bottom: 1rem;
        }
        .stats-row {
            flex-direction: column;
            gap: 1rem;
        }
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <div>
        <h3 class="mb-2 fw-bold text-dark">Arsip Surat Fakultas</h3>
        <p class="mb-1 text-muted">Semua surat yang sudah tuntas ditampilkan per jenis, sama seperti kartu pada halaman manajemen.</p>
        <small class="text-info">Total arsip: {{ $arsipTugas->count() }} surat</small>
    </div>
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

@if($arsipTugas->isEmpty())
    <div class="empty-state">
        <i class="fas fa-archive"></i>
        <h4>Belum Ada Arsip</h4>
        <p>Surat yang telah diselesaikan otomatis muncul di sini setelah proses manajemen selesai.</p>
    </div>
@else
    @php
        $cardStyles = [
            ['icon' => 'fa-id-card', 'color' => 'blue', 'badge' => 'primary'],
            ['icon' => 'fa-briefcase', 'color' => 'green', 'badge' => 'success'],
            ['icon' => 'fa-stamp', 'color' => 'orange', 'badge' => 'warning'],
            ['icon' => 'fa-folder', 'color' => 'purple', 'badge' => 'info'],
            ['icon' => 'fa-file-alt', 'color' => 'blue', 'badge' => 'secondary'],
            ['icon' => 'fa-certificate', 'color' => 'green', 'badge' => 'danger'],
        ];
    @endphp

    <div class="row">
        @foreach($arsipByJenis as $group)
        @php
            $jenisSurat = $group->jenis;
            $items = $group->items;
            $style = $cardStyles[$loop->index % count($cardStyles)];
            $bulanIni = $items->filter(function($t) {
                return $t->Tanggal_Diselesaikan && $t->Tanggal_Diselesaikan->isCurrentMonth();
            })->count();
        @endphp
        <div class="col-lg-4 col-md-6 mb-4">
            <a href="{{ route('admin_fakultas.surat.archive.detail', $jenisSurat->Id_Jenis_Surat) }}" class="text-decoration-none">
                <div class="card card-jenis-surat position-relative">
                    <span class="badge badge-count bg-{{ $style['badge'] }}">{{ $items->count() }} Arsip</span>
                    <div class="card-body">
                        <div class="card-icon {{ $style['color'] }}">
                            <i class="fas {{ $style['icon'] }}"></i>
                        </div>
                        <h5>{{ $jenisSurat->Nama_Surat }}</h5>
                        <p>Klik untuk melihat seluruh surat yang sudah selesai diproses.</p>
                        <div class="stats-row">
                            <div class="stat-item">
                                <div class="stat-label">Total</div>
                                <div class="stat-value text-success">{{ $items->count() }}</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-label">Bulan Ini</div>
                                <div class="stat-value text-primary">{{ $bulanIni }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        @endforeach
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="info-card shadow">
                <div class="d-flex align-items-start">
                    <i class="fas fa-info-circle fa-2x me-3 mt-1"></i>
                    <div>
                        <h6 class="fw-bold mb-2">Cara kerja sama seperti Manajemen Surat</h6>
                        <p class="mb-0" style="opacity: 0.95;">
                            Setiap card mewakili satu jenis surat. Setelah proses di halaman manajemen selesai, surat berpindah otomatis ke card jenis yang sama pada arsip. Klik card untuk membuka daftar arsip lengkap berikut filter bulan, tahun, dan pencarian nomor surat.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection

@push('scripts')
<script>
    // Smooth hover animation
    document.querySelectorAll('.card-jenis-surat').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.borderColor = '#4e73df';
        });
        card.addEventListener('mouseleave', function() {
            this.style.borderColor = '#e3e6f0';
        });
    });
</script>
@endpush
