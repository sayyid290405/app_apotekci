<!DOCTYPE html>
<html>
<head>
    <title>QRIS</title>
</head>
<body>

<div style="text-align:center;margin-top:50px;">

    <h2>Pembayaran QRIS</h2>

    <h3>
        Rp <?= number_format($pesanan->total_harga,0,',','.') ?>
    </h3>

    <img src="<?= $qr_url ?>" width="300">

    <p>Silahkan scan QRIS</p>

</div>

<script>

setInterval(function(){

    fetch("<?= base_url('kasir/cek-qris/'.$pesanan->id_pesanan) ?>")
    .then(response => response.json())
    .then(data => {

        console.log(data);

        if(data.transaction_status == 'settlement'){

            alert('Pembayaran berhasil');

            window.location.href =
            "<?= base_url('kasir/struk/'.$pesanan->id_pesanan) ?>";
        }

    });

}, 3000);

</script>

</body>
</html>