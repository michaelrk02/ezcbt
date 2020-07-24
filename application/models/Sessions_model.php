<?php

class Sessions_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function get($course_id, $user_id, $columns = '*') {
        $this->db->select($columns)->from('sessions');
        $this->db->where('course_id', $course_id);
        $this->db->where('user_id', $user_id);
        return $this->db->get()->row_array(0);
    }

    public function set($course_id, $user_id, $data) {
        $this->db->where('course_id', $course_id);
        $this->db->where('user_id', $user_id);
        $this->db->update('sessions', $data);
    }

    public function update_state($course_id, $user_id) {
        $this->load->model('courses_model');

        $data = $this->get($course_id, $user_id, 'start_time,state');
        if (isset($data)) {
            $course = $this->courses_model->get($course_id, TRUE, 'duration');
            if (isset($course)) {
                if ($data['state'] === 'started') {
                    if (time() > $data['start_time'] + $course['duration']) {
                        $data['state'] = 'finished';
                    }
                }
                $this->db->where('course_id', $course_id);
                $this->db->where('user_id', $user_id);
                $this->db->update('sessions', $data);
            }
        }
    }

}

?>
