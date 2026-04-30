<div class="container-fluid">

<div class="card shadow-sm border-0">
<div class="card-body">

<h4 class="mb-4">
<?= isset($user) ? '✏️ Edit' : '➕ Tambah' ?> User
</h4>

<form method="post" action="<?= $action ?>">

<input type="hidden" 
name="<?= $this->security->get_csrf_token_name(); ?>" 
value="<?= $this->security->get_csrf_hash(); ?>">

<div class="row">

<div class="col-md-6 mb-3">
    <label>Nama</label>
    <input type="text" name="name" class="form-control"
           value="<?= $user->name ?? '' ?>" required>
</div>

<div class="col-md-6 mb-3">
    <label>Email</label>
    <input type="email" name="email" class="form-control"
           value="<?= $user->email ?? '' ?>" required>
</div>

<?php if(!isset($user)): ?>
<div class="col-md-6 mb-3">
    <label>Password</label>
    <input type="password" name="password" class="form-control" required>
</div>
<?php endif; ?>

<div class="col-md-6 mb-3">
    <label>Role</label>
    <select name="role_id" class="form-control">
        <option value="1" <?= (isset($user) && $user->role_id == 1) ? 'selected' : '' ?>>Admin</option>
        <option value="2" <?= (isset($user) && $user->role_id == 2) ? 'selected' : '' ?>>Manager</option>
        <option value="3" <?= (isset($user) && $user->role_id == 3) ? 'selected' : '' ?>>User</option>
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