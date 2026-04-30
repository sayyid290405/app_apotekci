<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kasir extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
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

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('kasir/index', $data);
        $this->load->view('templates/footer');
    }

    // =========================
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
    }

    // =========================
    // STRUK
    // =========================
    public function struk($id)
    {
        $this->db->select('pesanan.*, users.nama');
        $this->db->join('users','users.id_user = pesanan.user_id','left');
        $data['pesanan'] = $this->db->get_where('pesanan', ['id_pesanan'=>$id])->row();

        if(!$data['pesanan']){
            show_404();
        }

        $this->db->select('detail_pesanan.*, produk.nama_produk');
        $this->db->join('produk','produk.id_produk = detail_pesanan.produk_id');
        $data['detail'] = $this->db->get_where('detail_pesanan', ['pesanan_id'=>$id])->result();

        $this->load->view('kasir/struk', $data);
    }

    // =========================
    // PDF
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
}