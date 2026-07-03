<?php
class Pembelian extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Pembelian_model');
        $this->load->model('Supplier_model');
    }
    public function approve($id_pembelian)
{
    // ======================================
    // AMBIL DATA PEMBELIAN
    // ======================================
    $pembelian = $this->db
        ->where('id_pembelian', $id_pembelian)
        ->get('pembelian')
        ->row();

    // ======================================
    // VALIDASI DATA
    // ======================================
    if (!$pembelian) {

        $this->session->set_flashdata(
            'error',
            'Data pembelian tidak ditemukan'
        );

        redirect('Pembelian/approval_pembelian');
    }

    // ======================================
    // ANTI DOUBLE APPROVE
    // ======================================
    if ($pembelian->status == 'selesai') {

        $this->session->set_flashdata(
            'warning',
            'Pembelian sudah pernah diapprove'
        );

        redirect('Pembelian/approval_pembelian');
    }

    // ======================================
    // AMBIL DETAIL PEMBELIAN
    // ======================================
    $detail = $this->db
        ->where('pembelian_id', $id_pembelian)
        ->get('detail_pembelian')
        ->result();

    if (empty($detail)) {

        $this->session->set_flashdata(
            'error',
            'Detail pembelian kosong'
        );

        redirect('Pembelian/approval_pembelian');
    }

    // ======================================
    // START TRANSACTION
    // ======================================
    $this->db->trans_start();

    // ======================================
    // LOOPING TAMBAH STOK PRODUK
    // ======================================
    foreach ($detail as $d) {

        // Ambil produk
        $produk = $this->db
            ->where('id_produk', $d->produk_id)
            ->get('produk')
            ->row();

        if ($produk) {

            // Hitung stok baru
            $stokBaru = $produk->stok + $d->jumlah;

            // Update stok
            $this->db
                ->where('id_produk', $d->produk_id)
                ->update('produk', [

                    'stok' => $stokBaru

                ]);
        }
    }

    // ======================================
    // UPDATE STATUS PEMBELIAN
    // ======================================
    $this->db
        ->where('id_pembelian', $id_pembelian)
        ->update('pembelian', [

            'status' => 'selesai',

            'approved_by' => $this->session->userdata('id'),

            'approved_at' => date('Y-m-d H:i:s')

        ]);

    // ======================================
    // COMPLETE TRANSACTION
    // ======================================
    $this->db->trans_complete();

    // ======================================
    // CHECK TRANSACTION
    // ======================================
    if ($this->db->trans_status() === FALSE) {

        $this->session->set_flashdata(
            'error',
            'Gagal approve pembelian'
        );

    } else {

        $this->session->set_flashdata(
            'success',
            'Pembelian berhasil diapprove & stok otomatis bertambah'
        );

    }

    redirect('Pembelian/approval_pembelian');
}

    public function terima_barang_supplier()
    {   
    $this->load->model('Pembelian_model');
    $data['pembelian_diproses'] = $this->Pembelian_model->get_pembelian_diproses();
    $data['pembelian_disetujui'] = $this->Supplier_model->get_approved_pembelian();

    $this->load->view('templates/sidebar');
    $this->load->view('pembelian/terima_barang_supplier', $data);
    $this->load->view('supplier/templates/footer');

    }
    public function shipment_record()
    {
        $this->load->model->Pembelian_model();
        $data['shipment_record'] = $this->Pembelian_model->get_shipment_record();
        $this->load->view('supplier/templates/sidebar_supplier');
        $this->load->view('pembelian/shipment_record', $data);
        $this->load->view('supplier/templates/footer');
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

    public function update_status()
    {
    $id     = $this->input->post('id_pembelian');
    $status = $this->input->post('status');
    $approved_by = $this->session->userdata('id_user    ');
    $tanggal = date('Y-m-d H:i:s');
    $catatan = $this->input->post('catatan');

    $this->load->model('Pembelian_model');
    $this->Pembelian_model->update_pembelian($id, $status, $approved_by, $tanggal, $catatan); // ✅ kirim parameter

    redirect('Pembelian/approval_pembelian');
    }


    public function update_status_admin()
    {
    $id     = $this->input->post('id_pembelian');
    $status = $this->input->post('status');
    $approved_by = $this->session->userdata('id_user    ');
    $tanggal = date('Y-m-d H:i:s');
    $catatan = $this->input->post('catatan');

    $this->load->model('Pembelian_model');
    $this->Pembelian_model->update_pembelian($id, $status, $approved_by, $tanggal, $catatan); // ✅ kirim parameter

    redirect('pembelian/terima_barang_supplier');
    }


public function approval_pembelian()
{
    $this->load->model('Pembelian_model');

    $filter = $this->input->get('filter'); // 🔥 ambil dari form

    $this->db->select('
        pembelian.*,
        supplier.nama_supplier,
        produk.nama_produk
    ');
    $this->db->from('pembelian');
    $this->db->join('supplier', 'supplier.id_supplier = pembelian.supplier_id', 'left');
    $this->db->join('produk', 'produk.id_produk = pembelian.id_produk', 'left');

    // 🔥 INI YANG KAMU KURANG
    if (!empty($filter)) {
        $this->db->where('pembelian.status', $filter);
    }

    $data['pembelian'] = $this->db->get()->result();

    $this->load->view('manajer/templates/sidebar_manajer');
    $this->load->view('pembelian/order_supplier_approval', $data);
    $this->load->view('manajer/templates/footer');
}
public function bayar($id)
{
    // 1. Ambil data pembelian untuk validasi awal
    $pembelian = $this->db->get_where('pembelian', ['id_pembelian' => $id])->row();

    if (!$pembelian) {
        $this->session->set_flashdata('error', 'Data pembelian tidak ditemukan.');
        redirect('pembelian/bayar_supplier');
    }

    if ($pembelian->id_transaksi != NULL) {
        $this->session->set_flashdata('error', 'Pembelian ini sudah pernah dibayar.');
        redirect('pembelian/bayar_supplier');
    }

    // 2. Konfigurasi Upload Gambar
    $config['upload_path']   = './uploads/bukti/';
    $config['allowed_types'] = 'jpg|jpeg|png';
    $config['max_size']      = 10240; // 10MB
    $config['encrypt_name']  = TRUE;

    $this->load->library('upload', $config);

    // Buat folder jika belum ada
    if (!is_dir($config['upload_path'])) {
        mkdir($config['upload_path'], 0777, TRUE);
    }

    // 3. Proses Upload
    if (!$this->upload->do_upload('bukti_pembayaran')) {
        $this->session->set_flashdata('error', $this->upload->display_errors());
        redirect('pembelian/bayar_supplier');
    }

    $uploadData = $this->upload->data();
    $namaFile   = $uploadData['file_name'];

    // 4. Proses Database dengan Transaksi
    $this->db->trans_start();

    $dataTransaksi = [
        'id_pembelian'     => $id,
        'total_bayar'      => $pembelian->total, // Mengambil total dari tabel pembelian
        'tanggal_bayar'    => date('Y-m-d H:i:s'),
        'status_bayar'     => 'lunas',
        'bukti_pembayaran' => $namaFile
    ];

    // Panggil Model untuk insert transaksi dan ambil ID-nya
    $id_transaksi_baru = $this->Pembelian_model->insert_transaksi($dataTransaksi);

    // Update tabel pembelian agar terhubung dengan transaksi ini
    $this->Pembelian_model->update_pembelian_transaksi($id, $id_transaksi_baru);

    $this->db->trans_complete();

    // 5. Cek Status Akhir
    if ($this->db->trans_status() === FALSE) {
        // Jika gagal, hapus file yang sudah terlanjur diupload
        unlink($config['upload_path'] . $namaFile);
        $this->session->set_flashdata('error', 'Gagal memproses database pembayaran.');
    } else {
        $this->session->set_flashdata('success', 'Pembayaran berhasil dikonfirmasi.');
    }

    redirect('pembelian/bayar_supplier');
}

    public function pembelian_supplier()
    {
    $this->load->model('Pembelian_model');
    $data['pembelian'] = $this->Pembelian_model->get_pembelian();


    $this->load->view('templates/sidebar');
    $this->load->view('pembelian/order_supplier', $data);
    }

public function bayar_supplier()
{
    // Pastikan memanggil fungsi model yang baru kita buat/update
    $data['pembelian'] = $this->Pembelian_model->get_pembelian_untuk_bayar();
    $this->load->view('manajer/templates/header');
    $this->load->view('manajer/templates/sidebar_manajer');
    $this->load->view('pembelian/bayar_supplier', $data);
}

    public function index()
    {
        $data['supplier'] = $this->db->get('supplier')->result();
        $data['js'] = 'pembelian.js';

    //     $data['pembelian'] = $this->db
    // ->select('pembelian.*, supplier.nama_supplier')
    // ->join('supplier','supplier.id_supplier = pembelian.supplier_id')
    // ->where('id_pembelian',$id)
    // ->get('pembelian')
    // ->row();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar');
        $this->load->view('pembelian/index', $data);
        $this->load->view('templates/footer');
    }

    // ======================
    // GET PRODUK BY SUPPLIER
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
    // SIMPAN PEMBELIAN
    // ======================
    public function simpan()
{
    $cart = json_decode($this->input->post('cart'), true);

    if(empty($cart)){
        echo json_encode(['status'=>'error','message'=>'Cart kosong']);
        return;
    }

    $supplier_id = $this->input->post('supplier');
    $user_id = $this->session->userdata('id') ?? 1;

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
public function detail_pembelian($id)
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
    $this->load->view('manajer/detail_pembelian',$data);
    $this->load->view('templates/footer');
}

public function delete($id = null)
{
if ($id == null) {
            $this->session->set_flashdata('error', 'Pilih data yang mau dihapus!');
            redirect('Pembelian');
        }

        $hapus = $this->Pembelian_model->hapus($id);

        if ($hapus) {
            $this->session->set_flashdata('success', 'Data berhasil dibuang!');
        } else {
            $this->session->set_flashdata('error', 'Gagal hapus data.');
        }

        redirect('Pembelian/approval_pembelian');
}
public function cetak($id)
{
    // 🔥 WAJIB LOAD
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

    $this->pdf->generate($html, 'invoice-'.$id);
}

}