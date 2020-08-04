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

    public function course_pdf() {
        $token = $this->input->get('token');
        if (!empty($token)) {
            $token = explode(':', $token);
            $payload = $token[0];
            $signature = $token[1];
            if (hash_hmac('sha256', $payload, SERVER_SECRET) === $signature) {
                $payload = json_decode(base64_decode($payload), TRUE);
                if (!empty($payload['course_id']) && !empty($payload['__t'])) {
                    if (time() <= $payload['__t'] + 10) {
                        $path = APPPATH.'third_party/ezcbt/course/'.$payload['course_id'].'.pdf';
                        if (file_exists($path)) {
                            $this->output->set_status_header(200);
                            $this->output->set_content_type('application/pdf');
                            $this->output->set_output(file_get_contents($path));
                        } else {
                            $this->output->set_status_header(404);
                        }
                    } else {
                        $this->output->set_status_header(401);
                    }
                } else {
                    $this->output->set_status_header(400);
                }
            } else {
                $this->output->set_status_header(401);
            }
        } else {
            $this->output->set_status_header(403);
        }
    }

}

?>
