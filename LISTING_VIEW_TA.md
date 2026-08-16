# LAMPIRAN LISTING VIEW TEMPLATE APLIKASI (TAMPILAN BLADE INTERFACE LENGKAP UTUH)
## SISTEM INFORMASI PRESENSI PEGAWAI BERBASIS FACE RECOGNITION DAN LIVENESS DETECTION

---

### A. MODUL INTERFACE PEGAWAI (`resources/views/pegawai/`)

#### 1. View Absensi Kamera & Liveness Real-Time (`resources/views/pegawai/absensi.blade.php`)
```html
@extends('layouts.pegawai')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm border-0 text-center">
        <div class="card-body p-4">
            <h4 class="font-weight-bold mb-3"><i class="fas fa-camera mr-2 text-primary"></i> Presensi Wajah Real-Time</h4>
            <div class="camera-wrapper position-relative mx-auto mb-3" style="max-width: 440px;">
                <video id="video" width="420" height="520" autoplay muted playsinline class="rounded border shadow-sm w-100"></video>
                <canvas id="overlay" class="position-absolute top-0 start-0 w-100 h-100"></canvas>
            </div>
            <div class="alert alert-info mb-0">
                <i class="fas fa-info-circle mr-1"></i> Dekatkan wajah Anda ke oval panduan dan ikuti tantangan gerakan wajah di layar secara real-time.
            </div>
        </div>
    </div>
</div>
@endsection
```

#### 2. View Dashboard Overview Pegawai (`resources/views/pegawai/dashboard.blade.php`)
```html
@extends('layouts.pegawai')

@section('content')
<div class="container py-4">
    <div class="card bg-gradient-primary text-white shadow-sm p-4 mb-4">
        <h3 class="font-weight-bold mb-1 text-white">Selamat Datang, {{ auth()->user()->nama }}!</h3>
        <p class="mb-0 text-white-50">Sistem Presensi Pegawai Berbasis Liveness Detection & Face Recognition</p>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 text-center p-3 mb-3">
                <h6 class="text-muted">Total Kehadiran Bulan Ini</h6>
                <h2 class="font-weight-bold text-primary mb-0">{{ $stats['hadir_bulan_ini'] }} Hari</h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 text-center p-3 mb-3">
                <h6 class="text-muted">Status Presensi Masuk</h6>
                <h2 class="font-weight-bold {{ $stats['sudah_masuk_hari_ini'] ? 'text-success' : 'text-danger' }} mb-0">
                    {{ $stats['sudah_masuk_hari_ini'] ? 'Sudah Absen' : 'Belum Absen' }}
                </h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 text-center p-3 mb-3">
                <h6 class="text-muted">Status Presensi Pulang</h6>
                <h2 class="font-weight-bold {{ $stats['sudah_pulang_hari_ini'] ? 'text-success' : 'text-danger' }} mb-0">
                    {{ $stats['sudah_pulang_hari_ini'] ? 'Sudah Absen' : 'Belum Absen' }}
                </h2>
            </div>
        </div>
    </div>
</div>
@endsection
```

#### 3. View Perekaman Dataset Wajah Pegawai (`resources/views/pegawai/dataset.blade.php`)
```html
@extends('layouts.pegawai')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white"><h4 class="font-weight-bold mb-0">Perekaman Dataset Wajah Pegawai</h4></div>
        <div class="card-body">
            <div class="progress mb-3" style="height: 25px;">
                <div id="progressBar" class="progress-bar bg-success" role="progressbar" style="width: {{ ($datasetCount / $minDataset) * 100 }}%;">
                    {{ $datasetCount }} / {{ $minDataset }} Sampel
                </div>
            </div>
            <div class="camera-box text-center">
                <video id="videoDataset" width="400" height="400" autoplay muted class="rounded border"></video>
            </div>
        </div>
    </div>
</div>
@endsection
```

