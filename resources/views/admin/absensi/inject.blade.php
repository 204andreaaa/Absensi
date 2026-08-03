@extends('layouts.admin')

@section('content')
<div class="section-header">
    <h1><i class="fas fa-magic text-primary mr-2"></i> Inject Presensi & Profil Pegawai</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
        <div class="breadcrumb-item">Inject Presensi & Profil</div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8 col-md-12 mx-auto">

        <!-- CARD 1: FORM UPDATE FOTO PROFIL PEGAWAI -->
        <div class="card card-info shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h4 class="text-info"><i class="fas fa-id-badge mr-2"></i> Form Update Foto Profil Pegawai</h4>
            </div>
            <div class="card-body">
                <form id="formUpdateFotoProfil" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-group mb-0">
                                <label class="font-weight-bold">Pilih Pegawai <span class="text-danger">*</span></label>
                                <select name="pegawai_id" id="profil_pegawai_id" class="form-control select2" required style="width: 100%;">
                                    <option value="">-- Pilih Pegawai --</option>
                                    @foreach($pegawais as $p)
                                        <option value="{{ $p->id }}">{{ $p->nama }} (NIK: {{ $p->nik }}) - {{ $p->departemen->nama_departemen ?? '-' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="form-group mb-0">
                                <label class="font-weight-bold">Pilih Foto Profil Baru <span class="text-danger">*</span></label>
                                <div class="custom-file">
                                    <input type="file" name="foto_profil" id="foto_profil_only" class="custom-file-input" accept="image/jpeg,image/png,image/jpg" required>
                                    <label class="custom-file-label" for="foto_profil_only">Pilih Foto Profil...</label>
                                </div>
                                <small class="form-text text-muted" id="previewProfilOnlyText">Pilih 1 gambar untuk foto profil pegawai di sistem.</small>
                            </div>
                        </div>
                    </div>

                    <button type="submit" id="btnSubmitProfilOnly" class="btn btn-info btn-block shadow-sm font-weight-bold mt-2">
                        <i class="fas fa-save mr-2"></i> Update Foto Profil Pegawai
                    </button>
                </form>
            </div>
        </div>

        <!-- CARD 2: FORM INJEK PRESENSI BULK -->
        <div class="card card-primary shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h4 class="text-primary"><i class="fas fa-cloud-upload-alt mr-2"></i> Form Injek Foto Presensi Pegawai (Bulk)</h4>
            </div>

            <div class="card-body">
                <div class="alert alert-info alert-has-icon mb-4">
                    <div class="alert-icon"><i class="fas fa-info-circle"></i></div>
                    <div class="alert-body">
                        <div class="alert-title">Petunjuk Penggunaan Fitur Injek Presensi:</div>
                        Upload beberapa pasang foto presensi. Sistem akan secara otomatis mengompresi foto dan menginjek presensi pegawai sesuai dengan jumlah pasang foto yang kamu unggah ke tanggal-tanggal kerja di bulan yang kamu pilih!
                    </div>
                </div>

                <form id="formInjectPresensi" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <!-- Pegawai -->
                        <div class="col-md-12 mb-3">
                            <div class="form-group mb-0">
                                <label class="font-weight-bold">1. Pilih Pegawai <span class="text-danger">*</span></label>
                                <select name="pegawai_id" id="pegawai_id" class="form-control select2" required style="width: 100%;">
                                    <option value="">-- Pilih Pegawai --</option>
                                    @foreach($pegawais as $p)
                                        <option value="{{ $p->id }}">{{ $p->nama }} (NIK: {{ $p->nik }}) - {{ $p->departemen->nama_departemen ?? '-' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Bulan & Tahun -->
                        <div class="col-md-6 mb-3">
                            <div class="form-group mb-0">
                                <label class="font-weight-bold">2. Pilih Bulan <span class="text-danger">*</span></label>
                                <select name="bulan" id="bulan" class="form-control" required>
                                    @foreach(range(1, 12) as $m)
                                        @php $monthName = \Carbon\Carbon::createFromDate(null, $m, 1)->translatedFormat('F'); @endphp
                                        <option value="{{ $m }}" {{ $m == 7 ? 'selected' : '' }}>{{ $monthName }} (Bulan {{ $m }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="form-group mb-0">
                                <label class="font-weight-bold">3. Pilih Tahun <span class="text-danger">*</span></label>
                                <select name="tahun" id="tahun" class="form-control" required>
                                    <option value="2026" selected>2026</option>
                                    <option value="2025">2025</option>
                                </select>
                            </div>
                        </div>

                        <!-- Mode Tanggal -->
                        <div class="col-md-12 mb-3">
                            <div class="form-group mb-0">
                                <label class="font-weight-bold">4. Mode Pemilihan Tanggal Kerja <span class="text-danger">*</span></label>
                                <div class="selectgroup w-100">
                                    <label class="selectgroup-item">
                                        <input type="radio" name="mode_tanggal" value="random" class="selectgroup-input" checked>
                                        <span class="selectgroup-button"><i class="fas fa-random mr-1"></i> Random (Acak Hari Kerja)</span>
                                    </label>
                                    <label class="selectgroup-item">
                                        <input type="radio" name="mode_tanggal" value="urut" class="selectgroup-input">
                                        <span class="selectgroup-button"><i class="fas fa-sort-numeric-down mr-1"></i> Urut Hari Kerja (Awal Bulan)</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Upload Foto Masuk -->
                        <div class="col-md-6 mb-3">
                            <div class="form-group mb-0">
                                <label class="font-weight-bold">5. Upload Foto Masuk (Bisa Multiple) <span class="text-danger">*</span></label>
                                <div class="custom-file">
                                    <input type="file" name="foto_masuk[]" id="foto_masuk" class="custom-file-input" multiple accept="image/jpeg,image/png,image/jpg" required>
                                    <label class="custom-file-label" for="foto_masuk">Pilih Foto Masuk...</label>
                                </div>
                                <small class="form-text text-muted" id="previewMasukCount">Pilih 1 atau beberapa foto sekaligus.</small>
                            </div>
                        </div>

                        <!-- Upload Foto Pulang -->
                        <div class="col-md-6 mb-3">
                            <div class="form-group mb-0">
                                <label class="font-weight-bold">6. Upload Foto Pulang (Opsional / Multiple)</label>
                                <div class="custom-file">
                                    <input type="file" name="foto_pulang[]" id="foto_pulang" class="custom-file-input" multiple accept="image/jpeg,image/png,image/jpg">
                                    <label class="custom-file-label" for="foto_pulang">Pilih Foto Pulang...</label>
                                </div>
                                <small class="form-text text-muted" id="previewPulangCount">Jika kosong, foto masuk akan dipakai otomatis.</small>
                            </div>
                        </div>

                        <!-- Random Status Checkbox -->
                        <div class="col-md-12 mb-3">
                            <div class="custom-control custom-checkbox bg-light p-3 rounded border">
                                <input type="checkbox" class="custom-control-input" id="random_status" name="random_status" value="1" checked>
                                <label class="custom-control-label font-weight-bold text-dark" for="random_status">
                                    <i class="fas fa-dice text-warning mr-1"></i> Sertakan Alasan Terlambat & Pulang Cepat Secara Alami (~12% Peluang Acak)
                                </label>
                                <small class="form-text text-muted pl-4">Jika dicentang, beberapa tanggal presensi akan secara alami berisi alasan keterlambatan atau alasan pulang awal.</small>
                            </div>
                        </div>

                        <!-- Reset Existing Month Attendance Checkbox -->
                        <div class="col-md-12 mb-3">
                            <div class="custom-control custom-checkbox bg-light p-3 rounded border">
                                <input type="checkbox" class="custom-control-input" id="reset_bulan_ini" name="reset_bulan_ini" value="1">
                                <label class="custom-control-label font-weight-bold text-dark" for="reset_bulan_ini">
                                    <i class="fas fa-trash-alt text-danger mr-1"></i> Bersihkan (Reset) Presensi Pegawai Ini di Bulan yang Dipilih Sebelum Injek Baru
                                </label>
                                <small class="form-text text-muted pl-4">Jika dicentang, presensi lama pegawai ini di bulan yang dipilih akan dihapus bersih terlebih dahulu agar tidak menumpuk.</small>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Realtime Summary Preview Badge -->
                    <div class="p-3 bg-light rounded mb-4 text-center border">
                        <span class="badge badge-primary px-3 py-2 text-wrap" style="font-size: 14px;" id="badgeSummary">
                            <i class="fas fa-info-circle mr-1"></i> Pilih foto masuk untuk melihat ringkasan injek presensi.
                        </span>
                    </div>

                    <button type="submit" id="btnSubmitInject" class="btn btn-primary btn-block btn-lg shadow-sm font-weight-bold">
                        <i class="fas fa-paper-plane mr-2"></i> Proses Injek Presensi Foto Sekarang
                    </button>
                </form>
            </div>
        </div>

        <!-- CARD 3: DANGER ZONE - RESET TOTAL ABSENSI -->
        <div class="card card-danger shadow-sm">
            <div class="card-header bg-white py-3">
                <h4 class="text-danger"><i class="fas fa-exclamation-triangle mr-2"></i> Danger Zone - Reset Total Data Presensi</h4>
            </div>
            <div class="card-body text-center">
                <p class="text-muted mb-3">Tombol ini akan menghapus total seluruh data presensi di database dan membersihkan seluruh file foto di storage server.</p>
                <button type="button" id="btnResetTotalAbsensi" class="btn btn-outline-danger btn-block font-weight-bold">
                    <i class="fas fa-trash-alt mr-2"></i> Reset Total Seluruh Data Absensi & Hapus Semua Foto (Kosongkan Total)
                </button>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function(){

    function compressImageFile(file, maxWidth = 800, quality = 0.6) {
        return new Promise((resolve) => {
            if (!file.type.startsWith('image/')) {
                resolve(file);
                return;
            }
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = new Image();
                img.onload = function() {
                    let w = img.width;
                    let h = img.height;
                    if (w > maxWidth) {
                        h = Math.round((h * maxWidth) / w);
                        w = maxWidth;
                    }
                    const canvas = document.createElement('canvas');
                    canvas.width = w;
                    canvas.height = h;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, w, h);
                    canvas.toBlob(
                        (blob) => {
                            if (blob) {
                                const newFile = new File([blob], file.name, {
                                    type: 'image/jpeg',
                                    lastModified: Date.now()
                                });
                                resolve(newFile);
                            } else {
                                resolve(file);
                            }
                        },
                        'image/jpeg',
                        quality
                    );
                };
                img.onerror = () => resolve(file);
                img.src = e.target.result;
            };
            reader.onerror = () => resolve(file);
            reader.readAsDataURL(file);
        });
    }

    // Custom File Label Updates
    $('#foto_profil_only').on('change', function(){
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').html(fileName || 'Pilih Foto Profil...');
    });

    $('#foto_masuk').on('change', function(){
        let count = this.files.length;
        $(this).next('.custom-file-label').html(count > 0 ? count + ' foto terpilih' : 'Pilih Foto Masuk...');
    });

    $('#foto_pulang').on('change', function(){
        let count = this.files.length;
        $(this).next('.custom-file-label').html(count > 0 ? count + ' foto terpilih' : 'Pilih Foto Pulang...');
    });

    function updateSummary() {
        let masukCount = $('#foto_masuk')[0].files.length;
        let pulangCount = $('#foto_pulang')[0].files.length;
        let bulanText = $('#bulan option:selected').text();
        let tahunText = $('#tahun').val();
        let modeText = $('input[name="mode_tanggal"]:checked').val() === 'random' ? 'ACAK (Random)' : 'URUT';

        if (masukCount > 0) {
            $('#previewMasukCount').text('Terpilih: ' + masukCount + ' file foto masuk.');
            $('#badgeSummary').removeClass('badge-primary badge-secondary').addClass('badge-success')
                .html('<i class="fas fa-check-circle mr-1"></i> Siap menginjek <strong>' + masukCount + ' hari presensi</strong> secara <strong>' + modeText + '</strong> di ' + bulanText + ' ' + tahunText + '!');
        } else {
            $('#previewMasukCount').text('Pilih 1 atau beberapa foto sekaligus.');
            $('#badgeSummary').removeClass('badge-success badge-secondary').addClass('badge-primary')
                .html('<i class="fas fa-info-circle mr-1"></i> Silakan pilih foto masuk untuk mulai injek.');
        }

        if (pulangCount > 0) {
            $('#previewPulangCount').text('Terpilih: ' + pulangCount + ' file foto pulang.');
        } else {
            $('#previewPulangCount').text('Jika kosong, foto masuk akan dipakai otomatis.');
        }
    }

    $('#foto_masuk, #foto_pulang, #pegawai_id, #bulan, #tahun, input[name="mode_tanggal"]').on('change', function(){
        updateSummary();
    });

    // SUBMIT HANDLER: FORM UPDATE FOTO PROFIL PEGAWAI
    $('#formUpdateFotoProfil').on('submit', async function(e){
        e.preventDefault();

        let btn = $('#btnSubmitProfilOnly');
        let originalText = btn.html();

        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Mengompresi & Memperbarui Profil...');

        let formData = new FormData();
        formData.append('_token', $('input[name="_token"]').val());
        formData.append('pegawai_id', $('#profil_pegawai_id').val());

        let profilFiles = $('#foto_profil_only')[0].files;
        if (profilFiles.length > 0) {
            let compressedProfil = await compressImageFile(profilFiles[0]);
            formData.append('foto_profil', compressedProfil);
        }

        $.ajax({
            url: "{{ route('admin.absensi.inject-update-profile') }}",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function(response){
                btn.prop('disabled', false).html(originalText);
                if (response.success) {
                    alert('SUKSES! 🎉\n\n' + response.message);
                    $('#formUpdateFotoProfil')[0].reset();
                    $('#foto_profil_only').next('.custom-file-label').html('Pilih Foto Profil...');
                } else {
                    alert('Gagal: ' + response.message);
                }
            },
            error: function(xhr){
                btn.prop('disabled', false).html(originalText);
                if (xhr.status === 422 && xhr.responseJSON) {
                    let errors = xhr.responseJSON.errors || {};
                    let msg = xhr.responseJSON.message || '';
                    if (errors) {
                        $.each(errors, function(k, v){ msg += '\n• ' + v[0]; });
                    }
                    alert('Gagal Update Profil:\n' + msg);
                } else {
                    alert('Terjadi kesalahan server (' + xhr.status + '). Silakan coba lagi.');
                }
            }
        });
    });

    // SUBMIT HANDLER: FORM INJEK PRESENSI BULK
    $('#formInjectPresensi').on('submit', async function(e){
        e.preventDefault();

        let btn = $('#btnSubmitInject');
        let originalText = btn.html();

        btn.prop('disabled', true).html('<i class="fas fa-compress-arrows-alt fa-spin mr-2"></i> Mengompresi Foto Menghemat Kuota Upload...');

        let formData = new FormData();
        formData.append('_token', $('input[name="_token"]').val());
        formData.append('pegawai_id', $('#pegawai_id').val());
        formData.append('bulan', $('#bulan').val());
        formData.append('tahun', $('#tahun').val());
        formData.append('mode_tanggal', $('input[name="mode_tanggal"]:checked').val());

        if ($('#random_status').is(':checked')) {
            formData.append('random_status', '1');
        }

        if ($('#reset_bulan_ini').is(':checked')) {
            formData.append('reset_bulan_ini', '1');
        }

        let masukFiles = $('#foto_masuk')[0].files;
        let pulangFiles = $('#foto_pulang')[0].files;

        // Compress foto masuk
        for (let i = 0; i < masukFiles.length; i++) {
            btn.html('<i class="fas fa-spinner fa-spin mr-2"></i> Mengompresi Foto Masuk (' + (i + 1) + '/' + masukFiles.length + ')...');
            let compressed = await compressImageFile(masukFiles[i]);
            formData.append('foto_masuk[]', compressed);
        }

        // Compress foto pulang
        for (let j = 0; j < pulangFiles.length; j++) {
            btn.html('<i class="fas fa-spinner fa-spin mr-2"></i> Mengompresi Foto Pulang (' + (j + 1) + '/' + pulangFiles.length + ')...');
            let compressed = await compressImageFile(pulangFiles[j]);
            formData.append('foto_pulang[]', compressed);
        }

        btn.html('<i class="fas fa-cloud-upload-alt fa-spin mr-2"></i> Mengunggah & Menginjek Presensi...');

        $.ajax({
            url: "{{ route('admin.absensi.inject-process') }}",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function(response){
                btn.prop('disabled', false).html(originalText);
                if (response.success) {
                    alert('SUKSES! 🎉\n\n' + response.message);
                    $('#formInjectPresensi')[0].reset();
                    $('#foto_masuk').next('.custom-file-label').html('Pilih Foto Masuk...');
                    $('#foto_pulang').next('.custom-file-label').html('Pilih Foto Pulang...');
                    updateSummary();
                } else {
                    alert('Gagal: ' + response.message);
                }
            },
            error: function(xhr){
                btn.prop('disabled', false).html(originalText);
                if (xhr.status === 422 && xhr.responseJSON) {
                    let errors = xhr.responseJSON.errors || {};
                    let msg = xhr.responseJSON.message || '';
                    if (errors) {
                        $.each(errors, function(k, v){ msg += '\n• ' + v[0]; });
                    }
                    alert('Gagal Menginjek Presensi:\n' + msg);
                } else if (xhr.status === 413) {
                    alert('Ukuran File Terlalu Besar (HTTP 413). Coba kurangi jumlah foto yang diunggah secara bersamaan.');
                } else {
                    alert('Terjadi kesalahan server (' + xhr.status + '). Silakan coba lagi.');
                }
            }
        });
    });

    // HANDLER: RESET TOTAL ABSENSI
    $('#btnResetTotalAbsensi').on('click', function(){
        if (confirm("⚠️ PERINGATAN BERSAMA!\n\nApakah Anda YAKIN ingin MENGHAPUS TOTAL SELURUH DATA PRESENSI dan SELURUH FILE FOTO di storage server?\n\nData yang dihapus tidak bisa dikembalikan.")) {
            let btn = $(this);
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Membersihkan Total Absensi & Storage Foto...');

            $.ajax({
                url: "{{ route('admin.absensi.reset-total') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(response){
                    btn.prop('disabled', false).html('<i class="fas fa-trash-alt mr-2"></i> Reset Total Seluruh Data Absensi & Hapus Semua Foto (Kosongkan Total)');
                    if (response.success) {
                        alert('BERSIH TOTAL! 🧹\n\n' + response.message);
                    }
                },
                error: function(xhr){
                    btn.prop('disabled', false).html('<i class="fas fa-trash-alt mr-2"></i> Reset Total Seluruh Data Absensi & Hapus Semua Foto (Kosongkan Total)');
                    alert('Gagal membersihkan absensi: ' + xhr.status);
                }
            });
        }
    });

});
</script>
@endpush
