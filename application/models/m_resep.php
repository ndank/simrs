<?php

class M_resep extends CI_Model {
    
    function resep_report_muat_data($id = null, $awal = null, $akhir = null, $pasien = null, $dokter = null, $detail = null, $apoteker = NULL) {
        $group = "group by rr.id";
        $q = null;
        if ($id != null) {
            $q.=" and r.id = '$id'";
        } else {
            if ($awal != null and $akhir != null) {
                $q.= " and date(r.waktu) between '".datetopg($awal)."' and '".datetopg($akhir)."'";
            }
            if ($pasien != null) {
                $q.=" and ps.id = '$pasien'";
            }
            if ($dokter != null) {
                $q.=" and r.dokter_penduduk_id = '$dokter'";
            }
            if ($apoteker != null) {
                $q.=" and rr.pegawai_penduduk_id = '$apoteker'";
            }
        }
        if ($detail != null) {
            $q.="";
        }
        $sql = "select r.*, pdk.nama as apoteker, l.nama as layanan, rr.profesi_layanan_tindakan_jasa_total, rr.r_no, ps.no_rm, rr.resep_id, pd.lahir_tanggal, bp.isi, s.nama as satuan, sd.nama as sediaan, st.nama as satuan_terbesar, ri.nama as pabrik, o.kekuatan,  rr.resep_r_jumlah, rr.tebus_r_jumlah,
            rr.pakai_aturan, rr.iter, p.nama dokter, un.nama as nama_unit, pd.nama as pasien, b.nama as barang, bp.barcode, rrr.dosis_racik, rrr.pakai_jumlah
            from resep r
            left join penduduk p on (r.dokter_penduduk_id = p.id)
            join pelayanan_kunjungan pk on (r.id_pelayanan_kunjungan = pk.id)
            join pendaftaran pdf on (pdf.no_daftar = pk.id_kunjungan)
            join pasien ps on (ps.no_rm = pdf.pasien)
            left join penduduk pd on (ps.id = pd.id)
            join resep_r rr on (rr.resep_id = r.id)
            left join tarif t on (rr.tarif_id = t.id)
            left join layanan l on (t.id_layanan = l.id)
            left join penduduk pdk on (pdk.id = rr.pegawai_penduduk_id)
            left join resep_racik_r_detail rrr on (rr.id = rrr.r_resep_id)
            left join barang_packing bp on (bp.id = rrr.barang_packing_id)
            left join barang b on (b.id = bp.barang_id)
            left join obat o on (o.id = b.id)
            left join sediaan sd on (o.sediaan_id = sd.id)
            left join satuan st on (bp.terbesar_satuan_id = st.id)
            left join relasi_instansi ri on (ri.id = b.pabrik_relasi_instansi_id)
            left join satuan s on (s.id = bp.terkecil_satuan_id)
            left join unit un on (un.id = pk.id_unit)
            where r.id IS NOT NULL $q $group";
        //echo "<pre>".$sql."</pre>";
        return $this->db->query($sql);
    }
    
