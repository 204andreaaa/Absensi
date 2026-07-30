@extends('layouts.admin')

@section('content')

<div class="section-header">
<h1>Master Pegawai</h1>
</div>

<div class="row">

<!-- FORM -->
<div class="col-md-4">

<div class="card">
<div class="card-header">
<h4>Form Pegawai</h4>
</div>

<div class="card-body">

<form id="formPegawai">

<input type="hidden" id="id" name="id">

<div class="form-group">
<label>NIK</label>
<input type="text" class="form-control" name="nik" id="nik">
</div>

<div class="form-group">
<label>Nama</label>
<input type="text" class="form-control" name="nama" id="nama">
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
<input type="text" class="form-control" name="username" id="username">
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

<button type="submit" id="btnSave" class="btn btn-primary btn-block">
Simpan
</button>

</form>

</div>
</div>
</div>


<!-- TABLE -->
<div class="col-md-8">

<div class="card">

<div class="card-header">
<h4>Data Pegawai</h4>
</div>

<div class="card-body">
    <div class="table-responsive">
        <table class="table table-bordered table-striped w-100" id="tablePegawai" style="width: 100% !important;">
            <thead>
                <tr>
                    <th width="40">No</th>
                    <th>NIK</th>
                    <th>Nama</th>
                    <th>Departemen</th>
                    <th>Jadwal</th>
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

let table = $('#tablePegawai').DataTable({

autoWidth:false,
processing:true,
serverSide:true,

ajax:"{{ route('admin.pegawai.data') }}",

columns:[
{data:'DT_RowIndex'},
{data:'nik'},
{data:'nama'},
{data:'departemen'},
{data:'jadwal'},
{data:'aksi', className: 'text-center'}
]

});


$('#formPegawai').off('submit').on('submit',function(e){

e.preventDefault();

$.ajax({

url:"{{ route('admin.pegawai.store') }}",

type:"POST",

data:$(this).serialize(),

success:function(){

$('#formPegawai')[0].reset();
$('#id').val('');

table.ajax.reload();

}

});

});


window.editData = function(id){

$.get("/admin/pegawai/edit/"+id,function(data){

$('#id').val(data.id);
$('#nik').val(data.nik);
$('#nama').val(data.nama);
$('#departemen_id').val(data.departemen_id);
$('#jadwal_kerja_id').val(data.jadwal_kerja_id);
$('#jabatan').val(data.jabatan);
$('#username').val(data.username);
$('#status').val(data.status);

});

}


window.deleteData = function(id){

if(confirm("Hapus data ini?")){

$.ajax({

url:"/admin/pegawai/delete/"+id,

type:"DELETE",

success:function(){

table.ajax.reload();

}

});

}

}

});

</script>

@endpush