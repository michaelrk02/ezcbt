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

        $session_id = $this->rpc->param('session_id');
        if (!empty($session_id)) {
            $this->load->model('sessions_model');

            $session = $this->sessions_model->get($session_id, 'user_id');
            if (isset($session)) {
                $this->load->model('users_model');

                $user = $this->users_model->get($session['user_id'], 'name');
                if (isset($user)) {
                    $this->auth->set_payload(['session_id' => $session_id], SERVER_SECRET);
                    $this->rpc->reply(['name' => $user['name']]);
                } else {
                    $this->rpc->error('anda tidak terdaftar sebagai peserta. Ini merupakan kesalahan internal server, mohon hubungi panitia', 500);
                }
            } else {
                $this->rpc->error('ID sesi anda tidak valid. Cek penulisan sekali lagi', 403);
            }
        } else {
            $this->rpc->error();
        }
    }

    public function GetAppInfo() {
        $logo = FCPATH.APP_LOGO;
        if (file_exists($logo)) {
            $logo = base_url(APP_LOGO);
        } else {
            $logo = NULL;
        }
        $this->rpc->reply([
            'title' => APP_TITLE,
            'logo' => $logo
        ]);
    }

    public function GetHeaderInfo() {
        $this->load->model(['courses_model', 'users_model']);

        $session = $this->get_session('user_id,course_id');
        $course = $this->courses_model->get($session['course_id'], TRUE, 'title');
        $user = $this->users_model->get($session['user_id'], 'name');
        if (isset($course)) {
            $this->rpc->reply([
                'course_title' => $course['title'],
                'user_name' => $user['name']
            ]);
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
        $course = $this->courses_model->get($session['course_id'], TRUE, 'title,description,locked,duration,num_questions,num_choices,allow_empty,score_correct,score_empty,score_wrong');
        if (isset($course)) {
            $this->rpc->reply($course);
        } else {
            $this->rpc->error('tes tidak terdaftar', 404);
        }
    }

    public function GetStatus() {
        $this->load->model(['courses_model', 'sessions_model']);

        $session = $this->get_session('session_id');
        $this->sessions_model->update_state($session['session_id']);

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
        $this->rpc->reply($this->ezcbt->course_pdf_url($session['course_id']));
    }

    public function Start() {
        $this->load->model('sessions_model');

        $session = $this->get_session('session_id');
        $this->sessions_model->update_state($session['session_id']);

        $session = $this->get_session('session_id,state');
        if ($session['state'] === 'not started') {
            $data = [];
            $data['start_time'] = time();
            $data['state'] = 'started';
            $this->sessions_model->set($session['session_id'], $data);
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
        $session = $this->get_session('session_id,course_id,answer_data,state');
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

                $this->sessions_model->set($session['session_id'], ['state' => 'finished']);
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

        $session = $this->get_session('session_id');
        $this->sessions_model->update_state($session['session_id']);

        $session = $this->get_session('session_id,course_id,answer_data,state');
        if ($session['state'] === 'started') {
            $course = $this->courses_model->get($session['course_id'], TRUE, 'num_questions,num_choices,allow_empty');
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
                        $this->sessions_model->set($session['session_id'], ['answer_data' => $session['answer_data']]);
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
        if (isset($payload['session_id'])) {
            $this->load->model('sessions_model');

            $session = $this->sessions_model->get($payload['session_id'], $columns);
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
