<!DOCTYPE html>
<html lang="id">
<head>
    {{-- ... bagian head kamu ... --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    @stack('styles')
</head>
<body>
    <div class="d-flex">
        
        @include('layouts.partials.sidebar')

        <main class="w-100 p-4" style="margin-left: 280px;">
            @yield('content')
        </main>
        
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>