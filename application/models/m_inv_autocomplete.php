<?php

class M_inv_autocomplete extends CI_Model {

    function load_data_instansi_relasi($jenis = null, $q) {
        $w = '';
        if ($jenis != null) {
            $w = " and j.nama = '$jenis' ";
        }
        $sql = "select i.*, j.nama as jenis from relasi_instansi i
        join relasi_instansi_jenis j on (i.relasi_instansi_jenis_id = j.id)
        where i.nama like ('%$q%') $w order by locate('$q', i.nama)";
        return $this->db->query($sql);
    }

    function load_data_penduduk($jenis = null, $q) {
        $sort = null;
        if ($jenis != NULL) {
            $sort.=" and pf.nama = '$jenis'";
        }
        $sql = "select p.nama, dp.*, kl.nama as kelurahan from penduduk p
        join dinamis_penduduk dp on (p.id = dp.penduduk_id)
        left join kelurahan kl on (kl.id = dp.kelurahan_id)
        left join pekerjaan pf on (pf.id = dp.pekerjaan_id)
        where p.nama like ('%$q%') $sort and dp.id in (select max(id) from dinamis_penduduk group by penduduk_id) order by locate('$q', p.nama)";
        return $this->db->query($sql);
    }

    function load_penduduk($q) {
        $sql = "select p.*, d.alamat, d.kelurahan_id, d.pendidikan_id, d.pekerjaan_id,
            d.profesi_id, d.pernikahan, d.agama, k.nama as tempat_lahir
            from penduduk p
            left join dinamis_penduduk d on (p.id = d.penduduk_id)
            left join kabupaten k on (k.id = p.lahir_kabupaten_id)
            inner join (
                    select max(id) as id_max, penduduk_id from dinamis_penduduk group by penduduk_id
            ) dm on (dm.id_max = d.id and dm.penduduk_id = d.penduduk_id)
            where p.nama like ('%$q%') order by locate('$q', p.nama)";
        return $this->db->query($sql);
    }

    function load_data_pegawai($q){
        $w = '';

        $sql = "select p.id as id_penduduk, p.nama, p.gender, kp.*, jk.id as id_jurusan, jk.nama as jurusan_kualifikasi 
            from kepegawaian kp
            join penduduk p on (kp.penduduk_id = p.id)
            left join jurusan_kualifikasi_pendidikan jk on (kp.id_jurusan_kualifikasi_pendidikan = jk.id)
            left join jenis_jurusan_kualifikasi_pendidikan jjk on (jjk.id = jk.id_jenis_jurusan_kualifikasi_pendidikan)
            where p.nama like ('%$q%') and jjk.nama = 'Medis' $w order by locate('$q', p.nama)";
        //echo $sql;
        return $this->db->query($sql);
    }

    function load_data_pegawai_nakes($q){
        $w = '';

        $sql = "select p.id as id_penduduk, p.nama, p.gender, kp.*, jk.id as id_jurusan, jk.nama as jurusan_kualifikasi 
            from kepegawaian kp
            join penduduk p on (kp.penduduk_id = p.id)
            left join jurusan_kualifikasi_pendidikan jk on (kp.id_jurusan_kualifikasi_pendidikan = jk.id)
            left join jenis_jurusan_kualifikasi_pendidikan jjk on (jjk.id = jk.id_jenis_jurusan_kualifikasi_pendidikan)
            where p.nama like ('%$q%') and jjk.nakes = 'Ya' $w order by locate('$q', p.nama)";
        //echo $sql;
        return $this->db->query($sql);
    }

    function load_data_pegawai_profesi($q, $profesi){
        $w = '';

        $sql = "select p.id as id_penduduk, p.nama, p.gender, kp.*, jk.id as id_jurusan, jk.nama as jurusan_kualifikasi 
            from kepegawaian kp
            join penduduk p on (kp.penduduk_id = p.id)
            left join jurusan_kualifikasi_pendidikan jk on (kp.id_jurusan_kualifikasi_pendidikan = jk.id)
            left join jenis_jurusan_kualifikasi_pendidikan jjk on (jjk.id = jk.id_jenis_jurusan_kualifikasi_pendidikan)
            where p.nama like ('%$q%') and jk.nama like '%$profesi%' $w order by locate('$q', p.nama)";
        //echo $sql;
        return $this->db->query($sql);
    }

    function load_data_penduduk_pasien($name = null, $id = null) {
        if ($id != NULL) {
            $q = " where ps.no_rm = '$id'";
        } else if ($name != null) {
            $q = "where p.nama like ('%$name%') or ps.no_rm like ('%$name%') limit 0, 20";
        }
        $sql = "select p.nama, p.keterangan, p.lahir_tanggal,dp.*, p.gender,p.darah_gol,
        kl.nama as kelurahan, ps.no_rm , k.nama as tempat_lahir, p.telp
        from penduduk p
        join dinamis_penduduk dp on (p.id = dp.penduduk_id)
        join pasien ps on (p.id = ps.id)
        left join kelurahan kl on (dp.kelurahan_id = kl.id)
        left join kabupaten k on (k.id = p.lahir_kabupaten_id)
        inner join (
            select penduduk_id, max(id) as id_max
            from dinamis_penduduk group by penduduk_id
        ) idp on (idp.penduduk_id = dp.penduduk_id and idp.id_max = dp.id)
        $q ";
        //echo $sql;
        return $this->db->query($sql);
    }

    
    function load_data_penduduk_pasien_form_resep($name = null, $id = null) {
        if ($id != NULL) {
            $q = " where ps.no_rm = '$id'";
        } else if ($name != null) {
            $q = "and (p.nama like ('%$name%') or ps.no_rm like ('%$name%'))";
        }
        $sql = "select p.nama, p.lahir_tanggal,dp.*, p.gender,p.darah_gol,
        ps.no_rm from pendaftaran pdf
        join pasien ps on (pdf.pasien = ps.no_rm)
        join penduduk p on (ps.id = p.id)
        join dinamis_penduduk dp on (p.id = dp.penduduk_id)
        inner join (
            select pasien, max(no_daftar) as id_max from pendaftaran group by pasien
        ) pdm on (pdf.pasien = pdm.pasien and pdf.no_daftar = pdm.id_max)
        inner join (
            select penduduk_id, max(id) as id_max from dinamis_penduduk group by penduduk_id
        ) dpm on (dpm.penduduk_id = dp.penduduk_id and dpm.id_max = dp.id)
        where pdf.waktu_keluar is NULL
        $q";
        //echo $sql;
        return $this->db->query($sql);
    }

