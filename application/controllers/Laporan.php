<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Laporan extends CI_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('Laporan_model');
    }

    // ================= HALAMAN UTAMA =================
public function index(){

    $data['js'] = 'laporan.js';

    // ================= DEFAULT PARAM =================
    $jenis      = $this->input->get('jenis') ?? 'semua';
    $limitInput = $this->input->get('limit') ?? 'all';

    // 🔥 DEFAULT TANGGAL HARI INI
    $tgl_awal   = $this->input->get('tgl_awal') ?? date('Y-m-d');
    $tgl_akhir  = $this->input->get('tgl_akhir') ?? date('Y-m-d');

    $search     = $this->input->get('search');

    // ================= HANDLE LIMIT =================
    $limit = ($limitInput == 'all') ? null : $limitInput;

    // ================= DEFAULT DATA =================
    $data['penjualan'] = [];
    $data['pembelian'] = [];
    $data['penjualan_resep'] = [];

    // ================= FILTER =================
    if($jenis == 'penjualan'){

        $data['penjualan'] = $this->Laporan_model
            ->filterPenjualan($limit, $tgl_awal, $tgl_akhir, $search);

    } elseif($jenis == 'pembelian'){

        $data['pembelian'] = $this->Laporan_model
            ->filterPembelian($limit, $tgl_awal, $tgl_akhir, $search);

    } elseif($jenis == 'resep'){

        $data['penjualan_resep'] = $this->Laporan_model
            ->filterPenjualanResep($limit, $tgl_awal, $tgl_akhir, $search);

    } else { // 🔥 SEMUA (DEFAULT)

        $data['penjualan'] = $this->Laporan_model
            ->filterPenjualan($limit, $tgl_awal, $tgl_akhir, $search);

        $data['pembelian'] = $this->Laporan_model
            ->filterPembelian($limit, $tgl_awal, $tgl_akhir, $search);

        $data['penjualan_resep'] = $this->Laporan_model
            ->filterPenjualanResep($limit, $tgl_awal, $tgl_akhir, $search);
    }

    // ================= SUMMARY =================
    $data['total_hari_ini'] = $this->Laporan_model->getTotalHariIni() ?? 0;
    $data['obat_terlaris']  = $this->Laporan_model->getObatTerlaris() ?? [];

    // 🔥 COUNT (optional tapi bagus)
    $data['total_penjualan'] = $this->Laporan_model->countPenjualan($tgl_awal, $tgl_akhir, $search);
    $data['total_pembelian'] = $this->Laporan_model->countPembelian($tgl_awal, $tgl_akhir, $search);
    $data['total_resep'] = $this->Laporan_model->countPenjualanResep($tgl_awal, $tgl_akhir, $search);
    $data['total_pendapatan_resep'] = $this->Laporan_model->getTotalPendapatanResep($tgl_awal, $tgl_akhir, $search);

    // ================= KIRIM KE VIEW =================
    $data['jenis']     = $jenis;
    $data['limit']     = $limitInput;
    $data['tgl_awal']  = $tgl_awal;
    $data['tgl_akhir'] = $tgl_akhir;
    $data['search']    = $search;

    $this->load->view('templates/header', $data);
    $this->load->view('templates/sidebar');
    $this->load->view('laporan/index', $data);
    $this->load->view('templates/footer');
}
    // ================= DETAIL PEMBELIAN =================
    public function detail_pembelian($id){

        if(!$id) show_404();

        $data['detail'] = $this->Laporan_model->getDetailPembelian($id) ?? [];

        $this->load->view('templates/header');
        $this->load->view('templates/sidebar');
        $this->load->view('laporan/detail_pembelian', $data);
        $this->load->view('templates/footer');
    }

    // ================= PRINT PEMBELIAN =================
    public function print_pembelian($id){

        if(!$id) show_404();

        $this->load->library('pdf');

        $data['detail'] = $this->Laporan_model->getDetailPembelian($id) ?? [];

        $html = $this->load->view('laporan/print_pembelian', $data, true);

        $this->pdf->generate($html, 'laporan_pembelian');
    }

    // ================= REDIRECT INVOICE =================
    public function invoice($id){
        redirect('kasir/pdf/'.$id);
    }

    public function thermal($id){
        redirect('kasir/struk/'.$id);
    }
