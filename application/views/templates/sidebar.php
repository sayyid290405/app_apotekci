<!-- ================= SIDEBAR ================= -->

<style>

.sidebar{
    width:230px;
    height:100vh;

    position:fixed;
    top:0;
    left:0;

    background:#ffffff;

    border-right:1px solid #e5e7eb;

    overflow-y:auto;

    z-index:1001;
}

/* BRAND */

.sidebar-brand{
    padding:20px 15px;
    display:flex;
    align-items:center;
    gap:14px;
    border-bottom:1px solid #e5e7eb;
    background:#ffffff;
}

.brand-badge{
    width:48px;
    height:48px;
    border-radius:14px;

    background:linear-gradient(135deg,#059669,#10b981);

    display:flex;
    align-items:center;
    justify-content:center;

    color:white;
    font-size:20px;

    box-shadow:0 4px 10px rgba(16,185,129,.25);
}

.brand-info h5{
    margin:0;
    font-size:15px;
    font-weight:700;
    color:#111827;
    line-height:1.2;
}

.brand-subtitle{
    font-size:12px;
    color:#6b7280;

    display:flex;
    align-items:center;
    gap:6px;
}

.brand-status{
    background:#dcfce7;
    color:#059669;

    font-size:10px;
    font-weight:700;

    padding:2px 6px;
    border-radius:20px;
}

/* MENU */

.sidebar-menu{
    padding:10px 0;
}

.sidebar-menu a{
    display:flex;
    align-items:center;
    gap:12px;

    padding:13px 20px;

    color:#374151;
    font-size:14px;
    font-weight:500;

    transition:.2s ease;
}

.sidebar-menu a i{
    width:18px;
    color:#0f766e;
}

.sidebar-menu a:hover{
    background:#f3f4f6;
    color:#059669;
}

.sidebar-menu a.active{
    background:#ecfdf5;
    color:#059669;
    border-left:4px solid #10b981;
}

.sidebar-menu a.active i{
    color:#059669;
}

/* DROPDOWN */

.submenu{
    padding-left:20px;
}

.submenu a{
    font-size:13px;
    padding:10px 20px;
}

/* LOGOUT */

.sidebar-footer{
    border-top:1px solid #e5e7eb;
    margin-top:15px;
    padding-top:10px;
}

.logout-link{
    color:#ef4444 !important;
}

/* RESPONSIVE */

@media(max-width:768px){

    .sidebar{
        left:-230px;
    }
}

</style>

<div class="sidebar shadow-sm">

    <!-- BRAND -->
    <div class="sidebar-brand">

        <div class="brand-badge">
            <i class="fa-solid fa-hospital"></i>
        </div>

        <div class="brand-info">

            <h5>Smart Pharmacy</h5>

            <div class="brand-subtitle">
                Management System
                <span class="brand-status">PRO</span>
            </div>

        </div>

    </div>

    <!-- MENU -->
    <div class="sidebar-menu">

        <a href="<?= base_url('dashboard') ?>"
           class="<?= (uri_string() == 'dashboard') ? 'active' : '' ?>">

            <i class="fa fa-home"></i>
            Dashboard

        </a>

        <a href="<?= base_url('kasir') ?>">

            <i class="fa fa-cash-register"></i>
            KASIR

        </a>

        <?php if($this->session->userdata('role_id') == 1): ?>

        <!-- PEMBELIAN -->
        <a class="d-flex justify-content-between align-items-center"
           data-bs-toggle="collapse"
           href="#menuPembelian"
           role="button">

            <span>
                <i class="fa fa-shopping-bag"></i>
                Pembelian
            </span>

            <i class="fa fa-chevron-down"></i>

        </a>

        <div class="collapse <?= ($this->uri->segment(1) == 'pembelian') ? 'show' : '' ?>"
             id="menuPembelian">

            <div class="submenu">

                <a href="<?= base_url('pembelian/pembelian_supplier') ?>">
                    Data Pembelian
                </a>

                <a href="<?= base_url('pembelian/terima_barang_supplier') ?>">
                    Barang Masuk
                </a>

            </div>

        </div>

        <?php endif; ?>

        <a href="<?= base_url('resep') ?>">

            <i class="fa fa-file-medical"></i>
            Resep

        </a>

        <a href="<?= base_url('laporan') ?>">

            <i class="fa fa-chart-line"></i>
            Laporan

        </a>
                <a href="<?= base_url('produk') ?>">
            <i class="fa fa-capsules"></i> Produk 
           
        </a>  

        <!-- NOte:  fitur  ini dipindahkan hak akses nya ke role supplier -->

        <a href="<?= base_url('kategori') ?>">
            <i class="fa fa-tags"></i> Kategori
        </a>

        <!-- LOGOUT -->
        <div class="sidebar-footer">

            <a href="<?= base_url('auth/logout') ?>"
               class="logout-link" id="btnLogout">

<script>
document.getElementById('btnLogout').addEventListener('click', function(e) {
    e.preventDefault();

    Swal.fire({
        title: 'Yakin ingin logout?',
        text: 'Session login akan berakhir',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Logout',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "<?= base_url('auth/logout') ?>";
        }
    });
});
</script>

                <i class="fa fa-sign-out-alt"></i>
                Keluar

            </a>

        </div>

    </div>

</div>

<!-- CONTENT -->
<div class="content">