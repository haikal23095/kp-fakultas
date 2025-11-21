<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Informasi Fakultas</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-image: url("{{ asset('images/images.jpg') }}");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            min-height: 100vh;
            position: relative;
        }

        /* Overlay untuk membuat text lebih terbaca dan kesan elegan */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(13, 110, 253, 0.85), rgba(13, 202, 240, 0.75)); 
            /* Atau warna gelap: background: rgba(0, 0, 0, 0.6); */
            z-index: 0;
        }

        .login-card {
            position: relative;
            z-index: 1;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            border: none;
        }

        .login-header {
            background: #fff;
            padding: 2rem 2rem 1rem;
            text-align: center;
        }

        .logo-icon {
            width: 60px;
            height: 60px;
            background: #0d6efd;
            color: white;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin: 0 auto 1rem;
            box-shadow: 0 4px 10px rgba(13, 110, 253, 0.3);
        }

        .login-body {
            padding: 2rem;
        }

        .form-control {
            padding: 0.75rem 1rem;
            border-radius: 8px;
            border: 1px solid #dee2e6;
            background-color: #f8f9fa;
        }

        .form-control:focus {
            background-color: #fff;
            box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.15);
            border-color: #0d6efd;
        }

        .btn-login {
            padding: 0.75rem;
            border-radius: 8px;
            font-weight: 600;
            letter-spacing: 0.5px;
            background: linear-gradient(to right, #0d6efd, #0099ff);
            border: none;
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(13, 110, 253, 0.4);
        }

        .footer-text {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.85rem;
            color: #6c757d;
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                <div class="login-card animate__animated animate__fadeInUp">
                    <div class="login-header">
                        <div class="logo-icon">
                            <i class="fas fa-university"></i>
                        </div>
                        <h4 class="fw-bold text-dark mb-1">Sistem Informasi</h4>
                        <p class="text-muted mb-0">Fakultas</p>
                    </div>
                    
                    <div class="login-body pt-0">
                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            
                            <div class="mb-3">
                                <label for="email" class="form-label small fw-bold text-secondary">EMAIL INSTITUSI</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-envelope"></i></span>
                                    <input type="email" class="form-control border-start-0 ps-0 {{ $errors->has('email') ? 'is-invalid' : '' }}" 
                                           id="email" name="email" value="{{ old('email') }}" 
                                           placeholder="nama@fakultas.ac.id" required autofocus>
                                </div>
                                @if ($errors->has('email'))
                                    <div class="text-danger small mt-1">{{ $errors->first('email') }}</div>
                                @endif
                            </div>

                            <div class="mb-4">
                                <label for="password" class="form-label small fw-bold text-secondary">PASSWORD</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-lock"></i></span>
                                    <input type="password" class="form-control border-start-0 border-end-0 ps-0 {{ $errors->has('password') ? 'is-invalid' : '' }}" 
                                           id="password" name="password" placeholder="••••••••" required>
                                    <button class="btn bg-white border border-start-0 text-muted" type="button" id="togglePassword" style="border-color: #dee2e6 !important; border-top-right-radius: 8px; border-bottom-right-radius: 8px;">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                @if ($errors->has('password'))
                                    <div class="text-danger small mt-1">{{ $errors->first('password') }}</div>
                                @endif
                            </div>

                            <div class="mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                                    <label class="form-check-label small text-secondary" for="remember">
                                        Ingat Saya
                                    </label>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 btn-login text-white">
                                MASUK <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </form>

                        <div class="footer-text">
                            &copy; {{ date('Y') }} Sistem Informasi Fakultas.<br>All rights reserved.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');

        togglePassword.addEventListener('click', function (e) {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.querySelector('i').classList.toggle('fa-eye-slash');
            this.querySelector('i').classList.toggle('fa-eye');
        });
    </script>
</body>
</html>