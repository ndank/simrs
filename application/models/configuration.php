<?php

class Configuration extends CI_Model {

    function get_biaya_kartu() {
        $db = $this->db->get('konfigurasi');
        $biaya = $db->row();
        return $biaya->biaya_kartu;
    }

    function get_biaya_daftar() {
        $db = $this->db->get('konfigurasi');
        $biaya = $db->row();
        return $biaya->biaya_daftar;
    }

    function set_biaya_kartu($biaya) {
        $data = array('biaya_kartu', $biaya);
        $this->db->where('id', 1);
        $this->db->update('konfigurasi',$data);
    }

    function set_biaya_daftar($biaya) {
        $data = array('biaya_daftar', $biaya);
        $this->db->where('id', 1);
        $this->db->update('konfigurasi',$data);
    }
    
    function get_manager_farmasi() {
        $sql = "select * from penduduk p
            join dinamis_penduduk dp on (p.id = dp.penduduk_id)
            inner join (
                select penduduk_id, max(id) as id_max from dinamis_penduduk
                group by penduduk_id
            ) idp on (idp.penduduk_id = dp.penduduk_id and idp.id_max = dp.id)
            where dp.jabatan = 'Manajer' and p.unit_id = (select id from unit where nama = 'Pelayanan Farmasi')";
        return $this->db->query($sql)->row();
    }
    
    function get_apoteker($q) {
        $sql = "select p.id, p.nama, dp.sip_no from penduduk p
            join dinamis_penduduk dp on (p.id = dp.penduduk_id)
            left join profesi pf on (pf.id = dp.profesi_id)
            inner join (
                select penduduk_id, max(id) as id_max from dinamis_penduduk
                group by penduduk_id
            ) idp on (idp.penduduk_id = dp.penduduk_id and idp.id_max = dp.id)
            where pf.nama = 'Apoteker' and p.nama like ('%$q%') order by locate ('$q',p.nama)";
        return $this->db->query($sql);
    }
    
    function jenis_transaksi() {
        return array (
            '' => 'Semua Transaksi ...',
//            'Inkaso' => 'Inkaso',
//            'Penerimaan Retur Pembelian' => 'Penerimaan Retur Pembelian',
            'Pembayaran Billing Pasien' => 'Pembayaran Billing Pasien',
            'Penjualan Non Resep' => 'Penjualan Non Resep',
            'Pengeluaran Retur Penjualan' => 'Pengeluaran Retur Penjualan',
            'Penerimaan dan Pengeluaran' => 'Penerimaan dan Pengeluaran'
            
        );
    }
    
    function rumah_sakit_get_atribute() {
        $sql = "select a.*, kl.nama as kelurahan, kc.nama as kecamatan, kb.nama as kabupaten, p.nama as provinsi 
        from rumah_sakit a
        left join kelurahan kl on (kl.id = a.kelurahan_id)
        left join kecamatan kc on (kc.id = kl.kecamatan_id)
        left join kabupaten kb on (kb.id = kc.kabupaten_id)
        left join provinsi p on (p.id = kb.provinsi_id)";
        return $this->db->query($sql);
    }
    
    function penduduk_manager_farmasi() {
        $sql = "select p.nama, p.lahir_tanggal, dp.* from penduduk p
            join dinamis_penduduk dp on (p.id = dp.penduduk_id)
            join unit u on (p.unit_id = u.id)
            inner join (
                select penduduk_id, max(id) as id_max
                from dinamis_penduduk group by penduduk_id
            ) dm on (dp.penduduk_id = dm.penduduk_id and dp.id = dm.id_max)
            where dp.jabatan = 'Manajer' and u.nama = 'Pelayanan Farmasi'";
        return $this->db->query($sql);
    }
    
    function unit_load_data($id = null, $jenis) {
        $q = null;
        if ($id != null) {
            $q.="and id = '$id'";
        }
        $sql = "select * from unit where jenis = '$jenis' $q order by nama";
        //echo $sql;
        return $this->db->query($sql);
        
    }
    
    function instansi_relasi_load_data($id = null, $jenis = null) {
        $q = null;
        if ($id != null) {
            $q.=" and r.id = '$id'";
        }
        if ($jenis != null) {
            $q.=" and j.nama = '$jenis'";
        }
        $sql = "select r.*, j.nama as jenis from relasi_instansi r
            join relasi_instansi_jenis j on (r.relasi_instansi_jenis_id = j.id)
            where r.id is not NULL $q";
        return $this->db->query($sql);
    }
    