    function resep_dokter_muat_data($id = null, $awal = null, $akhir = null, $pasien = null, $dokter = null, $detail = null, $apoteker = NULL) {
        $group = "group by rr.id";
        $q = null;
        if ($id != null) {
            $q.=" and r.id = '$id'";
        } else {
            if ($awal != null and $akhir != null) {
                $q.= " and date(r.waktu) between '".datetopg($awal)."' and '".datetopg($akhir)."'";
            }
            if ($pasien != null) {
                $q.=" and ps.id = '$pasien'";
            }
            if ($dokter != null) {
                $q.=" and r.dokter_penduduk_id = '$dokter'";
            }
            if ($apoteker != null) {
                $q.=" and rr.pegawai_penduduk_id = '$apoteker'";
            }
        }
        if ($detail != null) {
            $q.="";
        }
        $sql = "select r.*, rr.r_no, ps.no_rm, rr.id_resep, pd.lahir_tanggal, b.kekuatan, 
            rr.resep_r_jumlah, rr.tebus_r_jumlah, CONCAT_WS(' ',b.nama, b.kekuatan, st.nama) as nama_barang,
            CONCAT_WS(' ',rr.aturan,' X ',rr.pakai) as pakai_aturan, rr.iter, p.nama dokter, pd.nama as pasien, un.nama as nama_unit, 
            b.nama as barang, rr.dosis_racik, rr.jumlah_pakai
            from resep r
            join penduduk p on (r.id_penduduk_dokter = p.id)
            join pelayanan_kunjungan pk on (r.id_kunjungan_pelayanan = pk.id)
            join pendaftaran pdf on (pdf.no_daftar = pk.id_kunjungan)
            join pasien ps on (ps.no_rm = pdf.pasien)
            left join penduduk pd on (ps.id = pd.id)
            join resep_r rr on (rr.id_resep = r.id)
            left join barang b on (b.id = rr.id_barang)
            left join satuan st on (st.id = b.satuan_kekuatan)
            left join unit un on (un.id = pk.id_unit)
            where r.id IS NOT NULL $q $group order by r.waktu asc";
        //echo "<pre>".$sql."</pre>";
        return $this->db->query($sql);
    }
    
    function cetak_etiket($id, $no_r = null) {
        $q = null;
        if ($no_r != null) {
            $q.="and rr.r_no = '$no_r'";
        }
        $sql = "select r.*, rr.r_no, pd.lahir_tanggal, s.nama as satuan, sd.nama as sediaan, ri.nama as pabrik, o.kekuatan,  rr.resep_r_jumlah, rr.tebus_r_jumlah,
            rr.pakai_aturan, rr.iter, p.nama dokter, pd.nama as pasien, b.nama as barang, rrr.dosis_racik, rrr.pakai_jumlah
            from resep r
            left join penduduk p on (r.dokter_penduduk_id = p.id)
            join pelayanan_kunjungan pk on (r.id_pelayanan_kunjungan = pk.id)
            join pendaftaran pdf on (pdf.no_daftar = pk.id_kunjungan)
            join pasien ps on (ps.no_rm = pdf.pasien)
            left join penduduk pd on (ps.id = pd.id)
            left join resep_r_orig rr on (rr.resep_id = r.id)
            left join resep_racik_r_detail_orig rrr on (rr.id = rrr.r_resep_id_orig)
            left join barang b on (b.id = rrr.barang_id)
            left join obat o on (o.id = b.id)
            left join sediaan sd on (o.sediaan_id = sd.id)
            left join relasi_instansi ri on (ri.id = b.pabrik_relasi_instansi_id)
            left join satuan s on (s.id = o.satuan_id)
            where r.id = '$id' $q";
        //echo "<pre>".$sql."</pre>";
        return $this->db->query($sql);
    }
    
    function cetak_etiket_pelayanan_farmasi($id, $no_r = null) {
        $q = null;
        if ($no_r != null) {
            $q.="and rr.r_no = '$no_r'";
        }
        $sql = "select r.*, rr.r_no, pd.lahir_tanggal, s.nama as satuan, sd.nama as sediaan, ri.nama as pabrik, o.kekuatan,  rr.resep_r_jumlah, rr.tebus_r_jumlah,
            rr.pakai_aturan, rr.iter, p.nama dokter, pd.nama as pasien, b.nama as barang, rrr.dosis_racik, sum(rrr.pakai_jumlah) as pakai_jumlah
            from resep r
            left join penduduk p on (r.dokter_penduduk_id = p.id)
            join pelayanan_kunjungan pk on (r.id_pelayanan_kunjungan = pk.id)
            join pendaftaran pdf on (pdf.no_daftar = pk.id_kunjungan)
            join pasien ps on (ps.no_rm = pdf.pasien)
            left join penduduk pd on (ps.id = pd.id)
            left join resep_r rr on (rr.resep_id = r.id)
            left join resep_racik_r_detail rrr on (rr.id = rrr.r_resep_id)
            left join barang_packing bp on (rrr.barang_packing_id = bp.id)
            left join barang b on (b.id = bp.barang_id)
            left join obat o on (o.id = b.id)
            left join sediaan sd on (o.sediaan_id = sd.id)
            left join relasi_instansi ri on (ri.id = b.pabrik_relasi_instansi_id)
            left join satuan s on (s.id = o.satuan_id)
            where r.id = '$id' $q group by bp.id";
        //echo "<pre>".$sql."</pre>";
        return $this->db->query($sql);
    }
    
