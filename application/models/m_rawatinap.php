<?php

class M_Rawatinap extends CI_Model {

    function data_pasien_muat_data($q) {
        $sql = "select p.*, pd.id as id_penduduk, pd.nama, pdf.*, pd.lahir_tanggal, pd.gender, 
        dp.alamat, kel.nama as kelurahan,kec.nama as kecamatan, 
        kelj.nama as pj_kelurahan, kecj.nama as pj_kecamatan
        from pasien p
        join penduduk pd on (p.id = pd.id)
        join pendaftaran pdf on (p.no_rm = pdf.pasien)
        join dinamis_penduduk dp on (dp.id = pdf.dinamis_penduduk_id)
        left join kelurahan kel on (kel.id = dp.kelurahan_id)
        left join kecamatan kec on (kel.kecamatan_id = kec.id)
        left join kelurahan kelj on (kelj.id = pdf.kelurahan_id_pjwb)
        left join kecamatan kecj on (kelj.kecamatan_id = kecj.id)
        inner join (
            select pasien, max(no_daftar) as max_no_daftar
            from pendaftaran group by pasien
        ) pdfi on (pdf.pasien = pdfi.pasien and pdf.no_daftar = pdfi.max_no_daftar)
        where ( p.no_rm like ('%$q%') or pd.nama like ('%$q%') )
        and pdf.waktu_keluar is null 
        order by locate ('$q',p.no_rm)";
            
        $exe = $this->db->query($sql);
        return $exe->result();
    }

    function get_data_pasien($no_rm) {
        $sql = "select p.*, pd.id as id_penduduk, pd.nama, pdf.*, 
        pd.lahir_tanggal, pd.gender, 
        dp.alamat, kel.nama as kelurahan,kec.nama as kecamatan,
        kelj.nama as pj_kelurahan, kecj.nama as pj_kecamatan
        from pasien p
        join penduduk pd on (p.id = pd.id)
        join pendaftaran pdf on (p.no_rm = pdf.pasien)
        join dinamis_penduduk dp on (dp.id = pdf.dinamis_penduduk_id)
        left join kelurahan kel on (kel.id = dp.kelurahan_id)
        left join kecamatan kec on (kel.kecamatan_id = kec.id)
        left join kelurahan kelj on (kelj.id = pdf.kelurahan_id_pjwb)
        left join kecamatan kecj on (kelj.kecamatan_id = kecj.id)
        inner join (
            select pasien, max(no_daftar) as max_no_daftar
            from pendaftaran group by pasien
        ) pdfi on (pdf.pasien = pdfi.pasien and pdf.no_daftar = pdfi.max_no_daftar)
        where p.no_rm = '$no_rm' and pdf.waktu_keluar is null";
            
        $exe = $this->db->query($sql);
        return $exe->row();
    }



    function data_unit_muat_data($q) {
        $sql = "select * FROM unit where nama like ('%$q%') order by locate ('$q',nama)";
        $exe = $this->db->query($sql);
        return $exe->result();
    }

    function get_data_rawatinap($no_daftar) {
        $sql = "select ir.*, t.kelas, t.nominal, u.nama as unit, pk.id as id_pelayanan_kunjungan, pk.no_tt,
                DATEDIFF(ir.keluar_waktu, ir.masuk_waktu) as durasi
                FROM pelayanan_kunjungan pk
                join pendaftaran p on(p.no_daftar = pk.id_kunjungan)
                join inap_rawat_kunjungan ir on (pk.id = ir.id_pelayanan_kunjungan)
                join tarif t on (ir.id_tarif = t.id)
                join unit u on (u.id = t.id_unit) where p.no_daftar='" . $no_daftar . "'
                order by ir.id";
        return $this->db->query($sql)->result();
    }

    function data_bed_muat_data($param) {
        $sql = "select t.id, t.no, t.tarif FROM tt t
            join unit u on (t.unit_id = u.id)
            where u.id = '" . $param['unit'] . "' 
            and t.kelas = '" . $param['kelas'] . "' 
            and t.use = '0' ";
        return $this->db->query($sql)->result();
    }

