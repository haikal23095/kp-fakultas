<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Fakultas')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <link rel="stylesheet" href="{{ asset('css/sidebar-fix.css') }}">
    @stack('styles')
</head>
<body>
    <div class="d-flex">
        {{-- Memanggil sidebar khusus Kajur --}}
        @include('layouts.partials.sidebar_kajur')
        <main class="w-100 p-4" style="margin-left: 280px;">
            @yield('content')
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @include('layouts.partials.notifikasi_badge_script')
    @stack('scripts')
</body>
</html>