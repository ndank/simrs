<?php

class M_registrasi_rs extends CI_Model {
    /* Jenis RS */

    function jenis_get_data($limit = null, $start = null, $search = null) {
        $q = null;
        if ($limit != null) {
            $q.=" limit " . $start . ", $limit";
        }
        $w = '';
        if (($search != 'null') & isset($search['id'])) {
            $w = " where id = '" . $search['id'] . "'";
        }
        $sql = "select * from jenis_rs $w order by nama asc";

        $query = $this->db->query($sql . $q);
        $ret['data'] = $query->result();
        $ret['jumlah'] = $this->db->query($sql)->num_rows();
        return $ret;
    }

    function jenis_add_data($data) {
        $this->db->insert('jenis_rs', $data);
        return $this->db->insert_id();
    }

    function jenis_edit_data($data) {
        $this->db->where('id', $data['id']);
        $this->db->update('jenis_rs', $data);
    }

    function jenis_delete_data($id) {
        $this->db->delete('jenis_rs', array('id' => $id));
    }

    function jenis_cek_data($data) {
        $q = '';

        $sql = "select count(*) as jumlah from jenis_rs
            where nama = '" . $data['nama'] . "' $q";
        $query = $this->db->query($sql);
        $jml = $query->row()->jumlah;
        if ($jml == 0) {
            return true;
        } else {
            return false;
        }
    }

    /* Jenis RS */

    /* Registrasi RS */

    function register_get_data($limit = null, $start = null, $search) {
        $q = null;

        if (($limit !== null) & ($start !== null)) {
            $q.=" limit " . $start . ", $limit";
        }
        
        $w = '';
        if (($search != 'null') & isset($search['id'])) {
            $w = " where rg.id = '" . $search['id'] . "'";
        }
        $sql = "select rg.* , pp.nama as direktur, ins.nama as penyelenggara, ir.nama as penetap,
                kl.nama as kelurahan from reg_rs rg
                left join kepegawaian kp on (rg.id_kepegawaian_direktur = kp.id)
                left join penduduk pp on (pp.id = kp.penduduk_id)
                left join relasi_instansi ins on (rg.id_instansi_relasi_penyelenggara = ins.id)
                left join relasi_instansi ir on (rg.id_instansi_relasi_penetap = ir.id)
                left join kelurahan kl on (rg.id_kelurahan = kl.id)
                $w order by rg.nama asc , rg.waktu desc";
        //echo $sql;
        $query = $this->db->query($sql . $q);
        $ret['data'] = $query->result();
        $ret['jumlah'] = $this->db->query($sql)->num_rows();
        return $ret;
    }

    function register_add_data($data) {
        // ambil data tempat tidur 
        $tt = "select count(id) as jumlah from tt";
        $vvip = " where kelas = 'VVIP'";
        $vip = " where kelas = 'VIP'";
        $satu = " where kelas = 'I'";
        $dua = " where kelas = 'II'";
        $tiga = " where kelas = 'III'";

        $data['jumlah_tt_VVIP'] = $this->db->query($tt . $vip)->row()->jumlah;
        $data['jumlah_tt_VIP'] = $this->db->query($tt . $vip)->row()->jumlah;
        $data['jumlah_tt_I'] = $this->db->query($tt . $satu)->row()->jumlah;
        $data['jumlah_tt_II'] = $this->db->query($tt . $dua)->row()->jumlah;
        $data['jumlah_tt_III'] = $this->db->query($tt . $tiga)->row()->jumlah;

        // ambila data nakes
        $nakes = "select count(kp.id) as jumlah from kepegawaian kp
                  left join jurusan_kualifikasi_pendidikan jk on (kp.id_jurusan_kualifikasi_pendidikan = jk.id)
                  left join jenis_jurusan_kualifikasi_pendidikan jj on (jk.id_jenis_jurusan_kualifikasi_pendidikan = jj.id)
                  where jj.nama = 'Medis' and jj.nakes = 'Ya'";
        $data['jumlah_nakes_medis'] = $this->db->query($nakes)->row()->jumlah;

        $this->db->insert('reg_rs', $data);
        return $this->db->insert_id();
    }

    function get_last_register_data($tahun) {
        $sql = "select max(rg.id) as id_max,jrs.nama as jenis_rs ,rg.* , 
                pp.nama as direktur,
                ins.nama as penyelenggara, ir.nama as penetap,
                kl.nama as kelurahan, kab.nama as kabupaten, kec.nama as kecamatan, 
                kab.nama as kabupaten,
                pr.nama as provinsi
                from reg_rs rg
                left join kepegawaian kp on (rg.id_kepegawaian_direktur = kp.id)
                left join penduduk pp on (pp.id = kp.penduduk_id)
                left join relasi_instansi ins on (rg.id_instansi_relasi_penyelenggara = ins.id)
                left join relasi_instansi ir on (rg.id_instansi_relasi_penetap = ir.id)
                left join kelurahan kl on (rg.id_kelurahan = kl.id)
                left join kecamatan kec on (kl.kecamatan_id = kec.id)
                left join kabupaten kab on(kec.kabupaten_id = kab.id)
                left join provinsi pr on(pr.id = kab.provinsi_id)
                left join jenis_rs jrs on (rg.id_jenis_rs = jrs.id)
                where year(rg.waktu) = '$tahun'
                order by rg.id limit 0, 1";
        $query = $this->db->query($sql);
        return $query->row();
    }

