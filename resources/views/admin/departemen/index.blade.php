@extends('layouts.admin')

@section('content')

<div class="section-header">
    <h1>Master Departemen</h1>
</div>


<div class="row">

    <!-- =======================
        FORM INPUT
    ======================== -->
    <div class="col-md-4">

        <div class="card">

            <div class="card-header">
                <h4>Form Departemen</h4>
            </div>

            <div class="card-body">

                <form id="formDepartemen">

                    <input type="hidden" id="id" name="id">


                    <!-- Nama Departemen -->
                    <div class="form-group">
                        <label>Nama Departemen</label>

                        <input
                            type="text"
                            class="form-control"
                            name="nama_departemen"
                            id="nama_departemen"
                            required
                        >
                    </div>


                    <!-- Keterangan -->
                    <div class="form-group">
                        <label>Keterangan</label>

                        <textarea
                            class="form-control"
                            name="keterangan"
                            id="keterangan"
                            rows="3"
                        ></textarea>
                    </div>


                    <!-- Button -->
                    <button
                        type="submit"
                        id="btnSave"
                        class="btn btn-primary btn-block"
                    >
                        Simpan
                    </button>

                </form>

            </div>

        </div>

    </div>



    <!-- =======================
        TABLE DATA
    ======================== -->
    <div class="col-md-8">

        <div class="card">

            <div class="card-header">
                <h4>Data Departemen</h4>
            </div>

            <div class="card-body">

                <table
                    class="table table-bordered table-striped"
                    id="tableDepartemen"
                >

                    <thead>
                        <tr>
                            <th width="60">No</th>
                            <th>Nama</th>
                            <th>Keterangan</th>
                            <th width="150">Aksi</th>
                        </tr>
                    </thead>

                </table>

            </div>

        </div>

    </div>

</div>

@endsection




@push('scripts')

<script>

$(document).ready(function(){

    /*
    =====================================
    DATATABLE LOAD
    =====================================
    */

    let table = $('#tableDepartemen').DataTable({

        processing: true,
        serverSide: true,

        ajax: "{{ route('admin.departemen.data') }}",

        columns: [

            {
                data: 'DT_RowIndex',
                name: 'DT_RowIndex'
            },

            {
                data: 'nama_departemen'
            },

            {
                data: 'keterangan'
            },

            {
                data: 'aksi',
                orderable: false,
                searchable: false
            }

        ]

    });



    /*
    =====================================
    SIMPAN DATA (ADD / UPDATE)
    =====================================
    */

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



    /*
    =====================================
    EDIT DATA
    =====================================
    */

    window.editData = function(id){

        $.get("/admin/departemen/edit/" + id, function(data){

            $('#id').val(data.id);
            $('#nama_departemen').val(data.nama_departemen);
            $('#keterangan').val(data.keterangan);

        });

    }



    /*
    =====================================
    DELETE DATA
    =====================================
    */

    window.deleteData = function(id){

        if(confirm("Yakin ingin menghapus data ini?")){

            $.ajax({

                url: "/admin/departemen/delete/" + id,

                type: "DELETE",

                success: function(){

                    table.ajax.reload();

                }

            });

        }

    }


});

</script>

@endpush