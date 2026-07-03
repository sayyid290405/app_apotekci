<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pembelian extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Pembelian_model');
        $this->load->model('Supplier_model');
        $this->load->library('form_validation');
        
        if(!$this->session->userdata('id_user')) {
            redirect('auth/login');
        }
    }

    // ======================
    // VIEW INDEX (BUAT PEMBELIAN BARU)
    // ======================
    public function index()
    {
        $data['supplier'] = $this->db->get('supplier')->result();


        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('pembelian/index', $data);
        $this->load->view('templates/footer');
    }

    // ======================
    // GET PRODUK BY SUPPLIER (AJAX)
    // ======================
public function getProdukBySupplier()
{
    $supplier_id = $this->input->get('supplier_id');

    if (!$supplier_id) {
        echo json_encode([]);
        return;
    }

    // 🔥 JOIN ke satuan_produk dan GROUP_CONCAT
    // Urutkan agar BOX (konversi terbesar) muncul pertama
    $this->db->select('
        produk.*, 
        GROUP_CONCAT(
            CONCAT(
                satuan_produk.id, "::", 
                satuan_produk.nama_satuan, "::", 
                IFNULL(satuan_produk.konversi_ke_dasar, satuan_produk.konversi), "::", 
                satuan_produk.harga, "::", 
                IFNULL(satuan_produk.level, 1)
            ) ORDER BY satuan_produk.konversi DESC SEPARATOR "||"
        ) as list_satuan
    ');
    $this->db->from('produk');
    $this->db->join('satuan_produk', 'satuan_produk.produk_id = produk.id_produk', 'left');
    $this->db->where('produk.supplier_id', $supplier_id);
    $this->db->group_by('produk.id_produk');
    
    $data = $this->db->get()->result();

    $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($data));
}  

    // ======================
    // SIMPAN PEMBELIAN BARU
    // ======================
public function simpan()
{
    $supplier_id = $this->input->post('supplier');
    $cart = json_decode($this->input->post('cart'), true);
    
    if (!$supplier_id || empty($cart)) {
        echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']);
        return;
    }
    
    // Hitung total
    $total = 0;
    foreach ($cart as $item) {
        $total += $item['subtotal'];
    }
    
    // Simpan ke tabel pembelian
    $kode_pembelian = 'INV-' . time();
    $pembelian_data = [
        'kode_pembelian' => $kode_pembelian,
        'supplier_id' => $supplier_id,
        'tanggal' => date('Y-m-d H:i:s'),
        'total' => $total,
        'status' => 'menunggu',
        'user_id' => $this->session->userdata('id_user')
    ];
    
    $this->db->insert('pembelian', $pembelian_data);
    $pembelian_id = $this->db->insert_id();
    
    // Simpan detail pembelian dengan satuan_id
    foreach ($cart as $item) {
        // 🔥 PASTIKAN satuan_id tersimpan
        $satuan_id = isset($item['satuan_id']) ? $item['satuan_id'] : null;
        
        // 🔥 Hitung jumlah dalam satuan dasar untuk update stok
        $konversi = isset($item['konversi']) ? $item['konversi'] : 1;
        $jumlah_dasar = $item['qty'] * $konversi;
        
        $detail_data = [
            'pembelian_id' => $pembelian_id,
            'produk_id' => $item['id'],
            'satuan_id' => $satuan_id,  // 🔥 INI PENTING!
            'jumlah' => $item['qty'],
            'harga' => $item['harga'],
            'subtotal' => $item['subtotal']
        ];
        $this->db->insert('detail_pembelian', $detail_data);
        
        // 🔥 UPDATE STOK berdasarkan jumlah dalam satuan dasar
        $this->db->set('stok', 'stok + ' . $jumlah_dasar, FALSE);
        $this->db->where('id_produk', $item['id']);
        $this->db->update('produk');
    }
    
    echo json_encode(['status' => 'ok', 'id' => $pembelian_id]);
}



