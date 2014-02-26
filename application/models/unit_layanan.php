<?php

class Unit_layanan extends CI_Model {

    function get_next_antrian($data) {
        $this->db->from('antrian_kunjungan');
        $this->db->where('id_jurusan_kualifikasi_pendidikan', $data['kd_unit']);
        $this->db->where('tanggal', $data['tgl_layan']);
        $this->db->select_max('no_antri');
        $pasien = "";
        if($data['pasien']){
            $pasien = " and pasien  = '1'";
        }else{
            $pasien = " and pasien = '0'";
        }

        $sql = "select max(no_antri) as no_antri from antrian_kunjungan 
                where id_jurusan_kualifikasi_pendidikan = '".$data['kd_unit']."' 
                and tanggal = '".$data['tgl_layan']."' $pasien ";
        //echo $sql;
        $query = $this->db->query($sql);
        $next_antrian = 0;

        if ($query->row() != null) {
            $unit = $query->row();
            $next_antrian = $unit->no_antri + 1;
        } else {
            $next_antrian = 1;
        }
        return $next_antrian;
    }


    function get_unit_layanan($n = null) {
        $q = '';
        if($n == 'nakes'){
            $q = "where jj.nakes = 'Ya'";
        }
        $sql = "select kp.* , jj.nama as jenis from jurusan_kualifikasi_pendidikan kp
                left join jenis_jurusan_kualifikasi_pendidikan jj on (kp.id_jenis_jurusan_kualifikasi_pendidikan = jj.id)
                 $q order by kp.nama asc";
        $query = $this->db->query($sql);

        $data = array();
        foreach ($query->result() as $row) {
            $data[$row->id] = $row->nama." - ".$row->jenis;
        }
        // return value sudah berupa array
        return $data;
    }

    function load_data_unit_layan($q) {
        $sql = "select * from layanan
        where nama like ('%$q%')  and nama != 'Pelayanan Resep'
        and nama != 'Cetak Kartu Pasien' and nama != 'Kunjungan'
        order by locate('$q', nama), nama asc";
        return $this->db->query($sql);
    }

    function get_unit_layanan_id() {

        $query = $this->db->query("selec * from jurusan_kualifikasi_pendidikan order by nama asc");
        $data = array();
        foreach ($query->result() as $row) {
            $data[$row->id] = $row->nama;
        }
        // return value sudah berupa array
        return $data;
    }

}

?>