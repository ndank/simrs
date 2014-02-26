<?php

class Referensi extends CI_Controller {

    function __construct() {
        parent::__construct();
        
        $this->load->model('m_referensi');
        $this->load->model('m_inv_autocomplete');
        date_default_timezone_set('Asia/Jakarta');
        
    }

    function ganti_password() {
        $data['title'] = "Ganti Password";
        $data['user'] = $this->session->userdata('user');
        $this->load->view('ganti_password', $data);
    }

    function cek_password() {
        $pwd = md5(post_safe('password'));

        $id = $this->session->userdata('id_user');

        $pwd_cek = $this->m_referensi->get_user_detail($id)->password;
        $status = false;
        if ($pwd == $pwd_cek) {
            $status = true;
        } else {
            $status = false;
        }
        die(json_encode(array('status' => $status)));
    }

    function simpan_password() {
        $pwd = md5(post_safe('password'));

        $id = $this->session->userdata('id_user');

        $data = array(
            'password' => $pwd
        );
        $this->m_referensi->ubah_password($id, $data);
    }

    /* Referensi Tempat Tidur */

    function tempat_tidur() {
        $data['title'] = "Tempat Tidur";
        $data['bangsal'] = $this->m_referensi->unit_get_data();
        $data['kelas'] = $this->m_referensi->kelas_tarif_get_data();
        $this->load->view('referensi/tempat_tidur/bed', $data);
    }

