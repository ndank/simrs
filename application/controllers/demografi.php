<?php

class Demografi extends CI_Controller {

    public $waktu = null;
    public $hari = null;

    function __construct() {
        parent::__construct();
        is_logged_in();
        $this->load->model('configuration');
        $this->load->model('m_demografi');
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->helper('functions');
        date_default_timezone_set('Asia/Jakarta');

        $this->waktu = gmdate('Y-m-d H:i:s', gmdate('U') + 25200);
        $this->hari = gmdate('Y-m-d', gmdate('U') + 25200);
        
    }

    function is_login() {
        $user = $this->session->userdata('user');
        if ($user != '') {
            
        } else {
            redirect(base_url());
        }
    }

    function get_jurusan() {
        $q = get_safe('q');
        $data = $this->m_demografi->load_jurusan($q)->result();
        die(json_encode($data));
    }

    function antrian_kunjungan(){
        $this->load->model('m_pendaftaran');
        $data['title'] = "Antrian Kunjungan Poliklinik (Phone)";
        $data['no_rm'] = post_safe('no_rm');
        $data['alamat'] = post_safe('alamat');
        $data['layanan'] = $this->m_pendaftaran->get_layanan_admission();
        
        if($data['no_rm']!=""){
            $pasien = $this->m_demografi->get_by_no_rm($data['no_rm']);
            foreach ($pasien as  $value) {
                $data['nama'] = $value->nama;
                $data['gender'] = $value->gender;
                $data['lahir_tanggal'] = datefmysql($value->lahir_tanggal);
                $data['telp_no'] = $value->telp; 
                $data['alamat'] = $value->alamat;
            }
        }else{
            $data['id_penduduk'] = post_safe('id_penduduk');
            $data['nama'] = post_safe('nama_antri');
            $data['gender'] = post_safe('kelamin');
            $data['lahir_tanggal'] = post_safe('tgl_lahir_antri');
            $data['telp_no'] = post_safe('tlpn');  
            $data['alamat'] = post_safe('alamat');
            if (post_safe('id_kelurahan') != '') {
                $kelurahan = $this->m_demografi->detail_kelurahan(post_safe('id_kelurahan'));
                $data['id_kelurahan'] = post_safe('id_kelurahan');
                $data['kelurahan'] = $kelurahan->nama;
                $data['kecamatan'] = $kelurahan->kecamatan;
                $data['kabupaten'] = $kelurahan->kabupaten;
                $data['provinsi'] = $kelurahan->provinsi;
            }

            $data['norm'] = get_last_id('pasien','no_rm');
            

        }
        $this->load->view('demografi/antrian_kunjungan', $data);
    }

    function antrian_save(){
        $this->m_demografi->antrian_save_data();
    }

    function get_antrian(){
        $this->load->model('unit_layanan');
        $data['kd_unit'] = get_safe('id_layanan');
        $data['tgl_layan'] = date2mysql(get_safe('tgl_layan'));
        $data['pasien'] = true;
        $antri = $this->unit_layanan->get_next_antrian($data);
        die(json_encode(array('antrian' => $antri)));
    }

    function get_antrian_non(){
        $this->load->model('unit_layanan');
        $data['kd_unit'] = 21;
        $data['tgl_layan'] = date2mysql(get_safe('tgl_layan'));
        $data['pasien'] = false;
        $antri = $this->unit_layanan->get_next_antrian($data);
        die(json_encode(array('antrian' => $antri)));
    }

    function edit($no_rm, $id_antri) {
        $this->load->view('layout');
       
        $data['id_antri'] = $id_antri;
        $data['pasien'] = $this->m_demografi->get_by_no_rm($no_rm);

        $data['kelamin'] = $this->m_demografi->kelamin();
        $data['darah'] = $this->m_demografi->gol_darah();
        $data['agama'] = $this->m_demografi->agama();
        $data['pendidikan'] = $this->m_demografi->pendidikan();
        $data['pekerjaan'] = $this->m_demografi->pekerjaan();
        $data['stat_nikah'] = $this->m_demografi->stat_nikah();
        $data['title'] = "Edit Data Pasien";

        $this->load->view('demografi/edit', $data);
    }

