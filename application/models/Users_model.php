<?php

class Users_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function get($id = NULL, $columns = '*') {
        $this->db->select($columns)->from('users');
        if (isset($id)) {
            $this->db->where('user_id', $id);
            return $this->db->get()->row_array(0);
        }

        $data = $this->db->get()->result_array();
        if (!isset($data)) {
            $data = [];
        }
        return $data;
    }

    public function set($id, $data) {
        $this->db->where('user_id', $id);
        $this->db->update('users', $data);
    }

}

?>
