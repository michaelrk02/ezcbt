<?php

class User extends CI_Controller {

    public function __construct() {
        parent::__construct();

        $this->load->library(['auth', 'rpc']);

        $this->rpc->init();
    }

    public function AuthCheck() {
        $this->auth->check(SERVER_SECRET);
        $this->rpc->reply();
    }

    public function Login() {
        if (isset($this->rpc->cookie[$this->auth->cookie])) {
            unset($this->rpc->cookie[$this->auth->cookie]);
        }

        $course_id = $this->rpc->param('course_id');
        $user_id = $this->rpc->param('user_id');
        if (!empty($course_id) && !empty($user_id)) {
            $this->load->model('users_model');

            $user = $this->users_model->get($user_id, $course_id, 'name');
            if (isset($user)) {
                $this->auth->set_payload(['user_id' => $user_id], SERVER_SECRET);
                $this->rpc->reply(['name' => $user['name']]);
            } else {
                $this->rpc->error('user ID tidak terdaftar', 404);
            }
        } else {
            $this->rpc->error();
        }
    }

    public function GetHeaderInfo() {
        $this->load->model('courses_model');

        $user = $this->get_user('course_id,name');
        $course = $this->courses_model->get($user['course_id'], TRUE, 'title');
        if (isset($course)) {
            $this->rpc->reply(['course_title' => $course['title'], 'user_name' => $user['name']]);
        } else {
            $this->rpc->error('tes tidak terdaftar', 404);
        }
    }

    public function GetCourseDetails() {
        $this->load->model('courses_model');

        $user = $this->get_user('course_id');
        $course = $this->courses_model->get($user['course_id'], TRUE, 'title,description,locked,duration,num_choices,num_questions,allow_empty,score_correct,score_empty,score_wrong');
        if (isset($course)) {
            $this->rpc->reply($course);
        } else {
            $this->rpc->error('tes tidak terdaftar', 404);
        }
    }

    public function GetStatus() {
        $this->load->model(['courses_model', 'users_model']);

        $user = $this->get_user('user_id');
        $this->users_model->update_state($user['user_id']);

        $user = $this->get_user('course_id,answer_data,start_time,state');
        $course = $this->courses_model->get($user['course_id'], TRUE, 'duration,num_questions');
        if (isset($course)) {
            $status = [];
            $status['state'] = $user['state'];
            if ($status['state'] !== 'not started') {
                $status['num_answered'] = 0;
                for ($i = 0; $i < $course['num_questions']; $i++) {
                    if ($user['answer_data'][$i] !== '-') {
                        $status['num_answered']++;
                    }
                }
                $status['seconds_left'] = max($user['start_time'] + $course['duration'] - time(), 0);
            }
            $this->rpc->reply($status);
        } else {
            $this->rpc->error('tes tidak terdaftar', 404);
        }
    }

    public function GetCoursePDFURL() {
        $user = $this->get_user('course_id');

        $payload = ['course_id' => $user['course_id'], '__t' => time()];
        $token = [];
        $token[0] = base64_encode(json_encode($payload));
        $token[1] = hash_hmac('sha256', $token[0], SERVER_SECRET);
        $token = implode(':', $token);
        $this->rpc->reply(base_url('public/pdfjs/web/viewer.html').'?file='.urlencode(site_url('content/course_pdf').'?token='.urlencode($token)));
    }

    public function Start() {
        $this->load->model('users_model');

        $user = $this->get_user('user_id');
        $this->users_model->update_state($user['user_id']);

        $user = $this->users_model->get($user['user_id'], NULL, 'user_id,state');
        if ($user['state'] === 'not started') {
            $data = [];
            $data['start_time'] = time();
            $data['state'] = 'started';
            $this->users_model->set($user['user_id'], $data);
            $this->rpc->reply();
        } else if ($user['state'] === 'started') {
            $this->rpc->reply();
        } else if ($user['state'] === 'finished') {
            $this->rpc->error('anda telah menyelesaikan tes ini', 403);
        } else {
            $this->rpc->error('unknown user state', 500);
        }
    }

    public function Finish() {
        $user = $this->get_user('user_id,course_id,answer_data,state');
        if ($user['state'] === 'started') {
            $this->load->model('courses_model');

            $course = $this->courses_model->get($user['course_id'], TRUE, 'num_questions,allow_empty');
            if (isset($course)) {
                $this->load->model('users_model');

                for ($i = 0; $i < $course['num_questions']; $i++) {
                    if (($user['answer_data'][$i] === '-') && ($course['allow_empty'] == 0)) {
                        $this->rpc->error('masih terdapat beberapa soal yang belum dijawab', 403);
                        return;
                    }
                }

                $this->users_model->set($user['user_id'], ['state' => 'finished']);
                $this->rpc->reply();
            } else {
                $this->rpc->error('tes tidak terdaftar', 404);
            }
        } else {
            $this->rpc->error('anda tidak sedang mengerjakan tes ini');
        }
    }

    public function GetAnswerData() {
        $user = $this->get_user('answer_data');

        $this->rpc->reply($user['answer_data']);
    }

    public function Mark() {
        $this->load->model(['courses_model', 'users_model']);

        $user = $this->get_user('user_id');
        $this->users_model->update_state($user['user_id']);

        $user = $this->get_user('user_id,course_id,answer_data,state');
        if ($user['state'] === 'started') {
            $course = $this->courses_model->get($user['course_id'], TRUE, 'num_choices,num_questions,allow_empty');
            if (isset($course)) {
                $question_id = $this->rpc->param('question_id');
                $choice_id = $this->rpc->param('choice_id');
                if (isset($question_id) && isset($choice_id) && ($question_id < $course['num_questions'])) {
                    $question_id = max(0, $question_id);
                    if (is_numeric($choice_id)) {
                        $choice_id = max(min($choice_id, $course['num_choices'] - 1), 0);
                    } else {
                        $choice_id = '-';
                    }
                    if ((($choice_id === '-') && ($course['allow_empty'] == 1)) || ($choice_id !== '-')) {
                        $user['answer_data'][$question_id] = $choice_id;
                        $this->users_model->set($user['user_id'], ['answer_data' => $user['answer_data']]);
                        $this->rpc->reply();
                    } else {
                        $this->rpc->error('pilihan jawaban anda tidak valid');
                    }
                } else {
                    $this->rpc->error();
                }
            } else {
                $this->rpc->error('tes tidak terdaftar', 404);
            }
        } else {
            $this->rpc->error('anda tidak sedang mengerjakan tes ini', 403);
        }
    }

    private function get_user($columns = '*') {
        $this->auth->check(SERVER_SECRET);

        $payload = $this->auth->get_payload();
        if (isset($payload['user_id'])) {
            $this->load->model('users_model');

            $user_id = $payload['user_id'];
            $user = $this->users_model->get($user_id, NULL, $columns);
            if (isset($user)) {
                return $user;
            } else {
                $this->rpc->error('user ID tidak ditemukan', 404);
                exit();
            }
        } else {
            $this->rpc->error('invalid authentication payload');
            exit();
        }
    }

}

?>
