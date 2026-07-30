<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>Login &mdash; FAKTOR Presensi AI</title>

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- General CSS Files -->
  <link rel="stylesheet" href="{{ asset('admin/dist/assets/modules/bootstrap/css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('admin/dist/assets/modules/fontawesome/css/all.min.css') }}">

  <style>
    * {
      box-sizing: border-box;
    }

    body {
      font-family: 'Plus Jakarta Sans', sans-serif;
      background: #f4f6f9;
      margin: 0;
      padding: 24px;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
      overflow: hidden;
    }

    /* TOP-LEFT ABSTRACT SHAPE & DOT GRID SILHOUETTE */
    .shape-top-left {
      position: fixed;
      top: -90px;
      left: -90px;
      width: 320px;
      height: 320px;
      z-index: 0;
      pointer-events: none;
    }

    .shape-top-left-circle {
      width: 300px;
      height: 300px;
      background: linear-gradient(135deg, rgba(99, 102, 241, 0.4) 0%, rgba(67, 56, 202, 0.35) 100%);
      border-radius: 50%;
      box-shadow: 0 15px 30px rgba(79, 70, 229, 0.12);
    }

    .dots-grid-top {
      position: fixed;
      top: 130px;
      left: 110px;
      z-index: 0;
      opacity: 0.35;
      pointer-events: none;
    }

    /* TOP-RIGHT SOFT AMBIENT CIRCLE */
    .shape-top-right {
      position: fixed;
      top: -40px;
      right: 140px;
      width: 180px;
      height: 180px;
      background: linear-gradient(135deg, rgba(6, 182, 212, 0.22) 0%, rgba(99, 102, 241, 0.15) 100%);
      border-radius: 50%;
      filter: blur(15px);
      z-index: 0;
      pointer-events: none;
    }

    /* BOTTOM-LEFT SOFT AMBIENT CIRCLE */
    .shape-bottom-left {
      position: fixed;
      bottom: 20px;
      left: 60px;
      width: 220px;
      height: 220px;
      background: linear-gradient(135deg, rgba(168, 85, 247, 0.18) 0%, rgba(99, 102, 241, 0.12) 100%);
      border-radius: 50%;
      filter: blur(20px);
      z-index: 0;
      pointer-events: none;
    }

    /* BOTTOM-RIGHT ABSTRACT SHAPE SILHOUETTE */
    .shape-bottom-right {
      position: fixed;
      bottom: -80px;
      right: -80px;
      width: 290px;
      height: 290px;
      background: linear-gradient(135deg, rgba(129, 140, 248, 0.35) 0%, rgba(79, 70, 229, 0.25) 100%);
      border-radius: 50%;
      z-index: 0;
      box-shadow: 0 15px 30px rgba(79, 70, 229, 0.1);
      pointer-events: none;
    }

    .dots-grid-bottom {
      position: fixed;
      bottom: 120px;
      right: 140px;
      z-index: 0;
      opacity: 0.3;
      pointer-events: none;
    }

    /* CENTERED CONTAINER MODAL CARD (SPLIT-SCREEN) */
    .login-modal-card {
      position: relative;
      z-index: 1;
      width: 100%;
      max-width: 940px;
      background: #ffffff;
      border-radius: 20px;
      box-shadow: 0 20px 45px -10px rgba(15, 23, 42, 0.15);
      overflow: hidden;
      display: flex;
      min-height: 520px;
      border: 1px solid #e2e8f0;
    }

    /* LEFT FORM PANEL (50%) */
    .left-panel {
      flex: 1;
      padding: 40px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      background: #ffffff;
    }

    .brand-logo {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 24px;
    }

    .brand-logo img {
      width: 32px;
      height: 32px;
      object-fit: contain;
    }

    .brand-name {
      font-size: 1.25rem;
      font-weight: 800;
      color: #4f46e5;
      letter-spacing: -0.3px;
    }

    .login-heading {
      font-size: 1.35rem;
      font-weight: 800;
      color: #0f172a;
      line-height: 1.3;
      margin-bottom: 6px;
      letter-spacing: -0.3px;
    }

    .login-subhead {
      color: #64748b;
      font-size: 0.85rem;
      margin-bottom: 24px;
    }

    .form-group-custom {
      margin-bottom: 16px;
    }

    .form-group-custom label {
      font-size: 0.8rem;
      font-weight: 700;
      color: #334155;
      margin-bottom: 6px;
      display: block;
    }

    .input-wrapper {
      position: relative;
    }

    .input-wrapper input {
      width: 100%;
      padding: 10px 14px 10px 38px;
      border-radius: 8px;
      border: 1.5px solid #e2e8f0;
      font-size: 0.88rem;
      color: #0f172a;
      background: #f8fafc;
      transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .input-wrapper input:focus {
      outline: none;
      border-color: #4f46e5;
      background: #ffffff;
      box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.12);
    }

    .input-wrapper i.field-icon {
      position: absolute;
      left: 14px;
      top: 50%;
      transform: translateY(-50%);
      color: #94a3b8;
      font-size: 0.9rem;
    }

    .toggle-password {
      position: absolute;
      right: 14px;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      color: #94a3b8;
      cursor: pointer;
      padding: 0;
      font-size: 0.9rem;
    }

    .toggle-password:hover {
      color: #4f46e5;
    }

    .btn-login {
      width: 100%;
      padding: 11px;
      border-radius: 8px;
      background: #4f46e5;
      border: none;
      color: #ffffff;
      font-weight: 700;
      font-size: 0.9rem;
      cursor: pointer;
      box-shadow: 0 8px 16px -4px rgba(79, 70, 229, 0.35);
      transition: all 0.2s ease;
      margin-top: 6px;
    }

    .btn-login:hover {
      background: #4338ca;
      transform: translateY(-1px);
      box-shadow: 0 12px 20px -4px rgba(79, 70, 229, 0.45);
    }

    .alert-danger-custom {
      background: #fef2f2;
      border: 1px solid #fecaca;
      color: #991b1b;
      padding: 10px 14px;
      border-radius: 8px;
      font-size: 0.82rem;
      margin-bottom: 18px;
    }

    .panel-footer {
      font-size: 0.78rem;
      color: #94a3b8;
      margin-top: 24px;
    }

    /* RIGHT VISUAL PANEL (50%) */
    .right-panel {
      flex: 1;
      background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 36px;
      position: relative;
    }

    .visual-wrapper {
      width: 100%;
      max-width: 340px;
      text-align: center;
    }

    .visual-tagline {
      margin-top: 20px;
    }

    .visual-tagline h4 {
      font-size: 1.05rem;
      font-weight: 800;
      color: #1e1b4b;
      margin-bottom: 6px;
    }

    .visual-tagline p {
      color: #475569;
      font-size: 0.8rem;
      line-height: 1.45;
    }

    @media (max-width: 767.98px) {
      body {
        padding: 12px;
      }
      .right-panel {
        display: none;
      }
      .left-panel {
        padding: 32px 24px;
      }
    }
  </style>
