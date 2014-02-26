<?php

class M_pelayanan extends CI_Model {
    
    function gol_sebab_sakit_load_data($q) {
       $sql = "select * from golongan_sebab_sakit
        where nama like ('%$q%') order by locate('$q', nama)";
        return $this->db->query($sql);
    }

    function get_by_no_rm($no_rm) {
        $sql = "select  p.id, p.no_rm, p.is_cetak_kartu ,pd.nama as nama, pd.gender
            from pasien p 
            join penduduk pd on (p.id = pd.id)
            where p.no_rm = '" . $no_rm . "'";

        $data = $this->db->query($sql);
        return $data->row();
    }


    function gol_sebab_sakit_load_data2($q) {
       $sql = "select * from golongan_sebab_sakit
        where no_daftar_terperinci like ('%$q%') order by locate('$q', no_daftar_terperinci)";
        return $this->db->query($sql);
    }

    function pelayanan_kunjungan_save($tipe){
        $this->db->trans_begin();

        $this->load->model('m_rawatinap');
        $cek = $this->db->query("select count(*) as jumlah from pelayanan_kunjungan where id_kunjungan = '".post_safe('id_kunjungan')."'")->row();
        
       
        if (post_safe('id_pelayanan_kunjungan') == '') {
            $data = array(
                'waktu' => (post_safe('waktu') != '')?datetime2mysql(post_safe('waktu')):date("Y-m-d H:i:s"),
                'id_kunjungan' => post_safe('id_kunjungan'),
                'jenis_pelayanan' => 'IGD',
                'no_polis' => post_safe('no_polis'),
                'id_kepegawaian_dpjp' => post_safe('id_dpjp'),
                'id_produk_asuransi' => (post_safe('id_asuransi') != '')?post_safe('id_asuransi'):NULL,
                'anamnesis' => post_safe('anamnesis'),
                'pemeriksaan_umum' => post_safe('pemeriksaan')
            );
            if($tipe == "igd"){
                $id_unit = $this->db->query("select id from unit where nama = 'IGD'")->row()->id;

                $data['jenis'] = "Rawat Jalan";
                //$data['jenis_pelayanan'] = "IGD";
                $data['id_unit'] = $id_unit;
                $data['p_tensi'] = post_safe('tensi');
                $data['p_nadi'] = post_safe('nadi');
                $data['p_suhu'] = post_safe('suhu');
                $data['p_nafas'] = post_safe('nafas');
                $data['p_bb'] = post_safe('bb');
                $data['rencana_tindak_lanjut'] = post_safe('lanjut');
            }else if($tipe == "poli"){
                $id_unit = $this->db->query("select id from unit where nama = 'Poliklinik'")->row()->id;

                $data['jenis'] = "Rawat Jalan";
                //$data['jenis_pelayanan'] = "Poliklinik";
                $data['id_unit'] = $id_unit;
                $data['id_jurusan_kualifikasi_pendidikan'] = (post_safe('id_jurusan')!= '')?post_safe('id_jurusan'):NULL;

                $data['p_tensi'] = post_safe('tensi');
                $data['p_nadi'] = post_safe('nadi');
                $data['p_suhu'] = post_safe('suhu');
                $data['p_nafas'] = post_safe('nafas');
                $data['p_bb'] = post_safe('bb');

                $data['pemeriksaan_umum'] = post_safe('pemeriksaan');
            }else if($tipe == "inap"){
                $data['jenis'] = "Rawat Inap";
                $data['jenis_pelayanan'] = "Rawat Inap";
                $data['id_jurusan_kualifikasi_pendidikan'] = post_safe('id_layanan');
                $data['pemeriksaan_umum'] = post_safe('pemeriksaan');
                $data['id_unit'] = (post_safe('id_bangsal') != '')?post_safe('id_bangsal'):NULL;
                $data['kelas'] = post_safe('kelas');
                $data['no_tt'] = post_safe('id_tt');
            }
          
            $this->db->insert('pelayanan_kunjungan', $data);
            $insert_id =  $this->db->insert_id();

            // insert billing rawat inap
            if($tipe == "inap"){
                    $data = array(
                        'id_pk' => $insert_id,
                        'id_tarif' => post_safe('id_tarif'),
                        'no_daftar' => post_safe('id_kunjungan'),
                        'in_time' => $data['waktu']
                    );
                    $this->m_rawatinap->save_bed_data($data);

                    //update status bed = terisi
                    $upt = array('status'=>'Terisi');
                    $this->db->where('id', post_safe('id_tt'))->update('tt', $upt);
                    $return =  $insert_id;
                
            }else{
                $return = $insert_id;
            }            

            
        } else {
            $data = array(
                    'waktu' => (post_safe('waktu') != '')?datetime2mysql(post_safe('waktu')):NULL,
                    'id_kunjungan' => post_safe('id_kunjungan'),
                    'jenis_pelayanan' => 'IGD',
                    'no_polis' => post_safe('no_polis'),
                    'id_kepegawaian_dpjp' => post_safe('id_dpjp'),
                    'id_produk_asuransi' => (post_safe('id_asuransi') != '')?post_safe('id_asuransi'):NULL,
                    'anamnesis' => post_safe('anamnesis'),
                    'pemeriksaan_umum' => post_safe('pemeriksaan')
            );
             if($tipe == "igd"){
                $data['jenis'] = "Rawat Jalan";
                $data['jenis_pelayanan'] = "IGD";
                $data['id_unit'] = $this->session->userdata('id_unit');
                $data['p_tensi'] = post_safe('tensi');
                $data['p_nadi'] = post_safe('nadi');
                $data['p_suhu'] = post_safe('suhu');
                $data['p_nafas'] = post_safe('nafas');
                $data['p_bb'] = post_safe('bb');
                $data['rencana_tindak_lanjut'] = post_safe('lanjut');
            }else if($tipe == "poli"){
                $data['jenis'] = "Rawat Jalan";
                $data['jenis_pelayanan'] = "Poliklinik";
                //$data['id_jurusan_kualifikasi_pendidikan'] = (post_safe('id_jurusan')!= '')?post_safe('id_jurusan'):NULL;
                $data['id_unit'] = $this->session->userdata('id_unit');

                $data['p_tensi'] = post_safe('tensi');
                $data['p_nadi'] = post_safe('nadi');
                $data['p_suhu'] = post_safe('suhu');
                $data['p_nafas'] = post_safe('nafas');
                $data['p_bb'] = post_safe('bb');
                
                $data['pemeriksaan_umum'] = post_safe('pemeriksaan');
            }else if($tipe == "inap"){
                $data['jenis'] = "Rawat Inap";
                $data['id_jurusan_kualifikasi_pendidikan'] = post_safe('id_layanan');
                $data['jenis_pelayanan'] = "Rawat Inap";
                $data['pemeriksaan_umum'] = post_safe('pemeriksaan');
                $data['id_unit'] = post_safe('id_bangsal');
                //$data['kelas'] = post_safe('kelas');
            }
            $this->db->where('id', post_safe('id_pelayanan_kunjungan'));
            $this->db->update('pelayanan_kunjungan', $data);

            $return =  post_safe('id_pelayanan_kunjungan');
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return null;
        } else {
            $this->db->trans_commit();
            return $return;
        }
    }


    function load_data_pelayanan($id_kunjungan, $jenis) {
        $q = '';

        if ($jenis == "poli") {
            //poliklinik
            $q = " and p2k.jenis_pelayanan = 'Poliklinik' and p2k.jenis = 'Rawat Jalan' ";
        }else if($jenis == "igd"){
            //igd
            $q = " and p2k.jenis_pelayanan = 'IGD' and p2k.jenis = 'Rawat Jalan' ";
        }else{
            // rawat inap
           // $q = " p2k.jenis = 'Rawat Inap' ";
        }

        $sql = "select p.no_daftar, ps.no_rm, pk.rencana_tindak_lanjut, pd.nama as pasien, 
            u.nama as unit, 
            dp.alamat, pd.lahir_tanggal, pk.rencana_tindak_lanjut,pk.anamnesis, 
            pk.pemeriksaan_umum as pemeriksaan,
            pk.no_polis, pra.id as id_produk_asuransi, pk.id as id_pelayanan_kunjungan,
            pk.*, jk.nama as jenis_layanan,
            kl.nama as kel, kb.nama as kab, kc.nama as kec, pr.nama as pro ,
            ppeg.nama as nama_dpjp,peg.id as id_dpjp , peg.id_jurusan_kualifikasi_pendidikan,
            pra.nama as produk_asuransi from 
            pendaftaran p
            join pasien ps on (p.pasien = ps.no_rm)
            join penduduk pd on (pd.id = ps.id)
            left join dinamis_penduduk dp on (pd.id = dp.penduduk_id)
            left join pelayanan_kunjungan pk on (p.no_daftar = pk.id_kunjungan)
            left join jurusan_kualifikasi_pendidikan jk on (jk.id = pk.id_jurusan_kualifikasi_pendidikan)
            left join unit u on (pk.id_unit = u.id)
            left join kepegawaian peg on(peg.id = pk.id_kepegawaian_dpjp)
            left join penduduk ppeg on (ppeg.id = peg.penduduk_id)
            left join kelurahan kl on (kl.id = dp.kelurahan_id)
            left join kecamatan kc on (kc.id = kl.kecamatan_id)
            left join kabupaten kb on (kb.id = kc.kabupaten_id)
            left join provinsi pr on (pr.id = kb.provinsi_id)
            left join asuransi_produk pra on (pra.id = pk.id_produk_asuransi)
            inner join (
                select penduduk_id, max(id) as id_max from dinamis_penduduk group by penduduk_id
            ) dm on (dp.penduduk_id = dm.penduduk_id and dp.id = dm.id_max)            
            where p.no_daftar = '$id_kunjungan' 
            and pk.id = (select max(p2k.id) from pelayanan_kunjungan p2k
                join pendaftaran p2 on(p2.no_daftar = p2k.id_kunjungan)
                where pk.id is not null $q and p2k.id_kunjungan = '$id_kunjungan' )
            ";
            
            //echo $sql;
        return $this->db->query($sql);
    }


    function get_pelayanan_kunjungan_list($id_kunjungan, $jenis = null){
        $q = null;
        if ($jenis != null) {
            $q = " and pk.jenis = '$jenis'";
        }
        $sql = "select pk.*, jjk.nama as jenis_jurusan, jk.nama as unit_layanan,
                u.nama as nama_unit, p.nama as nama_pegawai, tt.nomor as nomor_bed,
                asu.nama as nama_asuransi , null as diagnosis, null as tindakan,
                null as resep, null as lab, null as rad
                from pelayanan_kunjungan pk
                left join unit u on(u.id = pk.id_unit)
                left join jurusan_kualifikasi_pendidikan jk on( pk.id_jurusan_kualifikasi_pendidikan = jk.id ) 
                left join jenis_jurusan_kualifikasi_pendidikan jjk on(jjk.id = jk.id_jenis_jurusan_kualifikasi_pendidikan)
                left join kepegawaian peg on(peg.id = pk.id_kepegawaian_dpjp)
                left join penduduk p on (p.id = peg.penduduk_id)
                left join asuransi_produk asu on(asu.id = pk.id_produk_asuransi)
                left join tt on(tt.id = pk.no_tt)
                where pk.id_kunjungan = '$id_kunjungan' $q";
    
        $query = $this->db->query($sql);
        return $query->result();
    }

    function pelayanan_kunjungan_get_data($id){
        $sql = "select pk.* ,jjk.nama as jenis_jurusan, jk.nama as unit_layanan,
                u.nama as nama_unit, p.nama as nama_pegawai,
                asu.nama as nama_asuransi
                from pelayanan_kunjungan pk
                left join jurusan_kualifikasi_pendidikan jk on(pk.id_jurusan_kualifikasi_pendidikan = jk.id) 
                left join jenis_jurusan_kualifikasi_pendidikan jjk on(jjk.id = jk.id_jenis_jurusan_kualifikasi_pendidikan)
                left join unit u on(u.id = pk.id_unit)
                left join kepegawaian peg on(peg.id = pk.id_kepegawaian_dpjp)
                left join penduduk p on (p.id = peg.penduduk_id)
                left join asuransi_produk asu on(asu.id = pk.id_produk_asuransi)
                where pk.id = '$id' ";
        $query = $this->db->query($sql);
        return $query->row();
    }


    function diagnosa_pelayanan_kunjungan_save($id_pelayanan_kunjungan){
        $dokter = post_safe('dokter_diag'); // array
        $unit = post_safe('unit_diag'); // array
        $sebab = post_safe('sebab_diag'); // array
        $waktu = post_safe('waktu_diag'); // array
        $kasus = post_safe('kasus');
        if (is_array($waktu)) {        
            foreach ($waktu as $key => $value) {
                if ($sebab[$key] != '') {
                   $data = array(
                        'id_pelayanan_kunjungan' => $id_pelayanan_kunjungan,// dari insert pelayanan_kunjungan
                        'waktu' => datetime2mysql($value),
                        'id_kepegawaian_dokter' => ($dokter[$key] == '')?NULL:$dokter[$key], //$_POST[‘id_penduduk[dokter] dari form diagnosis’]
                        'id_unit_penunjang' => ($unit[$key]=='')?NULL:$unit[$key],//$_POST[‘id_unit’] dari form diagnosi,
                        'id_golongan_sebab_penyakit' => ($sebab[$key] != NULL)?$sebab[$key]:NULL,
                        'kasus' => $kasus[$key]
                    );
                    $this->db->insert('diagnosa_pelayanan_kunjungan', $data);
                }            
            }
        }
        
    }

    function get_diagnosis_list($id){
        $sql = "select u.nama as nama_unit, p.nama as nama_dokter, 
                gol.nama as golongan_sebab, gol.no_dtd, gol.no_daftar_terperinci, dp.*
                from diagnosa_pelayanan_kunjungan dp
                left join unit u on(u.id = dp.id_unit_penunjang)
                left join kepegawaian peg on(peg.id = dp.id_kepegawaian_dokter)
                left join penduduk p on (p.id = peg.penduduk_id)
                left join golongan_sebab_sakit gol on (gol.id = dp.id_golongan_sebab_penyakit)
                where dp.id_pelayanan_kunjungan = '$id' ";

        $query = $this->db->query($sql);
        return $query->result();
    }

    function get_tindakan_list($id){
        $sql = "select tp.*, u.nama as nama_unit, po.nama as nama_ope, pa.nama as nama_anes,
                tin.nama as tindakan, tin.kode_icdixcm
                from tindakan_pelayanan_kunjungan tp
                left join unit u on(u.id = tp.id_unit_penunjang)
                left join kepegawaian ope on(ope.id = tp.id_kepegawaian_nakes_operator)
                left join penduduk po on (po.id = ope.penduduk_id)
                left join kepegawaian an on(an.id = tp.id_kepegawaian_nakes_anesthesi)
                left join penduduk pa on (pa.id = an.penduduk_id)
                join pelayanan_kunjungan pk on (tp.id_pelayanan_kunjungan = pk.id)
                join tarif t on (tp.id_tarif = t.id)
                join layanan tin on (t.id_layanan = tin.id)
                where tp.id_pelayanan_kunjungan = '$id'";

        $query = $this->db->query($sql);
        return $query->result();
    }

    function tindakan_pelayanan_kunjungan_save($id_pelayanan_kunjungan){
        $nakes      = post_safe('id_nakes_tindak'); // array
        $anestesi   = post_safe('id_anes_tindak'); // array
        $tindakan   = post_safe('tindakan'); // array
        $unit       = post_safe('unit_tindak'); // array
        $waktu      = post_safe('waktu_tindak'); // array
        $tarif      = post_safe('id_tarif');
        if (is_array($waktu)) {
             foreach ($waktu as $key => $value) {
                if ($nakes[$key] != '') {
                    $data = array(
                       'id_pelayanan_kunjungan' => $id_pelayanan_kunjungan,// dari insert pelayanan_kunjungan,
                       'id_kepegawaian_nakes_operator' => ($nakes[$key]=='')?NULL:$nakes[$key],
                       'id_kepegawaian_nakes_anesthesi' => ($anestesi[$key]=='')?NULL:$anestesi[$key],
                       'waktu' => ($value != '')?datetime2mysql($value):NULL,
                       'id_tarif' => ($tarif[$key] !== '')?$tarif[$key]:NULL,
                       'id_unit_penunjang' => ($unit[$key]=='')?NULL:$unit[$key],
                    );
                    $this->db->insert('tindakan_pelayanan_kunjungan', $data);
                }
                $trf = $this->db->query("select * from tarif where id = '".$tarif[$key]."'")->row();
                $data_tarif = array(
                    'waktu' => ($value != '')?datetime2mysql($value):NULL,
                    'id_pelayanan_kunjungan' => $id_pelayanan_kunjungan,
                    'id_kepegawaian_nakes' => ($nakes[$key]=='')?NULL:$nakes[$key],
                    'tarif_id' => $tarif[$key],
                    'jasa_sarana' => $trf->jasa_sarana,
                    'jasa_nakes' => $trf->jasa_nakes,
                    'jasa_tindakan_rs' => $trf->jasa_tindakan_rs,
                    'bhp' => $trf->bhp,
                    'biaya_administrasi' => $trf->biaya_administrasi,
                    'total' => $trf->total,
                    'persentase_profit' => $trf->persentase_profit,
                    'nominal' => $trf->nominal,
                    'frekuensi' => 1
                );
                $this->db->insert('jasa_penjualan_detail', $data_tarif);
            }
        }
    }
    