    function kitir_load_atribute($id_resep) {
        $sql = "select rr.resep_id, r.id, ps.no_rm, rrr.barang_packing_id, rrr.pakai_jumlah, p.nama as pasien, b.nama as barang, st.nama as satuan_terkecil, bp.isi, 
            o.kekuatan, ri.nama as pabrik, pdk.nama as dokter, pdd.nama as pegawai, bp.diskon, bp.margin, rrr.pakai_jumlah, s.nama as satuan, sd.nama as sediaan from resep r
            join resep_r rr on (r.id = rr.resep_id)
            join resep_racik_r_detail rrr on (rr.id = rrr.r_resep_id)
            join pelayanan_kunjungan pk on (r.id_pelayanan_kunjungan = pk.id)
            join pendaftaran pdf on (pdf.no_daftar = pk.id_kunjungan)
            join pasien ps on (ps.no_rm = pdf.pasien)
            join penduduk p on (ps.id = p.id)
            left join penduduk pdk on (pdk.id = r.dokter_penduduk_id)
            left join penduduk pdd on (rr.pegawai_penduduk_id = pdd.id)
            join barang_packing bp on (rrr.barang_packing_id = bp.id)
            join barang b on (b.id = bp.barang_id)
            left join obat o on (o.id = b.id)
            left join satuan s on (s.id = o.satuan_id)
            left join satuan st on (st.id = bp.terkecil_satuan_id)
            left join sediaan sd on (sd.id = o.sediaan_id)
            left join relasi_instansi ri on (ri.id = b.pabrik_relasi_instansi_id)
            where r.id = '$id_resep'";
        //echo "<pre>".$sql."</pre>";
        return $this->db->query($sql);
    }
    
    function kitir_load_data($id_resep = NULL) {
        $q = null;
        if ($id_resep != null) {
            $q.="where rs.id = '$id_resep'";
        }
        $sql = "select rr.resep_id, rrr.pakai_jumlah as keluar, rrr.barang_packing_id, sum(rrr.pakai_jumlah) as pakai_jumlah, bp.id as id_pb, bp.barcode, bp.margin, bp.diskon, b.nama as barang, st.nama as satuan_terkecil, bp.isi, 
            o.kekuatan, r.nama as pabrik, s.nama as satuan, sd.nama as sediaan, pdk.nama as pegawai, pdd.nama as pasien, pdd.nama, ps.id as pasien_penduduk_id
            from resep rs
            join resep_r rr on (rs.id = rr.resep_id)
            join resep_racik_r_detail rrr on (rr.id = rrr.r_resep_id)
            join pelayanan_kunjungan pk on (rs.id_pelayanan_kunjungan = pk.id)
            join pendaftaran pdf on (pdf.no_daftar = pk.id_kunjungan)
            join pasien ps on (ps.no_rm = pdf.pasien)
            left join penduduk pdd on (pdd.id = ps.id)
            left join penduduk pdk on (pdk.id = rs.dokter_penduduk_id)
            join barang_packing bp on (rrr.barang_packing_id = bp.id)
            join barang b on (b.id = bp.barang_id)
            left join obat o on (o.id = b.id)
            left join satuan s on (s.id = o.satuan_id)
            left join satuan st on (st.id = bp.terkecil_satuan_id)
            left join sediaan sd on (sd.id = o.sediaan_id)
            left join relasi_instansi r on (r.id = b.pabrik_relasi_instansi_id) $q group by bp.id";
        //echo "<pre>".$sql."</pre>";
        return $this->db->query($sql);
    }
    