    function load_data_penduduk_asuransi($id_penduduk) {
        $query = $this->db->query("select * from asuransi_kepesertaan a join asuransi_produk p on (a.asuransi_produk_id = p.id) where a.penduduk_id = '$id_penduduk'")->result();
        foreach ($query as $key => $rows) {
            echo++$key . " " . $rows->nama . "<br/>";
        }
    }

    function load_data_penduduk_profesi($jenis = null, $q, $jns = null) {
        $sort = null;
        if ($jenis != NULL) {
            $sort.=" and pf.jenis = '$jenis'";
        }
        if ($jns != NULL) {
            $sort.=" and pf.jenis = '$jns'";
        }
        $sql = "select p.nama, p.id as id_penduduk, dp.*, kl.nama as kelurahan, pf.nama as  profesi from penduduk p
        join dinamis_penduduk dp on (p.id = dp.penduduk_id)
        left join kelurahan kl on (kl.id = dp.kelurahan_id)
        left join profesi pf on (pf.id = dp.profesi_id)
        where p.nama like ('%$q%') $sort and dp.id in (select max(id) from dinamis_penduduk group by penduduk_id) order by locate('$q', p.nama)";
        //echo $sql;
        return $this->db->query($sql);
    }
    
    function load_data_nakes($q) {
        $sql = "select p.id as id_penduduk, p.nama, p.gender,k.*, j.nama as jurusan, kp.nama as profesi from 
            kepegawaian k
            join penduduk p on (k.penduduk_id = p.id)
            join jurusan_kualifikasi_pendidikan j on (k.id_jurusan_kualifikasi_pendidikan = j.id)
            join kualifikasi_pendidikan kp on (k.id_kualifikasi_pendidikan = kp.id)
            where p.nama like ('%$q%') order by locate('$q', p.nama)";
        return $this->db->query($sql);
    }

    function load_data_user_system($q) {
        $sql = "select p.*, dp.alamat from penduduk p left join users u on (p.id = u.id) 
            join dinamis_penduduk dp on (dp.penduduk_id = p.id)
            inner join (
                select penduduk_id, max(id) as id_max
                from dinamis_penduduk GROUP by penduduk_id
            ) tm on (tm.penduduk_id = dp.penduduk_id and tm.id_max = dp.id)
            where p.nama like ('%$q%') and p.id not in (select id from users) order by locate('$q', p.nama)";
        return $this->db->query($sql);
    }

    function load_data_packing_barang($q, $extra_param = null) {
        $param = NULL;
        if ($extra_param != NULL) {
            $param.=" and b.id = '$extra_param'";
        }
        $sql = "select o.id as id_obat, o.generik, bk.nama as kategori, bp.*, r.nama as pabrik, b.id as id_barang, sd.nama as sediaan, b.nama, s.nama as satuan, st.nama as satuan_terkecil, stb.nama as satuan_terbesar, o.kekuatan from barang_packing bp
        join barang b on (b.id = bp.barang_id)
        join barang_kategori bk on (bk.id = b.barang_kategori_id)
        left join obat o on (b.id = o.id)
        left join satuan s on (s.id = o.satuan_id)
        left join satuan st on (st.id = bp.terkecil_satuan_id)
        left join satuan stb on (stb.id = bp.terbesar_satuan_id)
        left join sediaan sd on (sd.id = o.sediaan_id)
        left join relasi_instansi r on (r.id = b.pabrik_relasi_instansi_id)
        where b.nama like ('%$q%') $param order by bp.isi desc";
        //echo $sql;
        return $this->db->query($sql);
    }
    
    function get_hna_packing($id_packing) {
        $sql = "select max(id) as id, barang_packing_id, hna, hpp from transaksi_detail where barang_packing_id = '$id_packing'";
        return $this->db->query($sql);
    }
    
    function load_data_packing_barang_resep($q, $extra_param = null) {
        $param = NULL;
        if ($extra_param != NULL) {
            $param.=" and b.id = '$extra_param'";
        }
        $sql = "select bk.nama as kategori, o.id as id_obat, o.high_alert, o.generik, bp.*, r.nama as pabrik, b.id as id_barang, sd.nama as sediaan, b.nama, s.nama as satuan, st.nama as satuan_terkecil, stb.nama as satuan_terbesar, o.kekuatan from barang_packing bp
        join barang b on (b.id = bp.barang_id)
        left join obat o on (b.id = o.id)
        left join satuan s on (s.id = o.satuan_id)
        left join satuan st on (st.id = bp.terkecil_satuan_id)
        left join satuan stb on (stb.id = bp.terbesar_satuan_id)
        left join sediaan sd on (sd.id = o.sediaan_id)
        left join relasi_instansi r on (r.id = b.pabrik_relasi_instansi_id)
        left join barang_kategori bk on (bk.id = b.barang_kategori_id)
        where bk.jenis = 'Farmasi' and b.nama like ('%$q%') $param order by locate ('$q', b.nama)";
        //echo "<pre>".$sql."</pre>";
        return $this->db->query($sql);
    }

    function load_data_packing_barang_per_ed($q, $extra_param = null) {
        $param = NULL;
        if ($extra_param != NULL) {
            $param.=" and b.id = '$extra_param'";
        }
        $ext = "";
        if ($this->session->userdata('unit') === 'Pelayanan Farmasi') {
            $ext = "and bk.jenis = 'Farmasi'";
        }
        $sql = "select bk.id as id_kategori, bk.nama as kategori, o.id as id_obat, o.generik, bp.*, r.nama as pabrik, td.ed, 
            td.ppn, bp.ppn_jual, b.id as id_barang, sd.nama as sediaan, b.nama, s.nama as satuan, st.nama as satuan_terkecil, 
            stb.nama as satuan_terbesar, o.kekuatan from barang_packing bp
            join barang b on (b.id = bp.barang_id)
            join barang_kategori bk on (bk.id = b.barang_kategori_id)
            left join obat o on (b.id = o.id)
            left join satuan s on (s.id = o.satuan_id)
            left join satuan st on (st.id = bp.terkecil_satuan_id)
            left join satuan stb on (stb.id = bp.terbesar_satuan_id)
            left join sediaan sd on (sd.id = o.sediaan_id)
            left join relasi_instansi r on (r.id = b.pabrik_relasi_instansi_id)
            join transaksi_detail td on (td.barang_packing_id = bp.id)
            where b.nama like ('%$q%') $ext and td.ed > '" . date("Y-m-d") . "' and td.unit_id = '" . $this->session->userdata('id_unit') . "' $param group by bp.id, td.ed order by locate ('$q', b.nama)";
        //echo "<pre>".$sql."</pre>";
        return $this->db->query($sql);
    }
    
