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

            $user = $this->users_model->get($user_id, 'name');
            if (isset($user)) {
                $this->load->model('sessions_model');

                $session = $this->sessions_model->get($course_id, $user_id, 'user_id');
                if (isset($session)) {
                    $this->auth->set_payload(['course_id' => $course_id, 'user_id' => $user_id], SERVER_SECRET);
                    $this->rpc->reply(['name' => $user['name']]);
                } else {
                    $this->rpc->error('anda tidak didaftarkan untuk mengikuti tes ini', 403);
                }
            } else {
                $this->rpc->error('user ID tidak terdaftar', 404);
            }
        } else {
            $this->rpc->error();
        }
    }

    public function GetHeaderInfo() {
        $this->load->model(['courses_model', 'users_model']);

        $session = $this->get_session('user_id,course_id');
        $course = $this->courses_model->get($session['course_id'], TRUE, 'title');
        $user = $this->users_model->get($session['user_id'], 'name');
        if (isset($course)) {
            $this->rpc->reply(['course_title' => $course['title'], 'user_name' => $user['name']]);
        } else {
            $this->rpc->error('tes tidak terdaftar', 404);
        }
    }

    public function GetFooterInfo() {
        $this->rpc->reply(['copyright_year' => COPYRIGHT_YEAR, 'copyright_owner' => COPYRIGHT_OWNER]);
    }

    public function GetCourses() {
        $this->load->model('courses_model');

        $incl_locked = $this->rpc->param('incl_locked');

        $this->rpc->reply($this->courses_model->get(NULL, !empty($incl_locked), 'course_id,title'));
    }

    public function GetCourseDetails() {
        $this->load->model('courses_model');

        $session = $this->get_session('course_id');
        $course = $this->courses_model->get($session['course_id'], TRUE, 'title,description,locked,duration,num_choices,num_questions,allow_empty,score_correct,score_empty,score_wrong');
        if (isset($course)) {
            $this->rpc->reply($course);
        } else {
            $this->rpc->error('tes tidak terdaftar', 404);
        }
    }

    public function GetStatus() {
        $this->load->model(['courses_model', 'sessions_model']);

        $session = $this->get_session('course_id,user_id');
        $this->sessions_model->update_state($session['course_id'], $session['user_id']);

        $session = $this->get_session('course_id,answer_data,start_time,state');
        $course = $this->courses_model->get($session['course_id'], TRUE, 'duration,num_questions');
        if (isset($course)) {
            $status = [];
            $status['state'] = $session['state'];
            if ($status['state'] !== 'not started') {
                $status['num_answered'] = 0;
                for ($i = 0; $i < $course['num_questions']; $i++) {
                    if ($session['answer_data'][$i] !== '-') {
                        $status['num_answered']++;
                    }
                }
                $status['seconds_left'] = max($session['start_time'] + $course['duration'] - time(), 0);
            }
            $this->rpc->reply($status);
        } else {
            $this->rpc->error('tes tidak terdaftar', 404);
        }
    }

    public function GetCoursePDFURL() {
        $session = $this->get_session('course_id');

        $payload = ['course_id' => $session['course_id'], '__t' => time()];
        $token = [];
        $token[0] = base64_encode(json_encode($payload));
        $token[1] = hash_hmac('sha256', $token[0], SERVER_SECRET);
        $token = implode(':', $token);
        $this->rpc->reply(base_url('public/pdfjs/web/viewer.html').'?file='.urlencode(site_url('content/course_pdf').'?token='.urlencode($token)));
    }

    public function Start() {
        $this->load->model('sessions_model');

        $session = $this->get_session('course_id,user_id');
        $this->sessions_model->update_state($session['course_id'], $session['user_id']);

        $session = $this->get_session('course_id,user_id,state');
        if ($session['state'] === 'not started') {
            $data = [];
            $data['start_time'] = time();
            $data['state'] = 'started';
            $this->sessions_model->set($session['course_id'], $session['user_id'], $data);
            $this->rpc->reply();
        } else if ($session['state'] === 'started') {
            $this->rpc->reply();
        } else if ($session['state'] === 'finished') {
            $this->rpc->error('anda telah menyelesaikan tes ini', 403);
        } else {
            $this->rpc->error('unknown user state', 500);
        }
    }

    public function Finish() {
        $session = $this->get_session('course_id,user_id,answer_data,state');
        if ($session['state'] === 'started') {
            $this->load->model('courses_model');

            $course = $this->courses_model->get($session['course_id'], TRUE, 'num_questions,allow_empty');
            if (isset($course)) {
                $this->load->model('sessions_model');

                for ($i = 0; $i < $course['num_questions']; $i++) {
                    if (($session['answer_data'][$i] === '-') && ($course['allow_empty'] == 0)) {
                        $this->rpc->error('masih terdapat beberapa soal yang belum dijawab', 403);
                        return;
                    }
                }

                $this->sessions_model->set($session['course_id'], $session['user_id'], ['state' => 'finished']);
                $this->rpc->reply();
            } else {
                $this->rpc->error('tes tidak terdaftar', 404);
            }
        } else {
            $this->rpc->error('anda tidak sedang mengerjakan tes ini');
        }
    }

    public function GetAnswerData() {
        $session = $this->get_session('answer_data');

        $this->rpc->reply($session['answer_data']);
    }

    public function Mark() {
        $this->load->model(['courses_model', 'users_model']);

        $session = $this->get_session('course_id,user_id');
        $this->sessions_model->update_state($session['course_id'], $session['user_id']);

        $session = $this->get_session('course_id,user_id,answer_data,state');
        if ($session['state'] === 'started') {
            $course = $this->courses_model->get($session['course_id'], TRUE, 'num_choices,num_questions,allow_empty');
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
                        $session['answer_data'][$question_id] = $choice_id;
                        $this->sessions_model->set($session['course_id'], $session['user_id'], ['answer_data' => $session['answer_data']]);
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

    private function get_session($columns = '*') {
        $this->auth->check(SERVER_SECRET);

        $payload = $this->auth->get_payload();
        if (isset($payload['course_id']) && isset($payload['user_id'])) {
            $this->load->model('sessions_model');

            $session = $this->sessions_model->get($payload['course_id'], $payload['user_id'], $columns);
            if (isset($session)) {
                return $session;
            } else {
                $this->rpc->error('sesi tidak terdaftar', 404);
                exit();
            }
        } else {
            $this->rpc->error('invalid authentication payload');
            exit();
        }
    }

}

?>
