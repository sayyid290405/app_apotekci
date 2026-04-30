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

<<<<<<< HEAD
    // =========================
    // GET BY ID
    // =========================
=======
>>>>>>> ee11432ab565d27cb1eddab3a20fae4e15c9c552
    public function getById($id)
    {
        return $this->db->get_where('produk',['id_produk'=>$id])->row();
    }

    // =========================
<<<<<<< HEAD
    // INSERT
    // =========================
    public function insert($data)
    {
        return $this->db->insert('produk', [
=======
    // INSERT (PERBAIKAN UTAMA)
    // =========================
    public function insert($data)
    {
        $insert_data = [
            'nama_produk'        => $data['nama_produk'],
            'gambar'             => $data['gambar'],
            'kategori_id'        => $data['kategori_id'],
            'supplier_id'        => $data['supplier_id'],
            'harga_beli'         => $data['harga_beli'],
            'harga_jual'         => $data['harga_jual'], // Harga satuan terkecil
            'stok'               => $data['stok'],
            'stok_minimal'       => $data['stok_minimal'],
            'tanggal_kadaluarsa' => $data['tanggal_kadaluarsa']
        ];

        $this->db->insert('produk', $insert_data);
        
        // PENTING: Mengembalikan ID produk yang baru saja dibuat
        // agar Controller bisa memasukkan data ke tabel satuan_produk
        return $this->db->insert_id();
    }

    // =========================
    // UPDATE
    // =========================
    public function update($id, $data)
    {
        $update_data = [
>>>>>>> ee11432ab565d27cb1eddab3a20fae4e15c9c552
            'nama_produk'        => $data['nama_produk'],
            'gambar'             => $data['gambar'],
            'kategori_id'        => $data['kategori_id'],
            'supplier_id'        => $data['supplier_id'],
            'harga_beli'         => $data['harga_beli'],
            'harga_jual'         => $data['harga_jual'],
            'stok'               => $data['stok'],
            'stok_minimal'       => $data['stok_minimal'],
            'tanggal_kadaluarsa' => $data['tanggal_kadaluarsa']
<<<<<<< HEAD
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
=======
        ];

        return $this->db
            ->where('id_produk',$id)
            ->update('produk', $update_data);
>>>>>>> ee11432ab565d27cb1eddab3a20fae4e15c9c552
    }

    // =========================
    // DELETE
    // =========================
    public function delete($id)
    {
<<<<<<< HEAD
        return $this->db->delete('produk',['id_produk'=>$id]);
    }

    // =========================
    // KATEGORI
    // =========================
=======
        // Secara default, data di satuan_produk harus ikut terhapus
        // Jika database Anda tidak menggunakan ON DELETE CASCADE, aktifkan baris bawah:
        // $this->db->delete('satuan_produk', ['produk_id' => $id]);
        
        return $this->db->delete('produk',['id_produk'=>$id]);
    }

>>>>>>> ee11432ab565d27cb1eddab3a20fae4e15c9c552
    public function getKategori()
    {
        return $this->db->get('kategori')->result();
    }

<<<<<<< HEAD
    // =========================
    // SUPPLIER
    // =========================
=======
>>>>>>> ee11432ab565d27cb1eddab3a20fae4e15c9c552
    public function getSupplier()
    {
        return $this->db->get('supplier')->result();
    }

    public function getFilteredAjax($keyword = null)
{
<<<<<<< HEAD
=======
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

public function getAllWithSatuan()
{
    $produk = $this->db
        ->select('produk.*, kategori.nama_kategori, supplier.nama_supplier')
        ->join('kategori','kategori.id_kategori = produk.kategori_id','left')
        ->join('supplier','supplier.id_supplier = produk.supplier_id','left')
        ->get('produk')
        ->result();

    // 🔥 FOREACH HARUS DI DALAM FUNCTION
    foreach ($produk as $p) {

        // ambil semua satuan
        $p->satuan = $this->db
            ->get_where('satuan_produk', ['produk_id' => $p->id_produk])
            ->result();

        // ambil satuan utama (konversi terkecil)
        $default = $this->db
            ->order_by('konversi', 'ASC')
            ->get_where('satuan_produk', ['produk_id' => $p->id_produk])
            ->row();

        if($default){
            $p->harga_tampil  = $default->harga;
            $p->satuan_tampil = $default->nama_satuan;
        } else {
            $p->harga_tampil  = $p->harga_jual;
            $p->satuan_tampil = $p->satuan_dasar;
        }
    }

    return $produk;
}

public function searchProduk($keyword = null)
{
>>>>>>> ee11432ab565d27cb1eddab3a20fae4e15c9c552
    $this->db->select('
        produk.*,
        kategori.nama_kategori,
        supplier.nama_supplier
    ');
    $this->db->from('produk');
    $this->db->join('kategori','kategori.id_kategori = produk.kategori_id','left');
    $this->db->join('supplier','supplier.id_supplier = produk.supplier_id','left');

<<<<<<< HEAD
    if($keyword){
=======
    if(!empty($keyword)){
>>>>>>> ee11432ab565d27cb1eddab3a20fae4e15c9c552
        $this->db->group_start();
        $this->db->like('produk.nama_produk', $keyword);
        $this->db->or_like('kategori.nama_kategori', $keyword);
        $this->db->or_like('supplier.nama_supplier', $keyword);
        $this->db->group_end();
    }

    return $this->db->get()->result();
}
}