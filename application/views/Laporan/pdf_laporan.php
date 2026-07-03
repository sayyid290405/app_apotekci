<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">

<title>Laporan Apotek</title>

<style>

/* ================= GLOBAL ================= */

body{
    font-family: DejaVu Sans, sans-serif;
    font-size: 12px;
    color:#1e293b;
    margin:0;
    padding:25px;
}
/* ================= HEADER ================= */

.top-header{

    width:100%;

    border-collapse:collapse;

    margin-bottom:12px;
}

/* LOGO */

.logo-column{

    width:90px;

    vertical-align:top;
}

.logo-box{

    width:65px;

    height:65px;

    border-radius:14px;

    background:#0f766e;

    color:white;

    text-align:center;

    line-height:65px;

    font-size:34px;

    font-weight:bold;
}

/* COMPANY */

.company-column{

    vertical-align:top;
}

.company-name{

    font-size:24px;

    font-weight:800;

    color:#0f766e;

    letter-spacing:1px;

    margin-bottom:6px;
}

.company-sub{

    font-size:12px;

    color:#64748b;

    line-height:1.8;
}

/* DOC INFO */

.doc-column{

    width:240px;

    vertical-align:top;
}

.doc-info{

    width:100%;

    font-size:11px;

    border-collapse:collapse;
}

.doc-info td{

    padding:3px 0;

    color:#334155;
}

/* HEADER LINE */

.header-line{

    border-top:3px solid #0f766e;

    margin-top:10px;

    margin-bottom:20px;
}

/* TITLE */

.report-title{

    text-align:center;

    margin-bottom:25px;
}

.report-title h2{

    margin:0;

    font-size:24px;

    font-weight:800;

    color:#0f172a;

    letter-spacing:.5px;
}

.report-title p{

    margin-top:6px;

    font-size:12px;

    color:#64748b;
}
/* ================= INFO ================= */

.info-box{
    margin-top:18px;
    margin-bottom:20px;
    border:1px solid #e2e8f0;
    padding:15px;
    border-radius:10px;
    background:#f8fafc;
}

.info-table{
    width:100%;
}

.info-table td{
    padding:5px 0;
}

/* ================= TABLE ================= */

.table{
    width:100%;
    border-collapse:collapse;
    margin-top:15px;
}

.table th{
    background:#0f766e;
    color:white;
    padding:10px;
    border:1px solid #d1d5db;
    font-size:12px;
}

.table td{
    padding:9px;
    border:1px solid #e5e7eb;
    font-size:11px;
}

.table tbody tr:nth-child(even){
    background:#f8fafc;
}

/* ================= SECTION ================= */

.section-title{
    margin-top:25px;
    margin-bottom:10px;
    font-size:16px;
    font-weight:bold;
    color:#0f766e;
}

/* ================= TOTAL ================= */

.total-box{
    margin-top:10px;
    width:100%;
}

.total-table{
    width:40%;
    margin-left:auto;
    border-collapse:collapse;
}

.total-table td{
    border:1px solid #e5e7eb;
    padding:8px;
}

.total-label{
    background:#f1f5f9;
    font-weight:bold;
}

.total-value{
    text-align:right;
    font-weight:bold;
}

/* ================= FOOTER ================= */

.footer{
    margin-top:40px;
}

.footer-table{
    width:100%;
}

.signature{
    text-align:center;
    vertical-align:bottom;
    height:120px;
}

.signature-name{
    margin-top:70px;
    font-weight:bold;
}

/* ================= BADGE ================= */

.badge{
    display:inline-block;
    padding:4px 10px;
    border-radius:20px;
    font-size:10px;
    font-weight:bold;
}

.badge-success{
    background:#dcfce7;
    color:#166534;
}

.badge-warning{
    background:#fef3c7;
    color:#92400e;
}

/* ================= SMALL ================= */

.text-right{
    text-align:right;
}

.text-center{
    text-align:center;
}

hr{
    border:none;
    border-top:1px solid #cbd5e1;
    margin:18px 0;
}

</style>
</head>

<body>

<!-- ================= HEADER ================= -->

