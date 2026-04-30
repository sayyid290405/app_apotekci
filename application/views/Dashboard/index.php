<div class="container-fluid">

<<<<<<< HEAD
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
=======
    <?php if($stok_rendah > 0 || $kadaluarsa > 0): ?>
    <div class="row">
        <div class="col-12">
            <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center" role="alert">
                <i class="fas fa-exclamation-triangle fa-2x mr-3"></i>
                <div>
                    <strong>Pusat Perhatian:</strong> Ada <?= $stok_rendah ?> produk hampir habis dan <?= $kadaluarsa ?> produk mendekati kadaluarsa.
                    <a href="#tabelKontrol" class="alert-link ml-1">Lihat detail di bawah &darr;</a>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="font-weight-bold text-gray-800">Ringkasan Apotek</h3>
        <div>
            <a href="<?= base_url('produk') ?>" class="btn btn-light shadow-sm border">
                <i class="fas fa-box"></i> Stok
            </a>
            <a href="<?= base_url('kasir') ?>" class="btn btn-success shadow-sm">
                <i class="fas fa-cash-register"></i> KASIR
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="card text-white shadow" style="background:#0f766e; border:none;">
                <div class="card-body">
                    <small class="text-uppercase" style="opacity:0.8">Total Produk</small>
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="font-weight-bold mb-0"><?= $total_produk ?></h2>
                        <i class="fas fa-pills fa-2x" style="opacity:0.3"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card text-white shadow" style="background:#14b8a6; border:none;">
                <div class="card-body">
                    <small class="text-uppercase" style="opacity:0.8">Total Pelanggan</small>
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="font-weight-bold mb-0"><?= $total_pelanggan ?></h2>
                        <i class="fas fa-users fa-2x" style="opacity:0.3"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card text-dark shadow" style="background:#facc15; border:none;">
                <div class="card-body">
                    <small class="text-uppercase" style="opacity:0.8">Produk Kadaluarsa</small>
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="font-weight-bold mb-0"><?= $kadaluarsa ?></h2>
                        <i class="fas fa-calendar-times fa-2x" style="opacity:0.3"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card text-white shadow" style="background:#ef4444; border:none;">
                <div class="card-body">
                    <small class="text-uppercase" style="opacity:0.8">Stok Rendah</small>
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="font-weight-bold mb-0"><?= $stok_rendah ?></h2>
                        <i class="fas fa-exclamation-circle fa-2x" style="opacity:0.3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- GRAFIK -->
>>>>>>> ee11432ab565d27cb1eddab3a20fae4e15c9c552
    <div class="col-md-8">
        <div class="card p-3">
            <h5>Grafik Penjualan</h5>
            <canvas id="chart"></canvas>
        </div>
    </div>

<<<<<<< HEAD
    

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
=======
        <div class="col-md-4 mb-4">
            <div class="card shadow p-4 text-center h-100 border-bottom-success">
                <h6 class="text-uppercase text-muted font-weight-bold">Penjualan Hari Ini</h6>
                <h1 class="font-weight-bold text-success mt-2">
                    Rp <?= number_format($penjualan_hari_ini,0,',','.') ?>
                </h1>
                
                <div class="p-3 bg-light rounded mt-3">
                    <p class="mb-0 text-muted">Estimasi Keuntungan (20%):</p>
                    <h4 class="font-weight-bold text-dark">
                        Rp <?= number_format($penjualan_hari_ini * 0.2,0,',','.') ?>
                    </h4>
                </div>

                <div class="mt-4">
                    <a href="<?= base_url('kasir') ?>" class="btn btn-success btn-block py-2 shadow-sm">
                        <i class="fas fa-shopping-cart mr-2"></i> Buka KASIR
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div id="tabelKontrol" class="row">
        <div class="col-12 mb-4">
            <div class="card shadow">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-danger">⚠️ Daftar Obat Harus Segera Diorder</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Nama Produk</th>
                                    <th>Sisa Stok</th>
                                    <th>Stok Minimal</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($stok_minim_data)): ?>
                                    <tr><td colspan="5" class="text-center py-4">Semua stok aman.</td></tr>
                                <?php else: ?>
                                    <?php foreach($stok_minim_data as $sm): ?>
                                    <tr>
                                        <td class="font-weight-bold"><?= $sm->nama_produk ?></td>
                                        <td class="text-danger font-weight-bold"><?= $sm->stok ?></td>
                                        <td><?= $sm->stok_minimal ?></td>
                                        <td><span class="badge badge-danger">Kritis</span></td>
                                        <td>
                                            <a href="<?= base_url('Supplier/tambah') ?>" class="btn btn-sm btn-outline-primary">Order ke Supplier</a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-primary">📦 Riwayat Order Supplier Terbaru</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
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
                                <?php foreach($order as $o): ?>
                                <tr>
                                    <td><b><?= $o->kode_pembelian ?></b></td>
                                    <td><?= $o->nama_supplier ?></td>
                                    <td><?= date('d M Y', strtotime($o->tanggal)) ?></td>
                                    <td class="text-success font-weight-bold">Rp <?= number_format($o->total,0,',','.') ?></td>
                                    <td>
                                        <?php if($o->status == 'menunggu'): ?>
                                            <span class="badge badge-warning text-dark">Menunggu</span>
                                        <?php elseif($o->status == 'disetujui'): ?>
                                            <span class="badge badge-success">Disetujui</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger"><?= $o->status ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?= base_url('pembelian/detail/'.$o->id_pembelian) ?>" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
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
>>>>>>> ee11432ab565d27cb1eddab3a20fae4e15c9c552

</div>

<script>
window.chart_labels = <?= json_encode($chart_labels) ?>;
window.chart_data   = <?= json_encode($chart_data) ?>;
</script>