<?php

class Display extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('unit_layanan');
        $this->load->model('m_display');
        date_default_timezone_set('Asia/Jakarta');
    }

    function antrian_poli(){
        $data['title'] = "Papan Antrian Poliklinik";
        $this->load->view('display/antri-poli', $data);
    }

    function reload_antrian(){
        $data['unit'] = $this->m_display->reload_antrian();

        $this->load->view('display/jenis_layanan', $data);
    }

    function submit_error(){
        $data = array(
            'waktu' => date('Y-m-d H:i:s'),
            'menu' => $this->input->post('menu'),
            'status' => $this->input->post('status'),
            'url' => $this->input->post('url'),
            'response' => $this->input->post('response')
        );

        $this->db->insert('error_log', $data);      
    }

    function error($page=1){
        $data = $this->get_error_log_list($page);
        $data['title'] = "Error Log";
        $this->load->view('display/error',$data);
    }

    function get_error_log_list($page) {
        $limit = 15;
        if ($page == 'undefined') {
            $page = 1;
        }
        $start = ($page - 1) * $limit;
        $data['page'] = $page;
        $data['limit'] = $limit;
        $query = $this->m_display->error_get_data($limit, $start);
        $data['jumlah'] = $query['jumlah'];
        $data['list'] = $query['data'];
        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, 1, '');
        return $data;
    }

    function delete_error($id, $page){
        $this->db->where('id', $id)->delete('error_log');
        $data = $this->get_error_log_list($page);
        if ($data['list'] == null) {
            $data = $this->get_error_log_list($page-1);
        }
        $data['title'] = "Error Log";
        $this->load->view('display/error',$data);
    }

    function delete_error_all(){
        $this->db->query('delete from error_log');
        $data = $this->get_error_log_list(1);
        $data['title'] = "Error Log";
        $this->load->view('display/error',$data);
    }

}

?>