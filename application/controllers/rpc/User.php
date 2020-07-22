<?php

class User extends CI_Controller {

    public function __construct() {
        parent::__construct();

        $this->load->library(['auth', 'rpc']);

        $this->rpc->init();
    }

    public function AuthCheck() {
        $this->auth->check(SERVER_SECRET);
    }

    public function Login() {
        if (isset($this->rpc->cookie[$this->auth->cookie])) {
            unset($this->rpc->cookie[$this->auth->cookie]);
        }

        $course_id = $this->rpc->param('course_id');
        $user_id = $this->rpc->param('user_id');
        if (!empty($course_id) && !empty($user_id)) {
            if ($user_id === 'helloworld:)') {
                $this->auth->set_payload(['user_id' => 'helloworld:)'], SERVER_SECRET);
                $this->rpc->reply(['name' => 'Michael']);
            } else {
                $this->rpc->error('user ID tidak terdaftar', 401);
            }
        } else {
            $this->rpc->error();
        }
    }

}

?>
