<?php
class Kategori_model extends CI_Model {

    public function getAll()
    {
        return $this->db->get('kategori')->result();
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
}