    function pemeriksaan_lab_save($id_pk) {
        $dokter = post_safe('dokter_lab'); 
        $analis = post_safe('analis_lab'); 
        $waktu_order = post_safe('waktu_order_lab');
        $waktu_hasil = post_safe('waktu_hasil_lab'); 
        $layanan = post_safe('layanan_lab'); 
        $hasil = post_safe('hasil_lab'); 
        $ket = post_safe('ket_lab'); 
        $satuan = post_safe('satuan_lab'); 

        if(is_array($dokter)){
            foreach ($dokter as $key => $value) {
                $data = array(
                    'id_pelayanan_kunjungan' => $id_pk,
                    'id_kepegawaian_dokter_pemesan' => ($value !== '')?$value:NULL,
                    'id_kepegawaian_analis_lab' => ($analis[$key] !== '')?$analis[$key]:NULL,
                    'waktu_order' => $waktu_order[$key],
                    'waktu_hasil' => ($waktu_hasil[$key] !== '')?$waktu_hasil[$key]:NULL,
                    'id_layanan_lab' => $layanan[$key],
                    'hasil' => $hasil[$key],
                    'ket_nilai_rujukan'=> $ket[$key],
                    'id_satuan' => ($satuan[$key] !== '')?$satuan[$key]:NULL
                );
                $this->db->insert('pemeriksaan_lab_pelayanan_kunjungan', $data);    
            }
        }
    }

    function pemeriksaan_rad_save($id_pk) {
        $id_dokter = post_safe('id_dokter_radio');
        $id_radio = post_safe('id_radio_radio');
        $wkt_order = post_safe('waktu_order_radio');
        $wkt_hasil = post_safe('waktu_hasil_radio');
        $id_layan  = post_safe('id_layanan_radio');
        $kv = post_safe('kv_radio');
        $ma = post_safe('ma_radio');
        $s = post_safe('s_radio');
        $p = post_safe('p_radio');
        $fr = post_safe('fr_radio');
        
        if(isset($_POST['id_dokter_radio'])){
            foreach ($id_dokter as $key => $data) {
                $data_pemeriksaan = array(
                    'id_pelayanan_kunjungan' => $id_pk,
                    'id_kepegawaian_dokter_pemesan' => ($data !== '')?$data:NULL,
                    'id_kepegawaian_radiografer' => ($id_radio[$key] != '')?$id_radio[$key]:NULL,
                    'waktu_order' => datetime2mysql($wkt_order[$key]),
                    'waktu_hasil' => ($wkt_hasil[$key] !== '')?datetime2mysql($wkt_hasil[$key]):NULL,
                    'id_layanan_radiologi' => $id_layan[$key],
                    'kv' => $kv[$key],
                    'ma' => $ma[$key],
                    's' => $s[$key],
                    'p' => $p[$key],
                    'fr' => $fr[$key]
                );
                $this->db->insert('pemeriksaan_radiologi_pelayanan_kunjungan', $data_pemeriksaan);
            }
        }
    }

    function vital_sign_save($id_pk) {
        $waktu = post_safe('waktu');
        $tensi = post_safe('tensi_data');
        $nadi = post_safe('nadi_data');
        $suhu = post_safe('suhu_data');
        $nafas = post_safe('nafas_data');
        
        if(is_array($waktu)){
            foreach ($waktu as $key => $data) {
                $data_pemeriksaan = array(
                    'id_pelayanan_kunjungan' => $id_pk,
                    'waktu' => ($data !== '')?datetime2mysql($data.":".date('s')):NULL,
                    'tensi' => $tensi[$key],
                    'nadi' => $nadi[$key],
                    'suhu' => $suhu[$key],
                    'nafas' => $nafas[$key]
                );
                $this->db->insert('vital_sign', $data_pemeriksaan);
            }
        }
    }

    function get_vital_sign($id_pk){
        $sql = "select * from vital_sign where id_pelayanan_kunjungan = '$id_pk' order by waktu";
        //echo $sql;
        return $this->db->query($sql)->result();
    }

    function delete_tindakan($id){
        $this->db->where('id', $id);
        $this->db->delete('tindakan_pelayanan_kunjungan');
    }

    function delete_diagnosis($id){
        $this->db->where('id', $id);
        $this->db->delete('diagnosa_pelayanan_kunjungan');
    }

    
    function get_atribute_ic($id_pk) {
        $sql = "select p.*, ps.no_rm, pk.rencana_tindak_lanjut,
            pd.nama as pasien, u.nama as unit, 
            dp.alamat, pd.lahir_tanggal, pk.rencana_tindak_lanjut,pk.anamnesis, pk.pemeriksaan_umum as pemeriksaan,
            pk.no_polis, pra.id as id_produk_asuransi, pk.id as id_pelayanan_kunjungan,
            pk.*, kl.nama as kel, kb.nama as kab, kc.nama as kec, pr.nama as pro ,
            p1.nama as nama_dpjp,peg.id as id_dpjp , peg.id_jurusan_kualifikasi_pendidikan,pra.nama as produk_asuransi from 
            pendaftaran p
            join pasien ps on (p.pasien = ps.no_rm)
            join penduduk pd on (pd.id = ps.id)
            left join dinamis_penduduk dp on (pd.id = dp.penduduk_id)
            left join pelayanan_kunjungan pk on (p.no_daftar = pk.id_kunjungan)
            left join unit u on (pk.id_unit = u.id)
            left join kepegawaian peg on(peg.id = pk.id_kepegawaian_dpjp)
            left join penduduk p1 on (p1.id = peg.penduduk_id)
            left join kelurahan kl on (kl.id = dp.kelurahan_id)
            left join kecamatan kc on (kc.id = kl.kecamatan_id)
            left join kabupaten kb on (kb.id = kc.kabupaten_id)
            left join provinsi pr on (pr.id = kb.provinsi_id)
            left join asuransi_produk pra on (pra.id = pk.id_produk_asuransi)
            left join (
                select id_kunjungan, max(id) as id_max from pelayanan_kunjungan group by id_kunjungan
            ) pli on (pk.id_kunjungan = pli.id_kunjungan and pk.id = pli.id_max)
            inner join (
                select penduduk_id, max(id) as id_max from dinamis_penduduk group by penduduk_id
            ) dm on (dp.penduduk_id = dm.penduduk_id and dp.id = dm.id_max)
            where pk.id = '$id_pk'";
            //echo $sql;
        return $this->db->query($sql);
    }
    
    function load_data_diagnosis($id_pk , $jenis) {
        $q = '';

        if ($jenis == "poli") {
            //poliklinik
            $q = "  and jenis = 'Rawat Jalan' ";
        }else if($jenis == "igd"){
            //igd
            $q = "  and jenis = 'Rawat Jalan' ";
        }else{
            // rawat inap
            $q = " and jenis = 'Rawat Inap' ";
        }

        $sql = "select d.*, p.nama as dokter, g.nama as golongan, u.nama as unit,
            g.no_daftar_terperinci as kode
            from diagnosa_pelayanan_kunjungan d
            join kepegawaian k on (d.id_kepegawaian_dokter = k.id)
            left join penduduk p on (p.id = k.penduduk_id)
            join unit u on (d.id_unit_penunjang = u.id)
            join golongan_sebab_sakit g on (d.id_golongan_sebab_penyakit = g.id)
            where d.id_pelayanan_kunjungan = '".$id_pk."'";
        //echo "<pre>".$sql."</pre>";
        return $this->db->query($sql);
    }
    
    function load_data_tindakan($id_pk, $jenis) {
        $q = '';

        if ($jenis == "poli") {
            //poliklinik
            $q = "  and jenis = 'Rawat Jalan' ";
        }else if($jenis == "igd"){
            //igd
            $q = "  and jenis = 'Rawat Jalan' ";
        }else{
            // rawat inap
            $q = " and jenis = 'Rawat Inap' ";
        }

        $sql = "select d.*, p1.nama as operator, p2.nama as anestesi, CONCAT_WS(' ',l.nama,u.nama,t.kelas) as tindakan, l.kode_icdixcm as kode, u.nama as unit 
            from tindakan_pelayanan_kunjungan d
            left join kepegawaian k on (d.id_kepegawaian_nakes_operator = k.id)
            left join penduduk p1 on (p1.id = k.penduduk_id)
            left join kepegawaian kp on (d.id_kepegawaian_nakes_anesthesi = kp.id)
            left join penduduk p2 on (p2.id = kp.penduduk_id)
            join unit u on (d.id_unit_penunjang = u.id)
            join tarif t on (d.id_tarif = t.id)
            left join layanan l on (t.id_layanan = l.id)
            where d.id_pelayanan_kunjungan = '".$id_pk."'";
      //  echo "<pre>".$sql."</pre>";
        return $this->db->query($sql);
    }

    function get_data_tindakan($id_tindakan) {
        $sql = "select d.*, p1.nama as operator, p2.nama as anestesi, l.nama as tindakan, 
            l.kode_icdixcm as kode, u.nama as unit from tindakan_pelayanan_kunjungan d
            left join kepegawaian k on (d.id_kepegawaian_nakes_operator = k.id)
            left join penduduk p1 on (p1.id = k.penduduk_id)
            left join kepegawaian kp on (d.id_kepegawaian_nakes_anesthesi = kp.id)
            left join penduduk p2 on (p2.id = kp.penduduk_id)
            join unit u on (d.id_unit_penunjang = u.id)
            join layanan l on (d.id_layanan = l.id)
            where d.id = '".$id_tindakan."'";
        //echo "<pre>".$sql."</pre>";
        return $this->db->query($sql);
    }
    
    function klinis_load_data($var, $limit = null, $start = null, $inap = null) {
        $q = NULL; $limitation = NULL;
        if ($limit != NULL or $start != NULL) {
            $limitation = " limit $start, $limit";
        }
        if ($var['awal'] != NULL and $var['akhir'] != NULL) {
            $q.=" and p.arrive_time between '".  datetime2mysql($var['awal'])."' and '".  datetime2mysql($var['akhir'])."'";
        }
        if ($var['no'] != NULL) {
            $q.=" and p.no_daftar = '$var[no]'";
        }
        if ($var['nama'] != NULL) {
            $q.=" and pdd.nama like ('%$var[nama]%')";
        }
        if ($var['no_rm'] != NULL) {
            $q.=" and p.pasien = '$var[no_rm]'";
        }
        if ($var['alamat'] != NULL) {
            $q.=" and dp.alamat like ('%$var[alamat]%')";
        }
        if ($var['kelurahan'] != NULL) {
            $q.=" and dp.kelurahan_id = '$var[kelurahan]'";
        }

        $ir = '';
        if($inap != null){
            $ir = " join pelayanan_kunjungan pk on (pk.id_kunjungan = p.no_daftar and pk.jenis = 'Rawat Inap')";
                   }
        $sql = "select p.no_daftar, ps.no_rm, p.arrive_time, p.waktu_keluar, p.jenis_rawat, pdd.nama, 
            k.nama as kelurahan, kc.nama as kecamatan, kb.nama as kabupaten, pr.nama as provinsi 
            from pendaftaran p
            join pasien ps on (p.pasien = ps.no_rm)
            join penduduk pdd on (ps.id = pdd.id) $ir
            join dinamis_penduduk dp on (pdd.id = dp.penduduk_id)
            left join kelurahan k on (k.id = dp.kelurahan_id)
            left join kecamatan kc on (kc.id = k.kecamatan_id)
            left join kabupaten kb on (kb.id = kc.kabupaten_id)
            left join provinsi pr on (pr.id = kb.provinsi_id)
            inner join (
                select penduduk_id, max(id) as id_max from dinamis_penduduk group by penduduk_id
            ) dm on (dp.penduduk_id = dm.penduduk_id and dp.id = dm.id_max)
            where p.no_daftar is not NULL $q 
            union 
            select p.no_daftar, 'Pasien Luar' as no_rm, p.arrive_time, p.waktu_keluar, p.jenis_rawat, pdd.nama, 
                k.nama as kelurahan, kc.nama as kecamatan, kb.nama as kabupaten, pr.nama as provinsi 
                from pendaftaran p
                join penduduk pdd on (p.id_customer = pdd.id) $ir
                join dinamis_penduduk dp on (pdd.id = dp.penduduk_id)
                left join kelurahan k on (k.id = dp.kelurahan_id)
                left join kecamatan kc on (kc.id = k.kecamatan_id)
                left join kabupaten kb on (kb.id = kc.kabupaten_id)
                left join provinsi pr on (pr.id = kb.provinsi_id)
                inner join (
                    select penduduk_id, max(id) as id_max from dinamis_penduduk group by penduduk_id
                ) dm on (dp.penduduk_id = dm.penduduk_id and dp.id = dm.id_max)
                where p.no_daftar is not NULL $q  
            ";
        //echo "<pre>".$sql."</pre>";
        return $this->db->query($sql.$limitation);
    }
    
    function detail_kunjungan($id_kunjungan, $irna=null) {
        $q = '';
        if ($irna != null) {
            $q .= " and pk.jenis = 'Rawat Inap'";
        }

        $sql = "select pk.*, pd.pasien, p.nama, u.nama as unit, pd.jenis_rawat, 
            ap.nama as asuransi, pd.no_daftar, tt.nomor as nomor_bed
            from pelayanan_kunjungan pk
            join pendaftaran pd on (pk.id_kunjungan = pd.no_daftar)
            left join kepegawaian k on (pk.id_kepegawaian_dpjp = k.id)
            left join penduduk p on (p.id = k.penduduk_id)
            left join tt on (tt.id = pk.no_tt)
            left join unit u on (pk.id_unit = u.id)
            left join asuransi_produk ap on (ap.id = pk.id_produk_asuransi)
            where pk.id_kunjungan = '$id_kunjungan' $q";
      // echo $sql;
        return $this->db->query($sql);
    }
    
    function detail_pelayanan_diagnosis($id_pelayanan_kunjungan) {
        $sql = "select dpk.*, p.nama, u.nama as unit, g.nama as golongan,
            g.no_dtd , g.no_daftar_terperinci
            from diagnosa_pelayanan_kunjungan dpk
            join kepegawaian k on (dpk.id_kepegawaian_dokter = k.id)
            left join penduduk p on (p.id = k.penduduk_id)
            join unit u on (dpk.id_unit_penunjang = u.id)
            join golongan_sebab_sakit g on (dpk.id_golongan_sebab_penyakit = g.id)
            where dpk.id_pelayanan_kunjungan = '$id_pelayanan_kunjungan'";
        return $this->db->query($sql);
    }
    
    function detail_pelayanan_tindakan($id_pelayanan_kunjungan) {
        $sql = "select tpk.*,  u.nama as unit, l.nama as layanan, po.nama as operator, 
            pa.nama as anestesi, l.kode_icdixcm from tindakan_pelayanan_kunjungan tpk
            left join kepegawaian k on (tpk.id_kepegawaian_nakes_operator = k.id)
            left join penduduk po on (po.id = k.penduduk_id)
            left join kepegawaian kp on (tpk.id_kepegawaian_nakes_anesthesi = kp.id)
            left join penduduk pa on (pa.id = kp.penduduk_id)
            join unit u on (tpk.id_unit_penunjang = u.id)
            join pelayanan_kunjungan pk on (tpk.id_pelayanan_kunjungan = pk.id)
            join tarif t on (t.id = tpk.id_tarif)
            join layanan l on (t.id_layanan = l.id)
            where tpk.id_pelayanan_kunjungan = '$id_pelayanan_kunjungan'";
        //echo $sql;
        return $this->db->query($sql);
    }
    