    function load_data_packing_barang_pemusnahan_per_ed($q, $extra_param = null) {
        $param = NULL;
        if ($extra_param != NULL) {
            $param.=" and b.id = '$extra_param'";
        }
        $ext = "";
        if ($this->session->userdata('unit') === 'Pelayanan Farmasi') {
            $ext = "and bk.jenis = 'Farmasi'";
        }
        $sql = "select bk.id as id_kategori, bk.nama as kategori, o.id as id_obat, o.generik, bp.*, r.nama as pabrik, td.ed, 
            td.hpp, td.hna, td.ppn, bp.ppn_jual, b.id as id_barang, sd.nama as sediaan, b.nama, s.nama as satuan, st.nama as satuan_terkecil, 
            stb.nama as satuan_terbesar, o.kekuatan from barang_packing bp
            join barang b on (b.id = bp.barang_id)
            join barang_kategori bk on (bk.id = b.barang_kategori_id)
            left join obat o on (b.id = o.id)
            left join satuan s on (s.id = o.satuan_id)
            left join satuan st on (st.id = bp.terkecil_satuan_id)
            left join satuan stb on (stb.id = bp.terbesar_satuan_id)
            left join sediaan sd on (sd.id = o.sediaan_id)
            left join relasi_instansi r on (r.id = b.pabrik_relasi_instansi_id)
            join transaksi_detail td on (td.barang_packing_id = bp.id)
            where b.nama like ('%$q%') $ext and td.unit_id = '" . $this->session->userdata('id_unit') . "' $param group by bp.id, td.ed order by locate ('$q', b.nama)";
        //echo "<pre>".$sql."</pre>";
        return $this->db->query($sql);
    }

    function distribusi_load_data($id, $id_pb = NULL) {
        $q = null;
        if ($id_pb != null) {
            $q .=" and barang_packing_id = '$id_pb'";
        }
        $sql = "select o.id as id_obat, o.generik, td.*, bp.id as id_pb, b.nama as barang, st.nama as satuan_terkecil, bp.isi, o.kekuatan, r.nama as pabrik, 
            s.nama as satuan, sd.nama as sediaan, u.nama as unit, stb.nama as satuan_terbesar, pd.nama as pegawai from transaksi_detail td
            join distribusi d on (d.id = td.transaksi_id)
            join penduduk pd on (pd.id = d.pegawai_penduduk_id)
            join barang_packing bp on (td.barang_packing_id = bp.id)
            join barang b on (bp.barang_id = b.id)
            left join obat o on (b.id = o.id)
            left join relasi_instansi r on (r.id = b.pabrik_relasi_instansi_id)
            left join satuan s on (s.id = o.satuan_id)
            left join satuan st on (st.id = bp.terkecil_satuan_id)
            left join satuan stb on (stb.id = bp.terbesar_satuan_id)
            left join sediaan sd on (sd.id = o.sediaan_id)
            left join unit u on (d.tujuan_unit_id = u.id)
            where td.transaksi_id = '$id' $q and td.transaksi_jenis = 'Distribusi'";
        //echo "<pre>".$sql."</pre>";
        return $this->db->query($sql);
    }

    function load_data_rop($id) {
        $start = $this->db->query("select date(waktu) as tanggal from transaksi_detail where transaksi_jenis = 'Pemesanan' and barang_packing_id = '$id' order by waktu desc limit 1")->row();
        $end   = $this->db->query("select date(waktu) as tanggal from transaksi_detail where transaksi_jenis = 'Pembelian' and barang_packing_id = '$id' order by waktu desc limit 1")->row();
        if (!isset($start->tanggal) and !isset($end->tanggal)) {
            $sql = "select td.*, (sum(td.masuk)-sum(td.keluar)) as sisa, td.ss*0 as average_usage from transaksi_detail td
                    where td.barang_packing_id = '$id' and td.transaksi_jenis != 'Pemesanan'";
            //echo $sql;
        } else {
        $sql = "select id, 
            (select avg(selisih_waktu_beli) from transaksi_detail where transaksi_jenis = 'Pembelian') as selisih_waktu_beli,
            (select (sum(masuk) - sum(keluar)) as sisa from transaksi_detail where transaksi_jenis != 'Pemesanan' and barang_packing_id = '$id') as sisa, 
            (select datediff('".(isset($end->tanggal)?$end->tanggal:'0')."','".(isset($start->tanggal)?$start->tanggal:'0')."')) as leadtime_hours,
            (select ss from transaksi_detail where transaksi_jenis = 'Pembelian' and barang_packing_id = '$id' order by id desc limit 1) as ss,
            (select avg(keluar) from transaksi_detail where barang_packing_id = '$id' and transaksi_jenis in ('Pemakaian','Penjualan') and date(waktu) 
                between '".(isset($start->tanggal)?$start->tanggal:'')."' and '".(isset($end->tanggal)?$end->tanggal:'')."') as average_usage
                from transaksi_detail t
            where id = (select max(id) from transaksi_detail where barang_packing_id = '$id' and transaksi_jenis != 'Pemesanan')
            ";
        //echo $sql;
        }
        return $this->db->query($sql)->row();
    }
    
    function get_data_sisa($id) {
        $sql = $this->db->query("select IFNULL(sum(masuk)-sum(keluar),'0') as sisa from transaksi_detail where barang_packing_id = '$id' and transaksi_jenis != 'Pemesanan'")->row();
        return $sql;
    }

    function get_harga_jual($id) {
        $sql = "select d.hpp, b.margin, d.ppn, ((d.hna*(b.margin/100))+d.hna)+(((d.hna*(b.margin/100))+d.hna)*(d.ppn/100)) as harga, b.diskon from transaksi_detail d 
            join barang_packing b on (d.barang_packing_id = b.id) 
            inner join (
                select barang_packing_id, max(id) as id_max from transaksi_detail where unit_id = '" . $this->session->userdata('id_unit') . "' group by barang_packing_id
            ) tm on (d.barang_packing_id = tm.barang_packing_id and d.id = tm.id_max)
            where d.transaksi_jenis != 'Pemesanan' 
            and d.barang_packing_id = '$id' 
            and d.unit_id = '" . $this->session->userdata('id_unit') . "' order by d.id desc limit 1";
        return $this->db->query($sql);
    }

