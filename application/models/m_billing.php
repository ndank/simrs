<?php

class M_billing extends CI_Model {

    function modul_muat_data() {
        return array(
            array('0' => array('admission/', 'Admission')),
            array('1' => array('inventory/', 'Inventory')),
            array('2' => array('billing/', 'Billing')),
            array('3' => array('', 'Rekam Medik')),
            array('4' => array('referensi/', 'Referensi / Setting'))
        );
    }

    function data_pasien_muat_data($q) {
        $sql = "select p.id as id_pasien,p.*, pd.nama, pdf.no_daftar from pasien p
            join penduduk pd on (p.id = pd.id)
            join pendaftaran pdf on (p.no_rm = pdf.pasien)
            inner join (
                select pasien, max(no_daftar) as max_no_daftar
                from pendaftaran group by pasien
            ) pdfi on (pdf.pasien = pdfi.pasien and pdf.no_daftar = pdfi.max_no_daftar)
            where p.no_rm like ('%$q%') or pd.nama like ('%$q%') order by locate ('$q',p.no_rm)";
        $exe = $this->db->query($sql);
        return $exe->result();
    }

    function get_data_pasien($no_daftar) {
        $sql = "select s.id, s.no_rm, p.no_daftar, pd.nama from pendaftaran p
                join pasien s on(p.pasien = s.no_rm)
                join penduduk pd on (s.id = pd.id)
                where p.no_daftar = '" . $no_daftar . "'";
        $exe = $this->db->query($sql);
        return $exe->row();
    }

    function data_kunjungan_muat_data($q) {

        $sql = "select p.*, pd.nama, pd.lahir_tanggal, ps.id as id_pasien, ps.no_rm, 
                (select sum(total) from penjualan where no_daftar = '$q') as total_barang,
                (select sum(t.nominal*jpd.frekuensi) from jasa_penjualan_detail jpd
                    join pelayanan_kunjungan pk on(pk.id = jpd.id_pelayanan_kunjungan)
                    join tarif t on (jpd.tarif_id = t.id) 
                    where pk.id_kunjungan = '$q') as total_jasa 
                from pendaftaran p 
                join pasien ps on (p.pasien = ps.no_rm)
                join penduduk pd on (ps.id = pd.id)
                where p.no_daftar = '$q'
                union
                    select p.*, pd.nama, pd.lahir_tanggal, null as id_pasien, '', 
                    (select sum(total) from penjualan where no_daftar = '$q') as total_barang,
                    (select sum(t.nominal*jpd.frekuensi) from jasa_penjualan_detail jpd 
                        join pelayanan_kunjungan pk on(pk.id = jpd.id_pelayanan_kunjungan)
                        join tarif t on (jpd.tarif_id = t.id) 
                        where pk.id_kunjungan = '$q') as total_jasa 
                    from pendaftaran p 
                    join penduduk pd on (p.id_customer = pd.id)
                    where p.no_daftar = '$q'";
        //echo $sql;
        $exe = $this->db->query($sql);
        return $exe->result();
    }
    
    function detail_atribute_penduduk_by_norm($no_rm) {
        $sql = "select ps.no_rm, dp.alamat, kl.nama as kelurahan, kc.nama as kecamatan, kb.nama as kabupaten, pr.nama as provinsi from pasien ps
            join penduduk pd on (ps.id = pd.id)
            join dinamis_penduduk dp on (dp.penduduk_id = pd.id)
            join kelurahan kl on (kl.id = dp.kelurahan_id)
            join kecamatan kc on (kc.id = kl.kecamatan_id)
            join kabupaten kb on (kb.id = kc.kabupaten_id)
            join provinsi pr on (pr.id = kb.provinsi_id)
            inner join (
                select penduduk_id, max(id) as id_max from dinamis_penduduk GROUP BY penduduk_id
            ) dm on (dm.id_max = dp.id and dm.penduduk_id = dp.penduduk_id)
            where ps.no_rm = '$no_rm'";
        return $this->db->query($sql);
    }

    function data_kunjungan($q) {

        $sql = "select p.*, pd.nama, pd.lahir_tanggal, ps.id as id_pasien, ps.no_rm 
            from pendaftaran p 
            join pasien ps on (p.pasien = ps.no_rm)
            join penduduk pd on (ps.id = pd.id)
            where p.no_daftar like '%$q%' 
            ";
            /*
             union 
                select p.*, pd.nama, pd.lahir_tanggal, pd.id as id_pasien, pd.id as no_rm
                from pendaftaran p 
                join penduduk pd on (p.id_customer = pd.id)
                where p.no_daftar like '%$q%'

            */
            // echo $sql;
        $exe = $this->db->query($sql);
        return $exe->result();
    }

    function total_pembayaran($no_daftar) {
        $sql = "select sum(bayar) as total_pembayaran from kunjungan_billing_pembayaran where no_daftar = '$no_daftar'";
        $exe = $this->db->query($sql);
        return $exe->row();
    }


    function penjualan_barang_load_data($id_pasien = null, $status = null) {
        $q = null;
        if ($status != null) {
            $q.=" group by p.id";
        }
        $exe = $this->db->query("select * from pendaftaran p join pasien ps on (p.pasien = ps.no_rm) where ps.id = '$id_pasien' order by p.no_daftar desc limit 1");
        $row = $exe->row();
        $sql = "select p.id as no_penjualan, o.generik, p.resep_id as status, td.*, bp.id as id_pb, bp.barcode, bp.margin, bp.diskon, b.nama as barang, st.nama as satuan_terkecil, 
            bp.isi, o.kekuatan, r.nama as pabrik, s.nama as satuan, sd.nama as sediaan from penjualan p
            join pendaftaran pdf on (pdf.no_daftar = p.no_daftar)
            join pasien ps on (ps.no_rm = pdf.pasien)
            join transaksi_detail td on (p.id = td.transaksi_id)
            join barang_packing bp on (td.barang_packing_id = bp.id)
            join barang b on (b.id = bp.barang_id)
            left join obat o on (o.id = b.id)
            left join satuan s on (s.id = o.satuan_id)
            left join satuan st on (st.id = bp.terkecil_satuan_id)
            left join sediaan sd on (sd.id = o.sediaan_id)
            left join relasi_instansi r on (r.id = b.pabrik_relasi_instansi_id)
            where p.no_daftar = '" . $row->no_daftar . "' $q";
        //echo "<pre>$sql</pre>";
        return $this->db->query($sql);
    }

