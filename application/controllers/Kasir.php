    <?php
    defined('BASEPATH') OR exit('No direct script access allowed');

    class Kasir extends CI_Controller {

        public function __construct()
        {
            parent::__construct();

            $this->load->model('Kasir_model');
            $this->load->model('Produk_model');
            $this->load->library('midtrans');

            // Cek login (uncomment jika perlu)
            // if(!$this->session->userdata('logged_in')){
            //     redirect('auth');
            // }
        }

        public function index()
        {
            $data['title'] = 'Kasir';
            $data['produk'] = $this->Produk_model->getAllWithSatuan();
            
            $resep_id = $this->input->get('resep');
            if($resep_id){
                $data['resep_items'] = $this->db
                    ->select('detail_resep.produk_id as id, detail_resep.satuan, detail_resep.harga, detail_resep.jumlah as qty, produk.nama_produk')
                    ->join('produk','produk.id_produk = detail_resep.produk_id')
                    ->where('detail_resep.resep_id', $resep_id)
                    ->get('detail_resep')
                    ->result();
            } else {
                $data['resep_items'] = [];
            }

            $this->load->view('templates/header', $data);
            $this->load->view('templates/sidebar');
            $this->load->view('kasir/index', $data);
            $this->load->view('templates/footer');
        }

    public function simpan()
    {
        // Ambil data
        $produk       = json_decode($this->input->post('produk'), true);
        $subtotal     = $this->input->post('subtotal');
        $diskon       = $this->input->post('diskon');
        $ppn          = $this->input->post('ppn');
        $total        = $this->input->post('total');
        $bayar        = $this->input->post('bayar');
        $resep_id     = $this->input->post('resep_id');
        $metode_bayar = $this->input->post('metode_pembayaran');

        $bukti_pembayaran = null;
        
        if($metode_bayar == 'transfer'){
            // Cek apakah ada file yang diupload
            if(isset($_FILES['bukti_pembayaran']) && $_FILES['bukti_pembayaran']['error'] == 0){
                $config['upload_path'] = './uploads/bukti_transfer/';
                $config['allowed_types'] = 'jpg|jpeg|png|pdf';
                $config['max_size'] = 2048; // 2MB
                $config['encrypt_name'] = true;
                
                // Buat folder jika belum ada
                if (!is_dir($config['upload_path'])) {
                    mkdir($config['upload_path'], 0777, true);
                }
                
                $this->load->library('upload', $config);
                
                if ($this->upload->do_upload('bukti_pembayaran')) {
                    $upload_data = $this->upload->data();
                    $bukti_pembayaran = 'uploads/bukti_transfer/' . $upload_data['file_name'];
                } else {
                    $error = $this->upload->display_errors();
                    echo $this->_sweetAlert('Upload Gagal', 'Bukti pembayaran gagal diupload: ' . $error, 'error', null, true);
                    return;
                }
            } else {
                echo $this->_sweetAlert('Bukti Transfer Diperlukan', 'Harap upload bukti pembayaran transfer!', 'error', null, true);
                return;
            }
        }

        if(empty($produk)){
            echo $this->_sweetAlert('Keranjang Kosong', 'Silakan tambahkan produk terlebih dahulu', 'error', 'kasir');
            return;
        }

        if(empty($metode_bayar)){
            echo $this->_sweetAlert('Metode Bayar Belum Dipilih', 'Silakan pilih metode pembayaran', 'error', null, true);
            return;
        }

        $subtotal = is_numeric($subtotal) ? $subtotal : 0;
        $diskon   = is_numeric($diskon) ? $diskon : 0;
        $ppn      = is_numeric($ppn) ? $ppn : 0;
        $total    = is_numeric($total) ? $total : 0;
        $bayar    = is_numeric($bayar) ? $bayar : 0;

        if($total <= 0){
            echo $this->_sweetAlert('Total Tidak Valid', 'Total transaksi harus lebih dari 0', 'error', 'kasir');
            return;
        }

        // 🔥 Untuk transfer, otomatis bayar = total
        if($metode_bayar == 'transfer'){
            $bayar = $total;
        }

        if($bayar < $total){
            echo $this->_sweetAlert('Pembayaran Kurang', 'Uang yang dibayarkan kurang dari total', 'error', null, true);
            return;
        }

        $kembalian = $bayar - $total;
        $tipe = $resep_id ? 'resep' : 'non_resep';

        // Start transaksi
        $this->db->trans_start();

        // 🔥 INSERT PESANAN DENGAN BUKTI QRIS
        $data = [
            'user_id'        => $this->session->userdata('id_user') ?? 1,
            'tanggal_pesan'  => date('Y-m-d H:i:s'),
            'subtotal'       => $subtotal,
            'diskon'         => $diskon,
            'ppn'            => $ppn,
            'total_harga'    => $total,
            'bayar'          => $bayar,
            'kembalian'      => $kembalian,
            'tipe_transaksi' => $tipe,
            'resep_id'       => $resep_id ?: null,
            'metode_bayar'   => $metode_bayar,
            'status'         => ($metode_bayar == 'qris') ? 'pending' : 'selesai',
            'bukti_qris'     => $bukti_pembayaran  // 🔥 SIMPAN BUKTI TRANSFER
        ];

        $this->db->insert('pesanan', $data);
        $pesanan_id = $this->db->insert_id();

        // Loop produk
        foreach($produk as $p){
            $id_produk = isset($p['id']) ? $p['id'] : (isset($p['produk_id']) ? $p['produk_id'] : null);

            if(!$id_produk){
                $this->db->trans_rollback();
                echo $this->_sweetAlert('Error', 'ID Produk tidak valid', 'error', 'kasir');
                return;
            }

            $produk_db = $this->db->get_where('produk', ['id_produk' => $id_produk])->row();

            if (!$produk_db) {
                $this->db->trans_rollback();
                echo $this->_sweetAlert('Error', 'Produk ID '.$id_produk.' tidak ditemukan', 'error', 'kasir');
                return;
            }

            $konversi = isset($p['konversi']) ? (int)$p['konversi'] : 1;
            $qty      = isset($p['qty']) ? (int)$p['qty'] : 1;
            $qty_real = $qty * $konversi;

            if($produk_db->stok < $qty_real){
                $this->db->trans_rollback();
                echo $this->_sweetAlert('Stok Tidak Cukup', $produk_db->nama_produk.' stok tersisa '.$produk_db->stok, 'error', 'kasir');
                return;
            }

            // Kurangi stok
            $this->db->set('stok', 'stok - '.$qty_real, FALSE);
            $this->db->where('id_produk', $id_produk);
            $this->db->update('produk');

            // Insert detail
            $this->db->insert('detail_pesanan', [
                'pesanan_id' => $pesanan_id,
                'produk_id'  => $id_produk,
                'jumlah'     => $qty,
                'harga'      => $p['harga'],
                'subtotal'   => $p['harga'] * $qty,
                'satuan'     => $p['satuan'] ?? 'unit',
                'konversi'   => $konversi
            ]);
        }

        $this->db->trans_complete();

        if($this->db->trans_status() === FALSE){
            echo $this->_sweetAlert('Gagal', 'Transaksi gagal disimpan', 'error', 'kasir');
            return;
        }

        // Redirect berdasarkan metode bayar
        if($metode_bayar == 'qris') {
            echo $this->_sweetAlert('Berhasil!', 'Transaksi berhasil.', 'success', 'kasir/barcode/'.$pesanan_id);
        } else {
            echo $this->_sweetAlert('Berhasil!', 'Transaksi berhasil.', 'success', 'kasir/struk/'.$pesanan_id);
        }
    }   
        private function _sweetAlert($title, $message, $icon = 'success', $redirect = null, $back = false)
        {
            $redirectJs = '';
            if ($redirect) {
                $redirectJs = "window.location.href = '" . site_url($redirect) . "';";
            } elseif ($back) {
                $redirectJs = "window.history.back();";
            } else {
                $redirectJs = "setTimeout(() => { window.location.href = '" . site_url('kasir') . "'; }, 2000);";
            }

            return '<!DOCTYPE html>
            <html>
            <head>
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            </head>
            <body>
            <script>
                Swal.fire({
                    icon: "' . $icon . '",
                    title: "' . addslashes($title) . '",
                    text: "' . addslashes($message) . '",
                    confirmButtonText: "OK"
                }).then(() => {
                    ' . $redirectJs . '
                });
            </script>
            </body>
            </html>';
        }

public function struk($id)
{
    // Ambil data pesanan
    $data['pesanan'] = $this->db
        ->select('pesanan.*, users.nama as nama_kasir, resep.nama_pasien, resep.nama_dokter, resep.kode_resep')
        ->join('users', 'users.id_user = pesanan.user_id', 'left')
        ->join('resep', 'resep.id_resep = pesanan.resep_id', 'left')
        ->where('pesanan.id_pesanan', $id)
        ->get('pesanan')
        ->row();

    if(!$data['pesanan']){
        show_404();
    }

    // Ambil detail pesanan
    $data['detail'] = $this->db
        ->select('detail_pesanan.*, produk.nama_produk')
        ->join('produk', 'produk.id_produk = detail_pesanan.produk_id')
        ->where('detail_pesanan.pesanan_id', $id)
        ->get('detail_pesanan')
        ->result();

    // Jika transaksi resep, ambil dosis dari detail_resep
    if($data['pesanan']->tipe_transaksi == 'resep' && $data['pesanan']->resep_id){
        // Ambil dosis berdasarkan resep_id dan produk_id
        foreach($data['detail'] as $d){
            $dosis = $this->db
                ->select('dosis')
                ->where('resep_id', $data['pesanan']->resep_id)
                ->where('produk_id', $d->produk_id)
                ->get('detail_resep')
                ->row();
            
            $d->dosis = $dosis ? $dosis->dosis : '';
        }
    }

    $this->load->view('kasir/struk', $data);
}

        // =========================
        // STRUK THERMAL
        // =========================
        public function struk_thermal($id)
        {
            $data['pesanan'] = $this->db
                ->join('users','users.id_user = pesanan.user_id','left')
                ->where('id_pesanan', $id)
                ->get('pesanan')
                ->row();

            $data['detail'] = $this->db
                ->select('detail_pesanan.*, produk.nama_produk')
                ->join('produk','produk.id_produk = detail_pesanan.produk_id')
                ->where('pesanan_id', $id)
                ->get('detail_pesanan')
                ->result();

            $this->load->view('kasir/struk_thermal', $data);
        }

        // =========================
        // BARCODE QRIS
        // =========================
        public function barcode($id_pesanan)
        {
            $pesanan = $this->Kasir_model->getById($id_pesanan);

            if(!$pesanan){
                show_404();
            }

            $order_id = 'ORDER-' . $id_pesanan . '-' . time();

            $params = array(
                'transaction_details' => array(
                    'order_id' => $order_id,
                    'gross_amount' => (int)$pesanan->total_harga,
                ),
                'payment_type' => 'qris',
                'qris' => array(
                    'acquirer' => 'gopay'
                ),
                'customer_details' => array(
                    'first_name' => 'Customer',
                    'email' => 'customer@gmail.com',
                )
            );

            try {
                $response = \Midtrans\CoreApi::charge($params);

                $qr_url = null;
                if(isset($response->actions)){
                    foreach($response->actions as $action){
                        if($action->name == 'generate-qr-code'){
                            $qr_url = $action->url;
                        }
                    }
                }

                // Cek apakah sudah ada data qris
                $cek_qris = $this->db->get_where('qris', ['pesanan_id' => $id_pesanan])->row();
                
                if($cek_qris){
                    $this->db->where('pesanan_id', $id_pesanan);
                    $this->db->update('qris', [
                        'transaction_id' => $response->transaction_id,
                        'qr_url' => $qr_url
                    ]);
                } else {
                    $this->db->insert('qris', [
                        'pesanan_id' => $id_pesanan,
                        'transaction_id' => $response->transaction_id,
                        'qr_url' => $qr_url,
                        'status' => 'pending'
                    ]);
                }

                $data['pesanan'] = $pesanan;
                $data['qr_url'] = $qr_url;
                $data['transaction_id'] = $response->transaction_id;

                $this->load->view('kasir/barcode_v', $data);

            } catch (Exception $e) {
                echo $this->_sweetAlert('Error QRIS', $e->getMessage(), 'error', 'kasir');
            }
        }

        // =========================
        // CEK STATUS QRIS
        // =========================
        public function cek_qris($id_pesanan)
        {
            $qris = $this->Kasir_model->getByPesanan($id_pesanan);

            if(!$qris){
                echo json_encode(['status' => 'not_found']);
                return;
            }

            try {
                $status = \Midtrans\Transaction::status($qris->transaction_id);

                if($status->transaction_status == 'settlement'){
                    $this->Kasir_model->updateStatus($qris->transaction_id, 'settlement');
                    $this->db->where('id_pesanan', $id_pesanan);
                    $this->db->update('pesanan', ['status' => 'selesai']);
                }

                echo json_encode($status);

            } catch(Exception $e){
                echo json_encode(['error' => $e->getMessage()]);
            }
        }
// =========================
// PDF - A4 VERSION
// =========================
public function pdf($id)
{
    try {
        // Load library PDF
        $this->load->library('pdf');
        
        // Ambil data pesanan
        $data['pesanan'] = $this->db
            ->select('pesanan.*, users.nama as nama_kasir')
            ->join('users', 'users.id_user = pesanan.user_id', 'left')
            ->get_where('pesanan', ['id_pesanan' => $id])
            ->row();
        
        if (!$data['pesanan']) {
            show_404();
        }
        
        // Ambil detail pesanan
        $data['detail'] = $this->db
            ->select('detail_pesanan.*, produk.nama_produk')
            ->join('produk', 'produk.id_produk = detail_pesanan.produk_id')
            ->get_where('detail_pesanan', ['pesanan_id' => $id])
            ->result();
        
        // Generate HTML dari view
        $html = $this->load->view('kasir/struk_pdf', $data, true);
        
        // Generate PDF
        $this->pdf->loadHtml($html);
        $this->pdf->setPaper('A4', 'portrait');
        $this->pdf->render();
        $this->pdf->stream("struk_" . $id . ".pdf", array("Attachment" => 0));
        
    } catch (Exception $e) {
        // Jika error, tampilkan pesan
        echo "<h3>Error: " . $e->getMessage() . "</h3>";
        echo "<p>Silakan cek kembali data atau install Dompdf dengan Composer.</p>";
    }
}

// =========================
// PDF - THERMAL VERSION (80mm)
// =========================
public function pdf_thermal($id)
{
    try {
        $this->load->library('pdf');
        
        $data['pesanan'] = $this->db
            ->select('pesanan.*, users.nama as nama_kasir, resep.nama_pasien, resep.nama_dokter, resep.kode_resep')
            ->join('users', 'users.id_user = pesanan.user_id', 'left')
            ->join('resep', 'resep.id_resep = pesanan.resep_id', 'left')
            ->get_where('pesanan', ['id_pesanan' => $id])
            ->row();
        
        if (!$data['pesanan']) {
            show_404();
        }
        
        $data['detail'] = $this->db
            ->select('detail_pesanan.*, produk.nama_produk')
            ->join('produk', 'produk.id_produk = detail_pesanan.produk_id')
            ->get_where('detail_pesanan', ['pesanan_id' => $id])
            ->result();
        
        // Ambil dosis untuk resep
        if ($data['pesanan']->tipe_transaksi == 'resep' && $data['pesanan']->resep_id) {
            foreach ($data['detail'] as $d) {
                $dosis = $this->db
                    ->select('dosis')
                    ->where('resep_id', $data['pesanan']->resep_id)
                    ->where('produk_id', $d->produk_id)
                    ->get('detail_resep')
                    ->row();
                $d->dosis = $dosis ? $dosis->dosis : '';
            }
        }
        
        $html = $this->load->view('kasir/struk_pdf_thermal', $data, true);
        
        // Ukuran thermal: 80mm x custom height
        $this->pdf->loadHtml($html);
        $this->pdf->setPaper(array(0, 0, 226.77, 600), 'portrait');
        $this->pdf->render();
        $this->pdf->stream("struk_thermal_" . $id . ".pdf", array("Attachment" => 0));
        
    } catch (Exception $e) {
        echo "<h3>Error: " . $e->getMessage() . "</h3>";
        echo "<p>Silakan cek kembali data atau install Dompdf dengan Composer.</p>";
    }
}

        // =========================
        // AMBIL DATA RESEP (AJAX)
        // =========================
        public function dariResep($id)
        {
            $data = $this->db
                ->select('detail_resep.produk_id as id, detail_resep.satuan, detail_resep.harga, detail_resep.jumlah as qty, produk.nama_produk')
                ->from('detail_resep')
                ->join('produk', 'produk.id_produk = detail_resep.produk_id')
                ->where('detail_resep.resep_id', $id)
                ->get()
                ->result();
            
            echo json_encode($data);
        }
    }