    function get_tempat_tidur_list($page, $search) {
        $limit = 10;
        $q = '';
        if ($page == 'undefined') {
            $page = 1;
        }
        if (isset($search['id'])) {
            $q = " and t.id = '" . $search['id'] . "'";
        }

        if (isset($search['bangsal']) && ($search['bangsal'] != '')) {
            $q .= " and u.id = '".$search['bangsal']."'";
        }

        if (isset($search['kelas']) && ($search['kelas'] != '')) {
            $q .= " and t.kelas = '".$search['kelas']."'";   
        }

        if (isset($search['']) && ($search[''] != '')) {
            $q .= " and t.nomor = '".$search['nomor']."'";
        }

        $sql = "select t.id,t.unit_id,t.kelas,  u.nama, t.nomor, t.status FROM tt t
            join unit u on(t.unit_id = u.id) where t.id is not null $q order by u.nama, t.kelas, t.nomor";
        $start = ($page - 1) * $limit;
        $lm = " limit $start , $limit";

        $data['page'] = $page;
        $data['limit'] = $limit;
        $data['hasil'] = $this->db->query($sql . $lm)->result();
        $data['jumlah'] = $this->db->query($sql)->num_rows();
        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, 1, '');
        return $data;
    }

    function tempat_tidur_manage() {
        $act = get_safe('act');
        $searchnull = 'null';

        if ($act == 'list_bed') {
            $page = get_safe('page');
            $search =  array(
                'bangsal' => get_safe('bangsal'),
                'kelas' => get_safe('kelas'),
                'nomor' => get_safe('nomor')
            );
            $data = $this->get_tempat_tidur_list($page, $search);
            $this->load->view('referensi/tempat_tidur/list_bed', $data);
        } else if ($act == 'delete_bed') {
            $page = get_safe('page');
            $sql = "delete from tt where id = '" . get_safe('id') . "'";
            $this->db->query($sql);

            $data = $this->get_tempat_tidur_list($page, $searchnull);
            if ($data['hasil'] == null) {
                $data = $this->get_tempat_tidur_list(1, $searchnull);
            }
            $this->load->view('referensi/tempat_tidur/list_bed', $data);
        } else if ($act == 'add_bed') {
            $page = get_safe('page');
            $unit = post_safe('bangsal');
            $kelas = post_safe('kelas');
            $nomor = post_safe('nomor');

            $cek_bed = $this->db->query("select * from tt where 
                unit_id = '$unit' 
                and kelas = '$kelas' 
                and nomor = '$nomor'")->num_rows();

            if($cek_bed < 1){
                $tarif = currencyToNumber(post_safe('tarif'));
                $sql = "insert into tt values('','$unit','$kelas','$nomor', 'Tersedia')";
                $insert = $this->db->query($sql);
            }else{
                $insert = false;
            }
            die(json_encode(array('insert'=>$insert, 'id'=> $this->db->insert_id())));
            
        } else if ($act == 'get_bed') {
            $search['id'] = get_safe('id');
            $data = $this->get_tempat_tidur_list(1, $search);
            $this->load->view('referensi/tempat_tidur/list_bed', $data);
        } else if ($act == 'edit_bed') {
            $page = get_safe('page');
            $id = post_safe('hd_id');
            $unit = post_safe('bangsal');
            $kelas = post_safe('kelas');
            $nomor = post_safe('nomor');

            $cek_bed = $this->db->query("select * from tt where 
                unit_id = '$unit' 
                and kelas = '$kelas' 
                and nomor = '$nomor'")->num_rows();

            
            $search['id'] = $id;
            if($cek_bed < 1){
                $sql = "update tt set unit_id='" . $unit . "', kelas='" . $kelas . "', nomor='" . $nomor . "' where id = '" . $id . "' ";
                $insert = $this->db->query($sql);
            }else{
                $insert = false;
            }
            
            die(json_encode(array('insert'=>$insert, 'id'=> $id)));
        }
    }


    /* Referensi Tempat Tidur */

    /* Masterdata Unit */

    function master_unit() {
        $data['title'] = "Unit";
        $this->load->view('referensi/unit/unit', $data);
    }

    function get_unit_list($page, $search = null) {
        $limit = 15;
        $q = '';
        if ($page == 'undefined') {
            $page = 1;
        }
        if (isset($search['id'])) {
            $q = " where id = '" . $search['id'] . "'";
        }

        $sql = "select * from unit $q order by nama";
        $start = ($page - 1) * $limit;
        $lm = " limit $start , $limit";
        //echo $sql;
        $data['page'] = $page;
        $data['limit'] = $limit;
        $data['unit'] = $this->db->query($sql . $lm)->result();
        $data['jumlah'] = $this->db->query($sql)->num_rows();
        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, 1, $search);
        return $data;
    }

    function master_unit_list($page) {
        $data = $this->get_unit_list($page);
        $this->load->view('referensi/unit/list_unit', $data);
    }

    function master_unit_search() {
        $unit = get_safe('unit');
        $count = $this->m_referensi->cek_unit($unit);

        if ($count->jumlah == 0) {
            $status = true;
        } else {
            $status = false;
        }
        die(json_encode(array('status' => $status)));
    }

    function master_unit_save() {
        $unit = post_safe('unit');
        $id = $this->m_referensi->add_unit($unit);
        die(json_encode(array('id'=>$id)));
    }

    function master_unit_delete($page) {
        $id = get_safe('id');
        $this->m_referensi->delete_unit($id);
        $data = $this->get_unit_list($page);
        if ($data['unit'] == NULL) {
            $data = $this->get_unit_list(1);
        }
        $this->load->view('referensi/unit/list_unit', $data);
    }

    function master_unit_edit() {
        $id = post_safe('id');
        $param = array(
            'id' => post_safe('id'),
            'nama' => post_safe('unit')
        );
        $this->m_referensi->edit_unit($param);
        die(json_encode(array('id'=>$id)));
    }

    function master_unit_get_data(){
        $search['id'] = get_safe('id');
        $data = $this->get_unit_list(1, $search);
        $this->load->view('referensi/unit/list_unit', $data);
    }

    /* Masterdata Unit */

    /* Produk Asuransi */

    function produk_asuransi() {
        $data['title'] = "Produk Asuransi";
        $this->load->view('referensi/asuransi/produk-asuransi', $data);
    }

    function produk_asuransi_list($page) {
        $search['nama'] = get_safe('nama');
        $search['id_perusahaan'] = get_safe('id_ap');
        $search['perusahaan'] = get_safe('perusahaan');
        $data = $this->produk_asuransi_data($page, $search);
        $data = array_merge($data, $search);
        $this->load->view('referensi/asuransi/list_produk_asuransi', $data);
    }

    function produk_asuransi_data($page, $search) {
        $limit = 15;

        if ($page == 'undefined') {
            $page = 1;
        }
        $start = ($page - 1) * $limit;
        $data['page'] = $page;
        $data['limit'] = $limit;
        $query = $this->m_referensi->get_produk_asuransi_data($limit, $start, $search);
        $data['jumlah'] = $query['jumlah'];
        $data['asuransi'] = $query['data'];

        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, 1, '');
        return $data;
    }

    function produk_asuransi_add($page) {

        $dat = array(
            'id' => post_safe('id_produk'),
            'nama' => post_safe('nama'),
            'reimbursement' => post_safe('reimbursement'),
            'reimbursement_rupiah' => str_replace('.', '', post_safe('reimbursement_rp'))
        );
        if (post_safe('id_ap') == '') {
            // Perusahaan belum ada
            $relasi = array(
                'nama' => post_safe('perusahaan'),
                'relasi_instansi_jenis_id' => 4
            );
            $dat['relasi_instansi_id'] = $this->m_referensi->add_relasi_instansi_data($relasi);
        } else {
            $dat['relasi_instansi_id'] = post_safe('id_ap');
        }
        $id = $this->m_referensi->add_produk_asuransi_data($dat);
        die(json_encode(array('id'=> $id)));
    }

    function get_relasi_instansi() {
        $q = get_safe('q');
        $rows = $this->m_referensi->relasi_instansi_data($q);
        die(json_encode($rows));
    }

    function get_produk_asuransi_last_no() {
        $no = $this->m_referensi->produk_asuransi_last_no();
        die(json_encode(array('no' => $no)));
    }

    function produk_asuransi_delete($page) {
        $id = get_safe('id');
        $this->m_referensi->delete_produk_asuransi($id);
        $this->produk_asuransi_list($page);
    }

    function produk_asuransi_edit($page) {
        $dat = array(
            'id' => post_safe('id_produk'),
            'nama' => post_safe('nama'),
            'relasi_instansi_id' => post_safe('id_ap'),
            'reimbursement' => post_safe('reimbursement'),
            'reimbursement_rupiah' => str_replace('.', '', post_safe('reimbursement_rp'))
        );
        $id = $dat['id'];
        $this->m_referensi->edit_produk_asuransi_data($dat);
        die(json_encode(array('id'=> $id)));
    }

    function produk_asuransi_get_data(){
        $search['id'] = get_safe('id');
        $data = $this->produk_asuransi_data(1, $search);
        $this->load->view('referensi/asuransi/list_produk_asuransi', $data);
    }

    function produk_asuransi_cek() {

        $prov = array(
            'nama' => get_safe('nama'),
            'relasi' => get_safe('relasi')
        );
        $cek = $this->m_referensi->produk_cek_data($prov);
        die(json_encode(array('status' => $cek)));
    }

    /* Produk Asuransi */


    /* Data Wilayah */

    function data_wilayah() {
        $data['title'] = "Wilayah";
        $data['provinsi'] = $this->m_referensi->provinsi_get_data(0, 15, 'null');
        $this->load->view('referensi/wilayah/wilayah', $data);
    }

    function data_provinsi() {
        $this->load->view('referensi/wilayah/provinsi');
    }

    function get_pro_list($limit, $page, $search) {
        if ($page == 'undefined') {
            $page = 1;
        }
        $start = ($page - 1) * $limit;
        $data['page'] = $page;
        $data['limit'] = $limit;
        $query = $this->m_referensi->provinsi_get_data($limit, $start, $search);
        $data['jumlah'] = $query['jumlah'];
        $data['provinsi'] = $query['data'];
        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, 1, '');
        return $data;
    }

    function manage_provinsi($mode, $page = null) {
        $limit = 15;
        $add = array(
            'nama' => post_safe('provinsi'),
            'kode' => post_safe('kode')
        );
        $searchnull = 'null';
        switch ($mode) {
            case 'list':
                $search['provinsi'] = get_safe('provinsi');
                $search['kode'] = get_safe('kode');

                $data = $this->get_pro_list($limit, $page, $search);
                $data['nama'] = $search['provinsi'];
                $data['kode'] = $search['kode'];
                $this->load->view('referensi/wilayah/list_pro', $data);
                break;
            case 'add':
                $id = $this->m_referensi->provinsi_add_data($add);
                die(json_encode(array('id'=> $id)));
                break;

            case 'edit':
                $add['id'] = post_safe('id');
                $this->m_referensi->provinsi_edit_data($add);
                die(json_encode(array('id'=> $add['id'])));
                break;

            case 'delete':
                $id = get_safe('id');
                $this->m_referensi->provinsi_delete_data($id);
                $data = $this->get_pro_list($limit, $page, null);
                if ($data['provinsi'] == null) {
                    $data = $this->get_pro_list($limit, $page - 1, null);
                }
                $this->load->view('referensi/wilayah/list_pro', $data);
                break;
            case 'cek':
                $prov = array(
                    'nama' => get_safe('provinsi')
                );
                $cek = $this->m_referensi->provinsi_cek_data($prov);
                die(json_encode(array('status' => $cek)));

                break;
            case 'get_data':
                $search['id'] = get_safe('id');
                $data = $this->get_pro_list($limit, 1, $search);
                $this->load->view('referensi/wilayah/list_pro', $data);
                break;

            default:
                break;
        }
    }

    function get_provinsi() {
        $q = get_safe('q');
        $rows = $this->m_referensi->provinsi_data($q);
        die(json_encode($rows));
    }

    function data_kabupaten() {
        $this->load->view('referensi/wilayah/kabupaten');
    }

    function get_kab_list($limit, $page, $search) {
        if ($page == 'undefined') {
            $page = 1;
        }
        $start = ($page - 1) * $limit;
        $data['page'] = $page;
        $data['limit'] = $limit;
        $query = $this->m_referensi->kabupaten_get_data($limit, $start, $search);
        $data['jumlah'] = $query['jumlah'];
        $data['kabupaten'] = $query['data'];
        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, 2, '');
        return $data;
    }

    function manage_kabupaten($mode, $page = null) {
        $limit = 15;
        $add = array(
            'nama' => post_safe('kabupaten'),
            'provinsi_id' => post_safe('idprovinsikab'),
            'kode' => post_safe('kodekab')
        );
        $searchnull = 'null';
        switch ($mode) {
            case 'list':
                $search['idprovinsikab'] = get_safe('idprovinsikab');
                $search['provinsi'] = get_safe('provinsi');
                $search['kabupaten'] = get_safe('kabupaten');
                $search['kode'] = get_safe('kodekab');

                $data = $this->get_kab_list($limit, $page, $search);
                $data['nama'] = $search['kabupaten'];
                $data['provinsi'] = $search['provinsi'];
                $data['kode'] = $search['kode'];
                $this->load->view('referensi/wilayah/list_kab', $data);
                break;
            case 'add':
                $id = $this->m_referensi->kabupaten_add_data($add);
                die(json_encode(array('id'=> $id)));
                break;

            case 'edit':
                $add['id'] = post_safe('id_kab');
                $this->m_referensi->kabupaten_edit_data($add);
                die(json_encode(array('id'=> $add['id'])));
                break;

            case 'delete':
                $id = get_safe('id');
                $this->m_referensi->kabupaten_delete_data($id);
                break;
            case 'cek':
                $kab = array(
                    'nama' => get_safe('kabupaten'),
                    'provinsi_id' => get_safe('provid')
                );
                $cek = $this->m_referensi->kabupaten_cek_data($kab);
                die(json_encode(array('status' => $cek)));

                break;

            case 'get_data':
                $search['id'] = get_safe('id');
                $data = $this->get_kab_list($limit, 1, $search);
                $this->load->view('referensi/wilayah/list_kab', $data);
                break;

            default:
                break;
        }
    }

    function get_kabupaten() {
        $q = get_safe('q');
        $rows = $this->m_referensi->kabupaten_data($q);
        die(json_encode($rows));
    }

    function data_kecamatan() {
        $this->load->view('referensi/wilayah/kecamatan');
    }

    function get_kec_list($limit, $page, $search) {
        if ($page == 'undefined') {
            $page = 1;
        }
        $start = ($page - 1) * $limit;
        $data['page'] = $page;
        $data['limit'] = $limit;
        $query = $this->m_referensi->kecamatan_get_data($limit, $start, $search);
        $data['jumlah'] = $query['jumlah'];
        $data['kecamatan'] = $query['data'];
        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, 3, '');
        return $data;
    }

    function manage_kecamatan($mode, $page = null) {
        $limit = 15;
        $add = array(
            'nama' => post_safe('kecamatan'),
            'kabupaten_id' => post_safe('idkabupatenkec'),
            'kode' => post_safe('kodekec')
        );
        $searchnull = 'null';
        switch ($mode) {
            case 'list':
                $search['idkabupatenkec'] = get_safe('idkabupatenkec');
                $search['kecamatan'] = get_safe('kecamatan');
                $search['kabupaten'] = get_safe('kabupaten');
                $search['kode'] = get_safe('kodekec');

                $data = $this->get_kec_list($limit, $page, $search);
                $data['nama'] = $search['kecamatan'];
                $data['kabupaten'] = $search['kabupaten'];
                $data['kode'] = $search['kode'];
                $this->load->view('referensi/wilayah/list_kec', $data);
                break;
            case 'add':
                $id = $this->m_referensi->kecamatan_add_data($add);
                die(json_encode(array('id'=> $id)));
                break;

            case 'edit':
                $add['id'] = post_safe('id_kec');
                $this->m_referensi->kecamatan_edit_data($add);
                die(json_encode(array('id'=> $add['id'])));
                break;

            case 'delete':
                $id = get_safe('id');
                $this->m_referensi->kecamatan_delete_data($id);
                break;
            case 'cek':
                $kab = array(
                    'nama' => get_safe('kecamatan'),
                    'kabupaten_id' => get_safe('kabid')
                );
                $cek = $this->m_referensi->kecamatan_cek_data($kab);
                die(json_encode(array('status' => $cek)));

                break;

            case 'get_data':
                $search['id'] = get_safe('id');
                $data = $this->get_kec_list($limit, 1, $search);
                $this->load->view('referensi/wilayah/list_kec', $data);
                break;

            default:
                break;
        }
    }

    function get_kecamatan() {
        $q = get_safe('q');
        $rows = $this->m_referensi->kecamatan_data($q);
        die(json_encode($rows));
    }

    function get_kelurahan() {
        $q = get_safe('q');
        $rows = $this->m_referensi->kelurahan_data($q);
        die(json_encode($rows));
    }

    function data_kelurahan() {
        $this->load->view('referensi/wilayah/kelurahan');
    }

    function get_kel_list($limit, $page, $search) {
        if ($page == 'undefined') {
            $page = 1;
        }
        $start = ($page - 1) * $limit;
        $data['page'] = $page;
        $data['limit'] = $limit;
        $query = $this->m_referensi->kelurahan_get_data($limit, $start, $search);
        $data['jumlah'] = $query['jumlah'];
        $data['kelurahan'] = $query['data'];
        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, 4, '');
        return $data;
    }

    function manage_kelurahan($mode, $page = null) {
        $limit = 15;
        $add = array(
            'nama' => post_safe('kelurahan'),
            'kecamatan_id' => (post_safe('idkecamatankel') == '') ? NULL : post_safe('idkecamatankel'),
            'kode' => post_safe('kodekel')
        );
        $searchnull = 'null';
        switch ($mode) {
            case 'list':
                $search['idkecamatankel'] = get_safe('idkecamatankel');
                $search['kecamatan'] = get_safe('kecamatan');
                $search['kelurahan'] = get_safe('kelurahan');
                $search['kode'] = get_safe('kodekel');

                $data = $this->get_kel_list($limit, $page, $search);
                $data['nama'] = $search['kelurahan'];
                $data['kecamatan'] = $search['kecamatan'];
                $data['kode'] = $search['kode'];
                $this->load->view('referensi/wilayah/list_kel', $data);
                break;
            case 'add':
                $id = $this->m_referensi->kelurahan_add_data($add);
                die(json_encode(array('id'=> $id)));
                break;

            case 'edit':
                $add['id'] = post_safe('id_kel');
                $this->m_referensi->kelurahan_edit_data($add);
                die(json_encode(array('id'=> $add['id'])));
                break;

            case 'delete':
                $id = get_safe('id');
                $this->m_referensi->kelurahan_delete_data($id);
                break;
            case 'cek':
                $kab = array(
                    'nama' => get_safe('kelurahan'),
                    'kecamatan_id' => get_safe('kecid')
                );
                $cek = $this->m_referensi->kelurahan_cek_data($kab);
                die(json_encode(array('status' => $cek)));

                break;

            case 'get_data':
                $search['id'] = get_safe('id');
                $data = $this->get_kel_list($limit, 1, $search);
                $this->load->view('referensi/wilayah/list_kel', $data);
                break;

            default:
                break;
        }
    }

    /* Data Wilayah */
    /* Relasi Instansi */

    function instansi_relasi() {
        $data['title'] = 'Data Perusahaan';
        $data['jenis'] = null;
        $jenis[''] = 'Pilih Jenis';
        $arr = $this->m_referensi->relasi_instansi_jenis_get_data();
        foreach ($arr as $value) {
            $jenis[$value->id] = $value->nama;
        }
        $data['jenis'] = $jenis;

        $this->load->view('referensi/instansi_relasi/instansi', $data);
    }

    function get_instansi_list($limit, $page, $search) {
        if ($page == 'undefined') {
            $page = 1;
        }
        $start = ($page - 1) * $limit;
        $data['page'] = $page;
        $data['limit'] = $limit;
        $query = $this->m_referensi->instansi_get_data($limit, $start, $search);
        $data['jumlah'] = $query['jumlah'];
        $data['instansi'] = $query['data'];
        $str = '';
       

        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, 1, $str);
        return $data;
    }

    function manage_instansi($mode, $page = null) {
        $limit = 15;
        $add = array(
            'nama' => post_safe('nama'),
            'alamat' => preg_replace('~[\r\n]+~', ' ', post_safe('alamat')),
            'kelurahan_id' => (post_safe('id_kelurahan') == '') ? NULL : post_safe('id_kelurahan'),
            'telp' => post_safe('telp'),
            'fax' => post_safe('fax'),
            'email' => post_safe('email'),
            'website' => post_safe('website'),
            'relasi_instansi_jenis_id' => (post_safe('jenis') == '') ? NULL : post_safe('jenis'),
            'diskon_penjualan' => post_safe('disk_penjualan')
        );
        $searchnull = 'null';
        switch ($mode) {
            case 'list':
                $search['nama'] = get_safe('nama');
                $search['alamat'] = get_safe('alamat');
                $search['id_kelurahan'] = get_safe('id_kelurahan');
                $search['kelurahan'] = get_safe('kelurahan');
                $search['jenis'] = get_safe('jenis');
                $data = $this->get_instansi_list($limit, $page, $search);
                $data = array_merge($data, $search);

                $jenis = array();
                $arr = $this->m_referensi->relasi_instansi_jenis_get_data();
                foreach ($arr as $value) {
                    $jenis[$value->id] = $value->nama;
                }
                $data['jenis_list'] = $jenis;

                $this->load->view('referensi/instansi_relasi/list_instansi', $data);
                break;
            case 'add':
                $id = $this->m_referensi->instansi_add_data($add);
                die(json_encode(array('id'=> $id)));
                break;

            case 'edit':
                $add['id'] = post_safe('id');
                $this->m_referensi->instansi_edit_data($add);
                die(json_encode(array('id'=> $add['id'])));
                break;

            case 'delete':
                $id = get_safe('id');
                $this->m_referensi->instansi_delete_data($id);
                break;

            case 'cek':
                $ins = array(
                    'instansi' => get_safe('instansi')
                );
                $cek = $this->m_referensi->instansi_cek_data($ins);
                die(json_encode(array('status' => $cek)));

                break;
            case 'get_data':
                $search['id'] = get_safe('id');
                $data = $this->get_instansi_list($limit, 1, $search);
                $this->load->view('referensi/instansi_relasi/list_instansi', $data);
                break;

            default:
                break;
        }
    }

    /* Relasi Instansi */


    /* Barang */

    function barang() {
        $data = array();
        $data['title'] = 'Barang Perbekalan Farmasi';
        $this->load->view('referensi/barang/barang', $data);
    }

    function barang_non_obat() {
        $query = $this->m_referensi->kategori_barang_get_data(null, "Farmasi");
        $kat[''] = "Pilih Kategori";
        foreach ($query as $value) {
            if ($value->nama != 'Obat') {
                $kat[$value->id] = $value->nama;
            }
        }
        $data['kategori'] = $kat;
        $this->load->view('referensi/barang/non_obat', $data);
    }

    function barang_obat() {
        $data['satuan'] = $this->m_referensi->satuan_get_data(null);
        $data['sediaan'] = $this->m_referensi->sediaan_get_data(null);
        $data['admr'] = $this->m_referensi->adm_r_get_data(null);
        $data['perundangan'] = $this->m_referensi->perundangan_get_data(null);
        $this->load->view('referensi/barang/obat', $data);
    }

    function get_barang_list($limit, $page, $tab, $tipe, $search) {
        
        if ($page == 'undefined') {
            $page = 1;
        }
        $str = 'null';
        $start = ($page - 1) * $limit;
        $data['page'] = $page;
        $data['limit'] = $limit;
        $data['auto'] = $start;
        if (isset($search['id'])) {
            $query = $this->m_referensi->barang_get_data($limit, $start, $tipe, $search['id'], null, null, null);
        } else {
            $query = $this->m_referensi->barang_get_data($limit, $start, $tipe, null, isset($search['nama'])?$search['nama']:null, isset($search['id_pabrik'])?$search['id_pabrik']:'',isset($search['kategori'])?$search['kategori']:'' ,$search, null);
        }

        $data['barang'] = $query['data'];
        $data['jumlah'] = $query['jumlah'];

        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, $tab, $str);
        return $data;
    }

    function manage_barang_non($mode, $page = null) {
        $limit = 15;
        $add = array(
            'nama' => post_safe('nama'),
            'barang_kategori_id' => (post_safe('kategori') == '') ? NULL : post_safe('kategori'),
            'pabrik_relasi_instansi_id' => (post_safe('id_pabrik') == '') ? NULL : post_safe('id_pabrik')
        );
        $searchnull = 'null';
        switch ($mode) {
            case 'list':
                $search['nama'] = get_safe('nama');
                $search['id_pabrik'] = get_safe('id_pabrik');
                $search['pabrik'] = get_safe('pabrik');
                $search['kategori'] = get_safe('kategori');

                $data = $this->get_barang_list($limit, $page, 1, 'Non Obat', $search);
                
                $data['key'] = $search['nama'];
                $data['pabrik'] = $search['pabrik'];
                $data['kategori'] = get_safe('nama_kategori');             

                $this->load->view('referensi/barang/list_non_obat', $data);
                break;
            case 'add':
                $insert['barang'] = $add;
                $id = $this->m_referensi->barang_add_data($insert, 'non obat');
                die(json_encode(array('id'=> $id)));
                break;

            case 'edit':
                $add['id'] = post_safe('id_barang');
                $update['barang'] = $add;
                $id = post_safe('id_barang');
                $this->m_referensi->barang_edit_data($update, 'non obat');
                die(json_encode(array('id'=> $id)));
                break;

            case 'delete':
                $id = get_safe('id');
                $this->m_referensi->barang_delete_data($id, 'non obat');
                break;
            case 'cek':
                $kab = array(
                    'nama' => get_safe('nama')
                );
                $cek = $this->m_referensi->barang_non_cek_data($kab);
                die(json_encode(array('status' => $cek)));

                break;

            case 'get_data':
                $search['id'] = get_safe('id');
                $data = $this->get_barang_list($limit, 1, 1, 'Non Obat', $search);
                $this->load->view('referensi/barang/list_non_obat', $data);
                break;

            default:
                break;
        }
    }
    
    function manage_barang_perbekalan_rt($mode, $page = null) {
        $limit = 15;
        $add = array(
            'nama' => post_safe('nama'),
            'pabrik_relasi_instansi_id' => (post_safe('id_pabrik') == '') ? NULL : post_safe('id_pabrik'),
            'barang_kategori_id' => (post_safe('kategori_rt') != '')?post_safe('kategori_rt'):NULL
        );
        $searchnull = 'null';
        switch ($mode) {
            case 'list':
                $search['nama'] = get_safe('nama');
                $search['id_pabrik'] = get_safe('id_pabrik');
                $search['pabrik'] = get_safe('pabrik');
                $search['kategori'] = get_safe('kategori_rt');

                $data = $this->get_barang_list($limit, $page, 1, 'Rt', $search);
                
                $data['key'] = $search['nama'];
                $data['pabrik'] = $search['pabrik'];
                $data['kategori'] = $search['kategori'];
                $query = $this->m_referensi->kategori_barang_get_data(null, "Rumah Tangga");
                $kat[''] = "Pilih Kategori";
                foreach ($query as $value) {
                    if ($value->nama != 'Obat') {
                        $kat[$value->id] = $value->nama;
                    }
                }
                $data['kategori_gizi'] = $kat;                

                $this->load->view('referensi/barang/list_non_obat', $data);
                break;
            case 'add':
              
                $insert['barang'] = $add;
                $id = $this->m_referensi->barang_add_data($insert, 'Rt');
                die(json_encode(array('id'=> $id)));
                break;

            case 'edit':
                /*
                 * Butuh data unit, user
                 */

                $add['id'] = post_safe('id_barang');
                $update['barang'] = $add;
                $id = post_safe('id_barang');
                $this->m_referensi->barang_edit_data($update, 'Rt');
                die(json_encode(array('id'=> $id)));
                break;

            case 'delete':
                $id = get_safe('id');
                $this->m_referensi->barang_delete_data($id, 'non obat');
                break;
            case 'cek':
                $kab = array(
                    'nama' => get_safe('nama')
                );
                $cek = $this->m_referensi->barang_non_cek_data($kab);
                die(json_encode(array('status' => $cek)));

                break;

            case 'get_data':
                $search['id'] = get_safe('id');
                $data = $this->get_barang_list($limit, 1, 1, 'Rt', $search);
                $this->load->view('referensi/barang/list_non_obat', $data);
                break;

            default:
                break;
        }
    }
    
    function manage_barang_perbekalan_gizi($mode, $page = null) {
        $limit = 15;
        $add = array(
            'nama' => post_safe('nama'),
            'pabrik_relasi_instansi_id' => (post_safe('id_pabrik') == '') ? NULL : post_safe('id_pabrik'),
            'barang_kategori_id' => (post_safe('kategori_gizi') != '')?post_safe('kategori_gizi'):NULL
        );
        $searchnull = 'null';
        switch ($mode) {
            case 'list':
                $search['nama'] = get_safe('nama');
                $search['id_pabrik'] = get_safe('id_pabrik');
                $search['pabrik'] = get_safe('pabrik');
                $search['kategori'] = get_safe('kategori_gizi');

                $data = $this->get_barang_list($limit, $page, 1, 'Gizi', $search);
                
                $data['key'] = $search['nama'];
                $data['pabrik'] = $search['pabrik'];
                $data['kategori'] = $search['kategori'];
                $query = $this->m_referensi->kategori_barang_get_data(null, "Gizi");
                $kat[''] = "Pilih Kategori";
                foreach ($query as $value) {
                    if ($value->nama != 'Obat') {
                        $kat[$value->id] = $value->nama;
                    }
                }
                $data['kategori_gizi'] = $kat;                

                $this->load->view('referensi/barang/list_non_obat', $data);
                break;
            case 'add':
                $insert['barang'] = $add;
                $id = $this->m_referensi->barang_add_data($insert, 'Gizi');
                die(json_encode(array('id'=> $id)));           
                break;

            case 'edit':
                $add['id'] = post_safe('id_barang');
                $update['barang'] = $add;
                $id = post_safe('id_barang');
                $this->m_referensi->barang_edit_data($update, 'Gizi');
                die(json_encode(array('id'=> $id)));
                break;

            case 'delete':
                $id = get_safe('id');
                $this->m_referensi->barang_delete_data($id, 'Gizi');
                break;

            case 'cek':
                $kab = array(
                    'nama' => get_safe('nama')
                );
                $cek = $this->m_referensi->barang_non_cek_data($kab);
                die(json_encode(array('status' => $cek)));

                break;

            case 'get_data':
                $search['id'] = get_safe('id');
                $data = $this->get_barang_list($limit, 1, 1, 'Gizi', $search);
                $this->load->view('referensi/barang/list_non_obat', $data);
                break;

            default:
                break;
        }
    }

    function manage_barang_obat($mode, $page = null) {
        $limit = 10;

        $add = array(
            'nama' => post_safe('nama'),
            'barang_kategori_id' => '1',
            'pabrik_relasi_instansi_id' => (post_safe('id_pabrik_obat') == '') ? NULL : post_safe('id_pabrik_obat')
        );
        $obat = array(
            'kekuatan' => (post_safe('kekuatan') != '') ? post_safe('kekuatan') : '1',
            'ven' => (post_safe('ven') != '')?post_safe('ven'):NULL,
            'high_alert' => post_safe('ha'),
            'perundangan' => post_safe('perundangan'),
            'satuan_id' => (post_safe('satuan') == '') ? NULL : post_safe('satuan'),
            'adm_r' => post_safe('admr'),
            'sediaan_id' => (post_safe('sediaan') == '') ? NULL : post_safe('sediaan'),
            'generik' => post_safe('generik'),
            'formularium' => post_safe('formularium'),
            'kandungan' => post_safe('kandungan'),
            'aturan_pakai' => post_safe('aturan_pakai'),
            'efek_samping' => post_safe('efek_samping'),
            'konsinyasi' => post_safe('konsinyasi')
        );
        $searchnull = 'null';
        switch ($mode) {
            case 'list':
                $search = array();
               
                if (get_safe('nama') != '') {
                    $search['nama'] = get_safe('nama');
                    
                }
                if (get_safe('kekuatan') != '') {
                    $search['kekuatan'] = get_safe('kekuatan');
                }

                if (get_safe('satuan') != '') {
                    $search['satuan'] = get_safe('satuan');
                }

                if (get_safe('sediaan') != '') {
                    $search['sediaan'] = get_safe('sediaan');
                }

                if (get_safe('ven') != '') {
                    $search['ven'] = get_safe('ven');
                }

                if (get_safe('perundangan') != '') {
                    $search['perundangan'] = get_safe('perundangan');
                }

                if (isset($_GET['generik']) and $_GET['generik'] != '') {
                    $search['generik'] = get_safe('generik');
                }

                if (isset($_GET['formularium']) and $_GET['formularium'] != '') {
                    $search['formularium'] = get_safe('formularium');
                }

                if (get_safe('id_pabrik_obat') != '') {
                    $search['id_pabrik_obat'] = get_safe('id_pabrik_obat');
                    $search['pabrik_obat'] = get_safe('pabrik_obat');
                }
                if (isset($_GET['ha']) and get_safe('ha') !== '') {
                    $search['ha'] = get_safe('ha');
                }
                
                
                    
                $data = $this->get_barang_list($limit, $page, 2, 'Obat', $search);
            
                $data = array_merge($data, $search);
                $data['satuan_list'] = $this->m_referensi->satuan_get_data(null);
                $data['sediaan_list'] = $this->m_referensi->sediaan_get_data(null);

                $this->load->view('referensi/barang/list_obat', $data);
                break;
            case 'add':
                $insert['barang'] = $add;
                $insert['obat'] = $obat;
                $id = $this->m_referensi->barang_add_data($insert, 'Obat');
                die(json_encode(array('id'=> $id)));
                break;

            case 'edit':
                $add['id'] = post_safe('id_obat');
                $obat['id'] = post_safe('id_obat');
                $update['barang'] = $add;
                $update['obat'] = $obat;
                $this->m_referensi->barang_edit_data($update, 'Obat');
                die(json_encode(array('id'=> $add['id'])));
                break;

            case 'delete':
                $id = get_safe('id');
                $this->m_referensi->barang_delete_data($id, 'obat');
                break;
            case 'cek':
                $get = array(
                    'nama' => get_safe('nama')
                );
                $cek = $this->m_referensi->obat_cek_data($get);
                die(json_encode(array('status' => $cek)));
                break;

        
            case 'get_data':
                $search['id'] = get_safe('id');
                $data = $this->get_barang_list($limit, 1, 2, 'Obat', $search);
                $this->load->view('referensi/barang/list_obat', $data);
                break;

            default:
                break;
        }
    }

    /* Barang */


    /* Packing Barang */

    function packing_barang() {
        $data['satuan'] = $this->m_referensi->satuan_get_data(null);
        $data['kemasan'] = $this->m_referensi->satuan_get_data(null);
        $data['kemasan'][''] = "Pilih Kemasan";
        $data['title'] = 'Kemasan Barang P.F.';
        $this->load->view('referensi/barang/packing', $data);
    }

    function get_packing_list($limit, $page, $id, $search, $kat = null) {
        if ($page == 'undefined') {
            $page = 1;
        }
        $start = ($page - 1) * $limit;
        $data['page'] = $page;
        $data['limit'] = $limit;
        $query = $this->m_referensi->packing_get_data($limit, $start, $id, $search, urldecode($kat));
        $data['jumlah'] = $query['jumlah'];
        $data['packing'] = $query['data'];
        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, 1, '');
        return $data;
    }

    function manage_packing($mode, $page = null, $jenis = null) {
        $limit = 15;
        $add = array(
            'barcode' => post_safe('barcode'),
            'barang_id' => (post_safe('id_barang') == '') ? NULL : post_safe('id_barang'),
            'terbesar_satuan_id' => (post_safe('kemasan') == '') ? NULL : post_safe('kemasan'),
            'isi' => post_safe('isi'),
            'terkecil_satuan_id' => (post_safe('satuan') == '') ? NULL : post_safe('satuan'),
        );
        //$searchnull = 'null';
        switch ($mode) {
            case 'list':
                $search = array();

                if (get_safe('barcode') != '') {
                    $search['barcode'] = get_safe('barcode');
                    
                }
                if (get_safe('id_barang') != '') {
                    $search['id_barang'] = get_safe('id_barang');
                    $search['barang'] = get_safe('barang');
                }

                if (get_safe('kemasan') != '') {
                    $search['kemasan'] = get_safe('kemasan');
                }

                if (get_safe('isi') != '') {
                    $search['isi'] = get_safe('isi');
                }

                if (get_safe('satuan') != '') {
                    $search['satuan'] = get_safe('satuan');
                }

                $data = $this->get_packing_list($limit, $page, 'null', $search, $jenis);
                $data = array_merge($data, $search);
                $data['satuan_list'] = $this->m_referensi->satuan_get_data(null);
                $data['sediaan_list'] = $this->m_referensi->satuan_get_data(null);
                $this->load->view('referensi/barang/list_packing', $data);
                break;
            case 'add':
                $id = $this->m_referensi->packing_add_data($add);
                die(json_encode(array('id'=> $id)));
                break;

            case 'edit':
                $add['id'] = post_safe('id');
                $this->m_referensi->packing_edit_data($add);
                die(json_encode(array('id'=> $add['id'])));
                break;

            case 'delete':
                $id = get_safe('id');
                $this->m_referensi->packing_delete_data($id);
                break;
            case 'cek':
                $ins = array(
                    'barcode' => get_safe('barcode'),
                    'id_barang' => get_safe('id_barang'),
                    'kemasan' => get_safe('kemasan'),
                    'isi' => get_safe('isi'),
                    'satuan' => get_safe('satuan')
                );
                $cek = $this->m_referensi->packing_cek_data($ins);
                die(json_encode(array('status' => $cek)));
                break;

            case 'get_data':
                $id = get_safe('id');
                $data = $this->get_packing_list($limit, 1, $id, '', $jenis);
                $this->load->view('referensi/barang/list_packing', $data);
                break;

            default:
                break;
        }
    }
    
    function packing_barang_rt() {
        $data['satuan'] = $this->m_referensi->satuan_get_data(null);
        $data['kemasan'] = $this->m_referensi->satuan_get_data(null);
        $data['kemasan'][''] = "Pilih Kemasan";
        $data['title'] = 'Kemasan Barang R.T';
        $this->load->view('referensi/barang/packing-rt', $data);
    }
    
    function packing_barang_gizi() {
        $data['satuan'] = $this->m_referensi->satuan_get_data(null);
        $data['kemasan'] = $this->m_referensi->satuan_get_data(null);
        $data['kemasan'][''] = "Pilih Kemasan";
        $data['title'] = 'Kemasan Barang Gizi';
        $this->load->view('referensi/barang/packing-gizi', $data);
    }

    function cetak_barcode() {
        $data['jml'] = get_safe('jumlah');
        $data['barcode'] = get_safe('barcode');
        $this->load->view('barcode', $data);
    }

    /* Packing Barang */

    /* Layanan */


    function layanan() {

        $data['title'] = 'Layanan';
        $this->load->view('referensi/layanan/layanan', $data);
    }

    function get_layanan_list($limit, $page, $search) {
        if ($page == 'undefined') {
            $page = 1;
        }
        $str = '';
        $start = ($page - 1) * $limit;
        $data['page'] = $page;
        $data['limit'] = $limit;
        $query = $this->m_referensi->layanan_get_data($limit, $start, $search);
        $data['jumlah'] = $query['jumlah'];
        $data['layanan'] = $query['data'];
        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, 1, '');
        return $data;
    }

    function manage_layanan($mode, $page = null) {
        $limit = 10;
        $add = array(
            'nama' => post_safe('nama'),
            'kode_icdixcm' => post_safe('icd'),
            'id_sub_sub_jenis_layanan' => (post_safe('id_sub_sub')=="")?NULL:post_safe('id_sub_sub')
        );
        $searchnull = '';
        switch ($mode) {
            case 'list':
                $search['nama'] = get_safe('nama');
                $search['icd'] = get_safe('icd');
                $search['jenis'] = get_safe('jenis');
                $search['id_sub_sub'] = get_safe('id_sub_sub');
                $data = $this->get_layanan_list($limit, $page, $search);
                $data = array_merge($data, $search);               

                $this->load->view('referensi/layanan/list_layanan', $data);
                break;
            case 'add':
                $id = $this->m_referensi->layanan_add_data($add);
                die(json_encode(array('id'=> $id)));
                break;

            case 'edit':
                $add['id'] = post_safe('id_layanan');
                $this->m_referensi->layanan_edit_data($add);
                die(json_encode(array('id'=> $add['id'])));
                break;

            case 'delete':
                $id = get_safe('id');
                $this->m_referensi->layanan_delete_data($id);

                break;
            case 'cek':
                $ins = array(
                    'layanan' => get_safe('layanan')
                );
                $cek = $this->m_referensi->layanan_cek_data($ins);
                die(json_encode(array('status' => $cek)));

                break;

            case 'get_data':
                $search['id'] = get_safe('id');
                $data = $this->get_layanan_list($limit, 1, $search);
                $this->load->view('referensi/layanan/list_layanan', $data);
                break;
            

            default:
                break;
        }
    }

    /* Layanan */


    /* Penduduk */

    function penduduk() {
        $this->load->model('m_demografi');
        $data['title'] = 'Kependudukan (Pasien, Dokter, Perawat, Nakes Lain-lain)';
        $data['gol_darah'] = $this->m_demografi->gol_darah();
        $data['agama'] = $this->m_demografi->agama();
        $data['pendidikan'] = $this->m_demografi->pendidikan();
        $data['pernikahan'] = $this->m_demografi->stat_nikah();
        $data['pekerjaan'] = $this->m_demografi->pekerjaan();
        $data['profesi'] = $this->m_referensi->profesi_get_data();
        $data['posisi'] = $this->m_referensi->posisi_keluarga_get_data();
        $data['jabatan'] = $this->m_referensi->jabatan_get_data();
        $this->load->view('referensi/penduduk/penduduk', $data);
    }

    function get_penduduk_list($limit, $page, $search) {
        if ($page == 'undefined') {
            $page = 1;
        }
        $str = '';
        $start = ($page - 1) * $limit;
        $data['page'] = $page;
        $data['limit'] = $limit;
        
        $query = $this->m_referensi->penduduk_get_data($limit, $start, $search);
     
        $data['penduduk'] = $query['data'];
        $data['jumlah'] = $query['jumlah'];

     
        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, 1, '');
        return $data;
    }

    function manage_penduduk($mode, $page = null) {
        $limit = 15;
        $pdd = array(
            'nama' => post_safe('nama'),
            'lahir_kabupaten_id' => (post_safe('id_kabupaten') == "") ? NULL : post_safe('id_kabupaten'),
            'gender' => post_safe('kelamin'),
            'telp' => post_safe('telp'),
            'darah_gol' => post_safe('gol_darah'),
            'lahir_tanggal' => date2mysql(post_safe('tgl_lahir')),
        );

        $dinamis = array(
            'penduduk_id' => post_safe('id_penduduk'),
            'tanggal' => date('Y-m-d'),
            'alamat' => preg_replace('~[\r\n]+~', ' ', post_safe('alamat')),
        );
        $searchnull = '';
        switch ($mode) {
            case 'list':

                $search = array(
                    'nama' => get_safe('nama_cari'),
                    'alamat' => get_safe('alamat_cari'),
                    'telp' => get_safe('telp_cari'),
                    'kabupaten' => get_safe('id_kabupaten_cari'),
                    'gender' => isset($_GET['kelamin_cari'])?get_safe('kelamin_cari'):'',
                    'gol_darah' => get_safe('gol_darah_cari'),
                    'tgl_lahir' => get_safe('tgl_lahir_cari')
                );
                                   
                $data = $this->get_penduduk_list($limit, $page, $search);
                $data = array_merge($data, $search);
                $this->load->view('referensi/penduduk/list_penduduk', $data);
                break;
            case 'add':
                $add['penduduk'] = $pdd;
                $add['dinamis'] = $dinamis;
                $search['id'] = $this->m_referensi->penduduk_add_data($add);
                $data = $this->get_penduduk_list($limit,1, $search);
                $this->load->view('referensi/penduduk/list_penduduk', $data);
                break;

            case 'edit':
                $dinamis['alamat_lama'] = preg_replace('~[\r\n]+~', ' ', post_safe('alamat_lama'));
                $pdd['id'] = post_safe('id_penduduk');
                $edit['penduduk'] = $pdd;
                $edit['dinamis'] = $dinamis;
                $search['id'] = $pdd['id'];
                $this->m_referensi->penduduk_edit_data($edit);
                $data = $this->get_penduduk_list($limit, 1 , $search);
                $this->load->view('referensi/penduduk/list_penduduk', $data);
                break;

            case 'delete':
                $id = get_safe('id');
                $this->m_referensi->penduduk_delete_data($id);
                break;
            case 'cek':
                $ins = array(
                    'penduduk' => get_safe('nama'),
                );
                $cek = $this->m_referensi->penduduk_cek_data($ins);
                die(json_encode(array('status' => $cek)));

                break;

            case 'edit_dinamis':
                $ret = $this->m_referensi->dinamis_penduduk_edit_data();
                die(json_encode(array('id' => $ret['id'])));
                break;

            case 'use':
                $id = get_safe('id');
                $stat = $this->m_referensi->penduduk_cek_user_data($id);
                die(json_encode(array('status'=>$stat)));
                break;

            default:
                break;
        }
    }

    function dinamis_penduduk_get_data() {
        /*
         * untuk mengambil data dinamis penduduk yg terakhir
         */
        $id = get_safe('id');
        $id_dp = get_safe('id_dp');
        $query = $this->m_referensi->penduduk_dinamis_get_data($id, $id_dp);
        die(json_encode($query));
    }

    function dinamis_penduduk_get_list() {
        /*
         * unutk mengambil histori data dinamis penduduk 
         */
        $id = get_safe('id');
        $query['dinamis'] = $this->m_referensi->penduduk_dinamis_get_data($id, null);
        $this->load->view('referensi/penduduk/list_dinamis', $query);
    }

    function dinamis_penduduk_delete_data($id){
        $this->m_referensi->dinamis_penduduk_delete_data($id);
    }

    function harga_jual() {
        $data['title'] = 'Harga Jual';
        $this->load->view('referensi/harga_jual/harga-jual', $data);
    }

    function harga_jual_load() {
        $data['title'] = 'Administrasi Harga Jual';
        $id = get_safe('pb');
        $data['list_data'] = $this->m_referensi->harga_jual_load_data($id)->result();
        $this->load->view('referensi/harga_jual/harga-jual-table', $data);
    }

    function harga_jual_update() {
        $data['title'] = 'Update Harga Jual';
        $id = implode(',', post_safe('pb'));
        $data['list_data'] = $this->m_referensi->harga_jual_load_data_update($id)->result();
        $this->load->view('referensi/harga_jual/harga-jual-update-table', $data);
    }

    function harga_jual_update_save() {
        $data = $this->m_referensi->harga_jual_update_save();
        die(json_encode($data));
    }

    function setting_kas() {
        $data['title'] = 'Posisi Kas Awal';
        $this->load->view('referensi/setting-kas', $data);
    }

    function setting_kas_save() {
        $data = $this->m_referensi->setting_kas_save();
        die(json_encode($data));
    }

    function layanan_profesi() {
        $data['title'] = 'Jasa Tindakan Layanan Profesi';
        $this->load->view('referensi/layanan/adm-layanan-profesi', $data);
    }

    function layanan_profesi_save() {
        $data = $this->m_referensi->layanan_profesi_save();
        die(json_encode($data));
    }

    function layanan_profesi_delete($id_tindakan, $id_layanan) {
        $this->m_referensi->layanan_profesi_delete($id_tindakan);
        $this->layanan_profesi_load_table($id_layanan);
    }

    function layanan_profesi_load_table($id_layanan) {
        $data['title'] = 'Jasa Tindakan Layanan Profesi';
        $data['list_data'] = $this->m_inv_autocomplete->adm_layanan_profesi($id_layanan)->result();
        $this->load->view('referensi/layanan/adm-layanan-profesi-table', $data);
    }

    function load_data_profesi() {
        $q = get_safe('q');
        $data = $this->m_inv_autocomplete->load_data_profesi($q)->result();
        die(json_encode($data));
    }
    
    function rt() {
        $data['title'] = 'Barang Perbekalan Rumah Tangga';
        $this->load->view('referensi/barang/rt', $data);
    }
    
    function perbekalan_rt() {
        $data['title'] = 'Barang Perbekalan Rumah Tangga';
        $query = $this->m_referensi->kategori_barang_get_data(null, "Rumah Tangga");
        $kat[''] = "Pilih Kategori";
        foreach ($query as $value) {
            if ($value->nama != 'Obat') {
                $kat[$value->id] = $value->nama;
            }
        }
        $data['kategori_rt'] = $kat;
        $this->load->view('referensi/barang/perbekalan_rt', $data);
    }
    
    function gizi() {
        $data['title'] = 'Barang Perbekalan Gizi';
        $this->load->view('referensi/barang/gizi', $data);
    }
    
    function perbekalan_gizi() {
        $query = $this->m_referensi->kategori_barang_get_data(null, "Gizi");
        $kat[''] = "Pilih Kategori";
        foreach ($query as $value) {
            if ($value->nama != 'Obat') {
                $kat[$value->id] = $value->nama;
            }
        }
        $data['kategori_gizi'] = $kat;
        $this->load->view('referensi/barang/perbekalan_gizi', $data);
    }

    /* Tarif Jasa */

    function tarif($id = null) {
       if ($id != null) {
            $data['id_tarif'] = $id;
       }

       $data['tipe'] = 'tindakan';

       if (isset($_GET['tipe'])) {
           $data['tipe'] = get_safe('tipe');
       }

       $data['title'] = 'Tarif';
       $this->load->view('referensi/tarif/tarif', $data);
    }

    function tarif_tindakan($id = null){
         if ($id != null) {
            $search['id'] = $id;
            $detail = $this->m_referensi->tarif_get_data(1, 0, $search,'tindakan');
            $data['edit'] = $detail['data'][0];
        }
        $data['unit'] = $this->m_referensi->unit_get_data(null);
        $data['profesi'] = $this->m_referensi-> profesi_get_data('Nakes');
        $data['kelas'] = $this->m_referensi->kelas_tarif_get_data();
        $data['bobot'] = $this->m_referensi->bobot_tarif_get_data();
        $data['jenis_layan'] = $this->m_referensi->jenis_pelayanan_get_data();
        $this->load->view('referensi/tarif/tindakan', $data);
    }

    function tarif_barang($id = null){
        if ($id != null) {
            $search['id'] = $id;
            $detail = $this->m_referensi->tarif_get_data(1, 0, $search,'sewa');
            $data['edit'] = $detail['data'][0];
        }
        $data['unit'] = $this->m_referensi-> unit_get_data(null);
        $data['profesi'] = $this->m_referensi-> profesi_get_data();
        $data['kelas'] = $this->m_referensi->kelas_tarif_get_data();
        $data['bobot'] = $this->m_referensi->bobot_tarif_get_data();
        $data['jenis_layan'] = $this->m_referensi->jenis_pelayanan_get_data();
        $data['id_layanan'] = $this->db->query("select id from layanan where nama = 'Sewa Barang'")->row()->id;
        $this->load->view('referensi/tarif/barang', $data);
    }

    function tarif_kamar($id = null){
        if ($id != null) {
            $search['id'] = $id;
            $detail = $this->m_referensi->tarif_get_data(1, 0, $search,'kamar');
            $data['edit'] = $detail['data'][0];
        }
        $data['unit'] = $this->m_referensi-> unit_get_data(null);
        $data['profesi'] = $this->m_referensi-> profesi_get_data('Nakes');
        $data['kelas'] = $this->m_referensi->kelas_tarif_get_data();
        $data['bobot'] = $this->m_referensi->bobot_tarif_get_data();
        $data['jenis_layan'] = $this->m_referensi->jenis_pelayanan_get_data();
        $this->load->view('referensi/tarif/kamar', $data);
    }

    function get_tarif_list($limit, $page,$tab, $search, $jenis) {
        if ($page == 'undefined') {
            $page = 1;
        }
        $str = '';
        $start = ($page - 1) * $limit;
        $data['page'] = $page;
        $data['limit'] = $limit;
        $query = $this->m_referensi->tarif_get_data($limit, $start, $search, $jenis);
        $data['jumlah'] = $query['jumlah'];
        $data['tarif'] = $query['data'];

        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, $tab, '');
        return $data;
    }

    function get_last_id($tabel, $id) {
        return die(json_encode(array('last_id' => get_last_id($tabel, $id))));
    }

    function manage_tindakan($mode,$page = null) {
        $limit = 10;
        $searchnull = '';
        switch ($mode) {
            case 'list':
                $search = array(
                    
                    'nama' => get_safe('layanan'),
                    'profesi' => get_safe('profesi'),
                    'jurusan' => get_safe('jurusan'),
                    'id_jurusan' => get_safe('id_jurusan'),
                    'jurusan' => get_safe('jurusan'),
                    'jenis_layan' => get_safe('jenis_layan'),
                    'unit' => get_safe('unit'),
                    'nama_unit' => get_safe('nama_unit'),
                    'bobot' => get_safe('bobot'),
                    'kelas' => get_safe('kelas')
                );
                $data = $this->get_tarif_list($limit, $page,1, $search,'tindakan');
                $data = array_merge($data, $search);

                $this->load->view('referensi/tarif/tindakan_list', $data);
                break;
            case 'add':
                $search['id'] = $this->m_referensi->tindakan_add_data();
                $data = $this->get_tarif_list($limit, 1,1, $search,'tindakan');
                $this->load->view('referensi/tarif/tindakan_list', $data);
                break;
            case 'edit':
                $id = $this->m_referensi->tindakan_edit_data();
                $search['id'] = $id;
                $data = $this->get_tarif_list($limit, 1,1, $search,'tindakan');
                $this->load->view('referensi/tarif/tindakan_list', $data);
                break;

            case 'delete':
                $id = get_safe('id');
                $this->m_referensi->tarif_delete_data($id);
                break;
            case 'cek':
                $cek = $this->m_referensi->tindakan_cek_data();
                die(json_encode(array('status' => $cek)));
                break;
            case 'detail':
                $search['id'] = get_safe('id');
                $detail = $this->m_referensi->tarif_get_data($limit, 0, $search,'tindakan');
                die(json_encode($detail['data'][0]));
                break;

            default:
                break;
        }
    }

    function get_data_bhp_tarif($id_tarif = null){
        if($id_tarif != null){
            $data = $this->m_referensi->get_data_bhp_tarif($id_tarif);
            die(json_encode($data));
        }
    }

    function delete_data_bhp_tarif($id_bhp){
        $this->db->where('id', $id_bhp)->delete('bhp_tarif');
    }


    function manage_kamar($mode,$page = null) {
        $limit = 15;
        $searchnull = '';
        switch ($mode) {
            case 'list':
                $search['unit'] = get_safe('unit');
                $search['kelas'] = get_safe('kelas');
                if ($search['unit'] != '') {
                    $data = $this->get_tarif_list($limit, $page,3, $search,'kamar');
                    $data['key'] = get_safe('nama_unit');
                    $data['kelas'] = $search['kelas'];
                } else {
                    $data = $this->get_tarif_list($limit, $page,3, $searchnull,'kamar');
                }

                $this->load->view('referensi/tarif/kamar_list', $data);
                break;
            case 'add':
                $search['id'] = $this->m_referensi->kamar_add_data();
                $data = $this->get_tarif_list($limit, 1,3, $search,'kamar');
                $this->load->view('referensi/tarif/kamar_list', $data);
                break;
            case 'edit':
                $id = $this->m_referensi->kamar_edit_data();
                $search['id'] = $id;
                $data = $this->get_tarif_list($limit, 1,3, $search,'kamar');
                $this->load->view('referensi/tarif/kamar_list', $data);
                break;

            case 'delete':
                $id = get_safe('id');
                $this->m_referensi->tarif_delete_data($id);
                break;
            case 'cek':
                $cek = $this->m_referensi->kamar_cek_data();
                die(json_encode(array('status' => $cek)));
                break;
            case 'detail':
                $search['id'] = get_safe('id');
                $detail = $this->m_referensi->tarif_get_data($limit, 0, $search,'kamar');
                die(json_encode($detail['data'][0]));
                break;
            case 'cari':
                $search['unit'] = get_safe('unit');
                $search['kelas'] = get_safe('kelas');
                $data = $this->get_tarif_list($limit, 1,3, $search,'kamar');
                if(get_safe('unit') != ''){
                    $data['key'] = get_safe('nama_unit');
                    $data['kelas'] = get_safe('kelas');
                }
                $this->load->view('referensi/tarif/kamar_list', $data);
                break;

            default:
                break;
        }
    }
    
    function manage_sewa($mode, $page = null) {
        $limit = 15;
        $searchnull = 'null';
        switch ($mode) {
            case 'list':
                $q = get_safe('search');
                if ($q != 'null') {
                    $search['nama'] = $q;
                    $data = $this->get_tarif_list($limit, $page,2, $search,'sewa');
                    $data['key'] = $search['nama'];
                }else if(get_safe('id') != ''){
                    $search['id'] = get_safe('id');
                    $data = $this->get_tarif_list($limit, $page,2, $search,'sewa');
                } else {
                    $data = $this->get_tarif_list($limit, $page, 2,$searchnull,'sewa');
                }

                $this->load->view('referensi/tarif/sewa_list', $data);
                break;
            case 'add':
                $search['id'] = $this->m_referensi->sewa_add_data();
                $data = $this->get_tarif_list($limit, 1, 2,$search,'sewa');
                $this->load->view('referensi/tarif/sewa_list', $data);
                break;
            case 'edit':
                $id = $this->m_referensi->sewa_edit_data();
                $search['id'] = $id;
                $data = $this->get_tarif_list($limit, 1, 2, $search,'sewa');
                $this->load->view('referensi/tarif/sewa_list', $data);
                break;

            case 'delete':
                $id = get_safe('id');
                $this->m_referensi->tarif_delete_data($id);
                $data = $this->get_tarif_list($limit, $page, 2,$searchnull,'sewa');
                if ($data['tarif'] == null) {
                    $data = $this->get_tarif_list($limit, 1, 2,$searchnull,'sewa');
                }
                $this->load->view('referensi/tarif/sewa_list', $data);
                break;
            case 'cek':
                $cek = $this->m_referensi->sewa_cek_data();
                die(json_encode(array('status' => $cek)));
                break;
            case 'detail':
                $search['id'] = get_safe('id');
                $detail = $this->m_referensi->tarif_get_data($limit, 0, $search,'sewa');
                die(json_encode($detail['data'][0]));
                break;

            default:
                break;
        }
    }

    function get_jasa_profesi() {
        $id = get_safe('id_layanan');
        $jp = $this->m_referensi->get_jasa_profesi($id);
        die(json_encode(array('total_jp' => $jp->total)));
    }

    function gol_sebab_sakit() {
        $data['title'] = 'Golongan Sebab Sakit';
        $this->load->view('referensi/gol_sebab_sakit/gol_sebab_sakit', $data);
    }
    
    function get_list_gol_sebab_sakit($limit, $page, $search) {
        if ($page == 'undefined') {
            $page = 1;
        }
        $str = 'null';
        $start = ($page - 1) * $limit;
        $data['page'] = $page;
        $data['limit'] = $limit;

        if ($search != 'null') {
            $query = $this->m_referensi->gol_sebab_sakit_get_data($limit, $start, $search);
        } else {
            $query = $this->m_referensi->gol_sebab_sakit_get_data($limit, $start, null);
        }
        $data['gol_sebab_sakit'] = $query['data'];
        $data['jumlah'] = $query['jumlah'];
        $str = null;
        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, 1, $str);
        return $data;
    }
    
    function manage_gol_sebab_sakit($mode, $page = null) {
        $limit = 15;
        $add = array(
            'no_dtd' => post_safe('no_dtd'),
            'no_daftar_terperinci' => post_safe('no_daftar'),
            'nama' => post_safe('nama')
        );
        $searchnull = 'null';
        switch ($mode) {
            case 'list':
                $data = $this->get_list_gol_sebab_sakit($limit, $page, $searchnull);
                $this->load->view('referensi/gol_sebab_sakit/list_gol_sebab_sakit', $data);
                break;
            case 'add':
                $add['id'] = $this->m_referensi->gol_sebab_sakit_add_data($add);
                $data = $this->get_list_gol_sebab_sakit($limit, 1, $add);
                $this->load->view('referensi/gol_sebab_sakit/list_gol_sebab_sakit', $data);
                break;

            case 'edit':
                $add['id'] = post_safe('id_gol');
                $this->m_referensi->gol_sebab_sakit_edit_data($add);
                $data = $this->get_list_gol_sebab_sakit($limit, 1, $add);
                $this->load->view('referensi/gol_sebab_sakit/list_gol_sebab_sakit', $data);
                break;

            case 'delete':
                $id = get_safe('id');
                $this->m_referensi->gol_sebab_sakit_delete_data($id);
                $data = $this->get_list_gol_sebab_sakit($limit, $page, $searchnull);
                $this->load->view('referensi/gol_sebab_sakit/list_gol_sebab_sakit', $data);

                break;
            
            case 'search':
                $searchnull = array(
                    'id' => post_safe('id_gol'),
                    'no_dtd' => post_safe('no_dtd'),
                    'no_daftar_terperinci' => post_safe('no_daftar'),
                    'nama' => post_safe('nama'),
                    'code_icdx' => post_safe('icdx')
                );
                $data = $this->get_list_gol_sebab_sakit($limit, 1, $searchnull);
                $this->load->view('referensi/gol_sebab_sakit/list_gol_sebab_sakit', $data);
                break;
            default:
                break;
        }
    }

    function kategori_barang(){
        $data['title'] = 'Kategori Barang';
        $this->load->view('referensi/barang/kategori', $data); 
    }

    function get_kategori_barang_list($param, $page) {
        $limit = 15;
        if ($page == 'undefined') {
            $page = 1;
        }
        $start = ($page - 1) * $limit;
        $data['page'] = $page;
        $data['limit'] = $limit;
        $query = $this->m_referensi->kategori_barang_load_data($limit, $start, $param);
        $data['jumlah'] = $query['jumlah'];
        $data['list_data'] = $query['data'];
        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, 1, '');
        return $data;
    }

    function manage_kategori($mode, $page = null) {
        $param = array();
        switch ($mode) {
            case 'list': 
                if(get_safe('nama') != ''){
                    $param['nama'] = get_safe('nama');
                }
                if(isset($_GET['jenis'])){
                    $param['jenis'] = get_safe('jenis');

                }
                
                $data = $this->get_kategori_barang_list($param, $page); 
                
                $data['nama'] = get_safe('nama');
                $data['jenis'] = isset($_GET['jenis'])?get_safe('jenis'):'';
                
                $this->load->view('referensi/barang/kategori_list', $data);
            break;

            case 'add':
                $param['id'] = $this->m_referensi->kategori_add_data();
                $data = $this->get_kategori_barang_list($param, $page);
                echo $param['id']."##".
                $this->load->view('referensi/barang/kategori_list', $data);
            break;

            case 'edit':
                $id = $this->m_referensi->kategori_edit_data();
                $param['id'] = $id;
                $data = $this->get_kategori_barang_list($param, $page);
                echo $param['id']."##".
                $this->load->view('referensi/barang/kategori_list', $data);
            break;

            case 'delete':
                $id = get_safe('id');
                $this->m_referensi->kategori_delete_data($id);
            break;

            default:
                break;
        }
    }

    function sediaan(){
        $data['title'] = 'Sediaan';
        $this->load->view('referensi/barang/sediaan', $data);
    }

    function get_sediaan_list($param, $page) {
        $limit = 15;
        if ($page == 'undefined') {
            $page = 1;
        }
        $start = ($page - 1) * $limit;
        $data['page'] = $page;
        $data['limit'] = $limit;
        $query = $this->m_referensi->sediaan_load_data($limit, $start, $param);
        $data['jumlah'] = $query['jumlah'];
        $data['list_data'] = $query['data'];
        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, 1, '');
        return $data;
    }

    function manage_sediaan($mode, $page = null) {
        $param = array();
        switch ($mode) {
            case 'list': 
                if(get_safe('nama') != ''){
                    $param['nama'] = get_safe('nama');
                }
                
                $data = $this->get_sediaan_list($param, $page); 
                
                $data['nama'] = get_safe('nama');
                
                $this->load->view('referensi/barang/sediaan_list', $data);
            break;

            case 'add':
                $param['id'] = $this->m_referensi->sediaan_add_data();
                $data = $this->get_sediaan_list($param, $page);
                echo $param['id']."##".
                $this->load->view('referensi/barang/sediaan_list', $data);
            break;

            case 'edit':
                $id = $this->m_referensi->sediaan_edit_data();
                $param['id'] = $id;
                $data = $this->get_sediaan_list($param, $page);
                echo $param['id']."##".
                $this->load->view('referensi/barang/sediaan_list', $data);
            break;

            case 'delete':
                $id = get_safe('id');
                $this->m_referensi->sediaan_delete_data($id);
            break;

            default:
                break;
        }
    }

    function satuan(){
        $data['title'] = 'Satuan';
        $this->load->view('referensi/barang/satuan', $data);
    }

    function get_satuan_list($param, $page) {
        $limit = 10;
        if ($page == 'undefined') {
            $page = 1;
        }
        $start = ($page - 1) * $limit;
        $data['page'] = $page;
        $data['limit'] = $limit;
        $query = $this->m_referensi->satuan_load_data($limit, $start, $param);
        $data['jumlah'] = $query['jumlah'];
        $data['list_data'] = $query['data'];
        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, 1, '');
        return $data;
    }

    function manage_satuan($mode, $page = null) {
        $param = array();
        switch ($mode) {
            case 'list': 
                if(get_safe('nama') != ''){
                    $param['nama'] = get_safe('nama');
                }
                
                $data = $this->get_satuan_list($param, $page); 
                
                $data['nama'] = get_safe('nama');
                
                $this->load->view('referensi/barang/satuan_list', $data);
            break;

            case 'add':
                $param['id'] = $this->m_referensi->satuan_add_data();
                $data = $this->get_satuan_list($param, $page);
                echo $param['id']."##".
                $this->load->view('referensi/barang/satuan_list', $data);
            break;

            case 'edit':
                $id = $this->m_referensi->satuan_edit_data();
                $param['id'] = $id;
                $data = $this->get_satuan_list($param, $page);
                echo $param['id']."##".
                $this->load->view('referensi/barang/satuan_list', $data);
            break;

            case 'delete':
                $id = get_safe('id');
                $this->m_referensi->satuan_delete_data($id);
            break;

            default:
                break;
        }
    }


    /* User Account */

    function account(){
        $data['title'] = 'Account';
        $this->load->view('referensi/user_account/group', $data);
    }

    function user_group(){
        $this->load->view('referensi/user_account/user_group');
    }

    function user_account() {
        $data['user_group'] = $this->m_referensi->get_user_group();
        $this->load->view('referensi/user_account/account', $data);
    }

    function get_group_list($limit, $page, $search){
        if ($page == 'undefined') {
            $page = 1;
        }
        $start = ($page - 1) * $limit;
        $data['page'] = $page;
        $data['limit'] = $limit;
        $query = $this->m_referensi->group_get_data($limit, $start, $search);
        $data['jumlah'] = $query['jumlah'];
        $data['user'] = $query['data'];
        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, 1, '');
        return $data;
    }

    function manage_group($mode,$page=null){
        $limit = 15;
        $add = array(
            'id' => post_safe('id'),
            'nama' => post_safe('nama')
        );
        $search = array();
        switch ($mode) {
            case 'list':
                $data = $this->get_group_list($limit, $page, $search);
                $this->load->view('referensi/user_account/list_group', $data);
                break;
            case 'post':
                $search['id'] = $this->m_referensi->group_update_data($add);
                $data = $this->get_group_list($limit, $page, $search);
                $this->load->view('referensi/user_account/list_group', $data);
                break;

             case 'edit':
                /*
                 * Butuh data unit, user
                 */
                $data['title'] = "User Group Privileges";
                $data['id'] = get_safe('id');
                $data['nama'] = get_safe('nama');
                $this->load->view('referensi/user_account/privilege', $data);
                break;

            case 'delete':
                $id = get_safe('id');
                $this->m_referensi->group_delete_data($id);
                $data = $this->get_group_list($limit, $page, null);
                if ($data['user'] == null) {
                    $data = $this->get_group_list($limit, 1, null);
                }
                $this->load->view('referensi/user_account/list_group', $data);
                break;

            default:
                
                break;
        }
    }

    function get_user_list($limit, $page, $search) {
        if ($page == 'undefined') {
            $page = 1;
        }
        $start = ($page - 1) * $limit;
        $data['page'] = $page;
        $data['limit'] = $limit;
        $query = $this->m_referensi->user_get_data($limit, $start, $search);
        $data['jumlah'] = $query['jumlah'];
        $data['user'] = $query['data'];
        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, 2, '');
        return $data;
    }

    function manage_user($mode, $page = null) {
        $limit = 15;
        $add = array(
            'id' => post_safe('id_penduduk'),
            'username' => post_safe('username'),
            'status' => post_safe('status'),
            'user_group_id' => post_safe('user_group'),
        );
        $searchnull = 'null';
        switch ($mode) {
            case 'list':
                $search['id'] = post_safe('id_penduduk');
                $search['nama'] = post_safe('nama');
                $search['username'] = post_safe('username');
                $data = $this->get_user_list($limit, $page, $search);
                $this->load->view('referensi/user_account/list_account', $data);
                break;
            case 'add':
                $add['password'] = md5('1234');
                $this->m_referensi->user_add_data($add);
                $search['id'] = $add['id'];
                $data = $this->get_user_list($limit, 1, $search);
                $this->load->view('referensi/user_account/list_account', $data);
                break;


            case 'delete':
                $id = get_safe('id');
                $this->m_referensi->user_delete_data($id);
                $data = $this->get_user_list($limit, $page, null);
                if ($data['user'] == null) {
                    $data = $this->get_user_list($limit, 1, null);
                }
                $this->load->view('referensi/user_account/list_account', $data);
                break;
            case 'cek':
                $kab = array(
                    'nama' => get_safe('user'),
                    'kecamatan_id' => get_safe('kecid'),
                    'kode' => get_safe('kode')
                );
                $cek = $this->m_referensi->user_cek_data($kab);
                die(json_encode(array('status' => $cek)));

                break;
            case 'search': 
                $search['id'] = post_safe('id_penduduk');
                $search['nama'] = post_safe('nama');
                $search['username'] = post_safe('username');
                $data = $this->get_user_list($limit, 1, $search);
                $this->load->view('referensi/user_account/list_account', $data);
                break;
            default:
                break;
        }
    }

    function get_unit() {
        $q = get_safe('q');
        $data = $this->m_referensi->get_unit($q)->result();
        die(json_encode($data));
    }

    function get_group_privileges($id) {
        $data['user_priv'] = $this->m_referensi->group_privileges_data($id);
        $data['privilege'] = $this->m_referensi->privileges_get_data();
        return $data;
    }

    function manage_privileges($mode) {

        switch ($mode) {
            case 'list':
                $id = get_safe('id');
                $data = $this->get_group_privileges($id);
                $this->load->view('referensi/user_account/list_privileges', $data);

                break;

            case 'add':
                $add = array(
                    'privileges' => post_safe('data'),
                    'id_group' => (post_safe('id_group') == '') ? NULL : post_safe('id_group')
                );
                $this->m_referensi->privileges_edit_data($add);
                $data = $this->get_group_privileges(post_safe('id_group'));
                $this->load->view('referensi/user_account/list_privileges', $data);

                break;

            default:
                break;
        }
    }

    /* User Account */
    
    function instansi() {
        $data['title'] = 'Management Data Instansi';
        $this->load->view('masterdata/instansi', $data);
    }
    /*MANAGEMEN PABRIK*/
    function pabrik() {
        $data['title'] = 'Data Pabrik';
        $this->load->view('masterdata/pabrik', $data);
    }
    
    function get_list_data_pabrik($limit, $page, $search) {
        if ($page == 'undefined' or $page === '') {
            $page = 1;
        }
        //$str = 'null';
        $start = ($page - 1) * $limit;
        $data['page'] = $page;
        $data['limit'] = $limit;
        $data['auto'] = $start+1;
        $query = $this->m_referensi->get_data_pabrik($limit, $start, $search);
        $data['list_data'] = $query['data'];
        $data['jumlah'] = $query['jumlah'];
        
        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, 1, $search['key']);
        return $data;
    }
    
    function manage_pabrik($status, $page = null) {
        $limit = 10;
        switch ($status) {
            case 'list':
                $search['key'] = $_GET['search'];
                $search['id']  = $_GET['id'];
                $data = $this->get_list_data_pabrik($limit, $page, $search);
                $this->load->view('masterdata/pabrik-list', $data);
                break;
            case 'save': 
                $this->m_referensi->save_pabrik();
                $data = $this->get_list_data_pabrik($limit, 1, null);
                $this->load->view('masterdata/pabrik-list', $data);
                break;
            case 'delete': 
                $this->m_referensi->delete_pabrik($_GET['id']);
                break;
            
        }
    }
    
    /*MANAGEMEN supplier*/
    function supplier() {
        $data['title'] = 'Data supplier';
        $this->load->view('masterdata/supplier', $data);
    }
    
    function get_list_data_supplier($limit, $page, $search) {
        if ($page == 'undefined' or $page === '') {
            $page = 1;
        }
        //$str = 'null';
        $start = ($page - 1) * $limit;
        $data['page'] = $page;
        $data['limit'] = $limit;
        $data['auto'] = $start+1;
        $query = $this->m_referensi->get_data_supplier($limit, $start, $search);
        $data['list_data'] = $query['data'];
        $data['jumlah'] = $query['jumlah'];
        
        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, 1, $search['key']);
        return $data;
    }
    
    function manage_supplier($status, $page = null) {
        $limit = 10;
        switch ($status) {
            case 'list':
                $search['key'] = $_GET['search'];
                $search['id']  = $_GET['id'];
                $data = $this->get_list_data_supplier($limit, $page, $search);
                $this->load->view('masterdata/supplier-list', $data);
                break;
            case 'save': 
                $this->m_referensi->save_supplier();
                $data = $this->get_list_data_supplier($limit, 1, null);
                $this->load->view('masterdata/supplier-list', $data);
                break;
            case 'delete': 
                $this->m_referensi->delete_supplier($_GET['id']);
                break;
            
        }
    }
    
    /*MANAGEMEN instansi_lain*/
    function instansi_lain() {
        $data['title'] = 'Data instansi_lain';
        $this->load->view('masterdata/instansi-lain', $data);
    }
    
    function get_list_data_instansi_lain($limit, $page, $search) {
        if ($page == 'undefined' or $page === '') {
            $page = 1;
        }
        //$str = 'null';
        $start = ($page - 1) * $limit;
        $data['page'] = $page;
        $data['limit'] = $limit;
        $data['auto'] = $start+1;
        $query = $this->m_referensi->get_data_instansi_lain($limit, $start, $search);
        $data['list_data'] = $query['data'];
        $data['jumlah'] = $query['jumlah'];
        
        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, 1, $search['key']);
        return $data;
    }
    
    function manage_instansi_lain($status, $page = null) {
        $limit = 10;
        switch ($status) {
            case 'list':
                $search['key'] = $_GET['search'];
                $search['id']  = $_GET['id'];
                $data = $this->get_list_data_instansi_lain($limit, $page, $search);
                $this->load->view('masterdata/instansi-lain-list', $data);
                break;
            case 'save': 
                $this->m_referensi->save_instansi_lain();
                $data = $this->get_list_data_instansi_lain($limit, 1, null);
                $this->load->view('masterdata/instansi-lain-list', $data);
                break;
            case 'delete': 
                $this->m_referensi->delete_instansi_lain($_GET['id']);
                break;
            
        }
    }
    
    /*MANAGEMEN asuransi*/
    function asuransi() {
        $data['title'] = 'Data asuransi';
        $this->load->view('masterdata/asuransi', $data);
    }
    
    function get_list_data_asuransi($limit, $page, $search) {
        if ($page === 'undefined' or $page === '') {
            $page = 1;
        }
        //$str = 'null';
        $start = ($page - 1) * $limit;
        $data['page'] = $page;
        $data['limit'] = $limit;
        $data['auto'] = $start+1;
        $query = $this->m_referensi->get_data_asuransi($limit, $start, $search);
        $data['list_data'] = $query['data'];
        $data['jumlah'] = $query['jumlah'];
        
        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, 1, $search['key']);
        return $data;
    }
    
    function manage_asuransi($status, $page = null) {
        $limit = 10;
        switch ($status) {
            case 'list':
                $search['key'] = $_GET['search'];
                $search['id']  = $_GET['id'];
                $data = $this->get_list_data_asuransi($limit, $page, $search);
                $this->load->view('masterdata/asuransi-list', $data);
                break;
            case 'save': 
                $this->m_referensi->save_asuransi();
                $data = $this->get_list_data_asuransi($limit, 1, null);
                $this->load->view('masterdata/asuransi-list', $data);
                break;
            case 'delete': 
                $this->m_referensi->delete_asuransi($_GET['id']);
                break;
        }
    }
    
    /*MANAGEMEN bank*/
    function bank() {
        $data['title'] = 'Data bank';
        $this->load->view('masterdata/bank', $data);
    }
    
    function get_list_data_bank($limit, $page, $search) {
        if ($page === 'undefined' or $page === '') {
            $page = 1;
        }
        //$str = 'null';
        $start = ($page - 1) * $limit;
        $data['page'] = $page;
        $data['limit'] = $limit;
        $data['auto'] = $start+1;
        $query = $this->m_referensi->get_data_bank($limit, $start, $search);
        $data['list_data'] = $query['data'];
        $data['jumlah'] = $query['jumlah'];
        
        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, 1, $search['key']);
        return $data;
    }
    
    function manage_bank($status, $page = null) {
        $limit = 10;
        switch ($status) {
            case 'list':
                $search['key'] = $_GET['search'];
                $search['id']  = $_GET['id'];
                $data = $this->get_list_data_bank($limit, $page, $search);
                $this->load->view('masterdata/bank-list', $data);
                break;
            case 'save': 
                $this->m_referensi->save_bank();
                $data = $this->get_list_data_bank($limit, 1, null);
                $this->load->view('masterdata/bank-list', $data);
                break;
            case 'delete': 
                $this->m_referensi->delete_bank($_GET['id']);
                break;
        }
    }
    
    /*MANAGEMEN customer*/
    function customer() {
        $data['title'] = 'Data customer';
        $data['asuransi'] = $this->m_referensi->load_data_asuransi()->result();
        $this->load->view('masterdata/pasien', $data);
    }
    
    function get_list_data_customer($limit, $page, $search) {
        
        if ($page === 'undefined' or $page === '') {
            $page = 1;
        }
        //$str = 'null';
        $start = ($page - 1) * $limit;
        $data['page'] = $page;
        $data['limit'] = $limit;
        $data['auto'] = $start+1;
        $query = $this->m_referensi->get_data_customer($limit, $start, $search);
        $data['list_data'] = $query['data'];
        $data['jumlah'] = $query['jumlah'];
        
        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, 1, $search['key']);
        return $data;
    }
    
    function manage_customer($status, $page = null) {
        $limit = 15;
        switch ($status) {
            case 'list':
                $search['key'] = $_GET['search'];
                $search['id']  = $_GET['id'];
                $data = $this->get_list_data_customer($limit, $page, $search);
                $this->load->view('masterdata/pasien-list', $data);
                break;
            case 'save': 
                $this->m_referensi->save_customer();
                $data = $this->get_list_data_customer($limit, 1, null);
                $this->load->view('masterdata/pasien-list', $data);
                break;
            case 'delete': 
                $this->m_referensi->delete_customer($_GET['id']);
                break;
        }
    }
    
    /*LAYANAN*/
    /*function layanan() {
        $data['title'] = 'Data Spesialisasi';
        $this->load->view('masterdata/layanan', $data);
    }*/
    
    function get_list_data_layanan($limit, $page, $search) {
        if ($page == 'undefined' or $page === '') {
            $page = 1;
        }
        //$str = 'null';
        $start = ($page - 1) * $limit;
        $data['page'] = $page;
        $data['limit'] = $limit;
        $data['auto'] = $start+1;
        $query = $this->m_referensi->get_data_layanan($limit, $start, $search);
        $data['list_data'] = $query['data'];
        $data['jumlah'] = $query['jumlah'];
        
        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, 1, $search['key']);
        return $data;
    }
    
