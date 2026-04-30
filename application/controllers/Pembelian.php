<?php
class Pembelian extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Pembelian_model');
    }

    // ======================
    // HALAMAN UTAMA
    // ======================
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

    $this->load->view('templates/header',$data);
    $this->load->view('templates/sidebar');
    $this->load->view('pembelian/detail',$data);
    $this->load->view('templates/footer');
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