    function penjualan_barang_detail_load_data($id_penjualan) {
        $sql = "select p.id as no_penjualan, o.generik, p.resep_id as status, td.*, bp.id as id_pb, bp.barcode, bp.margin, bp.diskon, b.nama as barang, st.nama as satuan_terkecil, 
            bp.isi, o.kekuatan, r.nama as pabrik, s.nama as satuan, sd.nama as sediaan from penjualan p
            join pendaftaran pdf on (pdf.no_daftar = p.no_daftar)
            join pasien ps on (ps.no_rm = pdf.pasien)
            join transaksi_detail td on (p.id = td.transaksi_id)
            join barang_packing bp on (td.barang_packing_id = bp.id)
            join barang b on (b.id = bp.barang_id)
            left join obat o on (o.id = b.id)
            left join satuan s on (s.id = o.satuan_id)
            left join satuan st on (st.id = bp.terkecil_satuan_id)
            left join sediaan sd on (sd.id = o.sediaan_id)
            left join relasi_instansi r on (r.id = b.pabrik_relasi_instansi_id)
            where p.id = '$id_penjualan' and td.transaksi_jenis = 'Penjualan'";
        //echo "<pre>$sql</pre>";
        return $this->db->query($sql);
    }
    
    function penjualan_barang_load_data_detail($no_daftar) {
        $sql = "select p.id as no_penjualan, o.generik, p.resep_id as status, td.*, bp.id as id_pb, bp.barcode, bp.margin, bp.diskon, b.nama as barang, st.nama as satuan_terkecil, 
            bp.isi, o.kekuatan, r.nama as pabrik, s.nama as satuan, sd.nama as sediaan from penjualan p
            join pendaftaran pdf on (pdf.no_daftar = p.no_daftar)
            join pasien ps on (ps.no_rm = pdf.pasien)
            join transaksi_detail td on (p.id = td.transaksi_id)
            join barang_packing bp on (td.barang_packing_id = bp.id)
            join barang b on (b.id = bp.barang_id)
            left join obat o on (o.id = b.id)
            left join satuan s on (s.id = o.satuan_id)
            left join satuan st on (st.id = bp.terkecil_satuan_id)
            left join sediaan sd on (sd.id = o.sediaan_id)
            left join relasi_instansi r on (r.id = b.pabrik_relasi_instansi_id)
            where p.no_daftar = '$no_daftar' and td.transaksi_jenis = 'Penjualan'";
        //echo "<pre>$sql</pre>";
        return $this->db->query($sql);
    }
    
    function pasien_load_data($no_daftar) {
        $sql = "select p.arrive_time, ps.no_rm, p.no_daftar, pd.nama as pasien from pendaftaran p
            join pasien ps on (p.pasien = ps.no_rm)
            join penduduk pd on (ps.id = pd.id)
            where p.no_daftar = '$no_daftar'";
        return $this->db->query($sql);
    }
    
    function penjualan_jasa_detail_load_data_detail($no_daftar) {
        $sql = "select jpd.*, jpd.id as id_jasa, jpd.tarif, jpd.frekuensi, (jpd.frekuensi*jpd.tarif) as subtotal, (jpd.tarif*jpd.frekuensi) as total, tk.nama as kategori, l.nama as layanan
            from pendaftaran pdf
            join jasa_penjualan_detail jpd on (pdf.no_daftar = jpd.no_daftar)
            join pasien ps on (ps.no_rm = pdf.pasien)
            join tarif t on (jpd.tarif_id = t.id)
            join tarif_kategori tk on (t.tarif_kategori_id = tk.id)
            join layanan l on (t.id_layanan = l.id)
            where jpd.no_daftar = '$no_daftar' order by jpd.id";
        //echo "<pre>$sql</pre>";
        return $this->db->query($sql);
    }
    
    function rawat_inap_detail_load_data_detail($no_daftar) {
        $sql = "select i.*, u.nama as unit, t.kelas, t.no, t.tarif from inap_rawat_kunjungan i
            join pelayanan_kunjungan pk on (pk.id = i.id_pelayanan_kunjungan)
            join tt t on (i.tt_id = t.id)
            join unit u on (t.unit_id = u.id)
            where pk.id_kunjungan = '$no_daftar'";
        return $this->db->query($sql);
    }
    
    function penjualan_jasa_load_data($param) {
        $q = null;
        if ($param['awal'] != null and $param['akhir'] != null) {
            $q.=" and date(jpd.waktu) between '".date2mysql($param['awal'])."' and '".date2mysql($param['akhir'])."'";
        }
        if ($param['nakes'] != null) {
            $q.=" and jpd.id_kepegawaian_nakes = '$param[nakes]'";
        }
        $sql = "select jpd.*, pdf.no_daftar, jpd.id as id_jasa, t.nominal, t.jasa_nakes,
            jpd.frekuensi, ps.no_rm, pd.nama as pasien, pp.nama as pegawai,
            (jpd.frekuensi*t.nominal) as subtotal, (t.nominal*jpd.frekuensi) as total, 
            l.nama as layanan
            from pendaftaran pdf
            join pelayanan_kunjungan pk on (pdf.no_daftar = pk.id_kunjungan)
            join jasa_penjualan_detail jpd on (pk.id = jpd.id_pelayanan_kunjungan)
            join pasien ps on (ps.no_rm = pdf.pasien)
            join penduduk pd on (pd.id = ps.id)
            join tarif t on (jpd.tarif_id = t.id)
            join kepegawaian kp on (kp.id = jpd.id_kepegawaian_nakes)
            left join penduduk pp on (pp.id = kp.penduduk_id)
            join layanan l on (t.id_layanan = l.id) where pdf.no_daftar is not null $q
            and jpd.id_kepegawaian_nakes is not null and l.nama != 'Sewa Kamar'";
        //echo "<pre>$sql</pre>";
        return $this->db->query($sql);
    }

