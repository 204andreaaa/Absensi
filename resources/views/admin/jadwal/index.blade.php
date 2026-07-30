@extends('layouts.admin')

@section('content')

<div class="section-header">
<h1>Master Jadwal Kerja</h1>
</div>


<div class="row">

<!-- FORM -->
<div class="col-md-4">

<div class="card">

<div class="card-header">
<h4>Form Jadwal</h4>
</div>

<div class="card-body">

<form id="formJadwal">

<input type="hidden" id="id" name="id">


<div class="form-group">
<label>Nama Shift</label>

<input type="text"
class="form-control"
name="nama_shift"
id="nama_shift"
required>

</div>


<div class="form-group">
<label>Jam Masuk</label>

<input type="time"
class="form-control"
name="jam_masuk"
id="jam_masuk"
required>

</div>


<div class="form-group">
<label>Jam Pulang</label>

<input type="time"
class="form-control"
name="jam_pulang"
id="jam_pulang"
required>

</div>


<div class="form-group">
<label>Toleransi Telat (menit)</label>

<input type="number"
class="form-control"
name="toleransi_telat"
id="toleransi_telat">

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
<h4>Data Jadwal</h4>
</div>

<div class="card-body">
    <div class="table-responsive">
        <table class="table table-bordered table-striped w-100" id="tableJadwal" style="width: 100% !important;">

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

autoWidth:false,
processing:true,
serverSide:true,

ajax:"{{ route('admin.jadwal.data') }}",

columns:[

{data:'DT_RowIndex'},
{data:'nama_shift'},
{data:'jam_masuk'},
{data:'jam_pulang'},
{data:'toleransi_telat'},
{data:'aksi', className: 'text-center'}

]

});



$('#formJadwal').off('submit').on('submit',function(e){

e.preventDefault();

let btn = $('#btnSave');
btn.prop('disabled',true);

$.ajax({

url:"{{ route('admin.jadwal.store') }}",

type:"POST",

data:$(this).serialize(),

success:function(){

$('#formJadwal')[0].reset();
$('#id').val('');

table.ajax.reload();

btn.prop('disabled',false);

}

});

});



window.editData = function(id){

$.get("/admin/jadwal/edit/"+id,function(data){

$('#id').val(data.id);

$('#nama_shift').val(data.nama_shift);

$('#jam_masuk').val(data.jam_masuk);

$('#jam_pulang').val(data.jam_pulang);

$('#toleransi_telat').val(data.toleransi_telat);

});

}



window.deleteData = function(id){

if(confirm("Hapus data ini?")){

$.ajax({

url:"/admin/jadwal/delete/"+id,

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