@extends('layouts.pegawai')

@section('content')
<div class="section-header mb-4">
    <h1>Profil Saya</h1>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible show fade mb-4">
        <div class="alert-body">
            <button class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
        </div>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible show fade mb-4">
        <div class="alert-body">
            <button class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
            <ul class="mb-0 pl-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<div class="row">
    <!-- Informasi & Ubah Profil -->
    <div class="col-lg-6 mb-4">
        <div class="card h-100 shadow-sm">
            <div class="card-header bg-white font-weight-bold">
                <h4><i class="fas fa-user-edit mr-2 text-primary"></i> Edit Informasi Profil</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('pegawai.profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <!-- Avatar Preview & Upload -->
                    <div class="text-center mb-4">
                        <div class="position-relative d-inline-block">
                            @if($pegawai->foto)
                                <img src="{{ asset('storage/' . $pegawai->foto) }}" id="avatarPreview" alt="Foto {{ $pegawai->nama }}" class="mb-2 shadow-sm" style="width: 110px; height: 110px; object-fit: cover; border-radius: 28px; border: 3px solid var(--pegawai-primary, #4f46e5);">
                            @else
                                <div id="avatarPlaceholder" class="pegawai-user-avatar mx-auto mb-2 shadow-sm d-flex align-items-center justify-content-center" style="width: 110px; height: 110px; font-size: 42px; border-radius: 28px; background: #e0e7ff; color: #4f46e5;">
                                    <i class="fas fa-user"></i>
                                </div>
                                <img src="" id="avatarPreview" alt="Preview Foto" class="mb-2 shadow-sm d-none" style="width: 110px; height: 110px; object-fit: cover; border-radius: 28px; border: 3px solid var(--pegawai-primary, #4f46e5);">
                            @endif
                        </div>
                        <div class="mt-2">
                            <label for="fotoInput" class="btn btn-sm btn-outline-primary px-3 rounded-pill cursor-pointer mb-0">
                                <i class="fas fa-camera mr-1"></i> Pilih Foto Baru
                            </label>
                            <input type="file" name="foto" id="fotoInput" class="d-none" accept="image/*" onchange="previewImage(this)">
                            <small class="form-text text-muted">Format: JPG, PNG, max 2MB</small>
                        </div>
                    </div>

                    <!-- Input Nama -->
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark">Nama Lengkap <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                            </div>
                            <input type="text" name="nama" class="form-control" value="{{ old('nama', $pegawai->nama) }}" placeholder="Masukkan nama lengkap" required>
                        </div>
                    </div>

                    <!-- Informasi Tetap (Readonly) -->
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-muted">NIK (Nomor Induk Karyawan)</label>
                        <input type="text" class="form-control bg-light" value="{{ $pegawai->nik }}" readonly>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label class="font-weight-bold text-muted">Departemen</label>
                            <input type="text" class="form-control bg-light" value="{{ optional($pegawai->departemen)->nama_departemen ?? '-' }}" readonly>
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label class="font-weight-bold text-muted">Jadwal / Shift</label>
                            <input type="text" class="form-control bg-light" value="{{ optional($pegawai->jadwal)->nama_shift ?? '-' }}" readonly>
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label class="font-weight-bold text-muted">Username Login</label>
                        <input type="text" class="form-control bg-light" value="{{ $pegawai->username }}" readonly>
                    </div>

                    <div class="text-right">
                        <button type="submit" class="btn btn-primary px-4 font-weight-bold">
                            <i class="fas fa-save mr-1"></i> Simpan Perubahan Profil
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Ubah Password -->
    <div class="col-lg-6 mb-4">
        <div class="card h-100 shadow-sm">
            <div class="card-header bg-white font-weight-bold">
                <h4><i class="fas fa-key mr-2 text-warning"></i> Ubah Password Akun</h4>
            </div>
            <div class="card-body">
                <p class="text-muted small mb-4">Gunakan password yang kuat dengan minimal 6 karakter untuk menjaga keamanan akun Anda.</p>

                <form action="{{ route('pegawai.profile.update-password') }}" method="POST">
                    @csrf
                    
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark">Password Saat Ini <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            </div>
                            <input type="password" name="current_password" class="form-control" placeholder="Masukkan password saat ini" required>
                        </div>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark">Password Baru <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-key"></i></span>
                            </div>
                            <input type="password" name="new_password" class="form-control" placeholder="Masukkan password baru (min. 6 karakter)" required minlength="6">
                        </div>
                    </div>
                    
                    <div class="form-group mb-4">
                        <label class="font-weight-bold text-dark">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-check-double"></i></span>
                            </div>
                            <input type="password" name="new_password_confirmation" class="form-control" placeholder="Ulangi password baru" required minlength="6">
                        </div>
                    </div>
                    
                    <div class="text-right">
                        <button type="submit" class="btn btn-warning px-4 font-weight-bold text-white" style="background-color: #f59e0b; border: none;">
                            <i class="fas fa-shield-alt mr-1"></i> Update Password Baru
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            let reader = new FileReader();
            reader.onload = function(e) {
                let img = document.getElementById('avatarPreview');
                let placeholder = document.getElementById('avatarPlaceholder');
                img.src = e.target.result;
                img.classList.remove('d-none');
                if (placeholder) {
                    placeholder.classList.add('d-none');
                }
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush
