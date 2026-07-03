<!DOCTYPE html>
<html lang="id">
<head>
    

    <meta charset="UTF-8">
    <title><?= $title ?? 'Smart Pharmacy Management System' ?></title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
        

    <style>

    /* ================= ROOT ================= */

:root{

    --primary:#059669;
    --primary-dark:#0f766e;
    --primary-light:#10b981;

    --bg:#f4f7fb;

    --text:#111827;
    --text-soft:#6b7280;

    --border:#e5e7eb;

    --white:#ffffff;

    --shadow-sm:0 2px 8px rgba(0,0,0,.04);
    --shadow-md:0 6px 18px rgba(0,0,0,.06);
    --shadow-lg:0 10px 30px rgba(0,0,0,.08);

    --radius-sm:12px;
    --radius-md:16px;
    --radius-lg:22px;
}

/* ================= GLOBAL ================= */

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

html{
    scroll-behavior:smooth;
}

body{
    background:var(--bg);
    font-family:'Poppins',sans-serif;
    overflow-x:hidden;
    color:var(--text);
}

/* smooth */

body,
button,
a,
.card,
.sidebar-menu a,
.topbar{
    transition:.25s ease;
}

a{
    text-decoration:none;
}

/* ================= TOPBAR ================= */

.topbar{

    position:fixed;

    top:0;
    left:230px;
    right:0;

    height:74px;

    background:
        linear-gradient(
            135deg,
            var(--primary-dark),
            var(--primary)
        );

    display:flex;
    align-items:center;
    justify-content:space-between;

    padding:0 28px;

    z-index:1000;

    border-bottom:1px solid rgba(255,255,255,.08);

    box-shadow:
        0 4px 20px rgba(5,150,105,.15);
}

/* glass effect */

.topbar::before{

    content:'';

    position:absolute;

    inset:0;

    background:
        linear-gradient(
            to right,
            rgba(255,255,255,.04),
            transparent
        );

    pointer-events:none;
}

/* ================= LEFT ================= */

.topbar-left{
    display:flex;
    align-items:center;
    gap:16px;
}

/* ================= LOGO ================= */

.nav-logo{

    width:50px;
    height:50px;

    border-radius:18px;

    background:
        linear-gradient(
            135deg,
            rgba(255,255,255,.18),
            rgba(255,255,255,.08)
        );

    border:1px solid rgba(255,255,255,.15);

    backdrop-filter:blur(12px);

    display:flex;
    align-items:center;
    justify-content:center;

    color:#ffffff;
    font-size:20px;

    box-shadow:
        0 8px 20px rgba(0,0,0,.12);
}

/* ================= BRAND ================= */

.nav-brand-group{
    display:flex;
    flex-direction:column;
    line-height:1.1;
}

.nav-title{
    margin:0;
    font-size:24px;
    font-weight:700;
    color:#ffffff;
    letter-spacing:.2px;
}

.nav-subtitle{
    font-size:12px;
    color:rgba(255,255,255,.75);
    letter-spacing:.5px;
    margin-top:2px;
}

/* ================= RIGHT ================= */

.topbar-right{
    display:flex;
    align-items:center;
    gap:18px;
}

/* ================= ICON BUTTON ================= */

.nav-icon-btn{

    width:44px;
    height:44px;

    border:none;
    border-radius:14px;

    background:
        rgba(255,255,255,.12);

    color:#ffffff;

    backdrop-filter:blur(10px);

    display:flex;
    align-items:center;
    justify-content:center;

    font-size:16px;

    transition:.25s ease;
}

.nav-icon-btn:hover{

    transform:translateY(-2px);

    background:
        rgba(255,255,255,.18);

    box-shadow:
        0 6px 16px rgba(0,0,0,.12);
}

/* ================= USER ================= */

.user-info{
    display:flex;
    align-items:center;
    gap:12px;
}

/* avatar */

.user-avatar{

    width:44px;
    height:44px;

    border-radius:50%;

    background:
        rgba(255,255,255,.15);

    border:2px solid rgba(255,255,255,.2);

    backdrop-filter:blur(10px);

    color:#ffffff;

    display:flex;
    align-items:center;
    justify-content:center;

    font-weight:700;
    font-size:15px;

    box-shadow:
        0 4px 10px rgba(0,0,0,.12);
}

/* detail */

.user-detail{
    display:flex;
    flex-direction:column;
    line-height:1.1;
}

.user-name{
    font-size:14px;
    font-weight:600;
    color:#ffffff;
}

.user-detail small{
    font-size:11px;
    color:rgba(255,255,255,.72);
    margin-top:2px;
}

/* ================= CONTENT ================= */

.content{

    margin-left:230px;

    padding:28px;

    padding-top:100px;

    min-height:100vh;
}

/* ================= CARD ================= */

.card{

    border:none;

    border-radius:var(--radius-lg);

    background:var(--white);

    overflow:hidden;

    box-shadow:var(--shadow-md);

    transition:.25s ease;
}

.card:hover{

    transform:translateY(-3px);

    box-shadow:
        0 14px 30px rgba(0,0,0,.08);
}

/* ================= BUTTON ================= */

.btn{

    border-radius:14px;

    font-size:14px;
    font-weight:600;

    padding:10px 18px;

    transition:.2s ease;
}

.btn-success{

    background:
        linear-gradient(
            135deg,
            var(--primary-dark),
            var(--primary)
        );

    border:none;
}

.btn-success:hover{

    transform:translateY(-2px);

    box-shadow:
        0 8px 20px rgba(5,150,105,.25);
}

/* ================= TABLE ================= */

.table{
    border-radius:16px;
    overflow:hidden;
}

.table thead{

    background:#f9fafb;
}

.table th{

    font-size:13px;
    font-weight:600;
    color:#374151;
}

.table td{

    font-size:13px;
    vertical-align:middle;
}

/* ================= SCROLLBAR ================= */

::-webkit-scrollbar{
    width:6px;
}

::-webkit-scrollbar-thumb{
    background:#cbd5e1;
    border-radius:20px;
}

/* ================= RESPONSIVE ================= */

@media(max-width:992px){

    .topbar{
        left:210px;
    }

    .content{
        margin-left:210px;
    }
}

@media(max-width:768px){

    .topbar{
        left:0;
        padding:0 16px;
    }

    .content{
        margin-left:0;
        padding:18px;
        padding-top:90px;
    }

    .user-detail{
        display:none;
    }

    .nav-title{
        font-size:16px;
    }

    .nav-subtitle{
        font-size:11px;
    }
}

    </style>

</head>

<body>

<!-- ================= TOPBAR ================= -->

<header class="topbar">

    <!-- LEFT -->
    <div class="topbar-left">

        <!-- LOGO -->
        <!-- <div class="nav-logo">
            <i class="fa-solid fa-prescription-bottle-medical"></i>
        </div> -->

        <!-- BRAND -->
        <div class="nav-brand-group">

            <h5 class="nav-title">
                Gempas Farma
            </h5>

           

        </div>

    </div>

    <!-- RIGHT -->
    <div class="topbar-right">

        <!-- Notification -->
        <button class="nav-icon-btn" type="button">
            <i class="fa-regular fa-bell"></i>
        </button>

        <!-- USER -->
        <div class="user-info">

            <div class="user-avatar">
                <?= strtoupper(substr($this->session->userdata('nama') ?? 'A',0,1)); ?>
            </div>

            <div class="user-detail">

                <span class="user-name">
                    <?= $this->session->userdata('nama') ?? 'Administrator'; ?>
                </span>

                <small>
                    Administrator
                </small>

            </div>

        </div>

    </div>

</header>