    function pendapatan_load_data($param) {
        $q = null;
        if ($param['awal'] != null and $param['akhir'] != null) {
            $q.=" and date(jpd.waktu) between '".date2mysql($param['awal'])."' and '".date2mysql($param['akhir'])."'";
        }
        if ($param['layanan'] !== '') {
            $q.=" and pk.id_jurusan_kualifikasi_pendidikan = '".$param['layanan']."'";
        }
        $sql = "select (jpd.jasa_sarana * jpd.frekuensi) as jasa_sarana,
                  (jpd.jasa_tindakan_rs * jpd.frekuensi) as jasa_tindakan_rs, 
                  (jpd.bhp * jpd.frekuensi) as bhp,
                  jpd.waktu,
            jpd.id as id_jasa, pdf.no_daftar, 
            jpd.frekuensi, ps.no_rm, pd.nama as pasien,
            (jpd.frekuensi*t.nominal) as subtotal, (t.nominal*jpd.frekuensi) as total, 
            l.nama as layanan, CONCAT_WS('; ',l.nama, pr.nama, t.bobot, t.kelas) as nama_tarif
            from pendaftaran pdf
            join pelayanan_kunjungan pk on(pk.id_kunjungan = pdf.no_daftar)
            join jasa_penjualan_detail jpd on (pk.id = jpd.id_pelayanan_kunjungan)
            left join pasien ps on (ps.no_rm = pdf.pasien)
            left join penduduk pd on (pd.id = ps.id)
            join tarif t on (jpd.tarif_id = t.id)
            left join profesi pr on (pr.id = t.id_profesi)
            left join layanan l on (t.id_layanan = l.id) 
            where pdf.no_daftar is not null $q order by pdf.no_daftar asc";
        //echo "<pre>$sql</pre>";

        $total = "select sum(jpd.jasa_sarana * jpd.frekuensi) as sum_jasa_sarana,
                  sum(jpd.jasa_tindakan_rs * jpd.frekuensi) as sum_tindakan_rs, 
                  sum(jpd.bhp * jpd.frekuensi) as sum_bhp
                  from pendaftaran pdf 
                  join pelayanan_kunjungan pk on(pk.id_kunjungan = pdf.no_daftar)
                  join jasa_penjualan_detail jpd on (pk.id = jpd.id_pelayanan_kunjungan)
                  join tarif t on (jpd.tarif_id = t.id)
                  where pdf.no_daftar is not null $q";
        //echo "<pre>".$total."</pre>";
        $query = $this->db->query($sql);
        $ret['data'] = $query->result();
        $ret['jumlah'] = $this->db->query($sql)->num_rows();
        $ret['total'] = $this->db->query($total)->row();
        return $ret;
    }

    function rawat_inap_detail_load_data($id_pasien) {
        $exe = $this->db->query("select * from pendaftaran p join pasien ps on (p.pasien = ps.no_rm) where ps.id = '$id_pasien' order by p.no_daftar desc limit 1");
        $row = $exe->row();
        $sql = "select i.*, u.nama as unit, t.kelas, t.no, t.tarif from inap_rawat_kunjungan i
            join tt t on (i.tt_id = t.id)
            join unit u on (t.unit_id = u.id)
            where i.no_daftar = '" . $row->no_daftar . "'";
        return $this->db->query($sql);
    }

    function load_data_tagihan($id_kunjungan) {
        $sql = "select id as id_nota, waktu, total, bayar, sisa, no_daftar 
            from kunjungan_billing_pembayaran
            where no_daftar = '$id_kunjungan'
            ";
        //echo $sql;
        return $this->db->query($sql);
    }

    function load_last_data_tagihan($id_kunjungan) {
        $sql = "select * from kunjungan_billing_pembayaran
            where no_daftar = '$id_kunjungan'  order by id desc limit 1";
        return $this->db->query($sql);
    }

    function data_kunjungan_muat_data_total_jasa($no_daftar) {
        $sql = "select sum(t.nominal*jpd.frekuensi) as total_jasa 
            from pendaftaran p
            join pelayanan_kunjungan pk on(pk.id_kunjungan = p.no_daftar)
            join jasa_penjualan_detail jpd on (jpd.id_pelayanan_kunjungan = pk.id)            
            join tarif t on (t.id = jpd.tarif_id)
            join layanan l on (l.id = t.id_layanan)
            where pk.id_kunjungan = '$no_daftar' and jpd.frekuensi is not null";
        $exe = $this->db->query($sql);
        return $exe->row();
    }
    
    function data_rawat_inap_tagihan_run($no_daftar) {
        $sql2 = "select 
            sum(ceiling(if((TIMESTAMPDIFF(HOUR, jpd.waktu, now())/24)=0, 1,  (TIMESTAMPDIFF(HOUR, jpd.waktu, now())/24)))*t.nominal) as total_inap_run
                    from jasa_penjualan_detail jpd
                    join pelayanan_kunjungan pk on(pk.id = jpd.id_pelayanan_kunjungan)
                    join inap_rawat_kunjungan i on(i.id_pelayanan_kunjungan = jpd.id_pelayanan_kunjungan)
                    left join tarif t on (jpd.tarif_id = t.id)
                    where pk.id_kunjungan =  '$no_daftar' and i.keluar_waktu is null ";
        return $this->db->query($sql2)->row();
    }