    function get_harga_by_barang_packing($id_packing_barang) {
        $sql = "select td.hna, td.hpp, td.ed, bp.margin, bp.diskon, (td.hna+(td.hna*(bp.margin/100)))-(td.hna*(bp.diskon/100)) as harga_jual from transaksi_detail td
            join barang_packing bp on (td.barang_packing_id = bp.id)
            where td.barang_packing_id = '$id_packing_barang'
            order by td.waktu desc limit 1";
        return $this->db->query($sql)->row();
    }
    
    function data_resep_load_data($id) {
        $sql = "select r.*, rr.id as id_rr, rr.tarif_id, sum(rr.resep_r_jumlah) as jml_tebus, rr.r_no, ps.no_rm, pd.nama as pasien, pd.lahir_tanggal, rr.resep_r_jumlah, rr.tebus_r_jumlah,
            rr.pakai_aturan, rr.iter, rr.perintah_resep, p.nama dokter, t.nominal, pd.id as pasien_id, concat_ws(' ',u.nama, pk.no_tt) as nama_unit
            from resep r
            left join penduduk p on (r.dokter_penduduk_id = p.id)
            join pelayanan_kunjungan pk on (r.id_pelayanan_kunjungan = pk.id)
            left join unit u on (u.id = pk.id_unit)
            join pendaftaran pdf on (pdf.no_daftar = pk.id_kunjungan)
            join pasien ps on (ps.no_rm = pdf.pasien)
            left join penduduk pd on (ps.id = pd.id)
            left join resep_r rr on (rr.resep_id = r.id)
            left join tarif t on (t.id = rr.tarif_id)
            where r.id = '$id' group by rr.resep_id, rr.r_no";
        return $this->db->query($sql);
    }
    function detail_data_resep_load_data($id_resep_r) {
        $sql = "select bp.id as id_packing, o.kekuatan, r.*, rr.r_no, pd.lahir_tanggal, bp.isi, s.nama as satuan, sd.nama as sediaan, st.nama as satuan_terkecil, 
        stb.nama as satuan_terbesar, ri.nama as pabrik, o.kekuatan,  rr.resep_r_jumlah, rr.tebus_r_jumlah,
            rr.pakai_aturan, rr.iter, rr.perintah_resep, p.nama dokter, pd.nama as pasien, b.nama as barang, bp.barcode, rrr.dosis_racik, rrr.pakai_jumlah
            from resep r
            left join penduduk p on (r.dokter_penduduk_id = p.id)
            join pelayanan_kunjungan pk on (r.id_pelayanan_kunjungan = pk.id)
            join pendaftaran pdf on (pdf.no_daftar = pk.id_kunjungan)
            join pasien ps on (ps.no_rm = pdf.pasien)
            left join penduduk pd on (ps.id = pd.id)
            left join resep_r rr on (rr.resep_id = r.id)
            left join resep_racik_r_detail rrr on (rr.id = rrr.r_resep_id)
            left join barang_packing bp on (bp.id = rrr.barang_packing_id)
            left join barang b on (b.id = bp.barang_id)
            left join obat o on (o.id = b.id)
            left join sediaan sd on (o.sediaan_id = sd.id)
            left join satuan st on (bp.terkecil_satuan_id = st.id)
            left join satuan stb on (stb.id = bp.terbesar_satuan_id)
            left join relasi_instansi ri on (ri.id = b.pabrik_relasi_instansi_id)
            left join satuan s on (s.id = o.satuan_id)
            where rrr.r_resep_id = '$id_resep_r' group by rrr.barang_packing_id";
        //echo $sql;
        return $this->db->query($sql);
    }
    
