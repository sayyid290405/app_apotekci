<!DOCTYPE html>
<html>
<head>
    <title><?= $title ?></title>
    <style>
        body { font-family: sans-serif; font-size: 9px; color: #333; line-height: 1.2; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; table-layout: fixed; }
        th, td { border: 1px solid #444; padding: 4px 2px; word-wrap: break-word; vertical-align: middle; }
        th { background-color: #4e73df; color: white; text-align: center; text-transform: uppercase; font-size: 8px; }
        
        /* Kop Surat Styling */
        .kop-surat { width: 100%; border-bottom: 2px double #000; margin-bottom: 10px; padding-bottom: 5px; }
        .nama-pt { font-size: 18px; font-weight: bold; color: #1a4da1; text-transform: uppercase; margin: 0; text-align: center; }
        .info-pt { font-size: 9px; color: #555; text-align: center; margin-top: 2px; }
        
        .judul-laporan { text-align: center; text-decoration: underline; font-size: 14px; font-weight: bold; margin-top: 10px; }
        .cetak-info { text-align: center; font-size: 8px; color: #666; margin-bottom: 10px; }
        
        /* Helpers */
        .text-right { text-align: right; padding-right: 5px; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .bg-light { background-color: #f8f9fc; }
    </style>
</head>
<body>
    <div class="kop-surat">
        <h1 class="nama-pt">PT MAJU JAYA ELEKTRONIK</h1>
        <div class="info-pt">
            Gedung Maju Jaya Tower, Lt. 5, Jl. Jenderal Sudirman No. 123, Jakarta Pusat 10220<br>
            Telp: (021) 555-0888 | Email: info@majujayaelektronik.co.id | Website: www.majujayaelektronik.co.id
        </div>
    </div>

    <div class="judul-laporan">LAPORAN DATA ORDER PENJUALAN</div>
    <div class="cetak-info">
        Periode: <?= ($filter['from']) ? date('d/m/Y', strtotime($filter['from'])) : 'Awal' ?> 
        s/d <?= ($filter['to']) ? date('d/m/Y', strtotime($filter['to'])) : 'Sekarang' ?>
        <br>
        Dicetak pada: <?= date('d-m-Y H:i:s') ?>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 20px;">NO</th>
                <th style="width: 50px;">KODE</th>
                <th style="width: 100px;">CUSTOMER</th>
                <th>PRODUK</th>
                <th style="width: 60px;">SALES</th>
                <th style="width: 50px;">STATUS</th>
                <th style="width: 70px;">TGL ORDER</th>
                <th style="width: 70px;">TGL UPDATE</th> <th style="width: 80px;">TOTAL</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1; 
            $grand_total = 0; 
            if(!empty($orders)):
                foreach($orders as $o): 
            ?>
            <tr>
                <td class="text-center"><?= $no++ ?></td>
                <td class="text-center font-bold"><?= $o->order_code ?></td>
                <td><?= $o->customer_name ?></td>
                <td>
                    <div style="font-size: 8px;"><?= !empty($o->product_names) ? $o->product_names : '-' ?></div>
                </td>
                <td class="text-center"><?= $o->sales_name ?></td>
                <td class="text-center"><?= ucfirst($o->status) ?></td>
                <td class="text-center"><?= date('d/m/Y', strtotime($o->order_date)) ?></td>
                <td class="text-center">
                    <?php 
                        // Implementasi histori tanggal sesuai struktur tabel
                        $tgl_update = '-';
                        if ($o->status == 'dikirim' && !empty($o->sent_at)) $tgl_update = date('d/m/Y H:i', strtotime($o->sent_at));
                        elseif ($o->status == 'selesai' && !empty($o->completed_at)) $tgl_update = date('d/m/Y H:i', strtotime($o->completed_at));
                        elseif ($o->status == 'dibatalkan' && !empty($o->canceled_at)) $tgl_update = date('d/m/Y H:i', strtotime($o->canceled_at));
                        
                        echo $tgl_update;
                    ?>
                </td>
                <td class="text-right font-bold">Rp <?= number_format($o->total_price, 0, ',', '.') ?></td>
            </tr>
            <?php 
                $grand_total += $o->total_price;
                endforeach; 
            else: ?>
                <tr>
                    <td colspan="9" class="text-center">Data tidak ditemukan</td>
                </tr>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr class="bg-light">
                <th colspan="8" class="text-right" style="color: #333;">GRAND TOTAL</th>
                <th class="text-right" style="color: #333;">Rp <?= number_format($grand_total, 0, ',', '.') ?></th>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top: 20px; text-align: right; padding-right: 30px;">
        <p>Jakarta, <?= date('d F Y') ?></p>
        <br><br><br>
        <p><strong>( ____________________ )</strong><br>Manager Operasional</p>
    </div>
</body>
</html>