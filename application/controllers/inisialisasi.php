<?php

class Inisialisasi extends CI_Controller {
    
    function __construct() {
        parent::__construct();
        $this->load->model('configuration');
        date_default_timezone_set('Asia/Jakarta');
        is_logged_in();
    }
    
    function reset() {
        $data['title'] = 'Reset Data';
        $this->load->view('reset', $data);
    }
    
    function delete_data() {
        $data = $this->configuration->reset_data();
        die(json_encode(array('status' => true)));
    }

    function depan(){
        $this->load->view('registrasi');
    }

    function set_active_module($id){
        $this->session->set_userdata('active_modul', $id);
    }
}
?>