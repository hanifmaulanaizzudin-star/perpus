<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Portal Siswa') - PerpusKu</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: #f8fafc;
            color: #0f172a;
            font-size: 14px;
            line-height: 1.5;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            -webkit-font-smoothing: antialiased;
        }

        /* Clean Top Navigation Bar */
        .site-header {
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .header-inner {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: #0f172a;
            font-weight: 800;
            font-size: 16px;
            letter-spacing: -0.02em;
            flex-shrink: 0;
        }

        .brand-icon {
            width: 32px;
            height: 32px;
            background: #2563eb;
            color: #ffffff;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .brand-badge {
            background: #f1f5f9;
            color: #475569;
            font-size: 11px;
            font-weight: 600;
            padding: 2px 7px;
            border-radius: 6px;
            letter-spacing: 0;
        }

        /* Clean Navigation Links */
        .nav-menu {
            display: flex;
            align-items: center;
            gap: 4px;
            flex-wrap: nowrap;
        }

        .nav-link {
            padding: 8px 14px;
            color: #475569;
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 500;
            border-radius: 8px;
            transition: all 0.15s ease;
            white-space: nowrap;
        }

        .nav-link:hover {
            color: #0f172a;
            background-color: #f1f5f9;
        }

        .nav-link.active {
            color: #2563eb;
            background-color: #eff6ff;
            font-weight: 600;
        }

        /* Right User Controls */
        .user-controls {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-shrink: 0;
        }

        .user-profile-chip {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: inherit;
            padding: 4px 8px;
            border-radius: 8px;
            transition: background-color 0.15s ease;
        }

        .user-profile-chip:hover {
            background-color: #f8fafc;
        }

        .user-avatar-circle {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            background: #eff6ff;
            color: #2563eb;
            font-weight: 700;
            font-size: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #dbeafe;
        }

        .user-meta-name {
            font-size: 13px;
            font-weight: 600;
            color: #0f172a;
            line-height: 1.25;
            max-width: 140px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-meta-nis {
            font-size: 11px;
            color: #64748b;
        }

        .btn-header-logout {
            background: transparent;
            border: 1px solid #e2e8f0;
            color: #64748b;
            padding: 6px 12px;
            font-size: 12.5px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            font-family: inherit;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.15s ease;
        }

        .btn-header-logout:hover {
            color: #ef4444;
            border-color: #fecaca;
            background: #fef2f2;
        }

        /* Main Container */
        .content-area {
            max-width: 1200px;
            margin: 28px auto;
            padding: 0 24px;
            flex: 1;
            width: 100%;
        }

        /* Modern Flat Card Box */
        .card-box {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 24px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
            margin-bottom: 24px;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 8px 16px;
            font-size: 13.5px;
            font-weight: 600;
            border-radius: 8px;
            text-decoration: none;
            cursor: pointer;
            font-family: inherit;
            border: 1px solid transparent;
            transition: all 0.15s ease;
            white-space: nowrap;
        }

        .btn-primary {
            background-color: #2563eb;
            color: #ffffff;
            border-color: #2563eb;
        }
        .btn-primary:hover {
            background-color: #1d4ed8;
            border-color: #1d4ed8;
        }

        .btn-secondary {
            background-color: #ffffff;
            color: #334155;
            border-color: #cbd5e1;
        }
        .btn-secondary:hover {
            background-color: #f8fafc;
            border-color: #94a3b8;
            color: #0f172a;
        }

        /* Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 3px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            line-height: 1.3;
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
            padding: 10px 14px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        td {
            padding: 13px 14px;
            border-bottom: 1px solid #f1f5f9;
            color: #1e293b;
            vertical-align: middle;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover td {
            background-color: #f8fafc;
        }

        /* Footer */
        .site-footer {
            border-top: 1px solid #e2e8f0;
            background: #ffffff;
            padding: 20px 0;
            text-align: center;
            color: #64748b;
            font-size: 12.5px;
            margin-top: auto;
        }

        @media (max-width: 900px) {
            .header-inner {
                flex-direction: column;
                height: auto;
                padding: 14px 20px;
                gap: 12px;
            }
            .nav-menu {
                width: 100%;
                overflow-x: auto;
                padding-bottom: 4px;
                justify-content: flex-start;
            }
            .content-area {
                padding: 0 16px;
                margin: 20px auto;
            }
        }
    </style>
</head>
<body>

    <!-- Header Navigation -->
    <header class="site-header">
        <div class="header-inner">
            
            {{-- Brand Logo --}}
            <a href="{{ route('anggota.dashboard') }}" class="brand-logo">
                <div class="brand-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                    </svg>
                </div>
                <span>PerpusKu</span>
                <span class="brand-badge">Siswa</span>
            </a>

            {{-- Clean Navigation Menu --}}
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
                <a href="{{ route('anggota.pengaturan') }}" class="nav-link {{ request()->routeIs('anggota.pengaturan*') ? 'active' : '' }}">
                    Pengaturan
                </a>
            </nav>

            {{-- User Chip & Logout --}}
            <div class="user-controls">
                <a href="{{ route('anggota.pengaturan') }}" class="user-profile-chip" title="Buka Pengaturan Profil">
                    <div class="user-avatar-circle">
                        {{ strtoupper(substr(auth('anggota')->user()->nama_lengkap ?? 'A', 0, 1)) }}
                    </div>
                    <div>
                        <div class="user-meta-name">{{ auth('anggota')->user()->nama_lengkap ?? 'Anggota' }}</div>
                        <div class="user-meta-nis">NIS: {{ auth('anggota')->user()->nomor_induk ?? '-' }}</div>
                    </div>
                </a>

                <form action="{{ route('anggota.logout') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="btn-header-logout" title="Keluar dari akun">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                            <polyline points="16 17 21 12 16 7"/>
                            <line x1="21" y1="12" x2="9" y2="12"/>
                        </svg>
                        <span>Keluar</span>
                    </button>
                </form>
            </div>

        </div>
    </header>

    <!-- Main Content -->
    <main class="content-area">
        @yield('content')
    </main>

    <footer class="site-footer">
        &copy; {{ date('Y') }} PerpusKu &bull; Sistem Perpustakaan Sekolah
    </footer>

</body>
</html>
