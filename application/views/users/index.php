<div class="container-fluid">
    <div class="card shadow-sm border-0">
        <div class="card-body">

            <h4 class="mb-4">👤 Manajemen Users</h4>

            <?php if($this->session->flashdata('success')): ?>
                <div class="alert alert-success"><?= $this->session->flashdata('success'); ?></div>
            <?php endif; ?>

            <div class="d-flex justify-content-between mb-3">
                <a href="<?= base_url('users/create'); ?>" class="btn btn-primary">
                    ➕ Tambah User
                </a>
                <!-- <input type="text" id="searchUser" class="form-control w-25" placeholder="🔍 Cari user..."> -->
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role ID</th>
                            <th>Role Name</th>
                            <th>Created At</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tableUsers">
                        <?php $no=1; foreach($users as $u): ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= $u->nama; ?></td> 
                            <td><?= $u->email; ?></td>
                            <td><span class="badge bg-secondary"><?= $u->role_id; ?></span></td>
                            <td><?= $u->nama_role; ?></td>
                            <td><?= date('d M Y', strtotime($u->created_at)); ?></td>
                            <td class="text-center">
                                <a href="<?= base_url('users/edit/'.$u->id_user); ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="<?= base_url('users/delete/'.$u->id_user); ?>" 
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Yakin ingin menghapus user ini?')">Hapus</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<script>
    const baseUrl = "<?= base_url(); ?>";
</script>
<script src="<?= base_url('assets/js/users.js'); ?>"></script>