<?php

class M_laporan extends CI_Model {

    function __construct() {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
    }

    function get_bulan() {
        return array(
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember'
        );
    }

    function get_tahun() {
        $start = 2012;
        $arrYear = array();
        while ($start <= date('Y')) {
            $arrYear[$start] = $start;
            $start++;
        }

        return $arrYear;
    }

    function get_kunjungan_harian($param) {
        /*
         * Param
         * 1. from
         * 2. to
         */

        $q = " and tgl_layan BETWEEN '" . $param['from'] . "' AND '" . $param['to'] . "' and p.arrive_time is not NULL ";
        $db = "select tgl_layan, count(*) as jumlah FROM pendaftaran p 
            where p.pasien is not null ";
        if ($param['from'] != 'undefined-undefined-') {
            $db.=$q;
        }

        $db .="GROUP BY tgl_layan";
        //echo $db;
        $data = $this->db->query($db);
        return $data->result();
    }

    function get_kunjungan_harian_unit($param, $kd_unit) {
        /*
         * Param
         * 1. layanan =  semua layanan
         * 2. tgl = range tanggal
         */

        foreach ($param as $tgl) {
            $db = "select  count(*) as jumlah from pelayanan_kunjungan p 
            join pendaftaran pd on(p.id_kunjungan = pd.no_daftar)
            left join jurusan_kualifikasi_pendidikan u ON ( p.id_jurusan_kualifikasi_pendidikan = u.id )
            where p.jenis != 'Rawat Inap' ";
            if ($tgl->tgl_layan != null) {
                $db.="and date(pd.tgl_layan) = '$tgl->tgl_layan'";
            }

            if($kd_unit == 'igd'){
                $db .="and pd.jenis_rawat = 'IGD'";
            }else{
                $db .="and u.id = '" . $kd_unit . "'";
            }
            //echo "<pre>".$db."</pre><br/>";
            $query = $this->db->query($db." and pd.pasien is not null");
            $data[] = $query->row()->jumlah;
        }

        return $data;
    }

    function get_kunjungan_bulanan_unit($param, $kd_unit) {
        /*
         * Param
         * 1. layanan =  semua layanan
         * 2. tgl = range tanggal
         */


        foreach ($param as $row) {
            $db = "select  count(*) as jumlah from pelayanan_kunjungan p 
                join pendaftaran pd on(p.id_kunjungan = pd.no_daftar)
                left join jurusan_kualifikasi_pendidikan u ON ( p.id_jurusan_kualifikasi_pendidikan = u.id )  
                WHERE p.jenis != 'Rawat Inap' and month(pd.tgl_layan) = '" . $row->bulan . "' AND year(pd.tgl_layan) = '" . $row->tahun . "' ";

            if($kd_unit == 'igd'){
                $db .="AND pd.jenis_rawat = 'IGD'";
            }else{
                $db .="AND u.id = '" . $kd_unit . "'";
            }

            $query = $this->db->query($db." and pd.pasien is not null");
            $data[] = $query->row()->jumlah;
        }

        return $data;
    }

    function get_kunjungan_bulanan_pasien($param) {
        $db = "select month(tgl_layan) as bulan, year(tgl_layan) as tahun,  count(*) as jumlah FROM pendaftaran 
            where pasien is not null and
            tgl_layan between '" . $param['th_from'] . "-" . $param['bl_from'] . "-01" . "' 
                and '" . $param['th_to'] . "-" . $param['bl_to'] . "-31" . "' group by month(tgl_layan)";
        //echo $db;
        $data = $this->db->query($db);
        return $data->result();
    }

    function get_pasien_baru($param) {
        /*
         * Param =  result dari get_kunjungan_harian
         * 
         */
        $data = array();
        foreach ($param as $row) {
            $db = "select  count(*) as baru FROM pendaftaran p join pasien d on(p.pasien = d.no_rm) 
                where tgl_layan = '" . $row->tgl_layan . "' AND kunjungan = 1";
            $query = $this->db->query($db);


            $data[] = $query->row()->baru;
        }

        return $data;
    }

