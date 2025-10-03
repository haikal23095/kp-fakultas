<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Sistem Fakultas</title>
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
            background: linear-gradient(135deg, #dc3545 0%, #6f42c1 100%);
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
            background: #dc3545;
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
            border-left: 5px solid #dc3545;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
            border-top: 4px solid #dc3545;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #dc3545;
            margin-bottom: 0.5rem;
        }

        .admin-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 2rem;
        }

        .action-card {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
            cursor: pointer;
            transition: transform 0.3s;
            border: 2px solid transparent;
        }

        .action-card:hover {
            transform: translateY(-5px);
            border-color: #dc3545;
        }

        .action-icon {
            font-size: 2rem;
            color: #dc3545;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div>
            <h1>🔧 Dashboard Admin</h1>
            <span class="role-badge">Administrator</span>
        </div>
        <div class="user-info">
            <span>{{ Auth::user()->Name_User ?? 'Admin' }}</span>
            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                @csrf
                <button type="submit" class="logout-btn">Keluar</button>
            </form>
        </div>
    </nav>

    <div class="container">
        <div class="welcome-card">
            <h2>Selamat Datang, Administrator!</h2>
            <p>Anda memiliki akses penuh ke seluruh sistem fakultas. Kelola semua aspek sistem dengan bijak.</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number">6</div>
                <div class="stat-label">Total Role</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">150</div>
                <div class="stat-label">Total User</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">8</div>
                <div class="stat-label">Program Studi</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">100%</div>
                <div class="stat-label">System Health</div>
            </div>
        </div>

        <div class="admin-actions">
            <div class="action-card">
                <div class="action-icon">👥</div>
                <h3>Kelola User</h3>
                <p>Tambah, edit, hapus user</p>
            </div>
            <div class="action-card">
                <div class="action-icon">🏛️</div>
                <h3>Kelola Fakultas</h3>
                <p>Pengaturan fakultas</p>
            </div>
            <div class="action-card">
                <div class="action-icon">📊</div>
                <h3>Laporan Sistem</h3>
                <p>Analytics & Reports</p>
            </div>
            <div class="action-card">
                <div class="action-icon">⚙️</div>
                <h3>Pengaturan</h3>
                <p>Konfigurasi sistem</p>
            </div>
            <div class="action-card">
                <div class="action-icon">🔒</div>
                <h3>Keamanan</h3>
                <p>Backup & Security</p>
            </div>
            <div class="action-card">
                <div class="action-icon">📋</div>
                <h3>Audit Log</h3>
                <p>Riwayat aktivitas</p>
            </div>
        </div>
    </div>
</body>
</html>