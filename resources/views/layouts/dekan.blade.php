<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Fakultas')</title>

    {{-- CSS Frameworks --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    
    @stack('styles')
</head>
<body>
    <div class="d-flex">
        
        {{-- Memanggil sidebar KHUSUS untuk Dekan --}}
        @include('layouts.partials.sidebar_dekan')

        {{-- Area konten utama yang akan diisi oleh halaman lain --}}
        <main class="w-100 p-4" style="margin-left: 280px;">
            @yield('content')
        </main>
        
    </div>

    {{-- JavaScript --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>