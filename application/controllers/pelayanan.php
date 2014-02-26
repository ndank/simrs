<?php

class Pelayanan extends CI_Controller {
    
    function __construct() {
        parent::__construct();
        $this->load->model('configuration');
        $this->load->helper('functions');
        $this->load->model(array('m_inventory','m_referensi','m_pelayanan','m_resep','m_pendaftaran','m_registrasi_rs'));
        date_default_timezone_set('Asia/Jakarta');
        
    }

    function search_pendaftaran_penduduk($page) {
        $limit = 10;
        if ($page == 'undefined') {
            $page = 1;
        }
        $start = ($page - 1) * $limit;

        $query = $this->m_pelayanan->search_pendaftaran_penduduk($limit, $start);
        $data['penduduk'] = $query['data'];
        $data['page'] = $page;
        $data['limit'] = $limit;
        $data['jumlah'] = $query['jumlah'];
        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, 1, '');
        $this->load->view('demografi/list_penduduk', $data);
    }

    function gol_sebab_sakit_load_data(){
        $q = get_safe('q');
        $data = $this->m_pelayanan->gol_sebab_sakit_load_data($q)->result();
        die(json_encode($data));
    }

    function gol_sebab_sakit_load_data2(){
        $q = get_safe('q');
        $data = $this->m_pelayanan->gol_sebab_sakit_load_data2($q)->result();
        die(json_encode($data));
    }

    function  icd_load_data(){
        $q = get_safe('q');
        $data = $this->m_pelayanan->gol_sebab_sakit_load_data($q)->result();
        die(json_encode($data));
    }

    function load_data_unit_layanan(){
        $data = $this->m_referensi->unit_layanan_load_data(NULL, "")->result();
        die(json_encode($data));
    }
    
    function penjualan_jasa($no = null) {
        $this->load->model('m_inv_autocomplete');
        $this->load->model('m_billing');
        $data['title'] = 'Entri Jasa Tindakan';
        $no_rm = post_safe('id_penduduk');
        $data['no_daftar'] = $no;
        $data['id_unit'] = $this->session->userdata('id_unit');

        $id_pk = post_safe('pelayanan');
        if (isset($no_rm) and $no_rm != '') {
            $data = $this->m_inventory->penjualan_jasa_save($no, $id_pk);
            die(json_encode($data));
        }else if(post_safe('id_penduduk_hide') != ''){
             $data = $this->m_inventory->penjualan_jasa_save($no, $id_pk);
            die(json_encode($data));
        }
        if ($no != null) {
            $data['data'] = $this->m_pelayanan->data_pasien_muat_data($no)->row(); 
            $inap = $this->m_inv_autocomplete->load_data_pelayanan_kunjungan_by_id_penduduk($data['data']->id_penduduk)->row();
            
            if ($inap != null) {
                $data['inap'] = $inap;
            }
            $data['list_pk'][''] = "Pilih Semua Pelayanan Kunjungan";
            $pel = $this->m_pelayanan->get_pelayanan_kunjungan_list($no);
            foreach ($pel as $key => $val) {
                $bed = "";
                if ($val->jenis == 'Rawat Inap') {
                    $bed = $val->nama_unit." ".$val->kelas." ".$val->nomor_bed;
                };
                $jenis_pl = ($val->jenis_pelayanan !== null)?$val->jenis_pelayanan:'Pasien Luar';

                $data['list_pk'][$val->id] = $jenis_pl." (".$val->unit_layanan.") ". $bed;
            }
            
            $data['list_data'] = $this->m_billing->penjualan_jasa_detail_load_data($no, null)->result();
        }
        $this->load->view('penjualan-jasa', $data);
    }
    
    function penjualan_nr($id = NULL) {
        $data['title'] = 'Penyerahan Non Resep (Bebas)';
        $bank = $this->configuration->instansi_relasi_load_data(null, 'Bank');
        $ddmenu = array();
        $ddmenu[''] = 'Pilih Pembayaran ..';
        foreach ($bank->result_array() as $rows) {
            $ddmenu[$rows['id']] = $rows['nama'];
        }
        $tanggal = post_safe('tanggal');
        if (isset($tanggal) and $tanggal != '') {
            $data = $this->m_inventory->penjualan_non_resep_save();
            die(json_encode($data));
        }
        if ($id !== NULL) {
            $data['list_data'] = $this->m_inventory->penjualan_load_data($id)->result();
        }
        $data['list_bank'] = $ddmenu;
        $data['apt'] = $this->configuration->rumah_sakit_get_atribute()->row();
        $this->load->view('penjualan-nr', $data);
    }
    
    function pembayaran_penjualan_nr() {
        $data['title'] = 'Pembayaran Penjualan Non Resep';
        $bank = $this->configuration->instansi_relasi_load_data(null, 'Bank');
        $ddmenu = array();
        $ddmenu[''] = 'Pilih Pembayaran ..';
        foreach ($bank->result_array() as $rows) {
            $ddmenu[$rows['id']] = $rows['nama'];
        }
        $data['list_bank'] = $ddmenu;
        $this->load->view('pembayaran-penjualan-nr', $data);
    }
    
    function pembayaran_penjualan_nr_save() {
        $data = $this->m_inventory->pembayaran_penjualan_nr_save();
        die(json_encode($data));
    }
    
    function get_no_penjualan_bebas() {
        $q = get_safe('q');
        $data = $this->m_inventory->penjualan_load_nomor($q)->result();
        die(json_encode($data));
    }
    
    function penjualan_cetak_nota($id_penjualan, $penjualan = null) {
        $data['title'] = 'Kitir';
        $data['jenis'] = '';
        if ($penjualan != null) {
            $data['title'] = 'NOTA PENJUALAN NON RESEP';
            $data['jenis'] = $penjualan;
        }
        
        $data['apt'] = $this->configuration->rumah_sakit_get_atribute()->row();
        $data['penjualan'] = $this->m_inventory->penjualan_load_data($id_penjualan)->result();
        $this->load->view('inventory/print/kitir', $data);
    }
    
    function kitir_cetak_nota($id_resep) {
        $data['title'] = 'KITIR';
        $data['jenis'] = '';
        $data['apt'] = $this->configuration->rumah_sakit_get_atribute()->row();
        $data['rows'] = $this->m_resep->kitir_load_atribute($id_resep)->result();
        $data['penjualan'] = $this->m_resep->kitir_load_data($id_resep)->result();
        $this->load->view('inventory/print/kitir-resep', $data);
    }
    
    function cetak_etiket() {
        $data['title'] = 'Etiket';
        $data['list_data'] = $this->m_resep->cetak_etiket(get_safe('no_resep'), get_safe('no_r'))->result();
        $this->load->view('inventory/print/etiket',$data);
    }
    
    function cetak_etiket_pelayanan_farmasi() {
        $data['title'] = 'Etiket';
        $data['list_data'] = $this->m_resep->cetak_etiket_pelayanan_farmasi(get_safe('no_resep'), get_safe('no_r'))->result();
        $this->load->view('inventory/print/etiket',$data);
    }
    
    function cetak_pmr() {
        $data['title'] = 'Patient Medical Record';
        $data['apt'] = $this->configuration->rumah_sakit_get_atribute()->row();
        $data['list_data'] = $this->m_resep->get_data_pmr_penduduk_detail(get_safe('id_pasien'))->result();
        $data['rows'] = $this->m_resep->get_data_pmr_penduduk(get_safe('id_pasien'))->row();
        $this->load->view('inventory/print/pmr',$data);
    }
    
    function get_jenis_rawat_by_pasien($id_pasien, $no_rm) {
        $data = $this->m_resep->get_jenis_rawat_by_pasien($id_pasien, $no_rm);
        die(json_encode($data));
    }

    function poliklinik($no_daftar = 'null', $id_pk = null){
        $data['title'] = 'Pelayanan Poliklinik';
        $data['layanan'] = $this->m_pendaftaran->get_layanan_admission('igd'); 
        if ($id_pk != NULL) {
            $data['data'] = $this->m_pelayanan->detail_pelayanan_kunjungan($id_pk)->row();
            $data['rujuk']= $this->m_pelayanan->detail_pelayanan_kunjungan_rujukan($id_pk)->row();
            
            $data['diagnosis'] = $this->m_pelayanan->get_diagnosis_list($id_pk);
            $data['tindakan'] = $this->m_pelayanan->get_tindakan_list($id_pk);
        } else if ($no_daftar != 'null') {
            $data['data'] = $this->m_pelayanan->data_pasien_muat_data($no_daftar)->row();
        }


        $this->load->view('pelayanan/poliklinik',$data);
    }

    function poliklinik_save(){
        $this->db->trans_begin();

        $id_pelayanan = $this->m_pelayanan->pelayanan_kunjungan_save("poli");

        if($id_pelayanan !== null){        
            if(post_safe('dokter_diag') != null){
                $this->m_pelayanan->diagnosa_pelayanan_kunjungan_save($id_pelayanan);
            }
            if(post_safe('id_nakes_tindak') != null){
                $this->m_pelayanan->tindakan_pelayanan_kunjungan_save($id_pelayanan);
            }
            if (post_safe('dokter_lab') !== '') {
                $this->m_pelayanan->pemeriksaan_lab_save($id_pelayanan);
            }
            if (post_safe('id_dokter_radio') !== '') {
                $this->m_pelayanan->pemeriksaan_rad_save($id_pelayanan);
            }
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $status = FALSE;
        } else {
            $this->db->trans_commit();
            $status = TRUE;
        }
        echo json_encode(array('status' => $status, 'id_pelayanan' => $id_pelayanan));
    }

    function igd($no_daftar = 'null', $id_pk = null){
        $data['title'] = 'Pelayanan IGD';
        if($id_pk != NULL){
            $data['data'] = $this->m_pelayanan->detail_pelayanan_kunjungan($id_pk)->row();
            $data['rujuk']= $this->m_pelayanan->detail_pelayanan_kunjungan_rujukan($id_pk)->row();
            
            $data['diagnosis'] = $this->m_pelayanan->get_diagnosis_list($id_pk);
            $data['tindakan'] = $this->m_pelayanan->get_tindakan_list($id_pk);
        }else if ($no_daftar != 'null') {
            $data['data'] = $this->m_pelayanan->data_pasien_muat_data($no_daftar)->row();
        }

        $this->load->view('pelayanan/igd',$data);
    }

    function igd_save(){
        $this->db->trans_begin();

        $id_pelayanan = $this->m_pelayanan->pelayanan_kunjungan_save("igd");
        
        if($id_pelayanan !== null){    
            if(post_safe('dokter_diag') != null){
                $this->m_pelayanan->diagnosa_pelayanan_kunjungan_save($id_pelayanan);
            }
            if(post_safe('id_nakes_tindak') != null){
                $this->m_pelayanan->tindakan_pelayanan_kunjungan_save($id_pelayanan);
            }

            if (isset($_POST['dokter_lab'])) {
                $this->m_pelayanan->pemeriksaan_lab_save($id_pelayanan);
            }

            if (post_safe('id_dokter_radio') !== '') {
                $this->m_pelayanan->pemeriksaan_rad_save($id_pelayanan);
            }
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $status = FALSE;
        } else {
            $this->db->trans_commit();
            $status = TRUE;
        }
        echo json_encode(array('status' => $status, 'id_pelayanan'=>$id_pelayanan));
    }

    function rawat_inap($no_daftar="null",$id_pk = null){
        $data['title'] = 'Pelayanan Rawat Inap';
        $data['kelas'] = $this->m_referensi->kelas_tarif_get_data();
        $data['layanan'] = $this->m_pendaftaran->get_layanan_admission(); 
        if ($id_pk != NULL) {
            // Edit pelayanan kunjungan
            $data['print'] = "true";
            $data['data'] = $this->m_pelayanan->detail_pelayanan_kunjungan($id_pk)->row();
            $data['rujuk']= $this->m_pelayanan->detail_pelayanan_kunjungan_rujukan($id_pk)->row();
            $data['diagnosis'] = $this->m_pelayanan->get_diagnosis_list($id_pk);
            $data['tindakan'] = $this->m_pelayanan->get_tindakan_list($id_pk);
            $data['inap_list'] = $this->m_pelayanan->get_pelayanan_kunjungan_list($data['data']->id_kunjungan,'Rawat Inap');
        } else if ($no_daftar != 'null') {
            // Entri pelayanan kunjungan
            $data['data'] = $this->m_pelayanan->data_pasien_muat_data($no_daftar)->row();
        }
        
        $this->load->view('pelayanan/rawat_inap',$data);
    }

    function rawat_inap_save(){
        $this->db->trans_begin();

        $id_pelayanan = $this->m_pelayanan->pelayanan_kunjungan_save("inap");

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $status = FALSE;
        } else {
            if($id_pelayanan !== null){  
                $this->db->trans_commit();
                $status = TRUE;         
            }else{
                $this->db->trans_rollback();
                $status = FALSE;
            }
        }


        echo json_encode(array('status' => $status,'id_pelayanan'=>$id_pelayanan));
    }

    function rawat_inap_list($id_kunjungan){
        $data['inap'] = $this->m_pelayanan->get_pelayanan_kunjungan_list($id_kunjungan,'Rawat Inap'); 
        $this->load->view('pelayanan/inap_list', $data);
    }
    
    function load_data_diagnosis($id_kunjungan, $jenis) {
        $data['total_list'] = $this->m_pelayanan->load_data_diagnosis($id_kunjungan, $jenis)->num_rows();
        $data['diag_list'] = $this->m_pelayanan->load_data_diagnosis($id_kunjungan, $jenis)->result();
        $this->load->view('pelayanan/poli-diagnosis-table', $data);
    }
    
    function load_data_tindakan($id_kunjungan, $jenis) {
        $data['total_list_tindakan'] = $this->m_pelayanan->load_data_tindakan($id_kunjungan, $jenis)->num_rows();
        $data['tind_list'] = $this->m_pelayanan->load_data_tindakan($id_kunjungan, $jenis)->result();
        $this->load->view('pelayanan/poli-tindakan-table', $data);
    }

    function load_data_pelayanan_kunjungan($id_kunjungan, $jenis = null) {
        $pelayanan = $this->m_pelayanan->load_data_pelayanan($id_kunjungan, $jenis)->row();
        die(json_encode($pelayanan));
    }

    function detail_pelayanan_kunjungan($id_pk){
        $data = $this->m_pelayanan->detail_pelayanan_kunjungan($id_pk)->row();
        echo json_encode($data);
    }


    function inap_kunjungan_get_data($id){
         $data['inap'] = $this->m_pelayanan->pelayanan_kunjungan_get_data($id); 
         $data['diagnosis'] = $this->m_pelayanan->get_diagnosis_list($id);
         $data['tindakan'] = $this->m_pelayanan->get_tindakan_list($id);
         die(json_encode($data));
    }

    function delete_tindakan($id){
        $this->m_pelayanan->delete_tindakan($id);
    }

    function delete_diagnosis($id){
        $this->m_pelayanan->delete_diagnosis($id);
    }

    
    function rekap_morbiditas() {
        $data['title'] = 'Rekap. Morbiditas';
        $data['klp_umur'] = array(
            '' => 'Semua ...',
            '1' => '0 - 6 Hari', 
            '2' => '7 - 28 Hari', 
            '3' => '28 - 1 Tahun', 
            '4' => '1 - 4 Tahun',
            '5' => '5 - 14 Tahun',
            '6' => '15 - 24 Tahun',
            '7' => '25 - 44 Tahun',
            '8' => '45 - 64 Tahun',
            '9' => 'Lebih dari 65 Tahun');
        $this->load->view('pelayanan/rekap-morbiditas', $data);
    }
    
    function rekap_morbiditas_load_data() {
        $var = array(
            'awal' => date2mysql(get_safe('awal')),
            'akhir' => date2mysql(get_safe('akhir')),
            'kondisi' => get_safe('kondisi'),
            'keluar' => get_safe('keluar'),
            'sex' => get_safe('sex'),
            'klpumur' => get_safe('klpumur')
        );
        $data['list_data'] = $this->m_pelayanan->rekap_morbiditas_load_data($var)->result();
        $this->load->view('pelayanan/rekap-morbiditas-table', $data);
    }

    function diagnosis_rawat_inap($id_pelayanan){
        $this->db->trans_begin();
        $this->m_pelayanan->diagnosa_pelayanan_kunjungan_save($id_pelayanan);
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $status = FALSE;
        } else {
            $this->db->trans_commit();
            $status = TRUE;
        }
        echo json_encode(array('status' => $status,'id_pelayanan'=>$id_pelayanan));
    }

    function tindakan_rawat_inap($id_pelayanan){
        $this->db->trans_begin();
        $this->m_pelayanan->tindakan_pelayanan_kunjungan_save($id_pelayanan);
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $status = FALSE;
        } else {
            $this->db->trans_commit();
            $status = TRUE;
        }
        echo json_encode(array('status' => $status,'id_pelayanan'=>$id_pelayanan));
    }

    function ic_persetujuan_tindakan($id_kunjungan, $id_tindakan){
        $this->load->model('m_registrasi_rs');
        $data['apt'] = $this->configuration->rumah_sakit_get_atribute()->row();
        $data['title'] = "IC Persetujuan Tindakan";
        $data['detail']= $this->m_pelayanan->get_atribute_ic($id_kunjungan)->row();
        $data['tindakan'] = $this->m_pelayanan->get_data_tindakan($id_tindakan)->row();
        $this->load->view('pelayanan/ic_persetujuan', $data);
    }

    function ic_penolakan_tindakan($id_kunjungan, $id_tindakan){
        $this->load->model('m_registrasi_rs');
        $data['apt'] = $this->configuration->rumah_sakit_get_atribute()->row();
        $data['title'] = "IC Penolakan Tindakan";
        $data['detail']= $this->m_pelayanan->get_atribute_ic($id_kunjungan)->row();
        $data['tindakan'] = $this->m_pelayanan->get_data_tindakan($id_tindakan)->row();
        $this->load->view('pelayanan/ic_penolakan', $data);
    }
    
    function ic_pasien_tidak_sadar($id_kunjungan, $id_tindakan){
        $this->load->model('m_registrasi_rs');
        $data['apt'] = $this->configuration->rumah_sakit_get_atribute()->row();
        $data['title'] = "IC Pengembilan Keputusan Tindakan Medis Pasien Tidak Sadar";
        $data['detail']= $this->m_pelayanan->get_atribute_ic($id_kunjungan)->row();
        $data['tindakan'] = $this->m_pelayanan->get_data_tindakan($id_tindakan)->row();
        $this->load->view('pelayanan/ic_pk_pasien_tidak_sadar', $data);
    }
    
    function ic_penghentian_tindakan($id_kunjungan,$id_tindakan){
        $this->load->model('m_registrasi_rs');
        $data['apt'] = $this->configuration->rumah_sakit_get_atribute()->row();
        $data['title'] = "IC Penghentian Tindakan";
        $data['detail']= $this->m_pelayanan->get_atribute_ic($id_kunjungan)->row();
        $data['tindakan'] = $this->m_pelayanan->get_data_tindakan($id_tindakan)->row();
        $this->load->view('pelayanan/ic_penghentian_tindakan', $data);
    }
    
    function ic_persetujuan_rawat_inap($id_pk){
        $this->load->model('m_registrasi_rs');
        $data['apt'] = $this->configuration->rumah_sakit_get_atribute()->row();
        $data['title'] = "IC Persetujuan Rawat Inap";
        $data['detail']= $this->m_pelayanan->get_atribute_ic($id_pk)->row();
        $this->load->view('pelayanan/ic_persetujuan_rawat_inap', $data);
    }
    
    function ic_persetujuan_tindakan_sterilisasi($id_kunjungan){
        $this->load->model('m_registrasi_rs');
        $data['apt'] = $this->configuration->rumah_sakit_get_atribute()->row();
        $data['title'] = "IC Persetujuan Tindakan Sterilisasi";
        $data['detail']= $this->m_pelayanan->get_atribute_ic($id_kunjungan)->row();
        $this->load->view('pelayanan/ic_persetujuan_tindakan_sterilisasi', $data);
    }

    function cetak_gelang_rawat_inap($no_rm){
        $data['title'] = "Cetak Gelang Rawat Inap";
        $data['detail']= $this->m_pelayanan->get_by_no_rm($no_rm);
        $this->load->view('pelayanan/cetak_gelang', $data);
    }
    
    function resep_load($id_resep) {
        $data['biaya_apoteker'] =  $this->m_referensi->biaya_apoteker_load_data()->result();
        $data['list_data'] = $this->m_resep->data_resep_dokter_muat_data($id_resep)->result();
        $this->load->view('resep-table', $data);
    }
    
    function receipt($id_resep = null) {
        $data['title'] = 'Resep';
        $data['biaya_apoteker'] =  $this->m_referensi->biaya_apoteker_load_data()->result();
        $id_dokter = post_safe('id_dokter');
        $id_pasien = post_safe('id_pasien');
        if (isset($id_dokter) and $id_dokter != '' and isset($id_pasien) and $id_pasien != '') {
            $data = $this->m_inventory->receipt_save();
            die(json_encode($data));
        }
        if ($id_resep != NULL) {
            $data['id_resep'] = $id_resep;
            $data['list_data'] = $this->m_resep->data_resep_dokter_muat_data($id_resep)->result();
        }
        $this->load->view('receipt', $data);
    }
    
    function delete_resep($id_resep) {
        return $this->db->delete('resep', array('id' => $id_resep));
    }
    
    function klinis() {
        $data['title'] = 'Rekap. Pelayanan Klinis Pasien';
        $this->load->view('pelayanan/klinis', $data);
    }
    
    function klinis_load_data($page = null) {
        $param = array(
            'awal' => post_safe('awal'),
            'akhir' => post_safe('akhir'),
            'no' => post_safe('no'),
            'nama' => post_safe('nama'),
            'no_rm' => post_safe('no_rm'),
            'alamat' => post_safe('alamat'),
            'kelurahan' => post_safe('id_kelurahan'),
        );
        $limit = 10;
        if ($page == 'undefined') {
            $page = 1;
        }
        $str = 'null';
        $start = ($page - 1) * $limit;
        $data['page'] = $page;
        $data['limit'] = $limit;
        //echo $page.' - '.$start;
        $data['data_list'] = $this->m_pelayanan->klinis_load_data($param, $limit, $start)->result();
        $data['jumlah'] = $this->m_pelayanan->klinis_load_data($param, null, null)->num_rows();
        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, 1, $str);
        $this->load->view('pelayanan/klinis-table', $data);
    }
    
    function detail($id_kunjungan, $irna = null) {
        if ($irna != null) {
            $data['irna'] = $irna;
        }
        $data['no_rm'] = get_safe('no_rm');
        $data['nama'] = get_safe('nama');
        $data['id_kunjungan'] = $id_kunjungan;
        $data['data_list'] = $this->m_pelayanan->detail_kunjungan($id_kunjungan, $irna)->result();
        $this->load->view('pelayanan/detail-pelayanan', $data);
    }

    function detail_pelayanan($id_pk){
        $data['list_diagnosis'] = $this->m_pelayanan->detail_pelayanan_diagnosis($id_pk)->result();
        $data['list_tindakan'] = $this->m_pelayanan->detail_pelayanan_tindakan($id_pk)->result();
        $param = array(
            'id_pk' => $id_pk
        );
        $data['list_lab'] = $this->m_pelayanan->laboratorium_load_data($param)->result();
        $data['list_rad'] = $this->m_pelayanan->radiologi_load_data($param)->result();
        $data['satuan'] = $this->m_pelayanan->load_data_master_satuan()->result();
        $data['list_resep'] = $this->m_resep->resep_muat_data_by_pelayanan($id_pk)->result();
        $this->load->view('pelayanan/detail_pelayanan_pasien', $data);
    }
    
    function detail_diagnosis($id_pelayanan_kunjungan) {
        $data['data_list'] = $this->m_pelayanan->detail_pelayanan_diagnosis($id_pelayanan_kunjungan)->result();
        $this->load->view('pelayanan/detail-pelayanan-diagnosis', $data);
    }
    
    function detail_tindakan($id_pelayanan_kunjungan) {
        $data['data_list'] = $this->m_pelayanan->detail_pelayanan_tindakan($id_pelayanan_kunjungan)->result();
        $param = array(
            'id_pk' => $id_pelayanan_kunjungan
        );
        $data['list_lab'] = $this->m_pelayanan->laboratorium_load_data($param)->result();
        $data['satuan'] = $this->m_pelayanan->load_data_master_satuan()->result();
        $this->load->view('pelayanan/detail-pelayanan-tindakan', $data);
    }
    
    function rekap_igd_load_data() {
        $data = array(
            'awal' => date2mysql(get_safe('awal')),
            'akhir' => date2mysql(get_safe('akhir')),
            'rujukan' => get_safe('rujukan'),
            'tindaklanjut' => get_safe('tindak_lanjut'),
            'matiigd' => get_safe('matiigd'),
            'doa' => get_safe('doa')
        );
        $data['list_data'] = $this->m_pelayanan->rekap_igd_load_data($data)->result();
        $this->load->view('pelayanan/rekap_igd-table', $data);
    }

    function jenis_layanan(){
        $data['title'] = "Jenis Layanan";
        $jenis = $this->m_pelayanan->data_jenis_layanan_load();
        $ddmenu = array();
        $ddmenu[''] = 'Pilih Jenis  Layanan ..';
        foreach ($jenis->result_array() as $rows) {
            $ddmenu[$rows['id']] = $rows['nama'];
        }
        $data['jenis_layanan'] = $ddmenu;


        $this->load->view("pelayanan/jenis_layanan",$data);
    }

    function get_jenis_layanan_list($limit, $page, $search, $id=null) {
        if ($page == 'undefined') {
            $page = 1;
        }
        $start = ($page - 1) * $limit;
        $data['page'] = $page;
        $data['limit'] = $limit;
        $query = $this->m_pelayanan->data_jenis_layanan_load_data($limit, $start, $search, $id);
        $data['jumlah'] = $query['jumlah'];
        $data['list_data'] = $query['data'];
        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, 1, $search);
        return $data;
    }

    function manage_jenis_layanan($mode, $page=null){
            $data['list_sub'] = $this->m_pelayanan->data_subjenis_layanan_load_data()->result();
            $data['list_jenis'] = $this->m_pelayanan->data_jenis_layanan_load()->result();
            $limit = 15;
            switch ($mode) {
                case 'list':
                    $nama_sub_sub = (isset($_GET['nama_sub_sub_jenis']))?get_safe('nama_sub_sub_jenis'):'';
                    $data = $this->get_jenis_layanan_list($limit, $page,$nama_sub_sub);
                    $this->load->view('pelayanan/jenis_layanan_list',$data);
                    break;

                case 'add':                   
                    $id =  $this->m_pelayanan->jenis_layanan_save();
                    $data = $this->get_jenis_layanan_list($limit, 1,'',$id);
                    $data['id'] = $id;
                    $this->load->view('pelayanan/jenis_layanan_list',$data);
                    break;
                case 'edit':                   
                    $id =  $this->m_pelayanan->jenis_layanan_edit();
                    $data = $this->get_jenis_layanan_list($limit, 1,'',$id);
                    $data['id'] = $id;
                    $this->load->view('pelayanan/jenis_layanan_list',$data);
                    break;

                case 'add_sub':
                    $id_sub =  $this->m_pelayanan->jenis_sub_layanan_save();
                    $data = $this->get_jenis_layanan_list($limit, 1,'',post_safe("jenis_id"));
                    $data['id_sub'] = $id_sub;
                    $this->load->view('pelayanan/jenis_layanan_list',$data);
                    break;

                 case 'edit_sub':
                    $id_sub=  $this->m_pelayanan->jenis_sub_layanan_edit();
                    $data = $this->get_jenis_layanan_list($limit, 1,'',post_safe("jenis_id"));
                    $data['id_sub'] = $id_sub;
                    $this->load->view('pelayanan/jenis_layanan_list',$data);
                    break;

                case 'add_sub_sub':
                    $id_sub = post_safe('sub_jenis_id');
                    $id_sub_sub =  $this->m_pelayanan->jenis_sub_sub_layanan_save();
                    $data = $this->get_jenis_layanan_list($limit, 1,'',post_safe("jenis_id"));
                    $data['id_sub'] = $id_sub;
                    $data['id_sub_sub'] = $id_sub_sub;
                    $this->load->view('pelayanan/jenis_layanan_list',$data);
                    break;

                case 'edit_sub_sub':
                    $id_sub = post_safe('sub_jenis_id');
                    $id_sub_sub =  $this->m_pelayanan->jenis_sub_sub_layanan_edit();
                    $data = $this->get_jenis_layanan_list($limit, 1,'',post_safe("jenis_id"));
                    $data['id_sub'] = $id_sub;
                    $data['id_sub_sub'] = $id_sub_sub;
                    $this->load->view('pelayanan/jenis_layanan_list',$data);
                    break;
                
                default:
                    # code...
                    break;
            }
    }

    function delete_jenis_layanan($id){
        $deleted = $this->m_pelayanan->jenis_layanan_delete($id);
        $this->manage_jenis_layanan('list', 1);
    }

    function delete_sub_jenis_layanan($id){
        $deleted = $this->m_pelayanan->sub_jenis_layanan_delete($id);
        $this->manage_jenis_layanan('list', 1);
    }

    function delete_sub_sub_jenis_layanan($id){
        $deleted = $this->m_pelayanan->sub_sub_jenis_layanan_delete($id);
        $this->manage_jenis_layanan('list', 1);
    }

    function load_data_jenis_layanan() {
        $q = get_safe('q');
        $data = $this->m_pelayanan->jenis_layanan_load_data($q)->result();
        die(json_encode($data));
    }

    function load_data_sub_jenis_layanan() {
        $q = get_safe('q');
        $id_jenis = get_safe('id_jenis');
        $data = $this->m_pelayanan->sub_jenis_layanan_load_data($q, $id_jenis)->result();
        die(json_encode($data));
    }

    function load_data_sub_sub_jenis_layanan() {
        $q = get_safe('q');
        $id_sub = (isset($_GET['id_sub']))?get_safe('id_sub'):'';
        $data = $this->m_pelayanan->sub_sub_jenis_layanan_load_data($q, $id_sub)->result();
        die(json_encode($data));
    }
   
    function rekap_kegiatan(){
        $data['title'] = "Rekap. Kegiatan Pelayanan";
        $this->load->view('pelayanan/rekap_kegiatan',$data);
    }
    
    function load_data_jenis_layanan2() {
        $array = $this->m_pelayanan->load_data_jenis_layanan()->result();
        foreach ($array as $rows) {
            echo "<option value='".$rows->id."'>".$rows->nama."</option>";
        }
    }
    
    function load_data_sub_jenis_layanan2() {
        $array = $this->m_pelayanan->load_data_sub_jenis_layanan()->result();
        foreach ($array as $rows) {
            echo "<option value='".$rows->id."'>".$rows->nama."</option>";
        }
    }

    function rekap_kegiatan_load_data(){
        $data = $this->m_pelayanan->rekap_kegiatan_query()->result();
        echo json_encode($data);
    }

    function cetak_rl_kegiatan_rs($tahun){
        $this->load->model('m_registrasi_rs');
        $data["rs"] = $this->m_registrasi_rs->get_last_register_data(date('Y')); 
        $laporan = array();
        $data['tahun'] = $tahun;
        $jenis = $this->m_pelayanan->get_jenis_layanan();        
        foreach ($jenis as $key => $value) {
            if( $this->m_pelayanan->get_laporan_tindakan($tahun, $value->id_sub_sub) != null){
                $laporan[$key]['id_jenis'] = $value->id_jenis;
                $laporan[$key]['jenis'] = $value->nama_jenis;
                $laporan[$key]['id_sub'] = $value->id_sub;
                $laporan[$key]['sub'] = $value->nama_sub;
                $laporan[$key]['id_sub_sub'] = $value->id_sub_sub;
                $laporan[$key]['sub_sub'] = $value->nama_sub_sub;
                $laporan[$key]['layanan'] =  $this->m_pelayanan->get_laporan_tindakan($tahun,$value->id_sub_sub);
            }
            
        }
        $data['laporan'] = $laporan;
        $data['title'] = "Kegiatan RS";
        $this->load->view('pelayanan/rl_kegiatan_rs', $data);
    }

    function get_tindakan_laporan($tahun){
        $laporan = array();
        $data['tahun'] = $tahun;
        $jenis = $this->m_pelayanan->get_jenis_layanan();        
        foreach ($jenis as $key => $value) {
            if( $this->m_pelayanan->get_laporan_tindakan($tahun, $value->id_sub_sub) != null){
                $laporan[$key]['id_jenis'] = $value->id_jenis;
                $laporan[$key]['jenis'] = $value->nama_jenis;
                $laporan[$key]['id_sub'] = $value->id_sub;
                $laporan[$key]['sub'] = $value->nama_sub;
                $laporan[$key]['id_sub_sub'] = $value->id_sub_sub;
                $laporan[$key]['sub_sub'] = $value->nama_sub_sub;
                $laporan[$key]['layanan'] =  $this->m_pelayanan->get_laporan_tindakan($tahun,$value->id_sub_sub);
            }
            
        }
        echo json_encode($laporan);
    }

    function pasien_load_data() {
        $q = get_safe('q');
        $data = $this->m_pelayanan->data_pasien($q);
        return die(json_encode($data));
    }

    function pasien_load_detail($no_rm) {
        $data = $this->m_pelayanan->detail_data_pasien($no_rm);
        return die(json_encode($data));
    }

    function get_no_kasus($no_rm, $id_diag){
        $kasus = "Baru";
        $no = $this->m_pelayanan->get_no_kasus($no_rm, $id_diag);
        if($no > 1){
            $kasus = "Lama";
        }
        die(json_encode(array('jenis_kasus'=>$kasus)));
    }
    
    function cek_data_pasien_on_pel_kunjungan($no_rm) {
        $data = $this->m_pelayanan->cek_data_pasien_on_pel_kunjungan($no_rm)->row();
        die(json_encode($data));
    }
    
    function cek_ketersediaan_penjualan($id_resep) {
        $data = $this->m_pelayanan->cek_ketersediaan_penjualan($id_resep);
        die(json_encode($data));
    }

    function informasi_irna() {
        $data['title'] = 'Informasi Pelayanan Rawat Inap';
        $this->load->view('pelayanan/informasi_irna', $data);
    }

    function irna_load_data($page = null) {
        $param = array(
            'awal' => post_safe('awal'),
            'akhir' => post_safe('akhir'),
            'no' => post_safe('no'),
            'nama' => post_safe('nama'),
            'no_rm' => post_safe('no_rm'),
            'alamat' => post_safe('alamat'),
            'kelurahan' => post_safe('id_kelurahan'),
        );
        $limit = 10;
        if ($page == 'undefined') {
            $page = 1;
        }
        $str = 'null';
        $start = ($page - 1) * $limit;
        $data['page'] = $page;
        $data['limit'] = $limit;
        //echo $page.' - '.$start;
        $data['data_list'] = $this->m_pelayanan->klinis_load_data($param, $limit, $start, true)->result();
        $data['jumlah'] = $this->m_pelayanan->klinis_load_data($param, null, null, true)->num_rows();
        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, 1, $str);
        $this->load->view('pelayanan/irna-table', $data);
    }


    function laboratorium() {
        $data['title'] = 'Rekap Pemeriksaan Lab.';
        $data['satuan'] = $this->m_pelayanan->load_data_master_satuan()->result();
        $this->load->view('pelayanan/laboratorium', $data);
    }
    
    function laboratorium_load_data() {
        $param = array(
            'awal' => isset($_GET['awal'])?datetime2mysql(get_safe('awal')):NULL,
            'akhir' => isset($_GET['awal'])?datetime2mysql(get_safe('akhir')):NULL,
            'pasien' => isset($_GET['awal'])?get_safe('id_pasien'):NULL,
            'dokter' => isset($_GET['awal'])?get_safe('id_dokter'):NULL,
            'pemeriksa' => isset($_GET['awal'])?get_safe('id_pemeriksa'):NULL,
            'layanan' => isset($_GET['awal'])?get_safe('id_layanan'):NULL
        );
        $data['list_data'] = $this->m_pelayanan->laboratorium_load_data($param)->result();
        $this->load->view('laporan/laboratorium-rekap', $data);
    }
    
    function laboratorium_load_data_pemeriksaan($id) {
        $param = array(
            'id' => $id
        );
        $data = $this->m_pelayanan->laboratorium_load_data($param)->row();
        die(json_encode($data));
    }

    function laboratorium_luar_load_data_pemeriksaan($id) {
        $param = array(
            'id' => $id
        );
        $data = $this->m_pelayanan->laboratorium_luar_load_data($param)->row();
        die(json_encode($data));
    }

    function radiologi_load_data_pemeriksaan($id) {
        $param = array(
            'id' => $id
        );
        $data = $this->m_pelayanan->radiologi_load_data($param)->row();
        die(json_encode($data));
    }

    function radiologi_luar_load_data_pemeriksaan($id) {
        $param = array(
            'id' => $id
        );
        $data = $this->m_pelayanan->radiologi_luar_load_data($param)->row();
        die(json_encode($data));
    }

    function fisioterapi_load_data_pemeriksaan($id) {
        $param = array(
            'id' => $id
        );
        $data = $this->m_pelayanan->fisioterapi_load_data($param)->row();
        die(json_encode($data));
    }
    
    function pemeriksaan_lab() {
        $data['title'] = 'Pemeriksaan Lab.';
        $this->load->view('pelayanan/pemeriksaan-lab', $data);
    }
    
    function delete_data_lab($id_pl) {
        $this->m_pelayanan->delete_data_lab($id_pl);
    }
    
    function pemeriksaan_lab_save() {
        $data = $this->m_pelayanan->pemeriksaan_lab_update();
        die(json_encode($data));
    }

    function cetak_lembar_rm_inap($id_pk, $no_daftar){
        $data['title'] = 'Lembar Rawat Inap';
        $data['apt'] = $this->configuration->rumah_sakit_get_atribute()->row();
        $data['pk'] = $this->m_pelayanan->get_atribute_ic($id_pk)->row();
        $data['rows'] = $this->m_pendaftaran->get_by_no_daftar($no_daftar);
        $this->load->view('pelayanan/cetak_lembar_rm_inap', $data);
    }

    function cetak_surat_kontrol($id_pk, $no_daftar){
        $data['title'] = 'Surat Kontrol';
        $data['apt'] = $this->configuration->rumah_sakit_get_atribute()->row();
        $data['pk'] = $this->m_pelayanan->get_atribute_ic($id_pk)->row();
        $data['rows'] = $this->m_pendaftaran->get_by_no_daftar($no_daftar);
        $this->load->view('pelayanan/cetak_surat_kontrol', $data);
    }

    function pemeriksaan_radiologi_save($id_pelayanan){
        $this->db->trans_begin();

        
        if (post_safe('id_dokter_radio') !== '') {
            $this->m_pelayanan->pemeriksaan_rad_save($id_pelayanan);
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $status = FALSE;
        } else {
            $this->db->trans_commit();
            $status = TRUE;
        }
        echo json_encode(array('status' => $status, 'id_pelayanan'=>$id_pelayanan));
    }

    function pemeriksaan_radiologi_list($id_pk){
        $data = $this->m_pelayanan->get_pemeriksaan_radiologi($id_pk);
        echo json_encode($data);
    }

    function delete_pemeriksaan_radiologi($id){
        $this->db->where('id',$id)->delete('pemeriksaan_radiologi_pelayanan_kunjungan');
    }

    function radiologi_non_pasien($no_daftar = 'null', $id_pk = null){
        $this->load->model('m_demografi');
        $data['title'] = 'Pemeriksaan Radiologi Pasien Luar)';
        $data['kelamin'] = $this->m_demografi->kelamin();
        $data['tgl_lahir'] = $this->m_demografi->usia();
        $data['customer'] = $this->m_pelayanan->detail_pelayanan_kunjungan_non_pasien($id_pk)->row();
        $data['radiologi'] =  $this->m_pelayanan->get_pemeriksaan_radiologi($id_pk);
        $this->load->view('pelayanan/pemeriksaan_radiologi_luar', $data);
    }

    function laboratorium_non_pasien($no_daftar = 'null', $id_pk = null){
        $this->load->model('m_demografi');
        $this->load->model('m_laboratorium');
        $data['title'] = 'Pemeriksaan Lab. (Pasien Luar)';
        $data['kelamin'] = $this->m_demografi->kelamin();
        $data['tgl_lahir'] = $this->m_demografi->usia();
        $data['customer'] = $this->m_pelayanan->detail_pelayanan_kunjungan_non_pasien($id_pk)->row();
        $data['laboratorium'] =  $this->m_laboratorium->get_pemeriksaan_lab($id_pk);
        $data['satuan'] = $this->m_pelayanan->load_data_master_satuan()->result();
        $this->load->view('lab/pemeriksaan_lab_non', $data);
    }

    function pelayanan_fisioterapi_luar($no_daftar=null, $id_pk){
        $this->load->model('m_referensi');
        $this->load->model('m_laboratorium');
        $data['title'] = 'Pelayanan Fisioterapi (Pasien Luar)';
        $data['customer'] = $this->m_laboratorium->detail_pendaftaran_non_pasien($no_daftar)->row();
        $data['id_pk'] = $id_pk;
        $data['fisioterapi'] = $this->m_pelayanan->detail_pelayanan_tindakan($id_pk)->result();
        $unit = $this->m_referensi->unit_layanan_load_data(NULL, "")->result();
        foreach ($unit as $key => $value) {
            $data['unit'][$value->id] = $value->nama; 
        }

        $this->load->view('lab/pelayanan_fisioterapi_luar', $data);
    }

    function rekap_profil_pasien(){
        $data['title'] = 'Rekap Profil Pasien';
        $this->load->view('pelayanan/rekap_profil_pasien', $data);
    }

    function cetak_rekap_profil_pasien($awal,$akhir){
        $this->load->model('m_billing');
        $param = array(
            'awal' => $awal,
            'akhir' => $akhir,
        );
        $data = $param;

        $query = $this->m_pelayanan->rekap_profil_pasien($param);

        foreach ($query as $key => $val) {
            foreach ($this->m_pelayanan->get_pelayanan_kunjungan_list($val->no_daftar) as $key2 => $val2) {
                $query[$key]->asuransi[] = $val2->nama_asuransi;
                $query[$key]->diagnosis = $this->m_pelayanan->get_diagnosis_list($val2->id);
                $query[$key]->tindakan = $this->m_pelayanan->get_tindakan_list($val2->id);
                $query[$key]->resep = $this->m_pelayanan->get_resep_pelayanan_kunjungan($val2->id);
                
                $bayar = (int)$this->m_billing->total_pembayaran($val->no_daftar)->total_pembayaran;
                $sisa = (int)$this->m_billing->get_sisa_tagihan($val->no_daftar);

                $query[$key]->total_biaya = $bayar + $sisa;
                $query[$key]->los = $this->m_pelayanan->get_durasi_inap_pasien($val->no_daftar);
            }
        }
        $data['apt'] = $this->configuration->rumah_sakit_get_atribute()->row();
        $data['profil'] = $query;
        $this->load->view('pelayanan/cetak_rekap_profil_pasien', $data);
    }

    function laporan_morbiditas(){
        $data['title'] = "Laporan Morbiditas";
        $this->load->view('pelayanan/laporan_morbiditas', $data);

    }

    function load_data_morbiditas(){
   
        $param = array(
                'awal' => (get_safe('fromdate') != '')?date2mysql(get_safe('fromdate')):NULL,
                'akhir' => (get_safe('todate') != '')?date2mysql(get_safe('todate')):NULL,
                'unit' => (get_safe('unit') != '')?get_safe('unit'):NULL
            );
        $between = "<br/>";
        if( ($param['awal'] != NULL) & ($param['akhir'] != NULL) ){
            $between .=  indo_tgl($param['awal'])." s.d ".indo_tgl($param['akhir']);
        }
  
        $data = $this->m_pelayanan->load_data_morbiditas($param);
        $result['nama'] = array();
        $result['jumlah'] = array();
        $result['title'] = "Laporan Morbiditas 10 Penyakit <br/>".$param['unit'].$between;
        foreach ($data as $key => $value) {
            $result['nama'][] = $value->nama;
            $result['jumlah'][] = (int)$value->jumlah;
        }
        die(json_encode($result));
    }

    function vital_sign_save($id_pelayanan){
        $this->db->trans_begin();

        
        if ($id_pelayanan !== '') {
            $this->m_pelayanan->vital_sign_save($id_pelayanan);
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $status = FALSE;
        } else {
            $this->db->trans_commit();
            $status = TRUE;
        }
        echo json_encode(array('status' => $status, 'id_pelayanan'=>$id_pelayanan));
    }

    function vital_sign_list($id_pk){
        $data = $this->m_pelayanan->get_vital_sign($id_pk);
        $result['data'] = $data;
        $tensi_s = array(); $tensi_d = array(); $nadi = array(); $suhu = array(); $nafas = array();

        foreach ($data as $key => $value) {
             $result['waktu'][] = ($value->waktu != null)?datetimefmysql($value->waktu, true):'';

             $x = explode('/', $value->tensi);
             if (sizeof($x) > 1) {
                $tensi_s[] = (double)$x[0];
                $tensi_d[] = (double)$x[1];
             }else{
                $tensi_s[] = 0;
                $tensi_d[] = 0;
             }
             

             $nadi[] = (int)$value->nadi;
             $suhu[] = (int)$value->suhu;
             $nafas[] = (int)$value->nafas;
        }

        $result['title_tensi'] = "Grafik Status Tekanan Darah Pasien";
        $result['title_nadi'] = "Grafik Status Denyut Nadi Pasien";
        $result['title_suhu'] = "Grafik Status Suhu Tubuh Pasien";
        $result['title_nafas'] = "Grafik Status Nafas Pasien";

        $result['tensi'] = array(
                array('name'=>'sistole', 'stack'=>'sistole', 'data'=>$tensi_s),
                array('name'=>'diastole', 'stack'=>'diastole', 'data'=>$tensi_d),
            );
        $result['nadi'] = array(
                array('type'=>'spline', 'name'=>'Nadi', 'data'=>$nadi),
            );
        $result['suhu'] = array(
                array('type'=>'spline', 'name'=>'Suhu Tubuh', 'data'=>$suhu),
            );
        $result['nafas'] = array(
                array('type'=>'spline', 'name'=>'Nafas', 'data'=>$nafas),
            );
        
        echo json_encode($result);
    }

    function delete_vital_sign($id){
        $this->db->where('id',$id)->delete('vital_sign');
    }


    function get_available_bed(){
        $id_bangsal = get_safe('id_bangsal_cari');
        $kelas = get_safe('kelas');
        $data['bed'] = $this->m_pelayanan->get_available_bed($id_bangsal, $kelas);
        $this->load->view('pelayanan/available_bed_list', $data);
    }
    
    function print_no_receipt($id_resep) {
        $data['detail'] = $this->m_pelayanan->print_no_receipt($id_resep)->row();
        $this->load->view('pelayanan/cetak_receipt', $data);
    }

    function get_list_pelayanan_kunjungan($no_daftar){
        $data = $this->m_pelayanan->get_pelayanan_kunjungan_list($no_daftar);
        die(json_encode($data));
    }

    function indikator_pelayanan_rs(){
        $data['title'] = 'Indikator Pelayanan Rumah Sakit';
        $this->load->view('pelayanan/indikator_pelayanan_rs', $data);
    }


    function load_indikator_pelayanan_rs(){
         $param = array(
                    'awal' => get_safe('awal'),
                    'akhir' => get_safe('akhir'),
                    'kondisi' => ''
                );
        $bed = $this->m_pelayanan->get_jumlah_layanan_irna();
        $hari_perawatan =  $this->m_pelayanan->get_jumlah_hari_perawatan($param);
        $jumlah_pasien = $this->m_pelayanan->get_jumlah_pasien_irna($param);


        $nilai_bor = ($hari_perawatan['hari'] / ($bed * $hari_perawatan['periode'])) * 100;
        
        if($jumlah_pasien->jumlah != 0){
            $nilai_toi = (($bed * $hari_perawatan['periode']) - $hari_perawatan['hari'] ) / $jumlah_pasien->jumlah ;
        }else{
            $nilai_toi = 0;
        }
        

        $param['kondisi'] = 'Meninggal';
        $pasien_mati = $this->m_pelayanan->get_jumlah_pasien_irna($param);
        $ndr_pasien_mati = $this->m_pelayanan->get_jumlah_pasien_irna($param, 'ndr');

        $data['bor'] = (Object) array('hari_perawatan' => $hari_perawatan['hari'], 'bed' => $bed, 'nilai'=>$nilai_bor);
        $data['alos'] = (Object) array('lama_inap' => $jumlah_pasien->lama_inap, 'jumlah'=>$jumlah_pasien->jumlah);
        $data['bto'] = (Object) array('bed' => $bed, 'jumlah'=>$jumlah_pasien->jumlah);
        $data['toi'] = (Object) array('nilai' => $nilai_toi,'bed' => $bed, 'jumlah'=>$jumlah_pasien->jumlah, 'periode' => $hari_perawatan['periode']);
        $data['ndr'] = (Object) array('jumlah'=>$jumlah_pasien->jumlah, 'jumlah_mati' => $ndr_pasien_mati->jumlah);
        $data['gdr'] = (Object) array('jumlah'=>$jumlah_pasien->jumlah, 'jumlah_mati' => $pasien_mati->jumlah);
        
        $data['awal'] = get_safe('awal');
        $data['akhir'] = get_safe('akhir');
        $this->load->view('pelayanan/indikator_pelayanan_rs_list', $data);
    }

    function bor_rawat_inap(){
        // Update BOR
        $this->m_pelayanan->save_bor_harian();

        $data['title'] = 'BOR Rawat Inap Per Unit';
        $data['layanan'] = $this->m_referensi->get_bangsal_irna();
        $this->load->view('pelayanan/bor_rawat_inap', $data);
    }

    function get_bor_list($page){
        $limit = 15;
        $search = array(
            'awal' => date2mysql(get_safe('awal')),
            'akhir' => date2mysql(get_safe('akhir')),
            'unit' => get_safe('layanan')
        );
        if ($page == 'undefined') {
            $page = 1;
        }
        $start = ($page - 1) * $limit;
        $data['page'] = $page;
        $data['limit'] = $limit;
        $query = $this->m_pelayanan->bor_rawat_inap_data($limit, $start, $search);
        $data['jumlah'] = $query['jumlah'];
        $data['bor'] = $query['data'];
        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, 1, '');
        $data['bed'] = $this->m_pelayanan->get_jumlah_layanan_irna();
        $this->load->view('pelayanan/bor_rawat_inap_list', $data);
    }
    
    function resep_jual() {
        $data['title'] = 'Resep dan Penjualan';
        $this->load->view('pelayanan/resep-jual', $data);
    }
    
    function resep() {
        $data['asuransi'] = $this->m_referensi->load_data_asuransi()->result();
        $data['biaya_apoteker'] = $this->m_referensi->load_data_tarif()->result();
        $this->load->view('pelayanan/resep', $data);
    }
    
    function get_list_data_resep($limit, $page, $search) {
        if ($page == 'undefined') {
            $page = 1;
        }
        //$str = 'null';
        $start = ($page - 1) * $limit;
        $data['page'] = $page;
        $data['limit'] = $limit;
        $data['auto'] = $start+1;
        $query = $this->m_pelayanan->get_data_resep($limit, $start, $search);
        $data['list_data'] = $query['data'];
        $data['jumlah'] = $query['jumlah'];
        
        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, 1, null);
        return $data;
    }
    
    function manage_resep($status, $page = null) {
        $limit = 15;
        switch ($status) {
            case 'list':
                $search['key'] = $_GET['search'];
                $search['id']  = $_GET['id'];
                $data = $this->get_list_data_resep($limit, $page, $search);
                $this->load->view('pelayanan/resep-list', $data);
                break;
            case 'save': 
                $this->m_pelayanan->save_resep();
                $data = $this->get_list_data_resep($limit, 1, null);
                $this->load->view('pelayanan/resep-list', $data);
                break;
            case 'cetak_copy':
                $param['id'] = $_GET['id']; // id resep
                $data = $this->get_list_data_resep($limit, 1, $param);
                $this->load->view('pelayanan/resep-cetak', $data);
                break;
            case 'cetak_kitir':
                $id = $_GET['id']; // id resep
                $data['list_data'] = $this->m_pelayanan->get_list_data_kitir($id)->result();
                $this->load->view('pelayanan/resep-cetak-kitir', $data);
                break;
            case 'edit': 
                $param['id'] = $_GET['id']; // id resep
                $data = $this->get_list_data_resep($limit, 1, $param);
                $this->load->view('pelayanan/resep-edit', $data);
                break;
            case 'get_detail_resep':
                $id = $_GET['id'];
                $data = $this->m_pelayanan->get_detail_resep($id)->row();
                die(json_encode($data));
                break;
            case 'get_detail_resep_penjualan':
                $id = $_GET['id'];
                $data['list_data'] = $this->m_pelayanan->get_data_resep_penjualan($id)->result();
                $data['cek'] = $this->m_pelayanan->cek_ketersediaan_resep($id)->row();
                $this->load->view('pelayanan/penjualan-list-resep', $data);
                break;
            case 'delete': 
                $this->m_pelayanan->delete_resep($_GET['id']);
                break;
            
        }
    }
    
    /*Penjualan Resep*/
    function penjualan_resep() {
        $this->load->view('pelayanan/penjualan-resep');
    }
    
    function get_list_data_penjualan($limit, $page, $search) {
        if ($page == 'undefined') {
            $page = 1;
        }
        //$str = 'null';
        $start = ($page - 1) * $limit;
        $data['page'] = $page;
        $data['limit'] = $limit;
        $data['auto'] = $start+1;
        $query = $this->m_pelayanan->get_data_penjualan($limit, $start, $search);
        $data['list_data'] = $query['data'];
        $data['jumlah'] = $query['jumlah'];
        
        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, 1, null);
        return $data;
    }
    
    function manage_penjualan($status, $page = null) {
        $limit = 15;
        switch ($status) {
            case 'list':
                //$search['key'] = $_GET['search'];
                $search['id']  = $_GET['id'];
                $data = $this->get_list_data_penjualan($limit, $page, $search);
                $this->load->view('pelayanan/penjualan-resep-list', $data);
                break;
            case 'save': 
                $this->m_pelayanan->save_penjualan();
                $data = $this->get_list_data_penjualan($limit, 1, null);
                $this->load->view('pelayanan/penjualan-resep-list', $data);
                break;
            case 'delete': 
                $this->m_pelayanan->delete_penjualan($_GET['id']);
                break;
            case 'cetak': 
                
                break;
            case 'get_data_penjualan':
                $data = $this->m_pelayanan->get_data_penjualan_edit($_GET['id'])->result();
                die(json_encode($data));
                break;
            
        }
    }
    
    /*PENJUALAN NON RESEP*/
    function penjualan_non_resep() {
        $data['asuransi'] = $this->m_referensi->load_data_asuransi()->result();
        $this->load->view('pelayanan/penjualan-non-resep', $data);
    }
    
    function get_list_data_penjualan_non_resep($limit, $page, $search) {
        if ($page == 'undefined') {
            $page = 1;
        }
        //$str = 'null';
        $start = ($page - 1) * $limit;
        $data['page'] = $page;
        $data['limit'] = $limit;
        $data['auto'] = $start+1;
        $query = $this->m_pelayanan->get_data_penjualan_non_resep($limit, $start, $search);
        $data['list_data'] = $query['data'];
        $data['jumlah'] = $query['jumlah'];
        
        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, 1, null);
        return $data;
    }
    
    function manage_penjualan_non_resep($status, $page = null) {
        $limit = 15;
        switch ($status) {
            case 'list':
                //$search['key'] = $_GET['search'];
                $search['id']  = $_GET['id'];
                $data = $this->get_list_data_penjualan_non_resep($limit, $page, $search);
                $this->load->view('pelayanan/penjualan-non-resep-list', $data);
                break;
            case 'save': 
                $data = $this->m_pelayanan->save_penjualan_non_resep();
                die(json_encode($data));
                break;
            case 'delete': 
                $this->m_pelayanan->delete_penjualan_non_resep($_GET['id']);
                break;
            case 'cetak': 
                
                break;
            case 'get_data_penjualan_non_resep':
                $data = $this->m_pelayanan->get_data_penjualan_non_resep_edit($_GET['id'])->result();
                die(json_encode($data));
                break;
            
        }
    }
    
    function get_kunjungan_pelayanan($id_pasien) {
        $data = $this->m_pelayanan->get_kunjungan_pelayanan($id_pasien)->row();
        die(json_encode($data));
    }
}
?>