    function get_nomor_pemesanan($q) {
        $sql = "select p.*, r.nama as pabrik, pd.nama as sales from pemesanan p
            join relasi_instansi r on (p.suplier_relasi_instansi_id = r.id)
            left join penduduk pd on (p.salesman_penduduk_id = pd.id)
            where p.id not in (select pemesanan_id from pembelian) and p.id like ('%$q%') order by locate ('$q', p.id)";
        return $this->db->query($sql);
    }

    function get_nomor_pembelian($q) {
        $sql = "select distinct sum(td.subtotal-(td.subtotal*(td.beli_diskon_percentage/100)))*(p.ppn/100) as ppn_rupiah,
            sum(td.subtotal-(td.subtotal*(td.beli_diskon_percentage/100))) as total, 
            p.*, r.nama as instansi, (select sum(jumlah_bayar) from inkaso where pembelian_id = '$q') as jumlah_terbayar 
            from pembelian p
            join transaksi_detail td on (td.transaksi_id = p.id)
            join relasi_instansi r on (p.suplier_relasi_instansi_id = r.id)
            where p.id like '%$q%' and td.transaksi_jenis = 'Pembelian' group by p.id";
        return $this->db->query($sql);
    }
    
    function get_tagihan_pembelian($q) {
        $sql = "select distinct sum(td.subtotal-(td.subtotal*(td.beli_diskon_percentage/100)))*(p.ppn/100) as ppn_rupiah,
            p.total, 
            p.*, r.nama as instansi, (select sum(jumlah_bayar) from inkaso where pembelian_id = '$q') as jumlah_terbayar 
            from pembelian p
            join transaksi_detail td on (td.transaksi_id = p.id)
            join relasi_instansi r on (p.suplier_relasi_instansi_id = r.id)
            where p.id = '$q' and td.transaksi_jenis = 'Pembelian' group by p.id";
        return $this->db->query($sql);
    }

    function get_detail_inkaso($id_inkaso) {
        $sql = "select distinct sum(td.subtotal)+(sum(td.subtotal)*(p.ppn/100)) as total, p.*, r.nama as instansi, i.*
            from inkaso i
            join pembelian p on (i.pembelian_id = p.id)
            join transaksi_detail td on (td.transaksi_id = p.id)
            join relasi_instansi r on (p.suplier_relasi_instansi_id = r.id)
            where i.id = '$id_inkaso' and td.transaksi_jenis = 'Pembelian' group by p.id";
        //echo "<pre>".$sql."</pre>";
        return $this->db->query($sql);
    }

    function get_last_transaction($id_pb, $ed) {
        $sql = "select * from transaksi_detail 
             where barang_packing_id = '$id_pb' and ed = '$ed' and unit_id = '" . $this->session->userdata('id_unit') . "' order by id desc limit 1";
        return $this->db->query($sql);
    }

    function get_nomor_distribusi($q) {
        $sql = "select distinct d.*, p.nama as pegawai, date(td.waktu) as waktu 
            from distribusi d 
            join penduduk p on (d.pegawai_penduduk_id = p.id) 
            join transaksi_detail td on (d.id = td.transaksi_id) 
            where d.id = '$q' and td.transaksi_jenis = 'Distribusi' and d.id not in (select distribusi_id from distribusi_penerimaan)";
        return $this->db->query($sql);
    }

    function get_diskon_instansi_relasi($id_instansi_relasi) {
        $sql = "select * from relasi_instansi where id = '$id_instansi_relasi'";
        return $this->db->query($sql);
    }

    function get_harga_barang_penjualan($id) {
        $sql = "select *, (hna+(hna*(margin/100))) as harga from barang_packing where id = '$id'";
        return $this->db->query($sql);
    }

    function get_penjualan_field($barcode) {
        $sql = "select d.hpp, bp.margin, d.hna, d.ed, (d.hna*(bp.margin/100)+d.hna) as harga, bp.diskon,
        o.id as id_obat, bp.*, r.nama as pabrik, b.id as id_barang, sd.nama as sediaan, b.nama, s.nama as satuan, st.nama as satuan_terkecil, 
        stb.nama as satuan_terbesar, o.kekuatan from transaksi_detail d 
            join barang_packing bp on (d.barang_packing_id = bp.id) 
            join barang b on (b.id = bp.barang_id)
            left join obat o on (b.id = o.id)
            left join satuan s on (s.id = o.satuan_id)
            left join satuan st on (st.id = bp.terkecil_satuan_id)
            left join satuan stb on (stb.id = bp.terbesar_satuan_id)
            left join sediaan sd on (sd.id = o.sediaan_id)
            left join relasi_instansi r on (r.id = b.pabrik_relasi_instansi_id)
            where d.transaksi_jenis != 'Pemesanan' and d.barang_packing_id = (select id from barang_packing where barcode = '$barcode') order by d.id desc limit 1";
        return $this->db->query($sql);
    }

    function get_layanan_jasa($q, $extraParam = null) {
        $x = null;
        if (isset($extraParam['layanan'])) {
            $x = " and l.nama != '".$extraParam['layanan']."'";
        }

        if (isset($extraParam['id_unit'])) {
            //$x .= " and t.id_unit = '".$extraParam['id_unit']."'";
        }
        $sql = "select b.nama as barang , l.nama as layanan, 
            u.nama as unit, t.*, t.nominal, max(t.id) as id_tarif, 
            p.nama as profesi, j.nama as jurusan from tarif t
            left join layanan l on (l.id = t.id_layanan)
            left join profesi p on (t.id_profesi = p.id)
            left join jurusan_kualifikasi_pendidikan j on (t.id_jurusan_kualifikasi_pendidikan = j.id)
            left join unit u on (t.id_unit = u.id)
            left join kemasan bp on (bp.id = t.id_barang_sewa)
            left join barang b on (b.id = bp.id_barang)
        where t.id is not null $x and l.nama like ('%$q%') or 
        b.nama like ('%$q%') or t.id like ('%$q%')  group by t.id_layanan, t.id_profesi, t.id_jurusan_kualifikasi_pendidikan, t.jenis_pelayanan_kunjungan, t.id_unit, t.bobot, t.kelas order by locate ('$q',l.nama)";
        //echo $sql;        
        return $this->db->query($sql);
    }

