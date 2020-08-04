<?php

class Users_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function create($data) {
        $this->load->helper('string');

        $data['user_id'] = '';
        do {
            $data['user_id'] = random_string();
        } while ($this->get($data['user_id'], 'user_id') != NULL);

        $this->db->insert('users', $data);
    }

    public function update($id, $data) {
        $this->db->where('user_id', $id);
        $this->db->update('users', $data);
    }

    public function get($id = NULL, $columns = '*') {
        $this->db->select($columns)->from('users');
        if (isset($id)) {
            $this->db->where('user_id', $id);
            return $this->db->get()->row_array(0);
        }

        $this->db->order_by('name');
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

    public function can_delete($id) {
        return empty($this->db->query('SELECT COUNT(*) AS `count` FROM `sessions` WHERE `user_id` = ?', [$id])->row_array(0)['count']);
    }

    public function delete($id) {
        $this->db->query('DELETE FROM `users` WHERE `user_id` = ?', [$id]);
    }

}

?>
