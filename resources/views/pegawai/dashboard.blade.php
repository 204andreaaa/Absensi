@extends('layouts.pegawai')

@push('styles')
    <style>
        .mobile-only { display: block; }
        .desktop-only { display: none; }

        .home-shell {
            display: grid;
            gap: 20px;
        }

        .home-desktop-hero {
            border-radius: 30px;
            padding: 26px 28px;
            background:
                radial-gradient(circle at top right, rgba(20, 184, 166, 0.22), transparent 28%),
                linear-gradient(135deg, #ffffff 0%, #f8fbff 48%, #eef5fb 100%);
            box-shadow: 0 22px 48px rgba(148, 163, 184, 0.16);
        }

        .home-desktop-hero__row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
        }

        .home-desktop-hero__eyebrow {
            color: #64748b;
            font-size: 0.76rem;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .home-desktop-hero__title {
            margin: 0 0 8px;
            font-size: clamp(2rem, 3vw, 2.7rem);
            font-weight: 800;
            color: #0f172a;
        }

        .home-desktop-hero__text {
            margin: 0;
            max-width: 700px;
            color: #64748b;
            line-height: 1.7;
        }

        .home-desktop-clock {
            min-width: 240px;
            padding: 18px 20px;
            border-radius: 24px;
            background: linear-gradient(135deg, #5b74ea, #6d7ff3);
            color: #fff;
            box-shadow: 0 18px 38px rgba(91, 116, 234, 0.24);
            text-align: center;
        }

        .home-desktop-clock__time {
            font-size: 2rem;
            font-weight: 800;
            line-height: 1;
        }

        .home-desktop-clock__date {
            margin-top: 8px;
            font-size: 0.86rem;
            opacity: 0.9;
        }

        .home-stats-row {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 16px;
            margin-top: 22px;
        }

        .home-stat-card {
            padding: 18px 18px 16px;
            border-radius: 24px;
            background: rgba(255, 255, 255, 0.92);
            box-shadow: 0 18px 38px rgba(148, 163, 184, 0.14);
        }

        .home-stat-card__label {
            color: #94a3b8;
            font-size: 0.74rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-weight: 700;
        }

        .home-stat-card__value {
            margin-top: 10px;
            color: #0f172a;
            font-size: 1.45rem;
            font-weight: 800;
        }

        .home-stat-card__hint {
            margin-top: 6px;
            color: #64748b;
            font-size: 0.84rem;
        }

        .home-mobile-card {
            position: relative;
            overflow: hidden;
            border-radius: 34px;
            background: linear-gradient(180deg, #6d7ff3 0%, #5b74ea 100%);
            box-shadow: 0 26px 60px rgba(91, 116, 234, 0.26);
            color: #fff;
        }

        .home-mobile-card::after {
            content: '';
            position: absolute;
            left: -8%;
            right: -8%;
            bottom: -92px;
            height: 180px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.14);
        }

        .home-mobile-card__content {
            position: relative;
            z-index: 1;
            padding: 22px 22px 34px;
        }

        .home-greeting {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            margin-bottom: 18px;
        }

        .home-greeting__left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .home-avatar {
            width: 46px;
            height: 46px;
            border-radius: 999px;
            display: grid;
            place-items: center;
            background: rgba(255, 255, 255, 0.28);
            color: #fff;
            font-size: 1rem;
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.14);
        }

        .home-greeting__hello {
            font-size: 0.92rem;
            opacity: 0.92;
        }

        .home-greeting__name {
            font-weight: 800;
            font-size: 1rem;
        }

        .home-quick-badge {
            width: 42px;
            height: 42px;
            border-radius: 14px;
            display: grid;
            place-items: center;
            background: rgba(255, 255, 255, 0.18);
            color: #fff;
        }

        .home-time-card {
            background: #fff;
            color: #1e293b;
            border-radius: 28px;
            padding: 22px 18px 18px;
            box-shadow: 0 16px 34px rgba(30, 41, 59, 0.12);
            text-align: center;
        }

        .home-time-card__time {
            font-size: clamp(2rem, 8vw, 2.45rem);
            font-weight: 800;
            color: #4f6be5;
            line-height: 1;
        }

        .home-time-card__date {
            margin-top: 8px;
            color: #94a3b8;
            font-size: 0.82rem;
        }

        .home-time-card__divider {
            height: 1px;
            margin: 16px 0 14px;
            background: #e2e8f0;
        }

        .home-time-card__label {
            color: #64748b;
            font-size: 0.78rem;
        }

        .home-time-card__schedule {
            margin-top: 8px;
            font-size: 1.24rem;
            font-weight: 800;
            color: #334155;
        }

        .home-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .home-action {
            display: block;
            padding: 18px 16px;
            border-radius: 24px;
            background: rgba(255, 255, 255, 0.94);
            color: #1e293b;
            text-decoration: none;
            box-shadow: 0 18px 38px rgba(148, 163, 184, 0.18);
            min-height: 142px;
        }

        .home-action:hover {
            color: #1e293b;
            text-decoration: none;
            transform: translateY(-1px);
        }

        .home-action__icon {
            width: 56px;
            height: 56px;
            border-radius: 18px;
            display: grid;
            place-items: center;
            font-size: 1.2rem;
            margin-bottom: 14px;
        }

        .home-action__title {
            font-size: 1rem;
            font-weight: 800;
            color: #0f172a;
        }

        .home-action__desc {
            margin-top: 8px;
            color: #64748b;
            font-size: 0.86rem;
            line-height: 1.5;
        }

        .home-action--dataset .home-action__icon {
            background: rgba(245, 158, 11, 0.14);
            color: #d97706;
        }

        .home-action--absen .home-action__icon {
            background: rgba(37, 99, 235, 0.12);
            color: #2563eb;
        }

        .home-action--riwayat .home-action__icon {
            background: rgba(16, 185, 129, 0.14);
            color: #059669;
        }

        .home-action--status .home-action__icon {
            background: rgba(99, 102, 241, 0.12);
            color: #4f46e5;
        }

        .home-action--highlight {
            box-shadow: 0 20px 42px rgba(245, 158, 11, 0.2);
            background: linear-gradient(180deg, #fff9eb 0%, #ffffff 100%);
        }

        .home-action__badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-top: 10px;
            padding: 7px 10px;
            border-radius: 999px;
            font-size: 0.72rem;
            font-weight: 800;
        }

        .home-action__badge--warning {
            background: rgba(245, 158, 11, 0.14);
            color: #b45309;
        }

        .home-action__badge--success {
            background: rgba(34, 197, 94, 0.14);
            color: #15803d;
        }

        .home-summary {
            display: grid;
            gap: 14px;
        }

        .home-summary-card {
            border-radius: 24px;
            background: rgba(255, 255, 255, 0.94);
            box-shadow: 0 18px 38px rgba(148, 163, 184, 0.16);
            padding: 20px;
        }

        .home-summary-card__label {
            color: #94a3b8;
            font-size: 0.76rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .home-summary-card__title {
            margin: 0 0 8px;
            font-size: 1.18rem;
            font-weight: 800;
            color: #0f172a;
        }

        .home-summary-card__text {
            color: #64748b;
            margin: 0;
            line-height: 1.6;
        }

        .today-status-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
            margin-top: 16px;
        }

        .today-status-item {
            padding: 14px 14px 12px;
            border-radius: 18px;
            background: #f8fbff;
            box-shadow: inset 0 0 0 1px rgba(226, 232, 240, 0.9);
        }

        .today-status-item__label {
            color: #94a3b8;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .today-status-item__value {
            margin-top: 8px;
            color: #0f172a;
            font-size: 1.08rem;
            font-weight: 800;
        }

        .home-history {
            display: grid;
            gap: 12px;
            margin-top: 16px;
        }

        .home-history-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 14px 16px;
            border-radius: 18px;
            background: #f8fbff;
            box-shadow: inset 0 0 0 1px rgba(226, 232, 240, 0.88);
        }

        .home-history-item__date {
            font-weight: 700;
            color: #0f172a;
        }

        .home-history-item__meta {
            margin-top: 4px;
            font-size: 0.84rem;
            color: #64748b;
        }

        .home-history-badge {
            padding: 8px 11px;
            border-radius: 999px;
            font-size: 0.74rem;
            font-weight: 800;
            white-space: nowrap;
        }

        .home-history-badge--success {
            background: rgba(34, 197, 94, 0.14);
            color: #15803d;
        }

        .home-history-badge--warning {
            background: rgba(245, 158, 11, 0.14);
            color: #b45309;
        }

        .home-history-badge--neutral {
            background: rgba(148, 163, 184, 0.18);
            color: #475569;
        }

        .home-empty {
            padding: 22px 18px;
            border-radius: 18px;
            background: #f8fbff;
            color: #64748b;
            text-align: center;
            box-shadow: inset 0 0 0 1px rgba(226, 232, 240, 0.88);
        }

        @media (min-width: 992px) {
            .mobile-only { display: none; }
            .desktop-only { display: block; }

            .home-shell {
                grid-template-columns: minmax(0, 0.95fr) minmax(340px, 0.9fr);
                align-items: start;
            }
        }

        @media (max-width: 1199.98px) {
            .home-stats-row {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 575.98px) {
            .home-mobile-card__content,
            .home-summary-card {
                padding: 18px;
            }

            .home-grid,
            .today-status-grid {
                grid-template-columns: 1fr 1fr;
            }

            .home-action {
                min-height: 128px;
                padding: 16px 14px;
            }
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

        <div class="home-summary">
            @if($holidayMessage)
                <div class="alert alert-warning mb-0">
                    {{ $holidayMessage }}. Absensi hari ini tidak tersedia.
                </div>
            @endif

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

                    <a href="{{ route('pegawai.absensi') }}" class="home-action home-action--absen">
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