    function get_status_diagnosa_kasus($param){
        $data = array();
        foreach ($param as $row) {
            $sql = "select count(*) as jumlah from diagnosa_pelayanan_kunjungan dp
                    join pelayanan_kunjungan pk on (pk.id = dp.id_pelayanan_kunjungan)
                    join pendaftaran pd on (pk.id_kunjungan = pd.no_daftar)
                    where pd.tgl_layan = '".$row->tgl_layan."' and dp.kasus = ";
            $data['kasus_baru'][] = $this->db->query($sql."'Baru'")->row()->jumlah;
            $data['kasus_lama'][] = $this->db->query($sql."'Lama'")->row()->jumlah;
        }

        return $data;
    }

    function get_status_diagnosa_kasus_bulanan($param){
        $data = array();
        foreach ($param as $row) {
            $sql = "select count(*) as jumlah from diagnosa_pelayanan_kunjungan dp
                    join pelayanan_kunjungan pk on (pk.id = dp.id_pelayanan_kunjungan)
                    join pendaftaran pd on (pk.id_kunjungan = pd.no_daftar)
                    where month(pd.tgl_layan) = '".$row->bulan."' 
                    and year(pd.tgl_layan) = '" . $row->tahun . "'
                    and dp.kasus = ";
            $data['kasus_baru'][] = $this->db->query($sql."'Baru'")->row()->jumlah;
            $data['kasus_lama'][] = $this->db->query($sql."'Lama'")->row()->jumlah;
        }

        return $data;
    }

    function get_pasien_bl_baru($param) {
        /*
         * Param =  result dari get_kunjungan_harian
         * 
         */
        foreach ($param as $row) {
            $db = "select  count(*) as baru FROM pendaftaran p join pasien d on(p.pasien = d.no_rm) 
                WHERE month(tgl_layan) = '" . $row->bulan . "' and year(tgl_layan) = '" . $row->tahun . "' AND kunjungan = 1";
            $query = $this->db->query($db);


            $data[] = $query->row()->baru;
        }

        return $data;
    }

    function get_pasien_lama($param) {
        /*
         * Param =  result dari get_kunjungan_harian
         * 
         */
        foreach ($param as $row) {
            $db = "select  count(*) as lama FROM pendaftaran p join pasien d on(p.pasien= d.no_rm)
                WHERE tgl_layan = '" . $row->tgl_layan . "' AND kunjungan > 1";
            $query = $this->db->query($db);

            $data[] = $query->row()->lama;
        }

        return $data;
    }

    function get_pasien_bl_lama($param) {
        /*
         * Param =  result dari get_kunjungan_harian
         * 
         */
        foreach ($param as $row) {
            $db = "select  count(*) as lama FROM pendaftaran p join pasien d on(p.pasien = d.no_rm)
               WHERE month(tgl_layan) = '" . $row->bulan . "' AND year(tgl_layan) = '" . $row->tahun . "' AND kunjungan > 1";
            $query = $this->db->query($db);

            $data[] = $query->row()->lama;
        }

        return $data;
    }

    function get_demografi_agama($param) {
        /*
         * Param
         * 1. from
         * 2. to
         */
        $data = array();
        foreach ($param['agama'] as $key => $row) {
            $db = "select agama, count(*) AS jumlah FROM pasien p
                    JOIN penduduk d ON(p.id = d.id) 
                    JOIN dinamis_penduduk dp on(d.id = dp.penduduk_id)
                    WHERE dp.id in (select max(id) from dinamis_penduduk group by penduduk_id)
                    and dp.agama = '" . $row . "' ";
            if ($param['from'] != 'undefined-undefined-') {
                $db .= "AND date(p.registrasi_waktu) BETWEEN '" . $param['from'] . "' AND '" . $param['to'] . "' ";
            }


            $query = $this->db->query($db);

            $data[] = array($row, (int)$query->row()->jumlah);
        }
        return array('data' => $data);
    }

    function get_demografi_pekerjaan($param) {
        $item = array();
        $data = array();
        foreach ($param['pekerjaan'] as $row) {
            $db = "select pk.nama as pekerjaan , count(*) AS jumlah FROM pasien p
                JOIN penduduk d ON(p.id = d.id) 
                JOIN dinamis_penduduk dp on(d.id = dp.penduduk_id)
                JOIN pekerjaan pk on (dp.pekerjaan_id = pk.id)
               WHERE dp.id in (select max(id) from dinamis_penduduk group by penduduk_id)
               and pk.nama = '" . $row . "' ";

            if ($param['from'] != 'undefined-undefined-') {
                $db .= "AND date(p.registrasi_waktu) BETWEEN '" . $param['from'] . "' AND '" . $param['to'] . "' ";
            }
            $query = $this->db->query($db);
            $item[] = $row;
            $data[] = (int)$query->row()->jumlah;
                
        }

        return array('item' => $item, 'jumlah' => $data);
    }

    function get_demografi_pendidikan($param) {
        $item = array();
        $data = array();
       
        foreach ($param['pendidikan'] as $row) {
            $db = "select pend.nama as pendidikan , count(*) AS jumlah FROM pasien p
                JOIN penduduk d ON(p.id = d.id) 
                JOIN dinamis_penduduk dp on(d.id = dp.penduduk_id)
                JOIN pendidikan pend on (dp.pendidikan_id = pend.id)
                WHERE dp.id in (select max(id) from dinamis_penduduk group by penduduk_id)
                and pend.nama = '" . $row . "' ";

            if ($param['from'] != 'undefined-undefined-') {
                $db .= "AND date(p.registrasi_waktu) BETWEEN '" . $param['from'] . "' AND '" . $param['to'] . "' ";
            }

            $query = $this->db->query($db);
            $item[] = $row;
            $data[] = (int)$query->row()->jumlah;
                
        }

        return array('item' => $item, 'jumlah' => $data);
    }

    function get_demografi_nikah($param) {
        $data = array();
        foreach ($param['nikah'] as $row) {
            $db = "select pernikahan, count(*) AS jumlah FROM pasien p
                JOIN penduduk d ON(p.id = d.id) 
                JOIN dinamis_penduduk dp on(d.id = dp.penduduk_id)
                WHERE dp.id in (select max(id) from dinamis_penduduk group by penduduk_id)
                and dp.pernikahan = '" . $row . "'";

            if ($param['from'] != 'undefined-undefined-') {
                $db .= "AND date(p.registrasi_waktu) BETWEEN '" . $param['from'] . "' AND '" . $param['to'] . "' ";
            }
            $query = $this->db->query($db);

            $data[] = array($row, (int)$query->row()->jumlah);
        }
        return array('data' => $data);
    }

    function get_demografi_wilayah($awal, $akhir, $tipe, $param) {
        $area = array();
        $data = array();
        if(is_array($param)){
            foreach ($param as $row) {
                $db = "count(*) as jumlah 
                        FROM pasien p
                        JOIN penduduk d on(p.id = d.id)
                        JOIN dinamis_penduduk dp on(d.id = dp.penduduk_id)
                        left JOIN kelurahan kel on (kel.id = dp.kelurahan_id)
                        left JOIN kecamatan kec on (kel.kecamatan_id = kec.id)
                        left JOIN kabupaten kabb on (kec.kabupaten_id = kabb.id)
                        left JOIN provinsi pro on (kabb.provinsi_id = pro.id)";

                if ($tipe == "kelurahan") {
                    // array
                    $db = "select kel.nama as area ," . $db . " WHERE kel.id = '" . $row . "'";
                } else if ($tipe == "kecamatan") {
                    //array
                    $db = "select kec.nama as area, " . $db . "WHERE kec.id = '" . $row . "'";
                } else if ($tipe == "kabupaten") {
                    $db = "select  kabb.nama as area," . $db . "WHERE kabb.id = '" . $row . "'";
                } else if ($tipe == "provinsi") {
                    $db = "select  pro.nama as area," . $db . "WHERE pro.id = '" . $row . "'";
                }

                $db .= " and dp.id in (select max(id) from dinamis_penduduk group by penduduk_id)";
                if ($awal != null) {
                    $db .= "AND date(p.registrasi_waktu) BETWEEN '" . $awal . "' AND '" . $akhir . "' ";
                }
                //echo $db;
                $query = $this->db->query($db);
                if ($query->row()->jumlah != 0) {
                    $area[] = $query->row()->area;
                    $data[] = (int)$query->row()->jumlah;
                }
            }

            return array('area' => $area, 'jumlah' => $data);
        }else{
            return null;
        }
    }

