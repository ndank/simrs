<?php

class User extends CI_Controller {

    function __construct() {
        parent::__construct();
        //is_logged_in();
        $this->load->helper('url');
        $this->load->library('session');
        $this->load->model('m_user');
        $this->load->model('configuration');
        $this->load->helper('login');
        $this->load->model('m_rawatinap');
        $user = $this->session->userdata('user');

        if ($user != '') {
            $data = $this->menu_user_load_data();
            $this->load->view('layout', $data);
        }
        date_default_timezone_set('Asia/Jakarta');
    }

    public function menu_user_load_data() {
        $id_group = $this->session->userdata('id_group');
        $active = $this->session->userdata('active_modul');
        if($active != NULL){
            $data['detail_menu'] = $this->m_user->menu_user_load_data($id_group, $active)->result();
        }
        
        $data['master_menu'] = $this->m_user->module_load_data($id_group)->result();
        return $data;
    }

    function index() {
        $data['title'] = 'Home Sistem Informasi Rumah Sakit';
        $user = $this->session->userdata('user');
        
        
        
        if ($user == '') {
            $this->load->view('login', $data);
        }
        //$this->is_login();
    }

    function login() {
        $jml = $this->m_user->cek_login();
        if (isset($jml->username) and $jml->username != '') {
            $data = array(
                'id_user' => $jml->id, 
                'user' => $jml->username, 
                'pass' => $jml->password, 
                'nama' => $jml->nama, 
                'id_unit' => $jml->unit_id, 
                'unit' => $jml->unit, 
                'status' => $jml->status,
                'jenis' => $jml->jenis,
                'active_modul' => NULL,
                'id_group'=> $jml->id_group,
                'id_pegawai' => $jml->id_pegawai
            );
            $this->session->set_userdata($data);

            die(json_encode(array('status'=>'login')));
        } else {
            die(json_encode(array('status'=>'gagal')));
        }
    }

    public function is_login() {
        
    }

    function logout() {
        $this->session->sess_destroy();
        redirect(base_url());
    }

    

}

?>