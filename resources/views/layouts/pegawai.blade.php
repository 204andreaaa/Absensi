<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Pegawai Panel</title>

    <link rel="stylesheet" href="{{ asset('admin/dist/assets/modules/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/dist/assets/modules/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/dist/assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/dist/assets/css/components.css') }}">

    <style>
        :root {
            --pegawai-bg: #eef3f8;
            --pegawai-surface: #ffffff;
            --pegawai-surface-soft: #f6f9fc;
            --pegawai-primary: #1d4ed8;
            --pegawai-primary-dark: #163ea8;
            --pegawai-accent: #14b8a6;
            --pegawai-text: #162033;
            --pegawai-muted: #6b7a90;
            --pegawai-border: #d9e3f0;
            --pegawai-danger: #ef4444;
            --pegawai-success: #16a34a;
            --pegawai-warning: #f59e0b;
            --pegawai-shadow: 0 14px 40px rgba(15, 23, 42, 0.10);
            --pegawai-radius: 24px;
        }

        body {
            background:
                radial-gradient(circle at top left, rgba(20, 184, 166, 0.14), transparent 28%),
                linear-gradient(180deg, #f4f8fc 0%, #edf2f7 100%);
            color: var(--pegawai-text);
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        }

        #app,
        .main-wrapper {
            min-height: 100vh;
        }

        .pegawai-shell {
            display: flex;
            min-height: 100vh;
        }

        .pegawai-sidebar {
            width: 280px;
            padding: 24px 18px;
            background: rgba(255, 255, 255, 0.82);
            backdrop-filter: blur(20px);
            border-right: 1px solid rgba(217, 227, 240, 0.9);
            position: sticky;
            top: 0;
            height: 100vh;
        }

        .pegawai-brand {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 12px 14px;
            border-radius: 20px;
            background: linear-gradient(135deg, var(--pegawai-primary), var(--pegawai-accent));
            color: #fff;
            box-shadow: var(--pegawai-shadow);
            margin-bottom: 28px;
        }

        .pegawai-brand-mark {
            width: 46px;
            height: 46px;
            border-radius: 16px;
            display: grid;
            place-items: center;
            background: rgba(255, 255, 255, 0.18);
            font-size: 18px;
        }

        .pegawai-brand-title {
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.14em;
            opacity: 0.82;
            margin-bottom: 2px;
        }

        .pegawai-brand-name {
            font-size: 1.1rem;
            font-weight: 700;
            line-height: 1.2;
        }

        .pegawai-menu-title {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.14em;
            color: #8b98ab;
            margin: 22px 12px 10px;
            font-weight: 700;
        }

        .pegawai-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .pegawai-menu li {
            margin-bottom: 8px;
        }

        .pegawai-menu a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 16px;
            border-radius: 18px;
            color: var(--pegawai-text);
            text-decoration: none;
            font-weight: 600;
            transition: 0.2s ease;
        }

        .pegawai-menu a i {
            width: 18px;
            text-align: center;
            color: var(--pegawai-muted);
        }

        .pegawai-menu li.active a,
        .pegawai-menu a:hover {
            background: linear-gradient(135deg, rgba(29, 78, 216, 0.12), rgba(20, 184, 166, 0.12));
            color: var(--pegawai-primary-dark);
        }

        .pegawai-menu li.active a i,
        .pegawai-menu a:hover i {
            color: var(--pegawai-primary);
        }

        .pegawai-main {
            flex: 1;
            min-width: 0;
            padding: 22px 22px 110px;
        }

        .pegawai-topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 18px 22px;
            border-radius: 28px;
            background: rgba(255, 255, 255, 0.84);
            border: 1px solid rgba(217, 227, 240, 0.8);
            box-shadow: var(--pegawai-shadow);
            backdrop-filter: blur(18px);
            margin-bottom: 22px;
        }

        .pegawai-topbar-title {
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.14em;
            color: var(--pegawai-muted);
            margin-bottom: 2px;
            font-weight: 700;
        }

        .pegawai-topbar-subtitle {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--pegawai-text);
            line-height: 1.2;
        }

        .pegawai-user {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 10px 8px 8px;
            border-radius: 20px;
            background: var(--pegawai-surface-soft);
        }

        .pegawai-user-avatar {
            width: 42px;
            height: 42px;
            border-radius: 15px;
            display: grid;
            place-items: center;
            background: linear-gradient(135deg, var(--pegawai-primary), #4f46e5);
            color: #fff;
            font-size: 16px;
        }

        .pegawai-user-name {
            font-weight: 700;
            line-height: 1.1;
        }

        .pegawai-user-role {
            color: var(--pegawai-muted);
            font-size: 0.82rem;
        }

        .section-header {
            background: transparent;
            box-shadow: none;
            min-height: auto;
            margin: 0 0 16px;
            padding: 0 4px;
        }

        .section-header h1 {
            font-size: 2rem;
            font-weight: 800;
            color: var(--pegawai-text);
            margin: 0;
        }

        .section-body {
            padding-top: 0;
        }

        .card,
        .card.card-statistic-1 {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(217, 227, 240, 0.9);
            border-radius: var(--pegawai-radius);
            box-shadow: var(--pegawai-shadow);
            overflow: hidden;
        }

        .card .card-header {
            background: transparent;
            border-bottom: 1px solid rgba(217, 227, 240, 0.8);
            padding: 20px 22px 16px;
        }

        .card .card-header h4 {
            font-size: 1.03rem;
            font-weight: 800;
            color: var(--pegawai-text);
            margin: 0;
        }

        .card .card-body {
            padding: 22px;
        }

        .card.card-statistic-1 {
            padding: 18px;
        }

        .card.card-statistic-1 .card-icon {
            width: 56px;
            height: 56px;
            border-radius: 18px;
            box-shadow: none;
        }

        .card.card-statistic-1 .card-header h4 {
            font-size: 0.82rem;
            color: var(--pegawai-muted);
            letter-spacing: 0.04em;
        }

        .card.card-statistic-1 .card-body {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--pegawai-text);
            padding: 0;
        }

        .table th {
            color: var(--pegawai-muted);
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            border-top: 0;
        }

        .table td,
        .table th {
            padding: 16px 18px;
            vertical-align: middle;
        }

        .badge {
            padding: 8px 12px;
            border-radius: 999px;
            font-weight: 700;
        }

        .btn {
            border-radius: 16px;
            font-weight: 700;
            padding: 12px 18px;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--pegawai-primary), #3b82f6);
            border-color: transparent;
        }

        .btn-success {
            background: linear-gradient(135deg, var(--pegawai-success), #22c55e);
            border-color: transparent;
        }

        .btn-danger {
            background: linear-gradient(135deg, var(--pegawai-danger), #fb7185);
            border-color: transparent;
        }

        .btn-outline-primary {
            border-color: rgba(29, 78, 216, 0.24);
            color: var(--pegawai-primary);
        }

        .main-footer {
            display: none;
        }

        .pegawai-bottom-nav {
            position: fixed;
            left: 16px;
            right: 16px;
            bottom: 14px;
            z-index: 20;
            display: none;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 8px;
            padding: 10px;
            border-radius: 24px;
            background: rgba(255, 255, 255, 0.94);
            border: 1px solid rgba(217, 227, 240, 0.95);
            box-shadow: 0 18px 38px rgba(15, 23, 42, 0.18);
            backdrop-filter: blur(18px);
        }

        .pegawai-bottom-nav a {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 5px;
            padding: 10px 6px;
            border-radius: 18px;
            color: var(--pegawai-muted);
            text-decoration: none;
            font-size: 0.72rem;
            font-weight: 700;
        }

        .pegawai-bottom-nav a i {
            font-size: 1rem;
        }

        .pegawai-bottom-nav a.active {
            background: linear-gradient(135deg, rgba(29, 78, 216, 0.14), rgba(20, 184, 166, 0.14));
            color: var(--pegawai-primary);
        }

        .pegawai-logout {
            margin-top: 28px;
            padding-top: 20px;
            border-top: 1px solid rgba(217, 227, 240, 0.9);
        }

        .pegawai-logout button {
            width: 100%;
            text-align: left;
            border: 0;
            background: #fff3f2;
            color: #b42318;
            border-radius: 18px;
            padding: 14px 16px;
            font-weight: 700;
        }

        @media (max-width: 991.98px) {
            .pegawai-shell {
                display: block;
            }

            .pegawai-sidebar {
                display: none;
            }

            .pegawai-main {
                padding: 14px 14px 104px;
            }

            .pegawai-topbar {
                padding: 16px;
                border-radius: 24px;
                margin-bottom: 16px;
            }

            .pegawai-topbar-subtitle {
                font-size: 1.05rem;
            }

            .section-header {
                padding: 0 2px;
            }

            .section-header h1 {
                font-size: 1.55rem;
            }

            .card .card-header,
            .card .card-body {
                padding: 18px;
            }

            .pegawai-bottom-nav {
                display: grid;
            }
        }

        @media (max-width: 575.98px) {
            .pegawai-topbar-title {
                font-size: 0.7rem;
            }

            .pegawai-user {
                padding: 6px 8px 6px 6px;
            }

            .pegawai-user-avatar {
                width: 38px;
                height: 38px;
                border-radius: 13px;
            }

            .table td,
            .table th {
                padding: 14px 12px;
            }
        }
    </style>

    @stack('styles')
</head>
<body>
    @php
        $showDatasetMenu = auth()->user()->dataset_wajahs()->count() < 15;
    @endphp

    <div id="app">
        <div class="pegawai-shell">
            <aside class="pegawai-sidebar">
                <div class="pegawai-brand">
                    <div class="pegawai-brand-mark">
                        <i class="fas fa-camera"></i>
                    </div>
                    <div>
                        <div class="pegawai-brand-title">Face Attendance</div>
                        <div class="pegawai-brand-name">Pegawai App</div>
                    </div>
                </div>

                <div class="pegawai-menu-title">Dashboard</div>
                <ul class="pegawai-menu">
                    <li class="{{ request()->routeIs('pegawai.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('pegawai.dashboard') }}">
                            <i class="fas fa-home"></i>
                            <span>Beranda</span>
                        </a>
                    </li>
                </ul>

                <div class="pegawai-menu-title">Absensi</div>
                <ul class="pegawai-menu">
                    @if($showDatasetMenu)
                        <li class="{{ request()->routeIs('pegawai.dataset') ? 'active' : '' }}">
                            <a href="{{ route('pegawai.dataset') }}">
                                <i class="fas fa-database"></i>
                                <span>Dataset Wajah</span>
                            </a>
                        </li>
                    @endif

                    <li class="{{ request()->routeIs('pegawai.absensi') ? 'active' : '' }}">
                        <a href="{{ route('pegawai.absensi') }}">
                            <i class="fas fa-camera"></i>
                            <span>Absensi</span>
                        </a>
                    </li>

                    <li class="{{ request()->routeIs('pegawai.riwayat') ? 'active' : '' }}">
                        <a href="{{ route('pegawai.riwayat') }}">
                            <i class="fas fa-history"></i>
                            <span>Riwayat Absensi</span>
                        </a>
                    </li>
                </ul>

                <div class="pegawai-logout">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit">
                            <i class="fas fa-sign-out-alt mr-2"></i>
                            Logout
                        </button>
                    </form>
                </div>
            </aside>

            <main class="pegawai-main">
                <div class="pegawai-topbar">
                    <div>
                        <div class="pegawai-topbar-title">Employee Workspace</div>
                        <div class="pegawai-topbar-subtitle">Absensi wajah dari mana saja</div>
                    </div>

                    <div class="pegawai-user">
                        <div class="pegawai-user-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div>
                            <div class="pegawai-user-name">{{ auth()->user()->nama ?? 'Pegawai' }}</div>
                            <div class="pegawai-user-role">Pegawai</div>
                        </div>
                    </div>
                </div>

                <section class="section">
                    <div class="section-body">
                        @yield('content')
                    </div>
                </section>
            </main>
        </div>

        <nav class="pegawai-bottom-nav">
            <a
                href="{{ route('pegawai.dashboard') }}"
                class="{{ request()->routeIs('pegawai.dashboard') ? 'active' : '' }}"
            >
                <i class="fas fa-home"></i>
                <span>Home</span>
            </a>

            <a
                href="{{ route('pegawai.absensi') }}"
                class="{{ request()->routeIs('pegawai.absensi') ? 'active' : '' }}"
            >
                <i class="fas fa-camera"></i>
                <span>Absen</span>
            </a>

            @if($showDatasetMenu)
                <a
                    href="{{ route('pegawai.dataset') }}"
                    class="{{ request()->routeIs('pegawai.dataset') ? 'active' : '' }}"
                >
                    <i class="fas fa-database"></i>
                    <span>Dataset</span>
                </a>
            @else
                <a
                    href="{{ route('pegawai.riwayat') }}"
                    class="{{ request()->routeIs('pegawai.riwayat') ? 'active' : '' }}"
                >
                    <i class="fas fa-history"></i>
                    <span>Riwayat</span>
                </a>
            @endif

            <form method="POST" action="{{ route('logout') }}" class="m-0">
                @csrf
                <button
                    type="submit"
                    class="w-100 border-0 bg-transparent p-0 h-100"
                    style="outline: none;"
                >
                    <span
                        class="d-flex flex-column align-items-center justify-content-center"
                        style="gap: 5px; padding: 10px 6px; border-radius: 18px; color: var(--pegawai-muted); font-size: 0.72rem; font-weight: 700;"
                    >
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Keluar</span>
                    </span>
                </button>
            </form>
        </nav>
    </div>

    <script src="{{ asset('admin/dist/assets/modules/jquery.min.js') }}"></script>
    <script src="{{ asset('admin/dist/assets/modules/popper.js') }}"></script>
    <script src="{{ asset('admin/dist/assets/modules/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('admin/dist/assets/modules/nicescroll/jquery.nicescroll.min.js') }}"></script>
    <script src="{{ asset('admin/dist/assets/js/stisla.js') }}"></script>
    <script src="{{ asset('admin/dist/assets/js/scripts.js') }}"></script>
    <script src="{{ asset('admin/dist/assets/js/custom.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @stack('scripts')
</body>
</html>