    function get_demografi_kelamin($param) {
        $data = array();
        foreach ($param['kelamin'] as $key => $row) {
            $db = "select gender, count(*) as jumlah FROM pasien p
                JOIN penduduk d on(p.id = d.id)
            WHERE d.gender = '" . $key . "' ";

            if ($param['from'] != 'undefined-undefined-') {
                $db .= "AND date(p.registrasi_waktu) BETWEEN '" . $param['from'] . "' AND '" . $param['to'] . "' ";
            }

            $query = $this->db->query($db);
            if ($key == 'L') {
                $kelamin = 'Laki - laki';
            }else{
                $kelamin = 'Perempuan';
            }
            $data[] = array($kelamin, (int)$query->row()->jumlah);
        }
        return array('data' => $data);
    }

    function get_demografi_darah($param) {
        $data = array();
        foreach ($param['darah'] as $row) {
            $db = "select darah_gol, count(*) as jumlah FROM pasien p
                JOIN penduduk d on(p.id = d.id)
                WHERE d.darah_gol = '" . $row . "' ";

            if ($param['from'] != 'undefined-undefined-') {
                $db .= "AND date(p.registrasi_waktu) BETWEEN '" . $param['from'] . "' AND '" . $param['to'] . "' ";
            }

            $query = $this->db->query($db);

            $data[] = array($row, (int)$query->row()->jumlah);
        }
        return array('data' => $data);
    }

    function get_demografi_usia($param) {
        $data = array();
        foreach ($param['usia'] as $key => $row) {
            $usia = explode("-", $row);
            $db = "select  count(*) as jumlah FROM pasien p
                JOIN penduduk d on(p.id = d.id)
                WHERE datediff('" . date('Y-m-d') . "',d.lahir_tanggal ) >  '" . $usia[0] . "' ";
            if (isset($usia[1])) {
                $db .= " AND datediff('" . date('Y-m-d') . "',d.lahir_tanggal ) < '" . $usia[1] . "' ";
            }

            if ($param['from'] != 'undefined-undefined-') {
                $db .= "AND date(p.registrasi_waktu) BETWEEN '" . $param['from'] . "' AND '" . $param['to'] . "' ";
            }
            //echo "<pre>".$db."</pre>";
            $query = $this->db->query($db);
            $data[] = array($param['format_usia'][$key], (int)$query->row()->jumlah);
        }

        return array('data' => $data);
    }
    
    function get_data_all_pasien($awal) {
        $sql = "
            select
            AllDaysYouWant.MyJoinDate,
            count( tgl_layan ) as jumlah
            from
            ( select
                    @curDate := Date_Add(@curDate, interval 1 day) as MyJoinDate
                 from
                    ( select @curDate := '$awal' ) sqlvars,
                    pendaftaran
                 limit 7 ) AllDaysYouWant
            LEFT JOIN pendaftaran p
               on AllDaysYouWant.MyJoinDate = date(p.tgl_daftar)
            group by
            AllDaysYouWant.MyJoinDate
        ";
        return $this->db->query($sql);
    }
    
    


    function kategori_demografi($tabel) {
        $db = $this->db->get($tabel);
        foreach ($db->result() as $row) {
            $data[$row->id] = $row->nama;
        }
        return $data;
    }

