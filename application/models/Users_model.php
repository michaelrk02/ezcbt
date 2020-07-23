<?php

class Users_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function get($id = NULL, $course_id = NULL, $columns = '*') {
        $this->db->select($columns)->from('users');
        if (isset($course_id)) {
            $this->db->where('course_id', $course_id);
        }
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

    public function update_state($id) {
        $this->load->model('courses_model');

        $data = $this->get($id, NULL, 'course_id,start_time,state');
        if (isset($data)) {
            $course = $this->courses_model->get($data['course_id'], TRUE, 'duration');
            if (isset($course)) {
                $this->db->where('user_id', $id);
                if ($data['state'] === 'started') {
                    if (time() > $data['start_time'] + $course['duration']) {
                        $data['state'] = 'finished';
                    }
                }
                $this->db->update('users', $data);
            }
        }
    }

}

?>