#### 4. View Edit Profil & Password Pegawai (`resources/views/pegawai/profile.blade.php`)
```html
@extends('layouts.pegawai')

@section('content')
<div class="container py-4">
    <h4 class="font-weight-bold mb-3">Profil Saya</h4>
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white"><h5 class="mb-0">Edit Informasi Profil</h5></div>
                <div class="card-body">
                    <form action="{{ route('pegawai.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="text-center mb-3">
                            <img src="{{ $pegawai->foto ? asset('storage/' . $pegawai->foto) : asset('admin/dist/assets/img/avatar/avatar-1.png') }}" class="rounded-circle" style="width:100px; height:100px; object-fit:cover;">
                            <input type="file" name="foto" class="form-control mt-2">
                        </div>
                        <div class="form-group mb-3"><label>Nama Lengkap</label><input type="text" name="nama" value="{{ $pegawai->nama }}" class="form-control" required></div>
                        <button type="submit" class="btn btn-primary btn-block">Simpan Perubahan Profil</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white"><h5 class="mb-0">Ubah Password Akun</h5></div>
                <div class="card-body">
                    <form action="{{ route('pegawai.profile.password') }}" method="POST">
                        @csrf
                        <div class="form-group mb-3"><label>Password Saat Ini</label><input type="password" name="current_password" class="form-control" required></div>
                        <div class="form-group mb-3"><label>Password Baru</label><input type="password" name="password" class="form-control" required></div>
                        <div class="form-group mb-3"><label>Konfirmasi Password Baru</label><input type="password" name="password_confirmation" class="form-control" required></div>
                        <button type="submit" class="btn btn-warning btn-block font-weight-bold">Update Password Baru</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
```

#### 5. View Riwayat Presensi Pegawai (`resources/views/pegawai/riwayat.blade.php`)
```html
@extends('layouts.pegawai')

@section('content')
<div class="container py-4">
    <h4 class="font-weight-bold mb-3">Riwayat Presensi Saya</h4>
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead>
                    <tr><th>Tanggal</th><th>Jam Masuk</th><th>Jam Pulang</th><th>Status</th><th>Bukti Foto</th></tr>
                </thead>
                <tbody>
                    @forelse($riwayatAbsensi as $item)
                        <tr>
                            <td>{{ $item->tanggal }}</td>
                            <td>{{ $item->jam_masuk ?? '-' }}</td>
                            <td>{{ $item->jam_pulang ?? '-' }}</td>
                            <td><span class="badge badge-{{ $item->status == 'terlambat' ? 'warning' : 'success' }}">{{ $item->status }}</span></td>
                            <td>
                                @if($item->foto_masuk) <img src="{{ asset('storage/' . $item->foto_masuk) }}" style="width:40px; height:40px; border-radius:6px; object-fit:cover;"> @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted">Belum ada riwayat presensi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
```

---

### B. MODUL INTERFACE ADMIN MASTER & DATA (`resources/views/admin/`)

#### 6. View Beranda Dashboard Admin (`resources/views/admin/index.blade.php`)
```html
@extends('layouts.admin')

@section('content')
<div class="section-header"><h1>Dashboard Overview</h1></div>
<div class="row mb-4">
  <div class="col-12">
    <div class="card bg-gradient-primary text-white shadow-sm p-3">
      <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div>
          <h3 class="font-weight-bold text-white mb-1">Selamat Datang, Admin! 👋</h3>
          <p class="mb-0 text-white-50">Sistem Presensi Pegawai berbasis Liveness Detection & Face Recognition</p>
        </div>
        <form method="GET" action="{{ route('admin.dashboard') }}" class="form-inline bg-white px-3 py-2 rounded-pill shadow-sm">
          <i class="far fa-calendar-alt text-primary mr-2"></i>
          <select name="bulan" class="form-control form-control-sm border-0 font-weight-bold text-primary mr-2" onchange="this.form.submit()">
            @foreach(range(1, 12) as $m)
              @php $mName = \Carbon\Carbon::createFromDate(null, $m, 1)->translatedFormat('F'); @endphp
              <option value="{{ $m }}" {{ $m == $bulan ? 'selected' : '' }}>{{ $mName }}</option>
            @endforeach
          </select>
          <select name="tahun" class="form-control form-control-sm border-0 font-weight-bold text-primary" onchange="this.form.submit()">
            @foreach([2025, 2026, 2027] as $y)
              <option value="{{ $y }}" {{ $y == $tahun ? 'selected' : '' }}>{{ $y }}</option>
            @endforeach
          </select>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
```