    function get_layanan_laboratorium($q){
        $sql = "select l.* from layanan l
        left join sub_sub_jenis_layanan ssj on (ssj.id = l.id_sub_sub_jenis_layanan )
        left join sub_jenis_layanan sj on (sj.id = ssj.id_sub_jenis_layanan)
        left join jenis_layanan j on (j.id =  sj.id_jenis_layanan)
        where l.nama like ('%$q%') order by locate ('$q',l.nama)";
        return $this->db->query($sql);
    }

    function get_satuan($q){
        $sql = "select * from satuan
        where nama like ('%$q%') order by locate ('$q', nama)";
        return $this->db->query($sql);
    }
    
    function get_last_id_tarif($data) {
        $prof = "and id_profesi is NULL";
        if ($data['id_profesi'] != 'null') {
            $prof = " and id_profesi = '$data[id_profesi]'";
        }
        $juru = " and id_jurusan_kualifikasi_pendidikan is NULL";
        if ($data['id_jurusan'] != 'null') {
            $juru = " and id_jurusan_kualifikasi_pendidikan = '$data[id_jurusan]'";
        }
        $jpk = " and jenis_pelayanan_kunjungan is NULL";
        if ($data['jpk'] != 'null') {
            $jpk = " and jenis_pelayanan_kunjungan = '$data[jpk]'";
        }
        $unit = " and id_unit is NULL";
        if ($data['id_unit'] != 'null') {
            $unit = " and id_unit = '$data[id_unit]'";
        }
        $bobo = " and bobot is NULL";
        if ($data['bobot'] != 'null') {
            $bobo = " and bobot = '$data[bobot]'";
        }
        $klas = " and kelas is NULL";
        if ($data['kelas'] != null) {
            $klas = " and kelas = '$data[kelas]'";
        }
        $sql = "select max(id) as id from tarif where id is not NULL $prof $juru $jpk $unit $bobo $klas";
        //echo $sql;
        return $this->db->query($sql);
    }


    function layanan_jasa_load_data($q) {
        $sql = "select * from layanan where nama like ('%$q%') order by locate ('$q',nama)";
        return $this->db->query($sql);
    }

    function layanan_jasa_load_data_radiologi($q) {
        $sql = "select l.* from layanan l
                left join sub_sub_jenis_layanan ssj on (ssj.id = l.id_sub_sub_jenis_layanan )
                left join sub_jenis_layanan sj on (sj.id = ssj.id_sub_jenis_layanan)
                left join jenis_layanan j on (j.id =  sj.id_jenis_layanan)
                where l.nama like ('%$q%') order by locate ('$q',l.nama)";
        return $this->db->query($sql);
    }
    
    function tindakan_tarif_load_data($q) {
        $sql = "select t.id, t.id_layanan, CONCAT_WS('; ',l.nama,p.nama,t.jenis_pelayanan_kunjungan,t.bobot,t.kelas) as nama_tarif from tarif t
            join layanan l on (t.id_layanan = l.id)
            left join profesi p on (t.id_profesi = p.id)
            having nama_tarif like ('%$q%') order by locate('$q',nama_tarif)
            ";
        return $this->db->query($sql);
    }

    function load_data_produk_asuransi($q) {
        $sql = "select a.*, r.nama as instansi from asuransi_produk a
        join relasi_instansi r on (r.id = a.relasi_instansi_id)
        where a.nama like ('%$q%') order by locate('$q', a.nama)";
        return $this->db->query($sql);
    }

    function load_data_pabrik($q) {
        $sql = "select i.*, j.nama as jenis from relasi_instansi i
        join relasi_instansi_jenis j on (i.relasi_instansi_jenis_id = j.id)
        where i.nama like ('%$q%') and j.nama = 'Pabrik' order by locate('$q', i.nama)";
        return $this->db->query($sql);
    }

    function load_data_penduduk_dokter($q) {

        $sql = "select p.*, dp.*, p.id as penduduk_id from penduduk p
           join dinamis_penduduk dp on (p.id = dp.penduduk_id)
           join profesi pr on (pr.id = dp.profesi_id)
           where dp.id in (select max(id) from dinamis_penduduk group by penduduk_id) 
           and pr.nama in ('Dokter','Dokter Gigi')
           and p.nama like ('%$q%') order by locate ('$q',p.nama)
        ";
        return $this->db->query($sql);
    }

    function load_data_no_resep($q) {
        $sql = "select r.*, p.nama as dokter, pd.nama as pasien, ps.id as pasien_penduduk_id, ps.no_rm from resep r
            join penduduk p on (r.dokter_penduduk_id = p.id)
            join pelayanan_kunjungan pk on (r.id_pelayanan_kunjungan = pk.id)
            join pendaftaran pdf on (pk.id_kunjungan = pdf.no_daftar)
            join pasien ps on (pdf.pasien = ps.no_rm)
            join penduduk pd on (ps.id = pd.id)
            where r.id like '%$q%' group by ps.id order by locate ('$q', r.id)";
        return $this->db->query($sql);
    }

    function load_jasa_apoteker($id_resep) {
        $sql = "select sum(t.nominal) as jasa_apoteker from resep_r rr join tarif t on (t.id = rr.tarif_id) where rr.resep_id = '$id_resep'";
        return $this->db->query($sql);
    }
    
    function load_unit_kelas_by_no_rm($no_rm) {
        $no_daftar = $this->db->query("select no_daftar from pendaftaran where pasien = '$no_rm' order by no_daftar desc limit 1")->row();
        $sql = "select pk.kelas, u.nama, pk.jenis from pendaftaran p
            join pelayanan_kunjungan pk on (p.no_daftar = pk.id_kunjungan)
            join unit u on (pk.id_unit = u.id)
            inner join (
                select id_kunjungan, max(id) as id_max from pelayanan_kunjungan group by id_kunjungan
            ) pkm on (pkm.id_kunjungan = pk.id_kunjungan and pkm.id_max = pk.id)
            where pk.id_kunjungan = '".$no_daftar->no_daftar."'";
        return $this->db->query($sql);
    }

