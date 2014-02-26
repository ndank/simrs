<?php

class M_pendaftaran extends CI_Model {

    function keb_rawat() {
        return array('' => 'Pilih ...', 'Biasa' => 'Biasa', 'Darurat' => 'Darurat', 'Mendesak' => 'Mendesak', 'Segera' => 'Segera');
    }

    function jenis_rawat() {
        return array('' => 'Pilih ...', 'IGD' => 'IGD', 'Poliklinik' => 'Poliklinik');
    }

    function alasan_datang(){
        return  array('Penyakit'=>'Penyakit','Laka Lantas'=>'Laka Lantas','Lain-lain'=>'Lain-lain');
    }

    function jenis_layan() {
        return array('Kuratif' => 'Kuratif', 'Preventif' => 'Preventif', 'Paliatif' => 'Paliatif', 'Rehabilitatif' => 'Rehabilitatif');
    }

    function unit_layan() {
        return array('layanan 1' => 'Layanan 1');
    }

    function krit_layan() {
        return array('Biasa' => 'Biasa', 'Intensif' => 'Intensif', 'Khusus' => 'Khusus');
    }

    function get_layanan_admission($igd = null){
        $sql = "select kp.*  from jurusan_kualifikasi_pendidikan kp
            where kp.admission = 'Ya' order by kp.nama";
        
        $hasil = $this->db->query($sql)->result();
        $data = array();
        $data[''] = 'Pilih...';
        if($igd !== null){
            $data['igd'] = 'IGD';    
        }
        
        foreach ($hasil as $key => $value) {
            $data[$value->id] = $value->nama; 
        }
        return $data;
    }

    function add_mahasiswa($nim, $nama) {
        $data = array('nim' => $nim, 'nama' => $nama);
        $this->db->insert('mahasiswa', $data);
    }


    function create_and_save() {
        
        $waktu_daftar = date('Y-m-d H:i:s');
        $data = array(
            'pasien' => post_safe('no_rm'),
            'tgl_daftar' => $waktu_daftar,
            'tgl_layan' => date2mysql(post_safe('tgl_layan')),
            //'no_antri' => post_safe('no_antri'),
            'keb_rawat' => 'Biasa',
            'jenis_rawat' => 'Poliklinik',
            'jenis_layan' => post_safe('jenis_layan'),
            'krit_layan' => post_safe('krit_layan'),
            'kd_ptgs_daft' => $this->session->userdata('id_user'),
            'kd_ptgs_confirm' => NULL,
            'arrive_time' => NULL,
            'alasan_datang' => post_safe('alasan'),
            'keterangan_kecelakaan' => post_safe('keterangan_kecelakaan'),
            'dinamis_penduduk_id' => post_safe('dinamis'),
            'rujukan_instansi_id' => (post_safe('id_instansi') != '')?post_safe('id_instansi') : NULL,
            'nama_pjwb' => post_safe('pjawab'),
            'telp_pjwb' => post_safe('telppjawab'),
            'alamat_pjwb' => post_safe('alamatpjawab'),
            'kelurahan_id_pjwb' => post_safe('id_kelurahan_pjawab'),
            'nakes_perujuk' => post_safe('nakes')
        );

        $this->db->insert('pendaftaran', $data);
        $no_daftar = $this->db->insert_id();


        // insert pelayanan kunjungan
        $pk = array(
            'waktu' => NULL,
            'id_kepegawaian_dpjp' => (post_safe('id_dokter') != '')?post_safe('id_dokter'):NULL,
            'id_kunjungan' => $no_daftar,
            'id_jurusan_kualifikasi_pendidikan' => post_safe('unit_layan'),
            'no_antri' => post_safe('no_antri'),
            'jenis' => 'Rawat Jalan',
            'jenis_pelayanan' => 'Poliklinik' 
        );
        $this->db->insert('pelayanan_kunjungan', $pk);
        $id_pk = $this->db->insert_id();
        return array('no_daftar'=>$no_daftar ,'id_pk'=> $id_pk, 'waktu' => $waktu_daftar);
    }

