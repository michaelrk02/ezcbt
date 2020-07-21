<?php

class Rpc {

    private $CI;

    public $cookie = [];

    public function __construct() {
        $this->CI =& get_instance();
    }

    public function init() {
        $_POST = json_decode($this->CI->input->raw_input_stream, TRUE);
        $cookie = $this->CI->input->get_request_header('X-RPC-Cookie');
        if (isset($cookie)) {
            $this->cookie = json_decode(base64_decode($cookie), TRUE);
        }
    }

    public function param($name, $default = NULL) {
        $arg = $this->CI->input->post($name);
        return isset($arg) ? $arg : $default;
    }

    public function reply($object = NULL) {
        $this->CI->output->set_status_header(200);
        $this->CI->output->set_content_type('application/json');
        $this->CI->output->set_output(json_encode(['__value' => $object, '__cookie' => $this->cookie]));
    }

    public function error($message = 'incorrect parameter', $code = 400) {
        $this->CI->output->set_status_header($code, $message);
    }

}

?>
