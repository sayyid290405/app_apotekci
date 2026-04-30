<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kasir extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
<<<<<<< HEAD
        $this->load->model('Kasir_model');
        $this->load->library('session');

        if(!$this->session->userdata('logged_in')){
            redirect('auth');
        }
    }

    public function index()
    {
        $data['title'] = 'Kasir';
        $data['produk'] = $this->Kasir_model->getProduk();
=======

        // 🔥 LOAD MODEL WAJIB
        $this->load->model('Kasir_model');
        $this->load->model('Produk_model');

        // OPTIONAL: cek login
        // if(!$this->session->userdata('logged_in')){
        //     redirect('auth');
        // }
    }

    // =========================
    // HALAMAN KASIR
    // =========================
    public function index()
    {
        $data['title'] = 'Kasir';

        // 🔥 AMBIL PRODUK + SATUAN
        $data['produk'] = $this->Produk_model->getAllWithSatuan();

        // 🔥 AMBIL RESEP (JIKA ADA)
        $resep_id = $this->input->get('resep');

        if($resep_id){
        $data['resep_items'] = $this->db
            ->select('
                detail_resep.produk_id as id, 
                detail_resep.satuan, 
                detail_resep.harga, 
                detail_resep.jumlah as qty, 
                produk.nama_produk
            ') // 🔥 Ambil HARGA & SATUAN dari detail_resep, bukan dari produk
            ->join('produk','produk.id_produk = detail_resep.produk_id')
            ->where('detail_resep.resep_id', $resep_id)
            ->get('detail_resep')
            ->result();
        } else {
            $data['resep_items'] = [];
        }
>>>>>>> ee11432ab565d27cb1eddab3a20fae4e15c9c552

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('kasir/index', $data);
        $this->load->view('templates/footer');
    }

    // =========================
<<<<<<< HEAD
    // SIMPAN TRANSAKSI (FIXED)
    // =========================
    public function simpan()
    {
        $produk = json_decode($this->input->post('produk'), true);
        $total  = (int)$this->input->post('total');
        $bayar  = (int)$this->input->post('bayar');
        $kembali = $bayar - $total;

        // VALIDASI
        if(!$produk || empty($produk)){
            $this->session->set_flashdata('error','Keranjang kosong');
            redirect('kasir');
        }

        if($bayar < $total){
            $this->session->set_flashdata('error','Uang tidak cukup');
            redirect('kasir');
        }

        // =========================
        // START TRANSACTION 🔥
        // =========================
        $this->db->trans_start();

        // insert pesanan
        $this->db->insert('pesanan', [
            'user_id' => $this->session->userdata('id_user'),
            'total_harga' => $total,
            'bayar' => $bayar,
            'kembalian' => $kembali,
            'status' => 'selesai',
            'created_at' => date('Y-m-d H:i:s')
        ]);

        $id_pesanan = $this->db->insert_id();

        foreach($produk as $p){

            // VALIDASI ITEM
            if(!isset($p['id']) || !isset($p['qty']) || !isset($p['harga'])){
                continue;
            }

            // CEK STOK
            $dbProduk = $this->db->get_where('produk', [
                'id_produk' => $p['id']
            ])->row();

            if(!$dbProduk || $dbProduk->stok < $p['qty']){
                $this->db->trans_rollback();
                $this->session->set_flashdata('error','Stok tidak cukup: '.$p['nama']);
                redirect('kasir');
            }

            $subtotal = $p['qty'] * $p['harga'];

            // detail pesanan
            $this->db->insert('detail_pesanan', [
                'pesanan_id' => $id_pesanan,
                'produk_id'  => $p['id'],
                'jumlah'     => $p['qty'],
                'harga'      => $p['harga'],
                'subtotal'   => $subtotal
            ]);

            // update stok
            $this->db->set('stok', 'stok-'.$p['qty'], FALSE)
                     ->where('id_produk', $p['id'])
                     ->update('produk');
        }

        // =========================
        // END TRANSACTION
        // =========================
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE){
            $this->session->set_flashdata('error','Gagal menyimpan transaksi');
            redirect('kasir');
        }

        redirect('kasir/struk/'.$id_pesanan);
=======
    // SIMPAN TRANSAKSI
    // =========================
    public function simpan()
    {
        // ================= AMBIL DATA
        $produk   = json_decode($this->input->post('produk'), true);
        $subtotal = $this->input->post('subtotal');
        $diskon   = $this->input->post('diskon');
        $ppn      = $this->input->post('ppn');
        $total    = $this->input->post('total');
        $bayar    = $this->input->post('bayar');
        $resep_id = $this->input->post('resep_id');

        // ================= VALIDASI DASAR
        if(empty($produk)){
            show_error('Keranjang kosong');
        }

        // ================= AMANKAN NILAI
        $subtotal = is_numeric($subtotal) ? $subtotal : 0;
        $diskon   = is_numeric($diskon) ? $diskon : 0;
        $ppn      = is_numeric($ppn) ? $ppn : 0;
        $total    = is_numeric($total) ? $total : 0;
        $bayar    = is_numeric($bayar) ? $bayar : 0;

        if($total <= 0){
            show_error('Total tidak valid');
        }

        // ================= HITUNG KEMBALIAN
        $kembalian = $bayar - $total;

        // ================= TIPE TRANSAKSI
        $tipe = $resep_id ? 'resep' : 'non_resep';

        // ================= START TRANSACTION
        $this->db->trans_start();

        // ================= INSERT PESANAN
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
            'metode_bayar'   => 'cash',
            'status'         => 'selesai'
        ];

        $this->db->insert('pesanan', $data);
        $pesanan_id = $this->db->insert_id();

        // ================= LOOP PRODUK
        foreach($produk as $p){
            // 1. Amankan ID
            $id_produk = isset($p['id']) ? $p['id'] : (isset($p['produk_id']) ? $p['produk_id'] : null);

            if(!$id_produk){
                $this->db->trans_rollback();
                show_error('ID Produk tidak valid dalam data keranjang');
            }

            // 2. Cek apakah produk ada di DB
            $produk_db = $this->db
                ->get_where('produk', ['id_produk' => $id_produk])
                ->row();

            if (!$produk_db) {
                $this->db->trans_rollback();
                show_error('Produk dengan ID '.$id_produk.' tidak ditemukan');
            }

            // 3. Hitung Kuantitas Real (berdasarkan konversi satuan)
            $konversi = isset($p['konversi']) ? (int)$p['konversi'] : 1;
            $qty      = isset($p['qty']) ? (int)$p['qty'] : (isset($p['jumlah']) ? (int)$p['jumlah'] : 1);
            $qty_real = $qty * $konversi;

            // 4. Validasi Stok
            if($produk_db->stok < $qty_real){
                $this->db->trans_rollback();
                show_error('Stok tidak cukup: '.$produk_db->nama_produk.' (Sisa: '.$produk_db->stok.')');
            }

            // ================= KURANGI STOK
            $this->db->set('stok', 'stok - '.$qty_real, FALSE);
            $this->db->where('id_produk', $p['id']);
            $this->db->update('produk');

            // ================= INSERT DETAIL
            $this->db->insert('detail_pesanan', [
                'pesanan_id' => $pesanan_id,
                'produk_id'  => $p['id'],
                'jumlah'     => $qty,
                'harga'      => $p['harga'],
                'subtotal'   => $p['harga'] * $qty,

                // 🔥 tambahan multi satuan
                'satuan'     => $p['satuan'] ?? 'unit',
                'konversi'   => $konversi
            ]);
        }

        // ================= END TRANSACTION
        $this->db->trans_complete();

        if($this->db->trans_status() === FALSE){
            show_error('Gagal menyimpan transaksi');
        }

        // ================= REDIRECT
        redirect('kasir/struk/'.$pesanan_id);
>>>>>>> ee11432ab565d27cb1eddab3a20fae4e15c9c552
    }

    // =========================
    // STRUK
    // =========================
    public function struk($id)
    {
<<<<<<< HEAD
        $this->db->select('pesanan.*, users.nama');
        $this->db->join('users','users.id_user = pesanan.user_id','left');
        $data['pesanan'] = $this->db->get_where('pesanan', ['id_pesanan'=>$id])->row();

        if(!$data['pesanan']){
            show_404();
        }

        $this->db->select('detail_pesanan.*, produk.nama_produk');
        $this->db->join('produk','produk.id_produk = detail_pesanan.produk_id');
        $data['detail'] = $this->db->get_where('detail_pesanan', ['pesanan_id'=>$id])->result();
=======
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
>>>>>>> ee11432ab565d27cb1eddab3a20fae4e15c9c552

        $this->load->view('kasir/struk', $data);
    }

    // =========================
