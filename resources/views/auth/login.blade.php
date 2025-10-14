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
    <link rel="shortcut icon"
        href="{data:image/png;base64,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"
        type="image/png">

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
                <input type="email" name="email" id="email" class="form-control"
                    placeholder="Masukkan email anda" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control"
                    placeholder="Masukkan password" required>
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
