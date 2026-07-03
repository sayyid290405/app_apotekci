<?php
class Kategori_model extends CI_Model {

    public function getAll()
    {
        return $this->db->order_by('nama_kategori', 'ASC')->get('kategori')->result();
    }

    public function getById($id)
    {
        return $this->db->get_where('kategori', ['id_kategori' => $id])->row();
    }

    public function insert($data)
    {
        return $this->db->insert('kategori', $data);
    }

    public function update($id, $data)
    {
        return $this->db->where('id_kategori', $id)
                        ->update('kategori', $data);
    }

    public function delete($id)
    {
        return $this->db->delete('kategori', ['id_kategori' => $id]);
    }

    // SEARCH AJAX
    public function search($keyword)
    {
        if($keyword){
            $this->db->like('nama_kategori', $keyword);
            $this->db->or_like('kelas_obat', $keyword);
            $this->db->or_like('peruntukan_usia', $keyword);
        }
        return $this->db->get('kategori')->result();
    }

    // ==================== CEK APAKAH KATEGORI DIGUNAKAN ====================
    public function isUsed($id)
    {
        return $this->db->where('kategori_id', $id)->count_all_results('produk') > 0;
    }

    // ==================== HITUNG JUMLAH PRODUK YANG MENGGUNAKAN ====================
    public function countUsed($id)
    {
        return $this->db->where('kategori_id', $id)->count_all_results('produk');
    }

    // ==================== GET PRODUK YANG MENGGUNAKAN KATEGORI ====================
    public function getProductsByCategory($id)
    {
        return $this->db->select('id_produk, nama_produk')
                        ->where('kategori_id', $id)
                        ->get('produk')
                        ->result();
    }
}