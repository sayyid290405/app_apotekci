<?php
class Supplier_model extends CI_Model {

    public function getAll()
    {
        return $this->db->get('supplier')->result();
    }

    public function getById($id)
    {
        return $this->db->get_where('supplier',['id_supplier'=>$id])->row();
    }

    public function insert($data)
    {
        return $this->db->insert('supplier',$data);
    }

    public function update($id,$data)
    {
        return $this->db->where('id_supplier',$id)->update('supplier',$data);
    }

    public function delete($id)
    {
        return $this->db->where('id_supplier',$id)->delete('supplier');
    }

    public function getFiltered($limit, $start, $keyword = null)
{
    if($keyword){
        $this->db->group_start();
        $this->db->like('nama_supplier', $keyword);
        $this->db->or_like('legalitas', $keyword);
        $this->db->or_like('kontak', $keyword);
        $this->db->group_end();
    }

    return $this->db->get('supplier', $limit, $start)->result();
}

public function countFiltered($keyword = null)
{
    if($keyword){
        $this->db->group_start();
        $this->db->like('nama_supplier', $keyword);
        $this->db->or_like('legalitas', $keyword);
        $this->db->or_like('kontak', $keyword);
        $this->db->group_end();
    }

    return $this->db->count_all_results('supplier');
}

public function countAll()
{
    return $this->db->count_all('supplier');
}

public function getPaginated($limit, $start)
{
    return $this->db->get('supplier', $limit, $start)->result();
}

public function getProdukBySupplier($id)
{
    return $this->db
        ->join('supplier', 'supplier.id_supplier = produk.supplier_id')
        ->where('supplier_id', $id)
        ->get('produk')
        ->result();
}

public function getFilteredAjax($keyword = null)
{
    if($keyword){
        $this->db->group_start();
        $this->db->like('nama_supplier', $keyword);
        $this->db->or_like('legalitas', $keyword);
        $this->db->or_like('kontak', $keyword);
        $this->db->group_end();
    }

    return $this->db->get('supplier')->result();
}
}