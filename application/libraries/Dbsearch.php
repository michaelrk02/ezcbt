<?php

class Dbsearch {

    private $CI;

    public function __construct() {
        $this->CI =& get_instance();
    }

    public function search($table, $criterion, $columns, $match_param = 'match', $page_param = 'page', $items_param = 'items') {
        $match = isset($_GET[$match_param]) ? $_GET[$match_param] : '';
        $page = isset($_GET[$page_param]) ? $_GET[$page_param] : 1;
        $items = isset($_GET[$items_param]) ? $_GET[$items_param] : 15;

        $result = [];

        $this->CI->load->database();
        $this->CI->db->select($columns)->from($table);
        $this->CI->db->order_by($criterion);
        if (!empty($match)) {
            $this->CI->db->like($criterion, $match);
        }
        $max_items = $this->CI->db->count_all_results('', FALSE);

        if ($items > 0) {
            $result['max_page'] = max(ceil($max_items / $items), 1);
        } else {
            $result['max_page'] = 1;
        }

        if ((1 <= $page) && ($page <= $result['max_page']) && ($items > 0)) {
            $this->CI->db->limit($items, ($page - 1) * $items);
            $result['data'] = $this->CI->db->get()->result_array();
        } else {
            $result['data'] = [];
        }

        $_GET['match'] = $match;
        $_GET['page'] = $page;
        $_GET['items'] = $items;

        return $result;
    }

}

?>