//    function manage_layanan($status, $page = null) {
//        $limit = 10;
//        switch ($status) {
//            case 'list':
//                $search['key'] = $_GET['search'];
//                $search['id']  = $_GET['id'];
//                $data = $this->get_list_data_layanan($limit, $page, $search);
//                $this->load->view('masterdata/layanan-list', $data);
//                break;
//            case 'save': 
//                $this->m_referensi->save_layanan();
//                $data = $this->get_list_data_layanan($limit, 1, null);
//                $this->load->view('masterdata/layanan-list', $data);
//                break;
//            case 'delete': 
//                $this->m_referensi->delete_layanan($_GET['id']);
//                break;
//            
//        }
//    }
    
    /*DOKTER*/
    function dokter() {
        $data['title'] = 'Data Dokter';
        $data['spesialis'] = $this->m_referensi->load_data_spesialis()->result();
        $this->load->view('masterdata/dokter', $data);
    }
    
    function get_list_data_dokter($limit, $page, $search) {
        if ($page === 'undefined' or $page === '') {
            $page = 1;
        }
        //$str = 'null';
        $start = ($page - 1) * $limit;
        $data['page'] = $page;
        $data['limit'] = $limit;
        $data['auto'] = $start+1;
        $query = $this->m_referensi->get_data_dokter($limit, $start, $search);
        $data['list_data'] = $query['data'];
        $data['jumlah'] = $query['jumlah'];
        
        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, 1, $search['key']);
        return $data;
    }
    
    function manage_dokter($status, $page = null) {
        $limit = 15;
        switch ($status) {
            case 'list':
                $search['key'] = $_GET['search'];
                $search['id']  = $_GET['id'];
                $data = $this->get_list_data_dokter($limit, $page, $search);
                $this->load->view('masterdata/dokter-list', $data);
                break;
            case 'save': 
                $this->m_referensi->save_dokter();
                $data = $this->get_list_data_dokter($limit, 1, null);
                $this->load->view('masterdata/dokter-list', $data);
                break;
            case 'delete': 
                $this->m_referensi->delete_dokter($_GET['id']);
                break;
        }
    }
    
    /*Tarif*/
    function tindakan() {
        $data['layanan'] = $this->m_referensi->get_data_pemeriksaan_tindakan()->result();
        $this->load->view('masterdata/tindakan', $data);
    }
    
    function get_list_data_tindakan($limit, $page, $search) {
        if ($page == 'undefined' or $page === '') {
            $page = 1;
        }
        //$str = 'null';
        $start = ($page - 1) * $limit;
        $data['page'] = $page;
        $data['limit'] = $limit;
        $data['auto'] = $start+1;
        $query = $this->m_referensi->get_data_tindakan($limit, $start, $search);
        $data['list_data'] = $query['data'];
        $data['jumlah'] = $query['jumlah'];
        
        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, 1, $search['key']);
        return $data;
    }
    
