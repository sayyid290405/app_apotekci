<?php
class Kasir_model extends CI_Model {

    public function getProduk()
{
    return $this->db->select('
            produk.*,
            kategori.nama_kategori,
            supplier.nama_supplier
        ')
        ->from('produk')
        ->join('kategori','kategori.id_kategori = produk.kategori_id','left')
        ->join('supplier','supplier.id_supplier = produk.supplier_id','left')
        ->where('produk.stok >', 0) // hanya tampil stok tersedia
        ->get()
        ->result();
}
<<<<<<< HEAD
=======

public function getAllWithSatuan()
{
    $produk = $this->db
        ->select('produk.*, kategori.nama_kategori, supplier.nama_supplier')
        ->join('kategori','kategori.id_kategori = produk.kategori_id','left')
        ->join('supplier','supplier.id_supplier = produk.supplier_id','left')
        ->get('produk')
        ->result();

    foreach ($produk as $p) {
        $p->satuan = $this->db
            ->get_where('satuan_produk', ['produk_id' => $p->id_produk])
            ->result();
    }

    return $produk;
}
>>>>>>> ee11432ab565d27cb1eddab3a20fae4e15c9c552
}