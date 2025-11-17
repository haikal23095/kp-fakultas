/**
 * Pengajuan Surat - JavaScript Module
 * Menangani form dinamis dan preview surat
 */

document.addEventListener('DOMContentLoaded', function () {

    const jenisSuratSelect = document.getElementById('jenisSurat');
    const dynamicForms = document.querySelectorAll('.dynamic-form');
    const formPengajuan = document.getElementById('formPengajuan');

    // --- PENTING: PETA ID DARI DATABASE KE ID FORM & ROUTE ---
    // Ini akan di-set dari Blade template melalui window.routeConfig
    const formIdMap = window.formIdMap || {};
    // --- Akhir Peta ---

    function hideAllDynamicForms() {
        dynamicForms.forEach(function (form) {
            form.style.display = 'none';

            // Simpan status required asli dan nonaktifkan validasi
            form.querySelectorAll('input, select, textarea').forEach(function (input) {
                if (input.required) {
                    input.setAttribute('data-was-required', 'true');
                    input.required = false;
                }
                // Disable input agar tidak tersubmit jika kosong
                input.disabled = true;
            });
        });
    }

    jenisSuratSelect.addEventListener('change', function () {
        hideAllDynamicForms();

        const selectedValue = this.value; // Ini adalah ID, misal '3'
        const formConfig = formIdMap[selectedValue];

        if (formConfig) {
            // Set action form sesuai jenis surat
            formPengajuan.action = formConfig.route;

            const targetForm = document.getElementById(formConfig.formId);
            if (targetForm) {
                targetForm.style.display = 'block';

                // Aktifkan kembali input dan restore status required
                targetForm.querySelectorAll('input, select, textarea').forEach(function (input) {
                    input.disabled = false;
                    if (input.getAttribute('data-was-required') === 'true') {
                        input.required = true;
                    }
                });

                // === [BARU] Jika form magang, inisialisasi preview ===
                if (formConfig.formId === 'form-surat-magang') {
                    initMagangPreview();
                }
            }
        } else {
            // Jika jenis surat tidak ada mapping, kosongkan action
            formPengajuan.action = '';
            alert('Jenis surat ini belum tersedia. Silakan pilih jenis surat lain.');
        }
    });

    // Sembunyikan semua saat halaman baru dimuat
    hideAllDynamicForms();


    // === SCRIPT KHUSUS UNTUK SURAT MAGANG === //
    let mahasiswaIndex = 1; // Mulai dari 1 karena 0 sudah ada (mahasiswa yang login)
    let autocompleteTimeout = null;

    // Fungsi untuk inisialisasi form magang
    function initMagangPreview() {
        // Set tanggal hari ini di preview
        const previewTanggal = document.getElementById('preview-tanggal-magang');
        if (previewTanggal) {
            previewTanggal.textContent = formatTanggal(new Date().toISOString().split('T')[0]);
        }

        // Tambah event listener untuk tombol tambah mahasiswa
        const btnTambah = document.getElementById('btn-tambah-mahasiswa');
        if (btnTambah) {
            btnTambah.addEventListener('click', tambahMahasiswa);
        }

        // Event listener untuk mahasiswa pertama (yang sudah ada) - dengan multiple approach
        const firstMhsNama = document.querySelector('.mahasiswa-nama[data-index="0"]');
        const firstMhsNim = document.querySelector('.mahasiswa-nim[data-index="0"]');
        const firstMhsSemester = document.querySelector('.mahasiswa-semester[data-index="0"]');

        if (firstMhsNama) {
            firstMhsNama.addEventListener('input', updateMahasiswaPreviewList);
            firstMhsNama.addEventListener('change', updateMahasiswaPreviewList);
        }
        if (firstMhsNim) {
            firstMhsNim.addEventListener('input', updateMahasiswaPreviewList);
            firstMhsNim.addEventListener('change', updateMahasiswaPreviewList);
        }
        if (firstMhsSemester) {
            firstMhsSemester.addEventListener('change', updateMahasiswaPreviewList);
            firstMhsSemester.addEventListener('input', updateMahasiswaPreviewList);
        }

        // Event delegation untuk semua input mahasiswa (termasuk yang ditambah nanti)
        document.addEventListener('input', function (e) {
            if (e.target.matches('.mahasiswa-nama, .mahasiswa-nim, .mahasiswa-semester')) {
                updateMahasiswaPreviewList();
            }
        });

        document.addEventListener('change', function (e) {
            if (e.target.matches('.mahasiswa-semester')) {
                updateMahasiswaPreviewList();
            }
        });

        // Update preview dari input yang sudah ada
        setupPreviewListeners();

        // Initial update preview mahasiswa - dipanggil multiple kali untuk memastikan
        updateMahasiswaPreviewList();
        setTimeout(() => {
            updateMahasiswaPreviewList();
        }, 100);
        setTimeout(() => {
            updateMahasiswaPreviewList();
        }, 300);
    }

    // Helper function to format date
    function formatTanggal(dateStr) {
        if (!dateStr) return null;
        const date = new Date(dateStr);
        const options = { day: 'numeric', month: 'long', year: 'numeric' };
        return date.toLocaleDateString('id-ID', options);
    }

    // Setup listeners untuk update preview
    function setupPreviewListeners() {
        const inputInstansi = document.getElementById('input-instansi-magang');
        const inputJudul = document.getElementById('input-judul-magang');
        const inputDospem1 = document.getElementById('input-dospem1-magang');
        const inputDospem2 = document.getElementById('input-dospem2-magang');
        const inputMulai = document.getElementById('input-mulai-magang');
        const inputSelesai = document.getElementById('input-selesai-magang');

        if (inputInstansi) inputInstansi.addEventListener('input', updatePreview);
        if (inputJudul) inputJudul.addEventListener('input', updatePreview);
        if (inputDospem1) inputDospem1.addEventListener('change', updatePreview);
        if (inputDospem2) inputDospem2.addEventListener('change', updatePreview);
        if (inputMulai) inputMulai.addEventListener('change', updatePreview);
        if (inputSelesai) inputSelesai.addEventListener('change', updatePreview);

        // Event listener untuk upload TTD dengan background removal
        const inputTTD = document.getElementById('input-ttd-magang');
        if (inputTTD) {
            inputTTD.addEventListener('change', handleTTDUpload);
        }

        // Initial preview
        updatePreview();
    }

    // Fungsi untuk update preview
    function updatePreview() {
        const inputInstansi = document.getElementById('input-instansi-magang');
        const inputJudul = document.getElementById('input-judul-magang');
        const inputDospem1 = document.getElementById('input-dospem1-magang');
        const inputDospem2 = document.getElementById('input-dospem2-magang');
        const inputMulai = document.getElementById('input-mulai-magang');
        const inputSelesai = document.getElementById('input-selesai-magang');

        const previewInstansi = document.getElementById('preview-instansi-magang');
        const previewJudul = document.getElementById('preview-judul-magang');
        const previewDospem1 = document.getElementById('preview-dospem1-magang');
        const previewDospem2 = document.getElementById('preview-dospem2-magang');
        const previewJangkaWaktu = document.getElementById('preview-jangka-waktu-magang');

        if (previewInstansi) {
            previewInstansi.innerHTML = inputInstansi?.value || '<span class="preview-placeholder">[Nama Instansi]</span>';
        }
        if (previewJudul) {
            previewJudul.innerHTML = inputJudul?.value || '<span class="preview-placeholder">[Judul Penelitian]</span>';
        }
        if (previewDospem1) {
            previewDospem1.innerHTML = inputDospem1?.value || '<span class="preview-placeholder">[Pilih Dosen]</span>';
        }
        if (previewDospem2) {
            previewDospem2.innerHTML = inputDospem2?.value || '<span class="preview-placeholder">[Opsional]</span>';
        }
        // Note: preview-dospem1-magang-ttd (Koordinator KP/TA) sekarang diambil dari backend (Kaprodi)
        if (previewJangkaWaktu) {
            const mulai = formatTanggal(inputMulai?.value);
            const selesai = formatTanggal(inputSelesai?.value);
            if (mulai && selesai) {
                previewJangkaWaktu.innerHTML = `${mulai} – ${selesai}`;
            } else if (mulai) {
                previewJangkaWaktu.innerHTML = `${mulai} – ...`;
            } else {
                previewJangkaWaktu.innerHTML = '<span class="preview-placeholder">[Tanggal]</span>';
            }
        }

        // Update daftar mahasiswa di preview
        updateMahasiswaPreviewList();

        // Update jurusan di preview (ambil dari mahasiswa pertama)
        const firstMhsJurusan = document.querySelector('.mahasiswa-jurusan[data-index="0"]');
        const previewJurusan = document.getElementById('preview-jurusan-magang');
        if (previewJurusan && firstMhsJurusan) {
            previewJurusan.innerHTML = firstMhsJurusan.value || '<span class="preview-placeholder">[Jurusan]</span>';
        }

        // Update nama dan NIM di bagian tanda tangan (mahasiswa pertama)
        const firstMhsNama = document.querySelector('.mahasiswa-nama[data-index="0"]');
        const firstMhsNim = document.querySelector('.mahasiswa-nim[data-index="0"]');
        const previewNamaTtd = document.getElementById('preview-nama-magang-ttd');
        const previewNIMTtd = document.getElementById('preview-nim-magang-ttd');

        if (previewNamaTtd && firstMhsNama) {
            previewNamaTtd.innerHTML = firstMhsNama.value || '<span class="preview-placeholder">[Nama Mahasiswa]</span>';
        }
        if (previewNIMTtd && firstMhsNim) {
            previewNIMTtd.innerHTML = firstMhsNim.value || '<span class="preview-placeholder">[NIM]</span>';
        }
    }

    // Fungsi untuk update daftar mahasiswa di preview
    function updateMahasiswaPreviewList() {
        const previewList = document.getElementById('preview-mahasiswa-list');
        if (!previewList) {
            console.log('Preview list element not found');
            return;
        }

        previewList.innerHTML = '';

        // Ambil semua mahasiswa item
        const mahasiswaItems = document.querySelectorAll('.mahasiswa-item');
        console.log('Found mahasiswa items:', mahasiswaItems.length);

        mahasiswaItems.forEach((item, index) => {
            const dataIndex = item.getAttribute('data-index');
            const namaInput = item.querySelector(`.mahasiswa-nama[data-index="${dataIndex}"]`);
            const nimInput = item.querySelector(`.mahasiswa-nim[data-index="${dataIndex}"]`);
            const semesterInput = item.querySelector(`.mahasiswa-semester[data-index="${dataIndex}"]`);

            const nama = namaInput?.value || '';
            const nim = nimInput?.value || '';
            const semester = semesterInput?.value || '';

            console.log(`Mahasiswa ${index}:`, { nama, nim, semester, dataIndex });

            const previewItem = document.createElement('div');
            previewItem.className = 'preview-mahasiswa-item';
            previewItem.setAttribute('data-preview-index', dataIndex);

            const namaDisplay = nama ? nama : '<span class="preview-placeholder">[Nama Mahasiswa]</span>';
            const nimDisplay = nim ? nim : '<span class="preview-placeholder">[NIM]</span>';
            const semesterDisplay = semester ? semester : '<span class="preview-placeholder">-</span>';

            previewItem.innerHTML = `
                <strong>${index + 1}. <span class="preview-mhs-nama">${namaDisplay}</span></strong><br>
                <small>NIM: <span class="preview-mhs-nim">${nimDisplay}</span> | Semester: <span class="preview-mhs-semester">${semesterDisplay}</span></small>
            `;

            previewList.appendChild(previewItem);
        });
    }

    // Fungsi tambah mahasiswa
    function tambahMahasiswa() {
        const container = document.getElementById('mahasiswa-container');
        const template = document.getElementById('mahasiswa-template');
        const clone = template.content.cloneNode(true);

        // Update index
        const item = clone.querySelector('.mahasiswa-item');
        item.setAttribute('data-index', mahasiswaIndex);

        // Update nomor item
        clone.querySelector('.item-number').textContent = mahasiswaIndex + 1;

        // Update name attributes
        clone.querySelector('.mahasiswa-nama').setAttribute('name', `mahasiswa[${mahasiswaIndex}][nama]`);
        clone.querySelector('.mahasiswa-nama').setAttribute('data-index', mahasiswaIndex);

        clone.querySelector('.mahasiswa-nim').setAttribute('name', `mahasiswa[${mahasiswaIndex}][nim]`);
        clone.querySelector('.mahasiswa-nim').setAttribute('data-index', mahasiswaIndex);

        clone.querySelector('.mahasiswa-jurusan').setAttribute('name', `mahasiswa[${mahasiswaIndex}][jurusan]`);
        clone.querySelector('.mahasiswa-jurusan').setAttribute('data-index', mahasiswaIndex);

        clone.querySelector('.mahasiswa-semester').setAttribute('name', `mahasiswa[${mahasiswaIndex}][semester]`);
        clone.querySelector('.mahasiswa-semester').setAttribute('data-index', mahasiswaIndex);

        // Event listener untuk tombol hapus
        clone.querySelector('.btn-hapus-mahasiswa').addEventListener('click', function () {
            item.remove();
            updateItemNumbers();
            updateMahasiswaPreviewList(); // Update preview setelah hapus
        });

        // Event listener untuk autocomplete
        const inputNama = clone.querySelector('.autocomplete-mahasiswa');
        inputNama.addEventListener('input', function (e) {
            handleAutocomplete(e.target);
        });

        // Event listener untuk update preview tidak perlu karena sudah pakai delegation

        container.appendChild(clone);
        mahasiswaIndex++;

        // Update preview setelah menambah mahasiswa baru
        setTimeout(updateMahasiswaPreviewList, 50);
    }

    // Fungsi hapus dan update nomor urut
    function updateItemNumbers() {
        document.querySelectorAll('.mahasiswa-item').forEach((item, index) => {
            const number = item.querySelector('.item-number');
            if (number) {
                number.textContent = index + 1;
            }
        });
    }

    // Fungsi autocomplete
    function handleAutocomplete(input) {
        clearTimeout(autocompleteTimeout);

        const query = input.value.trim();
        const resultsDiv = input.nextElementSibling;

        if (query.length < 2) {
            resultsDiv.style.display = 'none';
            return;
        }

        const searchUrl = window.mahasiswaSearchRoute || '/mahasiswa/api/mahasiswa/search';

        autocompleteTimeout = setTimeout(() => {
            fetch(`${searchUrl}?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.length === 0) {
                        resultsDiv.innerHTML = '<div class="autocomplete-item"><small>Tidak ada hasil</small></div>';
                        resultsDiv.style.display = 'block';
                        return;
                    }

                    resultsDiv.innerHTML = '';
                    data.forEach(mhs => {
                        const div = document.createElement('div');
                        div.className = 'autocomplete-item';
                        div.innerHTML = `<strong>${mhs.nama}</strong><br><small>NIM: ${mhs.nim} - ${mhs.jurusan}</small>`;
                        div.addEventListener('click', () => {
                            selectMahasiswa(input, mhs);
                            resultsDiv.style.display = 'none';
                        });
                        resultsDiv.appendChild(div);
                    });
                    resultsDiv.style.display = 'block';
                })
                .catch(error => {
                    console.error('Error fetching mahasiswa:', error);
                });
        }, 300);
    }

    // Fungsi select mahasiswa dari autocomplete
    function selectMahasiswa(input, mhs) {
        const index = input.getAttribute('data-index');

        // Set nilai
        input.value = mhs.nama;
        const nimInput = document.querySelector(`.mahasiswa-nim[data-index="${index}"]`);
        const jurusanInput = document.querySelector(`.mahasiswa-jurusan[data-index="${index}"]`);

        if (nimInput) nimInput.value = mhs.nim;
        if (jurusanInput) jurusanInput.value = mhs.jurusan;

        // Tutup autocomplete
        input.nextElementSibling.style.display = 'none';

        // Update preview setelah select
        updateMahasiswaPreviewList();
    }

    // Close autocomplete ketika klik di luar
    document.addEventListener('click', function (e) {
        if (!e.target.classList.contains('autocomplete-mahasiswa')) {
            document.querySelectorAll('.autocomplete-results').forEach(div => {
                div.style.display = 'none';
            });
        }
    });

    // Fungsi untuk handle upload TTD dan preview
    function handleTTDUpload(event) {
        const file = event.target.files[0];
        if (!file) return;

        const previewImg = document.getElementById('preview-ttd-image');
        const processingDiv = document.getElementById('ttd-processing');

        // Show processing indicator
        if (processingDiv) processingDiv.style.display = 'block';

        const reader = new FileReader();
        reader.onload = function (e) {
            const img = new Image();
            img.onload = function () {
                // Create canvas for background removal
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');

                canvas.width = img.width;
                canvas.height = img.height;

                // Draw image
                ctx.drawImage(img, 0, 0);

                // Get image data
                const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                const data = imageData.data;

                // Simple background removal - remove white/light colors
                for (let i = 0; i < data.length; i += 4) {
                    const red = data[i];
                    const green = data[i + 1];
                    const blue = data[i + 2];

                    // If pixel is mostly white/light (threshold = 200)
                    if (red > 200 && green > 200 && blue > 200) {
                        data[i + 3] = 0; // Make transparent
                    }
                }

                ctx.putImageData(imageData, 0, 0);

                // Set preview image
                const processedImage = canvas.toDataURL('image/png');
                previewImg.src = processedImage;
                previewImg.style.display = 'block';
                previewImg.style.maxHeight = '80px';
                previewImg.style.margin = '10px auto';

                // Hide processing indicator
                if (processingDiv) processingDiv.style.display = 'none';
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }

    // Note: initMagangPreview() dipanggil dari event 'change' dropdown jenis surat
});
