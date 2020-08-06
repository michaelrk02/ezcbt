<?php

class Sessions_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function create($course_id, $user_id) {
        $this->load->helper('string');
        $this->load->model('courses_model');

        $data = [];
        $data['course_id'] = $course_id;
        $data['user_id'] = $user_id;
        $data['start_time'] = 0;
        $data['state'] = 'not started';
        $data['score'] = 0;
        $data['details'] = '';

        $data['session_id'] = '';
        do {
            $data['session_id'] = random_string('alnum', 8);
        } while ($this->get($data['session_id'], 'session_id') !== NULL);

        $course = $this->courses_model->get($course_id, TRUE, 'num_questions');
        if (isset($course)) {
            $data['answer_data'] = str_repeat('-', $course['num_questions']);

            $this->db->insert('sessions', $data);
        } else {
            header('HTTP/1.0 500 course not found');
            exit();
        }
    }

    public function get($session_id = NULL, $columns = '*') {
        $this->db->select($columns)->from('sessions');
        if (isset($session_id)) {
            $this->db->where('session_id', $session_id);
            return $this->db->get()->row_array(0);
        }

        $data = $this->db->get()->result_array();
        if (!isset($data)) {
            $data = [];
        }
        return $data;
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

                $this->calculate_score($session_id);
            }
        }
    }

    public function filter($course_id = NULL, $user_id = NULL, $state = NULL) {
        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $items = isset($_GET['items']) ? $_GET['items'] : 15;

        $result = [];

        $this->load->database();
        $this->db->select('session_id,sessions.course_id,sessions.user_id,courses.title,users.name,start_time,state,score,details')->from('sessions');
        if (isset($course_id)) {
            $this->db->where('sessions.course_id', $course_id);
        }
        if (isset($user_id)) {
            $this->db->where('sessions.user_id', $user_id);
        }
        if (isset($state)) {
            $this->db->where('state', $state);
        }
        $this->db->order_by('score', 'DESC');
        $this->db->order_by('start_time', 'DESC');
        $this->db->order_by('users.name', 'ASC');
        $max_items = $this->db->count_all_results('', FALSE);

        $this->db->join('courses', 'sessions.course_id = courses.course_id');
        $this->db->join('users', 'sessions.user_id = users.user_id');

        if ($items > 0) {
            $result['max_page'] = max(ceil($max_items / $items), 1);
        } else {
            $result['max_page'] = 1;
        }

        if ((1 <= $page) && ($page <= $result['max_page']) && ($items > 0)) {
            $this->db->limit($items, ($page - 1) * $items);
            $result['data'] = $this->db->get()->result_array();
        } else {
            $result['data'] = [];
        }

        $_GET['page'] = $page;
        $_GET['items'] = $items;

        return $result;
    }

    public function update_answer_data($session_id) {
        $session = $this->get($session_id, 'course_id,answer_data');
        if (isset($session)) {
            $this->load->model('courses_model');

            $course = $this->courses_model->get($session['course_id'], TRUE, 'num_questions,num_choices');
            if (isset($course)) {
                if ($course['num_questions'] < strlen($session['answer_data'])) {
                    $session['answer_data'] = substr($session['answer_data'], 0, $course['num_questions']);
                } else if ($course['num_questions'] > strlen($session['answer_data'])) {
                    $session['answer_data'] .= str_repeat('-', $course['num_questions'] - strlen($session['answer_data']));
                }
                for ($i = 0; $i < $course['num_questions']; $i++) {
                    if ($session['answer_data'][$i] !== '-') {
                        if ($course['num_choices'] - 1 < $session['answer_data'][$i]) {
                            $session['answer_data'][$i] = '-';
                        }
                    }
                }
                $this->set($session_id, ['answer_data' => $session['answer_data']]);
                $this->calculate_score($session_id);
            } else {
                header('HTTP/1.0 500 course not found');
                exit();
            }
        } else {
            header('HTTP/1.0 500 session not found');
            exit();
        }
    }

    public function calculate_score($session_id) {
        $session = $this->get($session_id, 'course_id,answer_data,state');
        if (isset($session)) {
            if ($session['state'] === 'finished') {
                $this->load->model('courses_model');

                $course = $this->courses_model->get($session['course_id'], TRUE, 'num_questions,num_choices,correct_answers,score_correct,score_empty,score_wrong');
                if (isset($course)) {
                    if (strlen($course['correct_answers']) == $course['num_questions']) {
                        $num_correct = 0;
                        $num_empty = 0;
                        $num_wrong = 0;
                        for ($i = 0; $i < $course['num_questions']; $i++) {
                            $answer = $session['answer_data'][$i];
                            $correct = $course['correct_answers'][$i];
                            if ($answer === $correct) {
                                $num_correct++;
                            } else {
                                if ($answer === '-') {
                                    $num_empty++;
                                } else {
                                    $num_wrong++;
                                }
                            }
                        }
                        $score = $num_correct * $course['score_correct'] + $num_empty * $course['score_empty'] + $num_wrong * $course['score_wrong'];
                        $details = 'B'.$num_correct.' K'.$num_empty.' S'.$num_wrong;
                        $this->set($session_id, ['score' => $score, 'details' => $details]);
                    } else {
                        header('HTTP/1.0 500 invalid correct answer data');
                        exit();
                    }
                } else {
                    header('HTTP/1.0 500 course not found');
                    exit();
                }
            }
        } else {
            header('HTTP/1.0 500 session not found');
            exit();
        }
    }

    public function delete($id) {
        $this->db->where('session_id', $id);
        $this->db->delete('sessions');
    }

}

?>
