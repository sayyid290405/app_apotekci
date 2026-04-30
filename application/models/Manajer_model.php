<?php
defined('BASEPATH') OR exit('No direct script access allowed');
public class Manajer_model extends CI_Model{
// model untuk handling users
    public funtion get_all_users(){
        return $this->db->get('users')->result();
    }
    public function insert_users($data){
        return $this->db->insert('users', $data);
    }
    public function update_users($id, $data){
        return $this->db->where('id', $id)->update('users', $data);
    }

    public function delete_users($id){
        return $this->db->where('id', $id)->delete('users');
    }
// laporan ppenjualan graik penjualan obat, stock obat kadaluarssa
}
 

// approval pr(purchase request)

?>