    function detail_pelayanan_kunjungan($id_pelayanan_kunjungan) {
        $sql = "select pk.*, p.nama, u.nama as unit, pdd.nama as pasien, ap.nama as asuransi,
            pkj.nama as pekerjaan, pdk.nama as pendidikan, dp.alamat, pdd.gender,
            pdd.lahir_tanggal, pd.jenis_rawat, pd.pasien as no_rm, pd.no_daftar as id_kunjungan,pd.waktu_keluar,
            kl.nama as kelurahan, kc.nama as kecamatan, kb.nama as kabupaten, 
            pr.nama as provinsi, tt.nomor as nomor_bed
            from pelayanan_kunjungan pk
            join pendaftaran pd on (pk.id_kunjungan = pd.no_daftar)
            join pasien ps on (pd.pasien = ps.no_rm)
            join penduduk pdd on (ps.id = pdd.id)
            left join kepegawaian k on (pk.id_kepegawaian_dpjp = k.id)
            left join penduduk p on (p.id = k.penduduk_id)
            left join unit u on (pk.id_unit = u.id)
            left join dinamis_penduduk dp on (dp.penduduk_id = pdd.id)
            left join pekerjaan pkj on (pkj.id = dp.pekerjaan_id)
            left join pendidikan pdk on (pdk.id = dp.pendidikan_id)
            left join asuransi_produk ap on (ap.id = pk.id_produk_asuransi)
            left join kelurahan kl on (kl.id = dp.kelurahan_id)
            left join kecamatan kc on (kc.id = kl.kecamatan_id)
            left join kabupaten kb on (kb.id = kc.kabupaten_id)
            left join provinsi pr on (pr.id = kb.provinsi_id)
            left join tt on (tt.id = pk.no_tt)
            inner join (
                select penduduk_id, max(id) as id_max from dinamis_penduduk group by penduduk_id
            ) dm on (dp.penduduk_id = dm.penduduk_id and dp.id = dm.id_max)
            where pk.id = '$id_pelayanan_kunjungan'";
        //echo "<pre>".$sql."</pre>";
        return $this->db->query($sql);
    }

    function detail_pelayanan_kunjungan_non_pasien($id_pelayanan_kunjungan) {
        $sql = "select pk.*, p.nama, u.nama as unit, pdd.nama as pasien, ap.nama as asuransi,
            pkj.nama as pekerjaan, pdk.nama as pendidikan, dp.alamat, pdd.gender,
            pdd.lahir_tanggal, pd.jenis_rawat, pd.pasien as no_rm, pd.no_daftar as id_kunjungan,pd.waktu_keluar,
            kl.nama as kelurahan, kc.nama as kecamatan, kb.nama as kabupaten, pr.nama as provinsi
            from pelayanan_kunjungan pk
            join pendaftaran pd on (pk.id_kunjungan = pd.no_daftar)
            join penduduk pdd on (pd.id_customer = pdd.id)
            left join kepegawaian k on (pk.id_kepegawaian_dpjp = k.id)
            left join penduduk p on (p.id = k.penduduk_id)
            left join unit u on (pk.id_unit = u.id)
            left join dinamis_penduduk dp on (dp.penduduk_id = pdd.id)
            left join pekerjaan pkj on (pkj.id = dp.pekerjaan_id)
            left join pendidikan pdk on (pdk.id = dp.pendidikan_id)
            left join asuransi_produk ap on (ap.id = pk.id_produk_asuransi)
            left join kelurahan kl on (kl.id = dp.kelurahan_id)
            left join kecamatan kc on (kc.id = kl.kecamatan_id)
            left join kabupaten kb on (kb.id = kc.kabupaten_id)
            left join provinsi pr on (pr.id = kb.provinsi_id)
            inner join (
                select penduduk_id, max(id) as id_max from dinamis_penduduk group by penduduk_id
            ) dm on (dp.penduduk_id = dm.penduduk_id and dp.id = dm.id_max)
            where pk.id = '$id_pelayanan_kunjungan'";
        //echo $sql;
        return $this->db->query($sql);
    }

     function data_pasien_muat_data($no_daftar) {
        $sql = "select pdf.*,p.id as id_pasien,p.no_rm, pd.nama as pasien, 
            pdf.no_daftar as id_kunjungan,
            pd.gender, pd.id as id_penduduk,
            pk.nama as pekerjaan, pdi.nama as pendidikan, 
            pd.lahir_tanggal, kel.nama as kelurahan,
            kec.nama as kecamatan, dp.alamat, 
            kelpj.nama as kelurahan_pj, rel.nama as instansi_rujukan, 
            pd.darah_gol, kecpj.nama as kecamatan_pj
            from pasien p
            join penduduk pd on (p.id = pd.id)
            join pendaftaran pdf on (p.no_rm = pdf.pasien)
            join dinamis_penduduk dp on (pd.id = dp.penduduk_id)
            left join pekerjaan pk on (pk.id = dp.pekerjaan_id)
            left join pendidikan pdi on (pdi.id = dp.pendidikan_id)
            left join kelurahan kel on (kel.id = dp.kelurahan_id)
            left join kecamatan kec on (kec.id = kel.kecamatan_id)
            left join kelurahan kelpj on (pdf.kelurahan_id_pjwb = kelpj.id)
            left join kecamatan kecpj on (kecpj.id = kelpj.kecamatan_id)
            left join relasi_instansi rel on (rel.id = pdf.rujukan_instansi_id)
            inner join (
                select penduduk_id, max(id) as id_max
                from dinamis_penduduk GROUP BY penduduk_id
            ) dpi on (dp.penduduk_id = dpi.penduduk_id and dp.id = dpi.id_max)
            where pdf.no_daftar = '$no_daftar'";
           // echo $sql;
        $exe = $this->db->query($sql);
        
        return $exe;
    }
    
    function detail_pelayanan_kunjungan_rujukan($id_pelayanan_kunjungan) {
        $sql = "select pd.*, r.nama as rujukan from pelayanan_kunjungan pk
            join pendaftaran pd on (pd.no_daftar = pk.id_kunjungan)
            left join relasi_instansi r on (r.id = pd.rujukan_instansi_id)
            where pk.id = '$id_pelayanan_kunjungan'";
        return $this->db->query($sql);
    }
    
    function data_jenis_layanan_load_data($limit,$start, $search, $id = null) {
        $q = null;
        if ($id != null) {
            $q = "where j.id = '$id'";
        }

        if($search != ''){
            $q = " where ss.nama like '%$search%' ";
        }
        if($limit == 'all'){
            $page = '';
        }else{
            $page = " limit $start, $limit";
        }
        $sql = "select j.* from jenis_layanan j
            left join sub_jenis_layanan s on(j.id = s.id_jenis_layanan)
            left join sub_sub_jenis_layanan ss on(ss.id_sub_jenis_layanan = s.id)
            $q group by j.nama order by nama asc  ";
        //echo $sql.$page;
        $data['data'] = $this->db->query($sql.$page)->result();
        $data['jumlah'] = $this->db->query($sql)->num_rows();
        return $data;
    }

    function data_jenis_layanan_load($id=null) {
        $q = null;
        if ($id != null) {
            $q = "where id = '$id'";
        }

        $sql = "select * from jenis_layanan $q order by id asc  ";
      
        return $this->db->query($sql);
    }

    function jenis_layanan_save(){
        $data = array(
            'nama' => post_safe("nama_jenis")
         );
        $this->db->insert("jenis_layanan", $data);
        return $this->db->insert_id();
    }

    function jenis_layanan_edit(){
        $data = array(
            'nama' => post_safe("nama_jenis")
         );
        $this->db->where('id', post_safe("id_jenis"));
        $this->db->update("jenis_layanan", $data);
        return post_safe("id_jenis");
    }

    function jenis_layanan_delete($id){
        $this->db->where('id', $id);
        $this->db->delete("jenis_layanan");
                   
    }

    function sub_jenis_layanan_delete($id){
        $this->db->where('id', $id);
        $this->db->delete("sub_jenis_layanan");
    }

    function sub_sub_jenis_layanan_delete($id){
        $this->db->where('id', $id);
        $this->db->delete("sub_sub_jenis_layanan");
    }
    

    function data_subjenis_layanan_load_data($id = null, $id_jenis_layanan = null) {
        $q = null;
        if ($id != null) {
            $q.=" and id = '$id'";
        }
        if ($id_jenis_layanan != null) {
            $q.=" and id_jenis_layanan = '$id_jenis_layanan'";
        }
        $sql = "select * from sub_jenis_layanan where id is not NULL $q order by id";
        //echo $sql;
        return $this->db->query($sql);
    }

    function data_subsubjenis_layanan_load_data($id = null, $id_sub_jenis_layanan = null) {
        $q = null;
        if ($id != null) {
            $q.=" and id = '$id'";
        }
        if ($id_sub_jenis_layanan != null) {
            $q.=" and id_sub_jenis_layanan = '$id_sub_jenis_layanan'";
        }
        $sql = "select * from sub_sub_jenis_layanan where id is not NULL $q order by id";
        //echo $sql;
        return $this->db->query($sql);
    }


    function jenis_sub_layanan_save(){
        $data = array(
            'nama' => post_safe("nama_sub_jenis"),
            'id_jenis_layanan' => post_safe("jenis_id")
         );
        $this->db->insert("sub_jenis_layanan", $data);
        return $this->db->insert_id();
    }

    function jenis_sub_layanan_edit(){
        $data = array(
            'nama' => post_safe("nama_sub_jenis"),
            'id_jenis_layanan' => post_safe("jenis_id")
         );
        $this->db->where('id', post_safe("id_sub_jenis"));
        $this->db->update("sub_jenis_layanan", $data);
        return post_safe("id_sub_jenis");
    }

    function jenis_sub_sub_layanan_save(){
        $data = array(
            'nama' => post_safe("nama_sub_sub_jenis"),
            'id_sub_jenis_layanan' => post_safe("sub_jenis_id")
        );
        $this->db->insert("sub_sub_jenis_layanan", $data);
        return $this->db->insert_id();
    }

    function jenis_sub_sub_layanan_edit(){
        $data = array(
            'nama' => post_safe("nama_sub_sub_jenis"),
            'id_sub_jenis_layanan' => post_safe("sub_jenis_id")
         );


        $this->db->where('id', post_safe("id_sub_sub_jenis"));
        $this->db->update("sub_sub_jenis_layanan", $data);
        return post_safe("id_sub_sub_jenis");
    }

    function jenis_layanan_load_data($q){
         $sql = "select nama as jenis , id as id_jenis
            from  jenis_layanan
            where nama like ('%$q%') order by locate('$q', nama)";
        return $this->db->query($sql);
    }

    function sub_jenis_layanan_load_data($q, $id_jenis){
        $w = '';
        if ($id_jenis != '') {
            $w = " and s.id_jenis_layanan = '$id_jenis'";
        }

         $sql = "select s.nama as sub_jenis, s.id as id_sub,
            j.nama as jenis , j.id as id_jenis
            from sub_jenis_layanan s
            join jenis_layanan j on(s.id_jenis_layanan = j.id)
            where s.nama like ('%$q%') $w order by locate('$q', s.nama)";
        return $this->db->query($sql);
    }

    function sub_sub_jenis_layanan_load_data($q, $id_sub){
        $w = '';
        if ($id_sub != '') {
            $w = " and ss.id_sub_jenis_layanan = '$id_sub'";
        }

         $sql = "select ss.nama as subsub_jenis , ss.id as id_subsub, 
            s.nama as sub_jenis, s.id as id_sub,
            j.nama as jenis , j.id as id_jenis,
            CONCAT_WS(' - ',ss.nama, s.nama, j.nama) as jenis_layanan
            from sub_sub_jenis_layanan ss
            join sub_jenis_layanan s on(ss.id_sub_jenis_layanan = s.id)
            join jenis_layanan j on(s.id_jenis_layanan = j.id)
            having jenis_layanan like ('%$q%') $w order by locate('$q', ss.nama)";
        return $this->db->query($sql);
    }

    function get_laporan_tindakan($tahun, $id_sub_sub){
         $sql = "select l.id, count(l.nama) as jumlah, l.nama as nama_layanan,
            ss.id as id_sub_sub
            from tindakan_pelayanan_kunjungan tk 
            join layanan l on (tk.id_layanan = l.id)
            join sub_sub_jenis_layanan ss on (l.id_sub_sub_jenis_layanan = ss.id)
            join sub_jenis_layanan s on (ss.id_sub_jenis_layanan = s.id)
            join jenis_layanan j on (s.id_jenis_layanan = j.id)
            where EXTRACT(YEAR FROM tk.waktu) = '$tahun' and ss.id = '$id_sub_sub' group by l.nama";
        //echo $sql;
        return $this->db->query($sql)->result();
    }

    function rekap_kegiatan_query(){
        $id_sub_sub = get_safe('id_sub_sub');
        $id_sub = get_safe('id_sub');
        $id_jenis = get_safe('id_jenis');

        $awal = date2mysql(get_safe('awal'));
        $akhir = date2mysql(get_safe('akhir'));
        $waktu = '';
        $where = '';
        if(($awal != '') & ($akhir != '')){
            $waktu = "where tk.waktu between '$awal 00:00:00' and '$akhir 23:59:59' ";
        }

        if(($awal != '') & ($akhir == '')){
            $waktu = "where tk.waktu > '$awal 00:00:00'  ";
        }

        if(($awal == '') & ($akhir != '')){
            $waktu = "where tk.waktu < '$akhir 23:59:59' ";
        }
        if($id_sub_sub != ''){
            $where = "and ss.id = '$id_sub_sub'";
        }else if($id_sub != ''){
            $where = "and s.id = '$id_sub'";
        }else if($id_jenis != ''){
            $where = "and j.id = '$id_jenis'";
        }



        $sql = "select count(l.nama) as jumlah, l.nama as nama_layanan,
            ss.id as id_sub_sub, ss.nama as nama_sub_sub
            from tindakan_pelayanan_kunjungan tk 
            join tarif t on (tk.id_tarif = t.id)
            join layanan l on (t.id_layanan = l.id)
            left join sub_sub_jenis_layanan ss on (l.id_sub_sub_jenis_layanan = ss.id)
            left join sub_jenis_layanan s on (ss.id_sub_jenis_layanan = s.id)
            left join jenis_layanan j on (s.id_jenis_layanan = j.id)
             $waktu $where group by l.nama";
        //echo $sql;
        return $this->db->query($sql);
    }


