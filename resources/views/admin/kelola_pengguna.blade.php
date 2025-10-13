@extends('layouts.admin')

@section('title', 'Kelola Pengguna')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Kelola Pengguna Sistem</h1>
    <a href="#" class="btn btn-primary shadow-sm">
        <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Pengguna Baru
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Seluruh Pengguna</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Nama Lengkap</th>
                        <th>Email</th>
                        <th class="text-center">Role</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>Dr. John Doe, M.Kom.</td>
                        <td>johndoe@example.ac.id</td>
                        <td class="text-center"><span class="badge bg-danger">Admin</span></td>
                        <td class="text-center"><span class="badge bg-success">Aktif</span></td>
                        <td class="text-center">
                            <a href="#" class="btn btn-warning btn-sm" title="Edit"><i class="fas fa-edit"></i></a>
                            <a href="#" class="btn btn-danger btn-sm" title="Hapus"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Prof. Dr. Jane Smith, M.Sc.</td>
                        <td>janesmith@example.ac.id</td>
                        <td class="text-center"><span class="badge bg-primary">Dekan</span></td>
                        <td class="text-center"><span class="badge bg-success">Aktif</span></td>
                        <td class="text-center">
                            <a href="#" class="btn btn-warning btn-sm" title="Edit"><i class="fas fa-edit"></i></a>
                            <a href="#" class="btn btn-danger btn-sm" title="Hapus"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>Budi Santoso</td>
                        <td>2210511001</td>
                        <td class="text-center"><span class="badge bg-info">Mahasiswa</span></td>
                        <td class="text-center"><span class="badge bg-secondary">Tidak Aktif</span></td>
                        <td class="text-center">
                           <a href="#" class="btn btn-warning btn-sm" title="Edit"><i class="fas fa-edit"></i></a>
                           <a href="#" class="btn btn-danger btn-sm" title="Hapus"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection