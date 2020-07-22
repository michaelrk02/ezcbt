<?php

class User extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $data['basename'] = parse_url(site_url('user'), PHP_URL_PATH);

        $this->load->view('user', $data);
    }

}

?>