    function get_pendaftar($limit = null, $start = null, $param) {

        $db = "select p.*, d.no_rm, pd.nama, dp.alamat, pk.jenis,
            pk.id_jurusan_kualifikasi_pendidikan, pk.waktu,pk.no_antri
            from pendaftaran p 
            join pasien d on (p.pasien = d.no_rm) 
            join pelayanan_kunjungan pk on(p.no_daftar = pk.id_kunjungan)
            join penduduk pd on(d.id = pd.id) 
            left join dinamis_penduduk dp on (dp.penduduk_id = pd.id)
            inner join (
                select max(id) as id_max, penduduk_id from dinamis_penduduk GROUP BY penduduk_id
            ) dm on (dp.id = dm.id_max and dp.penduduk_id = dm.penduduk_id)
            where p.no_daftar IS NOT NULL
            ";
        $q = '';
        if ($param['from'] != '') {
            $q .= " and tgl_layan between '" . date2mysql($param['from']) . "' and '" . date2mysql($param['to']) . "' ";
        }

        if($param['nama'] != ''){
            $q .= " and pd.nama like '%".$param['nama']."%' ";
        }

        if($param['layanan'] == 'igd'){
            $q .= " and p.jenis_rawat = 'IGD' ";
        }else if($param['layanan'] != ''){
            $q .= " and pk.id_jurusan_kualifikasi_pendidikan = '".$param['layanan']."' ";
        }

        if($param['alamat'] != ''){
            $q .= " and dp.alamat like '%".$param['alamat']."%' ";
        }

        $order = "  order by p.tgl_layan desc";
        $paging = " limit " . $start . "," . $limit . " ";
        $data = $this->db->query($db . $q . $order . $paging);
        //echo $db . $q . $order . $paging;
        $ret['data'] = $data->result();
        $ret['jumlah'] = $this->db->query($db . $q . $order)->num_rows;
        return $ret;
    }
    
    function get_pendaftar_pemeriksaan($limit, $start, $param) {

        $db = "select p.*, d.no_rm, pd.nama, dp.alamat, pk.id_kepegawaian_dpjp, pk.p_tensi,
            pk.id_jurusan_kualifikasi_pendidikan, pk.waktu, pk.no_antri, pk.id as id_pk
            from pendaftaran p 
            join pasien d on (p.pasien = d.no_rm) 
            join pelayanan_kunjungan pk on(p.no_daftar = pk.id_kunjungan)
            join penduduk pd on(d.id = pd.id) 
            left join dinamis_penduduk dp on (dp.penduduk_id = pd.id)
            inner join (
                select max(id) as id_max, penduduk_id from dinamis_penduduk GROUP BY penduduk_id
            ) dm on (dp.id = dm.id_max and dp.penduduk_id = dm.penduduk_id)
            where pk.jenis_pelayanan = 'Poliklinik'
            ";
        $q = '';
        if ($param['from'] != '') {
            $q .= " and tgl_layan between '" . date2mysql($param['from']) . "' and '" . date2mysql($param['to']) . "' ";
        }

        if($param['nama'] != ''){
            $q .= " and pd.nama like '%".$param['nama']."%' ";
        }

        if($param['layanan'] == 'igd'){
            $q .= " and p.jenis_rawat = 'IGD' ";
        }else if($param['layanan'] != ''){
            $q .= " and pk.id_jurusan_kualifikasi_pendidikan = '".$param['layanan']."' ";
        }

        if($param['alamat'] != ''){
            $q .= " and dp.alamat like '%".$param['alamat']."%' ";
        }

        $order = "  order by p.tgl_layan desc";
        $paging = " limit " . $start . "," . $limit . " ";
        $data = $this->db->query($db . $q . $order . $paging);
        //echo $db . $q . $order . $paging;
        $ret['data'] = $data->result();
        $ret['jumlah'] = $this->db->query($db . $q . $order)->num_rows;
        return $ret;
    }
    
    function get_pendaftar_pemeriksaan_ranap($limit, $start, $param) {
        $q = '';
        if ($param['from'] != '') {
            $q .= " and tgl_layan between '" . date2mysql($param['from']) . "' and '" . date2mysql($param['to']) . "' ";
        }

        if($param['nama'] != ''){
            $q .= " and pd.nama like '%".$param['nama']."%' ";
        }

        if($param['layanan'] == 'igd'){
            $q .= " and p.jenis_rawat = 'IGD' ";
        }else if($param['layanan'] != ''){
            $q .= " and pk.id_jurusan_kualifikasi_pendidikan = '".$param['layanan']."' ";
        }

        if($param['alamat'] != ''){
            $q .= " and dp.alamat like '%".$param['alamat']."%' ";
        }
        
        $db = "select p.*, d.no_rm, pd.nama, dp.alamat, pk.id_kepegawaian_dpjp, pk.p_tensi,
            pk.id_jurusan_kualifikasi_pendidikan, pk.jenis, CONCAT_WS(' ',u.nama,t.kelas,t.nomor) as ranap, pk.jenis_pelayanan, pk.waktu, pk.no_antri, pk.id as id_pk
            from pendaftaran p 
            join pasien d on (p.pasien = d.no_rm) 
            join pelayanan_kunjungan pk on(p.no_daftar = pk.id_kunjungan)
            join penduduk pd on(d.id = pd.id) 
            left join dinamis_penduduk dp on (dp.penduduk_id = pd.id)
            left join tt t on (pk.no_tt = t.id)
            left join unit u on (t.unit_id = u.id)
            inner join (
                select max(id) as id_max, id_kunjungan from pelayanan_kunjungan GROUP BY id_kunjungan
            ) pm on (pk.id = pm.id_max and pk.id_kunjungan = pm.id_kunjungan)
            
            ";
        $order = "  order by p.tgl_layan desc";
        $paging = " limit " . $start . "," . $limit . " ";
        $data = $this->db->query($db . $q . $order . $paging);
        //echo "<pre>".$db . $q . $order . $paging."</pre>";
        $ret['data'] = $data->result();
        $ret['jumlah'] = $this->db->query($db . $q . $order)->num_rows;
        return $ret;
    }

