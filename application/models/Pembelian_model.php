<?php
class Pembelian_model extends CI_Model {

    public function getProdukBySupplier($supplier_id)
    {
        return $this->db
            ->where('supplier_id', $supplier_id)
            ->get('produk')
            ->result();
    }

    public function getStokMinim()
    {
        return $this->db
            ->where('stok <= stok_minimal', NULL, FALSE)
            ->get('produk')
            ->result();
    }

    public function searchProduk($q)
    {
        return $this->db
            ->like('nama_produk', $q)
            ->get('produk')
            ->result();
    }

    public function simpanPembelian($supplier_id, $user_id, $cart)
    {
        $this->db->trans_start();

        $total = array_sum(array_column($cart,'subtotal'));

        $pembelian = [
            'kode_pembelian' => 'INV-'.date('YmdHis'),
            'supplier_id'    => $supplier_id,
            'user_id'        => $user_id,
            'status'         => 'menunggu',
            'total'          => $total
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

        return $id;
    }
}