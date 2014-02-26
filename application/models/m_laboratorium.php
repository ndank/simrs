<?php

class M_laboratorium extends CI_Model {

	function pemeriksaan_lab_save(){
		$this->db->trans_begin();

		$id_pk = post_safe('id_pelayanan_kunjungan');
		$dokter = json_decode(post_safe('dokter')); 
		$analis = json_decode(post_safe('analis')); 
		$waktu_order = json_decode(post_safe('waktu_order'));
		$waktu_hasil = json_decode(post_safe('waktu_hasil')); 
		$layanan = json_decode(post_safe('layanan')); 
		$hasil = json_decode(post_safe('hasil')); 
		$ket = json_decode(post_safe('ket')); 
		$satuan = json_decode(post_safe('satuan')); 

		foreach ($dokter as $key => $value) {
			$data = array(
				'id_pelayanan_kunjungan' => $id_pk,
				'id_kepegawaian_dokter_pemesan' => ($value !== '')?$value:NULL,
                'id_kepegawaian_analis_lab' => ($analis[$key] !== '')?$analis[$key]:NULL,
				'waktu_order' => $waktu_order[$key],
				'waktu_hasil' => ($waktu_hasil[$key]!=='')?$waktu_hasil[$key]:NULL,
				'id_layanan_lab' => $layanan[$key],
				'hasil' => $hasil[$key],
				'ket_nilai_rujukan'=> $ket[$key],
				'id_satuan' => ($satuan[$key] !== '')?$satuan[$key]:NULL,
			);
			$this->db->insert('pemeriksaan_lab_pelayanan_kunjungan', $data);	
		}

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $status = false;
        } else {
            $this->db->trans_commit();
            $status = true;
        }
        return $status;
	}

	function get_pemeriksaan_lab($id_pk){
		$sql = "select pl.*, p1.nama as dokter, p2.nama as laboran,  
				s.nama as satuan, l.nama as layanan 
				from pemeriksaan_lab_pelayanan_kunjungan pl  
				left join penduduk p1 on (p1.id = pl.id_kepegawaian_dokter_pemesan) 
				left join kepegawaian kp2 on (kp2.id = pl.id_kepegawaian_analis_lab) 
                left join penduduk p2 on (p2.id = kp2.penduduk_id) 
				left join satuan s on (s.id =  pl.id_satuan) 
				left join layanan l on (l.id = pl.id_layanan_lab) 
				where pl.id_pelayanan_kunjungan = '$id_pk'";
        //echo $sql;
		return $this->db->query($sql)->result();
	}

	function delete_pemeriksaan_lab($id){
		$this->db->where('id', $id)->delete('pemeriksaan_lab_pelayanan_kunjungan');
	}

	function pelayanan_kunjungan_get_data($id){
        $sql = "select pk.*, pk.id as id_pk ,u.nama as nama_unit,ppeg.nama as nama_pegawai,
                asu.nama as nama_asuransi, pd.nama as pasien, ps.no_rm, pd.*,
                dp.alamat, kel.nama as kelurahan, kec.nama as kecamatan,
                pkj.nama as pekerjaan, pdk.nama as pendidikan,
                kab.nama as kabupaten, pro.nama as provinsi
                from pelayanan_kunjungan pk
                join pendaftaran p on(pk.id_kunjungan = p.no_daftar)
                join pasien ps on (p.pasien = ps.no_rm)
                join penduduk pd on (ps.id = pd.id)
                join dinamis_penduduk dp on (dp.penduduk_id = pd.id)
                left join kelurahan kel on (kel.id = dp.kelurahan_id)
	            left join kecamatan kec on (kel.kecamatan_id = kec.id)
	            left join kabupaten kab on (kec.kabupaten_id = kab.id)
	            left join provinsi pro on (kab.provinsi_id = pro.id)
                left join pekerjaan pkj on (pkj.id = dp.pekerjaan_id)
                left join pendidikan pdk on (pdk.id = dp.pendidikan_id)
                left join unit u on(u.id = pk.id_unit)
                left join kepegawaian peg on(peg.id = pk.id_kepegawaian_dpjp)
                left join penduduk ppeg on (ppeg.id = peg.penduduk_id)
                left join asuransi_produk asu on(asu.id = pk.id_produk_asuransi)
                where pk.id = '$id' and dp.id in (select max(id) from dinamis_penduduk group by penduduk_id)";
        $query = $this->db->query($sql);
        //echo $sql;
        return $query->row();
    }

    function pemeriksaan_lab_non_save($id_pk){
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
					'dokter_pemesan' => ($value !== '')?$value:NULL,
					'id_kepegawaian_analis_lab' => ($analis[$key] !== '')?$analis[$key]:NULL,
					'waktu_order' => ($waktu_order[$key]!=='')?datetime2mysql($waktu_order[$key]):NULL,
					'waktu_hasil' => ($waktu_hasil[$key]!=='')?datetime2mysql($waktu_hasil[$key]):NULL,
					'id_layanan_lab' => $layanan[$key],
					'hasil' => $hasil[$key],
					'ket_nilai_rujukan'=> ($ket[$key] !== '')?$ket[$key]:NULL,
					'id_satuan' =>($satuan[$key] !== '')?$satuan[$key]:NULL
				);
				$this->db->insert('pemeriksaan_lab_pelayanan_kunjungan', $data);	
			}
		}
    }

    function pemeriksaan_radiologi_luar_save($id_pk){
        $id_dokter = post_safe('dokter_radio');
        $id_radio = post_safe('id_radio_radio');
        $wkt_order = post_safe('waktu_order_radio');
        $wkt_hasil = post_safe('waktu_hasil_radio');
        $id_layan  = post_safe('id_layanan_radio');
        $kv = post_safe('kv_radio');
        $ma = post_safe('ma_radio');
        $s = post_safe('s_radio');
        $p = post_safe('p_radio');
        $fr = post_safe('fr_radio');
        
        if(isset($_POST['dokter_radio'])){
            foreach ($id_dokter as $key => $data) {
                $data_pemeriksaan = array(
                    'id_pelayanan_kunjungan' => $id_pk,
                    'dokter_pemesan' => ($data !== '')?$data:NULL,
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

    function insert_penduduk(){
    	$this->load->model('m_demografi');
        $kelurahan_id = (post_safe('id_kelurahan') == "") ? NULL : post_safe('id_kelurahan');
        

        $penduduk = array(
            'nama' => post_safe('nama'),
            'gender' => post_safe('kelamin'),
            'lahir_tanggal' => date2mysql(post_safe('tgl_lahir'))
        );

        $dinamis = array(
            'tanggal' => $this->hari,
            'alamat' => post_safe('alamat'),
            'kelurahan_id' => $kelurahan_id
        );


        // entry penduduk dulu
        // baru entry pasien
        if (post_safe('id_penduduk') == '') {
            $last_id = $this->m_demografi->create_penduduk($penduduk);
            $dinamis['penduduk_id'] = $last_id;
            $id_dinamis = $this->m_demografi->create_dinamis_penduduk($dinamis);
        } else {
            $last_id = post_safe('id_penduduk');
            $dinamis['penduduk_id'] = $last_id;

            /* */

            $id_dinamis = $this->m_demografi->create_dinamis_penduduk($dinamis);
            /* Update tanggal lahir */
            $this->db->where('id', $last_id);
            $this->db->update('penduduk',$penduduk);

        }
        $return['id'] = $last_id;
        $return['id_dinamis'] = $id_dinamis;
        return $return;
    }

    function insert_pendaftaran($id_pdd, $id_dinamis){
    	date_default_timezone_set('Asia/Jakarta');
    	$waktu = date("Y-m-d H:i:s");
    	$data_daftar = array(
                'pasien' => NULL,     
                'id_customer' => $id_pdd,           
                'tgl_daftar' => $waktu,
                'tgl_layan' =>  date('Y-m-d'),
                'kd_ptgs_daft' => $this->session->userdata('id_user'),
                'kd_ptgs_confirm' => $this->session->userdata('id_user'),
                'arrive_time' => $waktu,
                'dinamis_penduduk_id' => $id_dinamis
            );
        $this->db->insert('pendaftaran', $data_daftar);
        return $this->db->insert_id();

    }

    function insert_pelayanan_kunjungan($no_daftar, $id_jurusan, $id_unit, $no_antri = null){
    	$data = array(
                'waktu' => date('Y-m-d H:i:s'),
    			'id_kunjungan' => $no_daftar,
    			'jenis' => 'Rawat Jalan',
                'id_jurusan_kualifikasi_pendidikan' => $id_jurusan,
                'id_unit' => $id_unit,
                'no_antri' => $no_antri
    		);
    	$this->db->insert('pelayanan_kunjungan', $data);
    	return $this->db->insert_id();
    }

    function fisioterapi_load_data($limit, $start, $param){
        $q = '';
        $paging = " limit " . $start . "," . $limit . " ";
        if ($param['awal'] != NULL and $param['akhir'] != NULL) {
            $q.=" and p.tgl_layan between '".  $param['awal']."' and '".  $param['akhir']."'";
        }
        if ($param['no'] != NULL) {
            $q.=" and p.no_daftar = '$param[no]'";
        }
        if ($param['nama'] != NULL) {
            $q.=" and pd.nama like ('%$param[nama]%')";
        }
      
        if ($param['alamat'] != NULL) {
            $q.=" and dp.alamat like ('%$param[alamat]%')";
        }
        if ($param['id_kelurahan'] != NULL) {
            $q.=" and dp.kelurahan_id = '$param[id_kelurahan]'";
        }

        //$q .= " and p.id_jurusan_kualifikasi_pendidikan = '21' ";
        $db = "select pk.id as id_pk,(select count(*) from pelayanan_kunjungan 
            where id_kunjungan = p.no_daftar) as pk, 
            p.*, pd.nama,dp.alamat ,kl.nama as kelurahan, kec.nama as kecamatan,
            kabb.nama as kabupaten from pendaftaran p
            join penduduk pd on (pd.id = p.id_customer)
            left join pelayanan_kunjungan pk on (pk.id_kunjungan = p.no_daftar)
            left join dinamis_penduduk dp on (dp.penduduk_id = pd.id)
            left join kelurahan kl on (dp.kelurahan_id = kl.id)
            left join kecamatan kec on (kl.kecamatan_id = kec.id)
            left join kabupaten kabb on (kec.kabupaten_id = kabb.id)
            inner join (
                select penduduk_id, max(id) as id_max
                from dinamis_penduduk GROUP BY penduduk_id
            ) dpi on (dp.penduduk_id = dpi.penduduk_id and dp.id = dpi.id_max)
            where p.pasien is null $q order by pd.nama ";
       //echo $db.$paging;
        $data = $this->db->query($db . $paging);
        $ret['data'] = $data->result();
        $ret['jumlah'] = $this->db->query($db)->num_rows;
        return $ret;
    }

    function detail_pendaftaran_non_pasien($no_daftar) {
        $sql = "select pdd.nama as pasien, dp.alamat, pdd.gender,
            pdd.lahir_tanggal, pd.jenis_rawat, pd.no_daftar ,pd.waktu_keluar,
            kl.nama as kelurahan, kc.nama as kecamatan, kb.nama as kabupaten, pr.nama as provinsi
            from pendaftaran pd
            join penduduk pdd on (pd.id_customer = pdd.id)
            left join dinamis_penduduk dp on (dp.penduduk_id = pdd.id)
            left join kelurahan kl on (kl.id = dp.kelurahan_id)
            left join kecamatan kc on (kc.id = kl.kecamatan_id)
            left join kabupaten kb on (kb.id = kc.kabupaten_id)
            left join provinsi pr on (pr.id = kb.provinsi_id)
            inner join (
                select penduduk_id, max(id) as id_max from dinamis_penduduk group by penduduk_id
            ) dm on (dp.penduduk_id = dm.penduduk_id and dp.id = dm.id_max)
            where pd.no_daftar = '$no_daftar'";
       // echo $sql;
        return $this->db->query($sql);
    }

    function pelayanan_fisioterapi_luar_save($id_pk){
        $nakes = post_safe('dokter'); // array
        $anestesi = post_safe('anestesi'); // array
        $tindakan = post_safe('tindakan'); // array
        $unit = post_safe('unit'); // array
        $waktu = post_safe('waktu'); // array
        if (is_array($waktu)) {
             foreach ($waktu as $key => $value) {
                if ($nakes[$key] != '') {
                    $data = array(
                       'id_pelayanan_kunjungan' => $id_pk,// dari insert pelayanan_kunjungan,
                       'id_kepegawaian_nakes_operator' => ($nakes[$key]=='')?NULL:$nakes[$key],
                       'id_kepegawaian_nakes_anesthesi' => ($anestesi[$key]=='')?NULL:$anestesi[$key],
                       'waktu' => datetime2mysql($value),
                       'id_layanan' => ($tindakan[$key] !== '')?$tindakan[$key]:NULL,
                       'id_unit_penunjang' => ($unit[$key]=='')?NULL:$unit[$key],
                    );
                    $this->db->insert('tindakan_pelayanan_kunjungan', $data);
                }                 
             }
        }
    }

    function list_pelayanan_kunjungan($var, $limit = null, $start = null, $inap = null) {
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
        $join = '';
        if ($var['jenis'] === 'laboratorium') {
            $join = "join pemeriksaan_lab_pelayanan_kunjungan plk on (plk.id_pelayanan_kunjungan = pk.id)";
        }else if ($var['jenis'] === 'radiologi') {
            $join = "join pemeriksaan_radiologi_pelayanan_kunjungan prk on (prk.id_pelayanan_kunjungan = pk.id)";
        }

    
        $sql = "select pk.id as id_pk,p.no_daftar, ps.no_rm, p.arrive_time, p.waktu_keluar, p.jenis_rawat, pdd.nama, 
            k.nama as kelurahan, kc.nama as kecamatan, kb.nama as kabupaten, pr.nama as provinsi, ppeg.nama as dpjp,
            pk.kelas, pk.no_tt, u.nama as unit
            from pelayanan_kunjungan pk
            join pendaftaran p on (pk.id_kunjungan =  p.no_daftar)
            ".$join."
            join pasien ps on (p.pasien = ps.no_rm)
            join penduduk pdd on (ps.id = pdd.id)
            join dinamis_penduduk dp on (pdd.id = dp.penduduk_id)
            left join kelurahan k on (k.id = dp.kelurahan_id)
            left join kecamatan kc on (kc.id = k.kecamatan_id)
            left join kabupaten kb on (kb.id = kc.kabupaten_id)
            left join provinsi pr on (pr.id = kb.provinsi_id)
            left join kepegawaian peg on (pk.id_kepegawaian_dpjp = peg.id)
            left join penduduk ppeg on (ppeg.id = peg.penduduk_id)
            left join unit u on (u.id = pk.id_unit)
            inner join (
                select penduduk_id, max(id) as id_max from dinamis_penduduk group by penduduk_id
            ) dm on (dp.penduduk_id = dm.penduduk_id and dp.id = dm.id_max)
            where p.no_daftar is not NULL $q 
            union 
            select pk.id as id_pk, p.no_daftar, 'Pasien Luar' as no_rm, p.arrive_time, p.waktu_keluar, p.jenis_rawat, pdd.nama, 
                k.nama as kelurahan, kc.nama as kecamatan, kb.nama as kabupaten, pr.nama as provinsi, ppeg.nama as dpjp,
                pk.kelas, pk.no_tt, u.nama as unit
                from pelayanan_kunjungan pk
                join pendaftaran p on (pk.id_kunjungan =  p.no_daftar)
                join penduduk pdd on (p.id_customer = pdd.id)
                join dinamis_penduduk dp on (pdd.id = dp.penduduk_id)
                left join kelurahan k on (k.id = dp.kelurahan_id)
                left join kecamatan kc on (kc.id = k.kecamatan_id)
                left join kabupaten kb on (kb.id = kc.kabupaten_id)
                left join provinsi pr on (pr.id = kb.provinsi_id)
                left join kepegawaian peg on (pk.id_kepegawaian_dpjp = peg.id)
                left join penduduk ppeg on (ppeg.id = peg.penduduk_id)
                left join unit u on (u.id = pk.id_unit)
                inner join (
                    select penduduk_id, max(id) as id_max from dinamis_penduduk group by penduduk_id
                ) dm on (dp.penduduk_id = dm.penduduk_id and dp.id = dm.id_max)
                where p.no_daftar is not NULL and p.id_customer is null $q  
            ";
        //echo "<pre>".$sql."</pre>";
        return $this->db->query($sql.$limitation);
    }
}

?>