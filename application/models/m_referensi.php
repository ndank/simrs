<?php

class M_referensi extends CI_Model {

    function get_attribute_rs() {
        $sql = "select * from rumah_sakit";
        return $this->db->query($sql);
    }
    
    function get_apoteker() {
        $sql = "select * from rumah_sakit";
        return $this->db->query($sql);
    }
    
    function ubah_password($id, $data) {
        $this->db->where('id', $id);
        $this->db->update('users', $data);
    }


    function get_user_detail($id) {
        $this->db->where('id', $id);
        $query = $this->db->get('users');
        return $query->row();
    }


    function unit_get_data($jenis = NULL) {
        $sort = NULL;
        if ($jenis != NULL) {
            $sort = "where jenis = '$jenis'";
        }


        $sql = "select id, nama from unit $sort order by nama";

        $query = $this->db->query($sql)->result();
        $data[''] = "Pilih unit";
        foreach ($query as $value) {
            $data[$value->id] = $value->nama;
        }
        return $data;
    }

    function unit_layanan_load_data($jenis, $q){
        $sort = NULL;
        if ($jenis != NULL) {
            $sort = "jenis = '$jenis' and";
        }
        $order =  "order by locate('$q', nama)";
        if ($q == '') {
            $order = " order by nama asc";
        }
        $sql = "select id, nama from unit where $sort  nama like ('%$q%') $order";
        return $this->db->query($sql);
    }

    function perundangan_load_data() {
        $array = array(
            '' => 'Semua perundangan ...',
            'Bebas' => 'Bebas',
            'Bebas Terbatas' => 'Bebas Terbatas',
            'OWA' => 'OWA',
            'Keras' => 'Keras',
            'Psikotropika' => 'Psikotropika',
            'Narkotika' => 'Narkotika'
        );
        return $array;
    }

    function generik_load_data() {
        $array = array(
            '' => 'Semua ..',
            'Generik' => 'Generik',
            'Non Generik' => 'Non Generik'
        );
        return $array;
    }

    function kolom_multiselect() {
        $array = array(
            'HPP',
            'HNA',
            'HET',
            'Alasan',
            'Awal',
            'Masuk',
            'Keluar',
            'No. Transaksi',
            'Jenis Transaksi',
            'Tanggal',
            'Packing Barang',
            'ED',
            'Harga',
            'Sisa'
        );
        return $array;
    }

    function adm_r_get_data() {
        $array = array(
            'Inhalasi', 'Oral', 'Rektal', 'Infus', 'Topikal', 'Sublingual', 'Intrakutan', 'Subkutan', 'Intravena', 'Intramuskuler', 'Vagina', 'Injeksi', 'Intranasal', 'Intraokuler', 'Intraaurikuler', 'Intrapulmonal', 'Implantasi', 'Intralumbal', 'Intrarteri'
        );
        sort($array);
        $rows[] = 'Pilih';
        foreach ($array as $val) {
            $rows[$val] = $val;
        }
        return $rows;
    }

    
    /* Masterdata Unit */

    function cek_unit($unit) {
        $unit = $this->db->escape_str($unit);
        $db = "select count(*) as jumlah from unit where nama = '$unit'";
        $query = $this->db->query($db);
        return $query->row();
    }

    function add_unit($unit) {
        $unit = $this->db->escape_str($unit);
        $db = "insert into unit (id, nama) values ('','$unit')";
        $this->db->query($db);
        return $this->db->insert_id();
    }

    function delete_unit($id) {
        $db = "delete from unit where id = '$id'";
        $this->db->query($db);
    }

    function edit_unit($data) {
        $this->db->where('id', $data['id']);
        $this->db->update('unit', $data);
    }

    /* Masterdata Unit */

    /* Produk Asuransi */

    function get_produk_asuransi_data($limit, $start, $search) {
        $q = '';
        if (isset($search['id_perusahaan']) && ($search['id_perusahaan'] != '')) {
            $q .= " and ap.relasi_instansi_id = '" . $search['id_perusahaan'] . "'";
        }

        if (isset($search['nama']) && ($search['nama'] != '')) {
            $q .= " and ap.nama like '%" . $search['nama'] . "%' ";
        }

        if (isset($search['id']) && ($search['id'] != '')) {
            $q = " and ap.id = '" . $search['id'] . "'";
        }
        $limit = " limit $start , $limit";
        $sort = "order by ap.nama , r.nama asc";
        $sql = "select ap.*, r.nama as prsh, r.id as id_ap from asuransi_produk ap
        join relasi_instansi r on (ap.relasi_instansi_id = r.id)
        where ap.id is not null ";
        $query = $this->db->query($sql . $q . $sort . $limit);
        $ret['data'] = $query->result();
        $ret['jumlah'] = $this->db->query($sql . $q)->num_rows();
        return $ret;
    }

    function add_produk_asuransi_data($data) {
        $this->db->insert('asuransi_produk', $data);
        return $this->db->insert_id();
    }

    function relasi_instansi_data($q) {
        $sql = "select i.*, j.nama as jenis from relasi_instansi i
        join relasi_instansi_jenis j on (i.relasi_instansi_jenis_id = j.id)
        where i.nama like ('%$q%') and j.nama = 'Asuransi' order by locate('$q', i.nama)";
        $query = $this->db->query($sql);
        return $query->result();
    }

    function produk_cek_data($data) {
        $sql = "select count(*) as jumlah from asuransi_produk 
            where nama = '" . $data['nama'] . "' and relasi_instansi_id = '" . $data['relasi'] . "' ";
        $query = $this->db->query($sql);
        $jml = $query->row()->jumlah;
        if ($jml == 0) {
            return true;
        } else {
            return false;
        }
    }

    function produk_asuransi_last_no() {
        $sql = "select max(id) as last from asuransi_produk";
        $row = $this->db->query($sql)->row();
        if ($row != null) {
            $last = $row->last;
            $last++;
        } else {
            $last = 1;
        }

        return $last;
    }

    function delete_produk_asuransi($id) {
        $db = "delete from asuransi_produk where id = '$id'";
        $this->db->query($db);
    }

    function edit_produk_asuransi_data($data) {
        $this->db->where('id', $data['id']);
        $this->db->update('asuransi_produk', $data);
    }

    function add_relasi_instansi_data($data) {
        $this->db->insert('relasi_instansi', $data);
        return $this->db->insert_id();
    }

    /* Produk Asuransi */

    /* Data Wilayah */

    function provinsi_data($q) {
        $q = mysql_real_escape_string($q);
        $sql = "select * from provinsi where nama like ('%$q%') order by locate ('$q', nama)";
        //echo $sql;
        $query = $this->db->query($sql);
        return $query->result();
    }

    function provinsi_get_data($limit = null, $start = null, $search = null) {
        $w = "";
        $page = "  limit $start ,$limit";
        if (isset($search['provinsi'])&&($search['provinsi'] != '')) {
            $w .= " and p.nama like '%".$search['provinsi']."%' ";
        }
        if (isset($search['kode']) && ($search['kode'] != '')) {
            $w .= " and p.kode = '".$search['kode']."' ";
        }
        if (isset($search['id'])) {
            $w = " and p.id = '".$search['id']."' ";
        }
        $sql = "select p.* from 
            provinsi p where p.id is not null $w order by nama asc";
        //echo $sql;
        $query = $this->db->query($sql . $page);
        $ret['data'] = $query->result();
        $ret['jumlah'] = $this->db->query($sql)->num_rows();
        return $ret;
    }

    function provinsi_add_data($data) {
        $this->db->insert('provinsi', $data);
        return $this->db->insert_id();
    }

    function provinsi_delete_data($id) {
        $db = "delete from provinsi where id = '$id'";
        $this->db->query($db);
    }

    function provinsi_edit_data($data) {
        $this->db->where('id', $data['id']);
        $this->db->update('provinsi', $data);
    }

    function provinsi_cek_data($data) {

        $sql = "select count(*) as jumlah from provinsi where nama = '" . $data['nama'] . "'";
        $query = $this->db->query($sql);
        $jml = $query->row()->jumlah;
        if ($jml == 0) {
            return true;
        } else {
            return false;
        }
    }

    function kabupaten_data($q) {
        $sql = "select k.*, p.nama as provinsi, p.id as id_provinsi FROM kabupaten k
        join provinsi p on (k.provinsi_id = p.id)
        where k.nama like ('%$q%') order by locate ('$q', k.nama)";
        $query = $this->db->query($sql);
        return $query->result();
    }

    function kabupaten_get_data($limit, $start, $search) {
        $w = "";
        $page = "  limit $start ,$limit";

        if (isset($search['kabupaten'])&&($search['kabupaten'] != '')) {
            $w .= " and k.nama like '%".$search['kabupaten']."%' ";
        }

        if (isset($search['idprovinsikab']) && ($search['idprovinsikab'] != '')) {
            $w .= " and k.provinsi_id = '".$search['idprovinsikab']."' ";
        }

        if (isset($search['kode']) && ($search['kode'] != '')) {
            $w .= " and k.kode = '".$search['kode']."' ";
        }

        if (isset($search['id'])) {
            $w = " and k.id = '".$search['id']."' ";
        }
        $sql = "select k.*, p.nama as provinsi, p.id as id_provinsi FROM kabupaten k
        join provinsi p on (k.provinsi_id = p.id) where k.id is not null $w
            order by nama asc";
        //echo $sql;
        $query = $this->db->query($sql . $page);
        $ret['data'] = $query->result();
        $ret['jumlah'] = $this->db->query($sql)->num_rows();
        return $ret;
    }

