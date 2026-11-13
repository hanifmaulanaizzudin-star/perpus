<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Portal Perpustakaan') - PerpusKu</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            font-size: 14px;
            line-height: 1.55;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            -webkit-font-smoothing: antialiased;
        }

        /* Top Navbar */
        .site-header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid #edf2f7;
            position: sticky;
            top: 0;
            z-index: 50;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.02);
        }

        .header-inner {
            max-width: 1140px;
            margin: 0 auto;
            padding: 0 24px;
            height: 68px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 11px;
            text-decoration: none;
            color: #0f172a;
            font-weight: 700;
            font-size: 17px;
            letter-spacing: -0.02em;
        }

        .brand-mark {
            width: 34px;
            height: 34px;
            background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
            color: #ffffff;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 15px;
            box-shadow: 0 4px 10px rgba(79, 70, 229, 0.25);
        }

        .nav-menu {
            display: flex;
            align-items: center;
            gap: 6px;
            background: #f1f5f9;
            padding: 4px;
            border-radius: 9999px;
        }

        .nav-link {
            padding: 7px 16px;
            color: #64748b;
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 500;
            border-radius: 9999px;
            transition: all 0.18s ease-in-out;
        }

        .nav-link:hover {
            color: #1e293b;
        }

        .nav-link.active {
            color: #4338ca;
            background-color: #ffffff;
            font-weight: 600;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar-circle {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
            color: #ffffff;
            font-weight: 700;
            font-size: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 6px rgba(99, 102, 241, 0.25);
        }

        .user-meta-name {
            font-size: 13.5px;
            font-weight: 600;
            color: #0f172a;
            line-height: 1.2;
        }

        .user-meta-nis {
            font-size: 11.5px;
            color: #64748b;
        }

        .btn-header-logout {
            background: transparent;
            border: 1px solid #e2e8f0;
            color: #64748b;
            padding: 6px 14px;
            font-size: 12.5px;
            border-radius: 9999px;
            cursor: pointer;
            font-weight: 500;
            font-family: inherit;
            transition: all 0.15s ease;
        }

        .btn-header-logout:hover {
            color: #ef4444;
            border-color: #fca5a5;
            background: #fef2f2;
        }

        /* Container */
        .content-area {
            max-width: 1140px;
            margin: 28px auto;
            padding: 0 24px;
            flex: 1;
            width: 100%;
        }

        .card-box {
            background: #ffffff;
            border: 1px solid #edf2f7;
            border-radius: 16px;
            padding: 26px;
            box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.04);
            margin-bottom: 24px;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            padding: 9px 18px;
            font-size: 13.5px;
            font-weight: 600;
            border-radius: 10px;
            text-decoration: none;
            cursor: pointer;
            font-family: inherit;
            border: 1px solid transparent;
            transition: all 0.18s ease-in-out;
            white-space: nowrap;
        }

        .btn-primary {
            background-color: #4f46e5;
            color: #ffffff;
            border-color: #4f46e5;
            box-shadow: 0 2px 8px rgba(79, 70, 229, 0.25);
        }
        .btn-primary:hover {
            background-color: #4338ca;
            border-color: #4338ca;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.35);
        }

        .btn-secondary {
            background-color: #ffffff;
            color: #334155;
            border-color: #e2e8f0;
        }
        .btn-secondary:hover {
            background-color: #f8fafc;
            border-color: #cbd5e1;
            color: #0f172a;
            transform: translateY(-1px);
        }

        /* Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 12px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 600;
            line-height: 1;
        }

        .badge-aktif {
            background-color: #ecfdf5;
            color: #047857;
            border: 1px solid #a7f3d0;
        }

        .badge-warning {
            background-color: #fffbeb;
            color: #b45309;
            border: 1px solid #fde68a;
        }

        .badge-danger {
            background-color: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        .badge-neutral {
            background-color: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
        }

        /* Tables */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13.5px;
        }

        th {
            background-color: #f8fafc;
            color: #64748b;
            font-weight: 600;
            text-align: left;
            padding: 12px 16px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        td {
            padding: 14px 16px;
            border-bottom: 1px solid #f1f5f9;
            color: #1e293b;
            vertical-align: middle;
        }

        tr:hover td {
            background-color: #fcfdfe;
        }

        .site-footer {
            border-top: 1px solid #edf2f7;
            background: #ffffff;
            padding: 24px 0;
            text-align: center;
            color: #94a3b8;
            font-size: 13px;
            margin-top: auto;
        }

        @media (max-width: 768px) {
            .header-inner {
                flex-direction: column;
                height: auto;
                padding: 14px 20px;
                gap: 12px;
            }
            .content-area {
                padding: 0 16px;
            }
            .nav-menu {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>

    <!-- Header Navigation -->
    <header class="site-header">
        <div class="header-inner">
            <a href="{{ route('anggota.dashboard') }}" class="brand-logo">
                <div class="brand-mark">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                    </svg>
                </div>
                <span>PerpusKu</span>
            </a>

            <nav class="nav-menu">
                <a href="{{ route('anggota.dashboard') }}" class="nav-link {{ request()->routeIs('anggota.dashboard') ? 'active' : '' }}">
                    Dashboard
                </a>
                <a href="{{ route('anggota.katalog') }}" class="nav-link {{ request()->routeIs('anggota.katalog*') ? 'active' : '' }}">
                    Katalog Buku
                </a>
                <a href="{{ route('anggota.pinjaman-saya') }}" class="nav-link {{ request()->routeIs('anggota.pinjaman-saya') ? 'active' : '' }}">
                    Pinjaman Saya
                </a>
                <a href="{{ route('anggota.reservasi-saya') }}" class="nav-link {{ request()->routeIs('anggota.reservasi-saya*') ? 'active' : '' }}">
                    Reservasi Saya
                </a>
                <a href="{{ route('anggota.kartu-saya') }}" class="nav-link {{ request()->routeIs('anggota.kartu-saya') ? 'active' : '' }}">
                    Kartu Anggota
                </a>
            </nav>

            <div class="user-menu">
                <div class="user-profile">
                    <div class="user-avatar-circle">
                        {{ strtoupper(substr(auth('anggota')->user()->nama_lengkap ?? 'A', 0, 1)) }}
                    </div>
                    <div>
                        <div class="user-meta-name">{{ auth('anggota')->user()->nama_lengkap ?? 'Anggota' }}</div>
                        <div class="user-meta-nis">NIS: {{ auth('anggota')->user()->nomor_induk ?? '-' }}</div>
                    </div>
                </div>

                <form action="{{ route('anggota.logout') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="btn-header-logout">Keluar</button>
                </form>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="content-area">
        @yield('content')
    </main>

    <footer class="site-footer">
        &copy; {{ date('Y') }} PerpusKu &bull; Sistem Perpustakaan Digital
    </footer>

</body>
</html>
