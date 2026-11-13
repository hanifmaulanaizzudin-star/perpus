<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Perpustakaan') - PerpusKu</title>

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
            -webkit-font-smoothing: antialiased;
        }

        /* Sidebar Navigasi */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: 255px;
            background-color: #0f172a;
            color: #f8fafc;
            display: flex;
            flex-direction: column;
            z-index: 50;
            border-right: 1px solid #1e293b;
        }

        .sidebar-brand {
            height: 68px;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0 20px;
            border-bottom: 1px solid #1e293b;
            text-decoration: none;
            color: #ffffff;
            font-size: 17px;
            font-weight: 700;
            letter-spacing: -0.02em;
        }

        .sidebar-brand-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
            color: white;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 10px rgba(79, 70, 229, 0.35);
        }

        .sidebar-brand small {
            display: block;
            font-size: 11px;
            color: #94a3b8;
            font-weight: normal;
        }

        .sidebar-nav {
            flex: 1;
            padding: 18px 12px;
            overflow-y: auto;
        }

        .nav-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            padding: 12px 10px 6px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 10px 14px;
            color: #94a3b8;
            text-decoration: none;
            border-radius: 10px;
            font-size: 13.5px;
            font-weight: 500;
            transition: all 0.18s ease-in-out;
            margin-bottom: 3px;
        }

        .nav-item svg {
            width: 18px;
            height: 18px;
            color: #64748b;
            transition: color 0.18s ease;
        }

        .nav-item:hover {
            color: #ffffff;
            background-color: #1e293b;
            transform: translateX(2px);
        }

        .nav-item:hover svg {
            color: #cbd5e1;
        }

        .nav-item.active {
            color: #ffffff;
            background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }

        .nav-item.active svg {
            color: #ffffff;
        }

        .sidebar-footer {
            padding: 16px 18px;
            border-top: 1px solid #1e293b;
            background: #0b1120;
        }

        .user-badge {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
            box-shadow: 0 2px 6px rgba(99, 102, 241, 0.25);
        }

        .user-details {
            flex: 1;
            overflow: hidden;
        }

        .user-name {
            font-size: 13px;
            font-weight: 600;
            color: #f8fafc;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-role {
            font-size: 11px;
            color: #94a3b8;
        }

        .btn-logout {
            background: transparent;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            padding: 6px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            transition: all 0.15s ease;
        }

        .btn-logout:hover {
            color: #ef4444;
            background: rgba(239, 68, 68, 0.1);
        }

        /* Konten Utama */
        .main-wrapper {
            margin-left: 255px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .topbar {
            height: 68px;
            background: #ffffff;
            border-bottom: 1px solid #edf2f7;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 32px;
            position: sticky;
            top: 0;
            z-index: 40;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
        }

        .topbar-title {
            font-size: 19px;
            font-weight: 700;
            color: #0f172a;
            letter-spacing: -0.02em;
        }

        .main-content {
            padding: 28px 32px;
            flex: 1;
        }

        /* Komponen UI Standar */
        .card-box {
            background: #ffffff;
            border: 1px solid #edf2f7;
            border-radius: 16px;
            padding: 26px;
            box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.04);
            margin-bottom: 24px;
        }

        /* Tombol */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            padding: 9px 18px;
            font-size: 13.5px;
            font-weight: 600;
            font-family: inherit;
            border-radius: 10px;
            text-decoration: none;
            cursor: pointer;
            border: 1px solid transparent;
            transition: all 0.18s ease-in-out;
            white-space: nowrap;
        }

        .btn-primary, .btn-blue {
            background-color: #4f46e5;
            color: #ffffff;
            border-color: #4f46e5;
            box-shadow: 0 2px 8px rgba(79, 70, 229, 0.25);
        }
        .btn-primary:hover, .btn-blue:hover {
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

        .btn-danger {
            background-color: #ef4444;
            color: #ffffff;
            border-color: #ef4444;
            box-shadow: 0 2px 6px rgba(239, 68, 68, 0.2);
        }
        .btn-danger:hover {
            background-color: #dc2626;
            transform: translateY(-1px);
        }

        /* Tabel Data */
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

        /* Badge Status */
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

        .badge-nonaktif {
            background-color: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
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

        /* Action Buttons */
        .actions {
            display: inline-flex;
            gap: 6px;
            align-items: center;
        }

        .actions a, .actions button {
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            color: #334155;
            cursor: pointer;
            text-decoration: none;
            font-family: inherit;
            transition: all 0.15s ease;
        }

        .actions a:hover, .actions button:hover {
            transform: translateY(-1px);
        }

        .act-detail { color: #4338ca !important; border-color: #c7d2fe !important; background: #eef2ff !important; }
        .act-edit { color: #b45309 !important; border-color: #fde68a !important; background: #fffbeb !important; }
        .act-delete { color: #b91c1c !important; border-color: #fecaca !important; background: #fef2f2 !important; }
        .act-barcode { color: #4f46e5 !important; border-color: #e0e7ff !important; background: #eef2ff !important; }

        /* Form Controls */
        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-size: 13px;
            font-weight: 600;
            color: #334155;
        }

        .form-control {
            width: 100%;
            height: 42px;
            padding: 8px 14px;
            font-size: 13.5px;
            font-family: inherit;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            background-color: #ffffff;
            color: #0f172a;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.12);
        }

        textarea.form-control {
            height: auto;
            min-height: 95px;
            padding: 10px 14px;
            resize: vertical;
        }

        .form-actions {
            display: flex;
            gap: 10px;
            align-items: center;
            margin-top: 24px;
        }

        /* Toolbar & Search Box */
        .toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .search-box {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .search-box input {
            height: 40px;
            padding: 6px 14px;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            font-size: 13.5px;
            font-family: inherit;
            min-width: 260px;
            background: #ffffff;
        }

        .search-box input:focus {
            outline: none;
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.12);
        }

        /* Notifikasi Alert */
        .alert {
            padding: 14px 18px;
            border-radius: 10px;
            font-size: 13.5px;
            margin-bottom: 22px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background-color: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .alert-danger {
            background-color: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        /* Pagination */
        .pagination svg {
            width: 18px;
            height: 18px;
            vertical-align: middle;
        }
        .pagination nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 20px;
            font-size: 13px;
        }

        @media (max-width: 768px) {
            .sidebar {
                display: none;
            }
            .main-wrapper {
                margin-left: 0;
            }
            .topbar, .main-content {
                padding: 16px;
            }
        }
    </style>
</head>
<body>

    <!-- Sidebar Admin -->
    <aside class="sidebar">
        <a href="{{ route('petugas.dashboard') }}" class="sidebar-brand">
            <div class="sidebar-brand-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                </svg>
            </div>
            <div>
                PerpusKu
                <small>Sistem Manajemen</small>
            </div>
        </a>

        <div class="sidebar-nav">
            <div class="nav-label">Menu Utama</div>

            <a href="{{ route('petugas.dashboard') }}" class="nav-item {{ request()->routeIs('petugas.dashboard') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Dashboard
            </a>

            <a href="{{ route('petugas.anggota.index') }}" class="nav-item {{ request()->routeIs('petugas.anggota.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                Data Anggota
            </a>

            <div class="nav-label">Katalog & Koleksi</div>

            <a href="{{ route('petugas.kategori.index') }}" class="nav-item {{ request()->routeIs('petugas.kategori.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                Kategori Buku
            </a>

            <a href="{{ route('petugas.rak.index') }}" class="nav-item {{ request()->routeIs('petugas.rak.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                Rak Buku
            </a>

            <a href="{{ route('petugas.buku.index') }}" class="nav-item {{ request()->routeIs('petugas.buku.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                Data Buku
            </a>

            <div class="nav-label">Sirkulasi & Transaksi</div>

            <a href="{{ route('petugas.peminjaman.index') }}" class="nav-item {{ request()->routeIs('petugas.peminjaman.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                Peminjaman
            </a>

            <a href="{{ route('petugas.pengembalian.index') }}" class="nav-item {{ request()->routeIs('petugas.pengembalian.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Pengembalian
            </a>

            <a href="{{ route('petugas.reservasi.index') }}" class="nav-item {{ request()->routeIs('petugas.reservasi.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Reservasi Buku
            </a>

            <div class="nav-label">Rekapitulasi & LPJ</div>

            <a href="{{ route('petugas.laporan.index') }}" class="nav-item {{ request()->routeIs('petugas.laporan.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Laporan & Kas
            </a>

            @if(auth('petugas')->user()->isAdmin())
            <div class="nav-label">Administrasi & Hak Akses</div>

            <a href="{{ route('petugas.staf.index') }}" class="nav-item {{ request()->routeIs('petugas.staf.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                Manajemen Staf
            </a>

            <a href="{{ route('petugas.pengaturan.index') }}" class="nav-item {{ request()->routeIs('petugas.pengaturan.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Pengaturan
            </a>
            @endif
        </div>

        <div class="sidebar-footer">
            <div class="user-badge">
                <div class="user-avatar">
                    {{ strtoupper(substr(auth('petugas')->user()->nama_lengkap ?? 'P', 0, 1)) }}
                </div>
                <div class="user-details">
                    <div class="user-name">{{ auth('petugas')->user()->nama_lengkap ?? 'Petugas' }}</div>
                    <div class="user-role">{{ auth('petugas')->user()->label_role ?? 'Petugas Perpustakaan' }}</div>
                </div>
                <form action="{{ route('petugas.logout') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="btn-logout" title="Logout">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Content Wrapper -->
    <div class="main-wrapper">
        <header class="topbar">
            <div>
                <h1 class="topbar-title">@yield('title', 'Admin Perpustakaan')</h1>
            </div>
            <div style="display: flex; align-items: center; gap: 12px;">
                <span style="font-size: 13px; color: #64748b; background: #f1f5f9; padding: 5px 12px; border-radius: 9999px; font-weight: 500;">
                    {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                </span>
            </div>
        </header>

        <main class="main-content">
            @yield('content')
        </main>
    </div>

</body>
</html>