#### 7. View Absensi Kamera Admin (`resources/views/admin/absensi/index.blade.php`)
```html
@extends('layouts.admin')

@section('content')
<div class="section-header">
    <h1>Absensi Wajah Admin</h1>
</div>
<div class="card shadow-sm">
    <div class="card-header"><h4 class="mb-0">Kamera Absensi</h4></div>
    <div class="card-body text-center">
        <div class="mb-4">
            <button class="btn btn-success mr-2" onclick="pilihMode('masuk')">Absen Masuk</button>
            <button class="btn btn-danger" onclick="pilihMode('keluar')">Absen Keluar</button>
        </div>
        <div class="position-relative d-inline-block border rounded bg-dark">
            <video id="video" width="420" height="320" autoplay muted playsinline class="d-block" style="transform: scaleX(-1);"></video>
            <canvas id="overlay" class="position-absolute top-0 start-0 w-100 h-100"></canvas>
        </div>
        <div class="mt-4">
            <h5 id="result" class="font-weight-bold mb-2">Silakan pilih mode absensi</h5>
            <p id="gestureInfo" class="text-muted mb-0">Pilih absen masuk atau keluar terlebih dahulu</p>
        </div>
    </div>
</div>
@endsection
```

#### 8. View Master Departemen (`resources/views/admin/departemen/index.blade.php`)
```html
@extends('layouts.admin')

@section('content')
<div class="section-header"><h1>Master Departemen</h1></div>
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header"><h4>Form Departemen</h4></div>
            <div class="card-body">
                <form id="formDepartemen">
                    <input type="hidden" id="id" name="id">
                    <div class="form-group">
                        <label>Nama Departemen</label>
                        <input type="text" class="form-control" name="nama_departemen" id="nama_departemen" required>
                    </div>
                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea class="form-control" name="keterangan" id="keterangan" rows="3"></textarea>
                    </div>
                    <button type="submit" id="btnSave" class="btn btn-primary btn-block">Simpan</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><h4>Data Departemen</h4></div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped w-100" id="tableDepartemen">
                        <thead>
                            <tr>
                                <th width="60">No</th>
                                <th>Nama</th>
                                <th>Keterangan</th>
                                <th width="150" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function(){
    let table = $('#tableDepartemen').DataTable({
        autoWidth: false,
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.departemen.data') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex' },
            { data: 'nama_departemen' },
            { data: 'keterangan' },
            { data: 'aksi', orderable: false, searchable: false, className: 'text-center' }
        ]
    });

    $('#formDepartemen').off('submit').on('submit', function(e){
        e.preventDefault();
        let btn = $('#btnSave');
        btn.prop('disabled', true);
        $.ajax({
            url: "{{ route('admin.departemen.store') }}",
            type: "POST",
            data: $(this).serialize(),
            success: function(){
                $('#formDepartemen')[0].reset();
                $('#id').val('');
                table.ajax.reload();
                btn.prop('disabled', false);
            }
        });
    });

    window.editData = function(id){
        $.get("/admin/departemen/edit/" + id, function(data){
            $('#id').val(data.id);
            $('#nama_departemen').val(data.nama_departemen);
            $('#keterangan').val(data.keterangan);
        });
    };

    window.deleteData = function(id){
        if(confirm("Yakin ingin menghapus data ini?")){
            $.ajax({
                url: "/admin/departemen/delete/" + id,
                type: "DELETE",
                success: function(){ table.ajax.reload(); }
            });
        }
    };
});
</script>
@endpush
```

