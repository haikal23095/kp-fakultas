<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Fakultas')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <link rel="stylesheet" href="{{ asset('css/sidebar-fix.css') }}">
    <style>
        /* Desktop Styles (Default) */
        .sidebar-wrapper {
            width: 280px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            overflow-y: auto;
            transition: transform 0.3s ease-in-out;
        }

        .main-content {
            margin-left: 280px;
            transition: margin-left 0.3s ease-in-out;
            min-height: 100vh;
            width: calc(100% - 280px);
        }

        .mobile-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1040;
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
        }

        .btn-toggle-sidebar {
            display: none;
        }

        /* Mobile Styles */
        @media (max-width: 768px) {
            .sidebar-wrapper {
                transform: translateX(-100%);
                z-index: 1050;
                box-shadow: 4px 0 15px rgba(0,0,0,0.1);
            }

            .sidebar-wrapper.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0 !important;
                width: 100% !important;
                padding-top: 70px !important; /* Space for toggle button */
            }

            .mobile-overlay.show {
                display: block;
                opacity: 1;
            }

            .btn-toggle-sidebar {
                display: flex;
                align-items: center;
                justify-content: center;
                position: fixed;
                top: 15px;
                left: 15px;
                z-index: 1060;
                width: 45px;
                height: 45px;
                border-radius: 10px;
                background-color: #0d6efd;
                color: white;
                border: none;
                box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                transition: background-color 0.2s;
            }

            .btn-toggle-sidebar:active {
                transform: scale(0.95);
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    {{-- Mobile Toggle Button --}}
    <button class="btn-toggle-sidebar" onclick="toggleSidebar()">
        <i class="fas fa-bars fa-lg"></i>
    </button>
    
    {{-- Mobile Overlay --}}
    <div class="mobile-overlay" id="mobileOverlay" onclick="toggleSidebar()"></div>
    
    <div class="d-flex w-100">
        @include('layouts.partials.sidebar_mahasiswa')
        <main class="p-4 main-content">
            @yield('content')
        </main>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar-wrapper');
            const overlay = document.getElementById('mobileOverlay');
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        }
    </script>
    @include('layouts.partials.notifikasi_badge_script')
    @stack('scripts')
</body>
</html>