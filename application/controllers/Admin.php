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

    /*=======================================================================*/

    // === DASHBOARD ===

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

    // === COURSE MANAGEMENT ===

    public function course_manage() {
        $this->login_check();

        $this->load->library('dbsearch');

        $data = [];
        $data['courses'] = $this->dbsearch->search('courses', 'title', 'course_id,title,locked,duration,num_questions');
        $data['status'] = $this->ezcbt->status();

        $this->render_header('Kelola Materi');
        $this->load->view('admin/course_manage', $data);
        $this->render_footer();
    }

    public function course_create() {
        $this->login_check();

        $this->load->helper('form');

        if (!empty($this->input->post('submit'))) {
            $this->load->library('form_validation');

            $this->course_init_editor(TRUE);
            if ($this->form_validation->run()) {
                $this->load->model('courses_model');

                $data = $this->course_get_data();

                $id = $this->courses_model->create($data);
                $this->ezcbt->success('Materi <b>'.$data['title'].'</b> berhasil ditambahkan');
                $this->course_upload_pdf($id);
                redirect('admin/course_manage');
            } else {
                $this->ezcbt->error(validation_errors());
            }
        }

        $course = $this->course_init_form_data(TRUE);
        $course['file_available'] = FALSE;
        $course['_ezcbt_status'] = $this->ezcbt->status();

        $this->render_header('Tambah Materi');
        $this->load->view('admin/course_editor', $course);
        $this->render_footer();
    }

    public function course_edit() {
        $this->login_check();

        $this->load->helper('form');

        $id = $this->input->get('id');
        if (isset($id)) {
            $this->load->model('courses_model');

            $course = $this->courses_model->get($id, TRUE, 'course_id');
            if (isset($course)) {
                if (!empty($this->input->post('submit'))) {
                    $this->load->library('form_validation');

                    $this->course_init_editor();
                    if ($this->form_validation->run()) {
                        $data = $this->course_get_data();

                        $this->courses_model->update($id, $data);
                        $this->ezcbt->success('Materi <b>'.$data['title'].'</b> berhasil diperbarui');
                        $this->course_upload_pdf($id);
                        redirect('admin/course_manage');
                    } else {
                        $this->ezcbt->error(validation_errors());
                    }
                }
            } else {
                $this->ezcbt->error('Materi tidak terdaftar');
                redirect('admin/course_manage');
            }
        } else {
            $this->ezcbt->error('Silakan pilih materi terlebih dahulu');
            redirect('admin/course_manage');
        }

        $course = $this->course_init_form_data();
        $course['course_id'] = $this->input->get('id');
        $course['file_available'] = file_exists(APPPATH.'third_party/ezcbt/course/'.$course['course_id'].'.pdf');
        $course['_ezcbt_status'] = $this->ezcbt->status();

        $this->render_header('Perbarui Materi');
        $this->load->view('admin/course_editor', $course);
        $this->render_footer();
    }

    public function course_delete() {
        $this->login_check();

        $course = NULL;
        $id = $this->input->get('id');
        if (isset($id)) {
            $this->load->model('courses_model');

            $course = $this->courses_model->get($id, TRUE, 'title');
            if (isset($course)) {
                if ($this->courses_model->can_delete($id)) {
                    if (!empty($this->input->get('confirm'))) {
                        $this->courses_model->delete($id);
                        unlink(APPPATH.'third_party/ezcbt/course/'.$id.'.pdf');
                        $this->ezcbt->success('Materi <b>'.$course['title'].'</b> berhasil dihapus');
                        redirect('admin/course_manage');
                    }
                } else {
                    $this->ezcbt->error('Tidak dapat menghapus materi <b>'.$course['title'].'</b>, masih terdapat sesi yang berlangsung');
                    redirect('admin/course_manage');
                }
            } else {
                $this->ezcbt->error('Materi tidak ditemukan');
                redirect('admin/course_manage');
            }
        } else {
            $this->ezcbt->error('Silakan pilih materi terlebih dahulu');
            redirect('admin/course_manage');
        }

        $this->render_header('Hapus Materi');
        $this->load->view('admin/course_delete', $course);
        $this->render_footer();
    }

    public function course_pdf() {
        $this->login_check();

        $id = $this->input->get('id');
        if (isset($id)) {
            redirect($this->ezcbt->course_pdf_url($id));
        } else {
            exit('Kesalahan parameter');
        }
    }

    private function course_init_editor($create = FALSE) {
        $this->load->database();
        $this->load->library('form_validation');

        if (!$create) {
            $this->form_validation->set_rules('course_id', 'course ID', 'required');
        }
        $this->form_validation->set_rules('title', 'course title', 'required|max_length[100]'.($create ? '|is_unique[courses.title]' : ''));
        $this->form_validation->set_rules('description', 'course description', 'max_length[500]');
        $this->form_validation->set_rules('locked', 'locked status', 'in_list[0,1]');
        $this->form_validation->set_rules('duration', 'duration', 'required|is_natural');
        $this->form_validation->set_rules('num_questions', 'number of questions', 'required|is_natural_no_zero|less_than_equal_to[500]');
        $this->form_validation->set_rules('num_choices', 'number of choices', 'required|integer|greater_than[1]|less_than_equal_to[10]');

        $this->form_validation->set_rules('correct_answers', 'correct answer data', [
            'required',
            'numeric',
            function($value) {
                return strlen($value) == $this->input->post('num_questions');
            }
        ]);

        $this->form_validation->set_rules('allow_empty', 'allow empty status', 'in_list[0,1]');
        $this->form_validation->set_rules('score_correct', 'score if correct', 'required|integer');
        $this->form_validation->set_rules('score_empty', 'score if empty', 'required|integer');
        $this->form_validation->set_rules('score_wrong', 'score if wrong', 'required|integer');
    }

    private function course_get_data() {
        $fields = ['title', 'description', 'locked:bool', 'duration', 'num_questions', 'num_choices', 'correct_answers', 'allow_empty:bool', 'score_correct', 'score_empty', 'score_wrong'];
        $course = [];
        foreach ($fields as $field) {
            $field = explode(':', $field);
            if (count($field) == 1) {
                $course[$field[0]] = $this->input->post($field[0]);
            } else {
                if ($field[1] == 'bool') {
                    $course[$field[0]] = (int)!empty($this->input->post($field[0]));
                }
            }
        }
        return $course;
    }

    private function course_init_form_data($create = FALSE) {
        $this->load->helper('form_helper');

        $course = NULL;
        $entries = ['title:', 'description:', 'locked:0', 'duration:0', 'num_questions:1', 'num_choices:2', 'correct_answers:', 'allow_empty:1', 'score_correct:0', 'score_empty:0', 'score_wrong:0'];
        if (!$create) {
            $this->load->model('courses_model');

            $course = $this->courses_model->get($this->input->get('id'), TRUE, implode(',', array_map(function($value) { return explode(':', $value)[0]; }, $entries)));
        } else {
            $course = [];
        }
        foreach ($entries as $entry) {
            $entry = explode(':', $entry);
            $course[$entry[0]] = set_value($entry[0], $create ? $entry[1] : $course[$entry[0]]);
        }
        return $course;
    }

    private function course_upload_pdf($id) {
        if ($_FILES['course_pdf']['size'] != 0) {
            $this->load->library('upload');

            $this->upload->initialize([
                'upload_path' => APPPATH.'third_party/ezcbt/course/',
                'allowed_types' => 'pdf',
                'file_name' => $id,
                'file_ext_tolower' => TRUE,
                'max_size' => 2048,
                'overwrite' => TRUE
            ]);

            if (!$this->upload->do_upload('course_pdf')) {
                $this->ezcbt->error('PDF upload error: '.$this->upload->display_errors().'. Please reupload the file with correct parameters');
            } else {
                $this->load->model('courses_model');

                chmod($this->upload->data('full_path'), 0666);
                $this->courses_model->update_signature($id);
            }
        }
    }

    // === USER MANAGEMENT ===

    public function user_manage() {
        $this->login_check();

        $this->load->library('dbsearch');

        $data = [];
        $data['users'] = $this->dbsearch->search('users', 'name', 'user_id,name');
        $data['status'] = $this->ezcbt->status();

        $this->render_header('Kelola Peserta');
        $this->load->view('admin/user_manage', $data);
        $this->render_footer();
    }

    public function user_create() {
        $this->login_check();

        if (!empty($this->input->post('submit'))) {
            $this->load->library('form_validation');

            $this->user_init_editor(TRUE);
            if ($this->form_validation->run()) {
                $this->load->model('users_model');

                $data = $this->user_get_data();

                $id = $this->users_model->create($data);
                $this->ezcbt->success('Peserta <b>'.$data['name'].'</b> berhasil ditambahkan');
                redirect('admin/user_manage');
            } else {
                $this->ezcbt->error(validation_errors());
            }
        }

        $user = $this->user_init_form_data(TRUE);
        $user['_ezcbt_status'] = $this->ezcbt->status();

        $this->render_header('Tambah Peserta');
        $this->load->view('admin/user_editor', $user);
        $this->render_footer();
    }

    public function user_edit() {
        $this->login_check();

        $this->load->helper('form');

        $id = $this->input->get('id');
        if (isset($id)) {
            $this->load->model('users_model');

            $user = $this->users_model->get($id, 'user_id');
            if (isset($user)) {
                if (!empty($this->input->post('submit'))) {
                    $this->load->library('form_validation');

                    $this->user_init_editor();
                    if ($this->form_validation->run()) {
                        $data = $this->user_get_data();

                        $this->users_model->update($id, $data);
                        $this->ezcbt->success('Peserta <b>'.$data['name'].'</b> berhasil diperbarui');
                        redirect('admin/user_manage');
                    } else {
                        $this->ezcbt->error(validation_errors());
                    }
                }
            } else {
                $this->ezcbt->error('Peserta tidak terdaftar');
                redirect('admin/user_manage');
            }
        } else {
            $this->ezcbt->error('Silakan pilih peserta terlebih dahulu');
            redirect('admin/user_manage');
        }

        $user = $this->user_init_form_data();
        $user['user_id'] = $this->input->get('id');
        $user['_ezcbt_status'] = $this->ezcbt->status();

        $this->render_header('Perbarui Peserta');
        $this->load->view('admin/user_editor', $user);
        $this->render_footer();
    }

    public function user_delete() {
        $this->login_check();

        $user = NULL;
        $id = $this->input->get('id');
        if (isset($id)) {
            $this->load->model('users_model');

            $user = $this->users_model->get($id, 'name');
            if (isset($user)) {
                if ($this->users_model->can_delete($id)) {
                    if (!empty($this->input->get('confirm'))) {
                        $this->users_model->delete($id);
                        $this->ezcbt->success('Peserta <b>'.$user['name'].'</b> berhasil dihapus');
                        redirect('admin/user_manage');
                    }
                } else {
                    $this->ezcbt->error('Tidak dapat menghapus peserta <b>'.$user['name'].'</b>, masih terdapat sesi yang berlangsung');
                    redirect('admin/user_manage');
                }
            } else {
                $this->ezcbt->error('Peserta tidak ditemukan');
                redirect('admin/user_manage');
            }
        } else {
            $this->ezcbt->error('Silakan pilih peserta terlebih dahulu');
            redirect('admin/user_manage');
        }

        $this->render_header('Hapus Peserta');
        $this->load->view('admin/user_delete', $user);
        $this->render_footer();
    }

    private function user_init_editor($create = FALSE) {
        $this->load->library('form_validation');

        if (!$create) {
            $this->form_validation->set_rules('user_id', 'user ID', 'required');
        }
        $this->form_validation->set_rules('name', 'user name', 'required|max_length[100]');
    }

    private function user_get_data() {
        $user = [];
        $user['name'] = $this->input->post('name');
        return $user;
    }

    private function user_init_form_data($create = FALSE) {
        $this->load->helper('form_helper');

        if (!$create) {
            $this->load->model('users_model');

            $user = $this->users_model->get($this->input->get('id'), 'name');
        } else {
            $user = [];
        }
        $user['name'] = set_value('name', $create ? '' : $user['name']);
        return $user;
    }

    // === SESSION MANAGEMENT ===

    public function session_manage() {
        $this->login_check();

        $this->load->helper('text');
        $this->load->model(['courses_model', 'users_model', 'sessions_model']);

        $data = [];

        $data['courses'] = array_merge([['course_id' => '', 'title' => '-- semua --']], $this->courses_model->get(NULL, TRUE, 'course_id,title'));
        $data['users'] = array_merge([['user_id' => '', 'name' => '-- semua --']], $this->users_model->get(NULL, 'user_id,name'));
        $data['states'] = [
            ['id' => '', 'text' => '-- semua --'],
            ['id' => 'not started', 'text' => 'Belum Mengerjakan'],
            ['id' => 'started', 'text' => 'Sedang Mengerjakan'],
            ['id' => 'finished', 'text' => 'Telah Menyelesaikan']
        ];

        $course_id = !empty($_GET['course_id']) ? $_GET['course_id'] : NULL;
        $user_id = !empty($_GET['user_id']) ? $_GET['user_id'] : NULL;
        $state = !empty($_GET['state']) ? $_GET['state'] : NULL;
        $data['sessions'] = $this->sessions_model->filter($course_id, $user_id, $state);
        $data['course_id'] = isset($course_id) ? $course_id : '';
        $data['user_id'] = isset($user_id) ? $user_id : '';
        $data['state'] = isset($state) ? $state : '';

        $data['param'] = [];
        $data['param'][] = 'course_id='.urlencode($data['course_id']);
        $data['param'][] = 'user_id='.urlencode($data['user_id']);
        $data['param'][] = 'state='.urlencode($data['state']);
        $data['param'] = implode('&', $data['param']);

        $data['status'] = $this->ezcbt->status();

        $this->render_header('Kelola Sesi');
        $this->load->view('admin/session_manage', $data);
        $this->render_footer();
    }

    public function session_create() {
        $this->login_check();

        $this->load->helper('form');
        $this->load->model(['courses_model', 'users_model']);

        if (!empty($this->input->post('submit'))) {
            $this->load->library('form_validation');

            $this->form_validation->set_rules('course_id', 'course ID', 'required');
            $this->form_validation->set_rules('user_id', 'user ID', 'required');

            if ($this->form_validation->run()) {
                $course_id = $this->input->post('course_id');
                $user_id = $this->input->post('user_id');

                $course = $this->courses_model->get($course_id, TRUE, 'course_id');
                $user = $this->users_model->get($user_id, 'user_id');
                if (isset($course) && isset($user)) {
                    $this->load->model('sessions_model');

                    $this->sessions_model->create($course_id, $user_id);
                    $this->ezcbt->success('Sesi berhasil dibuat!');

                    $param = isset($_GET['param']) ? '?'.$_GET['param'] : '';

                    redirect(site_url('admin/session_manage').$param);
                } else {
                    $this->ezcbt->error('materi atau peserta tidak terdaftar', 404);
                }
            } else {
                $this->ezcbt->error(validation_errors());
            }
        }

        $data = [];
        $data['courses'] = array_merge([['course_id' => '', 'title' => '-- pilih --']], $this->courses_model->get(NULL, 'course_id,title'));
        $data['users'] = array_merge([['user_id' => '', 'name' => '-- pilih --']], $this->users_model->get(NULL, 'user_id,name'));
        $data['param'] = isset($_GET['param']) ? $_GET['param'] : '';
        $data['status'] = $this->ezcbt->status();

        $this->render_header('Tambah Sesi');
        $this->load->view('admin/session_create', $data);
        $this->render_footer();
    }

    public function session_delete() {
        $this->login_check();

        $id = $this->input->get('id');
        if (!empty($id)) {
            $this->load->model('sessions_model');

            $session = $this->sessions_model->get($id, 'session_id');
            if (isset($session)) {
                $this->sessions_model->delete($id);
                $this->ezcbt->success('Sesi berhasil dihapus!');
            } else {
                $this->ezcbt->error('Sesi tidak terdaftar');
            }
        }

        $param = isset($_GET['param']) ? '?'.$_GET['param'] : '';

        redirect(site_url('admin/session_manage').$param);
    }

    /*=======================================================================*/

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