    function rujukan_get_data($limit, $start, $data) {
        $range = '';
        $nakes = '';
        $relasi = '';
        $q = '';
        $q .= " limit " . $start . ", $limit";

        if (($data['from'] != '') & ($data['to'] != '')) {
            $range = " and p.tgl_daftar  BETWEEN '" . $data['from'] . "' AND '" . $data['to'] . "'";
        }

        if ($data['instansi'] != '') {
            $relasi = " and r.id = '" . $data['instansi'] . "'";
        }
        if ($data['nakes'] != '') {
            $nakes = " and nk.id = '" . $data['nakes'] . "'";
        }

        $sql = "select p.tgl_daftar, pd.nama as nama_pasien,r.nama as nama_instansi, nk.nama as nama_nakes  FROM pendaftaran p
            left join pasien d on (p.pasien = d.no_rm) 
            left join penduduk pd on (pd.id = d.id)
            left join relasi_instansi r on(p.rujukan_instansi_id = r.id)
            left join penduduk nk on(p.nakes_penduduk_id = nk.id)
            WHERE rujukan_instansi_id is not null and nakes_penduduk_id is not null $range $relasi $nakes";
   
        $data = $this->db->query($sql . $q);
        $ret['hasil'] = $data->result();
        $ret['jumlah'] = $this->db->query($sql)->num_rows();
        return $ret;
    }
    
    function kegiatan_rujukan_load_data($ar) {
        $q = null;
        if ($ar['awal'] != '' and $ar['akhir'] != '') {
            $q.=" and p.tgl_layan between '$ar[awal]' and '$ar[akhir]'";
        }
        if ($ar['jenis'] != '') {
            if ($ar['jenis'] == 'Puskesmas') {
                $q.=" and rij.nama = 'Puskesmas'";
            }
            if ($ar['jenis'] == 'R.S') {
                $q.=" and rij.nama = 'Rumah Sakit'";
            }
            if ($ar['jenis'] == 'lain') {
                $q.=" and rij.nama not in ('Puskesmas','Rumah Sakit')";
            }
        }
        $sql = "select count(*) as jumlah, p.*, j.nama from pendaftaran p
            join pelayanan_kunjungan pk on (p.no_daftar = pk.id_kunjungan)
            left join jurusan_kualifikasi_pendidikan j on (j.id = pk.id_jurusan_kualifikasi_pendidikan)
            left join relasi_instansi r on (p.rujukan_instansi_id = r.id)
            left join relasi_instansi_jenis rij on(rij.id = r.relasi_instansi_jenis_id) 
            left join jenis_jurusan_kualifikasi_pendidikan jn on (jn.id = j.id_jenis_jurusan_kualifikasi_pendidikan)
            where p.diterima_kembali = 'Tidak' $q
            and p.rujukan_instansi_id is not null
            and pk.id in (select min(id) from pelayanan_kunjungan  group by id_kunjungan  )
            group by p.no_daftar";
        //echo "<pre>".$sql."</pre>";
        return $this->db->query($sql);
    }
    