public function bayar_supplier()
{
    // Ambil parameter filter dari GET
    $filter = $this->input->get('filter');
    
    // Query dasar
    $this->db->select('pembelian.*, supplier.nama_supplier, transaksi.bukti_pembayaran')
             ->from('pembelian')
             ->join('supplier', 'supplier.id_supplier = pembelian.supplier_id', 'left')
             ->join('transaksi', 'transaksi.id_pembelian = pembelian.id_pembelian', 'left')
             ->order_by('pembelian.tanggal', 'DESC');
    
    // Terapkan filter berdasarkan status
    if($filter == 'menunggu') {
        $this->db->where('pembelian.status', 'menunggu');
        $this->db->where('transaksi.bukti_pembayaran IS NULL', null, false);
    } 
    elseif($filter == 'disetujui') {
        $this->db->where('pembelian.status', 'disetujui');
        $this->db->where('transaksi.bukti_pembayaran IS NOT NULL', null, false);
    } 
    elseif($filter == 'selesai') {
        $this->db->where('pembelian.status', 'selesai');
    }
    // filter kosong = semua data
    
    $data['pembelian'] = $this->db->get()->result();
    
    // Kirim filter ke view untuk mempertahankan pilihan
    $data['filter_aktif'] = $filter;
    
    $this->load->view('templates/header');
    $this->load->view('manajer/templates/sidebar_manajer');
    $this->load->view('pembelian/bayar_supplier', $data);
    $this->load->view('templates/footer');
}
    // ======================
    // PROSES UPLOAD BUKTI PEMBAYARAN
    // ======================
    public function bayar($id)
    {
        // Ambil data pembelian
        $pembelian = $this->db->get_where('pembelian', ['id_pembelian' => $id])->row();

        if (!$pembelian) {
            $this->session->set_flashdata('error', 'Data pembelian tidak ditemukan.');
            redirect('pembelian/bayar_supplier');
        }

        if ($pembelian->id_transaksi != NULL) {
            $this->session->set_flashdata('error', 'Pembelian ini sudah pernah dibayar.');
            redirect('pembelian/bayar_supplier');
        }

        // Konfigurasi Upload
        $config['upload_path']   = './uploads/bukti/';
        $config['allowed_types'] = 'jpg|jpeg|png';
        $config['max_size']      = 10240;
        $config['encrypt_name']  = TRUE;

        $this->load->library('upload', $config);

        if (!is_dir($config['upload_path'])) {
            mkdir($config['upload_path'], 0777, TRUE);
        }

        if (!$this->upload->do_upload('bukti_pembayaran')) {
            $this->session->set_flashdata('error', $this->upload->display_errors());
            redirect('pembelian/bayar_supplier');
        }

        $uploadData = $this->upload->data();
        $namaFile   = $uploadData['file_name'];

        // Simpan ke database
        $this->db->trans_start();

        $dataTransaksi = [
            'id_pembelian'     => $id,
            'total_bayar'      => $pembelian->total,
            'tanggal_bayar'    => date('Y-m-d H:i:s'),
            'status_bayar'     => 'lunas',
            'bukti_pembayaran' => $namaFile
        ];

        $this->db->insert('transaksi', $dataTransaksi);
        $id_transaksi = $this->db->insert_id();

       $this->db->where('id_pembelian', $id);

        $this->db->update('pembelian', [
            'id_transaksi' => $id_transaksi,
            'status' => 'disetujui'
        ]);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            unlink($config['upload_path'] . $namaFile);
            $this->session->set_flashdata('error', 'Gagal memproses database pembayaran.');
        } else {
            $this->session->set_flashdata('success', 'Pembayaran berhasil dikonfirmasi. Menunggu approve Manager.');
        }

        redirect('pembelian/bayar_supplier');
    }

    // ======================
    // APPROVE PEMBELIAN & TAMBAH STOK
    // ======================
    public function approve($id_pembelian)
    {
        $pembelian = $this->db
            ->where('id_pembelian', $id_pembelian)
            ->get('pembelian')
            ->row();

        if (!$pembelian) {
            $this->session->set_flashdata('error', 'Data pembelian tidak ditemukan');
            redirect('Pembelian/approval_pembelian');
        }

        if ($pembelian->status == 'selesai') {
            $this->session->set_flashdata('warning', 'Pembelian sudah pernah diapprove');
            redirect('Pembelian/approval_pembelian');
        }

        if ($pembelian->id_transaksi == NULL) {
            $this->session->set_flashdata('error', 'Pembelian belum dibayar, tidak bisa diapprove');
            redirect('Pembelian/approval_pembelian');
        }

        // Ambil detail pembelian
        $detail = $this->db
            ->where('pembelian_id', $id_pembelian)
            ->get('detail_pembelian')
            ->result();

        if (empty($detail)) {
            $this->session->set_flashdata('error', 'Detail pembelian kosong');
            redirect('Pembelian/approval_pembelian');
        }

        // START TRANSACTION
        $this->db->trans_start();

        // Tambah stok produk
        foreach ($detail as $d) {
            $produk = $this->db
                ->where('id_produk', $d->produk_id)
                ->get('produk')
                ->row();

            if ($produk) {
                $stokBaru = $produk->stok + $d->jumlah;
                $this->db
                    ->where('id_produk', $d->produk_id)
                    ->update('produk', ['stok' => $stokBaru]);
            }
        }

        // Update status pembelian
        $this->db
            ->where('id_pembelian', $id_pembelian)
            ->update('pembelian', [
                'status' => 'selesai',
                'approved_by' => $this->session->userdata('id_user'),
                'tanggal_approve' => date('Y-m-d H:i:s')
            ]);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('error', 'Gagal approve pembelian');
        } else {
            $this->session->set_flashdata('success', 'Pembelian berhasil diapprove & stok otomatis bertambah');
        }

        redirect('Pembelian/approval_pembelian');
    }


