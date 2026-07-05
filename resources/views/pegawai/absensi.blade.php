@extends('layouts.pegawai')

@push('styles')
    <style>
        .attendance-shell { display: grid; gap: 18px; }
        .attendance-hero { position: relative; overflow: hidden; border-radius: 8px; background: #101827; color: #f8fafc; box-shadow: 0 22px 52px rgba(15, 23, 42, 0.18); }
        .attendance-hero::before { content: ''; position: absolute; inset: 0; background: linear-gradient(115deg, rgba(14, 165, 233, 0.28), transparent 36%), linear-gradient(150deg, transparent 45%, rgba(20, 184, 166, 0.24)); pointer-events: none; }
        .attendance-hero__content { position: relative; display: flex; align-items: center; justify-content: space-between; gap: 20px; padding: 22px; }
        .attendance-hero__copy { display: grid; gap: 8px; }
        .attendance-label { display: inline-flex; align-items: center; width: max-content; padding: 6px 10px; border-radius: 999px; background: rgba(255, 255, 255, 0.12); color: #bae6fd; font-size: 0.72rem; font-weight: 800; letter-spacing: 0.08em; text-transform: uppercase; }
        .attendance-title { margin: 0; font-size: clamp(1.35rem, 2.4vw, 2rem); font-weight: 850; line-height: 1.08; color: #fff; }
        .attendance-subtitle { margin: 0; max-width: 720px; color: #cbd5e1; font-size: 0.96rem; line-height: 1.62; }
        .attendance-actions { display: grid; grid-template-columns: repeat(2, minmax(140px, 1fr)); gap: 10px; min-width: min(100%, 340px); padding: 6px; border-radius: 8px; background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.14); }
        .mode-button { border: 0; border-radius: 6px; padding: 13px 16px; font-weight: 850; font-size: 0.95rem; letter-spacing: 0.01em; transition: transform 0.18s ease, box-shadow 0.18s ease, opacity 0.18s ease, filter 0.18s ease; }
        .mode-button:hover { transform: translateY(-1px); filter: brightness(1.03); }
        .mode-button:disabled { opacity: 0.72; cursor: not-allowed; transform: none; filter: none; }
        .mode-button--masuk { background: #17b26a; box-shadow: 0 14px 28px rgba(23, 178, 106, 0.24); color: #fff; }
        .mode-button--keluar { background: #f04438; box-shadow: 0 14px 28px rgba(240, 68, 56, 0.24); color: #fff; }
        .verification-panel { position: relative; display: grid; gap: 18px; padding: 18px; border-radius: 8px; background: rgba(255, 255, 255, 0.92); border: 1px solid rgba(203, 213, 225, 0.86); box-shadow: 0 24px 60px rgba(15, 23, 42, 0.14); overflow: hidden; }
        .verification-panel::before { content: ''; position: absolute; left: 0; top: 0; right: 0; height: 4px; background: linear-gradient(90deg, #0ea5e9, #14b8a6, #22c55e); }
        .verification-panel__top { display: flex; align-items: center; justify-content: space-between; gap: 16px; }
        .verification-panel__title { margin: 0; color: #0f172a; font-size: 0.86rem; font-weight: 850; letter-spacing: 0.08em; text-transform: uppercase; }
        .verification-panel__actions { display: flex; align-items: center; gap: 10px; }
        .info-trigger { width: 40px; height: 40px; border: 0; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center; background: #e0f2fe; color: #0369a1; box-shadow: inset 0 0 0 1px rgba(14, 165, 233, 0.16); transition: transform 0.18s ease, background 0.18s ease; }
        .info-trigger:hover { transform: translateY(-1px); background: #bae6fd; }
        .modal-close-trigger { width: 40px; height: 40px; border: 0; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center; background: #f1f5f9; color: #475569; transition: transform 0.18s ease, background 0.18s ease; }
        .modal-close-trigger:hover { transform: translateY(-1px); background: #e2e8f0; }
        .attendance-modal .modal-dialog { max-width: min(1120px, calc(100vw - 28px)); margin: 18px auto; }
        .attendance-modal { z-index: 2080 !important; }
        .modal-backdrop { z-index: 2070 !important; }
        .attendance-modal .modal-content { border: 0; border-radius: 8px; background: transparent; box-shadow: none; }
        .attendance-modal .modal-body { padding: 0; }
        .verification-camera { position: relative; aspect-ratio: 4 / 5; max-width: 420px; width: 100%; margin: 0 auto; border-radius: 8px; overflow: hidden; background: #020617; box-shadow: 0 24px 48px rgba(15, 23, 42, 0.28); }
        .verification-camera::before { content: ''; position: absolute; inset: 0; background: linear-gradient(180deg, rgba(15, 23, 42, 0.02) 0%, rgba(15, 23, 42, 0.32) 100%); pointer-events: none; z-index: 1; }
        .verification-camera::after { content: ''; position: absolute; inset: 12px; border-radius: 6px; border: 1px solid rgba(255, 255, 255, 0.12); pointer-events: none; z-index: 2; }
        .verification-video, .verification-overlay { position: absolute; inset: 0; width: 100%; height: 100%; }
        .verification-video { object-fit: cover; display: block; transform: scaleX(-1); }
        .verification-overlay { pointer-events: none; z-index: 3; }
        .face-guide-wrap { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; z-index: 2; pointer-events: none; }
        .face-guide-ring { position: relative; width: min(72%, 286px); aspect-ratio: 0.82; border-radius: 46%; box-shadow: 0 0 0 999px rgba(2, 6, 23, 0.30), inset 0 0 0 1px rgba(255, 255, 255, 0.24); overflow: hidden; }
        .face-guide-ring::before { content: ''; position: absolute; inset: -14px; border-radius: 48%; background: conic-gradient(from -90deg, #38bdf8 0deg, #14b8a6 var(--progress-angle, 0deg), rgba(255,255,255,0.16) var(--progress-angle, 0deg), rgba(255,255,255,0.08) 360deg); -webkit-mask: radial-gradient(farthest-side, transparent calc(100% - 15px), #000 calc(100% - 14px)); mask: radial-gradient(farthest-side, transparent calc(100% - 15px), #000 calc(100% - 14px)); }
        .face-guide-ring::after { content: ''; position: absolute; inset: 18px; border-radius: 44%; border: 1px dashed rgba(255, 255, 255, 0.42); }
        .face-guide-pulse { position: absolute; inset: 10px; border-radius: 46%; border: 1px solid rgba(56, 189, 248, 0.58); animation: pulse-ring 2.2s ease-in-out infinite; opacity: 0.9; }
        .face-guide-label { position: absolute; left: 50%; bottom: 20px; transform: translateX(-50%); z-index: 4; padding: 9px 15px; border-radius: 6px; background: rgba(15, 23, 42, 0.72); color: #f8fafc; box-shadow: 0 10px 24px rgba(2, 6, 23, 0.22); font-size: 0.76rem; font-weight: 850; letter-spacing: 0.08em; text-transform: uppercase; }
        .verification-meta { display: grid; gap: 14px; }
        .verification-result { padding: 18px; text-align: left; border-radius: 8px; background: #f8fafc; border: 1px solid #e2e8f0; }
        .verification-result h5 { margin-bottom: 8px; font-size: clamp(1.26rem, 2vw, 1.7rem); font-weight: 850; line-height: 1.15; color: #0f172a; }
        .verification-result p { margin: 0; color: #64748b; font-size: 0.96rem; line-height: 1.65; }
        .status-band { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 14px; border-radius: 8px; background: #fff; border: 1px solid #e2e8f0; }
        .status-band__label { display: block; color: #94a3b8; font-size: 0.7rem; font-weight: 800; letter-spacing: 0.08em; text-transform: uppercase; }
        .status-band__value { margin-top: 4px; color: #0f172a; font-weight: 850; }
        .status-pill { display: inline-flex; align-items: center; padding: 8px 10px; border-radius: 6px; font-size: 0.8rem; font-weight: 850; white-space: nowrap; }
        .status-pill--idle { background: #f1f5f9; color: #475569; }
        .status-pill--scan { background: #dbeafe; color: #1d4ed8; }
        .status-pill--success { background: #dcfce7; color: #15803d; }
        .status-pill--error { background: #fee2e2; color: #b91c1c; }
        .step-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; }
        .step-card { position: relative; padding: 14px; border-radius: 8px; background: #fff; border: 1px solid #e2e8f0; transition: transform 0.18s ease, border-color 0.18s ease, background 0.18s ease; }
        .step-card.is-active { background: #eff6ff; border-color: #93c5fd; transform: translateY(-1px); }
        .step-card.is-complete { background: #ecfdf3; border-color: #86efac; }
        .step-card__number { width: 30px; height: 30px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center; background: #f1f5f9; color: #475569; font-weight: 850; }
        .step-card.is-active .step-card__number { background: #2563eb; color: #fff; }
        .step-card.is-complete .step-card__number { background: #16a34a; color: #fff; }
        .step-card__title { margin-top: 12px; margin-bottom: 4px; font-size: 0.9rem; font-weight: 850; color: #0f172a; }
        .step-card__caption { color: #64748b; font-size: 0.78rem; line-height: 1.45; }
        @keyframes pulse-ring { 0%,100% { transform: scale(0.985); opacity: 0.8; } 50% { transform: scale(1.02); opacity: 1; } }
        @media (max-width: 991.98px) { .step-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } .attendance-hero__content { flex-direction: column; align-items: stretch; } }
        @media (max-width: 575.98px) { .attendance-hero__content, .verification-panel { padding: 14px; } .attendance-actions { grid-template-columns: 1fr; } .step-grid { grid-template-columns: 1fr; } .status-band { align-items: flex-start; flex-direction: column; } .attendance-modal .modal-dialog { max-width: calc(100vw - 16px); margin: 8px auto; } }
    </style>
@endpush

@section('content')
    <div class="section-header">
        <h1>Absensi Wajah</h1>
    </div>

    <div class="attendance-shell">
        @if($datasetCount < $minDataset)
            <div class="alert alert-warning mb-0">
                Dataset wajah Anda belum lengkap. Silakan daftar dan lengkapi dataset terlebih dahulu sebelum melakukan absensi.
            </div>
        @endif

        <div class="attendance-hero">
            <div class="attendance-hero__content">
                <div class="attendance-hero__copy">
                    <div class="attendance-label">Face Verify Flow</div>
                    <h2 class="attendance-title">Verifikasi absensi yang terasa seperti aplikasi mobile.</h2>
                    <p class="attendance-subtitle">Posisikan wajah di dalam frame, ikuti instruksi gesture, lalu sistem akan menyimpan bukti absensi secara otomatis setelah proses verifikasi selesai.</p>
                </div>
                <div class="attendance-actions">
                    <button id="btnMasuk" type="button" class="mode-button mode-button--masuk" onclick="pilihMode('masuk')">Absen Masuk</button>
                    <button id="btnKeluar" type="button" class="mode-button mode-button--keluar" onclick="pilihMode('keluar')">Absen Keluar</button>
                </div>
            </div>
        </div>

        <div class="modal fade attendance-modal" id="attendanceCameraModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="false">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="verification-panel">
                            <div class="verification-panel__top">
                                <h3 class="verification-panel__title">Live Verification</h3>
                                <div class="verification-panel__actions">
                                    <button type="button" class="info-trigger" id="infoTrigger" aria-label="Lihat panduan absensi">
                                        <i class="fas fa-info"></i>
                                    </button>
                                    <button type="button" class="modal-close-trigger" data-dismiss="modal" aria-label="Tutup kamera">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="step-grid">
                                <div class="step-card is-active" data-step-card="0"><div class="step-card__number">1</div><div class="step-card__title">Wajah Dikenali</div><div class="step-card__caption">Pastikan wajah terbaca jelas oleh kamera.</div></div>
                                <div class="step-card" data-step-card="1"><div class="step-card__number">2</div><div class="step-card__title">Hadap Kiri</div><div class="step-card__caption">Ikuti arahan dan miringkan wajah ke kiri.</div></div>
                                <div class="step-card" data-step-card="2"><div class="step-card__number">3</div><div class="step-card__title">Hadap Kanan</div><div class="step-card__caption">Kembalikan posisi dan lanjutkan ke kanan.</div></div>
                                <div class="step-card" data-step-card="3"><div class="step-card__number">4</div><div class="step-card__title">Buka Mulut</div><div class="step-card__caption">Selesaikan gesture akhir untuk konfirmasi.</div></div>
                            </div>

                            <div class="row align-items-center">
                                <div class="col-lg-7 text-center text-lg-left mb-4 mb-lg-0">
                                    <div class="verification-camera">
                                        <video id="video" autoplay muted playsinline class="verification-video"></video>
                                        <canvas id="overlay" class="verification-overlay"></canvas>
                                        <div class="face-guide-wrap"><div class="face-guide-ring" id="faceGuideRing" style="--progress-angle: 0deg;"><div class="face-guide-pulse"></div></div></div>
                                        <div class="face-guide-label" id="cameraGuideLabel">Scan Wajah</div>
                                    </div>
                                </div>
                                <div class="col-lg-5">
                                    <div class="verification-meta">
                                        <div class="verification-result">
                                            <h5 id="result">Silakan pilih mode absensi</h5>
                                            <p id="gestureInfo">Pilih absen masuk atau keluar terlebih dahulu</p>
                                        </div>
                                        <div class="status-band">
                                            <div><span class="status-band__label">Status Verifikasi</span><div class="status-band__value" id="modeBadge">Menunggu mode absensi</div></div>
                                            <span class="status-pill status-pill--idle" id="scanStatusPill">Idle</span>
                                        </div>
                                        <div class="status-band">
                                            <div><span class="status-band__label">Progress</span><div class="status-band__value"><span id="progressPercent">0</span>% selesai</div></div>
                                            <span class="status-pill status-pill--scan" id="gestureBadge">Mulai verifikasi</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/face-api.min.js') }}"></script>
    <script>
        const video = document.getElementById('video');
        const overlay = document.getElementById('overlay');
        const resultText = document.getElementById('result');
        const gestureText = document.getElementById('gestureInfo');
        const faceGuideRing = document.getElementById('faceGuideRing');
        const scanStatusPill = document.getElementById('scanStatusPill');
        const gestureBadge = document.getElementById('gestureBadge');
        const progressPercent = document.getElementById('progressPercent');
        const modeBadge = document.getElementById('modeBadge');
        const cameraGuideLabel = document.getElementById('cameraGuideLabel');
        const infoTrigger = document.getElementById('infoTrigger');
        const attendanceCameraModal = document.getElementById('attendanceCameraModal');
        const stepCards = Array.from(document.querySelectorAll('[data-step-card]'));
        const modeButtons = [document.getElementById('btnMasuk'), document.getElementById('btnKeluar')];

        let detectionInterval = null;
        let absenDone = false;
        let gestureStep = 0;
        let absensiMode = null;
        let isStarting = false;
        let pendingReasonPayload = {};
        let pendingAttendancePhoto = null;
        let isCameraModalVisible = false;
        const faceMatchThreshold = 0.55;
        const jadwalMasuk = @json(optional($jadwalPegawai)->jam_masuk);
        const jadwalPulang = @json(optional($jadwalPegawai)->jam_pulang);
        const toleransiTelat = {{ (int) optional($jadwalPegawai)->toleransi_telat }};
        const gestures = ['Hadap Kiri', 'Hadap Kanan', 'Buka Mulut'];

        function setButtonsDisabled(disabled) { modeButtons.forEach((button) => { button.disabled = disabled; }); }
        function setStatusPill(type, text) { scanStatusPill.className = `status-pill status-pill--${type}`; scanStatusPill.innerText = text; }
        function showAttendanceCameraModal() {
            isCameraModalVisible = true;
            if (window.jQuery && attendanceCameraModal) {
                $('#attendanceCameraModal').modal({ backdrop: false, keyboard: true, show: true });
            }
        }
        function hideAttendanceCameraModal() {
            isCameraModalVisible = false;
            if (window.jQuery && attendanceCameraModal) {
                $('#attendanceCameraModal').modal('hide');
            }
        }
        function updateProgressUI() {
            const completedCount = absenDone ? 4 : Math.min(gestureStep + 1, 3);
            const progress = Math.round((completedCount / 4) * 100);
            progressPercent.innerText = progress;
            faceGuideRing.style.setProperty('--progress-angle', `${(progress / 100) * 360}deg`);
            stepCards.forEach((card, index) => {
                const isComplete = absenDone ? index <= 3 : index < completedCount;
                const isActive = !absenDone && index === completedCount;
                card.classList.toggle('is-complete', isComplete);
                card.classList.toggle('is-active', isActive);
            });
        }
        function resetVerificationUI() {
            absenDone = false;
            gestureStep = 0;
            pendingAttendancePhoto = null;
            cameraGuideLabel.innerText = 'Scan Wajah';
            gestureBadge.innerText = 'Mulai verifikasi';
            modeBadge.innerText = absensiMode ? `Mode ${absensiMode === 'masuk' ? 'Masuk' : 'Keluar'} dipilih` : 'Menunggu mode absensi';
            setStatusPill('idle', 'Idle');
            updateProgressUI();
        }

        async function pilihMode(mode) {
            if (isStarting) return;
            if ({{ $datasetCount }} < {{ $minDataset }}) {
                resultText.innerText = 'Dataset wajah belum lengkap';
                gestureText.innerText = 'Silakan lengkapi dataset terlebih dahulu di menu Dataset Wajah';
                modeBadge.innerText = 'Dataset belum siap';
                setStatusPill('error', 'Blokir');
                showErrorAlert('Dataset wajah belum lengkap. Silakan daftar dataset terlebih dahulu.');
                return;
            }
            absensiMode = mode;
            resetVerificationUI();
            try { pendingReasonPayload = await askAttendanceReasonIfNeeded(); }
            catch (error) {
                resultText.innerText = 'Absensi dibatalkan';
                gestureText.innerText = error.message;
                modeBadge.innerText = 'Absensi dibatalkan';
                setStatusPill('error', 'Batal');
                showErrorAlert(error.message);
                return;
            }
            showAttendanceCameraModal();
            resultText.innerText = `Mode ${mode === 'masuk' ? 'Absen Masuk' : 'Absen Keluar'} siap`;
            gestureText.innerText = 'Memuat kamera dan model verifikasi...';
            gestureBadge.innerText = 'Persiapan kamera';
            modeBadge.innerText = `Mode ${mode === 'masuk' ? 'Masuk' : 'Keluar'} aktif`;
            cameraGuideLabel.innerText = 'Persiapan';
            setStatusPill('scan', 'Loading');
            start();
        }

        function updateGestureText() {
            gestureText.innerText = `Silakan lakukan gesture: ${gestures[gestureStep]}`;
            gestureBadge.innerText = gestures[gestureStep] || 'Selesai';
            cameraGuideLabel.innerText = gestures[gestureStep] || 'Selesai';
            updateProgressUI();
        }

        function buildScheduleDate(timeString) {
            const now = new Date();
            if (!timeString) return null;
            const [hours, minutes, seconds] = timeString.split(':').map(Number);
            const target = new Date(now);
            target.setHours(hours, minutes, seconds || 0, 0);
            return target;
        }

        async function askAttendanceReasonIfNeeded() {
            const now = new Date();
            if (absensiMode === 'masuk' && jadwalMasuk) {
                const batasMasuk = buildScheduleDate(jadwalMasuk);
                batasMasuk.setMinutes(batasMasuk.getMinutes() + toleransiTelat);
                if (now > batasMasuk) {
                    const { value } = await Swal.fire({ title: 'Anda Terlambat', text: 'Masukkan alasan keterlambatan sebelum absensi disimpan.', input: 'textarea', inputPlaceholder: 'Tulis alasan keterlambatan...', inputAttributes: { 'aria-label': 'Alasan keterlambatan' }, showCancelButton: true, confirmButtonText: 'Simpan Alasan', cancelButtonText: 'Batal', inputValidator: (value) => { if (!value) return 'Alasan keterlambatan wajib diisi'; } });
                    if (!value) throw new Error('Absensi dibatalkan karena alasan belum diisi');
                    return { alasan_telat: value || '' };
                }
            }
            if (absensiMode === 'keluar' && jadwalMasuk && jadwalPulang) {
                const jadwalMasukDate = buildScheduleDate(jadwalMasuk);
                const jadwalPulangDate = buildScheduleDate(jadwalPulang);
                if (jadwalPulangDate <= jadwalMasukDate) jadwalPulangDate.setDate(jadwalPulangDate.getDate() + 1);
                if (now < jadwalPulangDate) {
                    const { value } = await Swal.fire({ title: 'Pulang Lebih Awal', text: 'Masukkan alasan pulang awal sebelum absensi disimpan.', input: 'textarea', inputPlaceholder: 'Tulis alasan pulang awal...', inputAttributes: { 'aria-label': 'Alasan pulang awal' }, showCancelButton: true, confirmButtonText: 'Simpan Alasan', cancelButtonText: 'Batal', inputValidator: (value) => { if (!value) return 'Alasan pulang awal wajib diisi'; } });
                    if (!value) throw new Error('Absensi dibatalkan karena alasan belum diisi');
                    return { alasan_pulang_awal: value || '' };
                }
            }
            return {};
        }

        if (infoTrigger) {
            infoTrigger.addEventListener('click', () => {
                Swal.fire({
                    title: 'Panduan Absensi',
                    icon: 'info',
                    confirmButtonText: 'Tutup',
                    html: `
                        <div style="text-align:left; display:grid; gap:10px;">
                            <div>1. Dekatkan wajah ke area oval dan hindari backlight kuat dari belakang.</div>
                            <div>2. Ikuti gesture satu per satu. Setelah kanan, kembalikan wajah ke tengah sebelum buka mulut.</div>
                            <div>3. Jaga kamera tetap stabil agar confidence wajah tidak turun saat proses berjalan.</div>
                            <div>4. Foto bukti akan diambil saat wajah kembali netral, bukan saat mulut terbuka.</div>
                        </div>
                    `
                });
            });
        }

        if (window.jQuery && attendanceCameraModal) {
            $('#attendanceCameraModal').on('hidden.bs.modal', () => {
                isCameraModalVisible = false;
                stopCamera();
                setButtonsDisabled(false);
            });
        }

        function getFriendlyCameraError(error) {
            if (!window.isSecureContext) return 'Kamera HP butuh HTTPS atau localhost. Jika dibuka dari IP jaringan, aktifkan HTTPS.';
            if (error.name === 'NotAllowedError') return 'Izin kamera ditolak. Izinkan akses kamera di browser HP Anda.';
            if (error.name === 'NotFoundError') return 'Kamera depan tidak ditemukan di perangkat ini.';
            if (error.name === 'NotReadableError') return 'Kamera sedang dipakai aplikasi lain. Tutup aplikasi kamera lalu coba lagi.';
            return error.message || 'Periksa dataset wajah atau izin kamera Anda';
        }

        function stopCamera() {
            if (video.srcObject) {
                video.srcObject.getTracks().forEach((track) => { track.stop(); });
                video.srcObject = null;
            }
            if (detectionInterval) { clearInterval(detectionInterval); detectionInterval = null; }
        }

        function showSuccessAlert(message) {
            Swal.fire({ icon: 'success', title: 'Absensi Berhasil', text: message || `Absensi ${absensiMode} berhasil`, confirmButtonText: 'OK' }).then(() => {
                window.location.href = "{{ route('pegawai.dashboard') }}";
            });
        }

        function showErrorAlert(message) {
            Swal.fire({ icon: 'error', title: 'Absensi Gagal', text: message, confirmButtonText: 'OK' });
        }

        function getCenterPoint(points) {
            const total = points.reduce((accumulator, point) => {
                accumulator.x += point.x;
                accumulator.y += point.y;
                return accumulator;
            }, { x: 0, y: 0 });
            return { x: total.x / points.length, y: total.y / points.length };
        }

        function detectHeadDirection(landmarks) {
            const nose = landmarks.getNose();
            const leftEyeCenter = getCenterPoint(landmarks.getLeftEye());
            const rightEyeCenter = getCenterPoint(landmarks.getRightEye());
            const eyeCenterX = (leftEyeCenter.x + rightEyeCenter.x) / 2;
            const noseTipX = nose[3].x;
            const diff = noseTipX - eyeCenterX;
            if (diff > 12) return 'kiri';
            if (diff < -12) return 'kanan';
            return 'tengah';
        }

        function mouthOpen(landmarks) {
            const mouth = landmarks.getMouth();
            return mouth[19].y - mouth[13].y > 15;
        }

        function captureAttendancePhoto() {
            const captureCanvas = document.createElement('canvas');
            const width = video.videoWidth || 420;
            const height = video.videoHeight || 520;
            captureCanvas.width = width;
            captureCanvas.height = height;
            captureCanvas.getContext('2d').drawImage(video, 0, 0, width, height);
            return captureCanvas.toDataURL('image/jpeg', 0.85);
        }

        function captureNeutralAttendancePhoto(landmarks) {
            if (pendingAttendancePhoto || mouthOpen(landmarks) || detectHeadDirection(landmarks) !== 'tengah') return;
            pendingAttendancePhoto = captureAttendancePhoto();
            cameraGuideLabel.innerText = 'Foto Siap';
        }

        function getMatchSimilarity(match) {
            const normalizedDistance = Math.max(0, Math.min(1.5, match.distance / faceMatchThreshold));
            const calibratedScore = 100 - (normalizedDistance * 20);

            return Math.max(0, Math.min(100, Math.round(calibratedScore)));
        }
        function isMatchValid(match) { return match.label !== 'unknown' && match.distance <= faceMatchThreshold; }
        function formatMatchLabel(match) { return match.label === 'unknown' ? 'Unknown' : `${match.label} (${getMatchSimilarity(match)}%)`; }
        function mirrorBox(box, displayWidth) {
            return {
                x: displayWidth - box.x - box.width,
                y: box.y,
                width: box.width,
                height: box.height
            };
        }

        function beginFaceDetection(faceMatcher) {
            const displaySize = { width: video.clientWidth || video.offsetWidth || 420, height: video.clientHeight || video.offsetHeight || 520 };
            overlay.width = displaySize.width;
            overlay.height = displaySize.height;
            faceapi.matchDimensions(overlay, displaySize);
            setStatusPill('scan', 'Scanning');
            gestureBadge.innerText = 'Kenali wajah';
            cameraGuideLabel.innerText = 'Scan Wajah';
            updateProgressUI();
            if (detectionInterval) clearInterval(detectionInterval);

            detectionInterval = setInterval(async () => {
                const detections = await faceapi.detectAllFaces(video, new faceapi.TinyFaceDetectorOptions()).withFaceLandmarks().withFaceDescriptors();
                const resizedDetections = faceapi.resizeResults(detections, displaySize);
                const context = overlay.getContext('2d');
                context.clearRect(0, 0, overlay.width, overlay.height);

                if (!resizedDetections.length) {
                    resultText.innerText = 'Posisikan wajah di dalam frame';
                    gestureText.innerText = 'Pastikan wajah masuk penuh ke dalam area oval';
                    cameraGuideLabel.innerText = 'Cari Wajah';
                    setStatusPill('scan', 'Mencari');
                    return;
                }

                resizedDetections.forEach((detection) => {
                    const match = faceMatcher.findBestMatch(detection.descriptor);
                    const matchIsValid = isMatchValid(match);
                    const label = matchIsValid ? formatMatchLabel(match) : `Unknown (${getMatchSimilarity(match)}%)`;
                    new faceapi.draw.DrawBox(mirrorBox(detection.detection.box, displaySize.width), { label }).draw(overlay);
                    resultText.innerText = `Terdeteksi: ${label}`;
                    const landmarks = detection.landmarks;

                    if (!matchIsValid) {
                        gestureStep = 0;
                        pendingAttendancePhoto = null;
                        cameraGuideLabel.innerText = 'Wajah Tidak Valid';
                        gestureBadge.innerText = 'Wajah belum cocok';
                        gestureText.innerText = 'Wajah tidak dikenali. Dekatkan wajah, cari cahaya yang cukup, atau ulangi perekaman dataset.';
                        setStatusPill('error', 'Unknown');
                        updateProgressUI();
                        return;
                    }

                    setStatusPill('success', 'Wajah Terkunci');
                    if (gestureStep === 0) {
                        cameraGuideLabel.innerText = 'Hadap Kiri';
                        gestureBadge.innerText = 'Langkah 1';
                        if (detectHeadDirection(landmarks) === 'kiri') { gestureStep++; updateGestureText(); }
                    } else if (gestureStep === 1) {
                        cameraGuideLabel.innerText = 'Hadap Kanan';
                        gestureBadge.innerText = 'Langkah 2';
                        if (detectHeadDirection(landmarks) === 'kanan') { gestureStep++; updateGestureText(); }
                    } else if (gestureStep === 2) {
                        cameraGuideLabel.innerText = pendingAttendancePhoto ? 'Buka Mulut' : 'Netral Dulu';
                        gestureBadge.innerText = pendingAttendancePhoto ? 'Langkah 3' : 'Ambil Foto';
                        captureNeutralAttendancePhoto(landmarks);
                        if (mouthOpen(landmarks) && !absenDone) {
                            absenDone = true;
                            const fotoBukti = pendingAttendancePhoto || captureAttendancePhoto();
                            gestureBadge.innerText = 'Menyimpan';
                            cameraGuideLabel.innerText = 'Verifikasi';
                            updateProgressUI();
                            stopCamera();
                            absenPegawai({ ...pendingReasonPayload, foto_bukti: fotoBukti })
                                .then((message) => {
                                    resultText.innerText = 'Absensi Berhasil';
                                    gestureText.innerText = 'Data absensi berhasil disimpan';
                                    cameraGuideLabel.innerText = 'Selesai';
                                    gestureBadge.innerText = 'Sukses';
                                    setStatusPill('success', 'Selesai');
                                    showSuccessAlert(message);
                                })
                                .catch((error) => {
                                    absenDone = false;
                                    gestureStep = 0;
                                    pendingAttendancePhoto = null;
                                    resultText.innerText = 'Absensi gagal disimpan';
                                    gestureText.innerText = error.message;
                                    cameraGuideLabel.innerText = 'Ulangi';
                                    gestureBadge.innerText = 'Gagal';
                                    setStatusPill('error', 'Gagal');
                                    updateProgressUI();
                                    showErrorAlert(error.message);
                                });
                        }
                    }
                });
            }, 250);
        }

        async function loadModels() {
            await faceapi.nets.tinyFaceDetector.loadFromUri('/models/tiny_face_detector');
            await faceapi.nets.faceLandmark68Net.loadFromUri('/models/face_landmark_68');
            await faceapi.nets.faceRecognitionNet.loadFromUri('/models/face_recognition');
        }

        async function startCamera() {
            const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' }, audio: false });
            video.srcObject = stream;
            video.setAttribute('playsinline', 'true');
            video.muted = true;
            try { await video.play(); } catch (error) { console.log(error); }
        }

        async function loadDataset() {
            const response = await fetch('/pegawai/dataset/load');
            const data = await response.json();
            const groupedDescriptors = {};
            data.forEach((item) => {
                if (!groupedDescriptors[item.label]) groupedDescriptors[item.label] = [];
                groupedDescriptors[item.label].push(new Float32Array(item.descriptor));
            });
            return Object.keys(groupedDescriptors).map((label) => new faceapi.LabeledFaceDescriptors(label, groupedDescriptors[label]));
        }

        async function absenPegawai(reasonPayload = {}) {
            const response = await fetch("{{ route('pegawai.absensi.store') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
                body: JSON.stringify({ mode: absensiMode, ...reasonPayload })
            });
            const contentType = response.headers.get('content-type') || '';
            if (!contentType.includes('application/json')) {
                const errorText = await response.text();
                throw new Error(errorText.includes('DOCTYPE') ? 'Server mengembalikan halaman error. Cek migration database dan session login.' : errorText);
            }
            const result = await response.json();
            if (!result.status) throw new Error(result.message || 'Absensi gagal disimpan');
            return result.message || `Absensi ${absensiMode} berhasil`;
        }

        async function start() {
            if (!absensiMode) return;
            isStarting = true;
            stopCamera();
            pendingAttendancePhoto = null;
            setButtonsDisabled(true);
            updateProgressUI();

            try {
                resultText.innerText = 'Memuat model verifikasi...';
                gestureText.innerText = 'Sistem sedang menyiapkan engine pengenalan wajah';
                await loadModels();
                if (!isCameraModalVisible) return;
                resultText.innerText = 'Mengaktifkan kamera...';
                gestureText.innerText = 'Izinkan akses kamera jika browser memintanya';
                await startCamera();
                if (!isCameraModalVisible) { stopCamera(); return; }
                resultText.innerText = 'Memuat dataset wajah...';
                gestureText.innerText = 'Menyiapkan identitas wajah yang sudah terdaftar';
                const labeledDescriptors = await loadDataset();
                if (!isCameraModalVisible) { stopCamera(); return; }
                if (!labeledDescriptors.length) {
                    stopCamera();
                    resultText.innerText = 'Dataset wajah belum tersedia';
                    gestureText.innerText = 'Silakan rekam dataset wajah terlebih dahulu';
                    modeBadge.innerText = 'Dataset belum tersedia';
                    cameraGuideLabel.innerText = 'Dataset';
                    setStatusPill('error', 'Dataset');
                    return;
                }
                const faceMatcher = new faceapi.FaceMatcher(labeledDescriptors, faceMatchThreshold);
                if (video.readyState >= 2) { beginFaceDetection(faceMatcher); return; }
                video.addEventListener('loadeddata', () => { beginFaceDetection(faceMatcher); }, { once: true });
            } catch (error) {
                console.error(error);
                stopCamera();
                resultText.innerText = 'Gagal memulai absensi';
                gestureText.innerText = getFriendlyCameraError(error);
                cameraGuideLabel.innerText = 'Kamera Gagal';
                modeBadge.innerText = 'Periksa izin kamera';
                setStatusPill('error', 'Error');
            } finally {
                isStarting = false;
                setButtonsDisabled(false);
            }
        }

        updateProgressUI();
    </script>
@endpush
