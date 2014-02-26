<?php

class M_inventory extends CI_Model {
    
    public $waktu = "";
    
    function __construct() {
        parent::__construct();
        date_default_timezone_set("Asia/Jakarta");
        $this->waktu = gmdate('Y-m-d H:i:s' ,gmdate('U')+25200);
    }
    
    function cek_delete($trans_id, $trans_jenis) {
        $result = TRUE;
        /*$get = $this->db->query("select id from transaksi_detail where transaksi_id = '$trans_id' and transaksi_jenis = '$trans_jenis' order by id desc limit 1")->row();
        $cek = $this->db->query("select count(*) as jumlah from transaksi_detail where id > '".$get->id."'")->row();
        if ($cek->jumlah > 0) {
            $result = FALSE;
        }*/
        return $result;
    }
    
    function biaya_apoteker_by_penjualan($id) {
        $sql="select p.*, sum(t.nominal) as jasa from penjualan p
                left join resep r on (p.resep_id = r.id)
                left join resep_r rr on (r.id = rr.resep_id)
                left join tarif t on (t.id = rr.tarif_id) 
                where p.id = '$id'";
        
        return $this->db->query($sql);
    }
    
    function save_pemesanan() {
        $this->db->trans_begin();
        $id             = $_POST['no_sp'];
        $id_hidden      = $_POST['id'];
        $tanggal        = date2mysql($_POST['tanggal'])." ".date("H:i:s");
        $tgl_datang     = date2mysql($_POST['tanggal_datang']);
        $id_supplier    = $_POST['id_supplier'];
        $id_barang      = $_POST['id_barang'];
        $id_kemasan     = $_POST['kemasan'];
        $jumlah         = $_POST['jumlah'];
        //$id_user        = 'NULL';
        if ($id_hidden === '') {
            $sql = "insert INTO pemesanan set
                id = '$id',
                tanggal = '$tanggal',
                tgl_datang = '$tgl_datang',
                id_supplier = '$id_supplier',
                id_users = '".$this->session->userdata('id_user')."'";
            //echo $sql;
            $this->db->query($sql);
            $id_pemesanan = $id;

            foreach ($id_barang as $key => $data) {
                $id_packing = $this->db->query("select id from kemasan where id_barang = '$data' and id_kemasan = '".$id_kemasan[$key]."'")->row();
                //echo "select id from kemasan where id_barang = '$data' and id_kemasan = '".$id_kemasan[$key]."'<br/>";
                $sql = "insert into detail_pemesanan set
                    id_pemesanan = '$id_pemesanan',
                    id_kemasan = '".$id_packing->id."',
                    jumlah = '$jumlah[$key]'";
                //echo "select id from kemasan where id_barang = '$data' and id_kemasan = '".$id_kemasan[$key]."'<br/>";
                //echo $sql;
                $this->db->query($sql);
            }
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $result['status'] = FALSE;
            } else {
                $this->db->trans_commit();
                $result['status'] = TRUE;
            }
        } else {
            $this->db->query("delete from detail_pemesanan where id_pemesanan = '$id_hidden'");
            $sql = "update pemesanan set
                id = '$id',
                tanggal = '$tanggal',
                tgl_datang = '$tgl_datang',
                id_supplier = '$id_supplier',
                id_users = '".$this->session->userdata('id_user')."'
                where id = '$id_hidden'";
            //echo $sql;
            $this->db->query($sql);
            $id_pemesanan = $id_hidden;

            foreach ($id_barang as $key => $data) {
                $id_packing = $this->db->query("select id from kemasan where id_barang = '$data' and id_kemasan = '".$id_kemasan[$key]."'")->row();
                //echo "select id from kemasan where id_barang = '$data' and id_kemasan = '".$id_kemasan[$key]."'<br/>";
                $sql = "insert into detail_pemesanan set
                    id_pemesanan = '$id_pemesanan',
                    id_kemasan = '".$id_packing->id."',
                    jumlah = '$jumlah[$key]'";
                //echo "select id from kemasan where id_barang = '$data' and id_kemasan = '".$id_kemasan[$key]."'<br/>";
                //echo $sql;
                $this->db->query($sql);
            }
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $result['status'] = FALSE;
            } else {
                $this->db->trans_commit();
                $result['status'] = TRUE;
            }
        }
        $result['id_pemesanan'] = $id;
        $result['id'] = $id;
        die(json_encode($result));
    }
    
    function cetak_sp($id) {
        $sql = "select p.*, k.nama as karyawan, dp.jumlah, concat_ws(' ',b.nama, b.kekuatan, st.nama) as nama_barang, b.perundangan,
        st.nama as kemasan, s.nama as supplier, s.alamat as alamat_supplier from pemesanan p
        join supplier s on (p.id_supplier = s.id)
        join detail_pemesanan dp on (dp.id_pemesanan = p.id)
        join kemasan km on (km.id = dp.id_kemasan)
        join barang b on (b.id = km.id_barang)
        join satuan st on (st.id = km.id_kemasan)
        left join users u on (p.id_users = u.id)
        left join penduduk k on (u.id = k.id)
        where p.id = '$id'";
        return $this->db->query($sql);
    }
    
    function pemesanan_delete($id) {
        $cek = $this->cek_delete($id, 'Pemesanan');
        if ($cek == true) {
            $this->db->trans_begin();
            $this->db->delete('pemesanan', array('id' => $id));
            $this->db->delete('transaksi_detail', array('transaksi_id' => $id, 'transaksi_jenis' => 'Pemesanan'));
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $status['status'] = FALSE;
            } else {
                $this->db->trans_commit();
                $status['status'] = TRUE;
            }
        } else {
            $status['status'] = TRUE;
        }
        return $status;
    }
    
    function pemusnahan_delete($id) {
        $$cek = $this->cek_delete($id, 'Pemusnahan');
        if ($cek == true) {
            $this->db->trans_begin();
            $this->db->delete('pemusnahan', array('id' => $id));
            $this->db->delete('transaksi_detail', array('transaksi_id' => $id, 'transaksi_jenis' => 'Pemusnahan'));
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $status = FALSE;
            } else {
                $this->db->trans_commit();
                $status = TRUE;
            }
        } else {
            $status = FALSE;
        }
        $result['status'] = $status;
        return $result;
    }
    
    function penjualan_delete($id) {
        $cek = $this->cek_delete($id, 'Penjualan');
        if ($cek == true) {
            $this->db->trans_begin();
            $this->db->delete('penjualan', array('id' => $id));
            $this->db->delete('transaksi_detail', array('transaksi_id' => $id, 'transaksi_jenis' => 'Penjualan'));
            $this->db->delete('jurnal', array('jenis_transaksi' => 'Penjualan', 'id_transaksi' => $id));
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $status = FALSE;
            } else {
                $this->db->trans_commit();
                $status = TRUE;
            }
        } else {
            $status = FALSE;
        }
        
        $result['status'] = $status;
        return $result;
    }
    
    function pemesanan_muat_data($id = null, $id_user = NULL) {
        $q = null;
        if ($id != null) {
            $q.=" and p.id = '$id'";
        }
        if ($id_user != null) {
            $q.=" and t.unit_id = '".$this->session->userdata('id_unit')."'";
        }
        $sql = "select bk.nama as kategori, o.id as id_obat, o.generik, stb.nama as satuan_terbesar, o.perundangan, p.*, b.nama as barang, bp.isi, r.nama as pabrik, o.kekuatan, bp.id as id_pb, b.id as id_barang, bp.barcode, 
        bp.isi, s.nama as satuan, pd.nama as salesman, pdd.nama as petugas, t.masuk, sd.nama as sediaan, t.leadtime_hours, t.ss, ri.nama as suplier,
        r.alamat, kl.nama as kelurahan, st.nama as satuan_terkecil, t.barang_packing_id, t.ed, t.beli_diskon_percentage, t.beli_diskon_rupiah from pemesanan p
            join transaksi_detail t on (p.id = t.transaksi_id)
            join barang_packing bp on (t.barang_packing_id = bp.id)
            join barang b on (b.id = bp.barang_id)
            join barang_kategori bk on (bk.id = b.barang_kategori_id)
            left join obat o on (o.id = b.id)
            left join satuan s on (o.satuan_id = s.id)
            left join relasi_instansi r on (r.id = b.pabrik_relasi_instansi_id)
            left join relasi_instansi ri on (ri.id = p.suplier_relasi_instansi_id)
            left join sediaan sd on (sd.id = o.sediaan_id)
            left join satuan st on (st.id = bp.terkecil_satuan_id)
            left join satuan stb on (stb.id = bp.terbesar_satuan_id)
            left join penduduk pd on (pd.id = p.salesman_penduduk_id)
            join penduduk pdd on (pdd.id = p.pegawai_penduduk_id)
            left join kelurahan kl on (kl.id = r.kelurahan_id) where t.transaksi_jenis = 'Pemesanan' $q";
        //echo "<pre>".$sql."</pre>";
            return $this->db->query($sql);
        }
        
    function pembelian_save() {
        $this->db->trans_begin();
        $id_beli = post_safe('id_pembelian');
        $materai = currencyToNumber(post_safe('materai'));
        $data1 = array(
            'dokumen_no' => post_safe('nodoc'),
            'dokumen_tanggal' => date2mysql(post_safe('tgldoc')),
            'pemesanan_id' => post_safe('no_pemesanan'),
            'pegawai_penduduk_id' => $this->session->userdata('id_user'),
            'suplier_relasi_instansi_id' => post_safe('id_suplier'),
            'ppn' => post_safe('ppn'),
            'materai' => $materai,
            'tanggal_jatuh_tempo' => datetopg(post_safe('tempo')),
            'salesman_penduduk_id' => (post_safe('id_sales') != '')?post_safe('id_sales'):NULL,
            'ada_penerima_ttd' => post_safe('ttd'),
            'keterangan' => post_safe('keterangan'),
            'total' => post_safe('total_tagihan')
        );
        $kategori = post_safe('kategori');
        $pb = post_safe('pb');
        $id_pb = post_safe('id_pb');
        $ed = post_safe('ed');
        $harga = post_safe('harga');
        $disk_pr = str_replace(",", ".", post_safe('diskon_pr'));
        $disk_rp = post_safe('diskon_rp');
        $subtotal= post_safe('subtotal');
        $jumlah  = post_safe('jml');
        $het = post_safe('net');
        $terdiscsubtotal = post_safe('subtotaldisc');
        $ppn             = post_safe('ppn')/100;
        //$ppntotal= post_safe('ppn_total');
        if ($id_beli === '') {
            
            $this->db->insert('pembelian', $data1);
            $id_pembelian = $this->db->insert_id();

            //$debet_1 = 0;

            foreach ($id_pb as $key => $data) {
                if ($data != '') {
                    $base_hna=$harga[$key];
                    $hna_ppn= ($this->input->post('ppn')/100)*$base_hna;
                    $hna = $base_hna+$hna_ppn;

                    $base_hpp 	= ((currencyToNumber($harga[$key])*$jumlah[$key]) - ((currencyToNumber($harga[$key])*$jumlah[$key]) * ($disk_pr[$key]/100))) / ($jumlah[$key]);
                    $hpp_ppn	= ($this->input->post('ppn')/100)*$base_hpp;
                    $hpp 	= $base_hpp+$hpp_ppn;

                    $hna = currencyToNumber($harga[$key])+(currencyToNumber($harga[$key])*($this->input->post('ppn')/100));
                    //$hpp = $hna - (currencyToNumber($harga[$key]) - ($disk_pr[$key]/100)*currencyToNumber($harga[$key]));
                    $hpp_var1 = currencyToNumber($harga[$key]) - ($disk_pr[$key]/100)*currencyToNumber($harga[$key]);
                    $hpp_ppn  = $hpp_var1*($this->input->post('ppn')/100);
                        $hpp = $hpp_var1+$hpp_ppn;

                    if ($disk_rp[$key] == 0) {
                        $harga_terdiskon = currencyToNumber($harga[$key]) - (currencyToNumber($harga[$key])*($disk_pr[$key])/100);
                    } else if ($disk_pr[$key] == 0){
                        $harga_terdiskon = currencyToNumber($harga[$key]) - $disk_rp[$key];
                    }
                    //$jml = $this->db->query("select ed, date(waktu) as tanggal from transaksi_detail where transaksi_jenis != 'Pemesanan' and barang_packing_id = '$id_pb[$key]' and unit_id = '".$this->session->userdata('id_unit')."' and ed = '".  datetopg($ed[$key])."' order by waktu desc limit 1")->row();

                    $cek = $this->db->query("select date(waktu) as tanggal from transaksi_detail where transaksi_jenis = 'Pemesanan' and transaksi_id = '".post_safe('no_pemesanan')."' order by waktu desc limit 1")->row();
                    $beli= $this->db->query("select waktu from transaksi_detail where transaksi_jenis = 'Pembelian' and barang_packing_id = '$data' and unit_id = '".$this->session->userdata('id_unit')."' order by waktu desc limit 1")->row();

                    $leadTime = $this->db->query("select datediff('".date("Y-m-d")."','".$cek->tanggal."') as selisih")->row();
                    $sekarang = gmdate('Y-m-d' ,gmdate('U')+25200);
                    //$ss  = $this->db->query("select (avg(masuk)-avg(keluar)) as safetystock from transaksi_detail where transaksi_jenis != 'Pemesanan' and barang_packing_id = '$id_pb[$key]' and unit_id = '".$this->session->userdata('id_unit')."' and date(waktu) between '".$cek->tanggal."' and '$sekarang'")->row();

                    $data_trans = array(
                        'transaksi_id' => $id_pembelian,
                        'transaksi_jenis' => 'Pembelian',
                        'waktu' => date2mysql(post_safe('tgldoc')).' '.date("H:i:s"),
                        'ed' => datetopg($ed[$key]),
                        'barang_packing_id' => $data,
                        'unit_id' => $this->session->userdata('id_unit'),
                        'harga' => currencyToNumber($harga[$key]),
                        'beli_diskon_percentage' => $disk_pr[$key],
                        'beli_diskon_rupiah' => currencyToNumber($disk_rp[$key]),
                        'terdiskon_harga' => $harga_terdiskon,
                        'subtotal' => $subtotal[$key],
                        'ppn' => post_safe('ppn'),
                        'hna' => $hna,
                        'hpp' => $hpp,
                        'het' => currencyToNumber($het[$key]),
                        'masuk' => $jumlah[$key],
                        'leadtime_hours' => $leadTime->selisih,
                        'ss' => (isset($ss->safetystock)?$ss->safetystock:'0'),
                        'selisih_waktu_beli' => (isset($beli->waktu)?range_hours_between_two_dates($beli->waktu, date2mysql(post_safe('tgldoc')).' '.date("H:i:s")):'0')
                    );
                    $this->db->insert('transaksi_detail', $data_trans);
                    /*Update HPP & HNA*/
                    $data_packing = array(
                        'hpp' => $hpp,
                        'hna' => $hna
                    );
                    $this->db->where('id', $data);
                    $this->db->update('barang_packing', $data_packing);
                    
                    if ($kategori[$key] === 'Alat Kesehatan') {
                        $akun_debet = 34;
                        $akun_kredit= 84;
                    }
                    else if ($kategori[$key] === 'Obat') {
                        $akun_debet = 188;
                        $akun_kredit= 84;
                    }
                    else if ($kategori[$key] === 'Bahan Baku Obat') {
                        $akun_debet = 189;
                        $akun_kredit= 84;
                    }
                    else if (($kategori[$key] === 'BHP') or ($kategori[$key] === 'Embalase')) {
                        $akun_debet = 191;
                        $akun_kredit= 84;
                    }
                    else if ($kategori[$key] === 'Reagen Lab.') {
                        $akun_debet = 192;
                        $akun_kredit= 84;
                    }
                    else if ($kategori[$key] === 'Bahan Radiologi') {
                        $akun_debet = 193;
                        $akun_kredit= 84;
                    }
                    else if ($kategori[$key] === 'Alat Fisioterapi') {
                        $akun_debet = 194;
                        $akun_kredit= 84;
                    }
                    else if ($kategori[$key] === 'Gas Medis') {
                        $akun_debet = 206;
                        $akun_kredit= 84;
                    }
                    else if (($kategori[$key] === 'Alat Tulis Kantor') or ($kategori[$key] === 'Pos dan Giro')) {
                        $akun_debet = 51;
                        $akun_kredit= 84;
                    }
                    else if ($kategori[$key] === 'Percetakan') {
                        $akun_debet = 195;
                        $akun_kredit= 84;
                    }
                    else if ($kategori[$key] === 'Linen') {
                        $akun_debet = 196;
                        $akun_kredit= 84;
                    }
                    else if ($kategori[$key] === 'Kelontong') {
                        $akun_debet = 197;
                        $akun_kredit= 84;
                    }
                    else if ($kategori[$key] === 'Lain-lain') {
                        $akun_debet = 198;
                        $akun_kredit= 84;
                    }
                    else if ($kategori[$key] === 'Bahan Makanan Basah') {
                        $akun_debet = 199;
                        $akun_kredit= 84;
                    }
                    else if ($kategori[$key] === 'Bahan Makanan Kering') {
                        $akun_debet = 200;
                        $akun_kredit= 84;
                    }
                    
                    $terdiscount = $terdiscsubtotal[$key]+($terdiscsubtotal[$key]*$ppn);
                    
                    if ($disk_pr[$key] === '100') {
                        $jurnal_188 = array(
                            'waktu' => date("Y-m-d H:i:s"),
                            'id_transaksi' => $id_pembelian,
                            'jenis_transaksi' => 'Pembelian',
                            'ket_transaksi' => post_safe('keterangan'),
                            'id_sub_sub_sub_sub_rekening' => $akun_debet,
                            'debet' => (currencyToNumber($harga[$key])*$jumlah[$key])
                        );
                        $this->db->insert('jurnal', $jurnal_188);

                        $jurnal_84 = array(
                            'waktu' => date("Y-m-d H:i:s"),
                            'id_transaksi' => $id_pembelian,
                            'jenis_transaksi' => 'Pembelian',
                            'ket_transaksi' => post_safe('keterangan'),
                            'id_sub_sub_sub_sub_rekening' => '273',
                            'kredit' => (currencyToNumber($harga[$key])*$jumlah[$key])
                        );
                        $this->db->insert('jurnal', $jurnal_84);
                    } else {
                        $jurnal_188 = array(
                            'waktu' => date("Y-m-d H:i:s"),
                            'id_transaksi' => $id_pembelian,
                            'jenis_transaksi' => 'Pembelian',
                            'ket_transaksi' => post_safe('keterangan'),
                            'id_sub_sub_sub_sub_rekening' => $akun_debet,
                            'debet' => ($terdiscount)
                        );
                        $this->db->insert('jurnal', $jurnal_188);

                        $jurnal_84 = array(
                            'waktu' => date("Y-m-d H:i:s"),
                            'id_transaksi' => $id_pembelian,
                            'jenis_transaksi' => 'Pembelian',
                            'ket_transaksi' => post_safe('keterangan'),
                            'id_sub_sub_sub_sub_rekening' => $akun_kredit,
                            'kredit' => ($terdiscount)
                        );
                        $this->db->insert('jurnal', $jurnal_84);
                    }
                }
            }
            
            if ($materai !== '0') {
                $jurnal_188 = array(
                    'waktu' => date("Y-m-d H:i:s"),
                    'id_transaksi' => $id_pembelian,
                    'jenis_transaksi' => 'Pembelian',
                    'ket_transaksi' => post_safe('keterangan'),
                    'id_sub_sub_sub_sub_rekening' => 156,
                    'debet' => $materai
                );
                $this->db->insert('jurnal', $jurnal_188);

                $jurnal_84 = array(
                    'waktu' => date("Y-m-d H:i:s"),
                    'id_transaksi' => $id_pembelian,
                    'jenis_transaksi' => 'Pembelian',
                    'ket_transaksi' => post_safe('keterangan'),
                    'id_sub_sub_sub_sub_rekening' => 51,
                    'kredit' => $materai
                );
                $this->db->insert('jurnal', $jurnal_84);
            }
            $result['action'] = "add";
        } else {
            $this->db->where('id', $id_beli);
            $this->db->update('pembelian', $data1);
            $this->db->delete('transaksi_detail', array('transaksi_jenis' => 'Pembelian', 'transaksi_id' => $id_beli));
            $this->db->delete('jurnal', array('id_transaksi' => $id_beli, 'jenis_transaksi' => 'Pembelian'));
            foreach ($id_pb as $key => $data) {
                if ($data != '') {
                    $base_hna=$harga[$key];
                    $hna_ppn= (post_safe('ppn')/100)*$base_hna;
                    $hna = $base_hna+$hna_ppn;

                    $base_hpp 	= ((currencyToNumber($harga[$key])*$jumlah[$key]) - ((currencyToNumber($harga[$key])*$jumlah[$key]) * ($disk_pr[$key]/100))) / ($jumlah[$key]);
                    $hpp_ppn	= (post_safe('ppn')/100)*$base_hpp;
                    $hpp 	= $base_hpp+$hpp_ppn;

                    $hna = currencyToNumber($harga[$key])+(currencyToNumber($harga[$key])*(post_safe('ppn')/100));
                    //$hpp = $hna - (currencyToNumber($harga[$key]) - ($disk_pr[$key]/100)*currencyToNumber($harga[$key]));
                    $hpp_var1 = currencyToNumber($harga[$key]) - ($disk_pr[$key]/100)*currencyToNumber($harga[$key]);
                    $hpp_ppn  = $hpp_var1*(post_safe('ppn')/100);
                    $hpp = $hpp_var1+$hpp_ppn;

                    if ($disk_rp[$key] == 0) {
                        $harga_terdiskon = currencyToNumber($harga[$key]) - (currencyToNumber($harga[$key])*($disk_pr[$key])/100);
                    } else if ($disk_pr[$key] == 0){
                        $harga_terdiskon = currencyToNumber($harga[$key]) - $disk_rp[$key];
                    }
                    //$jml = $this->db->query("select ed, date(waktu) as tanggal from transaksi_detail where transaksi_jenis != 'Pemesanan' and barang_packing_id = '$id_pb[$key]' and unit_id = '".$this->session->userdata('id_unit')."' and ed = '".  datetopg($ed[$key])."' order by waktu desc limit 1")->row();

                    $cek = $this->db->query("select date(waktu) as tanggal from transaksi_detail where transaksi_jenis = 'Pemesanan' and transaksi_id = '".post_safe('no_pemesanan')."' order by waktu desc limit 1")->row();
                    $beli= $this->db->query("select waktu from transaksi_detail where transaksi_jenis = 'Pembelian' and barang_packing_id = '$data' and unit_id = '".$this->session->userdata('id_unit')."' order by waktu desc limit 1")->row();

                    $leadTime = $this->db->query("select datediff('".date("Y-m-d")."','".$cek->tanggal."') as selisih")->row();
                    $sekarang = gmdate('Y-m-d' ,gmdate('U')+25200);
                    //$ss  = $this->db->query("select (avg(masuk)-avg(keluar)) as safetystock from transaksi_detail where transaksi_jenis != 'Pemesanan' and barang_packing_id = '$id_pb[$key]' and unit_id = '".$this->session->userdata('id_unit')."' and date(waktu) between '".$cek->tanggal."' and '$sekarang'")->row();

                    $data_trans = array(
                        'transaksi_id' => $id_beli,
                        'transaksi_jenis' => 'Pembelian',
                        'waktu' => date2mysql(post_safe('tgldoc')).' '.date("H:i:s"),
                        'ed' => datetopg($ed[$key]),
                        'barang_packing_id' => $data,
                        'unit_id' => $this->session->userdata('id_unit'),
                        'harga' => currencyToNumber($harga[$key]),
                        'beli_diskon_percentage' => $disk_pr[$key],
                        'beli_diskon_rupiah' => currencyToNumber($disk_rp[$key]),
                        'terdiskon_harga' => $harga_terdiskon,
                        'subtotal' => $subtotal[$key],
                        'ppn' => post_safe('ppn'),
                        'hna' => $hna,
                        'hpp' => $hpp,
                        'het' => currencyToNumber($het[$key]),
                        'masuk' => $jumlah[$key],
                        'leadtime_hours' => $leadTime->selisih,
                        'ss' => (isset($ss->safetystock)?$ss->safetystock:'0'),
                        'selisih_waktu_beli' => (isset($beli->waktu)?range_hours_between_two_dates($beli->waktu, date2mysql(post_safe('tgldoc')).' '.date("H:i:s")):'0')
                    );
                    $this->db->insert('transaksi_detail', $data_trans);
                    /*Update HPP & HNA*/
                    $data_packing = array(
                        'hpp' => $hpp,
                        'hna' => $hna
                    );
                    $this->db->where('id', $data);
                    $this->db->update('barang_packing', $data_packing);
                    
                    if ($kategori[$key] === 'Alat Kesehatan') {
                        $akun_debet = 34;
                        $akun_kredit= 84;
                    }
                    else if ($kategori[$key] === 'Obat') {
                        $akun_debet = 188;
                        $akun_kredit= 84;
                    }
                    else if ($kategori[$key] === 'Bahan Baku Obat') {
                        $akun_debet = 189;
                        $akun_kredit= 84;
                    }
                    else if ($kategori[$key] === 'BHP') {
                        $akun_debet = 191;
                        $akun_kredit= 84;
                    }
                    else if ($kategori[$key] === 'Reagen Lab.') {
                        $akun_debet = 192;
                        $akun_kredit= 84;
                    }
                    else if ($kategori[$key] === 'Bahan Radiologi') {
                        $akun_debet = 193;
                        $akun_kredit= 84;
                    }
                    else if ($kategori[$key] === 'Alat Fisioterapi') {
                        $akun_debet = 194;
                        $akun_kredit= 84;
                    }
                    else if ($kategori[$key] === 'Gas Medis') {
                        $akun_debet = 206;
                        $akun_kredit= 84;
                    }
                    else if ($kategori[$key] === 'Alat Tulis Kantor') {
                        $akun_debet = 51;
                        $akun_kredit= 84;
                    }
                    else if ($kategori[$key] === 'Percetakan') {
                        $akun_debet = 195;
                        $akun_kredit= 84;
                    }
                    else if ($kategori[$key] === 'Linen') {
                        $akun_debet = 196;
                        $akun_kredit= 84;
                    }
                    else if ($kategori[$key] === 'Kelontong') {
                        $akun_debet = 197;
                        $akun_kredit= 84;
                    }
                    else if ($kategori[$key] === 'Lain-lain') {
                        $akun_debet = 198;
                        $akun_kredit= 84;
                    }
                    else if ($kategori[$key] === 'Bahan Makanan Basah') {
                        $akun_debet = 199;
                        $akun_kredit= 84;
                    }
                    else if ($kategori[$key] === 'Bahan Makanan Kering') {
                        $akun_debet = 200;
                        $akun_kredit= 84;
                    }
                    $terdiscount = $terdiscsubtotal[$key]+($terdiscsubtotal[$key]*$ppn);
                    
                    $jurnal_188 = array(
                        'waktu' => date("Y-m-d H:i:s"),
                        'id_transaksi' => $id_beli,
                        'jenis_transaksi' => 'Pembelian',
                        'ket_transaksi' => post_safe('keterangan'),
                        'id_sub_sub_sub_sub_rekening' => $akun_debet,
                        'debet' => ($terdiscount)
                    );
                    $this->db->insert('jurnal', $jurnal_188);

                    $jurnal_84 = array(
                        'waktu' => date("Y-m-d H:i:s"),
                        'id_transaksi' => $id_beli,
                        'jenis_transaksi' => 'Pembelian',
                        'ket_transaksi' => post_safe('keterangan'),
                        'id_sub_sub_sub_sub_rekening' => $akun_kredit,
                        'kredit' => ($terdiscount)
                    );
                    $this->db->insert('jurnal', $jurnal_84);
                }
            
                $terdiscount = $terdiscsubtotal[$key]+($terdiscsubtotal[$key]*$ppn);
                    
                if ($disk_pr[$key] === '100') {
                    $jurnal_188 = array(
                        'waktu' => date("Y-m-d H:i:s"),
                        'id_transaksi' => $id_beli,
                        'jenis_transaksi' => 'Pembelian',
                        'ket_transaksi' => post_safe('keterangan'),
                        'id_sub_sub_sub_sub_rekening' => $akun_debet,
                        'debet' => (currencyToNumber($harga[$key])*$jumlah[$key])
                    );
                    $this->db->insert('jurnal', $jurnal_188);

                    $jurnal_84 = array(
                        'waktu' => date("Y-m-d H:i:s"),
                        'id_transaksi' => $id_beli,
                        'jenis_transaksi' => 'Pembelian',
                        'ket_transaksi' => post_safe('keterangan'),
                        'id_sub_sub_sub_sub_rekening' => '273',
                        'kredit' => (currencyToNumber($harga[$key])*$jumlah[$key])
                    );
                    $this->db->insert('jurnal', $jurnal_84);
                } else {
                    $jurnal_188 = array(
                        'waktu' => date("Y-m-d H:i:s"),
                        'id_transaksi' => $id_beli,
                        'jenis_transaksi' => 'Pembelian',
                        'ket_transaksi' => post_safe('keterangan'),
                        'id_sub_sub_sub_sub_rekening' => $akun_debet,
                        'debet' => ($terdiscount)
                    );
                    $this->db->insert('jurnal', $jurnal_188);

                    $jurnal_84 = array(
                        'waktu' => date("Y-m-d H:i:s"),
                        'id_transaksi' => $id_beli,
                        'jenis_transaksi' => 'Pembelian',
                        'ket_transaksi' => post_safe('keterangan'),
                        'id_sub_sub_sub_sub_rekening' => $akun_kredit,
                        'kredit' => ($terdiscount)
                    );
                    $this->db->insert('jurnal', $jurnal_84);
                }

            }
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
            }
            
            
            if ($materai !== '0') {
                $jurnal_188 = array(
                    'waktu' => date("Y-m-d H:i:s"),
                    'id_transaksi' => $id_beli,
                    'jenis_transaksi' => 'Pembelian',
                    'ket_transaksi' => post_safe('keterangan'),
                    'id_sub_sub_sub_sub_rekening' => 156,
                    'debet' => $materai
                );
                $this->db->insert('jurnal', $jurnal_188);

                $jurnal_84 = array(
                    'waktu' => date("Y-m-d H:i:s"),
                    'id_transaksi' => $id_beli,
                    'jenis_transaksi' => 'Pembelian',
                    'ket_transaksi' => post_safe('keterangan'),
                    'id_sub_sub_sub_sub_rekening' => 51,
                    'kredit' => $materai
                );
                $this->db->insert('jurnal', $jurnal_84);
            }
            $id_pembelian = $id_beli;
            $result['action'] = "edit";
        }
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $status = FALSE;
        } else {
            $this->db->trans_commit();
            $status = TRUE;
        }
        $result['status'] = $status;
        $result['id_pembelian'] = $id_pembelian;
        return $result;
    }
    
    function pembelian_load_data($id = null) {
        $q = null;
        if ($id != null) {
            $q.=" and p.id = '$id'";
        }
        $sql = "select bk.nama as kategori, stb.nama as satuan_terbesar, td.*, o.generik, o.id as id_obat, b.nama as barang, p.id as id_pembelian, p.dokumen_no, p.dokumen_tanggal, p.id as id_pembelian, p.pemesanan_id,
            p.materai, p.ppn, p.tanggal_jatuh_tempo, p.ada_penerima_ttd, p.keterangan,
        bp.barcode, bp.isi, ri.nama as suplier, ri.id as id_suplier, st.nama as satuan_terkecil, o.kekuatan, pdd.id as id_sales, pdd.nama as salesman, s.nama as satuan, sd.nama as sediaan, r.nama as pabrik from transaksi_detail td
        join pembelian p on (td.transaksi_id = p.id)
        left join penduduk pdd on (pdd.id = p.salesman_penduduk_id)
        join barang_packing bp on (bp.id = td.barang_packing_id)
        join barang b on (b.id = bp.barang_id)
        join barang_kategori bk on (bk.id = b.barang_kategori_id)
        left join obat o on (b.id = o.id)
        left join sediaan sd on (sd.id = o.sediaan_id)
        left join satuan s on (s.id = o.satuan_id)
        left join satuan st on (st.id = bp.terkecil_satuan_id)
        left join satuan stb on (stb.id = bp.terbesar_satuan_id)
        left join relasi_instansi r on (r.id = b.pabrik_relasi_instansi_id)
        left join relasi_instansi ri on (p.suplier_relasi_instansi_id = ri.id)
        where td.transaksi_jenis = 'Pembelian' $q";
        //echo "<pre>".$sql."</pre>";
        return $this->db->query($sql);
    }
    
    function repackage_load_data($id) {
        $sql = "select td.*, o.generik, o.id as id_obat, b.id as id_barang, b.nama as barang,
        bp.barcode, bp.isi, st.nama as satuan_terkecil, o.kekuatan, s.nama as satuan, sd.nama as sediaan, r.nama as pabrik from transaksi_detail td
        join barang_packing bp on (bp.id = td.barang_packing_id)
        join barang b on (b.id = bp.barang_id)
        left join obat o on (b.id = o.id)
        left join sediaan sd on (sd.id = o.sediaan_id)
        left join satuan s on (s.id = o.satuan_id)
        left join satuan st on (st.id = bp.terkecil_satuan_id)
        left join relasi_instansi r on (r.id = b.pabrik_relasi_instansi_id)
        where td.transaksi_jenis = 'Repackage' and td.transaksi_id = '$id'";
        //echo "<pre>".$sql."</pre>";
        return $this->db->query($sql);
    }
    
    function repackage_save() {
        $this->db->trans_begin();
        $id_pb = post_safe('id_pb');
        $id_pb_hasil = post_safe('id_pb_hasil');
        $asal = post_safe('jml_asal');
        $id_repackage = post_safe('id_repackage');
        if ($id_repackage === '') {
            $data = $this->db->query("select transaksi_id from transaksi_detail where transaksi_jenis = 'Repackage' order by waktu desc limit 1")->row();
            if (!isset($data->transaksi_id)) {
                $transaksi_id = 1;
                //$transaksi_id2= 2;
            } else {
                $transaksi_id = $data->transaksi_id + 1;
                //$transaksi_id2= $data->transaksi_id + 2;
            }
            $awal= $this->db->query("select * from transaksi_detail where barang_packing_id = '$id_pb' and unit_id = '".$this->session->userdata('id_unit')."' and transaksi_jenis != 'Pemesanan' order by waktu desc limit 1")->row();
    //        $sisa = $awal->sisa - post_safe('jml_asal');
            $data_packing_awal = array(
                'transaksi_id' => $transaksi_id,
                'transaksi_jenis' => 'Repackage',
                'waktu' => $this->waktu,
                'ed' => post_safe('ed'),
                'barang_packing_id' => $id_pb,
                'unit_id' => $this->session->userdata('id_unit'),
                'hna' => isset($awal->hna)?$awal->hna:'0',
                'hpp' => isset($awal->hpp)?$awal->hpp:'0',
                'het' => isset($awal->het)?$awal->het:'0',
                'masuk' => '0',
                'keluar' => $asal,
            );
            $this->db->insert('transaksi_detail',$data_packing_awal);

            $awals= $this->db->query("select * from transaksi_detail where barang_packing_id = '$id_pb_hasil' and unit_id = '".$this->session->userdata('id_unit')."' and transaksi_jenis != 'Pemesanan' and ed = '".post_safe('ed')."' order by waktu desc limit 1")->row();
    //        $sisas = (isset($awals->sisa)?$awals->sisa:0) + post_safe('isi_hasil');
            $pb = $this->db->query("select * from barang_packing where id = '$id_pb_hasil'")->row();

                $data_packing_hasil = array(
                'transaksi_id' => $transaksi_id,
                'transaksi_jenis' => 'Repackage',
                'waktu' => $this->waktu,
                'ed' => post_safe('ed'),
                'barang_packing_id' => $id_pb_hasil,
                'unit_id' => $this->session->userdata('id_unit'),
                'hna' => ($awal->hna/(post_safe('isi_hasil')/$asal)),
                'hpp' => ($awal->hpp/(post_safe('isi_hasil')/$asal)),
                'het' => ($awal->het/(post_safe('isi_hasil')/$asal)),
                'masuk' => post_safe('isi_hasil'),
                'keluar' => '0',
            );
            $this->db->insert('transaksi_detail',$data_packing_hasil);
            
            $data_packing = array(
                'hpp' => ($awal->hpp/(post_safe('isi_hasil')/$asal)),
                'hna' => ($awal->hna/(post_safe('isi_hasil')/$asal))
            );
            $this->db->where('id', $id_pb_hasil);
            $this->db->update('barang_packing', $data_packing);
            
        } else {
            $this->db->delete('transaksi_detail', array('transaksi_id' => $id_repackage, 'transaksi_jenis' => 'Repackage'));
            $awal= $this->db->query("select * from transaksi_detail where barang_packing_id = '$id_pb' and unit_id = '".$this->session->userdata('id_unit')."' and transaksi_jenis != 'Pemesanan' and ed = '".post_safe('ed')."' order by waktu desc limit 1")->row();
    //        $sisa = $awal->sisa - post_safe('jml_asal');
            $data_packing_awal = array(
                'transaksi_id' => $id_repackage,
                'transaksi_jenis' => 'Repackage',
                'waktu' => $this->waktu,
                'ed' => post_safe('ed'),
                'barang_packing_id' => $id_pb,
                'unit_id' => $this->session->userdata('id_unit'),
                'hna' => isset($awal->hna)?$awal->hna:'0',
                'hpp' => isset($awal->hpp)?$awal->hpp:'0',
                'het' => isset($awal->het)?$awal->het:'0',
                'masuk' => '0',
                'keluar' => $asal,
            );
            $this->db->insert('transaksi_detail',$data_packing_awal);

            $awals= $this->db->query("select * from transaksi_detail where barang_packing_id = '$id_pb_hasil' and unit_id = '".$this->session->userdata('id_unit')."' and transaksi_jenis != 'Pemesanan' order by waktu desc limit 1")->row();
    //        $sisas = (isset($awals->sisa)?$awals->sisa:0) + post_safe('isi_hasil');
            $pb = $this->db->query("select * from barang_packing where id = '$id_pb_hasil'")->row();

                $data_packing_hasil = array(
                'transaksi_id' => $id_repackage,
                'transaksi_jenis' => 'Repackage',
                'waktu' => $this->waktu,
                'ed' => post_safe('ed'),
                'barang_packing_id' => $id_pb_hasil,
                'unit_id' => $this->session->userdata('id_unit'),
                'hna' => ($awal->hna/(post_safe('isi_hasil')/$asal)),
                'hpp' => ($awal->hpp/(post_safe('isi_hasil')/$asal)),
                'het' => ($awal->het/(post_safe('isi_hasil')/$asal)),
                'masuk' => post_safe('isi_hasil'),
                'keluar' => '0',
            );
            $this->db->insert('transaksi_detail',$data_packing_hasil);
            $data_packing = array(
                'hpp' => ($awal->hpp/(post_safe('isi_hasil')/$asal)),
                'hna' => ($awal->hna/(post_safe('isi_hasil')/$asal))
            );
            $this->db->where('id', $id_pb_hasil);
            $this->db->update('barang_packing', $data_packing);
            
            $transaksi_id = $id_repackage;
        }
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $status = FALSE;
        } else {
            $this->db->trans_commit();
            $status = TRUE;
        }
        $result['status'] = $status;
        $result['id_repackage'] = $transaksi_id;
        return $result;
    }
    
    function jenis_transaksi_load_data() {
        return array(
            '' => 'Semua Jenis Transaksi ...',
            'Stok Opname' => 'Stok Opname',
            'Pemesanan' => 'Pemesanan',
            'Pembelian' => 'Penerimaan Pembelian',
            'Repackage' => 'Repackage',
            'Retur Pembelian' => 'Retur Pembelian',
            'Penerimaan Retur Pembelian' => 'Penerimaan Retur Pembelian',
            'Pemusnahan' => 'Pemusnahan',
            'Distribusi' => 'Distribusi',
            'Pemakaian' => 'Pemakaian',
            'Penerimaan Distribusi' => 'Penerimaan Distribusi',
            'Penerimaan Retur Distribusi' => 'Penerimaan Retur Distribusi',
            'Penjualan' => 'Penjualan',
            'Retur Penjualan' => 'Retur Penjualan'
        );
    }
    
    function informasi_stok_load_data($param) {
        $q = null; $last = null; $order = null; $where = null; $jml = null;
        if ($param['awal'] != null and $param['akhir'] != NULL) {
            $q.=" and td.waktu between '". datetime2mysql($param['awal']).":00' and '". datetime2mysql($param['akhir']).":59'";
        }
        if ($param['id_pb'] != NULL) {
            $q.=" and td.barang_packing_id = '$param[id_pb]'";
        }
        if (isset($param['sediaan']) and $param['sediaan'] != NULL) {
            $q.=" and o.sediaan_id = '$param[sediaan]'";
        }
        if ($param['ven'] != NULL) {
            $q.=" and o.ven like ('%$param[ven]%')";
        }
        if (isset($param['ha']) and $param['ha'] != NULL) {
            $q.=" and o.high_alert = '$param[ha]'";
        }
        if ($param['perundangan'] != NULL) {
            $q.=" and o.perundangan = '$param[perundangan]'";
        }
        if ($param['generik'] != NULL) {
            $q.=" and o.generik = '$param[generik]'";
        }
        $unit = "and td.unit_id = '".$this->session->userdata('id_unit')."'";
        if ($param['unit'] != null) {
            $unit=" and td.unit_id = '$param[unit]'"; 
        }
        if ($param['jenis'] != NULL) {
            $q.=" and td.transaksi_jenis = '$param[jenis]'";
            $where = " where transaksi_jenis = '$param[jenis]'";
        }
        if (isset($param['formularium']) and $param['formularium'] != NULL) {
            $q.=" and o.formularium = '$param[formularium]'";
        }
        if (isset($param['kategori']) and $param['kategori'] != NULL) {
            $q.=" and bk.jenis = '$param[kategori]'";
        }
        $cek = $this->db->query("select count(*) as jumlah from transaksi_detail where transaksi_jenis = 'Pembelian'")->row();
        if ($cek->jumlah > 0 and $param['sort'] == 'last') {
            $q.=" and td.transaksi_jenis != 'Pemesanan'";
        }
        $attr = NULL;
        if ($param['sort'] != NULL) {
            if ($param['sort'] == 'Terakhir') {
                //$q.=" and td.sisa > 0";
                $jml   = "(sum(masuk)-sum(keluar)) as sisa,";
                $attr  = "sum(masuk) as masuk, sum(keluar) as keluar,";
                $order = " group by bp.id, td.ed order by td.waktu asc";
                $q.=" and td.transaksi_jenis != 'Pemesanan'";
            }
            if ($param['sort'] == 'History') {
                if ($param['awal'] === '' and $param['akhir'] === '') {
                    $q.=" and date(td.waktu) = '".date("Y-m-d")."'";
                }
                $lap = null; 
                if (isset($param['laporan']) and $param['laporan'] == 'abc') {
                    $jml = "sum(td.keluar) as jml_keluar, avg(td.hna*td.margin_percentage)+td.hna as harga_obat, ";
                    $lap = "group by td.barang_packing_id";
                }
                $q.=" $lap order by td.waktu asc, td.id asc";
            }
            if ($param['sort'] == 'Kosong') {
                //$q.=" and td.sisa > 0";
                $jml   = "(sum(masuk)-sum(keluar)) as sisa,";
                $attr  = "sum(masuk) as masuk, sum(keluar) as keluar,";
                $order = " group by bp.id having sisa = '0' order by td.waktu asc";
                $q.=" and td.transaksi_jenis != 'Pemesanan'";
            }
            if ($param['sort'] == 'ED') {
                //$q.=" and td.sisa > 0";
                $jml   = "(sum(masuk)-sum(keluar)) as sisa,";
                $attr  = "sum(masuk) as masuk, sum(keluar) as keluar,";
                $order = " group by bp.id, td.ed having ed <= (SELECT date(DATE_ADD( NOW(), INTERVAL 6 month ))) order by td.waktu asc";
                $q.=" and td.transaksi_jenis != 'Pemesanan'";
            }
            if ($param['sort'] === 'Death Stock') {
                $date = mktime(0, 0, 0, date("m"), date("d")-180, date("Y"));
                $past = date("Y-m-d", $date);
                $attr = "count(*) as jml_death, sum(masuk)-sum(keluar) as sisa,";
                $order= "group by td.barang_packing_id having jml_death = '1'";
                $q.=" and td.transaksi_jenis != 'Pemesanan' and date(td.waktu) between '".$past."' and '".date("Y-m-d")."'";
            }
        }
        
        if ($param['sort'] == NULL) {
            $q.=" group by bp.id, td.ed order by td.waktu asc";
        }
        
        $sql = "select $jml td.*, $attr bp.id as id_pb, bp.margin, bp.ppn_jual, o.high_alert, 
            bp.diskon, o.generik, b.nama as barang, st.nama as satuan_terkecil, stb.nama as satuan_terbesar,
            bp.isi, o.kekuatan, r.nama as pabrik, s.nama as satuan, 
            sd.nama as sediaan from transaksi_detail td
            join barang_packing bp on (td.barang_packing_id = bp.id)
            join barang b on (bp.barang_id = b.id)
            join barang_kategori bk on (b.barang_kategori_id = bk.id)
            left join relasi_instansi r on (r.id = b.pabrik_relasi_instansi_id)
            left join obat o on (b.id = o.id)
            left join satuan s on (s.id = o.satuan_id)
            left join satuan st on (st.id = bp.terkecil_satuan_id)
            left join satuan stb on (stb.id = bp.terbesar_satuan_id)
            left join sediaan sd on (sd.id = o.sediaan_id)
            $last
            where td.id is not null $unit
            $q 
            $order";
        //echo "<pre>".$sql."</pre>";
        return $this->db->query($sql);
    }
    
    function stelling_load_data($id, $awal = null, $akhir = null) {
        
        $q = null; $c = NULL;
        if ($awal != null and $akhir != null) {
            $q = "and date(td.waktu) between '".date2mysql($awal)."' and '".date2mysql($akhir)."'";
            $c = "and date(waktu) between '".date2mysql($awal)."' and '".date2mysql($akhir)."'";
        }
        $cek = $this->db->query("select count(*) as jumlah from transaksi_detail where barang_packing_id = '$id' $c")->row();
        if ($cek->jumlah == '0') {
            $sql = "select bp.id as id_pb, o.generik, b.nama as barang, st.nama as satuan_terkecil, bp.isi, o.kekuatan, r.nama as pabrik, s.nama as satuan, sd.nama as sediaan from barang b
                join barang_packing bp on (bp.barang_id = b.id)
                left join obat o on (o.id = b.id)
                left join sediaan sd on (sd.id = o.sediaan_id)
                left join satuan s on (s.id = o.satuan_id)
                left join satuan st on (st.id = bp.terkecil_satuan_id)
                left join relasi_instansi r on (r.id = b.pabrik_relasi_instansi_id)
                where bp.id = '$id'";
        } else {
            $sql = "select td.*, bp.id as id_pb, o.generik, b.nama as barang, st.nama as satuan_terkecil, bp.isi, o.kekuatan, r.nama as pabrik, s.nama as satuan, sd.nama as sediaan 
                from barang b 
                join barang_packing bp on (bp.barang_id = b.id)
                left join transaksi_detail td on (td.barang_packing_id = bp.id)
                left join relasi_instansi r on (r.id = b.pabrik_relasi_instansi_id)
                left join obat o on (b.id = o.id)
                left join satuan s on (s.id = o.satuan_id)
                left join satuan st on (st.id = bp.terkecil_satuan_id)
                left join sediaan sd on (sd.id = o.sediaan_id) where td.id is NOT NULL
                    and td.transaksi_jenis != 'Pemesanan' and bp.id = '$id' $q";
        }
        //echo "<pre>".$sql."</pre>";
        return $this->db->query($sql);
    }
    
    function stelling_list_data($id, $awal = null, $akhir = null) {
        $q = null;
        if ($awal != null and $akhir != null) {
            $q = "and date(td.waktu) between '".datetime2mysql($awal)."' and '".datetime2mysql($akhir)."'";
        }
        $sql = "select td.*, bp.id as id_pb, o.generik, b.nama as barang, st.nama as satuan_terkecil, bp.isi, o.kekuatan, r.nama as pabrik, s.nama as satuan, sd.nama as sediaan 
                from barang b 
                join barang_packing bp on (bp.barang_id = b.id)
                left join transaksi_detail td on (td.barang_packing_id = bp.id)
                left join relasi_instansi r on (r.id = b.pabrik_relasi_instansi_id)
                left join obat o on (b.id = o.id)
                left join satuan s on (s.id = o.satuan_id)
                left join satuan st on (st.id = bp.terkecil_satuan_id)
                left join sediaan sd on (sd.id = o.sediaan_id) where td.id is NOT NULL
                    and td.transaksi_jenis != 'Pemesanan' and td.unit_id = '".$this->session->userdata('id_unit')."' and bp.id = '$id' $q";
        //echo "<pre>".$sql."</pre>";
        return $this->db->query($sql);
    }
    
    function save_stok_opname() {
        $tanggal    = date2mysql($_POST['tanggal']).' '.date("H:i:s");
        $id_barang  = $_POST['id_barang'];
        $nobatch    = $_POST['nobatch'];
        $ed         = $_POST['ed'];
        $masuk      = $_POST['masuk'];
        $keluar     = $_POST['keluar'];

        foreach ($id_barang as $key => $data) {
            $sql = "insert into stok set
                waktu = '$tanggal',
                transaksi = 'Stok Opname',
                nobatch = '$nobatch[$key]',
                id_barang = '$data',
                ed = '".date2mysql($ed[$key])."',
                masuk = '$masuk[$key]',
                keluar = '$keluar[$key]',
                id_unit = '".$this->session->userdata('id_unit')."'
            ";
            $this->db->query($sql);
        }
        return array('status' => true);
    }
    function hutang_load_data($param) {
        $q = null;
        /*if ($awal != null and $akhir != null) {
            $q.="and p.dokumen_tanggal between '".  datetime2mysql($awal)."' and '".datetime2mysql($akhir)."'";
        }*/
        if ($param['id_supplier'] !== '') {
            $q.=" and p.id_supplier = '".$param['id_supplier']."'";
        }
        if ($param['tempo'] === 'Ya') {
            $q.=" and p.jatuh_tempo <= '".date("Y-m-d")."' ";
        }
        if ($param['tempo'] === 'Belum') {
            $q.=" and p.jatuh_tempo > '".date("Y-m-d")."' ";
        }
        
        $sql = "
            select p.*, r.nama, r.alamat, DATEDIFF(p.jatuh_tempo, '".date("Y-m-d")."') as tenggat, p.total
            from penerimaan p
            join detail_penerimaan td on (p.id = td.id_penerimaan)
            join supplier r on (r.id = p.id_supplier)
            left join pemesanan ps on (p.id_pemesanan = ps.id)
            where p.status = 'Tempo' $q
        ";
        //echo "<pre>".$sql."</pre>";
        return $this->db->query($sql);
    }
    
    function inkaso_load_data($param) {
        $q = null;
        /*if ($awal != null and $akhir != null) {
            $q.="and p.dokumen_tanggal between '".  datetime2mysql($awal)."' and '".datetime2mysql($akhir)."'";
        }*/
        if ($param['id_supplier'] !== '') {
            $q.=" and p.suplier_relasi_instansi_id = '".$param['id_supplier']."'";
        }
        if ($param['awal'] !== '' and $param['akhir'] !== '') {
            $q.=" and date(i.waktu) between '".  date2mysql($param['awal'])."' and '".  date2mysql($param['akhir'])."'";
        }
        if ($param['faktur'] !== '') {
            $q.=" and p.dokumen_no = '".$param['faktur']."'";
        }
        $sql = "
            select i.*, p.id as no_pembelian, p.total, p.tanggal_jatuh_tempo as tempo, r.nama, p.dokumen_no
            from inkaso i
            join pembelian p on (i.pembelian_id = p.id)
            join relasi_instansi r on (p.suplier_relasi_instansi_id = r.id)
            where i.id is not NULL $q order by p.id, i.waktu
            asc
        ";
        //echo "<pre>".$sql."</pre>";
        return $this->db->query($sql);
    }
    
    function get_data_inkaso($id = null) {
        $q = NULL;
        if ($id != null) {
            $q.=" where id_penerimaan = '$id'";
        }
        $sql = "
            select sum(nominal) as inkaso from inkaso $q
        ";
        return $this->db->query($sql);
    }
    
    function pemakaian_save() {
        $this->db->trans_begin();
        $data_pemakaian = array(
            'waktu' => datetime2mysql(post_safe('tanggal')).":".date("s"),
            'pegawai_penduduk_id' => $this->session->userdata('id_user')
        );
        $id_pemakaian = post_safe('id_pemakaian');
        $id_pb = post_safe('id_pb');
        $jumlah = post_safe('jl');
        $ed = post_safe('exp');
        $id_kategori = post_safe('id_kategori');
        $subtotal = post_safe('subtotal');
        if ($id_pemakaian === '') {
            $this->db->insert('pemakaian', $data_pemakaian);
            $id_pemakaian = $this->db->insert_id();

            foreach ($id_pb as $key => $data) {
                if ($data != '') {
                    $jml = $this->db->query("select * from transaksi_detail
                            WHERE barang_packing_id = '$data' 
                                and unit_id = '".$this->session->userdata('id_unit')."' and ed = '$ed[$key]'
                                and transaksi_jenis != 'Pemesanan' order by ed asc")->row();
                    $leadtime = $this->db->query("select leadtime_hours from transaksi_detail 
                        where unit_id = '".$this->session->userdata('id_unit')."' and barang_packing_id = '$data' and transaksi_jenis = 'Pembelian'
                            order by id desc limit 1")->row();
    //                    $sisa = (isset($jml->sisa)?$jml->sisa:'0')-$jumlah[$key];
                        $data_trans = array(
                            'transaksi_id' => $id_pemakaian,
                            'transaksi_jenis' => 'Pemakaian',
                            'waktu' => $this->waktu,
                            'barang_packing_id' => $id_pb[$key],
                            'unit_id' => $this->session->userdata('id_unit'),
                            'ed' => $jml->ed,
                            'harga' => (isset($jml->harga)?$jml->harga:'0'),
                            'ppn' => (isset($jml->ppn)?$jml->ppn:'0'),
                            'hna' => (isset($jml->hna)?$jml->hna:'0'),
                            'hpp' => (isset($jml->hna)?$jml->hna:'0'),
                            'het' => (isset($jml->het)?$jml->het:'0'),
                            'masuk' => '0',
                            'keluar' => $jumlah[$key],
                            'leadtime_hours' => (isset($leadtime->loadtime_hours)?$leadtime->loadtime_hours:'0')
                        );
                        //$this->session->set_userdata(array('sisa_stok' => $sisa));
                        $this->db->insert('transaksi_detail', $data_trans);
                        if ($id_kategori[$key] === '1') {
                            $jurnal_218 = array(
                                'waktu' => date("Y-m-d H:i:s"),
                                'id_transaksi' => $id_pemakaian,
                                'jenis_transaksi' => 'Pemakaian',
                                'ket_transaksi' => '',
                                'id_sub_sub_sub_sub_rekening' => '218',
                                'debet' => currencyToNumber($subtotal[$key])
                            );
                            $this->db->insert('jurnal', $jurnal_218);
                            $jurnal_118 = array(
                                'waktu' => date("Y-m-d H:i:s"),
                                'id_transaksi' => $id_pemakaian,
                                'jenis_transaksi' => 'Pemakaian',
                                'ket_transaksi' => '',
                                'id_sub_sub_sub_sub_rekening' => '118',
                                'kredit' => currencyToNumber($subtotal[$key])
                            );
                            $this->db->insert('jurnal', $jurnal_118);
                        }
                        if ($id_kategori[$key] === '2') {
                            $jurnal_130 = array(
                                'waktu' => date("Y-m-d H:i:s"),
                                'id_transaksi' => $id_pemakaian,
                                'jenis_transaksi' => 'Pemakaian',
                                'ket_transaksi' => '',
                                'id_sub_sub_sub_sub_rekening' => '130',
                                'debet' => currencyToNumber($subtotal[$key])
                            );
                            $this->db->insert('jurnal', $jurnal_130);
                            $jurnal_34 = array(
                                'waktu' => date("Y-m-d H:i:s"),
                                'id_transaksi' => $id_pemakaian,
                                'jenis_transaksi' => 'Pemakaian',
                                'ket_transaksi' => '',
                                'id_sub_sub_sub_sub_rekening' => '34',
                                'kredit' => currencyToNumber($subtotal[$key])
                            );
                            $this->db->insert('jurnal', $jurnal_34);
                        }
                        if ($id_kategori[$key] === '3') {
                            $jurnal_130 = array(
                                'waktu' => date("Y-m-d H:i:s"),
                                'id_transaksi' => $id_pemakaian,
                                'jenis_transaksi' => 'Pemakaian',
                                'ket_transaksi' => '',
                                'id_sub_sub_sub_sub_rekening' => '230',
                                'debet' => currencyToNumber($subtotal[$key])
                            );
                            $this->db->insert('jurnal', $jurnal_130);
                            $jurnal_34 = array(
                                'waktu' => date("Y-m-d H:i:s"),
                                'id_transaksi' => $id_pemakaian,
                                'jenis_transaksi' => 'Pemakaian',
                                'ket_transaksi' => '',
                                'id_sub_sub_sub_sub_rekening' => '200',
                                'kredit' => currencyToNumber($subtotal[$key])
                            );
                            $this->db->insert('jurnal', $jurnal_34);
                        }
                        if ($id_kategori[$key] === '4') {
                            $jurnal_130 = array(
                                'waktu' => date("Y-m-d H:i:s"),
                                'id_transaksi' => $id_pemakaian,
                                'jenis_transaksi' => 'Pemakaian',
                                'ket_transaksi' => '',
                                'id_sub_sub_sub_sub_rekening' => '132',
                                'debet' => currencyToNumber($subtotal[$key])
                            );
                            $this->db->insert('jurnal', $jurnal_130);
                            $jurnal_34 = array(
                                'waktu' => date("Y-m-d H:i:s"),
                                'id_transaksi' => $id_pemakaian,
                                'jenis_transaksi' => 'Pemakaian',
                                'ket_transaksi' => '',
                                'id_sub_sub_sub_sub_rekening' => '199',
                                'kredit' => currencyToNumber($subtotal[$key])
                            );
                            $this->db->insert('jurnal', $jurnal_34);
                        }
                        if ($id_kategori[$key] === '7') {
                            $jurnal_130 = array(
                                'waktu' => date("Y-m-d H:i:s"),
                                'id_transaksi' => $id_pemakaian,
                                'jenis_transaksi' => 'Pemakaian',
                                'ket_transaksi' => '',
                                'id_sub_sub_sub_sub_rekening' => '229',
                                'debet' => currencyToNumber($subtotal[$key])
                            );
                            $this->db->insert('jurnal', $jurnal_130);
                            $jurnal_34 = array(
                                'waktu' => date("Y-m-d H:i:s"),
                                'id_transaksi' => $id_pemakaian,
                                'jenis_transaksi' => 'Pemakaian',
                                'ket_transaksi' => '',
                                'id_sub_sub_sub_sub_rekening' => '198',
                                'kredit' => currencyToNumber($subtotal[$key])
                            );
                            $this->db->insert('jurnal', $jurnal_34);
                        }
                        if ($id_kategori[$key] === '9') {
                            $jurnal = array(
                                'waktu' => date("Y-m-d H:i:s"),
                                'id_transaksi' => $id_pemakaian,
                                'jenis_transaksi' => 'Pemakaian',
                                'ket_transaksi' => '',
                                'id_sub_sub_sub_sub_rekening' => '219',
                                'debet' => currencyToNumber($subtotal[$key])
                            );
                            $this->db->insert('jurnal', $jurnal);
                            $jurnal_189 = array(
                                'waktu' => date("Y-m-d H:i:s"),
                                'id_transaksi' => $id_pemakaian,
                                'jenis_transaksi' => 'Pemakaian',
                                'ket_transaksi' => '',
                                'id_sub_sub_sub_sub_rekening' => '189',
                                'kredit' => currencyToNumber($subtotal[$key])
                            );
                            $this->db->insert('jurnal', $jurnal_189);
                        }
                        if ($id_kategori[$key] === '10') {
                            $jurnal = array(
                                'waktu' => date("Y-m-d H:i:s"),
                                'id_transaksi' => $id_pemakaian,
                                'jenis_transaksi' => 'Pemakaian',
                                'ket_transaksi' => '',
                                'id_sub_sub_sub_sub_rekening' => '221',
                                'debet' => currencyToNumber($subtotal[$key])
                            );
                            $this->db->insert('jurnal', $jurnal);
                            $jurnal_191 = array(
                                'waktu' => date("Y-m-d H:i:s"),
                                'id_transaksi' => $id_pemakaian,
                                'jenis_transaksi' => 'Pemakaian',
                                'ket_transaksi' => '',
                                'id_sub_sub_sub_sub_rekening' => '191',
                                'kredit' => currencyToNumber($subtotal[$key])
                            );
                            $this->db->insert('jurnal', $jurnal_191);
                        }
                        if ($id_kategori[$key] === '11') {
                            $jurnal = array(
                                'waktu' => date("Y-m-d H:i:s"),
                                'id_transaksi' => $id_pemakaian,
                                'jenis_transaksi' => 'Pemakaian',
                                'ket_transaksi' => '',
                                'id_sub_sub_sub_sub_rekening' => '222',
                                'debet' => currencyToNumber($subtotal[$key])
                            );
                            $this->db->insert('jurnal', $jurnal);
                            $jurnal_192 = array(
                                'waktu' => date("Y-m-d H:i:s"),
                                'id_transaksi' => $id_pemakaian,
                                'jenis_transaksi' => 'Pemakaian',
                                'ket_transaksi' => '',
                                'id_sub_sub_sub_sub_rekening' => '192',
                                'kredit' => currencyToNumber($subtotal[$key])
                            );
                            $this->db->insert('jurnal', $jurnal_192);
                        }
                        if ($id_kategori[$key] === '12') {
                            $jurnal = array(
                                'waktu' => date("Y-m-d H:i:s"),
                                'id_transaksi' => $id_pemakaian,
                                'jenis_transaksi' => 'Pemakaian',
                                'ket_transaksi' => '',
                                'id_sub_sub_sub_sub_rekening' => '223',
                                'debet' => currencyToNumber($subtotal[$key])
                            );
                            $this->db->insert('jurnal', $jurnal);
                            $jurnal_193 = array(
                                'waktu' => date("Y-m-d H:i:s"),
                                'id_transaksi' => $id_pemakaian,
                                'jenis_transaksi' => 'Pemakaian',
                                'ket_transaksi' => '',
                                'id_sub_sub_sub_sub_rekening' => '193',
                                'kredit' => currencyToNumber($subtotal[$key])
                            );
                            $this->db->insert('jurnal', $jurnal_193);
                        }
                        if ($id_kategori[$key] === '13') {
                            $jurnal = array(
                                'waktu' => date("Y-m-d H:i:s"),
                                'id_transaksi' => $id_pemakaian,
                                'jenis_transaksi' => 'Pemakaian',
                                'ket_transaksi' => '',
                                'id_sub_sub_sub_sub_rekening' => '224',
                                'debet' => currencyToNumber($subtotal[$key])
                            );
                            $this->db->insert('jurnal', $jurnal);
                            $jurnal_194 = array(
                                'waktu' => date("Y-m-d H:i:s"),
                                'id_transaksi' => $id_pemakaian,
                                'jenis_transaksi' => 'Pemakaian',
                                'ket_transaksi' => '',
                                'id_sub_sub_sub_sub_rekening' => '194',
                                'kredit' => currencyToNumber($subtotal[$key])
                            );
                            $this->db->insert('jurnal', $jurnal_194);
                        }
                        if ($id_kategori[$key] === '14') {
                            $jurnal = array(
                                'waktu' => date("Y-m-d H:i:s"),
                                'id_transaksi' => $id_pemakaian,
                                'jenis_transaksi' => 'Pemakaian',
                                'ket_transaksi' => '',
                                'id_sub_sub_sub_sub_rekening' => '225',
                                'debet' => currencyToNumber($subtotal[$key])
                            );
                            $this->db->insert('jurnal', $jurnal);
                            $jurnal_206 = array(
                                'waktu' => date("Y-m-d H:i:s"),
                                'id_transaksi' => $id_pemakaian,
                                'jenis_transaksi' => 'Pemakaian',
                                'ket_transaksi' => '',
                                'id_sub_sub_sub_sub_rekening' => '206',
                                'kredit' => currencyToNumber($subtotal[$key])
                            );
                            $this->db->insert('jurnal', $jurnal_206);
                        }
                        if ($id_kategori[$key] === '15') {
                            $jurnal_130 = array(
                                'waktu' => date("Y-m-d H:i:s"),
                                'id_transaksi' => $id_pemakaian,
                                'jenis_transaksi' => 'Pemakaian',
                                'ket_transaksi' => '',
                                'id_sub_sub_sub_sub_rekening' => '131',
                                'debet' => currencyToNumber($subtotal[$key])
                            );
                            $this->db->insert('jurnal', $jurnal_130);
                            $jurnal_34 = array(
                                'waktu' => date("Y-m-d H:i:s"),
                                'id_transaksi' => $id_pemakaian,
                                'jenis_transaksi' => 'Pemakaian',
                                'ket_transaksi' => '',
                                'id_sub_sub_sub_sub_rekening' => '51',
                                'kredit' => currencyToNumber($subtotal[$key])
                            );
                            $this->db->insert('jurnal', $jurnal_34);
                        }
                        if ($id_kategori[$key] === '16') {
                            $jurnal_130 = array(
                                'waktu' => date("Y-m-d H:i:s"),
                                'id_transaksi' => $id_pemakaian,
                                'jenis_transaksi' => 'Pemakaian',
                                'ket_transaksi' => '',
                                'id_sub_sub_sub_sub_rekening' => '226',
                                'debet' => currencyToNumber($subtotal[$key])
                            );
                            $this->db->insert('jurnal', $jurnal_130);
                            $jurnal_34 = array(
                                'waktu' => date("Y-m-d H:i:s"),
                                'id_transaksi' => $id_pemakaian,
                                'jenis_transaksi' => 'Pemakaian',
                                'ket_transaksi' => '',
                                'id_sub_sub_sub_sub_rekening' => '195',
                                'kredit' => currencyToNumber($subtotal[$key])
                            );
                            $this->db->insert('jurnal', $jurnal_34);
                        }
                        if ($id_kategori[$key] === '18') {
                            $jurnal_130 = array(
                                'waktu' => date("Y-m-d H:i:s"),
                                'id_transaksi' => $id_pemakaian,
                                'jenis_transaksi' => 'Pemakaian',
                                'ket_transaksi' => '',
                                'id_sub_sub_sub_sub_rekening' => '228',
                                'debet' => currencyToNumber($subtotal[$key])
                            );
                            $this->db->insert('jurnal', $jurnal_130);
                            $jurnal_34 = array(
                                'waktu' => date("Y-m-d H:i:s"),
                                'id_transaksi' => $id_pemakaian,
                                'jenis_transaksi' => 'Pemakaian',
                                'ket_transaksi' => '',
                                'id_sub_sub_sub_sub_rekening' => '197',
                                'kredit' => currencyToNumber($subtotal[$key])
                            );
                            $this->db->insert('jurnal', $jurnal_34);
                        }
                }   

            }
            $result['action'] = 'add';
        } else { // jika pemakaian edit data
            $this->db->where('id', $id_pemakaian);
            $this->db->update('pemakaian', $data_pemakaian);
            $this->db->delete('transaksi_detail', array('transaksi_jenis' => 'Pemakaian', 'transaksi_id' => $id_pemakaian));
            $this->db->delete('jurnal', array('id_transaksi' => $id_pemakaian, 'jenis_transaksi' => 'Pemakaian'));
            $id_pemakaian = $id_pemakaian;

            foreach ($id_pb as $key => $data) {
                if ($data != '') {
                    $jml = $this->db->query("select * from transaksi_detail
                            WHERE barang_packing_id = '$data' 
                                and unit_id = '".$this->session->userdata('id_unit')."' and ed = '$ed[$key]'
                                and transaksi_jenis != 'Pemesanan' order by ed asc")->row();
                    $leadtime = $this->db->query("select leadtime_hours from transaksi_detail 
                        where unit_id = '".$this->session->userdata('id_unit')."' and barang_packing_id = '$data' and transaksi_jenis = 'Pembelian'
                            order by id desc limit 1")->row();
    //                    $sisa = (isset($jml->sisa)?$jml->sisa:'0')-$jumlah[$key];
                        $data_trans = array(
                            'transaksi_id' => $id_pemakaian,
                            'transaksi_jenis' => 'Pemakaian',
                            'waktu' => $this->waktu,
                            'barang_packing_id' => $id_pb[$key],
                            'unit_id' => $this->session->userdata('id_unit'),
                            'ed' => $jml->ed,
                            'harga' => (isset($jml->harga)?$jml->harga:'0'),
                            'ppn' => (isset($jml->ppn)?$jml->ppn:'0'),
                            'hna' => (isset($jml->hna)?$jml->hna:'0'),
                            'hpp' => (isset($jml->hna)?$jml->hna:'0'),
                            'het' => (isset($jml->het)?$jml->het:'0'),
                            'masuk' => '0',
                            'keluar' => $jumlah[$key],
                            'leadtime_hours' => (isset($leadtime->loadtime_hours)?$leadtime->loadtime_hours:'0')
                        );
                        //$this->session->set_userdata(array('sisa_stok' => $sisa));
                        $this->db->insert('transaksi_detail', $data_trans);
                        if ($id_kategori[$key] === '1') {
                            $jurnal_218 = array(
                                'waktu' => date("Y-m-d H:i:s"),
                                'id_transaksi' => $id_pemakaian,
                                'jenis_transaksi' => 'Pemakaian',
                                'ket_transaksi' => '',
                                'id_sub_sub_sub_sub_rekening' => '218',
                                'debet' => currencyToNumber($subtotal[$key])
                            );
                            $this->db->insert('jurnal', $jurnal_218);
                            $jurnal_118 = array(
                                'waktu' => date("Y-m-d H:i:s"),
                                'id_transaksi' => $id_pemakaian,
                                'jenis_transaksi' => 'Pemakaian',
                                'ket_transaksi' => '',
                                'id_sub_sub_sub_sub_rekening' => '118',
                                'kredit' => currencyToNumber($subtotal[$key])
                            );
                            $this->db->insert('jurnal', $jurnal_118);
                        }
                        if ($id_kategori[$key] === '2') {
                            $jurnal_130 = array(
                                'waktu' => date("Y-m-d H:i:s"),
                                'id_transaksi' => $id_pemakaian,
                                'jenis_transaksi' => 'Pemakaian',
                                'ket_transaksi' => '',
                                'id_sub_sub_sub_sub_rekening' => '130',
                                'debet' => currencyToNumber($subtotal[$key])
                            );
                            $this->db->insert('jurnal', $jurnal_130);
                            $jurnal_34 = array(
                                'waktu' => date("Y-m-d H:i:s"),
                                'id_transaksi' => $id_pemakaian,
                                'jenis_transaksi' => 'Pemakaian',
                                'ket_transaksi' => '',
                                'id_sub_sub_sub_sub_rekening' => '34',
                                'kredit' => currencyToNumber($subtotal[$key])
                            );
                            $this->db->insert('jurnal', $jurnal_34);
                        }
                        if ($id_kategori[$key] === '3') {
                            $jurnal_130 = array(
                                'waktu' => date("Y-m-d H:i:s"),
                                'id_transaksi' => $id_pemakaian,
                                'jenis_transaksi' => 'Pemakaian',
                                'ket_transaksi' => '',
                                'id_sub_sub_sub_sub_rekening' => '230',
                                'debet' => currencyToNumber($subtotal[$key])
                            );
                            $this->db->insert('jurnal', $jurnal_130);
                            $jurnal_34 = array(
                                'waktu' => date("Y-m-d H:i:s"),
                                'id_transaksi' => $id_pemakaian,
                                'jenis_transaksi' => 'Pemakaian',
                                'ket_transaksi' => '',
                                'id_sub_sub_sub_sub_rekening' => '200',
                                'kredit' => currencyToNumber($subtotal[$key])
                            );
                            $this->db->insert('jurnal', $jurnal_34);
                        }
                        if ($id_kategori[$key] === '4') {
                            $jurnal_130 = array(
                                'waktu' => date("Y-m-d H:i:s"),
                                'id_transaksi' => $id_pemakaian,
                                'jenis_transaksi' => 'Pemakaian',
                                'ket_transaksi' => '',
                                'id_sub_sub_sub_sub_rekening' => '132',
                                'debet' => currencyToNumber($subtotal[$key])
                            );
                            $this->db->insert('jurnal', $jurnal_130);
                            $jurnal_34 = array(
                                'waktu' => date("Y-m-d H:i:s"),
                                'id_transaksi' => $id_pemakaian,
                                'jenis_transaksi' => 'Pemakaian',
                                'ket_transaksi' => '',
                                'id_sub_sub_sub_sub_rekening' => '199',
                                'kredit' => currencyToNumber($subtotal[$key])
                            );
                            $this->db->insert('jurnal', $jurnal_34);
                        }
                        if ($id_kategori[$key] === '7') {
                            $jurnal_130 = array(
                                'waktu' => date("Y-m-d H:i:s"),
                                'id_transaksi' => $id_pemakaian,
                                'jenis_transaksi' => 'Pemakaian',
                                'ket_transaksi' => '',
                                'id_sub_sub_sub_sub_rekening' => '229',
                                'debet' => currencyToNumber($subtotal[$key])
                            );
                            $this->db->insert('jurnal', $jurnal_130);
                            $jurnal_34 = array(
                                'waktu' => date("Y-m-d H:i:s"),
                                'id_transaksi' => $id_pemakaian,
                                'jenis_transaksi' => 'Pemakaian',
                                'ket_transaksi' => '',
                                'id_sub_sub_sub_sub_rekening' => '198',
                                'kredit' => currencyToNumber($subtotal[$key])
                            );
                            $this->db->insert('jurnal', $jurnal_34);
                        }
                        if ($id_kategori[$key] === '9') {
                            $jurnal = array(
                                'waktu' => date("Y-m-d H:i:s"),
                                'id_transaksi' => $id_pemakaian,
                                'jenis_transaksi' => 'Pemakaian',
                                'ket_transaksi' => '',
                                'id_sub_sub_sub_sub_rekening' => '219',
                                'debet' => currencyToNumber($subtotal[$key])
                            );
                            $this->db->insert('jurnal', $jurnal);
                            $jurnal_189 = array(
                                'waktu' => date("Y-m-d H:i:s"),
                                'id_transaksi' => $id_pemakaian,
                                'jenis_transaksi' => 'Pemakaian',
                                'ket_transaksi' => '',
                                'id_sub_sub_sub_sub_rekening' => '189',
                                'kredit' => currencyToNumber($subtotal[$key])
                            );
                            $this->db->insert('jurnal', $jurnal_189);
                        }
                        if ($id_kategori[$key] === '10') {
                            $jurnal = array(
                                'waktu' => date("Y-m-d H:i:s"),
                                'id_transaksi' => $id_pemakaian,
                                'jenis_transaksi' => 'Pemakaian',
                                'ket_transaksi' => '',
                                'id_sub_sub_sub_sub_rekening' => '221',
                                'debet' => currencyToNumber($subtotal[$key])
                            );
                            $this->db->insert('jurnal', $jurnal);
                            $jurnal_191 = array(
                                'waktu' => date("Y-m-d H:i:s"),
                                'id_transaksi' => $id_pemakaian,
                                'jenis_transaksi' => 'Pemakaian',
                                'ket_transaksi' => '',
                                'id_sub_sub_sub_sub_rekening' => '191',
                                'kredit' => currencyToNumber($subtotal[$key])
                            );
                            $this->db->insert('jurnal', $jurnal_191);
                        }
                        if ($id_kategori[$key] === '11') {
                            $jurnal = array(
                                'waktu' => date("Y-m-d H:i:s"),
                                'id_transaksi' => $id_pemakaian,
                                'jenis_transaksi' => 'Pemakaian',
                                'ket_transaksi' => '',
                                'id_sub_sub_sub_sub_rekening' => '222',
                                'debet' => currencyToNumber($subtotal[$key])
                            );
                            $this->db->insert('jurnal', $jurnal);
                            $jurnal_192 = array(
                                'waktu' => date("Y-m-d H:i:s"),
                                'id_transaksi' => $id_pemakaian,
                                'jenis_transaksi' => 'Pemakaian',
                                'ket_transaksi' => '',
                                'id_sub_sub_sub_sub_rekening' => '192',
                                'kredit' => currencyToNumber($subtotal[$key])
                            );
                            $this->db->insert('jurnal', $jurnal_192);
                        }
                        if ($id_kategori[$key] === '12') {
                            $jurnal = array(
                                'waktu' => date("Y-m-d H:i:s"),
                                'id_transaksi' => $id_pemakaian,
                                'jenis_transaksi' => 'Pemakaian',
                                'ket_transaksi' => '',
                                'id_sub_sub_sub_sub_rekening' => '223',
                                'debet' => currencyToNumber($subtotal[$key])
                            );
                            $this->db->insert('jurnal', $jurnal);
                            $jurnal_193 = array(
                                'waktu' => date("Y-m-d H:i:s"),
                                'id_transaksi' => $id_pemakaian,
                                'jenis_transaksi' => 'Pemakaian',
                                'ket_transaksi' => '',
                                'id_sub_sub_sub_sub_rekening' => '193',
                                'kredit' => currencyToNumber($subtotal[$key])
                            );
                            $this->db->insert('jurnal', $jurnal_193);
                        }
                        if ($id_kategori[$key] === '13') {
                            $jurnal = array(
                                'waktu' => date("Y-m-d H:i:s"),
                                'id_transaksi' => $id_pemakaian,
                                'jenis_transaksi' => 'Pemakaian',
                                'ket_transaksi' => '',
                                'id_sub_sub_sub_sub_rekening' => '224',
                                'debet' => currencyToNumber($subtotal[$key])
                            );
                            $this->db->insert('jurnal', $jurnal);
                            $jurnal_194 = array(
                                'waktu' => date("Y-m-d H:i:s"),
                                'id_transaksi' => $id_pemakaian,
                                'jenis_transaksi' => 'Pemakaian',
                                'ket_transaksi' => '',
                                'id_sub_sub_sub_sub_rekening' => '194',
                                'kredit' => currencyToNumber($subtotal[$key])
                            );
                            $this->db->insert('jurnal', $jurnal_194);
                        }
                        if ($id_kategori[$key] === '14') {
                            $jurnal = array(
                                'waktu' => date("Y-m-d H:i:s"),
                                'id_transaksi' => $id_pemakaian,
                                'jenis_transaksi' => 'Pemakaian',
                                'ket_transaksi' => '',
                                'id_sub_sub_sub_sub_rekening' => '225',
                                'debet' => currencyToNumber($subtotal[$key])
                            );
                            $this->db->insert('jurnal', $jurnal);
                            $jurnal_206 = array(
                                'waktu' => date("Y-m-d H:i:s"),
                                'id_transaksi' => $id_pemakaian,
                                'jenis_transaksi' => 'Pemakaian',
                                'ket_transaksi' => '',
                                'id_sub_sub_sub_sub_rekening' => '206',
                                'kredit' => currencyToNumber($subtotal[$key])
                            );
                            $this->db->insert('jurnal', $jurnal_206);
                        }
                        if ($id_kategori[$key] === '15') {
                            $jurnal_130 = array(
                                'waktu' => date("Y-m-d H:i:s"),
                                'id_transaksi' => $id_pemakaian,
                                'jenis_transaksi' => 'Pemakaian',
                                'ket_transaksi' => '',
                                'id_sub_sub_sub_sub_rekening' => '131',
                                'debet' => currencyToNumber($subtotal[$key])
                            );
                            $this->db->insert('jurnal', $jurnal_130);
                            $jurnal_34 = array(
                                'waktu' => date("Y-m-d H:i:s"),
                                'id_transaksi' => $id_pemakaian,
                                'jenis_transaksi' => 'Pemakaian',
                                'ket_transaksi' => '',
                                'id_sub_sub_sub_sub_rekening' => '51',
                                'kredit' => currencyToNumber($subtotal[$key])
                            );
                            $this->db->insert('jurnal', $jurnal_34);
                        }
                        if ($id_kategori[$key] === '16') {
                            $jurnal_130 = array(
                                'waktu' => date("Y-m-d H:i:s"),
                                'id_transaksi' => $id_pemakaian,
                                'jenis_transaksi' => 'Pemakaian',
                                'ket_transaksi' => '',
                                'id_sub_sub_sub_sub_rekening' => '226',
                                'debet' => currencyToNumber($subtotal[$key])
                            );
                            $this->db->insert('jurnal', $jurnal_130);
                            $jurnal_34 = array(
                                'waktu' => date("Y-m-d H:i:s"),
                                'id_transaksi' => $id_pemakaian,
                                'jenis_transaksi' => 'Pemakaian',
                                'ket_transaksi' => '',
                                'id_sub_sub_sub_sub_rekening' => '195',
                                'kredit' => currencyToNumber($subtotal[$key])
                            );
                            $this->db->insert('jurnal', $jurnal_34);
                        }
                        if ($id_kategori[$key] === '18') {
                            $jurnal_130 = array(
                                'waktu' => date("Y-m-d H:i:s"),
                                'id_transaksi' => $id_pemakaian,
                                'jenis_transaksi' => 'Pemakaian',
                                'ket_transaksi' => '',
                                'id_sub_sub_sub_sub_rekening' => '228',
                                'debet' => currencyToNumber($subtotal[$key])
                            );
                            $this->db->insert('jurnal', $jurnal_130);
                            $jurnal_34 = array(
                                'waktu' => date("Y-m-d H:i:s"),
                                'id_transaksi' => $id_pemakaian,
                                'jenis_transaksi' => 'Pemakaian',
                                'ket_transaksi' => '',
                                'id_sub_sub_sub_sub_rekening' => '197',
                                'kredit' => currencyToNumber($subtotal[$key])
                            );
                            $this->db->insert('jurnal', $jurnal_34);
                        }
                }   

            }
            $result['action'] = 'edit';
        }
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $status = FALSE;
        } else {
            $this->db->trans_commit();
            $status = TRUE;
        }
        $result['status'] = $status;
        $result['id_pemakaian'] = $id_pemakaian;
        return $result;
    }
    
    function pemakaian_delete($id) {
        $cek = $this->cek_delete($id, 'Pemakaian');
        if ($cek == true) {
            $this->db->trans_begin();
            //$cek = $this->db->get_where('pemakaian', array('id' => $id))->num_rows();

            $this->db->delete('pemakaian', array('id' => $id));
            $this->db->delete('transaksi_detail', array('transaksi_id' => $id, 'transaksi_jenis' => 'Pemakaian'));
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $status = FALSE;
            } else {
                $this->db->trans_commit();
                $status = TRUE;
            }
        } else {
            $status = FALSE;
        }
        $result['status'] = $status;
        return $result;
    }
    
    function stok_opname_delete($id) {
        $this->cek_delete($id, 'Stok Opname');
        $this->db->trans_begin();
        //$cek = $this->db->get_where('pemakaian', array('id' => $id))->num_rows();
        
        $this->db->delete('opname_stok', array('id' => $id));
        $this->db->delete('transaksi_detail', array('transaksi_id' => $id, 'transaksi_jenis' => 'Stok Opname'));
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
    
    function reretur_pembelian_delete($id) {
        $cek= $this->cek_delete($id, 'Penerimaan Retur Pembelian');
        if ($cek == true) {
            $this->db->trans_begin();
            //$cek = $this->db->get_where('pemakaian', array('id' => $id))->num_rows();

            $this->db->delete('pembelian_retur_penerimaan', array('id' => $id));
            $this->db->delete('transaksi_detail', array('transaksi_id' => $id, 'transaksi_jenis' => 'Penerimaan Retur Pembelian'));
            $this->db->delete('jurnal', array('id_transaksi' => $id, 'jenis_transaksi' => 'Penerimaan Retur Pembelian'));

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $status = FALSE;
            } else {
                $this->db->trans_commit();
                $status = TRUE;
            }
        } else {
            $status = FALSE;
        }
        $result['status'] = $status;
        return $result;
    }
    
    function retur_pembelian_delete($id) {
        $cek = $this->cek_delete($id, 'Retur Pembelian');
        if ($cek == true) {
            $this->db->trans_begin();
            //$cek = $this->db->get_where('pemakaian', array('id' => $id))->num_rows();

            $this->db->delete('pembelian_retur', array('id' => $id));
            $this->db->delete('transaksi_detail', array('transaksi_id' => $id, 'transaksi_jenis' => 'Retur Pembelian'));
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $status = FALSE;
            } else {
                $this->db->trans_commit();
                $status = TRUE;
            }
        } else {
            $status = FALSE;
        }
        $result['status'] = $status;
        return $result;
    }
    
    function pembelian_delete($id) {
        $cek = $this->cek_delete($id, 'Pembelian');
        if ($cek == true) {
            $this->db->trans_begin();
            //$cek = $this->db->get_where('pembelian', array('id' => $id))->num_rows();
            $row = $this->db->query("select id from pembelian_retur where pembelian_id = '$id'")->row();
            if (isset($row->id)) {
                $rows= $this->db->query("select id from pembelian_retur_penerimaan where retur_id = '".$row->id."'")->row();
            }
            $this->db->delete('transaksi_detail', array('transaksi_id' => $id, 'transaksi_jenis' => 'Pembelian'));
            if (isset($row->id)) {
                $this->db->delete('transaksi_detail', array('transaksi_id' => $row->id, 'transaksi_jenis' => 'Retur Pembelian'));
            }
            if (isset($rows->id)) {
                $this->db->delete('transaksi_detail', array('transaksi_id' => $rows->id, 'transaksi_jenis' => 'Penerimaan Retur Pembelian'));
            }
            $this->db->delete('pembelian', array('id' => $id));
            $this->db->delete('jurnal', array('id_transaksi' => $id, 'jenis_transaksi' => 'Pembelian'));
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $status = FALSE;
            } else {
                $this->db->trans_commit();
                $status = TRUE;
            }
        } else {
            $status = FALSE;
        }
        $result['status'] = $status;
        return $result;
    }
    
    function distribusi_delete($id) {
        $cek = $this->cek_delete($id, 'Distribusi');
        if ($cek == true) {
            $this->db->trans_begin();
            //$cek = $this->db->get_where('pembelian', array('id' => $id))->num_rows();
            $row = $this->db->query("select id from distribusi_penerimaan where distribusi_id = '$id'")->row();
            if (isset($row->id)) {
                $rows= $this->db->query("select id from distribusi_retur where penerimaan_distribusi_id = '".$row->id."'")->row();
            }
            if (isset($rows->id)) {
                $rowA= $this->db->query("select id from distribusi_retur_penerimaan where distribusi_retur_id = '".$rows->id."'")->row();
            }
            $this->db->delete('transaksi_detail', array('transaksi_id' => $id, 'transaksi_jenis' => 'Distribusi'));
            if (isset($row->id)) {
                $this->db->delete('transaksi_detail', array('transaksi_id' => $row->id, 'transaksi_jenis' => 'Penerimaan Distribusi'));
            }
            if (isset($rows->id)) {
                $this->db->delete('transaksi_detail', array('transaksi_id' => $rows->id, 'transaksi_jenis' => 'Retur Distribusi'));
            }
            if (isset($rowA)) {
                $this->db->delete('transaksi_detail', array('transaksi_id' => $rowA->id, 'transaksi_jenis' => 'Penerimaan Retur Distribusi'));
            }
            $this->db->delete('distribusi', array('id' => $id));
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $status = FALSE;
            } else {
                $this->db->trans_commit();
                $status = TRUE;
            }
        } else {
            $status = TRUE;
        }
        $result['status'] = $status;
        return $result;
    }
    
    function distribusiGetSisa($id_pb, $ed) {
        $sql = "select * from transaksi_detail where barang_packing_id = '$id_pb' and ed = '".  date2mysql($ed)."' and unit_id = '".$this->session->userdata('id_unit')."' order by waktu desc limit 1";
        return $this->db->query($sql);
    }
    
    function distribusi_save() {
        $this->db->trans_begin();
        $id = post_safe('id_distribusi');
        
        $data_distribusi = array(
            'unit_id' => $this->session->userdata('id_unit'),
            'tujuan_unit_id' => post_safe('unit'),
            'pegawai_penduduk_id' => $this->session->userdata('id_user')
        );
        $pb = post_safe('pb');
        $id_pb = post_safe('id_pb');
        $ed = post_safe('ed');
        $jl = post_safe('jl');
        if (empty($id)) {
            $this->db->insert('distribusi', $data_distribusi);
            $id_distribusi = $this->db->insert_id();

            foreach ($pb as $key => $data) {
                if ($data != '') {
                    $jml = $this->db->query("select * from transaksi_detail where transaksi_jenis != 'Pemesanan' 
                    and barang_packing_id = '$id_pb[$key]' and ed = '".date2mysql($ed[$key])."' and unit_id = '".$this->session->userdata('id_unit')."' order by waktu desc limit 1")->row();
                    //$sisa = (isset($jml->sisa)?$jml->sisa:'0') - $jl[$key];
                    $data_trans = array(
                        'transaksi_id' => $id_distribusi,
                        'transaksi_jenis' => 'Distribusi',
                        'waktu' => datetime2mysql(post_safe('tanggal')).":".date("s"),
                        'barang_packing_id' => $id_pb[$key],
                        'unit_id' => $this->session->userdata('id_unit'),
                        'ed' => date2mysql($ed[$key]),
                        'harga' => (isset($jml->harga)?$jml->harga:'0'),
                        'ppn' => (isset($jml->ppn)?$jml->ppn:'0'),
                        'hna' => (isset($jml->hna)?$jml->hna:'0'),
                        'hpp' => (isset($jml->hpp)?$jml->hpp:'0'),
                        'het' => (isset($jml->het)?$jml->het:'0'),
                        'masuk' => '0',
                        'keluar' => $jl[$key],
                    );
                    $this->db->insert('transaksi_detail', $data_trans);
                }
            }
            $result['action'] = 'add';
        } else {
            $this->db->where('id', $id);
            $this->db->update('distribusi', $data_distribusi);
            $this->db->delete('transaksi_detail', array('transaksi_id' => $id, 'transaksi_jenis' => 'Distribusi'));
            $id_distribusi = $id;

            foreach ($pb as $key => $data) {
                if ($data != '') {
                    $jml = $this->db->query("select * from transaksi_detail where transaksi_jenis != 'Pemesanan' 
                    and barang_packing_id = '$id_pb[$key]' and ed = '".$ed[$key]."' and unit_id = '".$this->session->userdata('id_unit')."' order by waktu desc limit 1")->row();
                    //$sisa = (isset($jml->sisa)?$jml->sisa:'0') - $jl[$key];
                    $data_trans = array(
                        'transaksi_id' => $id_distribusi,
                        'transaksi_jenis' => 'Distribusi',
                        'waktu' => datetime2mysql(post_safe('tanggal')).":".date("s"),
                        'barang_packing_id' => $id_pb[$key],
                        'unit_id' => $this->session->userdata('id_unit'),
                        'ed' => $ed[$key],
                        'harga' => (isset($jml->harga)?$jml->harga:'0'),
                        'ppn' => (isset($jml->ppn)?$jml->ppn:'0'),
                        'hna' => (isset($jml->hna)?$jml->hna:'0'),
                        'hpp' => (isset($jml->hpp)?$jml->hpp:'0'),
                        'het' => (isset($jml->het)?$jml->het:'0'),
                        'masuk' => '0',
                        'keluar' => $jl[$key],
                    );
                    $this->db->insert('transaksi_detail', $data_trans);
                }
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                }
            }
        $result['action'] = 'edit';
        }
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $status = FALSE;
        } else {
            $this->db->trans_commit();
            $status = TRUE;
        }
        $result['status'] = $status;
        $result['id_distribusi'] = $id_distribusi;
        return $result;
    }
    
    function inkaso_save() {
        $this->db->trans_begin();
        $data_inkaso = array(
            'waktu' => datetime2mysql(post_safe('tanggal')).":".date("s"),
            'pembelian_id' => post_safe('nopembelian'),
            'pegawai_penduduk_id' => $this->session->userdata('id_user'),
            'jumlah_bayar' => currencyToNumber(post_safe('bayar'))
        );
        $this->db->insert('inkaso', $data_inkaso);
        $id_inkaso = $this->db->insert_id();
        
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
        }
        
        /*$rows = $this->db->query("select * from kas order by waktu desc limit 1")->row();
        $data_kas = array(
            'waktu' => datetime2mysql(post_safe('tanggal')),
            'transaksi_id' => $id_inkaso,
            'transaksi_jenis' => 'Inkaso',
            'awal_saldo' => $rows->akhir_saldo,
            'penerimaan' => '0',
            'pengeluaran' => currencyToNumber(post_safe('bayar')),
            'akhir_saldo' => ($rows->akhir_saldo-currencyToNumber(post_safe('bayar')))
        );*/
        //$this->db->insert('kas', $data_kas);  // entrinya ada pada jurnal
        $jurnal_84 = array(
            'waktu' => date("Y-m-d H:i:s"),
            'id_transaksi' => $id_inkaso,
            'jenis_transaksi' => 'Inkaso',
            'ket_transaksi' => '',
            'id_sub_sub_sub_sub_rekening' => '84',
            'debet' => currencyToNumber(post_safe('bayar'))
        );
        $this->db->insert('jurnal', $jurnal_84);
        
        $jurnal_2 = array(
            'waktu' => date("Y-m-d H:i:s"),
            'id_transaksi' => $id_inkaso,
            'jenis_transaksi' => 'Inkaso',
            'ket_transaksi' => '',
            'id_sub_sub_sub_sub_rekening' => '1',
            'kredit' => currencyToNumber(post_safe('bayar'))
        );
        $this->db->insert('jurnal', $jurnal_2);
        
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $status = FALSE;
        } else {
            $this->db->trans_commit();
            $status = TRUE;
        }
        $result['status'] = $status;
        $result['id_pembelian'] = post_safe('nopembelian');
        return $result;
    }
    
    function dinamic_load_data($id_kemasan) {
        $sql = "select * from dinamic_harga_jual where id_kemasan = '$id_kemasan'";
        return $this->db->query($sql);
    }
    
    function pemusnahan_save() {
        $this->db->trans_begin();
        $id = post_safe('id_pemusnahan');
        $data_pemusnahan = array(
            'pegawai_penduduk_id' => $this->session->userdata('id_user'),
            'apotek_saksi_penduduk_id' => post_safe('id_sapt'),
            'bpom_saksi_penduduk_id' => post_safe('id_sbpom')
        );
        $ed = post_safe('ed');
        $pb = post_safe('pb');
        $id_pb = post_safe('id_pb');
        $jl = post_safe('jl');
        $kategori = post_safe('kategori');
        if ($id === '') {
            $this->db->insert('pemusnahan', $data_pemusnahan);
            $id_pemusnahan = $this->db->insert_id();
            
            foreach ($id_pb as $key => $data) {
                if ($data != '') {
                    $jml = $this->db->query("select * from transaksi_detail where transaksi_jenis != 'Pemesanan' and barang_packing_id = '$id_pb[$key]' 
                    and ed = '".  date2mysql($ed[$key])."' and unit_id = '".$this->session->userdata('id_unit')."' order by waktu desc limit 1")->row();
                    //$sisa = $jml->sisa - $jl[$key];
                    $data_trans = array(
                        'transaksi_id' => $id_pemusnahan,
                        'transaksi_jenis' => 'Pemusnahan',
                        'waktu' => datetime2mysql(post_safe('tanggal')).":".date("s"),
                        'barang_packing_id' => $data,
                        'unit_id' => $this->session->userdata('id_unit'),
                        'ed' => date2mysql($ed[$key]),
                        'harga' => (isset($jml->harga)?$jml->harga:'0'),
                        'ppn' => (isset($jml->ppn)?$jml->ppn:'0'),
                        'hna' => (isset($jml->hna)?$jml->hna:'0'),
                        'hpp' => (isset($jml->hna)?$jml->hna:'0'),
                        'het' => (isset($jml->het)?$jml->het:'0'),
                        'masuk' => '0',
                        'keluar' => $jl[$key],
                    );
                    if ($kategori[$key] === 'Alat Kesehatan') {
                        $akun_debet = 150;
                        $akun_kredit= 34;
                    }
                    else if ($kategori[$key] === 'Obat') {
                        $akun_debet = 150;
                        $akun_kredit= 188;
                    }
                    else if ($kategori[$key] === 'Bahan Baku Obat') {
                        $akun_debet = 150;
                        $akun_kredit= 189;
                    }
                    else if ($kategori[$key] === 'BHP') {
                        $akun_debet = 150;
                        $akun_kredit= 191;
                    }
                    else if ($kategori[$key] === 'Reagen Lab.') {
                        $akun_debet = 150;
                        $akun_kredit= 192;
                    }
                    else if ($kategori[$key] === 'Bahan Radiologi') {
                        $akun_debet = 150;
                        $akun_kredit= 193;
                    }
                    else if ($kategori[$key] === 'Alat Fisioterapi') {
                        $akun_debet = 150;
                        $akun_kredit= 193;
                    }
                    else if ($kategori[$key] === 'Gas Medis') {
                        $akun_debet = 150;
                        $akun_kredit= 206;
                    }
                    else if ($kategori[$key] === 'Alat Tulis Kantor') {
                        $akun_debet = 150;
                        $akun_kredit= 51;
                    }
                    else if ($kategori[$key] === 'Percetakan') {
                        $akun_debet = 150;
                        $akun_kredit= 195;
                    }
                    else if ($kategori[$key] === 'Linen') {
                        $akun_debet = 150;
                        $akun_kredit= 196;
                    }
                    else if ($kategori[$key] === 'Kelontong') {
                        $akun_debet = 150;
                        $akun_kredit= 197;
                    }
                    else if ($kategori[$key] === 'Lain-lain') {
                        $akun_debet = 150;
                        $akun_kredit= 198;
                    }
                    else if ($kategori[$key] === 'Bahan Makanan Basah') {
                        $akun_debet = 150;
                        $akun_kredit= 199;
                    }
                    else if ($kategori[$key] === 'Bahan Makanan Kering') {
                        $akun_debet = 150;
                        $akun_kredit= 200;
                    }
                    $this->db->insert('transaksi_detail', $data_trans);
                    $jurnal_84 = array(
                        'waktu' => date("Y-m-d H:i:s"),
                        'id_transaksi' => $id_pemusnahan,
                        'jenis_transaksi' => 'Pemusnahan',
                        'ket_transaksi' => '',
                        'id_sub_sub_sub_sub_rekening' => $akun_debet,
                        'debet' => ($jl[$key]*(isset($jml->hpp)?$jml->hpp:'0'))
                    );
                    $this->db->insert('jurnal', $jurnal_84);

                    $jurnal_2 = array(
                        'waktu' => date("Y-m-d H:i:s"),
                        'id_transaksi' => $id_pemusnahan,
                        'jenis_transaksi' => 'Pemusnahan',
                        'ket_transaksi' => '',
                        'id_sub_sub_sub_sub_rekening' => $akun_kredit,
                        'kredit' => ($jl[$key]*(isset($jml->hpp)?$jml->hpp:'0'))
                    );
                    $this->db->insert('jurnal', $jurnal_2);
                }
            }
            $result['action'] = 'add';
        } else {
            $this->db->where('id', $id);
            $this->db->update('pemusnahan', $data_pemusnahan);
            $this->db->delete('transaksi_detail', array('transaksi_jenis' => 'Pemusnahan', 'transaksi_id' => $id));
            $this->db->delete('jurnal', array('jenis_transaksi' => 'Pemusnahan', 'id_transaksi' => $id));
            $id_pemusnahan = $id;
            foreach ($id_pb as $key => $data) {
                if ($data != '') {
                    $jml = $this->db->query("select * from transaksi_detail where transaksi_jenis != 'Pemesanan' and barang_packing_id = '$id_pb[$key]' 
                    and ed = '".  date2mysql($ed[$key])."' and unit_id = '".$this->session->userdata('id_unit')."' order by waktu desc limit 1")->row();
                    //$sisa = $jml->sisa - $jl[$key];
                    $data_trans = array(
                        'transaksi_id' => $id_pemusnahan,
                        'transaksi_jenis' => 'Pemusnahan',
                        'waktu' => datetime2mysql(post_safe('tanggal')).":".date("s"),
                        'barang_packing_id' => $data,
                        'unit_id' => $this->session->userdata('id_unit'),
                        'ed' => date2mysql($ed[$key]),
                        'harga' => (isset($jml->harga)?$jml->harga:'0'),
                        'ppn' => (isset($jml->ppn)?$jml->ppn:'0'),
                        'hna' => (isset($jml->hna)?$jml->hna:'0'),
                        'hpp' => (isset($jml->hna)?$jml->hna:'0'),
                        'het' => (isset($jml->het)?$jml->het:'0'),
                        'masuk' => '0',
                        'keluar' => $jl[$key],
                    );
                    $this->db->insert('transaksi_detail', $data_trans);
                    $jurnal_84 = array(
                        'waktu' => date("Y-m-d H:i:s"),
                        'id_transaksi' => $id_pemusnahan,
                        'jenis_transaksi' => 'Pemusnahan',
                        'ket_transaksi' => '',
                        'id_sub_sub_sub_sub_rekening' => '150',
                        'debet' => ($jl[$key]*(isset($jml->ppn)?$jml->ppn:'0'))
                    );
                    $this->db->insert('jurnal', $jurnal_84);

                    $jurnal_2 = array(
                        'waktu' => date("Y-m-d H:i:s"),
                        'id_transaksi' => $id_pemusnahan,
                        'jenis_transaksi' => 'Pemusnahan',
                        'ket_transaksi' => '',
                        'id_sub_sub_sub_sub_rekening' => '188',
                        'kredit' => ($jl[$key]*(isset($jml->ppn)?$jml->ppn:'0'))
                    );
                    $this->db->insert('jurnal', $jurnal_2);
                }
            }
            $result['action'] = 'edit';
        }
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $status = FALSE;
        } else {
            $this->db->trans_commit();
            $status = TRUE;
        }
        $result['status'] = $status;
        $result['id_pemusnahan'] = $id_pemusnahan;
        return $result;
    }
    
    function penerimaan_distribusi_save() {
        $this->db->trans_begin();
        $data_penerimaan_dist = array(
            'distribusi_id' => post_safe('nodistribusi'),
            'pegawai_penduduk_id' => $this->session->userdata('id_user')
        );
        $id_pb = post_safe('id_pb');
        $ed = post_safe('ed');
        $jp = post_safe('jp');
        $id = post_safe('id_penerimaan_dist');
        if ($id === '') {
            $this->db->insert('distribusi_penerimaan', $data_penerimaan_dist);
            $id_dist_penerimaan = $this->db->insert_id();



            $detail = $this->db->query("select unit_id, tujuan_unit_id from distribusi where id = '".post_safe('nodistribusi')."'")->row();

            foreach ($id_pb as $key => $data) {
                if (($data != '') and ($ed[$key] != '') and ($jp[$key] != '')) {
                    $rowx = $this->db->query("select * from transaksi_detail where transaksi_jenis != 'Pemesanan' 
                        and barang_packing_id = '$data' and ed = '".date2mysql($ed[$key])."' and unit_id = '".$detail->tujuan_unit_id."' order by waktu desc limit 1")->row();
                    $rows = $this->db->query("select * from transaksi_detail where transaksi_jenis != 'Pemesanan' 
                        and barang_packing_id = '$data' and ed = '".date2mysql($ed[$key])."' and unit_id = '".$detail->tujuan_unit_id."' order by waktu desc limit 1")->row();
                    if (!isset($rows->id)) {
                        $rows = $this->db->query("select * from transaksi_detail where transaksi_jenis != 'Pemesanan' 
                        and barang_packing_id = '$data' and ed = '".date2mysql($ed[$key])."' and unit_id = '".$detail->unit_id."' order by waktu desc limit 1")->row();
                    }
    //                $new_sisa = (isset($rowx->sisa)?$rowx->sisa:'0') + $jp[$key];
                    $new_ed  =  date2mysql($ed[$key]);//isset($rows->barang_packing_id)?date2mysql($ed[$key]):isset($rows->ed)?$rows->ed:NULL;

                    $data_trans = array(
                        'transaksi_id' => $id_dist_penerimaan,
                        'transaksi_jenis' => 'Penerimaan Distribusi',
                        'waktu' => datetime2mysql(post_safe('tanggal')).":".date("s"),
                        'barang_packing_id' => $data,
                        'unit_id' => $this->session->userdata('id_unit'),
                        'ed' => $new_ed,
                        'harga' => (isset($rows->harga)?$rows->harga:'0'),
                        'ppn' => (isset($rows->ppn)?$rows->ppn:'0'),
                        'hna' => (isset($rows->hna)?$rows->hna:'0'),
                        'hpp' => (isset($rows->hpp)?$rows->hpp:'0'),
                        'het' => (isset($rows->het)?$rows->het:'0'),
                        'masuk' => $jp[$key],
                        'keluar' => '0'
                    );
                    $this->db->insert('transaksi_detail', $data_trans);
                    if ($this->db->trans_status() === FALSE) {
                        $this->db->trans_rollback();
                    }
                }
            }
            $result['action'] = 'add';
        } else {
            $this->db->where('id', $id);
            $this->db->update('distribusi_penerimaan', $data_penerimaan_dist);
            $this->db->delete('transaksi_detail', array('transaksi_jenis' => 'Penerimaan Distribusi', 'transaksi_id' => $id));
            //$this->db->delete('jurnal', array('id_transaksi' => $id, 'jenis_transaksi' => 'Penerimaan Distribusi'));
            $id_dist_penerimaan = $id;



            $detail = $this->db->query("select unit_id, tujuan_unit_id from distribusi where id = '".post_safe('nodistribusi')."'")->row();

            foreach ($id_pb as $key => $data) {
                if (($data != '') and ($ed[$key] != '') and ($jp[$key] != '')) {
                    $rowx = $this->db->query("select * from transaksi_detail where transaksi_jenis != 'Pemesanan' 
                        and barang_packing_id = '$data' and ed = '".date2mysql($ed[$key])."' and unit_id = '".$detail->tujuan_unit_id."' order by waktu desc limit 1")->row();
                    $rows = $this->db->query("select * from transaksi_detail where transaksi_jenis != 'Pemesanan' 
                        and barang_packing_id = '$data' and ed = '".date2mysql($ed[$key])."' and unit_id = '".$detail->tujuan_unit_id."' order by waktu desc limit 1")->row();
                    if (!isset($rows->id)) {
                        $rows = $this->db->query("select * from transaksi_detail where transaksi_jenis != 'Pemesanan' 
                        and barang_packing_id = '$data' and ed = '".date2mysql($ed[$key])."' and unit_id = '".$detail->unit_id."' order by waktu desc limit 1")->row();
                    }
    //                $new_sisa = (isset($rowx->sisa)?$rowx->sisa:'0') + $jp[$key];
                    $new_ed  =  date2mysql($ed[$key]);//isset($rows->barang_packing_id)?date2mysql($ed[$key]):isset($rows->ed)?$rows->ed:NULL;

                    $data_trans = array(
                        'transaksi_id' => $id_dist_penerimaan,
                        'transaksi_jenis' => 'Penerimaan Distribusi',
                        'waktu' => datetime2mysql(post_safe('tanggal')).":".date("s"),
                        'barang_packing_id' => $data,
                        'unit_id' => $this->session->userdata('id_unit'),
                        'ed' => $new_ed,
                        'harga' => (isset($rows->harga)?$rows->harga:'0'),
                        'ppn' => (isset($rows->ppn)?$rows->ppn:'0'),
                        'hna' => (isset($rows->hna)?$rows->hna:'0'),
                        'hpp' => (isset($rows->hpp)?$rows->hpp:'0'),
                        'het' => (isset($rows->het)?$rows->het:'0'),
                        'masuk' => $jp[$key],
                        'keluar' => '0'
                    );
                    $this->db->insert('transaksi_detail', $data_trans);
                    if ($this->db->trans_status() === FALSE) {
                        $this->db->trans_rollback();
                    }
                }
            }
            $result['action'] = 'edit';
        }
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $status = FALSE;
        } else {
            $this->db->trans_commit();
            $status = TRUE;
        }
        $result['status'] = $status;
        $result['id_penerimaan_distribusi'] = $id_dist_penerimaan;
        return $result;
    }
    
    function penjualan_non_resep_save() {
        $this->db->trans_begin();
        $id_jual    = $_POST['id_penjualan'];
        $tanggal    = date2mysql($_POST['tanggal']).' '.date("H:i:s");
        $customer   = ($_POST['id_customer'] !== '')?$_POST['id_customer']:"NULL";
        $diskon_pr  = $_POST['diskon_pr'];
        $diskon_rp  = currencyToNumber($_POST['diskon_rp']);
        $ppn        = $_POST['ppn'];
        $total      = currencyToNumber($_POST['total_penjualan']);
        $tuslah     = currencyToNumber($_POST['tuslah']);
        $embalage   = currencyToNumber($_POST['embalage']);
        $reimburse  = isset($_POST['reimburse'])?$_POST['reimburse']:'0';
        $pembayaran = currencyToNumber($_POST['pembulatan']); // yang dientrikan pembulatan pembayarannya

        if ($id_jual === '') {
            $sql = "insert into penjualan set
                waktu = '$tanggal',
                id_pasien = $customer,
                diskon_persen = '$diskon_pr',
                diskon_rupiah = '$diskon_rp',
                ppn = '$ppn',
                total = '$total',
                tuslah = '$tuslah',
                embalage = '$embalage',
                reimburse = '$reimburse',
                id_unit = '".$this->session->userdata('id_unit')."'";
            
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
                        keluar = '".($jumlah[$key]*$isi)."',
                        id_unit = '".$this->session->userdata('id_unit')."'";
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
                reimburse = '$reimburse',
                id_unit = '".$this->session->userdata('id_unit')."'
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
                        keluar = '".($jumlah[$key]*$isi)."',
                        id_unit = '".$this->session->userdata('id_unit')."'";
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
        $result['status'] = $status;
        $result['id'] = $id_penjualan;
        return $result;
    }
    
    function penjualan_load_data($id_penjualan = NULL) {
        $q = null;
        if ($id_penjualan != null) {
            $q.="and p.id = '$id_penjualan'";
        }
        $sql = "select bk.jenis, bk.id as id_kategori, bk.nama as kategori_barang, pddk.nama as nama_pasien, pk.kelas, pdf.pasien as no_rm, u.nama as unit, td.*, p.resep_id, bp.id as id_pb, bp.barcode, bp.margin, bp.hna, bp.diskon, b.nama as barang, st.nama as satuan_terkecil, bp.isi, 
            o.kekuatan, o.id as id_obat, o.generik, r.nama as pabrik, s.nama as satuan, sd.nama as sediaan, pdk.nama as pegawai, p.pembeli_penduduk_id, pd.nama as pembeli, p.total, p.bayar, p.pembulatan
            from penjualan p
            left join transaksi_detail td on (p.id = td.transaksi_id)
            left join resep rs on (rs.id = p.resep_id)
            left join pelayanan_kunjungan pk on (pk.id = rs.id_pelayanan_kunjungan)
            left join pendaftaran pdf on (pdf.no_daftar = pk.id_kunjungan)
            left join pasien ps on (ps.no_rm = pdf.pasien)
            left join penduduk pddk on (pddk.id = ps.id)
            left join unit u on (u.id = pk.id_unit)
            left join penduduk pd on (pd.id = p.pembeli_penduduk_id)
            left join penduduk pdk on (pdk.id = p.pegawai_penduduk_id)
            join barang_packing bp on (td.barang_packing_id = bp.id)
            join barang b on (b.id = bp.barang_id)
            join barang_kategori bk on (bk.id = b.barang_kategori_id)
            left join obat o on (o.id = b.id)
            left join satuan s on (s.id = o.satuan_id)
            left join satuan st on (st.id = bp.terkecil_satuan_id)
            left join sediaan sd on (sd.id = o.sediaan_id)
            left join relasi_instansi r on (r.id = b.pabrik_relasi_instansi_id) where td.transaksi_jenis = 'Penjualan' $q";
        //echo "<pre>".$sql."</pre>";
        return $this->db->query($sql);
    }
    
    function penjualan_load_nomor($id_penjualan) {
        $sql = "select p.*, td.waktu from penjualan p 
            left join transaksi_detail td on (td.transaksi_id = p.id) 
            where p.id like ('%$id_penjualan%') and td.transaksi_jenis = 'Penjualan' and p.resep_id is NULL and p.bayar = '0' group by p.id";
        return $this->db->query($sql);
    }
    
    function save_barang() {
        $nama       = $_POST['nama'];
        $barcode    = $_POST['barcode'];
        $pabrik     = ($_POST['id_pabrik'] !== '')?$_POST['id_pabrik']:'NULL';
        $perundangan= $_POST['perundangan'];
        $rak        = $_POST['rak'];
        $formularium= $_POST['formularium'];
        $kekuatan   = $_POST['kekuatan'];
        $golongan   = ($_POST['golongan'] !== '')?$_POST['golongan']:'NULL';
        $s_kekuatan = ($_POST['s_sediaan'] !== '')?$_POST['s_sediaan']:'NULL';
        $sediaan    = ($_POST['sediaan'] !== '')?$_POST['sediaan']:'NULL';
        $admr       = $_POST['admr'];
        $generik    = $_POST['generik'];

        $indikasi   = strip_tags($_POST['indikasi']);
        $dosis      = strip_tags($_POST['dosis']);
        $kandungan  = strip_tags($_POST['kandungan']);
        $perhatian  = strip_tags($_POST['perhatian']);
        $kontra_ind = strip_tags($_POST['kontra_indikasi']);
        $ef_samping = strip_tags($_POST['efek_samping']);

        $stok_min   = $_POST['stok_min'];
        $margin_nr  = $_POST['margin_nr'];
        $margin_r   = $_POST['margin_r'];
        $hna        = currencyToNumber($_POST['hna']);
        $plus_ppn   = isset($_POST['ppn'])?$_POST['ppn']:'0';
        $aktif      = isset($_POST['aktifasi'])?$_POST['aktifasi']:'0';
        $aturan_pki = $_POST['aturan_pakai'];
        $kls_terapi = (isset($_POST['kls_terapi']) and ($_POST['kls_terapi'] !== ''))?$_POST['kls_terapi']:'NULL';
        $fda_pregnan= $_POST['fda_pregnan'];
        $fda_lactacy= $_POST['fda_lactacy'];
        $id_barang  = $_POST['id_barang'];
        $status     = ($_POST['status'] !== '')?$_POST['status']:'NULL';
        $rangeterapi= ($_POST['range_terapi'] !== '')?$_POST['range_terapi']:'NULL';
        $pengawasan = ($_POST['pengawasan'] !== '')?$_POST['pengawasan']:'NULL';
        $fornas     = ($_POST['fornas'] !== '')?$_POST['fornas']:'NULL';
        $NewFileName= "";
//        $UploadDirectory	= $_SERVER['DOCUMENT_ROOT'].'/barang/'; //Upload Directory, ends with slash & make sure folder exist
//        $NewFileName= NULL;
//            // replace with your mysql database details
//        if (!@file_exists($UploadDirectory)) {
//                //destination folder does not exist
//                die("Make sure Upload directory exist!");
//        }
//        if(isset($_FILES['mFile']['name'])) {
//
//                $FileName           = strtolower($_FILES['mFile']['name']); //uploaded file name
//                $FileTitle		= mysql_real_escape_string($_POST['nama']); // file title
//                $ImageExt		= substr($FileName, strrpos($FileName, '.')); //file extension
//                $FileType		= $_FILES['mFile']['type']; //file type
//                //$FileSize		= $_FILES['mFile']["size"]; //file size
//                $RandNumber   		= rand(0, 9999999999); //Random number to make each filename unique.
//                //$uploaded_date		= date("Y-m-d H:i:s");
//
//                switch(strtolower($FileType))
//                {
//                        //allowed file types
//                        case 'image/png': //png file
//                        case 'image/gif': //gif file 
//                        case 'image/jpeg': //jpeg file
//                        case 'application/pdf': //PDF file
//                        case 'application/msword': //ms word file
//                        case 'application/vnd.ms-excel': //ms excel file
//                        case 'application/x-zip-compressed': //zip file
//                        case 'text/plain': //text file
//                        case 'text/html': //html file
//                                break;
//                        default:
//                                die('Unsupported File!'); //output error
//                }
//
//
//                //File Title will be used as new File name
//                $NewFileName = preg_replace(array('/\s/', '/\.[\.]+/', '/[^\w_\.\-]/'), array('_', '.', ''), strtolower($FileTitle));
//                $NewFileName = $NewFileName.'_'.$RandNumber.$ImageExt;
//           //Rename and save uploded file to destination folder.
//           if(move_uploaded_file($_FILES['mFile']["tmp_name"], $UploadDirectory . $NewFileName ))
//           {
//                        //die('Success! File Uploaded.');
//
//           }else{
//                        //die('error uploading File!');
//           }
//        }
        if ($id_barang === '') {
            $sql = "insert into barang set
                    barcode = '$barcode',
                    nama = '$nama',
                    id_pabrik = $pabrik,
                    perundangan = '$perundangan',
                    rak = '$rak',
                    kekuatan = '$kekuatan',
                    id_golongan = $golongan,
                    satuan_kekuatan = $s_kekuatan,
                    id_sediaan = $sediaan,
                    adm_r = '$admr',
                    generik = '$generik',
                    indikasi = '$indikasi',
                    dosis = '$dosis',
                    kandungan = '$kandungan',
                    perhatian = '$perhatian',
                    kontra_indikasi = '$kontra_ind',
                    efek_samping = '$ef_samping',
                    formularium = '$formularium',
                    aturan_pakai = '$aturan_pki',
                    id_kelas_terapi = $kls_terapi,
                    fda_pregnancy = '$fda_pregnan',
                    fda_lactacy = '$fda_lactacy',
                    stok_minimal = '$stok_min',
                    margin_non_resep = '$margin_nr',
                    margin_resep = '$margin_r',
                    plus_ppn = '$plus_ppn',
                    hna = '$hna',
                    aktif = '$aktif',
                    status = '$status',
                    range_terapi = '$rangeterapi',
                    pengawasan = '$pengawasan',
                    fornas = '$fornas'";
            $this->db->query($sql);
            $id = $this->db->insert_id();

            $default = $_POST['default'];

            if (isset($_POST['jumlah'])) {
                $jumlah     = $_POST['jumlah'];
                for ($i = 0; $i <= $jumlah; $i++) {
                    $id_kemasan = $_POST['id_kemasan'.$i];
                    $barcode    = $_POST['barcode'.$i];
                    $kemasan    = $_POST['kemasan'.$i]; // kemasan terbesar
                    $isi        = $_POST['isi'.$i];
                    $satuan     = $_POST['satuan'.$i]; // kemasan terkecil
                    $isi_satuan = $_POST['isi_kecil'.$i];
                    $bertingkat = isset($_POST['is_bertingkat'.$i])?$_POST['is_bertingkat'.$i]:'0';

                    if ($default == $i) {
                        $query="insert into kemasan set 
                            id_barang = '$id',
                            barcode = '$barcode',
                            id_kemasan = '$kemasan',
                            isi = '$isi',
                            id_satuan = '$satuan',
                            isi_satuan = '$isi_satuan',
                            default_kemasan = '1',
                            is_harga_bertingkat = '".(isset($bertingkat)?$bertingkat:'0')."'";
                    } else {
                        $query="insert into kemasan set 
                            id_barang = '$id',
                            barcode = '$barcode',
                            id_kemasan = '$kemasan',
                            isi = '$isi',
                            id_satuan = '$satuan',
                            isi_satuan = '$isi_satuan',
                            default_kemasan = '0',
                            is_harga_bertingkat = '".(isset($bertingkat)?$bertingkat:'0')."'";
                    }
                    //echo $default.' - '.$i.' - '.$dft_kmsan."<br/>";

                    //echo $query."-";
                    $this->db->query($query);
                    $id_packing = $this->db->insert_id();
                    if (isset($_POST['awal'.$i])) {
                        $awal       = $_POST['awal'.$i];
                        $akhir      = $_POST['akhir'.$i];
                        $margin_nr  = $_POST['margin_nr'.$i];
                        $margin_r   = $_POST['margin_r'.$i];
                        $diskon     = $_POST['d_persen'.$i];
                        $diskon_rp  = $_POST['d_rupiah'.$i];
                        $hj_nonresep= $_POST['hj_nonresep'.$i];
                        $hj_resep   = $_POST['hj_resep'.$i];
                        foreach ($awal as $no => $rows) {
                            $query1 = "insert into dinamic_harga_jual set
                                id_kemasan = '$id_packing',
                                jual_min = '$rows',
                                jual_max = '$akhir[$no]',
                                margin_non_resep = '$margin_nr[$no]',
                                margin_resep = '$margin_r[$no]',
                                diskon_persen = '$diskon[$no]',
                                diskon_rupiah = '".currencyToNumber($diskon_rp[$no])."',
                                hj_non_resep = '".currencyToNumber($hj_nonresep[$no])."',
                                hj_resep = '".currencyToNumber($hj_resep[$no])."'";
                            //echo $query1;
                            $this->db->query($query1);
                        }
                    }
                }
            }
        } else {
            $sql= "update barang set
                    barcode = '$barcode',
                    nama = '$nama',
                    id_pabrik = $pabrik,
                    perundangan = '$perundangan',
                    rak = '$rak',
                    kekuatan = '$kekuatan',
                    id_golongan = $golongan,
                    satuan_kekuatan = $s_kekuatan,
                    id_sediaan = $sediaan,
                    adm_r = '$admr',
                    generik = '$generik',
                    indikasi = '$indikasi',
                    dosis = '$dosis',
                    kandungan = '$kandungan',
                    perhatian = '$perhatian',
                    kontra_indikasi = '$kontra_ind',
                    efek_samping = '$ef_samping',
                    formularium = '$formularium',
                    aturan_pakai = '$aturan_pki',
                    id_kelas_terapi = $kls_terapi,
                    fda_pregnancy = '$fda_pregnan',
                    fda_lactacy = '$fda_lactacy',
                    stok_minimal = '$stok_min',
                    margin_non_resep = '$margin_nr',
                    margin_resep = '$margin_r',
                    plus_ppn = '$plus_ppn',
                    hna = '$hna',
                    aktif = '$aktif',
                    status = '$status',
                    range_terapi = '$rangeterapi',
                    pengawasan = '$pengawasan',
                    fornas = '$fornas'";

            if (isset($_FILES['mFile']['name']) and $_FILES['mFile']['name'] !== '') {
                $sql.= ",image = '$NewFileName'";
            }
            $sql.="where id = '$id_barang'";
            //echo "<pre>".$sql."</pre>";
            $this->db->query($sql);
            $this->db->query("update kemasan set default_kemasan = '0' where id_barang = '$id_barang'");
            $id = $id_barang;

            if (isset($_POST['jumlah'])) {
                $jumlah     = $_POST['jumlah'];
                for ($i = 0; $i <= $jumlah; $i++) {
                    $id_kemasan = $_POST['id_kemasan'.$i];
                    $barcode    = $_POST['barcode'.$i];
                    $kemasan    = $_POST['kemasan'.$i]; // kemasan terbesar
                    $isi        = $_POST['isi'.$i];
                    $satuan     = $_POST['satuan'.$i]; // kemasan terkecil
                    $isi_satuan = $_POST['isi_kecil'.$i];
                    $bertingkat = isset($_POST['is_bertingkat'.$i])?$_POST['is_bertingkat'.$i]:'0';
                    if ($id_kemasan !== '') {
                        if ($_POST['default'] == $id_kemasan) {
                            $query="update kemasan set 
                                id_barang = '$id',
                                barcode = '$barcode',
                                id_kemasan = '$kemasan',
                                isi = '$isi',
                                id_satuan = '$satuan',
                                isi_satuan = '$isi_satuan',
                                default_kemasan = '1',
                                is_harga_bertingkat = '$bertingkat'
                                where id = '$id_kemasan'";
                        } else {
                            $query="update kemasan set 
                                id_barang = '$id',
                                barcode = '$barcode',
                                id_kemasan = '$kemasan',
                                isi = '$isi',
                                id_satuan = '$satuan',
                                isi_satuan = '$isi_satuan',
                                default_kemasan = '0',
                                is_harga_bertingkat = '$bertingkat'
                                where id = '$id_kemasan'";
                        }
                        //echo $query."<br/>";
                        $this->db->query($query);
                        $id_packing = $id_kemasan;
                    } else {
                        if ($_POST['default'] == $i) {
                            $query="insert into kemasan set 
                                id_barang = '$id',
                                barcode = '$barcode',
                                id_kemasan = '$kemasan',
                                isi = '$isi',
                                id_satuan = '$satuan',
                                isi_satuan = '$isi_satuan',
                                default_kemasan = '1',
                                is_harga_bertingkat = '$bertingkat'";
                        } else {
                            $query="insert into kemasan set 
                                id_barang = '$id',
                                barcode = '$barcode',
                                id_kemasan = '$kemasan',
                                isi = '$isi',
                                id_satuan = '$satuan',
                                isi_satuan = '$isi_satuan',
                                default_kemasan = '0',
                                is_harga_bertingkat = '$bertingkat'";
                        }
                        //echo $query."<br/>";
                        $this->db->query($query);
                        $id_packing = $this->db->insert_id();
                    }
                    //echo $query."<br/>";


                    $this->db->query("delete from dinamic_harga_jual where id_kemasan = '$id_packing'");
                    if (isset($_POST['awal'.$i])) {
                        $awal       = $_POST['awal'.$i];
                        $akhir      = $_POST['akhir'.$i];
                        $margin_nr  = $_POST['margin_nr'.$i];
                        $margin_r   = $_POST['margin_r'.$i];
                        $diskon     = $_POST['d_persen'.$i];
                        $diskon_rp  = $_POST['d_rupiah'.$i];
                        $hj_nonresep= $_POST['hj_nonresep'.$i];
                        $hj_resep   = $_POST['hj_resep'.$i];
                        foreach ($awal as $no => $rows) {
                            $query1 = "insert into dinamic_harga_jual set
                                id_kemasan = '$id_packing',
                                jual_min = '$rows',
                                jual_max = '$akhir[$no]',
                                margin_non_resep = '$margin_nr[$no]',
                                margin_resep = '$margin_r[$no]',
                                diskon_persen = '$diskon[$no]',
                                diskon_rupiah = '".currencyToNumber($diskon_rp[$no])."',
                                hj_non_resep = '".currencyToNumber($hj_nonresep[$no])."',
                                hj_resep = '".currencyToNumber($hj_resep[$no])."'";
                            //echo $query1;
                            $this->db->query($query1);
                        }
                    }
                }

            }
        }

        die(json_encode(array('status' => TRUE, 'id_barang' => $id, 'nama' => $nama)));
    }
    
    function penjualan_jasa_save($no_daftar, $id_pk) {
        $this->load->model('m_pendaftaran');
        $this->db->trans_begin();
        $complete = TRUE;
                
        $waktu = post_safe('waktu');
        $tarif = post_safe('id_tarif');
        $nakes = post_safe('id_nakes');
        $jumlah = post_safe('jumlah');
        $jenis = post_safe('jenis_pelayanan_kunjungan');
        
        if (is_array($tarif)){
            foreach ($tarif as $key => $rows) {
              
                if ($rows != '') {
                    /*$sql_kode = "select * from 
                        kode_rekening_tarif where 
                        tarif_id = '$rows' and jenis_pelayanan = '".$jenis[$key]."' ";
                    $kode_rek = $this->db->query($sql_kode)->row();
                    //echo $sql_kode;
                    if($kode_rek !== null){*/
                        $param['id_pk'] = $id_pk;
                        $param['no_daftar'] = $no_daftar;
                        $param['tarif_id'] = $rows; // kunjungan pasien
                        //$param['id_debet'] = $kode_rek->kode_debet;
                        //$param['id_kredit'] = $kode_rek->kode_kredit;
                        $param['waktu'] = datetime2mysql($waktu[$key]);
                        $param['frekuensi'] = $jumlah[$key];
                        $param['nakes'] = ($nakes[$key] !== '')?$nakes[$key]:NULL;
                        $this->m_pendaftaran->insert_biaya($param);
                        //$this->insert_penjualan_transaksi_detail($id_pk, $rows, $param['frekuensi']); // sementara didisabled
                        $complete = TRUE;
                    /*}else{
                        $complete = FALSE;
                    }*/
                } else {
                    $complete = FALSE;
                    break;
                }
                    
            }            

        }        
        
        
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $status = FALSE;
        } else  if($complete === FALSE){
            $this->db->trans_rollback();
            $status = FALSE;
        }else{            
            $this->db->trans_commit();
            $status = TRUE;
        }
        
        $result['status'] = $status;
        $result['no_daftar'] = $no_daftar;
        return $result;
    }

    function insert_penjualan_transaksi_detail($id_pk, $id_tarif, $frekuensi){
        $this->load->model('m_referensi');
        $data_tarif = $this->m_referensi->get_data_bhp_tarif($id_tarif);
        $waktu = date("Y-m-d H:i:s");
        foreach ($data_tarif as $key => $v);
            // Penjualan
            $data_penjualan = array(
                'pegawai_penduduk_id' => $this->session->userdata('id_user'),
                'id_pelayanan_kunjungan' => $id_pk,
                'total' => $v->harga_jual
            );
            $id_penjualan = $this->db->insert('penjualan', $data_penjualan);
            $sql_ed = "select * from bhp_tarif where id_tarif = '$id_tarif'";
            $data_ed = $this->db->query($sql_ed)->result();
            foreach ($data_ed as $key => $data) {
                $data_detail = array(
                    'id_penjualan' => $id_penjualan,
                    'id_kemasan' => $data->id_kemasan_barang,
                    'expired' => ''
                );
                $this->db->insert('detail_penjualan', $data_detail);
            }
            

            /*$jurnal_1 = array(
                'waktu' => $waktu,
                'id_transaksi' => $id_penjualan,
                'jenis_transaksi' => 'Penjualan',
                'ket_transaksi' => '',
                'id_sub_sub_sub_sub_rekening' => '1',
                'debet' => $v->harga_jual
            );
            $this->db->insert('jurnal', $jurnal_1);
            $jurnal_264 = array(
                'waktu' => $waktu,
                'id_transaksi' => $id_penjualan,
                'jenis_transaksi' => 'Penjualan',
                'ket_transaksi' => '',
                'id_sub_sub_sub_sub_rekening' => '231',
                'kredit' => $v->harga_jual
            );
            $this->db->insert('jurnal', $jurnal_264);*/
    }
    
    function resep_save() {
        $this->db->trans_begin();
        $sah = post_safe('absah');
        $data_resep = array(
            'waktu' => datetime2mysql(post_safe('tanggal')).":".date("s"),
            'dokter_penduduk_id' => post_safe('id_dokter'),
//            'id_pelayanan_kunjungan' => post_safe('id_pelayanan'),
            'sah' => isset($sah)?$sah:'Sah',
            'keterangan' => post_safe('ket')
        );
        $id_resep = post_safe('id_resep');
        
        $this->db->where('id', $id_resep);
        $this->db->update('resep', $data_resep);
        //$this->db->delete('resep_r', array('resep_id' => $id_resep));
        
        
        $nr  = post_safe('nr');
        $jr  = post_safe('jr');
        $jt  = post_safe('jt');
        $ap  = post_safe('ap');
        $it  = post_safe('it');
        $ja  = post_safe('ja');
        $t_tebus = post_safe('t_tebus');
        $pr  = post_safe('pr');
        
        foreach ($nr as $key => $data) {
            if (($jr[$key] != '') and ($jt[$key] != '') and ($ap[$key] != '') and ($it[$key] != '') and ($ja[$key] != '0-0')) {
                $jasa = explode("-", $ja[$key]);
                $data_resep_r = array(
                    'resep_id' => $id_resep,
                    'r_no' => $data,
                    'resep_r_jumlah' => $jr[$key],
                    'tebus_r_jumlah' => $jt[$key],
                    't_tebus' => $jt[$key],
                    'pakai_aturan' => $ap[$key],
                    'iter' => $it[$key],
                    'perintah_resep' => $pr[$key],
                    'tarif_id' => $jasa[0],
                    'profesi_layanan_tindakan_jasa_total' => $jasa[1],
                    'pegawai_penduduk_id' => $this->session->userdata('id_user')
                );
                $this->db->insert('resep_r', $data_resep_r);
                $id_r= $this->db->insert_id();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                }
                //$pb = post_safe('pb'.$key);
                $dr  = post_safe('dr'.$key);
                $id_pb = post_safe('id_pb'.$key);
                $jp  = post_safe('jp'.$key);
                if (!empty($id_pb)) {
                    foreach ($id_pb as $num => $rows) {
                        $form = $this->db->query("select o.formularium from obat o join barang_packing b on (o.id = b.id) where b.id = '$rows'")->row();
                        /*$harga= $this->db->query("select td.*, bp.margin, bp.diskon from transaksi_detail td 
                            join barang_packing bp on (td.barang_packing_id = bp.id) where td.barang_packing_id = '$rows' and 
                            td.transaksi_jenis != 'Pemesanan' order by td.waktu desc limit 1")->row();*/
                        //$hjual = (($harga->hna*($harga->margin/100))+$harga->hna) - (($harga->diskon/100)*$harga->hna);
                        $data_resep_r_racik = array(
                            'r_resep_id' => $id_r,
                            'barang_packing_id' => $rows,
                            'jual_harga' => '0',
                            'dosis_racik' => $dr[$num],
                            'pakai_jumlah' => $jp[$num],
                            'formularium' => isset($form->formularium)?$form->formularium:NULL
                        );
                        $this->db->insert('resep_racik_r_detail', $data_resep_r_racik);
                        if ($this->db->trans_status() === FALSE) {
                            $this->db->trans_rollback();
                        }
                    }
                }
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
        $result['id_resep'] = $id_resep;
        return $result;
    }
    
    function receipt_save() {
        $this->db->trans_begin();
        $sah = post_safe('absah');
        $data_resep = array(
            'waktu' => datetime2mysql(post_safe('tanggal')).":".date("s"),
            'dokter_penduduk_id' => post_safe('id_dokter'),
            'id_pelayanan_kunjungan' => post_safe('id_pelayanan'),
            'sah' => isset($sah)?$sah:NULL,
            'keterangan' => post_safe('ket')
        );
        $id_resep = post_safe('id_resep');
        if (isset($id_resep) and $id_resep == '') {
            $this->db->insert('resep', $data_resep);
            $id_resep = $this->db->insert_id();
        } else {
            $this->db->where('id', $id_resep);
            $this->db->update('resep', $data_resep);
            $this->db->delete('resep_r_orig', array('resep_id' => $id_resep));
        }
        
        $nr  = post_safe('nr');
        $jr  = post_safe('jr');
        $jt  = post_safe('jt');
        $ap  = post_safe('ap');
        $it  = post_safe('it');
        $ja  = post_safe('ja');
        $pr  = post_safe('pr');
        
        foreach ($nr as $key => $data) {
            //$jasa = explode("-", $ja[$key]);
            $data_resep_r = array(
                'resep_id' => $id_resep,
                'r_no' => $data,
                'resep_r_jumlah' => $jr[$key],
                'tebus_r_jumlah' => '0',
                'pakai_aturan' => $ap[$key],
                'iter' => $it[$key],
                'perintah_resep' => $pr[$key]
                //'profesi_layanan_tindakan_jasa_total' => (isset($jasa[1])?$jasa[1]:'0'),
//                'pegawai_penduduk_id' => $this->session->userdata('id_user')
            );
            $this->db->insert('resep_r_orig', $data_resep_r);
            $id_r= $this->db->insert_id();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
            }
            //$pb = post_safe('pb'.$key);
            $dr  = post_safe('dr'.$key);
            $id_pb = post_safe('id_pb'.$key);
            $jp  = post_safe('jp'.$key);
            foreach ($id_pb as $num => $rows) {
                $form = $this->db->query("select o.formularium from obat o join barang_packing b on (o.id = b.id) where b.id = '$rows'")->row();
                
                $data_resep_r_racik = array(
                    'r_resep_id_orig' => $id_r,
                    'barang_id' => $rows,
                    'dosis_racik' => $dr[$num],
                    'pakai_jumlah' => $jp[$num],
                    'formularium' => isset($form->formularium)?$form->formularium:NULL
                );
                $this->db->insert('resep_racik_r_detail_orig', $data_resep_r_racik);
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                }
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
        $result['id_resep'] = $id_resep;
        return $result;
    }
    
    function retur_pembelian_save() {
        $this->db->trans_begin();
        $id_retur_pembelian = post_safe('id_retur_pembelian');
        $data_retur = array(
            'pembelian_id' => post_safe('id_pembelian'),
            'pegawai_penduduk_id' => $this->session->userdata('id_user'),
            'salesman_penduduk_id' => (post_safe('id_sales') != '')?post_safe('id_sales'):NULL,
            'suplier_relasi_instansi' => post_safe('id_suplier')
        );
        $kategori = post_safe('kategori');
        $ed = post_safe('ed');
        $pb = post_safe('id_pb');
        $jml_retur = post_safe('jml_retur');
        $total_retur = post_safe('total_retur_beli');
        if ($id_retur_pembelian === '') {
            
            $this->db->insert('pembelian_retur', $data_retur);
            $id = $this->db->insert_id();
            
            foreach ($pb as $key => $data) {
                if ($data != '') {

                    $jml = $this->db->query("select * from transaksi_detail where transaksi_jenis != 'Pemesanan' and barang_packing_id = '$pb[$key]' and ed = '$ed[$key]' and unit_id = '".$this->session->userdata('id_unit')."' order by waktu desc limit 1")->row();
                    //$sisa = (isset($jml->sisa)?$jml->sisa:0) - $jml_retur[$key];
                    $data_trans = array(
                        'transaksi_id' => $id,
                        'transaksi_jenis' => 'Retur Pembelian',
                        'waktu' => datetime2mysql(post_safe('tanggal')).":".date("s"),
                        'barang_packing_id' => $data,
                        'unit_id' => $this->session->userdata('id_unit'),
                        'ed' => $jml->ed,
                        'harga' => isset($jml->harga)?$jml->harga:'0',
                        'ppn' => isset($jml->ppn)?$jml->ppn:'0',
                        'hna' => isset($jml->hna)?$jml->hna:'0',
                        'hpp' => isset($jml->hpp)?$jml->hpp:'0',
                        'het' => isset($jml->het)?$jml->het:'0',
                        'masuk' => '0',
                        'keluar' => $jml_retur[$key]
                    );
                    $this->db->insert('transaksi_detail', $data_trans);
                    if ($kategori[$key] === 'Alat Kesehatan') {
                        $akun_debet = 34;
                        $akun_kredit= 84;
                    }
                    else if ($kategori[$key] === 'Obat') {
                        $akun_debet = 188;
                        $akun_kredit= 84;
                    }
                    else if ($kategori[$key] === 'Bahan Baku Obat') {
                        $akun_debet = 189;
                        $akun_kredit= 84;
                    }
                    else if ($kategori[$key] === 'BHP') {
                        $akun_debet = 191;
                        $akun_kredit= 84;
                    }
                    else if ($kategori[$key] === 'Reagen Lab.') {
                        $akun_debet = 192;
                        $akun_kredit= 84;
                    }
                    else if ($kategori[$key] === 'Bahan Radiologi') {
                        $akun_debet = 193;
                        $akun_kredit= 84;
                    }
                    else if ($kategori[$key] === 'Alat Fisioterapi') {
                        $akun_debet = 194;
                        $akun_kredit= 84;
                    }
                    else if ($kategori[$key] === 'Gas Medis') {
                        $akun_debet = 206;
                        $akun_kredit= 84;
                    }
                    else if ($kategori[$key] === 'Alat Tulis Kantor') {
                        $akun_debet = 51;
                        $akun_kredit= 84;
                    }
                    else if ($kategori[$key] === 'Percetakan') {
                        $akun_debet = 195;
                        $akun_kredit= 84;
                    }
                    else if ($kategori[$key] === 'Linen') {
                        $akun_debet = 196;
                        $akun_kredit= 84;
                    }
                    else if ($kategori[$key] === 'Kelontong') {
                        $akun_debet = 197;
                        $akun_kredit= 84;
                    }
                    else if ($kategori[$key] === 'Lain-lain') {
                        $akun_debet = 198;
                        $akun_kredit= 84;
                    }
                    else if ($kategori[$key] === 'Bahan Makanan Basah') {
                        $akun_debet = 199;
                        $akun_kredit= 84;
                    }
                    else if ($kategori[$key] === 'Bahan Makanan Kering') {
                        $akun_debet = 200;
                        $akun_kredit= 84;
                    }
                    $jurnal_188 = array(
                        'waktu' => date("Y-m-d H:i:s"),
                        'id_transaksi' => $id,
                        'jenis_transaksi' => 'Retur Pembelian',
                        'ket_transaksi' => post_safe('keterangan'),
                        'id_sub_sub_sub_sub_rekening' => $akun_kredit,
                        'debet' => $jml_retur[$key]*(isset($jml->hpp)?$jml->hpp:'0')
                    );
                    $this->db->insert('jurnal', $jurnal_188);

                    $jurnal_84 = array(
                        'waktu' => date("Y-m-d H:i:s"),
                        'id_transaksi' => $id,
                        'jenis_transaksi' => 'Retur Pembelian',
                        'ket_transaksi' => post_safe('keterangan'),
                        'id_sub_sub_sub_sub_rekening' => $akun_debet,
                        'kredit' => $jml_retur[$key]*(isset($jml->hpp)?$jml->hpp:'0')
                    );
                    $this->db->insert('jurnal', $jurnal_84);
                }
            }
            
            $result['action'] = 'add';
        } else {
            $this->db->where('id', $id_retur_pembelian);
            $this->db->update('pembelian_retur', $data_retur);
            $this->db->delete('transaksi_detail', array('transaksi_jenis' => 'Retur Pembelian', 'transaksi_id' => $id_retur_pembelian));
            $this->db->delete('jurnal', array('jenis_transaksi' => 'Retur Pembelian', 'id_transaksi' => $id_retur_pembelian));
            $id = $id_retur_pembelian;
            
            foreach ($pb as $key => $data) {
                if ($data != '') {

                    $jml = $this->db->query("select * from transaksi_detail where transaksi_jenis != 'Pemesanan' and barang_packing_id = '$pb[$key]' and ed = '$ed[$key]' and unit_id = '".$this->session->userdata('id_unit')."' order by waktu desc limit 1")->row();
                    //$sisa = (isset($jml->sisa)?$jml->sisa:0) - $jml_retur[$key];
                    $data_trans = array(
                        'transaksi_id' => $id,
                        'transaksi_jenis' => 'Retur Pembelian',
                        'waktu' => datetime2mysql(post_safe('tanggal')).":".date("s"),
                        'barang_packing_id' => $data,
                        'unit_id' => $this->session->userdata('id_unit'),
                        'ed' => $jml->ed,
                        'harga' => isset($jml->harga)?$jml->harga:'0',
                        'ppn' => isset($jml->ppn)?$jml->ppn:'0',
                        'hna' => isset($jml->hna)?$jml->hna:'0',
                        'hpp' => isset($jml->hpp)?$jml->hpp:'0',
                        'het' => isset($jml->het)?$jml->het:'0',
                        'masuk' => '0',
                        'keluar' => $jml_retur[$key]
                    );
                    $this->db->insert('transaksi_detail', $data_trans);
                    if ($kategori[$key] === 'Alat Kesehatan') {
                        $akun_kredit = 34;
                        $akun_debet= 267;
                    }
                    else if ($kategori[$key] === 'Obat') {
                        $akun_kredit = 188;
                        $akun_debet= 267;
                    }
                    else if ($kategori[$key] === 'Bahan Baku Obat') {
                        $akun_kredit = 189;
                        $akun_debet= 267;
                    }
                    else if ($kategori[$key] === 'BHP') {
                        $akun_kredit = 191;
                        $akun_debet= 267;
                    }
                    else if ($kategori[$key] === 'Reagen Lab.') {
                        $akun_kredit = 192;
                        $akun_debet= 267;
                    }
                    else if ($kategori[$key] === 'Bahan Radiologi') {
                        $akun_kredit = 193;
                        $akun_debet= 267;
                    }
                    else if ($kategori[$key] === 'Alat Fisioterapi') {
                        $akun_kredit = 194;
                        $akun_debet= 267;
                    }
                    else if ($kategori[$key] === 'Gas Medis') {
                        $akun_kredit = 206;
                        $akun_debet= 267;
                    }
                    else if ($kategori[$key] === 'Alat Tulis Kantor') {
                        $akun_kredit = 51;
                        $akun_debet= 267;
                    }
                    else if ($kategori[$key] === 'Percetakan') {
                        $akun_kredit = 195;
                        $akun_debet= 267;
                    }
                    else if ($kategori[$key] === 'Linen') {
                        $akun_kredit = 196;
                        $akun_debet= 267;
                    }
                    else if ($kategori[$key] === 'Kelontong') {
                        $akun_kredit = 197;
                        $akun_debet= 267;
                    }
                    else if ($kategori[$key] === 'Lain-lain') {
                        $akun_kredit = 198;
                        $akun_debet= 267;
                    }
                    else if ($kategori[$key] === 'Bahan Makanan Basah') {
                        $akun_kredit = 199;
                        $akun_debet= 267;
                    }
                    else if ($kategori[$key] === 'Bahan Makanan Kering') {
                        $akun_kredit = 200;
                        $akun_debet= 267;
                    }
                    $jurnal_188 = array(
                        'waktu' => date("Y-m-d H:i:s"),
                        'id_transaksi' => $id,
                        'jenis_transaksi' => 'Retur Pembelian',
                        'ket_transaksi' => post_safe('keterangan'),
                        'id_sub_sub_sub_sub_rekening' => $akun_kredit,
                        'debet' => $jml_retur[$key]*(isset($jml->hpp)?$jml->hpp:'0')
                    );
                    $this->db->insert('jurnal', $jurnal_188);

                    $jurnal_84 = array(
                        'waktu' => date("Y-m-d H:i:s"),
                        'id_transaksi' => $id,
                        'jenis_transaksi' => 'Retur Pembelian',
                        'ket_transaksi' => post_safe('keterangan'),
                        'id_sub_sub_sub_sub_rekening' => $akun_debet,
                        'kredit' => $jml_retur[$key]*(isset($jml->hpp)?$jml->hpp:'0')
                    );
                    $this->db->insert('jurnal', $jurnal_84);
                }
            }
            
            $result['action'] = 'edit';
        }
        
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $status = FALSE;
        } else {
            $this->db->trans_commit();
            $status = TRUE;
        }
        $result['status'] = $status;
        $result['id_retur_pembelian'] = $id;
        return $result;
    }
    
    function reretur_pembelian_save() {
        $this->db->trans_begin();
        $berupa = post_safe('berupa');
        $total  = post_safe('total');
        $jenis  = post_safe('jenis');
        $kategori = post_safe('kategori');
        $id_reretur_pembelian = post_safe('id_reretur_pembelian');
        if ($id_reretur_pembelian !== '') {
            //$this->db->delete('pembelian_retur_penerimaan', array('id' => $id_reretur_pembelian));
            $this->db->delete('transaksi_detail', array('transaksi_jenis' => 'Penerimaan Retur Pembelian', 'transaksi_id' => $id_reretur_pembelian));
            $this->db->delete('jurnal', array('id_transaksi' => $id_reretur_pembelian, 'jenis_transaksi' => 'Penerimaan Retur Pembelian'));
        }
        if ($berupa == 'uang') {
            $data_retur_pembelian_penerimaan = array(
                'retur_id' => post_safe('noretur'),
                'pegawai_penduduk_id' => $this->session->userdata('id_user'),
                'uang' => '1'
            );
            $this->db->insert('pembelian_retur_penerimaan', $data_retur_pembelian_penerimaan);
            $id_retur = $this->db->insert_id();
            
            //$total = post_safe('total'); // yg asli disini
            
            $jurnal_1 = array(
                'waktu' => date("Y-m-d H:i:s"),
                'id_transaksi' => $id_retur,
                'jenis_transaksi' => 'Penerimaan Retur Pembelian',
                'ket_transaksi' => '',
                'id_sub_sub_sub_sub_rekening' => '1',
                'debet' => $total
            );
            $this->db->insert('jurnal', $jurnal_1);
            
            $jurnal_84 = array(
                'waktu' => date("Y-m-d H:i:s"),
                'id_transaksi' => $id_retur,
                'jenis_transaksi' => 'Penerimaan Retur Pembelian',
                'ket_transaksi' => '',
                'id_sub_sub_sub_sub_rekening' => '267',
                'kredit' => $total
            );
            $this->db->insert('jurnal', $jurnal_84);
        }
        if ($berupa == 'barang') {

            $data_retur_pembelian_penerimaan = array(
                'retur_id' => post_safe('noretur'),
                'pegawai_penduduk_id' => $this->session->userdata('id_user'),
                'uang' => '0'
            );
            $this->db->insert('pembelian_retur_penerimaan', $data_retur_pembelian_penerimaan);
            $id_retur = $this->db->insert_id();
            
            $id_pb = post_safe('id_pb');
            $ed = post_safe('ed');
            $jumlah = post_safe('jml');
            //$total = 0;
            foreach ($id_pb as $key => $data) {
                $rows = $this->db->query("select * from transaksi_detail where barang_packing_id = '$data' and ed = '".date2mysql($ed[$key])."' and unit_id = '".$this->session->userdata('id_unit')."' and transaksi_jenis != 'Pemesanan' order by waktu desc limit 1")->row();
                //$sisa = $rows->sisa + $jumlah[$key];
                $data_trans = array(
                    'transaksi_id' => $id_retur,
                    'transaksi_jenis' => 'Penerimaan Retur Pembelian',
                    'waktu' => datetime2mysql(post_safe('tanggal')).":".date("s"),
                    'barang_packing_id' => $data,
                    'unit_id' => $this->session->userdata('id_unit'),
                    'ed' => date2mysql($ed[$key]),
                    'harga' => (isset($rows->harga)?$rows->harga:'0'),
                    'ppn' => (isset($rows->ppn)?$rows->ppn:'0'),
                    'hna' => (isset($rows->hna)?$rows->hna:'0'),
                    'hpp' => (isset($rows->hpp)?$rows->hpp:'0'),
                    'het' => (isset($rows->het)?$rows->het:'0'),
                    'masuk' => $jumlah[$key],
                    'keluar' => '0'
                );
                $this->db->insert('transaksi_detail', $data_trans);
            }
            
            if ($kategori[$key] === 'Alat Kesehatan') {
                $id_rekening = '34';
            }
            if ($kategori[$key] === 'Obat') {
                $id_rekening = '188';
            }
            if ($kategori[$key] === 'Bahan Baku Obat') {
                $id_rekening = '189';
            }
            if ($kategori[$key] === 'BHP') {
                $id_rekening = '191';
            }
            if ($kategori[$key] === 'Reagen Laboratorium') {
                $id_rekening = '192';
            }
            if ($kategori[$key] === 'Bahan Radiologi') {
                $id_rekening = '193';
            }
            if ($kategori[$key] === 'Alat Fisioterapi') {
                $id_rekening = '194';
            }
            if ($kategori[$key] === 'Gas Medis') {
                $id_rekening = '206';
            }
            if ($kategori[$key] === 'Alat Tulis Kantor') {
                $id_rekening = '51';
            }
            if ($kategori[$key] === 'Barang Percetakan') {
                $id_rekening = '195';
            }
            if ($kategori[$key] === 'Linen') {
                $id_rekening = '196';
            }
            if ($kategori[$key] === 'Kelontong') {
                $id_rekening = '197';
            }
            if ($kategori[$key] === 'Lain-lain') {
                $id_rekening = '198';
            }
            if ($kategori[$key] === 'Bahan Makanan Kering') {
                $id_rekening = '199';
            }
            if ($kategori[$key] === 'Bahan Makanan Basah') {
                $id_rekening = '200';
            }
            
            $jurnal_84 = array(
                'waktu' => date("Y-m-d H:i:s"),
                'id_transaksi' => $id_retur,
                'jenis_transaksi' => 'Penerimaan Retur Pembelian',
                'ket_transaksi' => '',
                'id_sub_sub_sub_sub_rekening' => $id_rekening,
                'debet' => $total
            );
            $this->db->insert('jurnal', $jurnal_84);

            $jurnal_265 = array(
                'waktu' => date("Y-m-d H:i:s"),
                'id_transaksi' => $id_retur,
                'jenis_transaksi' => 'Penerimaan Retur Pembelian',
                'ket_transaksi' => '',
                'id_sub_sub_sub_sub_rekening' => '84',
                'kredit' => $total
            );
            $this->db->insert('jurnal', $jurnal_265);
        }
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $status = FALSE;
        } else {
            $this->db->trans_commit();
            $status = TRUE;
        }
        $result['status'] = $status;
        $result['id_penerimaan_retur'] = $id_retur;
        return $result;
    }
    
    function retur_penjualan_save() {
        $this->db->trans_begin();
        $id_pembeli = post_safe('idpembeli');
        $id_pb = post_safe('id_pb');
        $jml_retur = post_safe('jml_retur');
        $ed = post_safe('ed');
        $jenis = post_safe('jenis');
        $harga = post_safe('harga');
        $kategori = post_safe('kategori');
        $id_retur = post_safe('id_retur_penjualan');
        $data_retur = array(
            'penjualan_id' => post_safe('id_penjualan'),
            'pembeli_penduduk_id' => ($id_pembeli != null)?$id_pembeli:NULL,
            'pegawai_penduduk_id' => $this->session->userdata('id_user')
        );
        
        if ($id_retur === '') {
            $this->db->insert('penjualan_retur', $data_retur);
            $id = $this->db->insert_id();

            foreach ($id_pb as $key => $data) {
                if ($data != '') {
                $jml = $this->db->query("select * from transaksi_detail where transaksi_jenis = 'Penjualan' and barang_packing_id = '$data' and ed = '$ed[$key]' and transaksi_id = '".post_safe('id_penjualan')."'")->row();
                //$sisa= (isset($jml->sisa)?$jml->sisa:'0') + $jml_retur[$key];
                $data_trans = array(
                        'transaksi_id' => $id,
                        'transaksi_jenis' => 'Retur Penjualan',
                        'waktu' => datetime2mysql(post_safe('tanggal')).":".date("s"),
                        'barang_packing_id' => $data,
                        'unit_id' => $this->session->userdata('id_unit'),
                        'ed' => $jml->ed,
                        'harga' => $jml->harga,
                        'ppn' => $jml->ppn,
                        'hna' => $jml->hna,
                        'hpp' => $jml->hpp,
                        'het' => $jml->het,
                        'masuk' => $jml_retur[$key],
                        'keluar' => '0',
                        'ppn_jual' => $jml->ppn_jual
                    );
                    $this->db->insert('transaksi_detail', $data_trans);
                    if ($jenis[$key] === 'Farmasi') {
                        if ($kategori[$key] === 'Alat Kesehatan') {
                            $id_rekening = '34';
                        }
                        if ($kategori[$key] === 'Obat') {
                            $id_rekening = '188';
                        }
                        if ($kategori[$key] === 'Bahan Baku Obat') {
                            $id_rekening = '189';
                        }
                        if ($kategori[$key] === 'BHP') {
                            $id_rekening = '191';
                        }
                        if ($kategori[$key] === 'Reagen Laboratorium') {
                            $id_rekening = '192';
                        }
                        if ($kategori[$key] === 'Bahan Radiologi') {
                            $id_rekening = '193';
                        }
                        if ($kategori[$key] === 'Alat Fisioterapi') {
                            $id_rekening = '194';
                        }
                        if ($kategori[$key] === 'Gas Medis') {
                            $id_rekening = '206';
                        }
                    } else if ($jenis[$key] === 'Rumah Tangga') {
                        if ($kategori[$key] === 'Alat Tulis Kantor') {
                            $id_rekening = '51';
                        }
                        if ($kategori[$key] === 'Barang Percetakan') {
                            $id_rekening = '195';
                        }
                        if ($kategori[$key] === 'Linen') {
                            $id_rekening = '196';
                        }
                        if ($kategori[$key] === 'Kelontong') {
                            $id_rekening = '197';
                        }
                        if ($kategori[$key] === 'Lain-lain') {
                            $id_rekening = '198';
                        }
                    }
                    else if ($jenis[$key] === 'Gizi') {
                        if ($kategori[$key] === 'Bahan Makanan Kering') {
                            $id_rekening = '199';
                        }
                        if ($kategori[$key] === 'Bahan Makanan Basah') {
                            $id_rekening = '200';
                        }
                    }
                    $jurnal_84 = array(
                        'waktu' => date("Y-m-d H:i:s"),
                        'id_transaksi' => $id,
                        'jenis_transaksi' => 'Retur Penjualan',
                        'ket_transaksi' => '',
                        'id_sub_sub_sub_sub_rekening' => $id_rekening,
                        'debet' => ($jml_retur[$key]*$harga[$key])
                    );
                    $this->db->insert('jurnal', $jurnal_84);
                    
                    $jurnal_265 = array(
                        'waktu' => date("Y-m-d H:i:s"),
                        'id_transaksi' => $id,
                        'jenis_transaksi' => 'Retur Penjualan',
                        'ket_transaksi' => '',
                        'id_sub_sub_sub_sub_rekening' => '265',
                        'kredit' => ($jml_retur[$key]*$harga[$key])
                    );
                    $this->db->insert('jurnal', $jurnal_265);
                }
            }
            $result['action'] = 'add';
        } else {
            $this->db->where('id', $id_retur);
            $this->db->update('penjualan_retur', $data_retur);
            $this->db->delete('transaksi_detail', array('transaksi_jenis' => 'Retur Penjualan', 'transaksi_id' => $id_retur));
            $this->db->delete('jurnal', array('jenis_transaksi' => 'Retur Penjualan', 'id_transaksi' => $id_retur));
            $id = $this->db->insert_id();

            foreach ($id_pb as $key => $data) {
                if ($data != '') {
                $jml = $this->db->query("select * from transaksi_detail where transaksi_jenis != 'Pemesanan' and barang_packing_id = '$data' and ed = '$ed[$key]' and unit_id = '".$this->session->userdata('id_unit')."' order by waktu desc limit 1")->row();
                //$sisa= (isset($jml->sisa)?$jml->sisa:'0') + $jml_retur[$key];
                $data_trans = array(
                        'transaksi_id' => $id,
                        'transaksi_jenis' => 'Retur Penjualan',
                        'waktu' => datetime2mysql(post_safe('tanggal')).":".date("s"),
                        'barang_packing_id' => $data,
                        'unit_id' => $this->session->userdata('id_unit'),
                        'ed' => $jml->ed,
                        'harga' => $jml->harga,
                        'ppn' => $jml->ppn,
                        'hna' => $jml->hna,
                        'hpp' => $jml->hpp,
                        'het' => $jml->het,
                        'masuk' => $jml_retur[$key],
                        'keluar' => '0'
                    );
                    $this->db->insert('transaksi_detail', $data_trans);
                    if ($jenis[$key] === 'Farmasi') {
                        if ($kategori[$key] === 'Alat Kesehatan') {
                            $id_rekening = '34';
                        }
                        if ($kategori[$key] === 'Obat') {
                            $id_rekening = '188';
                        }
                        if ($kategori[$key] === 'Bahan Baku Obat') {
                            $id_rekening = '189';
                        }
                        if ($kategori[$key] === 'BHP') {
                            $id_rekening = '191';
                        }
                        if ($kategori[$key] === 'Reagen Laboratorium') {
                            $id_rekening = '192';
                        }
                        if ($kategori[$key] === 'Bahan Radiologi') {
                            $id_rekening = '193';
                        }
                        if ($kategori[$key] === 'Alat Fisioterapi') {
                            $id_rekening = '194';
                        }
                        if ($kategori[$key] === 'Gas Medis') {
                            $id_rekening = '206';
                        }
                    } else if ($jenis[$key] === 'Rumah Tangga') {
                        if ($kategori[$key] === 'Alat Tulis Kantor') {
                            $id_rekening = '51';
                        }
                        if ($kategori[$key] === 'Barang Percetakan') {
                            $id_rekening = '195';
                        }
                        if ($kategori[$key] === 'Linen') {
                            $id_rekening = '196';
                        }
                        if ($kategori[$key] === 'Kelontong') {
                            $id_rekening = '197';
                        }
                        if ($kategori[$key] === 'Lain-lain') {
                            $id_rekening = '198';
                        }
                    }
                    else if ($jenis[$key] === 'Gizi') {
                        if ($kategori[$key] === 'Bahan Makanan Kering') {
                            $id_rekening = '199';
                        }
                        if ($kategori[$key] === 'Bahan Makanan Basah') {
                            $id_rekening = '200';
                        }
                    }
                    $jurnal_84 = array(
                        'waktu' => date("Y-m-d H:i:s"),
                        'id_transaksi' => $id_retur,
                        'jenis_transaksi' => 'Retur Penjualan',
                        'ket_transaksi' => '',
                        'id_sub_sub_sub_sub_rekening' => $id_rekening,
                        'debet' => ($jml_retur[$key]*$harga[$key])
                    );
                    $this->db->insert('jurnal', $jurnal_84);
                    
                    $jurnal_265 = array(
                        'waktu' => date("Y-m-d H:i:s"),
                        'id_transaksi' => $id_retur,
                        'jenis_transaksi' => 'Retur Penjualan',
                        'ket_transaksi' => '',
                        'id_sub_sub_sub_sub_rekening' => '265',
                        'kredit' => ($jml_retur[$key]*$harga[$key])
                    );
                    $this->db->insert('jurnal', $jurnal_265);
                }
            }
            $result['action'] = 'edit';
        }
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $status = FALSE;
        } else {
            $this->db->trans_commit();
            $status = TRUE;
        }
        $result['status'] = $status;
        $result['id_retur_penjualan'] = $id;
        return $result;
    }
    
    function reretur_penjualan_save() {
        $this->db->trans_begin();
        $berupa = post_safe('berupa');
        $total = post_safe('totalreretur');
        $jenis = post_safe('jenis');
        $kategori = post_safe('kategori');
        
        if ($berupa == 'uang') {
            $data_retur_penjualan_pengeluaran = array(
                'penjualan_retur_id' => post_safe('noretur'),
                'pegawai_penduduk_id' => $this->session->userdata('id_user'),
                'uang' => '1'
            );
            $this->db->insert('penjualan_retur_pengeluaran', $data_retur_penjualan_pengeluaran);
            $id_retur = $this->db->insert_id();
            
            $jurnal_84 = array(
                'waktu' => date("Y-m-d H:i:s"),
                'id_transaksi' => $id_retur,
                'jenis_transaksi' => 'Pengeluaran Retur Penjualan',
                'ket_transaksi' => '',
                'id_sub_sub_sub_sub_rekening' => '265',
                'debet' => $total
            );
            $this->db->insert('jurnal', $jurnal_84);

            $jurnal_265 = array(
                'waktu' => date("Y-m-d H:i:s"),
                'id_transaksi' => $id_retur,
                'jenis_transaksi' => 'Pengeluaran Retur Penjualan',
                'ket_transaksi' => '',
                'id_sub_sub_sub_sub_rekening' => '1',
                'kredit' => $total
            );
            $this->db->insert('jurnal', $jurnal_265);
        }
        if ($berupa == 'barang') {

            $data_retur_penjualan_pengeluaran = array(
                'penjualan_retur_id' => post_safe('noretur'),
                'pegawai_penduduk_id' => $this->session->userdata('id_user'),
                'uang' => '0'
            );
            $this->db->insert('penjualan_retur_pengeluaran', $data_retur_penjualan_pengeluaran);
            $id_retur = $this->db->insert_id();
            
            $id_pb = post_safe('id_pb');
            $ed = post_safe('ed');
            $jumlah = post_safe('jml');
            
            foreach ($id_pb as $key => $data) {
                $rows = $this->db->query("select * from transaksi_detail where barang_packing_id = '$data' and ed = '".$ed[$key]."' and unit_id = '".$this->session->userdata('id_unit')."' and transaksi_jenis != 'Pemesanan' order by waktu desc limit 1")->row();
                //$sisa = $rows->sisa - $jumlah[$key];
                $data_trans = array(
                    'transaksi_id' => $id_retur,
                    'transaksi_jenis' => 'Pengeluaran Retur Penjualan',
                    'waktu' => datetime2mysql(post_safe('tanggal')).":".date("s"),
                    'barang_packing_id' => $data,
                    'unit_id' => $this->session->userdata('id_unit'),
                    'ed' => $rows->ed,
                    'harga' => $rows->harga,
                    'ppn' => $rows->ppn,
                    'hna' => $rows->hna,
                    'hpp' => $rows->hpp,
                    'het' => $rows->het,
                    'masuk' => '0',
                    'keluar' => $jumlah[$key],
                );
                $this->db->insert('transaksi_detail', $data_trans);
                
                if ($jenis[$key] === 'Farmasi') {
                    if ($kategori[$key] === 'Alat Kesehatan') {
                        $id_rekening = '34';
                    }
                    if ($kategori[$key] === 'Obat') {
                        $id_rekening = '188';
                    }
                    if ($kategori[$key] === 'Bahan Baku Obat') {
                        $id_rekening = '189';
                    }
                    if ($kategori[$key] === 'BHP') {
                        $id_rekening = '191';
                    }
                    if ($kategori[$key] === 'Reagen Laboratorium') {
                        $id_rekening = '192';
                    }
                    if ($kategori[$key] === 'Bahan Radiologi') {
                        $id_rekening = '193';
                    }
                    if ($kategori[$key] === 'Alat Fisioterapi') {
                        $id_rekening = '194';
                    }
                    if ($kategori[$key] === 'Gas Medis') {
                        $id_rekening = '206';
                    }
                } else if ($jenis[$key] === 'Rumah Tangga') {
                    if ($kategori[$key] === 'Alat Tulis Kantor') {
                        $id_rekening = '51';
                    }
                    if ($kategori[$key] === 'Barang Percetakan') {
                        $id_rekening = '195';
                    }
                    if ($kategori[$key] === 'Linen') {
                        $id_rekening = '196';
                    }
                    if ($kategori[$key] === 'Kelontong') {
                        $id_rekening = '197';
                    }
                    if ($kategori[$key] === 'Lain-lain') {
                        $id_rekening = '198';
                    }
                }
                else if ($jenis[$key] === 'Gizi') {
                    if ($kategori[$key] === 'Bahan Makanan Kering') {
                        $id_rekening = '199';
                    }
                    if ($kategori[$key] === 'Bahan Makanan Basah') {
                        $id_rekening = '200';
                    }
                }
                $jurnal_84 = array(
                    'waktu' => date("Y-m-d H:i:s"),
                    'id_transaksi' => $id_retur,
                    'jenis_transaksi' => 'Pengeluaran Retur Penjualan',
                    'ket_transaksi' => '',
                    'id_sub_sub_sub_sub_rekening' => $id_rekening,
                    'debet' => $total
                );
                $this->db->insert('jurnal', $jurnal_84);

                $jurnal_265 = array(
                    'waktu' => date("Y-m-d H:i:s"),
                    'id_transaksi' => $id_retur,
                    'jenis_transaksi' => 'Pengeluaran Retur Penjualan',
                    'ket_transaksi' => '',
                    'id_sub_sub_sub_sub_rekening' => '265',
                    'kredit' => $total
                );
                $this->db->insert('jurnal', $jurnal_265);
                
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                }
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
        $result['id_pengeluaran_retur'] = $id_retur;
        return $result;
    }
    
    function penjualan_save() {
        
        $id = post_safe('id_penjualan');
        //$cek = $this->db->query("select count(*) as jumlah from penjualan where resep_id = '".post_safe('id_resep')."'")->row();
        $resep = post_safe('id_resep');
        $kunjungan = $this->db->query("select no_daftar as id FROM pendaftaran where pasien = '".post_safe('id_pasien')."' order by no_daftar desc limit 1")->row();
        $id_billing = $kunjungan->id;
        $data_penjualan = array(
            'pegawai_penduduk_id' => $this->session->userdata('id_user'),
            'no_daftar' => $id_billing,
            'resep_id' => $resep,
            'total' => currencyToNumber(post_safe('total'))
        );
        $ed = post_safe('ed');
        $id_pb = post_safe('id_pb');
        $subtotal = post_safe('subtotal');
        $jumlah = post_safe('jl');
        $disc = post_safe('disc');
        $harga_jual = post_safe('harga_jual');
        $ppn_jual = post_safe('ppn_jual');
        $id_kategori = post_safe('id_kategori');
        $jenis = post_safe('jenis');
        //$this->session->set_userdata(array('sisa_stok' => NULL));
        if ($id === '') {
            $this->db->trans_begin();
            
            $this->db->insert('penjualan', $data_penjualan);
            $id_penjualan = $this->db->insert_id();
            
            $hrg_jual = 0;
            foreach ($id_pb as $key => $data) {
                if ($data != '') {
                $terdiskon = $harga_jual[$key] - ($harga_jual[$key]*($disc[$key]/100));
                $hrg_jual = $hrg_jual+($terdiskon*$jumlah[$key]);
                $jml = $this->db->query("select * from transaksi_detail 
                    WHERE barang_packing_id = '$data' and transaksi_jenis != 'Pemesanan'
                    and ed = '$ed[$key]' order by waktu desc limit 1")->row();
                
                //$sisa = (isset($jml->sisa)?$jml->sisa:'0')-$jumlah[$key];
                
                    $leadtime = $this->db->query("select leadtime_hours from transaksi_detail 
                    where unit_id = '".$this->session->userdata('id_unit')."' and barang_packing_id = '$data' and transaksi_jenis = 'Pembelian'
                        order by waktu desc limit 1")->row();
                    
                    $marg= $this->db->query("select margin from barang_packing where id = '$id_pb[$key]]'")->row();
                    $data_trans = array(
                        'transaksi_id' => $id_penjualan,
                        'transaksi_jenis' => 'Penjualan',
                        'waktu' => datetime2mysql(post_safe('tanggal')).":".date("s"),
                        'barang_packing_id' => $id_pb[$key],
                        'unit_id' => $this->session->userdata('id_unit'),
                        'ed' => (isset($jml->ed)?$jml->ed:'2014-08-12'),
                        'harga' => (isset($jml->harga)?$jml->harga:'0'),
                        'margin_percentage' => $marg->margin,
                        'jual_diskon_percentage' => $disc[$key],
                        'terdiskon_harga' => $terdiskon,
                        'subtotal' => currencyToNumber($subtotal[$key]),
                        'ppn' => (isset($jml->ppn)?$jml->ppn:'0'),
                        'hna' => (isset($jml->hna)?$jml->hna:'0'),
                        'hpp' => (isset($jml->hpp)?$jml->hpp:'0'),
                        'het' => (isset($jml->het)?$jml->het:'0'),
                        'masuk' => '0',
                        'keluar' => $jumlah[$key],
                        'leadtime_hours' => (isset($leadtime->loadtime_hours)?$leadtime->loadtime_hours:'0'),
                        'ppn_jual' => $ppn_jual[$key]
                    );
                    $this->db->insert('transaksi_detail', $data_trans); 
                    /*HItung PPN JUAL HUTANG PPN*/
                    $ppn = (isset($jml->ppn)?$jml->ppn:'0');
                    $pendapatan_sblm_ppn = currencyToNumber($subtotal[$key])/(($ppn/100)+1);
                    $ppn_penjualan = currencyToNumber($subtotal[$key])-$pendapatan_sblm_ppn;
                    
                    $get = $this->db->query("select harga, ppn from transaksi_detail where transaksi_jenis = 'Pembelian' and barang_packing_id = '$id_pb[$key]' order by waktu desc limit 1")->row();
                    $hrg_htg = (isset($get->harga)?$get->harga:'0');
                    $ppn_htg = (isset($get->ppn)?$get->ppn:'0');
                    $ppn_beli_dari_yg_terjual = $jumlah[$key]*($hrg_htg*($ppn_htg/100));
                    $hutang_ppn = $ppn_penjualan - $ppn_beli_dari_yg_terjual;
                    /*End of hitung PPN JUAL HUTANG PPN*/
                    /*JURNAL PPN JUAL HUTANG PPN*/
                    if ($hutang_ppn > 0) {
                        $jurnal_1 = array(
                            'waktu' => date("Y-m-d H:i:s"),
                            'id_transaksi' => $id_penjualan,
                            'jenis_transaksi' => 'Penjualan',
                            'ket_transaksi' => '',
                            'id_sub_sub_sub_sub_rekening' => '231',
                            'debet' => $hutang_ppn
                        );
                        $this->db->insert('jurnal', $jurnal_1);

                        $jurnal_2 = array(
                            'waktu' => date("Y-m-d H:i:s"),
                            'id_transaksi' => $id_penjualan,
                            'jenis_transaksi' => 'Penjualan',
                            'ket_transaksi' => '',
                            'id_sub_sub_sub_sub_rekening' => '271',
                            'kredit' => $hutang_ppn
                        );
                        $this->db->insert('jurnal', $jurnal_2);
                    }
                    /*END OF JURNAL PPN JUAL HUTANG PPN*/
                    if ($id_kategori[$key] === '1') {
                        $jurnal_218 = array(
                            'waktu' => date("Y-m-d H:i:s"),
                            'id_transaksi' => $id_penjualan,
                            'jenis_transaksi' => 'Penjualan',
                            'ket_transaksi' => '',
                            'id_sub_sub_sub_sub_rekening' => '218',
                            'debet' => (isset($jml->hna)?($jml->hna*$jumlah[$key]):'0')
                        );
                        $this->db->insert('jurnal', $jurnal_218); // beban pemakaian sediaan obat (HNA)
                        
                        $jurnal_188 = array(
                            'waktu' => date("Y-m-d H:i:s"),
                            'id_transaksi' => $id_penjualan,
                            'jenis_transaksi' => 'Penjualan',
                            'ket_transaksi' => '',
                            'id_sub_sub_sub_sub_rekening' => '188',
                            'kredit' => (isset($jml->hna)?($jml->hna*$jumlah[$key]):'0')
                        );
                        $this->db->insert('jurnal', $jurnal_188); // Persediaan obat (HNA)
                    }
                    if ($id_kategori[$key] === '2') {
                        $jurnal_130 = array(
                            'waktu' => date("Y-m-d H:i:s"),
                            'id_transaksi' => $id_penjualan,
                            'jenis_transaksi' => 'Penjualan',
                            'ket_transaksi' => '',
                            'id_sub_sub_sub_sub_rekening' => '130',
                            'debet' => (isset($jml->hna)?($jml->hna*$jumlah[$key]):'0')
                        );
                        $this->db->insert('jurnal', $jurnal_130);
                        
                        $jurnal_34 = array(
                            'waktu' => date("Y-m-d H:i:s"),
                            'id_transaksi' => $id_penjualan,
                            'jenis_transaksi' => 'Penjualan',
                            'ket_transaksi' => '',
                            'id_sub_sub_sub_sub_rekening' => '34',
                            'kredit' => (isset($jml->hna)?($jml->hna*$jumlah[$key]):'0')
                        );
                        $this->db->insert('jurnal', $jurnal_34);
                    }
                }
            }
            
            $biaya_apt = 0;
            $array = $this->db->query("select r.*, t.nominal, rs.id_pelayanan_kunjungan from resep_r r join resep rs on (r.resep_id = rs.id) join tarif t on (r.tarif_id = t.id) where resep_id = '$resep'")->result();
            foreach ($array as $key => $data) {
                $biaya_apt = $biaya_apt + $data->nominal;
                $tabel_tarif = $this->db->query("select * from tarif where id = '".$data->tarif_id."' ")->row();
                
                $data_jasa_penjualan = array(
                    'waktu' => datetime2mysql(post_safe('tanggal')).":".date("s"),
                    'id_pelayanan_kunjungan' => $data->id_pelayanan_kunjungan,
                    'id_kepegawaian_nakes' => NULL,
                    'tarif_id' => $data->tarif_id,
                    'frekuensi' => 1
                );
                $this->db->insert('jasa_penjualan_detail', $data_jasa_penjualan);
            }
            
            if (post_safe('unit_layanan') === 'Poliklinik') {
                $jurnal_23x = array(
                    'waktu' => date("Y-m-d H:i:s"),
                    'id_transaksi' => $id_penjualan,
                    'jenis_transaksi' => 'Penjualan',
                    'ket_transaksi' => '',
                    'id_sub_sub_sub_sub_rekening' => '231',
                    'debet' => $biaya_apt
                );
                $this->db->insert('jurnal', $jurnal_23x); // piutang pasien perseorangan (Jasa Apoteker)
                
                $jurnal_23y = array(
                    'waktu' => date("Y-m-d H:i:s"),
                    'id_transaksi' => $id_penjualan,
                    'jenis_transaksi' => 'Penjualan',
                    'ket_transaksi' => '',
                    'id_sub_sub_sub_sub_rekening' => '102',
                    'kredit' => $biaya_apt
                );
                $this->db->insert('jurnal', $jurnal_23y); // pendapatan penunjang poliklinik (Jasa Apoteker)
                
                $jurnal_c = array(
                    'waktu' => date("Y-m-d H:i:s"),
                    'id_transaksi' => $id_penjualan,
                    'jenis_transaksi' => 'Penjualan',
                    'ket_transaksi' => '',
                    'id_sub_sub_sub_sub_rekening' => '103',
                    'kredit' => $hrg_jual
                );
                $this->db->insert('jurnal', $jurnal_c); // Pendapatan Penjualan Barang Poliklinik (HJual)
                
                $jurnal_d = array(
                    'waktu' => date("Y-m-d H:i:s"),
                    'id_transaksi' => $id_penjualan,
                    'jenis_transaksi' => 'Penjualan',
                    'ket_transaksi' => '',
                    'id_sub_sub_sub_sub_rekening' => '231',
                    'debet' => $hrg_jual
                );
                $this->db->insert('jurnal', $jurnal_d); // Piutang Pasien/Perseorangan (HJual)
                
            }
            else if (post_safe('unit_layanan') === 'IGD') {
                $jurnal_23x = array(
                    'waktu' => date("Y-m-d H:i:s"),
                    'id_transaksi' => $id_penjualan,
                    'jenis_transaksi' => 'Penjualan',
                    'ket_transaksi' => '',
                    'id_sub_sub_sub_sub_rekening' => '231',
                    'debet' => $biaya_apt
                );
                $this->db->insert('jurnal', $jurnal_23x); // piutang pasien perseorangan (Jasa Apoteker)
                
                $jurnal_23y = array(
                    'waktu' => date("Y-m-d H:i:s"),
                    'id_transaksi' => $id_penjualan,
                    'jenis_transaksi' => 'Penjualan',
                    'ket_transaksi' => '',
                    'id_sub_sub_sub_sub_rekening' => '108',
                    'kredit' => $biaya_apt
                );
                $this->db->insert('jurnal', $jurnal_23y); // Pendapatan Penunjang Gawat Darurat (Jasa Apoteker)
                
                $jurnal_c = array(
                    'waktu' => date("Y-m-d H:i:s"),
                    'id_transaksi' => $id_penjualan,
                    'jenis_transaksi' => 'Penjualan',
                    'ket_transaksi' => '',
                    'id_sub_sub_sub_sub_rekening' => '109',
                    'kredit' => $hrg_jual
                );
                $this->db->insert('jurnal', $jurnal_c); // Pendapatan Penjualan Barang Gawat Darurat (HJual)
                
                $jurnal_d = array(
                    'waktu' => date("Y-m-d H:i:s"),
                    'id_transaksi' => $id_penjualan,
                    'jenis_transaksi' => 'Penjualan',
                    'ket_transaksi' => '',
                    'id_sub_sub_sub_sub_rekening' => '231',
                    'debet' => $hrg_jual
                );
                $this->db->insert('jurnal', $jurnal_d); // Piutang Pasien/Perseorangan (HJual)
            }
            else {
                $jurnal_23y = array(
                    'waktu' => date("Y-m-d H:i:s"),
                    'id_transaksi' => $id_penjualan,
                    'jenis_transaksi' => 'Penjualan',
                    'ket_transaksi' => '',
                    'id_sub_sub_sub_sub_rekening' => '113',
                    'kredit' => $biaya_apt
                );
                $this->db->insert('jurnal', $jurnal_23y);
            }
            
            $array_update_stok = $this->db->query("select r.resep_id, rrr.* from resep_r r join resep_racik_r_detail rrr on (r.id = rrr.r_resep_id) where resep_id = '$resep'")->result();
            foreach ($array_update_stok as $rows) {
                
                $harga= $this->db->query("select td.*, bp.margin, bp.diskon from transaksi_detail td 
                        join barang_packing bp on (td.barang_packing_id = bp.id) where td.barang_packing_id = '".$rows->barang_packing_id."' and 
                        td.transaksi_jenis != 'Pemesanan' order by td.waktu desc limit 1")->row();
                if (isset($harga->hna)) {
                    $hjual = (($harga->hna*($harga->margin/100))+$harga->hna) - (($harga->diskon/100)*$harga->hna);
                    $update_harga = array(
                        'jual_harga' => $hjual
                    );
                    $this->db->where(array('r_resep_id' => $rows->r_resep_id, 'barang_packing_id' => $rows->barang_packing_id));
                    $this->db->update('resep_racik_r_detail', $update_harga);
                }
            }
            
            

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $status = FALSE;
            } else {
                $this->db->trans_commit();
                $status = TRUE;
            }
            $result['action'] = 'add';
            $result['status'] = $status;
            $result['id_penjualan'] = $id_penjualan;
        } else { // jika penjualan update
            $this->db->where('id', $id);
            $this->db->update('penjualan', $data_penjualan);
            $this->db->delete('transaksi_detail', array('transaksi_jenis' => 'Penjualan', 'transaksi_id' => $id)); // hapus transaksi_detail sblm insert ulang
            $this->db->delete('jurnal', array('jenis_transaksi' => 'Penjualan', 'id_transaksi' => $id));
            $this->db->delete('jurnal', array('jenis_transaksi' => 'Penjualan Jasa', 'id_transaksi' => $id));
            $id_penjualan = $id;
            
            $hrg_jual = 0;
            foreach ($id_pb as $key => $data) {
                if ($data != '') {
                $terdiskon = $harga_jual[$key] - ($harga_jual[$key]*($disc[$key]/100));
                $hrg_jual = $hrg_jual+($terdiskon*$jumlah[$key]);
                $jml = $this->db->query("select * from transaksi_detail 
                    WHERE barang_packing_id = '$data' and transaksi_jenis != 'Pemesanan'
                    and ed = '$ed[$key]' order by waktu desc limit 1")->row();
                
                //$sisa = (isset($jml->sisa)?$jml->sisa:'0')-$jumlah[$key];
                
                    $leadtime = $this->db->query("select leadtime_hours from transaksi_detail 
                    where unit_id = '".$this->session->userdata('id_unit')."' and barang_packing_id = '$data' and transaksi_jenis = 'Pembelian'
                        order by waktu desc limit 1")->row();
                    
                    $marg= $this->db->query("select margin from barang_packing where id = '$id_pb[$key]]'")->row();
                    $data_trans = array(
                        'transaksi_id' => $id_penjualan,
                        'transaksi_jenis' => 'Penjualan',
                        'waktu' => datetime2mysql(post_safe('tanggal')).":".date("s"),
                        'barang_packing_id' => $id_pb[$key],
                        'unit_id' => $this->session->userdata('id_unit'),
                        'ed' => (isset($jml->ed)?$jml->ed:'2014-08-12'),
                        'harga' => (isset($jml->harga)?$jml->harga:'0'),
                        'margin_percentage' => $marg->margin,
                        'jual_diskon_percentage' => $disc[$key],
                        'terdiskon_harga' => $terdiskon,
                        'subtotal' => currencyToNumber($subtotal[$key]),
                        'ppn' => (isset($jml->ppn)?$jml->ppn:'0'),
                        'hna' => (isset($jml->hna)?$jml->hna:'0'),
                        'hpp' => (isset($jml->hpp)?$jml->hpp:'0'),
                        'het' => (isset($jml->het)?$jml->het:'0'),
                        'masuk' => '0',
                        'keluar' => $jumlah[$key],
                        'leadtime_hours' => (isset($leadtime->loadtime_hours)?$leadtime->loadtime_hours:'0'),
                        'ppn_jual' => $ppn_jual[$key]
                    );
                    $this->db->insert('transaksi_detail', $data_trans);   
                    
                    /*HItung PPN JUAL HUTANG PPN*/
                    $ppn = (isset($jml->ppn)?$jml->ppn:'0');
                    $pendapatan_sblm_ppn = currencyToNumber($subtotal[$key])/(($ppn/100)+1);
                    $ppn_penjualan = currencyToNumber($subtotal[$key])-$pendapatan_sblm_ppn;
                    
                    $get = $this->db->query("select harga, ppn from transaksi_detail where transaksi_jenis = 'Pembelian' and barang_packing_id = '$id_pb[$key]' order by waktu desc limit 1")->row();
                    $hrg_htg = (isset($get->harga)?$get->harga:'0');
                    $ppn_htg = (isset($get->ppn)?$get->ppn:'0');
                    $ppn_beli_dari_yg_terjual = $jumlah[$key]*($hrg_htg*($ppn_htg/100));
                    $hutang_ppn = $ppn_penjualan - $ppn_beli_dari_yg_terjual;
                    /*End of hitung PPN JUAL HUTANG PPN*/
                    /*JURNAL PPN JUAL HUTANG PPN*/
                    $jurnal_1 = array(
                        'waktu' => date("Y-m-d H:i:s"),
                        'id_transaksi' => $id_penjualan,
                        'jenis_transaksi' => 'Penjualan',
                        'ket_transaksi' => '',
                        'id_sub_sub_sub_sub_rekening' => '231',
                        'debet' => $hutang_ppn
                    );
                    $this->db->insert('jurnal', $jurnal_1);

                    $jurnal_2 = array(
                        'waktu' => date("Y-m-d H:i:s"),
                        'id_transaksi' => $id_penjualan,
                        'jenis_transaksi' => 'Penjualan',
                        'ket_transaksi' => '',
                        'id_sub_sub_sub_sub_rekening' => '271',
                        'kredit' => $hutang_ppn
                    );
                    $this->db->insert('jurnal', $jurnal_2);
                    /*END OF JURNAL PPN JUAL HUTANG PPN*/
                    
                    if ($id_kategori[$key] === '1') {
                        $jurnal_218 = array(
                            'waktu' => date("Y-m-d H:i:s"),
                            'id_transaksi' => $id_penjualan,
                            'jenis_transaksi' => 'Penjualan',
                            'ket_transaksi' => '',
                            'id_sub_sub_sub_sub_rekening' => '218',
                            'debet' => (isset($jml->hna)?$jml->hna:'0')
                        );
                        $this->db->insert('jurnal', $jurnal_218);
                        
                        $jurnal_188 = array(
                            'waktu' => date("Y-m-d H:i:s"),
                            'id_transaksi' => $id_penjualan,
                            'jenis_transaksi' => 'Penjualan',
                            'ket_transaksi' => '',
                            'id_sub_sub_sub_sub_rekening' => '188',
                            'kredit' => (isset($jml->hna)?$jml->hna:'0')
                        );
                        $this->db->insert('jurnal', $jurnal_188);
                    }
                    if ($id_kategori[$key] === '2') {
                        $jurnal_130 = array(
                            'waktu' => date("Y-m-d H:i:s"),
                            'id_transaksi' => $id_penjualan,
                            'jenis_transaksi' => 'Penjualan',
                            'ket_transaksi' => '',
                            'id_sub_sub_sub_sub_rekening' => '130',
                            'debet' => (isset($jml->hna)?$jml->hna:'0')
                        );
                        $this->db->insert('jurnal', $jurnal_130);
                        
                        $jurnal_34 = array(
                            'waktu' => date("Y-m-d H:i:s"),
                            'id_transaksi' => $id_penjualan,
                            'jenis_transaksi' => 'Penjualan',
                            'ket_transaksi' => '',
                            'id_sub_sub_sub_sub_rekening' => '34',
                            'kredit' => (isset($jml->hna)?$jml->hna:'0')
                        );
                        $this->db->insert('jurnal', $jurnal_34);
                    }
                }
            }
            
            $biaya_apt = 0;
            $array = $this->db->query("select r.*, t.nominal, rs.id_pelayanan_kunjungan from resep_r r join resep rs on (r.resep_id = rs.id) join tarif t on (r.tarif_id = t.id) where resep_id = '$resep'")->result();
            foreach ($array as $key => $data) {
                $biaya_apt = $biaya_apt + $data->nominal;
                $tabel_tarif = $this->db->query("select * from tarif where id = '".$data->tarif_id."' ")->row();
                
                $data_jasa_penjualan = array(
                    'waktu' => datetime2mysql(post_safe('tanggal')).":".date("s"),
                    'id_pelayanan_kunjungan' => $data->id_pelayanan_kunjungan,
                    'id_kepegawaian_nakes' => NULL,
                    'tarif_id' => $data->tarif_id,
                    'frekuensi' => 1
                );
                $this->db->insert('jasa_penjualan_detail', $data_jasa_penjualan);
            }
            
            if (post_safe('unit_layanan') === 'Poliklinik') {
                $jurnal_23x = array(
                    'waktu' => date("Y-m-d H:i:s"),
                    'id_transaksi' => $id_penjualan,
                    'jenis_transaksi' => 'Penjualan Jasa',
                    'ket_transaksi' => '',
                    'id_sub_sub_sub_sub_rekening' => '231',
                    'debet' => $biaya_apt
                );
                $this->db->insert('jurnal', $jurnal_23x); // piutang pasien perseorangan (Jasa Apoteker)
                
                $jurnal_23y = array(
                    'waktu' => date("Y-m-d H:i:s"),
                    'id_transaksi' => $id_penjualan,
                    'jenis_transaksi' => 'Penjualan Jasa',
                    'ket_transaksi' => '',
                    'id_sub_sub_sub_sub_rekening' => '102',
                    'kredit' => $biaya_apt
                );
                $this->db->insert('jurnal', $jurnal_23y); // pendapatan penunjang poliklinik (Jasa Apoteker)
                
                $jurnal_c = array(
                    'waktu' => date("Y-m-d H:i:s"),
                    'id_transaksi' => $id_penjualan,
                    'jenis_transaksi' => 'Penjualan',
                    'ket_transaksi' => '',
                    'id_sub_sub_sub_sub_rekening' => '103',
                    'debet' => $hrg_jual
                );
                $this->db->insert('jurnal', $jurnal_c); // Pendapatan Penjualan Barang Poliklinik (HJual)
                
                $jurnal_d = array(
                    'waktu' => date("Y-m-d H:i:s"),
                    'id_transaksi' => $id_penjualan,
                    'jenis_transaksi' => 'Penjualan',
                    'ket_transaksi' => '',
                    'id_sub_sub_sub_sub_rekening' => '231',
                    'kredit' => $hrg_jual
                );
                $this->db->insert('jurnal', $jurnal_d); // Piutang Pasien/Perseorangan (HJual)
                
            }
            else if (post_safe('unit_layanan') === 'IGD') {
                $jurnal_23x = array(
                    'waktu' => date("Y-m-d H:i:s"),
                    'id_transaksi' => $id_penjualan,
                    'jenis_transaksi' => 'Penjualan Jasa',
                    'ket_transaksi' => '',
                    'id_sub_sub_sub_sub_rekening' => '231',
                    'debet' => $biaya_apt
                );
                $this->db->insert('jurnal', $jurnal_23x); // piutang pasien perseorangan (Jasa Apoteker)
                
                $jurnal_23y = array(
                    'waktu' => date("Y-m-d H:i:s"),
                    'id_transaksi' => $id_penjualan,
                    'jenis_transaksi' => 'Penjualan Jasa',
                    'ket_transaksi' => '',
                    'id_sub_sub_sub_sub_rekening' => '108',
                    'kredit' => $biaya_apt
                );
                $this->db->insert('jurnal', $jurnal_23y); // Pendapatan Penunjang Gawat Darurat (Jasa Apoteker)
                
                $jurnal_c = array(
                    'waktu' => date("Y-m-d H:i:s"),
                    'id_transaksi' => $id_penjualan,
                    'jenis_transaksi' => 'Penjualan',
                    'ket_transaksi' => '',
                    'id_sub_sub_sub_sub_rekening' => '109',
                    'kredit' => $hrg_jual
                );
                $this->db->insert('jurnal', $jurnal_c); // Pendapatan Penjualan Barang Gawat Darurat (HJual)
                
                $jurnal_d = array(
                    'waktu' => date("Y-m-d H:i:s"),
                    'id_transaksi' => $id_penjualan,
                    'jenis_transaksi' => 'Penjualan',
                    'ket_transaksi' => '',
                    'id_sub_sub_sub_sub_rekening' => '231',
                    'debet' => $hrg_jual
                );
                $this->db->insert('jurnal', $jurnal_d); // Piutang Pasien/Perseorangan (HJual)
            }
            else {
                $jurnal_23y = array(
                    'waktu' => date("Y-m-d H:i:s"),
                    'id_transaksi' => $id_penjualan,
                    'jenis_transaksi' => 'Penjualan Jasa',
                    'ket_transaksi' => '',
                    'id_sub_sub_sub_sub_rekening' => '113',
                    'kredit' => $biaya_apt
                );
                $this->db->insert('jurnal', $jurnal_23y);
            }
            
            $array_update_stok = $this->db->query("select r.resep_id, rrr.* from resep_r r join resep_racik_r_detail rrr on (r.id = rrr.r_resep_id) where resep_id = '$resep'")->result();
            foreach ($array_update_stok as $rows) {
                $harga= $this->db->query("select td.*, bp.margin, bp.diskon from transaksi_detail td 
                        join barang_packing bp on (td.barang_packing_id = bp.id) where td.barang_packing_id = '".$rows->barang_packing_id."' and 
                        td.transaksi_jenis != 'Pemesanan' order by td.waktu desc limit 1")->row();
                if (isset($harga->hna)) {
                    $hjual = (($harga->hna*($harga->margin/100))+$harga->hna) - (($harga->diskon/100)*$harga->hna);
                    $update_harga = array(
                        'jual_harga' => $hjual
                    );
                    $this->db->where(array('r_resep_id' => $rows->r_resep_id, 'barang_packing_id' => $rows->barang_packing_id));
                    $this->db->update('resep_racik_r_detail', $update_harga);
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
            $result['id_penjualan'] = $id_penjualan;
            $result['action'] = 'edit';
        }
        return $result;
    }
    
    function kas_load_data($awal = null, $akhir = null, $jenis = null, $nama = null) {
        $q = null;
        if ($awal != null and $akhir != null) {
            $q.="where date(waktu) between '".date2mysql($awal)."' and '".date2mysql($akhir)."'";
        }
        if ($jenis != null) {
            $q.=" and transaksi_jenis = '$jenis'";
        }
        if ($nama != null) {
            $q.=" and penerimaan_pengeluaran_nama like ('%$nama%')";
        }
        $sql = "select * from kas $q";
        //echo $sql;
        return $this->db->query($sql);
    }
    
    function reimbursement_load_data($awal = null, $akhir = null, $instansi = null) {
        $q = null;
        if ($awal != null and $akhir != null) {
            $q.="and date(kp.waktu) between '".date2mysql($awal)."' and '".date2mysql($akhir)."'";
        }
        if ($instansi != null) {
            $q.="and ris.id = '$instansi'";
        }
        $sql = "
            select p.id, td.waktu, pd.nama as pasien, p.no_polis, p.nominal_tereimburse, kp.total
            from produk_asuransi_pembayaran p
            join kunjungan_billing_pembayaran kp on (p.id_pembayaran = kp.id)
            join pendaftaran pdf on (kp.no_daftar = pdf.no_daftar)
            join pasien ps on (pdf.pasien = ps.no_rm)
            left join penduduk pd on (ps.id = pd.id)
            join transaksi_detail td on (td.transaksi_id = p.id)
            join asuransi_produk ap on (p.id_produk_asuransi = ap.id)
            join relasi_instansi ris on (ap.relasi_instansi_id = ris.id)
            where p.id IS NOT NULL $q group by p.id
        ";
        //echo "<pre>".$sql."</pre>";
        return $this->db->query($sql);
    }
    function stok_opname_load_data($id) {
        $sql = "
            select os.alasan, td.*, bp.id as id_pb, b.nama as barang, o.kekuatan, o.generik, bp.isi, r.nama as pabrik, 
            s.nama as satuan_terkecil, sto.nama as satuan, sd.nama as sediaan, st.nama as satuan_terbesar, bp.barcode 
            from opname_stok os
            join transaksi_detail td on (os.id = td.transaksi_id)
            join barang_packing bp on (td.barang_packing_id = bp.id)
            join barang b on (bp.barang_id = b.id)
            left join relasi_instansi r on (r.id = b.pabrik_relasi_instansi_id)
            left join obat o on (b.id = o.id)
            left join satuan sto on (sto.id = o.satuan_id)
            left join satuan s on (s.id = bp.terkecil_satuan_id)
            left join satuan st on (st.id = bp.terbesar_satuan_id)
            left join sediaan sd on (sd.id = o.sediaan_id)
            where os.id = '$id' and td.transaksi_jenis = 'Stok Opname'
        ";
        //echo "<pre>".$sql."</pre>";
        return $this->db->query($sql);
    }
    
    function pemusnahan_load_data($id = null) {
        $q = null;
        if ($id != null) {
            $q.="and p.id = '$id'";
        }
        $sql = "select o.id as id_obat, pd.nama as saksi_apotek, o.generik, pdd.nama as saksi_bpom, td.*, bp.id as id_pb, bp.barcode, 
            bp.margin, bp.diskon, b.nama as barang, p.apotek_saksi_penduduk_id, p.bpom_saksi_penduduk_id,
        st.nama as satuan_terkecil, bp.isi, o.kekuatan, r.nama as pabrik, s.nama as satuan, sd.nama as sediaan from pemusnahan p 
        left join transaksi_detail td on (p.id = td.transaksi_id) 
        join barang_packing bp on (td.barang_packing_id = bp.id) 
        join barang b on (b.id = bp.barang_id) 
        left join obat o on (o.id = b.id) left join satuan s on (s.id = o.satuan_id) 
        left join satuan st on (st.id = bp.terkecil_satuan_id) 
        left join sediaan sd on (sd.id = o.sediaan_id) 
        left join relasi_instansi r on (r.id = b.pabrik_relasi_instansi_id)
        left join penduduk pd on (pd.id = p.apotek_saksi_penduduk_id)
        left join penduduk pdd on (pdd.id = p.bpom_saksi_penduduk_id) where td.transaksi_jenis = 'Pemusnahan' $q";
        ///echo "<pre>".$sql."</pre>";
        return $this->db->query($sql);
    }
    
    function retur_pembelian_load_data($id) {
        $q = null;
        if ($id != null) {
            $q.="and p.id = '$id'";
        }
        $sql = "select bk.nama as kategori, o.id as id_obat, p.id as retur_id, o.generik, td.*, bp.id as id_pb, ri.nama as suplier, 
            bp.barcode, bp.margin, bp.diskon, b.nama as barang, 
            st.nama as satuan_terkecil, bp.isi, ri.id as id_supplier, pd.id as id_sales, o.kekuatan, 
            r.nama as pabrik, pd.nama as salesman, s.nama as satuan, sd.nama as sediaan, p.pembelian_id from pembelian_retur p 
        left join transaksi_detail td on (p.id = td.transaksi_id) 
        join barang_packing bp on (td.barang_packing_id = bp.id) 
        join barang b on (b.id = bp.barang_id) 
        join barang_kategori bk on (bk.id = b.barang_kategori_id)
        left join obat o on (o.id = b.id) left join satuan s on (s.id = o.satuan_id) 
        left join satuan st on (st.id = bp.terkecil_satuan_id) 
        left join sediaan sd on (sd.id = o.sediaan_id) 
        left join relasi_instansi r on (r.id = b.pabrik_relasi_instansi_id)
        left join relasi_instansi ri on (p.suplier_relasi_instansi = ri.id)
        left join penduduk pd on (p.salesman_penduduk_id = pd.id)
        where td.transaksi_jenis = 'Retur Pembelian' $q";
        return $this->db->query($sql);
    }
    
    function reretur_pembelian_load_data($id) {
        $q = null;
        if ($id != null) {
            $q.="and p.id = '$id'";
        }
        $sql = "select p.retur_id, p.id as penerimaan_retur_id, p.uang, bk.jenis, o.id as id_obat, o.generik, 
            td.*, bp.id as id_pb, bp.barcode, bp.margin, bp.diskon, b.nama as barang, ri.nama as suplier,
        st.nama as satuan_terkecil, bp.isi, o.kekuatan, r.nama as pabrik, pd.nama as salesman, s.nama as satuan, bk.nama as nama_kategori,
        sd.nama as sediaan, concat_ws(' ',b.nama,o.kekuatan,s.nama,sd.nama,r.nama,stb.nama,'@',bp.isi,st.nama) as nama_barang
        from pembelian_retur_penerimaan p 
        join pembelian_retur pr on (p.retur_id = pr.id)
        left join transaksi_detail td on (p.id = td.transaksi_id) 
        join barang_packing bp on (td.barang_packing_id = bp.id) 
        join barang b on (b.id = bp.barang_id) 
        join barang_kategori bk on (b.barang_kategori_id = bk.id)
        left join obat o on (o.id = b.id) 
        left join satuan s on (s.id = o.satuan_id) 
        left join satuan st on (st.id = bp.terkecil_satuan_id) 
        left join satuan stb on (stb.id = bp.terbesar_satuan_id) 
        left join sediaan sd on (sd.id = o.sediaan_id) 
        left join relasi_instansi r on (r.id = b.pabrik_relasi_instansi_id)
        left join relasi_instansi ri on (pr.suplier_relasi_instansi = ri.id)
        left join penduduk pd on (pr.salesman_penduduk_id = pd.id)
        where td.transaksi_jenis = 'Penerimaan Retur Pembelian' $q";
        //echo "<pre>".$sql."</pre>";
        return $this->db->query($sql);
    }
    
    function cek_reretur_pembelian($id) {
        $sql = "select * from pembelian_retur_penerimaan WHERE id = '$id'";
        return $this->db->query($sql);
    }
    
    function reretur_pembelian_load_data_uang($id_retur_pembelian) {
        $sql = "select p.*, k.waktu, pdd.nama as pegawai, p.id as penerimaan_retur_id, r.nama as suplier, k.pengeluaran, pd.nama as salesman from pembelian_retur_penerimaan p 
        join pembelian_retur pr on (p.retur_id = pr.id)
        join kas k on (k.transaksi_id = p.id)
        join penduduk pdd on (pdd.id = p.pegawai_penduduk_id)
        join penduduk pd on (pr.salesman_penduduk_id = pd.id)
        join relasi_instansi r on (pr.suplier_relasi_instansi = r.id)
        where p.id = '$id_retur_pembelian'";

//echo $sql;
        return $this->db->query($sql);
    }
    
    function cek_reretur_penjualan($id) {
        $sql = "select * from penjualan_retur_pengeluaran WHERE id = '$id'";
        return $this->db->query($sql);
    }
    
    function reretur_penjualan_load_data($id_retur) {
        $q = null;
        if ($id_retur != null) {
            $q.="and p.id = '$id_retur'";
        }
        $sql = "select p.uang, o.id as id_obat, o.generik, td.*, bp.id as id_pb, bp.barcode, bp.margin, bp.diskon, b.nama as barang,
        st.nama as satuan_terkecil, bp.isi, o.kekuatan, r.nama as pabrik, s.nama as satuan, sd.nama as sediaan from penjualan_retur_pengeluaran p 
        join penjualan_retur pr on (p.penjualan_retur_id = pr.id)
        left join transaksi_detail td on (p.id = td.transaksi_id) 
        join barang_packing bp on (td.barang_packing_id = bp.id) 
        join barang b on (b.id = bp.barang_id) 
        left join obat o on (o.id = b.id) left join satuan s on (s.id = o.satuan_id) 
        left join satuan st on (st.id = bp.terkecil_satuan_id) 
        left join sediaan sd on (sd.id = o.sediaan_id) 
        left join relasi_instansi r on (r.id = b.pabrik_relasi_instansi_id)
        where td.transaksi_jenis = 'Penjualan Retur Pengeluaran' $q";
        //echo "<pre>".$sql."</pre>";
        return $this->db->query($sql);
    }
    
    function reretur_penjualan_load_data_uang($id_retur) {
        $sql = "select p.*, k.waktu, pdd.nama as pegawai, k.pengeluaran from penjualan_retur_pengeluaran p 
        join penjualan_retur pr on (p.penjualan_retur_id = pr.id)
        join kas k on (k.transaksi_id = p.id)
        join penduduk pdd on (pdd.id = p.pegawai_penduduk_id)
        where p.id = '$id_retur'";
        return $this->db->query($sql);
    }
    
    function pemakaian_load_data($id = null) {
        $q = null;
        if ($id != null) {
            $q.="and p.id = '$id'";
        }
        $sql = "select o.id as id_obat, pd.nama as pegawai, bk.id as id_kategori, bk.jenis, o.generik,  td.*, bp.id as id_pb, p.waktu, bp.barcode, bp.margin, bp.diskon, b.nama as barang, 
        st.nama as satuan_terkecil, bp.isi, o.kekuatan, r.nama as pabrik, s.nama as satuan, sd.nama as sediaan from pemakaian p 
        left join transaksi_detail td on (p.id = td.transaksi_id) 
        join barang_packing bp on (td.barang_packing_id = bp.id) 
        join barang b on (b.id = bp.barang_id) 
        join barang_kategori bk on (bk.id = b.barang_kategori_id)
        left join obat o on (o.id = b.id) left join satuan s on (s.id = o.satuan_id) 
        left join satuan st on (st.id = bp.terkecil_satuan_id) 
        left join sediaan sd on (sd.id = o.sediaan_id) 
        left join relasi_instansi r on (r.id = b.pabrik_relasi_instansi_id)
        left join penduduk pd on (pd.id = p.pegawai_penduduk_id)
        where td.transaksi_jenis = 'Pemakaian' $q";
        ///echo "<pre>".$sql."</pre>";
        return $this->db->query($sql);
    }
    
    function distribusi_load_data($id = null) {
        $q = null;
        if ($id != null) {
            $q.="and d.id = '$id'";
        }
        $sql = "select o.id as id_obat, pd.nama as pegawai, u.nama as unit, ut.nama as tujuan, o.generik,  td.*, bp.id as id_pb, bp.barcode, bp.margin, bp.diskon, b.nama as barang, 
        st.nama as satuan_terkecil, stn.nama as satuan_terbesar, bp.isi, o.kekuatan, r.nama as pabrik, s.nama as satuan, sd.nama as sediaan from distribusi d
        join unit u on (d.unit_id = u.id)
        join unit ut on (d.tujuan_unit_id = ut.id)
        left join transaksi_detail td on (d.id = td.transaksi_id) 
        join barang_packing bp on (td.barang_packing_id = bp.id) 
        join barang b on (b.id = bp.barang_id) 
        left join obat o on (o.id = b.id) 
        left join satuan s on (s.id = o.satuan_id) 
        left join satuan stn on (stn.id = bp.terbesar_satuan_id) 
        left join satuan st on (st.id = bp.terkecil_satuan_id) 
        left join sediaan sd on (sd.id = o.sediaan_id) 
        left join relasi_instansi r on (r.id = b.pabrik_relasi_instansi_id)
        left join penduduk pd on (pd.id = d.pegawai_penduduk_id)
        where td.transaksi_jenis = 'Distribusi' $q";
        //echo "<pre>".$sql."</pre>";
        return $this->db->query($sql);
    }
    
    function penerimaan_distribusi_load_data($id = null) {
        $q = null;
        if ($id != null) {
            $q.="and p.id = '$id'";
        }
        $sql = "select d.unit_id as id_unit, d.id as id_distribusi, o.id as id_obat, pd.nama as pegawai, u.nama as unit, ut.nama as tujuan, o.generik,  td.*, bp.id as id_pb, p.distribusi_id, bp.barcode, bp.margin, bp.diskon, b.nama as barang, 
        st.nama as satuan_terkecil, bp.isi, o.kekuatan, r.nama as pabrik, s.nama as satuan, sd.nama as sediaan from distribusi_penerimaan p 
        join distribusi d on (p.distribusi_id = d.id)
        join unit u on (d.unit_id = u.id)
        join unit ut on (d.tujuan_unit_id = ut.id)
        left join transaksi_detail td on (p.id = td.transaksi_id) 
        join barang_packing bp on (td.barang_packing_id = bp.id) 
        join barang b on (b.id = bp.barang_id) 
        left join obat o on (o.id = b.id) left join satuan s on (s.id = o.satuan_id) 
        left join satuan st on (st.id = bp.terkecil_satuan_id) 
        left join sediaan sd on (sd.id = o.sediaan_id) 
        left join relasi_instansi r on (r.id = b.pabrik_relasi_instansi_id)
        left join penduduk pd on (pd.id = p.pegawai_penduduk_id)
        where td.transaksi_jenis = 'Penerimaan Distribusi' $q";
        //echo "<pre>".$sql."</pre>";
        return $this->db->query($sql);
    }
    
    function retur_penjualan_delete($id) {
        $cek = $this->cek_delete($id, 'Retur Penjualan');
        if ($cek == true) {
            $this->db->trans_begin();
            $this->db->delete('penjualan_retur', array('id' => $id));
            $this->db->delete('transaksi_detail', array('transaksi_id' => $id, 'transaksi_jenis' => 'Retur Penjualan'));
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $status['status'] = FALSE;
            } else {
                $this->db->trans_commit();
                $status['status'] = TRUE;
            }
        } else {
            $status['status'] = FALSE;
        }
        return $status;
    }
    
    function retur_penjualan_load_data($id) {
        $q = null;
        if ($id != null) {
            $q.="and p.id = '$id'";
        }
        $sql = "select p.id as id_retur_penjualan, o.id as id_obat, pd.nama as pegawai, o.generik, pdd.nama as pembeli,  td.*, bp.id as id_pb, bp.barcode, bp.margin, bp.diskon, b.nama as barang, 
        st.nama as satuan_terkecil, bp.isi, o.kekuatan, bk.nama as kategori_barang, bk.jenis, r.nama as pabrik, s.nama as satuan, sd.nama as sediaan from penjualan_retur p 
        left join transaksi_detail td on (p.id = td.transaksi_id) 
        join barang_packing bp on (td.barang_packing_id = bp.id) 
        join barang b on (b.id = bp.barang_id) 
        join barang_kategori bk on (bk.id = b.barang_kategori_id)
        left join obat o on (o.id = b.id) left join satuan s on (s.id = o.satuan_id) 
        left join satuan st on (st.id = bp.terkecil_satuan_id) 
        left join sediaan sd on (sd.id = o.sediaan_id) 
        left join relasi_instansi r on (r.id = b.pabrik_relasi_instansi_id)
        left join penduduk pd on (pd.id = p.pegawai_penduduk_id)
        left join penduduk pdd on (p.pembeli_penduduk_id = pdd.id)
        where td.transaksi_jenis = 'Retur Penjualan' $q";
        //echo "<pre>".$sql."</pre>";
        return $this->db->query($sql);
    }
    
    function retur_distribusi_save() {
        $this->db->trans_begin();
        
        $id_penerimaan_dist = post_safe('id_distribusi_penerimaan');
        $data_retur_dist = array(
            'waktu' => datetime2mysql(post_safe('tanggal')).":".date("s"),
            'penerimaan_distribusi_id' => $id_penerimaan_dist,
            'pegawai_penduduk_id' => $this->session->userdata('id_user')
        );
        $this->db->insert('distribusi_retur', $data_retur_dist);
        $id_retur = $this->db->insert_id();
        $id_pb = post_safe('id_pb');
        $ed    = post_safe('ed');
        $jumlah= post_safe('jp');
        foreach ($id_pb as $key => $data) {
            $jml = $this->db->query("select * from transaksi_detail where barang_packing_id = '$data' and ed = '".$ed[$key]."' and unit_id = '".$this->session->userdata('id_unit')."' and transaksi_jenis != 'Pemesanan' order by waktu desc limit 1")->row();
            
            //$sisa= $jml->sisa - $jumlah[$key];
            $data_trans = array(
                'transaksi_id' => $id_retur,
                'transaksi_jenis' => 'Retur Distribusi',
                'waktu' => datetime2mysql(post_safe('tanggal')).":".date("s"),
                'barang_packing_id' => $data,
                'unit_id' => $this->session->userdata('id_unit'),
                'ed' => $jml->ed,
                'harga' => $jml->harga,
                'ppn' => $jml->ppn,
                'hna' => $jml->hna,
                'hpp' => $jml->hpp,
                'het' => $jml->het,
                'masuk' => '0',
                'keluar' => $jumlah[$key]
            );
            $this->db->insert('transaksi_detail', $data_trans);
        }
        
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $status = FALSE;
        } else {
            $this->db->trans_commit();
            $status = TRUE;
        }
        $result['status'] = $status;
        $result['id_retur_distribusi'] = $id_retur;
        return $result;
    }
    
    function penerimaan_retur_distribusi_save() {
        $this->db->trans_begin();
        
        $data_penerimaan_retur_distribusi = array(
            'waktu' => datetime2mysql(post_safe('tanggal')).":".date("s"),
            'distribusi_retur_id' => post_safe('noretur'),
            'pegawai_penduduk_id' => $this->session->userdata('id_user')
        );
        $this->db->insert('distribusi_retur_penerimaan', $data_penerimaan_retur_distribusi);
        $id_penerimaan = $this->db->insert_id();
        $id_pb = post_safe('id_pb');
        $ed    = post_safe('ed');
        $jumlah= post_safe('jml');
        foreach ($id_pb as $key => $data) {
            $jml = $this->db->query("select * from transaksi_detail where barang_packing_id = '$data' and ed = '".$ed[$key]."' and unit_id = '".$this->session->userdata('id_unit')."' and transaksi_jenis != 'Pemesanan' order by waktu desc limit 1")->row();
            //$sisa= (isset($jml->sisa)?$jml->sisa:0) + $jumlah[$key];
            $data_trans = array(
                'transaksi_id' => $id_penerimaan,
                'transaksi_jenis' => 'Penerimaan Retur Distribusi',
                'waktu' => datetime2mysql(post_safe('tanggal')).":".date("s"),
                'barang_packing_id' => $data,
                'unit_id' => $this->session->userdata('id_unit'),
                'ed' => $jml->ed,
                'harga' => isset($jml)?$jml->harga:'0',
                'ppn' => isset($jml)?$jml->ppn:'0',
                'hna' => isset($jml)?$jml->hna:'0',
                'hpp' => isset($jml)?$jml->hpp:'0',
                'het' => isset($jml)?$jml->het:'0',
                'masuk' => $jumlah[$key],
                'keluar' => '0'
            );
            $this->db->insert('transaksi_detail', $data_trans);
        }
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $status = FALSE;
        } else {
            $this->db->trans_commit();
            $status = TRUE;
        }
        $result['status'] = $status;
        $result['id_penerimaan'] = $id_penerimaan;
        return $result;
    }
    
    function repackage_delete($id) {
        //$cek = $this->cek_delete($id, 'Repackage');
        $this->db->trans_begin();
        $this->db->delete('transaksi_detail', array('transaksi_id' => $id, 'transaksi_jenis' => 'Repackage'));
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
    
    function penerimaan_distribusi_delete($id) {
        $cek = $this->cek_delete($id, 'Penerimaan Distribusi');
        if ($cek == true) {
            $this->db->trans_begin();
            //$cek = $this->db->get_where('pembelian', array('id' => $id))->num_rows();        
            $rows= $this->db->query("select id from distribusi_retur where penerimaan_distribusi_id = '$id'")->row();

            if (isset($rows->id)) {
                $rowA= $this->db->query("select id from distribusi_retur_penerimaan where distribusi_retur_id = '".$rows->id."'")->row();
            }
            if (isset($rows->id)) {
                $this->db->delete('transaksi_detail', array('transaksi_id' => $rows->id, 'transaksi_jenis' => 'Retur Distribusi'));
            }
            if (isset($rowA)) {
                $this->db->delete('transaksi_detail', array('transaksi_id' => $rowA->id, 'transaksi_jenis' => 'Penerimaan Retur Distribusi'));
            }
            $this->db->delete('distribusi_penerimaan', array('id' => $id));
            $this->db->delete('transaksi_detail', array('transaksi_id' => $id, 'transaksi_jenis' => 'Penerimaan Distribusi'));
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $status = FALSE;
            } else {
                $this->db->trans_commit();
                $status = TRUE;
            }
        } else {
            $status = FALSE;
        }
        $result['status'] = $status;
        return $result;
    }
    
    function pembayaran_penjualan_nr_save() {
        $this->db->trans_begin();
        $no_penjualan = post_safe('nopenjualan');
        $data = array(
            'bayar' => currencyToNumber(post_safe('bayar')),
            'pembulatan' => currencyToNumber(post_safe('bulat'))
        );
        $this->db->where('id', $no_penjualan);
        $this->db->update('penjualan', $data);
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
        }
        $jurnal_1 = array(
            'waktu' => date("Y-m-d H:i:s"),
            'id_transaksi' => $no_penjualan,
            'jenis_transaksi' => 'Penjualan',
            'ket_transaksi' => '',
            'id_sub_sub_sub_sub_rekening' => '1',
            'debet' => currencyToNumber(post_safe('total'))
        );
        $this->db->insert('jurnal', $jurnal_1);
        $jurnal_264 = array(
            'waktu' => date("Y-m-d H:i:s"),
            'id_transaksi' => $no_penjualan,
            'jenis_transaksi' => 'Penjualan',
            'ket_transaksi' => '',
            'id_sub_sub_sub_sub_rekening' => '231',
            'kredit' => currencyToNumber(post_safe('total'))
        );
        $this->db->insert('jurnal', $jurnal_264);
        
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
    
    function pp_uang_detail($id) {
        $sql = "select pp.dokumen_no, pp.tanggal, pp.jenis, k.penerimaan, k.pengeluaran, k.penerimaan_pengeluaran_nama from uang_penerimaan_pengeluaran pp
            join kas k on (pp.id = k.transaksi_id)
            where k.transaksi_jenis = 'Penerimaan dan Pengeluaran' and k.transaksi_id = '$id'";
        return $this->db->query($sql);
    }
    
    function penyerahan_resep() {
        $this->db->trans_begin();
        $sah = post_safe('absah');
        $data_resep = array(
            'waktu' => datetime2mysql(post_safe('tanggal')).":".date("s"),
            'dokter_penduduk_id' => post_safe('id_dokter'),
            'pasien_penduduk_id' => post_safe('id_pasien'),
            'sah' => isset($sah)?$sah:'Sah',
            'keterangan' => post_safe('ket'),
            'jenis' => post_safe('jenis')
        );
        $id_resep = post_safe('id_resep');
        if (isset($id_resep) and $id_resep == '') {
            $this->db->insert('resep', $data_resep);
            $id_resep = $this->db->insert_id();
        } else {
            $this->db->where('id', $id_resep);
            $this->db->update('resep', $data_resep);
            $this->db->delete('resep_r', array('resep_id' => $id_resep));
        }
        
        $nr  = post_safe('nr');
        $jr  = post_safe('jr');
        $jt  = post_safe('jt');
        $ap  = post_safe('ap');
        $it  = post_safe('it');
        $ja  = post_safe('ja');
        
        foreach ($nr as $key => $data) {
            if (($jr[$key] != '') and ($jt[$key] != '') and ($ap[$key] != '') and ($it[$key] != '') and ($ja[$key] != '0-0')) {
                $jasa = explode("-", $ja[$key]);
                $data_resep_r = array(
                    'resep_id' => $id_resep,
                    'r_no' => $data,
                    'resep_r_jumlah' => $jr[$key],
                    'tebus_r_jumlah' => $jt[$key],
                    'pakai_aturan' => $ap[$key],
                    'iter' => $it[$key],
                    'tarif_id' => $jasa[0],
                    'profesi_layanan_tindakan_jasa_total' => $jasa[1],
                    'pegawai_penduduk_id' => $this->session->userdata('id_user')
                );
                //$this->db->insert('resep_r', $data_resep_r);
                $id_r= $this->db->insert_id();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                }
                $pb = post_safe('pb'.$key);
                $dr  = post_safe('dr'.$key);
                $id_pb = post_safe('id_pb'.$key);
                $jp  = post_safe('jp'.$key);
                foreach ($id_pb as $num => $rows) {
                    $form = $this->db->query("select o.formularium from obat o join barang_packing b on (o.id = b.id) where b.id = '$rows'")->row();
                    $harga= $this->db->query("select td.*, bp.margin, bp.diskon from transaksi_detail td 
                        join barang_packing bp on (td.barang_packing_id = bp.id) where td.barang_packing_id = '$rows' and 
                        td.transaksi_jenis != 'Pemesanan' order by td.waktu desc limit 1")->row();
                    //$hjual = (($harga->hna*($harga->margin/100))+$harga->hna) - (($harga->diskon/100)*$harga->hna);
                    $jml = $this->db->query("select * from transaksi_detail where barang_packing_id = '$data' and ed = '".$ed[$key]."' and unit_id = '".$this->session->userdata('id_unit')."' and transaksi_jenis != 'Pemesanan' order by waktu desc limit 1")->row();
                    //$sisa= (isset($jml->sisa)?$jml->sisa:0) + $jumlah[$key];
                    $data_resep_r_racik = array(
                        'r_resep_id' => $id_r,
                        'barang_packing_id' => $rows,
                        'jual_harga' => '0',
                        'dosis_racik' => $dr[$num],
                        'pakai_jumlah' => $jp[$num],
                        'formularium' => isset($form->formularium)?$form->formularium:NULL
                    );
                    $this->db->insert('resep_racik_r_detail', $data_resep_r_racik);
                    if ($this->db->trans_status() === FALSE) {
                        $this->db->trans_rollback();
                    }
                }
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
        $result['id_resep'] = $id_resep;
        return $result;
    }
    
    function get_pembayaran_inkaso($id) {
        $sql = "select i.*, p.nama from inkaso i
            join penduduk p on (i.pegawai_penduduk_id = p.id) 
            where i.pembelian_id = '$id'";
        return $this->db->query($sql);
    }
    
    function get_pembayaran_inkaso_total($id) {
        $sql = "select sum(i.jumlah_bayar) as terbayar from inkaso i
            join penduduk p on (i.pegawai_penduduk_id = p.id) 
            where i.pembelian_id = '$id'";
        return $this->db->query($sql);
    }
    
    function delete_inkaso($id) {
        $this->db->delete('inkaso', array('id' => $id));
        return TRUE;
    }
    
    function load_data_pembelian_inkaso() {
        $sql = "select sum(td.subtotal)+(sum(td.subtotal)*(p.ppn/100))+p.materai as subtotal, p.*, r.nama as supplier from pembelian p
            join transaksi_detail td on (p.id = td.transaksi_id)
            join relasi_instansi r on (r.id = p.suplier_relasi_instansi_id)
            where td.transaksi_jenis = 'Pembelian' 
            group by p.id order by p.id desc
            ";
        return $this->db->query($sql);
    }
    
    function load_pembayaran_inkaso($id) {
        $sql = "select distinct sum(td.subtotal)+(sum(td.subtotal)*(p.ppn/100))+p.materai as total, p.*, r.nama as instansi, (select sum(jumlah_bayar) from inkaso where pembelian_id = '$id') as jumlah_terbayar 
            from pembelian p
            join transaksi_detail td on (td.transaksi_id = p.id)
            join relasi_instansi r on (p.suplier_relasi_instansi_id = r.id)
            where p.id = '$id' and td.transaksi_jenis = 'Pembelian' group by p.id";
        return $this->db->query($sql);
    }
    
    function penerimaan_save() {
        $this->db->trans_begin();
        $this->db->insert('penerimaan', array('ket' => post_safe('keterangan')));
        $id_penerimaan = $this->db->insert_id();
        
        $id_pb      = post_safe('id_pb');
        $ed         = post_safe('ed');
        $jumlah     = post_safe('jumlah');
        $perkiraan  = post_safe('perkiraan_harga');
        $kategori   = post_safe('kategori');
        
        foreach ($id_pb as $key => $data) {
            if ($data != '') {
                $jml = $this->db->query("select * from transaksi_detail where transaksi_jenis != 'Pemesanan' and barang_packing_id = '$data' and ed = '".date2mysql($ed[$key])."' and unit_id = '".$this->session->userdata('id_unit')."' order by waktu desc limit 1")->row();

                $data_transaksi_detail = array(
                    'transaksi_id' => $id_penerimaan,
                    'transaksi_jenis' => 'Penerimaan',
                    'waktu' => datetime2mysql(post_safe('waktu')).':'.date("s"),
                    'barang_packing_id' => $data,
                    'unit_id' => $this->session->userdata('id_unit'),
                    'ed' => date2mysql($ed[$key]),
                    'harga' => currencyToNumber($perkiraan[$key]),
                    'ppn' => isset($jml->ppn)?$jml->ppn:'0',
                    'hna' => isset($jml->hna)?$jml->hna:'0',
                    'hpp' => isset($jml->hpp)?$jml->hpp:'0',
                    'het' => isset($jml->het)?$jml->het:'0',
                    'masuk' => $jumlah[$key],
                    'keluar' => '0'
                );
                $this->db->insert('transaksi_detail', $data_transaksi_detail);
                if ($kategori[$key] === 'Alat Kesehatan') {
                    $akun_debet = 34;
                    $akun_kredit= 182;
                }
                else if ($kategori[$key] === 'Obat') {
                    $akun_debet = 188;
                    $akun_kredit= 182;
                }
                else if ($kategori[$key] === 'Bahan Baku Obat') {
                    $akun_debet = 189;
                    $akun_kredit= 182;
                }
                else if ($kategori[$key] === 'BHP') {
                    $akun_debet = 191;
                    $akun_kredit= 182;
                }
                else if ($kategori[$key] === 'Reagen Lab.') {
                    $akun_debet = 192;
                    $akun_kredit= 182;
                }
                else if ($kategori[$key] === 'Bahan Radiologi') {
                    $akun_debet = 193;
                    $akun_kredit= 182;
                }
                else if ($kategori[$key] === 'Alat Fisioterapi') {
                    $akun_debet = 194;
                    $akun_kredit= 182;
                }
                else if ($kategori[$key] === 'Gas Medis') {
                    $akun_debet = 206;
                    $akun_kredit= 182;
                }
                else if ($kategori[$key] === 'Alat Tulis Kantor') {
                    $akun_debet = 51;
                    $akun_kredit= 182;
                }
                else if ($kategori[$key] === 'Percetakan') {
                    $akun_debet = 195;
                    $akun_kredit= 182;
                }
                else if ($kategori[$key] === 'Linen') {
                    $akun_debet = 196;
                    $akun_kredit= 182;
                }
                else if ($kategori[$key] === 'Kelontong') {
                    $akun_debet = 197;
                    $akun_kredit= 182;
                }
                else if ($kategori[$key] === 'Lain-lain') {
                    $akun_debet = 198;
                    $akun_kredit= 182;
                }
                else if ($kategori[$key] === 'Bahan Makanan Basah') {
                    $akun_debet = 199;
                    $akun_kredit= 182;
                }
                else if ($kategori[$key] === 'Bahan Makanan Kering') {
                    $akun_debet = 200;
                    $akun_kredit= 182;
                }
                $jurnal_188 = array(
                    'waktu' => date("Y-m-d H:i:s"),
                    'id_transaksi' => $id_penerimaan,
                    'jenis_transaksi' => 'Penerimaan',
                    'ket_transaksi' => post_safe('keterangan'),
                    'id_sub_sub_sub_sub_rekening' => $akun_debet,
                    'debet' => (currencyToNumber($perkiraan[$key])*$jumlah[$key])
                );
                $this->db->insert('jurnal', $jurnal_188);

                $jurnal_84 = array(
                    'waktu' => date("Y-m-d H:i:s"),
                    'id_transaksi' => $id_penerimaan,
                    'jenis_transaksi' => 'Penerimaan',
                    'ket_transaksi' => post_safe('keterangan'),
                    'id_sub_sub_sub_sub_rekening' => $akun_kredit,
                    'kredit' => (currencyToNumber($perkiraan[$key])*$jumlah[$key])
                );
                $this->db->insert('jurnal', $jurnal_84);
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
        $result['id_penerimaan'] = $id_penerimaan;
        return $result;
    }
    
    function penerimaan_delete($id) {
        $this->db->delete('penerimaan', array('id' => $id));
        $this->db->delete('transaksi_detail', array('transaksi_id' => $id, 'transaksi_jenis' => 'Penerimaan'));
        $this->db->delete('jurnal', array('id_transaksi' => $id, 'jenis_transaksi' => 'Penerimaan'));
        $status['status'] = TRUE;
        return $status;
    }
    
    function load_data_probabilitas($awal,$akhir,$jenis) {
        //echo $this->session->userdata('unit');
        $sql = "select bp.id, concat_ws(' ',b.nama, o.kekuatan, s.nama, '@', bp.isi, st.nama) as nama_barang, 
        sum(td.keluar) as jumlah_pemakaian, td.hna+(td.hna*(bp.margin/100)) as hja, td.hna, td.hpp, 
        (sum(td.keluar)*(td.hna+(td.hna*(bp.margin/100)))) as total_nilai_penjualan,
        (sum(td.keluar)*(td.hna+(td.hna*(bp.margin/100)))) as total_nilai_pemakaian,
        (sum(td.keluar)*(td.hna+(td.hna*(bp.margin/100))))+(sum(td.keluar)*td.hpp) as total_nilai
        from transaksi_detail td
        join barang_packing bp on (td.barang_packing_id = bp.id)
        join barang b on (bp.barang_id = b.id)
        join obat o on (b.id = o.id)
        left join satuan s on (o.satuan_id = s.id)
        left join satuan st on (bp.terkecil_satuan_id = st.id)
        inner join (
            select barang_packing_id, max(id) as id_max from transaksi_detail group by id
        ) tm on (td.barang_packing_id = tm.barang_packing_id and td.id = tm.id_max)
        where date(td.waktu) between '$awal' and '$akhir' and td.transaksi_jenis = '$jenis' 
            
            group by bp.id order by jumlah_pemakaian desc";
            
        return $this->db->query($sql);
        //and td.unit_id = '".$this->session->userdata('id_unit')."'
    }
    
    function load_data_abc($awal,$akhir,$jenis) {
        if ($jenis === 'Penjualan') {
            $sql = "select bp.id, concat_ws(' ',b.nama, o.kekuatan, s.nama, '@', bp.isi, st.nama) as nama_barang, 
            sum(td.keluar) as jumlah_pemakaian, td.hna+(td.hna*(bp.margin/100)) as hja, td.hna, td.hpp, 
            (sum(td.keluar)*(td.hna+(td.hna*(bp.margin/100)))) as total_nilai_penjualan,
            (sum(td.keluar)*(td.hna+(td.hna*(bp.margin/100)))) as total_nilai_pemakaian,
            (sum(td.keluar)*(td.hna+(td.hna*(bp.margin/100))))+(sum(td.keluar)*td.hpp) as total_nilai
            from transaksi_detail td
            join barang_packing bp on (td.barang_packing_id = bp.id)
            join barang b on (bp.barang_id = b.id)
            join obat o on (b.id = o.id)
            left join satuan s on (o.satuan_id = s.id)
            left join satuan st on (bp.terkecil_satuan_id = st.id)
            inner join (
                select barang_packing_id, max(id) as id_max from transaksi_detail group by id
            ) tm on (td.barang_packing_id = tm.barang_packing_id and td.id = tm.id_max)
            where date(td.waktu) between '$awal' and '$akhir' and td.transaksi_jenis = '$jenis'
                
                group by bp.id order by total_nilai desc";
            //and td.unit_id = '".$this->session->userdata('id_unit')."'
        } else {
            $sql = "select bp.id, concat_ws(' ',b.nama, o.kekuatan, s.nama, '@', bp.isi, st.nama) as nama_barang, 
                sum(td.keluar) as jumlah_pemakaian, td.hna+(td.hna*(bp.margin/100)) as hja, td.hna, td.hpp, 
                (sum(td.keluar)*(td.hna+(td.hna*(bp.margin/100)))) as total_nilai_penjualan,
                (sum(td.keluar)*td.hpp) as total_nilai_pemakaian,
                (sum(td.keluar)*(td.hna+(td.hna*(bp.margin/100))))+(sum(td.keluar)*td.hpp) as total_nilai
                from transaksi_detail td
                join barang_packing bp on (td.barang_packing_id = bp.id)
                join barang b on (bp.barang_id = b.id)
                join obat o on (b.id = o.id)
                left join satuan s on (o.satuan_id = s.id)
                left join satuan st on (bp.terkecil_satuan_id = st.id)
                inner join (
                    select barang_packing_id, max(id) as id_max from transaksi_detail group by id
                ) tm on (td.barang_packing_id = tm.barang_packing_id and td.id = tm.id_max)
                where date(td.waktu) between '$awal' and '$akhir' and td.transaksi_jenis = '$jenis'
                    
                    group by bp.id order by total_nilai desc";
        }
        //and td.unit_id = '".$this->session->userdata('id_unit')."'
        //echo "<pre>".$sql."</pre>";
        return $this->db->query($sql);
        
    }
    
    function koreksi_stok_load_data($search = NULL, $limit = NULL, $start = NULL) {
        $q = NULL; $l = NULL;
        if ($search !== NULL) {
            $q = " having nama_barang like ('%$search%')";
        }
        $l = " limit $start, $limit";
        $sql = "select td.*, sum(masuk) as masuk, concat_ws(' ',b.nama,o.kekuatan,s.nama) as nama_barang, sum(keluar) as keluar, (sum(masuk)-sum(keluar)) as sisa, bp.hpp, bp.hna, 
            bp.id as id_pb, bp.margin, bp.ppn_jual, o.high_alert, o.id as id_obat,
            bp.diskon, o.generik, b.nama as barang, st.nama as satuan_terkecil, stb.nama as satuan_terbesar,
            bp.isi, o.kekuatan, r.nama as pabrik, s.nama as satuan, 
            sd.nama as sediaan from transaksi_detail td
            join barang_packing bp on (td.barang_packing_id = bp.id)
            join barang b on (bp.barang_id = b.id)
            join barang_kategori bk on (b.barang_kategori_id = bk.id)
            left join relasi_instansi r on (r.id = b.pabrik_relasi_instansi_id)
            left join obat o on (b.id = o.id)
            left join satuan s on (s.id = o.satuan_id)
            left join satuan st on (st.id = bp.terkecil_satuan_id)
            left join satuan stb on (stb.id = bp.terbesar_satuan_id)
            left join sediaan sd on (sd.id = o.sediaan_id)
            
            where td.id is not null  and td.unit_id = '".$this->session->userdata('id_unit')."'
            and bk.jenis = 'Farmasi' and td.transaksi_jenis != 'Pemesanan' 
            group by bp.id, td.ed $q order by td.waktu asc";
        //echo "<pre>".$sql."</pre>";
        $result['data'] = $this->db->query($sql.$l)->result();
        $result['jumlah'] = $this->db->query($sql)->num_rows();
        return $result;
    }
    
    function koreksi_stok_save() {
        $id_kemasan = $this->input->post('id_packing');
        $penyesuaian= $this->input->post('penyesuaian');
        $alasan     = $this->input->post('alasan');
        $ed         = $this->input->post('ed');
        $kenyataan  = $this->input->post('kenyataan');
        
        foreach ($id_kemasan as $key => $data) {
            
            if ($kenyataan[$key] !== '') {
                if ($penyesuaian[$key] < 0) {
                    $array = array(
                        'transaksi_jenis' => 'Koreksi Stok',
                        'waktu' => date("Y-m-d H:i:s"),
                        'barang_packing_id' => $data,
                        'ed' => $ed[$key],
                        'unit_id' => $this->session->userdata('id_unit'),
                        'keluar' => abs($penyesuaian[$key]),
                        'keterangan' => $alasan[$key]
                    );
                } else {
                    $array = array(
                        'transaksi_jenis' => 'Koreksi Stok',
                        'waktu' => date("Y-m-d H:i:s"),
                        'barang_packing_id' => $data,
                        'ed' => $ed[$key],
                        'unit_id' => $this->session->userdata('id_unit'),
                        'masuk' => abs($penyesuaian[$key]),
                        'keterangan' => $alasan[$key]
                    );
                }
                $this->db->insert('transaksi_detail', $array);
            }
        }
        die(json_encode(array('status' => TRUE)));
    }
    
    function get_data_pemesanan($limit, $start, $search) {
        $q = null;
        if ($search['key'] !== '') {
            $q.=" and p.id like '%".$search['key']."%'";
        }
        if ($search['id'] !== '') {
            $q.=" and p.id = '".$search['id']."'";
        }
        $sql = "select p.*, k.nama as karyawan, dp.jumlah, concat_ws(' ',b.nama, b.kekuatan, st.nama) as nama_barang, b.perundangan,
        st.nama as kemasan, s.nama as supplier from pemesanan p
        join supplier s on (p.id_supplier = s.id)
        join detail_pemesanan dp on (dp.id_pemesanan = p.id)
        join kemasan km on (km.id = dp.id_kemasan)
        join barang b on (b.id = km.id_barang)
        join satuan st on (st.id = km.id_kemasan)
        left join users u on (p.id_users = u.id)
        left join penduduk k on (u.id = k.id)
        where p.id is not NULL $q order by p.tanggal desc";
        
        $limitation = null;
        $limitation.=" limit $start , $limit";
        $query = $this->db->query($sql . $q . $limitation);
        //echo $sql . $q . $limitation;
        $queryAll = $this->db->query($sql . $q);
        $data['data'] = $query->result();
        $data['jumlah'] = $queryAll->num_rows();
        return $data;
    }
    
    function get_data_defecta($limit, $start, $search) {
        $q = NULL;
        if ($search['id'] !== '') {
            $q.=" and s.id = '".$search['id']."' ";
        }
        if ($search['key'] !== '') {
            $q.=" and b.nama like ('%".$search['key']."%')";
        }
        $sql = "select s.*, b.kekuatan, b.id as id_barang, b.nama, b.stok_minimal, st.nama as satuan_kekuatan, sum(s.masuk) as masuk, 
            sum(s.keluar) as keluar, (sum(s.masuk)-sum(s.keluar)) as sisa 
            from stok s 
            join barang b on (s.id_barang = b.id) 
            left join satuan st on (b.satuan_kekuatan = st.id) 
            where b.id not in (select id_barang from defecta where status = '0') $q
            group by s.id_barang  
            having sisa <= b.stok_minimal order by b.nama";
        $limitation = null;
        $limitation.=" limit $start , $limit";
        $query = $this->db->query($sql . $q . $limitation);
        //echo $sql . $q . $limitation;
        $queryAll = $this->db->query($sql . $q);
        $data['data'] = $query->result();
        $data['jumlah'] = $queryAll->num_rows();
        return $data;
    }
    
    function get_data_barang($limit, $start, $param) {
        $q = "and b.aktif = '".$_GET['aktif']."'";
        if ($param['id'] !== '') {
            $q.= "and b.id = '".$param['id']."'";
        }
        if ($param['search'] !== '') {
            $q.="and (
                b.nama like ('%".$param['search']."%') or 
                p.nama like ('%".$param['search']."%') or 
                g.nama like ('%".$param['search']."%') or 
                b.rak like ('%".$param['search']."%') or 
                b.indikasi like ('%".$param['search']."%') or 
                b.dosis like ('%".$param['search']."%') or 
                b.kandungan like ('%".$param['search']."%') or 
                b.perhatian like ('%".$param['search']."%') or 
                b.kontra_indikasi like ('%".$param['search']."%') or 
                b.efek_samping like ('%".$param['search']."%') or 
                b.aturan_pakai like ('%".$param['search']."%') or 
                k.nama like ('%".$param['search']."%'))";
        }
        //$limit = " limit $start, $limit";
        $sql = "select b.*, p.nama as pabrik, g.nama as golongan, f.id as id_farmakoterapi, st.nama as satuan, sd.nama as sediaan 
            from barang b 
            left join pabrik p on (b.id_pabrik = p.id)
            left join golongan g on (b.id_golongan = g.id)
            left join satuan st on (b.satuan_kekuatan = st.id)
            left join kelas_terapi k on (k.id = b.id_kelas_terapi)
            left join farmako_terapi f on (f.id = k.id_farmako_terapi)
            left join sediaan sd on (b.id_sediaan = sd.id) where b.id is not NULL $q order by b.nama
            ";
        
        $limitation = null;
        $limitation.=" limit $start , $limit";
        $query = $this->db->query($sql . $q . $limitation);
        //echo "<pre>".$sql . $q . $limitation."</pre>";
        $queryAll = $this->db->query($sql . $q);
        $data['data'] = $query->result();
        $data['jumlah'] = $queryAll->num_rows();
        return $data;
    }
    
    function get_data_stok_opname($limit, $start, $search) {
        $q = NULL;
        if ($search['id'] !== '') {
            $q.="and s.id = '".$search['id']."'";
        }
        if (isset($search['key']) and $search['key'] !== '') {
            $q.=" and b.nama like ('%".$search['key']."%')";
        }
        $limitation = " limit ".$start.", ".$limit."";
        $sql = "select s.*, b.kekuatan, b.nama, st.nama as satuan_kekuatan, sum(s.masuk) as masuk, sum(s.keluar) as keluar, 
            (sum(s.masuk)-sum(s.keluar)) as sisa, b.id as id_barang from stok s 
            join barang b on (s.id_barang = b.id)
            left join satuan st on (b.satuan_kekuatan = st.id)
            where s.transaksi = 'Stok Opname' $q group by s.id_barang";
        $query = $this->db->query($sql . $limitation);
        //echo $sql . $q . $limitation;
        $queryAll = $this->db->query($sql);
        $data['data'] = $query->result();
        $data['jumlah'] = $queryAll->num_rows();
        return $data;
    }
    
    function sediaan_load_data($status = null) {
        $q = NULL;
        if ($status != NULL) {
            $q = "where id = '$status'";
        }
        $sql = "select * from sediaan $q order by nama asc";
        return $this->db->query($sql);
    }
    /*PENERIMAAN*/
    function get_data_penerimaan($limit, $start, $search) {
        $q = NULL; $limitation = NULL;
        if (isset($search['id']) and $search['id'] !== '') {
            $q.="and p.id = '".$search['id']."' ";
        }
        if (isset($search['id_supplier'])) {
            //$q.=" and p.status != 'Cash'";
        }
        if (isset($search['id_supplier']) and $search['id_supplier'] !== '') {
            $q.=" and s.id = '".$search['id_supplier']."'";
        }
        if (isset($search['faktur']) and $search['faktur'] !== '') {
            $q.=" and p.faktur = '".$search['faktur']."'";
        }
        if (isset($search['awal'])) {
            $q.=" and p.tanggal between '".$search['awal']."' and '".$search['akhir']."'";
        }
        if (isset($search['start'])) {
            $limitation = " limit ".$start.", ".$limit."";
        }

        $sql = "select p.*, k.nama as karyawan, IFNULL(s.nama,'-') as supplier, concat_ws(' ',b.nama, b.kekuatan, st.nama) as nama_barang, 
            dp.jumlah, dp.expired, dp.nobatch, dp.harga, dp.disc_pr, dp.disc_rp, stn.nama as kemasan
            from penerimaan p
            left join pemesanan ps on (p.id_pemesanan = ps.id)
            join detail_penerimaan dp on (dp.id_penerimaan = p.id)
            join kemasan km on (dp.id_kemasan = km.id)
            join satuan stn on (stn.id = km.id_kemasan)
            join barang b on (km.id_barang = b.id)
            left join satuan st on (b.satuan_kekuatan = st.id)
            left join supplier s on (p.id_supplier = s.id)
            left join users u on (p.id_users = u.id)
            left join penduduk k on (u.id = k.id)
            where p.id is not NULL $q order by p.id desc";
        
        $query = $this->db->query($sql . $q . $limitation);
        //echo $sql . $q . $limitation;
        $queryAll = $this->db->query($sql . $q);
        $data['data'] = $query->result();
        $data['jumlah'] = $queryAll->num_rows();
        return $data;
    }
    
    function save_penerimaan() {
        $this->db->trans_begin();
        $faktur         = $_POST['faktur'];
        $tanggal        = date2mysql($_POST['tanggal']);
        $no_sp          = ($_POST['no_sp'] !== '')?$_POST['no_sp']:"NULL";
        $supplier       = $_POST['id_supplier'];
        $ppn            = $_POST['ppn'];
        $materai        = currencyToNumber($_POST['materai']);
        $tempo          = ($_POST['tempo'] !== '')?date2mysql($_POST['tempo']):"NULL";
        $status         = $_POST['status'];
        //$id_user        = ""; // unUsed
        $disc_pr        = $_POST['disc_pr'];
        $disc_rp        = currencyToNumber($_POST['disc_rp']);
        $total          = currencyToNumber($_POST['total']);
        $id_penerimaan  = $_POST['id_penerimaan'];
        $hna            = $_POST['hna'];

        if ($id_penerimaan === '') {
            $sql = "insert into penerimaan set
                faktur = '$faktur',
                tanggal = '$tanggal',
                id_supplier = '$supplier',
                id_pemesanan = '$no_sp',
                ppn = '$ppn',
                materai = '$materai',
                jatuh_tempo = '$tempo',
                id_users = '".$this->session->userdata('id_user')."',
                diskon_persen = '$disc_pr',
                diskon_rupiah = '$disc_rp',
                total = '$total',
                status = '$status',
                id_unit = '".$this->session->userdata('id_unit')."'";
            
            $this->db->query($sql);
            $id = $this->db->insert_id();
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $result['status'] = FALSE;
            }
            $id_barang  = $_POST['id_barang'];
            $id_kemasan = $_POST['satuan'];
            $jumlah     = $_POST['jumlah'];
            $no_batch   = $_POST['nobatch'];
            $ed         = $_POST['ed'];
            $harga      = $_POST['harga'];
            $diskon_pr  = $_POST['diskon_pr'];
            $diskon_rp  = $_POST['diskon_rp'];
            //$subtotal   = $_POST['subtotal'];
            foreach ($id_barang as $key => $data) {
                $rows  = $this->db->query("select * from kemasan where id_barang = '$data' and id_kemasan = '$id_kemasan[$key]'")->row();
                
                $harga_a= currencyToNumber($harga[$key]);

                $base_hpp 	= ((currencyToNumber($harga[$key])*$jumlah[$key]) - ((currencyToNumber($harga[$key])*$jumlah[$key]) * ($diskon_pr[$key]/100))) / ($jumlah[$key]);
                $hpp_ppn	= ($ppn/100)*$base_hpp;
                $hpp 	= $base_hpp+$hpp_ppn;

                $sql = "insert into detail_penerimaan set
                    id_penerimaan = '$id',
                    id_kemasan = '".$rows->id."',
                    nobatch = '$no_batch[$key]',
                    expired = '".date2mysql($ed[$key])."',
                    harga = '$harga_a',
                    jumlah = '$jumlah[$key]',
                    disc_pr = '$diskon_pr[$key]',
                    disc_rp = '".currencyToNumber($diskon_rp[$key])."',
                    hpp = '$hpp'
                    ";
                $this->db->query($sql);
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $result['status'] = FALSE;
                }
                $this->db->query("update barang set hna = '".$hna[$key]."' where id = '$data'");

                $sqk = $this->db->query("select dhj.id, b.nama, b.hna, dhj.margin_non_resep, dhj.margin_resep, k.isi, k.isi_satuan,
                    b.hna+(b.hna*(dhj.margin_non_resep/100)) as hja_nr, dhj.diskon_persen, dhj.diskon_rupiah,
                    b.hna+(b.hna*(dhj.margin_resep/100)) as hja_r from barang b
                    join kemasan k on (b.id = k.id_barang)
                    join dinamic_harga_jual dhj on (k.id = dhj.id_kemasan)
                    where b.id = '$data'")->result();
                //while ($rowk = mysql_fetch_object($sqk)) {
                foreach ($sqk as $rowk) {
                    $isi = $rowk->isi*$rowk->isi_satuan;
                    if ($rowk->diskon_persen === '0') {
                        $terdiskon_nr = ($rowk->hja_nr*$isi)-$rowk->diskon_rupiah;  // hitung diskon rupiah
                        $terdiskon_r  = ($rowk->hja_r*$isi)-$rowk->diskon_rupiah;
                    }
                    else {
                        $terdiskon_nr = ($rowk->hja_nr*$isi)-(($rowk->hja_nr*$isi)*($rowk->diskon_persen/100));
                        $terdiskon_r  = ($rowk->hja_r*$isi)-(($rowk->hja_r*$isi)*($rowk->diskon_persen/100));
                    }
                    $this->db->query("update dinamic_harga_jual set 
                        hj_non_resep = '$terdiskon_nr',
                        hj_resep = '$terdiskon_r'
                        where id = '".$rowk->id."'");
                }

                $stok= "insert into stok set
                    waktu = '$tanggal ".date("H:i:s")."',
                    id_transaksi = '$id',
                    transaksi = 'Penerimaan',
                    nobatch = '$no_batch[$key]',
                    id_barang = '$data',
                    ed = '".date2mysql($ed[$key])."',
                    masuk = '".($jumlah[$key]*($rows->isi*$rows->isi_satuan))."',
                    id_unit = '".$this->session->userdata('id_unit')."'
                ";
                $this->db->query($stok);
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $result['status'] = FALSE;
                }
                $akun_debet = 188;
                /*if ($diskon_pr[$key] === '100') {
                    $this->db->query("insert into jurnal set
                        waktu = '".date("Y-m-d H:i:s")."',
                        id_transaksi = $id,
                        transaksi = 'Penerimaan',
                        id_sub_sub_sub_sub_rekening = $akun_debet,
                        debet = '".($harga_a*$jumlah[$key])."'");

                    $this->db->query("insert into jurnal set
                        waktu = '".date("Y-m-d H:i:s")."',
                        id_transaksi = $id,
                        transaksi = 'Penerimaan',
                        id_sub_sub_sub_sub_rekening = '273',
                        kredit = '".($harga_a*$jumlah[$key])."'");
                }*/
            }

            /*$akun_debet = 188;
            $akun_kredit= 84;
            $this->db->query("insert into jurnal set
                waktu = '".date("Y-m-d H:i:s")."',
                id_transaksi = $id,
                transaksi = 'Penerimaan',
                id_sub_sub_sub_sub_rekening = $akun_debet,
                debet = '$total'");

            $this->db->query("insert into jurnal set
                waktu = '".date("Y-m-d H:i:s")."',
                id_transaksi = $id,
                transaksi = 'Penerimaan',
                id_sub_sub_sub_sub_rekening = $akun_kredit,
                kredit = '$total'");

            if ($materai !== '0') { // materai
                $this->db->query("insert into jurnal set
                    waktu = '".date("Y-m-d H:i:s")."',
                    id_transaksi = $id,
                    transaksi = 'Penerimaan',
                    id_sub_sub_sub_sub_rekening = '156',
                    debet = '$total'");

                $this->db->query("insert into jurnal set
                    waktu = '".date("Y-m-d H:i:s")."',
                    id_transaksi = $id,
                    transaksi = 'Penerimaan',
                    id_sub_sub_sub_sub_rekening = '51',
                    kredit = '$total'");
            }*/

            if ($status === 'Cash') {
                $row = $this->db->query("select substr(no_ref, 4,3) as id  from inkaso order by id desc limit 1")->row();
                if (!isset($row->id)) {
                    $res = "IN.001-".date("m/Y");
                } else {
                    $res = "IN.".str_pad((string)($row->id+1), 3, "0", STR_PAD_LEFT)."-".date("m/Y");
                }
                $q_inkaso = "insert into inkaso set
                    no_ref = '$res',
                    tanggal = NOW(),
                    id_penerimaan = '$id_penerimaan',
                    cara_bayar = 'Uang',
                    nominal = '$total'";
                $this->db->query($q_inkaso);
                $id_inkaso = $this->db->insert_id();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $result['status'] = FALSE;
                }

                /*$this->db->query("insert into jurnal set
                    waktu = '".date("Y-m-d H:i:s")."',
                    id_transaksi = $id_inkaso,
                    transaksi = 'Inkaso',
                    id_sub_sub_sub_sub_rekening = '84',
                    debet = '$total'");

                $this->db->query("insert into jurnal set
                    waktu = '".date("Y-m-d H:i:s")."',
                    id_transaksi = $id_inkaso,
                    transaksi = 'Inkaso',
                    id_sub_sub_sub_sub_rekening = '1',
                    kredit = '$total'");*/
            }

            $result['action'] = 'add';
        } else {
            $sql = "update penerimaan set
                faktur = '$faktur',
                tanggal = '$tanggal',
                id_supplier = '$supplier',
                id_pemesanan = '$no_sp',
                ppn = '$ppn',
                materai = '$materai',
                jatuh_tempo = '$tempo',
                id_users = '".$this->session->userdata('id_user')."',
                diskon_persen = '$disc_pr',
                diskon_rupiah = '$disc_rp',
                total = '$total',
                status = '$status',
                id_unit = '".$this->session->userdata('id_unit')."'
                where id = '$id_penerimaan'";
            
            $this->db->query($sql);
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $result['status'] = FALSE;
            }
            $id = $id_penerimaan;
            $this->db->query("delete from detail_penerimaan where id_penerimaan = '$id_penerimaan'");
            $this->db->query("delete from stok where id_transaksi = '$id' and transaksi = 'Penerimaan'");
            $this->db->query("delete from jurnal where id_transaksi = '$id' and transaksi = 'Penerimaan'");
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $result['status'] = FALSE;
            }
            $id_barang  = $_POST['id_barang'];
            $id_kemasan = $_POST['satuan'];
            $jumlah     = $_POST['jumlah'];
            $no_batch   = $_POST['nobatch'];
            $ed         = $_POST['ed'];
            $harga      = $_POST['harga'];
            $diskon_pr  = $_POST['diskon_pr'];
            $diskon_rp  = $_POST['diskon_rp'];
            //$subtotal   = $_POST['subtotal'];
            foreach ($id_barang as $key => $data) {
                $rows  = $this->db->query("select * from kemasan where id_barang = '$data' and id_kemasan = '$id_kemasan[$key]'")->row();
                
                $harga_a= currencyToNumber($harga[$key]);

                $base_hpp 	= ((currencyToNumber($harga[$key])*$jumlah[$key]) - ((currencyToNumber($harga[$key])*$jumlah[$key]) * ($diskon_pr[$key]/100))) / ($jumlah[$key]);
                $hpp_ppn	= ($ppn/100)*$base_hpp;
                $hpp 	= $base_hpp+$hpp_ppn;

                $sql = "insert into detail_penerimaan set
                    id_penerimaan = '$id',
                    id_kemasan = '".$rows->id."',
                    nobatch = '$no_batch[$key]',
                    expired = '".date2mysql($ed[$key])."',
                    harga = '$harga_a',
                    jumlah = '$jumlah[$key]',
                    disc_pr = '$diskon_pr[$key]',
                    disc_rp = '".currencyToNumber($diskon_rp[$key])."',
                    hpp = '$hpp'";
                $this->db->query($sql);
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $result['status'] = FALSE;
                }
                $this->db->query("update barang set hna = '".$hna[$key]."' where id = '$data'");

                $sqk = $this->db->query("select dhj.id, b.nama, b.hna, dhj.margin_non_resep, dhj.margin_resep, k.isi, k.isi_satuan,
                    b.hna+(b.hna*(dhj.margin_non_resep/100)) as hja_nr, dhj.diskon_persen, dhj.diskon_rupiah,
                    b.hna+(b.hna*(dhj.margin_resep/100)) as hja_r from barang b
                    join kemasan k on (b.id = k.id_barang)
                    join dinamic_harga_jual dhj on (k.id = dhj.id_kemasan)
                    where b.id = '$data'")->result();
                //while ($rowk = mysql_fetch_object($sqk)) {
                foreach ($sqk as $rowk) {
                    $isi = $rowk->isi*$rowk->isi_satuan;
                    if ($rowk->diskon_persen === '0') {
                        $terdiskon_nr = ($rowk->hja_nr*$isi)-$rowk->diskon_rupiah;  // hitung diskon rupiah
                        $terdiskon_r  = ($rowk->hja_r*$isi)-$rowk->diskon_rupiah;
                    }
                    else {
                        $terdiskon_nr = ($rowk->hja_nr*$isi)-(($rowk->hja_nr*$isi)*($rowk->diskon_persen/100));
                        $terdiskon_r  = ($rowk->hja_r*$isi)-(($rowk->hja_r*$isi)*($rowk->diskon_persen/100));
                    }
                    $this->db->query("update dinamic_harga_jual set 
                        hj_non_resep = '$terdiskon_nr',
                        hj_resep = '$terdiskon_r'
                        where id = '".$rowk->id."'");
                }

                $stok= "insert into stok set
                    waktu = '$tanggal ".date("H:i:s")."',
                    id_transaksi = '$id',
                    transaksi = 'Penerimaan',
                    nobatch = '$no_batch[$key]',
                    id_barang = '$data',
                    ed = '".date2mysql($ed[$key])."',
                    masuk = '".($jumlah[$key]*($rows->isi*$rows->isi_satuan))."',
                    id_unit = '".$this->session->userdata('id_unit')."'
                ";
                $this->db->query($stok);
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $result['status'] = FALSE;
                }
                /*$akun_debet = 188;
                if ($diskon_pr[$key] === '100') {
                    $this->db->query("insert into jurnal set
                        waktu = '".date("Y-m-d H:i:s")."',
                        id_transaksi = $id,
                        transaksi = 'Penerimaan',
                        id_sub_sub_sub_sub_rekening = $akun_debet,
                        debet = '".($harga_a*$jumlah[$key])."'");

                    $this->db->query("insert into jurnal set
                        waktu = '".date("Y-m-d H:i:s")."',
                        id_transaksi = $id,
                        transaksi = 'Penerimaan',
                        id_sub_sub_sub_sub_rekening = '273',
                        kredit = '".($harga_a*$jumlah[$key])."'");
                }*/
            }

            /*$akun_debet = 188;
            $akun_kredit= 84;
            $this->db->query("insert into jurnal set
                waktu = '".date("Y-m-d H:i:s")."',
                id_transaksi = $id,
                transaksi = 'Penerimaan',
                id_sub_sub_sub_sub_rekening = $akun_debet,
                debet = '$total'");

            $this->db->query("insert into jurnal set
                waktu = '".date("Y-m-d H:i:s")."',
                id_transaksi = $id,
                transaksi = 'Penerimaan',
                id_sub_sub_sub_sub_rekening = $akun_kredit,
                kredit = '$total'");

            if ($materai !== '0') { // materai
                $this->db->query("insert into jurnal set
                    waktu = '".date("Y-m-d H:i:s")."',
                    id_transaksi = $id,
                    transaksi = 'Penerimaan',
                    id_sub_sub_sub_sub_rekening = '156',
                    debet = '$total'");

                $this->db->query("insert into jurnal set
                    waktu = '".date("Y-m-d H:i:s")."',
                    id_transaksi = $id,
                    transaksi = 'Penerimaan',
                    id_sub_sub_sub_sub_rekening = '51',
                    kredit = '$total'");
            }*/

            if ($status === 'Cash') {
                $row = $this->db->query("select substr(no_ref, 4,3) as id  from inkaso order by id desc limit 1")->row();
                if (!isset($row->id)) {
                    $res = "IN.001-".date("m/Y");
                } else {
                    $res = "IN.".str_pad((string)($row->id+1), 3, "0", STR_PAD_LEFT)."-".date("m/Y");
                }
                $q_inkaso = "insert into inkaso set
                    no_ref = '$res',
                    tanggal = NOW(),
                    id_penerimaan = '$id_penerimaan',
                    cara_bayar = 'Uang',
                    nominal = '$total'";
                $this->db->query($q_inkaso);
                $id_inkaso = $this->db->insert_id();
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $result['status'] = FALSE;
                }
                /*$this->db->query("insert into jurnal set
                    waktu = '".date("Y-m-d H:i:s")."',
                    id_transaksi = $id_inkaso,
                    transaksi = 'Inkaso',
                    id_sub_sub_sub_sub_rekening = '84',
                    debet = '$total'");

                $this->db->query("insert into jurnal set
                    waktu = '".date("Y-m-d H:i:s")."',
                    id_transaksi = $id_inkaso,
                    transaksi = 'Inkaso',
                    id_sub_sub_sub_sub_rekening = '1',
                    kredit = '$total'");*/
            }
            $result['action'] = 'edit';
        }
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $result['status'] = FALSE;
        } else {
            $this->db->trans_commit();
            $result['status'] = TRUE;
        }
        $result['id_penerimaan'] = $id;

        die(json_encode($result));
    }
    
    function get_data_pemusnahan($limit, $start, $search) {
        $q = NULL;
        if (isset($search['awal'])) {
            $q.=" and p.tanggal between '".$search['awal']."' and '".$search['akhir']."'";
        }
        $sql = "select p.*, pd.nama as apoteker, dp.ed,
        concat_ws(' ',b.nama,b.kekuatan,s.nama) as nama_barang, st.nama as kemasan, dp.jumlah, dp.hpp as subtotal
        from pemusnahan p
        join detail_pemusnahan dp on (p.id = dp.id_pemusnahan)
        join penduduk pd on (pd.id = p.saksi_apotek)
        join penduduk pdd on (pdd.id = p.saksi_bpom)
        join kemasan k on (k.id = dp.id_kemasan)
        join barang b on (k.id_barang = b.id)
        left join satuan s on (b.satuan_kekuatan = s.id)
        left join satuan st on (k.id_kemasan = st.id)
        where p.id is not NULL $q order by p.id";
        $limitation = " limit ".$start.", ".$limit."";
        $query = $this->db->query($sql . $limitation);
        //echo $sql . $q . $limitation;
        $queryAll = $this->db->query($sql);
        $data['data'] = $query->result();
        $data['jumlah'] = $queryAll->num_rows();
        return $data;
    }
    
    function kemasan_load_data($id) {
        $sql = "select k.*, s.nama from kemasan k
        join satuan s on (k.id_kemasan = s.id) where k.id_barang = '$id'";
        
        return $this->db->query($sql);
    }
    
    function get_detail_harga_barang_pemesanan() {
        $id = $_GET['id']; // id barang
        $id_kemasan = $_GET['id_kemasan'];
        $query = "select b.*, IFNULL((b.hna*k.isi*k.isi_satuan), 0) as hna, (b.hna*k.isi_satuan*k.isi) as esti, 
            k.id as id_packing from barang b
            join kemasan k on (b.id = k.id_barang)
            where k.id_barang = '$id' and k.id_kemasan = '$id_kemasan'";
        return $this->db->query($query);
    }
    
    /*DISTRIBUSI*/
    function get_data_distribusi($limit, $start, $search) {
        $q = NULL;
        if (isset($search['awal'])) {
            $q.=" and d.tanggal between '".$search['awal']."' and '".$search['akhir']."'";
        }
        $sql = "select d.*, dd.jumlah, concat_ws(' ',b.nama,b.kekuatan,s.nama) as nama_barang, st.nama as kemasan,
        u.nama as asal, un.nama as tujuan
        from distribusi d
        join detail_distribusi dd on (d.id = dd.id_distribusi)
        join unit u on (d.id_unit_asal = u.id)
        join unit un on (d.id_unit_tujuan = un.id)
        join kemasan k on (k.id = dd.id_kemasan)
        join barang b on (k.id_barang = b.id)
        left join satuan s on (b.satuan_kekuatan = s.id)
        left join satuan st on (k.id_kemasan = st.id)
        where d.id is not NULL $q order by d.id";
        $limitation = " limit ".$start.", ".$limit."";
        $query = $this->db->query($sql . $limitation);
        //echo $sql . $q . $limitation;
        $queryAll = $this->db->query($sql);
        $data['data'] = $query->result();
        $data['jumlah'] = $queryAll->num_rows();
        return $data;
    }
    
    function load_data_distribusi($id) {
        $sql = "select d.*, dd.jumlah, concat_ws(' ',b.nama,b.kekuatan,s.nama) as nama_barang, st.nama as kemasan,
        u.nama as asal, un.nama as tujuan
        from distribusi d
        join detail_distribusi dd on (d.id = dd.id_distribusi)
        join unit u on (d.id_unit_asal = u.id)
        join unit un on (d.id_unit_tujuan = un.id)
        join kemasan k on (k.id = dd.id_kemasan)
        join barang b on (k.id_barang = b.id)
        left join satuan s on (b.satuan_kekuatan = s.id)
        left join satuan st on (k.id_kemasan = st.id)
        where d.id = '$id' order by d.id desc";
        return $this->db->query($sql);
    }
    
    function save_distribusi() {
        $this->db->trans_begin();
        $id_dist    = post_param('id');
        $tanggal    = date2mysql(post_param('tanggal'));
        $unit       = $this->session->userdata('id_unit');
        $tujuan     = post_param('id_unit');
        $id_user    = $this->session->userdata('id_user');
        
        if ($id_dist === '') {
            $data_dist  = array(
                'tanggal' => $tanggal,
                'id_unit_asal' => $unit,
                'id_unit_tujuan' => $tujuan,
                'id_users' => $id_user
            );
            $this->db->insert('distribusi', $data_dist);
            $id = $this->db->insert_id();
            $id_barang  = post_param('id_barang');
            $id_kemasan = post_param('kemasan');
            $jumlah     = post_param('jumlah');
            $ed         = post_param('ed');
            
            foreach ($id_barang as $key => $data) {
                $barang = $this->db->query("select * from kemasan where id_barang = '$data'")->row();
                
                $data_detail = array(
                    'id_distribusi' => $id,
                    'id_kemasan' => $barang->id,
                    'jumlah' => $jumlah[$key]
                );
                $this->db->insert('detail_distribusi', $data_detail);
                
                $data_stok  = array(
                    'waktu' => $tanggal.' '.date("H:i:s"),
                    'id_transaksi' => $id,
                    'transaksi' => 'Distribusi',
                    'nobatch' => '',
                    'id_barang' => $data,
                    'ed' => $ed[$key],
                    'keluar' => $jumlah[$key],
                    'id_users' => $id_user,
                    'id_unit' => $unit
                );
                $this->db->insert('stok', $data_stok);
            }
            
        } else {
            $this->db->delete('detail_distribusi', array('id' => $id_dist));
            $this->db->delete('stok', array('id_transaksi' => $id_dist, 'transaksi' => 'Distribusi'));
            $data_dist  = array(
                'tanggal' => $tanggal,
                'id_unit_asal' => $unit,
                'id_unit_tujuan' => $tujuan,
                'id_users' => $id_user
            );
            $this->db->where('id', $id_dist);
            $this->db->insert('distribusi', $data_dist);
            
            $id_barang  = post_param('id_barang');
            $id_kemasan = post_param('kemasan');
            $jumlah     = post_param('jumlah');
            $ed         = post_param('ed');
            
            foreach ($id_barang as $key => $data) {
                $barang = $this->db->query("select * from kemasan where id_barang = '$data'")->row();
                $data_detail = array(
                    'id_distribusi' => $id_dist,
                    'id_kemasan' => $barang->id,
                    'jumlah' => $jumlah[$key]
                );
                $this->db->insert('detail_distribusi', $data_detail);
                
                $data_stok  = array(
                    'waktu' => $tanggal.' '.date("H:i:s"),
                    'id_transaksi' => $id_dist,
                    'transaksi' => 'Distribusi',
                    'nobatch' => '',
                    'id_barang' => $data,
                    'ed' => $ed[$key],
                    'keluar' => $jumlah[$key],
                    'id_users' => $id_user,
                    'id_unit' => $unit
                );
                $this->db->insert('stok', $data_stok);
            }
            
        }
        
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $result['status'] = FALSE;
        } else {
            $this->db->trans_commit();
            $result['status'] = TRUE;
        }
        
        return $result;
    }
    
    /*PENERIMAAN DISTRIBUSI*/
    function get_data_penerimaan_distribusi($limit, $start, $search) {
        $q = NULL;
        if (isset($search['awal'])) {
            $q.=" and d.tanggal between '".$search['awal']."' and '".$search['akhir']."'";
        }
        $sql = "select d.*, dd.jumlah, concat_ws(' ',b.nama,b.kekuatan,s.nama) as nama_barang, st.nama as kemasan,
        u.nama as asal, un.nama as tujuan
        from penerimaan_distribusi pd
        join distribusi d on (d.id = pd.id_distribusi)
        join detail_distribusi dd on (d.id = dd.id_distribusi)
        join unit u on (d.id_unit_asal = u.id)
        join unit un on (d.id_unit_tujuan = un.id)
        join kemasan k on (k.id = dd.id_kemasan)
        join barang b on (k.id_barang = b.id)
        left join satuan s on (b.satuan_kekuatan = s.id)
        left join satuan st on (k.id_kemasan = st.id)
        where d.id_unit_tujuan = '".$this->session->userdata('id_unit')."' $q order by d.id";
        $limitation = " limit ".$start.", ".$limit."";
        $query = $this->db->query($sql . $limitation);
        //echo $sql . $q . $limitation;
        $queryAll = $this->db->query($sql);
        $data['data'] = $query->result();
        $data['jumlah'] = $queryAll->num_rows();
        return $data;
    }
    
    function save_penerimaan_distribusi() {
        $data = array(
            'waktu' => date("Y-m-d H:i:s"),
            'id_distribusi' => post_param('no_dist'),
            'id_users' => $this->session->userdata('id_user')
        );
        $this->db->insert('penerimaan_distribusi', $data);
        return $this->db->insert_id();
    }
}
?>