    function get($limit, $start, $param) {
        // untuk data hari ini dan hari berikutnya
        date_default_timezone_set('Asia/Jakarta');
        $q = null;
         if ($param['tanggal'] != null) {
            $q.=" and p.tanggal = '" . $param['tanggal'] . "'";
        }

        if ($param['nama'] != null) {
            $q.=" and p.nama_calon_pasien like '%" . $param['nama'] . "%'";
        }

        if ($param['alamat'] != null) {
            $q.=" and p.alamat_jalan_calon_pasien like '%" . $param['alamat'] . "%'";
        }

        if ($param['id_kelurahan'] != null) {
            $q.=" and k.id = '" . $param['id_kelurahan'] . "'";
        }

        if ($param['layanan'] != null) {
            $q.=" and p.id_jurusan_kualifikasi_pendidikan= '" . $param['layanan'] . "'";
        }
        if ($param['no_antri'] != null) {
            $q.=" and  p.no_antri = " . $param['no_antri'] . "";
        }
        if ($param['no_rm'] != null) {
            $q.=" and p.no_rm = '" . $param['no_rm'] . "'";
        }
        $db = "select p.*,p.nama_calon_pasien as nama_pasien, p.id as no_daftar, p.tanggal as tgl_layan, 
            u.nama as nama_layanan, p.tanggal, p.no_antri, k.nama as kelurahan
            from antrian_kunjungan p 
            join jurusan_kualifikasi_pendidikan u on(u.id = p.id_jurusan_kualifikasi_pendidikan)
            left join kelurahan k on(p.id_kelurahan = k.id)
            where p.konfirm = '0' and p.pasien = '1' and p.tanggal >= '" . date('Y-m-d') . "' $q";
        //echo $db;
        $paging = " limit " . $start . "," . $limit . " ";
        $data = $this->db->query($db . $paging);
        $ret['data'] = $data->result();
        $ret['jumlah'] = $this->db->query($db)->num_rows;
        return $ret;
    }

    function get_unit($param) {


        $db = "select * from pendaftaran p 
            join pasien d on (p.pasien = d.no_rm) 
            join layanan u on(u.id = p.layanan) where
            p.layanan = " . $param['layanan'] . "  and p.tgl_layan = '" . date('Y-m-d') . "' ";

        //echo $db;
        $data = $this->db->query($db);
        return $data->result();
    }

    function get_by_no_daftar($no_daftar) {
        $db = "select d.*,p.*,pd.* , ptd.nama as petugas_daftar,
            pkj.nama as pekerjaan, dp.agama, dp.alamat, k.nama as kelurahan, 
            kc.nama as kecamatan, kb.nama as kabupaten, pr.nama as provinsi, 
            ri.nama as rs_perujuk, ptc.nama as petugas_confirm,
            ri2.nama as relasi_rujuk
            from pendaftaran p 
            join pasien d on (p.pasien = d.no_rm) 
            join penduduk pd on (pd.id = d.id)
            left join penduduk ptd on (ptd.id = p.kd_ptgs_daft)
            left join penduduk ptc on (ptc.id = p.kd_ptgs_confirm)
            join dinamis_penduduk dp on (dp.penduduk_id = pd.id)
            left join pekerjaan pkj on (pkj.id = dp.pekerjaan_id)
            left join kelurahan k on (k.id = dp.kelurahan_id)
            left join kecamatan kc on (kc.id = k.kecamatan_id)
            left join kabupaten kb on (kb.id = kc.kabupaten_id)
            left join provinsi pr on (pr.id = kb.provinsi_id)
            left join relasi_instansi ri on (p.rujukan_instansi_id = ri.id)
            left join relasi_instansi ri2 on (p.rujuk_instansi_id = ri2.id)
            where p.no_daftar = '" . $no_daftar . "' ";

        $data = $this->db->query($db);
        //echo "<pre>".$db."</pre>";
        return $data->row();
    }

    // diakses di surat rujukan
    function get_wilayah_relasi_instansi($id_instansi){
        $sql = "select kl.nama as kelurahan , kc.nama as kecamatan , kb.nama as kabupaten
                from relasi_instansi rl
                left join kelurahan kl on (rl.kelurahan_id = kl.id)
                left join kecamatan kc on (kl.kecamatan_id = kc.id)
                left join kabupaten kb on (kc.kabupaten_id = kb.id)
                where rl.id = '$id_instansi'";
        return $this->db->query($sql)->row();
    }

