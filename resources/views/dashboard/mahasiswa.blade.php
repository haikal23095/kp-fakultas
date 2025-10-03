<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Mahasiswa - Sistem Fakultas</title>
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
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
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
            border-top: 4px solid #6c757d;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #6c757d;
            margin-bottom: 0.5rem;
        }

        .mahasiswa-actions {
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
            border-color: #6c757d;
        }

        .action-icon {
            font-size: 2rem;
            color: #6c757d;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div>
            <h1>👨‍🎓 Dashboard Mahasiswa</h1>
            <span class="role-badge">Mahasiswa</span>
        </div>
        <div class="user-info">
            <span>{{ Auth::user()->Name_User ?? 'Mahasiswa' }}</span>
            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                @csrf
                <button type="submit" class="logout-btn">Keluar</button>
            </form>
        </div>
    </nav>

    <div class="container">
        <div class="welcome-card">
            <h2>Selamat Datang, Mahasiswa!</h2>
            <p>Dashboard untuk mengakses informasi akademik dan aktivitas perkuliahan Anda.</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number">6</div>
                <div class="stat-label">Mata Kuliah Semester Ini</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">20</div>
                <div class="stat-label">Total SKS</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">3.45</div>
                <div class="stat-label">IPK</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">85%</div>
                <div class="stat-label">Kehadiran</div>
            </div>
        </div>

        <div class="mahasiswa-actions">
            <div class="action-card">
                <div class="action-icon">📚</div>
                <h3>Mata Kuliah</h3>
                <p>Daftar mata kuliah</p>
            </div>
            <div class="action-card">
                <div class="action-icon">📊</div>
                <h3>Nilai & IPK</h3>
                <p>Transkrip nilai</p>
            </div>
            <div class="action-card">
                <div class="action-icon">📅</div>
                <h3>Jadwal Kuliah</h3>
                <p>Jadwal perkuliahan</p>
            </div>
            <div class="action-card">
                <div class="action-icon">📋</div>
                <h3>Presensi</h3>
                <p>Riwayat kehadiran</p>
            </div>
            <div class="action-card">
                <div class="action-icon">📝</div>
                <h3>Tugas & Ujian</h3>
                <p>Assignment & exam</p>
            </div>
            <div class="action-card">
                <div class="action-icon">🎓</div>
                <h3>Progress Studi</h3>
                <p>Tracking kemajuan</p>
            </div>
        </div>
    </div>
</body>
</html>