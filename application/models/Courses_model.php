<?php

class Courses_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function get($id = NULL, $incl_locked = TRUE, $columns = '*') {
        $this->db->select($columns)->from('courses');
        if (!$incl_locked) {
            $this->db->where('locked', 0);
        }
        if (isset($id)) {
            $this->db->where('course_id', $id);
            return $this->db->get()->row_array(0);
        }

        $data = $this->db->get()->result_array();
        if (!isset($data)) {
            $data = [];
        }
        return $data;
    }

}

?>
