<?php

class Laporan extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->helper('functions');
        $this->load->model(array(
                'configuration','m_laporan', 'm_demografi', 'm_inventory',
                'm_referensi', 'm_resep', 'm_billing', 'm_akuntansi', 'm_pendaftaran'
            ));
        is_logged_in();
        
        date_default_timezone_set('Asia/Jakarta');
    }

    function kunjungan_rs() {
        $data['title'] = "Laporan Kunjungan";
        $data['tipe'] = array('pilihtipe' => 'Pilih tipe Laporan ...', 'harian' => 'Harian', 'bulanan' => 'Bulanan');
        $data['jenis'] = array('pilihjenis' => 'Pilih Jenis Laporan ...', 'pasien' => 'Berdasarkan Pasien', 'unit' => 'Berdasarkan layanan');

        $data['bulan'] = $this->m_laporan->get_bulan();
        $data['tahun'] = $this->m_laporan->get_tahun();
        $data['bulan_now'] = array(date("m"));
        $data['tahun_now'] = array(date("Y"));
        $this->load->view('laporan/kunjungan_rs', $data);
    }

    function kunjungan_rs_harian($from = null, $to = null) {
        $data['from'] = $from;
        $data['to'] = $to;
        $data['title'] = "Laporan Kunjungan RS Harian";


        $this->load->view('laporan/kunjungan_rs_harian_tab', $data);
    }

    function kunjungan_rs_bulanan($from = null, $to = null) {
        $data['title'] = "Laporan Kunjungan RS Bulanan";
        $data['from'] = $from;
        $data['to'] = $to;
        $data['bulan_now'] = array(date("m"));
        $data['tahun_now'] = array(date("Y"));
        $data['bulan'] = $this->m_laporan->get_bulan();
        $data['tahun'] = $this->m_laporan->get_tahun();
        $this->load->view('laporan/kunjungan_rs_bulanan_tab', $data);
    }

    function kunjungan_rs_harian_pasien($from = null, $to = null) {

        $data['from'] = $from;
        $data['to'] = $to;
        if ($from != 'undefined-undefined-') {
            $data['pencarian'] = "<center>Laporan Harian Kunjungan RS <br/> " . indo_tgl($from) . " s.d " . indo_tgl($to) . "</center>";
        } else {
            $data['pencarian'] = "<center>Laporan Harian Kunjungan RS <br/></center>";
        }

        $data['hasil'] = $this->m_laporan->get_kunjungan_harian(array('from' => $from, 'to' => $to));
        if ($data['hasil'] != null) {
            $data['pasienbaru'] = $this->m_laporan->get_pasien_baru($data['hasil']);
            $data['pasienlama'] = $this->m_laporan->get_pasien_lama($data['hasil']);
            $data['kasus'] = $this->m_laporan->get_status_diagnosa_kasus($data['hasil']);
        }

        $this->load->view('laporan/kunjungan_rs_harian_pasien', $data);
    }

    function kunjungan_rs_harian_unit($from = null, $to = null) {
        $this->load->model('unit_layanan');
        $data['from'] = $from;
        $data['to'] = $to;

        if ($from != 'undefined-undefined-') {
            $data['pencarian'] = "<center>Laporan kunjungan RS Harian per Unit<br/> " . indo_tgl($from) . " s.d " . indo_tgl($to) . "</center>";
        } else {
            $data['pencarian'] = "<center>Laporan kunjungan RS Harian per Unit<br/></center>";
        }

        $data['semua_unit'] = $this->unit_layanan->get_unit_layanan('nakes');
        $data['semua_unit']['igd'] = 'IGD';
        asort($data['semua_unit']);
        $data['hasil'] = $this->m_laporan->get_kunjungan_harian(array('from' => $from, 'to' => $to));
        
        if ($data['hasil'] != null) {
            foreach ($data['semua_unit'] as $key => $row) {
                $data['hasil_unit'][$row] = $this->m_laporan->get_kunjungan_harian_unit($data['hasil'], $key);
            }
        }


        $this->load->view('laporan/kunjungan_rs_harian_unit', $data);
    }

    function kunjungan_rs_bulanan_pasien($bl_from = null, $th_from = null, $bl_to = null, $th_to = null) {
        $data['bl_from'] = $bl_from;
        $data['th_from'] = $th_from;
        $data['bl_to'] = $bl_to;
        $data['th_to'] = $th_to;
        $data['pencarian'] = "<center>Laporan Bulanan Kunjungan RS <br/>" . tampil_bulan('' . '-' . $bl_from . '-' . '') . " " . $th_from . " s.d " . tampil_bulan('' . '-' . $bl_to . '-' . '') . " " . $th_to ;
        $data['hasil'] = $this->m_laporan->get_kunjungan_bulanan_pasien($data);
        if ($data['hasil'] != null) {
            $data['pasienbaru'] = $this->m_laporan->get_pasien_bl_baru($data['hasil']);
            $data['pasienlama'] = $this->m_laporan->get_pasien_bl_lama($data['hasil']);
            $data['kasus'] = $this->m_laporan->get_status_diagnosa_kasus_bulanan($data['hasil']);
        }
        $this->load->view('laporan/kunjungan_rs_bulanan_pasien', $data);
    }

    function kunjungan_rs_bulanan_unit($bl_from, $th_from, $bl_to, $th_to) {
        $this->load->model('unit_layanan');
        $data['bl_from'] = $bl_from;
        $data['th_from'] = $th_from;
        $data['bl_to'] = $bl_to;
        $data['th_to'] = $th_to;
        $data['pencarian'] = "<center>Laporan Bulanan kunjungan RS per Unit <br/>" . tampil_bulan('' . '-' . $bl_from . '-' . '') . " " . $th_from . " s.d " . tampil_bulan('' . '-' . $bl_to . '-' . '') . " " . $th_to . "</center>";
        $data['semua_unit'] = $this->unit_layanan->get_unit_layanan('nakes');
        $data['semua_unit']['igd'] = 'IGD';
        asort($data['semua_unit']);
        $data['hasil'] = $this->m_laporan->get_kunjungan_bulanan_pasien($data);
        if ($data['hasil'] != null) {
            foreach ($data['semua_unit'] as $key => $row) {
                $data['hasil_unit'][$row] = $this->m_laporan->get_kunjungan_bulanan_unit($data['hasil'], $key);
            }
        }
        $this->load->view('laporan/kunjungan_rs_bulanan_unit', $data);
    }

    function laporan_demografi() {
        $this->load->model('m_demografi');
        $data['title'] = "Demografi Pasien";
        $data['jenis'] = $this->m_demografi->jenis_demografi();
        $this->load->view('laporan/laporan_demografi', $data);
    }

    function laporan_demografi_wilayah($from = null, $to = null) {
        $this->load->model('m_demografi');
        $subtitle = "Demografi Pasien Berdasarkan ";

        $tipe = post_safe('kategori');
        $awal = (post_safe('fromdate') != '')?date2mysql(post_safe('fromdate')):NULL;
        $akhir = (post_safe('todate') != '')?date2mysql(post_safe('todate')):NULL;
        $param = post_safe('multi');

        if ($tipe == "kelurahan") {
            $subtitle .="Kelurahan <br/>";
        } else if ($tipe == "kecamatan") {
            $subtitle .="Kecamatan <br/>";
        } else if ($tipe == "kabupaten") {
            $subtitle .="Kabupaten <br/>";
        } else if ($tipe == "provinsi") {
            $subtitle .="Provinsi <br/>";
        }

        if ($awal != '') {
            $subtitle .= indo_tgl($awal) . " s.d " . indo_tgl($akhir);
        }

        $hasil = $this->m_laporan->get_demografi_wilayah($awal, $akhir, $tipe,$param);
        $hasil['kategori'] = $tipe;
        $hasil['title'] = $subtitle;
        echo json_encode($hasil);    
       
    }

    function laporan_demografi_kelamin($from = null, $to = null) {
        $this->load->model('m_demografi');
        $kelamin = $this->m_demografi->kelamin();
        unset($kelamin['']);
        $data['kelamin'] = $kelamin;
        $subtitle = "Demografi Pasien Berdasarkan Jenis Kelamin<br/>";
        
        if ($from != 'undefined-undefined-') {
            $subtitle .= indo_tgl($from);
        }
        if ($to != 'undefined-undefined-') {
            $subtitle .= " s.d " . indo_tgl($to);
        }
        $hasil = $this->m_laporan->get_demografi_kelamin(array('from' => $from, 'to' => $to, 'kelamin' => $data['kelamin']));
        $hasil['title'] = $subtitle;
        $hasil['tipe'] = "pie";
        echo json_encode($hasil);
    }

    function laporan_demografi_usia($from = null, $to = null) {
        $this->load->model('m_demografi');
        $data['usia'] = $this->m_demografi->rentang_usia();
        $data['format_usia'] = $this->m_demografi->format_usia();
        $subtitle = "Demografi Pasien Berdasarkan Rentang Usia<br/> ";
        if ($from != 'undefined-undefined-') {
            $subtitle .= indo_tgl($from);
        }
        if ($to != 'undefined-undefined-') {
            $subtitle .= " s.d " . indo_tgl($to);
        }
        $hasil = $this->m_laporan->get_demografi_usia(array('from' => $from, 'to' => $to, 'usia' => $data['usia'], 'format_usia' => $data['format_usia']));
        $hasil['title'] = $subtitle;
        $hasil['tipe'] = "pie";
        echo json_encode($hasil);
    }

    function laporan_demografi_agama($from = null, $to = null) {
        $this->load->model('m_demografi');
        $agama = $this->m_demografi->agama();
        unset($agama['']);
        $data['agama'] = $agama;
        $subtitle = "Demografi Pasien Berdasarkan Agama<br/> ";
        if ($from != 'undefined-undefined-') {
            $subtitle .= indo_tgl($from);
        }
        if ($to != 'undefined-undefined-') {
            $subtitle .= " s.d " . indo_tgl($to);
        }
        $hasil = $this->m_laporan->get_demografi_agama(array('from' => $from, 'to' => $to, 'agama' => $data['agama']));
        $hasil['title'] = $subtitle;
        $hasil['tipe'] = "pie";
        echo json_encode($hasil);
    }

    function laporan_demografi_pekerjaan($from = null, $to = null) {
        $data['pekerjaan'] = $this->m_laporan->kategori_demografi('pekerjaan');
        $subtitle = "Demografi Pasien Berdasarkan Pekerjaan<br/> ";
        if ($from != 'undefined-undefined-') {
            $subtitle .= indo_tgl($from);
        }
        if ($to != 'undefined-undefined-') {
            $subtitle .= " s.d " . indo_tgl($to);
        }
        $hasil = $this->m_laporan->get_demografi_pekerjaan(array('from' => $from, 'to' => $to, 'pekerjaan' => $data['pekerjaan']));
        $hasil['kategori'] = "Pendidikan";
        $hasil['title'] = $subtitle;
        $hasil['tipe'] = "bar";
        echo json_encode($hasil);
    }

    function laporan_demografi_pendidikan($from = null, $to = null) {
        $data['pendidikan'] = $this->m_laporan->kategori_demografi('pendidikan');
        $subtitle = "Demografi Pasien Berdasarkan Pendidikan<br/> ";
        if ($from != 'undefined-undefined-') {
            $subtitle .= indo_tgl($from);
        }
        if ($to != 'undefined-undefined-') {
            $subtitle .= " s.d " . indo_tgl($to);
        }
        $hasil = $this->m_laporan->get_demografi_pendidikan(array('from' => $from, 'to' => $to, 'pendidikan' => $data['pendidikan']));
        $hasil['kategori'] = "Pendidikan";
        $hasil['title'] = $subtitle;
        $hasil['tipe'] = "bar";
        echo json_encode($hasil);
    }

    function laporan_demografi_nikah($from = null, $to = null) {
        $this->load->model('m_demografi');
        $nikah = $this->m_demografi->stat_nikah();
        unset($nikah['']);
        $data['nikah'] = $nikah;
        $subtitle = "Demografi Pasien Berdasarkan Status Pernikahan<br/> ";
        if ($from != 'undefined-undefined-') {
            $subtitle .= indo_tgl($from);
        }
        if ($to != 'undefined-undefined-') {
            $subtitle .= " s.d " . indo_tgl($to);
        }
        $hasil = $this->m_laporan->get_demografi_nikah(array('from' => $from, 'to' => $to, 'nikah' => $data['nikah']));
        $hasil['title'] = $subtitle;
        $hasil['tipe'] = "pie";
        echo json_encode($hasil);
    }


    function laporan_demografi_darah($from = null, $to = null) {
        $this->load->model('m_demografi');
        $darah = $this->m_demografi->gol_darah();
        unset($darah['']);
        $data['darah'] = $darah;
        $subtitle = "Demografi Pasien Berdasarkan Golongan Darah<br/> ";
        if ($from != 'undefined-undefined-') {
            $subtitle .= indo_tgl($from);
        }
        if ($to != 'undefined-undefined-') {
            $subtitle .= " s.d " . indo_tgl($to);
        }
        $hasil = $this->m_laporan->get_demografi_darah(array('from' => $from, 'to' => $to, 'darah' => $data['darah']));
        $hasil['title'] = $subtitle;
        $hasil['tipe'] = "pie";
        echo json_encode($hasil);
    }
    
    function get_data_all_pasien() {
        $date   = mktime(0, 0, 0, date("m"), date("d")-7, date("Y"));
        $start  = date("Y-m-d", $date);
        $result = $this->m_laporan->get_data_all_pasien($start)->result();
        $data   = array();
        $juml   = array();
        foreach ($result as $hasil) {
            $data[] = indo_tgl_graph($hasil->MyJoinDate);
            $juml[] = (int)$hasil->jumlah;
        }
        die(json_encode(array('tanggal' => $data, 'jumlah' => $juml)));
    }
    

    function get_kelurahan($kec_id) {
        $this->db->where('kecamatan_id', $kec_id);
        $rows = $this->db->get('kelurahan')->result();
        die(json_encode($rows));
    }

    function get_kecamatan($kab_id) {
        $this->db->where('kabupaten_id', $kab_id);
        $this->db->order_by("nama", "asc"); 
        $rows = $this->db->get('kecamatan')->result();
        die(json_encode($rows));
    }

    function get_kabupaten($prov_id) {
        $this->db->where('provinsi_id', $prov_id);
        $this->db->order_by("nama", "asc"); 
        $rows = $this->db->get('kabupaten')->result();
        die(json_encode($rows));
    }

    function get_provinsi() {
        $this->db->order_by("nama", "asc"); 
        $rows = $this->db->get('provinsi')->result();
        die(json_encode($rows));
    }

    function resep() {
        $data['title'] = 'Rekap Salinan Resep';
        if (isset($_GET['awal'])) {
            $noresep = isset($_GET['noresep']) ? get_safe('noresep') : NULL;
            $awal = isset($_GET['awal']) ? get_safe('awal') : NULL;
            $akhir = isset($_GET['awal']) ? get_safe('akhir') : NULL;
            $pasien = isset($_GET['id_pasien']) ? get_safe('id_pasien') : NULL;
            $dokter = isset($_GET['id_dokter']) ? get_safe('id_dokter') : NULL;
            $apoteker = isset($_GET['id_apoteker']) ? get_safe('id_apoteker') : NULL;
            $data['list_data'] = $this->m_resep->resep_report_muat_data(null, $awal, $akhir, $pasien, $dokter, null, $apoteker)->result();
        }
        $this->load->view('laporan/info-resep', $data);
    }

    function penjualan_jasa() {
        $data['title'] = 'Rekap Jasa Klinis';
        if (isset($_GET['awal'])) {
            $param['awal'] = get_safe('awal');
            $param['akhir'] = get_safe('akhir');
            $param['nakes'] = get_safe('id_nakes');
            $data['list_data'] = $this->m_billing->penjualan_jasa_load_data($param)->result();
        }
        $this->load->view('laporan/penjualan-jasa', $data);
    }

    function rekap_pendapatan() {
        $data['title'] = 'Rekap Pendapatan Jasa';       
        $data['layanan'] = $this->m_pendaftaran->get_layanan_admission(); 
        if (isset($_GET['awal'])){
            $param['awal'] = get_safe('awal');
            $param['akhir'] = get_safe('akhir');
            $param['layanan'] = get_safe('id_layanan');
            $query = $this->m_billing->pendapatan_load_data($param);
            $data['list_data'] = $query['data'];
            $data['sum'] = $query['total'];
        }

        $this->load->view('laporan/rekap_pendapatan', $data);
    }

    function get_penjualan_jasa_list($param, $page) {
        $limit = 15;
        if ($page == 'undefined') {
            $page = 1;
        }
        $start = ($page - 1) * $limit;
        $data['page'] = $page;
        $data['limit'] = $limit;
        $query = $this->m_laporan->penjualan_jasa_load_data($limit, $start, $param);
        $data['jumlah'] = $query['jumlah'];
        $data['list_data'] = $query['data'];
        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, 1, '');
        $data['total_tarif'] = $query['total'];
        return $data;
    }

    function laporan_penjualan() {
        if (isset($_GET['awal'])) {
            $param['awal'] = get_safe('awal');
            $param['akhir'] = get_safe('akhir');
            $param['nakes'] = get_safe('id_nakes');
            $data = $this->get_penjualan_jasa_list($param, get_safe('page'));
        }
        $data['title'] = 'Laporan Penjualan Jasa';
        $this->load->view('laporan/laporan_penjualan', $data);
    }

    function resep_detail($id_resep) {
        $data['title'] = 'Resep';
        $data['list_data'] = $this->m_resep->data_resep_load_data($id_resep)->result();
        $this->load->view('laporan/resep_detail', $data);
    }

    function salin_resep($id_resep) {
        $data['resep'] = $this->m_resep->data_resep_load_data($id_resep)->result();
        $data['id_resep'] = $id_resep;
        $data['datas'] = $this->configuration->rumah_sakit_get_atribute()->row();
        $data['apa'] = $this->configuration->penduduk_manager_farmasi()->row();
        $this->load->view('inventory/print/resep', $data);
    }

    function statistika_resep() {
        $data['title'] = 'Statistika Resep';
        $data['statistika_resep'] = $this->m_resep->statistika_resep(get_safe('awal'), get_safe('akhir'))->result();
        $data['data'] = $this->configuration->rumah_sakit_get_atribute()->row();
        $this->load->view('inventory/print/statistika-resep', $data);
    }

    function hutang() {
        $data['title'] = 'Rekap Hutang Pembelian';
        if (isset($_GET['id_suplier'])) {
            $param['id_supplier'] = get_safe('id_suplier');
            $param['tempo'] = get_safe('tempo');
            $data['list_data'] = $this->m_inventory->hutang_load_data($param)->result();
        }
        $this->load->view('laporan/laporan-utang', $data);
    }
    
    function inkaso() {
        $data['title'] = 'Laporan Inkaso (Pembayaran Pembelian)';
        if (isset($_GET['id_suplier'])) {
            $param['id_supplier'] = get_safe('id_suplier');
            $param['awal'] = get_safe('awal');
            $param['akhir']= get_safe('akhir');
            $param['faktur'] = get_safe('nofaktur');
            $data['list_data'] = $this->m_inventory->inkaso_load_data($param)->result();
        }
        $this->load->view('laporan/laporan-inkaso', $data);
    }

    function laporan_utang_cetak() {
        $this->load->view('laporan/print/laporan-utang', $data);
    }

    function stok() {

        $data['title'] = 'Stok Perbekalan Farmasi';
        $data['jenis_transaksi'] = $this->m_inventory->jenis_transaksi_load_data();
        $data['unit'] = $this->m_referensi->unit_get_data('inventori');
        $data['perundangan'] = $this->m_referensi->perundangan_load_data();
        $data['generik'] = $this->m_referensi->generik_load_data();
        $data['sediaan'] = $this->m_referensi->sediaan_get_data();
        $data['kat_barang'] = 'Farmasi';
        if (isset($_GET['sort'])) {
            $param['awal'] = get_safe('awal');
            $param['akhir'] = get_safe('akhir');
            $param['id_pb'] = get_safe('id_pb');
            $param['ven'] = get_safe('ven');
            $param['ha'] = get_safe('ha');
            $param['perundangan'] = get_safe('perundangan');
            $param['generik'] = get_safe('generik');
            $param['jenis'] = get_safe('transaksi_jenis');
            $param['sort'] = get_safe('sort');
            $param['unit'] = get_safe('unit');
            $param['sediaan'] = get_safe('sediaan');
            $param['formularium'] = get_safe('formularium');
            $param['kategori'] = get_safe('kategori');
            //($awal = null, $akhir = null,$id_pb = null, $ven = null, $ha = null, $perundangan = null, $generik = null, $jenis = null, $sort = null, $unit = null)
            $data['kat_barang'] = 'Farmasi';
            $data['list_data'] = $this->m_inventory->informasi_stok_load_data($param)->result();
           
        }
        $data['sort'] = isset($_GET['sort'])?get_safe('sort'):'';
        $this->load->view('laporan/stok', $data);
    }
    
    function stok_gizi() {

        $data['title'] = 'Stok Perbekalan Gizi';
        $data['jenis_transaksi'] = $this->m_inventory->jenis_transaksi_load_data();
        $data['unit'] = $this->m_referensi->unit_get_data('inventori');
        $data['perundangan'] = $this->m_referensi->perundangan_load_data();
        $data['generik'] = $this->m_referensi->generik_load_data();
        $data['sediaan'] = $this->m_referensi->sediaan_get_data();
        $data['kat_barang'] = 'Gizi';
        if (isset($_GET['sort'])) {
            $param['awal'] = get_safe('awal');
            $param['akhir'] = get_safe('akhir');
            $param['id_pb'] = get_safe('id_pb');
            $param['ven'] = get_safe('ven');
            $param['ha'] = get_safe('ha');
            $param['perundangan'] = get_safe('perundangan');
            $param['generik'] = get_safe('generik');
            $param['jenis'] = get_safe('transaksi_jenis');
            $param['sort'] = get_safe('sort');
            $param['unit'] = get_safe('unit');
            $param['sediaan'] = get_safe('sediaan');
            $param['formularium'] = get_safe('formularium');
            $param['kategori'] = get_safe('kategori');
            //($awal = null, $akhir = null,$id_pb = null, $ven = null, $ha = null, $perundangan = null, $generik = null, $jenis = null, $sort = null, $unit = null)
            $data['kat_barang'] = 'Gizi';
            $data['list_data'] = $this->m_inventory->informasi_stok_load_data($param)->result();
        }
        $data['sort'] = isset($_GET['sort'])?get_safe('sort'):'';
        $this->load->view('laporan/stok', $data);
    }
    
    function stok_rt() {

        $data['title'] = 'Stok Perbekalan Rumah Tangga';
        $data['jenis_transaksi'] = $this->m_inventory->jenis_transaksi_load_data();
        $data['unit'] = $this->m_referensi->unit_get_data('inventori');
        $data['perundangan'] = $this->m_referensi->perundangan_load_data();
        $data['generik'] = $this->m_referensi->generik_load_data();
        $data['sediaan'] = $this->m_referensi->sediaan_get_data();
        $data['kat_barang'] = 'Rumah Tangga';
        if (isset($_GET['sort'])) {
            $param['awal'] = get_safe('awal');
            $param['akhir'] = get_safe('akhir');
            $param['id_pb'] = get_safe('id_pb');
            $param['ven'] = get_safe('ven');
            $param['ha'] = get_safe('ha');
            $param['perundangan'] = get_safe('perundangan');
            $param['generik'] = get_safe('generik');
            $param['jenis'] = get_safe('transaksi_jenis');
            $param['sort'] = get_safe('sort');
            $param['unit'] = get_safe('unit');
            $param['sediaan'] = get_safe('sediaan');
            $param['formularium'] = get_safe('formularium');
            $param['kategori'] = get_safe('kategori');
            //($awal = null, $akhir = null,$id_pb = null, $ven = null, $ha = null, $perundangan = null, $generik = null, $jenis = null, $sort = null, $unit = null)
            $data['kat_barang'] = 'Rumah Tangga';
            $data['list_data'] = $this->m_inventory->informasi_stok_load_data($param)->result();
        }
        $data['sort'] = isset($_GET['sort'])?get_safe('sort'):'';
        $this->load->view('laporan/stok', $data);
    }

    function print_stok() {
        if (isset($_GET['sort'])) {
            $param['awal'] = get_safe('awal');
            $param['akhir'] = get_safe('akhir');
            $param['id_pb'] = get_safe('id_pb');
            $param['ven'] = get_safe('ven');
            $param['ha'] = get_safe('ha');
            $param['sediaan'] = get_safe('sediaan');
            $param['perundangan'] = get_safe('perundangan');
            $param['generik'] = get_safe('generik');
            $param['jenis'] = get_safe('transaksi_jenis');
            $param['sort'] = get_safe('sort');
            $param['unit'] = get_safe('unit');
            $param['formularium'] = get_safe('formularium');
            $param['kategori'] = get_safe('kategori');
            //($awal = null, $akhir = null,$id_pb = null, $ven = null, $ha = null, $perundangan = null, $generik = null, $jenis = null, $sort = null, $unit = null)
            $data['list_data'] = $this->m_inventory->informasi_stok_load_data($param)->result();
            $data['period'] = $param['awal'] . ' s.d ' . $param['akhir'];
            if ($param['awal'] == '' and $param['akhir'] == '') {
                $data['period'] = '';
            }
            $data['datas'] = $this->configuration->rumah_sakit_get_atribute()->row();
        }
        $this->load->view('inventory/print/stok', $data);
    }

    function psikotropika() {
        $param['awal'] = get_safe('awal');
        $param['akhir'] = get_safe('akhir');
        $param['id_pb'] = get_safe('id_pb');
        $param['ven'] = get_safe('ven');
        $param['ha'] = get_safe('ha');
        $param['perundangan'] = get_safe('perundangan');
        $param['generik'] = get_safe('generik');
        $param['jenis'] = get_safe('transaksi_jenis');
        $param['sort'] = get_safe('sort');
        $param['unit'] = get_safe('unit');
        $data['datas'] = $this->configuration->rumah_sakit_get_atribute()->row();
        $data['list_data'] = $data['list_data'] = $this->m_inventory->informasi_stok_load_data($param)->result();
        $this->load->view('inventory/print/lap-psikotropika', $data);
    }

    function stelling() {
        $data['title'] = 'Kartu Stelling';
        $data['stelling'] = $this->m_inventory->stelling_load_data(get_safe('id_pb'), get_safe('awal'), get_safe('akhir'))->result();
        $data['list_data'] = $this->m_inventory->stelling_list_data(get_safe('id_pb'), get_safe('awal'), get_safe('akhir'))->result();
        $data['apt'] = $this->configuration->rumah_sakit_get_atribute()->row();
        $this->load->view('inventory/print/stelling', $data);
    }

    function kas() {
        $data['title'] = 'Arus Kas Kasir';
        $data['jenis_transaksi'] = $this->configuration->jenis_transaksi();
        $this->load->view('laporan/kas', $data);
    }

    function kas_load_data() {
        $awal = get_safe('awal');
        $akhir = get_safe('akhir');
        $jenis = get_safe('jenis');
        $nama = get_safe('nama');
        $data['list_data'] = $this->m_inventory->kas_load_data($awal, $akhir, $jenis, $nama)->result();
        $this->load->view('laporan/kas-table', $data);
    }

    function reimbursement() {
        $data['title'] = 'Rekap Reimbursement';
        if (isset($_GET['awal'])) {
            $awal = isset($_GET['awal']) ? get_safe('awal') : NULL;
            $akhir = isset($_GET['akhir']) ? get_safe('akhir') : NULL;
            $id_asuransi = isset($_GET['id_asuransi']) ? get_safe('id_asuransi') : NULL;
            $data['list_data'] = $this->m_inventory->reimbursement_load_data($awal, $akhir, $id_asuransi)->result();
        }
        $this->load->view('laporan/reimbursement', $data);
    }

    function rekap_laporan() {
        $data['title'] = 'Rekap Laporan';
        $data['apt'] = $this->configuration->rumah_sakit_get_atribute()->row();
        $data['og'] = $this->m_resep->data_item_obat('Generik')->num_rows();
        $data['ng_form'] = $this->m_resep->data_item_obat('Non Generik', 'Ya')->num_rows();
        $data['ng'] = $this->m_resep->data_item_obat('Non Generik')->num_rows();
        $data['og_2'] = $this->m_resep->data_item_obat('Generik', NULL, TRUE, get_safe('awal'), get_safe('akhir'))->num_rows();
        $data['ng_form_2'] = $this->m_resep->data_item_obat('Non Generik', 'Ya', TRUE, get_safe('awal'), get_safe('akhir'))->num_rows();
        $data['ng_2'] = $this->m_resep->data_item_obat('Non Generik', NULL, TRUE, get_safe('awal'), get_safe('akhir'))->num_rows();

        $data['og_3'] = $this->m_resep->data_item_obat('Generik', 'Ya', TRUE, get_safe('awal'), get_safe('akhir'))->num_rows();
        $data['ng_form_3'] = $this->m_resep->data_item_obat('Non Generik', 'Ya', TRUE, get_safe('awal'), get_safe('akhir'))->num_rows();
        $data['ng_3'] = $this->m_resep->data_item_obat('Non Generik', 'Ya', TRUE, get_safe('awal'), get_safe('akhir'))->num_rows();

        $data['g_rj'] = $this->m_resep->pelayanan_resep('Generik', 'Rawat Jalan')->num_rows();
        $data['g_igd'] = $this->m_resep->pelayanan_resep('Generik', 'IGD')->num_rows();
        $data['g_ri'] = $this->m_resep->pelayanan_resep('Generik', 'Rawat Inap')->num_rows();

        $data['ngf_rj'] = $this->m_resep->pelayanan_resep('Non Generik', 'Rawat Jalan', 'Ya')->num_rows();
        $data['ngf_igd'] = $this->m_resep->pelayanan_resep('Non Generik', 'IGD', 'Ya')->num_rows();
        $data['ngf_ri'] = $this->m_resep->pelayanan_resep('Non Generik', 'Rawat Inap', 'Ya')->num_rows();

        $data['ng_rj'] = $this->m_resep->pelayanan_resep('Non Generik', 'Rawat Jalan')->num_rows();
        $data['ng_igd'] = $this->m_resep->pelayanan_resep('Non Generik', 'IGD')->num_rows();
        $data['ng_ri'] = $this->m_resep->pelayanan_resep('Non Generik', 'Rawat Inap')->num_rows();


        $this->load->view('laporan/rekap-laporan', $data);
    }

    function laporan_abc() {
        $param['awal'] = get_safe('awal');
        $param['akhir'] = get_safe('akhir');
        $param['id_pb'] = get_safe('id_pb');
        $param['ven'] = get_safe('ven');
        $param['ha'] = get_safe('ha');
        $param['perundangan'] = get_safe('perundangan');
        $param['generik'] = get_safe('generik');
        $param['jenis'] = 'Penjualan';
        $param['sort'] = 'History';
        $param['unit'] = get_safe('unit');
        $param['laporan'] = 'abc';
        $param['sediaan'] = NULL;
        $data['apt'] = $this->configuration->rumah_sakit_get_atribute()->row();
        $data['list_data'] = $this->m_inventory->informasi_stok_load_data($param)->result();
        $this->load->view('laporan/laporan-abc', $data);
    }

    function rujukan() {
        $data['title'] = 'Laporan Rujukan';
        $this->load->view('laporan/rujukan', $data);
    }

    function rujukan_data() {
        $limit = 15;
        $param = array(
            'from' => date2mysql(post_safe('fromdate')),
            'to' => date2mysql(post_safe('todate')),
            'instansi' => post_safe('id_instansi'),
            'nakes' => post_safe('id_nakes')
        );
        $page = post_safe('p');

        if ($page == 'undefined') {
            $page = 1;
        }
        $start = ($page - 1) * $limit;
        $data = $this->m_laporan->rujukan_get_data($limit, $start, $param);
        
        $data['page'] = $page;
        $data['limit'] = $limit;
        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, 1, 'null');

        if ($param['from'] != '') {
            $data['from'] = $param['from'];
        }

        if ($param['to'] != '') {
            $data['to'] = $param['to'];
        }

        $this->load->view('laporan/rujukan_list', $data);
    }

    function pembayaran_billing_pasien_detail($no_daftar) {
        $data['title'] = 'Pembayaran Billing Pasien';
        $data['pasien'] = $this->m_billing->pasien_load_data($no_daftar)->row();
        $data['pasien'] = $this->m_billing->pasien_load_data($no_daftar)->row();
//        $data['asuransi'] = $this->m_billing->get_asuransi_pembayaran($id)->result();
//        
//        $data['list_data'] = $this->m_billing->penjualan_barang_load_data_detail($id)->result();
//        $data['jasa_list_data'] = $this->m_billing->penjualan_jasa_detail_load_data_detail($id)->result();
//        $data['rawat_inap'] = $this->m_billing->rawat_inap_detail_load_data_detail($id)->result();
        
        $data['daftar_kunjungan'] = $this->m_billing->get_data_tagihan_jasa($no_daftar, 'Pendaftaran Kunjungan')->result();
        $data['akomodasi_kamar_inap'] = $this->m_billing->get_data_tagihan_jasa($no_daftar, 'Akomodasi Kamar Inap')->result();
        $data['selain'] = $this->m_billing->get_data_tagihan_jasa($no_daftar)->result();
        $data['barang_list_data'] = $this->m_billing->penjualan_barang_load_data2($no_daftar)->result();
        
        $this->load->view('laporan/pembayaran_billing_pasien_detail', $data);
    }
    
    function rekap_resep() {
        $data['title'] = 'Laporan Resep Dokter';
        if (isset($_GET['awal'])) {
            $noresep = isset($_GET['noresep']) ? get_safe('noresep') : NULL;
            $awal = isset($_GET['awal']) ? get_safe('awal') : NULL;
            $akhir = isset($_GET['awal']) ? get_safe('akhir') : NULL;
            $pasien = isset($_GET['id_pasien']) ? get_safe('id_pasien') : NULL;
            $dokter = isset($_GET['id_dokter']) ? get_safe('id_dokter') : NULL;
            $apoteker = isset($_GET['id_apoteker']) ? get_safe('id_apoteker') : NULL;
            $data['list_data'] = $this->m_resep->resep_dokter_muat_data(null, $awal, $akhir, $pasien, $dokter, null, $apoteker)->result();
        }
        $this->load->view('laporan/rekap-resep', $data);
    }
    
    function rekap_resep_dokter() {
        $data['title'] = 'Rekap. R/ Resep';
        if (isset($_GET['awal'])) {
            $noresep = isset($_GET['noresep']) ? get_safe('noresep') : NULL;
            $awal = isset($_GET['awal']) ? get_safe('awal') : NULL;
            $akhir = isset($_GET['awal']) ? get_safe('akhir') : NULL;
            $pasien = isset($_GET['id_pasien']) ? get_safe('id_pasien') : NULL;
            $dokter = isset($_GET['id_dokter']) ? get_safe('id_dokter') : NULL;
            $apoteker = isset($_GET['id_apoteker']) ? get_safe('id_apoteker') : NULL;
            $data['list_data'] = $this->m_resep->resep_dokter_muat_data(null, $awal, $akhir, $pasien, $dokter, null, $apoteker)->result();
        }
        $this->load->view('laporan/rekap-resep-dokter', $data);
    }
    
    function igd() {
        $data['title'] = 'Rekap Kunjungan Gawat Darurat';
        $this->load->view('laporan/igd', $data);
    }
    
    function kegiatan_rujukan() {
        $data['title'] = 'Rekap Kegiatan Rujukan';
        $this->load->view('laporan/kegiatan-rujukan', $data);
    }
    
    function kegiatan_rujukan_load_data() {
        $data = array(
            'awal' => date2mysql(get_safe('awal')),
            'akhir' => date2mysql(get_safe('akhir')),
            'jenis' => get_safe('jenis'),
            'dikembalikan' => get_safe('dikembalikan')
        );    
        $data['list_data'] = $this->m_laporan->kegiatan_rujukan_load_data($data)->result();
        $this->load->view('laporan/kegiatan-rujukan-table', $data);
    }
    
    function kebidanan() {
        $data['title'] = 'Rekap Kegiatan Kebidanan';
        $this->load->view('laporan/kebidanan', $data);
    }
    
    function kebidanan_load_data() {
        $data = array(
            'awal' => date2mysql(get_safe('awal')),
            'akhir' => date2mysql(get_safe('akhir')),
            'jenis' => get_safe('jenis'),
            'jnakes' => get_safe('jnakes'),
            'rujukan' => get_safe('rujukan'),
            'dirujuk' => get_safe('dirujuk')
        );
        $data['list_data'] = $this->m_laporan->kebidanan_load_data($data)->result();
        $this->load->view('laporan/kebidanan-table', $data);
    }
    
    function rekap_perinatologi() {
        $data['title'] = 'Rekap Kegiatan Perinatologi';
        $this->load->view('laporan/perinatologi', $data);
    }
    
    function perinatori_load_data() {
        $data = array(
            'awal' => date2mysql(get_safe('awal')),
            'akhir' => date2mysql(get_safe('akhir')),
            'jenis' => get_safe('jenis'),
            'jnakes' => get_safe('jnakes'),
            'rujukan' => get_safe('rujukan'),
            'dirujuk' => get_safe('dirujuk')
        );
        $data['list_data'] = $this->m_laporan->perinatori_load_data($data)->result();
        $this->load->view('laporan/perinatologi-table', $data);
    }
    
    function rekap_pembedahan() {
        $data['title'] = 'Rekap Kegiatan Pembedahan';
        $this->load->view('laporan/pembedahan', $data);
    }
    
    function pembedahan_load_data() {
        $data = array(
            'awal' => isset($_GET['awal'])?date2mysql(get_safe('awal')):'',
            'akhir' => isset($_GET['akhir'])?date2mysql(get_safe('akhir')):'',
            'bobot' => isset($_GET['bobot'])?get_safe('bobot'):'',
        );
        $data['list_data'] = $this->m_laporan->pembedahan_load_data($data)->result();
        $this->load->view('laporan/pembedahan-table', $data);
    }
    
    function neraca() {
        $data['title'] = 'Neraca';
        $this->load->view('laporan/neraca', $data);
    }
    
    function neraca_load_data() {
        $param = array(
            'awal' => get_safe('awal'),
            'akhir' => get_safe('akhir')
        );
        $data['aktiva'] = $this->m_akuntansi->neraca_load_data('aset')->result();
        $data['pasiva'] = $this->m_akuntansi->neraca_load_data('kewajiban')->result();
        $data['ekuitas'] = $this->m_akuntansi->neraca_load_data('ekuitas')->result();
        $data['pendapatan_operasional'] = $this->m_akuntansi->data_rekening_load_data(4)->result();
        $data['beban_operasional'] = $this->m_akuntansi->data_rekening_load_data(5)->result();
        $this->load->view('laporan/neraca-table', $data);
    }
    
    function abc() {
        $data['title'] = 'Laporan ABC';
        $this->load->view('laporan/lap-abc', $data);
    }
    
    function load_data_abc() {
        $awal  = date2mysql(get_safe('awal'));
        $akhir = date2mysql(get_safe('akhir'));
        $jenis = get_safe('jenis');
        $data['list_data'] = $this->m_inventory->load_data_abc($awal, $akhir, $jenis)->result();
        $this->load->view('laporan/lap-abc-list', $data);
    }

    function demografi_wilayah(){
        $data['title'] = "Demografi Pasien Berdasarkan Wilayah";
        $this->load->view('laporan/demografi_wilayah', $data);
    }

    
    
    function probabilitas() {
        $data['title'] = 'Laporan Probabilitas';
        $this->load->view('laporan/lap-probabilitas', $data);
    }
    
    function load_data_lap_probabilitas() {
        $awal  = date2mysql(get_safe('awal'));
        $akhir = date2mysql(get_safe('akhir'));
        $jenis = 'Penjualan';
        $data['list_data'] = $this->m_inventory->load_data_probabilitas($awal, $akhir, $jenis)->result();
        $this->load->view('laporan/lap-probabilitas-list', $data);
    }
    
    function load_data_graphic_abc() {
        $awal  = date("Y-m-01");
        $akhir = date("Y-m-31");
        $jenis = 'Penjualan';
        $result = $this->m_inventory->load_data_abc($awal, $akhir, $jenis)->result();
        $data   = array();
        $total = 0;
        foreach ($result as $rows) {
            $total = $total+$rows->total_nilai;
        }
        $persen = array();
        foreach ($result as $hasil) {
            $data[] = $hasil->nama_barang;
            $persen[] = (int)$persen+(($hasil->total_nilai/$total)*100);
        }
        die(json_encode(array('barang' => $data, 'persen' => $persen)));
    }
    
    function load_data_graphic_probabilitas() {
        $awal  = date("Y-m-01");
        $akhir = date("Y-m-31");
        $jenis = 'Penjualan';
        $result = $this->m_inventory->load_data_probabilitas($awal, $akhir, $jenis)->result();
        $data = array();
        $juml = array();
        foreach ($result as $hasil) {
            $data[] = $hasil->nama_barang;
            $juml[] = (int)$hasil->jumlah_pemakaian;
        }
        die(json_encode(array('barang' => $data, 'jumlah' => $juml)));
    }

    function dashboard_data_pasien_lama_baru(){
        $date   = mktime(0, 0, 0, date("m"), date("d")-6, date("Y"));
        $start  = date("Y-m-d", $date);
        $tgl = createRange($start, date('Y-m-d'));
        $status = $this->m_laporan->data_pasien_lama_baru($tgl);
        $result['data'] = array(
                array('type'=>'spline', 'name'=>'Pasien Lama', 'data'=>$status['lama']),
                array('type'=>'spline', 'name'=>'Pasien Baru', 'data'=>$status['baru']),
            );

        $result['title'] = "Grafik Status Kunjungan Pasien";
        foreach ($tgl as $key => $value) {
             $result['tanggal'][] = date('d M', strtotime($value));
        }
       
        die(json_encode($result));

    }

   function dashboard_data_pasien_per_unit(){
        $this->load->model('unit_layanan');
        $date   = mktime(0, 0, 0, date("m"), date("d")-6, date("Y"));
        $start  = date("Y-m-d", $date);
        $tgl = createRange($start, date('Y-m-d'));
        
        $result['data'] = array();
        $result['title'] = "Grafik Kunjungan Pasien per Unit";

        $sql = "select  u.id ,u.nama, jj.nama as jenis from pelayanan_kunjungan p 
                join jurusan_kualifikasi_pendidikan u ON ( p.id_jurusan_kualifikasi_pendidikan = u.id )
                left join jenis_jurusan_kualifikasi_pendidikan jj on (u.id_jenis_jurusan_kualifikasi_pendidikan = jj.id)
                where p.id_kunjungan is not null and date(p.waktu) between '$start' and '".date('Y-m-d')."' 
                group by u.id ";

        $unit = $this->db->query($sql)->result();
        //echo $sql;
        //echo print_r($unit);

        foreach ($unit as $key => $value) {
            $status = $this->m_laporan->data_pasien_per_unit($value->id,$tgl);
            $result['data'][] = array('type'=>'spline', 'name'=>$value->nama.", ".$value->jenis, 'data'=>$status);
        }

        foreach ($tgl as $key => $value) {
            $result['tanggal'][] = date('d M', strtotime($value));

        }
       
        die(json_encode($result));

    }

    function dashboard_data_diagnosis(){
        $awal = date('Y-m-01 00:00:00');
        $akhir = date('Y-m-t 23:59:59');
        $data = $this->m_laporan->data_top10_diagnosis($awal, $akhir);
        $result['nama'] = array();
        $result['jumlah'] = array();
        $result['title'] = "Data 10 Penyakit yang Diderita Pasien Kurun Waktu 1 Bulan";
        foreach ($data as $key => $value) {
            $result['nama'][] = $value->nama;
            $result['jumlah'][] = (int)$value->jumlah;
        }
        die(json_encode($result));
    }


}

?>