    function get_penduduk_by_no_daftar($no_daftar){
        $sql = "select p.*, pdd.nama from pendaftaran p
                join penduduk pdd on(p.id_customer = pdd.id)
                where p.no_daftar = '$no_daftar'";

        $data = $this->db->query($sql);
        return $data->row();
    }

    function antrian_get_data($id){
        $sql = "select a.*, l.nama as nama_layanan, kp.id as id_kepegawaian,
                p.nama as nama_pegawai
                from antrian_kunjungan a
                join jurusan_kualifikasi_pendidikan l on (a.id_jurusan_kualifikasi_pendidikan = l.id)
                left join kepegawaian kp on (kp.id = a.id_kepegawaian_dpjp)
                left join penduduk p on (p.id = kp.penduduk_id)
                where a.id = $id";
        $db = $this->db->query($sql);
        return $db->row();
    }

    function get_biaya_kartu($no_daftar) {
        $db = "select sum(j.nominal) as total from jasa_penjualan_detail j 
                join pendaftaran p on(p.no_daftar = j.no_daftar) 
                join tarif t on (t.id = j.tarif_id)
                where p.no_daftar = '" . $no_daftar . "' and j.tarif_id = '1'";
       
        $sql = $this->db->query($db);
        return $sql->row()->total;
    }

    function get_biaya_kunjungan($no_daftar) {
        $db = "select sum(t.nominal) as total from  jasa_penjualan_detail j 
                join pendaftaran p on(p.no_daftar = j.no_daftar) 
                join tarif t on (t.id = j.tarif_id)
            where p.no_daftar = '" . $no_daftar . "' and j.tarif_id ='2'";
        $sql = $this->db->query($db);
        return $sql->row()->total;
    }

    function set_arrive_time($no_daftar) {
        $data = array(
            'arrive_time' => date('Y-m-d H:i:s'),
            'kd_ptgs_confirm' => $this->session->userdata('id_user')
        );
        $this->db->where('no_daftar', $no_daftar);
        $this->db->update('pendaftaran', $data);
    }

    function insert_biaya($data) {
        /*
         * Tarif_id
         * 2 = kunjungan
         * 1 =  kartu
         */

        if ($data['tarif_id'] == '1') {
            # cetak kartu
            $wkt = $this->db->query("select tgl_daftar from pendaftaran where no_daftar = '".$data['no_daftar']."' ")->row()->tgl_daftar;
        }else{
            $wkt = $data['waktu'];
        }

        $tabel_tarif = $this->db->query("select * from tarif where id = '".$data['tarif_id']."' ")->row();


        $jasa = array(
                'waktu' => $wkt,
                'id_pelayanan_kunjungan' => $data['id_pk'],
                'id_kepegawaian_nakes' => isset($data['nakes'])?$data['nakes']:NULL,
                'tarif_id' => $data['tarif_id'],
                'jasa_sarana'=> $tabel_tarif->jasa_sarana,
                'jasa_nakes' => $tabel_tarif->jasa_nakes,
                'jasa_tindakan_rs' => $tabel_tarif->jasa_tindakan_rs,
                'bhp' => $tabel_tarif->bhp,
                'biaya_administrasi' => $tabel_tarif->biaya_administrasi,
                'total' => $tabel_tarif->total,
                'persentase_profit' => $tabel_tarif->persentase_profit,
                'nominal' => $tabel_tarif->nominal,
                'frekuensi' => $data['frekuensi']
        );

        $this->db->insert('jasa_penjualan_detail', $jasa);
        $id_jasa = $this->db->insert_id();
      
        /*if($data['frekuensi'] !== null){
            $array = array(
                'waktu' => $wkt,
                'id_transaksi' => $id_jasa,
                'jenis_transaksi' => 'Penjualan Jasa',
                'id_sub_sub_sub_sub_rekening' => $data['id_debet'],
                'debet' => $tabel_tarif->nominal,
                'kredit' => '0'
            );
            $this->db->insert('jurnal', $array);
            $arrays = array(
                'waktu' => $wkt,
                'id_transaksi' => $id_jasa,
                'jenis_transaksi' => 'Penjualan Jasa',
                'id_sub_sub_sub_sub_rekening' => $data['id_kredit'],
                'debet' => '0',
                'kredit' => $tabel_tarif->nominal
            );
            $this->db->insert('jurnal', $arrays);
        }*/
        
    }

    function load_data_instansi_relasi($q) {
        $w = "and j.nama like '%Rumah Sakit%' or j.nama like '%klinik%' or j.nama like '%Puskesmas%' or j.nama like '%Praktek Dokter%'  ";
        $sql = "select i.*, j.nama as jenis from relasi_instansi i
        join relasi_instansi_jenis j on (i.relasi_instansi_jenis_id = j.id)
        where i.nama like ('%$q%') $w order by locate('$q', i.nama)";
        return $this->db->query($sql);
    }

