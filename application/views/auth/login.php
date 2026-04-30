<!DOCTYPE html>
<html lang="id">

<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title><?= isset($title) ? $title : 'Login'; ?></title>

<link href="<?= base_url('assets/vendor/fontawesome-free/css/all.min.css'); ?>" rel="stylesheet">
<link href="<?= base_url('assets/css/sb-admin-2.min.css'); ?>" rel="stylesheet">

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
body {
    background: linear-gradient(135deg, #065f46, #10b981);
}

.btn-success {
    background-color: #059669;
    border: none;
}

.btn-success:hover {
    background-color: #047857;
}

.left-panel {
    background: #ecfdf5;
}

.brand {
    color: #059669;
    font-weight: bold;
}
</style>

</head>

<body>

<div class="container">

<div class="row justify-content-center">

<div class="col-xl-10 col-lg-12 col-md-9">

<div class="card o-hidden border-0 shadow-lg my-5">
<div class="card-body p-0">

<div class="row">

<!-- LEFT -->
<div class="col-lg-6 d-none d-lg-flex align-items-center justify-content-center left-panel">
<div class="text-center p-5">

<h2 class="brand"> MyApotek</h2>

<!-- <img src="https://logowik.com/apotek-hjartat-logo-vector-49373.html" 
     class="img-fluid mt-4" style="max-width:220px;"> -->

<p class="mt-3 text-muted">
Kelola apotek lebih mudah & modern
</p>

</div>
</div>

<!-- RIGHT -->
<div class="col-lg-6">
<div class="p-5">

<div class="text-center">
<h1 class="h4 text-gray-900 mb-4">Login</h1>
</div>

<?php if ($this->session->flashdata('error')): ?>
<div class="alert alert-danger">
<?= $this->session->flashdata('error'); ?>
</div>
<?php endif; ?>

<form action="<?= site_url('auth/process'); ?>" method="post">

<input type="hidden" 
name="<?= $this->security->get_csrf_token_name(); ?>" 
value="<?= $this->security->get_csrf_hash(); ?>" />

<!-- EMAIL -->
<div class="form-group">
<input type="email" name="email" 
class="form-control form-control-user"
placeholder="Masukkan Email..." required>
</div>

<!-- PASSWORD -->
<div class="form-group position-relative">

<input type="password" name="password" id="password"
class="form-control form-control-user"
placeholder="Password" required>

<i class="fa fa-eye position-absolute"
style="right:15px; top:50%; transform:translateY(-50%); cursor:pointer;"
onclick="togglePassword()"></i>

</div>

<!-- BUTTON -->
<button type="submit" class="btn btn-success btn-user btn-block">
Login
</button>

</form>

<hr>

<div class="text-center">
<a class="small" href="<?= base_url('auth/register'); ?>">
Belum punya akun? Daftar
</a>
</div>

</div>
</div>

</div>

</div>
</div>

</div>

</div>

</div>

<!-- JS -->
<script>
function togglePassword(){
let input = document.getElementById('password');
input.type = input.type === "password" ? "text" : "password";
}
</script>

<script src="<?= base_url('assets/vendor/jquery/jquery.min.js'); ?>"></script>
<script src="<?= base_url('assets/vendor/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>

<script>
    <?php if($this->session->flashdata('success')): ?>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '<?= $this->session->flashdata('success'); ?>',
            showConfirmButton: false,
            timer: 2000
        });
    <?php endif; ?>

    <?php if($this->session->flashdata('error')): ?>
        Swal.fire({
            icon: 'error',
            title: 'Opps...',
            text: '<?= $this->session->flashdata('error'); ?>'
        });
    <?php endif; ?>
</script>

</body>
</html>