#### 9. View Master Jadwal Kerja & Shift (`resources/views/admin/jadwal/index.blade.php`)
```html
@extends('layouts.admin')

@section('content')
<div class="section-header"><h1>Master Jadwal Kerja</h1></div>
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header"><h4>Form Jadwal</h4></div>
            <div class="card-body">
                <form id="formJadwal">
                    <input type="hidden" id="id" name="id">
                    <div class="form-group">
                        <label>Nama Shift</label>
                        <input type="text" class="form-control" name="nama_shift" id="nama_shift" required>
                    </div>
                    <div class="form-group">
                        <label>Jam Masuk</label>
                        <input type="time" class="form-control" name="jam_masuk" id="jam_masuk" required>
                    </div>
                    <div class="form-group">
                        <label>Jam Pulang</label>
                        <input type="time" class="form-control" name="jam_pulang" id="jam_pulang" required>
                    </div>
                    <div class="form-group">
                        <label>Toleransi Telat (menit)</label>
                        <input type="number" class="form-control" name="toleransi_telat" id="toleransi_telat">
                    </div>
                    <button type="submit" id="btnSave" class="btn btn-primary btn-block">Simpan</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><h4>Data Jadwal</h4></div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped w-100" id="tableJadwal">
                        <thead>
                            <tr>
                                <th width="40">No</th>
                                <th>Shift</th>
                                <th>Jam Masuk</th>
                                <th>Jam Pulang</th>
                                <th>Toleransi</th>
                                <th width="120" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function(){
    let table = $('#tableJadwal').DataTable({
        autoWidth: false,
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.jadwal.data') }}",
        columns: [
            { data: 'DT_RowIndex' },
            { data: 'nama_shift' },
            { data: 'jam_masuk' },
            { data: 'jam_pulang' },
            { data: 'toleransi_telat' },
            { data: 'aksi', className: 'text-center' }
        ]
    });

    $('#formJadwal').off('submit').on('submit', function(e){
        e.preventDefault();
        let btn = $('#btnSave');
        btn.prop('disabled', true);
        $.ajax({
            url: "{{ route('admin.jadwal.store') }}",
            type: "POST",
            data: $(this).serialize(),
            success: function(){
                $('#formJadwal')[0].reset();
                $('#id').val('');
                table.ajax.reload();
                btn.prop('disabled', false);
            }
        });
    });

    window.editData = function(id){
        $.get("/admin/jadwal/edit/" + id, function(data){
            $('#id').val(data.id);
            $('#nama_shift').val(data.nama_shift);
            $('#jam_masuk').val(data.jam_masuk);
            $('#jam_pulang').val(data.jam_pulang);
            $('#toleransi_telat').val(data.toleransi_telat);
        });
    };

    window.deleteData = function(id){
        if(confirm("Hapus data ini?")){
            $.ajax({
                url: "/admin/jadwal/delete/" + id,
                type: "DELETE",
                success: function(){ table.ajax.reload(); }
            });
        }
    };
});
</script>
@endpush
```

#### 10. View Master Hari Libur Nasional (`resources/views/admin/hari_libur/index.blade.php`)
```html
@extends('layouts.admin')

@section('content')
<div class="section-header"><h1>Master Hari Libur Nasional</h1></div>
<div class="card shadow-sm">
    <div class="card-body">
        <table class="table table-striped">
            <thead><tr><th>No</th><th>Tanggal</th><th>Nama Hari Libur</th><th>Aksi</th></tr></thead>
            <tbody>
                @foreach($hariLibur as $key => $libur)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $libur->tanggal }}</td>
                        <td>{{ $libur->nama_libur }}</td>
                        <td>
                            <form action="{{ route('admin.hari-libur.delete', $libur->id) }}" method="POST" onsubmit="return confirm('Hapus tanggal merah ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
```

