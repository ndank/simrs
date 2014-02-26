<?php

class Laboratorium extends CI_Controller {

    function __construct() {
        parent::__construct();
        //is_logged_in();
        $this->load->helper('url');
        $this->load->library('session');
        $this->load->model('configuration');
        $this->load->model('m_laboratorium');
        $this->load->model('m_pelayanan');
        $this->hari = date('Y-m-d');
        date_default_timezone_set('Asia/Jakarta');
    }


    function pemeriksaan_lab_add(){
        $status = $this->m_laboratorium->pemeriksaan_lab_save();
        echo json_encode(array('status'=>$status));
    }

    function pemeriksaan_lab_list($id_pk){
        $data = $this->m_laboratorium->get_pemeriksaan_lab($id_pk);
        echo json_encode($data);
    }

    function delete_pemeriksaan_lab($id){
        $this->m_laboratorium->delete_pemeriksaan_lab($id);
    }  

    function pemeriksaan_radiologi_list($id_pk){
        $data = $this->m_pelayanan->get_pemeriksaan_radiologi($id_pk);
        echo json_encode($data);
    }

    function pemeriksaan_lab_non(){
        $this->load->model('m_demografi');
        $data['title'] = 'Pemeriksaan Lab. (Pasien Luar)';
        $data['kelamin'] = $this->m_demografi->kelamin();
        $data['satuan'] = $this->m_pelayanan->load_data_master_satuan()->result();
        $data['tgl_lahir'] = $this->m_demografi->usia();
        $this->load->view('lab/pemeriksaan_lab_non', $data);
    }