    function rekap_morbiditas_load_data($var) {
        date_default_timezone_set('Asia/Jakarta');
        $q = null;
        if ($var['awal'] != '' and $var['akhir'] != '') {
            $q.=" and dpk.waktu between '$var[awal] 00:00:00' and '$var[akhir] 23:59:59'";
        }
        if ($var['kondisi'] != '') {
            $q.=" and p.kondisi_keluar = '".(($var['kondisi'] === 'Tidak')?'Hidup':'Meninggal')."'";
        }
        if ($var['keluar'] != '') {
            if ($var['keluar'] == 'Sudah') {
                $q.=" and p.waktu_keluar is not NULL";
            }
            if ($var['keluar'] == 'Belum') {
                $q.=" and p.waktu_keluar is NULL";
            }
        }
        if ($var['sex'] != '') {
            $q.=" and pdd.gender = '$var[sex]'";
        }
        if ($var['klpumur'] != '') {
            if ($var['klpumur'] == '1') {
                $x = mktime(0, 0, 0, date("m"), date("d")-6, date("Y")); $awal = date("Y-m-d", $x);
                $akhir= date("Y-m-d");
                $q.=" and pdd.lahir_tanggal between '$awal' and '$akhir'";
            }
            if ($var['klpumur'] == '2') {
                $x = mktime(0, 0, 0, date("m"), date("d")-28, date("Y")); $awal = date("Y-m-d", $x);
                $y = mktime(0, 0, 0, date("m"), date("d")-7, date("Y")); $akhir= date("Y-m-d", $y);
                $q.=" and pdd.lahir_tanggal between '$awal' and '$akhir'";
            }
            if ($var['klpumur'] == '3') {
                $x = mktime(0, 0, 0, date("m"), date("d"), date("Y")-1); $awal = date("Y-m-d", $x);
                $y = mktime(0, 0, 0, date("m"), date("d")-28, date("Y")); $akhir= date("Y-m-d", $y);
                $q.=" and pdd.lahir_tanggal between '$awal' and '$akhir'";
            }
            if ($var['klpumur'] == '4') {
                $x = mktime(0, 0, 0, date("m"), date("d"), date("Y")-4); $awal = date("Y-m-d", $x);
                $y = mktime(0, 0, 0, date("m"), date("d"), date("Y")-1); $akhir= date("Y-m-d", $y);
                $q.=" and pdd.lahir_tanggal between '$awal' and '$akhir'";
            }
            if ($var['klpumur'] == '5') {
                $x = mktime(0, 0, 0, date("m"), date("d"), date("Y")-14); $awal = date("Y-m-d", $x);
                $y = mktime(0, 0, 0, date("m"), date("d"), date("Y")-5); $akhir= date("Y-m-d", $y);
                $q.=" and pdd.lahir_tanggal between '$awal' and '$akhir'";
            }
            if ($var['klpumur'] == '6') {
                $x = mktime(0, 0, 0, date("m"), date("d"), date("Y")-24); $awal = date("Y-m-d", $x);
                $y = mktime(0, 0, 0, date("m"), date("d"), date("Y")-15); $akhir= date("Y-m-d", $y);
                $q.=" and pdd.lahir_tanggal between '$awal' and '$akhir'";
            }
            if ($var['klpumur'] == '7') {
                $x = mktime(0, 0, 0, date("m"), date("d"), date("Y")-44); $awal = date("Y-m-d", $x);
                $y = mktime(0, 0, 0, date("m"), date("d"), date("Y")-25); $akhir= date("Y-m-d", $y);
                $q.=" and pdd.lahir_tanggal between '$awal' and '$akhir'";
            }
            if ($var['klpumur'] == '8') {
                $x = mktime(0, 0, 0, date("m"), date("d"), date("Y")-64); $awal = date("Y-m-d", $x);
                $y = mktime(0, 0, 0, date("m"), date("d"), date("Y")-45); $akhir= date("Y-m-d", $y);
                $q.=" and pdd.lahir_tanggal between '$awal' and '$akhir'";
            }
            if ($var['klpumur'] == '9') {
                $x = mktime(0, 0, 0, date("m"), date("d"), date("Y")-65); $tanggal = date("Y-m-d", $x);
                $q.=" and pdd.lahir_tanggal < '$tanggal'";
            }
        }
        $sql = "select count(*) as jumlah, dpk.*, gss.no_dtd, gss.no_daftar_terperinci, 
            gss.nama as nama_gol from diagnosa_pelayanan_kunjungan dpk
            join pelayanan_kunjungan pk on (dpk.id_pelayanan_kunjungan = pk.id)
            join golongan_sebab_sakit gss on (dpk.id_golongan_sebab_penyakit = gss.id)
            join pendaftaran p on (pk.id_kunjungan = p.no_daftar)
            join pasien ps on (p.pasien = ps.no_rm)
            join penduduk pdd on (ps.id = pdd.id)
            where dpk.id is not NULL $q group by dpk.id_golongan_sebab_penyakit";
        //echo "<pre>".$sql."</pre>";
        return $this->db->query($sql);
    }
    
    function rekap_igd_load_data($ar) {
        $q = '';//" and pd.rujukan_instansi_id is NULL  and pd.rujuk_instansi_id is NULL";
        if ($ar['awal'] != '' and $ar['akhir'] != '') {
            $q.=" and p.waktu between '$ar[awal] 00:00:00' and '$ar[akhir] 23:59:59'";
        }
        if ($ar['rujukan'] != '') {
            $q .=" and pd.rujukan_instansi_id is not NULL";
        }

        if ($ar['tindaklanjut'] == 'dirawat') {
            $q .=" and TIMESTAMPDIFF(DAY, pd.arrive_time, pd.waktu_keluar) > 1";
        }else if($ar['tindaklanjut'] == 'dirujuk'){ 
            $q .=" and pd.rujuk_instansi_id is not NULL";
        }else if($ar['tindaklanjut'] == 'pulang'){
            $q .=" and TIMESTAMPDIFF(DAY, pd.arrive_time, pd.waktu_keluar) = 0 ";
        }


        if ($ar['matiigd'] != '') {
            $q .=" and p.kondisi = '$ar[matiigd]'";
        }
        if ($ar['doa'] != '') {
            $q .=" and pd.doa = '$ar[doa]'";
        }
        $sql = "select count(*) as jumlah, t.*, ss.nama as nama_ss, jl.nama as jenis_layanan, 
            sj.nama as nama_sj, l.nama as nama_layanan from tindakan_pelayanan_kunjungan t
            join tarif tr on (t.id_tarif = tr.id)
            join layanan l on (l.id = tr.id_layanan)
            join sub_sub_jenis_layanan ss on (l.id_sub_sub_jenis_layanan = ss.id)
            join sub_jenis_layanan sj on (ss.id_sub_jenis_layanan = sj.id)
            join jenis_layanan jl on(sj.id_jenis_layanan = jl.id)
            join pelayanan_kunjungan p on (p.id = t.id_pelayanan_kunjungan)
            join pendaftaran pd on (pd.no_daftar = p.id_kunjungan)
            where t.id is not NULL 
            $q
            group by ss.id
            ";
        //echo "<pre>".$sql."</pre>";
        return $this->db->query($sql);
    }
    
    function get_status_pasien($ym, $status) {
        $q = null;
        if ($status == 'lama') {
            $q.=" where p.kunjungan > 1";
        }
        if ($status == 'baru') {
            $q.=" where p.kunjungan = 1";
        }
        $sql = "select count(*) as jumlah from pasien p 
            join penduduk pd on (p.id = pd.id)
            left join dinamis_penduduk dp on (dp.penduduk_id = pd.id)
            inner join (
                select penduduk_id, max(id) as id_max from dinamis_penduduk
                group by penduduk_id
            ) tm on (dp.penduduk_id = tm.penduduk_id and dp.id = tm.id_max)
            $q";
        //echo $sql."<br/>";
        $data = $this->db->query($sql);
        return $data;
        
    }
    
    function load_data_jenis_layanan() {
        $sql = "select * from jenis_layanan order by nama asc";
        return $this->db->query($sql);
    }
    
    function load_data_sub_jenis_layanan() {
        $sql = "select * from sub_jenis_layanan order by nama asc";
        return $this->db->query($sql);
    }

    function rekap_laporan_kegiatan(){
         $sql = "select count(l.nama) as jumlah, l.nama as nama_layanan,
            ss.id as id_sub_sub, ss.nama as nama_sub_sub
            from tindakan_pelayanan_kunjungan tk 
            join layanan l on (tk.id_layanan = l.id)
            join sub_sub_jenis_layanan ss on (l.id_sub_sub_jenis_layanan = ss.id)
            join sub_jenis_layanan s on (ss.id_sub_jenis_layanan = s.id)
            join jenis_layanan j on (s.id_jenis_layanan = j.id) group by l.nama";
        //echo $sql;
        return $this->db->query($sql);
    }


    function get_jenis_layanan(){
         $sql = "select ss.id as id_sub_sub , ss.nama as nama_sub_sub, 
            s.id as id_sub, s.nama as nama_sub,
            j.id as id_jenis, j.nama as nama_jenis
            from sub_sub_jenis_layanan ss
            join sub_jenis_layanan s on (ss.id_sub_jenis_layanan = s.id)
            join jenis_layanan j on (s.id_jenis_layanan = j.id)
            group by ss.nama order by j.nama, s.nama, ss.nama";
        //echo $sql;
        return $this->db->query($sql)->result();
    }

    function data_pasien($q) {
        $w= '';
        $jenis = (isset($_GET['jenis']))?get_safe('jenis'):'';
        if($jenis != ''){
            $w = " and pdf.jenis_rawat = '$jenis'";
        }

        $sql = "select pdf.*,p.id as id_pasien,p.*, pd.nama, pdf.no_daftar,pd.gender,
            pk.nama as pekerjaan, pdi.nama as pendidikan, pd.*, kel.nama as kelurahan, dp.alamat,
             pd.darah_gol, kec.nama as kecamatan, kelj.nama as kelurahan_pj, kecj.nama as kecamatan_pj, rl.nama as instansi_rujukan
            from pasien p
            join penduduk pd on (p.id = pd.id)
            join pendaftaran pdf on (p.no_rm = pdf.pasien)
            join dinamis_penduduk dp on (pd.id = dp.penduduk_id)
            left join pekerjaan pk on (pk.id = dp.pekerjaan_id)
            left join pendidikan pdi on (pdi.id = dp.pendidikan_id)
            left join kelurahan kel on (kel.id = dp.kelurahan_id)
            left join kecamatan kec on (kec.id = kel.kecamatan_id)
            left join kelurahan kelj on (pdf.kelurahan_id_pjwb = kelj.id)
            left join kecamatan kecj on (kecj.id = kelj.kecamatan_id)
            left join relasi_instansi rl on (rl.id = pdf.rujukan_instansi_id)
            inner join (
                select pasien, max(no_daftar) as max_no_daftar
                from pendaftaran group by pasien
            ) pdfi on (pdf.pasien = pdfi.pasien and pdf.no_daftar = pdfi.max_no_daftar)
            where (p.no_rm like ('%$q%') or pd.nama like ('%$q%') )
            $w and pdf.waktu_keluar is null
            order by locate ('$q',p.no_rm) limit 0, 50";
        //echo $sql;
        $exe = $this->db->query($sql);
        return $exe->result();
    }

    function search_pendaftaran_penduduk($limit, $start){
        $norm   = get_safe('norm');
        $nama   = get_safe('nama');
        $alamat = get_safe('alamat');
        $q = NULL;
        if ($norm !== '') {
            $q.=" and ps.no_rm like ('%$norm%')";
        }
        if ($nama !== '') {
            $q.=" and pd.nama like '%$nama%'";
        }
        if ($alamat !== '') {
            $q.=" and dp.alamat like '%".$alamat."%'";
        }
        $limitation =" limit $start , $limit";
        $sql = "select p.*, pd.*, ps.no_rm, pd.id as id_penduduk, dp.* 
                from pendaftaran p
                join pasien ps on (ps.no_rm = p.pasien)
                join penduduk pd on (pd.id = ps.id)
                join dinamis_penduduk dp on (pd.id = dp.penduduk_id)
                left join kelurahan kel on (kel.id = dp.kelurahan_id)
                inner join (
                    select max(id) as id_max, penduduk_id from dinamis_penduduk group by penduduk_id
                ) dm on (dm.id_max = dp.id and dm.penduduk_id = dp.penduduk_id)
                where p.waktu_keluar is NULL $q
                order by pd.nama, dp.alamat ";
        //echo "<pre>".$sql."</pre>";
        $data['jumlah'] =  $this->db->query($sql)->num_rows();
        $data['data'] = $this->db->query($sql. $limitation)->result();
        return $data;
    }

    function detail_data_pasien($no_rm) {
        $w= '';
        $jenis = (isset($_GET['jenis']))?get_safe('jenis'):'';
        if($jenis != ''){
            $w = " and pdf.jenis_rawat = '$jenis'";
        }

        $sql = "select pdf.*,p.id as id_pasien,p.*, pd.nama, pdf.no_daftar,pd.gender,
            pk.nama as pekerjaan, pdi.nama as pendidikan, pd.*, kel.nama as kelurahan, dp.alamat,
             pd.darah_gol, kec.nama as kecamatan, kelj.nama as kelurahan_pj, kecj.nama as kecamatan_pj, rl.nama as instansi_rujukan
            from pasien p
            join penduduk pd on (p.id = pd.id)
            join pendaftaran pdf on (p.no_rm = pdf.pasien)
            join dinamis_penduduk dp on (pd.id = dp.penduduk_id)
            left join pekerjaan pk on (pk.id = dp.pekerjaan_id)
            left join pendidikan pdi on (pdi.id = dp.pendidikan_id)
            left join kelurahan kel on (kel.id = dp.kelurahan_id)
            left join kecamatan kec on (kec.id = kel.kecamatan_id)
            left join kelurahan kelj on (pdf.kelurahan_id_pjwb = kelj.id)
            left join kecamatan kecj on (kecj.id = kelj.kecamatan_id)
            left join relasi_instansi rl on (rl.id = pdf.rujukan_instansi_id)
            inner join (
                select penduduk_id, max(id) as id_max
                from dinamis_penduduk GROUP BY penduduk_id
            ) dpi on (dp.penduduk_id = dpi.penduduk_id and dp.id = dpi.id_max)
            inner join (
                select pasien, max(no_daftar) as max_no_daftar
                from pendaftaran group by pasien
            ) pdfi on (pdf.pasien = pdfi.pasien and pdf.no_daftar = pdfi.max_no_daftar)
            where p.no_rm ='$no_rm' $w and pdf.waktu_keluar is null";
        $exe = $this->db->query($sql);
        return $exe->row();
    }

    function get_no_kasus($no_rm, $id_diag){
        $sql = "select (count(dp.id)+1) as no from 
        diagnosa_pelayanan_kunjungan dp
        join pelayanan_kunjungan pk on (dp.id_pelayanan_kunjungan = pk.id)
        join pendaftaran pf on (pf.no_daftar = pk.id_kunjungan)
        where pf.pasien = '$no_rm' and dp.id_golongan_sebab_penyakit = '$id_diag'";
        return $this->db->query($sql)->row()->no;
    }
    
    function cek_data_pasien_on_pel_kunjungan($no_rm) {
        $sql = "select count(*) as jumlah, p.*, pk.id as id_pelayanan, pk.jenis from pendaftaran p
            join pelayanan_kunjungan pk on (p.no_daftar = pk.id_kunjungan)
            inner join (
                select pasien, max(no_daftar) as max_id from pendaftaran group by pasien
            ) pd on (pd.pasien = p.pasien and pd.max_id = p.no_daftar)
            where p.pasien = '$no_rm' order by p.no_daftar desc limit 1";
        return $this->db->query($sql);
    }
    
    function cek_ketersediaan_penjualan($id_resep) {
        $sql = "select count(*) as jumlah from penjualan where resep_id = '$id_resep'";
        $jml = $this->db->query($sql)->row();
        if ($jml->jumlah >= 1) {
            return FALSE;
        } else {
            $this->db->where('id', $id_resep);
            $this->db->delete('resep');
            return TRUE;
        }
    }
    
    function laboratorium_load_data($param) {
        $q = NULL;
        if (isset($param['id']) and $param['id'] !== '') {
            $q.=" and pl.id = '".$param['id']."'";
        }
        if (isset($param['id_pk']) and $param['id_pk'] !== '') {
            $q.=" and pk.id = '".$param['id_pk']."'";
        }
        if ((isset($param['awal']) and $param['awal'] !== '') and (isset($param['akhir']) and $param['akhir'] !== '')) {
            $q.=" and pl.waktu_order between '".$param['awal']."' and '".$param['akhir']."'";
        }
        if (isset($param['pasien']) and $param['pasien'] !== '') {
            $q.=" and p.pasien = '".$param['pasien']."'";
        }
        if (isset($param['dokter']) and $param['dokter'] !== '') {
            $q.=" and pl.id_kepegawaian_dokter_pemesan = '".$param['dokter']."'";
        }
        if (isset($param['pemeriksa']) and $param['pemeriksa'] !== '') {
            $q.=" and pl.id_kepegawaian_laboran = '".$param['pemeriksa']."'";
        }
        if (isset($param['layanan']) and $param['layanan'] !== '') {
            $q.=" and pl.id_layanan_lab = '".$param['layanan']."'";
        }
        $sql = "select pl.id_pelayanan_kunjungan, pl.id, pl.waktu_hasil, pl.hasil, 
            pl.ket_nilai_rujukan as ket, pdo.id as id_dokter, pdo.nama as dokter, dp.alamat, 
            kl.nama as kelurahan, kb.nama as kabupaten, kp.id as id_analis,
            pan.nama as analis, pl.waktu_order, p.pasien as no_rm, pdd.nama as pasien, 
            st.id as id_satuan, st.nama as satuan, u.nama as unit, pk.kelas, pk.no_tt,
            l.nama as layanan
            from pelayanan_kunjungan pk 
            join pemeriksaan_lab_pelayanan_kunjungan pl on (pk.id = pl.id_pelayanan_kunjungan)
            left join penduduk pdo on (pdo.id = pl.id_kepegawaian_dokter_pemesan)
            left join kepegawaian kp on (kp.id = pl.id_kepegawaian_analis_lab)
            left join penduduk pan on (pan.id = kp.penduduk_id)
            join pendaftaran p on (pk.id_kunjungan = p.no_daftar)
            join pasien ps on (p.pasien = ps.no_rm)
            join penduduk pdd on (pdd.id = ps.id)
            left join dinamis_penduduk dp on (dp.penduduk_id = pdd.id)
            left join kelurahan kl on (dp.kelurahan_id = kl.id)
            left join kecamatan kc on (kl.kecamatan_id = kc.id)
            left join kabupaten kb on (kc.kabupaten_id = kb.id)
            left join unit u on (pk.id_unit = u.id)
            left join satuan st on (pl.id_satuan = st.id)
            join layanan l on (pl.id_layanan_lab = l.id)
            inner join (
                select penduduk_id, max(id) as id_max from dinamis_penduduk group by penduduk_id
            ) dm on (dp.penduduk_id = dm.penduduk_id and dp.id = dm.id_max)
            where pk.id is not NULL $q";
        //echo $sql;
        return $this->db->query($sql);
    }

