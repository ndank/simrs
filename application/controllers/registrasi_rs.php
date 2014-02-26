<?php

class Registrasi_rs extends CI_Controller {

    public $limit = null;

    function __construct() {
        parent::__construct();
        //is_logged_in();
        $this->limit = 15;
        $this->load->helper('url');
        $this->load->model('m_registrasi_rs');
        date_default_timezone_set('Asia/Jakarta');
        
    }

    function get_last_id($tabel, $id) {
        return die(json_encode(array('last_id' => get_last_id($tabel, $id))));
    }

    /* Jenis RS */

    function jenis() {
        $data['title'] = "Jenis Rumah Sakit";
        $this->load->view('registrasi/jenis', $data);
    }

    function get_jenis_list($page, $search) {
        if ($page == 'undefined') {
            $page = 1;
        }
        $str = 'null';
        $start = ($page - 1) * $this->limit;
        $data['page'] = $page;
        $data['limit'] = $this->limit;

        $query = $this->m_registrasi_rs->jenis_get_data($this->limit, $start, $search);
        $data['jenis'] = $query['data'];
        $data['jumlah'] = $query['jumlah'];


        $data['paging'] = paging_ajax($data['jumlah'], $this->limit, $page, 1, $str);
        return $data;
    }

    function manage_jenis($mode, $page) {
        $jenis = array(
            'id' => post_safe('id'),
            'nama' => post_safe('nama'),
            'keterangan' => post_safe('ket')
        );
        $searchnull = 'null';
        switch ($mode) {
            case 'list':
                $data = $this->get_jenis_list($page, $searchnull);
                $this->load->view('registrasi/jenis_list', $data);
                break;

            case 'post':
                // untuk add or edit
                if ($jenis['id'] == '') {
                    //add
                    $search['id'] = $this->m_registrasi_rs->jenis_add_data($jenis);
                    $data = $this->get_jenis_list($page, $search);
                } else {
                    $search['id'] = $jenis['id'];
                    $this->m_registrasi_rs->jenis_edit_data($jenis);
                    $data = $this->get_jenis_list($page, $search);
                }
                $this->load->view('registrasi/jenis_list', $data);
                break;

            case 'delete':
                $id = get_safe('id');
                $this->m_registrasi_rs->jenis_delete_data($id);
                $data = $this->get_jenis_list($page, $searchnull);
                if ($data['jenis'] == null) {
                    $data = $this->get_jenis_list(1, $searchnull);
                }
                $this->load->view('registrasi/jenis_list', $data);

                break;



            case 'cek':
                $cek = array(
                    'nama' => post_safe('nama'),
                );
                $data = $this->m_registrasi_rs->jenis_cek_data($cek);
                die(json_encode(array('status' => $data)));

                break;

            default:
                break;
        }
    }

    /* Jenis RS */


    /* Registrasi RS */

    function register() {
        $data['title'] = "Registrasi Identitas Rumah Sakit";
        $data['jenis'] = $this->m_registrasi_rs->get_jenis_reg();
        $data['kelas'] = $this->m_registrasi_rs->get_kelas();
        $data['sifat'] = $this->m_registrasi_rs->get_sifat_penetapan();
        $data['status'] = $this->m_registrasi_rs->get_status_penyelenggara_swasta();
        $data['jenisrs'] = $this->m_registrasi_rs->get_jenis_rs();
        $this->load->view('registrasi/register', $data);
    }

    function get_registrasi_rs($id){
        $data = $this->m_registrasi_rs->get_registrasi_rs($id);
        die(json_encode($data));
    }

    function get_register_list($page, $search) {
        if ($page == 'undefined') {
            $page = 1;
        }
        $str = 'null';
        $start = ($page - 1) * $this->limit;
        $data['page'] = $page;
        $data['limit'] = $this->limit;

        $query = $this->m_registrasi_rs->register_get_data($this->limit, $start, $search);
        $data['register'] = $query['data'];
        $data['jumlah'] = $query['jumlah'];


        $data['paging'] = paging_ajax($data['jumlah'], $this->limit, $page, 1, $str);
        return $data;
    }

    function manage_register($mode, $page) {

        $searchnull = 'null';
        switch ($mode) {
            case 'list':
                $data = $this->get_register_list($page, $searchnull);
                $this->load->view('registrasi/register_list', $data);
                break;

            case 'post':
                $register = array(
                    'id' => post_safe('id'),
                    'waktu' => datetime2mysql(post_safe('wkt_reg')),
                    'jenis' => post_safe('jenis'),
                    'kode_rs' => post_safe('kode'),
                    'nama' => post_safe('nama'),
                    'id_jenis_rs' => post_safe('jenis_rs'),
                    'kelas' => post_safe('kelas'),
                    'id_kepegawaian_direktur' => post_safe('id_direktur'),
                    'id_instansi_relasi_penyelenggara' => post_safe('id_penyelenggara'),
                    'alamat_jalan' => post_safe('alamat_jln'),
                    'id_kelurahan' => post_safe('id_kelurahan'),
                    'telp_no' => post_safe('telp_no'),
                    'extension_telp_no' => post_safe('ex_telp_no'),
                    'fax_no' => post_safe('fax'),
                    'extension_fax_no' => post_safe('ex_fax_no'),
                    'alamat_email' => post_safe('email'),
                    'url_website' => post_safe('website'),
                    'luas_tanah' => post_safe('tanah'),
                    'luas_bangunan' => post_safe('bangunan'),
                    'no_surat_izin_penetapan' => post_safe('no_surat'),
                    'tanggal_surat_izin_penetapan' => date2mysql(post_safe('tgl')),
                    'id_instansi_relasi_penetap' => post_safe('id_penetap'),
                    'sifat_penetapan' => post_safe('sifat'),
                    'tanggal_batas_masa_berlaku' => date2mysql(post_safe('masa')),
                    'status_penyelenggara_swasta' => post_safe('sps'),
                    'tanggal_akreditasi' => date2mysql(post_safe('tgl_ak')),
                    'total_nilai_akreditasi' => post_safe('total')
                );
                // untuk add or edit
                if ($register['id'] == '') {
                    //add
                    $search['id'] = $this->m_registrasi_rs->register_add_data($register);
                    $data = $this->get_register_list($page, $search);
                } else {
                    $search['id'] = $register['id'];
                    
                    $this->m_registrasi_rs->register_edit_data($register);
                    $data = $this->get_register_list($page, $search);
                }
                $this->load->view('registrasi/register_list', $data);
                break;

            case 'delete':
                $id = get_safe('id');
                $this->m_registrasi_rs->register_delete_data($id);
                $data = $this->get_register_list($page, $searchnull);
                if ($data['register'] == null) {
                    $data = $this->get_register_list(1, $searchnull);
                }
                $this->load->view('registrasi/register_list', $data);

                break;



            default:
                break;
        }
    }

    function load_data_pegawai() {
        $q = get_safe('q');
        $data = $this->m_registrasi_rs->load_data_pegawai($q)->result();
        die(json_encode($data));
    }

    /* Registrasi RS */

    
}

?>