    function kabupaten_add_data($data) {
        $this->db->insert('kabupaten', $data);
        return $this->db->insert_id();
    }

    function kabupaten_delete_data($id) {
        $db = "delete from kabupaten where id = '$id'";
        $this->db->query($db);
    }

    function kabupaten_edit_data($data) {
        $this->db->where('id', $data['id']);
        $this->db->update('kabupaten', $data);
    }

    function kabupaten_cek_data($data) {

        $sql = "select count(*) as jumlah from kabupaten 
            where provinsi_id = '" . $data['provinsi_id'] . "' and nama = '" . $data['nama'] . "' ";
        $query = $this->db->query($sql);
        $jml = $query->row()->jumlah;
        if ($jml == 0) {
            return true;
        } else {
            return false;
        }
    }

    function kecamatan_data($q) {
        $sql = "select kc.*, k.nama as kabupaten, k.id as id_kabupaten, p.nama as provinsi FROM kecamatan kc
        join kabupaten k on (kc.kabupaten_id = k.id)
        join provinsi p on (k.provinsi_id = p.id)
        where kc.nama like ('%$q%') order by locate ('$q', kc.nama)";
        $query = $this->db->query($sql);
        return $query->result();
    }

    function kecamatan_get_data($limit, $start, $search) {
        $w = "";
        $page = "  limit $start ,$limit";

        if (isset($search['kecamatan'])&&($search['kecamatan'] != '')) {
            $w .= " and kc.nama like '%".$search['kecamatan']."%' ";
        }

        if (isset($search['idkabupatenkec']) && ($search['idkabupatenkec'] != '')) {
            $w .= " and kc.kabupaten_id = '".$search['idkabupatenkec']."' ";
        }

        if (isset($search['kode']) && ($search['kode'] != '')) {
            $w .= " and kc.kode = '".$search['kode']."' ";
        }

        if (isset($search['id'])) {
            $w = " and kc.id = '".$search['id']."' ";
        }

        $sql = "select  kc.*, k.nama as kabupaten, k.id as kabupaten_id FROM kecamatan kc
        left join kabupaten k on (kc.kabupaten_id = k.id)
        left join provinsi p on (k.provinsi_id = p.id) 
        where kc.id is not null $w
        order by nama asc ";
        //echo $sql;
        $query = $this->db->query($sql . $page);
        $ret['data'] = $query->result();
        $ret['jumlah'] = $this->db->query($sql)->num_rows();
        return $ret;
    }

    function kecamatan_add_data($data) {
        $this->db->insert('kecamatan', $data);
        return $this->db->insert_id();
    }

    function kecamatan_delete_data($id) {
        $db = "delete from kecamatan where id = '$id'";
        $this->db->query($db);
    }

    function kecamatan_edit_data($data) {
        $this->db->where('id', $data['id']);
        $this->db->update('kecamatan', $data);
    }

    function kecamatan_cek_data($data) {

        $sql = "select count(*) as jumlah from kecamatan where kabupaten_id = '" . $data['kabupaten_id'] . "' 
            and nama = '" . $data['nama'] . "'";
        $query = $this->db->query($sql);
        $jml = $query->row()->jumlah;
        if ($jml == 0) {
            return true;
        } else {
            return false;
        }
    }

    function kelurahan_get_data($limit, $start, $search) {
        $w = "";
        $page = "  limit $start ,$limit";

        if (isset($search['kelurahan'])&&($search['kelurahan'] != '')) {
            $w .= " and kl.nama like '%".$search['kelurahan']."%' ";
        }

        if (isset($search['idkecamatankel']) && ($search['idkecamatankel'] != '')) {
            $w .= " and kl.kecamatan_id = '".$search['idkecamatankel']."' ";
        }

        if (isset($search['kode']) && ($search['kode'] != '')) {
            $w .= " and kl.kode = '".$search['kode']."' ";
        }

        if (isset($search['id'])) {
            $w = " and kl.id = '".$search['id']."' ";
        }

        $sql = "select kl.*, kc.nama as kecamatan, kc.id as id_kecamatan FROM kelurahan kl
        left join kecamatan kc on (kl.kecamatan_id = kc.id)
        left join kabupaten k on (kc.kabupaten_id = k.id)
        left join provinsi p on (k.provinsi_id = p.id) 
        where kl.id is not null $w
        order by nama asc";
        //echo $sql;
        $query = $this->db->query($sql . $page);
        $ret['data'] = $query->result();
        $ret['jumlah'] = $this->db->query($sql)->num_rows();
        return $ret;
    }

    function kelurahan_data($q) {
        $sql = "select  kl.*, kc.nama as kecamatan, kb.id as id_kabupaten, kb.nama as kabupaten, p.nama as provinsi from kelurahan kl
            join kecamatan kc on (kl.kecamatan_id = kc.id)
            join kabupaten kb on (kc.kabupaten_id = kb.id)
            join provinsi p on (kb.provinsi_id = p.id)
            where kl.nama like ('%$q%') order by locate('$q', kl.nama)";
        $query = $this->db->query($sql);
        return $query->result();
    }

    function kelurahan_add_data($data) {
        $this->db->insert('kelurahan', $data);
        return $this->db->insert_id();
    }

    function kelurahan_delete_data($id) {
        $db = "delete from kelurahan where id = '$id'";
        $this->db->query($db);
    }

    function kelurahan_edit_data($data) {
        $this->db->where('id', $data['id']);
        $this->db->update('kelurahan', $data);
    }

    function kelurahan_cek_data($data) {

        $sql = "select count(*) as jumlah from kelurahan where kecamatan_id = '" . $data['kecamatan_id'] . "' 
            and nama = '" . $data['nama'] . "'";
        $query = $this->db->query($sql);
        $jml = $query->row()->jumlah;
        if ($jml == 0) {
            return true;
        } else {
            return false;
        }
    }

    /* Data Wilayah */

    /* Instansi Relasi */

    function relasi_instansi_jenis_get_data() {
        $query = $this->db->get('relasi_instansi_jenis');
        return $query->result();
    }

    function instansi_get_data($limit, $start, $search) {
        $q = '';

        $limit = " limit $start , $limit";
        if (isset($search['nama']) && ($search['nama'] != '')) {
            $q .= " and r.nama like '%" . $search['nama'] . "%' ";
        }

        if (isset($search['alamat']) && ($search['alamat'] != '')) {
            $q .= " and r.alamat like '%" . $search['alamat'] . "%' ";
        }

        if (isset($search['id_kelurahan']) && ($search['id_kelurahan'] != '')) {
            $q .= " and r.kelurahan_id = '" . $search['id_kelurahan'] . "' ";
        }

        if (isset($search['jenis']) && ($search['jenis'] != '')) {
            $q .= " and r.relasi_instansi_jenis_id = '" . $search['jenis'] . "' ";
        }


        if (($search != 'null') & (isset($search['id']) and $search['id'] != 'null')) {
            $q = " and r.id = '" . $search['id'] . "' ";
        }
        $sql = "select @row := @row + 1 as nomor, r.*, rj.id as jenis_id,
                rj.nama as jenis, k.id as kelurahan_id, k.nama as kelurahan
                from relasi_instansi r
                left join relasi_instansi_jenis rj on(rj.id = r.relasi_instansi_jenis_id)
                left join kelurahan k on (k.id = r.kelurahan_id), (SELECT @row := $start) rr 
                where r.id is not null $q order by r.nama asc  ";
        //echo $sql;
        
        $query = $this->db->query($sql . $limit);
        $ret['data'] = $query->result();
        $ret['jumlah'] = $this->db->query($sql)->num_rows();
        return $ret;
    }

    function count_instansi_data() {
        $sql = "select count(id) as jumlah from relasi_instansi";
        $query = $this->db->query($sql);
        return $query->row()->jumlah;
    }

    function instansi_add_data($data) {
        $this->db->insert('relasi_instansi', $data);
        return $this->db->insert_id();
    }

    function instansi_delete_data($id) {
        $db = "delete from relasi_instansi where id = '$id'";
        $this->db->query($db);
    }

    function instansi_edit_data($data) {
        $this->db->where('id', $data['id']);
        $this->db->update('relasi_instansi', $data);
    }

    function instansi_cek_data($data) {

        $instansi = "";

        if ($data['instansi'] != '') {
            $instansi = "where nama = '" . $data['instansi'] . "'";
        }

        $sql = "select count(*) as jumlah from relasi_instansi
          $instansi";
        $exe = $this->db->query($sql)->row();

        if ($exe->jumlah == 0) {
            $status = true;
        } else {
            $status = false;
        }
        die(json_encode(array('status' => $status)));
    }

    /* Instansi Relasi */

    /* User Account */
    function group_get_data($limit, $start, $search){
        $q = '';
        if (isset($search['id']) && ($search['id'] !== '')) {
            $q .= " and id = '".$search['id']."'";
        }
        $limit = " limit $start, $limit ";
        $sql = "select * from user_group where id is not null $q order by nama";
        $query = $this->db->query($sql . $limit);
        $ret['data'] = $query->result();
        $ret['jumlah'] = $this->db->query($sql)->num_rows();
        return $ret;
    }

