<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Manajer extends CI_Controller {

    public function __construct()
    {
        parent::__construct();

        // load model
        $this->load->model('manajer_model');
        $this->load->model('Dashboard_model');

        if(!$this->session->userdata('logged_in')){
            redirect('auth/login');
        }
        if($this->session->userdata('role_id') != 3){
            redirect('auth/blocked');
        }
    }

public function index()
{
    $data['title'] = 'Dashboard Admin';

    // statistik
    $data['total_produk']    = $this->Dashboard_model->totalProduk();
    $data['total_pelanggan'] = $this->Dashboard_model->totalPelanggan();
    $data['kadaluarsa']      = $this->Dashboard_model->produkKadaluarsa();
    $data['stok_rendah']     = $this->Dashboard_model->stokRendah();

    // penjualan
    $data['penjualan_hari_ini'] = $this->Dashboard_model->penjualanHariIni();
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


    $data['order'] = $this->db
    ->select('pembelian.*, supplier.nama_supplier')
    ->join('supplier','supplier.id_supplier = pembelian.supplier_id')
    ->order_by('id_pembelian','DESC')
    ->limit(5)
    ->get('pembelian')
    ->result();

    // ========================
    // VIEW
    // ========================
    $this->load->view('templates/header', $data);
    $this->load->view('templates/sidebar');
    $this->load->view('manajer/dashboard', $data);
    $this->load->view('templates/footer');
}


    // select all users
    public function get_all_users()
    {
        $users = $this->Manajer_model->get_all_users();
        echo json_encode($users);
    }

    public function tambah_users()
    {
        $data['action'] = base_url('manajer/simpan_users');

        $this->load->view('templates/header');
        $this->load->view('templates/sidebar_manajer');
        $this->load->view('manajer/tambah_users',$data);
        $this->load->view('templates/footer');
    }

    public function simpan_users()
    {
        $data = $this->input->post(NULL, TRUE);

        $this->Manajer_model->insert_users($data);

        $this->session->set_flashdata('success','User berhasil ditambahkan');
        redirect('manajer');
    }





}