    function load_penjualan_by_no_resep($noresep) {
        $sql = "select bk.id as id_kategori, rr.*, o.id as id_obat, stb.nama as satuan_terbesar, o.generik, td.ed, rd.jual_harga, rd.pakai_jumlah, b.nama as barang, bp.margin, bp.diskon, rd.barang_packing_id, bp.barcode, r.nama as pabrik, 
            o.kekuatan, st.nama as satuan_terkecil, bp.ppn_jual, sd.nama as sediaan, bp.isi, td.keluar, s.nama as satuan, td.harga, bp.diskon as percent,
            bp.hna
            from resep_r rr
                join resep_racik_r_detail rd on (rr.id = rd.r_resep_id)
                join barang_packing bp on (rd.barang_packing_id = bp.id)
                join barang b on (b.id = bp.barang_id)
                join barang_kategori bk on (bk.id = b.barang_kategori_id)
                left join obat o on (o.id = b.id)
                left join satuan s on (s.id = o.satuan_id)
                left join satuan st on (st.id = bp.terkecil_satuan_id)
                left join satuan stb on (stb.id = bp.terbesar_satuan_id)
                left join sediaan sd on (sd.id = o.sediaan_id)
                left join relasi_instansi r on (r.id = b.pabrik_relasi_instansi_id)
                join transaksi_detail td on (bp.id = td.barang_packing_id)
                inner join (
                    select barang_packing_id, max(id) as id_max from transaksi_detail group by barang_packing_id, ed
                ) tm on (tm.barang_packing_id = td.barang_packing_id and tm.id_max = td.id)
                where td.transaksi_jenis != 'Pemesanan' and rr.resep_id = '$noresep' group by td.barang_packing_id, td.ed";
        //echo "<pre>".$sql."</pre>";
        return $this->db->query($sql);
    }

    function reretur_pembelian_load_id($id) {
        $sql = "select p.*, pdd.nama as pegawai, pdk.nama as salesman, r.nama as suplier from pembelian_retur p 
            join penduduk pdd on (p.pegawai_penduduk_id = pdd.id)
            left join penduduk pdk on (p.salesman_penduduk_id = pdk.id)
            join relasi_instansi r on (r.id = p.suplier_relasi_instansi)
            where p.id like ('%$id%') 
                and p.id not in (select retur_id from pembelian_retur_penerimaan) order by locate ('$id', p.id)";
        return $this->db->query($sql);
    }
    
    function reretur_pembelian_load_data($id_retur_pembelian) {
        $sql = "select td.barang_packing_id, td.ed, td.masuk, bk.jenis, bk.nama as nama_kategori, o.id as id_obat, bp.*, td.hpp, r.nama as pabrik, b.id as id_barang, sd.nama as sediaan, 
            b.nama as barang, td.keluar, s.nama as satuan, st.nama as satuan_terkecil, stb.nama as satuan_terbesar, o.kekuatan from pembelian_retur p
            join transaksi_detail td on (p.id = td.transaksi_id)
            join barang_packing bp on (bp.id = td.barang_packing_id)
            join barang b on (b.id = bp.barang_id)
            join barang_kategori bk on (bk.id = b.barang_kategori_id)
            left join obat o on (b.id = o.id)
            left join satuan s on (s.id = o.satuan_id)
            left join satuan st on (st.id = bp.terkecil_satuan_id)
            left join satuan stb on (stb.id = bp.terbesar_satuan_id)
            left join sediaan sd on (sd.id = o.sediaan_id)
            left join relasi_instansi r on (r.id = b.pabrik_relasi_instansi_id)
            where td.transaksi_jenis = 'Retur Pembelian' and p.id = '$id_retur_pembelian'";

//echo $sql;
        return $this->db->query($sql);
    }

    function get_layanan($q, $tindakan = NULL) {
        $srt = null;
        if ($tindakan != NULL) {
            $srt=" and nama != 'Sewa Barang' and nama != 'Sewa Kamar'";
        }
        $sql = "select * from layanan
        where nama like ('%$q%') $srt order by locate ('$q',nama)";
        return $this->db->query($sql);
    }

    function get_tarif_kategori($q) {
        $sql = "select * from tarif_kategori
        where nama like ('%$q%') order by locate ('$q',nama)";
        return $this->db->query($sql);
    }

//echo $sql;

    function reretur_penjualan_get_nomor($q) {
        $sql = "select p.*, pdd.nama as pegawai, pdk.nama as pembeli from penjualan_retur p 
            join penduduk pdd on (p.pegawai_penduduk_id = pdd.id)
            left join penduduk pdk on (p.pembeli_penduduk_id = pdk.id)
            where p.id like ('%$q%') and p.id not in (select penjualan_retur_id from penjualan_retur_pengeluaran) order by locate ('$q', p.id)";
        return $this->db->query($sql);
    }

    function reretur_penjualan_table($id) {
        $sql = "
            select td.*, o.id as id_obat, bp.barcode, bp.margin, bp.isi, bk.nama as nama_kategori, bk.jenis, bp.diskon, r.nama as pabrik, b.id as id_barang, sd.nama as sediaan, 
            b.nama as barang, td.keluar, s.nama as satuan, st.nama as satuan_terkecil, stb.nama as satuan_terbesar, o.kekuatan from penjualan_retur p
            join transaksi_detail td on (p.id = td.transaksi_id)
            join barang_packing bp on (bp.id = td.barang_packing_id)
            join barang b on (b.id = bp.barang_id)
            join barang_kategori bk on (bk.id = b.barang_kategori_id)
            left join obat o on (b.id = o.id)
            left join satuan s on (s.id = o.satuan_id)
            left join satuan st on (st.id = bp.terkecil_satuan_id)
            left join satuan stb on (stb.id = bp.terbesar_satuan_id)
            left join sediaan sd on (sd.id = o.sediaan_id)
            left join relasi_instansi r on (r.id = b.pabrik_relasi_instansi_id)
            where td.transaksi_jenis = 'Retur Penjualan' and p.id = '$id'";
        return $this->db->query($sql);
    }