    function edit_put($id_antri) {
        $edited = false;
        if (post_safe('no_rm')) {
            if (post_safe('identitas_no') != post_safe('bf_identitas_no'))
                $edited = true;
            if (post_safe('agama') != post_safe('bf_agama'))
                $edited = true;
            if (post_safe('alamat') != post_safe('bf_alamat'))
                $edited = true;
            if (post_safe('pendidikan') != post_safe('bf_pendidikan'))
                $edited = true;
            if (post_safe('pernikahan') != post_safe('bf_pernikahan'))
                $edited = true;
            if (post_safe('pekerjaan') != post_safe('bf_pekerjaan'))
                $edited = true;
            if (post_safe('hd_kelurahan') != post_safe('bf_hd_kelurahan'))
                $edited = true;

            $kelurahan_id = (post_safe('hd_kelurahan') == "") ? NULL : post_safe('hd_kelurahan');
            $tmp_id = (post_safe('hd_lahir_tempat') == "") ? NULL : post_safe('hd_lahir_tempat');

            $penduduk = array(
                'id' => post_safe('id'),
                'nama' => post_safe('nama'),
                'gender' => post_safe('gender'),
                'darah_gol' => post_safe('darah_gol'),
                'lahir_kabupaten_id' => $tmp_id, // link to kabupaten_id
                'lahir_tanggal' => datetopg(post_safe('lahir_tanggal')),
                'telp' => post_safe('telp')
            );

            $this->m_demografi->save_penduduk($penduduk);


            if ($edited) {
                $dinamis = array(
                    'tanggal' => $this->hari,
                    'penduduk_id' => post_safe('id'),
                    'identitas_no' => post_safe('identitas_no'),
                    'agama' => post_safe('agama'),
                    'alamat' => post_safe('alamat'),
                    'kelurahan_id' => $kelurahan_id,
                    'pernikahan' => post_safe('pernikahan'),
                    'pendidikan_id' => (post_safe('pendidikan') != '') ? post_safe('pendidikan') : NULL,
                    'profesi_id' => 11,
                    'pekerjaan_id' => (post_safe('pekerjaan') != '') ? post_safe('pekerjaan') : NULL,
                );

                $this->m_demografi->create_dinamis_penduduk($dinamis);
            }
            
            $this->detail(post_safe('no_rm'), $id_antri);
        }
    }

    function new_pasien() {

        $data['title'] = "Antrian Kunjungan Poliklinik (Phone)";
        $data['kelamin'] = $this->m_demografi->kelamin();
        $data['tgl_lahir'] = $this->m_demografi->usia();
        $this->load->view('demografi/new', $data);
    }

    // buat menyimpan data baru
    function find_similar_post() {

        $where = array(
            'nama' => post_safe('nama'),
            'tgl_lahir' => post_safe('tgl_lahir'),
            'kelamin' => post_safe('kelamin'),
            'alamat' => post_safe('alamat')
        );

        $query = $this->m_demografi->find_similiar($where);

        // bila data yang diinputkan ada???

        $data['pasien'] = $query;
        $this->load->view('demografi/find_new', $data);
    }