<<<<<<< HEAD
    // PDF
=======
    // EXPORT PDF
>>>>>>> ee11432ab565d27cb1eddab3a20fae4e15c9c552
    // =========================
    public function pdf($id)
    {
        $this->load->library('pdf');

        $this->db->select('pesanan.*, users.nama');
        $this->db->join('users','users.id_user = pesanan.user_id','left');
        $data['pesanan'] = $this->db->get_where('pesanan', ['id_pesanan'=>$id])->row();

        $this->db->select('detail_pesanan.*, produk.nama_produk');
        $this->db->join('produk','produk.id_produk = detail_pesanan.produk_id');
        $data['detail'] = $this->db->get_where('detail_pesanan', ['pesanan_id'=>$id])->result();

        $html = $this->load->view('kasir/struk_pdf', $data, true);

        $this->pdf->loadHtml($html);
        $this->pdf->setPaper('A4', 'portrait');
        $this->pdf->render();
        $this->pdf->stream("struk.pdf", ["Attachment"=>true]);
    }
<<<<<<< HEAD
=======

    public function dariResep($id)
{
    $this->db->select('
        detail_resep.produk_id as id,
        detail_resep.satuan,
        detail_resep.harga, 
        detail_resep.jumlah as qty,
        produk.nama_produk
    ');
    $this->db->from('detail_resep');
    $this->db->join('produk', 'produk.id_produk = detail_resep.produk_id');
    $this->db->where('detail_resep.resep_id', $id);
    
    $data = $this->db->get()->result();
    echo json_encode($data);
}

>>>>>>> ee11432ab565d27cb1eddab3a20fae4e15c9c552
}