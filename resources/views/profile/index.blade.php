@extends('layouts.' . $layout)

@section('title', 'Profile')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Profile Pengguna</h1>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row">
        {{-- Informasi Profile --}}
        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-user-circle fa-5x text-primary"></i>
                    </div>
                    <h5 class="mb-0">{{ $user->Name_User }}</h5>
                    <p class="text-muted mb-2">{{ $user->role->Name_Role ?? 'User' }}</p>
                    <p class="text-muted"><i class="fas fa-envelope"></i> {{ $user->email }}</p>
                    
                    @if($user->mahasiswa)
                    <hr>
                    <div class="text-start">
                        <p class="mb-1"><strong>NIM:</strong> {{ $user->mahasiswa->NIM }}</p>
                        <p class="mb-1"><strong>Prodi:</strong> {{ $user->mahasiswa->prodi->Nama_Prodi ?? '-' }}</p>
                        <p class="mb-0"><strong>Semester:</strong> {{ $user->mahasiswa->Semester ?? '-' }}</p>
                    </div>
                    @endif

                    @if($user->dosen)
                    <hr>
                    <div class="text-start">
                        <p class="mb-1"><strong>NIP:</strong> {{ $user->dosen->NIP }}</p>
                        <p class="mb-0"><strong>Prodi:</strong> {{ $user->dosen->prodi->Nama_Prodi ?? '-' }}</p>
                    </div>
                    @endif

                    @if($user->pegawai)
                    <hr>
                    <div class="text-start">
                        <p class="mb-1"><strong>NIP:</strong> {{ $user->pegawai->NIP ?? '-' }}</p>
                        <p class="mb-0"><strong>Prodi:</strong> {{ $user->pegawai->prodi->Nama_Prodi ?? '-' }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Form Edit Profile --}}
        <div class="col-lg-8">
            {{-- Edit Informasi Dasar --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Edit Informasi Profile</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" value="{{ $user->Username }}" disabled readonly>
                            <small class="text-muted">Username tidak dapat diubah</small>
                        </div>

                        <div class="mb-3">
                            <label for="Name_User" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('Name_User') is-invalid @enderror" 
                                   id="Name_User" name="Name_User" value="{{ old('Name_User', $user->Name_User) }}" required>
                            @error('Name_User')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="role" class="form-label">Role</label>
                            <input type="text" class="form-control" id="role" value="{{ $user->role->Name_Role ?? '-' }}" disabled readonly>
                            <small class="text-muted">Role tidak dapat diubah</small>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Ganti Password --}}
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Ganti Password</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('profile.password') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Password Saat Ini <span class="text-danger">*</span></label>
                            <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                                   id="current_password" name="current_password" required>
                            @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password Baru <span class="text-danger">*</span></label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                   id="password" name="password" required>
                            <small class="text-muted">Minimal 8 karakter</small>
                            @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" 
                                   id="password_confirmation" name="password_confirmation" required>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-key me-2"></i>Ganti Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