    function new_pelengkap() {
        // prosedure membuat nomor rekam medis
        $data['no_rm'] = $this->m_demografi->get_last_no_rm();
        $data['tgl_lahir'] = date2mysql(post_safe('tgl_lahir'));
        $data['id_pdd'] = post_safe('id');
        if ($data['id_pdd'] != '') {
            $data['penduduk'] = $this->m_demografi->get_penduduk($data['id_pdd']);
        }

        $data['id_antri'] = post_safe('id_antri');
        $data['detail'] = $this->m_demografi->get_antrian(post_safe('id_antri'))->row();
        $data['nama'] = post_safe('nama');
        $data['kelamin'] = post_safe('kelamin');
        $data['telp'] = post_safe('telp');
        $data['gol_darah'] = $this->m_demografi->gol_darah();
        $data['dob'] = $this->m_demografi->dob();
        $data['agama'] = $this->m_demografi->agama();
        $data['pendidikan'] = $this->m_demografi->pendidikan();
        $data['pernikahan'] = $this->m_demografi->stat_nikah();
        $data['pekerjaan'] = $this->m_demografi->pekerjaan();
        if (post_safe('id_kelurahan') != '') {
            $kelurahan = $this->m_demografi->detail_kelurahan(post_safe('id_kelurahan'));
            $data['id_kelurahan'] = post_safe('id_kelurahan');
            $data['kelurahan'] = $kelurahan->nama;
            $data['kecamatan'] = $kelurahan->kecamatan;
            $data['kabupaten'] = $kelurahan->kabupaten;
            $data['provinsi'] = $kelurahan->provinsi;
        }
        $data['title'] = "Kependudukan Pasien";

        $this->load->view('demografi/new_pelengkap', $data);
    }

    function new_post() {

        $kelurahan_id = (post_safe('hd_kelurahan') == "") ? NULL : post_safe('hd_kelurahan');
        $tmp_id = (post_safe('hd_lahir_tempat') == "") ? NULL : post_safe('hd_lahir_tempat');

        $penduduk = array(
                'nama' => post_safe('nama'),
                'gender' => post_safe('gender'),
                'darah_gol' => post_safe('darah_gol'),
                'lahir_kabupaten_id' => $tmp_id, // link to kabupaten_id
                'lahir_tanggal' => post_safe('lahir_tanggal'),
                'telp' => post_safe('telp'),
                'unit_id' => null
            );

        $dinamis = array(
                'tanggal' => $this->hari,
                'identitas_no' => post_safe('identitas_no'),
                'agama' => (post_safe('agama') != '') ? post_safe('agama') : NULL,
                'alamat' => post_safe('alamat'),
                'kelurahan_id' => $kelurahan_id,
                'pernikahan' => post_safe('pernikahan'),
                'pendidikan_id' => (post_safe('pendidikan') != '') ? post_safe('pendidikan') : NULL,
                'profesi_id' => (post_safe('profesi') != '') ? post_safe('profesi') : NULL,
                'pekerjaan_id' => (post_safe('pekerjaan') != '') ? post_safe('pekerjaan') : NULL
            );


        // entry penduduk dulu
        // baru entry pasien
        if (post_safe('id_pdd') == '') {


            $last_id = $this->m_demografi->create_penduduk($penduduk);

            $pasien = array(
                'registrasi_waktu' => $this->waktu,
                'kunjungan' => 0,
                'id' => $last_id,
                'is_cetak_kartu' => 0
            );


            $no_rm = $this->m_demografi->create_pasien($pasien);

            $dinamis['penduduk_id'] = $last_id;
            $this->m_demografi->create_dinamis_penduduk($dinamis);
        } else {
            // Pasien dari data penduduk yg sudah ada
            $pasien = array(
                'registrasi_waktu' => $this->waktu,
                'kunjungan' => 0,
                'id' => post_safe('id_pdd'),
                'is_cetak_kartu' => 0
            );


            $no_rm = $this->m_demografi->create_pasien($pasien);
            $dinamis['penduduk_id'] = $pasien['id'];

            /* */

            $this->m_demografi->create_dinamis_penduduk($dinamis);
            /* Update tanggal lahir */
            $this->db->where('id', $pasien['id']);
            $this->db->update('penduduk',$penduduk);

        }
        $id_antri = post_safe('id_antri');
        $this->m_demografi->set_norm_antrian($id_antri, $no_rm);
        $this->detail($no_rm, $id_antri);
    }

    function get_kelurahan() {
        $q = get_safe('q');
        $rows = $this->m_demografi->get_kelurahan($q);
        die(json_encode($rows));
    }

    function detail_kelurahan($id){
        $row = $this->m_demografi->detail_kelurahan($id);
        die(json_encode($row));
    }

