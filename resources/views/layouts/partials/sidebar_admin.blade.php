<aside class="d-flex flex-column flex-shrink-0 p-3 text-white bg-dark" style="width: 280px; height: 100vh; position: fixed;">
    <a href="{{ route('dashboard.admin') }}" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
        <i class="fas fa-university fa-2x me-2"></i>
        <span class="fs-4">Sistem Fakultas</span>
    </a>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <a href="{{ route('dashboard.admin') }}" class="nav-link text-white {{ request()->routeIs('dashboard.admin') ? 'active' : '' }}">
                <i class="fas fa-home me-2"></i>
                Dashboard
            </a>
        </li>
        <li class="nav-heading mt-3 mb-1 text-muted small">ADMIN MENU</li>
        <li class="nav-item">
            <a href="{{ route('admin.surat.manage') }}" 
            class="nav-link {{ request()->routeIs('admin.surat.manage') || request()->routeIs('admin.surat.detail') ? 'active' : '' }}">
                <i class="fa fa-envelope me-2"></i>
                <span>Manajemen Surat</span>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.surat.archive') }}" class="nav-link text-white {{ request()->routeIs('admin.surat.archive') ? 'active' : '' }}">
                <i class="fas fa-archive me-2"></i>
                Arsip Surat
            </a>
        </li>
        <li>
            <a href="{{ route('admin.settings.index') }}" class="nav-link text-white {{ request()->routeIs('admin.settings.index') ? 'active' : '' }}">
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