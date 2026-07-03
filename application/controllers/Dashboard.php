<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

    public function __construct()
    {
        parent::__construct();

        // load model
        $this->load->model('Dashboard_model');

        // cek login
        if(!$this->session->userdata('logged_in')){
            redirect('auth/login');
        }

        // cek role (admin saja)
        if($this->session->userdata('role_id') != 1){
            redirect('auth/blocked');
        }
    }

   public function index()
{
    $data['title'] = 'Dashboard Admin';

    // 1. FIX: Sesuaikan nama kolom ke 'stok_minimal'
    $data['stok_minim_data'] = $this->db->query("
        SELECT * FROM produk 
        WHERE stok <= stok_minimal 
        ORDER BY stok ASC
    ")->result();

    // 2. FIX: Sesuaikan nama kolom ke 'tanggal_kadaluarsa'
    $data['expired_soon_data'] = $this->db->query("
        SELECT *, DATEDIFF(tanggal_kadaluarsa, CURDATE()) as sisa_hari 
        FROM produk 
        WHERE tanggal_kadaluarsa <= DATE_ADD(CURDATE(), INTERVAL 6 MONTH)
        AND tanggal_kadaluarsa > CURDATE()
        ORDER BY tanggal_kadaluarsa ASC
    ")->result();

    // Statistik dari Model (Pastikan di Model juga nama kolomnya benar)
    $data['total_produk']    = $this->Dashboard_model->totalProduk();
    $data['total_pelanggan'] = $this->Dashboard_model->totalPelanggan();
    $data['kadaluarsa']      = $this->Dashboard_model->produkKadaluarsa();
    $data['stok_rendah']     = $this->Dashboard_model->stokRendah();

    // Penjualan hari ini
    $data['penjualan_hari_ini'] = $this->Dashboard_model->penjualanHariIni();

    // ========================
    // GRAFIK PENJUALAN
    // ========================
    $raw = $this->Dashboard_model->grafikPenjualan();

    $range = [];

    for($i=6; $i>=0; $i--){
        $date = date('Y-m-d', strtotime("-$i days"));
        $range[$date] = 0;
    }

    foreach($raw as $r){
        $range[$r->tanggal] = (int)$r->total;
    }

    $labels = [];
    $chart_data = [];

    foreach($range as $tgl => $val){
        $labels[] = date('d M', strtotime($tgl));
        $chart_data[] = $val;
    }

    $data['chart_labels'] = $labels;
    $data['chart_data']   = $chart_data;
    $data['js'] = 'dashboard.js';


    // Order Pembelian Terbaru
    $data['order'] = $this->db  
    ->select('pembelian.*, supplier.nama_supplier')
    ->join('supplier','supplier.id_supplier = pembelian.supplier_id')
    ->order_by('id_pembelian','DESC')
    ->limit(5)
    ->get('pembelian')
    ->result();

    $this->load->view('templates/sidebar');
     $this->load->view('templates/header', $data);
    $this->load->view('dashboard/index', $data);
    $this->load->view('templates/footer');
}

}