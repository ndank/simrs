<?php

class M_demografi extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function get_last_no_rm(){
        $db = $this->db->query('SELECT max(no_rm) as no_rm FROM pasien')->row();
        return str_pad($db->no_rm+1, 6,"0",STR_PAD_LEFT);
    }

    function load_jurusan($q) {
        $sql = "select kp.* , jj.nama as jenis, jj.id as id_jenis from jurusan_kualifikasi_pendidikan kp
            left join jenis_jurusan_kualifikasi_pendidikan jj on(kp.id_jenis_jurusan_kualifikasi_pendidikan = jj.id)            
            where  kp.nama like ('%$q%') and jj.nakes = 'Ya' order by locate('$q', kp.nama)";
        
        return $this->db->query($sql);
    }

    function get_by_no_rm($no_rm) {
        $sql = "select p.kunjungan, p.id, p.no_rm, p.is_cetak_kartu ,pd.nama as nama, pd.gender, 
            kab.nama as tempat_lahir, kab.id as tempat_lahir_id, pd.lahir_tanggal,
            pd.telp, pd.darah_gol, dp.agama,pdi.nama as pendidikan, pdi.id as pendidikan_id ,
            pk.nama as pekerjaan, pk.id as pekerjaan_id , dp.pernikahan, dp.id as dinamis_penduduk_id,
            dp.alamat, dp.identitas_no, kel.nama as kelurahan, kel.id as kelurahan_id, 
            kabb.nama as kabupaten, kec.nama as kecamatan, dp.profesi_id, pd.lahir_kabupaten_id,
            pro.nama as provinsi
            from pasien p 
            join penduduk pd on (p.id = pd.id)
            left join kabupaten kab on (kab.id = pd.lahir_kabupaten_id)
            left join dinamis_penduduk dp on (pd.id = dp.penduduk_id)
            left join pendidikan pdi on (pdi.id = dp.pendidikan_id)
            left join pekerjaan pk on (pk.id = dp.pekerjaan_id)
            left join kelurahan kel on (kel.id = dp.kelurahan_id)
            left join kecamatan kec on (kel.kecamatan_id = kec.id)
            left join kabupaten kabb on (kec.kabupaten_id = kabb.id)
            left join provinsi pro on (kabb.provinsi_id = pro.id)
            inner join (
                select penduduk_id, max(id) as id_max
                from dinamis_penduduk GROUP BY penduduk_id
            ) dpi on (dp.penduduk_id = dpi.penduduk_id and dp.id = dpi.id_max)
            where p.no_rm = '" . $no_rm . "'";
            
        $data = $this->db->query($sql);
        return $data->result();
    }

    function detail_data_pasien($no_rm) {
        $sql = "select pd.id as penduduk_id, p.kunjungan, p.id, p.no_rm, dp.pernikahan,
            p.is_cetak_kartu ,pd.nama as nama, pd.gender, pdi.nama as pendidikan, pdi.id as pendidikan_id ,
            kab.nama as tempat_lahir, kab.id as tempat_lahir_id, pd.lahir_tanggal,
            pd.telp, pd.darah_gol, dp.agama, dp.id as dinamis_penduduk_id, dp.pendidikan_id,
            dp.pekerjaan_id, dp.profesi_id, pk.nama as pekerjaan, pk.id as pekerjaan_id,
            dp.alamat, dp.identitas_no, kel.nama as kelurahan, kel.id as kelurahan_id, 
            kabb.nama as kabupaten, kec.nama as kecamatan, 
            pro.nama as provinsi
            from pasien p 
            join penduduk pd on (p.id = pd.id)
            left join kabupaten kab on (kab.id = pd.lahir_kabupaten_id)
            left join dinamis_penduduk dp on (pd.id = dp.penduduk_id)
            left join kelurahan kel on (kel.id = dp.kelurahan_id)
            left join kecamatan kec on (kel.kecamatan_id = kec.id)
            left join kabupaten kabb on (kec.kabupaten_id = kabb.id)
            left join provinsi pro on (kabb.provinsi_id = pro.id)
            left join pendidikan pdi on (pdi.id = dp.pendidikan_id)
            left join pekerjaan pk on (pk.id = dp.pekerjaan_id)
            inner join (
                select penduduk_id, max(id) as id_max
                from dinamis_penduduk GROUP BY penduduk_id
            ) dpi on (dp.penduduk_id = dpi.penduduk_id and dp.id = dpi.id_max)
            where p.no_rm = '" . $no_rm . "'";
        $exe = $this->db->query($sql);
        return $exe->row();
    }

    function search_penduduk($limit, $start){
        $norm   = get_safe('norm');
        $nama   = get_safe('nama');
        $alamat = get_safe('alamat');
        $q      = NULL;
        if ($norm !== '') {
            $q.=" and ps.no_rm like '%$norm%'";
        }
        if ($nama !== '') {
            $q.=" and pd.nama like '%".get_safe('nama')."%'";
        }
        if ($alamat !== '') {
            $q.=" and dp.alamat like '%".get_safe('alamat')."%' ";
        }
        $limitation =" limit $start , $limit";
        $sql = "select pd.*, pd.id as id_penduduk, ps.no_rm,dp.* from penduduk pd
                left join pasien ps on (ps.id = pd.id)
                left join dinamis_penduduk dp on (pd.id = dp.penduduk_id)
                left join kelurahan kel on (kel.id = dp.kelurahan_id)
                where pd.id is not NULL $q
                order by pd.nama, dp.alamat ";
        $data['jumlah'] =  $this->db->query($sql)->num_rows();
        $data['data'] = $this->db->query($sql. $limitation)->result();
        return $data;
    }
    
    function search_pasien($search) {
        $q = NULL;
        if ($search['nama'] !== '') {
            $q.="and pd.nama like ('%".$search['nama']."%')";
        }
        if ($search['alamat'] !== '') {
            $q.="and dp.alamat like ('%".$search['alamat']."%') or kel.nama like ('%".$search['alamat']."%')";
        }
        $sql = "select pd.*, pd.id as id_penduduk, ps.no_rm,dp.* from penduduk pd
            join pasien ps on (ps.id = pd.id)
            join dinamis_penduduk dp on (pd.id = dp.penduduk_id)
            left join kelurahan kel on (kel.id = dp.kelurahan_id)
            where pd.id is not NULL $q
            order by pd.nama limit 10";
    //        echo $sql;
        return $this->db->query($sql);
    }

    function search_by_no_rm($limit, $start, $no_rm){
        $q = '';
        if ($no_rm != '') {
            $q = " and p.no_rm = '$no_rm' ";
        }
        $paging = " limit " . $start . "," . $limit . " ";
        $sql = "select p.kunjungan, p.id, p.no_rm, p.is_cetak_kartu ,pd.nama as nama, pd.gender, kab.nama as tempat_lahir, kab.id as tempat_lahir_id, pd.lahir_tanggal,
            pd.telp, pd.darah_gol, dp.agama,pdi.nama as pendidikan, pdi.id as pendidikan_id , pk.nama as pekerjaan, 
            pk.id as pekerjaan_id , dp.pernikahan, dp.id as dinamis_penduduk_id,
            dp.alamat, dp.identitas_no, kel.nama as kelurahan, kel.id as kelurahan_id, kabb.nama as kabupaten, kec.nama as kecamatan, 
            pro.nama as provinsi
            from pasien p 
            join penduduk pd on (p.id = pd.id)
            left join kabupaten kab on (kab.id = pd.lahir_kabupaten_id)
            left join dinamis_penduduk dp on (pd.id = dp.penduduk_id)
            left join pendidikan pdi on (pdi.id = dp.pendidikan_id)
            left join pekerjaan pk on (pk.id = dp.pekerjaan_id)
            left join kelurahan kel on (kel.id = dp.kelurahan_id)
            left join kecamatan kec on (kel.kecamatan_id = kec.id)
            left join kabupaten kabb on (kec.kabupaten_id = kabb.id)
            left join provinsi pro on (kabb.provinsi_id = pro.id)
            inner join (
                select penduduk_id, max(id) as id_max
                from dinamis_penduduk GROUP BY penduduk_id
            ) dpi on (dp.penduduk_id = dpi.penduduk_id and dp.id = dpi.id_max) $q";
        
        $status = "select (
                select count(*) from pasien p
        join penduduk pd on (p.id = pd.id)
            left join dinamis_penduduk dp on (dp.penduduk_id = pd.id)
                inner join (
                    select penduduk_id, max(id) as id_max from  dinamis_penduduk
                    group by penduduk_id
                ) tm on (dp.penduduk_id = tm.penduduk_id and dp.id =    tm.id_max) 
                where kunjungan > 1 and pd.id is not NULL $q
                )
             as lama, 
            (select count(*) from pasien p
        join penduduk pd on (p.id = pd.id)
            left join dinamis_penduduk dp on (dp.penduduk_id = pd.id)
                inner join (
                    select penduduk_id, max(id) as id_max from  dinamis_penduduk
                    group by penduduk_id
                ) tm on (dp.penduduk_id = tm.penduduk_id and dp.id = tm.id_max) 
            where kunjungan <= 1 and pd.id is not NULL $q) as baru ";
                
        //echo $sql.$paging;
        $data = $this->db->query($sql . $paging);
        $ret['data'] = $data->result();
        $ret['jumlah'] = $this->db->query($sql)->num_rows;
        $ret['status'] =  $this->db->query($status)->row();
        return $ret;
    }

    function antrian_save_data(){
        $data = array(
            'tanggal' => date2mysql(post_safe('tgl_layan')),
            'id_jurusan_kualifikasi_pendidikan' => post_safe('id_layanan'),
            'id_kepegawaian_dpjp' => (post_safe('id_dokter')=='')?NULL:post_safe('id_dokter'),
            'no_rm' => (post_safe('no_rm')=='')?NULL:post_safe('no_rm'),
            'nama_calon_pasien' => post_safe('nama'),
            'gender' => post_safe('kelamin'),
            'lahir_tanggal' => date2mysql(post_safe('tgl_lahir')),
            'alamat_jalan_calon_pasien' => post_safe('alamat'),
            'id_kelurahan' => (post_safe('id_kelurahan') != '')?post_safe('id_kelurahan'):NULL,
            'no_antri' => post_safe('antrian'),
            'telp_no' => post_safe('tlpn'),
            'penduduk_id' => (post_safe('id_penduduk')!="")?post_safe('id_penduduk'):NULL
        );

        $this->db->insert('antrian_kunjungan', $data);
    }

    function set_norm_antrian($id_antri, $no_rm){
        $update = array(
                'no_rm' => $no_rm
            );
        $this->db->where('id', $id_antri);
        $this->db->update('antrian_kunjungan', $update);
    }

    function konfirmasi_antrian($id_antri){
        // mengubah nilai konfirm tabel antrian_kunjungan
        $update = array(
                'konfirm' => 1
            );
        $this->db->where('id', $id_antri);
        $this->db->update('antrian_kunjungan', $update);
    }

    function add_kunjungan_pasien($no_rm){
        // menambah jumlah kunjungan pada tabel pasien
        $kunjungan = $this->db->query("select kunjungan from pasien where no_rm = '".$no_rm."' ")->row()->kunjungan;
        
        $data = array(
            'kunjungan' => ($kunjungan + 1)
        );

        $this->db->where('no_rm', $no_rm);
        $this->db->update('pasien',$data);
    }


    function get() {
        $this->db->from('pasien');
        $this->db->order_by('no_rm');
        $data = $this->db->get();
        return $data->result();
    }

    function get_where($data) {
        $data = $this->db->get_where('pasien', $data);
        return $data->row();
    }

    function next_kunjungan($param) {
        // $param = nomor id pasien pasien 
        $this->db->trans_begin();

        $this->db->from('pasien');
        $this->db->where('no_rm', $param['no_rm']);
        $data = $this->db->get();
        $demo = $data->row();

        $next_kunjungan = $demo->kunjungan + 1;
        $this->db->where('no_rm', $param['no_rm']);
        $this->db->update('pasien', array('kunjungan' => $next_kunjungan));

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
        } else {
            $this->db->trans_commit();
        }
    }

    function create_penduduk($data) {
        $this->db->insert('penduduk', $data);
        return $this->db->insert_id();
    }

    function save_penduduk($data) {
        $this->db->where('id', $data['id']);
        $this->db->update('penduduk', $data);
    }

    function create_dinamis_penduduk($data) {
        $this->db->insert('dinamis_penduduk', $data);
        return $this->db->insert_id();
    }

    function create_pasien($data) {
        $this->db->insert('pasien', $data);
        return $this->db->insert_id();
    }

    function save($data) {
        $this->db->where('no_rm', $data['no_rm']);
        $this->db->update('pasien', $data);
    }

    function add_is_cetak_kartu($no_rm) {
        //get jumlah is_cetak_kartu , then plus 1
        // update       
        $data = $this->get_by_no_rm($no_rm);
        $update = array(
            'is_cetak_kartu' => '1'
        );


        $this->db->where('no_rm', $no_rm);
        $this->db->update('pasien', $update);
    }

    function detail_kelurahan($id) {
        $sql = "select kel.*, kec.nama as kecamatan, kab.id as kabupaten_id ,
            kab.nama as kabupaten, pro.nama as provinsi from kelurahan kel
            left join kecamatan kec on (kel.kecamatan_id = kec.id)
            left join kabupaten kab on (kec.kabupaten_id = kab.id)
            left join provinsi pro on (kab.provinsi_id = pro.id)
        where kel.id = '$id'";
        $exe = $this->db->query($sql);
        return $exe->row();
    }

    function get_kelurahan($q) {
        $q = $this->db->escape_str($q);
        $sql = "select kel.*, kec.nama as kecamatan, kab.id as kabupaten_id ,kab.nama as kabupaten, pro.nama as provinsi from kelurahan kel
            left join kecamatan kec on (kel.kecamatan_id = kec.id)
            left join kabupaten kab on (kec.kabupaten_id = kab.id)
            left join provinsi pro on (kab.provinsi_id = pro.id)
        where kel.nama like ('%$q%') order by locate ('$q', kel.nama)";
        $exe = $this->db->query($sql);
        return $exe->result();
    }

    function get_kabupaten($q) {
        $sql = "select  kab.id as kabupaten_id ,kab.nama as kabupaten, pro.nama as provinsi from
            kabupaten kab left join provinsi pro on (kab.provinsi_id = pro.id)
        where kab.nama like ('%$q%') order by locate ('$q', kab.nama)";
        $exe = $this->db->query($sql);
        return $exe->result();
    }

    function get_asuransi($q) {
        $sql = "select * from asuransi_produk where nama like ('%$q%') order by locate ('$q', nama)";
        $exe = $this->db->query($sql);
        return $exe->result();
    }

    function get_penanggungjawab($q) {
        $sql = "select pd.*, dp.alamat, dp.kelurahan_id, k.nama as kelurahan, kec.nama as kecamatan, kab.nama as kabupaten, pro.nama as provinsi from penduduk pd
            left join dinamis_penduduk dp on (pd.id = dp.penduduk_id)
            left join kelurahan k on (dp.kelurahan_id = k.id)
            left join kecamatan kec on (k.kecamatan_id = kec.id)
            left join kabupaten kab on (kec.kabupaten_id = kab.id)
            left join provinsi pro on (kab.provinsi_id = pro.id)
           inner join (
                select penduduk_id, max(id) as id_max
                from dinamis_penduduk GROUP BY penduduk_id
            ) dpi on (dp.penduduk_id = dpi.penduduk_id and dp.id = dpi.id_max)
            where pd.nama like ('%$q%') order by locate ('$q',pd.nama)";
        $exe = $this->db->query($sql);
        return $exe->result();
    }

    function find_similiar($data) {
        $q = null;
        if ($data['nama'] != '') {
            $q.=" and pd.nama like ('%$data[nama]%')";
        }
        
        if ($data['alamat'] != '') {
            $q.=" and dp.alamat like '%" . $data['alamat'] . "%'";
        }
        if ($data['tgl_lahir'] != '') {
            $q.=" and pd.lahir_tanggal = '" . datetopg($data['tgl_lahir']) . "'";
        }
        if ($data['kelamin'] != '') {
            $q.=" and pd.gender = '$data[kelamin]'";
        }
        

        $sql = "select * from pasien p 
            join penduduk pd on (p.id = pd.id)
            left join dinamis_penduduk dp on (dp.penduduk_id = pd.id)           
            where dp.id in (select max(id) from dinamis_penduduk group by penduduk_id)
            and pd.id is not NULL $q";
        //echo "<pre>".$sql."</pre>";
        $data = $this->db->query($sql);
        return $data->result();
    }

    function get_umur($tgl_lahir) {
        $tglawal = date('Y');  // Format: Tanggal/Bulan/Tahun -> 12 Desember 2010
        $year1 = explode('-', $tgl_lahir);
        $selisih = $tglawal - $year1;
//Tampilkan hasil
        return $selisih;
    }

    function calc_tgl_lahir($umur) {
        $tgl = (date('Y') - $umur) . "-" . date('m-d');
        return $tgl;
    }

    function advanced_search($limit, $start, $data) {
        $umur = $this->calc_tgl_lahir($data['umur']);
        $q = null;
        
        if ($data['no_rm'] !== '') {
            $q.=" and p.no_rm = '".$data['no_rm']."'";
        }
        if ($data['nama'] != '') {
            $q.=" and pd.nama like ('%$data[nama]%')";
        }
        if ($data['kelamin'] != '') {
            $q.=" and pd.gender = '$data[kelamin]'";
        }
        if ($data['umur'] != '') {
            $year_now = date("Y");
            $selisih = $year_now - $data['umur'];
            $new_param = $selisih . "-" . date("m") . "-" . date("d");
            $last_param = ($selisih - 1) . "-" . date("m") . "-" . date("d");
            $q.=" and pd.lahir_tanggal between '$last_param' and '$new_param'";
        }
        if ($data['addr_jln'] != '') {
            $q.=" and dp.alamat like ('%$data[addr_jln]%')";
        }
        if ($data['kelurahan'] != '') {
            $q .= " and dp.kelurahan_id = '".$data['kelurahan']."'";
        }
        $sql = "select * from pasien p 
            join penduduk pd on (p.id = pd.id)
            left join dinamis_penduduk dp on (dp.penduduk_id = pd.id)
            inner join (
                select penduduk_id, max(id) as id_max from dinamis_penduduk
                group by penduduk_id
            ) tm on (dp.penduduk_id = tm.penduduk_id and dp.id = tm.id_max)
            where pd.id is not NULL $q";
        //echo $sql;
        $status = "select (
            select count(*) from pasien p
    join penduduk pd on (p.id = pd.id)
        left join dinamis_penduduk dp on (dp.penduduk_id = pd.id)
            inner join (
                select penduduk_id, max(id) as id_max from  dinamis_penduduk
                group by penduduk_id
            ) tm on (dp.penduduk_id = tm.penduduk_id and dp.id =    tm.id_max) 
            where kunjungan > 1 and pd.id is not NULL $q
            )
         as lama, 
        (select count(*) from pasien p
    join penduduk pd on (p.id = pd.id)
        left join dinamis_penduduk dp on (dp.penduduk_id = pd.id)
            inner join (
                select penduduk_id, max(id) as id_max from  dinamis_penduduk
                group by penduduk_id
            ) tm on (dp.penduduk_id = tm.penduduk_id and dp.id = tm.id_max) 
        where kunjungan <= 1 and pd.id is not NULL $q) as baru ";
        //echo $status;
        $paging = " limit " . $start . "," . $limit . " ";
        $data = $this->db->query($sql . $paging);
        $ret['data'] = $data->result();
        $ret['jumlah'] = $this->db->query($sql)->num_rows;
        $ret['status'] =  $this->db->query($status)->row();
        return $ret;
    }

    function dob() {
        return array('tgl_lahir' => 'Tanggal lahir', 'umur' => 'Umur');
    }

    function jenis_demografi() {
        return array('0' => 'Pilih Jenis Demografi', '1' => 'Jenis Kelamin', '2' => 'Usia', '3' => 'Agama', '4' => 'Pendidikan', '5' => 'Pekerjaan', '6' => 'Status Pernikahan', '7' => 'Golongan Darah');
    }

    function identitas() {
        return array('noktp' => 'No. KTP', 'sim' => 'SIM', 'passport' => 'Passport');
    }

    function usia() {
        return array('tanggal lahir' => 'Tanggal Lahir', 'umur' => 'Umur');
    }

    function agama() {
        return array('' => 'Pilih', 'Islam' => 'Islam', 'Kristen' => 'Kristen', 'Katholik' => 'Katholik', 'Hindu' => 'Hindu', 'Budha' => 'Budha', 'kepercayaan' => 'Kepercayaan', 'tidak beragama' => 'Tidak Beragama');
    }

    function pendidikan() {
        $db = $this->db->get('pendidikan');
        $data[''] = 'Pilih';
        foreach ($db->result() as $row) {
            $data[$row->id] = $row->nama;
        }
        return $data;
    }

    function stat_nikah() {
        return array('' => 'Pilih', 'Lajang' => 'Lajang', 'Menikah' => 'Menikah', 'Duda' => 'Duda', 'Janda' => 'Janda');
    }

    function pekerjaan() {
        $this->db->order_by('nama', 'asc');
        $db = $this->db->get('pekerjaan');
        $data[''] = 'Pilih';
        foreach ($db->result() as $row) {
            $data[$row->id] = $row->nama;
        }
        return $data;
    }

    function kelamin() {
        return array('' => 'Pilih ...', 'L' => 'Laki-laki', 'P' => 'Perempuan');
    }

    function gol_darah() {
        return array('' => 'Pilih ...', 'A' => 'A', 'B' => 'B', 'AB' => 'AB', 'O' => 'O');
    }

    function rentang_usia() {
        return array('0-6', '7-28', '29-364', '365-1824', '1825-5474', '5475-9124', '9125-16424', '16425-23139', '23140');
    }

    function format_usia() {
        return array('0 - 6 Hari',
            '7 - 28 Hari',
            '28 - 364 Hari',
            '1 -4 Tahun',
            '5 -14 Tahun',
            '15 - 24 Tahun',
            '25 - 44 Tahun',
            '45 - 64 Tahun',
            '> 65 Tahun',);
        ;
    }

    function get_penduduk($id) {
        $sql = "select pd.id as penduduk_id, ps.no_rm, pd.nama as nama, pd.gender, kab.nama as tempat_lahir, kab.id as tempat_lahir_id, pd.lahir_tanggal,
            pd.telp, pd.darah_gol, dp.agama,pdi.nama as pendidikan, pdi.id as pendidikan_id , pk.nama as pekerjaan, 
            pk.id as pekerjaan_id , dp.pernikahan, dp.id as dinamis_penduduk_id,
            dp.alamat, dp.identitas_no, kel.nama as kelurahan, kel.id as kelurahan_id, kabb.nama as kabupaten, kec.nama as kecamatan, 
            pro.nama as provinsi, dp.profesi_id
            from penduduk pd
            left join pasien ps on (ps.id = pd.id)
            left join kabupaten kab on (kab.id = pd.lahir_kabupaten_id)
            left join dinamis_penduduk dp on (pd.id = dp.penduduk_id)
            left join pendidikan pdi on (pdi.id = dp.pendidikan_id)
            left join pekerjaan pk on (pk.id = dp.pekerjaan_id)
            left join kelurahan kel on (kel.id = dp.kelurahan_id)
            left join kecamatan kec on (kel.kecamatan_id = kec.id)
            left join kabupaten kabb on (kec.kabupaten_id = kabb.id)
            left join provinsi pro on (kabb.provinsi_id = pro.id)
             inner join (
                select penduduk_id, max(id) as id_max
                from dinamis_penduduk GROUP BY penduduk_id
            ) dpi on (dp.penduduk_id = dpi.penduduk_id and dp.id = dpi.id_max)
            where pd.id= '" . $id . "'";
        //echo $sql;
        $exe = $this->db->query($sql);
        return $exe->row();
    }
    
    function get_antrian($id_antri) {
        $sql = "select * from antrian_kunjungan where id = '$id_antri'";
        return $this->db->query($sql);
    }

    function cek_pendaftaran($no_rm){

        $sql = "select count(*) as jumlah from pendaftaran
                 where pasien = '$no_rm' and waktu_keluar is null ";
        $hasil = $this->db->query($sql)->row();
       // echo $sql;
        $status = false;
        if ($hasil->jumlah > 0) 
            $status = true;
        else
            $status = false;        

        return $status;
    }

    function antrian_fisioterapi_save(){
        date_default_timezone_set('Asia/Jakarta');
        $this->load->model(array('m_pendaftaran', 'm_laboratorium'));
        $this->db->trans_begin();
        $waktu = date("Y-m-d H:i:s");

        if(post_safe('id_penduduk') != ''){
            // ambil dari data penduduk
            $id_dinamis = $this->db->query("select max(id) as id from dinamis_penduduk where penduduk_id = '".post_safe('id_penduduk')."'")->row()->id;
            $id_pdd = post_safe('id_penduduk');


            // Update data penduduk
            $update_pdd = array(
                'lahir_tanggal' => date2mysql(post_safe('tgl_lahir')),
                'gender' => post_safe('kelamin'),
                'darah_gol' => post_safe('gol_darah')
            );

            $this->db->where('id', $id_pdd)->update('penduduk', $update_pdd);

            // update dinamis
             // update data dinamis pjawab penduduk

             $din_pdd_update = array(
                'alamat' => post_safe('alamat'),
                'kelurahan_id' => (post_safe('id_kelurahan') != '')?post_safe('id_kelurahan'):NULL
            );             

            $this->db->where('id', $id_dinamis)->update('dinamis_penduduk', $din_pdd_update);
           
        }else{
            // penduduk baru
            $data_penduduk = array(
                'nama' => post_safe('nama'),
                'lahir_tanggal' => date2mysql(post_safe('tgl_lahir')),
                'gender' => post_safe('kelamin'),
                'darah_gol' => post_safe('gol_darah')
            );
            $this->db->insert('penduduk', $data_penduduk);
            $id_pdd = $this->db->insert_id();

            $data_dinamis = array(
                'penduduk_id' => $id_pdd,
                'alamat' => post_safe('alamat'),
                'kelurahan_id' => (post_safe('id_kelurahan') != '')?post_safe('id_kelurahan'):NULL
            );
            $this->db->insert('dinamis_penduduk', $data_dinamis);
            $id_dinamis = $this->db->insert_id();
        }


        $data_daftar = array(
            'pasien' => NULL,     
            'id_customer' => $id_pdd,
            'tgl_daftar' => date("Y-m-d H:i:s"),
            'tgl_layan' =>  date("Y-m-d"),
            'kd_ptgs_daft' => $this->session->userdata('id_user'),
            'kd_ptgs_confirm' => $this->session->userdata('id_user'),
            'arrive_time' => $waktu,
            'dinamis_penduduk_id' => $id_dinamis
        );
        $this->db->insert('pendaftaran', $data_daftar);
        $id_daftar = $this->db->insert_id();

        $id_pk = $this->m_laboratorium->insert_pelayanan_kunjungan($id_daftar, post_safe('id_jurusan'), 7, post_safe('antrian'));

        $this->antrian_fisioterapi_save_data($id_pdd);
        //insert biaya kunjungan
        $param['no_daftar'] = $id_daftar;
        $param['id_pk'] = $id_pk;
        $param['tarif_id'] = 2; // kunjungan pasien
        $param['id_debet'] = 231;
        $param['id_kredit'] = 99;
        $param['waktu'] = $waktu;
        $param['frekuensi'] = 1;
        $this->m_pendaftaran->insert_biaya($param);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $status = FALSE;
        } else {
            $this->db->trans_commit();
            $status = TRUE;
        }

        $data['status'] = $status;
        $data['id_pelayanan_kunjungan'] = $id_pk;
        return $data;
    }


    function antrian_fisioterapi_save_data($id_pdd){
        if (post_safe('jenis') == 'phone') {
            $konfirm = 0;
            $tanggal = date2mysql(post_safe('tgl_layan'));
        }else{
            $konfirm = 1;
            $tanggal = date("Y-m-d");
        }

        $data = array(
            'tanggal' => $tanggal,
            'id_jurusan_kualifikasi_pendidikan' => post_safe('id_jurusan'),
            'pasien' => 0,
            'nama_calon_pasien' => post_safe('nama'),
            'gender' => post_safe('kelamin'),
            'lahir_tanggal' => date2mysql(post_safe('tgl_lahir')),
            'alamat_jalan_calon_pasien' => post_safe('alamat'),
            'id_kelurahan' => (post_safe('id_kelurahan') != '')?post_safe('id_kelurahan'):NULL,
            'no_antri' => post_safe('antrian'),
            'konfirm'=> $konfirm,
            'telp_no' => post_safe('tlpn'),
            'penduduk_id' => ($id_pdd != '')?$id_pdd:NULL
        );

        $this->db->insert('antrian_kunjungan', $data);
    }

    function search_antrian_fisioterapi($limit, $start, $param){
        $q = '';
        $paging = " limit " . $start . "," . $limit . " ";
        if($param['tanggal'] != ''){
            $q .= " and tanggal = '".$param['tanggal']."' ";
        }

        if($param['layanan'] != ''){
            $q .= " and id_jurusan_kualifikasi_pendidikan = '".$param['layanan']."'";
        }

        if($param['no_antri'] != ''){
            $q .= " and no_antri = '".$param['no_antri']."'";
        }

        $db = "select a.*, kl.nama as kelurahan from antrian_kunjungan a
            left join kelurahan kl on (a.id_kelurahan = kl.id) 
            where pasien = '0' and konfirm = '0' $q ";
       // echo $db.$paging;
        $data = $this->db->query($db . $paging);
        $ret['data'] = $data->result();
        $ret['jumlah'] = $this->db->query($db)->num_rows;
        return $ret;
    }

    function get_antrian_fisioterapi_detail($id){
        $sql = "select a.* , a.id as id_kunjungan, p.*,dp.alamat, dp.pernikahan, dp.agama, dp.identitas_no,
                kel.nama as kelurahan, kec.nama as kecamatan, 
                kab.nama as kabupaten, pro.nama as provinsi, pdi.nama as pendidikan, pk.nama as pekerjaan, 
                kabb.nama as tempat_lahir
                from antrian_kunjungan a
                left join penduduk p on(a.penduduk_id = p.id)
                left join kabupaten kabb on (kabb.id = p.lahir_kabupaten_id)
                left join dinamis_penduduk dp on (p.id = dp.penduduk_id)
                left join pendidikan pdi on (pdi.id = dp.pendidikan_id)
                left join pekerjaan pk on (pk.id = dp.pekerjaan_id)
                left join kelurahan kel on (kel.id = dp.kelurahan_id)
                left join kecamatan kec on (kel.kecamatan_id = kec.id)
                left join kabupaten kab on (kec.kabupaten_id = kab.id)
                left join provinsi pro on (kab.provinsi_id = pro.id)
                inner join (
                    select penduduk_id, max(id) as id_max
                    from dinamis_penduduk GROUP BY penduduk_id
                ) dpi on (dp.penduduk_id = dpi.penduduk_id and dp.id = dpi.id_max)
                where a.id = '$id'";
            //echo $sql;
        return $this->db->query($sql)->row();
    }

    function antrian_fisioterapi_confirm($id){
        date_default_timezone_set('Asia/Jakarta');
        $this->load->model(array('m_pendaftaran', 'm_laboratorium'));
        $this->db->trans_begin();
        $waktu = date("Y-m-d H:i:s");

        $antrian = $this->db->where('id', $id)->get('antrian_kunjungan')->row();
        $id_daftar = null;
        if($antrian != null){
            // insert pendaftaran
            $id_dinamis = $this->db->query("select max(id) as id from dinamis_penduduk where penduduk_id = '".$antrian->penduduk_id."'")->row()->id;
            $data_daftar = array(
                'pasien' => NULL,     
                'id_customer' => $antrian->penduduk_id,      
                'tgl_daftar' => date("Y-m-d H:i:s"),
                'tgl_layan' =>  $antrian->tanggal,
                'kd_ptgs_daft' => $this->session->userdata('id_user'),
                'kd_ptgs_confirm' => $this->session->userdata('id_user'),
                'arrive_time' => date("Y-m-d H:i:s"),
                'dinamis_penduduk_id' => $id_dinamis
            );
            $this->db->insert('pendaftaran', $data_daftar);
            $id_daftar = $this->db->insert_id();

            // insert pelayanan kunjungan
            $id_pk = $this->m_laboratorium->insert_pelayanan_kunjungan($id_daftar, $antrian->id_jurusan_kualifikasi_pendidikan, 7, $antrian->no_antri);

            $data = array(
                'konfirm' => '1' 
            );

            $this->db->where('id', $id)->update('antrian_kunjungan', $data);
             //insert biaya kunjungan
            $param['no_daftar'] = $id_daftar;
            $param['id_pk'] = $id_pk;
            $param['tarif_id'] = 2; // kunjungan pasien
            $param['id_debet'] = 231;
            $param['id_kredit'] = 99;
            $param['waktu'] = $waktu;
            $param['frekuensi'] = 1;
            $this->m_pendaftaran->insert_biaya($param);
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $status = FALSE;
        } else {
            $this->db->trans_commit();
            $status = TRUE;
        }

        $data['status'] = $status;
        $data['id_pelayanan_kunjungan'] = $id_pk;
        return $data;

    }

    function antrian_fisioterapi_penduduk_save($id){
        date_default_timezone_set('Asia/Jakarta');
        $this->db->trans_begin();

        $data_penduduk = array(
            'nama' => post_safe('nama'),
            'lahir_tanggal' => post_safe('lahir_tanggal'),
            'gender' => post_safe('gender'),
            'darah_gol' => post_safe('darah_gol'),
            'telp' => post_safe('telp')
        );
        $this->db->insert('penduduk', $data_penduduk);
        $id_pdd = $this->db->insert_id();

        $data_dinamis = array(
            'tanggal' => date('Y-m-d'),
            'penduduk_id' => $id_pdd,
            'alamat' => post_safe('alamat'),
            'kelurahan_id' => (post_safe('id_kelurahan') != '')?post_safe('id_kelurahan'):NULL,
            'alamat' => post_safe('alamat')
        );
        $this->db->insert('dinamis_penduduk', $data_dinamis);
        $id_dinamis = $this->db->insert_id();

        $antrian = array(
                'penduduk_id' => $id_pdd
            );
        $this->db->where('id', $id)->update('antrian_kunjungan', $antrian);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $status = FALSE;
        } else {
            $this->db->trans_commit();
            //insert biaya kunjungan
            $status = TRUE;
        }

        $data['status'] = $status;
        $data['id_penduduk'] = $id_pdd;
        return $data;
    }
    
    function get_data_dinamis_penduduk($id_pk) {
        $sql = "select pdf.*, ps.no_rm, pdd.nama, dp.alamat, pk.id as id_pelayanan_kunjungan,
            pk.id_jurusan_kualifikasi_pendidikan, pk.waktu,pk.no_antri, kl.nama as kelurahan, kc.nama as kecamatan,
            pdd.gender, pdd.lahir_tanggal, pkj.nama as pekerjaan, jkp.nama as jenis_layanan, pdi.nama as pendidikan, ri.nama as instansi_rujukan
            from pelayanan_kunjungan pk
            join pendaftaran pdf on (pk.id_kunjungan = pdf.no_daftar)
            join pasien ps on (pdf.pasien = ps.no_rm)
            join penduduk pdd on (ps.id = pdd.id)
            join dinamis_penduduk dp on (pdd.id = dp.penduduk_id)
            left join kelurahan kl on (dp.kelurahan_id = kl.id)
            left join kelurahan kc on (kl.kecamatan_id = kc.id)
            left join pekerjaan pkj on (dp.pekerjaan_id = pkj.id)
            left join pendidikan pdi on (dp.pendidikan_id = pdi.id)
            left join relasi_instansi ri on (ri.id = pdf.rujukan_instansi_id)
            left join jurusan_kualifikasi_pendidikan jkp on (pk.id_jurusan_kualifikasi_pendidikan = jkp.id)
            inner join (
                select max(id) as id_max, penduduk_id from dinamis_penduduk group by penduduk_id
            ) dm on (dp.id = dm.id_max and dp.penduduk_id = dm.penduduk_id)
            where pk.id = '$id_pk'";
        return $this->db->query($sql);
    }
}

?>