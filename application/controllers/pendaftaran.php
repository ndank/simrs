<?php

class Pendaftaran extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->database();
        
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->helper('login');
        $this->load->model(array('m_pendaftaran','unit_layanan','configuration','m_demografi'));
        date_default_timezone_set('Asia/Jakarta');
        is_logged_in();
        
    }

    function is_login() {
        $user = $this->session->userdata('id_user');
        if ($user != '') {
            
        } else {
            //redirect(base_url());
        }
    }

    function index() {

        $this->index_get();
    }

    function index_get() {
        $data['title'] = "Pendaftaran";
        $this->load->view('pendaftaran/index', $data);
    }

    function list_pendaftar() {
        $data['title'] = 'Rekap Pelayanan Kunjungan';
        $data['layanan'] = $this->m_pendaftaran->get_layanan_admission('igd'); 
        $this->load->view('pendaftaran/list', $data);
    }

    function list_data_pendaftar($page = null) {
        $data['sub_title'] = "<b>KUNJUNGAN</b><br/>";
        $param['from'] = get_safe('fromdate');
        $param['to'] = get_safe('todate');
        $param['nama'] = get_safe('nama');
        $param['alamat'] = get_safe('alamat');
        $param['layanan'] = get_safe('id_layanan');

        if ($param['from'] != '') {
            $data['sub_title'] .= strtoupper(indo_tgl(date2mysql($param['from'])) . " - " . indo_tgl(date2mysql($param['to']))) . "<br/>";
        } else {
            $data['sub_title'] .= "Semua Kunjungan</h2>";
        }


        $limit = 10;
        if ($page == 'undefined') {
            $page = 1;
        }
        $start = ($page - 1) * $limit;
        $search = 'null';
        $data['page'] = $page;
        $data['limit'] = $limit;
        $query = $this->m_pendaftaran->get_pendaftar($limit, $start, $param);
        $data['jumlah'] = $query['jumlah'];
        $data['hasil'] = $query['data'];
        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, 1, $search);
        $this->load->view('pendaftaran/list_data', $data);
    }
    
    function list_data_pendaftar_poliklinik($page = null) {
        $data['sub_title'] = "<b>Daftar Kunjungan Pasien Rumah Sakit</b><br/>";
        $param['from'] = get_safe('fromdate');
        $param['to'] = get_safe('todate');
        $param['nama'] = get_safe('nama');
        $param['alamat'] = get_safe('alamat');
        $param['layanan'] = get_safe('id_layanan');

        if ($param['from'] != '') {
            $data['sub_title'] .= indo_tgl(date2mysql($param['from'])) . " s.d " . indo_tgl(date2mysql($param['to'])) . "<br/>";
        } else {
            $data['sub_title'] .= "Semua Kunjungan</h2>";
        }


        $limit = 10;
        if ($page == 'undefined') {
            $page = 1;
        }
        $start = ($page - 1) * $limit;
        $search = 'null';
        $data['page'] = $page;
        $data['limit'] = $limit;
        $query = $this->m_pendaftaran->get_pendaftar_pemeriksaan($limit, $start, $param);
        $data['jumlah'] = $query['jumlah'];
        $data['hasil'] = $query['data'];
        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, 1, $search);
        $this->load->view('pendaftaran/list_data_kunjungan', $data);
    }
    
    function list_data_pendaftar_ranap($page = null) {
        $data['sub_title'] = "<b>Daftar Kunjungan Pasien Rumah Sakit</b><br/>";
        $param['from'] = get_safe('fromdate');
        $param['to'] = get_safe('todate');
        $param['nama'] = get_safe('nama');
        $param['alamat'] = get_safe('alamat');
        $param['layanan'] = get_safe('id_layanan');

        if ($param['from'] != '') {
            $data['sub_title'] .= indo_tgl(date2mysql($param['from'])) . " s.d " . indo_tgl(date2mysql($param['to'])) . "<br/>";
        } else {
            $data['sub_title'] .= "Semua Kunjungan</h2>";
        }


        $limit = 10;
        if ($page == 'undefined') {
            $page = 1;
        }
        $start = ($page - 1) * $limit;
        $search = 'null';
        $data['page'] = $page;
        $data['limit'] = $limit;
        $query = $this->m_pendaftaran->get_pendaftar_pemeriksaan_ranap($limit, $start, $param);
        $data['jumlah'] = $query['jumlah'];
        $data['hasil'] = $query['data'];
        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, 1, $search);
        $this->load->view('pendaftaran/list_data_ranap', $data);
    }

    function pencarian() {
        $this->load->view('layout');

        $data['title'] = "Pencarian Pasien";
        $this->load->view('pencarian', $data);
    }

    function kunjungan(){
        $data['title'] = "Antrian Pendaftaran Layanan Poliklinik";

        $data['kelamin'] = $this->m_demografi->kelamin();
        $data['keb_rawat'] = $this->m_pendaftaran->keb_rawat();
        $data['jenis_rawat'] = $this->m_pendaftaran->jenis_rawat();
        $data['jenis_layan'] = $this->m_pendaftaran->jenis_layan();
        $data['krit_layan'] = $this->m_pendaftaran->krit_layan();
        $data['darah'] = $this->m_demografi->gol_darah();
        $data['agama'] = $this->m_demografi->agama();
        $data['pendidikan'] = $this->m_demografi->pendidikan();
        $data['pernikahan'] = $this->m_demografi->stat_nikah();
        $data['pekerjaan'] = $this->m_demografi->pekerjaan();
        $data['tgl_lahir'] = $this->m_demografi->usia();
        $data['alasan_datang'] = $this->m_pendaftaran->alasan_datang();
        $data['layanan'] = $this->m_pendaftaran->get_layanan_admission(); 

        if (isset($_GET['no_rm'])) {
            $data['no_rm'] = get_safe('no_rm');
            $pasien = $this->m_demografi->get_by_no_rm($data['no_rm']);
            foreach ($pasien as  $value) {
                $data['id_penduduk'] = $value->id;
                $data['nama'] = $value->nama;
                $data['gender'] = array($value->gender);
                $data['lahir_tanggal'] = datefmysql($value->lahir_tanggal);
                $data['alamat'] = $value->alamat;
                $data['gol_darah'] = $value->darah_gol;
                $data['pendidikan_id'] = $value->pendidikan_id;
                $data['pekerjaan_id'] = $value->pekerjaan_id;
                $data['profesi_id'] = $value->profesi_id;
                $data['pernikahan_id'] = $value->pernikahan;
                $data['agama_id'] = $value->agama;
                $data['lahir_tempat'] = $value->tempat_lahir;
                $data['lahir_kabupaten_id'] = $value->lahir_kabupaten_id;
                $data['kelurahan_id'] = $value->kelurahan_id;
                $data['kelurahan'] = $value->kelurahan;
                $data['telp'] = $value->telp;
            }
        }

        $this->load->view('pendaftaran/kunjungan', $data);
    }

    function new_pasien($no_rm, $id_antri) {
        $antri = $this->m_pendaftaran->antrian_get_data($id_antri);

        $data['id_dokter'] = $antri->id_kepegawaian;
        $data['dokter'] = $antri->nama_pegawai;
        $data['layanan'] = $antri->nama_layanan;
        $data['layanan_id'] = $antri->id_jurusan_kualifikasi_pendidikan;
        $data['no_antri'] = $antri->no_antri;
        $data['tgl_layan'] = datefmysql($antri->tanggal);

        $data['id_antri'] = $id_antri;

        $data['alasan_datang'] = $this->m_pendaftaran->alasan_datang();

        $this->load->model('m_demografi');
        $data['pasien'] = $this->m_demografi->get_by_no_rm($no_rm);
        $data['title'] = "Konfirmasi Antrian Kunjungan Pasien (Phone)";
        $data['keb_rawat'] = $this->m_pendaftaran->keb_rawat();
        $data['jenis_rawat'] = $this->m_pendaftaran->jenis_rawat();
        $data['jenis_layan'] = $this->m_pendaftaran->jenis_layan();
        $data['krit_layan'] = $this->m_pendaftaran->krit_layan();
        $this->load->view('pendaftaran/new', $data);
    }

    function discharge($no_daftar){
        // cek pelayanan rawat inap, harus di discharge dulu
        // cek pembayaran, harus dilunasi dulu
        $inap = $this->m_pendaftaran->cek_pelayanan_rawat_inap($no_daftar);
        $bayar = $this->m_pendaftaran->cek_pelunasan_pembayaran($no_daftar);

        if ($inap) {
            $discharge = 'inap';
        }else if (!$bayar) {
            $discharge = 'bayar';
        }else{
            $this->m_pendaftaran->edit_tindak_lanjut($no_daftar);
            $discharge = 'ya';
        }
        die(json_encode(array('status' => $discharge )));        
    }

    function cek_pendaftaran_terakhir($no_rm){
        // cek pelayanan rawat inap, harus di discharge dulu
        // cek pembayaran, harus dilunasi dulu
        $sql = "select max(no_daftar) as max_no_daftar
                 from pendaftaran where pasien = '$no_rm'";

        $no_daftar = $this->db->query($sql)->row()->max_no_daftar;

        $inap = $this->m_pendaftaran->cek_pelayanan_rawat_inap($no_daftar);
        $bayar = $this->m_pendaftaran->cek_pelunasan_pembayaran($no_daftar);

        if ($inap) {
            $discharge = 'inap';
        }else if (!$bayar) {
            $discharge = 'bayar';
        }else{
            $discharge = 'ya';
        }
        die(json_encode(array('status' => $discharge )));        
    }

    function get_unit_layanan() {
        $q = get_safe('q');
        $data = $this->unit_layanan->load_data_unit_layan($q)->result();
        die(json_encode($data));
    }

    function new_post() {
        $this->load->model('unit_layanan');
        $this->load->model('m_demografi');

        $data = $this->m_pendaftaran->create_and_save();
        $this->m_demografi->konfirmasi_antrian(post_safe("id_antri"));
        $this->m_demografi->add_kunjungan_pasien(post_safe('no_rm'));

        //insert biaya kunjungan
        $param['id_pk'] = $data['id_pk'];
        $param['no_daftar'] = $data['no_daftar'];
        $param['tarif_id'] = 2; // kunjungan pasien
        $param['id_debet'] = 231;
        $param['id_kredit'] = 99;
        $param['waktu'] = $data['waktu'];
        $param['frekuensi'] = 1;
        $this->m_pendaftaran->insert_biaya($param);
        redirect('pendaftaran/detail/' . $data['no_daftar']);
    }

    /* fungsi - fungsi untuk pencarian */

    function search($tab = null) {
        $this->load->view('layout');
        $data['title'] = "Konfirmasi Antrian Kunjungan Poliklinik (Phone)";
        $data['tab'] = $tab;
        $this->load->view('pendaftaran/search', $data);
    }

    function search_by_nama_get() {
        $this->load->view('pendaftaran/search-tab3');
    }

    function search_by_nama_post($page) {
        $limit = 10;
        $start = ($page - 1) * $limit;
        $search = 'null';
        $data['page'] = $page;
        $data['limit'] = $limit;
        $param = array(
            'tanggal' => (get_safe('tanggal') != '')?date2mysql(get_safe('tanggal')):null,
            'nama' => get_safe('nama'), 
            'no_rm' => null, 
            'layanan' => null, 
            'no_antri' => null,
            'alamat' => get_safe('alamat'),
            'id_kelurahan' => get_safe('id_kelurahan')
        );
        $query = $this->m_pendaftaran->get($limit, $start, $param);
        $data['pasien'] = $query['data'];
        $data['jumlah'] = $query['jumlah'];
        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, 1, $search);
        $this->load->view('pendaftaran/hasil_pencarian', $data);
    }

    function search_by_no_antri_get() {
        $pilih = array(''=>
            'Pilih');
        $data['layanan'] = $pilih+$this->unit_layanan->get_unit_layanan('nakes');
        $this->load->view('pendaftaran/search-tab1', $data);
    }

    function search_by_no_antri_post($page) {
        $limit = 10;
        $start = ($page - 1) * $limit;
        $search = 'null';
        $data['page'] = $page;
        $data['limit'] = $limit;
        $param = array(
            'tanggal' => (get_safe('tanggal2') != '')?date2mysql(get_safe('tanggal2')):null,
            'nama' => null, 
            'no_rm' => null, 
            'layanan' => get_safe('unit'), 
            'no_antri' => get_safe('antri'),
            'alamat' => null,
            'id_kelurahan' => null
        );
        $query = $this->m_pendaftaran->get($limit, $start, $param);
        $data['pasien'] = $query['data'];
        $data['jumlah'] = $query['jumlah'];
        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, 1, $search);
        $this->load->view('pendaftaran/hasil_pencarian', $data);
    }

    function search_by_no_rm_get() {
        $this->load->view('pendaftaran/search-tab2');
    }

    function search_by_no_rm_post() {
        $param = array(
            'tanggal' => (get_safe('tanggal3') != '')?date2mysql(get_safe('tanggal3')):null,
            'nama' => null, 
            'no_rm' => get_safe('no_rm'), 
            'layanan' => null, 
            'no_antri' => NULL,
            'alamat' => null,
            'id_kelurahan' => null
        );
        $query = $this->m_pendaftaran->get(1, 0, $param);
        $data['pasien'] = $query['data'];
        $data['jumlah'] = '';
        $data['paging'] = "";
        $this->load->view('pendaftaran/hasil_pencarian', $data);
    }


    /* fungsi - fungsi untuk pencarian */

    function detail($no_daftar) {
        $this->load->view('layout');
        $this->load->model(array('m_demografi', 'm_pelayanan'));
        $data['pasien'] = $this->m_pendaftaran->get_by_no_daftar($no_daftar);
        $data['pelayanan'] = $this->m_pelayanan->get_pelayanan_kunjungan_list($no_daftar);
        $data['layanan'] = $this->m_pendaftaran->get_layanan_admission('igd');
        $data['title'] = "Kunjungan Pasien";
        $this->load->view('pendaftaran/detail', $data);
    }

    function cetak_kartu_get($no_rm, $no_daftar,$id_pk, $unit) {
        $this->load->model('m_demografi');
        $data['title'] = "Cetak Kartu Pasien Nomor RM ";
        //ambil no rm berdasarkan no daftar
        $data['apt'] = $this->configuration->rumah_sakit_get_atribute()->row();
        $data['pasien'] = $this->m_pendaftaran->get_by_no_daftar($no_daftar);
        $param['no_daftar'] = $no_daftar;
        $param['id_pk'] = $id_pk;
        $param['tarif_id'] = 1; //cetak kartu
        $param['id_debet'] = 231;
        if($unit == 'igd'){
            $param['id_kredit'] = 105;
        }else{
            $param['id_kredit'] = 100;
        }
        $param['frekuensi'] = 1;
        $this->m_pendaftaran->insert_biaya($param);
        $this->m_demografi->add_is_cetak_kartu($no_rm);


        $this->load->view('demografi/card', $data);
    }


    function cetak_no_antri_get($id_pk) {
        $this->load->model('m_pelayanan');
        $data['title'] = 'Cetak Nomor Antrian';
        $data['antri'] = $this->m_pelayanan->pelayanan_kunjungan_get_data($id_pk);
        $data['pasien'] = $this->m_pendaftaran->get_by_no_daftar($data['antri']->id_kunjungan);
        $this->load->view('pendaftaran/cetak_no_antri', $data);
    }

    function cetak_no_antri($id_pk) {
        $this->load->model('m_pelayanan');
        $data['title'] = 'Cetak Nomor Antrian';
        $data['antri'] = $this->m_pelayanan->pelayanan_kunjungan_get_data($id_pk);
        $data['pasien'] = $this->m_pendaftaran->get_penduduk_by_no_daftar($data['antri']->id_kunjungan);
        $this->load->view('pendaftaran/cetak_no_antri', $data);
    }


    function set_arrive_time($no_daftar) {
        $this->m_pendaftaran->set_arrive_time($no_daftar);
        $this->detail($no_daftar);
    }

    function cetak_lembar_pertama($no_daftar, $perawatan) {
        $this->load->model(array('m_pelayanan'));
        $data['apt'] = $this->configuration->rumah_sakit_get_atribute()->row();
        $data['rows'] = $this->m_pendaftaran->get_by_no_daftar($no_daftar);

        $kunjungan = $this->m_pendaftaran->get_riwayat_kunjungan($data['rows']->no_rm, 'asc');

        foreach ($kunjungan as $key => $value) {
            $kunjungan[$key]->pelayanan_kunjungan = $this->m_pelayanan->get_pelayanan_kunjungan_list($value->no_daftar);

            foreach ($kunjungan[$key]->pelayanan_kunjungan as $key => $val2) {
                $val2->diagnosis = $this->m_pelayanan->get_diagnosis_list($val2->id);
                $val2->tindakan = $this->m_pelayanan->get_tindakan_list($val2->id);
            }
        }

        $data['rm'] = $kunjungan;

        /*if (urldecode($perawatan) == 'IGD') {
            $data['title'] = 'LEMBAR GAWAT DARURAT';
            $this->load->view('pendaftaran/lembar-pertama-rm-igd', $data);
        } else {*/
            $data['title'] = 'LEMBAR POLIKLINIK';
            $this->load->view('pendaftaran/lembar-pertama-rm-poli', $data);
        //}
    }

    public function load_data_instansi_relasi() {
        $q = get_safe('q');
        $data = $this->m_pendaftaran->load_data_instansi_relasi($q)->result();
        die(json_encode($data));
    }

    function load_data_penduduk_profesi() {
        $q = get_safe('q');
        $data = $this->m_pendaftaran->load_data_penduduk_profesi($q)->result();
        die(json_encode($data));
    }
    
    function igd_new() {
        $data['title'] = 'Pendaftaran Layanan IGD';
        $data['kelamin'] = $this->m_demografi->kelamin();
        $data['keb_rawat'] = $this->m_pendaftaran->keb_rawat();
        $data['jenis_rawat'] = $this->m_pendaftaran->jenis_rawat();
        $data['jenis_layan'] = $this->m_pendaftaran->jenis_layan();
        $data['krit_layan'] = $this->m_pendaftaran->krit_layan();
        $data['darah'] = $this->m_demografi->gol_darah();
        $data['tgl_lahir'] = $this->m_demografi->usia();
        $data['alasan_datang'] = $this->m_pendaftaran->alasan_datang();
        

        if (isset($_GET['no_rm'])) {
            $data['no_rm'] = get_safe('no_rm');
            $pasien = $this->m_demografi->get_by_no_rm($data['no_rm']);
            foreach ($pasien as  $value) {
                $data['nama'] = $value->nama;
                $data['gender'] = array($value->gender);
                $data['lahir_tanggal'] = datefmysql($value->lahir_tanggal);
                $data['alamat'] = $value->alamat;
                $data['gol_darah'] = $value->darah_gol;
                $data['telp'] = $value->telp;
            }
        }

        $this->load->view('pendaftaran/igd', $data);
    }
    
    function igd_save() {
        $data = $this->m_pendaftaran->igd_save();
        $this->m_demografi->add_kunjungan_pasien($data['no_rm']);
        die(json_encode($data));
    }

    function kunjungan_save(){
        $this->load->model('m_demografi');
        $data = $this->m_pendaftaran->kunjungan_save();
        $this->m_demografi->add_kunjungan_pasien($data['no_rm']);

        $no_daftar = $data['no_daftar'];
        redirect('pendaftaran/detail/' . $no_daftar);
    }

    function delete_kunjungan($id){
        $stat = $this->m_pendaftaran->delete_kunjungan($id);
        echo json_encode(array('status'=>$stat));
    }

    function cetak_surat_rujukan($no_daftar){
        $data['title'] = "Surat Rujukan";
        $data['apt'] = $this->configuration->rumah_sakit_get_atribute()->row();
        $data['pasien'] = $this->m_pendaftaran->get_by_no_daftar($no_daftar);
        $this->load->view('pendaftaran/surat_rujukan', $data);
    }

    function cetak_surat_jawaban_rujukan($no_daftar){
        $this->load->model('m_pelayanan');
        $data['title'] = "Surat Jawaban Rujukan";
        $data['apt'] = $this->configuration->rumah_sakit_get_atribute()->row();
        $data['pasien'] = $this->m_pendaftaran->get_by_no_daftar($no_daftar);
        $data['lanjut'] = $this->m_pelayanan->get_pelayanan_kunjungan_list($no_daftar);
        $this->load->view('pendaftaran/surat_jawaban_rujukan', $data);
    }

    function antrian_pelayanan($no_daftar){
        if(post_safe('antrian') != ''){
            $data = array(
                'tanggal' => date("Y-m-d"),
                'id_jurusan_kualifikasi_pendidikan' => post_safe('id_layanan'),         
                'alamat_jalan_calon_pasien' => 'alamat',
                'no_antri' => post_safe('antrian'),
                'konfirm'=> 1
            );
             $this->db->insert('antrian_kunjungan', $data);
        }
       

         // insert pelayanan kunjungan
       $pk = array(
            'waktu' => NULL,
            'id_kepegawaian_dpjp' => (post_safe('id_dokter') != '')?post_safe('id_dokter'):NULL,
            'id_kunjungan' => $no_daftar,
            'id_jurusan_kualifikasi_pendidikan' => (post_safe('id_layanan') !== 'igd')?post_safe('id_layanan'):NULL,
            'no_antri' => (post_safe('antrian') !== '')?post_safe('antrian'):NULL,
            'jenis' => 'Rawat Jalan',
            'jenis_pelayanan' => (post_safe('id_layanan') !== 'igd')?"Poliklinik":"IGD",
        );
        $this->db->insert('pelayanan_kunjungan', $pk);
        $this->detail($no_daftar);
    }

}

?>