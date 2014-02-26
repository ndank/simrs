<?php

class Kepegawaian extends CI_Controller {

    public $limit = null;

    function __construct() {
        parent::__construct();
        //is_logged_in();
        $this->limit = 15;
        $this->load->helper('url');
        $this->load->model('m_kepegawaian');
        date_default_timezone_set('Asia/Jakarta');
        
    }

    function get_last_id($tabel, $id) {
        return die(json_encode(array('last_id' => get_last_id($tabel, $id))));
    }

    /* Jenis Jurusan Kualifikasi Pendidikan */

    function jenis_kualifikasi() {
        $data['title'] = "Jenis Jurusan Kualifikasi Pendidikan";
        $this->load->view('kepegawaian/jenis_jurusan', $data);
    }

    function get_jenis_list($page, $search) {
        if ($page == 'undefined') {
            $page = 1;
        }
        $str = 'null';
        $start = ($page - 1) * $this->limit;
        $data['page'] = $page;
        $data['limit'] = $this->limit;

        $query = $this->m_kepegawaian->jenis_get_data($this->limit, $start, $search);
        $data['jenis'] = $query['data'];
        $data['jumlah'] = $query['jumlah'];

        $data['paging'] = paging_ajax($data['jumlah'], $this->limit, $page, 1, '');
        return $data;
    }

    function manage_jenis($mode, $page) {
        $jenis = array(
            'id' => post_safe('id'),
            'nama' => post_safe('nama'),
            'nakes' => post_safe('nakes'),
        );
        $searchnull = 'null';
        switch ($mode) {
            case 'list':  
                $search['nama'] = get_safe('nama');
                $search['nakes'] = isset($_GET['nakes'])?get_safe('nakes'):'';
                $data = $this->get_jenis_list($page, $search);

                $data = array_merge($data, $search);               
                $this->load->view('kepegawaian/jenis_jurusan_list', $data);
                break;

            case 'post':
                // untuk add or edit
                if ($jenis['id'] == '') {
                    //add
                    $search['id'] = $this->m_kepegawaian->jenis_add_data($jenis);
                    $data = $this->get_jenis_list($page, $search);
                    $this->load->view('kepegawaian/jenis_jurusan_list', $data);
                } else {
                    $search['id'] = $jenis['id'];
                    $this->m_kepegawaian->jenis_edit_data($jenis);
                    $data = $this->get_jenis_list($page, $search);
                    $this->load->view('kepegawaian/jenis_jurusan_list', $data);
                }
                break;

            case 'delete':
                $id = get_safe('id');
                $this->m_kepegawaian->jenis_delete_data($id);
                break;

            case 'cek':
                $cek = array(
                    'nama' => post_safe('nama'),
                    'nakes' => post_safe('nakes')
                );
                $data = $this->m_kepegawaian->jenis_cek_data($cek);
                die(json_encode(array('status' => $data)));

                break;

            default:
                break;
        }
    }

    function get_jenis() {
        $q = get_safe('q');
        $data = $this->m_kepegawaian->load_data_jenis($q)->result();
        die(json_encode($data));
    }

    /* Jenis Jurusan Kualifikasi Pendidikan */

    /* Kualifikasi Pendidikan */

    function kualifikasi_pendidikan() {
        $data['title'] = "Kualifikasi Pendidikan";
        $this->load->view('kepegawaian/kualifikasi_pendidikan', $data);
    }

    function get_pendidikan_list($page, $search) {
        if ($page == 'undefined') {
            $page = 1;
        }
        $str = 'null';
        $start = ($page - 1) * $this->limit;
        $data['page'] = $page;
        $data['limit'] = $this->limit;

        $query = $this->m_kepegawaian->pendidikan_get_data($this->limit, $start, $search);
        $data['pendidikan'] = $query['data'];
        $data['jumlah'] = $query['jumlah'];

        $data['paging'] = paging_ajax($data['jumlah'], $this->limit, $page, 1, '');
        return $data;
    }

