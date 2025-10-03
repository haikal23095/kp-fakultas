<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistem Fakultas</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fa;
        }

        .navbar {
            background: linear-gradient(135deg, #6c757d 0%, #343a40 100%);
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .navbar h1 {
            font-size: 1.5rem;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .logout-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .logout-btn:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 2rem;
        }

        .role-badge {
            background: #6c757d;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: bold;
        }

        .welcome-card {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
            border-left: 5px solid #6c757d;
            text-align: center;
        }

        .error-info {
            background: #fff3cd;
            color: #856404;
            padding: 1rem;
            border-radius: 5px;
            border: 1px solid #ffeaa7;
            margin-top: 1rem;
        }

        .contact-admin {
            background: #d1ecf1;
            color: #0c5460;
            padding: 1rem;
            border-radius: 5px;
            border: 1px solid #bee5eb;
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div>
            <h1>📋 Dashboard</h1>
            <span class="role-badge">Role Tidak Dikenali</span>
        </div>
        <div class="user-info">
            <span>{{ Auth::user()->Name_User ?? 'User' }}</span>
            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                @csrf
                <button type="submit" class="logout-btn">Keluar</button>
            </form>
        </div>
    </nav>

    <div class="container">
        <div class="welcome-card">
            <h2>Selamat Datang!</h2>
            <p>Anda berhasil masuk ke dalam sistem.</p>
            
            <div class="error-info">
                <strong>Perhatian:</strong> Role Anda (ID: {{ Auth::user()->Id_Role ?? 'Tidak Diketahui' }}) belum dikonfigurasi dengan dashboard khusus.
            </div>

            <div class="contact-admin">
                <strong>Informasi:</strong> Silakan hubungi administrator sistem untuk mengatur role dan hak akses yang sesuai.
            </div>
        </div>
    </div>
</body>
</html>