<?php

class Rawatinap extends CI_Controller {

    function __construct() {
        parent::__construct();
        is_logged_in();
        $this->load->model('m_rawatinap');
        $this->load->library('session');
        $this->load->helper('login');
        $this->load->helper('functions');
        $this->load->library('form_validation');
        $this->load->helper('url');
        date_default_timezone_set('Asia/Jakarta');
        
    }

    function index() {
        $this->load->view('layout');
    }

    function billing_rawat_inap() {
        //$this->load->view('layout');
        $data['title'] = 'Billing Rawat Inap';
        $this->load->view('rawat_inap/billing_rawat_inap', $data);
    }

    function pasien_load_data() {
        $q = get_safe('q');
        $data = $this->m_rawatinap->data_pasien_muat_data($q);
        return die(json_encode($data));
    }

    function get_data_pasien($no_rm){
        $data = $this->m_rawatinap->get_data_pasien($no_rm);
        echo json_encode($data);
    }

    function get_data_rawatinap($no_daftar) {
        $this->load->model('m_referensi');
        $data['bed'] = $this->m_rawatinap->get_data_rawatinap($no_daftar);
        $data['no_daftar'] = $no_daftar;
        $kelas = $this->m_referensi->kelas_tarif_get_data();
        $data['kelas'] = '';
        foreach ($kelas as $key => $value) {
            $data['kelas'] .= '<option value="'.$key.'">'.$value.'</option>';     
        }
        $this->load->view('rawat_inap/unit_list', $data);
    }
    
    function get_data_tarif_sewa_kamar($unit, $kelas) {
        $data = $this->m_rawatinap->get_data_tarif_sewa_kamar($unit, $kelas)->row();
        die(json_encode($data));
    }

    function get_data_unit() {
        $q = get_safe('q');
        $data = $this->m_rawatinap->data_unit_muat_data($q);
        return die(json_encode($data));
    }

    function get_data_bed($unit, $kelas) {
        $q = array(
            'unit' => $unit,
            'kelas' => $kelas
        );

        $data = $this->m_rawatinap->data_bed_muat_data($q);
        return die(json_encode($data));
    }

    function save_rawatinap() {
        $no_daftar = post_safe('no_daftar');
        // data lama
        $id = post_safe('id');
        $id_pk = post_safe('id_pk');
        $out = post_safe('out');
        $masuk = post_safe('masuk');
        
        if ($id != null) {
            $update = array(
                'id' => $id, //array
                'id_pk' => $id_pk, //array
                'out_time' => $out, //array
                'in_time' => $masuk,
                'no_daftar' => $no_daftar
            );
            $this->m_rawatinap->update_bed_data($update);
        }

        $this->get_data_rawatinap($no_daftar);
    }

    function delete_data_bed($id) {
        $no_daftar = get_safe('no_daftar');
        $result = $this->m_rawatinap->delete_bed_data($id);
        die(json_encode(array('no_daftar'=>$no_daftar, 'result' => $result)));
    }

}

?>