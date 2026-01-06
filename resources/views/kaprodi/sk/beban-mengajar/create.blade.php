@extends('layouts.kaprodi')

@section('title', 'Ajukan SK Beban Mengajar')

@section('content')

<div class="mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.kaprodi') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kaprodi.sk.index') }}">Ajukan SK</a></li>
            <li class="breadcrumb-item active">SK Beban Mengajar</li>
        </ol>
    </nav>
</div>

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white">
                <div class="d-flex align-items-center">
                    <i class="fas fa-chalkboard-teacher fa-lg me-3"></i>
                    <div>
                        <h5 class="mb-0 fw-bold">Ajukan SK Beban Mengajar</h5>
                        <small>Isi data beban mengajar dosen untuk semester aktif</small>
                    </div>
                </div>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('kaprodi.sk.beban-mengajar.store') }}" method="POST" id="formBebanMengajar">
                    @csrf

                    <!-- Identitas Pengajuan -->
                    <div class="mb-4">
                        <h6 class="fw-bold text-primary mb-3">
                            <i class="fas fa-info-circle me-2"></i>Identitas Pengajuan
                        </h6>
                        <div class="row g-3">
                            <!-- Program Studi -->
                            <div class="col-md-4">
                                <label for="prodi_id" class="form-label fw-semibold">
                                    Program Studi <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('prodi_id') is-invalid @enderror" 
                                        id="prodi_id" 
                                        name="prodi_id" 
                                        required>
                                    <option value="">-- Pilih Program Studi --</option>
                                    @foreach($prodis as $p)
                                        <option value="{{ $p->Id_Prodi }}" {{ (old('prodi_id', $prodi->Id_Prodi ?? '') == $p->Id_Prodi) ? 'selected' : '' }}>
                                            {{ $p->Nama_Prodi }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('prodi_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Semester -->
                            <div class="col-md-4">
                                <label for="semester" class="form-label fw-semibold">
                                    Semester <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('semester') is-invalid @enderror" 
                                        id="semester" 
                                        name="semester" 
                                        required>
                                    <option value="">-- Pilih Semester --</option>
                                    <option value="Ganjil">Ganjil</option>
                                    <option value="Genap">Genap</option>
                                </select>
                                @error('semester')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Tahun Akademik -->
                            <div class="col-md-4">
                                <label for="tahun_akademik" class="form-label fw-semibold">
                                    Tahun Akademik <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('tahun_akademik') is-invalid @enderror" 
                                       id="tahun_akademik" 
                                       name="tahun_akademik" 
                                       placeholder="Contoh: 2025/2026"
                                       value="{{ old('tahun_akademik') }}"
                                       required>
                                @error('tahun_akademik')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Format: YYYY/YYYY (contoh: 2025/2026)</small>
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
                                    <!-- Rows will be added here dynamically -->
                                </tbody>
                                <tfoot>
                                    <tr class="table-secondary fw-bold">
                                        <td colspan="4" class="text-end">Total SKS:</td>
                                        <td class="text-center" id="totalSKS">0</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="alert alert-info mt-3" role="alert">
                            <i class="fas fa-info-circle me-2"></i>
                            <small>
                                <strong>Petunjuk:</strong> Klik tombol "Tambah Beban Mengajar" untuk menambahkan data dosen dan mata kuliah yang diampu. Anda dapat menambahkan beberapa beban mengajar sekaligus.
                            </small>
                        </div>
                    </div>

                    <!-- Catatan (Optional) -->
                    <div class="mb-4">
                        <label for="catatan" class="form-label fw-semibold">
                            Catatan (Opsional)
                        </label>
                        <textarea class="form-control" 
                                  id="catatan" 
                                  name="catatan" 
                                  rows="3" 
                                  placeholder="Tambahkan catatan atau keterangan tambahan jika diperlukan">{{ old('catatan') }}</textarea>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('kaprodi.sk.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Kembali
                        </a>
                        <button type="submit" class="btn btn-primary" id="btnSubmit">
                            <i class="fas fa-paper-plane me-2"></i>Ajukan SK
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .table-hover tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.02);
    }
    
    .btn-hapus {
        transition: all 0.2s ease;
    }
    
    .btn-hapus:hover {
        transform: scale(1.1);
    }
</style>
@endpush

@push('scripts')
<script>
    let counter = 0;
    
    // Data dari server
    const dosens = @json($dosens);
    const mataKuliahList = @json($mataKuliahList);
    const allMataKuliah = @json($allMataKuliah);
    
    // Debug: Tampilkan data di console
    console.log('Dosens:', dosens);
    console.log('Mata Kuliah List:', mataKuliahList);
    console.log('All Mata Kuliah:', allMataKuliah);
    
    // Fungsi untuk mendapatkan kelas berdasarkan nama mata kuliah
    function getKelasByMataKuliah(namaMataKuliah) {
        return allMataKuliah.filter(mk => mk.Nama_Matakuliah === namaMataKuliah);
    }
    
    // Tambah baris beban mengajar
    document.getElementById('tambahBeban').addEventListener('click', function() {
        counter++;
        const tbody = document.getElementById('bodyBebanMengajar');
        
        const row = document.createElement('tr');
        row.id = `row-${counter}`;
        
        // Build dosen options
        let dosenOptions = '<option value="">-- Pilih Dosen --</option>';
        dosens.forEach(dosen => {
            dosenOptions += `<option value="${dosen.Id_Dosen}">${dosen.Nama_Dosen}</option>`;
        });
        
        // Build mata kuliah options
        let mataKuliahOptions = '<option value="">-- Pilih Mata Kuliah --</option>';
        mataKuliahList.forEach(mk => {
            mataKuliahOptions += `<option value="${mk.Nama_Matakuliah}" data-sks="${mk.SKS}">${mk.Nama_Matakuliah}</option>`;
        });
        
        row.innerHTML = `
            <td class="text-center align-middle">${counter}</td>
            <td>
                <select class="form-select form-select-sm" name="beban[${counter}][dosen_id]" required>
                    ${dosenOptions}
                </select>
            </td>
            <td>
                <select class="form-select form-select-sm mata-kuliah-select" 
                        data-row="${counter}" 
                        required>
                    ${mataKuliahOptions}
                </select>
            </td>
            <td>
                <select class="form-select form-select-sm kelas-select" 
                        name="beban[${counter}][mata_kuliah_id]" 
                        data-row="${counter}" 
                        required 
                        disabled>
                    <option value="">-- Pilih MK dulu --</option>
                </select>
            </td>
            <td>
                <input type="number" 
                       class="form-control form-control-sm text-center sks-input" 
                       name="beban[${counter}][sks]" 
                       min="1" 
                       max="6" 
                       value="3"
                       readonly
                       required>
            </td>
            <td class="text-center">
                <button type="button" 
                        class="btn btn-danger btn-sm btn-hapus" 
                        onclick="hapusBaris(${counter})">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        
        tbody.appendChild(row);
        
        // Add event listener untuk mata kuliah change
        const mataKuliahSelect = row.querySelector('.mata-kuliah-select');
        const kelasSelect = row.querySelector('.kelas-select');
        const sksInput = row.querySelector('.sks-input');
        
        mataKuliahSelect.addEventListener('change', function() {
            const namaMataKuliah = this.value;
            kelasSelect.innerHTML = '<option value="">-- Pilih Kelas --</option>';
            
            if (namaMataKuliah) {
                const kelasList = getKelasByMataKuliah(namaMataKuliah);
                
                kelasList.forEach(kelas => {
                    const option = document.createElement('option');
                    option.value = kelas.Nomor;
                    option.textContent = kelas.Kelas;
                    option.dataset.sks = kelas.SKS;
                    kelasSelect.appendChild(option);
                });
                
                kelasSelect.disabled = false;
                
                // Set SKS dari mata kuliah yang dipilih
                const selectedOption = this.options[this.selectedIndex];
                sksInput.value = selectedOption.dataset.sks || 3;
            } else {
                kelasSelect.disabled = true;
                sksInput.value = 3;
            }
            
            updateTotalSKS();
        });
        
        // Add event listener untuk kelas change (update SKS jika berbeda)
        kelasSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.dataset.sks) {
                sksInput.value = selectedOption.dataset.sks;
                updateTotalSKS();
            }
        });
        
        // Add event listener untuk update total SKS
        sksInput.addEventListener('input', updateTotalSKS);
        
        updateNomor();
        updateTotalSKS();
    });
    
    // Hapus baris
    function hapusBaris(id) {
        const row = document.getElementById(`row-${id}`);
        if (row) {
            row.remove();
            updateNomor();
            updateTotalSKS();
        }
    }
    
    // Update nomor urut
    function updateNomor() {
        const rows = document.querySelectorAll('#bodyBebanMengajar tr');
        rows.forEach((row, index) => {
            row.querySelector('td:first-child').textContent = index + 1;
        });
    }
    
    // Update total SKS
    function updateTotalSKS() {
        const sksInputs = document.querySelectorAll('.sks-input');
        let total = 0;
        
        sksInputs.forEach(input => {
            const value = parseInt(input.value) || 0;
            total += value;
        });
        
        document.getElementById('totalSKS').textContent = total;
    }
    
    // Validasi form sebelum submit
    document.getElementById('formBebanMengajar').addEventListener('submit', function(e) {
        const rows = document.querySelectorAll('#bodyBebanMengajar tr');
        
        if (rows.length === 0) {
            e.preventDefault();
            alert('Harap tambahkan minimal 1 beban mengajar!');
            return false;
        }
        
        // Konfirmasi submit
        if (!confirm('Apakah Anda yakin data sudah benar dan ingin mengajukan SK ini?')) {
            e.preventDefault();
            return false;
        }
    });
    
    // Auto-add satu baris pertama saat halaman load
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('tambahBeban').click();
    });
</script>
@endpush

@endsection
