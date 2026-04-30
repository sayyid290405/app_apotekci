<div class="sidebar">

    <div class="text-center p-3 border-bottom">
        <h5 class="fw-bold text-success">APOTEK</h5>
    </div>

    <a href="<?= base_url('dashboard') ?>">
        <i class="fa fa-home me-2"></i> Grafik
    </a>

    <a href="<?= base_url('kasir') ?>">
        <i class="fa fa-cash-register me-2"></i> KASIR
    </a>

    <a href="<?= base_url('produk') ?>">
        <i class="fa fa-capsules me-2"></i> Produk
    </a>

    <a href="<?= base_url('kategori') ?>">
        <i class="fa fa-tags me-2"></i> Kategori
    </a>

    <a href="<?= base_url('supplier') ?>">
        <i class="fa fa-truck me-2"></i> Supplier
    </a>

    <?php if($this->session->userdata('role_id') == 1): ?>
    <a href="<?= base_url('pembelian') ?>">
        <i class="fa fa-shopping-bag me-2"></i> Order Obat
    </a>
    <?php endif; ?>

    <a href="<?= base_url('pelanggan') ?>">
        <i class="fa fa-users me-2"></i> Pelanggan
    </a>

    <a href="<?= base_url('laporan') ?>">
        <i class="fa fa-chart-line me-2"></i> Laporan
    </a>

    <a href="<?= base_url('auth/logout') ?>" class="text-danger">
        <i class="fa fa-sign-out-alt me-2"></i> Logout
    </a>

</div>

<!-- CONTENT -->
<div class="content">