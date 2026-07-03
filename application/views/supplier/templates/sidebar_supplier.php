<!-- ================= SIDEBAR SUPPLIER ================= -->

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

    padding-top:0px;

    box-shadow:
        0 4px 20px rgba(0,0,0,.04);
}

/* ================= BRAND ================= */

.sidebar-brand{

    padding:22px 18px;

    display:flex;
    align-items:center;
    gap:14px;

    border-bottom:1px solid #eef2f7;

    background:#ffffff;
}

/* logo */

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

.brand-status{

    display:inline-block;

    margin-top:5px;

    padding:3px 8px;

    border-radius:30px;

    background:#dcfce7;

    color:#059669;

    font-size:10px;
    font-weight:700;

    letter-spacing:.3px;
}

/* ================= MENU ================= */

.sidebar-menu{

    padding:12px 0;
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

/* ================= SECTION ================= */

.sidebar-section{

    padding:18px 22px 8px;

    font-size:11px;
    font-weight:700;

    color:#9ca3af;

    letter-spacing:1px;

    text-transform:uppercase;
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
            <i class="fa-solid fa-truck-medical"></i>
        </div>

        <div class="brand-info">

            <h5>Supplier Portal</h5>

            <span>
                Smart Pharmacy System
            </span>

            <div class="brand-status">
                SUPPLIER
            </div>

        </div>

    </div>

    <!-- MENU -->
    <div class="sidebar-menu">

        <!-- SECTION -->
        <div class="sidebar-section">
            Main Menu
        </div>

        <!-- CUSTOMER ORDER -->
        <a href="<?= base_url('Supplier/approved_list') ?>"
           class="<?= ($this->uri->segment(2) == 'approved_list') ? 'active' : '' ?>">

            <i class="fa fa-shopping-bag"></i>
            Customer Order

        </a>

        <!-- SHIPMENT -->
        <!-- <a href="<?= base_url('Supplier/pembelian_selesai') ?>"
           class="<?= ($this->uri->segment(2) == 'pembelian_selesai') ? 'active' : '' ?>">

            <i class="fa fa-truck"></i>
            Shipment Order

        </a> -->

        <!-- LAPORAN PEMBELIAN -->
        <a href="<?= base_url('supplier/laporan_pembelian') ?>"
        class="<?= ($this->uri->segment(2) == 'laporan_pembelian') ? 'active' : '' ?>">

            <i class="fa fa-truck"></i>
            Record Order

        </a>

        <!-- LAPORAN SUPPLIER -->
        <!-- <a href="<?= base_url('supplier/laporan') ?>"
        class="<?= ($this->uri->segment(2) == 'laporan') ? 'active' : '' ?>">

            <i class="fa fa-chart-line"></i>
            Laporan Supplier

        </a> -->

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