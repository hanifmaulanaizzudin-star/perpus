<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk Portal Petugas - PerpusKu</title>

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
            background: radial-gradient(circle at 50% 10%, #e0e7ff 0%, #f8fafc 60%, #f1f5f9 100%);
            color: #0f172a;
            font-size: 14px;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            -webkit-font-smoothing: antialiased;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            background: #ffffff;
            border: 1px solid #edf2f7;
            border-radius: 20px;
            padding: 36px;
            box-shadow: 0 20px 40px -15px rgba(15, 23, 42, 0.08), 0 0 1px 1px rgba(0, 0, 0, 0.02);
        }

        .brand-header {
            margin-bottom: 28px;
            text-align: center;
        }

        .brand-logo {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 18px;
        }

        .brand-mark {
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
            color: #ffffff;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }

        .brand-name {
            font-size: 20px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.03em;
        }

        .title {
            font-size: 20px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 6px;
            letter-spacing: -0.02em;
        }

        .subtitle {
            font-size: 13.5px;
            color: #64748b;
            line-height: 1.5;
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 6px;
        }

        input {
            width: 100%;
            padding: 11px 14px;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            font-size: 13.5px;
            font-family: inherit;
            color: #0f172a;
            background-color: #ffffff;
            outline: none;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }

        input:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
        }

        .btn-submit {
            width: 100%;
            padding: 12px 18px;
            margin-top: 8px;
            background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
            color: #ffffff;
            font-size: 14px;
            font-weight: 700;
            font-family: inherit;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(79, 70, 229, 0.3);
            transition: all 0.18s ease-in-out;
        }

        .btn-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(79, 70, 229, 0.4);
        }

        .error-alert {
            background-color: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
            padding: 12px 14px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 13px;
        }

        .card-footer {
            margin-top: 26px;
            padding-top: 20px;
            border-top: 1px solid #f1f5f9;
            text-align: center;
            font-size: 13px;
            color: #64748b;
        }

        .card-footer a {
            color: #4f46e5;
            font-weight: 700;
            text-decoration: none;
        }

        .card-footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="brand-header">
            <div class="brand-logo">
                <div class="brand-mark">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                    </svg>
                </div>
                <span class="brand-name">PerpusKu</span>
            </div>
            <h1 class="title">Masuk Portal Petugas</h1>
            <p class="subtitle">Kelola sirkulasi buku, katalog, peminjaman, dan data anggota perpustakaan.</p>
        </div>

        @if ($errors->any())
            <div class="error-alert">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form action="{{ route('petugas.login.process') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="identitas">Username atau Email</label>
                <input
                    type="text"
                    id="identitas"
                    name="identitas"
                    value="{{ old('identitas') }}"
                    placeholder="nama@email.com atau username"
                    required
                    autofocus
                >
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Masukkan password akun"
                    required
                >
            </div>

            <button type="submit" class="btn-submit">Masuk Dashboard</button>
        </form>

        <div class="card-footer">
            Bukan petugas? <a href="{{ route('anggota.login') }}">Masuk sebagai Anggota &rarr;</a>
        </div>
    </div>

</body>
</html>
