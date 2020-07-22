<?php

class Auth {

    public $cookie = '_$auth_token';

    private $CI;

    public function __construct() {
        $this->CI =& get_instance();

        $this->CI->load->library('rpc');
    }

    public function check($secret, $expires = 86400) {
        $token = isset($this->CI->rpc->cookie[$this->cookie]) ? $this->CI->rpc->cookie[$this->cookie] : NULL;
        if (isset($token)) {
            $token = explode(':', $token);
            if (hash_hmac('sha256', $token[0], $secret) === $token[1]) {
                $payload = json_decode(base64_decode($token[0]), TRUE);
                if (!empty($payload['__t'])) {
                    if (time() <= $payload['__t'] + $expires) {
                        $payload['__t'] = time();
                        $this->set_payload($payload, $secret);
                    } else {
                        $this->CI->rpc->error('authentication token is expired', 401);
                        exit();
                    }
                } else {
                    $this->CI->rpc->error('missing timestamp on authentication payload', 401);
                    exit();
                }
            } else {
                $this->CI->rpc->error('invalid token signature', 401);
                exit();
            }
        } else {
            $this->CI->rpc->error('you must login first to access this resource', 401);
            exit();
        }
    }

    public function get_payload() {
        $token = isset($this->CI->rpc->cookie[$this->cookie]) ? $this->CI->rpc->cookie[$this->cookie] : NULL;
        if (isset($token)) {
            $token = explode(':', $token);
            return json_decode(base64_decode($token[0]), TRUE);
        }
        return NULL;
    }

    public function set_payload($payload, $secret) {
        $token = isset($this->CI->rpc->cookie[$this->cookie]) ? $this->CI->rpc->cookie[$this->cookie] : NULL;
        if (isset($token)) {
            $token = explode(':', $token);
        } else {
            $token = [];
        }
        if (!isset($payload['__t'])) {
            $payload['__t'] = time();
        }
        $token[0] = base64_encode(json_encode($payload));
        $token[1] = hash_hmac('sha256', $token[0], $secret);
        $token = implode(':', $token);
        $this->CI->rpc->cookie[$this->cookie] = $token;
    }

}

?>
