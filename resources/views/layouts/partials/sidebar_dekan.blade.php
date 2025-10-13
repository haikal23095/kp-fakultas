<aside class="d-flex flex-column flex-shrink-0 p-3 text-white bg-dark" style="width: 280px; height: 100vh; position: fixed;">
    
    {{-- Arahkan ke dashboard utama --}}
    <a href="/dashboard" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
        <i class="fas fa-university fa-2x me-2"></i>
        <span class="fs-4">Sistem Fakultas</span>
    </a>
    <hr>

    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            {{-- Menggunakan href langsung ke URL dashboard dekan --}}
            <a href="/dashboard/dekan" class="nav-link text-white {{ request()->is('dashboard/dekan') ? 'active' : '' }}">
                <i class="fas fa-home me-2"></i>
                Dashboard
            </a>
        </li>

        <li class="nav-heading mt-3 mb-1 text-muted small">DEKAN MENU</li>
        <li>
            {{-- Menggunakan href langsung ke URL persetujuan --}}
            <a href="/dekan/persetujuan-surat" class="nav-link text-white {{ request()->is('dekan/persetujuan-surat*') ? 'active' : '' }}">
                <i class="fas fa-signature me-2"></i>
                Persetujuan Surat
            </a>
        </li>
        <li>
            {{-- Menggunakan href langsung ke URL arsip --}}
            <a href="/dekan/arsip-surat" class="nav-link text-white {{ request()->is('dekan/arsip-surat*') ? 'active' : '' }}">
                <i class="fas fa-archive me-2"></i>
                Arsip Surat
            </a>
        </li>
    </ul>
    <hr>
    
    <div class="dropdown">
        <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle text-truncate w-100" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false" style="max-width: 240px;">
            <i class="fas fa-user-circle fa-2x me-2"></i>
            <strong class="text-truncate">{{ auth()->user()?->Name_User ?? 'Bapak/Ibu Dekan' }}</strong>
        </a>
        <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser1">
            <li><a class="dropdown-item" href="#">Profil</a></li>
            <li><hr class="dropdown-divider"></li>
            <li>
                {{-- Untuk logout, kita tetap butuh form --}}
                <a class="dropdown-item" href="#" 
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