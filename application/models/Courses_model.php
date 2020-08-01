<?php

class Courses_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function create($data) {
        $this->load->helper('string');

        $data['course_id'] = '';
        do {
            $data['course_id'] = random_string('alnum', 8);
        } while ($this->get($data['course_id'], TRUE, 'course_id') !== NULL);

        $this->db->insert('courses', $data);
        return $data['course_id'];
    }

    public function update($id, $data) {
        $this->db->where('course_id', $id);
        $this->db->update('courses', $data);
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

    public function can_delete($id) {
        return empty($this->db->query('SELECT COUNT(*) AS `count` FROM `sessions` WHERE `course_id` = ?', [$id])->row_array(0)['count']);
    }

    public function delete($id) {
        $this->db->query('DELETE FROM `courses` WHERE `course_id` = ?', [$id]);
    }

}

?>