    function load_data_penduduk_profesi($q) {
        $sort = " and pf.jenis = 'nakes'";

        $sql = "select p.nama, p.id as id_penduduk, dp.*,pf.nama as profesi, kl.nama as kelurahan from penduduk p
        join dinamis_penduduk dp on (p.id = dp.penduduk_id)
        left join kelurahan kl on (kl.id = dp.kelurahan_id)
        left join profesi pf on (pf.id = dp.profesi_id)
        where p.nama like ('%$q%') $sort and dp.id in (select max(id) from dinamis_penduduk group by penduduk_id) order by locate('$q', p.nama)";

        return $this->db->query($sql);
    }

    function cek_pelayanan_rawat_inap($no_daftar){
        $sql = "select count(*) as jumlah from inap_rawat_kunjungan ir
                join pelayanan_kunjungan pk on (ir.id_pelayanan_kunjungan = pk.id)
                join pendaftaran pd on (pd.no_daftar = pk.id_kunjungan)
                where pd.no_daftar = '$no_daftar' and ir.keluar_waktu is null";
        $numb = $this->db->query($sql)->row()->jumlah;
        $inap = true;

        if ($numb >= 1) {
            //masi dalam pelayanan rawat inap
            $inap = true;
        }else{
            //sudah discharge pelayanan rawat inap
            $inap = false;
        }
        return $inap;
    }

    function cek_pelunasan_pembayaran($no_daftar){
        // cek apakah sudah ada pembayaran
        $bayar = false;
        $pembayaran = $this->db->where('no_daftar', $no_daftar)->get('kunjungan_billing_pembayaran')->num_rows();
        if ($pembayaran < 1) {
            $bayar = false;
        }else{
            // cek apakah pembayaran sudah lunas
            $sql = "select * from kunjungan_billing_pembayaran kb
                    where no_daftar = '$no_daftar' and sisa between '0' and '100'";

            $pembayaran = $this->db->query($sql)->num_rows();
            //echo $sql." ".$pembayaran."<br/>";
            if ($pembayaran > 0) {
                $bayar = true;
            }
            
        }

        return $bayar;
    }
    function edit_tindak_lanjut($no_daftar){
        $update = array(
            'waktu_keluar' => datetime2mysql(post_safe('waktu_keluar')), 
            'kondisi_keluar' => post_safe('kondisi_keluar'), 
            'menolak_dirawat' => post_safe('menolak'), 
            'rujuk_instansi_id' => (post_safe('id_instansi_rujuk') != '') ? post_safe('id_instansi_rujuk') : NULL,
            'diterima_kembali' => post_safe('diterima')
        );

        $this->db->where('no_daftar',$no_daftar);
        $this->db->update('pendaftaran', $update);
    }
    
