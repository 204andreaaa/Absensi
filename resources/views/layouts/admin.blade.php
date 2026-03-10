<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<meta name="csrf-token" content="{{ csrf_token() }}">

<title>Admin Panel</title>

<!-- GENERAL CSS -->
<link rel="stylesheet" href="{{asset('admin/dist/assets/modules/bootstrap/css/bootstrap.min.css')}}">
<link rel="stylesheet" href="{{asset('admin/dist/assets/modules/fontawesome/css/all.min.css')}}">

<!-- DATATABLE -->
<link rel="stylesheet" href="{{asset('admin/dist/assets/modules/datatables/datatables.min.css')}}">
<link rel="stylesheet" href="{{asset('admin/dist/assets/modules/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{asset('admin/dist/assets/modules/datatables/Select-1.2.4/css/select.bootstrap4.min.css')}}">

<!-- TEMPLATE -->
<link rel="stylesheet" href="{{asset('admin/dist/assets/css/style.css')}}">
<link rel="stylesheet" href="{{asset('admin/dist/assets/css/components.css')}}">
<link rel="stylesheet" href="{{asset('admin/dist/assets/css/custom.css')}}">

@stack('styles')

</head>


<body>

<div id="app">

<div class="main-wrapper main-wrapper-1">

<div class="navbar-bg"></div>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg main-navbar">

<ul class="navbar-nav mr-3">

<li>
<a href="#" data-toggle="sidebar" class="nav-link nav-link-lg">
<i class="fas fa-bars"></i>
</a>
</li>

</ul>

<ul class="navbar-nav navbar-right">

<li class="dropdown">
<a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">

<img alt="image"
src="{{asset('admin/dist/assets/img/avatar/avatar-1.png')}}"
class="rounded-circle mr-1">

<div class="d-sm-none d-lg-inline-block">Hi, Admin</div>

</a>

<div class="dropdown-menu dropdown-menu-right">

<a href="#" class="dropdown-item has-icon">
<i class="far fa-user"></i> Profile
</a>

<div class="dropdown-divider"></div>

<form method="POST" action="{{ route('logout') }}">
  @csrf

  <button class="dropdown-item has-icon text-danger">

  <i class="fas fa-sign-out-alt"></i> Logout

  </button>

</form>

</div>

</li>

</ul>

</nav>


<!-- SIDEBAR -->
<div class="main-sidebar sidebar-style-2">

<aside id="sidebar-wrapper">

<div class="sidebar-brand">
<a href="#">Face Attendance</a>
</div>

<ul class="sidebar-menu">

<li class="menu-header">Dashboard</li>

<li class="{{ request()->is('admin/dashboard') ? 'active' : '' }}">
<a class="nav-link" href="{{ route('admin.dashboard') }}">
<i class="fas fa-home"></i>
<span>Beranda</span>
</a>
</li>

<li class="menu-header">MASTER</li>

<li class="{{ request()->is('admin/departemen*') ? 'active' : '' }}">
  <a class="nav-link" href="{{ route('admin.departemen.index') }}">
    <i class="fas fa-building"></i>
    <span>Departemen</span>
  </a>
</li>

<li class="{{ request()->is('admin/jadwal*') ? 'active' : '' }}">
  <a class="nav-link" href="{{ route('admin.jadwal.index') }}">
    <i class="fas fa-clock"></i>
    <span>Jadwal Kerja</span>
  </a>
</li>

<li class="{{ request()->is('admin/pegawai*') ? 'active' : '' }}">
  <a class="nav-link" href="{{ route('admin.pegawai.index') }}">
    <i class="fas fa-users"></i>
    <span>Pegawai</span>
  </a>
</li>

<li class="{{ request()->is('admin/absensi*') ? 'active' : '' }}">
<a class="nav-link" href="{{ route('admin.absensi.index') }}">
<i class="fas fa-users"></i>
<span>Absen</span>
</a>
</li>

<li class="{{ request()->is('admin/dataset*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('admin.dataset.index') }}">
        <i class="fas fa-database"></i>
        <span>Dataset Wajah</span>
    </a>
</li>

<li class="menu-header">LAPORAN</li>

<li>
<a class="nav-link" href="#">
<i class="fas fa-file"></i>
<span>Laporan Kehadiran</span>
</a>
</li>

</ul>

</aside>

</div>


<!-- MAIN CONTENT -->
<div class="main-content">

<section class="section">

<div class="section-body">

@yield('content')

</div>

</section>

</div>


<!-- FOOTER -->
<footer class="main-footer">

<div class="footer-left">
Copyright © {{date('Y')}} Face Recognition
</div>

</footer>

</div>

</div>


<!-- JS -->

<script src="{{asset('admin/dist/assets/modules/jquery.min.js')}}"></script>
<script src="{{asset('admin/dist/assets/modules/popper.js')}}"></script>
<script src="{{asset('admin/dist/assets/modules/bootstrap/js/bootstrap.min.js')}}"></script>
<script src="{{asset('admin/dist/assets/modules/nicescroll/jquery.nicescroll.min.js')}}"></script>

<script src="{{asset('admin/dist/assets/js/stisla.js')}}"></script>

<!-- DATATABLE -->
<script src="{{asset('admin/dist/assets/modules/datatables/datatables.min.js')}}"></script>
<script src="{{asset('admin/dist/assets/modules/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('admin/dist/assets/modules/datatables/Select-1.2.4/js/dataTables.select.min.js')}}"></script>

<!-- SWEET ALERT -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<!-- TEMPLATE -->
<script src="{{asset('admin/dist/assets/js/scripts.js')}}"></script>
<script src="{{asset('admin/dist/assets/js/custom.js')}}"></script>


<!-- AJAX SETUP -->
<script>

$.ajaxSetup({

headers: {

'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

}

});


$('#formDepartemen').submit(function(e){

e.preventDefault();

console.log("form jalan"); // cek ini

$.ajax({

url:"{{ route('admin.departemen.store') }}",

type:"POST",

data:$(this).serialize(),

success:function(){

alert("data tersimpan");

$('#formDepartemen')[0].reset();

table.ajax.reload();

}

});

});

</script>


@stack('scripts')


</body>
</html>