@extends('layouts.pegawai')

@push('styles')
    <style>
        .attendance-shell { display: grid; gap: 24px; }
        .attendance-hero { position: relative; overflow: hidden; border-radius: 24px; background: linear-gradient(135deg, var(--pegawai-primary-dark), var(--pegawai-primary), var(--pegawai-accent)); color: #f8fafc; box-shadow: 0 24px 50px rgba(29, 78, 216, 0.25); }
        .attendance-hero::before { content: ''; position: absolute; inset: -50%; background: radial-gradient(circle at 20% 30%, rgba(255, 255, 255, 0.15) 0%, transparent 40%), radial-gradient(circle at 80% 70%, rgba(20, 184, 166, 0.3) 0%, transparent 40%); pointer-events: none; animation: glow-move 10s ease-in-out infinite alternate; }
        .attendance-hero__content { position: relative; display: flex; align-items: center; justify-content: space-between; gap: 30px; padding: 32px 40px; z-index: 2; }
        .attendance-hero__copy { display: grid; gap: 12px; }
        .attendance-label { display: inline-flex; align-items: center; width: max-content; padding: 8px 14px; border-radius: 999px; background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.2); color: #fff; font-size: 0.75rem; font-weight: 800; letter-spacing: 0.1em; text-transform: uppercase; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .attendance-title { margin: 0; font-size: clamp(1.4rem, 2.5vw, 1.85rem); font-weight: 850; line-height: 1.2; color: #fff; text-shadow: 0 2px 10px rgba(0,0,0,0.15); }
        .attendance-subtitle { margin: 0; max-width: 720px; color: rgba(255, 255, 255, 0.85); font-size: 1.05rem; line-height: 1.7; }
        .attendance-actions { display: grid; grid-template-columns: repeat(2, minmax(150px, 1fr)); gap: 16px; min-width: min(100%, 380px); padding: 12px; border-radius: 16px; background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(16px); border: 1px solid rgba(255, 255, 255, 0.2); box-shadow: 0 8px 32px rgba(0,0,0,0.1); }
        .mode-button { border: 0; border-radius: 10px; padding: 16px 20px; font-weight: 850; font-size: 1rem; letter-spacing: 0.02em; transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1); }
        .mode-button:hover { transform: translateY(-3px) scale(1.02); filter: brightness(1.1); box-shadow: 0 12px 24px rgba(0,0,0,0.2); }
        .mode-button:disabled { opacity: 0.6; cursor: not-allowed; transform: none; filter: none; box-shadow: none; }
        .mode-button--masuk { background: #fff; color: var(--pegawai-primary-dark); box-shadow: 0 8px 20px rgba(0,0,0,0.1); }
        .mode-button--keluar { background: rgba(255, 255, 255, 0.2); color: #fff; border: 1px solid rgba(255,255,255,0.4); backdrop-filter: blur(10px); }
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
        .verification-camera::after { content: ''; position: absolute; inset: 12px; border-radius: 6px; border: 1px solid rgba(255, 255, 255, 0.12); pointer-events: none; z-index: 2; }
        .verification-video, .verification-overlay { position: absolute; inset: 0; width: 100%; height: 100%; }
        .verification-video { object-fit: cover; display: block; transform: scaleX(-1); }
        .verification-overlay { pointer-events: none; z-index: 3; }
        .face-guide-wrap { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; z-index: 2; pointer-events: none; }
        .face-guide-ring { position: relative; width: min(65%, 260px); aspect-ratio: 0.85; box-shadow: none; overflow: hidden; background: linear-gradient(to right, var(--pegawai-accent) 4px, transparent 4px) 0 0, linear-gradient(to bottom, var(--pegawai-accent) 4px, transparent 4px) 0 0, linear-gradient(to left, var(--pegawai-accent) 4px, transparent 4px) 100% 0, linear-gradient(to bottom, var(--pegawai-accent) 4px, transparent 4px) 100% 0, linear-gradient(to right, var(--pegawai-accent) 4px, transparent 4px) 0 100%, linear-gradient(to top, var(--pegawai-accent) 4px, transparent 4px) 0 100%, linear-gradient(to left, var(--pegawai-accent) 4px, transparent 4px) 100% 100%, linear-gradient(to top, var(--pegawai-accent) 4px, transparent 4px) 100% 100%; background-repeat: no-repeat; background-size: 35px 35px; }
        .face-guide-pulse { position: absolute; left: 8px; right: 8px; height: 3px; background: var(--pegawai-accent); box-shadow: 0 0 12px 2px var(--pegawai-accent), 0 0 4px #fff; border-radius: 4px; animation: scan-line 2.5s ease-in-out infinite alternate; opacity: 0.8; }
        @keyframes scan-line { 0% { top: 12px; opacity: 0; } 10% { opacity: 1; } 90% { opacity: 1; } 100% { top: calc(100% - 15px); opacity: 0; } }
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
        
        .tutorial-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-top: 24px; margin-bottom: 24px; }
        .tutorial-step { background: #fff; border-radius: 20px; overflow: hidden; border: 1px solid rgba(226, 232, 240, 0.8); box-shadow: 0 12px 30px rgba(15, 23, 42, 0.04); transition: transform 0.25s ease, box-shadow 0.25s ease; }
        .tutorial-step:hover { transform: translateY(-4px); box-shadow: 0 20px 40px rgba(15, 23, 42, 0.08); border-color: rgba(14, 165, 233, 0.3); }
        .tutorial-img-wrap { padding: 24px 24px 0 24px; background: linear-gradient(180deg, #f8fafc 0%, #eff6ff 100%); display: flex; justify-content: center; }
        .tutorial-img { width: 100%; max-width: 170px; aspect-ratio: 4 / 4.1; object-fit: cover; object-position: top; border-radius: 12px 12px 0 0; filter: drop-shadow(0 10px 20px rgba(0,0,0,0.05)); }
        .tutorial-content { padding: 20px; text-align: center; }
        .tutorial-badge { display: inline-flex; align-items: center; justify-content: center; width: 26px; height: 26px; border-radius: 50%; background: var(--pegawai-primary); color: #fff; font-size: 0.8rem; font-weight: 850; margin-right: 8px; }
        .tutorial-step-title { display: flex; align-items: center; justify-content: center; font-size: 1.05rem; font-weight: 800; color: #0f172a; margin-bottom: 8px; letter-spacing: -0.01em; }
        .tutorial-step-desc { font-size: 0.85rem; color: #64748b; line-height: 1.5; margin: 0; }
        
        .tutorial-info-box { background: linear-gradient(135deg, var(--pegawai-primary-dark), var(--pegawai-primary), var(--pegawai-accent)); border-radius: 16px; padding: 20px 24px; color: #fff; display: flex; align-items: center; justify-content: center; gap: 18px; margin: 0 auto 32px auto; max-width: 640px; box-shadow: 0 16px 32px rgba(20, 184, 166, 0.25); }
        .tutorial-info-icon { font-size: 1.75rem; opacity: 0.9; }
        .tutorial-info-text { font-size: 0.95rem; line-height: 1.6; font-weight: 500; text-align: left; margin: 0; }

        @keyframes pulse-ring { 0%,100% { transform: scale(0.985); opacity: 0.8; } 50% { transform: scale(1.02); opacity: 1; } }
        @keyframes glow-move { 0% { transform: translate(-2%, -2%); } 100% { transform: translate(2%, 2%); } }
        @media (max-width: 991.98px) { .step-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } .attendance-hero__content { flex-direction: column; align-items: stretch; } .tutorial-grid { grid-template-columns: repeat(2, 1fr); gap: 16px; } }
        @media (max-width: 575.98px) { .attendance-hero__content, .verification-panel { padding: 14px; } .attendance-actions { grid-template-columns: 1fr; } .step-grid { grid-template-columns: 1fr; } .status-band { align-items: flex-start; flex-direction: column; } .attendance-modal .modal-dialog { max-width: calc(100vw - 16px); margin: 8px auto; } .tutorial-grid { grid-template-columns: 1fr; } .tutorial-info-box { flex-direction: column; text-align: center; gap: 12px; } .tutorial-info-text { text-align: center; } }
    </style>
@endpush

@section('content')
    <div class="section-header">
        <h1>Absensi Wajah</h1>
    </div>

    <div class="attendance-shell">
        @if(isset($holidayMessage) && $holidayMessage)
            <div class="alert alert-warning mb-0" style="border-radius: 8px;">
                <strong>Hari Libur!</strong> {{ $holidayMessage }}. Absensi hari ini ditutup.
            </div>
        @endif

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
                    <button id="btnMasuk" type="button" class="mode-button mode-button--masuk" onclick="pilihMode('masuk')" {{ (isset($holidayMessage) && $holidayMessage) ? 'disabled' : '' }}>Absen Masuk</button>
                    <button id="btnKeluar" type="button" class="mode-button mode-button--keluar" onclick="pilihMode('keluar')" {{ (isset($holidayMessage) && $holidayMessage) ? 'disabled' : '' }}>Absen Keluar</button>
                </div>
            </div>
        </div>

        <div class="tutorial-grid">
            <div class="tutorial-step">
                <div class="tutorial-img-wrap">
                    <img src="{{ asset('images/steps/step1.png') }}" alt="Hadap Depan" class="tutorial-img">
                </div>
                <div class="tutorial-content">
                    <div class="tutorial-step-title"><span class="tutorial-badge">1</span> Hadap Depan</div>
                    <p class="tutorial-step-desc">Posisikan wajah pas di dalam frame kamera</p>
                </div>
            </div>
            <div class="tutorial-step">
                <div class="tutorial-img-wrap">
                    <img src="{{ asset('images/steps/step2.png') }}" alt="Hadap Kiri" class="tutorial-img">
                </div>
                <div class="tutorial-content">
                    <div class="tutorial-step-title"><span class="tutorial-badge">2</span> Hadap Kiri</div>
                    <p class="tutorial-step-desc">Putar wajah perlahan ke arah kiri</p>
                </div>
            </div>
            <div class="tutorial-step">
                <div class="tutorial-img-wrap">
                    <img src="{{ asset('images/steps/step3.png') }}" alt="Hadap Kanan" class="tutorial-img">
                </div>
                <div class="tutorial-content">
                    <div class="tutorial-step-title"><span class="tutorial-badge">3</span> Hadap Kanan</div>
                    <p class="tutorial-step-desc">Putar wajah perlahan ke arah kanan</p>
                </div>
            </div>
            <div class="tutorial-step">
                <div class="tutorial-img-wrap">
                    <img src="{{ asset('images/steps/step4.png') }}" alt="Buka Mulut" class="tutorial-img">
                </div>
                <div class="tutorial-content">
                    <div class="tutorial-step-title"><span class="tutorial-badge">4</span> Buka Mulut</div>
                    <p class="tutorial-step-desc">Buka mulut seperti mengucapkan "ah"</p>
                </div>
            </div>
        </div>

        <div class="tutorial-info-box">
            <i class="fas fa-info-circle tutorial-info-icon"></i>
            <p class="tutorial-info-text">Pastikan pencahayaan ruangan cukup terang dan wajah terlihat jelas tanpa atribut yang menutupi muka untuk hasil verifikasi yang optimal.</p>
        </div>

        <div class="modal fade attendance-modal" id="attendanceCameraModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="false">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="verification-panel">
                            <div class="verification-panel__top">
                                <h3 class="verification-panel__title">Live Verification</h3>
                                <div class="verification-panel__actions">
                                    <button type="button" id="toggleSoundBtn" onclick="toggleSound()" class="btn btn-sm btn-outline-light py-1 px-2 mr-2" style="font-size: 0.8rem; border-radius: 20px;">
                                        <i id="soundIcon" class="fas fa-volume-up mr-1"></i><span id="soundLabel">Suara: ON</span>
                                    </button>
                                    <button type="button" class="info-trigger" id="infoTrigger" aria-label="Lihat panduan absensi">
                                        <i class="fas fa-info"></i>
                                    </button>
                                    <button type="button" class="modal-close-trigger" data-dismiss="modal" aria-label="Tutup kamera">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="step-grid" id="stepGrid">
                                <div class="step-card is-active" data-step-card="0"><div class="step-card__number">1</div><div class="step-card__title">Wajah Dikenali</div><div class="step-card__caption">Pastikan wajah terbaca jelas.</div></div>
                                <div class="step-card" data-step-card="1"><div class="step-card__number">2</div><div class="step-card__title" id="stepTitle1">Tantangan 1</div><div class="step-card__caption" id="stepDesc1">Ikuti arahan gesture.</div></div>
                                <div class="step-card" data-step-card="2"><div class="step-card__number">3</div><div class="step-card__title" id="stepTitle2">Tantangan 2</div><div class="step-card__caption" id="stepDesc2">Ikuti arahan gesture.</div></div>
                                <div class="step-card" data-step-card="3"><div class="step-card__number">4</div><div class="step-card__title" id="stepTitle3">Tantangan 3</div><div class="step-card__caption" id="stepDesc3">Selesaikan gesture akhir.</div></div>
                            </div>

                            <div class="row align-items-center">
                                <div class="col-lg-7 text-center text-lg-left mb-4 mb-lg-0">
                                    <div class="verification-camera">
                                        <video id="video" autoplay muted playsinline class="verification-video"></video>
                                        <canvas id="overlay" class="verification-overlay"></canvas>
                                        <div class="face-guide-wrap">
                                            <div class="face-guide-ring" id="faceGuideRing">
                                                <div class="face-guide-pulse"></div>
                                            </div>
                                        </div>
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
        let isFaceRecognized = false;
        let pendingReasonPayload = {};
        let pendingAttendancePhoto = null;
        let isCameraModalVisible = false;
        let unknownFramesCount = 0;
        let mustResetToNeutral = false;
        const faceMatchThreshold = 0.55;
        const jadwalMasuk = @json(optional($jadwalPegawai)->jam_masuk);
        const jadwalPulang = @json(optional($jadwalPegawai)->jam_pulang);
        const toleransiTelat = {{ (int) optional($jadwalPegawai)->toleransi_telat }};

        // Text-to-Speech & Sound Logic
        let isSoundEnabled = true;
        let lastSpokenText = '';
        let lastSpokenTime = 0;

        function toggleSound() {
            isSoundEnabled = !isSoundEnabled;
            const soundIcon = document.getElementById('soundIcon');
            const soundLabel = document.getElementById('soundLabel');
            if (isSoundEnabled) {
                if (soundIcon) soundIcon.className = 'fas fa-volume-up mr-1';
                if (soundLabel) soundLabel.innerText = 'Suara: ON';
                speakInstruction('Suara instruksi diaktifkan', true);
            } else {
                if (soundIcon) soundIcon.className = 'fas fa-volume-mute mr-1';
                if (soundLabel) soundLabel.innerText = 'Suara: OFF';
                if ('speechSynthesis' in window) { window.speechSynthesis.cancel(); }
            }
        }

        function speakInstruction(text, force = false) {
            if (!isSoundEnabled || !('speechSynthesis' in window)) return;
            const now = Date.now();
            if (!force && text === lastSpokenText && (now - lastSpokenTime < 3500)) return;
            lastSpokenText = text;
            lastSpokenTime = now;
            try {
                window.speechSynthesis.cancel();
                const utterance = new SpeechSynthesisUtterance(text);
                utterance.lang = 'id-ID';
                utterance.rate = 1.0;
                utterance.pitch = 1.0;
                window.speechSynthesis.speak(utterance);
            } catch (e) { console.error('Speech error:', e); }
        }

        // Pool Tantangan Liveness Dynamic (Hadap Kiri, Hadap Kanan, Buka Mulut)
        const allChallengePool = [
            { id: 'kiri', title: 'Hadap Kiri', desc: 'Miringkan wajah sedikit ke kiri', speak: 'Arahkan wajah ke kiri', check: (lm) => detectHeadDirection(lm) === 'kiri' },
            { id: 'kanan', title: 'Hadap Kanan', desc: 'Miringkan wajah sedikit ke kanan', speak: 'Arahkan wajah ke kanan', check: (lm) => detectHeadDirection(lm) === 'kanan' },
            { id: 'mulut', title: 'Buka Mulut', desc: 'Buka mulut seperti bilang "ah"', speak: 'Buka mulut Anda', check: (lm) => mouthOpen(lm) }
        ];

        let activeChallenges = [];

        function randomizeChallenges() {
            // Shuffle pool & ambil 3 tantangan secara acak
            const shuffled = [...allChallengePool].sort(() => Math.random() - 0.5);
            activeChallenges = shuffled.slice(0, 3);

            // Update UI Step Cards
            for (let i = 0; i < 3; i++) {
                const elTitle = document.getElementById(`stepTitle${i + 1}`);
                const elDesc = document.getElementById(`stepDesc${i + 1}`);
                if (elTitle) elTitle.innerText = activeChallenges[i].title;
                if (elDesc) elDesc.innerText = activeChallenges[i].desc;
            }
        }

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
            const completedCount = absenDone ? 4 : (isFaceRecognized ? Math.min(gestureStep + 1, 3) : 0);
            const progress = Math.round((completedCount / 4) * 100);
            if (progressPercent) progressPercent.innerText = progress;
            if (faceGuideRing) faceGuideRing.style.setProperty('--progress-angle', `${(progress / 100) * 360}deg`);
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
            isFaceRecognized = false;
            unknownFramesCount = 0;
            mustResetToNeutral = false;
            pendingAttendancePhoto = null;
            randomizeChallenges();
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
            if (mustResetToNeutral) {
                gestureText.innerText = 'Kembali ke posisi netral (lihat lurus ke depan)';
                gestureBadge.innerText = 'Posisi Netral';
                cameraGuideLabel.innerText = 'Tengah';
                speakInstruction('Kembali ke posisi tengah');
                return;
            }

            const currentChallenge = activeChallenges[gestureStep];
            if (currentChallenge) {
                gestureText.innerText = `Silakan lakukan: ${currentChallenge.title} (${currentChallenge.desc})`;
                gestureBadge.innerText = currentChallenge.title;
                cameraGuideLabel.innerText = currentChallenge.title;
                speakInstruction(currentChallenge.speak);
            } else {
                gestureText.innerText = 'Semua tantangan selesai!';
                gestureBadge.innerText = 'Selesai';
                cameraGuideLabel.innerText = 'Selesai';
            }
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
                            <div>2. Ikuti gesture satu per satu secara acak yang diminta layar.</div>
                            <div>3. Setelah gesture, kembalikan wajah ke posisi tengah netral sebelum langkah berikutnya.</div>
                            <div>4. Foto bukti akan diambil saat wajah netral.</div>
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

        function isNeutralState(landmarks) {
            return detectHeadDirection(landmarks) === 'tengah' && !mouthOpen(landmarks);
        }

        function checkScreenSpoof(videoElement, detection) {
            try {
                const box = detection.detection.box;
                if (!box || box.width <= 0 || box.height <= 0) return { isSpoof: false };

                const tempCanvas = document.createElement('canvas');
                tempCanvas.width = 100;
                tempCanvas.height = 100;
                const ctx = tempCanvas.getContext('2d');
                
                // Crop area wajah + 30% padding sekeliling untuk mendeteksi bezel / bingkai HP
                const padX = box.width * 0.30;
                const padY = box.height * 0.30;
                const cropX = Math.max(0, box.x - padX);
                const cropY = Math.max(0, box.y - padY);
                const cropW = Math.min(videoElement.videoWidth - cropX, box.width + (padX * 2));
                const cropH = Math.min(videoElement.videoHeight - cropY, box.height + (padY * 2));

                ctx.drawImage(videoElement, cropX, cropY, cropW, cropH, 0, 0, 100, 100);

                const imgData = ctx.getImageData(0, 0, 100, 100).data;
                let glareCount = 0;
                let highFreqNoise = 0;
                let darkBezelCount = 0;
                const totalPixels = 100 * 100;

                for (let i = 0; i < imgData.length; i += 4) {
                    const r = imgData[i];
                    const g = imgData[i + 1];
                    const b = imgData[i + 2];
                    const lum = 0.299 * r + 0.587 * g + 0.114 * b;

                    // 1. Deteksi Glare / Pantulan Layar Kaca HP (Piksel Terang)
                    if (r > 215 && g > 215 && b > 215) {
                        glareCount++;
                    }

                    // 2. Deteksi Bingkai / Bezel HP Hitam di sekitar wajah
                    if (lum < 30) {
                        darkBezelCount++;
                    }

                    // 3. Deteksi Moiré Noise (Garis Piksel Layar Digital)
                    if (i >= 4) {
                        const prevLum = 0.299 * imgData[i - 4] + 0.587 * imgData[i - 3] + 0.114 * imgData[i - 2];
                        highFreqNoise += Math.abs(lum - prevLum);
                    }
                }

                const avgNoise = highFreqNoise / totalPixels;
                const glareRatio = glareCount / totalPixels;
                const bezelRatio = darkBezelCount / totalPixels;

                // Threshold sensitif untuk mendeteksi Layar HP / Bingkai Bezel Digital
                if (glareRatio > 0.05 || avgNoise > 22 || bezelRatio > 0.10) {
                    return { isSpoof: true, reason: 'Terdeteksi Layar HP / Bingkai Bezel Digital' };
                }
            } catch (e) {
                console.error('Screen spoof check error:', e);
            }
            return { isSpoof: false };
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
            if (pendingAttendancePhoto || !isNeutralState(landmarks)) return;
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
                    unknownFramesCount++;
                    if (unknownFramesCount > 5) {
                        isFaceRecognized = false;
                        gestureStep = 0;
                        pendingAttendancePhoto = null;
                        mustResetToNeutral = false;
                        updateProgressUI();
                    }
                    resultText.innerText = 'Posisikan wajah di dalam frame';
                    gestureText.innerText = 'Pastikan wajah masuk penuh ke dalam area oval';
                    cameraGuideLabel.innerText = 'Cari Wajah';
                    setStatusPill('scan', 'Mencari');
                    return;
                }

                if (resizedDetections.length > 1) {
                    isFaceRecognized = false;
                    gestureStep = 0;
                    pendingAttendancePhoto = null;
                    mustResetToNeutral = false;
                    updateProgressUI();
                    
                    resultText.innerText = 'Terlalu banyak wajah!';
                    gestureText.innerText = 'Pastikan hanya ada 1 orang di depan kamera.';
                    cameraGuideLabel.innerText = 'Wajah > 1';
                    setStatusPill('error', 'Blokir');
                    return;
                }

                const detection = resizedDetections[0];
                const match = faceMatcher.findBestMatch(detection.descriptor);
                const matchIsValid = isMatchValid(match);
                const label = matchIsValid ? formatMatchLabel(match) : `Unknown (${getMatchSimilarity(match)}%)`;
                new faceapi.draw.DrawBox(mirrorBox(detection.detection.box, displaySize.width), { label }).draw(overlay);
                resultText.innerText = `Terdeteksi: ${label}`;
                const landmarks = detection.landmarks;

                if (!matchIsValid) {
                    unknownFramesCount++;
                    if (unknownFramesCount > 5) {
                        isFaceRecognized = false;
                        gestureStep = 0;
                        pendingAttendancePhoto = null;
                        mustResetToNeutral = false;
                        updateProgressUI();
                    }
                    cameraGuideLabel.innerText = 'Wajah Tidak Valid';
                    gestureBadge.innerText = 'Wajah belum cocok';
                    gestureText.innerText = 'Wajah tidak dikenali. Dekatkan wajah, cari cahaya yang cukup, atau ulangi perekaman dataset.';
                    setStatusPill('error', 'Unknown');
                    return;
                }

                // Cek Spoofing Layar HP (Moiré / Glare Pantulan Kaca)
                const spoofStatus = checkScreenSpoof(video, detection);
                if (spoofStatus.isSpoof) {
                    resultText.innerText = 'Verifikasi Ditolak (Layar HP)';
                    cameraGuideLabel.innerText = 'Layar Terdeteksi';
                    gestureBadge.innerText = 'Ditolak (HP)';
                    gestureText.innerText = 'Ditolak! Terdeteksi pantulan layar HP / media digital. Harap gunakan wajah asli Anda secara langsung.';
                    setStatusPill('error', 'Spoof HP');
                    speakInstruction('Absensi ditolak. Terdeteksi pantulan layar HP.');
                    return;
                }

                unknownFramesCount = 0;
                let wasRecognized = isFaceRecognized;
                isFaceRecognized = true;
                if (!wasRecognized) {
                    updateProgressUI();
                    updateGestureText();
                }
                
                setStatusPill('success', 'Wajah Terkunci');
                captureNeutralAttendancePhoto(landmarks);

                // Cek pintu reset ke posisi netral terlebih dahulu
                if (mustResetToNeutral) {
                    if (isNeutralState(landmarks)) {
                        mustResetToNeutral = false;
                        updateGestureText();
                    } else {
                        cameraGuideLabel.innerText = 'Tengah';
                        gestureBadge.innerText = 'Posisi Netral';
                        gestureText.innerText = 'Posisikan wajah kembali ke tengah frame sebelum tantangan berikutnya.';
                        speakInstruction('Kembali ke tengah');
                    }
                    return;
                }

                // Jalankan tantangan aktif sesuai urutan acak
                if (gestureStep < activeChallenges.length) {
                    const currentChallenge = activeChallenges[gestureStep];
                    cameraGuideLabel.innerText = currentChallenge.title;
                    gestureBadge.innerText = `Langkah ${gestureStep + 1}`;

                    if (currentChallenge.check(landmarks)) {
                        gestureStep++;
                        mustResetToNeutral = true;
                        updateGestureText();
                    } else {
                        speakInstruction(currentChallenge.speak);
                    }
                } else if (!absenDone) {
                    // Semua tantangan acak selesai! Simpan absensi
                    absenDone = true;
                    const fotoBukti = pendingAttendancePhoto || captureAttendancePhoto();
                    gestureBadge.innerText = 'Menyimpan';
                    cameraGuideLabel.innerText = 'Verifikasi';
                    speakInstruction('Verifikasi liveness berhasil', true);
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
                            mustResetToNeutral = false;
                            resultText.innerText = 'Absensi gagal disimpan';
                            gestureText.innerText = error.message;
                            cameraGuideLabel.innerText = 'Ulangi';
                            gestureBadge.innerText = 'Gagal';
                            setStatusPill('error', 'Gagal');
                            updateProgressUI();
                            showErrorAlert(error.message);
                        });
                }
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
