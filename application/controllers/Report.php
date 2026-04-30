<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report extends CI_Controller {

    public function __construct() {
        parent::__construct();

        // 1. Keamanan Akses
        if (!$this->session->userdata('logged_in')) {
            redirect('auth');
        }

        // Hanya Manager (3) dan Admin (1)
        if (!in_array($this->session->userdata('role_id'), [1,3])) {
            show_error("Anda tidak memiliki akses ke laporan!", 403);
        }

        // Memastikan model terload dengan benar
        $this->load->model(['M_report','M_sales']);
        $this->load->helper('url');
    }

    public function index() {
    $filter = [
        'from'     => $this->input->get('from'),
        'to'       => $this->input->get('to'),
        'sales_id' => $this->input->get('sales_id'),
        'status'   => $this->input->get('status'),
    ];

    $orders = $this->M_report->get_filtered_report($filter);
    $orders = $orders ?: [];

    // Ambil data chart dari model
    $statusData = $this->M_report->get_chart_data($filter);
    $statusData = $statusData ?: [];

    // --- PERBAIKAN DI SINI ---
    // Gunakan huruf kecil semua (lowercase) untuk memastikan kecocokan key
    $all_status = ['draft', 'dikirim', 'selesai', 'dibatalkan'];
    $status_totals_map = array_fill_keys($all_status, 0);

    foreach($statusData as $row){
        // Pastikan key status diubah ke lowercase sebelum dipetakan
        $s = strtolower($row['status']); 
        if(array_key_exists($s, $status_totals_map)) {
            $status_totals_map[$s] = (int)$row['total_order'];
        }
    }

    $data = [
        'title'         => 'Laporan Penjualan',
        'orders'        => $orders,
        'sales'         => $this->M_sales->get_all(),
        'status_labels' => array_keys($status_totals_map), // Ambil key sebagai label
        'status_totals' => array_values($status_totals_map), // Ambil value sebagai data grafik
        'filter'        => $filter
    ];

    $this->load->view('template/header', $data);
    $this->load->view('template/sidebar', $data);
    $this->load->view('report/index', $data);
    $this->load->view('template/footer');
}
    // Bagian export_pdf yang disempurnakan
    public function export_pdf() {
        $filter = [
            'from'     => $this->input->get('from'),
            'to'       => $this->input->get('to'),
            'sales_id' => $this->input->get('sales_id'),
            'status'   => $this->input->get('status')
        ];

        $data['orders'] = $this->M_report->get_filtered_report($filter);
        
        // Proteksi: Jika data kosong, beri peringatan atau set array kosong
        if (empty($data['orders'])) {
            echo "<script>alert('Tidak ada data untuk diexport!'); window.close();</script>";
            return;
        }

        $data['title']  = "Laporan Penjualan PT Maju Jaya Elektronik";
        $data['filter'] = $filter; 

        $this->load->library('pdf');
        $html = $this->load->view('report/pdf_template', $data, true);

        // Pastikan fungsi ini namanya 'generate' atau 'create' sesuai isi library Anda
        $this->pdf->generate($html, "Laporan_Order_".date('Ymd'), 'A4', 'landscape');
    }
}