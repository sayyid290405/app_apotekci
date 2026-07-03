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
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #065f46, #10b981);
}

.card {
    border-radius: 15px;
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
<div class="col-lg-5">

<div class="card shadow-lg p-4">

<div class="text-center mb-4">
<h3 class="brand">Gempas Farma 🩺</h3>
<p class="text-muted">Silahkan login untuk melanjutkan</p>
</div>

<!-- ALERT -->
<?php if ($this->session->flashdata('error')): ?>
<div class="alert alert-danger">
<?= $this->session->flashdata('error'); ?>
</div>
<?php endif; ?>

<!-- FORM -->
<form action="<?= site_url('Auth/process'); ?>" method="post">

<!-- CSRF -->
<input type="hidden"
name="<?= $this->security->get_csrf_token_name(); ?>"
value="<?= $this->security->get_csrf_hash(); ?>" />

<!-- EMAIL -->
<div class="form-group">
<label>Email</label>
<input type="email" name="email"
class="form-control"
placeholder="Masukkan email"
required>
</div>

<!-- PASSWORD -->
<div class="form-group">
<label>Password</label>
<div class="input-group">
<input type="password" name="password" id="password"
class="form-control"
placeholder="Password" required>

<div class="input-group-append">
<span class="input-group-text" onclick="togglePassword()" style="cursor:pointer;">
<i class="fa fa-eye"></i>
</span>
</div>
</div>
</div>

<!-- BUTTON -->
<button type="submit" class="btn btn-success btn-block mt-3">
Login
</button>

</form>

<hr>

<div class="text-center">
<a href="<?= site_url('auth/register'); ?>">
</a>
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
    title: 'Berhasil',
    text: '<?= $this->session->flashdata('success'); ?>',
    timer: 2000,
    showConfirmButton: false
});
<?php endif; ?>
</script>

</body>
</html>