    function statistika_resep($awal, $akhir) {
    //
        $sql = "select td.*, o.generik, date(td.waktu) as awal, s.nama as sediaan, st.nama as satuan from transaksi_detail td
            join barang_packing bp on (td.barang_packing_id = bp.id)
            join obat o on (bp.id = o.id)
            join sediaan s on (s.id = o.sediaan_id)
            join satuan st on (st.id = bp.terbesar_satuan_id)
            where date(td.waktu) between '$awal' and '$akhir'
            and o.generik = 'Generik'";
        return $this->db->query($sql);
    }
    
    function get_data_pmr_penduduk($pasien) {
        $sql = "select p.*, d.*, p.id as penduduk_id, pk.nama as pekerjaan, kl.nama as kelurahan, pd.nama as pendidikan, pr.nama profesi from penduduk p
            left join dinamis_penduduk d on (p.id = d.penduduk_id)
            left join kelurahan kl on (d.kelurahan_id = kl.id)
            left join pendidikan pd on (d.pendidikan_id = pd.id)
            left join profesi pr on (d.profesi_id = pr.id)
            left join pekerjaan pk on (pk.id = d.pekerjaan_id)
            inner join (
                select penduduk_id, max(id) as id_max
                from dinamis_penduduk group by penduduk_id
                ) tm on (d.penduduk_id = tm.penduduk_id and d.id = tm.id_max)
            where d.id = (select max(id) from dinamis_penduduk where penduduk_id = '$pasien')";

        return $this->db->query($sql);
    }
    
    function get_data_pmr_penduduk_detail($pasien) {
        $sql = "select r.waktu,rr.*, rrr.dosis_racik, dp.sip_no, dp.alamat, pdd.nama as dokter, rrr.jual_harga, rrr.pakai_jumlah, b.nama as barang, bp.margin, rrr.barang_packing_id, bp.barcode, ri.nama as pabrik, 
        o.kekuatan, st.nama as satuan_terkecil, sd.nama as sediaan, bp.isi, s.nama as satuan from resep r
            join pelayanan_kunjungan pk on (r.id_pelayanan_kunjungan = pk.id)
            join pendaftaran pdf on (pdf.no_daftar = pk.id_kunjungan)
            join pasien ps on (ps.no_rm = pdf.pasien)
            join resep_r rr on (r.id = rr.resep_id)
            join resep_racik_r_detail rrr on (rr.id = rrr.r_resep_id)
            join barang_packing bp on (rrr.barang_packing_id = bp.id)
            join barang b on (b.id = bp.barang_id)
            left join obat o on (o.id = b.id)
            left join satuan s on (s.id = o.satuan_id)
            left join satuan st on (st.id = bp.terkecil_satuan_id)
            left join sediaan sd on (sd.id = o.sediaan_id)
            left join relasi_instansi ri on (ri.id = b.pabrik_relasi_instansi_id)
            left join penduduk pdd on (pdd.id = r.dokter_penduduk_id)
            left join dinamis_penduduk dp on (pdd.id = dp.penduduk_id)
            inner join (
                select penduduk_id, max(id) as id_max
                from dinamis_penduduk group by penduduk_id
                ) tm on (dp.penduduk_id = tm.penduduk_id and dp.id = tm.id_max)
            where ps.id = '$pasien'";
        //echo "<pre>".$sql."</pre>";
        return $this->db->query($sql);
    }
    
    function get_jenis_rawat_by_pasien($id_pasien, $no_rm) {
        $cek = $this->db->query("select no_daftar, jenis_rawat from pendaftaran where pasien = '$no_rm' order by no_daftar desc limit 1")->row(); // mengecek jenis_rawat
        $cek2= $this->db->query("select * from inap_rawat_kunjungan where no_daftar = '".$cek->no_daftar."' order by id desc limit 1")->row(); // mengecek rawat inap atau tidak
        $result = NULL;
        if (isset($cek2->id)) {
            if ($cek2->keluar_waktu == NULL) {
                $result = 'Rawat Inap';
            }
            if ($cek2->keluar_waktu != NULL) {
                $result = NULL;
            }
        }
        else if (!isset($cek2->id)) {
            if ($cek->jenis_rawat == 'IGD') {
                $result = 'IGD';
            }
            if ($cek->jenis_rawat == 'Rawat Jalan') {
                $result = 'Rawat Jalan';
            }
        }
        else {
            $result = NULL;
        }
        return $result;
    }
    
