<?php

class Auth {

    private $CI;
    private $cookie = '_$auth_token';

    public function __construct() {
        $this->CI =& get_instance();

        $this->load->library('rpc');
    }

    public function check($secret) {
        $token = $this->rpc->cookie[$this->cookie];
        if (isset($token)) {
            
        } else {
            $this->rpc->error('you must login first to access this resource', 401);
        }
    }

    public function get_payload() {
        $token = $this->rpc->cookie[$this->cookie];
        $token = explode(':', $token);
        return json_decode(base64_decode($token[0]), TRUE);
    }

    public function set_payload($payload, $secret) {
        $token = $this->rpc->cookie[$this->cookie];
        if (isset($token)) {
            $token = explode(':', $token);
        } else {
            $token = [];
        }
        $token[0] = base64_encode(json_encode($payload));
        $token[1] = sha1($token[0].'$'.$secret);
        $token = implode(':', $token);
        $this->rpc->cookie[$this->cookie] = $token;
    }

}

?>
