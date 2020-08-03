<?php

class Sessions_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function get($session_id, $columns = '*') {
        $this->db->select($columns)->from('sessions');
        $this->db->where('session_id', $session_id);
        return $this->db->get()->row_array(0);
    }

    public function set($session_id, $data) {
        $this->db->where('session_id', $session_id);
        $this->db->update('sessions', $data);
    }

    public function update_state($session_id) {
        $this->load->model('courses_model');

        $data = $this->get($session_id, 'course_id,start_time,state');
        if (isset($data)) {
            $course = $this->courses_model->get($data['course_id'], TRUE, 'duration');
            if (isset($course)) {
                if ($data['state'] === 'started') {
                    if (time() > $data['start_time'] + $course['duration']) {
                        $data['state'] = 'finished';
                    }
                }
                $this->db->where('session_id', $session_id);
                $this->db->update('sessions', $data);
            }
        }
    }

}

?>