#### 11. View Master Data Pegawai (`resources/views/admin/pegawai/index.blade.php`)
```html
@extends('layouts.admin')

@section('content')
<div class="section-header"><h1>Master Data Pegawai</h1></div>
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header"><h4>Form Pegawai</h4></div>
            <div class="card-body">
                <form id="formPegawai">
                    <input type="hidden" id="id" name="id">
                    <div class="form-group">
                        <label>NIK (Nomor Induk Karyawan)</label>
                        <input type="text" class="form-control" name="nik" id="nik" readonly placeholder="Otomatis oleh sistem">
                    </div>
                    <div class="form-group">
                        <label>Nama</label>
                        <input type="text" class="form-control" name="nama" id="nama" required>
                    </div>
                    <div class="form-group">
                        <label>Departemen</label>
                        <select name="departemen_id" id="departemen_id" class="form-control">
                            @foreach($departemen as $d)
                                <option value="{{ $d->id }}">{{ $d->nama_departemen }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Jadwal Kerja</label>
                        <select name="jadwal_kerja_id" id="jadwal_kerja_id" class="form-control">
                            @foreach($jadwal as $j)
                                <option value="{{ $j->id }}">{{ $j->nama_shift }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Jabatan</label>
                        <input type="text" class="form-control" name="jabatan" id="jabatan">
                    </div>
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" class="form-control" name="username" id="username" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" class="form-control" name="password" id="password">
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="1">Aktif</option>
                            <option value="0">Non Aktif</option>
                        </select>
                    </div>
                    <button type="submit" id="btnSave" class="btn btn-primary btn-block">Simpan</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><h4>Data Pegawai</h4></div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped w-100" id="tablePegawai">
                        <thead>
                            <tr><th>No</th><th>NIK</th><th>Nama</th><th>Departemen</th><th>Jadwal</th><th class="text-center">Aksi</th></tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function(){
    let table = $('#tablePegawai').DataTable({
        autoWidth: false,
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.pegawai.data') }}",
        columns: [
            { data: 'DT_RowIndex' },
            { data: 'nik' },
            { data: 'nama' },
            { data: 'departemen' },
            { data: 'jadwal' },
            { data: 'aksi', className: 'text-center' }
        ]
    });

    $('#formPegawai').off('submit').on('submit', function(e){
        e.preventDefault();
        $.ajax({
            url: "{{ route('admin.pegawai.store') }}",
            type: "POST",
            data: $(this).serialize(),
            success: function(){
                $('#formPegawai')[0].reset();
                $('#id').val('');
                table.ajax.reload();
                alert('Data pegawai berhasil disimpan!');
            }
        });
    });

    window.editData = function(id){
        $.get("/admin/pegawai/edit/" + id, function(data){
            $('#id').val(data.id);
            $('#nik').val(data.nik);
            $('#nama').val(data.nama);
            $('#departemen_id').val(data.departemen_id);
            $('#jadwal_kerja_id').val(data.jadwal_kerja_id);
            $('#jabatan').val(data.jabatan);
            $('#username').val(data.username);
            $('#status').val(data.status);
        });
    };

    window.deleteData = function(id){
        if(confirm("Hapus data ini?")){
            $.ajax({
                url: "/admin/pegawai/delete/" + id,
                type: "DELETE",
                success: function(){ table.ajax.reload(); }
            });
        }
    };
});
</script>
@endpush
```

#### 12. View Kelola Dataset Admin (`resources/views/admin/dataset/index.blade.php`)
```html
@extends('layouts.admin')

@section('content')
<div class="section-header"><h1>Kelola Dataset Wajah Admin</h1></div>
<div class="card shadow-sm">
    <div class="card-body">
        <table class="table table-striped">
            <thead><tr><th>No</th><th>Nama Pegawai</th><th>Jumlah Sampel</th><th>Status Ready</th><th>Aksi Reset</th></tr></thead>
            <tbody>
                @foreach($datasetPegawai as $key => $pegawai)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $pegawai->nama }}</td>
                        <td><span class="badge badge-light border">{{ $pegawai->dataset_wajahs_count }} Samples</span></td>
                        <td>
                            @if($pegawai->dataset_wajahs_count >= $minDataset)
                                <span class="badge badge-success">Siap Absensi Kamera</span>
                            @else
                                <span class="badge badge-warning">Belum Siap</span>
                            @endif
                        </td>
                        <td>
                            <form action="{{ route('admin.dataset.delete', $pegawai->id) }}" method="POST" onsubmit="return confirm('Reset seluruh sampel dataset wajah pegawai ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Reset Dataset</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
```

---

### C. MODUL INTERFACE ADMIN LAPORAN (`resources/views/admin/laporan/`)

#### 13. View Laporan Kehadiran (`resources/views/admin/laporan/index.blade.php`)
```html
@extends('layouts.admin')

@section('content')
<div class="section-header"><h1>Laporan Kehadiran Pegawai</h1></div>
<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead><tr><th>No</th><th>Tanggal</th><th>Nama Pegawai</th><th>Departemen</th><th>Jam Masuk</th><th>Jam Pulang</th><th>Status</th></tr></thead>
                <tbody>
                    @forelse($laporanAbsensi as $key => $item)
                        <tr>
                            <td>{{ $laporanAbsensi->firstItem() + $key }}</td>
                            <td>{{ $item->tanggal }}</td>
                            <td>{{ $item->pegawai->nama }}</td>
                            <td>{{ optional($item->pegawai->departemen)->nama_departemen ?? '-' }}</td>
                            <td>{{ $item->jam_masuk ?? '-' }}</td>
                            <td>{{ $item->jam_pulang ?? '-' }}</td>
                            <td><span class="badge badge-{{ $item->status == 'terlambat' ? 'warning' : 'success' }}">{{ $item->status }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted">Data laporan tidak ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
```

