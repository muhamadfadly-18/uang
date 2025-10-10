<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplikasi Keuangan</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

    <link rel="shortcut icon" href="{{ asset('img/icon-512.png') }}" type="image/png">

    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#007bff">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f5f7;
            overflow-x: hidden;
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            background: #2c3e50;
            color: #ecf0f1;
            padding-top: 70px;
            transition: all 0.3s;
            overflow-y: auto;
            z-index: 999;
        }

        .sidebar a {
            color: #ecf0f1;
            display: flex;
            align-items: center;
            padding: 12px 20px;
            text-decoration: none;
            font-weight: 500;
            transition: background 0.2s, color 0.2s;
            border-radius: 5px;
            margin: 5px 10px;
        }

        .sidebar a:hover {
            background: #34495e;
            color: #fff;
        }

        .sidebar a i {
            margin-right: 10px;
        }

        .sidebar a.active {
            background: #1abc9c;
            color: #fff;
        }

        /* Content */
        .content {
            margin-left: 250px;
            padding: 80px 20px 20px 20px;
            min-height: 100vh;
            transition: margin-left 0.3s;
        }

        /* Navbar */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            background-color: #34495e !important;
        }

        /* Footer */
        footer {
            background: #ecf0f1;
            border-top: 1px solid #dcdcdc;
            color: #2c3e50;
            padding: 10px 0;
            text-align: center;
        }

        /* Table */
        table th {
            background-color: #1abc9c;
            color: #fff;
        }

        table tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        table tr:hover {
            background-color: #e0f7fa;
        }

        /* Toggle Button */
        #sidebarToggle {
            display: none;
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 1100;
            background-color: #34495e;
            border: none;
            color: #fff;
            padding: 8px 12px;
            border-radius: 5px;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 992px) {
            .sidebar {
                left: -260px;
            }

            .sidebar.show {
                left: 0;
            }

            .content {
                margin-left: 0;
            }

            #sidebarToggle {
                display: block;
            }
        }

        @media (max-width: 576px) {
            .navbar .navbar-brand {
                font-size: 12px;
                margin-left: 45px;
            }

            .sidebar a i {
                margin-right: 5px;
            }
        }
    </style>
</head>

<body>
    <!-- Sidebar Toggle Button -->
    <button id="sidebarToggle"><i class="bi bi-list"></i></button>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <i class="bi bi-cash-stack"></i> Aplikasi Keuangan
            </a>

            <div class="ms-auto">
                <ul class="navbar-nav">
                    <!-- Profile Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            @php
                                $photoPath = Auth::user()->photo
                                    ? asset('img/profile/' . Auth::user()->photo)
                                    : asset('img/profile/default.png');
                            @endphp
                            <img src="{{ $photoPath }}" alt="Profile"
                                class="rounded-circle me-2 border border-2 border-white shadow-sm" width="35"
                                height="35" style="object-fit: cover;">
                            <span>{{ Auth::user()->name ?? 'Admin' }}</span>
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Profile</a></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button class="dropdown-item" type="submit">Logout</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}"><i
                class="bi bi-house-door"></i> Dashboard</a>

        @if (Auth::user()->role === 'admin')
            <a href="{{ route('pemasukan.index') }}" class="{{ request()->routeIs('pemasukan.*') ? 'active' : '' }}"><i
                    class="bi bi-wallet2"></i> Pemasukan</a>
            <a href="{{ route('pengeluaranday.index') }}"
                class="{{ request()->routeIs('pengeluaranday.*') ? 'active' : '' }}"><i
                    class="bi bi-credit-card-2-back"></i> Pengeluaran</a>
            <a href="{{ route('history') }}" class="{{ request()->routeIs('history') ? 'active' : '' }}"><i
                    class="bi bi-journal-text"></i> History</a>
            <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i> Kelola User</a>
            @else
                <a href="{{ route('pemasukan.user') }}"
                    class="{{ request()->routeIs('pemasukan.*') ? 'active' : '' }}"><i class="bi bi-wallet2"></i>
                    Pemasukan</a>
                <a href="{{ route('pengeluaranday.user') }}"
                    class="{{ request()->routeIs('pengeluaranday.*') ? 'active' : '' }}"><i
                        class="bi bi-credit-card-2-back"></i> Pengeluaran</a>
                <a href="{{ route('history.user') }}"
                    class="{{ request()->routeIs('history.user') ? 'active' : '' }}"><i class="bi bi-journal-text"></i>
                    History</a>
        @endif
    </div>

    <!-- Content -->
    <div class="content">
        @yield('content')
    </div>

    <!-- Footer -->
    <footer>
        <small>© {{ date('Y') }} Aplikasi Keuangan - by Kamu</small>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Sidebar Toggle Script -->
    <script>
        const sidebar = document.getElementById('sidebar');
        const toggleBtn = document.getElementById('sidebarToggle');

        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('show');
        });

        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/service-worker.js')
                .then(reg => console.log('Service Worker registered', reg))
                .catch(err => console.log('Service Worker error', err));
        }
    </script>
</body>

</html>