    function data_item_obat($generik, $formularium = null, $stok = null, $awal = null, $akhir = null) {
        $q=null; $stk=null; $sisa=null;
        if ($formularium != null) {
            $q.="and o.formularium = '$formularium'";
        }
        if ($stok != null) {
            $stk="left join transaksi_detail td on (td.barang_packing_id = b.id)
            inner join (
                select barang_packing_id, max(id) as id_max
                from transaksi_detail group by barang_packing_id
            ) tm on (td.barang_packing_id = tm.barang_packing_id and td.id = tm.id_max)";
            $sisa=" and td.sisa > 0 and date(td.waktu) between '".date2mysql($awal)."' and '".date2mysql($akhir)."'";
        }
        $sql = "select distinct(o.id) 
            from obat o 
            left join barang_packing b on (o.id = b.barang_id) $stk
            where o.generik = '$generik' $q $sisa";
        //echo "<pre>".$sql."</pre>";
        return $this->db->query($sql);
    }
    
    function pelayanan_resep($generik, $jenis, $formularium = null) {
        $q = null;
        if ($formularium != null) {
            $q.="and rrr.formularium = '$formularium'";
        }
        $sql = "select distinct(r.id) from resep r
            join resep_r rr on (r.id = rr.resep_id)
            join resep_racik_r_detail rrr on (rr.id = rrr.r_resep_id)
            join barang_packing b on (rrr.barang_packing_id = b.id) 
            join obat o on (b.id = o.id)
            where o.generik = '$generik' and r.jenis = '$jenis' $q";
        return $this->db->query($sql);
    }
    
    function data_resep_muat_data($id) {
        $sql = "select r.*, rr.id as id_rr, rr.tarif_id, t.nominal, pk.jenis, pk.kelas, ps.id as pasien_penduduk_id, rr.r_no, pd.nama as pasien, pd.lahir_tanggal, rr.resep_r_jumlah, rr.tebus_r_jumlah,
            rr.pakai_aturan, rr.iter, p.nama as dokter, sum(rr.t_tebus) as t_tebus, t.nominal, u.nama as unit, pd.id as pasien_id, ps.no_rm
            from resep r
            left join penduduk p on (r.dokter_penduduk_id = p.id)
            join pelayanan_kunjungan pk on (r.id_pelayanan_kunjungan = pk.id)
            join pendaftaran pdf on (pk.id_kunjungan = pdf.no_daftar)
            join pasien ps on (pdf.pasien = ps.no_rm)
            left join unit u on (pk.id_unit = u.id)
            left join penduduk pd on (ps.id = pd.id)
            left join resep_r rr on (rr.resep_id = r.id)
            left join tarif t on (t.id = rr.tarif_id)
            where r.id = '$id' group by rr.r_no";
        //echo "<pre>".$sql."</pre>";
        return $this->db->query($sql);
    }
    
    function data_salin_receipt_muat_data($id) {
        $sql = "select r.*, rr.id as id_rr, pk.jenis, pk.kelas, ps.id as pasien_penduduk_id, rr.r_no, pd.nama as pasien, pd.lahir_tanggal, rr.resep_r_jumlah, rr.tebus_r_jumlah,
            rr.pakai_aturan, rr.iter, p.nama as dokter, u.nama as unit, pd.id as pasien_id, ps.no_rm
            from resep r
            left join penduduk p on (r.dokter_penduduk_id = p.id)
            join pelayanan_kunjungan pk on (r.id_pelayanan_kunjungan = pk.id)
            join pendaftaran pdf on (pk.id_kunjungan = pdf.no_daftar)
            join pasien ps on (pdf.pasien = ps.no_rm)
            left join unit u on (pk.id_unit = u.id)
            left join penduduk pd on (ps.id = pd.id)
            left join resep_r_orig rr on (rr.resep_id = r.id)
            where r.id = '$id' group by rr.r_no";
        //echo "<pre>".$sql."</pre>";
        return $this->db->query($sql);
    }