<table class="top-header">

    <tr>

        <!-- LOGO -->
        <td class="logo-column">

            <div class="logo-box">
                +
            </div>

        </td>

        <!-- COMPANY -->
        <td class="company-column">

            <div class="company-name">
                Apotek Gempas Farma
            </div>

            <div class="company-sub">

                Jalan Gempol Sari Sepatan Timur Banten,  15520 <br>

                Telp: 0895-4176-48792 |
                

            </div>

        </td>

        <!-- DOC INFO -->
        <td class="doc-column">

            <table class="doc-info">

                <tr>
                    <td>No Dokumen</td>
                    <td>:</td>
                    <td><?= $no_dokumen ?></td>
                </tr>

                <tr>
                    <td>Jenis</td>
                    <td>:</td>
                    <td><?= strtoupper($jenis) ?></td>
                </tr>

                <tr>
                    <td>Dicetak</td>
                    <td>:</td>
                    <td><?= date('d M Y H:i') ?></td>
                </tr>

            </table>

        </td>

    </tr>

</table>

<!-- LINE -->

<div class="header-line"></div>

<!-- TITLE -->

<div class="report-title">

    <h2>
        LAPORAN GLOBAL MANAJEMEN APOTEK
    </h2>

    <p>
        Dokumen resmi laporan transaksi apotek
    </p>
</div>

<!-- ================= PENJUALAN ================= -->

<?php if($jenis == 'penjualan' || $jenis == 'semua'): ?>

<div class="section-title">
    A. DATA PENJUALAN OBAT
</div>

<table class="table">

    <thead>

        <tr>

            <th width="5%">No</th>
            <th>No Transaksi</th>
            <th>Tanggal</th>
            <th>Kasir</th>
            <th>Total</th>

        </tr>

    </thead>

    <tbody>

    <?php
    $no = 1;
    $grand_penjualan = 0;
    ?>

    <?php foreach($penjualan as $p): ?>

        <?php $grand_penjualan += $p->total_harga; ?>

        <tr>

            <td class="text-center">
                <?= $no++ ?>
            </td>

            <td>
                TRX-<?= $p->id_pesanan ?>
            </td>

            <td>
                <?= date('d/m/Y H:i', strtotime($p->tanggal_pesan)) ?>
            </td>

            <td>
                <?= $p->kasir ?>
            </td>

            <td class="text-right">
                Rp <?= number_format($p->total_harga,0,',','.') ?>
            </td>

        </tr>

    <?php endforeach; ?>

    </tbody>

</table>

<table class="total-table">

    <tr>

        <td class="total-label">
            GRAND TOTAL PENJUALAN
        </td>

        <td class="total-value">
            Rp <?= number_format($grand_penjualan,0,',','.') ?>
        </td>

    </tr>

</table>

<?php endif; ?>

<!-- ================= PEMBELIAN ================= -->

<?php if($jenis == 'pembelian' || $jenis == 'semua'): ?>

<div class="section-title">
    B. DATA PEMBELIAN / RESTOCK
</div>

<table class="table">

    <thead>

        <tr>

            <th width="5%">No</th>
            <th>Kode Pembelian</th>
            <th>Tanggal</th>
            <th>Status</th>
            <th>Total</th>

        </tr>

    </thead>

    <tbody>

    <?php
    $no = 1;
    $grand_pembelian = 0;
    ?>

    <?php foreach($pembelian as $p): ?>

        <?php $grand_pembelian += $p->total; ?>

        <tr>

            <td class="text-center">
                <?= $no++ ?>
            </td>

            <td>
                <?= $p->kode_pembelian ?>
            </td>

            <td>
                <?= date('d/m/Y', strtotime($p->tanggal)) ?>
            </td>

            <td class="text-center">

                <span class="badge badge-success">
                    <?= strtoupper($p->status) ?>
                </span>

            </td>

            <td class="text-right">
                Rp <?= number_format($p->total,0,',','.') ?>
            </td>

        </tr>

    <?php endforeach; ?>

    </tbody>

</table>

<table class="total-table">

    <tr>

        <td class="total-label">
            GRAND TOTAL PEMBELIAN
        </td>

        <td class="total-value">
            Rp <?= number_format($grand_pembelian,0,',','.') ?>
        </td>

    </tr>

</table>

<?php endif; ?>

<!-- ================= FOOTER ================= -->

<div class="footer">

    <table class="footer-table">

        <tr>

            <td width="60%">

                <strong>
                    Catatan:
                </strong>

                <br><br>

                Dokumen ini dibuat secara otomatis oleh sistem
                Smart Pharmacy Management System dan dinyatakan valid.

            </td>

            <td class="signature">

                Tangerang,
                <?= date('d F Y') ?>

                <div class="signature-name">
                    ( Admin )
                </div>

            </td>

        </tr>

    </table>

</div>

</body>
</html>