//    function manage_tindakan($status, $page = null) {
//        $limit = 15;
//        switch ($status) {
//            case 'list':
//                $search['key'] = $_GET['search'];
//                $search['id']  = $_GET['id'];
//                $data = $this->get_list_data_tindakan($limit, $page, $search);
//                $this->load->view('masterdata/tindakan-list', $data);
//                break;
//            case 'save': 
//                $data = $this->m_referensi->save_tindakan();
//                die(json_encode($data));
//                break;
//            case 'delete': 
//                $this->m_referensi->delete_tindakan($_GET['id']);
//                break;
//            
//        }
//    }
    
    /*function tarif() {
        $data['title'] = 'Data tarif';
        $this->load->view('masterdata/tarif', $data);
    }*/
    
    function komponen_tarif() {
        
        $data['title'] = 'Komponen tarif';
        $data['kelas'] = $this->m_referensi->kelas_load_data();
        $data['bangsal'] = $this->m_referensi->bangsal_load_data()->result();
        $data['bobot'] = $this->m_referensi->bobot_load_data();
        $this->load->view('masterdata/komponen-tarif', $data);
    }
    
    function get_list_data_komponen_tarif($limit, $page, $search) {
        if ($page == 'undefined' or $page === '') {
            $page = 1;
        }
        //$str = 'null';
        $start = ($page - 1) * $limit;
        $data['page'] = $page;
        $data['limit'] = $limit;
        $data['auto'] = $start+1;
        $query = $this->m_referensi->get_data_komponen_tarif($limit, $start, $search);
        $data['list_data'] = $query['data'];
        $data['jumlah'] = $query['jumlah'];
        
        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, 1, $search['key']);
        return $data;
    }
    
    function manage_komponen_tarif($status, $page = null) {
        $limit = 10;
        switch ($status) {
            case 'list':
                $search['key'] = $_GET['search'];
                $search['id']  = $_GET['id'];
                $data = $this->get_list_data_komponen_tarif($limit, $page, $search);
                $this->load->view('masterdata/komponen-tarif-list', $data);
                break;
            case 'save': 
                $data = $this->m_referensi->save_komponen_tarif();
                die(json_encode($data));
                break;
            case 'delete': 
                $this->m_referensi->delete_tarif($_GET['id']);
                break;
            
        }
    }
    
    function kamar() {
        $data['title'] = 'Data Bangsal & Bed';
        $this->load->view('masterdata/kamar', $data);
    }
    
    function bangsal() {
        $this->load->view('masterdata/bangsal');
    }
    
    function get_list_data_bangsal($limit, $page, $search) {
        if ($page == 'undefined' or $page === '') {
            $page = 1;
        }
        $start = ($page - 1) * $limit;
        $data['page'] = $page;
        $data['limit'] = $limit;
        $data['auto'] = $start+1;
        $query = $this->m_referensi->get_data_bangsal($limit, $start, $search);
        $data['list_data'] = $query['data'];
        $data['jumlah'] = $query['jumlah'];
        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, 1, $search['key']);
        return $data;
    }
    
    function manage_bangsal($status, $page = null) {
        $limit = 15;
        switch ($status) {
            case 'list':
                $search['key'] = $_GET['search'];
                $search['id']  = $_GET['id'];
                $data = $this->get_list_data_bangsal($limit, $page, $search);
                $this->load->view('masterdata/bangsal-list', $data);
                break;
            case 'save': 
                $data = $this->m_referensi->save_bangsal();
                die(json_encode($data));
                break;
            case 'delete': 
                $this->m_referensi->delete_bangsal($_GET['id']);
                break;
            
        }
    }
    
    function bed() {
        $query = $this->m_referensi->get_data_bangsal();
        $data['bangsal'] = $query['data'];
        $data['kelas'] = $this->m_referensi->kelas_load_data();
        $this->load->view('masterdata/bed', $data);
    }
    
    function get_list_data_bed($limit, $page, $search) {
        if ($page == 'undefined' or $page === '') {
            $page = 1;
        }
        $start = ($page - 1) * $limit;
        $data['page'] = $page;
        $data['limit'] = $limit;
        $data['auto'] = $start+1;
        $query = $this->m_referensi->get_data_bed($limit, $start, $search);
        $data['list_data'] = $query['data'];
        $data['jumlah'] = $query['jumlah'];
        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, 1, $search['key']);
        return $data;
    }
    
    function manage_bed($status, $page = null) {
        $limit = 15;
        switch ($status) {
            case 'list':
                $search['key'] = $_GET['search'];
                $search['id']  = $_GET['id'];
                $data = $this->get_list_data_bed($limit, $page, $search);
                $this->load->view('masterdata/bed-list', $data);
                break;
            case 'save': 
                $data = $this->m_referensi->save_bed();
                die(json_encode($data));
                break;
            case 'delete': 
                $this->m_referensi->delete_bed($_GET['id']);
                break;
            case 'load_data_bed_kosong': 
                $search['bangsal'] = get_param('bangsal');
                $search['kelas']   = get_param('kelas');
                $data['list_data'] = $this->m_referensi->load_data_bed_kosong($search)->result();
                $this->load->view('pelayanan/rawat-inap-list-bed', $data);
                break;
            
        }
    }

}

?>