    function get_user_group(){
        $query = $this->db->order_by('nama')->get('user_group')->result();

        $data = array();
        $data[''] = "Pilih...";
        foreach ($query as $key => $value) {
            $data[$value->id] = $value->nama;
        }

        return $data;
    }

    function group_update_data($data){
        if ($data['id'] === '') {
            // insert
            $this->db->insert('user_group', $data);
            $id = $this->db->insert_id();
        }else{
            // update
            $this->db->where('id', $data['id']);
            $this->db->update('user_group', $data);
            $id = $data['id'];
        }

        return $id;

    }

    function group_delete_data($id) {
        $db = "delete from user_group where id = '$id'";
        $this->db->query($db);
    }

    function user_get_data($limit, $start, $search) {
        $q = '';
        $limit = " limit $start, $limit ";
        if (isset($search['id']) && $search['id'] != '') {
            $q.=" and u.id = '" . $search['id'] . "'";
        }
        if (isset($search['nama']) && $search['nama'] != '') {
            $q.=" and p.nama like ('%$search[nama]%')";
        }
        if (isset($search['username']) && $search['username'] != '') {
            $q.=" and u.username like ('%$search[username]%')";
        }
        $sql = "select p.*, u.username, un.id as unit_id,
        un.nama as unit, u.status, 
        ug.id as group_id, ug.nama as user_group from penduduk p 
        join users u on (p.id = u.id)
        left join unit un on (un.id = p.unit_id) 
        left join user_group ug on (ug.id = u.user_group_id)
        where p.id is not NULL $q
        order by username asc ";

        //echo $sql."<br/><br/>";
        $query = $this->db->query($sql . $limit);
        $ret['data'] = $query->result();
        $ret['jumlah'] = $this->db->query($sql)->num_rows();
        return $ret;
    }

    function biaya_apoteker_load_data() {
        $sql = "select t.id, l.nama as layanan,  t.nominal, pf.nama as profesi, 
        jkp.nama as nama_jkp, u.nama as unit, t.bobot, t.kelas, t.jenis_pelayanan_kunjungan as jenis from tarif t
        join layanan l on (t.id_layanan = l.id)
        left join profesi pf on (pf.id = t.id_profesi)
        left join jurusan_kualifikasi_pendidikan jkp on (jkp.id = t.id_jurusan_kualifikasi_pendidikan)
        left join unit u on (u.id = t.id_unit)
        where l.nama = 'Pelayanan Resep'";
        return $this->db->query($sql);
    }

    function user_add_data($data) {
        if (post_safe('id') == '') {
            $this->db->insert('users', $data);
        } else {
            $data['password'] = md5('1234');
            $this->db->where('id', post_safe('id'));
            $this->db->update('users', $data);
        }

        if(post_safe('id_unit') != ''){
            $this->db->where('id', post_safe('id'));
            $this->db->update('penduduk', array('unit_id' => post_safe('id_unit')));
        }
    }

    function user_delete_data($id) {
        $db = "delete from users where id = '$id'";
        $this->db->query($db);
    }

    function get_unit($q){
        $sql = "select * from unit where nama like ('%$q%') order by locate('$q', nama)";
        return $this->db->query($sql);
    }

    function detail_user_data($id) {
        $sql = "select p.*, p.id as id_penduduk, dp.* from penduduk p
            left join dinamis_penduduk dp on (p.id = dp.penduduk_id)
            where p.id =  '$id'";
         // echo $sql;
        $query = $this->db->query($sql);
        return $query->row();
    }

    function group_privileges_data($id) {
        $sql = "select * from user_group_privileges where 
             user_group_id = '" . $id . "'";
        //echo $sql;
        $query = $this->db->query($sql)->result();
        $data = array();
        foreach ($query as $value) {
            $data[] = $value->privileges_id;
        }
        return $data;
    }

    function privileges_get_data() {
        $sql = "select p.*, m.nama as modul from `privileges`p 
            join module m on(p.module_id = m.id)
            order by m.nama, p.form_nama";
        $query = $this->db->query($sql);
        return $query->result();
    }

