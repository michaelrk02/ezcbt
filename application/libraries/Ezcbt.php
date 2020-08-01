<?php

class Ezcbt {

    private $CI;

    public function __construct() {
        $this->CI =& get_instance();

        date_default_timezone_set('UTC');
    }

    public function success($message) {
        return $this->set_status('success', $message);
    }

    public function warning($message) {
        return $this->set_status('warning', $message);
    }

    public function error($message) {
        return $this->set_status('error', $message);
    }

    public function set_status($type, $message) {
        $this->CI->load->library('session');

        $_SESSION['ezcbt_status'] = ['type' => $type, 'message' => $message];
    }

    public function status($unset = TRUE) {
        $this->CI->load->library('session');

        if (isset($_SESSION['ezcbt_status'])) {
            $status = $_SESSION['ezcbt_status'];
            if ($unset) {
                unset($_SESSION['ezcbt_status']);
            }

            $str = '';
            $str .= '<div id="__ezcbt_Status" class="toast toast-'.$status['type'].'" style="margin-bottom: 0.5rem">';
            $str .= ' <button type="button" class="btn btn-clear float-right" id="__ezcbt_CloseStatus"></button>';
            $str .= ' '.$status['message'];
            $str .= '</div>';
            $str .= '<script>';
            $str .= ' $(document).ready(function() { $("#__ezcbt_CloseStatus").click(function() { $("#__ezcbt_Status").remove(); }); });';
            $str .= '</script>';

            return $str;
        }
        return '<div></div>';
    }

    public function course_pdf_url($id) {
        $payload = ['course_id' => $id, '__t' => time()];
        $token = [];
        $token[0] = base64_encode(json_encode($payload));
        $token[1] = hash_hmac('sha256', $token[0], SERVER_SECRET);
        $token = implode(':', $token);

        return base_url('public/pdfjs/web/viewer.html').'?file='.urlencode(site_url('content/course_pdf').'?token='.urlencode($token));
    }

}

?>
