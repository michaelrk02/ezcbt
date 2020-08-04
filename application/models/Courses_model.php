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
        $this->update_signature($data['course_id']);

        return $data['course_id'];
    }

    public function update($id, $data) {
        $this->db->where('course_id', $id);
        $this->db->update('courses', $data);

        $sessions = $this->db->query('SELECT `session_id` FROM `sessions` WHERE `course_id` = ?', [$id])->result_array();
        if (isset($sessions)) {
            $this->load->model('sessions_model');

            foreach ($sessions as $session) {
                $this->sessions_model->update_answer_data($session['session_id']);
            }
        }

        $this->update_signature($id);
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

        $this->db->order_by('title');
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

    public function update_signature($id) {
        $course = $this->get($id, TRUE, 'duration,num_questions,num_choices,allow_empty');
        $pdf_hash = md5_file(APPPATH.'third_party/ezcbt/course/'.$id.'.pdf');
        $pdf_hash = empty($pdf_hash) ? '' : $pdf_hash;

        $data = [];
        $data['signature'] = md5(json_encode($course).$pdf_hash);

        $this->db->where('course_id', $id);
        $this->db->update('courses', $data);
    }

}

?>
