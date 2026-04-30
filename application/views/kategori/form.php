<div class="container-fluid">

<div class="card shadow-sm border-0">
<div class="card-body">

<h4 class="mb-4">
<?= isset($kategori) ? '✏️ Edit' : '➕ Tambah' ?> Kategori
</h4>

<form method="post" action="<?= $action ?>">

<input type="hidden" 
name="<?= $this->security->get_csrf_token_name(); ?>" 
value="<?= $this->security->get_csrf_hash(); ?>">

<div class="row">

<div class="col-md-6 mb-3">
    <label>Nama Kategori</label>
    <input type="text" name="nama_kategori" class="form-control"
           value="<?= $kategori->nama_kategori ?? '' ?>" required>
</div>

<div class="col-md-6 mb-3">
    <label>Peruntukan Usia</label>
    <input type="text" name="peruntukan_usia" class="form-control"
           value="<?= $kategori->peruntukan_usia ?? '' ?>">
</div>

<div class="col-md-6 mb-3">
    <label>Kelas Obat</label>
    <input type="text" name="kelas_obat" class="form-control"
           value="<?= $kategori->kelas_obat ?? '' ?>">
</div>

</div>

<div class="mt-3">
    <button class="btn btn-success">💾 Simpan</button>
    <a href="<?= base_url('kategori') ?>" class="btn btn-secondary">Batal</a>
</div>

</form>

</div>
</div>

</div>