</head>
<body>

  <!-- TOP-LEFT SILHOUETTE & DOT GRID -->
  <div class="shape-top-left">
    <div class="shape-top-left-circle"></div>
  </div>

  <svg class="dots-grid-top" width="120" height="120" viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg">
    <pattern id="dotGrid" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse">
      <circle cx="3" cy="3" r="2.5" fill="#4f46e5"/>
    </pattern>
    <rect width="120" height="120" fill="url(#dotGrid)" />
  </svg>

  <!-- TOP-RIGHT & BOTTOM-LEFT AMBIENT SOFT CIRCLES -->
  <div class="shape-top-right"></div>
  <div class="shape-bottom-left"></div>

  <!-- BOTTOM-RIGHT SILHOUETTE & DOT GRID -->
  <div class="shape-bottom-right"></div>

  <svg class="dots-grid-bottom" width="100" height="100" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
    <pattern id="dotGridBottom" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse">
      <circle cx="3" cy="3" r="2.5" fill="#6366f1"/>
    </pattern>
    <rect width="100" height="100" fill="url(#dotGridBottom)" />
  </svg>

  <!-- CENTERED SPLIT-SCREEN CONTAINER CARD -->
  <div class="login-modal-card">
    
    {{-- LEFT FORM PANEL --}}
    <div class="left-panel">
      <div>
        <div class="brand-logo">
          <img src="{{ asset('admin/dist/assets/img/stisla-fill.svg') }}" alt="FAKTOR Logo">
          <span class="brand-name">FAKTOR</span>
        </div>

        <h1 class="login-heading">Portal Presensi & Kehadiran Pegawai</h1>
        <p class="login-subhead">Selamat Datang! Silakan masuk ke akun Anda.</p>

        @if($errors->any())
          <div class="alert-danger-custom">
            <i class="fas fa-exclamation-circle mr-2"></i> {{ $errors->first() }}
          </div>
        @endif

        <form method="POST" action="{{ route('login.process') }}">
          @csrf

          <div class="form-group-custom">
            <label for="username">Username / NIK</label>
            <div class="input-wrapper">
              <input 
                type="text" 
                id="username" 
                name="username" 
                value="{{ old('username') }}" 
                placeholder="Masukkan username atau NIK" 
                required 
                autofocus
              >
              <i class="fas fa-user field-icon"></i>
            </div>
          </div>

          <div class="form-group-custom">
            <label for="password">Password</label>
            <div class="input-wrapper">
              <input 
                type="password" 
                id="password" 
                name="password" 
                placeholder="Masukkan password Anda" 
                required
              >
              <i class="fas fa-lock field-icon"></i>
              <button type="button" class="toggle-password" onclick="togglePasswordVisibility()">
                <i class="far fa-eye" id="eyeIcon"></i>
              </button>
            </div>
          </div>

          <button type="submit" class="btn-login">
            <i class="fas fa-sign-in-alt mr-2"></i> Login Sekarang
          </button>
        </form>
      </div>

      <div class="panel-footer">
        &copy; {{ date('Y') }} FAKTOR Attendance System. All rights reserved.
      </div>
    </div>

    {{-- RIGHT VISUAL PANEL WITH AI GRAPHIC --}}
    <div class="right-panel">
      <div class="visual-wrapper">
        
        <svg viewBox="0 0 500 450" fill="none" xmlns="http://www.w3.org/2000/svg" style="width: 100%; max-width: 280px; height: auto;">
          <circle cx="250" cy="220" r="190" fill="#ffffff" opacity="0.6"/>
          <circle cx="250" cy="220" r="145" stroke="#6366f1" stroke-width="2" stroke-dasharray="6 6" opacity="0.6"/>
          
          <path d="M175 320 C175 285, 195 265, 250 265 C305 265, 325 285, 325 320 V340 H175 V320 Z" fill="#4f46e5"/>
          <circle cx="250" cy="185" r="55" fill="#6366f1"/>
          
          <path d="M140 130 H170 M140 130 V160" stroke="#4f46e5" stroke-width="4" stroke-linecap="round"/>
          <path d="M360 130 H330 M360 130 V160" stroke="#4f46e5" stroke-width="4" stroke-linecap="round"/>
          <path d="M140 310 H170 M140 310 V280" stroke="#4f46e5" stroke-width="4" stroke-linecap="round"/>
          <path d="M360 310 H330 M360 310 V280" stroke="#4f46e5" stroke-width="4" stroke-linecap="round"/>
          
          <circle cx="230" cy="175" r="5" fill="#06b6d4"/>
          <circle cx="270" cy="175" r="5" fill="#06b6d4"/>
          <circle cx="250" cy="192" r="4" fill="#06b6d4"/>
          <path d="M236 210 Q250 220 264 210" stroke="#06b6d4" stroke-width="3" stroke-linecap="round" fill="none"/>
          
          <line x1="230" y1="175" x2="250" y2="192" stroke="#06b6d4" stroke-width="1.5" opacity="0.8"/>
          <line x1="270" y1="175" x2="250" y2="192" stroke="#06b6d4" stroke-width="1.5" opacity="0.8"/>
          <line x1="230" y1="175" x2="270" y2="175" stroke="#06b6d4" stroke-width="1.5" opacity="0.6"/>

          <line x1="130" y1="180" x2="370" y2="180" stroke="#06b6d4" stroke-width="3" stroke-linecap="round">
            <animate attributeName="y1" values="130; 300; 130" dur="3.5s" repeatCount="indefinite" />
            <animate attributeName="y2" values="130; 300; 130" dur="3.5s" repeatCount="indefinite" />
          </line>

          <g transform="translate(60, 70)">
            <rect width="135" height="38" rx="19" fill="#ffffff" filter="drop-shadow(0px 8px 16px rgba(0,0,0,0.08))"/>
            <circle cx="20" cy="19" r="10" fill="#10b981"/>
            <path d="M16 19L19 22L24 16" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <text x="38" y="23" fill="#0f172a" font-size="12" font-weight="bold" font-family="'Plus Jakarta Sans', sans-serif">Face Match</text>
          </g>

          <g transform="translate(300, 310)">
            <rect width="145" height="38" rx="19" fill="#ffffff" filter="drop-shadow(0px 8px 16px rgba(0,0,0,0.08))"/>
            <circle cx="20" cy="19" r="10" fill="#6366f1"/>
            <path d="M20 13V25M14 19H26" stroke="white" stroke-width="2" stroke-linecap="round"/>
            <text x="38" y="23" fill="#0f172a" font-size="12" font-weight="bold" font-family="'Plus Jakarta Sans', sans-serif">Liveness AI</text>
          </g>
        </svg>

        <div class="visual-tagline">
          <h4>Deteksi Wajah & Gestur Real-Time</h4>
          <p>Presensi otomatis berbasis AI dengan tingkat akurasi tinggi dan deteksi keaktifan anti-spoofing.</p>
        </div>

      </div>
    </div>

  </div>

  <script>
    function togglePasswordVisibility() {
      const passwordInput = document.getElementById('password');
      const eyeIcon = document.getElementById('eyeIcon');
      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.className = 'far fa-eye-slash';
      } else {
        passwordInput.type = 'password';
        eyeIcon.className = 'far fa-eye';
      }
    }
  </script>
</body>
</html>