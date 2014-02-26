<?php

class m_kepegawaian extends CI_Model {
    /* Jenis Jurusan Kualifikasi Pendidikan */

    function jenis_get_data($limit = null, $start = null, $search = null) {
        $q = null;
        if ($limit != null) {
            $q.=" limit " . $start . ", $limit";
        }
        $w = '';
        if (isset($search['id'])) {
            $w = " and id = '" . $search['id'] . "'";
        }

        if (isset($search['nama']) && ($search['nama'] != '')) {
            $w = " and nama like '%" . $search['nama'] . "%' ";
        }

        if (isset($search['nakes']) && ($search['nakes'] != '')) {
            $w = " and  nakes = '" . $search['nakes'] . "'";
        }

        $sql = "select * from jenis_jurusan_kualifikasi_pendidikan 
                where id is not null
                $w order by nama asc";
        // echo $sql;
        $query = $this->db->query($sql . $q);
        $ret['data'] = $query->result();
        $ret['jumlah'] = $this->db->query($sql)->num_rows();
        return $ret;
    }

    function jenis_add_data($data) {
        $this->db->insert('jenis_jurusan_kualifikasi_pendidikan', $data);
        return $this->db->insert_id();
    }

    function jenis_edit_data($data) {
        $this->db->where('id', $data['id']);
        $this->db->update('jenis_jurusan_kualifikasi_pendidikan', $data);
    }

    function jenis_delete_data($id) {
        $this->db->delete('jenis_jurusan_kualifikasi_pendidikan', array('id' => $id));
    }

    function jenis_cek_data($data) {
        $q = '';

        $q = "and nakes = '" . $data['nakes'] . "'";

        $sql = "select count(*) as jumlah from jenis_jurusan_kualifikasi_pendidikan
            where nama = '" . $data['nama'] . "' $q";
        $query = $this->db->query($sql);
        $jml = $query->row()->jumlah;
        if ($jml == 0) {
            return true;
        } else {
            return false;
        }
    }

    function load_data_jenis($q) {
        $sql = "select * from jenis_jurusan_kualifikasi_pendidikan
            where nama like ('%$q%')  order by locate('$q', nama)";
        return $this->db->query($sql);
    }

   

    /* Jenis Jurusan Kualifikasi Pendidikan */


    /* Kualifikasi Pendidikan */

    function pendidikan_get_data($limit, $start, $search) {
        $q = null;
        $q.=" limit " . $start . ", $limit";
        $w = '';
        if (($search != 'null') & isset($search['id'])) {
            $w = " where id = '" . $search['id'] . "'";
        }

        if (($search != 'null') & isset($search['nama'])) {
            $w = " where nama like '%" . $search['nama'] . "%' ";
        }

        $sql = "select * from kualifikasi_pendidikan $w order by nama asc";
        $query = $this->db->query($sql . $q);
        $ret['data'] = $query->result();
        $ret['jumlah'] = $this->db->query($sql)->num_rows();
        return $ret;
    }

    function pendidikan_add_data($data) {
        $this->db->insert('kualifikasi_pendidikan', $data);
        return $this->db->insert_id();
    }

    function pendidikan_edit_data($data) {
        $this->db->where('id', $data['id']);
        $this->db->update('kualifikasi_pendidikan', $data);
    }

    function pendidikan_delete_data($id) {
        $this->db->delete('kualifikasi_pendidikan', array('id' => $id));
    }

    function pendidikan_cek_data($data) {
        $sql = "select count(*) as jumlah from kualifikasi_pendidikan
            where nama = '" . $data['nama'] . "'";
        $query = $this->db->query($sql);
        $jml = $query->row()->jumlah;
        if ($jml == 0) {
            return true;
        } else {
            return false;
        }
    }

    /* Kualifikasi Pendidikan */

    /* Jurusan Kualifikasi Pendidikan */

    function jurusan_get_data($limit, $start, $search) {
        $q = null;
        $q.=" limit " . $start . ", $limit";
        $w = '';
        if (($search != 'null') & isset($search['id'])) {
            $w = " and kp.id = '" . $search['id'] . "'";
        }

        if (($search != 'null') & isset($search['nama']) && ($search['nama'] != '')) {
            $w .= " and kp.nama like '%" . $search['nama'] . "%' ";
        }
        if (($search != 'null') & isset($search['jenis']) && ($search['jenis'] != '')) {
            $w .= " and kp.id_jenis_jurusan_kualifikasi_pendidikan =  '" . $search['jenis'] . "' ";
        }


        $sql = "select kp.* , jj.nama as jenis, jj.id as id_jenis from jurusan_kualifikasi_pendidikan kp
            left join jenis_jurusan_kualifikasi_pendidikan jj on(kp.id_jenis_jurusan_kualifikasi_pendidikan = jj.id)            
        where kp.id is not null  $w order by kp.nama asc ";
        $query = $this->db->query($sql . $q);

        $ret['data'] = $query->result();
        $ret['jumlah'] = $this->db->query($sql)->num_rows();
        return $ret;
    }