    function resep_muat_data_by_pelayanan($id_pk) {
        $sql = "select p.waktu, CONCAT_WS(' ',b.nama, b.kekuatan, s.nama) as nama_barang, dp.qty
            from resep r
            join penjualan p on (r.id = p.id_resep)
            join detail_penjualan dp on (p.id = dp.id_penjualan)
            join kemasan k on (k.id = dp.id_kemasan)
            join barang b on (b.id = k.id_barang)
            join satuan s on (b.satuan_kekuatan = s.id)
            where p.id_pelayanan_kunjungan = '$id_pk'";
        //echo "<pre>".$sql."</pre>";
        return $this->db->query($sql);
    }
    
    function data_resep_dokter_muat_data($id) {
        $sql = "select r.*, rr.id as id_rr, rr.resep_id, pk.jenis, pk.kelas, ps.id as pasien_penduduk_id, rr.r_no, pd.nama as pasien, pd.lahir_tanggal, rr.resep_r_jumlah, rr.tebus_r_jumlah,
            rr.pakai_aturan, rr.iter, rr.perintah_resep, p.nama as dokter, ap.nama as asuransi, pk.kelas, u.nama as unit, pd.id as pasien_id, ps.no_rm
            from resep r
            left join penduduk p on (r.dokter_penduduk_id = p.id)
            join pelayanan_kunjungan pk on (r.id_pelayanan_kunjungan = pk.id)
            join pendaftaran pdf on (pk.id_kunjungan = pdf.no_daftar)
            join pasien ps on (pdf.pasien = ps.no_rm)
            left join unit u on (pk.id_unit = u.id)
            left join penduduk pd on (ps.id = pd.id)
            left join asuransi_produk ap on (pk.id_produk_asuransi = ap.id)
            join resep_r_orig rr on (rr.resep_id = r.id)
            where r.id = '$id'";
        //echo "<pre>".$sql."</pre>";
        return $this->db->query($sql);
    }
    
    function detail_data_resep_muat_data($id_resep_r) {
        $sql = "select bp.id as id_packing, o.kekuatan, r.*, rr.r_no, pd.lahir_tanggal, bp.isi, s.nama as satuan, sd.nama as sediaan, st.nama as satuan_terkecil, 
        stb.nama as satuan_terbesar, ri.nama as pabrik, o.kekuatan,  rr.resep_r_jumlah, rr.tebus_r_jumlah,
            rr.pakai_aturan, rr.iter, p.nama dokter, pd.nama as pasien, b.nama as barang, bp.barcode, rrr.dosis_racik, rrr.pakai_jumlah
            from resep r
            left join penduduk p on (r.dokter_penduduk_id = p.id)
            join pelayanan_kunjungan pk on (r.id_pelayanan_kunjungan = pk.id)
            join pendaftaran pdf on (pk.id_kunjungan = pdf.no_daftar)
            join pasien ps on (pdf.pasien = ps.no_rm)
            left join penduduk pd on (ps.id = pd.id)
            left join resep_r rr on (rr.resep_id = r.id)
            left join resep_racik_r_detail rrr on (rr.id = rrr.r_resep_id)
            left join barang_packing bp on (bp.id = rrr.barang_packing_id)
            left join barang b on (b.id = bp.barang_id)
            left join obat o on (o.id = b.id)
            left join sediaan sd on (o.sediaan_id = sd.id)
            left join satuan st on (bp.terkecil_satuan_id = st.id)
            left join satuan stb on (stb.id = bp.terbesar_satuan_id)
            left join relasi_instansi ri on (ri.id = b.pabrik_relasi_instansi_id)
            left join satuan s on (s.id = o.satuan_id)
            where rrr.r_resep_id = '$id_resep_r'";
        //echo $sql;
        return $this->db->query($sql);
    }
    
