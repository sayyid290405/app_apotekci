<div class="container-fluid">

<div class="d-flex justify-content-between mb-3">
    <h4>📂 Data Kategori</h4>

    <a href="<?= base_url('kategori/tambah') ?>" class="btn btn-success">
        ➕ Tambah Kategori
    </a>
</div>

<!-- SEARCH -->
<div class="mb-3">
    <input type="text" id="searchKategori" class="form-control" placeholder="🔍 Cari kategori...">
    
</div>

<div class="card shadow-sm border-0">
<div class="card-body">

<table class="table table-hover">
<thead class="table-success">
<tr>
    <th>#</th>
    <th>Nama</th>
    <th>Peruntukan</th>
    <th>Kelas Obat</th>
    <th class="text-center">Aksi</th>
</tr>
</thead>

<tbody id="kategoriTable">

<?php $no=1; foreach($kategori as $k): ?>
<tr>
    <td><?= $no++ ?></td>
    <td><strong><?= $k->nama_kategori ?></strong></td>
    <td><?= $k->peruntukan_usia ?></td>
    <td><?= $k->kelas_obat ?></td>
    <td class="text-center">

        <a href="<?= base_url('kategori/edit/'.$k->id_kategori) ?>" class="btn btn-warning btn-sm">✏️</a>

        <button onclick="hapus('<?= base_url('kategori/hapus/'.$k->id_kategori) ?>')" 
                class="btn btn-danger btn-sm">🗑️</button>

    </td>
</tr>
<?php endforeach; ?>

</tbody>
</table>

</div>
</div>

</div>