<aside class="d-flex flex-column flex-shrink-0 p-3 text-white bg-dark" style="width: 280px; height: 100vh; position: fixed;">
    @php
        $user = auth()->user();
        $fakultasName = 'Fakultas Teknik'; // Default fallback
        
        if ($user && $user->mahasiswa && $user->mahasiswa->prodi && $user->mahasiswa->prodi->fakultas) {
            $fakultasName = $user->mahasiswa->prodi->fakultas->Nama_Fakultas;
        }
    @endphp
    <a href="{{ route('dashboard') }}" class="sidebar-brand d-flex align-items-center mb-3 text-white text-decoration-none">
        <div class="d-flex align-items-center w-100">
            <div class="brand-icon rounded p-2 me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; flex-shrink: 0;">
                <i class="fas fa-user-graduate fa-lg text-white"></i>
            </div>
            <div class="d-flex flex-column" style="line-height: 1.2; flex: 1; min-width: 0;">
                <span class="fw-bold text-uppercase text-truncate-custom" style="font-size: 0.95rem;">Sistem Surat</span>
                <span class="small text-truncate-custom" style="font-size: 0.7rem; letter-spacing: 0.5px; color: #94a3b8;">{{ $fakultasName }}</span>
            </div>
        </div>
    </a>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <a href="{{ route('dashboard.mahasiswa') }}" class="nav-link text-white {{ request()->routeIs('dashboard.mahasiswa') ? 'active' : '' }}">
                <i class="fas fa-home me-2"></i>
                Dashboard
            </a>
        </li>
        <li class="nav-heading mt-3 mb-1 small">MAHASISWA MENU</li>
        <li>
            <a href="{{ route('mahasiswa.pengajuan.create') }}" class="nav-link text-white {{ request()->routeIs('mahasiswa.pengajuan.*') ? 'active' : '' }}">
                <i class="fas fa-pen-to-square me-2"></i>
                Buat Pengajuan
            </a>
        </li>
        <li>
            <a href="{{ route('mahasiswa.riwayat') }}" class="nav-link text-white {{ request()->routeIs('mahasiswa.riwayat') ? 'active' : '' }}">
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
    </ul>
    <hr>
    {{-- Notifikasi Link --}}
    <div class="mb-2">
        <a href="{{ route('notifikasi.index') }}" class="nav-link text-white d-flex align-items-center justify-content-between {{ request()->routeIs('notifikasi.*') ? 'active' : '' }}">
            <span><i class="fas fa-bell me-2"></i>Notifikasi</span>
            <span class="badge bg-danger rounded-pill" id="notifBadge" style="display: none;">0</span>
        </a>
    </div>
    <hr>
    <div class="dropdown">
        <a href="#" class="user-dropdown d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-user-circle fa-2x me-2" style="flex-shrink: 0;"></i>
            <strong class="text-truncate-custom" style="flex: 1; min-width: 0;">{{ auth()->user()?->Name_User ?? 'Mahasiswa' }}</strong>
        </a>
        <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser1">
            <li><a class="dropdown-item" href="{{ route('profile.index') }}">Profil</a></li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Sign out</a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
            </li>
        </ul>
    </div>
</aside>