    function manage_pendidikan($mode, $page) {
        $pendidikan = array(
            'id' => post_safe('id'),
            'nama' => post_safe('nama'),
        );
        $searchnull = 'null';
        switch ($mode) {
            case 'list':                
                $search['nama'] = get_safe('nama');
                $data = $this->get_pendidikan_list($page, $search);
                $data = array_merge($data, $search);                   
                $this->load->view('kepegawaian/kualifikasi_pendidikan_list', $data);
                break;

            case 'post':
                // untuk add or edit
                if ($pendidikan['id'] == '') {
                    //add
                    $search['id'] = $this->m_kepegawaian->pendidikan_add_data($pendidikan);
                    $data = $this->get_pendidikan_list($page, $search);
                    $this->load->view('kepegawaian/kualifikasi_pendidikan_list', $data);
                } else {
                    $search['id'] = $pendidikan['id'];
                    $this->m_kepegawaian->pendidikan_edit_data($pendidikan);
                    $data = $this->get_pendidikan_list($page, $search);
                    $this->load->view('kepegawaian/kualifikasi_pendidikan_list', $data);
                }
                break;

            case 'delete':
                $id = get_safe('id');
                $this->m_kepegawaian->pendidikan_delete_data($id);
                break;

            case 'cek':
                $cek = array(
                    'nama' => post_safe('nama')
                );
                $data = $this->m_kepegawaian->pendidikan_cek_data($cek);
                die(json_encode(array('status' => $data)));

                break;

            default:
                break;
        }
    }

    /* Kualifikasi Pendidikan */


    /* Jurusan Kualifikasi Pendidikan */

    function jurusan() {
        $data['title'] = "Jurusan Kualifikasi Pendidikan";
        $this->load->view('kepegawaian/jurusan', $data);
    }

    function get_jurusan_list($page, $search) {
        if ($page == 'undefined') {
            $page = 1;
        }
        $start = ($page - 1) * $this->limit;
        $data['page'] = $page;
        $data['limit'] = $this->limit;

        $query = $this->m_kepegawaian->jurusan_get_data($this->limit, $start, $search);
        $data['jurusan'] = $query['data'];
        $data['jumlah'] = $query['jumlah'];

        $data['paging'] = paging_ajax($data['jumlah'], $this->limit, $page, 1, '');
        return $data;
    }

    function manage_jurusan($mode, $page) {
        $jurusan = array(
            'id' => post_safe('id'),
            'nama' => post_safe('nama'),
            'titel' => post_safe('titel'),
            'id_jenis_jurusan_kualifikasi_pendidikan' => (post_safe('id_jenis') != '') ? post_safe('id_jenis') : 'NULL',
            'admission' => isset($_POST['admission'])?post_safe('admission'):'Tidak'
        );
        $searchnull = 'null';
        switch ($mode) {
            case 'list':
                $search['nama'] = get_safe('nama');
                $search['jenis'] = get_safe('id_jenis');
                $search['nm_jenis'] = get_safe('jenis');
                $data = $this->get_jurusan_list($page, $search);
                $data = array_merge($data, $search);
                    
                $this->load->view('kepegawaian/jurusan_list', $data);
                break;

            case 'post':
                // untuk add or edit
                if ($jurusan['id'] == '') {
                    //add
                    $search['id'] = $this->m_kepegawaian->jurusan_add_data($jurusan);
                    $data = $this->get_jurusan_list($page, $search);
                    $this->load->view('kepegawaian/jurusan_list', $data);
                } else {
                    $search['id'] = $jurusan['id'];
                    $this->m_kepegawaian->jurusan_edit_data($jurusan);
                    $data = $this->get_jurusan_list($page, $search);
                    $this->load->view('kepegawaian/jurusan_list', $data);
                }
                break;

            case 'delete':
                $id = get_safe('id');
                $this->m_kepegawaian->jurusan_delete_data($id);
                break;


            case 'cek':
                $cek = array(
                    'nama' => post_safe('nama'),
                    'jenis' => post_safe('id_jenis')
                );
                $data = $this->m_kepegawaian->jurusan_cek_data($cek);
                die(json_encode(array('status' => $data)));

                break;

            default:
                break;
        }
    }

  
    /* Jurusan Kualifikasi Pendidikan */


    /* Kepegawaian */

    function pegawai() {
        $data['title'] = "Kepegawaian";
        $data['jabatan'] = $this->m_kepegawaian->get_jabatan();
        $data['pendidikan'] = $this->m_kepegawaian->get_jenjang_pendidikan();
        $this->load->view('kepegawaian/pegawai', $data);
    }

