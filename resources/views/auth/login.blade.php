<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Aplikasi Keuangan</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
     <link rel="shortcut icon" href="{{ asset('img/icon-512.png') }}" type="image/png">

    <style>
        /* 🌈 Background dan tata letak */
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(145deg, #1e3a8a, #2563eb, #3b82f6);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 1rem;
            color: #1e293b;
        }

        /* 💳 Kartu login */
        .login-card {
            background: #ffffff;
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            padding: 2.5rem 2rem;
            width: 100%;
            max-width: 420px;
            transition: all 0.3s ease;
        }

        .login-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 45px rgba(0, 0, 0, 0.25);
        }

        /* 🪙 Header */
        .login-header {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .login-header i {
            font-size: 3.5rem;
            color: #2563eb;
            background: #eff6ff;
            border-radius: 50%;
            padding: 15px;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.2);
        }

        .login-header h4 {
            margin-top: 1rem;
            font-weight: 700;
            color: #1e3a8a;
        }

        .login-header p {
            color: #6b7280;
            font-size: 0.95rem;
        }

        /* 🧩 Input */
        .form-label {
            font-weight: 600;
            color: #334155;
        }

        .form-control {
            border-radius: 10px;
            padding: 0.75rem;
            border: 1px solid #cbd5e1;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-control:focus {
            border-color: #2563eb;
            box-shadow: 0 0 6px rgba(37, 99, 235, 0.3);
        }

        /* 🔘 Tombol login */
        .btn-primary {
            background-color: #2563eb;
            border: none;
            border-radius: 10px;
            padding: 0.8rem;
            font-weight: 600;
            letter-spacing: 0.3px;
            transition: 0.3s;
        }

        .btn-primary:hover {
            background-color: #1e40af;
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.4);
        }

        /* 🦶 Footer */
        footer {
            position: fixed;
            bottom: 10px;
            text-align: center;
            width: 100%;
            color: #e5e7eb;
            font-size: 0.9rem;
        }

        /* ⚙️ SweetAlert fix */
        .swal2-container {
            z-index: 9999 !important;
        }

        body.swal2-shown {
            padding-right: 0 !important;
            overflow: unset !important;
        }

        /* 📱 Responsif */
        @media (max-width: 576px) {
            .login-card {
                padding: 1.5rem;
                border-radius: 15px;
            }

            .login-header i {
                font-size: 2.8rem;
                padding: 10px;
            }

            .login-header h4 {
                font-size: 1.3rem;
            }

            .btn-primary {
                padding: 0.7rem;
                font-size: 0.95rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <i class="bi bi-cash-coin"></i>
            <h4>Aplikasi Keuangan</h4>
            <p>Masuk untuk melanjutkan</p>
        </div>

        <form method="POST" action="{{ route('login.post') }}">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="Masukkan email anda" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="Masukkan password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>
    </div>

    <!-- SweetAlert jika login gagal -->
    @if ($errors->any())
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Login Gagal!',
                text: '{{ $errors->first() }}',
                confirmButtonColor: '#2563eb',
            });
        </script>
    @endif

    <footer>
        &copy; {{ date('Y') }} Aplikasi Keuangan
    </footer>
</body>
</html>