    function kebidanan_load_data($ar) {
        $q = null;
        if ($ar['awal'] != '' and $ar['akhir'] != '') {
            $q.=" and t.waktu between '$ar[awal] 00:00:00' and '$ar[akhir] 23:59:59'";
        }
        
        if ($ar['jenis'] != '') {
            if ($ar['jenis'] == 'Puskesmas') {
                $q.=" and rij.nama = 'Puskesmas'";
            }
            if ($ar['jenis'] == 'R.S') {
                $q.=" and rij.nama = 'Rumah Sakit'";
            }
            if ($ar['jenis'] == 'lain') {
                $q.=" and rij.nama not in ('Puskesmas','Rumah Sakit')";
            }
        }
        if ($ar['jnakes'] != '') {
            if ($ar['jnakes'] == 'Medis') {
                $q.=" and pf.jenis = 'Nakes'";
            }
            if ($ar['jnakes'] == 'Non Medis') {
                $q.=" and pf.jenis = 'Non Nakes'";
            }
        }
        if ($ar['rujukan'] != '') {
            if ($ar['rujukan'] == 'Tidak') {
                $q.=" and pd.rujukan_instansi_id is NULL";
            } else {
                $q.=" and pd.rujukan_instansi_id is not NULL";
            }
        }

        $sql = "select count(*) as jumlah, t.*, ss.nama as nama_ss, 
        sj.nama as nama_sj, l.nama as nama_layanan  from tindakan_pelayanan_kunjungan t
            join layanan l on (l.id = t.id_layanan)
            join sub_sub_jenis_layanan ss on (l.id_sub_sub_jenis_layanan = ss.id)
            join sub_jenis_layanan sjl on (ss.id_sub_jenis_layanan = sjl.id)
            join jenis_layanan jl on (sjl.id_jenis_layanan = jl.id)
            left join sub_jenis_layanan sj on (ss.id_sub_jenis_layanan = sj.id)
            join pelayanan_kunjungan p on (p.id = t.id_pelayanan_kunjungan)
            join pendaftaran pd on (pd.no_daftar = p.id_kunjungan)
            left join relasi_instansi r on (pd.rujukan_instansi_id = r.id)
            left join relasi_instansi_jenis rij on (rij.id = r.relasi_instansi_jenis_id)
            left join dinamis_penduduk dp on (pdd.id = dp.penduduk_id)
            left join profesi pf on (dp.profesi_id = pf.id)
            inner join (
                select penduduk_id, max(id) as id_max from dinamis_penduduk GROUP BY penduduk_id
            ) dm on (dp.penduduk_id = dm.penduduk_id and dp.id = dm.id_max)
            where jl.nama = 'Kebidanan' $q group by ss.id
            ";
        //echo "<pre>".$sql."</pre>";
        return $this->db->query($sql);
    }
    function perinatori_load_data($ar) {
        $q = null;
        if ($ar['awal'] != '' and $ar['akhir'] != '') {
            $q.=" and t.waktu between '$ar[awal] 00:00:00' and '$ar[akhir] 23:59:59'";
        }
        if ($ar['jenis'] != '') {
            if ($ar['jenis'] == 'Puskesmas') {
                $q.=" and rij.nama = 'Puskesmas'";
            }
            if ($ar['jenis'] == 'R.S') {
                $q.=" and rij.nama = 'Rumah Sakit'";
            }
            if ($ar['jenis'] == 'lain') {
                $q.=" and rij.nama not in ('Puskesmas','Rumah Sakit')";
            }
        }
        if ($ar['jnakes'] != '') {
            if ($ar['jnakes'] == 'Medis') {
                $q.=" and pf.jenis = 'Nakes'";
            }
            if ($ar['jnakes'] == 'Non Medis') {
                $q.=" and pf.jenis = 'Non Nakes'";
            }
        }
        if ($ar['rujukan'] != '') {
            if ($ar['rujukan'] == 'Tidak') {
                $q.=" and pd.rujukan_instansi_id is NULL";
            } else {
                $q.=" and pd.rujukan_instansi_id is not NULL";
            }
        }
         $sql = "select count(*) as jumlah, dp.id as id_dinamis, jl.id as id_jl, sjl.id as id_sj, ss.id as id_ss, 
            t.*, jl.nama as nama_jl, ss.nama as nama_ss, sjl.nama as nama_sj, 
            l.nama as nama_layanan from tindakan_pelayanan_kunjungan t
            join layanan l on (l.id = t.id_layanan)
            join sub_sub_jenis_layanan ss on (l.id_sub_sub_jenis_layanan = ss.id)
            join sub_jenis_layanan sjl on (ss.id_sub_jenis_layanan = sjl.id)
            join jenis_layanan jl on (sjl.id_jenis_layanan = jl.id)
            join pelayanan_kunjungan p on (p.id = t.id_pelayanan_kunjungan)
            join pendaftaran pd on (pd.no_daftar = p.id_kunjungan)
            left join relasi_instansi r on (pd.rujukan_instansi_id = r.id)
            left join relasi_instansi_jenis rij on (rij.id = r.relasi_instansi_jenis_id)
            left join penduduk pdd on (pd.nakes_penduduk_id = pdd.id)
            left join dinamis_penduduk dp on (pdd.id = dp.penduduk_id)
            left join profesi pf on (dp.profesi_id = pf.id)
            left join (
                select penduduk_id, max(id) as id_max from dinamis_penduduk GROUP BY penduduk_id
            ) dm on (dp.penduduk_id = dm.penduduk_id and dp.id = dm.id_max)
            where jl.nama = 'Perinatologi' $q group by ss.id";
            /*
        $sql = "select count(*) as jumlah, jl.id as id_jl, sj.id as id_sjl, ss.id as id_ss, t.*, jl.nama as nama_jl, ss.nama as nama_ss, sj.nama as nama_sj, l.nama as nama_layanan  from tindakan_pelayanan_kunjungan t
            join layanan l on (l.id = t.id_layanan)
            join sub_sub_jenis_layanan ss on (l.id_sub_sub_jenis_layanan = ss.id)
            join sub_jenis_layanan sjl on (ss.id_sub_jenis_layanan = sjl.id)
            join jenis_layanan jl on (sjl.id_jenis_layanan = jl.id)
            left join sub_jenis_layanan sj on (ss.id_sub_jenis_layanan = sj.id)
            join pelayanan_kunjungan p on (p.id = t.id_pelayanan_kunjungan)
            join pendaftaran pd on (pd.no_daftar = p.id_kunjungan)
            left join relasi_instansi r on (pd.rujukan_instansi_id = r.id)
            left join relasi_instansi_jenis rij on (rij.id = r.relasi_instansi_jenis_id)
            left join penduduk pdd on (pd.nakes_penduduk_id = pdd.id)
            left join dinamis_penduduk dp on (pdd.id = dp.penduduk_id)
            left join profesi pf on (dp.profesi_id = pf.id)
            inner join (
                select penduduk_id, max(id) as id_max from dinamis_penduduk GROUP BY penduduk_id
            ) dm on (dp.penduduk_id = dm.penduduk_id and dp.id = dm.id_max)
            where jl.nama = 'Perinatologi' $q group by ss.id
            ";*/    
        //echo $sql;
        return $this->db->query($sql);
    }
    