    function igd_save() {
        date_default_timezone_set('Asia/Jakarta');
        $this->db->trans_begin();
        $waktu_daftar = date('Y-m-d H:i:s');

        if (post_safe('norm') != '') {
            $id_pasien = post_safe('norm');
            $id_dinamis = $this->db->query("select max(id) as id from dinamis_penduduk where penduduk_id = '".post_safe('id_penduduk')."'")->row()->id;
            $data_daftar = array(
                'pasien' => $id_pasien,
                'tgl_daftar' => $waktu_daftar,
                'tgl_layan' =>  date("Y-m-d"),
                'keb_rawat' => post_safe('keb_rawat'),
                'jenis_rawat' => 'IGD',
                'jenis_layan' =>  post_safe('jenis_layan'),
                'krit_layan' => post_safe('krit_layan'),
                'kd_ptgs_daft' => $this->session->userdata('id_user'),
                'kd_ptgs_confirm' => $this->session->userdata('id_user'),
                'arrive_time' => date("Y-m-d H:i:s"),
                'alasan_datang' => post_safe('alasan'),
                'keterangan_kecelakaan' => post_safe('keterangan_kecelakaan'),
                'dinamis_penduduk_id' => $id_dinamis,
                'doa' => post_safe('doa'),
                'rujukan_instansi_id' => (post_safe('id_instansi') != '') ? post_safe('id_instansi') : NULL,
                'nama_pjwb' => post_safe('pjawab'),
                'telp_pjwb' => post_safe('telppjawab'),
                'alamat_pjwb' => post_safe('alamatpjawab'),
                'kelurahan_id_pjwb' => post_safe('id_kelurahan_pjawab'),
                'nakes_perujuk' => post_safe('nakes')

            );
            $this->db->insert('pendaftaran', $data_daftar);
            $id_daftar = $this->db->insert_id();
        } else {
            $keterangan = post_safe('ket');
            if(post_safe('id_penduduk') != ''){
                $id_penduduk = post_safe('id_penduduk');
                $id_dinamis = $this->db->query("select id from dinamis_penduduk where penduduk_id = '".post_safe('id_penduduk')."'")->row()->id;
                
                // Update data penduduk
                $update_pdd = array(
                    'keterangan' => $keterangan,
                    'lahir_tanggal' => date2mysql(post_safe('tgl_lahir')),
                    'gender' => post_safe('gender'),
                    'darah_gol' => post_safe('gol_darah'),
                    'telp' => post_safe('telp')
                );

                $this->db->where('id', $id_penduduk)->update('penduduk', $update_pdd);

                // update dinamis
                 // update data dinamis pjawab penduduk

             $din_pdd_update = array(
                'tanggal' => date('Y-m-d'),
                'alamat' => post_safe('alamat'),
                'kelurahan_id' => (post_safe('id_kelurahan') != '')?post_safe('id_kelurahan'):NULL
            );             

            $this->db->where('id', $id_dinamis)->update('dinamis_penduduk', $din_pdd_update);
            }else{
                $data_penduduk = array(
                    'keterangan' => $keterangan,
                    'nama' => post_safe('nama'),
                    'lahir_tanggal' => date2mysql(post_safe('tgl_lahir')),
                    'gender' => post_safe('gender'),
                    'darah_gol' => post_safe('gol_darah'),
                    'telp' => post_safe('telp')
                );
                $this->db->insert('penduduk', $data_penduduk);
                $id_penduduk = $this->db->insert_id();

                $data_dinamis = array(
                    'tanggal' => date('Y-m-d'),
                    'penduduk_id' => $id_penduduk,
                    'alamat' => post_safe('alamat'),
                    'kelurahan_id' => (post_safe('id_kelurahan') != '')?post_safe('id_kelurahan'):NULL
                );
                $this->db->insert('dinamis_penduduk', $data_dinamis);
                $id_dinamis = $this->db->insert_id();
            }
            
            $data_pasien = array(
                'id' => $id_penduduk,
                'registrasi_waktu' => $waktu_daftar,
                'kunjungan' => '0',
                'is_cetak_kartu' => '0'
            );
            $this->db->insert('pasien', $data_pasien);
            $id_pasien = $this->db->insert_id();
            $data_daftar = array(
                'pasien' => $id_pasien,
                'tgl_daftar' => $waktu_daftar,
                'tgl_layan' =>  date("Y-m-d"),
                'keb_rawat' => post_safe('keb_rawat'),
                'jenis_rawat' => 'IGD',
                'jenis_layan' =>  post_safe('jenis_layan'),
                'krit_layan' => post_safe('krit_layan'),
                'kd_ptgs_daft' => $this->session->userdata('id_user'),
                'kd_ptgs_confirm' => $this->session->userdata('id_user'),
                'arrive_time' => date("Y-m-d H:i:s"),
                'alasan_datang' => post_safe('alasan'),
                'keterangan_kecelakaan' => post_safe('keterangan_kecelakaan'),
                'dinamis_penduduk_id' => $id_dinamis,
                'doa' => post_safe('doa'),
                'rujukan_instansi_id' => (post_safe('id_instansi') != '') ? post_safe('id_instansi') : NULL,
                'nama_pjwb' => post_safe('pjawab'),
                'telp_pjwb' => post_safe('telppjawab'),
                'alamat_pjwb' => post_safe('alamatpjawab'),
                'kelurahan_id_pjwb' => post_safe('id_kelurahan_pjawab'),
                'nakes_perujuk' => post_safe('nakes')
            );
            $this->db->insert('pendaftaran', $data_daftar);
            $id_daftar = $this->db->insert_id();
        }

        $pk = array(
            'waktu' => date("Y-m-d H:i:s"),
            'id_kunjungan' => $id_daftar,
            'jenis' => 'Rawat Jalan',
            'jenis_pelayanan' => 'IGD'
        );
        $this->db->insert('pelayanan_kunjungan', $pk);
        $id_pk = $this->db->insert_id();

        $param['no_daftar'] = $id_daftar;
        $param['id_pk'] = $id_pk;
        $param['tarif_id'] = 2; // kunjungan pasien
        $param['id_debet'] = 231;
        $param['id_kredit'] = 104;
        $param['waktu'] = $waktu_daftar;
        $param['frekuensi'] = 1;
        $this->insert_biaya($param);
        
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $status = FALSE;
        } else {
            $this->db->trans_commit();
            $status = TRUE;
        }

