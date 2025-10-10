<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Aplikasi Keuangan</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #1e293b, #3b82f6);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }

        .login-card {
            background: #ffffff;
            border: none;
            border-radius: 20px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            padding: 2rem;
            width: 400px;
            transition: 0.3s;
        }

        .login-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.2);
        }

        .login-header {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .login-header i {
            font-size: 3rem;
            color: #2563eb;
        }

        .btn-primary {
            background-color: #2563eb;
            border: none;
            border-radius: 10px;
            transition: 0.3s;
        }

        .btn-primary:hover {
            background-color: #1d4ed8;
        }

        .form-control {
            border-radius: 10px;
        }

        footer {
            position: absolute;
            bottom: 10px;
            text-align: center;
            width: 100%;
            color: #e5e7eb;
            font-size: 0.9rem;
        }

        /* 🔧 FIX supaya tampilan tidak bergeser saat SweetAlert muncul */
        .swal2-container {
            z-index: 9999 !important;
        }

        body.swal2-shown {
            padding-right: 0 !important;
            overflow: unset !important;
        }
    </style>
</head>
<body>
    <div class="d-flex justify-content-center align-items-center vh-100">
        <div class="login-card">
            <div class="login-header">
                <i class="bi bi-cash-coin"></i>
                <h4 class="mt-2">Aplikasi Keuangan</h4>
                <p class="text-muted">Masuk untuk melanjutkan</p>
            </div>

            <form method="POST" action="{{ route('login.post') }}">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">Email</label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="Masukkan email anda" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label fw-semibold">Password</label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Masukkan password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
        </div>
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

</body>
</html>
