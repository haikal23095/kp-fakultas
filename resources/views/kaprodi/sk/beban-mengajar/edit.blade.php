@extends('layouts.kaprodi')

@section('title', 'Edit SK Beban Mengajar')

@section('content')

<div class="mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.kaprodi') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kaprodi.sk.index') }}">Ajukan SK</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kaprodi.sk.beban-mengajar.index') }}">SK Beban Mengajar</a></li>
            <li class="breadcrumb-item active">Edit & Ajukan Ulang</li>
        </ol>
    </nav>
</div>

@if($sk->{'Alasan-Tolak'})
<div class="alert alert-danger border-0 shadow-sm mb-4">
    <div class="d-flex align-items-start">
        <i class="fas fa-exclamation-triangle fa-2x text-danger me-3"></i>
        <div>
            <h6 class="fw-bold mb-1">Alasan Penolakan</h6>
            <p class="mb-0">{{ $sk->{'Alasan-Tolak'} }}</p>
        </div>
    </div>
</div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-warning text-dark">
                <div class="d-flex align-items-center">
                    <i class="fas fa-edit fa-lg me-3"></i>
                    <div>
                        <h5 class="mb-0 fw-bold">Edit & Ajukan Ulang SK Beban Mengajar</h5>
                        <small>Perbaiki data dan ajukan kembali</small>
                    </div>
                </div>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('kaprodi.sk.beban-mengajar.update', $sk->No) }}" method="POST" id="formBebanMengajar">
                    @csrf
                    @method('PUT')

                    <!-- Identitas Pengajuan -->
                    <div class="mb-4">
                        <h6 class="fw-bold text-primary mb-3">
                            <i class="fas fa-info-circle me-2"></i>Identitas Pengajuan
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="prodi_id" class="form-label fw-semibold">Program Studi <span class="text-danger">*</span></label>
                                <select class="form-select" id="prodi_id" name="prodi_id" required>
                                    <option value="">-- Pilih Program Studi --</option>
                                    @foreach($prodis as $p)
                                        <option value="{{ $p->Id_Prodi }}" {{ $sk->Id_Prodi == $p->Id_Prodi ? 'selected' : '' }}>
                                            {{ $p->Nama_Prodi }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="semester" class="form-label fw-semibold">Semester <span class="text-danger">*</span></label>
                                <select class="form-select" id="semester" name="semester" required>
                                    <option value="Ganjil" {{ $sk->Semester == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                                    <option value="Genap" {{ $sk->Semester == 'Genap' ? 'selected' : '' }}>Genap</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="tahun_akademik" class="form-label fw-semibold">Tahun Akademik <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="tahun_akademik" name="tahun_akademik" 
                                       value="{{ $sk->Tahun_Akademik }}" placeholder="Contoh: 2025/2026" required>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Daftar Beban Mengajar -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold text-primary mb-0">
                                <i class="fas fa-list me-2"></i>Daftar Beban Mengajar
                            </h6>
                            <button type="button" class="btn btn-success btn-sm" id="tambahBeban">
                                <i class="fas fa-plus-circle me-2"></i>Tambah Beban Mengajar
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="tabelBebanMengajar">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%" class="text-center">No</th>
                                        <th width="25%">Nama Dosen</th>
                                        <th width="25%">Mata Kuliah</th>
                                        <th width="15%">Kelas</th>
                                        <th width="10%" class="text-center">SKS</th>
                                        <th width="10%" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="bodyBebanMengajar">
                                    @php
                                        $rawData = $sk->Data_Beban_Mengajar;
                                        $bebanData = is_string($rawData) ? json_decode($rawData, true) : (is_array($rawData) ? $rawData : []);
                                    @endphp
                                    @foreach($bebanData as $index => $beban)
                                    @php
                                        $selectedMk = $mataKuliahList->firstWhere('Nomor', $beban['id_mata_kuliah'] ?? null);
                                    @endphp
                                    <tr data-index="{{ $index }}">
                                        <td class="text-center row-number">{{ $index + 1 }}</td>
                                        <td>
                                            <select class="form-select form-select-sm select-dosen" name="beban[{{ $index }}][dosen_id]" required>
                                                <option value="">-- Pilih Dosen --</option>
                                                @foreach($dosens as $dosen)
                                                    <option value="{{ $dosen->Id_Dosen }}" {{ ($beban['id_dosen'] ?? '') == $dosen->Id_Dosen ? 'selected' : '' }}>
                                                        {{ $dosen->Nama_Dosen }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <div class="autocomplete-wrapper">
                                                <input type="text" class="form-control form-control-sm matakuliah-autocomplete" 
                                                       placeholder="Ketik nama mata kuliah..."
                                                       value="{{ $selectedMk ? $selectedMk->Nama_Matakuliah : '' }}" autocomplete="off">
                                                <input type="hidden" class="matakuliah-id-hidden" name="beban[{{ $index }}][mata_kuliah_id]" 
                                                       value="{{ $beban['id_mata_kuliah'] ?? '' }}" required>
                                                <div class="autocomplete-suggestions matakuliah-suggestions"></div>
                                            </div>
                                        </td>
                                        <td class="kelas-display">{{ $beban['kelas'] ?? '-' }}</td>
                                        <td class="text-center">
                                            <input type="number" class="form-control form-control-sm text-center sks-input" 
                                                   name="beban[{{ $index }}][sks]" value="{{ $beban['sks'] ?? 0 }}" min="1" max="6" required>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-danger btn-sm hapus-beban">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="table-secondary fw-bold">
                                        <td colspan="4" class="text-end">Total SKS:</td>
                                        <td class="text-center" id="totalSKS">{{ collect($bebanData)->sum('sks') }}</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('kaprodi.sk.beban-mengajar.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Batal
                        </a>
                        <button type="submit" class="btn btn-warning" id="btnSubmit">
                            <i class="fas fa-paper-plane me-2"></i>Ajukan Ulang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Autocomplete Styles */
    .autocomplete-wrapper {
        position: relative;
    }
    
    .autocomplete-suggestions {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        max-height: 200px;
        overflow-y: auto;
        background: #fff;
        border: 1px solid #dee2e6;
        border-top: none;
        border-radius: 0 0 0.375rem 0.375rem;
        z-index: 9999;
        display: none;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .autocomplete-suggestions.show {
        display: block;
    }
    
    .autocomplete-item {
        padding: 8px 12px;
        cursor: pointer;
        border-bottom: 1px solid #f0f0f0;
        font-size: 0.85rem;
    }
    
    .autocomplete-item:last-child {
        border-bottom: none;
    }
    
    .autocomplete-item:hover,
    .autocomplete-item.active {
        background-color: #e9ecef;
    }
    
    .autocomplete-item .item-name {
        font-weight: 500;
    }
    
    .autocomplete-item .item-info {
        font-size: 0.75rem;
        color: #6c757d;
    }
    
    .autocomplete-no-result {
        padding: 8px 12px;
        color: #6c757d;
        font-style: italic;
        font-size: 0.85rem;
    }

    .table-responsive {
        overflow: visible;
    }
</style>
@endpush

@push('scripts')
<script>
    let rowCounter = {{ count($bebanData) }};
    const dosensData = @json($dosens);
    const mataKuliahData = @json($mataKuliahList);

    // Initialize autocomplete for existing rows on page load
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('#bodyBebanMengajar tr').forEach(row => {
            const mkInput = row.querySelector('.matakuliah-autocomplete');
            if (mkInput) {
                initMataKuliahAutocomplete(mkInput);
            }
        });
    });

    document.getElementById('tambahBeban').addEventListener('click', function() {
        addNewRow();
    });

    document.getElementById('bodyBebanMengajar').addEventListener('click', function(e) {
        if (e.target.closest('.hapus-beban')) {
            e.target.closest('tr').remove();
            updateRowNumbers();
            calculateTotalSKS();
        }
    });

    document.getElementById('bodyBebanMengajar').addEventListener('change', function(e) {
        if (e.target.classList.contains('sks-input')) {
            calculateTotalSKS();
        }
    });

    // Initialize autocomplete for mata kuliah
    function initMataKuliahAutocomplete(inputElement) {
        const wrapper = inputElement.closest('.autocomplete-wrapper');
        const hiddenInput = wrapper.querySelector('.matakuliah-id-hidden');
        const suggestionsDiv = wrapper.querySelector('.matakuliah-suggestions');
        const row = inputElement.closest('tr');
        let activeIndex = -1;
        
        inputElement.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            hiddenInput.value = '';
            row.querySelector('.kelas-display').textContent = '-';
            row.querySelector('.sks-input').value = 0;
            
            if (searchTerm.length < 1) {
                suggestionsDiv.classList.remove('show');
                suggestionsDiv.innerHTML = '';
                return;
            }
            
            // Filter mata kuliah
            const filtered = mataKuliahData.filter(mk => {
                const nama = mk.Nama_Matakuliah.toLowerCase();
                const kelas = (mk.Kelas || '').toLowerCase();
                return nama.includes(searchTerm) || kelas.includes(searchTerm);
            });
            
            // Build suggestions HTML
            if (filtered.length > 0) {
                suggestionsDiv.innerHTML = filtered.slice(0, 15).map((mk, index) => `
                    <div class="autocomplete-item" data-id="${mk.Nomor}" data-nama="${mk.Nama_Matakuliah}" data-kelas="${mk.Kelas || '-'}" data-sks="${mk.SKS || 0}">
                        <div class="item-name">${mk.Nama_Matakuliah}</div>
                        <div class="item-info">Kelas: ${mk.Kelas || '-'} | SKS: ${mk.SKS || 0}</div>
                    </div>
                `).join('');
            } else {
                suggestionsDiv.innerHTML = '<div class="autocomplete-no-result">Mata kuliah tidak ditemukan</div>';
            }
            
            suggestionsDiv.classList.add('show');
            activeIndex = -1;
            
            // Add click handlers
            suggestionsDiv.querySelectorAll('.autocomplete-item').forEach(item => {
                item.addEventListener('click', function() {
                    selectMataKuliah(inputElement, hiddenInput, row, this);
                    suggestionsDiv.classList.remove('show');
                });
            });
        });
        
        // Focus event - show suggestions if has value
        inputElement.addEventListener('focus', function() {
            if (this.value.trim().length >= 1) {
                this.dispatchEvent(new Event('input'));
            }
        });
        
        // Keyboard navigation
        inputElement.addEventListener('keydown', function(e) {
            const items = suggestionsDiv.querySelectorAll('.autocomplete-item');
            
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                activeIndex = Math.min(activeIndex + 1, items.length - 1);
                updateActiveItem(items, activeIndex);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                activeIndex = Math.max(activeIndex - 1, 0);
                updateActiveItem(items, activeIndex);
            } else if (e.key === 'Enter') {
                e.preventDefault();
                if (activeIndex >= 0 && items[activeIndex]) {
                    selectMataKuliah(inputElement, hiddenInput, row, items[activeIndex]);
                    suggestionsDiv.classList.remove('show');
                }
            } else if (e.key === 'Escape') {
                suggestionsDiv.classList.remove('show');
            }
        });
        
        // Close on outside click
        document.addEventListener('click', function(e) {
            if (!wrapper.contains(e.target)) {
                suggestionsDiv.classList.remove('show');
            }
        });
    }
    
    // Select mata kuliah helper
    function selectMataKuliah(inputElement, hiddenInput, row, item) {
        const id = item.dataset.id;
        const nama = item.dataset.nama;
        const kelas = item.dataset.kelas;
        const sks = item.dataset.sks;
        
        inputElement.value = nama;
        hiddenInput.value = id;
        row.querySelector('.kelas-display').textContent = kelas;
        row.querySelector('.sks-input').value = sks;
        
        calculateTotalSKS();
    }
    
    // Helper function to update active item
    function updateActiveItem(items, activeIndex) {
        items.forEach((item, index) => {
            if (index === activeIndex) {
                item.classList.add('active');
                item.scrollIntoView({ block: 'nearest' });
            } else {
                item.classList.remove('active');
            }
        });
    }

    function addNewRow() {
        const tbody = document.getElementById('bodyBebanMengajar');
        const rowIndex = rowCounter++;

        let dosenOptions = '<option value="">-- Pilih Dosen --</option>';
        dosensData.forEach(d => {
            dosenOptions += `<option value="${d.Id_Dosen}">${d.Nama_Dosen}</option>`;
        });

        const newRow = `
            <tr data-index="${rowIndex}">
                <td class="text-center row-number">${tbody.children.length + 1}</td>
                <td>
                    <select class="form-select form-select-sm select-dosen" name="beban[${rowIndex}][dosen_id]" required>
                        ${dosenOptions}
                    </select>
                </td>
                <td>
                    <div class="autocomplete-wrapper">
                        <input type="text" class="form-control form-control-sm matakuliah-autocomplete" 
                               placeholder="Ketik nama mata kuliah..." autocomplete="off">
                        <input type="hidden" class="matakuliah-id-hidden" name="beban[${rowIndex}][mata_kuliah_id]" value="" required>
                        <div class="autocomplete-suggestions matakuliah-suggestions"></div>
                    </div>
                </td>
                <td class="kelas-display">-</td>
                <td class="text-center">
                    <input type="number" class="form-control form-control-sm text-center sks-input" 
                           name="beban[${rowIndex}][sks]" value="0" min="1" max="6" required>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm hapus-beban">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;

        tbody.insertAdjacentHTML('beforeend', newRow);
        
        // Initialize autocomplete for the new row
        const newRowElement = tbody.lastElementChild;
        const mkInput = newRowElement.querySelector('.matakuliah-autocomplete');
        initMataKuliahAutocomplete(mkInput);
    }

    function updateRowNumbers() {
        document.querySelectorAll('#bodyBebanMengajar tr').forEach((row, index) => {
            row.querySelector('.row-number').textContent = index + 1;
        });
    }

    function calculateTotalSKS() {
        let total = 0;
        document.querySelectorAll('.sks-input').forEach(input => {
            total += parseInt(input.value) || 0;
        });
        document.getElementById('totalSKS').textContent = total;
    }
    
    // Form validation
    document.getElementById('formBebanMengajar').addEventListener('submit', function(e) {
        const rowCount = document.querySelectorAll('#bodyBebanMengajar tr').length;
        
        if (rowCount === 0) {
            e.preventDefault();
            alert('Harap tambahkan minimal 1 beban mengajar!');
            return false;
        }
        
        // Validate all mata kuliah are selected
        let hasError = false;
        document.querySelectorAll('#bodyBebanMengajar tr').forEach(row => {
            const mkHidden = row.querySelector('.matakuliah-id-hidden');
            if (!mkHidden.value) {
                alert('Harap pilih mata kuliah dari daftar rekomendasi!');
                hasError = true;
                return;
            }
        });
        
        if (hasError) {
            e.preventDefault();
            return false;
        }
        
        // Disable submit button
        document.getElementById('btnSubmit').disabled = true;
        document.getElementById('btnSubmit').innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sedang diproses...';
    });
</script>
@endpush

@endsection
