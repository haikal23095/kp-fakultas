<aside class="d-flex flex-column flex-shrink-0 p-3 text-white bg-dark" style="width: 280px; height: 100vh; position: fixed;">
    <a href="{{ route('dashboard') }}" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
        <i class="fas fa-university fa-2x me-2"></i>
        <span class="fs-4">Sistem Fakultas</span>
    </a>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <a href="{{ route('dashboard.kaprodi') }}" class="nav-link text-white {{ request()->routeIs('dashboard.kaprodi') ? 'active' : '' }}">
                <i class="fas fa-home me-2"></i>
                Dashboard
            </a>
        </li>
        <li class="nav-heading mt-3 mb-1 text-muted small">KAPRODI MENU</li>
        {{-- Menu untuk Kaprodi --}}
        <li>
            <a href="{{ route('kaprodi.surat.index') }}" class="nav-link text-white {{ request()->routeIs('kaprodi.surat.*') ? 'active' : '' }}">
                <i class="fa fa-envelope me-2"></i>
                Permintaan Pengantar KP/Magang
            </a>
        </li>
    </ul>
    <hr>
    <div class="dropdown">
        <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle text-truncate w-100" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false" style="max-width: 240px;">
            <i class="fas fa-user-circle fa-2x me-2"></i>
            <strong class="text-truncate">{{ auth()->user()?->Name_User ?? 'Kaprodi' }}</strong>
        </a>
        <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser1">
            <li><a class="dropdown-item" href="#">Profil</a></li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Sign out</a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
            </li>
        </ul>
    </div>
</aside>