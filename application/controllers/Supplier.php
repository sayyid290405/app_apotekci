<?php
class Supplier extends CI_Controller {

    public function __construct()
    {
        parent::__construct();

        $this->load->model('Supplier_model');
        $this->load->model('Pembelian_model');
        $data['supplier_info'] = !empty($data['pembelian'])
    ? $data['pembelian'][0]
    : null;

    
    }

    // =========================
    // LIST APPROVED PEMBELIAN
    // =========================
    public function approved_list()
    {
        $data['title'] = "Daftar Pembelian Disetujui";

        $data['pembelian'] =
            $this->Supplier_model->get_approved_pembelian();
        $this->load->view(
            'supplier/templates/header',
        );    
        


        $this->load->view(
            'supplier/templates/sidebar_supplier',
            $data
        );

        $this->load->view(
            'supplier/customer_order',
            $data
        );

        $this->load->view(
            'supplier/templates/footer'
        );
    }

    // =========================
    // HISTORI PEMBAYARAN
    // =========================
    public function pembelian_selesai()
    {
        $data['pembayaran'] =
            $this->Supplier_model->get_pembayaran_selesai();

        $this->load->view(
            'supplier/templates/header',
        );    

        $this->load->view(
            'supplier/templates/sidebar_supplier',
            $data
        );

        $this->load->view(
            'supplier/pembelian_selesai',
            $data
        );

        $this->load->view(
            'supplier/templates/footer'
        );
    }

    // =========================
    // DETAIL ITEM
    // =========================
    public function detail_item($id)
    {
        $data['detail'] =
            $this->Supplier_model->get_detail_pembelian($id);

        echo json_encode($data['detail']);
    }

    // =========================
    // UPDATE STATUS SUPPLIER
    // =========================
    public function update_status_supplier()
    {
        $id = $this->input->post('id_pembelian');

        $status = $this->input->post('status');

        $approved_by =
            $this->session->userdata('id_user');

        $tanggal = date('Y-m-d H:i:s');

        $catatan = $this->input->post('catatan');

        $this->Pembelian_model->update_pembelian(
            $id,
            $status,
            $approved_by,
            $tanggal,
            $catatan
        );

        redirect('Supplier/approved_list');
    }

