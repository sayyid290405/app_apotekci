<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users_model extends CI_Model {

    private $table = 'users';
    private $rolesTable = 'roles';


    public function getAllUsers()
    {
        $this->db->select('users.*, roles.nama_role');
        $this->db->from('users');
        $this->db->join('roles', 'roles.id_role = users.role_id', 'left');
        return $this->db->get()->result();
    }

    public function getUserById($id)
    {
        return $this->db->get_where($this->table, ['id_user' => $id])->row();
    }

    public function createUser($data)
    {
        return $this->db->insert($this->table, $data);
    }

    public function updateUser($id, $data)
    {
        return $this->db->update($this->table, $data, ['id_user' => $id]);
    }

    public function deleteUser($id)
    {
        return $this->db->delete($this->table, ['id_user' => $id]);
    }

    // 🔥 AJAX SEARCH
    public function getFilteredAjax($keyword)
    {
        if(!empty($keyword)){
            $this->db->group_start();
            $this->db->like('nama', $keyword);
            $this->db->or_like('email', $keyword);
            $this->db->group_end();
        }

        return $this->db->get($this->table)->result();
    }

    public function getAllRoles()
    {
        return $this->db->get($this->rolesTable)->result();
    }
}