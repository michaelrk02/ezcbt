<?php

class Content extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $type = $this->input->get('type');
        $path = $this->input->get('path');
        $cache = $this->input->get('cache');

        if (!empty($path)) {
            $path = FCPATH.$path;
            if (file_exists($path)) {
                $this->output->set_status_header(200);
                if (empty($type)) {
                    $type = mime_content_type($path);
                }
                if (!empty($cache)) {
                    $this->output->set_header('Cache-Control: max-age='.$cache);
                } else {
                    $this->output->set_header('Cache-Control: no-cache');
                }
                $this->output->set_content_type($type);
                $this->output->set_output(file_get_contents($path));
            } else {
                $this->output->set_status_header(404);
            }
        } else {
            $this->output->set_status_header(400);
        }
    }

}

?>
