<aside class="d-flex flex-column flex-shrink-0 p-3 text-white bg-dark" style="width: 280px; height: 100vh; position: fixed;">
    @php
        $user = auth()->user();
        $fakultasName = 'Fakultas Teknik'; // Default fallback
        
        // Admin might be linked via Dosen or Pegawai
        if ($user->dosen && $user->dosen->prodi && $user->dosen->prodi->fakultas) {
            $fakultasName = $user->dosen->prodi->fakultas->Nama_Fakultas;
        } elseif ($user->pegawai && $user->pegawai->prodi && $user->pegawai->prodi->fakultas) {
            $fakultasName = $user->pegawai->prodi->fakultas->Nama_Fakultas;
        }
    @endphp
    <a href="{{ route('dashboard.admin_prodi') }}" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
        <div class="d-flex align-items-center">
            <div class="bg-success rounded p-2 me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                <i class="fas fa-university fa-lg text-white"></i>
            </div>
            <div class="d-flex flex-column" style="line-height: 1.2;">
                <span class="fs-6 fw-bold text-uppercase tracking-wide">Sistem Surat</span>
                <span class="small text-white-50" style="font-size: 0.7rem; letter-spacing: 0.5px;">{{ strtoupper($fakultasName) }}</span>
            </div>
        </div>
    </a>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <a href="{{ route('dashboard.admin_prodi') }}" class="nav-link text-white {{ request()->routeIs('dashboard.admin_prodi') ? 'active' : '' }}">
                <i class="fas fa-home me-2"></i>
                Dashboard
            </a>
        </li>
        <li class="nav-heading mt-3 mb-1 text-muted small">ADMIN MENU</li>
        <li class="nav-item">
            <a href="{{ route('admin_prodi.surat.manage') }}" 
            class="nav-link {{ request()->routeIs('admin_prodi.surat.manage') || request()->routeIs('admin_prodi.surat.detail') ? 'active' : '' }}">
                <i class="fa fa-envelope me-2"></i>
                <span>Manajemen Surat</span>
            </a>
        </li>
        <li>
            <a href="{{ route('admin_prodi.surat.archive') }}" class="nav-link text-white {{ request()->routeIs('admin_prodi.surat.archive') ? 'active' : '' }}">
                <i class="fas fa-archive me-2"></i>
                Arsip Surat
            </a>
        </li>
        <li>
            <a href="{{ route('admin_prodi.settings.index') }}" class="nav-link text-white {{ request()->routeIs('admin_prodi.settings.index') ? 'active' : '' }}">
                <i class="fas fa-cogs me-2"></i>
                Pengaturan Sistem
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
        <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-user-circle fa-2x me-2"></i>
            <strong>{{ auth()->user()?->Name_User ?? 'Admin' }}</strong>
        </a>
        <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser1">
            <li><a class="dropdown-item" href="{{ route('profile.index') }}">Profil</a></li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Sign out</a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
            </li>
        </ul>
    </div>
</aside>