    function get_barang($q) {
        $jenis = get_safe('jenis');
        $w = '';

        if ($jenis == 'Obat') {
            $w.=" and bk.jenis = 'Farmasi' or bk.jenis IS NULL";
        } 
        else if ($jenis == 'Rt') {
            $w.=" and bk.jenis = 'Rumah Tangga' or bk.jenis IS NULL ";
        }
        else if ($jenis == 'Gizi') {
            $w.=" and bk.jenis = 'Gizi' or bk.jenis IS NULL ";
        }else{
            $w = '';
        }

        $sql = "select b.id as id_barang, b.nama, r.nama as pabrik, bk.nama as kategori, 
            s.nama as satuan2, sd.nama as sediaan, o.kekuatan, o.id as id_obat, 
            st.nama as satuan from barang b
            left join barang_kategori bk on(bk.id = b.barang_kategori_id)
            left join obat o on (b.id = o.id)
            left join satuan s on (s.id = o.satuan_id)
            left join satuan st on (o.satuan_id = st.id)
            left join sediaan sd on (sd.id = o.sediaan_id)
            left join relasi_instansi r on (b.pabrik_relasi_instansi_id = r.id)
            where b.nama like ('%$q%') $w order by locate('$q', b.nama)";
        //echo $sql;
        return $this->db->query($sql);
    }

    function load_data_layanan_profesi($q) {
        $sql = "select * from layanan where nama like ('%$q%') order by locate('$q',nama)";
        return $this->db->query($sql);
    }

    function adm_layanan_profesi($id) {
        $q = null;
        if ($id != NULL) {
            $q.="where t.layanan_id = '$id'";
        }
        $sql = "
            select p.nama, t.* from tindakan_layanan_profesi_jasa t
            join profesi p on (t.profesi_id = p.id) $q
            ";

        return $this->db->query($sql);
    }

    function load_data_profesi($q) {
        $sql = "select * from profesi where jenis = 'Nakes' and nama like ('%$q%') order by locate('$q',nama)";
        return $this->db->query($sql);
    }

    function get_no_retur_distribusi($q) {
        $sql = "select p.*, date(p.waktu) as waktu, pdd.nama as pegawai, u.nama as unit from distribusi_retur p 
            join penduduk pdd on (p.pegawai_penduduk_id = pdd.id)
            join transaksi_detail td on (td.transaksi_id = p.id)
            join unit u on (td.unit_id = u.id)
            where p.id like ('%$q%') group by td.transaksi_id order by locate ('$q', p.id)";
        return $this->db->query($sql);
    }

    function load_data_retur_unit($id) {
        $sql = "select pdd.nama as pegawai,o.generik, td.barang_packing_id, td.ed, td.hpp, td.hna, td.masuk, o.id as id_obat, bp.*, r.nama as pabrik, b.id as id_barang, sd.nama as sediaan, 
        b.nama as barang, td.keluar, s.nama as satuan, st.nama as satuan_terkecil, stb.nama as satuan_terbesar, o.kekuatan from distribusi_retur p
        join transaksi_detail td on (p.id = td.transaksi_id)
        join penduduk pdd on (p.pegawai_penduduk_id = pdd.id)
        join barang_packing bp on (bp.id = td.barang_packing_id)
        join barang b on (b.id = bp.barang_id)
        left join obat o on (b.id = o.id)
        left join satuan s on (s.id = o.satuan_id)
        left join satuan st on (st.id = bp.terkecil_satuan_id)
        left join satuan stb on (stb.id = bp.terbesar_satuan_id)
        left join sediaan sd on (sd.id = o.sediaan_id)
        left join relasi_instansi r on (r.id = b.pabrik_relasi_instansi_id)
        where td.transaksi_jenis = 'Retur Distribusi' and p.id = '$id'";
        return $this->db->query($sql);
    }

    function hitung_detail_pemesanan($id_pb, $biaya) {
        $start = $this->db->query("select date(waktu) as tanggal from transaksi_detail where barang_packing_id = '$id_pb' and transaksi_jenis = 'Pemesanan' and unit_id = '" . $this->session->userdata('id_unit') . "' order by waktu desc limit 1")->row();
        $end = $this->db->query("select date(waktu) as tanggal from transaksi_detail where barang_packing_id = '$id_pb' and transaksi_jenis = 'Pembelian' and unit_id = '" . $this->session->userdata('id_unit') . "' order by waktu desc limit 1")->row();

        $result['eoq'] = 0;
        if (isset($start->tanggal) and isset($end->tanggal)) {
            $var1 = $this->db->query("select avg(keluar) as keluar, avg(leadtime_hours) as leadtime_hours from transaksi_detail where barang_packing_id = '$id_pb' and transaksi_jenis = 'Pemakaian' and date(waktu) between '" . $start->tanggal . "' and '" . $end->tanggal . "'")->row();
            $var2 = $this->db->query("select avg(keluar) as keluar, avg(leadtime_hours) as leadtime_hours from transaksi_detail where barang_packing_id = '$id_pb' and transaksi_jenis = 'Penjualan' and date(waktu) between '" . $start->tanggal . "' and '" . $end->tanggal . "'")->row();
            $hpp = $this->db->query("select avg(hpp) as hpp FROM transaksi_detail where barang_packing_id = '$id_pb' and transaksi_jenis = 'Pembelian' and date(waktu) between '" . $start->tanggal . "' and '" . $end->tanggal . "'")->row();
            if ($var1->keluar != NULL and $var2->keluar != NULL) {
                $result['eoq'] = round(sqrt((2 * ($var1->keluar) + ($var2->keluar * $biaya)) / (0.25 * $hpp->hpp)), 2);
            }
        }

        $result['eoi'] = 0;
        if (isset($start->tanggal) and isset($end->tanggal)) {
            $var1 = $this->db->query("select avg(keluar) as keluar, avg(leadtime_hours) as leadtime_hours from transaksi_detail where barang_packing_id = '$id_pb' and transaksi_jenis = 'Pemakaian' and date(waktu) between '" . $start->tanggal . "' and '" . $end->tanggal . "'")->row();
            $var2 = $this->db->query("select avg(keluar) as keluar, avg(leadtime_hours) as leadtime_hours from transaksi_detail where barang_packing_id = '$id_pb' and transaksi_jenis = 'Penjualan' and date(waktu) between '" . $start->tanggal . "' and '" . $end->tanggal . "'")->row();
            $hpp = $this->db->query("select avg(hpp) as hpp FROM transaksi_detail where barang_packing_id = '$id_pb' and transaksi_jenis = 'Pembelian' and date(waktu) between '" . $start->tanggal . "' and '" . $end->tanggal . "'")->row();
            if ($var1->keluar != NULL and $var2->keluar != NULL) {
                $result['eoi'] = round(sqrt((2 * $biaya) / (($var1->keluar + $var2->keluar) * (0.25 * $hpp->hpp))), 2);
            }
        }
        return $result;
    }

