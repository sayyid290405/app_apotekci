<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Laporan_supplier extends CI_Controller {

    public function __construct(){
        parent::__construct();
        
        // Cek login
        if(!$this->session->userdata('id_user')){
            redirect('auth/login');
        }
        
        // Cek role supplier (role_id = 2)
        $role_id = $this->session->userdata('role_id');
        if($role_id != 2){
            redirect('dashboard');
        }
        
        $this->load->model('Laporan_supplier_model');
        $this->load->library('pdf');
    }

    /**
     * Halaman utama laporan supplier
     */
    public function index(){
        
        $supplier_id = $this->session->userdata('supplier_id') ?? $this->getSupplierIdByUser();
        
        if(!$supplier_id){
            show_error('Data supplier tidak ditemukan untuk akun Anda.');
            return;
        }
        
        // ================= DEFAULT PARAM =================
        $limitInput = $this->input->get('limit') ?? 'all';
        $tgl_awal   = $this->input->get('tgl_awal');
        $tgl_akhir  = $this->input->get('tgl_akhir');
        $search     = $this->input->get('search');
        
        // ================= HANDLE LIMIT =================
        $limit = ($limitInput == 'all') ? null : (int)$limitInput;
        
        // ================= AMBIL DATA =================
        $data['pembelian'] = $this->Laporan_supplier_model->filterPembelianBySupplier(
            $supplier_id, $limit, $tgl_awal, $tgl_akhir, $search
        );
        
        // ================= STATISTIK =================
        $data['stats'] = $this->Laporan_supplier_model->getSupplierStats(
            $supplier_id, $tgl_awal, $tgl_akhir
        );
        
        $data['status_count'] = $this->Laporan_supplier_model->getStatusCount($supplier_id);
        
        // ================= DATA SUPPLIER =================
        $this->db->select('nama_supplier, alamat, kontak');
        $this->db->from('supplier');
        $this->db->where('id_supplier', $supplier_id);
        $data['supplier'] = $this->db->get()->row();
        
        // ================= PARAMETER UNTUK VIEW =================
        $data['limit']     = $limitInput;
        $data['tgl_awal']  = $tgl_awal;
        $data['tgl_akhir'] = $tgl_akhir;
        $data['search']    = $search;
        
        $data['js'] = 'laporan_supplier.js';
        
        $this->load->view('supplier/templates/sidebar_supplier');
        $this->load->view('supplier/laporan_pembelian', $data);
    }
    
    /**
     * Get supplier ID dari user yang login
     * Asumsi: tabel users memiliki relasi ke supplier
     */
    private function getSupplierIdByUser(){
        $user_id = $this->session->userdata('id_user');
        
        // Cek apakah ada relasi di tabel users
        $this->db->select('s.id_supplier');
        $this->db->from('users u');
        $this->db->join('supplier s', 'u.email = s.kontak OR u.nama = s.nama_supplier', 'left');
        $this->db->where('u.id_user', $user_id);
        $result = $this->db->get()->row();
        
        if($result && $result->id_supplier){
            return $result->id_supplier;
        }
        
        // Fallback: ambil supplier pertama (untuk demo)
        $this->db->select('id_supplier');
        $this->db->from('supplier');
        $this->db->limit(1);
        $first = $this->db->get()->row();
        
        return $first ? $first->id_supplier : null;
    }
    
    /**
     * Detail pembelian untuk supplier
     */
    public function detail_pembelian($id){
        
        if(!$id) show_404();
        
        $supplier_id = $this->session->userdata('supplier_id') ?? $this->getSupplierIdByUser();
        
        $data['pembelian'] = $this->Laporan_supplier_model->getPembelianById($id, $supplier_id);
        
        if(!$data['pembelian']){
            show_404('Data pembelian tidak ditemukan atau bukan milik supplier Anda.');
            return;
        }
        
        $data['detail'] = $this->Laporan_supplier_model->getDetailPembelianBySupplier($id, $supplier_id);
        

        $this->load->view('supplier/templates/sidebar_supplier');
        $this->load->view('supplier/detail_pembelian', $data);
        $this->load->view('templates/footer');
    }
    
    /**
     * Print pembelian PDF
     */
    public function print_pembelian($id){
        
        if(!$id) show_404();
        
        $supplier_id = $this->session->userdata('supplier_id') ?? $this->getSupplierIdByUser();
        
        $data['pembelian'] = $this->Laporan_supplier_model->getPembelianById($id, $supplier_id);
        
        if(!$data['pembelian']){
            show_404('Data tidak ditemukan');
            return;
        }
        
        $data['detail'] = $this->Laporan_supplier_model->getDetailPembelianBySupplier($id, $supplier_id);
        $data['supplier'] = $this->db->get_where('supplier', ['id_supplier' => $supplier_id])->row();
        
        $html = $this->load->view('laporan_supplier/print_pembelian', $data, true);
        
        $this->pdf->generate($html, 'PO_'.$data['pembelian']->kode_pembelian, 'A4', 'portrait');
    }

        public function print_pembelian2($id){
        
        if(!$id) show_404();
        
        $supplier_id = $this->session->userdata('supplier_id') ?? $this->getSupplierIdByUser();
        
        $data['pembelian'] = $this->Laporan_supplier_model->getPembelianById($id, $supplier_id);
        
        if(!$data['pembelian']){
            show_404('Data tidak ditemukan');
            return;
        }
        
        $data['detail'] = $this->Laporan_supplier_model->getDetailPembelianBySupplier($id, $supplier_id);
        $data['supplier'] = $this->db->get_where('supplier', ['id_supplier' => $supplier_id])->row();
        
        $html = $this->load->view('supplier/print_pembelian', $data, true);
        
        $this->pdf->generate($html, 'PO_'.$data['pembelian']->kode_pembelian, 'A4', 'portrait');
    }
    
    /**
     * Export Excel untuk supplier
     */
    public function export_excel(){
        
        $supplier_id = $this->session->userdata('supplier_id') ?? $this->getSupplierIdByUser();
        
        $params = [
            'limit'     => $this->input->get('limit') ?? 'all',
            'tgl_awal'  => $this->input->get('tgl_awal'),
            'tgl_akhir' => $this->input->get('tgl_akhir'),
            'search'    => $this->input->get('search')
        ];
        
        $data_pembelian = $this->Laporan_supplier_model->getExportData($supplier_id, $params);
        
        $supplier = $this->db->get_where('supplier', ['id_supplier' => $supplier_id])->row();
        
        // Header Excel
        $filename = "Laporan_Pembelian_Supplier_" . date('Ymd_His') . ".xls";
        header("Content-Type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=$filename");
        
        echo $this->load->view('supplier/export_excel', [
            'pembelian' => $data_pembelian,
            'supplier' => $supplier,
            'params' => $params
        ], true);
    }
    
    /**
     * Cetak PDF laporan lengkap
     */
    public function cetak_pdf(){
        
        $supplier_id = $this->session->userdata('supplier_id') ?? $this->getSupplierIdByUser();
        
        $tgl_awal   = $this->input->get('tgl_awal');
        $tgl_akhir  = $this->input->get('tgl_akhir');
        $search     = $this->input->get('search');
        $limitInput = $this->input->get('limit') ?? 'all';
        
        $limit = ($limitInput == 'all') ? null : (int)$limitInput;
        
        $data = [
            'pembelian' => $this->Laporan_supplier_model->filterPembelianBySupplier(
                $supplier_id, $limit, $tgl_awal, $tgl_akhir, $search
            ),
            'supplier' => $this->db->get_where('supplier', ['id_supplier' => $supplier_id])->row(),
            'stats' => $this->Laporan_supplier_model->getSupplierStats($supplier_id, $tgl_awal, $tgl_akhir),
            'tgl_awal' => $tgl_awal ?? '-',
            'tgl_akhir' => $tgl_akhir ?? '-',
            'search' => $search ?? '-',
            'limit' => $limitInput,
            'no_dokumen' => 'LAP/SUP/' . date('Y/m/') . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT)
        ];
        
        $html = $this->load->view('supplier/pdf_laporan', $data, true);
        
        $this->pdf->generate($html, 'Laporan_Supplier_' . date('Ymd_His'), 'A4', 'portrait');
    }
}
?>