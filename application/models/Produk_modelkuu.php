<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Produk_model extends CI_Model {

    // =========================
    // GET ALL PRODUK (JOIN)
    // =========================
    public function getAll()
    {
        return $this->db
            ->select('produk.*, kategori.nama_kategori, supplier.nama_supplier')
            ->join('kategori','kategori.id_kategori = produk.kategori_id','left')
            ->join('supplier','supplier.id_supplier = produk.supplier_id','left')
            ->get('produk')
            ->result();
    }

    // =========================
    // GET BY ID
    // =========================
    public function getById($id)
    {
        return $this->db->get_where('produk',['id_produk'=>$id])->row();
    }

    // =========================
    // INSERT
    // =========================
    public function insert($data)
    {
        return $this->db->insert('produk', [
            'nama_produk'        => $data['nama_produk'],
            'gambar'             => $data['gambar'],
            'kategori_id'        => $data['kategori_id'],
            'supplier_id'        => $data['supplier_id'],
            'harga_beli'         => $data['harga_beli'],
            'harga_jual'         => $data['harga_jual'],
            'stok'               => $data['stok'],
            'stok_minimal'       => $data['stok_minimal'],
            'tanggal_kadaluarsa' => $data['tanggal_kadaluarsa']
        ]);
    }

    // =========================
    // UPDATE
    // =========================
    public function update($id, $data)
    {
        return $this->db
            ->where('id_produk',$id)
            ->update('produk', [
                'nama_produk'        => $data['nama_produk'],
                'gambar'             => $data['gambar'],
                'kategori_id'        => $data['kategori_id'],
                'supplier_id'        => $data['supplier_id'],
                'harga_beli'         => $data['harga_beli'],
                'harga_jual'         => $data['harga_jual'],
                'stok'               => $data['stok'],
                'stok_minimal'       => $data['stok_minimal'],
                'tanggal_kadaluarsa' => $data['tanggal_kadaluarsa']
            ]);
    }

    // =========================
    // DELETE
    // =========================
    public function delete($id)
    {
        return $this->db->delete('produk',['id_produk'=>$id]);
    }

    // =========================
    // KATEGORI
    // =========================
    public function getKategori()
    {
        return $this->db->get('kategori')->result();
    }

    // =========================
    // SUPPLIER
    // =========================
    public function getSupplier()
    {
        return $this->db->get('supplier')->result();
    }

    public function getFilteredAjax($keyword = null)
{
    $this->db->select('
        produk.*,
        kategori.nama_kategori,
        supplier.nama_supplier
    ');
    $this->db->from('produk');
    $this->db->join('kategori','kategori.id_kategori = produk.kategori_id','left');
    $this->db->join('supplier','supplier.id_supplier = produk.supplier_id','left');

    if($keyword){
        $this->db->group_start();
        $this->db->like('produk.nama_produk', $keyword);
        $this->db->or_like('kategori.nama_kategori', $keyword);
        $this->db->or_like('supplier.nama_supplier', $keyword);
        $this->db->group_end();
    }

    return $this->db->get()->result();
}
}