    function privileges_edit_data($data) {
        $this->db->trans_begin();
        //delete privileges
        $this->db->where('user_group_id', $data['id_group']);
        $this->db->delete('user_group_privileges');

        // add privileges
       // echo "-".$data['privileges']."-";
        if (is_array($data['privileges'])) {
            foreach ($data['privileges'] as $value) {
            $insert = array(
                'user_group_id' => $data['id_group'],
                'privileges_id' => $value
            );
            $this->db->insert('user_group_privileges', $insert);
        }
        }
        



        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $status = FALSE;
        } else {
            $this->db->trans_commit();
            $status = TRUE;
        }
    }

    /* User Account */

    /* barang */

    function kategori_barang_get_data($id = null, $jenis = null) {
        $q = null;
        if ($id != null) {
            $q = "where id = '$id'";
        }
        if($jenis != null){
            $q = " where jenis = '$jenis' ";
        }
        $sql = "select * from barang_kategori $q order by nama asc";
        $query = $this->db->query($sql);

        return $query->result();
    }

    function satuan_get_data($id = null) {
        $q = null;
        if ($id != null) {
            $q.=" where id = '$id'";
        }
        $sql = "select * from satuan $q order by nama asc";
        $query = $this->db->query($sql)->result();
        $data[''] = 'Pilih Satuan';
        foreach ($query as $value) {
            $data[$value->id] = $value->nama;
        }
        return $data;
    }

    function sediaan_get_data($id = null) {
        $q = null;
        if ($id != null) {
            $q.=" where id = '$id'";
        }
        $sql = "select * from sediaan $q order by nama asc";
        $query = $this->db->query($sql)->result();
        $data[''] = 'Semua Sediaan';
        foreach ($query as $value) {
            $data[$value->id] = $value->nama;
        }
        return $data;
    }

    function perundangan_get_data() {
        return array(
            '' => 'Pilih',
            'Bebas' => 'Bebas',
            'Bebas Terbatas' => 'Bebas Terbatas',
            'OWA' => 'OWA',
            'Keras' => 'Keras',
            'Psikotropika' => 'Psikotropika',
            'Narkotika' => 'Narkotika'
        );
    }

    function barang_get_data($limit = null, $start = null, $status = null, $id = null, $nama = null, $pabrik = null,$kategori = null,$arrobat = null, $sort = null) {
        //  echo print_r($arrobat);
        $q = null;
        if ($status != null) {
            if ($status == 'Obat') {
                $q.=" where bk.nama = 'Obat'";// or b.barang_kategori_id IS NULL";
            }else if ($status == 'Rt') {
                $q.=" where bk.jenis = 'Rumah Tangga'";//" or bk.nama IS NULL ";
            }
            else if ($status == 'Gizi') {
                $q.=" where bk.jenis = 'Gizi'";//" or bk.nama IS NULL ";
            }
            else {
                $q.=" where (bk.nama != 'Obat' ) and bk.jenis = 'Farmasi'";
            }
        }
        if ($id != null) {
            $q =" where b.id = '$id'";
        }
        if (isset($arrobat['ha']) and $arrobat['ha'] !== '') {
            $q.=" and o.high_alert = '".$arrobat['ha']."'";
        }
        if (($nama != null) & ($nama != '')) {
            $q.=" and b.nama like ('%$nama%')";
        }
        if ($pabrik != null) {
            $q.=" and b.pabrik_relasi_instansi_id = '$pabrik'";
        }

        if ($kategori != null) {
            $q.=" and bk.id = '$kategori'";
        }

        if (isset($arrobat['kekuatan'])) {
            $q .= " and o.kekuatan = '".$arrobat['kekuatan']."'";
        }

        if (isset($arrobat['satuan'])) {
            $q .= " and o.satuan_id = '".$arrobat['satuan']."'";
        }

        if (isset($arrobat['sediaan'])) {
            $q .= " and o.sediaan_id = '".$arrobat['sediaan']."'";
        }

        if (isset($arrobat['ven'])) {
            $q .= " and o.ven = '".$arrobat['ven']."'";
        }

        if (isset($arrobat['perundangan'])) {
            $q .= " and o.perundangan = '".$arrobat['perundangan']."'";
        }

        if (isset($arrobat['generik'])) {
            $q .= " and o.generik = '".$arrobat['generik']."'";
        }

        if (isset($arrobat['formularium'])) {
            $q .= " and o.formularium = '".$arrobat['formularium']."'";
        }

        if (isset($arrobat['id_pabrik_obat'])) {
            $q .= " and b.pabrik_relasi_instansi_id = '".$arrobat['id_pabrik_obat']."'";
        }

        if ($sort != null) {
            if ($sort == 'asc') {
                $q.=" order by b.nama asc";
            } else {
                $q.=" order by b.nama desc";
            }
        }
        if ($sort == null) {
            $q.=" order by b.nama asc";
        }
        $limitation = null;
        $limitation.=" limit $start , $limit";



        $sql = "select o.*, b.*, bk.nama as kategori, r.id as id_pabrik, r.nama as pabrik, s.nama as satuan, sd.nama as sediaan from barang b
        left join barang_kategori bk on (b.barang_kategori_id = bk.id)
        left join relasi_instansi r on (b.pabrik_relasi_instansi_id = r.id)
        left join obat o on (b.id = o.id)
        left join satuan s on (s.id = o.satuan_id)
        left join sediaan sd on (sd.id = o.sediaan_id)";
        //echo "<pre>".$sql.$q.$limitation."</pre>";
        $query = $this->db->query($sql . $q . $limitation);
        $queryAll = $this->db->query($sql . $q);
        $data['data'] = $query->result();
        $data['jumlah'] = $queryAll->num_rows();
        return $data;
    }

    function barang_delete_data($id, $tipe) {
        // tipe = obat, non_obat
        $db = "delete from barang where id = '$id'";
        $this->db->query($db);

        // delete obat
        if ($tipe == 'obat') {
            $db = "delete from obat where id = '$id'";
            $this->db->query($db);
        }
    }

    function barang_non_cek_data($data) {
        $sql = "select count(*) as jumlah from barang 
            where nama = '" . $data['nama'] . "' ";
        $query = $this->db->query($sql);
        $jml = $query->row()->jumlah;
        if ($jml == 0) {
            return true;
        } else {
            return false;
        }
    }

    function barang_add_data($data, $tipe) {
        $cek = $this->db->query("select count(*) as jumlah from barang")->row();

        if ($cek->jumlah > 50) {
            $this->db->insert('barang', $data['barang']);
            $id = $this->db->insert_id();
            if ($tipe == 'Obat') {
                $data['obat']['id'] = $this->db->insert_id();
                $this->db->insert('obat', $data['obat']);
            }
            return $id;
        } else {
            return FALSE;
        }
    }

    function barang_edit_data($data, $tipe) {
        $this->db->where('id', $data['barang']['id']);
        $this->db->update('barang', $data['barang']);
        if ($tipe == 'Obat') {
            $this->db->where('id', $data['obat']['id']);
            $this->db->update('obat', $data['obat']);
        }
    }

    function obat_cek_data($data) {
        $q = null;

        $exe = $this->db->query("select count(*) as jumlah from barang b
        join obat o on (b.id = o.id) where 
        b.nama  = '" . $data['nama'] . "' ")->row();

        if ($exe->jumlah == 0) {
            $status = true;
        } else {
            $status = false;
        }
        return $status;
    }


    /* Packing Barang */

    function packing_get_data($limit, $start, $id, $cari, $kat) {
        
        $where = '';
        $search = '';
        $q = null;
        $q.=" limit " . $start . ", $limit";
        $order = ' order by b.nama asc';
        $sql = "select o.id as id_obat, o.generik, bp.*, 
        s.nama as s_besar, st.nama as s_kecil, b.nama, 
        o.kekuatan, stn.nama as satuan_obat, sd.nama as sediaan,
        sd.nama as sediaan_obat, stn.nama as satuan_obat, b.id as id_barang,
        r.nama as pabrik, CONCAT_WS(' ',b.nama,o.kekuatan,stn.nama,sd.nama,o.generik,r.nama) as nama_obat
        from barang_packing bp
        join barang b on (b.id = bp.barang_id)
        left join barang_kategori bk on (bk.id = b.barang_kategori_id)
        left join relasi_instansi r on (b.pabrik_relasi_instansi_id = r.id)
        join satuan s on (s.id = bp.terbesar_satuan_id)
        join satuan st on (st.id = bp.terkecil_satuan_id)
        left join obat o on (b.id = o.id)
        left join satuan stn on (o.satuan_id = stn.id)
        left join sediaan sd on (o.sediaan_id = sd.id) where bp.id is not NULL";

        if ($id != 'null') {
            $where = " and bp.id = '" . $id . "' ";
        }
        if (isset($cari['barcode']) && ($cari['barcode'] != NULL)) {
            $search .= " and bp.barcode like '%" . $cari['barcode'] . "%' ";
        }

        if (isset($cari['id_barang']) && ($cari['id_barang'] != NULL)) {
            $search .= " and b.id = '" . $cari['id_barang'] . "' ";
        }

        if (isset($cari['kemasan']) && ($cari['kemasan'] != NULL)) {
            $search .= " and bp.terbesar_satuan_id = '" . $cari['kemasan'] . "' ";
        }

        if (isset($cari['isi']) && ($cari['isi'] != NULL)) {
            $search .= " and bp.isi = '" . $cari['isi'] . "' ";
        }

        if (isset($cari['satuan']) && ($cari['satuan'] != NULL)) {
            $search .= " and bp.terkecil_satuan_id = '" . $cari['satuan'] . "' ";
        }
        if ($kat != '') {
            $where.= " and bk.jenis = '$kat'";
        }
        //echo $sql . $where . $search . $order . $q;
        $query = $this->db->query($sql . $where . $search . $order . $q);
        $ret['data'] = $query->result();
        $ret['jumlah'] = $this->db->query($sql . $where . $search . $order)->num_rows();
        return $ret;
    }

    function packing_add_data($data) {
        $this->db->insert('barang_packing', $data);
        $id = $this->db->insert_id();
        if ($data['barcode'] == '') {
            $edit = array(
                'barcode' => $id
            );
            $this->db->where('id', $id);
            $this->db->update('barang_packing', $edit);
        }
        return $id;
    }

    function packing_delete_data($id) {
        $db = "delete from barang_packing where id = '$id'";
        $this->db->query($db);
    }

    function packing_edit_data($data) {
        $this->db->where('id', $data['id']);
        $this->db->update('barang_packing', $data);
    }

    function packing_cek_data($data) {
        $sql = "select count(*) as jumlah from barang_packing
        where barcode = '" . $data['barcode'] . "' 
        and barang_id = '" . $data['id_barang'] . "' 
        and terbesar_satuan_id = '" . $data['kemasan'] . "' 
        and isi = '" . $data['isi'] . "' 
        and terkecil_satuan_id = '" . $data['satuan'] . "'";
        $exe = $this->db->query($sql)->row();

        if ($exe->jumlah == 0) {
            $status = true;
        } else {
            $status = false;
        }
        return $status;
    }

    /* Packing Barang */

    /* Layanan */


    function layanan_get_data($limit, $start, $search) {
        $q = null;
        $q.=" limit " . $start . ", $limit";
        $w = '';

        if (isset($search['nama'])&&($search['nama'] != '')) {
            $w .= " and l.nama like '%" . $search['nama'] . "%'";
        }

        if (isset($search['icd'])&&($search['icd'] != '')) {
            $w .= " and l.kode_icdixcm = '" . $search['icd'] . "'";
        }

        if (isset($search['id_sub_sub'])&&($search['id_sub_sub'] != '')) {
            $w .= " and l.id_sub_sub_jenis_layanan = '" . $search['id_sub_sub'] . "'";
        }


        if (($search != '') & isset($search['id'])) {
            $w .= " and l.id = '" . $search['id'] . "'";
        }


        $sql = "select  l.*, ss.nama as sub_sub_jenis,
            s.nama as sub_jenis, j.nama as jenis
            from layanan l
            left join sub_sub_jenis_layanan ss on (ss.id = l.id_sub_sub_jenis_layanan)
            left join sub_jenis_layanan s on (s.id = ss.id_sub_jenis_layanan)
            left join jenis_layanan j on (j.id = s.id_jenis_layanan)
            where l.id is not null
            $w order by l.nama asc";
           // echo $sql;
        $query = $this->db->query($sql . $q);
        $ret['data'] = $query->result();
        $ret['jumlah'] = $this->db->query($sql)->num_rows();
        return $ret;
    }

    function count_layanan_data() {
        $sql = "select count(id) as jumlah from layanan";
        $query = $this->db->query($sql);
        return $query->row()->jumlah;
    }

    function layanan_add_data($data) {
        $this->db->insert('layanan', $data);
        return $this->db->insert_id();
    }

    function layanan_delete_data($id) {
        $db = "delete from layanan where id = '$id'";
        $this->db->query($db);
    }

    function layanan_edit_data($data) {
        $this->db->where('id', $data['id']);
        $this->db->update('layanan', $data);
    }

    function layanan_cek_data($data) {
        $sql = "select count(*) as jumlah from layanan 
            where nama = '" . $data['layanan'] . "' ";
        $exe = $this->db->query($sql)->row();
        // echo $sql;
        if ($exe->jumlah == 0) {
            $status = true;
        } else {
            $status = false;
        }
        return $status;
    }

    /* Layanan */


    /* Penduduk */

    function penduduk_get_data($limit = null, $start = null, $search = null) {
        $q = null;

        if (isset($search['nama']) && ($search['nama'] != '')) {
            $q.=" and p.nama like ('%" . $search['nama'] . "%')";
        }
        if (isset($search['alamat']) && ($search['alamat'] != '')) {
            $q.=" and d.alamat like ('%" . $search['alamat'] . "%')";
        }
        if (isset($search['telp']) && ($search['telp'] != '')) {
            $q.=" and p.telp like ('%" . $search['telp'] . "%')";
        }
        if (isset($search['kabupaten']) && ($search['kabupaten'] != '')) {
            $q.=" and p.lahir_kabupaten_id = '" . $search['kabupaten'] . "'";
        }
        if (isset($search['gender']) && ($search['gender'] != '')) {
            $q.=" and p.gender = '" . $search['gender'] . "'";
        }
        if (isset($search['gol_darah']) && ($search['gol_darah'] != '')) {
            $q.=" and p.darah_gol = '" . $search['gol_darah'] . "'";
        }
        if (isset($search['tgl_lahir']) && ($search['tgl_lahir'] != '')) {
            $q.=" and p.lahir_tanggal = '" . $search['tgl_lahir'] . "'";
        }

        if (isset($search['id'])) {
            $q.=" and p.id = '" . $search['id'] . "'";
        }

        $limitation = null;
        $limitation.=" limit $start , $limit";

        $sql = "select p.*, d.*, d.id as id_dp, p.id as penduduk_id, kl.nama as kelurahan,kb.nama as kabupaten, pd.nama as pendidikan, pr.nama profesi 
            , dp.no_id from penduduk p
        left join dinamis_penduduk d on (p.id = d.penduduk_id)
        left join kabupaten kb on (p.lahir_kabupaten_id = kb.id)
        left join kelurahan kl on (d.kelurahan_id = kl.id)
        left join pendidikan pd on (d.pendidikan_id = pd.id)
        left join profesi pr on (d.profesi_id = pr.id)
        inner join (
            select identitas_no as no_id,penduduk_id, max(id) as id_max
            from dinamis_penduduk GROUP by penduduk_id
        ) dp on (dp.penduduk_id = d.penduduk_id and dp.id_max = d.id)
        where d.id is not null";
        $order = ' order by p.nama asc';


        $query = $this->db->query($sql . $q . $order . $limitation);
        $data['data'] = $query->result();
        $data['jumlah'] = $this->db->query($sql . $q)->num_rows();
        return $data;
    }

    function count_penduduk_data() {
        $sql = "select count(id) as jumlah from penduduk";
        $query = $this->db->query($sql);
        return $query->row()->jumlah;
    }

    function penduduk_add_data($data) {
        $this->db->insert('penduduk', $data['penduduk']);
        $id = $this->db->insert_id();
        $data['dinamis']['penduduk_id'] = $id;
        $this->db->insert('dinamis_penduduk', $data['dinamis']);
        return $id;
    }

    function penduduk_delete_data($id) {
        $db_pdd = "delete from penduduk where id = '$id'";
        $this->db->query($db_pdd);
        $db_dinamis = "delete from dinamis_penduduk where penduduk_id = '$id'";
        $this->db->query($db_dinamis);
    }

    function penduduk_edit_data($data) {
        $this->db->where('id', $data['penduduk']['id']);
        $this->db->update('penduduk', $data['penduduk']);

        if ($data['dinamis']['alamat'] != $data['dinamis']['alamat_lama']) {
            unset($data['dinamis']['alamat_lama']);
            $this->db->insert('dinamis_penduduk', $data['dinamis']);
        }
    }

    function penduduk_cek_data($data) {
        $sql = "select count(*) as jumlah from penduduk 
            where nama = '" . $data['penduduk'] . "' ";
        $exe = $this->db->query($sql)->row();

        if ($exe->jumlah == 0) {
            $status = true;
        } else {
            $status = false;
        }
        return $status;
    }
    function penduduk_cek_user_data($id){
        $sql = "select count(*) as jumlah from penduduk p
            join users u on(p.id = u.id)
            where p.id = '" . $id . "' ";
        $exe = $this->db->query($sql)->row();

        if ($exe->jumlah == 0) {
            $status = true;
        } else {
            $status = false;
        }
        return $status;
    }

    function dinamis_penduduk_delete_data($id){
        $this->db->where('id', $id)->delete('dinamis_penduduk');
    }

    function profesi_get_data($jenis = null) {
        $q = NULL;
        if ($jenis != NULL) {
            $q = "where jenis = '$jenis'";
        }
        $sql = "select * from profesi $q ORDER BY nama asc";
        $arr = $this->db->query($sql)->result();
        $data[''] = 'Pilih';
        foreach ($arr as $value) {
            $data[$value->id] = $value->nama;
        }
        return $data;
    }

    function posisi_keluarga_get_data() {
        return array(
            '' => 'Pilih',
            'Ayah' => 'Ayah',
            'Ibu' => 'Ibu',
            'Anak' => 'Anak',
        );
    }

    function jabatan_get_data() {
        return array(
            '' => 'Pilih',
            'Direktur' => 'Direktur',
            'Manajer' => 'Manajer',
            'Asisten Manajer' => 'Asisten Manajer',
            'Staf' => 'Staf'
        );
    }

    function penduduk_dinamis_get_data($id = null, $id_dp = null) {
        $q = null;
        $q1 = " and (d.id in (select max(id) from dinamis_penduduk group by penduduk_id) or d.id is null)";
        if ($id != NULL) {
            $q.=" and p.id = '$id'";
        }
        if ($id_dp != NULL) {
            $q.=" and d.id = '$id_dp'";
            $q1 = NULL;
        }
        $sort = ' order by d.tanggal desc , d.id desc';

        $sql = "select p.*, d.*, p.id as penduduk_id,pk.nama as pekerjaan, kl.nama as kelurahan, pd.nama as pendidikan, pr.nama profesi from penduduk p
        left join dinamis_penduduk d on (p.id = d.penduduk_id)
        left join kelurahan kl on (d.kelurahan_id = kl.id)
        left join pendidikan pd on (d.pendidikan_id = pd.id)
        left join profesi pr on (d.profesi_id = pr.id)
        left join pekerjaan pk on (d.pekerjaan_id = pk.id)
        where d.id is not null ";
        //echo $sql.$q;
        $query = $this->db->query($sql . $q . $sort);
        return $query->result();
    }

    function dinamis_penduduk_edit_data() {
        $data = array(
            'penduduk_id' => post_safe('id_pdd'),
            'tanggal' => date('Y-m-d'),
            'identitas_no' => post_safe('noid'),
            'agama' => post_safe('agama'),
            'alamat' => preg_replace('~[\r\n]+~', ' ', post_safe('alamat')),
            'kelurahan_id' => (post_safe('id_kelurahan') == '') ? NULL : post_safe('id_kelurahan'),
            'pernikahan' => post_safe('pernikahan'),
            'kk_no' => post_safe('nokk'),
            'posisi' => post_safe('posisi'),
            'pendidikan_id' => (post_safe('pendidikan') == '') ? NULL : post_safe('pendidikan'),
            'profesi_id' => (post_safe('profesi') == '') ? NULL : post_safe('profesi'),
            'str_no' => post_safe('nostr'),
            'sip_no' => post_safe('nosip'),
            'pekerjaan_id' => (post_safe('pekerjaan') == '') ? NULL : post_safe('pekerjaan'),
            'kerja_izin_surat_no' => post_safe('nosik'),
            'jabatan' => post_safe('jabatan')
        );
        
        $id_din = post_safe('hd_pdd_dinamis');

        if ($id_din != '') {
            // update Dinamis
            
            $this->db->where('id', $id_din)->update('dinamis_penduduk', $data);
            $ret['id_dp'] = $id_din;
        } else {
            // Insert Dinamis
            $this->db->insert('dinamis_penduduk', $data);        
            $ret['id_dp'] = $this->db->insert_id();
        }
        
       
        $ret['id'] = $data['penduduk_id'];
        return $ret;
    }

    function harga_jual_load_data($id_pb = null) {
        $q = null;
        if ($id_pb != null) {
            $q.="and b.nama like ('%$id_pb%')";
        }
                
        $sql = "select date(td.waktu) as tanggal, td.*, bp.id as id_pb, bp.margin, bp.diskon, o.generik, b.nama as barang, st.nama as satuan_terkecil, bp.isi, o.kekuatan, r.nama as pabrik, s.nama as satuan, 
            sd.nama as sediaan, bp.ppn_jual from transaksi_detail td
            join barang_packing bp on (td.barang_packing_id = bp.id)
            join barang b on (bp.barang_id = b.id)
            left join relasi_instansi r on (r.id = b.pabrik_relasi_instansi_id)
            left join obat o on (b.id = o.id)
            left join satuan s on (s.id = o.satuan_id)
            left join satuan st on (st.id = bp.terkecil_satuan_id)
            left join sediaan sd on (sd.id = o.sediaan_id)
            inner join (
                select barang_packing_id, max(id) as id_max from transaksi_detail group by barang_packing_id
            ) tm on (tm.barang_packing_id = td.barang_packing_id and tm.id_max = td.id)
            where td.id is not null $q
              order by b.nama asc";
        //echo "<pre>".$sql."</pre>";
        return $this->db->query($sql);
    }

    function harga_jual_load_data_update($pb) {
        $q = null;
        if ($pb != null) {
            $q.="and td.barang_packing_id in ($pb)";
        }
        $sql = "select date(td.waktu) as tanggal, td.*, bp.id as id_pb, bp.margin, bp.diskon, o.generik, b.nama as barang, st.nama as satuan_terkecil, bp.isi, o.kekuatan, r.nama as pabrik, s.nama as satuan, 
            sd.nama as sediaan from transaksi_detail td
            join barang_packing bp on (td.barang_packing_id = bp.id)
            join barang b on (bp.barang_id = b.id)
            left join relasi_instansi r on (r.id = b.pabrik_relasi_instansi_id)
            left join obat o on (b.id = o.id)
            left join satuan s on (s.id = o.satuan_id)
            left join satuan st on (st.id = bp.terkecil_satuan_id)
            left join sediaan sd on (sd.id = o.sediaan_id)
            inner join (
                select barang_packing_id, max(id) as id_max from transaksi_detail group by barang_packing_id
            ) tm on (tm.barang_packing_id = td.barang_packing_id and tm.id_max = td.id)
            where td.id is not null  and td.unit_id = '".$this->session->userdata('id_unit')."' $q
              order by b.nama asc";
        //echo "<pre>".$sql."</pre>";
        return $this->db->query($sql);
    }

    function harga_jual_update_save() {
        $this->db->trans_begin();
        $id_pb = post_safe('id_pb');
        $margin = post_safe('margin');
        $diskon = post_safe('diskon');
        $ppn_jual = post_safe('ppn_jual');
        if (is_array($id_pb)) {
            foreach ($id_pb as $key => $data) {
                $data_update = array(
                    'margin' => $margin[$key],
                    'diskon' => $diskon[$key],
                    'ppn_jual' => $ppn_jual[$key]
                );
                $this->db->where('id', $data);
                $this->db->update('barang_packing', $data_update);
                
            }
        }
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $status = FALSE;
        } else {
            $this->db->trans_commit();
            $status = TRUE;
        }
        $result['status'] = $status;
        return $result;
    }

    function setting_kas_save() {
        $data_kas = array(
            'waktu' => datetime2mysql(post_safe('tanggal')),
            'penerimaan_pengeluaran_nama' => post_safe('transaksi'),
            'akhir_saldo' => currencyToNumber(post_safe('akhir_saldo'))
        );
        $this->db->insert('kas', $data_kas);
        $id_kas = $this->db->insert_id();
        $result['id_kas'] = $id_kas;
        return $result;
    }

    function layanan_profesi_save() {
        $id_profesi = post_safe('id_profesi');
        $posisi = post_safe('posisi');
        $nominal = post_safe('nominal');
        if (is_array($id_profesi)) {
            foreach ($id_profesi as $key => $data) {
                if ($data != '') {
                    $data_adm_pro = array(
                        'layanan_id' => post_safe('id_layanan'),
                        'profesi_id' => $data,
                        'posisi' => $posisi[$key],
                        'nominal' => currencyToNumber($nominal[$key])
                    );
                    $this->db->insert('tindakan_layanan_profesi_jasa', $data_adm_pro);
                }
            }
        }
        $result['id_layanan'] = post_safe('id_layanan');
        return $result;
    }

    function layanan_profesi_delete($id_tindakan) {
        $this->db->delete('tindakan_layanan_profesi_jasa', array('id' => $id_tindakan));
    }

    /* Tarif Jasa */

    function tarif_get_data($limit, $start, $search, $jenis) {
        $q = null;
        $q.=" limit " . $start . ", $limit";
        $w = " where  ";
        $order = " order by l.nama";

        if($jenis == "tindakan"){
            $w .= "t.id_barang_sewa is null and l.nama != 'Sewa Kamar'";
        }else if($jenis == 'sewa'){
            $w .= "t.id_barang_sewa is not null";
        }else{
            $w .= "t.id_barang_sewa is null and l.nama = 'Sewa Kamar'";
            $order = " order by u.nama, t.kelas";
        }

        if (($search != '') & (isset($search['nama']) && ($search['nama'] != ''))) {
            $w .= " and l.nama like '%" . $search['nama'] . "%' or b.nama like '%" . $search['nama'] . "%'";
        }


        if (($search != '') & isset($search['id'])) {
            $w .= " and t.id = '" . $search['id'] . "'";
        }

        if ((isset($search['profesi'])) && ($search['profesi'] != '')) {
            $w .= " and t.id_profesi = '" . $search['profesi'] . "'";
        }

        if ((isset($search['id_jurusan'])) && ($search['id_jurusan'] != '')) {
            $w .= " and t.id_jurusan_kualifikasi_pendidikan = '" . $search['id_jurusan'] . "'";
        }

        if ((isset($search['jenis_layan'])) && ($search['jenis_layan'] != '')) {
            $w .= " and t.jenis_pelayanan_kunjungan = '" . $search['jenis_layan'] . "'";
        }

       if (($search != '') & isset($search['unit']) && $search['unit'] != '') {
            $w .= " and t.id_unit = '" . $search['unit'] . "'";
        }

        if (($search != '') & isset($search['kelas']) && $search['kelas'] != '') {
            $w .= " and t.kelas = '" . $search['kelas'] . "'";
        }

         if (($search != '') & isset($search['bobot']) && $search['bobot'] != '') {
            $w .= " and t.bobot = '" . $search['bobot'] . "'";
        }

        $sql = "select t.*, l.nama as layanan, l.id as id_layanan,
        jk.nama as jurusan, jk.id as id_jurusan, p.nama as profesi, 
        p.id as id_profesi,
        u.nama as unit, u.id as id_unit,
        b.nama as barang, k.id as id_barang
        from tarif t
        join layanan l on (t.id_layanan = l.id)
        left join jurusan_kualifikasi_pendidikan jk on(jk.id = t.id_jurusan_kualifikasi_pendidikan)
        left join profesi p on(p.id = t.id_profesi)
        left join unit u on(u.id = t.id_unit)
        left join bhp_tarif bhp on (bhp.id_tarif = t.id)
        left join kemasan k on (bhp.id_kemasan_barang = k.id)
        left join barang b on(b.id = k.id_barang)
        $w $order";
        //echo "<pre>".$sql."</pre>";
        $query = $this->db->query($sql . $q);
        $ret['data'] = $query->result();
        $ret['jumlah'] = $this->db->query($sql)->num_rows();
        return $ret;
    }

    function tindakan_add_data() {
        $this->db->trans_begin();

        $data = array(
            'id_layanan' => (post_safe('id_layanan') == '') ? NULL : post_safe('id_layanan'),
            'id_profesi' => (post_safe('profesi') == '') ? NULL : post_safe('profesi'),
            'id_jurusan_kualifikasi_pendidikan' => (post_safe('id_jurusan') == '') ? NULL : post_safe('id_jurusan'),
            'jenis_pelayanan_kunjungan' => (post_safe('jenis_layan') == '') ? NULL : post_safe('jenis_layan'),
            'id_unit' => (post_safe('unit')=='')?NULL:post_safe('unit'),
            'bobot' => (post_safe('bobot')=='')?NULL:post_safe('bobot'),
            'kelas' => (post_safe('kelas')=='')?NULL:post_safe('kelas'),
            'jasa_sarana' => currencyToNumber(post_safe('js')),
            'jasa_nakes' => currencyToNumber(post_safe('js_nakes')),
            'jasa_tindakan_rs' => currencyToNumber(post_safe('js_rs')),
            'bhp' => currencyToNumber(post_safe('bhp')),
            'biaya_administrasi' => currencyToNumber(post_safe('bia_adm')),
            'total' => post_safe('total'),
            'persentase_profit' => post_safe('margin'),
            'nominal' => currencyToNumber(post_safe('nominal_akhir'))
        );
        $this->db->insert('tarif', $data);
        $id_tarif =  $this->db->insert_id();

        // insert bhp

        $id_barang = post_safe('id_barang'); //array
        $qty = post_safe('qty'); // array

        if (is_array($id_barang)) {
            foreach ($id_barang as $key => $value) {
                if ($value != '') {
                    $bhp = array(
                        'id_tarif' => $id_tarif,
                        'id_packing_barang' => $value,
                        'jumlah' => $qty[$key]
                    );
                    $this->db->insert('bhp_tarif', $bhp);
                }
                
                
            }
        }
        

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return null;
        } else {
            $this->db->trans_commit();
            return $id_tarif;
        }        

        
    }



    function kamar_add_data() {
        $id_sewa = $this->db->select('id')->where('nama','Sewa Kamar')->get('layanan')->row()->id;
        $data = array(
            'id' => post_safe('id'),
            'id_layanan' => $id_sewa,
            'id_profesi' => NULL,
            'id_jurusan_kualifikasi_pendidikan' => NULL,
            'jenis_pelayanan_kunjungan' => 'Rawat Inap',
            'id_unit' => (post_safe('unit')=='')?NULL:post_safe('unit'),
            'bobot' => NULL,
            'kelas' => (post_safe('kelas')=='')?NULL:post_safe('kelas'),
            'jasa_sarana' => currencyToNumber(post_safe('js')),
            'bhp' => currencyToNumber(post_safe('bhp')),
            'biaya_administrasi' => currencyToNumber(post_safe('bia_adm')),
            'total' => post_safe('total_kamar'),
            'persentase_profit' => post_safe('margin'),
            'nominal' => currencyToNumber(post_safe('nominal_akhir'))
        );
        $this->db->insert('tarif', $data);
        return $this->db->insert_id();
    }

    function tarif_delete_data($id) {
        $db = "delete from tarif where id = '$id'";
        $this->db->query($db);
    }

    function tindakan_edit_data() {
        $this->db->trans_begin();
        $data = array(
            'id' => post_safe('id'),
            'id_layanan' => (post_safe('id_layanan') == '') ? NULL : post_safe('id_layanan'),
            'id_profesi' => (post_safe('profesi') == '') ? NULL : post_safe('profesi'),
            'id_jurusan_kualifikasi_pendidikan' => (post_safe('id_jurusan') == '') ? NULL : post_safe('id_jurusan'),
            'jenis_pelayanan_kunjungan' => (post_safe('jenis_layan') == '') ? NULL : post_safe('jenis_layan'),
            'id_unit' => (post_safe('unit')=='')?NULL:post_safe('unit'),
            'bobot' => (post_safe('bobot')=='')?NULL:post_safe('bobot'),
            'kelas' => (post_safe('kelas')=='')?NULL:post_safe('kelas'),
            'jasa_sarana' => currencyToNumber(post_safe('js')),
            'jasa_nakes' => currencyToNumber(post_safe('js_nakes')),
            'jasa_tindakan_rs' => currencyToNumber(post_safe('js_rs')),
            'bhp' => currencyToNumber(post_safe('bhp')),
            'biaya_administrasi' => currencyToNumber(post_safe('bia_adm')),
            'total' => post_safe('total'),
            'persentase_profit' => post_safe('margin'),
            'nominal' => currencyToNumber(post_safe('nominal_akhir'))
        );
        $cek = $this->db->query("select count(*) as jumlah from tarif where id = '".post_safe('id')."'")->row();
        if ($cek->jumlah > 0) {
            $this->db->where('id', $data['id']);
            $this->db->update('tarif', $data);
            $id_tarif =  $data['id'];
        } else {
            $this->db->insert('tarif', $data);
            $id_tarif = $this->db->insert_id();
        }

         // insert bhp
        $id_bhp = post_safe('id_bhp'); //array
        $id_barang = post_safe('id_barang'); //array
        $qty = post_safe('qty'); // array

        if (is_array($id_barang)) {
            foreach ($id_barang as $key => $value) {
                if ($value != '') {
                    $bhp = array(
                        'id_tarif' => $id_tarif,
                        'id_packing_barang' => $value,
                        'jumlah' => $qty[$key]
                    );
                    if($id_bhp[$key] !== ''){
                        // Update
                        $this->db->where('id', $id_bhp[$key])->update('bhp_tarif', $bhp);
                    }else{
                        // Insert
                        $this->db->insert('bhp_tarif', $bhp);
                    }
                    
                    
                }
                
                
            }
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return null;
        } else {
            $this->db->trans_commit();
            return $id_tarif;
        }  
    }

    function kamar_edit_data() {
        $data = array(
            'id' => post_safe('id'),
            'id_profesi' => NULL,
            'id_jurusan_kualifikasi_pendidikan' => NULL,
            'jenis_pelayanan_kunjungan' => NULL,
            'id_unit' => (post_safe('unit')=='')?NULL:post_safe('unit'),
            'bobot' => NULL,
            'kelas' => (post_safe('kelas')=='')?NULL:post_safe('kelas'),
            'jasa_sarana' => currencyToNumber(post_safe('js')),
            'bhp' => currencyToNumber(post_safe('bhp')),
            'biaya_administrasi' => currencyToNumber(post_safe('bia_adm')),
            'total' => post_safe('total_kamar'),
            'persentase_profit' => post_safe('margin'),
            'nominal' => currencyToNumber(post_safe('nominal_akhir'))
        );
        $cek = $this->db->query("select count(*) as jumlah from tarif where id = '".post_safe('id')."'")->row();
        if ($cek->jumlah > 0) {
            $this->db->where('id', $data['id']);
            $this->db->update('tarif', $data);
            return $data['id'];
        } else {
            $this->db->insert('tarif', $data);
            return $this->db->insert_id();
        }
    }

    function tindakan_cek_data() {
        $exe = $this->db->query("select count(*) as jumlah from tarif 
            where id_layanan = '" . get_safe('id_layanan'). "' and 
            id_profesi = '" . get_safe('profesi') . "' and 
            id_jurusan_kualifikasi_pendidikan = '" . get_safe('id_jurusan') . "' and 
            jenis_pelayanan_kunjungan = '" . get_safe('jenis_layan') . "' and 
            id_unit = '" . get_safe('unit') . "' and 
            bobot = '" . get_safe('bobot') . "' and
            kelas = '" . get_safe('kelas') . "' ")->row();

        if ($exe->jumlah == 0) {
            $status = true;
        } else {
            $status = false;
        }
        return $status;
    }

    function kamar_cek_data() {
        $exe = $this->db->query("select count(*) as jumlah from tarif 
            where id_unit = '" . get_safe('unit'). "' and 
            kelas = '" . get_safe('kelas') . "' ")->row();

        if ($exe->jumlah == 0) {
            $status = true;
        } else {
            $status = false;
        }
        return $status;
    }

    function sewa_add_data() {
        $id_layanan = $this->db->query("select id from layanan where nama = 'Sewa Barang' ")->row()->id;
        $data = array(
            'id' => post_safe('id_sewa'),
            'id_layanan' => ($id_layanan == '') ? NULL : $id_layanan,
            'id_barang_sewa' => (post_safe('id_barang_sewa') == '') ? NULL : post_safe('id_barang_sewa'),
            'jenis_pelayanan_kunjungan' => (post_safe('jenis_layan') == '') ? NULL : post_safe('jenis_layan'),
            'id_unit' => (post_safe('unit')=='')?NULL:post_safe('unit'),
            'kelas' => (post_safe('kelas')=='')?NULL:post_safe('kelas'),
            'nominal' => currencyToNumber(post_safe('nominal'))
        );
        $this->db->insert('tarif', $data);
        return $this->db->insert_id();
    }

    function sewa_edit_data() {
        $id_layanan = $this->db->query("select id from layanan where nama = 'Sewa Barang' ")->row()->id;

        $data = array(
            'id' => post_safe('id_sewa'),
            'id_layanan' => ($id_layanan == '') ? NULL : $id_layanan,
            'id_barang_sewa' => (post_safe('id_barang_sewa') == '') ? NULL : post_safe('id_barang_sewa'),
            'jenis_pelayanan_kunjungan' => (post_safe('jenis_layan') == '') ? NULL : post_safe('jenis_layan'),
            'id_unit' => (post_safe('unit')=='')?NULL:post_safe('unit'),
            'kelas' => (post_safe('kelas')=='')?NULL:post_safe('kelas'),
            'nominal' => currencyToNumber(post_safe('nominal'))
        );
        $cek = $this->db->query("select count(*) as jumlah from tarif where id = '".post_safe('id_sewa')."'")->row();
        if ($cek->jumlah > 0) {
            $this->db->where('id', $data['id']);
            $this->db->update('tarif', $data);
            return $data['id'];
        } else {
            $this->db->insert('tarif', $data);
            return $this->db->insert_id();
        }
    }

    function sewa_cek_data() {
        $sql = "select count(*) as jumlah from tarif 
            where id_layanan = '" . get_safe('id_layanan_sewa'). "' and 
            id_barang_sewa = '" . get_safe('id_barang_sewa') . "' and 
            jenis_pelayanan_kunjungan = '" . get_safe('jenis_layan') . "' and 
            id_unit = '" . get_safe('unit') . "' and 
            kelas = '" . get_safe('kelas') . "' ";
        //echo $sql;
        $exe = $this->db->query($sql)->row();

        if ($exe->jumlah == 0) {
            $status = true;
        } else {
            $status = false;
        }
        return $status;
    }

    function get_jasa_profesi($id_layanan) {
        $sql = "select sum(nominal) as total from tindakan_layanan_profesi_jasa where layanan_id = '$id_layanan'";
        $query = $this->db->query($sql)->row();

        return $query;
    }


    function kelas_tarif_get_data(){
         return array(
            '' => 'Pilih kelas','VVIP'=>'VVIP', 'VIP'=>'VIP', 'I'=>'I', 'II'=>'II', 'III'=>'III'
        );
    }

    function bobot_tarif_get_data() {
        return array(
            '' => 'Tanpa Bobot',
            'Khusus'=>'Khusus', 
            'Besar'=>'Besar', 
            'Sedang'=>'Sedang', 
            'Kecil'=>'Kecil'
        );
    }

    function jenis_pelayanan_get_data(){
        return array(''=>'Pilih','Poliklinik'=>'Poliklinik', 'Darurat'=>'Darurat', 'Rawat Inap'=>'Rawat Inap');
    }
    /* Tarif Jasa */
    
    function gol_sebab_sakit_get_data($limit = null, $start = null, $search = null) {
        $q = null;
        //if ($search)
        $limitation = null;
        $limitation.=" limit $start , $limit";
        if ($search['id'] != null) {
            $q.=" and id = '$search[id]'";
        }
        if ($search['no_dtd'] != null) {
            $q.=" and no_dtd = '$search[no_dtd]'";
        }
        if ($search['no_daftar_terperinci'] != null) {
            $q.=" and no_daftar_terperinci = '$search[no_daftar_terperinci]'";
        }
        if ($search['nama'] != null) {
            $q.=" and nama like '%$search[nama]%'";
        }
        $sql = "select * from golongan_sebab_sakit where id is not NULL";
        $order = ' order by nama asc';
        
        $query = $this->db->query($sql . $q . $order . $limitation);
        $data['data'] = $query->result();
        $data['jumlah'] = $this->db->query($sql . $q)->num_rows();
        return $data;
    }
    
    function gol_sebab_sakit_add_data($data) {
        $this->db->insert('golongan_sebab_sakit', $data);
        return $this->db->insert_id();
    }
    
    function gol_sebab_sakit_edit_data($data) {
        $this->db->where('id', $data['id']);
        $this->db->update('golongan_sebab_sakit', $data);
    }
    
    function gol_sebab_sakit_delete_data($id) {
        $this->db->delete('golongan_sebab_sakit', array('id' => $id));
    }

    function kategori_barang_load_data($limit, $start, $param){
        $q = null;
        //if ($search)
        $limitation = null;
        $order = " order by nama";
        $limitation.=" limit $start , $limit";

        if (isset($param['id']) && ($param['id'] != '')) {
            $q.=" and id = '".$param['id']."'";
        }

        if (isset($param['nama']) && ($param['nama'] != '')) {
            $q.=" and nama like '%".$param['nama']."%' ";
        }

        if (isset($param['jenis']) && ($param['jenis'] != '')) {
            $q.=" and jenis = '".$param['jenis']."' ";
        }

        $sql = "select * from barang_kategori where id is not null $q $order";
       // echo $sql;
        $query = $this->db->query($sql.$limitation);
        $data['data'] = $query->result();
        $data['jumlah'] = $this->db->query($sql)->num_rows();
        return $data;
    }

    function kategori_add_data(){
        $data = array(
            'nama' => post_safe('nama'), 
            'jenis' => post_safe('jenis')
        );

        $this->db->insert('barang_kategori', $data);
        return $this->db->insert_id();
    }

    function kategori_edit_data(){
        $data = array(
            'id' => post_safe('id_kategori'),
            'nama' => post_safe('nama'), 
            'jenis' => post_safe('jenis')
        );
        $this->db->where('id', $data['id']);
        $this->db->update('barang_kategori', $data);
        return $data['id'];
    }

    function kategori_delete_data($id){
        $this->db->where('id', $id)->delete('barang_kategori');
    }
    
    function sediaan_load_data($limit, $start, $param){
        $q = null;
        //if ($search)
        $limitation = null;
        $order = " order by nama";
        $limitation.=" limit $start , $limit";

        if (isset($param['id']) && ($param['id'] != '')) {
            $q.=" and id = '".$param['id']."'";
        }

        if (isset($param['nama']) && ($param['nama'] != '')) {
            $q.=" and nama like '%".$param['nama']."%' ";
        }

        $sql = "select * from sediaan where id is not null $q $order";
       // echo $sql;
        $query = $this->db->query($sql.$limitation);
        $data['data'] = $query->result();
        $data['jumlah'] = $this->db->query($sql)->num_rows();
        return $data;
    }

    function sediaan_add_data(){
        $data = array(
            'nama' => post_safe('nama')
        );

        $this->db->insert('sediaan', $data);
        return $this->db->insert_id();
    }

    function sediaan_edit_data(){
        $data = array(
            'id' => post_safe('id_sediaan'),
            'nama' => post_safe('nama')
        );
        $this->db->where('id', $data['id']);
        $this->db->update('sediaan', $data);
        return $data['id'];
    }

    function sediaan_delete_data($id){
        $this->db->where('id', $id)->delete('sediaan');
    }

    /* Satuan */
    /*function satuan_load_data($limit, $start, $param){
        $q = null;
        //if ($search)
        $limitation = null;
        $order = " order by nama";
        $limitation.=" limit $start , $limit";

        if (isset($param['id']) && ($param['id'] != '')) {
            $q.=" and id = '".$param['id']."'";
        }

        if (isset($param['nama']) && ($param['nama'] != '')) {
            $q.=" and nama like '%".$param['nama']."%' ";
        }

        $sql = "select * from satuan where id is not null $q $order";
       // echo $sql;
        $query = $this->db->query($sql.$limitation);
        $data['data'] = $query->result();
        $data['jumlah'] = $this->db->query($sql)->num_rows();
        return $data;
    }*/

    function satuan_add_data(){
        $data = array(
            'nama' => post_safe('nama')
        );

        $this->db->insert('satuan', $data);
        return $this->db->insert_id();
    }

    function satuan_edit_data(){
        $data = array(
            'id' => post_safe('id_satuan'),
            'nama' => post_safe('nama')
        );
        $this->db->where('id', $data['id']);
        $this->db->update('satuan', $data);
        return $data['id'];
    }

    function satuan_delete_data($id){
        $this->db->where('id', $id)->delete('satuan');
    }

    /* Satuan */

    function get_rl1_3_data(){
        $sql = "select t.id,  u.nama,
            (select count(*) from tt where unit_id = u.id) as jumlah_tt,
            (select count(*) from tt where unit_id = u.id and tt.kelas ='VVIP') as tt_vvip,
            (select count(*) from tt where unit_id = u.id and tt.kelas ='VIP') as tt_vip,
            (select count(*) from tt where unit_id = u.id and tt.kelas ='I') as tt_i,
            (select count(*) from tt where unit_id = u.id and tt.kelas ='II') as tt_ii,
            (select count(*) from tt where unit_id = u.id and tt.kelas ='III') as tt_iii
             from 
            tt t
            join unit u on(t.unit_id = u.id)
            group by u.id
            order by u.nama";
        return $this->db->query($sql)->result();
    }

    function get_bangsal_irna(){
        $sql = "select u.id, u.nama from unit u 
                join tt on (tt.unit_id = u.id)
                group by u.id";

        $query = $this->db->query($sql)->result();  
        $data[''] = 'Pilih bangsal...';
        foreach ($query as $key => $value) {
            $data[$value->id] = $value->nama;
        }

        return $data;
    }

    function get_data_bhp_tarif($id_tarif){
        $sql = "select bh.*, b.nama as barang,
                bp.margin, bp.diskon, bp.ppn_jual, bp.hpp, bp.hna,
                null as harga_jual
                from bhp_tarif bh
                join kemasan bp on (bh.id_kemasan_barang = bp.id)
                join barang b on (b.id = bp.id_barang)
                where bh.id_tarif = '".$id_tarif."' ";
        $query = $this->db->query($sql)->result();

        foreach ($query as $key => $v) {
            $harga = $v->hna + ($v->hna*($v->margin/100));
            $diskon = $harga*($v->diskon/100);
            $terdiskon = ($harga-$diskon);
            $harga_jual= $terdiskon+($terdiskon*($v->ppn_jual/100));
            $query[$key]->harga_jual = $harga_jual;
        }

        return $query;
    }
    
    function golongan_load_data() {
        $sql = "select * from golongan order by nama asc";
        return $this->db->query($sql);
    }
    
    function satuans_load_data($status = null) {
        $q = NULL;
        if ($status != NULL) {
            $q = "where is_satuan_kemasan = '$status'";
        }
        $sql = "select * from satuan $q order by nama asc";
        return $this->db->query($sql);
    }
    
    function satuan_load_data($limit = NULL, $start = NULL, $param = NULL){
        $q = null; $order = NULL; $limitation = NULL;
        if ($limit !== NULL) {
        $limitation = null;
            $order = " order by nama";
            $limitation.=" limit $start , $limit";
        }
        if (isset($param['id']) && ($param['id'] != '')) {
            $q.=" and id = '".$param['id']."'";
        }

        if (isset($param['nama']) && ($param['nama'] != '')) {
            $q.=" and nama like '%".$param['nama']."%' ";
        }

        $sql = "select * from satuan where id is not null $q $order";
       // echo $sql;
        $query = $this->db->query($sql.$limitation);
        $data['data'] = $query->result();
        $data['jumlah'] = $this->db->query($sql)->num_rows();
        return $data;
    }
    
    function admr_load_data() {
        return array('Oral','Rektal','Infus','Topikal','Sublingual','Transdermal','Intrakutan','Subkutan','Intravena','Intramuskuler','Vagina','Injeksi','Intranasal','Intraokuler','Intraaurikuler','Intrapulmonal','Implantasi','Subkutan','Intralumbal','Intrarteri');
    }
    
    function kelas_load_data() {
        return array('VVIP','VIP','I','II','III');
    }
    
    function bobot_load_data() {
        return array('Khusus','Besar','Sedang','Kecil');
    }
    
    function farmakoterapi_load_data() {
        $sql = "select * from farmako_terapi ORDER by nama";
        return $this->db->query($sql);
    }
    
    function fda_load_data() {
        return array('A','B','C','D','X');
    }
    
    function load_data_asuransi() {
        $sql = "select * from asuransi_produk order by nama";
        return $this->db->query($sql);
    }
    
    function load_data_tarif() {
        $sql = "select * from jasa_apoteker order by nama";
        return $this->db->query($sql);
    }
    
    function save_supplier() {
        $nama       = $_POST['nama'];
        $alamat     = $_POST['alamat'];
        $email      = $_POST['email'];
        $telp       = $_POST['telp'];
        $id_supplier= $_POST['id_supplier'];

        if ($id_supplier === '') {
            $sql = "
            insert into supplier set
                nama = '$nama',
                alamat = '$alamat',
                email = '$email',
                telp = '$telp'
            ";
            $this->db->query($sql);
            $id_sup = $this->db->insert_id();
        } else {
            $sql = "
            update supplier set
                nama = '$nama',
                alamat = '$alamat',
                email = '$email',
                telp = '$telp'
            where id = '$id_supplier'";
            $this->db->query($sql);
            $id_sup = $id_supplier;
        }
        die(json_encode(array('status' => TRUE, 'id_supplier' => $id_sup, 'nama' => $nama)));
    }
    
    function delete_supplier($id) {
        $this->db->delete('supplier', array('id' => $id));
    }
}
?>