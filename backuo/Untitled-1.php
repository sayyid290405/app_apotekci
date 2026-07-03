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
        $data['js'] = 'pembelian.js';

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

        if(!$supplier_id){
            echo json_encode([]);
            return;
        }

        $data = $this->Pembelian_model->getProdukBySupplier($supplier_id);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }

    // ======================
    // SIMPAN PEMBELIAN BARU
    // ======================
    public function simpan()
    {
        $cart = json_decode($this->input->post('cart'), true);

        if(empty($cart)){
            echo json_encode(['status'=>'error','message'=>'Cart kosong']);
            return;
        }

        $supplier_id = $this->input->post('supplier');
        $user_id = $this->session->userdata('id_user') ?? 1;

        $this->db->trans_start();

        $total = array_sum(array_column($cart,'subtotal'));

        $pembelian = [
            'kode_pembelian' => 'INV-'.time(),
            'supplier_id'    => $supplier_id,
            'user_id'        => $user_id,
            'status'         => 'menunggu',
            'total'          => $total,
            'tanggal'        => date('Y-m-d H:i:s')
        ];

        $this->db->insert('pembelian', $pembelian);
        $id = $this->db->insert_id();

        foreach($cart as $c){
            $this->db->insert('detail_pembelian',[
                'pembelian_id' => $id,
                'produk_id'    => $c['id'],
                'jumlah'       => $c['qty'],
                'harga'        => $c['harga'],
                'subtotal'     => $c['subtotal']
            ]);
        }

        $this->db->trans_complete();

        echo json_encode([
            'status'=>'ok',
            'id'=>$id
        ]);
    }

    // ======================
    // VIEW BAYAR SUPPLIER (MANAJER)
    // ======================
  // ======================
// VIEW BAYAR SUPPLIER (MANAJER) - YANG BELUM UPLOAD BUKTI
// ======================
public function bayar_supplier()
{
    // Tampilkan pembelian yang:
    // 1. Statusnya 'menunggu' atau 'diproses'
    // 2. Belum punya id_transaksi (belum upload bukti)
    $data['pembelian'] = $this->db
        ->select('
            pembelian.*, 
            supplier.nama_supplier,
            transaksi.bukti_pembayaran,
            transaksi.status_bayar,
            transaksi.id_transaksi as transaksi_id
        ')
        ->from('pembelian')
        ->join('supplier', 'supplier.id_supplier = pembelian.supplier_id', 'left')
        ->join('transaksi', 'transaksi.id_pembelian = pembelian.id_pembelian', 'left')
        ->where_in('pembelian.status', ['selesai'])  // Hanya yang aktif
        ->where('pembelian.id_transaksi IS NULL', null, true)     // Belum upload bukti
        ->group_by('pembelian.id_pembelian')
        ->order_by('pembelian.id_pembelian', 'DESC')
        ->get()
        ->result();
    
    $this->load->view('manajer/templates/header');
    $this->load->view('manajer/templates/sidebar_manajer');
    $this->load->view('pembelian/bayar_supplier', $data);
    $this->load->view('manajer/templates/footer');
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

    // ======================
    // VIEW APPROVAL PEMBELIAN (MANAJER)
    // ======================
    public function approval_pembelian()
    {
        $filter = $this->input->get('filter');

        $this->db->select('
            pembelian.*,
            supplier.nama_supplier,
            transaksi.bukti_pembayaran,
            transaksi.status_bayar
        ');
        $this->db->from('pembelian');
        $this->db->join('supplier', 'supplier.id_supplier = pembelian.supplier_id', 'left');
        $this->db->join('transaksi', 'transaksi.id_transaksi = pembelian.id_transaksi', 'left');

        if (!empty($filter)) {
            $this->db->where('pembelian.status', $filter);
        }

        $this->db->order_by('pembelian.tanggal', 'DESC');
        $data['pembelian'] = $this->db->get()->result();

        $this->load->view('manajer/templates/sidebar_manajer');
        $this->load->view('pembelian/order_supplier_approval', $data);
        $this->load->view('manajer/templates/footer');
    }

    // ======================
    // DETAIL PEMBELIAN
    // ======================
    public function detail($id)
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
            ->join('supplier','supplier.id_supplier = pembelian.supplier_id')
            ->where('id_pembelian',$id)
            ->get('pembelian')
            ->row();

        $data['detail'] = $this->db
            ->select('detail_pembelian.*, produk.nama_produk')
            ->join('produk','produk.id_produk = detail_pembelian.produk_id')
            ->where('pembelian_id',$id)
            ->get('detail_pembelian')
            ->result();

        $html = $this->load->view('pembelian/pdf',$data,true);
        $this->pdf->generate($html, 'invoice-pembelian-'.$id);
    }

    // ======================
    // VIEW PEMBELIAN SUPPLIER
    // ======================
    public function pembelian_supplier()
    {
        $data['pembelian'] = $this->db
            ->select('pembelian.*, supplier.nama_supplier')
            ->join('supplier', 'supplier.id_supplier = pembelian.supplier_id', 'left')
            ->order_by('pembelian.tanggal', 'DESC')
            ->get('pembelian')
            ->result();

        $this->load->view('templates/sidebar');
        $this->load->view('pembelian/order_supplier', $data);
    }

    // ======================
    // TERIMA BARANG SUPPLIER
    // ======================
    public function terima_barang_supplier()
    {   
        $data['pembelian_diproses'] = $this->db
            ->select('pembelian.*, supplier.nama_supplier')
            ->join('supplier', 'supplier.id_supplier = pembelian.supplier_id')
            ->where('pembelian.status', 'diproses')
            ->order_by('pembelian.tanggal', 'DESC')
            ->get('pembelian')
            ->result();

        $this->load->view('templates/sidebar');
        $this->load->view('pembelian/terima_barang_supplier', $data);
        $this->load->view('supplier/templates/footer');
    }

    // ======================
    // UPDATE STATUS (UNTUK SUPPLIER)
    // ======================
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