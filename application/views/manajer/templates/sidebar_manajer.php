<!-- ================= SIDEBAR MANAJER ================= -->

<style>

/* ================= SIDEBAR ================= */

.sidebar{

    width:230px;
    height:100vh;

    position:fixed;
    top:0;
    left:0;

    background:#ffffff;

    border-right:1px solid #e5e7eb;

    overflow-y:auto;

    z-index:1020;

    padding-top:0;

    box-shadow:
        0 4px 20px rgba(0,0,0,.04);
}

/* ================= BRAND ================= */

.sidebar-brand{

    height:92px;

    padding:20px 18px;

    display:flex;
    align-items:center;

    gap:14px;

    border-bottom:1px solid #eef2f7;

    background:#ffffff;
}

/* badge */

.brand-badge{

    width:52px;
    height:52px;

    border-radius:18px;

    background:
        linear-gradient(
            135deg,
            #0f766e,
            #10b981
        );

    display:flex;
    align-items:center;
    justify-content:center;

    color:#ffffff;

    font-size:20px;

    box-shadow:
        0 8px 18px rgba(16,185,129,.20);
}

/* text */

.brand-info h5{

    margin:0;

    font-size:15px;
    font-weight:700;

    color:#111827;

    line-height:1.2;
}

.brand-info span{

    display:block;

    font-size:12px;

    color:#6b7280;

    margin-top:2px;
}

/* badge role */

.brand-status{

    display:inline-block;

    margin-top:5px;

    padding:3px 8px;

    border-radius:30px;

    background:#dbeafe;

    color:#2563eb;

    font-size:10px;
    font-weight:700;

    letter-spacing:.3px;
}

/* ================= SECTION ================= */

.sidebar-section{

    padding:18px 22px 8px;

    font-size:11px;
    font-weight:700;

    color:#9ca3af;

    letter-spacing:1px;

    text-transform:uppercase;
}

/* ================= MENU ================= */

.sidebar-menu{

    padding:10px 0;
}

/* item */

.sidebar-menu a{

    display:flex;
    align-items:center;

    gap:14px;

    padding:14px 22px;

    color:#374151;

    font-size:14px;
    font-weight:500;

    transition:.25s ease;

    position:relative;
}

/* icon */

.sidebar-menu a i{

    width:18px;

    color:#0f766e;

    font-size:15px;
}

/* hover */

.sidebar-menu a:hover{

    background:#f0fdf4;

    color:#059669;

    padding-left:26px;
}

/* active */

.sidebar-menu a.active{

    background:
        linear-gradient(
            135deg,
            #0f766e,
            #059669
        );

    color:#ffffff;

    font-weight:600;

    box-shadow:
        0 4px 12px rgba(5,150,105,.15);
}

.sidebar-menu a.active i{
    color:#ffffff;
}

/* left indicator */

.sidebar-menu a.active::before{

    content:'';

    position:absolute;

    left:0;
    top:0;
    bottom:0;

    width:4px;

    background:#10b981;
}

/* ================= SUBMENU ================= */

.submenu{

    padding-left:16px;

    margin-top:4px;
}

.submenu a{

    font-size:13px;

    padding:11px 20px;

    color:#6b7280;
}

.submenu a:hover{

    background:#f9fafb;
}

/* ================= FOOTER ================= */

.sidebar-footer{

    border-top:1px solid #eef2f7;

    margin-top:20px;

    padding-top:10px;
}

/* logout */

.logout-link{

    color:#ef4444 !important;
}

.logout-link i{
    color:#ef4444 !important;
}

.logout-link:hover{

    background:#fef2f2 !important;

    color:#dc2626 !important;
}

/* ================= CONTENT ================= */

.content{

    margin-left:230px;

    padding:24px;

    padding-top:90px;

    min-height:100vh;

    background:#f5f7fb;
}

/* ================= RESPONSIVE ================= */

@media(max-width:768px){

    .sidebar{
        left:-230px;
    }

    .sidebar.active{
        left:0;
    }

    .content{
        margin-left:0;
    }
}

</style>

<!-- ================= SIDEBAR ================= -->

<div class="sidebar">

    <!-- BRAND -->
    <div class="sidebar-brand">

        <div class="brand-badge">
            <i class="fa-solid fa-user-tie"></i>
        </div>

        <div class="brand-info">

            <h5>Manager Portal</h5>

            <span>
                Smart Pharmacy System
            </span>

            <div class="brand-status">
                MANAGER
            </div>

        </div>

    </div>

    <!-- MENU -->
    <div class="sidebar-menu">

        <!-- SECTION -->
        <div class="sidebar-section">
            Main Menu
        </div>

        <!-- LAPORAN -->
        <a href="#"
           data-bs-toggle="collapse"
           data-bs-target="#submenu-dashboard"
           aria-expanded="false">

            <i class="fa fa-chart-line"></i>
            Laporan

        </a>

        <!-- SUBMENU -->
        <div class="collapse <?= ($this->uri->segment(2) == 'grafik_penjualan' || $this->uri->segment(2) == 'stok_obat') ? 'show' : '' ?>"
             id="submenu-dashboard">

            <div class="submenu">

                <a href="<?= base_url('dashboard_manajer/grafik_penjualan') ?>">
                    Laporan Penjualan
                </a>

                <a href="<?= base_url('dashboard_manajer/stok_obat') ?>">
                    Laporan Stok
                </a>

            </div>

        </div>

        <!-- PENGGUNA -->
        <a href="<?= base_url('Users') ?>"
           class="<?= ($this->uri->segment(1) == 'Users') ? 'active' : '' ?>">

            <i class="fa fa-users"></i>
            Pengguna

        </a>

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

        <!-- SUBMENU PEMBELIAN -->
        <div class="collapse <?= ($this->uri->segment(1) == 'pembelian') ? 'show' : '' ?>"
             id="menuPembelian">

            <div class="submenu">

                <a href="<?= base_url('pembelian/approval_pembelian') ?>">
                    Raw Request Pembelian
                </a>

                <a href="<?= base_url('pembelian/bayar_supplier') ?>">
                    Pembayaran Barang Tiba
                </a>

            </div>

        </div>

        <!-- SUPPLIER -->
        <a href="<?= base_url('supplier') ?>"
           class="<?= ($this->uri->segment(1) == 'supplier') ? 'active' : '' ?>">

            <i class="fa fa-truck"></i>
            Supplier

        </a>

        <!-- ORDER OBAT -->
        <?php if($this->session->userdata('role_id') == 1): ?>

        <a href="<?= base_url('pembelian') ?>">

            <i class="fa fa-box"></i>
            Order Obat

        </a>

        <?php endif; ?>

        <!-- FOOTER -->
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
                Logout

            </a>

        </div>

    </div>

</div>

<!-- ================= CONTENT ================= -->

<div class="content">