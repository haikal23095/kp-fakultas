@extends('layouts.wadek1')

@section('title', 'Riwayat SK Penguji Skripsi - Wadek 1')

@push('styles')
<style>
    .preview-document {
        font-family: 'Times New Roman', Times, serif;
        background: #ffffff;
        color: #000;
        border: 1px solid #000;
        padding: 2cm 2.5cm;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        font-size: 11pt;
        line-height: 1.5;
        min-height: 500px;
        width: 21cm;
        max-width: 100%;
        margin: 0 auto;
    }
    .preview-header {
        text-align: center;
        margin-bottom: 20px;
        border-bottom: 3px double #000;
        padding-bottom: 10px;
    }
    .preview-header img {
        width: 80px;
        float: left;
        margin-top: -5px;
    }
    .preview-header strong {
        display: block;
        text-transform: uppercase;
    }
    .preview-header .line-1 { font-size: 14pt; font-weight: bold; }
    .preview-header .line-2 { font-size: 16pt; font-weight: bold; }
    .preview-header .line-3 { font-size: 14pt; font-weight: bold; }
    .preview-header .address {
        font-size: 10pt;
        margin-top: 5px;
        font-weight: normal;
    }
    .preview-table-mahasiswa {
        width: 100%;
        border-collapse: collapse;
        margin: 15px 0;
        font-size: 10pt;
        border: 1px solid #000;
    }
    .preview-table-mahasiswa th,
    .preview-table-mahasiswa td {
        border: 1px solid #000;
        padding: 5px 8px;
        vertical-align: middle;
        line-height: 1.3;
        color: #000;
    }
    .preview-table-mahasiswa thead th {
        background-color: #ffffff;
        font-weight: bold;
        text-align: center;
        text-transform: capitalize;
    }
    .preview-table-mahasiswa tbody td {
        font-size: 9pt;
        vertical-align: top;
    }
    .preview-table-mahasiswa tbody td:nth-child(1) {
        text-align: center;
        vertical-align: top;
    }
</style>
@endpush

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-0">Riwayat SK Penguji Skripsi</h1>
        <p class="mb-0 text-muted">Daftar SK Penguji Skripsi yang telah diproses</p>
    </div>
    <div>
        <a href="{{ route('wadek1.sk.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>

