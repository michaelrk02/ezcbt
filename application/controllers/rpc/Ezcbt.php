<?php

class Ezcbt extends CI_Controller {

    public function __construct() {
        parent::__construct();

        $this->load->library('rpc');
    }

    public function GetCourses() {
        $this->load->model('courses_model');

        $incl_locked = $this->rpc->param('incl_locked');

        $this->rpc->reply($this->courses_model->get(NULL, !empty($incl_locked), 'course_id,title'));
    }

}

?>