    function reset_data() {
        $this->db->trans_begin();
        $this->db->query("delete from antrian_kunjungan where id > 1");
        $this->db->query("ALTER TABLE antrian_kunjungan AUTO_INCREMENT = 2");
        
        $this->db->query("delete from asuransi_produk where id > 2");
        $this->db->query("ALTER TABLE asuransi_produk AUTO_INCREMENT = 3");
        
        $this->db->query("delete from barang where id > 202");
        $this->db->query("ALTER TABLE barang AUTO_INCREMENT = 203");
        
        $this->db->query("delete from barang_kategori where id > 20");
        $this->db->query("ALTER TABLE barang_kategori AUTO_INCREMENT = 21");
        
        $this->db->query("delete from barang_packing where id > 45");
        $this->db->query("ALTER TABLE barang_packing AUTO_INCREMENT = 46");
        
        $this->db->query("delete from diagnosa_pelayanan_kunjungan where id > 4");
        $this->db->query("ALTER TABLE diagnosa_pelayanan_kunjungan AUTO_INCREMENT = 5");
        
        $this->db->query("delete from dinamis_penduduk where id > 164");
        $this->db->query("ALTER TABLE dinamis_penduduk AUTO_INCREMENT = 165");
        
        $this->db->query("delete from distribusi");
        $this->db->query("ALTER TABLE distribusi AUTO_INCREMENT = 1");
        
        $this->db->query("delete from distribusi_penerimaan");
        $this->db->query("ALTER TABLE distribusi_penerimaan AUTO_INCREMENT = 1");
        
        $this->db->query("delete from distribusi_retur");
        $this->db->query("ALTER TABLE distribusi_retur AUTO_INCREMENT = 1");
        
        $this->db->query("delete from distribusi_retur_penerimaan");
        $this->db->query("ALTER TABLE distribusi_retur_penerimaan AUTO_INCREMENT = 1");
        
        $this->db->query("delete from error_log");
        $this->db->query("delete from golongan_sebab_sakit where id > 574");
        $this->db->query("ALTER TABLE golongan_sebab_sakit AUTO_INCREMENT = 575");
        
        $this->db->query("delete from inap_rawat_kunjungan where id > 2");
        $this->db->query("ALTER TABLE inap_rawat_kunjungan AUTO_INCREMENT = 3");
        
        $this->db->query("delete from inkaso");
        $this->db->query("ALTER TABLE inkaso AUTO_INCREMENT = 1");
        
        $this->db->query("delete from jasa_penjualan_detail where id > 22");
        $this->db->query("ALTER TABLE jasa_penjualan_detail AUTO_INCREMENT = 23");
        
        $this->db->query("delete from jenis_jurusan_kualifikasi_pendidikan where id > 18");
        $this->db->query("ALTER TABLE jenis_jurusan_kualifikasi_pendidikan AUTO_INCREMENT = 19");
        
        $this->db->query("delete from jenis_layanan where id > 15");
        $this->db->query("ALTER TABLE jenis_layanan AUTO_INCREMENT = 16");
        
        $this->db->query("delete from jenis_rs where id > 19");
        $this->db->query("ALTER TABLE jenis_rs AUTO_INCREMENT = 20");
        
        $this->db->query("delete from jurnal where id > 70");
        $this->db->query("ALTER TABLE jurnal AUTO_INCREMENT = 71");
        
        $this->db->query("delete from jurusan_kualifikasi_pendidikan where id > 39");
        $this->db->query("ALTER TABLE jurusan_kualifikasi_pendidikan AUTO_INCREMENT = 40");
        
        $this->db->query("delete from kabupaten where id > 15");
        $this->db->query("ALTER TABLE kabupaten AUTO_INCREMENT = 16");
        
        $this->db->query("delete from kecamatan where id > 52");
        $this->db->query("ALTER TABLE kecamatan AUTO_INCREMENT = 53");
        
        $this->db->query("delete from kelurahan where id > 370");
        $this->db->query("ALTER TABLE kelurahan AUTO_INCREMENT = 371");
        
        $this->db->query("delete from kepegawaian where id > 38");
        $this->db->query("ALTER TABLE kepegawaian AUTO_INCREMENT = 39");
        
        $this->db->query("delete from kualifikasi_pendidikan where id > 12");
        $this->db->query("ALTER TABLE kualifikasi_pendidikan AUTO_INCREMENT = 13");
        
        $this->db->query("delete from kunjungan_billing_pembayaran");
        $this->db->query("ALTER TABLE kunjungan_billing_pembayaran AUTO_INCREMENT = 1");
        
        $this->db->query("delete from layanan where id > 4096");
        $this->db->query("ALTER TABLE layanan AUTO_INCREMENT = 4097");
        
        $this->db->query("delete from `module` where id > 11");
        $this->db->query("ALTER TABLE `module` AUTO_INCREMENT = 12");
        
        $this->db->query("delete from obat where id > 200");
        $this->db->query("ALTER TABLE obat AUTO_INCREMENT = 201");
        
        $this->db->query("delete from opname_stok where id > 2");
        $this->db->query("ALTER TABLE opname_stok AUTO_INCREMENT = 3");
                
        $this->db->query("delete from pekerjaan where id > 13");
        $this->db->query("ALTER TABLE pekerjaan AUTO_INCREMENT = 14");
        
        $this->db->query("delete from pelayanan_kunjungan where id > 3");
        $this->db->query("ALTER TABLE pelayanan_kunjungan AUTO_INCREMENT = 4");
        
        $this->db->query("delete from pemakaian where id > 15");
        $this->db->query("ALTER TABLE pemakaian AUTO_INCREMENT = 16");
        
        $this->db->query("delete from pembelian");
        $this->db->query("ALTER TABLE pembelian AUTO_INCREMENT = 1");
        
        $this->db->query("delete from pembelian_retur");
        $this->db->query("ALTER TABLE pembelian_retur AUTO_INCREMENT = 1");
        
        $this->db->query("delete from pembelian_retur_penerimaan");
        $this->db->query("ALTER TABLE pembelian_retur_penerimaan AUTO_INCREMENT = 1");
        
        $this->db->query("delete from pemeriksaan_lab_pelayanan_kunjungan");
        $this->db->query("ALTER TABLE pemeriksaan_lab_pelayanan_kunjungan AUTO_INCREMENT = 1");
        
        $this->db->query("delete from pemeriksaan_radiologi_pelayanan_kunjungan");
        $this->db->query("delete from pemesanan where id > 2");
        $this->db->query("ALTER TABLE pemesanan AUTO_INCREMENT = 3");
        
        $this->db->query("delete from pemusnahan where id > 6");
        $this->db->query("ALTER TABLE pemusnahan AUTO_INCREMENT = 7");
        
        $this->db->query("delete from pendaftaran where no_daftar > 4");
        $this->db->query("ALTER TABLE pendaftaran AUTO_INCREMENT = 5");
        
        $this->db->query("delete from pendidikan where id > 12");
        $this->db->query("delete from penduduk where id > 26");
        $this->db->query("ALTER TABLE inkaso AUTO_INCREMENT = 27");
        
        
        $this->db->query("delete from penjualan where id > 5");
        $this->db->query("ALTER TABLE penjualan AUTO_INCREMENT = 6");
        
        $this->db->query("delete from penjualan_retur");
        $this->db->query("ALTER TABLE penjualan_retur AUTO_INCREMENT = 1");
        
        $this->db->query("delete from penjualan_retur_pengeluaran");
        $this->db->query("ALTER TABLE penjualan_retur_pengeluaran AUTO_INCREMENT = 1");
        
        $this->db->query("delete from produk_asuransi_pembayaran");
        $this->db->query("ALTER TABLE produk_asuransi_pembayaran AUTO_INCREMENT = 1");
        
        $this->db->query("delete from profesi where id > 16");
        $this->db->query("ALTER TABLE profesi AUTO_INCREMENT = 17");
        
        $this->db->query("delete from provinsi where id > 6");
        $this->db->query("ALTER TABLE provinsi AUTO_INCREMENT = 7");
        
        $this->db->query("delete from reg_rs");
        $this->db->query("ALTER TABLE reg_rs AUTO_INCREMENT = 1");
        
        $this->db->query("delete from rekening where id > 5");
        $this->db->query("ALTER TABLE rekening AUTO_INCREMENT = 6");
        
        $this->db->query("delete from relasi_instansi where id > 300");
        $this->db->query("ALTER TABLE relasi_instansi AUTO_INCREMENT = 301");
        
        $this->db->query("delete from relasi_instansi_jenis where id > 9");
        $this->db->query("ALTER TABLE relasi_instansi_jenis AUTO_INCREMENT = 10");
        
        $this->db->query("delete from resep where id > 8");
        $this->db->query("ALTER TABLE resep AUTO_INCREMENT = 9");
        
        $this->db->query("delete from satuan where id > 83");
        $this->db->query("ALTER TABLE satuan AUTO_INCREMENT = 84");
        
        $this->db->query("delete from sediaan where id > 43");
        $this->db->query("ALTER TABLE sediaan AUTO_INCREMENT = 44");
        
        $this->db->query("delete from spesialisasi");
        $this->db->query("ALTER TABLE spesialisasi AUTO_INCREMENT = 1");
        
        $this->db->query("delete from sub_jenis_layanan where id > 63");
        $this->db->query("ALTER TABLE sub_jenis_layanan AUTO_INCREMENT = 64");
        
        $this->db->query("delete from sub_rekening where id > 14");
        $this->db->query("ALTER TABLE sub_rekening AUTO_INCREMENT = 15");
        
        $this->db->query("delete from tarif where id > 487");
        $this->db->query("ALTER TABLE tarif AUTO_INCREMENT = 488");
        
        $this->db->query("delete from tindakan_pelayanan_kunjungan");
        $this->db->query("ALTER TABLE tindakan_pelayanan_kunjungan AUTO_INCREMENT = 1");
        
        $this->db->query("delete from transaksi_detail where id > 74");
        $this->db->query("ALTER TABLE transaksi_detail AUTO_INCREMENT = 75");
        
        $this->db->query("delete from tt where id > 5");
        $this->db->query("ALTER TABLE tt AUTO_INCREMENT = 6");
        
        $this->db->query("delete from uang_penerimaan_pengeluaran");
        
        $this->db->query("delete from unit where id > 27");
        $this->db->query("ALTER TABLE unit AUTO_INCREMENT = 28");
        
        $this->db->query("delete from users where id > 20");
        $this->db->query("ALTER TABLE users AUTO_INCREMENT = 21");
        
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

}

?>