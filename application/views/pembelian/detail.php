<div class="container">

<h4>📄 Detail Pembelian</h4>

<p><b>Kode:</b> <?= $pembelian->kode_pembelian ?></p>
<p><b>Supplier:</b> <?= $pembelian->nama_supplier ?></p>
<p><b>Tanggal:</b> <?= $pembelian->tanggal ?></p>

<table class="table table-bordered">
<thead>
<tr>
    <th>Produk</th>
    <th>Harga</th>
    <th>Qty</th>
    <th>Subtotal</th>
</tr>
</thead>
<tbody>
<?php foreach($detail as $d): ?>
<tr>
    <td><?= $d->nama_produk ?></td>
    <td>Rp <?= number_format($d->harga,0,',','.') ?></td>
    <td><?= $d->jumlah ?></td>
    <td>Rp <?= number_format($d->subtotal,0,',','.') ?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>

<h4>Total: Rp <?= number_format($pembelian->total,0,',','.') ?></h4>

<div class="mt-3 d-flex gap-2">

    <a href="<?= base_url('pembelian') ?>" 
       class="btn btn-secondary">
        ⬅️ Kembali ke Order
    </a>

    <a href="<?= base_url('pembelian/cetak/'.$pembelian->id_pembelian) ?>" 
       class="btn btn-primary">
        🖨️ Cetak PDF
    </a>

</div>

</div>