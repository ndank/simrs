<?php
if (isset($_GET['q'])) {
    $q = $_GET['q'];
    if ($_GET['opsi'] == 'penduduk') {
        $sql = "select p.*, d.alamat from penduduk p
            left join dinamis_penduduk d on (p.id = d.penduduk_id)
            where d.id in (select max(id) from dinamis_penduduk group by penduduk_id)
            and p.nama like ('%$q%') order by locate('$q', p.nama)";
        $exe = _select_arr($sql);
        die(json_encode($exe));
    }
    
    if ($_GET['opsi'] == 'pegawai') {
        $sql = "select * from penduduk p 
        join users u on (p.id = u.id)
        join dinamis_penduduk dp on (dp.penduduk_id = p.id)
        inner join (
            select penduduk_id, max(id) as id_max
            from dinamis_penduduk group by penduduk_id
            ) tm on (dp.penduduk_id = tm.penduduk_id and dp.id = tm.id_max)
        ";
        $exe = _select_arr($sql);
        die(json_encode($exe));
    }
    
    if ($_GET['opsi'] == 'user-system') {
        $sql = "select p.* from penduduk p left join users u on (p.id = u.id) 
            where p.nama like ('%$q%') and p.id not in (select id from users) order by locate('$q', p.nama)";
        $exe = _select_arr($sql);
        die(json_encode($exe));
    }
    
    if ($_GET['opsi'] == 'pabrik') {
        $sql = "select i.*, j.nama as jenis from relasi_instansi i
        join relasi_instansi_jenis j on (i.relasi_instansi_jenis_id = j.id)
        where i.nama like ('%$q%') and j.nama = 'Pabrik' order by locate('$q', i.nama)";
        $exe = _select_arr($sql);
        die(json_encode($exe));
    }
    if ($_GET['opsi'] == 'asuransi') {
        $sql = "select i.*, j.nama as jenis from relasi_instansi i
        join relasi_instansi_jenis j on (i.relasi_instansi_jenis_id = j.id)
        where i.nama like ('%$q%') and j.nama = 'Asuransi' order by locate('$q', i.nama)";
        
        $exe = _select_arr($sql);
        die(json_encode($exe));
    }
    if ($_GET['opsi'] == 'produk-asuransi') {
        $sql = "select a.*, r.nama as instansi from asuransi_produk a
        join relasi_instansi r on (r.id = a.relasi_instansi_id)
        where a.nama like ('%$q%') order by locate('$q', a.nama)";
        $exe = _select_arr($sql);
        die(json_encode($exe));
    }
    if ($_GET['opsi'] == 'supplier') {
        $sql = "select i.*, j.nama as jenis from relasi_instansi i
        join relasi_instansi_jenis j on (i.relasi_instansi_jenis_id = j.id)
        where i.nama like ('%$q%') and j.nama = 'Supplier' order by locate('$q', i.nama)";
        $exe = _select_arr($sql);
        die(json_encode($exe));
    }
    if ($_GET['opsi'] == 'kelurahan') {
        $sql = "select kl.*, kc.nama as kecamatan, kb.id as id_kabupaten, kb.nama as kabupaten, p.nama as provinsi from kelurahan kl
            join kecamatan kc on (kl.kecamatan_id = kc.id)
            join kabupaten kb on (kc.kabupaten_id = kb.id)
            join provinsi p on (kb.provinsi_id = p.id)
            where kl.nama like ('%$q%') order by locate('$q', kl.nama)";
        $exe = _select_arr($sql);
        die(json_encode($exe));
    }
    if ($_GET['opsi'] == 'kabupaten') {
        $sql = "select kb.*, p.nama as provinsi from kabupaten kb
            join provinsi p on (kb.provinsi_id = p.id)
            where kb.nama like ('%$q%') order by locate('$q', kb.nama)";
        $exe = _select_arr($sql);
        die(json_encode($exe));
    }
    if ($_GET['opsi'] == 'barang') {
        $sort = null;
        if (isset($_GET['serial'])) {
            $sort = "and b.id = '$_GET[serial]'";
        }
        $sql = "select bp.*, b.id as id_barang, b.nama, r.nama as pabrik, s.nama as satuan, sd.nama as sediaan, o.kekuatan, o.id as id_obat, st.nama as satuan_terkecil from barang b
            left join barang_packing bp on (b.id = bp.barang_id)
            left join obat o on (b.id = o.id)
            left join satuan s on (s.id = o.satuan_id)
            left join satuan st on (bp.terkecil_satuan_id = st.id)
            left join sediaan sd on (sd.id = o.sediaan_id)
            left join relasi_instansi r on (b.pabrik_relasi_instansi_id = r.id)
            where b.nama like ('%$q%') $sort order by locate('$q', b.nama)";
        //echo $sql;
        $exe = _select_arr($sql);
        die(json_encode($exe));
    }
    if ($_GET['opsi'] == 'barang-search') {
        $sort = null;
        if (isset($_GET['serial'])) {
            $sort = "and b.id = '$_GET[serial]'";
        }
        $sql = "select b.id as id_barang, b.nama, r.nama as pabrik, s.nama as satuan2, sd.nama as sediaan, o.kekuatan, o.id as id_obat, st.nama as satuan from barang b
            left join obat o on (b.id = o.id)
            left join satuan s on (s.id = o.satuan_id)
            left join satuan st on (o.satuan_id = st.id)
            left join sediaan sd on (sd.id = o.sediaan_id)
            left join relasi_instansi r on (b.pabrik_relasi_instansi_id = r.id)
            where b.nama like ('%$q%') $sort order by locate('$q', b.nama)";
        //echo $sql;
        $exe = _select_arr($sql);
        die(json_encode($exe));
    }
    if ($_GET['opsi'] == 'dokter') {
        $sql = "select p.*, dp.*, p.id as penduduk_id from penduduk p
            join dinamis_penduduk dp on (p.id = dp.penduduk_id)
            join profesi pr on (pr.id = dp.profesi_id)
            where dp.id in (select max(id) from dinamis_penduduk group by penduduk_id) and pr.nama = 'Dokter' 
            and p.nama like ('%$q%') order by locate ('$q',p.nama)
            ";
        $exe = _select_arr($sql);
        die(json_encode($exe));
    }
    if ($_GET['opsi'] == 'pasien') {
        $sql = "select p.*, dp.*, p.id as penduduk_id, k.nama as kelurahan, ps.no_rm from penduduk p
            left join dinamis_penduduk dp on (p.id = dp.penduduk_id)
            left join kelurahan k on (dp.kelurahan_id = k.id)
            join pasien ps on (ps.id = p.id)
            where dp.id in (select max(id) from dinamis_penduduk group by penduduk_id)
            and (p.nama like ('%$q%')) order by locate ('$q',p.nama)";
        $exe = _select_arr($sql);
        die(json_encode($exe));
    }
    if ($_GET['opsi'] == 'sales') {
        $s = null;
        if (isset($_GET['jenis'])) {
            $s.="and pf.jenis = 'Nakes'";
        }
        $sql = "select p.nama, dp.*, kl.nama as kelurahan from penduduk p
        join dinamis_penduduk dp on (p.id = dp.penduduk_id)
        left join kelurahan kl on (kl.id = dp.kelurahan_id)
        left join pekerjaan pf on (pf.id = dp.pekerjaan_id)
        where pf.nama = 'Salesman' and p.nama like ('%$q%') and dp.id in (select max(id) from dinamis_penduduk group by penduduk_id) $s order by locate('$q', p.nama)";
        $exe = _select_arr($sql);
        die(json_encode($exe));
    }
    if ($_GET['opsi'] == 'sapt') {
        $s = null;
        if ($_GET['jenis'] == 'nakes') {
            $s.=" and dp.jabatan = 'APA'";
        }
        $sql = "select p.nama, dp.*, kl.nama as kelurahan from penduduk p
        join dinamis_penduduk dp on (p.id = dp.penduduk_id)
        left join kelurahan kl on (kl.id = dp.kelurahan_id)
        left join profesi pf on (pf.id = dp.profesi_id)
        where p.nama like ('%$q%') and dp.id in (select max(id) from dinamis_penduduk group by penduduk_id) $s order by locate('$q', p.nama)";
        
        $exe = _select_arr($sql);
        die(json_encode($exe));
    }
    if ($_GET['opsi'] == 'packing-barang') { 
        $sql = "select o.id as id_obat, o.generik, bp.*, r.nama as pabrik, b.id as id_barang, sd.nama as sediaan, b.nama, s.nama as satuan, st.nama as satuan_terkecil, stb.nama as satuan_terbesar, o.kekuatan from barang_packing bp
        join barang b on (b.id = bp.barang_id)
        left join obat o on (b.id = o.id)
        left join satuan s on (s.id = o.satuan_id)
        left join satuan st on (st.id = bp.terkecil_satuan_id)
        left join satuan stb on (stb.id = bp.terbesar_satuan_id)
        left join sediaan sd on (sd.id = o.sediaan_id)
        left join relasi_instansi r on (r.id = b.pabrik_relasi_instansi_id)
        where b.nama like ('%$q%') order by locate ('$q', b.nama)";
        
        $exe = _select_arr($sql);
        die(json_encode($exe));
    }
    if ($_GET['opsi'] == 'packing-barang-pemusnahan') { 
        $sql = "select td.ed, o.generik, td.hpp, td.sisa, o.id as id_obat, bp.*, r.nama as pabrik, b.id as id_barang, sd.nama as sediaan, b.nama, s.nama as satuan, st.nama as satuan_terkecil, stb.nama as satuan_terbesar, o.kekuatan from barang_packing bp
        join barang b on (b.id = bp.barang_id)
        left join obat o on (b.id = o.id)
        left join satuan s on (s.id = o.satuan_id)
        left join satuan st on (st.id = bp.terkecil_satuan_id)
        left join satuan stb on (stb.id = bp.terbesar_satuan_id)
        left join sediaan sd on (sd.id = o.sediaan_id)
        left join relasi_instansi r on (r.id = b.pabrik_relasi_instansi_id)
        join transaksi_detail td on (bp.id = td.barang_packing_id)
        where b.nama like ('%$q%') and td.unit_id = '$_SESSION[id_unit]' group by bp.id order by locate ('$q', b.nama)";
        
        $exe = _select_arr($sql);
        die(json_encode($exe));
    }
    if ($_GET['opsi'] == 'packing-barang_penjualan') { 
       $sql = "select o.id as id_obat, o.generik, bp.*, r.nama as pabrik, b.id as id_barang, sd.nama as sediaan, b.nama, s.nama as satuan, st.nama as satuan_terkecil, o.kekuatan from barang_packing bp
        join barang b on (b.id = bp.barang_id)
        left join obat o on (b.id = o.id)
        left join satuan s on (s.id = o.satuan_id)
        left join satuan st on (st.id = bp.terkecil_satuan_id)
        left join sediaan sd on (sd.id = o.sediaan_id)
        left join relasi_instansi r on (r.id = b.pabrik_relasi_instansi_id)
        where b.nama like ('%$q%') order by locate ('$q', b.nama)";
        $exe = _select_arr($sql);
        die(json_encode($exe));
    }
    if ($_GET['opsi'] == 'packingbarang') {
        $sql = "select bp.*, o.generik, s.nama as s_besar, st.nama as s_kecil, b.nama, td.sisa, td.ed, td.hpp, td.hna, td.het, o.kekuatan from barang_packing bp
        join barang b on (b.id = bp.barang_id)
        join satuan s on (s.id = bp.terbesar_satuan_id)
        join satuan st on (st.id = bp.terkecil_satuan_id)
        left join obat o on (b.id = o.id)
        left join transaksi_detail td on (td.barang_packing_id = bp.id)
        where b.nama like ('%$q%') and td.id in (select max(id) from transaksi_detail where transaksi_jenis != 'Pemesanan' group by barang_packing_id) order by locate ('$q', b.nama)";
        $exe = _select_arr($sql);
        die(json_encode($exe));
    }
    if ($_GET['opsi'] == 'pemesanan') {
        $sql = "select p.*, r.nama as pabrik, pd.nama as sales from pemesanan p
            join relasi_instansi r on (p.suplier_relasi_instansi_id = r.id)
            join penduduk pd on (p.salesman_penduduk_id = pd.id)
            where p.id not in (select pemesanan_id from pembelian) and p.id like ('%$q%') order by locate ('$q', p.id)";
        $exe = _select_arr($sql);
        die(json_encode($exe));
    }
    if ($_GET['opsi'] == 'noresep') {
        $sql = "select r.*, p.nama as dokter, pd.nama as pasien, sum(t.nominal) as jasa_apoteker, ps.no_rm from resep r
            join penduduk p on (r.dokter_penduduk_id = p.id)
            join penduduk pd on (r.pasien_penduduk_id = pd.id)
            join pasien ps on (pd.id = ps.id)
            join resep_r rr on (r.id = rr.resep_id)
            join tarif t on (t.id = rr.tarif_id)
            where r.id = '$q' order by locate ('$q', r.id)";
        $exe = _select_arr($sql);
        die(json_encode($exe));
    }
    if ($_GET['opsi'] == 'nodistribusi') {
        $sql = "select d.*, p.nama from distribusi d
        join penduduk p on (d.pegawai_penduduk_id = p.id) 
        where d.id like ('%$q%') order by locate ('$q',d.id)";
        $exe = _select_arr($sql);
        die(json_encode($exe));
    }
    if ($_GET['opsi'] == 'layanan') {
        $sql = "select * from layanan
        where nama like ('%$q%') order by locate ('$q',nama)";
        $exe = _select_arr($sql);
        die(json_encode($exe));
    }
    
    if ($_GET['opsi'] == 'layanan_jasa') {
        $sql = "select l.*, t.bobot, t.nominal, t.id as id_tarif from layanan l
            join tarif t on (l.id = t.layanan_id)
        where l.nama like ('%$q%') order by locate ('$q',l.nama)";
        $exe = _select_arr($sql);
        die(json_encode($exe));
    }
    
    if ($_GET['opsi'] == 'kategori') {
        $sql = "select * from tarif_kategori
        where nama like ('%$q%') order by locate ('$q',nama)";
        $exe = _select_arr($sql);
        die(json_encode($exe));
    }
    if ($_GET['opsi'] == 'inkaso') {
        $sql = "select sum(k.pengeluaran) as terbayar, p.*, r.nama as instansi, (sum(t.subtotal)+(p.materai+((p.ppn/100)*sum(t.subtotal)))) as total from pembelian p
            join relasi_instansi r on (p.suplier_relasi_instansi_id = r.id)
            join transaksi_detail t on (t.transaksi_id = p.id)
            left join inkaso_detail id on (id.pembelian_id = p.id)
            left join kas k on (k.transaksi_id = id.id)
            where t.transaksi_jenis = 'Pembelian' and p.id like ('%$q%') order by locate ('$q', p.id)";
        $exe = _select_arr($sql);
        die(json_encode($exe));
    }
    if ($_GET['opsi'] == 'retur-pembelian') {
        $sql = "select p.id, k.penerimaan, date(k.waktu) as tanggal from pembelian_retur p join kas k on (p.id = k.transaksi_id) where k.transaksi_jenis = 'Retur Pembelian' and p.id like ('%$q%') order by locate ('$q', p.id)";
        
        $exe = _select_arr($sql);
        die(json_encode($exe));
    }
    if ($_GET['opsi'] == 'retur_pembelian') {
        $sql = "select p.*, pd.nama as petugas, pdd.nama as salesman, r.id as id_suplier, r.nama as suplier, sum(td.hpp*td.keluar) as subtotal from pembelian_retur p 
            join penduduk pd on (p.pegawai_penduduk_id = pd.id)
            join penduduk pdd on (p.salesman_penduduk_id = pdd.id)
            left join relasi_instansi r on (r.id = p.suplier_relasi_instansi)
            join transaksi_detail td on (p.id = td.transaksi_id)
            where td.transaksi_jenis = 'Retur Pembelian' and p.id like ('%$q%') order by locate ('$q',p.id)";
        //echo $sql;
        $exe = _select_arr($sql);
        die(json_encode($exe));
    }
    if ($_GET['opsi'] == 'reretur_pembelian') {
        /*$sql = "select td.ed, td.hpp, td.sisa, o.id as id_obat, bp.*, r.nama as pabrik, b.id as id_barang, sd.nama as sediaan, 
        b.nama, s.nama as satuan, st.nama as satuan_terkecil, stb.nama as satuan_terbesar, o.kekuatan from pembelian_retur p
        join transaksi_detail td on (p.id = td.transaksi_id)
        join barang_packing bp on (bp.id = td.barang_packing_id)
        join barang b on (b.id = bp.barang_id)
        left join obat o on (b.id = o.id)
        left join satuan s on (s.id = o.satuan_id)
        left join satuan st on (st.id = bp.terkecil_satuan_id)
        left join satuan stb on (stb.id = bp.terbesar_satuan_id)
        left join sediaan sd on (sd.id = o.sediaan_id)
        left join relasi_instansi r on (r.id = b.pabrik_relasi_instansi_id)
        where td.transaksi_jenis = 'Retur Pembelian' and p.id like ('%$q%') order by locate ('$q', p.id)";*/
        $sql = "select p.*, pdd.nama as pegawai, pdk.nama as salesman, r.nama as suplier from pembelian_retur p 
            join penduduk pdd on (p.pegawai_penduduk_id = pdd.id)
            join penduduk pdk on (p.salesman_penduduk_id = pdk.id)
            join relasi_instansi r on (r.id = p.suplier_relasi_instansi)
            where p.id like ('%$q%') order by locate ('$q', p.id)";
        //echo $sql;
        $exe = _select_arr($sql);
        die(json_encode($exe));
    }
    if ($_GET['opsi'] == 'reretur_penjualan') {
        $sql = "select p.*, pdd.nama as pegawai, pdk.nama as pembeli from penjualan_retur p 
            join penduduk pdd on (p.pegawai_penduduk_id = pdd.id)
            join penduduk pdk on (p.pembeli_penduduk_id = pdk.id)
            where p.id like ('%$q%') order by locate ('$q', p.id)";
        //echo $sql;
        $exe = _select_arr($sql);
        die(json_encode($exe));
    }
    
    if ($_GET['opsi'] == 'penerimaan_retur_distribusi') {
        $sql = "select p.*, pdd.nama as pegawai, u.nama as unit from distribusi_retur p 
            join penduduk pdd on (p.pegawai_penduduk_id = pdd.id)
            join transaksi_detail td on (td.transaksi_id = p.id)
            join unit u on (td.unit_id = u.id)
            where p.id like ('%$q%') group by td.transaksi_id order by locate ('$q', p.id)";
        //echo $sql;
        $exe = _select_arr($sql);
        die(json_encode($exe));
    }
}
?>