    function detail_data_resep_dokter_muat_data($id_resep_r) {
        $sql = "select b.id as id_barang, o.kekuatan, r.*, rr.r_no, pd.lahir_tanggal, sd.nama as sediaan, s.nama as satuan,
            ri.nama as pabrik, o.kekuatan,  rr.resep_r_jumlah, rr.tebus_r_jumlah,
            rr.pakai_aturan, rr.iter, p.nama dokter, pd.nama as pasien, b.nama as barang, rrr.dosis_racik, rrr.pakai_jumlah
            from resep r
            left join penduduk p on (r.dokter_penduduk_id = p.id)
            join pelayanan_kunjungan pk on (r.id_pelayanan_kunjungan = pk.id)
            join pendaftaran pdf on (pk.id_kunjungan = pdf.no_daftar)
            join pasien ps on (pdf.pasien = ps.no_rm)
            left join penduduk pd on (ps.id = pd.id)
            join resep_r_orig rr on (rr.resep_id = r.id)
            join resep_racik_r_detail_orig rrr on (rr.id = rrr.r_resep_id_orig)
            left join barang b on (b.id = rrr.barang_id)
            left join obat o on (o.id = b.id)
            left join sediaan sd on (o.sediaan_id = sd.id)
            left join relasi_instansi ri on (ri.id = b.pabrik_relasi_instansi_id)
            left join satuan s on (s.id = o.satuan_id)
            where rrr.r_resep_id_orig = '$id_resep_r'";
        //echo $sql;
        return $this->db->query($sql);
    }
    
    function detail_data_resep_dokter_muat_data_kemasan($id_resep_r) {
        $sql = "select min(bp.isi) as isi, bp.id as id_packing, o.kekuatan, r.*, rr.r_no, pd.lahir_tanggal, s.nama as satuan, sd.nama as sediaan, st.nama as satuan_terkecil, 
        stb.nama as satuan_terbesar, ri.nama as pabrik, o.kekuatan,  rr.resep_r_jumlah, rr.tebus_r_jumlah,
            rr.pakai_aturan, rr.iter, p.nama dokter, pd.nama as pasien, b.nama as barang, bp.barcode, rrr.dosis_racik, rrr.pakai_jumlah
            from resep r
            left join penduduk p on (r.dokter_penduduk_id = p.id)
            join pelayanan_kunjungan pk on (r.id_pelayanan_kunjungan = pk.id)
            join pendaftaran pdf on (pk.id_kunjungan = pdf.no_daftar)
            join pasien ps on (pdf.pasien = ps.no_rm)
            left join penduduk pd on (ps.id = pd.id)
            join resep_r_orig rr on (rr.resep_id = r.id)
            join resep_racik_r_detail_orig rrr on (rr.id = rrr.r_resep_id_orig)
            left join barang b on (b.id = rrr.barang_id)
            left join barang_packing bp on (bp.barang_id = b.id)
            left join obat o on (o.id = b.id)
            left join sediaan sd on (o.sediaan_id = sd.id)
            left join satuan st on (bp.terkecil_satuan_id = st.id)
            left join satuan stb on (stb.id = bp.terbesar_satuan_id)
            left join relasi_instansi ri on (ri.id = b.pabrik_relasi_instansi_id)
            left join satuan s on (s.id = o.satuan_id)
            inner join (
                select barang_id, min(isi) as min_isi from barang_packing group by barang_id
            ) bm on (bm.barang_id = bp.barang_id and bm.min_isi = bp.isi)
            where rrr.r_resep_id_orig = '$id_resep_r' group by b.id";
        //echo "<pre>".$sql."</pre>";
        return $this->db->query($sql);
    }
    
    function cek_ketersediaan_stok($id_packing) {
        $sql = "select (sum(masuk)-sum(keluar)) as sisa from transaksi_detail where barang_packing_id = '$id_packing'";
        //echo $sql."<br/>";
        return $this->db->query($sql);
    }
}
?>