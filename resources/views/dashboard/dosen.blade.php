<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Dosen - Sistem Fakultas</title>
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
            background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%);
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
            background: #17a2b8;
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
            border-left: 5px solid #17a2b8;
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
            border-top: 4px solid #17a2b8;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #17a2b8;
            margin-bottom: 0.5rem;
        }

        .dosen-actions {
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
            border-color: #17a2b8;
        }

        .action-icon {
            font-size: 2rem;
            color: #17a2b8;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div>
            <h1>👨‍🏫 Dashboard Dosen</h1>
            <span class="role-badge">Dosen</span>
        </div>
        <div class="user-info">
            <span>{{ Auth::user()->Name_User ?? 'Dosen' }}</span>
            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                @csrf
                <button type="submit" class="logout-btn">Keluar</button>
            </form>
        </div>
    </nav>

    <div class="container">
        <div class="welcome-card">
            <h2>Selamat Datang, Bapak/Ibu Dosen!</h2>
            <p>Dashboard untuk mengelola aktivitas mengajar dan penelitian Anda.</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number">5</div>
                <div class="stat-label">Mata Kuliah Diampu</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">120</div>
                <div class="stat-label">Total Mahasiswa</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">18</div>
                <div class="stat-label">SKS Mengajar</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">3</div>
                <div class="stat-label">Penelitian Aktif</div>
            </div>
        </div>

        <div class="dosen-actions">
            <div class="action-card">
                <div class="action-icon">📚</div>
                <h3>Mata Kuliah</h3>
                <p>Kelola mata kuliah</p>
            </div>
            <div class="action-card">
                <div class="action-icon">👨‍🎓</div>
                <h3>Daftar Mahasiswa</h3>
                <p>Data mahasiswa mengikuti</p>
            </div>
            <div class="action-card">
                <div class="action-icon">📋</div>
                <h3>Presensi</h3>
                <p>Absensi mahasiswa</p>
            </div>
            <div class="action-card">
                <div class="action-icon">📊</div>
                <h3>Input Nilai</h3>
                <p>Entry nilai mahasiswa</p>
            </div>
            <div class="action-card">
                <div class="action-icon">📅</div>
                <h3>Jadwal Mengajar</h3>
                <p>Jadwal perkuliahan</p>
            </div>
            <div class="action-card">
                <div class="action-icon">🔬</div>
                <h3>Penelitian</h3>
                <p>Manajemen penelitian</p>
            </div>
        </div>
    </div>
</body>
</html>