    function get_kabupaten() {
        $q = get_safe('q');
        $rows = $this->m_demografi->get_kabupaten($q);
        die(json_encode($rows));
    }

    function get_asuransi() {
        $q = get_safe('q');
        $rows = $this->m_demografi->get_asuransi($q);
        die(json_encode($rows));
    }

    function get_penanggungjawab() {
        $q = get_safe('q');
        $rows = $this->m_demografi->get_penanggungjawab($q);
        die(json_encode($rows));
    }

    function message($title, $pesan, $redirect) {
        $this->load->view('layout');
        $data['title'] = $title;
        $data['pesan'] = $pesan;
        $data['redirect'] = $redirect;
        $this->load->view('demografi/message', $data);
    }

    function list_pasien() {
        $this->load->view('layout');
        $data['pasien'] = $this->m_demografi->get();
        $data['title'] = "Data Demografi Pasien";
        $this->load->view('demografi/list', $data);
    }

    /* Fungsi - fungsi untuk mencari data pasien */

    function search($tab = null) {
        $this->load->view('layout');
        $data['title'] = "Rekap Pasien";
        $data['tab'] = $tab;
        $this->load->view('demografi/search', $data);
    }

    function search_by_no_rm_get() {
        $this->load->view('demografi/search-tab1');
    }

    function advance_search_get() {
        $data['kelamin'] = $this->m_demografi->kelamin();
        $this->load->view('demografi/search-tab2', $data);
    }

