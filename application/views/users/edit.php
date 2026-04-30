<div class="container-fluid">

<div class="card shadow-sm border-0">
<div class="card-body">

<h4 class="mb-4">✏️ Edit User</h4>

<!-- FLASH MESSAGE -->
<?php if($this->session->flashdata('error')): ?>
    <div class="alert alert-danger">
        <?= $this->session->flashdata('error'); ?>
    </div>
<?php endif; ?>

<form method="post" action="<?= base_url('users/update/'.$user->id_user); ?>">

<!-- CSRF -->
<input type="hidden" 
name="<?= $this->security->get_csrf_token_name(); ?>" 
value="<?= $this->security->get_csrf_hash(); ?>">

<div class="row">

<!-- NAMA -->
<div class="col-md-6 mb-3">
    <label>Username</label>
    <input type="text" name="nama" class="form-control"
           value="<?= $user->nama; ?>" required>
</div>

<!-- EMAIL -->
<div class="col-md-6 mb-3">
    <label>Email</label>
    <input type="email" name="email" class="form-control"
           value="<?= $user->email; ?>" required>
</div>

<!-- PASSWORD (OPSIONAL) -->
<div class="col-md-6 mb-3">
    <label>Password (Kosongkan jika tidak diubah)</label>
    <input type="password" name="password" class="form-control">
</div>

<!-- ROLE -->
<div class="col-md-6 mb-3">
    <label>Role</label>
    <select name="role_id" class="form-control" required>
        <option value="">-- Pilih Role --</option>
        <?php foreach($roles as $r): ?>
            <option value="<?= $r->id_role; ?>"
                <?= ($user->role_id == $r->id_role) ? 'selected' : '' ?>>
                <?= $r->nama_role; ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>

</div>

<div class="mt-3">
    <button class="btn btn-success">💾 Update</button>
    <a href="<?= base_url('users'); ?>" class="btn btn-secondary">Batal</a>
</div>

</form>

</div>
</div>

</div>