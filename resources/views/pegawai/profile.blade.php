@extends('layouts.pegawai')

@section('content')
<div class="section-header mb-4">
    <h1>Profil Pegawai</h1>
</div>

<div class="row">
    <!-- Informasi Profil -->
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h4>Informasi Pegawai</h4>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    @if($pegawai->foto)
                        <img src="{{ asset('storage/' . $pegawai->foto) }}" alt="Foto {{ $pegawai->nama }}" class="mb-3" style="width: 100px; height: 100px; object-fit: cover; border-radius: 24px; border: 2px solid var(--pegawai-primary);">
                    @else
                        <div class="pegawai-user-avatar mx-auto mb-3" style="width: 100px; height: 100px; font-size: 38px; border-radius: 24px;">
                            <i class="fas fa-user"></i>
                        </div>
                    @endif
                    <h5 class="font-weight-bold mb-0">{{ $pegawai->nama }}</h5>
                    <div class="text-muted mb-3">{{ $pegawai->jabatan ?? 'Pegawai' }}</div>
                    
                    <button class="btn btn-sm btn-outline-primary" type="button" data-toggle="collapse" data-target="#uploadForm">
                        <i class="fas fa-camera mr-1"></i> Ubah Foto
                    </button>
                    
                    <div class="collapse mt-3" id="uploadForm">
                        <form action="{{ route('pegawai.profile.update-foto') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="custom-file mb-2 text-left">
                                <input type="file" name="foto" class="custom-file-input" id="customFile" accept="image/*" required>
                                <label class="custom-file-label" for="customFile">Pilih foto...</label>
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm btn-block">Simpan Foto</button>
                        </form>
                    </div>
                </div>

                <table class="table table-sm table-borderless">
                    <tr>
                        <th width="40%" class="text-muted">NIK</th>
                        <td class="font-weight-bold">{{ $pegawai->nik }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Departemen</th>
                        <td class="font-weight-bold">{{ optional($pegawai->departemen)->nama_departemen ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Jadwal / Shift</th>
                        <td class="font-weight-bold">{{ optional($pegawai->jadwal)->nama_shift ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Username Login</th>
                        <td class="font-weight-bold">{{ $pegawai->username }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Ubah Password -->
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h4>Ubah Password</h4>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible show fade">
                        <div class="alert-body">
                            <button class="close" data-dismiss="alert">
                                <span>&times;</span>
                            </button>
                            {{ session('success') }}
                        </div>
                    </div>
                @endif
                
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible show fade">
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

                <form action="{{ route('pegawai.profile.update-password') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label>Password Saat Ini</label>
                        <input type="password" name="current_password" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Password Baru</label>
                        <input type="password" name="new_password" class="form-control" required minlength="6">
                    </div>
                    
                    <div class="form-group">
                        <label>Konfirmasi Password Baru</label>
                        <input type="password" name="new_password_confirmation" class="form-control" required minlength="6">
                    </div>
                    
                    <div class="text-right mt-4">
                        <button type="submit" class="btn btn-primary px-4">Simpan Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Custom file input label logic
    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });
</script>
@endpush