    function data_retur_penjualan($no_daftar) {
        $cek = $this->db->query("select td.hna, bp.margin, bp.diskon, td.ppn_jual, td.masuk
            from pendaftaran p
            join pelayanan_kunjungan pk on (p.id = pk.id_kunjungan)
            join penjualan pj on (pj.id_pelayanan_kunjungan = pk.id)
            join transaksi_detail td on (pr.id = td.transaksi_id)
            join barang_packing bp on (bp.id = td.barang_packing_id)
            where td.transaksi_jenis = 'Retur Penjualan'
            and p.no_daftar = '$no_daftar'")->result();
        $total_retur = 0;
        foreach ($cek as $data) {
            $harga_kotor = $data->hna + ($data->hna * $data->margin / 100) - ($data->hna * ($data->diskon / 100));
            $harga_jual = $harga_kotor + ($harga_kotor*$data->ppn_jual/100);
            $subtotal =  $data->masuk*$harga_jual;
            $total_retur = $total_retur+$subtotal;
        }
        return $total_retur;
    }
    
    function data_kunjungan_muat_data_total_barang($no_daftar) {
        $sql = "select sum(pj.total) as total_barang 
            from pendaftaran p
            join pelayanan_kunjungan pk on (p.no_daftar = pk.id_kunjungan)
            join penjualan pj on (pj.id_pelayanan_kunjungan = pk.id)
            where p.no_daftar = '$no_daftar'";
        $exe = $this->db->query($sql);
        return $exe->row();
    }

    function office_muat_data() {
        $sql = "select a.*, k.nama as kelurahan from rumah_sakit a
            left join kelurahan k on (k.id = a.kelurahan_id)";
        return $this->db->query($sql);
    }

    function get_tagihan_jasa($no_daftar) {
        $sql = "select sum(t.nominal*jpd.frekuensi) as tarif_layanan, 
            l.nama from jasa_penjualan_detail jpd 
            join pelayanan_kunjungan pk on (pk.id = jpd.id_pelayanan_kunjungan)
            join tarif t on (jpd.tarif_id = t.id)
            join layanan l on (t.id_layanan = l.id)
            where pk.id_kunjungan = '$no_daftar' group by l.id";
        return $this->db->query($sql);
    }

    function get_tagihan_barang($no_daftar) {
        $sql = "select sum(pj.total) as total_barang from pendaftaran p
            join pelayanan_kunjungan pk on (p.no_daftar = pk.id_kunjungan)
            join penjualan pj on (pj.id_pelayanan_kunjungan = pk.id)
            where p.no_daftar = '$no_daftar'";
        return $this->db->query($sql);
    }

    function load_data_pembayaran($id_billing_pembayaran, $no_daftar) {
        $sql = "select id as id_nota, waktu, total, sum(bayar) as bayar, sisa 
            from kunjungan_billing_pembayaran
            where id <= '$id_billing_pembayaran' and no_daftar = '$no_daftar'
            ";
        return $this->db->query($sql);
    }
    
    function load_data_rawat_inap_tagihan($no_daftar) {
        $sql = "select i.*, t.kelas, t.no, u.nama 
            from inap_rawat_kunjungan i 
            join tt t on (i.tt_id = t.id) 
            join unit u on (t.unit_id = u.id)
            where i.no_daftar = '$no_daftar'";
        return $this->db->query($sql);
    }

    function laporan_load_data($limit, $start ,$awal, $akhir, $pembayaran) {
        $q = null;
        $limitation = " limit $start, $limit";

        if ($pembayaran == 'lunas') {
            $q = "and kbp.sisa = '0'";
        }
        if ($pembayaran == 'tidak') {
            $q = "and p.no_daftar not in (select no_daftar from kunjungan_billing_pembayaran)";
        }
        if ($awal !== '' or $akhir !== '') {
            $q.=" and kbp.waktu between '" . datetime2mysql($awal) . "' and '" . datetime2mysql($akhir) . "'";
        }
        if ($pembayaran == 'belum') {
            $q = "and kbp.sisa > 0";
        }
        if ($pembayaran == 'belum' or $pembayaran == 'lunas') {
            $sql = "select p.*, kbp.id as no_pembayaran,kbp.*, ps.no_rm,
            pd.nama as pasien, pd.lahir_tanggal, dp.alamat
            from pendaftaran p 
            join kunjungan_billing_pembayaran kbp on (p.no_daftar = kbp.no_daftar)
            join pasien ps on (p.pasien = ps.no_rm)
            join penduduk pd on (pd.id = ps.id)
            join dinamis_penduduk dp on (dp.penduduk_id = pd.id)
            inner join (
                select no_daftar, max(id) as id_max from kunjungan_billing_pembayaran
                group by no_daftar
            ) kbi on (kbi.no_daftar = kbp.no_daftar and kbi.id_max = kbp.id)
            inner join (
                select penduduk_id, max(id) as id_max from dinamis_penduduk group by penduduk_id
            ) tm on (tm.penduduk_id = dp.penduduk_id and tm.id_max = dp.id)
            where p.no_daftar is not NULL $q
            union 
                select p.*, kbp.id as no_pembayaran,kbp.*, '-' as no_rm,
                pd.nama as pasien, pd.lahir_tanggal, dp.alamat
                from pendaftaran p 
                join kunjungan_billing_pembayaran kbp on (p.no_daftar = kbp.no_daftar)
                join penduduk pd on (pd.id = p.id_customer)
                join dinamis_penduduk dp on (dp.penduduk_id = pd.id)
                inner join (
                    select no_daftar, max(id) as id_max from kunjungan_billing_pembayaran
                    group by no_daftar
                ) kbi on (kbi.no_daftar = kbp.no_daftar and kbi.id_max = kbp.id)
                inner join (
                    select penduduk_id, max(id) as id_max from dinamis_penduduk group by penduduk_id
                ) tm on (tm.penduduk_id = dp.penduduk_id and tm.id_max = dp.id)
                where p.no_daftar is not NULL $q";
        }
        if ($pembayaran == 'tidak') {
            $sql = "select p.*, kbp.id as no_pembayaran, kbp.sisa,kbp.bayar, ps.no_rm, 
            pd.nama as pasien, pd.lahir_tanggal, dp.alamat
            from pendaftaran p 
            left join kunjungan_billing_pembayaran kbp on (p.no_daftar = kbp.no_daftar)
            join pasien ps on (p.pasien = ps.no_rm)
            join penduduk pd on (pd.id = ps.id)
            join dinamis_penduduk dp on (dp.penduduk_id = pd.id)
            inner join (
                select penduduk_id, max(id) as id_max from dinamis_penduduk group by penduduk_id
            ) tm on (tm.penduduk_id = dp.penduduk_id and tm.id_max = dp.id)
            where p.no_daftar not in (select no_daftar from kunjungan_billing_pembayaran)
            union 
                select p.*, kbp.id as no_pembayaran, kbp.sisa,kbp.bayar, '-' as no_rm, 
                pd.nama as pasien, pd.lahir_tanggal, dp.alamat
                from pendaftaran p 
                left join kunjungan_billing_pembayaran kbp on (p.no_daftar = kbp.no_daftar)
                join penduduk pd on (pd.id = p.id_customer)
                join dinamis_penduduk dp on (dp.penduduk_id = pd.id)
                inner join (
                    select penduduk_id, max(id) as id_max from dinamis_penduduk group by penduduk_id
                ) tm on (tm.penduduk_id = dp.penduduk_id and tm.id_max = dp.id)
                where p.no_daftar not in (select no_daftar from kunjungan_billing_pembayaran)";
        }
        //echo "<pre>".$sql."</pre>";
        $data['data'] = $this->db->query($sql.$limitation)->result();
        $data['jumlah'] = $this->db->query($sql)->num_rows();
        return $data;
    }

    function pp_uang_save() {
        $this->db->trans_begin();
        $nama = post_safe('nama');
        $tanggal = datetime2mysql(post_safe('tanggal'));

        $data_pp_uang = array(
            'dokumen_no' => post_safe('nodoc'),
            'tanggal' => $tanggal,
            'pegawai_penduduk_id' => $this->session->userdata('id_user'),
            'jenis' => post_safe('jenis')
        );
        $this->db->insert('uang_penerimaan_pengeluaran', $data_pp_uang);
        $id_transaksi = $this->db->insert_id();
        $jml = post_safe('jml');
        $jenis = post_safe('jenis');
        foreach ($nama as $key => $data) {
            if ($data != '' and $jml[$key] != '') {
                $row = $this->db->query("select akhir_saldo from kas order by id desc limit 1")->row();
                $awal = $row->akhir_saldo;
                if ($jenis == 'Penerimaan') {
                    $terima = currencyToNumber($jml[$key]);
                    $keluar = 0;
                    $akhir = $terima + $awal;
                } else {
                    $terima = 0;
                    $keluar = currencyToNumber($jml[$key]);
                    $akhir = $awal - $keluar;
                }
                //$qry = _exec("insert into kas values ('','$tanggal','$id_transaksi','Penerimaan dan Pengeluaran','$data','$awal','$terima','$keluar','$akhir')");
                $data_kas = array(
                    'waktu' => datetime2mysql(post_safe('tanggal')),
                    'transaksi_id' => $id_transaksi,
                    'transaksi_jenis' => 'Penerimaan dan Pengeluaran',
                    'penerimaan_pengeluaran_nama' => $data,
                    'awal_saldo' => $awal,
                    'penerimaan' => $terima,
                    'pengeluaran' => $keluar,
                    'akhir_saldo' => $akhir
                );
                $this->db->insert('kas', $data_kas);
            }
        }
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $status = FALSE;
        } else {
            $this->db->trans_commit();
            $status = TRUE;
        }
        $result['status'] = $status;
        $result['id_pp_uang'] = $id_transaksi;
        return $result;
    }