    function laboratorium_luar_load_data($param) {
        $q = NULL;
        if (isset($param['id']) and $param['id'] !== '') {
            $q.=" and pl.id = '".$param['id']."'";
        }
        if (isset($param['id_pk']) and $param['id_pk'] !== '') {
            $q.=" and pk.id = '".$param['id_pk']."'";
        }
        if ((isset($param['awal']) and $param['awal'] !== '') and (isset($param['akhir']) and $param['akhir'] !== '')) {
            $q.=" and pl.waktu_order between '".$param['awal']."' and '".$param['akhir']."'";
        }
        if (isset($param['pasien']) and $param['pasien'] !== '') {
            $q.=" and p.pasien = '".$param['pasien']."'";
        }
        if (isset($param['dokter']) and $param['dokter'] !== '') {
            $q.=" and pl.id_kepegawaian_dokter_pemesan = '".$param['dokter']."'";
        }
        if (isset($param['pemeriksa']) and $param['pemeriksa'] !== '') {
            $q.=" and pl.id_kepegawaian_laboran = '".$param['pemeriksa']."'";
        }
        if (isset($param['layanan']) and $param['layanan'] !== '') {
            $q.=" and pl.id_layanan_lab = '".$param['layanan']."'";
        }
        $sql = "select pl.id_pelayanan_kunjungan, pl.id, pl.waktu_hasil, pl.hasil, 
            pl.ket_nilai_rujukan as ket, pdo.id as id_dokter, pdo.nama as dokter, dp.alamat, 
            kl.nama as kelurahan, kb.nama as kabupaten, kp.id as id_analis,
            pan.nama as analis, pl.waktu_order, p.pasien as no_rm, pdd.nama as pasien, 
            st.id as id_satuan, st.nama as satuan, u.nama as unit, pk.kelas, pk.no_tt,
            l.nama as layanan
            from pelayanan_kunjungan pk 
            join pemeriksaan_lab_pelayanan_kunjungan pl on (pk.id = pl.id_pelayanan_kunjungan)
            left join penduduk pdo on (pdo.id = pl.id_kepegawaian_dokter_pemesan)
            left join kepegawaian kp on (kp.id = pl.id_kepegawaian_analis_lab)
            left join penduduk pan on (pan.id = kp.penduduk_id)
            join pendaftaran p on (pk.id_kunjungan = p.no_daftar)
            join penduduk pdd on (pdd.id = p.id_customer)
            left join dinamis_penduduk dp on (dp.penduduk_id = pdd.id)
            left join kelurahan kl on (dp.kelurahan_id = kl.id)
            left join kecamatan kc on (kl.kecamatan_id = kc.id)
            left join kabupaten kb on (kc.kabupaten_id = kb.id)
            left join unit u on (pk.id_unit = u.id)
            left join satuan st on (pl.id_satuan = st.id)
            join layanan l on (pl.id_layanan_lab = l.id)
            inner join (
                select penduduk_id, max(id) as id_max from dinamis_penduduk group by penduduk_id
            ) dm on (dp.penduduk_id = dm.penduduk_id and dp.id = dm.id_max)
            where pk.id is not NULL $q";
        //echo $sql;
        return $this->db->query($sql);
    }

    function radiologi_load_data($param) {
        $q = NULL;
        if (isset($param['id']) and $param['id'] !== '') {
            $q.=" and pl.id = '".$param['id']."'";
        }
        if (isset($param['id_pk']) and $param['id_pk'] !== '') {
            $q.=" and pk.id = '".$param['id_pk']."'";
        }
        if ((isset($param['awal']) and $param['awal'] !== '') and (isset($param['akhir']) and $param['akhir'] !== '')) {
            $q.=" and pl.waktu_order between '".$param['awal']."' and '".$param['akhir']."'";
        }
        if (isset($param['pasien']) and $param['pasien'] !== '') {
            $q.=" and p.pasien = '".$param['pasien']."'";
        }
        if (isset($param['dokter']) and $param['dokter'] !== '') {
            $q.=" and pl.id_kepegawaian_dokter_pemesan = '".$param['dokter']."'";
        }
        if (isset($param['pemeriksa']) and $param['pemeriksa'] !== '') {
            $q.=" and pl.id_kepegawaian_laboran = '".$param['pemeriksa']."'";
        }
        if (isset($param['layanan']) and $param['layanan'] !== '') {
            $q.=" and pl.id_layanan_lab = '".$param['layanan']."'";
        }
        $sql = "select pl.*, pdo.id as id_dokter, 
            pdo.nama as dokter, dp.alamat, kl.nama as kelurahan, kb.nama as kabupaten, 
            pan.nama as radiografer, p.pasien as no_rm, pdd.nama as pasien, 
            u.nama as unit, pk.kelas, pk.no_tt,
            l.nama as layanan
            from pelayanan_kunjungan pk 
            join pemeriksaan_radiologi_pelayanan_kunjungan pl on (pk.id = pl.id_pelayanan_kunjungan)
            left join penduduk pdo on (pdo.id = pl.id_kepegawaian_dokter_pemesan)
            left join kepegawaian kp on (kp.id = pl.id_kepegawaian_radiografer)
            left join penduduk pan on (pan.id = kp.penduduk_id)
            join pendaftaran p on (pk.id_kunjungan = p.no_daftar)
            join pasien ps on (p.pasien = ps.no_rm)
            join penduduk pdd on (pdd.id = ps.id)
            left join dinamis_penduduk dp on (dp.penduduk_id = pdd.id)
            left join kelurahan kl on (dp.kelurahan_id = kl.id)
            left join kecamatan kc on (kl.kecamatan_id = kc.id)
            left join kabupaten kb on (kc.kabupaten_id = kb.id)
            left join unit u on (pk.id_unit = u.id)
            join layanan l on (pl.id_layanan_radiologi = l.id)
            inner join (
                select penduduk_id, max(id) as id_max from dinamis_penduduk group by penduduk_id
            ) dm on (dp.penduduk_id = dm.penduduk_id and dp.id = dm.id_max)
            where pk.id is not NULL $q";
        //echo $sql;
        return $this->db->query($sql);
    }

    function radiologi_luar_load_data($param) {
        $q = NULL;
        if (isset($param['id']) and $param['id'] !== '') {
            $q.=" and pl.id = '".$param['id']."'";
        }
        if (isset($param['id_pk']) and $param['id_pk'] !== '') {
            $q.=" and pk.id = '".$param['id_pk']."'";
        }
        if ((isset($param['awal']) and $param['awal'] !== '') and (isset($param['akhir']) and $param['akhir'] !== '')) {
            $q.=" and pl.waktu_order between '".$param['awal']."' and '".$param['akhir']."'";
        }
        if (isset($param['pasien']) and $param['pasien'] !== '') {
            $q.=" and p.pasien = '".$param['pasien']."'";
        }
        if (isset($param['dokter']) and $param['dokter'] !== '') {
            $q.=" and pl.id_kepegawaian_dokter_pemesan = '".$param['dokter']."'";
        }
        if (isset($param['pemeriksa']) and $param['pemeriksa'] !== '') {
            $q.=" and pl.id_kepegawaian_laboran = '".$param['pemeriksa']."'";
        }
        if (isset($param['layanan']) and $param['layanan'] !== '') {
            $q.=" and pl.id_layanan_lab = '".$param['layanan']."'";
        }
        $sql = "select pl.*, pdo.id as id_dokter, 
            pdo.nama as dokter, dp.alamat, kl.nama as kelurahan, kb.nama as kabupaten, 
            pan.nama as radiografer, p.pasien as no_rm, pdd.nama as pasien, 
            u.nama as unit, pk.kelas, pk.no_tt,
            l.nama as layanan
            from pelayanan_kunjungan pk 
            join pemeriksaan_radiologi_pelayanan_kunjungan pl on (pk.id = pl.id_pelayanan_kunjungan)
            left join penduduk pdo on (pdo.id = pl.id_kepegawaian_dokter_pemesan)
            left join kepegawaian kp on (kp.id = pl.id_kepegawaian_radiografer)
            left join penduduk pan on (pan.id = kp.penduduk_id)
            join pendaftaran p on (pk.id_kunjungan = p.no_daftar)
            join penduduk pdd on (pdd.id = p.id_customer)
            left join dinamis_penduduk dp on (dp.penduduk_id = pdd.id)
            left join kelurahan kl on (dp.kelurahan_id = kl.id)
            left join kecamatan kc on (kl.kecamatan_id = kc.id)
            left join kabupaten kb on (kc.kabupaten_id = kb.id)
            left join unit u on (pk.id_unit = u.id)
            join layanan l on (pl.id_layanan_radiologi = l.id)
            inner join (
                select penduduk_id, max(id) as id_max from dinamis_penduduk group by penduduk_id
            ) dm on (dp.penduduk_id = dm.penduduk_id and dp.id = dm.id_max)
            where pk.id is not NULL $q";
        //echo $sql;
        return $this->db->query($sql);
    }

    function fisioterapi_load_data($param) {
        $q = NULL;
        if (isset($param['id']) and $param['id'] !== '') {
            $q.=" and pl.id = '".$param['id']."'";
        }
        if (isset($param['id_pk']) and $param['id_pk'] !== '') {
            $q.=" and pk.id = '".$param['id_pk']."'";
        }
        if ((isset($param['awal']) and $param['awal'] !== '') and (isset($param['akhir']) and $param['akhir'] !== '')) {
            $q.=" and pl.waktu_order between '".$param['awal']."' and '".$param['akhir']."'";
        }
        if (isset($param['pasien']) and $param['pasien'] !== '') {
            $q.=" and p.pasien = '".$param['pasien']."'";
        }
        if (isset($param['dokter']) and $param['dokter'] !== '') {
            $q.=" and pl.id_kepegawaian_dokter_pemesan = '".$param['dokter']."'";
        }
        if (isset($param['pemeriksa']) and $param['pemeriksa'] !== '') {
            $q.=" and pl.id_kepegawaian_laboran = '".$param['pemeriksa']."'";
        }
        if (isset($param['layanan']) and $param['layanan'] !== '') {
            $q.=" and pl.id_layanan_lab = '".$param['layanan']."'";
        }
        $sql = "select pl.*, k.id as id_dokter, 
            pdo.nama as operator, dp.alamat, kl.nama as kelurahan, kb.nama as kabupaten, 
            pan.nama as anestesi, p.pasien as no_rm, pdd.nama as pasien, 
            u.nama as unit, pk.kelas, pk.no_tt,
            l.nama as layanan, l.kode_icdixcm
            from pelayanan_kunjungan pk 
            join tindakan_pelayanan_kunjungan pl on (pk.id = pl.id_pelayanan_kunjungan)
            join kepegawaian k on (k.id = pl.id_kepegawaian_nakes_operator)
            left join penduduk pdo on (pdo.id = k.penduduk_id)
            left join kepegawaian kp on (kp.id = pl.id_kepegawaian_nakes_anesthesi)
            left join penduduk pan on (pan.id = kp.penduduk_id)
            join pendaftaran p on (pk.id_kunjungan = p.no_daftar)
            join pasien ps on (p.pasien = ps.no_rm)
            join penduduk pdd on (pdd.id = ps.id)
            left join dinamis_penduduk dp on (dp.penduduk_id = pdd.id)
            left join kelurahan kl on (dp.kelurahan_id = kl.id)
            left join kecamatan kc on (kl.kecamatan_id = kc.id)
            left join kabupaten kb on (kc.kabupaten_id = kb.id)
            left join unit u on (pl.id_unit_penunjang = u.id)
            join layanan l on (pl.id_layanan = l.id)
            inner join (
                select penduduk_id, max(id) as id_max from dinamis_penduduk group by penduduk_id
            ) dm on (dp.penduduk_id = dm.penduduk_id and dp.id = dm.id_max)
            where pk.id is not NULL $q";
        //echo $sql;
        return $this->db->query($sql);
    }
    
    function delete_data_lab($id_pl) {
        $this->db->delete('pemeriksaan_lab_pelayanan_kunjungan', array('id' => $id_pl));
        die(json_encode(TRUE));
    }
    
    function load_data_master_satuan() {
        $sql = "select * from satuan order by nama";
        return $this->db->query($sql);
    }
    
    function pemeriksaan_lab_update() {
        $array = array(
            'waktu_hasil' => (post_safe('waktu_hasil') != '')?datetime2mysql(post_safe('waktu_hasil')):NULL,
            'hasil' => post_safe('hasil'),
            'ket_nilai_rujukan' => post_safe('nilai'),
            'id_satuan' => post_safe('satuan')
        );

        if(isset($_POST['id_analis']) && ($_POST['id_analis'] !== '') ){
            $array['id_kepegawaian_analis_lab'] = post_safe('id_analis');
        }

        $this->db->where('id', post_safe('id_pemeriksaan_lab'));
        $this->db->update('pemeriksaan_lab_pelayanan_kunjungan', $array);
        return TRUE;
    }

    function get_pemeriksaan_radiologi($id_pk){
        $sql = "select pl.*, pdo.nama as dokter, pan.nama as radiografer,  
                l.nama as layanan 
                from pemeriksaan_radiologi_pelayanan_kunjungan pl  
                left join penduduk pdo on (pdo.id = pl.id_kepegawaian_dokter_pemesan)
                left join kepegawaian kp2 on (kp2.id = pl.id_kepegawaian_radiografer)
                left join penduduk pan on (pan.id = kp2.penduduk_id)
                left join layanan l on (l.id = pl.id_layanan_radiologi) 
                where pl.id_pelayanan_kunjungan = '$id_pk'";
        //echo $sql;
        return $this->db->query($sql)->result();
    }


    function get_jumlah_layanan_irna(){
        /*
        $sql = "select count(t.id) as jumlah from tarif t
                join layanan l on (t.id_layanan = l.id)
                where l.nama like 'Sewa Kamar' "; */
        $sql = "select * from tt";
        return $this->db->query($sql)->num_rows();
    }

    function get_jumlah_pasien_irna($param, $ndr = null){
        $q = "";
        if(($param['awal'] != '') && ($param['akhir'] != '') ){
            $q .= " and p.tgl_daftar between  '".date2mysql($param['awal'])." 00:00' and '".date2mysql($param['akhir'])." 23:59' ";
        }

        if($param['kondisi'] != ''){
            $q .= " and p.kondisi_keluar = '".$param['kondisi']."' ";
        }

        if($ndr != null){
            $q  .= " and TIMESTAMPDIFF(DAY, p.tgl_daftar, p.waktu_keluar) >= '2'";
        }

        $sql = "select sum(TIMESTAMPDIFF(DAY, p.tgl_daftar, p.waktu_keluar)) as lama_inap , count(p.no_daftar) as jumlah
                from pendaftaran p 
                join pelayanan_kunjungan pk on (pk.id_kunjungan = p.no_daftar)
                where pk.jenis = 'Rawat Inap' and p.waktu_keluar is not null $q";
        //echo $sql."<br/><br/>";
        return $this->db->query($sql)->row();
    }

