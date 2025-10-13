@extends('layouts.dosen')

@section('title', 'Input Nilai Mahasiswa')

@section('content')

{{-- Header Halaman --}}
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Input Nilai Mahasiswa</h1>
</div>

{{-- Form Pemilihan Mata Kuliah --}}
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Pilih Mata Kuliah</h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <div class="form-group">
                    <label for="mataKuliahSelect">Mata Kuliah yang Diampu</label>
                    <select class="form-select" id="mataKuliahSelect">
                        <option selected disabled>-- Pilih Mata Kuliah dan Kelas --</option>
                        <option value="1">Pemrograman Berbasis Web - Kelas A</option>
                        <option value="2">Basis Data Lanjutan - Kelas B</option>
                        <option value="3">Kecerdasan Buatan - Kelas A</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button class="btn btn-primary w-100">Tampilkan Mahasiswa</button>
            </div>
        </div>
    </div>
</div>

{{-- Tabel Input Nilai (Contoh untuk Pemrograman Berbasis Web) --}}
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Mahasiswa - Pemrograman Berbasis Web (Kelas A)</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Nama Mahasiswa</th>
                        <th>NIM</th>
                        <th class="text-center" style="width: 15%;">Nilai Angka (0-100)</th>
                        <th class="text-center" style="width: 15%;">Nilai Huruf</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>Ahmad Budi Santoso</td>
                        <td>2210511001</td>
                        <td><input type="number" class="form-control text-center" value="85"></td>
                        <td class="text-center align-middle"><strong>A</strong></td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Citra Lestari</td>
                        <td>2210511002</td>
                        <td><input type="number" class="form-control text-center" value="78"></td>
                        <td class="text-center align-middle"><strong>B+</strong></td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>Eko Prasetyo</td>
                        <td>2410511040</td>
                        <td><input type="number" class="form-control text-center"></td>
                        <td class="text-center align-middle">-</td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td>Fitriana Sari</td>
                        <td>2210511005</td>
                        <td><input type="number" class="form-control text-center" value="92"></td>
                        <td class="text-center align-middle"><strong>A</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end mt-3">
            <button class="btn btn-success"><i class="fas fa-save me-2"></i>Simpan Perubahan</button>
        </div>
    </div>
</div>

@endsection