    function get_jurusan() {
        $q = get_safe('q');
        $data = $this->m_kepegawaian->load_jurusan($q)->result();
        die(json_encode($data));
    }

    function get_pegawai_list($page, $search) {
        if ($page == 'undefined') {
            $page = 1;
        }
        $str = 'null';
        $start = ($page - 1) * $this->limit;
        $data['page'] = $page;
        $data['limit'] = $this->limit;

        $query = $this->m_kepegawaian->pegawai_get_data($this->limit, $start, $search);
        $data['pegawai'] = $query['data'];
        $data['jumlah'] = $query['jumlah'];

        $data['paging'] = paging_ajax($data['jumlah'], $this->limit, $page, 1, '');
        return $data;
    }

    function manage_pegawai($mode, $page) {
        $searchnull = 'null';
        switch ($mode) {
            case 'list':
              
                $search['gender'] = isset($_GET['gender'])?get_safe('gender'):'';                   

                if ((get_safe('fromdate') != '') & (get_safe('todate') != '')) {
                    $search['fromdate'] = date2mysql(get_safe('fromdate'));
                    $search['todate'] = date2mysql(get_safe('todate'));
                }

                $search['jenjang'] = get_safe('jenjang');
                $search['jurusan'] = get_safe('id_jurusan');
                $search['nm_jurusan'] = get_safe('jurusan');
                
                $data = $this->get_pegawai_list($page, $search);
            
                $data['pendidikan'] = $this->m_kepegawaian->get_jenjang_pendidikan();
                $this->load->view('kepegawaian/pegawai_list', array_merge($data, $search));
                break;

            case 'post':
                $pegawai = array(
                    'id' => post_safe('id_baru'),
                    'waktu' => datetime2mysql(post_safe('waktu')),
                    'nip' => post_safe('nip'),
                    'penduduk_id' => post_safe('id_penduduk'),
                    'id_kualifikasi_pendidikan' => (post_safe('jenjang_baru')!="")?post_safe('jenjang_baru'):NULL,
                    'id_jurusan_kualifikasi_pendidikan' => (post_safe('id_jurusan_baru')!='')?post_safe('id_jurusan_baru'):NULL,
                    'jabatan' => post_safe('jabatan')
                );


                // untuk add or edit
                if ($pegawai['id'] == '') {
                    //add

                    if (post_safe('gender') == 'P') {
                        $pegawai['jumlah_kebutuhan_per_jenjang_pendidikan_pria'] = post_safe('jumlah');
                    } else {
                        $pegawai['jumlah_kebutuhan_per_jenjang_pendidikan_wanita'] = post_safe('jumlah');
                    }
                    $search['id'] = $this->m_kepegawaian->pegawai_add_data($pegawai);
                    $data = $this->get_pegawai_list($page, $search);
                    $this->load->view('kepegawaian/pegawai_list', $data);
                } else {
                    if (post_safe('gender') == 'L') {
                        $pegawai['jumlah_kebutuhan_per_jenjang_pendidikan_pria'] = post_safe('jumlah');
                    } else {
                        $pegawai['jumlah_kebutuhan_per_jenjang_pendidikan_wanita'] = post_safe('jumlah');
                    }
                    $search['id'] = $pegawai['id'];
                    $this->m_kepegawaian->pegawai_edit_data($pegawai);
                    $data = $this->get_pegawai_list($page, $search);
                    $this->load->view('kepegawaian/pegawai_list', $data);
                }
                break;

            case 'delete':
                $id = get_safe('id');
                $this->m_kepegawaian->pegawai_delete_data($id);
                break;

            case 'cek':
                $cek = array(
                    'nama' => post_safe('nama'),
                );
                $data = $this->m_kepegawaian->pegawai_cek_data($cek);
                die(json_encode(array('status' => $data)));

                break;

            default:
                break;
        }
    }

    /* Kepegawaian */

    function cetak_rl2() {
        $this->load->model("m_registrasi_rs");
        $data['title'] = "Formulir RL 2<br/>Ketenagaan";
        $data['rs'] = $this->m_registrasi_rs->get_last_register_data(date('Y'));

        $data['jenis'] = $this->m_kepegawaian->jenis_get_data(null, null, "null");
        $data['pegawai'] = $this->m_kepegawaian->pegawai_get_data(null,null,"null", true);
        $this->load->view('rl/rl2_ketenagaan', $data);
    }

}

?>