    function pemeriksaan_lab_non_save(){
        $this->load->model('m_pendaftaran');
        $this->db->trans_begin();
        if(post_safe('id_pelayanan') == ''){
            // get id_jurusan_kualifikasi_pendidikan
            $jurusan = $this->db->query("select id from jurusan_kualifikasi_pendidikan where nama like '%Analis Laboratorium%' ")->row();
            if(sizeof($jurusan) != 0){
                $id_jurusan = $jurusan->id;
            }else{
                $id_jurusan = NULL;
            }

            $unit = $this->db->query("select id from unit where nama like '%Laboratorium%' ")->row();
            if(sizeof($unit) != 0){
                $id_unit = $unit->id;
            }else{
                $id_unit = NULL;
            }
            $pdd = $this->m_laboratorium->insert_penduduk();
            $no_daftar = $this->m_laboratorium->insert_pendaftaran($pdd['id'], $pdd['id_dinamis']);
            $id_pk = $this->m_laboratorium->insert_pelayanan_kunjungan($no_daftar, $id_jurusan, $id_unit);
            $param['no_daftar'] = $no_daftar;
            $param['id_pk'] = $id_pk;
            $param['tarif_id'] = 2; // kunjungan pasien
            $param['id_debet'] = 231;
            $param['id_kredit'] = 99;
            $param['waktu'] = date('Y-m-d H:i:s');
            $param['frekuensi'] = 1;
            $this->m_pendaftaran->insert_biaya($param);
        }else{
            $id_pk = post_safe('id_pelayanan');
        }
        $this->m_laboratorium->pemeriksaan_lab_non_save($id_pk);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $status = false;
        } else {
            $this->db->trans_commit();

            $status = true;
        }
        echo json_encode(array('status' => $status, 'id_pelayanan' => $id_pk));
    }

    function edit_hasil_lab($id){
        $update = array(
                'hasil' => get_safe('hasil')
            );
        $this->db->where('id', $id)->update('pemeriksaan_lab_pelayanan_kunjungan', $update);
    }


    function edit_hasil_rad($id){
        $this->db->trans_begin();
        $update = array(
                'id_kepegawaian_radiografer' => (post_safe('id_radiografer') !== '')?post_safe('id_radiografer'):NULL,
                'waktu_hasil' => (post_safe('waktu_hasil') != '')?datetime2mysql(post_safe('waktu_hasil')):NULL,
                'kv' => post_safe('kv'),
                'ma' => post_safe('ma'),
                's' => post_safe('s'),
                'p' => post_safe('p'),
                'fr' => post_safe('fr'),
            );
        $this->db->where('id', $id)->update('pemeriksaan_radiologi_pelayanan_kunjungan', $update);
         if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $status = false;
        } else {
            $this->db->trans_commit();
            $status = true;
        }
        echo json_encode(array('status' => $status));

    }

     function edit_waktu_hasil_lab($id){
        $update = array(
                'waktu_hasil' => (get_safe('hasil') != '')?datetime2mysql(get_safe('hasil')):NULL
            );
        $this->db->where('id', $id)->update('pemeriksaan_lab_pelayanan_kunjungan', $update);
    }

    function edit_waktu_hasil_rad($id){
        $update = array(
                'waktu_hasil' => (get_safe('hasil') != '')?datetime2mysql(get_safe('hasil')):NULL
            );
        $this->db->where('id', $id)->update('pemeriksaan_radiologi_pelayanan_kunjungan', $update);
    }

    function edit_waktu_fisioterapi($id){
        $this->db->trans_begin();
        $update = array(
                'waktu' => (post_safe('waktu') != '')?datetime2mysql(post_safe('waktu')):NULL
            );
        $this->db->where('id', $id)->update('tindakan_pelayanan_kunjungan', $update);
         if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $status = false;
        } else {
            $this->db->trans_commit();
            $status = true;
        }
        echo json_encode(array('status' => $status));

    }

    function get_hasil_radiologi($id){
        $data = $this->db->where('id', $id)->get('pemeriksaan_radiologi_pelayanan_kunjungan');
        echo json_encode($data->row());
    }

    function cetak_sp_pemeriksaan_lab($id_pk){
        $this->load->model('m_pelayanan');
        $data['title'] = "Surat Pemesanan Pemeriksaan Lab";
        $data['apt'] = $this->configuration->rumah_sakit_get_atribute()->row();
        $data['pasien'] = $this->m_laboratorium->pelayanan_kunjungan_get_data($id_pk); 
        $data['diagnosis'] = $this->m_pelayanan->get_diagnosis_list($id_pk);
        $data['laboratorium'] = $this->m_laboratorium->get_pemeriksaan_lab($id_pk);
        $this->load->view('lab/sp_pemeriksaan_lab', $data);
    }

    function cetak_sp_pemeriksaan_radiologi($id_pk){
        $this->load->model('m_pelayanan');
        $data['title'] = "Surat Pemesanan Pemeriksaan Radiologi";
        $data['apt'] = $this->configuration->rumah_sakit_get_atribute()->row();
        $data['pasien'] = $this->m_laboratorium->pelayanan_kunjungan_get_data($id_pk); 
        $data['diagnosis'] = $this->m_pelayanan->get_diagnosis_list($id_pk);
        $data['radiologi'] = $this->m_pelayanan->get_pemeriksaan_radiologi($id_pk);
        $this->load->view('lab/sp_pemeriksaan_radiologi', $data);
    }

    function cetak_sp_pemeriksaan_fisioterapi($id_pk){
        $this->load->model('m_pelayanan');
        $data['title'] = "Surat Pemesanan Pemeriksaan Fisioterapi";
        $data['apt'] = $this->configuration->rumah_sakit_get_atribute()->row();
        $data['pasien'] = $this->m_laboratorium->pelayanan_kunjungan_get_data($id_pk); 
        $data['diagnosis'] = $this->m_pelayanan->get_diagnosis_list($id_pk);
        $data['tindakan'] = $this->m_pelayanan->get_tindakan_list($id_pk);
        $this->load->view('lab/sp_pemeriksaan_fisioterapi', $data);
    }

    function cetak_hasil_pemeriksaan_lab($id_pk){
        $data['title'] = "Hasil Pemeriksaan Laboratorium Klinik";
        $data['apt'] = $this->configuration->rumah_sakit_get_atribute()->row();
        $data['pasien'] = $this->m_laboratorium->pelayanan_kunjungan_get_data($id_pk); 
        $data['laboratorium'] = $this->m_laboratorium->get_pemeriksaan_lab($id_pk);
        $this->load->view('lab/hasil_pemeriksaan_lab', $data);
    }

    function cetak_hasil_pemeriksaan_lab_luar($id_pk){
        $data['title'] = "Hasil Pemeriksaan Laboratorium Klinik";
        $data['apt'] = $this->configuration->rumah_sakit_get_atribute()->row();
        $data['pasien'] = $this->m_pelayanan->detail_pelayanan_kunjungan_non_pasien($id_pk)->row();
        $data['laboratorium'] = $this->m_laboratorium->get_pemeriksaan_lab($id_pk);
        $this->load->view('lab/hasil_pemeriksaan_lab', $data);
    }

    function pemeriksaan_radiologi_luar(){
        $this->load->model('m_demografi');
        $data['title'] = 'Pemeriksaan Radiologi (Pasien Luar)';
        $data['kelamin'] = $this->m_demografi->kelamin();
        $data['tgl_lahir'] = $this->m_demografi->usia();
        $this->load->view('pelayanan/pemeriksaan_radiologi_luar', $data);
    }

    function pemeriksaan_radiologi_luar_save(){
        $this->load->model('m_pendaftaran');
        $this->db->trans_begin();
        $no_daftar = null;

        if(post_safe('id_pelayanan') == ''){
            $pdd = $this->m_laboratorium->insert_penduduk();
            $jurusan = $this->db->query("select id from jurusan_kualifikasi_pendidikan where nama like '%Radiologi, Spesialist%' ")->row();
            if(sizeof($jurusan) != 0){
                $id_jurusan = $jurusan->id;
            }else{
                $id_jurusan = NULL;
            }

            $unit = $this->db->query("select id from unit where nama like '%Radiologi%' ")->row();
            if(sizeof($unit) != 0){
                $id_unit = $unit->id;
            }else{
                $id_unit = NULL;
            }


            $no_daftar = $this->m_laboratorium->insert_pendaftaran($pdd['id'], $pdd['id_dinamis'], $id_jurusan);
            $id_pk = $this->m_laboratorium->insert_pelayanan_kunjungan($no_daftar, $id_jurusan, $id_unit);
        }else{
            $id_pk = post_safe('id_pelayanan');
        }

        $this->m_laboratorium->pemeriksaan_radiologi_luar_save($id_pk);
        $param['no_daftar'] = $no_daftar;
        $param['id_pk'] = $id_pk;
        $param['tarif_id'] = 2; // kunjungan pasien
        $param['id_debet'] = 231;
        $param['id_kredit'] = 99;
        $param['waktu'] = date('Y-m-d H:i:s');
        $param['frekuensi'] = 1;
        $this->m_pendaftaran->insert_biaya($param);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $status = false;
        } else {
            $this->db->trans_commit();

            
            $status = true;
        }
        echo json_encode(array('status' => $status, 'id_pelayanan' => $id_pk));
    }

    function pelayanan_fisioterapi_luar_list(){
        $data['title'] = 'List Kunjungan Fisioterapi (Pasien Luar)';
        $this->load->view('lab/pelayanan_fisioterapi_luar_list', $data);
    }

    function fisioterapi_load_data($page){
        $limit = 10;
        $start = ($page - 1) * $limit;
        $search = 'null';
        $data['page'] = $page;
        $data['limit'] = $limit;
        $param = array(
            'awal' => (get_safe('awal') != '')?date2mysql(get_safe('awal')):null,
            'akhir' => (get_safe('akhir') != '')?date2mysql(get_safe('akhir')):null,
            'no' => get_safe('no'), 
            'nama' => get_safe('nama'),
            'alamat' => get_safe('alamat'),
            'id_kelurahan' => get_safe('id_kelurahan')
        );
        $query = $this->m_laboratorium->fisioterapi_load_data($limit, $start, $param);
        $data['customer'] = $query['data'];
        $data['jumlah'] = $query['jumlah'];
        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, 1, $search);
        $this->load->view('lab/fisioterapi_list', $data);
    }


    function pelayanan_fisioterapi_luar($no_daftar){
        $this->load->model('m_referensi');
        $data['title'] = 'Pelayanan Fisioterapi (Pasien Luar)';
        $data['customer'] = $this->m_laboratorium->detail_pendaftaran_non_pasien($no_daftar)->row();
        $unit = $this->m_referensi->unit_layanan_load_data(NULL, "")->result();
        foreach ($unit as $key => $value) {
            $data['unit'][$value->id] = $value->nama; 
        }

        $this->load->view('lab/pelayanan_fisioterapi_luar', $data);
    }

    function pelayanan_fisioterapi_luar_save(){
        $this->db->trans_begin();
       
        $id_pk = post_safe('id_pelayanan');
        
        $this->m_laboratorium->pelayanan_fisioterapi_luar_save($id_pk);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $status = false;
        } else {
            $this->db->trans_commit();
            $status = true;
        }
        echo json_encode(array('status' => $status, 'id_pelayanan' => $id_pk));
    }

    function pelayanan_fisioterapi_list($id_pk){
        $this->load->model('m_pelayanan');
        $data = $this->m_pelayanan->load_data_tindakan($id_pk, null)->result();
        echo json_encode($data);
    }

    function hasil_pemeriksaan_lab_list(){
        $data['title'] = 'Entry Hasil Pemeriksaan Laboratorium';
        $data['jenis'] = 'laboratorium';
        $this->load->view('lab/list_pelayanan_kunjungan', $data);
    }

    function list_pelayanan_kunjungan($page = null, $jenis) {
        $param = array(
            'awal' => post_safe('awal'),
            'akhir' => post_safe('akhir'),
            'no' => post_safe('no'),
            'nama' => post_safe('nama'),
            'no_rm' => post_safe('no_rm'),
            'alamat' => post_safe('alamat'),
            'kelurahan' => post_safe('id_kelurahan'),
            'jenis' => $jenis
        );
        $limit = 10;
        if ($page == 'undefined') {
            $page = 1;
        }
        $str = 'null';
        $start = ($page - 1) * $limit;
        $data['page'] = $page;
        $data['limit'] = $limit;
        $data['pemeriksaan'] = $jenis;
        $data['data_list'] = $this->m_laboratorium->list_pelayanan_kunjungan($param, $limit, $start)->result();
        $data['jumlah'] = $this->m_laboratorium->list_pelayanan_kunjungan($param, null, null)->num_rows();
        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, 1, $str);
        $this->load->view('lab/pelayanan_kunjungan_table', $data);
    }

    function detail_pemeriksaan_lab($id_pk){
        $this->load->model('m_pelayanan');
        $data['list_lab'] = $this->m_laboratorium->get_pemeriksaan_lab($id_pk);
        $data['satuan'] = $this->m_pelayanan->load_data_master_satuan()->result();
        $data['pasien'] = $this->m_laboratorium->pelayanan_kunjungan_get_data($id_pk);
        $data['subtitle'] = 'Detail Pemeriksaan Laboratorium';
        $this->load->view('lab/detail_pemeriksaan_lab', $data);
    }



    function hasil_pemeriksaan_radiologi_list(){
        $data['title'] = 'Entry Hasil Pemeriksaan Radiologi';
        $data['jenis'] = 'radiologi';
        $this->load->view('lab/list_pelayanan_kunjungan', $data);
    }

    function detail_pemeriksaan_rad($id_pk){
        $this->load->model('m_pelayanan');

        $data['list_rad'] = $this->m_pelayanan->get_pemeriksaan_radiologi($id_pk);
        $data['pasien'] = $this->m_laboratorium->pelayanan_kunjungan_get_data($id_pk);
        $data['subtitle'] = 'Detail Pemeriksaan Radiologi';
        $this->load->view('lab/detail_pemeriksaan_rad', $data);
    }

    function pelayanan_fisioterapi_pasien_list(){
        $data['title'] = 'Entri Hasil Pemeriksan Fisioterapi';
        $data['jenis'] = 'fisioterapi';
        $this->load->view('lab/list_pelayanan_kunjungan', $data);
    }

    function detail_pelayanan_fisioterapi($id_pk){
        $this->load->model('m_pelayanan');
        $data['list'] = $this->m_pelayanan->get_tindakan_list($id_pk);
        $data['pasien'] = $this->m_laboratorium->pelayanan_kunjungan_get_data($id_pk);
        $data['subtitle'] = 'Detail Pelayanan Fisioterapi';
        $this->load->view('lab/detail_pelayanan_fisioterapi', $data);
    }

    function add_penduduk(){
        $this->load->model('m_referensi');

        $this->db->trans_begin();

        $pdd = array(
            'nama' => post_safe('nama')
        );
        $add['penduduk'] = $pdd;
        $return = $pdd;

        $dinamis = array(
            'tanggal' => date('Y-m-d'),
            'profesi_id' => 2
        );
        
        $add['dinamis'] = $dinamis;
        $return['id'] = $this->m_referensi->penduduk_add_data($add);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $return['status'] = FALSE;
        } else {
            $this->db->trans_commit();
            $return['status'] = TRUE;
        }
        
         echo json_encode($return);
    }

}

?>