    function data_pasien_muat_data($q) {
        $w= '';
        $jenis = (isset($_GET['jenis']))?get_safe('jenis'):'';
        if($jenis != ''){
            $w = " and pdf.jenis_rawat = '$jenis'";
        }

        $sql = "select pdf.*,p.id as id_pasien,p.*, pd.nama, pdf.no_daftar,pd.gender,
            pk.nama as pekerjaan, pdi.nama as pendidikan, pd.*, kel.nama as kelurahan, dp.alamat,
            kelpj.nama as kelurahan_pj, pj.telp as telp_pj,
            rel.nama as instansi_rujukan, pd.darah_gol,
            kec.nama as kecamatan, kecpj.nama as kecamatan_pj
            from pasien p
            join penduduk pd on (p.id = pd.id)
            join pendaftaran pdf on (p.no_rm = pdf.pasien)
            join dinamis_penduduk dp on (pd.id = dp.penduduk_id)
            left join pekerjaan pk on (pk.id = dp.pekerjaan_id)
            left join pendidikan pdi on (pdi.id = dp.pendidikan_id)
            left join kelurahan kel on (kel.id = dp.kelurahan_id)
            left join kecamatan kec on (kec.id = kel.kecamatan_id)
            left join kelurahan kelpj on (pdf.kelurahan_id_pjwb = kelpj.id)
            left join kecamatan kecpj on (kelpj.kecamatan_id = kecpj.id)
            left join relasi_instansi rel on (rel.id = pdf.rujukan_instansi_id)
            inner join (
                select penduduk_id, max(id) as id_max
                from dinamis_penduduk GROUP BY penduduk_id
            ) dpi on (dp.penduduk_id = dpi.penduduk_id and dp.id = dpi.id_max)
            inner join (
                select pasien, max(no_daftar) as max_no_daftar
                from pendaftaran group by pasien
            ) pdfi on (pdf.pasien = pdfi.pasien and pdf.no_daftar = pdfi.max_no_daftar)
            where p.no_rm like ('%$q%') or pd.nama like ('%$q%') $w order by locate ('$q',p.no_rm)";
        $exe = $this->db->query($sql);
        return $exe->result();
    }

    function load_data_pelayanan_kunjungan_by_id_penduduk($id) {
        $sql = "select pk.*, u.nama as unit, u.nama as bangsal, pk.id as id_pelayanan, ap.nama as asuransi from pendaftaran p
            join pasien ps on (p.pasien = ps.no_rm)
            join penduduk pd on (ps.id = pd.id)
            join pelayanan_kunjungan pk on (p.no_daftar = pk.id_kunjungan)
            join unit u on (u.id = pk.id_unit)
            left join asuransi_produk ap on (ap.id = pk.id_produk_asuransi)
            inner join (
                select id_kunjungan, max(id) as id_max from pelayanan_kunjungan group by id_kunjungan
            ) pm on (pm.id_kunjungan = pk.id_kunjungan and pm.id_max = pk.id)
            where ps.id = '$id' order by pk.id desc limit 1";
        //echo "<pre>".$sql."</pre>";
        return $this->db->query($sql);
    }
    
    function tindakan_load_data($q){
        $sql = "select * from layanan
        where kode_icdixcm like ('%$q%')order by locate ('$q',kode_icdixcm)";
        $exe = $this->db->query($sql);
        return $exe->result();
    }
    
    function load_data_kategori_barang($q) {
        $sql = "select * from barang_kategori where nama like ('%$q%') order by locate ('$q',nama)";
        return $this->db->query($sql);
    }
    
    function load_data_pemesanan($id = null) {
        $q = NULL;
        if ($id != NULL) {
            $q = "and p.id = '$id'";
        }
        $sql = "select p.*, r.nama as supplier, pd.nama as salesman from pemesanan p
            join relasi_instansi r on (p.suplier_relasi_instansi_id = r.id)
            left join penduduk pd on (p.salesman_penduduk_id = pd.id)
            where p.id not in (select pemesanan_id from pembelian) $q
            order by p.id desc
            ";
        return $this->db->query($sql);
    }
    
    function delete_penjualan_jasa($id) {
        $this->db->trans_begin();
        $this->db->delete('jasa_penjualan_detail', array('id' => $id));
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $status = FALSE;
        } else {
            $this->db->trans_commit();
            $status = TRUE;
        }
        return $status;
    }
    
    function get_sisa_stok($id_packing, $ed) {
        $sql = "select (sum(masuk)-sum(keluar)) as sisa from transaksi_detail where barang_packing_id = '$id_packing' and ed = '$ed' and unit_id = '".$this->session->userdata('id_unit')."'";
        return $this->db->query($sql);
    }
    
    function get_sisa_stok_kemasan($id_packing) {
        $sql = "select (sum(masuk)-sum(keluar)) as sisa 
            from transaksi_detail 
            where barang_packing_id = '$id_packing' 
                and unit_id = '".$this->session->userdata('id_unit')."'
                and ";
        return $this->db->query($sql);
    }
    
    function get_detail_kemasan($id_packing) {
        $sql = "select bk.id as id_kategori, bk.nama as kategori, o.id as id_obat, o.generik, bp.*, r.nama as pabrik, td.ed, 
            td.hpp, td.hna, td.ppn, bp.ppn_jual, b.id as id_barang, sd.nama as sediaan, b.nama, s.nama as satuan, st.nama as satuan_terkecil, 
            stb.nama as satuan_terbesar, o.kekuatan from barang_packing bp
            join barang b on (b.id = bp.barang_id)
            join barang_kategori bk on (bk.id = b.barang_kategori_id)
            left join obat o on (b.id = o.id)
            left join satuan s on (s.id = o.satuan_id)
            left join satuan st on (st.id = bp.terkecil_satuan_id)
            left join satuan stb on (stb.id = bp.terbesar_satuan_id)
            left join sediaan sd on (sd.id = o.sediaan_id)
            left join relasi_instansi r on (r.id = b.pabrik_relasi_instansi_id)
            join transaksi_detail td on (td.barang_packing_id = bp.id)
            where bp.barcode = '$id_packing' and td.ed > '" . date("Y-m-d") . "' and td.unit_id = '" . $this->session->userdata('id_unit') . "' group by bp.id, td.ed";
        return $this->db->query($sql);
    }

}

?>