    function advance_search_post($page = null) {
        $limit = 10;
        $where = array(
            'no_rm' => get_safe('no_rm'),
            'nama' => get_safe('nama'),
            'addr_jln' => get_safe('alamat'),
            'kelurahan' => get_safe('id_kelurahan'),
            'kelamin' => get_safe('kelamin'),
            'umur' => get_safe('umur')
        );
        $start = ($page - 1) * $limit;
        $search = 'null';
        $data['page'] = $page;
        $data['limit'] = $limit;
        $query = $this->m_demografi->advanced_search($limit, $start, $where);
        $data['pasien'] = $query['data'];
        $data['jumlah'] = $query['jumlah'];
        $data['status'] = $query['status'];
        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, 1, $search);
        $this->load->view('demografi/hasil_pencarian', $data);
    }

    function search_by_no_rm_post($page = null) {
        $limit = 10;
        // metode ajax        
        $no_rm = get_safe('no_rm');
        
        $start = ($page - 1) * $limit;
        $data['page'] = $page;
        $data['limit'] = $limit;

        $query = $this->m_demografi->search_by_no_rm($limit, $start ,$no_rm);

        $data['jumlah'] = $query['jumlah'];
        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, 2, '');
        $data['pasien'] = $query['data'];
        $data['status'] = $query['status'];
        $this->load->view('demografi/hasil_pencarian', $data);
    }

    /* Fungsi - fungsi untuk mencari data pasien */

    function detail($no_rm, $id_antri) {
        $this->load->view('layout');
        $data['id_antri'] = $id_antri;
        $data['pasien'] = $this->m_demografi->get_by_no_rm($no_rm);
        $data['title'] = "Detail Kependudukan Pasien";
        $this->load->view('demografi/detail', $data);
    }

    function cek_pendaftaran($no_rm){
        $status = $this->m_demografi->cek_pendaftaran($no_rm);
        die(json_encode(array('status'=>$status)));
    }

    function antrian_fisioterapi(){
        $this->load->model('unit_layanan');
        $data['title'] = "Antrian Pelayanan Fisioterapi Pasien Luar";
        $data['kelamin'] = $this->m_demografi->kelamin();
        $data['darah'] = $this->m_demografi->gol_darah();
        $data['tgl_lahir'] = $this->m_demografi->usia();

        $antri['kd_unit'] = 21;
        $antri['tgl_layan'] = date('Y-m-d');
        $antri['pasien'] = false;
        $data['antri'] = $this->unit_layanan->get_next_antrian($antri);
        $data['id_jurusan'] = 21;

        $this->load->view('demografi/antrian_fisioterapi', $data);
    }

    function search_penduduk($page){
        $limit = 10;
        if ($page == 'undefined') {
            $page = 1;
        }
        $start = ($page - 1) * $limit;

        $query = $this->m_demografi->search_penduduk($limit, $start);
        $data['penduduk'] = $query['data'];
        $data['page'] = $page;
        $data['limit'] = $limit;
        $data['jumlah'] = $query['jumlah'];
        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, 1, '');
        $this->load->view('demografi/list_penduduk', $data);
    }
    
    function search_pasien() {
        $search['nama']   = post_safe('nama');
        $search['alamat'] = post_safe('alamat');
        $data['penduduk'] = $this->m_demografi->search_pasien($search)->result();
        $this->load->view('demografi/list_penduduk', $data);
    }

    function antrian_fisioterapi_save(){
        $data = $this->m_demografi->antrian_fisioterapi_save();
        echo json_encode($data);
    }

    function antrian_fisioterapi_phone_save(){
        $this->db->trans_begin();

        $data = $this->m_demografi->antrian_fisioterapi_save_data(post_safe('id_penduduk'));

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $status = FALSE;
        } else {
            $this->db->trans_commit();
            $status = TRUE;
        }

        $data['status'] = $status;
        echo json_encode($data);
    }

    function antrian_fisioterapi_phone(){
        $this->load->model('unit_layanan');
        $data['title'] = "Antrian Pelayanan Fisioterapi Pasien Luar (Phone)";
        $data['kelamin'] = $this->m_demografi->kelamin();
        $data['tgl_lahir'] = $this->m_demografi->usia();

        $antri['kd_unit'] = 21;
        $antri['tgl_layan'] = date('Y-m-d');
        $antri['pasien'] = false;
        $data['antri'] = $this->unit_layanan->get_next_antrian($antri);
        $data['id_jurusan'] = 21;

        $this->load->view('demografi/antrian_fisioterapi_phone', $data);
    }

    function konfirmasi_antrian_fisioterapi_phone(){
        $data['title'] = "Konfirmasi Pelayanan Fisioterapi Pasien Luar (Phone)";
        $data['id_jurusan'] = 21;
        $this->load->view('demografi/konfirmasi_antrian_fisioterapi_phone', $data);
    }

    function search_antrian_fisioterapi($page){
        $limit = 10;
        $start = ($page - 1) * $limit;
        $search = 'null';
        $data['page'] = $page;
        $data['limit'] = $limit;
        $param = array(
            'tanggal' => (get_safe('tanggal') != '')?date2mysql(get_safe('tanggal')):null,
            'layanan' => get_safe('layanan'), 
            'no_antri' => get_safe('antri')
        );
        $query = $this->m_demografi->search_antrian_fisioterapi($limit, $start, $param);
        $data['customer'] = $query['data'];
        $data['jumlah'] = $query['jumlah'];
        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, 1, $search);
        $this->load->view('demografi/antrian_fisioterapi_list', $data);
    }

    function antrian_fisioterapi_detail($id){
        $data['title'] = 'Konfirmasi Antrian Fisioterapi Pasien Luar (Phone)';
        $data['pasien'] = $this->m_demografi->get_antrian_fisioterapi_detail($id);
        $this->load->view('demografi/antrian_fisioterapi_detail', $data);
    }

    function antrian_fisioterapi_confirm($id){
      $data = $this->m_demografi->antrian_fisioterapi_confirm($id);
      echo json_encode($data);
    }


    function antrian_fisioterapi_entry_penduduk($id) {
        // prosedure membuat nomor rekam medis
        $data['pasien'] = $this->db->where('id', $id)->get('antrian_kunjungan')->row();

        $data['gol_darah'] = $this->m_demografi->gol_darah();
        $data['dob'] = $this->m_demografi->dob();
        $data['agama'] = $this->m_demografi->agama();
        $data['pendidikan'] = $this->m_demografi->pendidikan();
        $data['pernikahan'] = $this->m_demografi->stat_nikah();
        $data['pekerjaan'] = $this->m_demografi->pekerjaan();

        if ($data['pasien']->id_kelurahan != '') {
            $kelurahan = $this->m_demografi->detail_kelurahan($data['pasien']->id_kelurahan);
            $data['kelurahan'] = $kelurahan->nama;
            $data['kecamatan'] = $kelurahan->kecamatan;
            $data['kabupaten'] = $kelurahan->kabupaten;
            $data['provinsi'] = $kelurahan->provinsi;
        }
        $data['title'] = "Entry Kependudukan";

        $this->load->view('demografi/antrian_fisioterapi_entry_penduduk', $data);
    }

    function antrian_fisioterapi_penduduk_save($id){
        $this->m_demografi->antrian_fisioterapi_penduduk_save($id);
        $this->antrian_fisioterapi_detail($id);
    }

    function pasien_get_detail($no_rm) {
        $data = $this->m_demografi->detail_data_pasien($no_rm);
        return die(json_encode($data));
    }

    function add_penduduk(){
        $this->load->model('m_referensi');

        $this->db->trans_begin();

        $pdd = array(
            'nama' => post_safe('nama'),
            'telp' => post_safe('telp'),
        );
        $add['penduduk'] = $pdd;
        $return = $pdd;

        $dinamis = array(
            'kelurahan_id' => (post_safe('kelurahan_id_pdd') !== '')?post_safe('kelurahan_id_pdd'):NULL,
            'tanggal' => date('Y-m-d'),
            'alamat' => preg_replace('~[\r\n]+~', ' ', post_safe('alamat')),
        );
        
        $add['dinamis'] = $dinamis;
        $return['id'] = $this->m_referensi->penduduk_add_data($add);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $return['status'] = FALSE;
        } else {
            $this->db->trans_commit();           
            
            $return['kelurahan'] = post_safe('kelurahan');
            $return['kelurahan_id'] = $dinamis['kelurahan_id'];
            $return['alamat'] = post_safe('alamat');
            $return['status'] = TRUE;
        }
        
         echo json_encode($return);
    }

    function get_rekam_medis_pasien($no_rm, $show=null){
        $this->load->model(array('m_pelayanan', 'm_pendaftaran','m_laboratorium'));
        // detail pasien
        $pasien = $this->m_demografi->detail_data_pasien($no_rm);
        $kunjungan = $this->m_pendaftaran->get_riwayat_kunjungan($no_rm, 'desc');

        foreach ($kunjungan as $key => $value) {
            $kunjungan[$key]->pelayanan_kunjungan = $this->m_pelayanan->get_pelayanan_kunjungan_list($value->no_daftar);

            foreach ($kunjungan[$key]->pelayanan_kunjungan as $key => $val2) {
                $val2->diagnosis = $this->m_pelayanan->get_diagnosis_list($val2->id);
                $val2->tindakan = $this->m_pelayanan->get_tindakan_list($val2->id);
                $val2->resep = $this->m_pelayanan->get_resep_pelayanan_kunjungan($val2->id);
                $val2->lab = $this->m_laboratorium->get_pemeriksaan_lab($val2->id);
                $val2->rad = $this->m_pelayanan->get_pemeriksaan_radiologi($val2->id);
            }
        }
        //die(json_encode($kunjungan));
        $data = array(
            'pasien' => $pasien,
            'kunjungan' => $kunjungan
        );

        if ($show == "print") {
            $data['apt'] = $this->configuration->rumah_sakit_get_atribute()->row();
            $data['title'] = "Riwayat Rekam Medis";
            $this->load->view('demografi/rekam_medis_print',$data);
        }else{
            $this->load->view('demografi/rekam_medis',$data);
        }
        
    }
    function get_penduduk($id){
        $data = $this->m_demografi->get_penduduk($id);
        die(json_encode($data));
    }

   
}

?>