    function save_bed_data($param) {
        $this->load->model('m_pendaftaran');
        $this->db->trans_begin();
        // $param = no_daftar, no_bed, in_time
        
        $insert = array(
            'id_pelayanan_kunjungan' => $param['id_pk'],
            'id_tarif' => $param['id_tarif'],
            'masuk_waktu' => $param['in_time']
        );
        $this->db->insert('inap_rawat_kunjungan', $insert);
        $id_inap = $this->db->insert_id();

        
        // insert ke jasa penjualan detail
        $biaya['no_daftar'] = $param['no_daftar'];
        $biaya['id_pk'] = $param['id_pk'];
        $biaya['tarif_id'] = $param['id_tarif']; // kunjungan pasien
        $biaya['id_debet'] = 231;
        $biaya['id_kredit'] = 110;
        $biaya['waktu'] = $param['in_time'];
        $biaya['frekuensi'] = null;
        $this->m_pendaftaran->insert_biaya($biaya);
       
        
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $status = FALSE;
        } else {
            $this->db->trans_commit();
            $status = TRUE;
        }

    }

    function update_bed_data($param) {
        $this->db->trans_begin();
        // $param = id, out_time, tarif, in_time
            // update
            $durasi = get_duration($param['in_time'], datetime2mysql($param['out_time']));
            //echo print_r($durasi);
            if ($durasi['day'] == 0) {
                $durasi['day']++;
            } else if ($durasi['hour'] > 0) {
                $durasi['day']++;
            }

            // mencari nilai nominal tarif

            $tarif = $this->db->query("select t.nominal from inap_rawat_kunjungan i 
                    join tarif t on (t.id = i.id_tarif) where i.id = '".$param['id']."' ")->row()->nominal;
            $subtotal = ($durasi['day']) * $tarif ;
            $this->db->where('id', $param['id']);
            $this->db->update('inap_rawat_kunjungan', array('keluar_waktu' => datetime2mysql($param['out_time']), 'subtotal'=>$subtotal));

            // update tempat tidur -- need id tt

            $this->db->where('id_pelayanan_kunjungan', $param['id_pk']);
            $this->db->update('jasa_penjualan_detail', array('frekuensi' => $durasi['day']));

            $wkt = $param['in_time'];

            $subtotal2 = ($durasi['day'] - 1) * $tarif;


            $id_jasa = $this->db->where('id_pelayanan_kunjungan', $param['id'])->get('jasa_penjualan_detail')->row()->id;
            $array = array(
                'waktu' => $wkt,
                'id_transaksi' => $id_jasa,
                'jenis_transaksi' => 'Penjualan Jasa',
                'id_sub_sub_sub_sub_rekening' => 231,
                'debet' => $subtotal2,
                'kredit' => '0'
            );
            $this->db->insert('jurnal', $array);
            /*$arrays = array(
                'waktu' => $wkt,
                'id_transaksi' => $id_jasa,
                'jenis_transaksi' => 'Penjualan Jasa',
                'id_sub_sub_sub_sub_rekening' => 110,
                'debet' => '0',
                'kredit' => $subtotal2
            );
            $this->db->insert('jurnal', $arrays);*/

             //update status bed = Tersedia
            $upt = array('status'=>'Tersedia');
            $this->db->where('id', post_safe('no_tt'))->update('tt', $upt);
        

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $status = FALSE;
        } else {
            $this->db->trans_commit();
            $status = TRUE;
        }
    }

    function delete_bed_data($id) {
        $this->db->trans_begin();
         // get id_pelayanan_kunjungan
        $id_pk = $this->db->where('id', $id)->get('inap_rawat_kunjungan')->row()->id_pelayanan_kunjungan;

        $id_jasa = $this->db->where('id_pelayanan_kunjungan', $id_pk)->get('jasa_penjualan_detail')->row()->id;

        $this->db->where('id', $id_pk);
        $this->db->delete('pelayanan_kunjungan');


        // inap rawat kunjungan
        $this->db->where('id', $id);
        $this->db->delete('inap_rawat_kunjungan');

        // Jasa Penjualan Detail
        $this->db->where('id_pelayanan_kunjungan', $id_pk);
        $this->db->delete('jasa_penjualan_detail');

        // Jurnal
        $this->db->where('id_transaksi', $id_jasa);
        $this->db->delete('jurnal');




        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $status = FALSE;
        } else {
            $this->db->trans_commit();
            $status = TRUE;
        }

        return $status;
    }

  
    
    function get_data_tarif_sewa_kamar($unit, $kelas) {
        $sql = "select t.nominal, t.id from tarif t
            join layanan l on (t.id_layanan = l.id)
            where t.id_unit = '$unit' and t.kelas = '$kelas' and t.jenis_pelayanan_kunjungan ='Rawat Inap' and l.nama like '%Sewa Kamar%'";
        //echo $sql;
        return $this->db->query($sql);
    }

}

?>