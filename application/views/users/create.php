<div class="container-fluid">

<div class="card shadow-sm border-0">
<div class="card-body">

<h4 class="mb-4">➕ Tambah User</h4>

<!-- ERROR -->
<?php if($this->session->flashdata('error')): ?>
    <div class="alert alert-danger">
        <?= $this->session->flashdata('error'); ?>
    </div>
<?php endif; ?>

<form method="post" action="<?= base_url('users/store'); ?>">

<input type="hidden" 
name="<?= $this->security->get_csrf_token_name(); ?>" 
value="<?= $this->security->get_csrf_hash(); ?>">

<div class="row">

<!-- NAMA -->
<div class="col-md-6 mb-3">
    <label>Username</label>
    <input type="text" name="nama" class="form-control" required>
</div>

<!-- EMAIL -->
<div class="col-md-6 mb-3">
    <label>Email</label>
    <input type="email" name="email" class="form-control" required>
</div>

<!-- PASSWORD -->
<div class="col-md-6 mb-3">
    <label>Password</label>
    <input type="password" name="password" class="form-control" required>
</div>

<!-- ROLE -->
<div class="col-md-6 mb-3">
    <label>Role</label>
<select name="role_id" class="form-control" required>
    <option value="">-- Pilih Role --</option>
    <?php foreach($roles as $r): ?>
        <option value="<?= $r->id_role; ?>">
            <?= $r->nama_role; ?>
        </option>
    <?php endforeach; ?>
</select>
</div>

</div>

<div class="mt-3">
    <button class="btn btn-success">💾 Simpan</button>
    <a href="<?= base_url('users'); ?>" class="btn btn-secondary">Batal</a>
</div>

</form>

</div>
</div>

</div>