    function get_ratarata_kunjungan_pasien($param){
        $q = "";
        if(($param['awal'] != '') && ($param['akhir'] != '') ){
            $q .= " and p.tgl_layan between '".date2mysql($param['awal'])."' and '".date2mysql($param['akhir'])."' ";
            $hari = sizeof(createRange(date2mysql($param['awal']), date2mysql($param['akhir'])));
        }

        $sql = "select count(*) as jumlah from pendaftaran p
                where p.pasien is not null $q";
        //echo $sql."<br/>";
        $jumlah = $this->db->query($sql)->row()->jumlah;
        return (int)$jumlah ;
    }

    function get_jumlah_hari_perawatan($param){
        $hp = 0;
        $dates = array();
        if(($param['awal'] != '') && ($param['akhir'] != '') ){
            $dates = createRange(date2mysql($param['awal']), date2mysql($param['akhir']));

        }

        foreach ($dates as $key => $date) {
            $sql = "select count(*) as jml  from inap_rawat_kunjungan ir 
                    where '$date' between date(ir.masuk_waktu) and  
                    date(if(ir.keluar_waktu is null, date(now()), ir.keluar_waktu))";
            //echo $sql."<br/>";
            $hp += $this->db->query($sql)->row()->jml;
        }

        return array('hari' => $hp, 'periode'=>sizeof($dates));
    }

    function rekap_profil_pasien($param){
        $q = "";
        if(($param['awal'] != '') && ($param['akhir'] != '') ){
            $q .= " where p.tgl_daftar between  '".$param['awal']." 00:00' and '".$param['akhir']." 23:59' ";
        }

        $sql = "select p.no_daftar, p.tgl_layan, ps.no_rm, pd.nama, 
                null as asuransi,null as diagnosis, null as tindakan,
                null as resep,
                null as total_biaya,
                null as los
                from pendaftaran p
                join pasien ps on (p.pasien = ps.no_rm)
                join penduduk pd on (ps.id = pd.id) $q";
       // echo "<pre>".$sql."</pre>";
        return $this->db->query($sql)->result();

    }

    function get_durasi_inap_pasien($no_daftar){
        $sql = "select sum(ceiling(if((TIMESTAMPDIFF(HOUR, i.masuk_waktu, i.keluar_waktu)/24)=0, 1,  (TIMESTAMPDIFF(HOUR, i.masuk_waktu, i.keluar_waktu)/24))))
                as total_inap
                from inap_rawat_kunjungan i 
                join pelayanan_kunjungan pk on (pk.id = i.id_pelayanan_kunjungan)
                where pk.id_kunjungan =  '$no_daftar'";

        return $this->db->query($sql)->row()->total_inap;
    }

    function get_resep_pelayanan_kunjungan($id_pk){
        $sql = "select b.nama as barang, b.kekuatan, st.nama as satuan, sd.nama as sediaan, dp.qty as resep_r_jumlah,
                (b.hna+(b.hna*(b.margin_resep/100))) as jual_harga, 
                dp.qty as dosis_racik
                from penjualan p
                join detail_penjualan dp on (p.id = dp.id_penjualan)
                join kemasan k on (dp.id_kemasan = k.id)
                join satuan s on (k.id_kemasan = s.id)
                join barang b on (k.id_barang = b.id)
                left join satuan st on (st.id = b.satuan_kekuatan)
                left join sediaan sd on (b.id_sediaan = sd.id)
                where p.id_pelayanan_kunjungan = '$id_pk'";
       // echo "<pre>".$sql."</pre>";
        return $this->db->query($sql)->result();
    }

    function load_data_morbiditas($param){
        $q = "";
        $join = "";
        if( ($param['awal'] != NULL) & ($param['akhir'] != NULL) ){
            $q .= " and do.waktu between '".$param['awal']."' and '".$param['akhir']."' ";
        }

        if($param['unit'] == "Rawat Inap"){
            $join = " join pelayanan_kunjungan pk on(do.id_pelayanan_kunjungan = pk.id)";
            $q .= " and pk.jenis = 'Rawat Inap' ";
        }else if( ($param['unit'] == 'Poliklinik') | ($param['unit'] == 'IGD') ){
            $join = " join pelayanan_kunjungan pk on(do.id_pelayanan_kunjungan = pk.id)
                      join pendaftaran p on (p.no_daftar = pk.id_kunjungan)";
            $q .= " and p.jenis_rawat = '".$param['unit']."' ";
        }

         $sql = "select count(*) as jumlah,  g.nama from diagnosa_pelayanan_kunjungan do
                join golongan_sebab_sakit g on(g.id = do.id_golongan_sebab_penyakit) $join
                where  do.id is not null $q 
                group by g.id order by jumlah desc limit 0, 10 ";
         // echo $sql;

        return $this->db->query($sql)->result();

    }

  

    function get_available_bed($id_bangsal, $kelas){
        $q = '';

        if ($id_bangsal !== '') {
            $q .= " and tt.unit_id = '$id_bangsal'";
        }

        if ($kelas !== '') {
            $q .= " and tt.kelas = '$kelas'";
        }
        $sql = "select tt.id, u.id as id_unit, u.nama, tt.kelas, tt.nomor , t.id as id_tarif from tt 
                join unit u on (tt.unit_id = u.id)
                join tarif t on(tt.unit_id = t.id_unit and tt.kelas = t.kelas)
                join layanan l on (l.id = t.id_layanan)
                where
                tt.status = 'Tersedia'
                and t.id_barang_sewa is null 
                and l.nama = 'Sewa Kamar' $q
                order by u.nama, tt.kelas, tt.nomor asc";
        //echo $sql;
        return $this->db->query($sql)->result();
    }
    
    function print_no_receipt($id_resep) {
        $sql = "select pd.nama, r.id as no_resep, pk.jenis_pelayanan, ps.no_rm,  u.nama as unit, pdd.nama as pasien from resep r
            join penduduk pd on (r.dokter_penduduk_id = pd.id)
            join pelayanan_kunjungan pk on (r.id_pelayanan_kunjungan = pk.id)
            join unit u on (pk.id_unit = u.id)
            join pendaftaran pdf on (pk.id_kunjungan = pdf.no_daftar)
            join pasien ps on (pdf.pasien = ps.no_rm)
            join penduduk pdd on (ps.id = pdd.id)
            where r.id = '$id_resep'";
        return $this->db->query($sql);
    }

    function get_rl31_data($tahun = null){
        if ($tahun == null) {
            $tahun = date('Y');
        }
        $sql = "select jk.nama as jenis_layanan, count(*) as jumlah,
                (select count(*) from pelayanan_kunjungan
                    where jenis = 'Rawat Inap' and kelas = 'VVIP'
                    and id_jurusan_kualifikasi_pendidikan = jk.id
                ) as vvip
                from pelayanan_kunjungan pk 
                join jurusan_kualifikasi_pendidikan jk on (jk.id = pk.id_jurusan_kualifikasi_pendidikan)
                inner join (
                    select max(id) as id_max, id_kunjungan from pelayanan_kunjungan 
                    group by id_kunjungan
                    ) pki on (pki.id_kunjungan = pk.id_kunjungan and pki.id_max = pk.id)
                where pk.jenis = 'Rawat Inap' and year(pk.waktu) = '$tahun'
                group by jk.id";
        //echo $sql;
        return $this->db->query($sql)->result();
    }

    function save_bor_harian(){
        
        $sql_unit = "select u.id from unit u 
                join tt on (tt.unit_id = u.id)
                group by u.id";
        $unit = array();
        $query = $this->db->query($sql_unit)->result(); 
        foreach ($query as $k => $v) {
            $unit[] = $v->id;
        }

        $unit_bor = array();

        // cek tanggal terakhir bor disimpan
        $last_date = $this->db->query("select max(tanggal) as tanggal from bor_rawat_inap")->row()->tanggal;
        $arr_date = array();
        if ($last_date !== null) {
            $date_start = new DateTime($last_date);
            $date_start->modify('+1 day');           
        }else{
            $date_start = new DateTime(date('2014-01-17'));
            $date_start->modify('+1 day');  
        }

        $date = new DateTime(date('Y-m-d'));
        $date->modify('-1 day');
        $arr_date = createRange($date_start->format('Y-m-d'), $date->format('Y-m-d'));

        foreach ($arr_date as $key => $value) {
            //echo $value."<br/>";
            $cek = $this->db->where('tanggal', $value)->get('bor_rawat_inap')->num_rows();
            if(($cek < 1) & ($value != date('Y-m-d'))){
            $sql = "select ir.masuk_waktu, ir.keluar_waktu,u.id as id_unit, u.nama, t.kelas ,
                (select count(*)  from inap_rawat_kunjungan i 
                join tarif tr on (i.id_tarif = tr.id) where tr.id_unit = u.id and tr.kelas = 'VVIP') as hari_vvip,

                (select count(*) from inap_rawat_kunjungan i 
                join tarif tr on (i.id_tarif = tr.id) where tr.id_unit = u.id and tr.kelas = 'VIP') as hari_vip,

                (select count(*) from inap_rawat_kunjungan i 
                join tarif tr on (i.id_tarif = tr.id) where tr.id_unit = u.id and tr.kelas = 'I') as hari_i,

                (select count(*) from inap_rawat_kunjungan i 
                join tarif tr on (i.id_tarif = tr.id) where tr.id_unit = u.id and tr.kelas = 'II') as hari_ii,

                (select count(*) from inap_rawat_kunjungan i 
                join tarif tr on (i.id_tarif = tr.id) where tr.id_unit = u.id and tr.kelas = 'III') as hari_iii

                from inap_rawat_kunjungan ir 
                join tarif t on (t.id = ir.id_tarif)
                join unit u on (u.id = t.id_unit)
                where '".$value."' between date(ir.masuk_waktu) 
                and date(if(ir.keluar_waktu is null, date(now()), ir.keluar_waktu))
                group by u.id";

                $bor = $this->db->query($sql)->result();
                foreach ($bor as $key2 => $val2) {
                    $insert = array(
                            'tanggal' => $value,
                            'id_unit' => $val2->id_unit,
                            'hari_vvip' => $val2->hari_vvip,
                            'hari_vip' => $val2->hari_vip,
                            'hari_i' => $val2->hari_i,
                            'hari_ii' => $val2->hari_ii,
                            'hari_iii' => $val2->hari_iii,
                        );

                    $this->db->insert('bor_rawat_inap', $insert);
                    $unit_bor[] = $val2->id_unit; 
                }

                $arr_dif = array_diff($unit, $unit_bor);
                foreach ($arr_dif as $key3 => $val3) {                   
                        $insert = array(
                            'tanggal' => $value,
                            'id_unit' => $val3,
                            'hari_vvip' => 0,
                            'hari_vip' => 0,
                            'hari_i' => 0,
                            'hari_ii' => 0,
                            'hari_iii' => 0,
                        );

                    $this->db->insert('bor_rawat_inap', $insert);
                    
                }
                
            }
            //echo print_r($unit_bor)."<br/><br/><br/>";
            unset($unit_bor);
        }
         
    }

    function bor_rawat_inap_data($limit = null, $start = null, $search = null) {
        $q = "";

        if (($search['awal'] != '') & ($search['akhir'] != '')){
            $q .= " and tanggal between '".$search['awal']."'  and '".$search['akhir']."' ";
        }
        

        if ($search['unit'] != '') {
            $q .= " and u.id = '".$search['unit']."'";
        }

    
        $page = "  limit $start ,$limit";

        $sql = "select u.nama, b.* from bor_rawat_inap b
                join unit u on (u.id = b.id_unit)
                where b.id is not null $q
                order by tanggal, u.nama";

        $query = $this->db->query($sql . $page);
        $ret['data'] = $query->result();
        $ret['jumlah'] = $this->db->query($sql)->num_rows();
        return $ret;
    }
    
    function get_data_resep($limit, $start, $search) {
        $q = NULL;
        if ($search['id'] !== '') {
            $q.="and r.id = '".$search['id']."' ";
        }
        if (isset($search['awal'])) {
            $q.=" and date(r.waktu) between '".$search['awal']."' and '".$search['akhir']."'";
        } else {
            $q.=" and date(r.waktu) = '".date("Y-m-d")."'";
        }
        $sql   = "select r.*, rr.id_jasa_apoteker as id_rr, rr.id_resep, rr.id_barang, rr.id_jasa_apoteker, rr.r_no, concat_ws(' ',b.nama, b.kekuatan, s.nama) as nama_barang, 
            rr.dosis_racik, rr.jumlah_pakai, rr.jual_harga, d.nama as dokter, k.nama as apoteker, t.nama as tarif, pdk.nama as pasien, d.lahir_tanggal as tanggal_lahir, b.kekuatan,
            rr.resep_r_jumlah, sd.nama as sediaan, concat_ws(' ',rr.pakai, ' X ', rr.aturan) as pakai_aturan,
            rr.tebus_r_jumlah, rr.pakai, rr.aturan, rr.iter, rr.nominal, t.id as id_tarif 
            from resep r
            join resep_r rr on (r.id = rr.id_resep)
            join barang b on (b.id = rr.id_barang)
            left join satuan s on (b.satuan_kekuatan = s.id)
            left join jasa_apoteker t on (t.id = rr.id_jasa_apoteker)
            left join pasien p on (p.id = r.id_pasien)
            left join penduduk d on (d.id = r.id_penduduk_dokter)
            left join penduduk pdk on (p.id = pdk.id)
            left join sediaan sd on (sd.id = b.id_sediaan)
            left join users u on (r.id_users = u.id)
            left join penduduk k on (k.id = u.id)
            where r.id is not NULL $q order by r.waktu desc, rr.r_no asc
        ";
        $limitation = null;
        $limitation.=" limit $start , $limit";
        $query = $this->db->query($sql . $limitation);
        //echo $sql . $q . $limitation;
        $queryAll = $this->db->query($sql);
        $data['data'] = $query->result();
        $data['jumlah'] = $queryAll->num_rows();
        return $data;
    }
    
    function save_resep() {
        $this->db->trans_begin();
        $noresep    = $_POST['noresep'];
        $waktu      = date2mysql($_POST['waktu']).' '.date("H:i:s");
        $dokter     = $_POST['id_dokter'];
        $pasien     = $_POST['id_pasien'];
        $keterangan = $_POST['keterangan'];
        $id_resep   = $_POST['id_resep'];
        $id_kp      = $_POST['id_kp'];
        $totallica  = $_POST['totallica'];

        $id_user    = $this->session->userdata('id_user');
        if ($id_resep === '') {
            $sql = "insert into resep set
                id = '$noresep',
                waktu = '$waktu',
                id_penduduk_dokter = '$dokter',
                id_pasien = '$pasien',
                id_kunjungan_pelayanan = '$id_kp',
                keterangan = '$keterangan',
                id_users = $id_user";
            $this->db->query($sql);
            $id = $noresep;
            $result['action'] = 'add';
        } else {
            $sql = "update resep set 
                waktu = '$waktu',
                id_penduduk_dokter = '$dokter',
                id_pasien = '$pasien',
                keterangan = '$keterangan',
                id_users = $id_user
                where id = '$id_resep'";
            $this->db->query($sql);
            $id = $id_resep;
            $this->db->query("delete from resep_r where id_resep = '$id'");
            $get = $this->db->query("select id from penjualan where id_resep = '$id_resep'")->row();
            $this->db->query("delete from detail_penjualan where id_penjualan = '".$get->id."'");
            $this->db->query("delete from stok where id_transaksi = '".$get->id."' and transaksi = 'Penjualan'");
            $result['action'] = 'edit';
        }
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $result['status'] = FALSE;
        }

