<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Fakultas')</title>

    {{-- CSS Frameworks --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <link rel="stylesheet" href="{{ asset('css/sidebar-fix.css') }}">
    <style>
        @media (max-width: 768px) {
            .sidebar-mobile { transform: translateX(-100%); transition: transform 0.3s ease; }
            .sidebar-mobile.show { transform: translateX(0); }
            .main-content { margin-left: 0 !important; padding: 1rem !important; }
            .mobile-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1040; }
            .mobile-overlay.show { display: block; }
        }
        @media (min-width: 769px) { .btn-toggle-sidebar { display: none; } }
    </style>
    @stack('styles')
</head>
<body>
    <button class="btn btn-primary btn-toggle-sidebar position-fixed" style="top: 10px; left: 10px; z-index: 1050;" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>
    
    <div class="mobile-overlay" id="mobileOverlay" onclick="toggleSidebar()"></div>
    
    <div class="d-flex">
        @include('layouts.partials.sidebar_wadek1')
        <main class="w-100 p-4 main-content" style="margin-left: 280px;">
            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSidebar() {
            document.querySelector('.sidebar-mobile').classList.toggle('show');
            document.getElementById('mobileOverlay').classList.toggle('show');
        }
    </script>
    @include('layouts.partials.notifikasi_badge_script')
    @stack('scripts')
</body>
</html>