    // =========================
    // DASHBOARD SUPPLIER
    // =========================
    public function supplier_dashboard()
    {
        $data['stats'] = $this->db
    ->select("
        COUNT(*) as jumlah_transaksi,
        COALESCE(SUM(total),0) as total_pembelian
    ")
    ->from('pembelian')
    ->get()
    ->row();

    $data['status_count'] = [

    'menunggu' => $this->db
        ->where('status','menunggu')
        ->count_all_results('pembelian'),

    'disetujui' => $this->db
        ->where('status','disetujui')
        ->count_all_results('pembelian'),

    'diproses' => $this->db
        ->where('status','diproses')
        ->count_all_results('pembelian'),

    'diterima' => $this->db
        ->where('status','diterima')
        ->count_all_results('pembelian'),

    'selesai' => $this->db
        ->where('status','selesai')
        ->count_all_results('pembelian')

];


        $data['supplier'] =
            $this->Supplier_model->get_order();
        $this->load->view(
            'supplier/templates/header',
        );    

        $this->load->view(
            'supplier/templates/sidebar_supplier',
            $data
        );

        $this->load->view(
            'supplier/dashboard',
            $data
        );

        $this->load->view(
            'supplier/templates/footer'
        );
    }

    // =========================
    // UPDATE STATUS ADMIN
    // =========================
    public function update_status_admin()
    {
        $id = $this->input->post('id_pembelian');

        $status = $this->input->post('status');

        $approved_by =
            $this->session->userdata('id_user');

        $tanggal = date('Y-m-d H:i:s');

        $catatan = $this->input->post('catatan');

        $this->Pembelian_model->update_pembelian(
            $id,
            $status,
            $approved_by,
            $tanggal,
            $catatan
        );

        redirect('pembelian/terima_barang_supplier');
    }

    // =========================
    // INDEX SUPPLIER
    // =========================
    public function index()
    {
        $this->load->library('pagination');

        $keyword = $this->input->get('q');

        $data['keyword'] = $keyword;

        $data['js'] = 'supplier.js';

        $total =
            $this->Supplier_model->countFiltered($keyword);

        $config['base_url'] =
            base_url('supplier/index?q='.$keyword);

        $config['total_rows'] = $total;

        $config['per_page'] = 5;

        $this->pagination->initialize($config);

        $page = $this->uri->segment(3);

        $data['supplier'] =
            $this->Supplier_model->getFiltered(
                $config['per_page'],
                $page,
                $keyword
            );

        $data['pagination'] =
            $this->pagination->create_links();

        $this->load->view(
            'templates/header',
        );

        $this->load->view(
            'manajer/templates/sidebar_manajer'
        );

        $this->load->view(
            'supplier/index',
            $data
        );

        $this->load->view(
            'templates/footer'
        );
    }

    // =========================
    // TAMBAH SUPPLIER
    // =========================
    public function tambah()
    {
        $data['action'] =
            base_url('supplier/simpan');
        $this->load->view('templates/header');
        $this->load->view(
            'manajer/templates/sidebar_manajer'
        );

        $this->load->view(
            'supplier/form',
            $data
        );

        $this->load->view(
            'templates/footer'
        );
    }

    // =========================
    // SIMPAN SUPPLIER
    // =========================
    public function simpan()
    {
        $data = $this->input->post(NULL, TRUE);

        $this->Supplier_model->insert($data);

        $this->session->set_flashdata(
            'success',
            'Supplier berhasil ditambahkan'
        );

        redirect('supplier');
    }

    // =========================
    // EDIT SUPPLIER
    // =========================
    public function edit($id)
    {
        $data['supplier'] =
            $this->Supplier_model->getById($id);

        $data['action'] =
            base_url('supplier/update/'.$id);
        $this->load->view('templates/header');
        $this->load->view(
            'manajer/templates/sidebar_manajer'
        );

        $this->load->view(
            'supplier/form',
            $data
        );

        $this->load->view(
            'templates/footer'
        );
    }

    // =========================
    // UPDATE SUPPLIER
    // =========================
    public function update($id)
    {
        $data = $this->input->post(NULL, TRUE);

        $this->Supplier_model->update($id, $data);

        $this->session->set_flashdata(
            'success',
            'Supplier berhasil diupdate'
        );

        redirect('supplier');
    }

    // =========================
    // HAPUS SUPPLIER
    // =========================
    public function hapus($id)
    {
        $this->Supplier_model->delete($id);

        $this->session->set_flashdata(
            'success',
            'Supplier berhasil dihapus'
        );

        redirect('supplier');
    }

    // =========================
    // DETAIL SUPPLIER
    // =========================
    public function detail($id)
    {
        $data['supplier'] =
            $this->Supplier_model->getById($id);

        $data['produk'] =
            $this->Supplier_model->getProdukBySupplier($id);
        $this->load->view('templates/header');
        $this->load->view(
            'manajer/templates/sidebar_manajer'
        );

        $this->load->view(
            'supplier/detail',
            $data
        );

        $this->load->view(
            'templates/footer'
        );
    }

    // =========================
    // SEARCH AJAX
    // =========================
    public function search()
    {
        $keyword = $this->input->get('q');

        $data =
            $this->Supplier_model->getFilteredAjax($keyword);

        echo json_encode($data);
    }

    public function laporan_pembelian()
{
    $data['title'] = 'Laporan Pembelian Supplier';

    // ==========================
    // FILTER
    // ==========================
    $tgl_awal = $this->input->get('tgl_awal');
    $tgl_akhir = $this->input->get('tgl_akhir');
    $search = $this->input->get('search');
    $limit = $this->input->get('limit') ?: '10';
    
    // Set default tanggal
    if(empty($tgl_awal)){
        $tgl_awal = date('Y-m-d', strtotime('-30 days'));
    }
    if(empty($tgl_akhir)){
        $tgl_akhir = date('Y-m-d');
    }

    // ==========================
    // QUERY PEMBELIAN
    // ==========================
    $this->db->select('
        pembelian.*,
        supplier.nama_supplier,
        supplier.kontak,
        users.nama as user_name
    ');
    $this->db->from('pembelian');
    $this->db->join('supplier', 'supplier.id_supplier = pembelian.supplier_id', 'left');
    $this->db->join('users', 'users.id_user = pembelian.user_id', 'left');
    
    // Filter tanggal
    $this->db->where('DATE(pembelian.tanggal) >=', $tgl_awal);
    $this->db->where('DATE(pembelian.tanggal) <=', $tgl_akhir);
    
    // Filter search
    if(!empty($search)){
        $this->db->group_start();
        $this->db->like('pembelian.kode_pembelian', $search);
        $this->db->or_like('supplier.nama_supplier', $search);
        $this->db->or_like('pembelian.status', $search);
        $this->db->group_end();
    }
    
    // Limit
    if($limit != 'all'){
        $this->db->limit((int)$limit);
    }
    
    $this->db->order_by('pembelian.tanggal', 'DESC');
    $data['pembelian'] = $this->db->get()->result();
    
    // Hitung total item untuk setiap pembelian
    foreach($data['pembelian'] as $p){
        $total_item = $this->db->select('SUM(jumlah) as total')
            ->from('detail_pembelian')
            ->where('pembelian_id', $p->id_pembelian)
            ->get()
            ->row();
        $p->total_item = $total_item->total ?? 0;
    }

    // ==========================
    // SUMMARY
    // ==========================
    $summary = $this->db->select('
        COUNT(*) as jumlah_transaksi,
        SUM(total) as total_nominal,
        SUM(CASE WHEN status = "selesai" THEN total ELSE 0 END) as total_selesai,
        SUM(CASE WHEN status = "diproses" THEN total ELSE 0 END) as total_diproses,
        SUM(CASE WHEN status = "diterima" THEN total ELSE 0 END) as total_diterima,
        SUM(CASE WHEN status = "menunggu" THEN total ELSE 0 END) as total_menunggu,
        SUM(CASE WHEN status = "disetujui" THEN total ELSE 0 END) as total_disetujui
    ')
    ->from('pembelian')
    ->where('DATE(tanggal) >=', $tgl_awal)
    ->where('DATE(tanggal) <=', $tgl_akhir)
    ->get()
    ->row();
    
    $data['summary'] = $summary;
    
    // ==========================
    // STATUS COUNT
    // ==========================
    $data['status_count'] = [
        'menunggu' => $this->db->where('status', 'menunggu')->count_all_results('pembelian'),
        'disetujui' => $this->db->where('status', 'disetujui')->count_all_results('pembelian'),
        'diproses' => $this->db->where('status', 'diproses')->count_all_results('pembelian'),
        'diterima' => $this->db->where('status', 'diterima')->count_all_results('pembelian'),
        'selesai' => $this->db->where('status', 'selesai')->count_all_results('pembelian')
    ];
    
    // ==========================
    // TOP PRODUK TERLARIS (dari pembelian)
    // ==========================
    $data['top_produk'] = $this->db->select('
        produk.nama_produk,
        SUM(detail_pembelian.jumlah) as total_dibeli,
        COUNT(DISTINCT detail_pembelian.pembelian_id) as frekuensi
    ')
    ->from('detail_pembelian')
    ->join('produk', 'produk.id_produk = detail_pembelian.produk_id')
    ->join('pembelian', 'pembelian.id_pembelian = detail_pembelian.pembelian_id')
    ->where('DATE(pembelian.tanggal) >=', $tgl_awal)
    ->where('DATE(pembelian.tanggal) <=', $tgl_akhir)
    ->group_by('detail_pembelian.produk_id')
    ->order_by('total_dibeli', 'DESC')
    ->limit(10)
    ->get()
    ->result();
    
    $data['summary']->produk_terlaris = $data['top_produk'];

    // ==========================
    // SIMPAN FILTER KE VIEW
    // ==========================
    $data['tgl_awal'] = $tgl_awal;
    $data['tgl_akhir'] = $tgl_akhir;
    $data['search'] = $search;
    $data['limit'] = $limit;

    // ==========================
    // LOAD VIEW
    // ==========================
    $this->load->view('supplier/templates/header', $data);
    $this->load->view('supplier/templates/sidebar_supplier', $data);
    $this->load->view('supplier/laporan_pembelian', $data);
    $this->load->view('supplier/templates/footer');
}

// Export Excel
public function export_excel() {
    // Ambil parameter filter jika ada
    $tgl_awal = $this->input->get('tgl_awal') ?? date('Y-m-01');
    $tgl_akhir = $this->input->get('tgl_akhir') ?? date('Y-m-d');
    $status = $this->input->get('status');
    
    // Query dengan filter tanggal dan status
    $this->db
        ->select('
            pembelian.*,
            supplier.nama_supplier,
            supplier.alamat as supplier_alamat,
            supplier.kontak as supplier_kontak,
            users.nama as kasir,
            GROUP_CONCAT(
                CONCAT(
                    produk.nama_produk, 
                    " (", 
                    detail_pembelian.jumlah, 
                    " ", 
                    IFNULL(satuan_produk.nama_satuan, "unit"),
                    ")"
                ) SEPARATOR ", "
            ) as detail_produk,
            COUNT(detail_pembelian.id_detail) as total_item
        ')
        ->from('pembelian')
        ->join('supplier', 'supplier.id_supplier = pembelian.supplier_id', 'left')
        ->join('users', 'users.id_user = pembelian.user_id', 'left')
        ->join('detail_pembelian', 'detail_pembelian.pembelian_id = pembelian.id_pembelian', 'left')
        ->join('produk', 'produk.id_produk = detail_pembelian.produk_id', 'left')
        ->join('satuan_produk', 'satuan_produk.id = detail_pembelian.satuan_id', 'left')
        ->group_by('pembelian.id_pembelian')
        ->order_by('pembelian.tanggal', 'DESC');
    
    // Filter tanggal
    if($tgl_awal && $tgl_akhir){
        $this->db->where('DATE(pembelian.tanggal) >=', $tgl_awal);
        $this->db->where('DATE(pembelian.tanggal) <=', $tgl_akhir);
    }
    
    // Filter status
    if($status && $status != 'semua'){
        $this->db->where('pembelian.status', $status);
    }
    
    $data['pembelian'] = $this->db->get()->result();
    $data['tgl_awal'] = $tgl_awal;
    $data['tgl_akhir'] = $tgl_akhir;
    $data['status'] = $status;
    
    // Hitung total
    $total_all = 0;
    foreach($data['pembelian'] as $p){
        $total_all += (int)$p->total;
    }
    $data['total_all'] = $total_all;
    
    $filename = "Laporan_Pembelian_" . date('Y-m-d_H-i') . ".xls";
    
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=" . $filename);
    header("Pragma: no-cache");
    header("Expires: 0");

    $this->load->view('supplier/export_excel', $data);
}
    public function export_pdf()
    {
        // Load library Dompdf
        $this->load->library('pdf');
        
        // Ambil parameter filter
        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');
        $status = $this->input->get('status');
        $supplier_id = $this->input->get('supplier_id');
        
        if(!$start_date) $start_date = date('Y-m-d', strtotime('-30 days'));
        if(!$end_date) $end_date = date('Y-m-d');
        
        if($supplier_id) {
            $data['pembelian'] = $this->Supplier_model->get_laporan_by_supplier($supplier_id, $start_date, $end_date);
        } else {
            $data['pembelian'] = $this->Supplier_model->get_laporan_pembelian($start_date, $end_date, $status);
        }
        
        $data['summary'] = $this->Supplier_model->get_laporan_summary($start_date, $end_date, $status);
        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;
        $data['export_date'] = date('d-m-Y H:i:s');
        
        $html = $this->load->view('supplier/laporan_pembelian_pdf', $data, true);
        
        $this->pdf->loadHtml($html);
        $this->pdf->setPaper('A4', 'landscape');
        $this->pdf->render();
        
        $nama_file = 'Laporan_Pembelian_Supplier_' . date('Ymd') . '.pdf';
        $this->pdf->stream($nama_file, array('Attachment' => 0));
    }
    
    private function get_supplier_name($supplier_id)
    {
        $supplier = $this->Supplier_model->getById($supplier_id);
        return $supplier ? preg_replace('/[^a-zA-Z0-9]/', '_', $supplier->nama_supplier) : 'Unknown';
    }



    public function laporan()
{
    $keyword = $this->input->get('q');
    $tgl_awal = $this->input->get('tgl_awal');
    $tgl_akhir = $this->input->get('tgl_akhir');

    $this->db->select("
        pembelian.*,
        supplier.nama_supplier,
        produk.nama_produk,
        detail_pembelian.jumlah
    ");

    $this->db->from('pembelian');

    $this->db->join(
        'supplier',
        'supplier.id_supplier = pembelian.supplier_id',
        'left'
    );

    $this->db->join(
        'detail_pembelian',
        'detail_pembelian.pembelian_id = pembelian.id_pembelian',
        'left'
    );

    $this->db->join(
        'produk',
        'produk.id_produk = detail_pembelian.produk_id',
        'left'
    );

    $this->db->where('pembelian.status','selesai');

    if(!empty($keyword))
    {
        $this->db->group_start();

        $this->db->like(
            'pembelian.kode_pembelian',
            $keyword
        );

        $this->db->or_like(
            'supplier.nama_supplier',
            $keyword
        );

        $this->db->or_like(
            'produk.nama_produk',
            $keyword
        );

        $this->db->group_end();
    }

    if(!empty($tgl_awal))
    {
        $this->db->where(
            'DATE(pembelian.tanggal) >=',
            $tgl_awal
        );
    }

    if(!empty($tgl_akhir))
    {
        $this->db->where(
            'DATE(pembelian.tanggal) <=',
            $tgl_akhir
        );
    }

    $this->db->order_by(
        'pembelian.tanggal',
        'DESC'
    );

    $data['laporan'] = $this->db
        ->get()
        ->result();

    $this->load->view(
        'supplier/templates/header'
    );

    $this->load->view(
        'supplier/templates/sidebar_supplier'
    );

    $this->load->view(
        'supplier/laporan_supplier',
        $data
    );

    $this->load->view(
        'supplier/templates/footer'
    );
}










}



