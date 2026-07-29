@extends('layouts.pegawai')

@push('styles')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

        body, .home-shell {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .mobile-only { display: block; }
        .desktop-only { display: none; }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .home-shell {
            display: grid;
            gap: 24px;
            animation: fadeInUp 0.6s ease-out;
        }

        /* Glassmorphism utility */
        .glass-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.05);
            border-radius: 24px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .glass-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.08);
        }

        .home-desktop-hero {
            border-radius: 32px;
            padding: 40px;
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            box-shadow: 0 22px 48px rgba(59, 130, 246, 0.25);
            color: #fff;
            position: relative;
            overflow: hidden;
            animation: fadeInUp 0.5s ease-out;
        }

        .home-desktop-hero::before {
            content: '';
            position: absolute;
            top: -50%; left: -20%;
            width: 80%; height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0) 70%);
            transform: rotate(30deg);
        }

        .home-desktop-hero__row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            position: relative;
            z-index: 2;
        }

        .home-desktop-hero__eyebrow {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            font-weight: 700;
            margin-bottom: 12px;
        }

        .home-desktop-hero__title {
            margin: 0 0 12px;
            font-size: clamp(2.2rem, 3.5vw, 3rem);
            font-weight: 800;
            color: #ffffff;
            line-height: 1.2;
        }

        .home-desktop-hero__text {
            margin: 0;
            max-width: 600px;
            color: rgba(255, 255, 255, 0.9);
            line-height: 1.7;
            font-size: 1.05rem;
        }

        .home-desktop-clock {
            min-width: 260px;
            padding: 24px;
            border-radius: 28px;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #fff;
            text-align: center;
        }

        .home-desktop-clock__time {
            font-size: 2.5rem;
            font-weight: 800;
            line-height: 1;
            letter-spacing: -1px;
        }

        .home-desktop-clock__date {
            margin-top: 10px;
            font-size: 0.95rem;
            opacity: 0.9;
        }

        .home-stats-row {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 20px;
            margin-top: 32px;
            position: relative;
            z-index: 2;
        }

        .home-stat-card {
            padding: 24px;
            border-radius: 24px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }

        .home-stat-card:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-5px);
        }

        .home-stat-card__label {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            font-weight: 700;
        }

        .home-stat-card__value {
            margin-top: 12px;
            color: #ffffff;
            font-size: 1.8rem;
            font-weight: 800;
        }

        .home-stat-card__hint {
            margin-top: 8px;
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.85rem;
        }

        .home-mobile-card {
            position: relative;
            overflow: hidden;
            border-radius: 36px;
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            box-shadow: 0 20px 40px rgba(29, 78, 216, 0.25);
            color: #fff;
        }

        .home-mobile-card::after {
            content: '';
            position: absolute;
            top: -20%; right: -10%;
            width: 150px; height: 150px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0) 70%);
        }

        .home-mobile-card__content {
            position: relative;
            z-index: 1;
            padding: 30px;
        }

        .home-greeting {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
        }

        .home-greeting__left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .home-avatar {
            width: 54px;
            height: 54px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
            font-size: 1.2rem;
            backdrop-filter: blur(5px);
        }

        .home-greeting__hello {
            font-size: 0.95rem;
            opacity: 0.9;
        }

        .home-greeting__name {
            font-weight: 800;
            font-size: 1.2rem;
        }

        .home-quick-badge {
            width: 46px;
            height: 46px;
            border-radius: 16px;
            display: grid;
            place-items: center;
            background: rgba(255, 255, 255, 0.15);
            color: #fff;
            backdrop-filter: blur(5px);
        }

        .home-time-card {
            background: #ffffff;
            color: #1e293b;
            border-radius: 28px;
            padding: 28px 24px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .home-time-card__time {
            font-size: clamp(2.5rem, 10vw, 3rem);
            font-weight: 800;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            line-height: 1;
        }

        .home-time-card__date {
            margin-top: 10px;
            color: #64748b;
            font-size: 0.95rem;
            font-weight: 600;
        }

        .home-time-card__divider {
            height: 2px;
            width: 50px;
            margin: 20px auto;
            background: #e2e8f0;
            border-radius: 2px;
        }

        .home-time-card__label {
            color: #94a3b8;
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
        }

        .home-time-card__schedule {
            margin-top: 8px;
            font-size: 1.3rem;
            font-weight: 800;
            color: #0f172a;
        }

        .home-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
        }

        .home-action {
            display: flex;
            flex-direction: column;
            padding: 24px;
            border-radius: 28px;
            background: #ffffff;
            color: #1e293b;
            text-decoration: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
            border: 1px solid rgba(0,0,0,0.02);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            min-height: 160px;
        }

        .home-action:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(59, 130, 246, 0.15);
            text-decoration: none;
            color: #1e293b;
        }

        .home-action__icon {
            width: 60px;
            height: 60px;
            border-radius: 20px;
            display: grid;
            place-items: center;
            font-size: 1.5rem;
            margin-bottom: 18px;
            transition: transform 0.3s ease;
        }
        
        .home-action:hover .home-action__icon {
            transform: scale(1.1) rotate(5deg);
        }

        .home-action__title {
            font-size: 1.15rem;
            font-weight: 800;
            color: #0f172a;
        }

        .home-action__desc {
            margin-top: 8px;
            color: #64748b;
            font-size: 0.9rem;
            line-height: 1.5;
        }

        .home-action--dataset .home-action__icon {
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            color: #d97706;
        }

        .home-action--absen .home-action__icon {
            background: linear-gradient(135deg, #dbeafe, #bfdbfe);
            color: #2563eb;
        }

        .home-action--riwayat .home-action__icon {
            background: linear-gradient(135deg, #d1fae5, #a7f3d0);
            color: #059669;
        }

        .home-action--status .home-action__icon {
            background: linear-gradient(135deg, #e0e7ff, #c7d2fe);
            color: #4f46e5;
        }

        .home-action--highlight {
            border: 2px solid #fbbf24;
            background: #fffbeb;
        }

        .home-action__badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-top: auto;
            padding: 8px 12px;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 800;
            align-self: flex-start;
        }

        .home-action__badge--warning { background: #fef3c7; color: #b45309; }
        .home-action__badge--success { background: #dcfce7; color: #15803d; }

        .home-summary { display: grid; gap: 24px; }

        .home-summary-card {
            border-radius: 28px;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.05);
            padding: 30px;
            transition: transform 0.3s ease;
        }
        
        .home-summary-card:hover {
            transform: translateY(-3px);
        }

        .home-summary-card__label {
            color: #3b82f6;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            font-weight: 800;
            margin-bottom: 12px;
        }

        .home-summary-card__title {
            margin: 0 0 10px;
            font-size: 1.4rem;
            font-weight: 800;
            color: #0f172a;
        }

        .home-summary-card__text {
            color: #475569;
            margin: 0;
            line-height: 1.6;
            font-size: 0.95rem;
        }

        .today-status-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
            margin-top: 24px;
        }

        .today-status-item {
            padding: 20px;
            border-radius: 20px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
        }
        
        .today-status-item:hover {
            background: #ffffff;
            box-shadow: 0 10px 20px rgba(0,0,0,0.03);
            border-color: #cbd5e1;
        }

        .today-status-item__label {
            color: #64748b;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .today-status-item__value {
            margin-top: 10px;
            color: #0f172a;
            font-size: 1.25rem;
            font-weight: 800;
        }

        .home-history {
            display: grid;
            gap: 16px;
            margin-top: 20px;
        }

        .home-history-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 20px;
            border-radius: 20px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            transition: all 0.2s ease;
        }
        
        .home-history-item:hover {
            border-color: #3b82f6;
            box-shadow: 0 5px 15px rgba(59, 130, 246, 0.08);
        }

        .home-history-item__date {
            font-weight: 800;
            color: #0f172a;
            font-size: 1.05rem;
        }

        .home-history-item__meta {
            margin-top: 6px;
            font-size: 0.9rem;
            color: #64748b;
        }

        .home-history-badge {
            padding: 10px 16px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 800;
            white-space: nowrap;
        }

        .home-history-badge--success { background: #dcfce7; color: #15803d; }
        .home-history-badge--warning { background: #fef3c7; color: #b45309; }
        .home-history-badge--neutral { background: #f1f5f9; color: #475569; }

        .home-empty {
            padding: 30px 20px;
            border-radius: 24px;
            background: #f8fafc;
            color: #64748b;
            text-align: center;
            border: 2px dashed #cbd5e1;
            font-weight: 600;
        }

        @media (min-width: 992px) {
            .mobile-only { display: none; }
            .desktop-only { display: block; }
            .home-shell {
                grid-template-columns: minmax(0, 1fr) minmax(360px, 400px);
                align-items: start;
            }
        }

        @media (max-width: 1199.98px) {
            .home-stats-row { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }

        @media (max-width: 768px) {
            .home-desktop-hero { padding: 30px; }
            .home-desktop-hero__row { flex-direction: column; align-items: flex-start; }
            .home-desktop-clock { width: 100%; margin-top: 20px; }
        }

        @media (max-width: 575.98px) {
            .home-mobile-card__content, .home-summary-card { padding: 24px; }
            .home-grid, .today-status-grid { grid-template-columns: 1fr; }
            .home-action { min-height: 140px; }
        }
    </style>
@endpush

@section('content')
    @php
        $now = \Carbon\Carbon::now();
        $scheduleLabel = optional(auth()->user()->jadwal)->jam_masuk && optional(auth()->user()->jadwal)->jam_pulang
            ? optional(auth()->user()->jadwal)->jam_masuk . ' WIB - ' . optional(auth()->user()->jadwal)->jam_pulang . ' WIB'
            : 'Jadwal belum diatur';
        $datasetReady = $stats['dataset_count'] >= 15;
    @endphp

    <div class="section-header">
        <h1>Home Pegawai</h1>
    </div>

    <div class="desktop-only">
        <div class="home-desktop-hero mb-4">
            <div class="home-desktop-hero__row">
                <div>
                    <div class="home-desktop-hero__eyebrow">Employee Workspace</div>
                    <h2 class="home-desktop-hero__title">Halo, {{ auth()->user()->nama }}</h2>
                    <p class="home-desktop-hero__text">
                        Versi desktop menampilkan ringkasan yang lebih lebar: jadwal hari ini, status absensi, dan shortcut utama ke dataset, absen, serta riwayat.
                    </p>
                </div>

                <div class="home-desktop-clock">
                    <div class="home-desktop-clock__time">{{ $now->format('H:i') }} WIB</div>
                    <div class="home-desktop-clock__date">{{ $now->translatedFormat('l, d F Y') }}</div>
                </div>
            </div>

            <div class="home-stats-row">
                <div class="home-stat-card">
                    <div class="home-stat-card__label">Jadwal Hari Ini</div>
                    <div class="home-stat-card__value">{{ $scheduleLabel }}</div>
                    <div class="home-stat-card__hint">Shift aktif pegawai</div>
                </div>

                <div class="home-stat-card">
                    <div class="home-stat-card__label">Dataset Wajah</div>
                    <div class="home-stat-card__value">{{ $stats['dataset_count'] }}/15</div>
                    <div class="home-stat-card__hint">{{ $datasetReady ? 'Siap dipakai absen' : 'Perlu dilengkapi dulu' }}</div>
                </div>

                <div class="home-stat-card">
                    <div class="home-stat-card__label">Masuk Hari Ini</div>
                    <div class="home-stat-card__value">{{ $stats['sudah_masuk_hari_ini'] ? 'Sudah' : 'Belum' }}</div>
                    <div class="home-stat-card__hint">Status absensi masuk</div>
                </div>

                <div class="home-stat-card">
                    <div class="home-stat-card__label">Hadir Bulan Ini</div>
                    <div class="home-stat-card__value">{{ $stats['hadir_bulan_ini'] }}</div>
                    <div class="home-stat-card__hint">Total kehadiran bulan berjalan</div>
                </div>
            </div>
        </div>
    </div>

    <div class="home-shell">
        <div class="home-mobile-card mobile-only">
            <div class="home-mobile-card__content">
                <div class="home-greeting">
                    <div class="home-greeting__left">
                        <div class="home-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div>
                            <div class="home-greeting__hello">Hello, {{ auth()->user()->nama }}</div>
                            <div class="home-greeting__name">Pegawai App</div>
                        </div>
                    </div>

                    <div class="home-quick-badge">
                        <i class="fas fa-bell"></i>
                    </div>
                </div>

                <div class="home-time-card">
                    <div class="home-time-card__time">{{ $now->format('H:i') }} WIB</div>
                    <div class="home-time-card__date">{{ $now->translatedFormat('l, d F Y') }}</div>
                    <div class="home-time-card__divider"></div>
                    <div class="home-time-card__label">Jadwal Hari Ini</div>
                    <div class="home-time-card__schedule">{{ $scheduleLabel }}</div>
                </div>
            </div>
        </div>

        @if($holidayMessage)
            <div class="alert alert-warning mb-3" style="border-radius: 16px;">
                <strong>Hari Libur!</strong> {{ $holidayMessage }}. Absensi hari ini tidak tersedia.
            </div>
        @endif

        <div class="home-summary mobile-only">

            <div class="home-summary-card">
                <div class="home-summary-card__label">Menu Cepat</div>
                <h3 class="home-summary-card__title">Akses fitur utama dari satu layar</h3>
                <p class="home-summary-card__text">Tampilan home ini diringkas supaya pegawai langsung tahu harus buka dataset, absen, atau cek riwayat.</p>

                <div class="home-grid">
                    <a href="{{ route('pegawai.dataset') }}" class="home-action home-action--dataset {{ $datasetReady ? '' : 'home-action--highlight' }}">
                        <div class="home-action__icon">
                            <i class="fas fa-database"></i>
                        </div>
                        <div class="home-action__title">Dataset</div>
                        <div class="home-action__desc">
                            {{ $datasetReady ? 'Dataset wajah sudah siap digunakan.' : 'Lengkapi dataset terlebih dahulu sebelum absen.' }}
                        </div>
                        <div class="home-action__badge {{ $datasetReady ? 'home-action__badge--success' : 'home-action__badge--warning' }}">
                            {{ $stats['dataset_count'] }}/15
                        </div>
                    </a>

                    @if($holidayMessage)
                        <a href="javascript:void(0)" onclick="Swal.fire({icon: 'info', title: 'Libur', text: '{{ $holidayMessage }}. Absensi ditutup.'})" class="home-action home-action--absen">
                    @else
                        <a href="{{ route('pegawai.absensi') }}" class="home-action home-action--absen">
                    @endif
                        <div class="home-action__icon">
                            <i class="fas fa-camera"></i>
                        </div>
                        <div class="home-action__title">Absen</div>
                        <div class="home-action__desc">Masuk ke live camera verification untuk absen wajah.</div>
                    </a>

                    <a href="{{ route('pegawai.riwayat') }}" class="home-action home-action--riwayat">
                        <div class="home-action__icon">
                            <i class="fas fa-history"></i>
                        </div>
                        <div class="home-action__title">Riwayat</div>
                        <div class="home-action__desc">Lihat daftar absensi masuk dan pulang yang sudah tersimpan.</div>
                    </a>

                    <div class="home-action home-action--status">
                        <div class="home-action__icon">
                            <i class="fas fa-clipboard-check"></i>
                        </div>
                        <div class="home-action__title">Status Hari Ini</div>
                        <div class="home-action__desc">
                            {{ $todayAttendance ? 'Absensi hari ini sudah mulai tercatat.' : 'Belum ada absensi hari ini.' }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="home-summary-card">
                <div class="home-summary-card__label">Hari Ini</div>
                <h3 class="home-summary-card__title">Ringkasan absensi harian</h3>
                <p class="home-summary-card__text">Pantau status masuk dan pulang tanpa harus pindah ke halaman lain.</p>

                <div class="today-status-grid">
                    <div class="today-status-item">
                        <div class="today-status-item__label">Jam Masuk</div>
                        <div class="today-status-item__value">{{ optional($todayAttendance)->jam_masuk ?? '-' }}</div>
                    </div>

                    <div class="today-status-item">
                        <div class="today-status-item__label">Jam Pulang</div>
                        <div class="today-status-item__value">{{ optional($todayAttendance)->jam_pulang ?? '-' }}</div>
                    </div>

                    <div class="today-status-item">
                        <div class="today-status-item__label">Masuk Hari Ini</div>
                        <div class="today-status-item__value">{{ $stats['sudah_masuk_hari_ini'] ? 'Sudah' : 'Belum' }}</div>
                    </div>

                    <div class="today-status-item">
                        <div class="today-status-item__label">Hadir Bulan Ini</div>
                        <div class="today-status-item__value">{{ $stats['hadir_bulan_ini'] }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="home-summary-card" style="grid-column: 1 / -1;">
            <div class="d-flex justify-content-between align-items-center mb-3" style="gap: 12px;">
                <div>
                    <div class="home-summary-card__label mb-1">Riwayat Cepat</div>
                    <h3 class="home-summary-card__title mb-0">Absensi terbaru</h3>
                </div>

                <a href="{{ route('pegawai.riwayat') }}" class="btn btn-outline-primary btn-sm">
                    Lihat Semua
                </a>
            </div>

            @if($monthlyAttendances->isEmpty())
                <div class="home-empty">Belum ada riwayat absensi.</div>
            @else
                <div class="home-history">
                    @foreach($monthlyAttendances as $item)
                        @php
                            $statusLabel = 'Tepat Waktu';
                            $statusClass = 'home-history-badge--success';

                            if ($item->status === 'terlambat') {
                                $statusLabel = 'Terlambat';
                                $statusClass = 'home-history-badge--warning';
                            } elseif ($item->jam_masuk && !$item->jam_pulang) {
                                $statusLabel = 'Belum Pulang';
                                $statusClass = 'home-history-badge--neutral';
                            }
                        @endphp

                        <div class="home-history-item">
                            <div>
                                <div class="home-history-item__date">{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d F Y') }}</div>
                                <div class="home-history-item__meta">
                                    Masuk {{ $item->jam_masuk ?? '-' }} · Pulang {{ $item->jam_pulang ?? '-' }}
                                </div>
                            </div>

                            <span class="home-history-badge {{ $statusClass }}">
                                {{ $statusLabel }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection
