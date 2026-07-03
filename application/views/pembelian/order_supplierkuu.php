<div class="container mt-4">

    <h3>📦 Data Pembelian</h3>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="<?= base_url('pembelian') ?>" class="btn btn-success">
            ➕ Buat Permintaan Barang
        </a>
    </div>

    <table class="table table-bordered table-striped mt-3">
        <thead class="table-dark">
            <tr>
                <th>No</th>
                <th>Kode Pembelian</th>
                <th>Nama Supplier</th>
                <th>Item Produk</th>
                <th>Tanggal</th>
                <th>Total Harga</th>
                <th>Status</th>
                <th>Catatan</th>
            </tr>
        </thead>

        <tbody>

        <?php if(empty($pembelian)): ?>
            <tr>
                <td colspan="8" class="text-center">Data kosong</td>
            </tr>
        <?php endif; ?>

        <?php $no = 1; foreach($pembelian as $p): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= $p->kode_pembelian ?? '-' ?></td>
                <td><?= $p->nama_supplier ?? '-' ?></td>
                <td><?= $p->nama_produk ?? '-' ?></td>
                <td><?= date('d M Y', strtotime($p->tanggal ?? date('Y-m-d'))) ?></td>
                <td>Rp <?= number_format($p->total ?? 0, 0, ',', '.') ?></td>

                <td>
                    <?php if($p->status == 'menunggu'): ?>
                        <span class="badge bg-warning text-dark">
                            Menunggu
                        </span>
                    <?php elseif($p->status == 'disetujui'): ?>
                        <span class="badge bg-primary">
                            Disetujui
                        </span>
                    <?php elseif($p->status == 'diproses'): ?>
                        <span class="badge bg-info text-dark">
                            Diproses
                        </span>
                    <?php elseif($p->status == 'selesai'): ?>
                        <span class="badge bg-success">
                            Selesai
                        </span>
                    <?php elseif($p->status == 'diterima'): ?>
                        <span class="badge bg-success">
                            Diterima
                        </span>
                    <?php elseif($p->status == 'ditolak'): ?>
                        <span class="badge bg-danger">
                            Ditolak
                        </span>
                    <?php elseif($p->status == 'dibatalkan'): ?>
                        <span class="badge bg-secondary">
                            Dibatalkan
                        </span>
                    
                    <?php elseif($p->status == 'Belum Lunas'): ?>
                        <span class="badge bg-danger">
                            Belum Lunas
                        </span>
                    <?php elseif($p->status == 'Lunas'): ?>
                        <span class="badge bg-success">
                            Lunas
                        </span>

                    <?php else: ?>
                        <span class="badge bg-dark">
                            Unknown (<?= $p->status; ?>)
                        </span>
                    <?php endif; ?>

                    <a href="<?= base_url('pembelian/detail/'.$p->id_pembelian) ?>" class="btn btn-sm btn-info ms-1">
                        <i class="fas fa-eye"></i>
                    </a>
                </td>
                <td><?= $p->catatan ?? 'Tidak ada catatan' ?></td>
            </tr>
        <?php endforeach; ?>

        </tbody>
    </table>

</div>