    function pembedahan_load_data($ar) {
        $q = null;
        if ($ar['awal'] != '' and $ar['akhir'] != '') {
            $q.=" and jp.waktu between '$ar[awal] 00:00:00' and '$ar[akhir] 23:59:59'";
        }
        if ($ar['bobot'] != '') {
            $q.=" and t.bobot = '$ar[bobot]'";
        }
        $sql = "select count(*) as jumlah, jp.*, jkp.nama, jl.nama as jenis_layanan, 
                l.nama as layanan 
                from tindakan_pelayanan_kunjungan jp
                join layanan l on (jp.id_layanan = l.id) 
                join sub_sub_jenis_layanan ssjl on (l.id_sub_sub_jenis_layanan = ssjl.id) 
                join sub_jenis_layanan sjl on (ssjl.id_sub_jenis_layanan = sjl.id) 
                join jenis_layanan jl on (sjl.id_jenis_layanan = jl.id) 
                join kepegawaian kp on (jp.id_kepegawaian_nakes_operator = kp.id) 
                join jurusan_kualifikasi_pendidikan jkp on (kp.id_jurusan_kualifikasi_pendidikan = jkp.id) 
                where jl.nama = 'Pembedahan' group by jkp.id";
       // echo "<pre>".$sql."</pre>";
        return $this->db->query($sql);
    }

