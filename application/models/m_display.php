<?php

class M_display extends CI_Model {

    function reload_antrian(){            
           

        $sql = "select jk.nama ,
            (select count(*) from pendaftaran 
            where tgl_layan = '".date('Y-m-d')."' 
            and id_jurusan_kualifikasi_pendidikan = jk.id 
            ) as jml,  (
                select (max(no_antri)+1) from pendaftaran where 
                tgl_layan = '".date('Y-m-d')."' and status = '1' 
                and id_jurusan_kualifikasi_pendidikan = jk.id
            ) as last
            from jurusan_kualifikasi_pendidikan jk order by jk.nama ";
           // echo $sql;

        return $this->db->query($sql)->result();
    }

    function error_get_data($limit, $start){
        $q = null;
        $q.=" limit $start, $limit";

        $sql = "select * from error_log order by id desc";
        $query = $this->db->query($sql . $q);
        $ret['data'] = $query->result();
        $ret['jumlah'] = $this->db->query($sql)->num_rows();
        return $ret;

    }
   

}

?>