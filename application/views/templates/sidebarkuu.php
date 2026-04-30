<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

  <!-- Sidebar - Brand -->
  <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?= site_url('dashboard'); ?>">
    <div class="sidebar-brand-icon rotate-n-15">
      <i class="fas fa-laugh-wink"></i>
    </div>
    <div class="sidebar-brand-text mx-3">PT Maju Jaya Elektronik</div>
  </a>

  <hr class="sidebar-divider my-0">

  <!-- Dashboard -->
  <li class="nav-item <?= ($this->uri->segment(1) == 'dashboard') ? 'active' : ''; ?>">
    <a class="nav-link" href="<?= site_url('dashboard'); ?>">
      <i class="fas fa-fw fa-tachometer-alt"></i>
      <span>Dashboard</span>
    </a>
  </li>

  <!-- Admin -->
  <?php if ($this->session->userdata('role_id') == 1): ?>
    <hr class="sidebar-divider">
    <div class="sidebar-heading">Manajemen Data</div>

    <li class="nav-item <?= ($this->uri->segment(1) == 'product') ? 'active' : ''; ?>">
      <a class="nav-link" href="<?= site_url('product'); ?>">
        <i class="fas fa-boxes"></i>
        <span>Produk</span>
      </a>
    </li>

    <li class="nav-item <?= ($this->uri->segment(1) == 'customer') ? 'active' : ''; ?>">
      <a class="nav-link" href="<?= site_url('customer'); ?>">
        <i class="fas fa-users"></i>
        <span>Pelanggan</span>
      </a>
    </li>

    <li class="nav-item <?= ($this->uri->segment(1) == 'sales') ? 'active' : ''; ?>">
      <a class="nav-link" href="<?= site_url('sales'); ?>">
        <i class="fas fa-user-tie"></i>
        <span>Sales</span>
      </a>
    </li>

    <li class="nav-item <?= ($this->uri->segment(1) == 'orders') ? 'active' : ''; ?>">
      <a class="nav-link" href="<?= site_url('orders'); ?>">
        <i class="fas fa-file-invoice-dollar"></i>
        <span>Sales Order</span>
      </a>
    </li>

    <hr class="sidebar-divider">
    <div class="sidebar-heading">Laporan Aktivitas</div>

    <li class="nav-item <?= ($this->uri->segment(1) == 'audit_log') ? 'active' : ''; ?>">
      <a class="nav-link" href="<?= site_url('audit_log'); ?>">
        <i class="fas fa-history"></i>
        <span>Riwayat Login</span>
      </a>
    </li>
  <?php endif; ?>

  <!-- Sales -->
  <?php if ($this->session->userdata('role_id') == 2): ?>
    <hr class="sidebar-divider">
    <div class="sidebar-heading">Transaksi Saya</div>

    <li class="nav-item <?= ($this->uri->segment(1) == 'orders') ? 'active' : ''; ?>">
      <a class="nav-link" href="<?= site_url('orders'); ?>">
        <i class="fas fa-file-invoice-dollar"></i>
        <span>Sales Order Saya</span>
      </a>
    </li>
  <?php endif; ?>

  <!-- Manager -->
  <?php if ($this->session->userdata('role_id') == 3): ?>
    <hr class="sidebar-divider">
    <div class="sidebar-heading">Laporan</div>

    <li class="nav-item <?= ($this->uri->segment(1) == 'report') ? 'active' : ''; ?>">
      <a class="nav-link" href="<?= site_url('report'); ?>">
        <i class="fas fa-chart-line"></i>
        <span>Laporan Penjualan</span>
      </a>
    </li>
  <?php endif; ?>

  <hr class="sidebar-divider d-none d-md-block">

  <!-- Logout -->
  <li class="nav-item">
    <a class="nav-link" href="<?= site_url('auth/logout'); ?>">
      <i class="fas fa-sign-out-alt"></i>
      <span>Logout</span>
    </a>
  </li>
</ul>
<!-- End of Sidebar -->
