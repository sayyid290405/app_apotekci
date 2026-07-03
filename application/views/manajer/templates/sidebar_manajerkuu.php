<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'MyApotek' ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f5f7fb;
            overflow-x: hidden;
        }

        /* Navbar Tetap di Atas */
        .navbar-custom {
            background: #0f766e;
            color: white;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1030;
            height: 56px;
        }

        /* Sidebar Tetap di Samping */
        .sidebar {
            width: 230px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background: white;
            border-right: 1px solid #ddd;
            padding-top: 56px; /* Agar tidak tertutup navbar */
            z-index: 1020;
            transition: all 0.3s;
        }

        .sidebar-brand {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid #eee;
        }

        .sidebar a {
            display: block;
            padding: 12px 20px;
            color: #444;
            text-decoration: none;
            font-size: 0.95rem;
            transition: 0.2s;
        }

        .sidebar a i {
            width: 25px;
            color: #0f766e;
        }

        .sidebar a:hover {
            background: #f1f5f4;
            color: #0f766e;
            border-left: 4px solid #0f766e;
        }

        .sidebar a.active {
            background: #0f766e;
            color: white;
        }
        
        .sidebar a.active i {
            color: white;
        }

        /* Area Konten Utama */
        .content {
            margin-left: 230px;
            padding: 20px;
            margin-top: 56px; /* Jarak dari navbar */
            min-height: calc(100vh - 56px);
        }

        .card {
            border-radius: 12px;
            border: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                margin-left: -230px;
            }
            .content {
                margin-left: 0;
            }
            .sidebar.active {
                margin-left: 0;
            }
        }
    </style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-custom px-3 shadow-sm">
    <div class="container-fluid">
        <span class="navbar-brand text-white fw-bold">
            <i class="fa fa-notes-medical me-2"></i>MyApotek
        </span>
        
        <div class="ms-auto d-flex align-items-center text-white">
            <div class="dropdown">
                <a href="#" class="text-white text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="fa fa-user-circle me-1"></i> 
                    <?= $this->session->userdata('nama') ?? 'Admin' ?>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="#"><i class="fa fa-cog me-2"></i>Settings</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="<?= base_url('auth/logout') ?>"><i class="fa fa-sign-out-alt me-2"></i>Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>
<div class="sidebar">

    <div class="text-center p-3 border-bottom">
        <h5 class="fw-bold text-success">APOTEK</h5>
    </div>

<a href="#" data-bs-toggle="collapse" data-bs-target="#submenu-dashboard" aria-expanded="false">
    <i class="fa fa-home me-2"></i> Laporan
</a>
<div class="collapse" id="submenu-dashboard">
    <ul class="list-unstyled">
        <li><a href="<?= base_url('dashboard_manajer/grafik_penjualan') ?>">Laporan Penjualan</a></li>
        <li><a href="<?= base_url('dashboard_manajer/stok_obat') ?>">Laporan Stok</a></li>
        <!-- <li><a href="<?= base_url('dashboard/statistics') ?>">Laporan Keuntungan</a></li> -->
    </ul>
</div>
    <a href="<?= base_url('Users') ?>">
        <i class="fa fa-cash-register me-2"></i> Pengguna
    </a>

    <!-- <a href="<?= base_url('produk') ?>">
        <i class="fa fa-capsules me-2"></i> Produk Kadaluarsa
    </a> -->

<li class="nav-item">

    <!-- BUTTON DROPDOWN -->
    <a class="nav-link d-flex justify-content-between align-items-center"
       data-bs-toggle="collapse"
       href="#menuPembelian"
       role="button">

        <span>
            <i class="fa fa-shopping-bag"></i>
            Pembelian
        </span>

        <i class="fa fa-chevron-down"></i>
    </a>

    <!-- SUBMENU -->
    <div class="collapse <?= ($this->uri->segment(1) == 'pembelian') ? 'show' : '' ?>"
         id="menuPembelian">

        <ul class="nav flex-column ms-3">

            <li class="nav-item">
                <a class="nav-link"
                   href="<?= base_url('pembelian/approval_pembelian') ?>">
                     Raw Request Pembelian
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link"
                   href="<?= base_url('pembelian/bayar_supplier') ?>">
                    Pembayaran Barang Tiba
                </a>
            </li>

            <!-- <li class="nav-item">
                <a class="nav-link"
                   href="<?= base_url('shipment') ?>">
                    🚚 Shipment
                </a>
            </li> -->

        </ul>

    </div>

</li>



        <a href="<?= base_url('supplier') ?>">
            <i class="fa fa-truck"></i> Supplier
        </a>

    <?php if($this->session->userdata('role_id') == 1): ?>
    <a href="<?= base_url('pembelian') ?>">
        <i class="fa fa-shopping-bag me-2"></i> Order Obat
    </a>
    <?php endif; ?>

    <!-- <a href="<?= base_url('pelanggan') ?>">
        <i class="fa fa-users me-2"></i> Pelanggan
    </a> -->

    <a href="<?= base_url('auth/logout') ?>" class="text-danger">
        <i class="fa fa-sign-out-alt me-2"></i> Logout
    </a>

</div>

<!-- CONTENT -->
<div class="content">