    function jurusan_add_data($data) {
        $this->db->insert('jurusan_kualifikasi_pendidikan', $data);
        return $this->db->insert_id();
    }

    function jurusan_edit_data($data) {
        $this->db->where('id', $data['id']);
        $this->db->update('jurusan_kualifikasi_pendidikan', $data);
    }

    function jurusan_delete_data($id) {
        $this->db->delete('jurusan_kualifikasi_pendidikan', array('id' => $id));
    }

    function jurusan_cek_data($data) {
        $q = '';

        $q = " and id_jenis_jurusan_kualifikasi_pendidikan = '" . $data['jenis'] . "'";

        $sql = "select count(*) as jumlah from jurusan_kualifikasi_pendidikan
            where nama = '" . $data['nama'] . "' $q";
        $query = $this->db->query($sql);
        $jml = $query->row()->jumlah;
        if ($jml == 0) {
            return true;
        } else {
            return false;
        }
    }

    /* Jurusan Kualifikasi Pendidikan */

    /* Kepegawaian */

    function get_jabatan() {
        return array('Direktur' => 'Direktur', 'Manajer' => 'Manajer', 'Asisten Manajer' => 'Asisten Manajer', 'Staf' => 'Staf',);
    }

    function get_jenjang_pendidikan() {
        $query = $this->db->query("select * from kualifikasi_pendidikan order by nama asc");
        $data[''] = 'Pilih ...';
        foreach ($query->result() as $key => $val) {
            $data[$val->id] = $val->nama;
        }
        return $data;
    }

    function load_jurusan($q) {
        $sql = "select kp.* , jj.nama as jenis, jj.id as id_jenis from jurusan_kualifikasi_pendidikan kp
            left join jenis_jurusan_kualifikasi_pendidikan jj on(kp.id_jenis_jurusan_kualifikasi_pendidikan = jj.id)            
            where  kp.nama like ('%$q%') order by locate('$q', kp.nama)";
        
        return $this->db->query($sql);
    }

    function pegawai_get_data($limit = null, $start = null, $search = null,$group=null) {
        $q = null;

        if ($limit != null) {
            $q.=" limit " . $start . ", $limit";
        }
        $w = '';
        if (($search != 'null') & isset($search['id'])) {
            $w = " and kp.id = '" . $search['id'] . "'";
        }

        if (($search != 'null') & isset($search['gender']) && $search['gender'] != '') {
            $w = " and p.gender = '" . $search['gender'] . "' ";
        }

        if (($search != 'null') & (isset($search['fromdate']) && $search['fromdate'] != '' ) & (isset($search['todate']) && $search['todate'] != '')) {
            $w .= " and kp.waktu between '" . $search['fromdate'] . " 00:00:00' and '" . $search['todate'] . " 23:59:59' ";
        }

        if (($search != 'null') & isset($search['jenjang']) && $search['jenjang'] != '') {
            $w .= " and ku.id = '" . $search['jenjang'] . "' ";
        }

        if (($search != 'null') & isset($search['jurusan']) && $search['jurusan'] != '') {
            $w .= " and jk.id = '" . $search['jurusan'] . "' ";
        }

       

        $sql = "select p.id as id_penduduk, p.nama, p.gender, kp.*, ku.id as id_kualifikasi, ku.nama as kualifikasi, 
            jk.id as id_jurusan, jk.nama as jurusan, js.id as id_jenis, 
            js.nama as jenis
            FROM kepegawaian kp 
            join penduduk p on (kp.penduduk_id = p.id)
            left join kualifikasi_pendidikan ku on (kp.id_kualifikasi_pendidikan = ku.id)
            left join jurusan_kualifikasi_pendidikan jk on (kp.id_jurusan_kualifikasi_pendidikan = jk.id)
            left join jenis_jurusan_kualifikasi_pendidikan js on (jk.id_jenis_jurusan_kualifikasi_pendidikan = js.id)
            where kp.id is not null $w ";
        
        if ($group) {
            $sql .= " group by ku.id";
        }

        $sql.= " order by kp.waktu, p.nama, ku.nama asc";
       // echo $sql;
        $query = $this->db->query($sql . $q);
        $ret['data'] = $query->result();
        $ret['jumlah'] = $this->db->query($sql)->num_rows();
        return $ret;
    }

    function pegawai_cek_data($data) {
        $sql = "select count(*) as jumlah from kepegawaian
            where nama = '" . $data['nama'] . "'";
        $query = $this->db->query($sql);
        $jml = $query->row()->jumlah;
        if ($jml == 0) {
            return true;
        } else {
            return false;
        }
    }

    function pegawai_add_data($data) {
        $this->db->insert('kepegawaian', $data);
        return $this->db->insert_id();
    }

    function pegawai_delete_data($id) {
        $this->db->delete('kepegawaian', array('id' => $id));
    }

    function pegawai_edit_data($data) {
        $this->db->where('id', $data['id']);
        $this->db->update('kepegawaian', $data);
    }

    /* Kepegawaian */
}

?>