        $data['status'] = $status;
        $data['no_daftar'] = $id_daftar;
        $data['id_pk'] = $id_pk;
        $data['no_rm'] = $id_pasien;
        return $data;
    }

    function kunjungan_save(){
        date_default_timezone_set('Asia/Jakarta');
        $this->db->trans_begin();
        $waktu_daftar = date('Y-m-d H:i:s');

        if (post_safe('norm') != '') {
            $id_pasien = post_safe('norm');
            $id_dinamis = $this->db->query("select max(id) as id from dinamis_penduduk where penduduk_id = '".post_safe('id_penduduk')."'")->row()->id;
            $data_daftar = array(
                'pasien' => $id_pasien,               
                'tgl_daftar' => $waktu_daftar,
                'tgl_layan' =>  date("Y-m-d"),
                'keb_rawat' => 'Biasa',
                'jenis_rawat' => 'Poliklinik',
                'jenis_layan' =>  post_safe('jenis_layan'),
                'krit_layan' => post_safe('krit_layan'),
                'kd_ptgs_daft' => $this->session->userdata('id_user'),
                'kd_ptgs_confirm' => $this->session->userdata('id_user'),
                'arrive_time' => NULL,
                'alasan_datang' => post_safe('alasan'),
                'keterangan_kecelakaan' => post_safe('keterangan_kecelakaan'),
                'dinamis_penduduk_id' => $id_dinamis,
                'rujukan_instansi_id' => (post_safe('id_instansi') != '') ? post_safe('id_instansi') : NULL,
                'nama_pjwb' => post_safe('pjawab'),
                'telp_pjwb' => post_safe('telppjawab'),
                'alamat_pjwb' => post_safe('alamatpjawab'),
                'kelurahan_id_pjwb' => post_safe('id_kelurahan_pjawab'),
                'nakes_perujuk' => post_safe('nakes')
            );
            $this->db->insert('pendaftaran', $data_daftar);
            $id_daftar = $this->db->insert_id();
        } else {
            $keterangan = post_safe('ket');
            if (post_safe('tgl_lahir') !== '') {
                $tgl_lahir = date2mysql(post_safe('tgl_lahir'));
            }
            if (post_safe('tgl_lahir') === '') {
                $tgl_lahir = (date("Y")-post_safe('umur')).'-'.date("m").'-'.date("d");
            }
            if(post_safe('id_penduduk') != ''){
                $id_penduduk = post_safe('id_penduduk');
                $id_dinamis = $this->db->query("select max(id) as id from dinamis_penduduk where penduduk_id = '".post_safe('id_penduduk')."'")->row()->id;
                
                // Update data penduduk
                $update_pdd = array(
                    'keterangan' => $keterangan,
                    'lahir_tanggal' => $tgl_lahir,
                    'gender' => post_safe('gender'),
                    'lahir_kabupaten_id' => (post_safe('hd_lahir_tempat') == "") ? NULL : post_safe('hd_lahir_tempat'),   
                    'darah_gol' => post_safe('gol_darah'),
                    'telp' => post_safe('telp')
                );

                $this->db->where('id', $id_penduduk)->update('penduduk', $update_pdd);

                // update dinamis
                 // update data dinamis pjawab penduduk

             $din_pdd_update = array(
                'alamat' => post_safe('alamat'),
                'kelurahan_id' => (post_safe('id_kelurahan') != '')?post_safe('id_kelurahan'):NULL,
                'pernikahan' => post_safe('pernikahan'),
                'agama' => (post_safe('agama') != '') ? post_safe('agama') : NULL,
                'pendidikan_id' => (post_safe('pendidikan') != '') ? post_safe('pendidikan') : NULL,
                'profesi_id' => (post_safe('profesi') != '') ? post_safe('profesi') : NULL,
                'pekerjaan_id' => (post_safe('pekerjaan') != '') ? post_safe('pekerjaan') : NULL
            );             

            $this->db->where('id', $id_dinamis)->update('dinamis_penduduk', $din_pdd_update);
            }else{
                $data_penduduk = array(
                    'keterangan' => post_safe('ket'),
                    'nama' => post_safe('nama'),
                    'lahir_tanggal' => $tgl_lahir,
                    'gender' => post_safe('gender'),
                    'darah_gol' => post_safe('gol_darah'),
                    'lahir_kabupaten_id' => (post_safe('hd_lahir_tempat') == "") ? NULL : post_safe('hd_lahir_tempat'),
                    'telp' => post_safe('telp')
                );
                $this->db->insert('penduduk', $data_penduduk);
                $id_penduduk = $this->db->insert_id();

                $data_dinamis = array(
                    'tanggal' => date('Y-m-d'),
                    'penduduk_id' => $id_penduduk,
                    'alamat' => post_safe('alamat'),
                    'kelurahan_id' => (post_safe('id_kelurahan') != '')?post_safe('id_kelurahan'):NULL,
                    'pernikahan' => post_safe('pernikahan'),
                    'agama' => (post_safe('agama') != '') ? post_safe('agama') : NULL,
                    'pendidikan_id' => (post_safe('pendidikan') != '') ? post_safe('pendidikan') : NULL,
                    'profesi_id' => (post_safe('profesi') != '') ? post_safe('profesi') : NULL,
                    'pekerjaan_id' => (post_safe('pekerjaan') != '') ? post_safe('pekerjaan') : NULL
                );
                $this->db->insert('dinamis_penduduk', $data_dinamis);
                $id_dinamis = $this->db->insert_id();
            }



            $data_pasien = array(
                'id' => $id_penduduk,
                'registrasi_waktu' => $waktu_daftar,
                'kunjungan' => '0',
                'is_cetak_kartu' => '0'
            );
            $this->db->insert('pasien', $data_pasien);
            $id_pasien = $this->db->insert_id();
            $data_daftar = array(
                'pasien' => $id_pasien,
                'tgl_daftar' => $waktu_daftar,
                'tgl_layan' =>  date("Y-m-d"),
                'keb_rawat' => 'Biasa',
                'jenis_rawat' => 'Poliklinik',
                'jenis_layan' =>  post_safe('jenis_layan'),
                'krit_layan' => post_safe('krit_layan'),
                'kd_ptgs_daft' => $this->session->userdata('id_user'),
                'kd_ptgs_confirm' => $this->session->userdata('id_user'),
                'arrive_time' => NULL,
                'alasan_datang' => post_safe('alasan'),
                'keterangan_kecelakaan' => post_safe('keterangan_kecelakaan'),
                'dinamis_penduduk_id' => $id_dinamis,
                'rujukan_instansi_id' => (post_safe('id_instansi') != '') ? post_safe('id_instansi') : NULL,
                'nama_pjwb' => post_safe('pjawab'),
                'telp_pjwb' => post_safe('telppjawab'),
                'alamat_pjwb' => post_safe('alamatpjawab'),
                'kelurahan_id_pjwb' => post_safe('id_kelurahan_pjawab'),
                'nakes_perujuk' => post_safe('nakes')
            );
            $this->db->insert('pendaftaran', $data_daftar);
            $id_daftar = $this->db->insert_id();
        }

        $pk = array(
            'waktu' => date("Y-m-d H:i:s"),
            'id_kepegawaian_dpjp' => (post_safe('id_dokter') != '')?post_safe('id_dokter'):NULL,
            'id_kunjungan' => $id_daftar,
            'id_jurusan_kualifikasi_pendidikan' => post_safe('id_layanan'),
            'no_antri' => post_safe('antrian'),
            'jenis' => 'Rawat Jalan',
            'jenis_pelayanan' => 'Poliklinik'
        );
        $this->db->insert('pelayanan_kunjungan', $pk);
        $id_pk = $this->db->insert_id();

        //insert biaya kunjungan
        $param['id_pk'] = $id_pk;
        $param['no_daftar'] = $id_daftar;
        $param['tarif_id'] = 2; // kunjungan pasien
        $param['id_debet'] = 231;
        $param['id_kredit'] = 99;
        $param['waktu'] = $waktu_daftar;
        $param['frekuensi'] = 1;
        $this->insert_biaya($param);
        $this->antrian_save_data();

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $status = FALSE;
        } else {
            $this->db->trans_commit();
            $status = TRUE;
        }

        $data['status'] = $status;
        $data['no_daftar'] = $id_daftar;
        $data['no_rm'] = $id_pasien;
        return $data;

    }

    function antrian_save_data(){
        $data = array(
            'tanggal' => date("Y-m-d"),
            'id_jurusan_kualifikasi_pendidikan' => post_safe('id_layanan'),
            'id_kepegawaian_dpjp' => (post_safe('id_dokter')=='')?NULL:post_safe('id_dokter'),
            'no_rm' => (post_safe('norm')=='')?NULL:post_safe('norm'),
            'nama_calon_pasien' => post_safe('nama'),
            'gender' => post_safe('gender'),
            'lahir_tanggal' => date2mysql(post_safe('tgl_lahir')),
            'alamat_jalan_calon_pasien' => post_safe('alamat'),
            'id_kelurahan' => (post_safe('id_kelurahan') != '')?post_safe('id_kelurahan'):NULL,
            'no_antri' => post_safe('antrian'),
            'konfirm'=> 1,
            'penduduk_id' => (post_safe('id_penduduk')!="")?post_safe('id_penduduk'):NULL
        );

        $this->db->insert('antrian_kunjungan', $data);
    }

    function delete_kunjungan($id){
        $this->db->trans_begin();
        $this->db->where('no_daftar', $id)->delete('inap_rawat_kunjungan');
        $this->db->where('no_daftar', $id)->delete('jasa_penjualan_detail');
        $this->db->where('id_kunjungan', $id)->delete('pelayanan_kunjungan');
        $this->db->where('no_daftar', $id)->delete('pendaftaran');

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $status = FALSE;
        } else {
            $this->db->trans_commit();
            
            $status = TRUE;
        }
        return $status;
    }

    function get_riwayat_kunjungan($no_rm, $order){
        $sql = "select * , null as pelayanan_kunjungan from pendaftaran p 
            where p.pasien = '$no_rm' order by p.no_daftar ".$order;
        return $this->db->query($sql)->result();
    }

}

?>