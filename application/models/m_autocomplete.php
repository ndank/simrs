<?php

class M_autocomplete extends CI_Controller {
    
    function generate_new_sp() {
        $tanggal = date2mysql($_GET['tanggal']);
        $yearmon = substr($tanggal, 0, 7);
        $monyear = substr($_GET['tanggal'], 3, 7);
        $sql = $this->db->query("select substr(id, 4,3) as id  from pemesanan where tanggal like ('%".$yearmon."%') order by tanggal desc limit 1");
        
        $row = $sql->row();
        if (!isset($row->id)) {
            $result['sp'] = "SP.001/".$monyear;
        } else {
            $result['sp'] = "SP.".str_pad((string)($row->id+1), 3, "0", STR_PAD_LEFT)."/".$monyear;
        }
        return $result;
    }
    
    function get_nomor_sp($q) {
        $sql = "select p.*, s.nama as supplier 
        FROM pemesanan p 
        join supplier s on (p.id_supplier = s.id) 
        where p.id not in (select id_pemesanan from penerimaan) and p.id like ('%$q%') order by locate('$q',p.id)";
        return $this->db->query($sql);
    }
    
    function get_data_pemesanan_penerimaan($id) {
        $sql = "select b.id as id_barang, b.nama, b.hna, b.kekuatan, st.nama as satuan_kekuatan, 
        s.id as id_kemasan, s.nama as kemasan, k.id, dp.jumlah, k.isi, k.isi_satuan 
        from detail_pemesanan dp
        join kemasan k on (k.id = dp.id_kemasan)
        join barang b on (b.id = k.id_barang)
        join satuan s on (k.id_kemasan = s.id)
        join satuan st on (b.satuan_kekuatan = st.id) where dp.id_pemesanan = '$id'";
        return $this->db->query($sql);
    }
    