#### 14. View Laporan Data Pegawai (`resources/views/admin/laporan/pegawai.blade.php`)
```html
@extends('layouts.admin')

@section('content')
<div class="section-header"><h1>Laporan Data Pegawai</h1></div>
<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead><tr><th>No</th><th>NIK</th><th>Nama</th><th>Departemen</th><th>Shift</th><th>Status Acc</th></tr></thead>
                <tbody>
                    @forelse($pegawai as $key => $item)
                        <tr>
                            <td>{{ $pegawai->firstItem() + $key }}</td>
                            <td>{{ $item->nik }}</td>
                            <td>{{ $item->nama }}</td>
                            <td>{{ optional($item->departemen)->nama_departemen ?? '-' }}</td>
                            <td>{{ optional($item->jadwal)->nama_shift ?? '-' }}</td>
                            <td><span class="badge badge-{{ $item->status ? 'success' : 'danger' }}">{{ $item->status ? 'Aktif' : 'Non-Aktif' }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted">Data pegawai tidak ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
```

#### 15. View Laporan Keterlambatan (`resources/views/admin/laporan/terlambat.blade.php`)
```html
@extends('layouts.admin')

@section('content')
<div class="section-header"><h1>Laporan Keterlambatan Pegawai</h1></div>
<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead><tr><th>No</th><th>Tanggal</th><th>Nama Pegawai</th><th>Jam Masuk</th><th>Alasan Keterlambatan</th></tr></thead>
                <tbody>
                    @forelse($laporanTerlambat as $key => $item)
                        <tr>
                            <td>{{ $laporanTerlambat->firstItem() + $key }}</td>
                            <td>{{ $item->tanggal }}</td>
                            <td>{{ $item->pegawai->nama }}</td>
                            <td>{{ $item->jam_masuk }}</td>
                            <td>{{ $item->alasan_telat ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted">Tidak ada catatan keterlambatan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
```

#### 16. View Laporan Pulang Cepat (`resources/views/admin/laporan/pulang_cepat.blade.php`)
```html
@extends('layouts.admin')

@section('content')
<div class="section-header"><h1>Laporan Pulang Cepat Pegawai</h1></div>
<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead><tr><th>No</th><th>Tanggal</th><th>Nama Pegawai</th><th>Jam Pulang</th><th>Alasan Pulang Cepat</th></tr></thead>
                <tbody>
                    @forelse($laporanPulangCepat as $key => $item)
                        <tr>
                            <td>{{ $laporanPulangCepat->firstItem() + $key }}</td>
                            <td>{{ $item->tanggal }}</td>
                            <td>{{ $item->pegawai->nama }}</td>
                            <td>{{ $item->jam_pulang }}</td>
                            <td>{{ $item->alasan_pulang_awal ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted">Tidak ada catatan pulang cepat.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
```

#### 17. View Laporan Rekap Bulanan (`resources/views/admin/laporan/rekap_bulanan.blade.php`)
```html
@extends('layouts.admin')

@section('content')
<div class="section-header"><h1>Laporan Rekapitulasi Bulanan Presensi</h1></div>
<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead><tr><th>No</th><th>Nama Pegawai</th><th>Total Hadir</th><th>Tepat Waktu</th><th>Terlambat</th><th>Pulang Cepat</th></tr></thead>
                <tbody>
                    @forelse($absensiMatrix as $key => $item)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $item['pegawai']->nama }}</td>
                            <td>{{ $item['total_hadir'] }} Hari</td>
                            <td><span class="badge badge-success">{{ $item['tepat_waktu'] }}</span></td>
                            <td><span class="badge badge-warning">{{ $item['terlambat'] }}</span></td>
                            <td><span class="badge badge-info">{{ $item['pulang_cepat'] }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted">Tidak ada data rekap bulanan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
```
