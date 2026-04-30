<div class="container-fluid">

<div class="card shadow-sm border-0">
<div class="card-body">

<h4 class="mb-4">👤 Manajemen Users</h4>

<?php if($this->session->flashdata('success')): ?>
    <div class="alert alert-success"><?= $this->session->flashdata('success'); ?></div>
<?php endif; ?>

<a href="<?= base_url('users/create'); ?>" class="btn btn-primary mb-3">
    ➕ Tambah User
</a>

<input type="text" id="searchUser" class="form-control mb-3" placeholder="🔍 Cari user...">

<table class="table table-bordered">
    <thead>
        <tr>
            <th>No</th>
            <th>username</th>
            <th>email</th>
            <th>Role id</th>
            <th>Role</th>
            <th>Createat</th>
        </tr>
    </thead>
    <tbody id="tableUsers">
        <?php $no=1; foreach($users as $u): ?>
        <tr>
<td><?= $u->nama; ?></td>
<td><?= $u->email; ?></td>

<td>
    <a href="<?= base_url('users/edit/'.$u->id_user); ?>" class="btn btn-warning btn-sm">Edit</a>
    <a href="<?= base_url('users/delete/'.$u->id_user); ?>" 
       class="btn btn-danger btn-sm"
       onclick="return confirm('Yakin hapus?')">Hapus</a>
</td>
<th><?= $u->role_id; ?></th>
<td><?= $u->nama_role; ?></td>
<th><?= $u->created_at; ?></th>

        </tr>
        
        <?php endforeach; ?>
    </tbody>
</table>

</div>
</div>

</div>

<script>
const baseUrl = "<?= base_url(); ?>";
</script>
<script src="<?= base_url('assets/js/users.js'); ?>"></script>