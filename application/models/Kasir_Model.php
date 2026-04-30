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
}