    function pp_uang_delete($id) {
        $this->db->trans_begin();
        $this->db->delete('uang_penerimaan_pengeluaran', array('id' => $id));
        $this->db->delete('kas', array('transaksi_id' => $id, 'transaksi_jenis' => 'Penerimaan dan Pengeluaran'));

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $status = FALSE;
        } else {
            $this->db->trans_commit();
            $status = TRUE;
        }
        $result['status'] = $status;
        return $result;
    }

    function insert_jurnal($time, $id_kunjungan , $jenis ,$ket ,$id_sub ,$debetkredit , $debetkreditvalue){
        $jurnal = array(
            'waktu' => $time,
            'id_transaksi' => $id_kunjungan,
            'jenis_transaksi' => $jenis,
            'ket_transaksi' => $ket,
            'id_sub_sub_sub_sub_rekening' => $id_sub,
            $debetkredit => $debetkreditvalue
        );
        $this->db->insert('jurnal', $jurnal);

    }

    function pembayaran_save() {
        $this->db->trans_begin();
        $this->load->helper('url');
        $id_kunjungan = post_safe('id_kunjungan');
        $sumre = post_safe('sumre');
        $waktu = gmdate('Y-m-d H:i:s', gmdate('U') + 25200);

        if (isset($id_kunjungan) and $id_kunjungan != '') {
            //Ubah waktu keluar pasien luar
            $this->db->where('no_daftar', $id_kunjungan)->update('pendaftaran', array('waktu_keluar' => date('Y-m-d H:i:s')));


            $cek = $this->m_billing->load_data_tagihan(post_safe('id_kunjungan'))->num_rows();
            if ($cek > 0) {
                $last = $this->get_sisa_tagihan(post_safe('id_kunjungan'));

                $bayar = currencyToNumber(post_safe('bayar'))+$sumre;
                $sisa_hasil = $bayar - $last;
                if ($sisa_hasil < 0) {
                    $sisa = abs($sisa_hasil);
                } else {
                    $sisa = 0;
                }
                $data = array(
                    'waktu' => $waktu,
                    'no_daftar' => $id_kunjungan,
                    'total' => $last,
                    'bayar' => currencyToNumber(post_safe('bayar'))+$sumre,
                    'sisa' => $sisa
                );
            } else {

                $sisa = (currencyToNumber(post_safe('bayar'))+$sumre) - post_safe('totallica');
                $data = array(
                    'waktu' => $waktu,
                    'no_daftar' => $id_kunjungan,
                    'total' => post_safe('totallica'),
                    'bayar' => currencyToNumber(post_safe('bayar'))+$sumre,
                    'sisa' => abs($sisa)
                );
            }
            $this->db->insert('kunjungan_billing_pembayaran', $data);
            
            $id_bayar = $this->db->insert_id();
            
            $id = post_safe('id_kunjungan');
            
            $id_asuransi = post_safe('id_asuransi');
            $no_polis = post_safe('nopolis');
            $nominal_reimburse = post_safe('nominal_reimburse');
            $re = post_safe('re');
            $total_reimburse = 0;

            // insert Jurnal
            $this->insert_jurnal($waktu, $id_kunjungan,
                'Pembayaran Billing Pasien','', 1, 'debet',
                currencyToNumber(post_safe('bayar')) );


            // insert Jurnal


            if (!empty($id_asuransi)) {
                $total_reimburse = 0;
                foreach ($id_asuransi as $key => $rows) {
                    $cek = $this->db->query("select * from asuransi_produk where id = '$rows'")->row();
                    $data_asuransi_penjualan = array(
                        'id_pembayaran' => $id_bayar,
                        'id_produk_asuransi' => $rows,
                        'no_polis' => $no_polis[$key],
                        'reimbursement_persentase' => $cek->reimbursement,
                        'reimbursement_rupiah' => $cek->reimbursement_rupiah,
                        'nominal_tereimburse' => $re[$key]
                    );
                    $this->db->insert('produk_asuransi_pembayaran',$data_asuransi_penjualan);
           
                    
                    if ($rows === '2') {
                        
                       $this->insert_jurnal($waktu, $id_kunjungan,
                            'Pembayaran Billing Pasien','', 7, 'debet',$re[$key]);
                        
                    }
                    if ($rows === '1') {
                       $this->insert_jurnal($waktu, $id_kunjungan,
                            'Pembayaran Billing Pasien','', 9, 'debet',$re[$key]);
                    }
                    


                    $total_reimburse = $total_reimburse + $re[$key];
                    if ($this->db->trans_status() === FALSE) {
                        $this->db->trans_rollback();
                    }
                }                

                 $this->insert_jurnal($waktu, $id_kunjungan,
                            'Pembayaran Billing Pasien','', 231, 'kredit',post_safe('totallica'));
            } else {

                $jurnal_231 = array(
                    'waktu' => date("Y-m-d H:i:s"),
                    'id_transaksi' => $id_kunjungan,
                    'jenis_transaksi' => 'Pembayaran Billing Pasien',
                    'ket_transaksi' => '',
                    'id_sub_sub_sub_sub_rekening' => '231',
                    'kredit' => currencyToNumber(post_safe('bayar'))+$total_reimburse
                );
                $this->db->insert('jurnal', $jurnal_231);
            }

        }
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $status = FALSE;
        } else {
            $this->db->trans_commit();
            $status = TRUE;
        }
        $result['status'] = $status;
        $result['id_kunjungan'] = $id;
        return $result;
    }
    
    function kunjungan_pasien_load_data($limit, $start, $var) {
        $q = null;
        $paging = " limit " . $start . "," . $limit . " ";

        if ($var['awal'] != '' and $var['akhir']) {
            $q.=" and date(p.arrive_time) between '".date2mysql($var['awal'])."' and '".date2mysql($var['akhir'])."'";
        }
        if ($var['nomor'] != '') {
            $q.=" and p.no_daftar = '$var[nomor]'";
        }
        if ($var['no_rm'] != '') {
            $q.=" and p.pasien = '$var[no_rm]'";
        }
        if ($var['pasien'] != '') {
            $q.=" and pd.nama like '%$var[pasien]%'";
        }
        if ($var['alamat'] != '') {
            $q.=" and dp.alamat like '%$var[alamat]%'";
        }
        if ($var['id_kelurahan'] != '') {
            $q.=" and dp.kelurahan_id = '$var[id_kelurahan]'";
        }

       // $q .= " order by p.no_daftar";
        $sql = "select p.*, pd.nama, dp.alamat,
            k.nama as kelurahan, p.pasien as no_rm, kc.nama as kecamatan, kb.nama as kabupaten, pr.nama as provinsi 
            from pendaftaran p
            left join pasien ps on (p.pasien = ps.no_rm)
            left join penduduk pd on (pd.id = ps.id)
            left join dinamis_penduduk dp on (pd.id = dp.penduduk_id)
            left join kelurahan k on (k.id = dp.kelurahan_id)
            left join kecamatan kc on (kc.id = k.kecamatan_id)
            left join kabupaten kb on (kb.id = kc.kabupaten_id)
            left join provinsi pr on (pr.id = kb.provinsi_id)
            left join kunjungan_billing_pembayaran kbp on (kbp.no_daftar = p.no_daftar)
            inner join (
                select penduduk_id, max(id) as id_max from dinamis_penduduk group by penduduk_id
            ) dm on (dm.penduduk_id = dp.penduduk_id and dm.id_max = dp.id)
            where p.no_daftar is not NULL $q";
        //echo "<pre>".$sql."</pre>";
        $data = $this->db->query($sql . $paging);
        $ret['data'] = $data->result();
        $ret['jumlah'] = $this->db->query($sql)->num_rows;
        return $ret;
    }
    
    function billing_jasa_pasien_load_data($id_pasien) {
        $exe = $this->db->query("select * from pendaftaran p join pasien ps on (p.pasien = ps.no_rm) where ps.id = '$id_pasien' order by p.no_daftar desc limit 1");
        $row = $exe->row();
        $sql = "select jpd.*, jpd.id as id_jasa, jpd.tarif, jpd.frekuensi, (jpd.frekuensi*jpd.tarif) as subtotal, (jpd.tarif*jpd.frekuensi) as total, tk.nama as kategori, l.nama as layanan
            from pendaftaran pdf
            join jasa_penjualan_detail jpd on (pdf.no_daftar = jpd.no_daftar)
            join pasien ps on (ps.no_rm = pdf.pasien)
            join tarif t on (jpd.tarif_id = t.id)
            join layanan l on (t.id_layanan = l.id)
            where jpd.no_daftar = '" . $row->no_daftar . "' order by jpd.id";
        //echo "<pre>$sql</pre>";
        return $this->db->query($sql);
    }
    
    function penjualan_barang_load_data2($no_daftar) {
        $sql = "select p.total, p.id as no_penjualan, b.kekuatan, rp.id as id_retur, b.generik, td.disc_pr as diskon, td.harga_jual as subtotal,
            p.id_resep as status, td.*, td.qty as keluar, bp.id as id_pb, bp.barcode, b.margin_resep as margin,
            b.nama as barang, st.nama as satuan_terkecil, 
            bp.isi, r.nama as pabrik, s.nama as satuan, sd.nama as sediaan from penjualan p
            left join retur_penjualan rp on (p.id = rp.id_penjualan)
            join pelayanan_kunjungan pk on (p.id_pelayanan_kunjungan = pk.id)
            join pendaftaran pdf on (pdf.no_daftar = pk.id_kunjungan)
            join pasien ps on (ps.no_rm = pdf.pasien)
            join detail_penjualan td on (p.id = td.id_penjualan)
            join kemasan bp on (td.id_kemasan = bp.id)
            join barang b on (b.id = bp.id_barang)
            left join satuan s on (s.id = bp.id_kemasan)
            left join satuan st on (st.id = bp.id_satuan)
            left join sediaan sd on (sd.id = b.id_sediaan)
            left join pabrik r on (r.id = b.id_pabrik)
            where pdf.no_daftar = '$no_daftar'";
        //echo "<pre>$sql</pre>";
        return $this->db->query($sql);
    }

    function penjualan_jasa_detail_load_data($no_daftar, $id_pk, $jenis_layanan = null) {
        $q = "";
        if ($jenis_layanan == 'partial') {
            $q = "and (jl.nama not in ('Pendaftaran Kunjungan Pasien', 'Akomodasi Kamar Inap') 
                or jl.nama is null) ";
        }else if ($jenis_layanan != null) {
            $q = "and jl.nama = '$jenis_layanan'";
        }
        
        if ($id_pk !== null) {
            $q .= " and pk.id = '".$id_pk."' ";
        }
        $sql = "select b.nama as barang, jpd.*, jpd.id as id_jasa, jpd.frekuensi,
            (IFNULL(jpd.frekuensi,1)*t.nominal) as subtotal, pp.nama as nakes, i.masuk_waktu, i.keluar_waktu,
            l.nama as layanan, t.nominal, jl.nama as jenis_layanan
            from pendaftaran pdf
            join pelayanan_kunjungan pk on (pk.id_kunjungan = pdf.no_daftar)
            join jasa_penjualan_detail jpd on (pk.id = jpd.id_pelayanan_kunjungan)
            left join kepegawaian kp on (kp.id = jpd.id_kepegawaian_nakes)
            left join penduduk pp on (pp.id = kp.penduduk_id)
            left join pasien ps on (ps.no_rm = pdf.pasien)
            join tarif t on (jpd.tarif_id = t.id)
            left join kemasan bp on (bp.id = t.id_barang_sewa)
            left join barang b on (bp.id_barang = b.id)
            join layanan l on (t.id_layanan = l.id)
            left join sub_sub_jenis_layanan ss on(ss.id = l.id_sub_sub_jenis_layanan)
            left join sub_jenis_layanan s on(s.id = ss.id_sub_jenis_layanan)
            left join jenis_layanan jl on(jl.id = s.id_jenis_layanan)
            left join inap_rawat_kunjungan i on(i.id_tarif = jpd.tarif_id 
                        and i.masuk_waktu = jpd.waktu)
            where pdf.no_daftar = '" . $no_daftar . "' $q order by jpd.id";
        //echo "<pre>$sql</pre>";
        return $this->db->query($sql);
    }
    
    function penjualan_jasa_detail_load_data3($no_daftar) {
        $sql = "select b.nama as barang, jpd.*, pp.nama as nakes, jpd.id as id_jasa, (jpd.frekuensi*t.nominal) as subtotal, l.nama as layanan, sjl.nama as nama_sub, t.nominal
            from pendaftaran pdf
            join jasa_penjualan_detail jpd on (pdf.no_daftar = jpd.no_daftar)
            left join kepegawaian kp on (jpd.id_kepegawaian_nakes = kp.id)
            left join penduduk pp on (pp.id = kp.penduduk_id)
            left join pasien ps on (ps.no_rm = pdf.pasien)
            left join tarif t on (jpd.tarif_id = t.id)
            left join layanan l on (t.id_layanan = l.id)
            left join barang_packing bp on (bp.id = t.id_barang_sewa)
            left join barang b on (bp.barang_id = b.id)
            left join sub_sub_jenis_layanan ssjl on (ssjl.id = l.id_sub_sub_jenis_layanan)
            left join sub_jenis_layanan sjl on (sjl.id = ssjl.id_sub_jenis_layanan)
            where jpd.no_daftar = '$no_daftar' order by jpd.id";
        //echo "<pre>$sql</pre>";
        return $this->db->query($sql);
    }


    function get_total_pembayaran($no_daftar){
        $sql_jasa = "select sum(jpd.frekuensi*t.nominal) as total_jasa
                    from jasa_penjualan_detail jpd
                    left join tarif t on (jpd.tarif_id = t.id)
                    where jpd.no_daftar = '$no_daftar'";
        $total_jasa = $this->db->query($sql_jasa)->row()->total_jasa;

        $sql_inap = "select (sum(ina.subtotal) + din.total_jalan) as total_inap from inap_rawat_kunjungan ina
                    inner join (
                    select i.no_daftar, sum(i.tarif* datediff(now(), i.masuk_waktu)) as total_jalan from                 inap_rawat_kunjungan i where i.no_daftar = '32' and i.keluar_waktu is NULL
                    )din on (din.no_daftar = ina.no_daftar)
                    where ina.no_daftar = '$no_daftar'";
        $total_inap = $this->db->query($sql_inap)->row()->total_inap;

        return $total_jasa+$total_inap;

    }
    
    function biaya_kunjungan($no_daftar, $jenis_layanan = null) {

        $q = "and (jl.nama not in ('Pendaftaran Kunjungan Pasien', 'Akomodasi Kamar Inap') 
            or jl.nama is null) ";
        if ($jenis_layanan != null) {
            $q ="and jl.nama = '$jenis_layanan'";
        }

        $sql = "select jpd.*, p.nama as nakes, jpd.id as id_jasa, 
            sum(jpd.frekuensi*t.nominal) as subtotal, l.nama, t.nominal,
            jl.nama as jenis_layanan, i.masuk_waktu, i.keluar_waktu
            from pendaftaran pdf
            join pelayanan_kunjungan pk on(pk.id_kunjungan = pdf.no_daftar)
            join jasa_penjualan_detail jpd on (pk.id = jpd.id_pelayanan_kunjungan)
            left join kepegawaian kp on (jpd.id_kepegawaian_nakes = kp.id)
            left join penduduk p on (p.id = kp.penduduk_id)
            left join pasien ps on (ps.no_rm = pdf.pasien)
            left join tarif t on (jpd.tarif_id = t.id)
            left join layanan l on (t.id_layanan = l.id)
            left join sub_sub_jenis_layanan ss on(ss.id = l.id_sub_sub_jenis_layanan)
            left join sub_jenis_layanan s on(s.id = ss.id_sub_jenis_layanan)
            left join jenis_layanan jl on(jl.id = s.id_jenis_layanan)
            left join inap_rawat_kunjungan i on(i.id_pelayanan_kunjungan = jpd.id_pelayanan_kunjungan)

            where pk.id_kunjungan = '$no_daftar' $q group by jl.nama";
        //echo $sql."<br/><br/>";
        return $this->db->query($sql);
    }
    
    function get_data_tagihan_jasa($no_daftar, $jenis_layanan = null) {
        $q = "and j.nama not in ('Pendaftaran Kunjungan Pasien', 'Akomodasi Kamar Inap') or j.nama is null";
        if ($jenis_layanan != null) {
            $q ="and j.nama = '$jenis_layanan'";
        }
        $sql = "select jpd.waktu, j.nama, t.nominal, jpd.frekuensi, t.nominal*jpd.frekuensi as subtotal from jasa_penjualan_detail jpd
            join tarif t on (jpd.tarif_id = t.id)
            join layanan l on (t.id_layanan = l.id)
            join sub_sub_jenis_layanan ss on (l.id_sub_sub_jenis_layanan = ss.id)
            join sub_jenis_layanan sj on (ss.id_sub_jenis_layanan = sj.id)
            join jenis_layanan j on (sj.id_jenis_layanan = j.id)
                where jpd.no_daftar = '$no_daftar' $q
            ";
        //echo "<pre>".$sql."</pre>";
        return $this->db->query($sql);
    }
    
    function get_asuransi_pembayaran() {
        $sql = "select ap.nama, p.no_polis as polis_no from produk_asuransi_pembayaran p 
            join kunjungan_billing_pembayaran k on (p.id_pembayaran = k.id)
            join asuransi_produk ap on (ap.id = p.id_produk_asuransi)";
        return $this->db->query($sql);
    }

    function cek_pembayaran($no_daftar){
       $sisa = $this->get_sisa_tagihan($no_daftar);
        if ($sisa === 0) {
            $status = true;
        } else {
            $status = false;
        }
        return $status;
    }

    function get_sisa_tagihan($no_daftar){
        $tb = $this->data_kunjungan_muat_data_total_barang($no_daftar);
        $tj = $this->data_kunjungan_muat_data_total_jasa($no_daftar);
        $ti = $this->data_rawat_inap_tagihan_run($no_daftar);

        $total_pembayaran = $this->total_pembayaran($no_daftar);

        $total = ($tb->total_barang + $tj->total_jasa + $ti->total_inap_run);
        $sisa = $total - $total_pembayaran->total_pembayaran;

        return $sisa;
    }
    
    function load_data_asuransi_by_nodaftar($nodaftar) {
        $sql = "select ap.*, r.nama as instansi, pk.no_polis from pelayanan_kunjungan pk
            join asuransi_produk ap on (pk.id_produk_asuransi = ap.id)
            join relasi_instansi r on (ap.relasi_instansi_id = r.id)
            where pk.id_kunjungan = '$nodaftar'";
        return $this->db->query($sql);
    }

}

?>