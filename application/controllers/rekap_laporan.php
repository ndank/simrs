<?php

class Rekap_laporan extends CI_Controller {
    
    function __construct() {
        parent::__construct();
        $this->load->model(array('m_laporan', 'm_registrasi_rs', 'm_referensi', 'm_pelayanan'));
        date_default_timezone_set('Asia/Jakarta');

    }

    /* Rekap Laporan */
    function laporan(){
        $this->load->model('m_laporan');
        $data['title'] = "Rekap Laporan (RL)";
        $data['bulan'] = $this->m_laporan->get_bulan();
        $data['tahun'] = $this->m_laporan->get_tahun();
        $data['bulan_now'] = array(date("m"));
        $data['tahun_now'] = array(date("Y"));
        $data['rl'] = $this->db->order_by('kode_rl','asc')->get('rekap_laporan')->result();
        $this->load->view('registrasi/rekap_laporan', $data);
    }
    /* Rekap Laporan */

    function cetak_rl1_1($tahun) {
        $data['title'] = "Formulir RL 1.1";
        $data['rs'] = $this->m_registrasi_rs->get_last_register_data($tahun);
        $data['spesialis'] = $this->m_registrasi_rs->get_data_spesialisasi_pegawai('Ya','Medis');
        $data['perawat'] = $this->m_registrasi_rs->get_data_spesialisasi_pegawai('Ya','Keperawatan');
        $data['farmasi'] = $this->m_registrasi_rs->get_data_spesialisasi_pegawai('Ya','Kefarmasian');
        $data['nakes_lain'] = $this->m_registrasi_rs->get_data_spesialisasi_pegawai('Ya','Nakes Lain');
        $data['non'] = $this->m_registrasi_rs->get_data_spesialisasi_pegawai('Tidak');
        $this->load->view('rl/rl1_1_data_rs', $data);
    }

    function cetak_rl1_2(){
        $this->load->model("m_registrasi_rs");
        $data['title'] = "Formulir RL 1.2<br/>Indikator Pelayanan RS";
        $data['rs'] = $this->m_registrasi_rs->get_last_register_data(date('Y'));

        $reg = $this->m_registrasi_rs->register_get_data(null, null, 'null');
        
        foreach ($reg['data'] as $key => $value) {
            $th = new DateTime($value->waktu);
            $th_reg = $th->format('Y');


            $param = array(
                    'awal' => "01/01/".$th_reg,
                    'akhir' => (($th_reg == date('Y'))?date('d/m/'):"31/12/").$th_reg,
                    'kondisi' => ''
                );
            $rata = $this->m_pelayanan->get_ratarata_kunjungan_pasien($param);

            $bed = $this->m_pelayanan->get_jumlah_layanan_irna();
            $hari_perawatan =  $this->m_pelayanan->get_jumlah_hari_perawatan($param);
            $jumlah_pasien = $this->m_pelayanan->get_jumlah_pasien_irna($param);

            $nilai_bor = ($hari_perawatan['hari'] / ($bed * $hari_perawatan['periode'])) * 0.01;
            if($jumlah_pasien->jumlah != 0){
                $nilai_toi = (($bed * $hari_perawatan['periode']) - $hari_perawatan['hari'] ) / $jumlah_pasien->jumlah ;
            }else{
                $nilai_toi = 0;
            }

            $param['kondisi'] = 'Meninggal';
            $pasien_mati = $this->m_pelayanan->get_jumlah_pasien_irna($param);
            $ndr_pasien_mati = $this->m_pelayanan->get_jumlah_pasien_irna($param, 'ndr');

            $nilai_alos = (($jumlah_pasien->jumlah != null) && ($jumlah_pasien->lama_inap != null)) ? round($jumlah_pasien->lama_inap / $jumlah_pasien->jumlah, 5):'-';
            $nilai_bto = (($jumlah_pasien->jumlah != null) && ($bed != null)) ? round($jumlah_pasien->jumlah / $bed, 5):'-'; 
            $nilai_ndr = (($jumlah_pasien->jumlah != null) & ($jumlah_pasien->jumlah != 0) && ($ndr_pasien_mati->jumlah != null)) ? round($ndr_pasien_mati->jumlah / $jumlah_pasien->jumlah, 5):'0';
            $nilai_gdr = (($jumlah_pasien->jumlah != null) & ($jumlah_pasien->jumlah != 0) && ($pasien_mati->jumlah != null)) ? round($pasien_mati->jumlah / $jumlah_pasien->jumlah, 5):'0';

            $data['indikator'][] = (Object)array(
                    'tahun' => $th_reg,
                    'hari' => $hari_perawatan['hari'],
                    'periode' => $hari_perawatan['periode'],
                    'bed' => $bed,
                    'bor' => round($nilai_bor, 5),
                    'toi' => round($nilai_toi, 5),
                    'alos' => round($nilai_alos, 5),
                    'bto' => round($nilai_bto, 5),
                    'ndr' => $nilai_ndr,
                    'gdr' => $nilai_gdr,
                    'rata' => $rata
                );
        }

        $this->load->view('rl/rl1_2_indikator_pelayanan', $data);
    }

    function cetak_rl3_1($tahun = null){
        $this->load->model("m_registrasi_rs");
        $data['title'] = "Formulir RL 3.1<br/>Kegiatan Pelayanan Rawat Inap";
        $data['rs'] = $this->m_registrasi_rs->get_last_register_data(date('Y'));
        $data['inap'] = $this->m_pelayanan->get_rl31_data($tahun);
        $this->load->view('rl/rl3_1_rawat_inap', $data);
    }

    function cetak_rl1_3(){
        $this->load->model("m_registrasi_rs");
        $data['title'] = "Formulir RL 1.3<br/>Tempat Tidur";
        $data['rs'] = $this->m_registrasi_rs->get_last_register_data(date('Y'));
        $data['tt'] = $this->m_referensi->get_rl1_3_data();
        $this->load->view('rl/rl1_3_tempat_tidur', $data);
    }
    function cetak_rl5_1($tahun = null, $bulan = null) {
        if ($tahun == null) {
            $tahun = date('Y');
        }

        if ($bulan == null) {
            $bulan = date('m');
        }
        $data["title"] = "RL 5.1<br/>Pengunjung Rumah Sakit";
        $data["rs"] = $this->m_registrasi_rs->get_last_register_data(date('Y'));
        $this->load->model('m_laporan');

        $param = array(
                'bl_from' => $bulan,
                'th_from' => $tahun,
                'bl_to' => $bulan,
                'th_to' => $tahun
                );
        $hasil = $this->m_laporan->get_kunjungan_bulanan_pasien($param);
        $data['tahun'] = $param['th_from'];
        $data['bulan'] = $param['bl_from'];
        if ($hasil != null) {
            $data['baru'] = $this->m_laporan->get_pasien_bl_baru($hasil);
            $data['lama'] = $this->m_laporan->get_pasien_bl_lama($hasil);
        }
        $this->load->view('rl/rl5_1_pengunjung', $data);
    }
    

}
?>