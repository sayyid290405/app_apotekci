<div class="container">

<h4>📄 Preview Resep</h4>

<p><b>Pasien:</b> <?= $resep->pasien_nama ?></p>
<p><b>Dokter:</b> <?= $resep->dokter ?></p>
<p><b>Tanggal:</b> <?= $resep->tanggal ?></p>
<p>
    <b>Status:</b> 
    <span class="badge bg-<?= $resep->status == 'verified' ? 'success':'warning' ?>">
        <?= ucfirst($resep->status) ?>
    </span>
</p>

<table class="table table-bordered">
<thead>
<tr>
    <th>Obat</th>
    <th>Satuan</th> <th>Harga</th>  <th>Qty</th>
    <th>Dosis</th>
    <th>Qty</th>
</tr>
</thead>
<tbody>
<?php foreach($detail as $d): ?>
<tr>
    <td><?= $d->nama_produk ?></td>
    <td><?= $d->satuan ?></td> <td>Rp <?= number_format($d->harga, 0, ',', '.') ?></td> <td><?= $d->jumlah ?></td>
    <td><?= $d->dosis ?></td>
    <td><?= $d->jumlah ?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>

<div class="d-flex gap-2">

    <!-- ⬅ Kembali -->
    <a href="<?= base_url('resep') ?>" class="btn btn-secondary">
        ⬅ Kembali
    </a>

    <!-- ✔️ Verifikasi -->
    <?php if($resep->status != 'verified'): ?>
    <a href="<?= base_url('resep/verifikasi/'.$resep->id_resep) ?>" 
       class="btn btn-success">
        ✔️ Verifikasi
    </a>
    <?php endif; ?>

    <!-- 💳 Langsung Bayar -->
    <a href="<?= base_url('kasir?resep='.$resep->id_resep) ?>" 
       class="btn btn-primary">
        💳 Langsung Bayar
    </a>

</div>

</div>