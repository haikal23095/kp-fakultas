<aside class="d-flex flex-column flex-shrink-0 p-3 text-white bg-dark" style="width: 280px; height: 100vh; position: fixed;">
    
    <a href="{{ route('dashboard') }}" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
        <i class="fas fa-university fa-2x me-2"></i>
        <span class="fs-4">Sistem Fakultas</span>
    </a>
    <hr>

    <ul class="nav nav-pills flex-column mb-auto">
        @auth
            <li class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link text-white {{ request()->routeIs('dashboard*') ? 'active' : '' }}">
                    <i class="fas fa-home me-2"></i>
                    Dashboard
                </a>
            </li>

            @php
                $role = auth()->user()->role ? auth()->user()->role->Name_Role : null;
            @endphp

            @if($role == 'dekan')
                <li class="nav-heading mt-3 mb-1 text-muted small">DEKAN MENU</li>
                <li>
                    <a href="{{ route('dekan.persetujuan.index') }}" class="nav-link text-white {{ request()->routeIs('dekan.persetujuan.*') ? 'active' : '' }}">
                        <i class="fas fa-signature me-2"></i>
                        Persetujuan Surat
                    </a>
                </li>
                <li>
                    <a href="{{ route('dekan.arsip.index') }}" class="nav-link text-white {{ request()->routeIs('dekan.arsip.*') ? 'active' : '' }}">
                        <i class="fas fa-archive me-2"></i>
                        Arsip Surat
                    </a>
                </li>
            
            @elseif($role == 'Mahasiswa')
                <li class="nav-heading mt-3 mb-1 text-muted small">MAHASISWA MENU</li>
                <li>
                    <a href="{{ route('mahasiswa.pengajuan.create') }}" class="nav-link text-white {{ request()->routeIs('mahasiswa.pengajuan.*') ? 'active' : '' }}">
                        <i class="fas fa-pen-to-square me-2"></i>
                        Buat Pengajuan
                    </a>
                </li>
                <li>
                    <a href="{{ route('mahasiswa.riwayat.index') }}" class="nav-link text-white {{ request()->routeIs('mahasiswa.riwayat.*') ? 'active' : '' }}">
                        <i class="fas fa-history me-2"></i>
                        Riwayat Surat
                    </a>
                </li>
                 <li>
                    <a href="{{ route('mahasiswa.legalisir.create') }}" class="nav-link text-white {{ request()->routeIs('mahasiswa.legalisir.*') ? 'active' : '' }}">
                        <i class="fas fa-stamp me-2"></i>
                        Legalisir Dokumen
                    </a>
                </li>

            @elseif($role == 'Pegawai')
                {{-- Nanti menu untuk Pegawai/Admin bisa ditambahkan di sini --}}
                <li class="nav-heading mt-3 mb-1 text-muted small">ADMIN MENU</li>
                 <li>
                    <a href="#" class="nav-link text-white">
                        <i class="fas fa-users-cog me-2"></i>
                        Kelola User
                    </a>
                </li>
            
            @endif

        @endauth
    </ul>
    <hr>
    
    <div class="dropdown">
        <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle text-truncate w-100" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false" style="max-width: 240px;">
            <i class="fas fa-user-circle fa-2x me-2"></i>
            <strong class="text-truncate">{{ auth()->user()->Name_User ?? 'User' }}</strong>
        </a>
        <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser1">
            <li><a class="dropdown-item" href="#">Profil</a></li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <a class="dropdown-item" href="{{ route('logout') }}" 
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    Sign out
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </li>
        </ul>
    </div>
</aside>