        $no_r       = $_POST['no_r'];
        $id_barang  = $_POST['id_barang'];
        $jml_minta  = $_POST['jp'];
        $jml_tebus  = $_POST['jt'];
        $aturan     = $_POST['a'];
        $pakai      = $_POST['p'];
        $iterasi    = $_POST['it'];
        //$kekuatan   = $_POST['kekuatan'];
        $dosis_racik= $_POST['dr'];
        $jml_pakai  = $_POST['jpi'];
        $id_tarif   = $_POST['id_tarif'];
        $jasa_apt   = $_POST['jasa'];
        $harga_brg  = $_POST['hrg_barang'];
        $tuslah     = 0;
        foreach ($no_r as $arr => $data) {
            $query = "insert into resep_r set
                id_resep = '$id',
                r_no = '$data',
                resep_r_jumlah = '$jml_minta[$arr]',
                tebus_r_jumlah = '$jml_tebus[$arr]',
                aturan = '$aturan[$arr]',
                pakai = '$pakai[$arr]',
                iter = '$iterasi[$arr]',
                id_jasa_apoteker = ".((($id_tarif[$arr] !== '0') and $id_tarif[$arr] !== '')?$id_tarif[$arr]:'NULL').",
                nominal = '".  currencyToNumber($jasa_apt[$arr])."',
                id_barang = '$id_barang[$arr]',
                jual_harga = '".  currencyToNumber($harga_brg[$arr])."',
                dosis_racik = '$dosis_racik[$arr]',
                jumlah_pakai = '$jml_pakai[$arr]'
                ";
            //echo $query."<br/>";
            $tuslah = $tuslah+currencyToNumber($jasa_apt[$arr]);
            $this->db->query($query);
        }
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $result['status'] = FALSE;
        }
        if ($id_resep === '') {
            $query2= "insert into penjualan set
                    id_pelayanan_kunjungan = '$id_kp',
                    waktu = NOW(),
                    id_resep = '$id',
                    id_pasien = '$pasien',
                    diskon_persen = '0',
                    diskon_rupiah = '0',
                    ppn = '0',
                    total = '$totallica',
                    tuslah = '$tuslah',
                    bayar = '$totallica'";
            $this->db->query($query2);
            $id_penjualan = $this->db->insert_id();
        } else {
            $query2= "update penjualan set
                    id_pelayanan_kunjungan = '$id_kp',
                    waktu = NOW(),
                    id_resep = '$id',
                    id_pasien = '$pasien',
                    diskon_persen = '0',
                    diskon_rupiah = '0',
                    ppn = '0',
                    total = '$totallica',
                    tuslah = '$tuslah',
                    bayar = '$totallica'
                    where id_resep = '$id_resep'";
            $this->db->query($query2);
            $id_penjualan = $get->id;
        }
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $result['status'] = FALSE;
        }
        foreach ($no_r as $key => $data) {
            $barang = $this->db->query("select b.*, k.id as id_kemasan, sum(s.masuk)-sum(s.keluar) as sisa, s.ed
                from barang b 
                join kemasan k on (b.id = k.id_barang) 
                left join stok s on (b.id = s.id_barang)
                where k.id_barang = '".$id_barang[$key]."' and k.default_kemasan = '1' group by s.ed order by s.ed asc")->row();
            
            $harga = round($barang->hna+($barang->hna*($barang->margin_resep/100)));
            $query3 = "insert into detail_penjualan set
                id_penjualan = '$id_penjualan',
                id_kemasan = '".$barang->id_kemasan."',
                expired = '".$barang->ed."',
                qty = '$jml_tebus[$key]',
                hna = '".$barang->hna."',
                harga_jual = '$harga'";
            $this->db->query($query3);
            
            $query4 = "insert into stok set
                waktu = NOW(),
                id_transaksi = '$id_penjualan',
                transaksi = 'Penjualan',
                id_barang = '".$barang->id."',
                ed = '".$barang->ed."',
                keluar = '$jml_tebus[$key]',
                id_users = '".$this->session->userdata('id_user')."',
                id_unit = '".$this->session->userdata('id_unit')."'";
            $this->db->query($query4);
        }
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $result['status'] = FALSE;
        } else {
            $this->db->trans_commit();
            $result['status'] = TRUE;
            $result['id'] = $id;
        }
        die(json_encode($result));
    }
    
    function delete_resep($id) {
        $this->db->delete('resep', array('id' => $id));
    }
    
    /*PENJUALAN RESEP*/
    function get_data_penjualan($limit, $start, $param) {
        $q = NULL;
        if ($param['id'] !== '') {
            $q.=" and p.id = '".$param['id']."' ";
        } 
        if (isset($param['pasien']) and $param['pasien'] !== '') {
            $q.=" and p.id_pelanggan = '".$param['pasien']."'";
        }
        if (isset($param['dokter']) and $param['dokter'] !== '') {
            $q.=" and r.id_dokter = '".$param['dokter']."'";
        }
        if (isset($param['laporan']) and $param['laporan'] !== 'detail') {
            $q.=" and date(p.waktu) between '".$param['awal']."' and '".$param['akhir']."'";
            $q.=" group by p.id";
        } else if (isset($param['laporan']) and $param['laporan'] === 'detail') {
            $q.=" and date(p.waktu) between '".$param['awal']."' and '".$param['akhir']."'";
        } else {
            $q.=" and date(p.waktu) between '".date("Y-m-d")."' and '".date("Y-m-d")."'";
            //$limit = " limit ".$param['start'].", ".$param['limit']."";
        }

        $sql = "select p.*, date(p.waktu) as tanggal, d.nama as customer, d.id as id_customer, a.nama as asuransi, d.nama as dokter,
            concat_ws(' ',b.nama,b.kekuatan,s.nama) as nama_barang, st.nama as kemasan, dp.qty, dp.harga_jual, (dp.harga_jual*dp.qty) as subtotal
            from penjualan p
            join detail_penjualan dp on (p.id = dp.id_penjualan)
            join kemasan k on (k.id = dp.id_kemasan)
            join barang b on (k.id_barang = b.id)
            left join satuan s on (b.satuan_kekuatan = s.id)
            left join satuan st on (k.id_kemasan = st.id)
            left join pelayanan_kunjungan pk on (pk.id = p.id_pelayanan_kunjungan)
            join pendaftaran pdf on (pdf.no_daftar = pk.id_kunjungan)
            left join pasien pl on (pl.no_rm = pdf.pasien)
            left join asuransi_produk a on (pk.id_produk_asuransi = a.id) 
            join resep r on (p.id_resep = r.id)
            left join kepegawaian kp on (pk.id_kepegawaian_dpjp = kp.id)
            join penduduk d on (d.id = kp.penduduk_id)
            where p.id_resep is not NULL $q order by p.waktu desc";
        $limitation = null;
        $limitation.=" limit $start , $limit";
        $query = $this->db->query($sql . $limitation);
        //echo $sql . $q . $limitation;
        $queryAll = $this->db->query($sql);
        $data['data'] = $query->result();
        $data['jumlah'] = $queryAll->num_rows();
        return $data;
    }
    
    function get_detail_resep($id) {
        $sql = "select r.*, IFNULL(pj.total,'0') as total_tagihan, IFNULL(sum(dp.bayar),'0') as terbayar, p.nama, p.id_asuransi, a.diskon as reimburse from resep r 
            join pelanggan p on (r.id_pasien = p.id)
            left join penjualan pj on (r.id = pj.id_resep)
            left join detail_bayar_penjualan dp on (pj.id = dp.id_penjualan)
            left join asuransi a on (p.id_asuransi = a.id)
            where r.id = '$id'";
        return $this->db->query($sql);
    }
    
    function get_list_data_kitir($id_resep) {
        $sql = "select p.*, date(p.waktu) as tanggal, d.nama as pasien, d.id as id_customer, d.nama as dokter,
            concat_ws(' ',b.nama,b.kekuatan,s.nama) as nama_barang, st.nama as kemasan, dp.qty, dp.harga_jual, (dp.harga_jual*dp.qty) as subtotal
            from penjualan p
            join detail_penjualan dp on (p.id = dp.id_penjualan)
            join kemasan k on (k.id = dp.id_kemasan)
            join barang b on (k.id_barang = b.id)
            left join satuan s on (b.satuan_kekuatan = s.id)
            left join satuan st on (k.id_kemasan = st.id)
            left join pelayanan_kunjungan pk on (pk.id = p.id_pelayanan_kunjungan)
            join pendaftaran pdf on (pdf.no_daftar = pk.id_kunjungan)
            left join pasien pl on (pl.no_rm = pdf.pasien)
            join resep r on (p.id_resep = r.id)
            left join kepegawaian kp on (pk.id_kepegawaian_dpjp = kp.id)
            join penduduk d on (d.id = pl.id)
            where p.id_resep = '$id_resep' order by p.waktu desc";
        //echo $sql;
        return $this->db->query($sql);
    }
    
    function get_data_resep_penjualan($id_resep) {
        $sql   = "select r.*, rr.id as id_rr, rr.id_resep, rr.id_barang, rr.id_tarif, rr.r_no, concat_ws(' ',b.nama, b.kekuatan, s.nama) as nama_barang, 
            rr.dosis_racik, rr.jumlah_pakai, rr.jual_harga, d.nama as dokter, k.nama as apoteker, t.nama as tarif, p.nama as pasien, p.tanggal_lahir, b.kekuatan,
            rr.resep_r_jumlah, sd.nama as sediaan, concat_ws(' ',rr.pakai, ' X ', rr.aturan) as pakai_aturan, d.no_str as sip_no, d.alamat as alamat_dokter,
            rr.tebus_r_jumlah, rr.pakai, rr.aturan, rr.iter, rr.nominal 
            from resep r
            join resep_r rr on (r.id = rr.id_resep)
            join barang b on (b.id = rr.id_barang)
            left join satuan s on (b.satuan_kekuatan = s.id)
            left join tarif t on (t.id = rr.id_tarif)
            left join pelanggan p on (p.id = r.id_pasien)
            left join dokter d on (d.id = r.id_dokter)
            left join sediaan sd on (sd.id = b.id_sediaan)
            left join users u on (r.id_users = u.id)
            left join karyawan k on (k.id = u.id_karyawan)
            where r.id = '$id_resep' order by r.waktu desc, rr.r_no asc
        ";
        return $this->db->query($sql);
    }
    
    function cek_ketersediaan_resep($id) {
        $sql = "select p.*, sum(db.bayar) as terbayar, (p.total-sum(db.bayar)) as sisa from penjualan p 
            join detail_bayar_penjualan db on (p.id = db.id_penjualan) 
            where p.id_resep = '$id' group by db.id_penjualan";
        return $this->db->query($sql);
    }
    
    function get_data_penjualan_edit($id) {
        $sql = mysql_query("select p.*, date(p.waktu) as tanggal, pl.nama as customer, pl.id as id_customer, a.nama as asuransi, d.nama as dokter, 
            dp.id_kemasan, b.id as id_barang,
            concat_ws(' ',b.nama,b.kekuatan,s.nama) as nama_barang, st.nama as kemasan, dp.qty, dp.harga_jual, dp.disc_pr, dp.disc_rp, (dp.harga_jual*dp.qty) as subtotal,
            dp.expired
            from penjualan p
            join detail_penjualan dp on (p.id = dp.id_penjualan)
            join kemasan k on (k.id = dp.id_kemasan)
            join barang b on (k.id_barang = b.id)
            left join satuan s on (b.satuan_kekuatan = s.id)
            left join satuan st on (k.id_kemasan = st.id)
            left join pelanggan pl on (p.id_pelanggan = pl.id)
            left join asuransi a on (pl.id_asuransi = a.id) 
            left join resep r on (p.id_resep = r.id)
            left join dokter d on (r.id_dokter = d.id)
            where p.id = '$id' order by p.waktu desc");
        return $this->db->query($sql);
    }
    
    function save_penjualan() {
        $this->db->trans_begin();
        $tanggal    = date2mysql($_POST['tanggal']).' '.date("H:i:s");
        $customer   = ($_POST['id_customer'] !== '')?$_POST['id_customer']:"NULL";
        $diskon_pr  = $_POST['diskon_pr'];
        $diskon_rp  = currencyToNumber($_POST['diskon_rp']);
        $ppn        = $_POST['ppn'];
        $total      = $_POST['total_penjualan'];
        $tuslah     = currencyToNumber($_POST['tuslah']);
        $asuransi   = ($_POST['asuransi'] !== '')?$_POST['asuransi']:'NULL';
        $embalage   = currencyToNumber($_POST['embalage']);
        $reimburse  = isset($_POST['reimburse'])?$_POST['reimburse']:'0';
        $uangserah  = currencyToNumber($_POST['pembayaran']);
        $pembayaran = currencyToNumber($_POST['pembulatan']); // yang dientrikan pembulatan pembayarannya
        $id_resep   = $_POST['id_resep'];
        $expired    = $_POST['ed'];
        $id_jual    = $_POST['id_penjualan'];
        // cek apakah nomor resep pernah ditransaksikan
        $row = $this->db->query("select count(*) as jumlah, id from penjualan where id_resep = '$id_resep'")->row();
        if ($row->jumlah === '0') {
            $sql = "insert into penjualan set
                waktu = '$tanggal',
                id_resep = '$id_resep',
                id_pelanggan = $customer,
                diskon_persen = '$diskon_pr',
                diskon_rupiah = '$diskon_rp',
                ppn = '$ppn',
                total = '$total',
                tuslah = '$tuslah',
                embalage = '$embalage',
                id_asuransi = $asuransi,
                reimburse = '$reimburse',
                bayar = '$uangserah'";
            $this->db->query($sql);
            $id_penjualan = $this->db->insert_id();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $result['status'] = FALSE;
            }
            $query2= "insert into arus_kas set
                id_transaksi = '$id_penjualan',
                transaksi = 'Penjualan Resep',
                id_users = '".$this->session->userdata('id_user')."',
                waktu = '$tanggal',
                masuk = '$pembayaran'";
            $this->db->query($query2);
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $result['status'] = FALSE;
            }
            $id_barang  = $_POST['id_barang'];
            $kemasan    = $_POST['kemasan'];
            $jumlah     = $_POST['jumlah'];
            $harga_jual = $_POST['harga_jual'];
            $disc_pr    = isset($_POST['diskon_persen'])?$_POST['diskon_persen']:'0';
            $disc_rp    = isset($_POST['diskon_rupiah'])?$_POST['diskon_rupiah']:'0';
            foreach ($id_barang as $key => $data) {
                $rows = $this->db->query("select k.*, b.hna from kemasan k join barang b on (k.id_barang = b.id) where k.id = '$kemasan[$key]'")->row();
                $isi   = $rows->isi*$rows->isi_satuan;
                $exp   = ($expired[$key] !== '')?"'.$expired[$key].'":'NULL';

                $sql = "insert into detail_penjualan set
                    id_penjualan = '$id_penjualan',
                    id_kemasan = '$kemasan[$key]',
                    expired = $exp,
                    hna = '".$rows->hna."',
                    qty = '".$jumlah[$key]."',
                    harga_jual = '$harga_jual[$key]',
                    disc_pr = '$disc_pr[$key]',
                    disc_rp = '".currencyToNumber($disc_rp[$key])."'";
                $this->db->query($sql);
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $result['status'] = FALSE;
                }
                $last = $this->db->query("select * from stok where id_barang = '$data' order by id desc limit 1")->row();

                $ed  = $this->db->query("SELECT id_barang, ed, IFNULL((sum(masuk)-sum(keluar)),'0') as sisa FROM `stok` WHERE id_barang = '$data' and ed > '".date("Y-m-d")."' group by ed HAVING sisa > 0 order by ed limit 1")->row();
                $stok = "insert into stok set
                    waktu = '$tanggal',
                    id_transaksi = '$id_penjualan',
                    transaksi = 'Penjualan',
                    id_barang = '$data',
                    ed = $exp,
                    keluar = '".($jumlah[$key]*$isi)."'";
                //echo $stok;
                $this->db->query($stok);
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $result['status'] = FALSE;
                }
            }
            $sqls = "insert into detail_bayar_penjualan set
                waktu = '$tanggal',
                id_penjualan = '$id_penjualan',
                bayar = '$pembayaran'";
            $this->db->query($sqls);
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $result['status'] = FALSE;
            }
            /*Insert jurnal*/
            $this->db->query("insert into jurnal set
                waktu = '".date("Y-m-d H:i:s")."',
                id_transaksi = $id_penjualan,
                transaksi = 'Penjualan',
                id_sub_sub_sub_sub_rekening = '218',
                debet = '$pembayaran'");

            $this->db->query("insert into jurnal set
                waktu = '".date("Y-m-d H:i:s")."',
                id_transaksi = $id_penjualan,
                transaksi = 'Penjualan',
                id_sub_sub_sub_sub_rekening = '188',
                kredit = '$pembayaran'");
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $result['status'] = FALSE;
            } else {
                $this->db->trans_commit();
                $result['status'] = TRUE;
                $result['id'] = $id_penjualan;
            }
        } else {
            $this->db->query("delete from detail_penjualan where id_penjualan = '$id_jual'");
            $this->db->query("delete from detail_bayar_penjualan where id_penjualan = '$id_jual'");
            $this->db->query("delete from arus_kas where id_transaksi = '$id_jual' and transaksi = 'Penjualan Resep'");
            $this->db->query("delete from stok where transaksi = 'Penjualan' and id_transaksi = '$id_jual'");
            $this->db->query("delete from jurnal where transaksi = 'Penjualan' and id_transaksi = '$id_jual'");
            $sql = "update penjualan set
                waktu = '$tanggal',
                id_resep = '$id_resep',
                id_pelanggan = $customer,
                diskon_persen = '$diskon_pr',
                diskon_rupiah = '$diskon_rp',
                ppn = '$ppn',
                total = '$total',
                tuslah = '$tuslah',
                embalage = '$embalage',
                id_asuransi = $asuransi,
                reimburse = '$reimburse',
                bayar = '$uangserah'
                where id = '$id_jual'";
            $this->db->query($sql);
            $id_penjualan = $id_jual;
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $result['status'] = FALSE;
            }
            $query = "insert into detail_bayar_penjualan set
                waktu = '$tanggal',
                id_penjualan = '$id_penjualan',
                bayar = '$pembayaran'";
            $this->db->query($query);
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $result['status'] = FALSE;
            }
            $query2= "insert into arus_kas set
                id_transaksi = '$id_penjualan',
                transaksi = 'Penjualan Resep',
                id_users = '".$this->session->userdata('id_user')."',
                waktu = '$tanggal',
                masuk = '$pembayaran'";
            $this->db->query($query2);
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $result['status'] = FALSE;
            }
            $id_barang  = $_POST['id_barang'];
            $kemasan    = $_POST['kemasan'];
            $jumlah     = $_POST['jumlah'];
            $harga_jual = $_POST['harga_jual'];
            $disc_pr    = isset($_POST['diskon_persen'])?$_POST['diskon_persen']:'0';
            $disc_rp    = isset($_POST['diskon_rupiah'])?$_POST['diskon_rupiah']:'0';
            foreach ($id_barang as $key => $data) {
                $rows = $this->db->query("select k.*, b.hna from kemasan k join barang b on (k.id_barang = b.id) where k.id = '$kemasan[$key]'")->row();
                $isi   = $rows->isi*$rows->isi_satuan;
                $exp   = ($expired[$key] !== '')?"'.$expired[$key].'":'NULL';

                $sql = "insert into detail_penjualan set
                    id_penjualan = '$id_penjualan',
                    id_kemasan = '$kemasan[$key]',
                    expired = $exp,
                    hna = '".$rows->hna."',
                    qty = '".$jumlah[$key]."',
                    harga_jual = '$harga_jual[$key]',
                    disc_pr = '$disc_pr[$key]',
                    disc_rp = '".currencyToNumber($disc_rp[$key])."'";
                $this->db->query($sql);
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $result['status'] = FALSE;
                }
                $last = $this->db->query("select * from stok where id_barang = '$data' order by id desc limit 1")->row();

                $ed  = $this->db->query("SELECT id_barang, ed, IFNULL((sum(masuk)-sum(keluar)),'0') as sisa FROM `stok` WHERE id_barang = '$data' and ed > '".date("Y-m-d")."' group by ed HAVING sisa > 0 order by ed limit 1")->row();
                $stok = "insert into stok set
                    waktu = '$tanggal',
                    id_transaksi = '$id_penjualan',
                    transaksi = 'Penjualan',
                    id_barang = '$data',
                    ed = $exp,
                    keluar = '".($jumlah[$key]*$isi)."'";
                //echo $stok;
                $this->db->query($stok);
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $result['status'] = FALSE;
                }
            }
            $sqls = "insert into detail_bayar_penjualan set
                waktu = '$tanggal',
                id_penjualan = '$id_penjualan',
                bayar = '$pembayaran'";
            $this->db->query($sqls);
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $result['status'] = FALSE;
            }
            /*Insert jurnal*/
            $this->db->query("insert into jurnal set
                waktu = '".date("Y-m-d H:i:s")."',
                id_transaksi = $id_penjualan,
                transaksi = 'Penjualan',
                id_sub_sub_sub_sub_rekening = '218',
                debet = '$pembayaran'");

            $this->db->query("insert into jurnal set
                waktu = '".date("Y-m-d H:i:s")."',
                id_transaksi = $id_penjualan,
                transaksi = 'Penjualan',
                id_sub_sub_sub_sub_rekening = '188',
                kredit = '$pembayaran'");
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $result['status'] = FALSE;
            } else {
                $this->db->trans_commit();
                $result['status'] = TRUE;
                $result['id'] = $id_penjualan;
            }
        }
        die(json_encode($result));
    }
    
    function delete_penjualan($id) {
        $this->db->delete('penjualan', array('id' => $id));
    }
    
    /*PENJUALAN NON RESEP*/
    function get_data_penjualan_non_resep($limit, $start, $param) {
        $q = NULL;
        if ($param['id'] !== '') {
            $q.=" and p.id = '".$param['id']."' ";
        } 
        if (isset($param['pasien']) and $param['pasien'] !== '') {
            $q.=" and p.id_pelanggan = '".$param['pasien']."'";
        }
        if (isset($param['laporan']) and $param['laporan'] !== 'detail') {
            $q.=" and date(p.waktu) between '".$param['awal']."' and '".$param['akhir']."'";
            $q.=" group by p.id";
        } else if (isset($param['laporan']) and $param['laporan'] === 'detail') {
            $q.=" and date(p.waktu) between '".$param['awal']."' and '".$param['akhir']."'";
        } else {
            $q.=" and date(p.waktu) between '".date("Y-m-d")."' and '".date("Y-m-d")."'";
            //$limit = " limit ".$param['start'].", ".$param['limit']."";
        }

        $sql = "select p.*, date(p.waktu) as tanggal, pl.nama as customer, pl.id as id_customer,
            concat_ws(' ',b.nama,b.kekuatan,s.nama) as nama_barang, st.nama as kemasan, k.isi_satuan, dp.qty, dp.harga_jual, (dp.harga_jual*dp.qty) as subtotal
            from penjualan p
            join detail_penjualan dp on (p.id = dp.id_penjualan)
            join kemasan k on (k.id = dp.id_kemasan)
            join barang b on (k.id_barang = b.id)
            left join satuan s on (b.satuan_kekuatan = s.id)
            left join satuan st on (k.id_kemasan = st.id)
            left join pasien ps on (p.id_pasien = ps.id)
            left join penduduk pl on (pl.id = ps.id)
            where p.id_resep is NULL $q order by p.waktu desc";
        $limitation = null;
        $limitation.=" limit $start , $limit";
        $query = $this->db->query($sql . $limitation);
        //echo $sql . $q . $limitation;
        $queryAll = $this->db->query($sql);
        $data['data'] = $query->result();
        $data['jumlah'] = $queryAll->num_rows();
        return $data;
    }
    
    function save_penjualan_non_resep() {
        $this->db->trans_begin();
        $id_jual    = $_POST['id_penjualan'];
        $tanggal    = date2mysql($_POST['tanggal']).' '.date("H:i:s");
        $customer   = ($_POST['id_customer'] !== '')?$_POST['id_customer']:"NULL";
        $diskon_pr  = $_POST['diskon_pr'];
        $diskon_rp  = currencyToNumber($_POST['diskon_rp']);
        $ppn        = $_POST['ppn'];
        $total      = currencyToNumber($_POST['total_penjualan']);
        $tuslah     = currencyToNumber($_POST['tuslah']);
        $asuransi   = ($_POST['asuransi'] !== '')?$_POST['asuransi']:'NULL';
        $embalage   = currencyToNumber($_POST['embalage']);
        $reimburse  = isset($_POST['reimburse'])?$_POST['reimburse']:'0';
        $uangserah  = currencyToNumber($_POST['pembayaran']);
        $pembayaran = currencyToNumber($_POST['pembulatan']); // yang dientrikan pembulatan pembayarannya

        if ($id_jual === '') {
            $sql = "insert into penjualan set
                waktu = '$tanggal',
                id_pelanggan = $customer,
                diskon_persen = '$diskon_pr',
                diskon_rupiah = '$diskon_rp',
                ppn = '$ppn',
                total = '$total',
                tuslah = '$tuslah',
                embalage = '$embalage',
                id_asuransi = $asuransi,
                reimburse = '$reimburse',
                bayar = '$uangserah'";
            
            $this->db->query($sql);
            $id_penjualan = $this->db->insert_id();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $result['status'] = FALSE;
            }
            $query = "insert into detail_bayar_penjualan set
                waktu = '$tanggal',
                id_penjualan = '$id_penjualan',
                bayar = '$pembayaran'";
            $this->db->query($query); // insert ke tabel detail pembayaran
            
            $query2= "insert into arus_kas set
                id_transaksi = '$id_penjualan',
                transaksi = 'Penjualan Non Resep',
                id_users = '".$this->session->userdata('id_user')."',
                waktu = '$tanggal',
                masuk = '$pembayaran'";
            $this->db->query($query2);
            
            $id_barang  = $_POST['id_barang'];
            $kemasan    = $_POST['kemasan'];
            $jumlah     = $_POST['jumlah'];
            $harga_jual = $_POST['harga_jual'];
            $ed         = $_POST['ed'];
            $disc_pr    = isset($_POST['diskon_persen'])?$_POST['diskon_persen']:'0';
            $disc_rp    = isset($_POST['diskon_rupiah'])?$_POST['diskon_rupiah']:'0';
            foreach ($id_barang as $key => $data) {
                $rows = $this->db->query("select k.*, b.hna from kemasan k join barang b on (k.id_barang = b.id) where k.id_kemasan = '$kemasan[$key]' and k.id_barang = '$data'")->row();
                $isi   = $rows->isi*$rows->isi_satuan;
                $expired = ($ed[$key] !== '')?"'.$ed[$key].'":'NULL';
                $sql = "insert into detail_penjualan set
                    id_penjualan = '$id_penjualan',
                    id_kemasan = '".$rows->id."',
                    expired = $expired,
                    hna = '".$rows->hna."',
                    qty = '".$jumlah[$key]."',
                    harga_jual = '$harga_jual[$key]',
                    disc_pr = '$disc_pr[$key]',
                    disc_rp = '".currencyToNumber($disc_rp[$key])."'";

                $this->db->query($sql);
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $result['status'] = FALSE;
                }
                $last = $this->db->query("select * from stok where id_barang = '$data' order by id desc limit 1")->row();

                //$fefo  = $this->db->query("SELECT id_barang, ed, (sum(masuk)-sum(keluar)) as sisa FROM `stok` WHERE id_barang = '$data' and ed > '".date("Y-m-d")."' group by ed order by ed");
                //while ($val = $this->db->fetch_object($fefo)) {
                    $stok = "insert into stok set
                        waktu = '$tanggal',
                        id_transaksi = '$id_penjualan',
                        transaksi = 'Penjualan',
                        id_barang = '$data',
                        ed = $expired,
                        keluar = '".($jumlah[$key]*$isi)."'";
                    //echo $stok;
                    $this->db->query($stok);
                    
                //}
            }
            $this->db->query("insert into jurnal set
                waktu = '".date("Y-m-d H:i:s")."',
                id_transaksi = $id_penjualan,
                transaksi = 'Penjualan',
                id_sub_sub_sub_sub_rekening = '1',
                debet = '$total'");
            
            $this->db->query("insert into jurnal set
                waktu = '".date("Y-m-d H:i:s")."',
                id_transaksi = $id_penjualan,
                transaksi = 'Penjualan',
                id_sub_sub_sub_sub_rekening = '231',
                kredit = '$total'");
            
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $result['status'] = FALSE;
            } else {
                $this->db->trans_commit();
                $result['status'] = TRUE;
                $result['id'] = $id_penjualan;
                $result['act'] = 'add';
            }
            return $result;

        } else {
            $this->db->query("delete from detail_penjualan where id_penjualan = '$id_jual'");
            $this->db->query("delete from jurnal where id_transaksi = '$id_jual' and transaksi = 'Penjualan'");
            $this->db->query("delete from detail_bayar_penjualan where id_penjualan = '$id_jual'");
            $this->db->query("delete from arus_kas where id_transaksi = '$id_jual' and transaksi = 'Penjualan Non Resep'");
            $this->db->query("delete from stok where id_transaksi = '$id_jual' and transaksi = 'Penjualan'");
            $sql = "update penjualan set
                waktu = '$tanggal',
                id_pelanggan = $customer,
                diskon_persen = '$diskon_pr',
                diskon_rupiah = '$diskon_rp',
                ppn = '$ppn',
                total = '$total',
                tuslah = '$tuslah',
                embalage = '$embalage',
                id_asuransi = $asuransi,
                reimburse = '$reimburse',
                bayar = '$uangserah'
                where id = '$id_jual'";

            $this->db->query($sql);
            $id_penjualan = $id_jual;

            $query = "insert into detail_bayar_penjualan set
                waktu = '$tanggal',
                id_penjualan = '$id_penjualan',
                bayar = '$pembayaran'";
            $this->db->query($query); // insert ke tabel detail pembayaran

            $query2= "insert into arus_kas set
                id_transaksi = '$id_penjualan',
                transaksi = 'Penjualan Non Resep',
                id_users = '$_SESSION[id_user]',
                waktu = '$tanggal',
                masuk = '$pembayaran'";
            $this->db->query($query2);

            $id_barang  = $_POST['id_barang'];
            $kemasan    = $_POST['kemasan'];
            $jumlah     = $_POST['jumlah'];
            $harga_jual = $_POST['harga_jual'];
            $ed         = $_POST['ed'];
            $disc_pr    = isset($_POST['diskon_persen'])?$_POST['diskon_persen']:'0';
            $disc_rp    = isset($_POST['diskon_rupiah'])?$_POST['diskon_rupiah']:'0';
            foreach ($id_barang as $key => $data) {
                $query = $this->db->query("select k.*, b.hna from kemasan k join barang b on (k.id_barang = b.id) where k.id = '$kemasan[$key]'");
                $rows  = $this->db->fetch_object($query);
                $isi   = $rows->isi*$rows->isi_satuan;
                $expired = ($ed[$key] !== '')?"'.$ed[$key].'":'NULL';
                $sql = "insert into detail_penjualan set
                    id_penjualan = '$id_penjualan',
                    id_kemasan = '$kemasan[$key]',
                    expired = $expired,
                    hna = '".$rows->hna."',
                    qty = '".$jumlah[$key]."',
                    harga_jual = '$harga_jual[$key]',
                    disc_pr = '$disc_pr[$key]',
                    disc_rp = '".currencyToNumber($disc_rp[$key])."'";

                $this->db->query($sql);

                $last = $this->db->fetch_object($this->db->query("select * from stok where id_barang = '$data' order by id desc limit 1"));

                //$fefo  = $this->db->query("SELECT id_barang, ed, (sum(masuk)-sum(keluar)) as sisa FROM `stok` WHERE id_barang = '$data' and ed > '".date("Y-m-d")."' group by ed order by ed");
                //while ($val = $this->db->fetch_object($fefo)) {
                    $stok = "insert into stok set
                        waktu = '$tanggal',
                        id_transaksi = '$id_penjualan',
                        transaksi = 'Penjualan',
                        id_barang = '$data',
                        ed = $expired,
                        keluar = '".($jumlah[$key]*$isi)."'";
                    //echo $stok;
                    $this->db->query($stok);
                //}
            }
            $this->db->query("insert into jurnal set
                waktu = '".date("Y-m-d H:i:s")."',
                id_transaksi = $id_penjualan,
                transaksi = 'Penjualan',
                id_sub_sub_sub_sub_rekening = '1',
                debet = '$total'");

            $this->db->query("insert into jurnal set
                waktu = '".date("Y-m-d H:i:s")."',
                id_transaksi = $id_penjualan,
                transaksi = 'Penjualan',
                id_sub_sub_sub_sub_rekening = '231',
                kredit = '$total'");
            die(json_encode(array('status' => TRUE, 'id' => $id_penjualan, 'act' => 'edit')));
        }
    }
    
    function get_kunjungan_pelayanan($id_pasien) {
        $sql = "select pk.id as id_kp
            from pendaftaran pdf
            join pelayanan_kunjungan pk on (pdf.no_daftar = pk.id_kunjungan)
            inner join (
                select max(id) as id_max, id_kunjungan from pelayanan_kunjungan GROUP BY id_kunjungan
            ) pm on (pk.id = pm.id_max and pk.id_kunjungan = pm.id_kunjungan)
            where pdf.pasien = '$id_pasien'
            ";
        return $this->db->query($sql);
    }

}
?>