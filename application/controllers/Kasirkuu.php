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

    // ========================
    // 🔥 AMBIL RESEP
    // ========================
    $resep_id = $this->input->get('resep');

    if($resep_id){

        $data['resep_items'] = $this->db
            ->select('detail_resep.*, produk.nama_produk, produk.harga_jual')
            ->join('produk','produk.id_produk = detail_resep.produk_id')
            ->where('resep_id', $resep_id)
            ->get('detail_resep')
            ->result();

    } else {
        $data['resep_items'] = [];
    }

    // ========================
    // VIEW
    // ========================
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
        // 🔥 AMBIL DATA
        $produk   = json_decode($this->input->post('produk'), true);
        $subtotal = $this->input->post('subtotal');
        $diskon   = $this->input->post('diskon');
        $ppn      = $this->input->post('ppn');
        $total    = $this->input->post('total');
        $bayar    = $this->input->post('bayar');
        $resep_id = $this->input->post('resep_id');

        // ================= VALIDASI
        if(empty($produk)){
            show_error('Keranjang kosong');
        }

        if($total <= 0){
            show_error('Total tidak valid');
        }

        // ================= HITUNG KEMBALIAN
        $kembalian = $bayar - $total;

        // ================= INSERT PESANAN
        $data = [
            'user_id'        => $this->session->userdata('id') ?? 1,
            'tanggal_pesan'  => date('Y-m-d H:i:s'),

            'subtotal'       => $subtotal,
            'diskon'         => $diskon,
            'ppn'          => $ppn, // ⚠️ sesuaikan dengan nama kolom kamu
            'total_harga'    => $total,

            'bayar'          => $bayar,
            'kembalian'      => $kembalian,

            'status'         => 'selesai'
        ];

        $this->db->insert('pesanan', $data);
        $pesanan_id = $this->db->insert_id();

        // ================= INSERT DETAIL
        foreach($produk as $p){

            $this->db->insert('detail_pesanan', [
                'pesanan_id' => $pesanan_id,
                'produk_id'  => $p['id'],
                'jumlah'     => $p['qty'],
                'harga'      => $p['harga'],
                'subtotal'   => $p['subtotal']
            ]);

        }

        // ================= REDIRECT KE STRUK
        redirect('kasir/struk/'.$pesanan_id);
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