public function export_excel()
{
    // Ambil parameter filter
    $tgl_awal = $this->input->get('tgl_awal') ?? date('Y-m-01');
    $tgl_akhir = $this->input->get('tgl_akhir') ?? date('Y-m-d');
    $search = $this->input->get('search');
    $jenis = $this->input->get('jenis') ?? 'semua';
    
    // ================= DATA PENJUALAN =================
    $penjualan = [];
    $penjualan_resep = [];
    $pembelian = [];
    
    // Ambil data penjualan jika jenis penjualan atau semua
    if($jenis == 'penjualan' || $jenis == 'semua' || $jenis == 'resep'){
        $this->db->select('
            pesanan.id_pesanan,
            pesanan.tanggal_pesan,
            pesanan.total_harga,
            pesanan.subtotal,
            pesanan.diskon,
            pesanan.ppn,
            pesanan.biaya_resep,
            pesanan.bayar,
            pesanan.kembalian,
            pesanan.status,
            pesanan.tipe_transaksi,
            pesanan.metode_bayar,
            pesanan.created_at,
            users.nama as kasir,
            resep.kode_resep,
            resep.nama_pasien,
            resep.nama_dokter,
            resep.gambar_resep
        ')
        ->from('pesanan')
        ->join('users', 'users.id_user = pesanan.user_id', 'left')
        ->join('resep', 'resep.id_resep = pesanan.resep_id', 'left')
        ->where('pesanan.status', 'selesai')
        ->where('DATE(pesanan.tanggal_pesan) >=', $tgl_awal)
        ->where('DATE(pesanan.tanggal_pesan) <=', $tgl_akhir)
        ->order_by('pesanan.tanggal_pesan', 'DESC');
        
        if($search){
            $this->db->group_start()
                     ->like('pesanan.id_pesanan', $search)
                     ->or_like('users.nama', $search)
                     ->or_like('pesanan.metode_bayar', $search)
                     ->group_end();
        }
        
        $all_penjualan = $this->db->get()->result();
        
        // Pisahkan penjualan biasa dan resep
        foreach($all_penjualan as $p){
            $p->detail = $this->db->select('
                    detail_pesanan.*, 
                    produk.nama_produk
                ')
                ->from('detail_pesanan')
                ->join('produk', 'produk.id_produk = detail_pesanan.produk_id', 'left')
                ->where('detail_pesanan.pesanan_id', $p->id_pesanan)
                ->get()
                ->result();
            
            // Set default
            if(empty($p->metode_bayar)){
                $p->metode_bayar = 'tunai';
            }
            if(empty($p->status)){
                $p->status = 'selesai';
            }
            
            // Pisahkan berdasarkan tipe
            if($p->tipe_transaksi == 'resep'){
                $penjualan_resep[] = $p;
            } else {
                $penjualan[] = $p;
            }
        }
    }
    
    // ================= DATA PEMBELIAN =================
    if($jenis == 'pembelian' || $jenis == 'semua'){
        $this->db->select('
            pembelian.*,
            supplier.nama_supplier,
            users.nama as kasir
        ')
        ->from('pembelian')
        ->join('supplier', 'supplier.id_supplier = pembelian.supplier_id', 'left')
        ->join('users', 'users.id_user = pembelian.user_id', 'left')
        ->where('DATE(pembelian.tanggal) >=', $tgl_awal)
        ->where('DATE(pembelian.tanggal) <=', $tgl_akhir)
        ->order_by('pembelian.tanggal', 'DESC');
        
        if($search){
            $this->db->group_start()
                     ->like('pembelian.kode_pembelian', $search)
                     ->or_like('supplier.nama_supplier', $search)
                     ->group_end();
        }
        
        $pembelian = $this->db->get()->result();
        
        // Ambil detail produk untuk setiap pembelian
        foreach($pembelian as $p){
            $p->detail = $this->db->select('
                    detail_pembelian.*, 
                    produk.nama_produk,
                    satuan_produk.nama_satuan
                ')
                ->from('detail_pembelian')
                ->join('produk', 'produk.id_produk = detail_pembelian.produk_id', 'left')
                ->join('satuan_produk', 'satuan_produk.id = detail_pembelian.satuan_id', 'left')
                ->where('detail_pembelian.pembelian_id', $p->id_pembelian)
                ->get()
                ->result();
        }
    }
    
    // ================= KIRIM KE VIEW =================
    $data = [
        'jenis' => $jenis,
        'tgl_awal' => $tgl_awal,
        'tgl_akhir' => $tgl_akhir,
        'search' => $search,
        'penjualan' => $penjualan,
        'penjualan_resep' => $penjualan_resep,
        'pembelian' => $pembelian,
        'total_penjualan' => count($penjualan),
        'total_resep' => count($penjualan_resep),
        'total_pembelian' => count($pembelian),
    ];
    
    // Hitung total pendapatan
    $total_pendapatan = 0;
    foreach($penjualan as $p){
        $total_pendapatan += (int)$p->total_harga;
    }
    foreach($penjualan_resep as $p){
        $total_pendapatan += (int)$p->total_harga;
    }
    $data['total_pendapatan'] = $total_pendapatan;
    
    // Hitung total pembelian
    $total_pembelian_all = 0;
    foreach($pembelian as $p){
        $total_pembelian_all += (int)$p->total;
    }
    $data['total_pembelian_all'] = $total_pembelian_all;
    
    // ================= LOAD VIEW =================
    $html = $this->load->view('laporan/export_excel', $data, true);
    
    // ================= HEADER DOWNLOAD =================
    $filename = 'Laporan_' . date('Ymd_His');
    if($jenis == 'pembelian'){
        $filename = 'Laporan_Pembelian_' . date('Ymd_His');
    } elseif($jenis == 'resep'){
        $filename = 'Laporan_Resep_' . date('Ymd_His');
    } else {
        $filename = 'Laporan_Penjualan_' . date('Ymd_His');
    }
    
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="' . $filename . '.xls"');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    echo $html;
}
// Generate nomor dokumen: LAP/2026/05/000123
private function generateNoDokumen(){
    return 'LAP/'.date('Y/m').'/'.str_pad(mt_rand(1,999999),6,'0',STR_PAD_LEFT);
}

    // ================= CETAK PDF =================
    public function cetak()
{
    $this->load->library('pdf');

    // ================= FILTER =================

    $jenis      = $this->input->get('jenis') ?? 'semua';

    $limitInput = $this->input->get('limit') ?? 10;

    $tgl_awal   = $this->input->get('tgl_awal');

    $tgl_akhir  = $this->input->get('tgl_akhir');

    $search     = $this->input->get('search');

    $limit =
        ($limitInput == 'all')
        ? null
        : (int)$limitInput;

    // ================= DATA =================

    $data = [

        'penjualan' => [],

        'pembelian' => [],

        'jenis'     => $jenis,

        'tgl_awal'  => $tgl_awal ?? '-',

        'tgl_akhir' => $tgl_akhir ?? '-',

        'search'    => $search ?? '-',
    ];

    // ================= PENJUALAN =================

    if($jenis == 'penjualan' || $jenis == 'semua'){

        $data['penjualan'] =
            $this->Laporan_model
            ->filterPenjualan(
                $limit,
                $tgl_awal,
                $tgl_akhir,
                $search
            );
    }

    // ================= PEMBELIAN =================

    if($jenis == 'pembelian' || $jenis == 'semua'){

        $data['pembelian'] =
            $this->Laporan_model
            ->filterPembelian(
                $limit,
                $tgl_awal,
                $tgl_akhir,
                $search
            );
    }

    // ================= DOKUMEN =================

    $data['no_dokumen'] =
        $this->generateNoDokumen();

    // ================= QR =================

    $qrPayload = [

        'no_dokumen' =>
            $data['no_dokumen'],

        'jenis' =>
            $jenis,

        'periode' =>
            ($tgl_awal ?? '-') .
            ' s/d ' .
            ($tgl_akhir ?? '-'),

        'generated' =>
            date('c')
    ];

    $data['qr_text'] =
        json_encode($qrPayload);

    // ================= VIEW =================

    $html = $this->load->view(
        'laporan/pdf_laporan',
        $data,
        true
    );

    // ================= PDF =================

    $this->pdf->generate(
        $html,
        'laporan_'.$data['no_dokumen'],
        'A4',
        'portrait'
    );
}


}