<!-- Filter Section -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('wadek1.sk.penguji-skripsi.history') }}">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="status" class="form-label">Filter Status</label>
                    <select name="status" id="status" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="Menunggu-Persetujuan-Dekan" {{ request('status') == 'Menunggu-Persetujuan-Dekan' ? 'selected' : '' }}>
                            Menunggu Persetujuan Dekan
                        </option>
                        <option value="Selesai" {{ request('status') == 'Selesai' ? 'selected' : '' }}>
                            Selesai
                        </option>
                        <option value="Ditolak-Wadek1" {{ request('status') == 'Ditolak-Wadek1' ? 'selected' : '' }}>
                            Ditolak Wadek1
                        </option>
                        <option value="Ditolak-Dekan" {{ request('status') == 'Ditolak-Dekan' ? 'selected' : '' }}>
                            Ditolak Dekan
                        </option>
                    </select>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Table Section -->
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 50px;">No</th>
                        <th>Semester</th>
                        <th>No. SK</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Jumlah Mahasiswa</th>
                        <th class="text-center">Status</th>
                        <th class="text-center" style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($skList as $index => $sk)
                    <tr>
                        <td class="text-center">{{ $skList->firstItem() + $index }}</td>
                        <td>{{ $sk->Semester ?? '-' }}</td>
                        <td>
                            <strong>{{ $sk->Nomor_Surat ?? 'Belum ada nomor' }}</strong>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($sk->{'Tanggal-Pengajuan'})->format('d M Y') }}</td>
                        <td>
                            @php
                                // Ambil data penguji dari JSON
                                $dataPenguji = $sk->Data_Penguji_Skripsi;
                                if (is_string($dataPenguji)) {
                                    $dataPenguji = json_decode($dataPenguji, true);
                                }
                                $jumlahMahasiswa = is_array($dataPenguji) ? count($dataPenguji) : 0;
                            @endphp
                            <span class="badge bg-secondary">{{ $jumlahMahasiswa }} Mahasiswa</span>
                        </td>
                        <td class="text-center">
                            @if($sk->Status == 'Menunggu-Persetujuan-Dekan')
                                <span class="badge bg-warning text-dark">
                                    <i class="fas fa-clock me-1"></i>Menunggu Dekan
                                </span>
                            @elseif($sk->Status == 'Selesai')
                                <span class="badge bg-success">
                                    <i class="fas fa-check-circle me-1"></i>Selesai
                                </span>
                            @elseif($sk->Status == 'Ditolak-Wadek1')
                                <span class="badge bg-danger">
                                    <i class="fas fa-times-circle me-1"></i>Ditolak Wadek1
                                </span>
                            @elseif($sk->Status == 'Ditolak-Dekan')
                                <span class="badge bg-danger">
                                    <i class="fas fa-times-circle me-1"></i>Ditolak Dekan
                                </span>
                            @else
                                <span class="badge bg-secondary">{{ $sk->Status }}</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-info text-white" onclick="showDetail({{ $sk->No }})">
                                <i class="fas fa-eye me-1"></i>Lihat Detail
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                            Belum ada riwayat SK Penguji Skripsi
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($skList->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $skList->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Info Section -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm bg-light">
            <div class="card-body">
                <div class="d-flex align-items-start">
                    <div class="me-3">
                        <i class="fas fa-info-circle fa-2x text-primary"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-2">Keterangan Status</h6>
                        <ul class="mb-0 small text-muted">
                            <li><strong>Menunggu Persetujuan Dekan:</strong> SK telah disetujui oleh Wadek 1 dan menunggu persetujuan Dekan</li>
                            <li><strong>Selesai:</strong> SK telah disetujui oleh Dekan dan proses selesai</li>
                            <li><strong>Ditolak Wadek1:</strong> SK ditolak oleh Wadek 1</li>
                            <li><strong>Ditolak Dekan:</strong> SK ditolak oleh Dekan</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

<!-- Modal Detail SK -->
<div class="modal fade" id="modalDetail" tabindex="-1" aria-labelledby="modalDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalDetailLabel">
                    <i class="fas fa-file-alt me-2"></i>Detail SK Penguji Skripsi
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div style="max-height: 750px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 4px;">
                    <div class="preview-document" id="previewContent">
                        <div class="text-center">
                            <i class="fas fa-spinner fa-spin fa-3x text-muted mb-3"></i>
                            <p>Memuat detail SK...</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Tutup
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const dekanName = @json($dekanName ?? '');
    const dekanNip = @json($dekanNip ?? '');

    function showDetail(skId) {
        const modal = new bootstrap.Modal(document.getElementById('modalDetail'));
        modal.show();

        console.log('Fetching SK detail for ID:', skId);

        fetch(`{{ url('/wadek1/sk-penguji-skripsi') }}/${skId}`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                renderPreview(data.sk, data.dekanName, data.dekanNip);
            } else {
                document.getElementById('previewContent').innerHTML = 
                    `<div class="alert alert-danger">${data.message || 'Gagal memuat detail SK'}</div>`;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('previewContent').innerHTML = 
                `<div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    Terjadi kesalahan saat memuat detail: ${error.message}
                </div>`;
        });
    }

    function renderPreview(sk, dekanNameParam, dekanNipParam) {
        const finalDekanName = dekanNameParam || dekanName || '-';
        const finalDekanNip = dekanNipParam || dekanNip || '-';
        
        // Handle berbagai tipe data untuk Data_Penguji_Skripsi
        let dataPenguji = sk.Data_Penguji_Skripsi || [];
        
        // Jika string, parse JSON
        if (typeof dataPenguji === 'string') {
            try {
                dataPenguji = JSON.parse(dataPenguji);
            } catch (e) {
                console.error('Error parsing Data_Penguji_Skripsi:', e);
                dataPenguji = [];
            }
        }
        
        // Jika object bukan array, convert ke array
        if (dataPenguji && typeof dataPenguji === 'object' && !Array.isArray(dataPenguji)) {
            dataPenguji = Object.values(dataPenguji);
        }
        
        // Pastikan dataPenguji adalah array
        if (!Array.isArray(dataPenguji)) {
            dataPenguji = [];
        }
        
        const semesterUpper = (sk.Semester || 'GANJIL').toUpperCase();
        const semesterText = sk.Semester || 'Ganjil';
        const tahunAkademik = sk.Tahun_Akademik || '2023/2024';
        const nomorSurat = sk.Nomor_Surat || '-';

        // Kelompokkan mahasiswa per jurusan
        const groupedByJurusan = {};
        dataPenguji.forEach(mhs => {
            let jurusanName = '-';
            if (mhs.prodi_data && mhs.prodi_data.jurusan && mhs.prodi_data.jurusan.Nama_Jurusan) {
                jurusanName = mhs.prodi_data.jurusan.Nama_Jurusan;
            } else if (mhs.prodi && mhs.prodi !== '-') {
                jurusanName = mhs.prodi;
            } else if (mhs.prodi_data && mhs.prodi_data.nama_prodi) {
                jurusanName = mhs.prodi_data.nama_prodi;
            }
            
            const prodiName = (mhs.prodi_data && mhs.prodi_data.nama_prodi) ? mhs.prodi_data.nama_prodi : (mhs.prodi || '-');
            
            if (!groupedByJurusan[jurusanName]) {
                groupedByJurusan[jurusanName] = { jurusan: jurusanName, prodi: [] };
            }
            if (!groupedByJurusan[jurusanName].prodi.includes(prodiName)) {
                groupedByJurusan[jurusanName].prodi.push(prodiName);
            }
            if (!groupedByJurusan[jurusanName].mahasiswa) {
                groupedByJurusan[jurusanName].mahasiswa = [];
            }
            groupedByJurusan[jurusanName].mahasiswa.push(mhs);
        });

        let lampiranHtml = '';
        Object.keys(groupedByJurusan).forEach((jurusanName, index) => {
            const jurusanData = groupedByJurusan[jurusanName];
            const mahasiswaProdi = jurusanData.mahasiswa;
            const prodiName = jurusanData.prodi[0] || jurusanName;
            
            lampiranHtml += `
                <div class="lampiran-prodi" style="margin-top: ${index === 0 ? '30px' : '60px'}; page-break-before: ${index === 0 ? 'auto' : 'always'};">
                    <div style="font-size: 11pt; text-align: left; margin-bottom: 10px;">
                        <p style="margin: 0 0 3px 0; font-weight: normal; font-size: 9pt;">SALINAN</p>
                        <p style="margin: 0 0 3px 0; font-weight: normal; font-size: 9pt;">LAMPIRAN KEPUTUSAN DEKAN FAKULTAS TEKNIK UNIVERSITAS TRUNOJOYO MADURA</p>
                        <p style="margin: 0 0 3px 0; font-weight: normal; font-size: 9pt;">NOMOR ${nomorSurat}</p>
                        <p style="margin: 0 0 10px 0; font-weight: normal; font-size: 9pt;">TENTANG</p>
                        <p style="margin: 0 0 10px 0; font-weight: normal; font-size: 9pt;">PENETAPAN DOSEN PENGUJI SKRIPSI PROGRAM STUDI ${prodiName.toUpperCase()} FAKULTAS TEKNIK UNIVERSITAS TRUNOJOYO MADURA SEMESTER ${semesterUpper} TAHUN AKADEMIK ${tahunAkademik}</p>
                        <p style="margin: 0 0 10px 0; text-align: center; font-weight: bold;">DAFTAR MAHASISWA DAN DOSEN PENGUJI SKRIPSI</p>
                        <p style="margin: 0 0 10px 0; text-align: center; font-weight: bold;">PROGRAM STUDI ${prodiName.toUpperCase()} FAKULTAS TEKNIK</p>
                        <p style="margin: 0 0 15px 0; text-align: center; font-weight: bold;">UNIVERSITAS TRUNOJOYO MADURA</p>
                        <p style="margin: 0 0 15px 0; text-align: center; font-weight: bold;">SEMESTER ${semesterUpper} TAHUN AKADEMIK ${tahunAkademik}</p>
                    </div>
                    <table class="preview-table-mahasiswa">
                        <colgroup>
                            <col style="width: 3%;">
                            <col style="width: 7%;">
                            <col style="width: 12%;">
                            <col style="width: 20%;">
                            <col style="width: 19%;">
                            <col style="width: 19%;">
                            <col style="width: 19%;">
                        </colgroup>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>NIM</th>
                                <th>Nama Mahasiswa</th>
                                <th>Judul Skripsi</th>
                                <th>Penguji 1</th>
                                <th>Penguji 2</th>
                                <th>Penguji 3</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${mahasiswaProdi.map((mhs, idx) => {
                                // Helper function untuk get nama dosen dengan berbagai kemungkinan field
                                const getNamaDosen = (penguji) => {
                                    if (!penguji) return '-';
                                    if (typeof penguji === 'string') return penguji;
                                    return penguji.nama_dosen || penguji.nama || penguji.Nama_Dosen || penguji.Nama || '-';
                                };
                                
                                // Helper function untuk get NIP dengan berbagai kemungkinan field
                                const getNIP = (penguji) => {
                                    if (!penguji) return '-';
                                    if (typeof penguji === 'string') return '';
                                    return penguji.nip || penguji.NIP || '';
                                };
                                
                                return `
                                <tr>
                                    <td style="text-align: center;">${idx + 1}</td>
                                    <td style="text-align: center;">${mhs.nim || '-'}</td>
                                    <td>${mhs.nama_mahasiswa || mhs.nama || '-'}</td>
                                    <td style="font-size: 9pt;">${mhs.judul_skripsi || mhs.judul || '-'}</td>
                                    <td style="font-size: 9pt;">
                                        ${getNamaDosen(mhs.penguji_1)}<br>
                                        <small>${getNIP(mhs.penguji_1) ? 'NIP: ' + getNIP(mhs.penguji_1) : ''}</small>
                                    </td>
                                    <td style="font-size: 9pt;">
                                        ${getNamaDosen(mhs.penguji_2)}<br>
                                        <small>${getNIP(mhs.penguji_2) ? 'NIP: ' + getNIP(mhs.penguji_2) : ''}</small>
                                    </td>
                                    <td style="font-size: 9pt;">
                                        ${getNamaDosen(mhs.penguji_3)}<br>
                                        <small>${getNIP(mhs.penguji_3) ? 'NIP: ' + getNIP(mhs.penguji_3) : ''}</small>
                                    </td>
                                </tr>
                                `;
                            }).join('')}
                        </tbody>
                    </table>
                    <div style="margin-top: 50px; font-size: 10pt;">
                        <div style="text-align: right;">
                            <p style="margin: 0 0 3px 0;">Ditetapkan di Bangkalan</p>
                            <p style="margin: 0 0 30px 0;">pada tanggal ${new Date(sk.Tanggal_Persetujuan_Dekan || new Date()).toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'})}</p>
                            <p style="margin: 0 0 70px 0;"><strong>DEKAN,</strong></p>
                            <p style="margin: 0 0 3px 0; text-decoration: underline;"><strong>${finalDekanName}</strong></p>
                            <p style="margin: 0;">NIP. ${finalDekanNip}</p>
                        </div>
                    </div>
                </div>
            `;
        });

        const html = `
            <div class="preview-header">
                <img src="{{ asset('images/logo_unijoyo.png') }}" alt="Logo UTM">
                <strong class="line-1">KEMENTERIAN PENDIDIKAN, KEBUDAYAAN,</strong>
                <strong class="line-1">RISET DAN TEKNOLOGI</strong>
                <strong class="line-2">UNIVERSITAS TRUNOJOYO MADURA</strong>
                <strong class="line-3">FAKULTAS TEKNIK</strong>
                <div class="address">
                    Jl. Raya Telang PO BOX 2 Kamal, Bangkalan - Madura<br>
                    Telp: (031) 3011146, Fax. (031) 3011506<br>
                    Laman: www.trunojoyo.ac.id
                </div>
                <div style="clear: both;"></div>
            </div>

            <div style="text-align: center; margin: 20px 0; font-weight: bold; font-size: 12pt;">
                KEPUTUSAN DEKAN FAKULTAS TEKNIK<br>
                UNIVERSITAS TRUNOJOYO MADURA
            </div>

            <div style="text-align: center; margin: 15px 0; font-size: 12pt;">
                NOMOR: ${nomorSurat}
            </div>

            <div style="text-align: center; margin: 15px 0; font-weight: bold; font-size: 11pt;">
                TENTANG
            </div>

            <div style="text-align: center; margin: 15px 0; font-weight: bold; font-size: 11pt;">
                PENETAPAN DOSEN PENGUJI SKRIPSI<br>
                FAKULTAS TEKNIK UNIVERSITAS TRUNOJOYO MADURA<br>
                SEMESTER ${semesterUpper} TAHUN AKADEMIK ${tahunAkademik}
            </div>

            <div style="margin: 20px 0; font-weight: bold; font-size: 11pt;">
                DEKAN FAKULTAS TEKNIK UNIVERSITAS TRUNOJOYO MADURA,
            </div>

            <div style="text-align: justify; margin-bottom: 20px; font-size: 10pt;">
                <table style="width: 100%; margin-bottom: 15px;">
                    <tr>
                        <td style="width: 120px; vertical-align: top;"><strong>Menimbang</strong></td>
                        <td style="width: 20px; vertical-align: top;">:</td>
                        <td style="vertical-align: top;">
                            <table style="width: 100%; border: none;">
                                <tr>
                                    <td style="width: 30px; vertical-align: top; border: none;">a.</td>
                                    <td style="border: none;">Bahwa untuk memperlancar pelaksanaan ujian Skripsi mahasiswa, perlu menugaskan dosen sebagai penguji Skripsi;</td>
                                </tr>
                                <tr>
                                    <td style="vertical-align: top; border: none; padding-top: 5px;">b.</td>
                                    <td style="border: none; padding-top: 5px;">Bahwa untuk melaksanakan butir a di atas, perlu ditetapkan dalam Keputusan Dekan Fakultas Teknik;</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

                <table style="width: 100%; margin-bottom: 15px;">
                    <tr>
                        <td style="width: 120px; vertical-align: top;"><strong>Mengingat</strong></td>
                        <td style="width: 20px; vertical-align: top;">:</td>
                        <td style="vertical-align: top;">
                            <ol style="margin: 0; padding-left: 20px;">
                                <li style="margin-bottom: 5px;">Undang-Undang Nomor 20 tahun 2003, tentang Sistem Pendidikan Nasional;</li>
                                <li style="margin-bottom: 5px;">Peraturan Pemerintah Nomor 4 Tahun 2012 Tentang Penyelenggaraan Pendidikan Tinggi;</li>
                                <li style="margin-bottom: 5px;">Peraturan Presiden RI Nomor 4 Tahun 2014 Tentang Perubahan Penyelenggaraan dan Pengelolaan Perguruan Tinggi;</li>
                                <li style="margin-bottom: 5px;">Keputusan RI Nomor 85 tahun 2001, tentang Statuta Universitas Trunojoyo Madura;</li>
                                <li style="margin-bottom: 5px;">Keputusan Menteri Pendidikan dan Kebudayaan RI Nomor 232/ U/ 2000, tentang pedoman Penyusunan Kurikulum Pendidikan Tinggi dan Penilaian Hasil Belajar Mahasiswa;</li>
                                <li style="margin-bottom: 5px;">Peraturan Menteri Pendidikan, Kebudayaan, Riset, dan Teknologi RI Nomor 79/M/MPK.A/ KP.09.02/ 2022 tentang pengangkatan Rektor UTM periode 2022-2026;</li>
                                <li>Keputusan Rektor Universitas Trunojoyo Madura Nomor 1357/UNM3/KP/ 2023 tentang Pengangkatan Pejabat Struktural Dekan Fakultas Teknik;</li>
                            </ol>
                        </td>
                    </tr>
                </table>

                <p><strong>Memperhatikan:</strong> ${Object.keys(groupedByJurusan).map(jurusanName => `Surat dari Ketua Jurusan ${jurusanName} tentang permohonan SK Dosen Penguji Skripsi`).join('; ')};</p>

                <div style="text-align: center; margin: 30px 0 20px 0; font-weight: bold;">
                    MEMUTUSKAN
                </div>

                <table style="width: 100%; margin-bottom: 15px;">
                    <tr>
                        <td style="width: 15%; vertical-align: top; font-weight: bold;">Menetapkan</td>
                        <td style="width: 3%; vertical-align: top;">:</td>
                        <td>PENETAPAN DOSEN PENGUJI SKRIPSI FAKULTAS TEKNIK UNIVERSITAS TRUNOJOYO MADURA SEMESTER ${semesterUpper} TAHUN AKADEMIK ${tahunAkademik}.</td>
                    </tr>
                </table>

                <table style="width: 100%; margin-bottom: 10px;">
                    <tr>
                        <td style="width: 15%; vertical-align: top; font-weight: bold;">Kesatu</td>
                        <td style="width: 3%; vertical-align: top;">:</td>
                        <td>Dosen Penguji Skripsi sebagaimana tercantum dalam lampiran Keputusan ini;</td>
                    </tr>
                </table>

                <table style="width: 100%;">
                    <tr>
                        <td style="width: 15%; vertical-align: top; font-weight: bold;">Kedua</td>
                        <td style="width: 3%; vertical-align: top;">:</td>
                        <td>Keputusan ini berlaku sejak tanggal ditetapkan.</td>
                    </tr>
                </table>
            </div>

            <div style="font-size: 10pt; margin: 40px 0 30px 0; text-align: right;">
                <p style="margin-bottom: 3px;">Ditetapkan di Bangkalan</p>
                <p style="margin-bottom: 3px;">pada tanggal ${new Date(sk.Tanggal_Persetujuan_Dekan || new Date()).toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'})}</p>
                <p style="margin-bottom: 70px;"><strong>DEKAN,</strong></p>
                <p style="margin-bottom: 0;">
                    <strong><u>${finalDekanName}</u></strong><br>
                    NIP. ${finalDekanNip}
                </p>
            </div>

            ${lampiranHtml}
        `;

        document.getElementById('previewContent').innerHTML = html;
    }
</script>
@endpush
