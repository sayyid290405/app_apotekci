<h3>STRUK PEMBAYARAN</h3>

<p>Kasir: <?= $pesanan->nama ?></p>

<table border="1" width="100%">
<?php foreach($detail as $d): ?>
<tr>
<td><?= $d->nama_produk ?></td>
<td><?= $d->jumlah ?></td>
<td><?= number_format($d->subtotal) ?></td>
</tr>
<?php endforeach; ?>
</table>

<p>Total: <?= number_format($pesanan->total_harga) ?></p>