    function get_detail_harga_barang_penerimaan($id_kemasan, $id_barang, $jml) {
        $get = $this->db->query("select b.*, k.id as id_packing from barang b
            join kemasan k on (b.id = k.id_barang)
            where b.id = '$id_barang' and k.id_kemasan = '$id_kemasan'")->row();

        $cek= $this->db->query("select is_harga_bertingkat from kemasan where id = '".$get->id_packing."'")->row();
        
        if ($cek->is_harga_bertingkat === '0') {
            $rows = $this->db->query("select b.*, k.id as id_packing, k.isi, k.isi_satuan, k.isi_satuan as isi_sat, (b.hna+(b.hna*(b.margin_resep/100))) as harga_jual, (b.hna+(b.hna*(b.margin_non_resep/100))) as harga_jual_nr from kemasan k join barang b on (k.id_barang = b.id) where k.id = '".$get->id_packing."'")->row();
            
        } else {
            $rows = $this->db->query("select d.*, d.hj_resep as harga_jual, k.isi, k.isi_satuan, k.isi_satuan as isi_sat, d.hj_non_resep as harga_jual_nr, k.id as id_packing, d.hj_resep as harga_jual_resep, (k.isi*k.isi_satuan) as isi_satuan
                from dinamic_harga_jual d
                join kemasan k on (d.id_kemasan = k.id)
                where d.id_kemasan = '".$get->id_packing."' and $jml between d.jual_min and d.jual_max")->row();
        }
        return $rows;
    }
    
    function get_data_penerimaan($id) {
        $sql = "select b.id as id_barang, CONCAT_WS(' ',b.nama, b.kekuatan, st.nama) as nama_barang, b.kekuatan, b.hna, st.nama as satuan_kekuatan, 
        s.id as id_kemasan, s.nama as kemasan, k.id, dp.*, k.isi, k.isi_satuan 
        from detail_penerimaan dp
        join kemasan k on (k.id = dp.id_kemasan)
        join barang b on (b.id = k.id_barang)
        left join satuan s on (k.id_kemasan = s.id)
        left join satuan st on (b.satuan_kekuatan = st.id) where dp.id_penerimaan = '$id'";
        return $this->db->query($sql);
    }
    
    function get_data_kemasan($id_barang) {
        $sql = "select s.id, s.nama as kemasan, st.nama as satuan_kecil, k.default_kemasan, k.isi from kemasan k
        join satuan s on (k.id_kemasan = s.id)
        left join satuan st on (k.id_satuan = st.id)
        where k.id_barang = '$id_barang'";
        return $this->db->query($sql);
    }
    
    function supplier($q) {
        $sql = "select * from supplier where nama like ('%$q%') order by locate('$q',nama)";
        return $this->db->query($sql);
    }
    
    function dokter($q) {
        $sql = "select p.*, IFNULL(pr.nama,'') as spesialis, dp.str_no, dp.sip_no 
            from penduduk p 
            join dinamis_penduduk dp on (p.id = dp.penduduk_id) 
            join profesi pr on (dp.profesi_id = pr.id)
            inner join (
                select max(id) as id_max, penduduk_id from dinamis_penduduk group by penduduk_id
            ) dm on (dp.id = dm.id_max and dp.penduduk_id = dm.penduduk_id)
            where p.nama like ('%$q%') and pr.nama = 'Dokter' order by locate('$q', p.nama)";
        return $this->db->query($sql);
    }
    
    function pasien($q) {
        $sql = "select p.*, dp.alamat, ps.no_rm
        from penduduk p
        join pasien ps on (p.id = ps.id)
        join dinamis_penduduk dp on (p.id = dp.penduduk_id) 
        inner join (
            select max(id) as id_max, penduduk_id from dinamis_penduduk group by penduduk_id
        ) dm on (dp.id = dm.id_max and dp.penduduk_id = dm.penduduk_id)
        where p.nama like ('%$q%') or p.id like ('%$q%') order by locate('$q', p.id)";
        return $this->db->query($sql);
    }
    
    function kabupaten($src, $id_prov = NULL) {
        $q = NULL;
        if ($id_prov !== NULL) {
            $q = "and k.provinsi_id = '$id_prov'";
        }
        $sql = "select k.*, p.nama as provinsi, k.nama as kabupaten 
            from kabupaten k
            join provinsi p on (k.provinsi_id = p.id) 
            where k.nama like ('%$src%') order by locate('$src',k.nama)";
        return $this->db->query($sql);
    }
    
    function kelurahan($q) {
        $sql = "select kl.*, kc.nama as kecamatan
            from kelurahan kl
            join kecamatan kc on (kl.kecamatan_id = kc.id)
            where kl.nama like ('%$q%') order by locate('$q',kl.nama)";
        return $this->db->query($sql);
    }
    
    function get_no_resep() {
        $row = $this->db->query("select substr(id, 1, 3) as jumlah from resep where date(waktu) like '%".date("Y-m")."%' order by waktu desc limit 1")->row();
        if (!isset($row->jumlah)) {
            $str = "001-".date("m")."/".date("Y");
        } else {
            $str = str_pad((string)($row->jumlah+1), 3, "0", STR_PAD_LEFT)."-".date("m")."/".date("Y");
        }
        return $str;
    }
    
    function load_no_resep($q) {
        $sql = "select r.*, IFNULL(pj.total,'0') as total_tagihan, IFNULL(sum(dp.bayar),'0') as terbayar, p.nama, p.id_asuransi, a.diskon as reimburse from resep r 
            join pelanggan p on (r.id_pasien = p.id)
            left join penjualan pj on (r.id = pj.id_resep)
            left join detail_bayar_penjualan dp on (pj.id = dp.id_penjualan)
            left join asuransi a on (p.id_asuransi = a.id)
            where r.id like ('%$q%') group by pj.id having terbayar = '0' or terbayar < total_tagihan";
        return $this->db->query($sql);
    }
    
    function barang($q) {
        $sql = "select b.*, p.nama as pabrik, g.nama as golongan, st.nama as satuan, sd.nama as sediaan,
        concat_ws(' ', b.nama, b.kekuatan, st.nama) as nama_barang
        from barang b 
        left join pabrik p on (b.id_pabrik = p.id)
        left join golongan g on (b.id_golongan = g.id)
        left join satuan st on (b.satuan_kekuatan = st.id)
        left join sediaan sd on (b.id_sediaan = sd.id) having nama_barang like ('%$q%')";
        return $this->db->query($sql);
    }
    
    function farmakoterapi($id) {
        $sql = "select * from kelas_terapi where id_farmako_terapi = '$id'";
        return $this->db->query($sql);
    }
    
    function golongan_load_data($id) {
        $sql ="select * from golongan where id = '$id'";
        return $this->db->query($sql);
    }
    
    function pabrik($q) {
        $sql = "select * from pabrik where nama like ('%$q%') order by locate('$q',nama)";
        return $this->db->query($sql);
    }
    
    function instansi($q) {
        $sql = "select * from instansi where nama like ('%$q%') order by locate('$q',nama)";
        return $this->db->query($sql);
    }
    
    function get_stok_sisa($id_barang) {
        $sql = "select (sum(masuk)-sum(keluar)) as sisa from stok where id_barang = '$id_barang'";
        return $this->db->query($sql);
    }
    
    function get_detail_harga_barang_resep($id, $jml) {
        $get = $this->db->query("select b.*, k.id as id_packing from barang b
            join kemasan k on (b.id = k.id_barang)
            where b.id = '$id' and k.default_kemasan = '1'")->row();

        $cek= $this->db->query("select is_harga_bertingkat from kemasan where id = '".$get->id_packing."'")->row();
        if ($cek->is_harga_bertingkat === '0') {
            $rows = $this->db->query("select b.*, k.id as id_packing, k.isi_satuan, 
                (b.hna+(b.hna*(b.margin_resep/100))) as harga_jual, 
                (b.hna+(b.hna*(b.margin_non_resep/100))) as harga_jual_nr 
                from kemasan k 
                join barang b on (k.id_barang = b.id) 
                where k.id = '".$get->id_packing."'")->row();
        } else {
            $rows= $this->db->query("select d.*, d.hj_resep as harga_jual, k.isi_satuan, d.hj_non_resep as harga_jual_nr, k.id as id_packing, d.hj_resep as harga_jual_resep, (k.isi*k.isi_satuan) as isi_satuan
                from dinamic_harga_jual d
                join kemasan k on (d.id_kemasan = k.id)
                where d.id_kemasan = '".$get->id_packing."' and $jml between d.jual_min and d.jual_max")->row();
        }
        die(json_encode($rows));
    }
    
    function get_detail_harga_barang($id, $kemasan, $jml) {
        
        $cek= $this->db->query("select id, is_harga_bertingkat from kemasan where id_barang = '$id' and id_kemasan = '$kemasan'")->row();
        if ($cek->is_harga_bertingkat === '0') {
            $rows = $this->db->query("select b.*, (b.hna+(b.hna*(b.margin_non_resep/100))) as harga_jual, 
                (b.hna+(b.hna*(b.margin_resep/100))) as harga_jual_resep, k.isi, k.isi_satuan as isi_sat, (k.isi*k.isi_satuan) as isi_satuan
                from kemasan k 
                join barang b on (k.id_barang = b.id) 
                where k.id = '".$cek->id."'")->row();
        } else {
            $rows = $this->db->query("select d.*, d.hj_non_resep as harga_jual, d.hj_resep as harga_jual_resep, k.isi, k.isi_satuan as isi_sat,  IF( k.isi_satuan !=1, '1', '1' ) AS isi_satuan
                from dinamic_harga_jual d
                join kemasan k on (d.id_kemasan = k.id)
                where d.id_kemasan = '".$cek->id."' and $jml between d.jual_min and d.jual_max")->row();
        }
        return $rows;
    }
    
    function get_barang_barcode($barcode) {
        $barcode = $_GET['barcode'];
        $sql = $this->db->query("select b.*, k.id as id_packing, p.nama as pabrik, g.nama as golongan, st.nama as satuan, sd.nama as sediaan,
        concat_ws(' ', b.nama, b.kekuatan, st.nama) as nama_barang
        from barang b 
        join kemasan k on (b.id = k.id_barang)
        left join pabrik p on (b.id_pabrik = p.id)
        left join golongan g on (b.id_golongan = g.id)
        left join satuan st on (b.satuan_kekuatan = st.id)
        left join sediaan sd on (b.id_sediaan = sd.id) where b.barcode = '$barcode' and default_kemasan = '1'");
        return $this->db->query($sql);
    }
    
    function get_expiry_barang($id_barang) {
        $sql = "SELECT id_barang, ed, IFNULL((sum(masuk)-sum(keluar)),'0') as sisa 
            FROM `stok` WHERE id_barang = '$id_barang' and ed > '".date("Y-m-d")."' group by ed having sisa > 0 order by ed";
        return $this->db->query($sql);
    }
    
    function check_alergi_obat_pasien($id_barang, $id_pasien) {
        $cek = $this->db->query("select kandungan from barang where id = '$id_barang'")->row();
        $alg = str_replace(' ', '', $cek->kandungan);
        $key = str_replace(',', '|', $alg);

        $row = array('jumlah' => 0);
        if ($key !== '') {
            $row = $this->db->query("select count(*) as jumlah, b.kandungan as nama from 
                alergi_obat_pasien a 
                join barang b on (a.id_barang = b.id) 
                where a.id_pasien = '$id_pasien' and b.kandungan RLIKE '$key'")->row();
        }
        die(json_encode($row));
    }
    
    function get_nomor_antri($id_spesialis, $tanggal) {
        $sql = "select IFNULL(max(no_antri),0)+1 as antrian from kunjungan_pelayanan where id_spesialis = '$id_spesialis' and date(waktu) = '$tanggal'";
        return $this->db->query($sql);
    }
    
    /*PEMERIKSAAN*/
    function diagnosis($q) {
        $sql = "select * from diagnosis where nama like ('%$q%') or no_daftar_terperinci like ('%$q%') order by locate('$q', nama)";
        return $this->db->query($sql);
    }
    
    function tindakan($q) {
        $sql = "select * from layanan where nama like ('%$q%') or kode_icdixcm like ('%$q%') order by locate('$q', nama)";
        return $this->db->query($sql);
    }
    
    function tindakan_komponen_tarif($q) {
        $sql = "select k.id, k.total, CONCAT_WS('; ',t.nama,k.operator,k.jenis_pelayanan,k.bobot,k.kelas) as tarif from komponen_tarif k 
            join layanan t on (k.id_tindakan = t.id) 
            having tarif like ('%$q%')";
        return $this->db->query($sql);
    }
    
    function get_layanan() {
        $sql = "select l.*, ly.nama as parent from layanan l 
            left join layanan ly on (l.id_parent = ly.id)
            order by ly.nama asc, l.nama asc
            ";
        return $this->db->query($sql);
    }
    
    function get_data_apoteker($q) {
        
    }
    
    function get_data_bpom($q) {
        $sql = "select saksi_bpom from pemusnahan where saksi_bpom like ('%$q%') GROUP by saksi_bpom order by locate('$q',saksi_bpom)";
        return $this->db->query($sql);
    }
    
    function layanan($q) {
        $sql = "select * from layanan where nama like ('%$q%') order by locate ('$q', nama)";
        return $this->db->query($sql);
    }
    
    function unit($q) {
        $sql = "select * from unit where jenis = 'inventori' and nama like ('%$q%') order by locate ('$q', nama)";
        return $this->db->query($sql);
    }
    
    function nomor_distribusi($q = NULL) {
        $sort = NULL;
        if ($q !== '') {
            $sort = " and id = '$q'";
        }
        $sql = "select * from distribusi where id_unit_tujuan = '".$this->session->userdata('id_unit')."' $sort";
        //echo $sql;
        return $this->db->query($sql);
    }
}
?>