// public function approval_pembelian()
// {
//     $filter = $this->input->get('filter');

//     $this->db->select('
//         pembelian.*,
//         supplier.nama_supplier,
//         produk.nama_produk
//     ');
//     $this->db->from('pembelian');
//     $this->db->join('supplier', 'supplier.id_supplier = pembelian.supplier_id', 'left');
//     $this->db->join('detail_pembelian', 'detail_pembelian.pembelian_id = pembelian.id_pembelian', 'left');
//     $this->db->join('produk', 'produk.id_produk = detail_pembelian.produk_id', 'left');

//     if (!empty($filter)) {
//         $this->db->where('pembelian.status', $filter);
//     }

//     $this->db->group_by('pembelian.id_pembelian');
//     $this->db->order_by('pembelian.tanggal', 'DESC');
//     $data['pembelian'] = $this->db->get()->result();

//     $this->load->view('manajer/templates/header');
//     $this->load->view('manajer/templates/sidebar_manajer');
//     $this->load->view('pembelian/order_supplier_approval', $data);
//     $this->load->view('manajer/templates/footer');
// }


public function approval_pembelian()
{
    $filter = $this->input->get('filter');

    $this->db->select('
        pembelian.*,
        supplier.nama_supplier,
        produk.nama_produk,
        transaksi.id_transaksi,
        transaksi.bukti_pembayaran
    ');

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

    // 🔥 JOIN TRANSAKSI
    $this->db->join(
        'transaksi',
        'transaksi.id_transaksi = pembelian.id_transaksi',
        'left'
    );

    if(!empty($filter)){
        $this->db->where('pembelian.status', $filter);
    }

    $this->db->group_by('pembelian.id_pembelian');

    $this->db->order_by(
        'pembelian.tanggal',
        'DESC'
    );

    $data['pembelian'] = $this->db->get()->result();

    $this->load->view('manajer/templates/header');
    $this->load->view('manajer/templates/sidebar_manajer');
    $this->load->view(
        'pembelian/order_supplier_approval',
        $data
    );
    $this->load->view('manajer/templates/footer');
}


    // ======================
    // DETAIL PEMBELIAN
    // ======================
// Di Controller detail() - tambahkan informasi konversi
public function detail($id)
{
    // Ambil data pembelian
    $data['pembelian'] = $this->db
        ->select('pembelian.*, supplier.nama_supplier')
        ->join('supplier', 'supplier.id_supplier = pembelian.supplier_id')
        ->where('id_pembelian', $id)
        ->get('pembelian')
        ->row();

    // 🔥 Ambil detail dengan JOIN ke satuan_produk
    $data['detail'] = $this->db
        ->select('
            detail_pembelian.*, 
            produk.nama_produk,
            satuan_produk.nama_satuan
        ')
        ->join('produk', 'produk.id_produk = detail_pembelian.produk_id')
        ->join('satuan_produk', 'satuan_produk.id = detail_pembelian.satuan_id', 'left')
        ->where('pembelian_id', $id)
        ->get('detail_pembelian')
        ->result();

    $this->load->view('templates/sidebar');
    $this->load->view('templates/header', $data);
    $this->load->view('pembelian/detail', $data);
    $this->load->view('templates/footer');
}



public function detail_supplier($id)
{
    // Ambil data pembelian
    $data['pembelian'] = $this->db
        ->select('pembelian.*, supplier.nama_supplier')
        ->join('supplier', 'supplier.id_supplier = pembelian.supplier_id')
        ->where('id_pembelian', $id)
        ->get('pembelian')
        ->row();

    // 🔥 Ambil detail dengan JOIN ke satuan_produk
    $data['detail'] = $this->db
        ->select('
            detail_pembelian.*, 
            produk.nama_produk,
            satuan_produk.nama_satuan
        ')
        ->join('produk', 'produk.id_produk = detail_pembelian.produk_id')
        ->join('satuan_produk', 'satuan_produk.id = detail_pembelian.satuan_id', 'left')
        ->where('pembelian_id', $id)
        ->get('detail_pembelian')
        ->result();

    $this->load->view(
        'supplier/templates/header'
    );

    $this->load->view(
        'supplier/templates/sidebar_supplier'
    );

    $this->load->view(
        'pembelian/detail_supplier',
        $data
    );

    $this->load->view(
        'supplier/templates/footer'
    );
}

        public function detail_manajer($id)
    {
        $data['pembelian'] = $this->db
            ->select('pembelian.*, supplier.nama_supplier')
            ->join('supplier','supplier.id_supplier = pembelian.supplier_id')
            ->where('id_pembelian', $id)
            ->get('pembelian')
            ->row();

        $data['detail'] = $this->db
            ->select('detail_pembelian.*, produk.nama_produk')
            ->join('produk','produk.id_produk = detail_pembelian.produk_id')
            ->where('pembelian_id', $id)
            ->get('detail_pembelian')
            ->result();

        $this->load->view('manajer/templates/sidebar_manajer');
        $this->load->view('pembelian/detail',$data);
        $this->load->view('templates/footer');
    }

    // ======================
    // CETAK PDF
    // ======================
public function cetak($id)
{
    $this->load->library('pdf');

    $data['pembelian'] = $this->db
        ->select('pembelian.*, supplier.nama_supplier')
        ->join('supplier', 'supplier.id_supplier = pembelian.supplier_id')
        ->where('id_pembelian', $id)
        ->get('pembelian')
        ->row();

    // Ambil detail dengan JOIN ke satuan_produk
    $data['detail'] = $this->db
        ->select('
            detail_pembelian.*, 
            produk.nama_produk,
            satuan_produk.nama_satuan,
            satuan_produk.konversi_ke_dasar
        ')
        ->join('produk', 'produk.id_produk = detail_pembelian.produk_id')
        ->join('satuan_produk', 'satuan_produk.id = detail_pembelian.satuan_id', 'left')
        ->where('pembelian_id', $id)
        ->get('detail_pembelian')
        ->result();

    $html = $this->load->view('pembelian/pdf', $data, true);
    $this->pdf->generate($html, 'invoice-pembelian-' . $id);
}
     public function edit_pembelian($id)
    {
        $this->load->model('Pembelian_model');
        $data['produk'] = $this->Pembelian_model->get_pembelian();
        
        // 1. Ambil data pembelian spesifik berdasarkan ID
        $data['pembelian'] = $this->db->get_where('pembelian', ['id_pembelian' => $id])->row();

        // 2. Ambil data supplier untuk pilihan di dropdown form
        $data['supplier'] = $this->db->get('supplier')->result();

        // 3. Load view form edit dan kirim datanya
         $this->load->view('manajer/templates/header');
        $this->load->view('manajer/templates/sidebar_manajer');
        $this->load->view('pembelian/edit_pembelian', $data);
        $this->load->view('manajer/templates/footer');
    }
public function edit_pembelian_data() 
{
    $id = $this->input->post('id_pembelian');

    // 🔥 Validasi ID
    if(empty($id)){
        $this->session->set_flashdata('error', 'ID tidak ditemukan');
        redirect('Pembelian/approval_pembelian');
        return;
    }

    $data = [
        'kode_pembelian' => $this->input->post('kode_pembelian', true),
        'supplier_id'    => $this->input->post('supplier_id', true),
        'tanggal'        => $this->input->post('tanggal', true),
        'total'          => $this->input->post('total', true),
        'status'         => $this->input->post('status', true)
    ];

    // 🔥 Load model cukup sekali di constructor (tidak perlu di sini lagi)

    $update = $this->Pembelian_model->edit_pembelian($id, $data);

    if($update){
        $this->session->set_flashdata('success', 'Data pembelian berhasil diupdate');
    } else {
        $this->session->set_flashdata('error', 'Gagal mengupdate data');
    }

    redirect('Pembelian/approval_pembelian');
}
public function pembelian_supplier()
    {
        // 🔥 PERBAIKAN: Melakukan JOIN melalui detail_pembelian agar multi-produk terbaca & tidak kosong
        $data['pembelian'] = $this->db
            ->select('
                pembelian.*,
                supplier.nama_supplier,
                GROUP_CONCAT(produk.nama_produk SEPARATOR ", ") as nama_produk
            ')
            ->from('pembelian')
            ->join('supplier', 'supplier.id_supplier = pembelian.supplier_id', 'left')
            ->join('detail_pembelian', 'detail_pembelian.pembelian_id = pembelian.id_pembelian', 'left')
            ->join('produk', 'produk.id_produk = detail_pembelian.produk_id', 'left')
            ->group_by('pembelian.id_pembelian')
            ->order_by('pembelian.tanggal', 'DESC')
            ->get()
            ->result();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('pembelian/order_supplier', $data);
        $this->load->view('templates/footer');
    }

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

    $this->load->view('pembelian/export_excell_view', $data);
}
    
    public function terima_barang_supplier()
{
    $data['title'] = 'Verifikasi Penerimaan Barang';

    $data['pembelian_diproses'] = $this->db
        ->select("
            pembelian.*,
            supplier.nama_supplier,
            GROUP_CONCAT(produk.nama_produk SEPARATOR ', ') as nama_produk
        ")
        ->from('pembelian')
        ->join(
            'supplier',
            'supplier.id_supplier = pembelian.supplier_id',
            'left'
        )
        ->join(
            'detail_pembelian',
            'detail_pembelian.pembelian_id = pembelian.id_pembelian',
            'left'
        )
        ->join(
            'produk',
            'produk.id_produk = detail_pembelian.produk_id',
            'left'
        )
        ->where('pembelian.status', 'diproses')
        ->group_by('pembelian.id_pembelian')
        ->order_by('pembelian.tanggal', 'DESC')
        ->get()
        ->result();

    $this->load->view('templates/header', $data);
    $this->load->view('templates/sidebar');
    $this->load->view('pembelian/terima_barang_supplier', $data);
    $this->load->view('templates/footer');
}

public function update_status()
{
    $id     = $this->input->post('id_pembelian');
    $status = $this->input->post('status');
    $approved_by = $this->session->userdata('id_user');
    $tanggal = date('Y-m-d H:i:s');
    $catatan = $this->input->post('catatan');

    $this->db->where('id_pembelian', $id);
    $this->db->update('pembelian', [
        'status' => $status,
        'approved_by' => $approved_by,
        'tanggal_approve' => $tanggal,
        'catatan' => $catatan
    ]);

    redirect('Pembelian/approval_pembelian');
}
    public function update_status_admin()
    {
        $id     = $this->input->post('id_pembelian');
        $status = $this->input->post('status');
        $approved_by = $this->session->userdata('id_user');
        $tanggal = date('Y-m-d H:i:s');
        $catatan = $this->input->post('catatan');

        $this->db->where('id_pembelian', $id);
        $this->db->update('pembelian', [
            'status' => $status,
            'approved_by' => $approved_by,
            'tanggal_approve' => $tanggal,
            'catatan' => $catatan
        ]);

        redirect('pembelian/terima_barang_supplier');
    }

    // ======================
    // DELETE PEMBELIAN
    // ======================
    public function delete($id = null)
    {
        if ($id == null) {
            $this->session->set_flashdata('error', 'Pilih data yang mau dihapus!');
            redirect('Pembelian/approval_pembelian');
        }

        $this->db->trans_start();
        
        $this->db->where('pembelian_id', $id);
        $this->db->delete('detail_pembelian');
        
        $this->db->where('id_pembelian', $id);
        $this->db->delete('pembelian');
        
        $this->db->trans_complete();

        if ($this->db->trans_status()) {
            $this->session->set_flashdata('success', 'Data berhasil dihapus!');
        } else {
            $this->session->set_flashdata('error', 'Gagal hapus data.');
        }

        redirect('Pembelian/approval_pembelian');
    }
}
?>