    function penjualan_jasa_load_data($limit, $start, $param) {
        $q = null;
        $page = "  limit $start ,$limit";
        if ($param['awal'] != null and $param['akhir'] != null) {
            $q.=" date(jpd.waktu) between '".date2mysql($param['awal'])."' and '".date2mysql($param['akhir'])."'";
        }
        if ($param['nakes'] != null) {
            $q.=" and jpd.id_kepegawaian_nakes = '$param[nakes]'";
        }
        $sql = "select b.nama as barang, jpd.*, jpd.id as id_jasa, pdf.no_daftar,
            t.nominal, pd.nama as pegawai,
            t.id_barang_sewa, l.nama as layanan, CONCAT_WS('; ',l.nama, pr.nama, t.bobot, t.kelas) as nama_tarif
            from  jasa_penjualan_detail jpd
            join pelayanan_kunjungan pk on (jpd.id_pelayanan_kunjungan = pk.id)
            join pendaftaran pdf on (pk.id_kunjungan = pdf.no_daftar)
            join tarif t on (jpd.tarif_id = t.id)
            left join profesi pr on (t.id_profesi = pr.id)
            left join layanan l on (t.id_layanan = l.id)
            left join kepegawaian kp on (jpd.id_kepegawaian_nakes = kp.id)
            left join penduduk pd on(pd.id = kp.penduduk_id)
            left join kemasan bp on (bp.id = t.id_barang_sewa)
            left join barang b on (bp.id_barang = b.id)
            where $q and jpd.id_kepegawaian_nakes is not null";

        $sql2 = "select sum(t.jasa_sarana) as jasa_sarana, 
            sum(t.jasa_nakes) jasa_nakes,
            sum(t.jasa_tindakan_rs) as jasa_tindakan, 
            sum(t.bhp) as bhp, sum(t.biaya_administrasi) as biaya_administrasi,
            sum(t.nominal) as total_biaya
            from  jasa_penjualan_detail jpd
            join tarif t on (jpd.tarif_id = t.id)
            left join kepegawaian kp on (jpd.id_kepegawaian_nakes = kp.id)
            where $q and jpd.id_kepegawaian_nakes is not null";
        $data['data'] = $this->db->query($sql.$page)->result();
        $data['jumlah'] = $this->db->query($sql)->num_rows();
        $data['total'] = $this->db->query($sql2)->row();
        return $data;
    }

    function data_pasien_lama_baru($tgl){
        $data = array();

        foreach ($tgl as $key => $value) {

            $sqllama = "select count(*) as data FROM pendaftaran p 
                    join pasien d on(p.pasien= d.no_rm)
                    WHERE p.tgl_layan = '$value'  and kunjungan > 1"; 

                    
            $sqlbaru = "select count(*) as data FROM pendaftaran p 
                    join pasien d on(p.pasien= d.no_rm)
                    WHERE  p.tgl_layan = '$value' and kunjungan = 1"; 

            $data['lama'][] = (int) $this->db->query($sqllama)->row()->data;
            $data['baru'][] = (int) $this->db->query($sqlbaru)->row()->data;
        }
        
        return $data;

    }

    function data_pasien_per_unit($kd_unit, $tgl){
        $data = array();
        foreach ($tgl as $key => $value) {
            $sql = "select  count(*) as jumlah , u.nama, p.waktu from pelayanan_kunjungan p 
                join jurusan_kualifikasi_pendidikan u ON ( p.id_jurusan_kualifikasi_pendidikan = u.id ) 
                where p.id_kunjungan is not null and (p.waktu between '$value 00:00:00' and '$value 23.:59:59') 
                and u.id = '$kd_unit' 
                and p.jenis != 'Rawat Inap'
                ";
            //echo $sql."<br/>";
            $query = $this->db->query($sql)->row();
            if ($query != null) {
                $data[] = (int)$query->jumlah;
            }else{
                $data[] = 0;
            }
        }

        return $data;
    }

    function data_top10_diagnosis($awal, $akhir){ 

        $sql = "select count(*) as jumlah,  g.nama from diagnosa_pelayanan_kunjungan do
                join golongan_sebab_sakit g on(g.id = do.id_golongan_sebab_penyakit)
                where do.waktu between '".$awal."' and '".$akhir."'
                group by g.id order by jumlah desc limit 0, 10 ";
       // echo $sql;

        return $this->db->query($sql)->result();
    }


}

?>