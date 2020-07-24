<?php

class Admin extends CI_Controller {

    public function __construct() {
        parent::__construct();

        $this->load->library('session');
    }

    public function index() {
        redirect(site_url('admin/dashboard'));
    }

    public function login() {
        if (!empty($_SESSION['ezcbt_admin'])) {
            redirect(site_url('admin/dashboard'));
        }

        $this->load->helper('form');

        $redirect = $this->input->get('redirect');
        if (!isset($redirect)) {
            $redirect = 'admin/dashboard';
        }

        if (!empty($this->input->post('login'))) {
            $password = $this->input->post('password');
            if (isset($password)) {
                if (password_verify($password, ADMIN_PASSWORD)) {
                    $_SESSION['ezcbt_admin'] = TRUE;
                        redirect(site_url($redirect));
                } else {
                    $this->ezcbt->error('password tidak valid');
                }
            } else {
                $this->ezcbt->error('invalid parameter');
            }
        }

        $data = [];
        $data['redirect'] = $redirect;
        $data['status'] = $this->ezcbt->status();

        $this->render_header('Login');
        $this->load->view('admin/login', $data);
        $this->render_footer();
    }

    public function logout() {
        unset($_SESSION['ezcbt_admin']);
        redirect(site_url('admin'));
    }

    public function dashboard() {
        $this->login_check();

        $this->load->database();

        $data = [];
        $data['num_courses'] = $this->db->query('SELECT COUNT(*) AS `count` FROM `courses`')->row_array(0)['count'];
        $data['num_courses_locked'] = $this->db->query('SELECT COUNT(*) AS `count` FROM `courses` WHERE `locked` = 0')->row_array(0)['count'];
        $data['num_users'] = $this->db->query('SELECT COUNT(*) AS `count` FROM `users`')->row_array(0)['count'];
        $data['num_sessions'] = $this->db->query('SELECT COUNT(*) AS `count` FROM `sessions`')->row_array(0)['count'];
        $data['num_results'] = $this->db->query('SELECT COUNT(*) AS `count` FROM `sessions` WHERE `state` = "finished"')->row_array(0)['count'];
        $data['status'] = $this->ezcbt->status();

        $this->render_header('Dashboard');
        $this->load->view('admin/dashboard', $data);
        $this->render_footer();
    }

    private function login_check() {
        if (empty($_SESSION['ezcbt_admin'])) {
            redirect(site_url('admin/login').'?redirect='.urlencode(uri_string()));
        }
    }

    private function render_header($title = NULL) {
        $data = [];
        $data['title'] = $title;

        $this->load->view('admin/header', $data);
    }

    private function render_footer() {
        $data = [];
        $data['copyright_year'] = COPYRIGHT_YEAR;
        $data['copyright_owner'] = COPYRIGHT_OWNER;

        $this->load->view('admin/footer', $data);
    }

}

?>
