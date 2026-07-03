<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard_manajer extends CI_Controller
{
    public function __construct() 
    {
        parent::__construct();
        $this->load->library(['session', 'form_validation']);
        $this->load->helper(['url', 'form']);
        $this->load->database();
        $this->load->model('Dashboard_manajer_model');
    }
    
    public function grafik_penjualan()
    {

        $this->load->model('Dashboard_manajer_model');

        $grafik = $this->Dashboard_manajer_model->grafik_penjualan();

        $labels = [];
        $data   = [];

        foreach ($grafik as $g) {
            $labels[] = date('d M', strtotime($g->tgl));
            $data[]   = (int)$g->total;
        }

        $data_view['chart_labels'] = $labels;
        $data_view['chart_data']   = $data;

        $this->load->view('manajer/templates/header');
        $this->load->view('manajer/templates/sidebar_manajer');
        $this->load->view('statistik/grafik_penjualan', $data_view);
        $this->load->view('manajer/templates/footer');
        
    }
    public function export_penjualan()
    {
        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=laporan_penjualan.xls");
        header("Pragma: no-cache");
        header("Expires: 0");

        // UBAH 'penjualan' MENJADI 'pesanan'
        $data['penjualan'] = $this->db->get('pesanan')->result(); 

        $this->load->view('statistik/export_penjualan', $data);
    }

public function stok_obat()
{
    // Pakai method yang sudah JOIN dengan kategori
    $data['produk'] = $this->Dashboard_manajer_model->get_produk_with_kategori();
    $data['total_stok'] = $this->Dashboard_manajer_model->get_total_stok();
    $data['stok_minim'] = $this->Dashboard_manajer_model->get_stok_minim_obat();
    $data['kadaluarsa'] = $this->Dashboard_manajer_model->get_kadaluarsa();
    $data['stat_kategori'] = $this->Dashboard_manajer_model->get_statistik_kategori();

    $this->load->view('manajer/templates/header', $data);
    $this->load->view('manajer/templates/sidebar_manajer');
    $this->load->view('statistik/grafik_stok', $data);
    $this->load->view('manajer/templates/footer');
}

public function export_stok_excel()
{
    $data['produk'] = $this->Dashboard_manajer_model->get_produk_with_kategori();
    $data['total_stok'] = $this->Dashboard_manajer_model->get_total_stok();
    $data['stok_minim'] = $this->Dashboard_manajer_model->get_stok_minim_obat();
    $data['kadaluarsa'] = $this->Dashboard_manajer_model->get_kadaluarsa();
    
    $this->load->view('statistik/export_stok_excel', $data);
    
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="Laporan_Stok_Obat_' . date('Ymd_His') . '.xls"');
}

}
