<div class="container-fluid">

<!-- NOTIF -->
<div class="alert alert-success">
    Selamat datang, admin!
</div>

<!-- HEADER -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Dashboard</h3>

    <div>
        <a href="<?= base_url('produk') ?>" class="btn btn-light">
            Daftar Produk
        </a>
        <a href="<?= base_url('kasir') ?>" class="btn btn-success">
            KASIR
        </a>
    </div>
</div>

<!-- CARD -->
<div class="row">

    <div class="col-md-3">
        <div class="card text-white p-3" style="background:#0f766e;">
            <small>Total Produk</small>
            <h3><?= $total_produk ?></h3>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card text-white p-3" style="background:#14b8a6;">
            <small>Total Pelanggan</small>
            <h3><?= $total_pelanggan ?></h3>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card text-dark p-3" style="background:#facc15;">
            <small>Produk Kadaluarsa</small>
            <h3><?= $kadaluarsa ?></h3>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card text-white p-3" style="background:#ef4444;">
            <small>Produk Stok Rendah</small>
            <h3><?= $stok_rendah ?></h3>
        </div>
    </div>

</div>

<br>

<!-- CONTENT -->
<div class="row">

    <!-- GRAFIK -->
    <div class="col-md-8">
        <div class="card p-3">
            <h5>Grafik Penjualan</h5>
            <canvas id="chart"></canvas>
        </div>
    </div>

    

    <!-- PANEL KANAN -->
    <div class="col-md-4">
        <div class="card p-3 text-center">

            <h5>Penjualan & Keuntungan Hari Ini</h5>

            <h2 style="color:green;">
                Rp <?= number_format($penjualan_hari_ini,0,',','.') ?>
            </h2>

            <p>Total transaksi hari ini</p>

            <p>Keuntungan hari ini:
                <strong>Rp <?= number_format($penjualan_hari_ini * 0.2,0,',','.') ?></strong>
            </p>

            <a href="<?= base_url('kasir') ?>" class="btn btn-success">
                Buka KASIR
            </a>

        </div>
    </div>

    <div class="card mt-4">
    <div class="card-body">

        <h5>📦 Order Obat Terbaru</h5>

        <div class="table-responsive">
        <table class="table table-hover align-middle">

            <thead class="table-light">
                <tr>
                    <th>Kode</th>
                    <th>Supplier</th>
                    <th>Tanggal</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>

            <?php if(empty($order)): ?>
                <tr>
                    <td colspan="6" class="text-center text-muted">
                        Belum ada order
                    </td>
                </tr>
            <?php endif; ?>

            <?php foreach($order as $o): ?>

                <tr>
                    <td><b><?= $o->kode_pembelian ?></b></td>

                    <td><?= $o->nama_supplier ?></td>

                    <td><?= date('d M Y', strtotime($o->tanggal)) ?></td>

                    <td class="text-success">
                        Rp <?= number_format($o->total,0,',','.') ?>
                    </td>

                    <td>
                        <?php if($o->status == 'menunggu'): ?>
                            <span class="badge bg-warning text-dark">Menunggu</span>
                        <?php elseif($o->status == 'disetujui'): ?>
                            <span class="badge bg-success">Disetujui</span>
                        <?php elseif($o->status == 'ditolak'): ?>
                            <span class="badge bg-danger">Ditolak</span>
                        <?php endif; ?>
                    </td>

                    <td>
                        <a href="<?= base_url('pembelian/detail/'.$o->id_pembelian) ?>" 
                           class="btn btn-sm btn-info">
                           👁️
                        </a>
                    </td>
                </tr>

            <?php endforeach; ?>

            </tbody>

        </table>
        </div>

    </div>
</div>

</div>

</div>

<script>
window.chart_labels = <?= json_encode($chart_labels) ?>;
window.chart_data   = <?= json_encode($chart_data) ?>;
</script>