    function get_registrasi_rs($id){
        $sql = "select rg.id as id_reg,jrs.nama as jenis_rs ,rg.* , 
                pp.nama as direktur,
                ins.nama as penyelenggara, ir.nama as penetap,
                kl.nama as kelurahan, kab.nama as kabupaten, kec.nama as kecamatan, 
                kab.nama as kabupaten,
                pr.nama as provinsi
                from reg_rs rg
                left join kepegawaian kp on (rg.id_kepegawaian_direktur = kp.id)
                left join penduduk pp on (pp.id = kp.penduduk_id)
                left join relasi_instansi ins on (rg.id_instansi_relasi_penyelenggara = ins.id)
                left join relasi_instansi ir on (rg.id_instansi_relasi_penetap = ir.id)
                left join kelurahan kl on (rg.id_kelurahan = kl.id)
                left join kecamatan kec on (kl.kecamatan_id = kec.id)
                left join kabupaten kab on(kec.kabupaten_id = kab.id)
                left join provinsi pr on(pr.id = kab.provinsi_id)
                left join jenis_rs jrs on (rg.id_jenis_rs = jrs.id)
                where rg.id = '$id' ";
        $query = $this->db->query($sql);
        return $query->row();
    }

    function register_edit_data($data) {
         // ambil data tempat tidur 
        $tt = "select count(id) as jumlah from tt";
        $vvip = " where kelas = 'VVIP'";
        $vip = " where kelas = 'VIP'";
        $satu = " where kelas = 'I'";
        $dua = " where kelas = 'II'";
        $tiga = " where kelas = 'III'";

        $data['jumlah_tt_VVIP'] = $this->db->query($tt . $vip)->row()->jumlah;
        $data['jumlah_tt_VIP'] = $this->db->query($tt . $vip)->row()->jumlah;
        $data['jumlah_tt_I'] = $this->db->query($tt . $satu)->row()->jumlah;
        $data['jumlah_tt_II'] = $this->db->query($tt . $dua)->row()->jumlah;
        $data['jumlah_tt_III'] = $this->db->query($tt . $tiga)->row()->jumlah;

        $this->db->where('id', $data['id']);
        $this->db->update('reg_rs', $data);
    }

    function register_delete_data($id) {
        $this->db->delete('reg_rs', array('id' => $id));
    }

    function register_cek_data($data) {
        $q = '';

        $sql = "select count(*) as jumlah from reg_rs
            where nama = '" . $data['nama'] . "' $q";
        $query = $this->db->query($sql);
        $jml = $query->row()->jumlah;
        if ($jml == 0) {
            return true;
        } else {
            return false;
        }
    }

    function get_jenis_reg() {
        return array('' => 'Pilih', 'Izin Pendirian' => 'Izin Pendirian', 'Izin Sementara' => 'Izin Sementara', 'Izin Operasional Tetap' => 'Izin Operasional Tetap');
    }

    function get_kelas() {
        return array('' => 'Pilih', "A" => "A", "B" => "B", "C" => "C", "D" => "D");
    }

    function get_sifat_penetapan() {
        return array('' => 'Pilih', 'Penurunan' => 'Penurunan', 'Peningkatan Kelas' => 'Peningkatan Kelas', 'Perpanjangan' => 'Perpanjangan');
    }

    function get_status_penyelenggara_swasta() {
        return array('' => 'Pilih', 'Islam' => 'Islam', 'Kristen' => 'Kristen', 'Katholik' => 'Katholik'
            , 'Hindu' => 'Hindu', 'Budha' => 'Budha',
            'Perusahaan' => 'Perusahaan', 'Perorangan' => 'Perorangan', 'Organisasi Sosial' => 'Organisasi Sosial');
    }

    function get_jenis_rs() {
        $sql = "select * from jenis_rs order by nama asc";
        $query = $this->db->query($sql)->result();
        $data[''] = 'Pilih';
        foreach ($query as $key => $value) {
            $data[$value->id] = $value->nama;
        }
        return $data;
    }

    function load_data_pegawai($q) {
        $sql = "select  kp.*, pp.nama from kepegawaian kp
            join penduduk pp on (pp.id = kp.penduduk_id)
            where pp.nama like ('%$q%') 
            and jabatan = 'Direktur'    
            order by locate('$q', pp.nama)";
        return $this->db->query($sql);
    }

    function get_data_spesialisasi_pegawai($nakes, $jenis = null){
        $q = " jjk.nakes = '$nakes'";
        if($jenis !== null){
            $q .= " and jjk.nama = '$jenis'";
        }

        $sql = "select jk.nama, jk.titel, count(*) as jumlah  from kepegawaian kp
                join jurusan_kualifikasi_pendidikan jk on (kp.id_jurusan_kualifikasi_pendidikan = jk.id)
                join jenis_jurusan_kualifikasi_pendidikan jjk on (jjk.id = jk.id_jenis_jurusan_kualifikasi_pendidikan)
                where $q
                group by jk.nama";
        return $this->db->query($sql)->result();
    }

    /* Registrasi RS */
}

?>