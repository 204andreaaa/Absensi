<!DOCTYPE html>
<html>

<head>

<title>Login Pegawai</title>

<link rel="stylesheet" href="{{asset('admin/dist/assets/modules/bootstrap/css/bootstrap.min.css')}}">

</head>

<body class="bg-light">

<div class="container">

<div class="row justify-content-center mt-5">

<div class="col-md-4">

<div class="card">

<div class="card-header text-center">

<h4>Login Pegawai</h4>

</div>

<div class="card-body">

@if($errors->any())

<div class="alert alert-danger">

{{$errors->first()}}

</div>

@endif

<form method="POST" action="{{ route('login.process') }}">

@csrf

<div class="form-group">

<label>Username</label>

<input type="text" name="username" class="form-control">

</div>

<div class="form-group">

<label>Password</label>

<input type="password" name="password" class="form-control">

</div>

<button class="btn btn-primary btn-block">

Login

</button>

</form>

</div>

</div>

</div>

</div>

</div>

</body>

</html>