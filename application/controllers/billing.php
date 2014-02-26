<?php

class Billing extends CI_Controller {

    function __construct() {
        parent::__construct();
        is_logged_in();
        $this->load->model('m_billing');
        $this->load->library('session');
        $this->load->helper('login');
        $this->load->helper('functions');
        $this->load->library('form_validation');
        $this->load->helper('url');
        date_default_timezone_set('Asia/Jakarta');
        
    }

    function index($no_daftar = null) {
        $data['title'] = 'Rekap Billing Pasien';
        //$this->load->view('layout');
        if ($no_daftar != null) {
            $data['pasien'] = $this->m_billing->get_data_pasien($no_daftar);
            $this->load->view('billing/billing', $data);
        } else {
            $this->load->view('billing/billing', $data);
        }
    }

    function pembayaran($var1 = null, $var2 = null) {
        // $var1 = no_daftar
        $data = null;
        if ($var1 != null and $var1 == 'saveok') {
            if ($var2 != null) {
                $data['attribute'] = $this->m_billing->data_kunjungan_muat_data($var2);
                $data['list_data'] = $this->m_billing->load_data_tagihan($var2)->result();
            }
        }

        if($var1 != null){
            $data['pasien'] = $this->m_billing->get_data_pasien($var1);

            $tb = $this->m_billing->data_kunjungan_muat_data_total_barang($var1);
            $tj = $this->m_billing->data_kunjungan_muat_data_total_jasa($var1);
            $ti = $this->m_billing->data_rawat_inap_tagihan_run($var1);
            $total_pembayaran = $this->m_billing->total_pembayaran($var1);
            $total = ($tb->total_barang + $tj->total_jasa + $ti->total_inap_run);

            $data['sisa'] = $total - $total_pembayaran->total_pembayaran;
            $data['bayar'] = $total_pembayaran->total_pembayaran;

        }
        $data['title'] = 'Pembayaran Billing';
        $this->load->view('billing/pembayaran', $data);
    }
    
    function pembayaran_total_kunjungan() {
        $data['title'] = '';
        $this->load->view('billing/billing-kunjungan', $data);
    }

    function get_detail_data_pasien($no_daftar){
        $data = $this->m_billing->get_data_pasien($no_daftar);
        die(json_encode($data));
    }

    function get_data_pasien() {
        $q = get_safe('q');
        $data = $this->m_billing->data_pasien_muat_data($q);
        return die(json_encode($data));
    }

    function get_data_kunjungan() {
        $q = get_safe('q');
        $data = $this->m_billing->data_kunjungan($q);
        die(json_encode($data));
    }

    function asuransi_kepesertaan_get_data($id_pasien) {
        $data['list_data'] = $this->m_billing->asuransi_kepesertaan_get_data($id_pasien)->result();
        $this->load->view('billing/asuransi_list', $data);
    }

    function load_data($id_pasien) {
        $data['list_data'] = $this->m_billing->penjualan_barang_load_data($id_pasien, 'true')->result();
        $data['jasa_list_data'] = $this->m_billing->penjualan_jasa_detail_load_data($id_pasien)->result();
        $data['rawat_inap'] = $this->m_billing->rawat_inap_detail_load_data($id_pasien)->result();
        $this->load->view('billing/billing_list_data', $data);
    }

    function load_data_pembayaran($id_kunjungan) {
        $data['list_data'] = $this->m_billing->load_data_tagihan($id_kunjungan)->result();
        $data['total_data'] = $this->m_billing->load_data_tagihan($id_kunjungan)->num_rows();
        $data['rows'] = $this->m_billing->load_data_tagihan($id_kunjungan)->row();
        //$rows = $this->m_billing->data_kunjungan_muat_data_total($id_kunjungan);
        //$data['totallica'] = $rows->total_jasa + (($rows->total_barang == NULL)?0:$rows->total_barang);

        $this->load->view('billing/list_pembayaran', $data);
    }

    function hapus_pembayaran($id_nota){
        $this->db->where('id', $id_nota)->delete('kunjungan_billing_pembayaran');
    }

    function total_tagihan($id_kunjungan_billing) {
        $tb = $this->m_billing->data_kunjungan_muat_data_total_barang($id_kunjungan_billing);
        $tj = $this->m_billing->data_kunjungan_muat_data_total_jasa($id_kunjungan_billing);
        $ti = $this->m_billing->data_rawat_inap_tagihan_run($id_kunjungan_billing);
        //$tr = $this->m_billing->data_retur_penjualan($id_kunjungan_billing);

        $total_pembayaran = $this->m_billing->total_pembayaran($id_kunjungan_billing);

        //$total = ($tb->total_barang + $tj->total_jasa + $ti->total_inap_run)-$tr;
        $total = ($tj->total_jasa + $ti->total_inap_run + $tb->total_barang);
        die(json_encode(array(
            'fuck' => ($total - $total_pembayaran->total_pembayaran), 
            'you' => $total_pembayaran->total_pembayaran,
            'barang' => '',
            'jasa' => $tj->total_jasa,
            'inap' => $ti->total_inap_run,
            'retur' => '')));
    }

    function load_data_tagihan() {
        $data['list_data'] = $this->m_billing->load_data_tagihan($id_pasien)->result();
        $this->load->view('billing/tagihan', $data);
    }

    function pembayaran_save() {
        $data = $this->m_billing->pembayaran_save();
        die(json_encode($data));
    }

    function cetak($id_pembayaran, $angsuran_ke, $no_daftar) {
        $data['title'] = 'Pembayaran Ke-' . $angsuran_ke;
        $data['apt'] = $this->m_billing->office_muat_data()->row();
        $data['attribute'] = $this->m_billing->data_kunjungan_muat_data($no_daftar);
        $data['list_jasa'] = $this->m_billing->get_tagihan_jasa($no_daftar)->result();
        $data['list_barang'] = $this->m_billing->get_tagihan_barang($no_daftar)->row();
        $data['pembayaran'] = $this->m_billing->load_data_pembayaran($id_pembayaran, $no_daftar)->row();
        //$data['rawat_inap'] = $this->m_billing->load_data_rawat_inap_tagihan($no_daftar)->result();
        $data['bayar_ke'] = $angsuran_ke;
        
        $data['daftar_kunjungan'] = $this->m_billing->penjualan_jasa_detail_load_data($no_daftar,null,'Pendaftaran Kunjungan Pasien')->result();
        $data['selain'] = $this->m_billing->penjualan_jasa_detail_load_data($no_daftar, null, 'partial')->result();
        $data['akomodasi_kamar_inap'] = $this->m_billing->penjualan_jasa_detail_load_data($no_daftar,null,'Akomodasi Kamar Inap')->result();
        $data['barang_list_data'] = $this->m_billing->penjualan_barang_load_data2($no_daftar)->result();
        $this->load->view('billing/cetak_billing', $data);
    }
    
    function cetak_simple($id_pembayaran, $angsuran_ke, $no_daftar) {
        $data['title'] = 'Pembayaran Ke-' . $angsuran_ke;
        $data['apt'] = $this->m_billing->office_muat_data()->row();
        $data['attribute'] = $this->m_billing->data_kunjungan_muat_data($no_daftar);
        
        $data['bayar_ke'] = $angsuran_ke;
        $data['daftar_kunjungan'] = $this->m_billing->biaya_kunjungan($no_daftar, 'Pendaftaran Kunjungan Pasien')->row();
        $data['akomodasi_kamar_inap'] = $this->m_billing->biaya_kunjungan($no_daftar, 'Akomodasi Kamar Inap')->row();
        $data['selain'] = $this->m_billing->biaya_kunjungan($no_daftar)->result();
        $data['total_barang'] = $this->m_billing->get_tagihan_barang($no_daftar)->row();
        
        $data['pembayaran'] = $this->m_billing->load_data_pembayaran($id_pembayaran, $no_daftar)->row();
        $this->load->view('billing/cetak_billing_simple', $data);
    }

    function laporan() {
        $data['title'] = 'Rekap Pembayaran Billing';
        $this->load->view('billing/laporan', $data);
    }

    function laporan_load_data($page = 1) {
        $limit = 15;
        $start = ($page - 1) * $limit;

        $query = $this->m_billing->laporan_load_data($limit, $start ,get_safe('awal'), get_safe('akhir'), get_safe('pembayaran'));
        $data['list_data'] = $query['data'];
        $data['page'] = $page;
        $data['limit'] = $limit;
        $data['jumlah'] = $query['jumlah'];
        $data['paging'] = paging_ajax($query['jumlah'], $limit, $page, 1, 'null');
        $this->load->view('billing/laporan-table', $data);
    }
    
    function pp_uang() {
        $data['title'] = 'Penerimaan dan Pengeluaran Uang';
        $this->load->view('pp-uang', $data);
    }
    
    function pp_uang_save() {
        $data = $this->m_billing->pp_uang_save();
        die(json_encode($data));
    }
    
    function pp_uang_delete ($id) {
        $data = $this->m_billing->pp_uang_delete($id);
        die(json_encode($data));
    }
    
    function kunjungan_pasien() {
        $data['title'] = 'Rekap. Billing Kunjungan Pasien';
        $this->load->view('billing/kunjungan_pasien', $data);
        
    }
    
    function kunjungan_pasien_load_data($page) {
        $search['awal'] = post_safe('awal');
        $search['akhir'] = post_safe('akhir');
        $search['nomor'] = post_safe('nomor');
        $search['no_rm'] = post_safe('no_rm');
        $search['pasien']= post_safe('pasien');
        $search['alamat']= post_safe('alamat');
        $search['id_kelurahan'] = post_safe('id_kelurahan');

        $limit = 10;
        $start = ($page - 1) * $limit;
        $data['page'] = $page;
        $data['limit'] = $limit;
        
        $query = $this->m_billing->kunjungan_pasien_load_data( $limit, $start,$search);
        $data['list_data_kunjungan'] = $query['data'];
        $data['jumlah'] = $query['jumlah'];
        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, 1, '');
        $this->load->view('billing/data_kunjungan_pasien', $data);
        
    }
    
    function rincian_billing($no_daftar) {
        $data['list_data'] = $this->m_billing->penjualan_barang_load_data2($no_daftar)->result();
        $data['list_kunjungan'] = $this->m_billing->penjualan_jasa_detail_load_data($no_daftar,null,'Pendaftaran Kunjungan Pasien')->result();
        $data['list_lain'] = $this->m_billing->penjualan_jasa_detail_load_data($no_daftar, null, 'partial')->result();
        $data['list_inap'] = $this->m_billing->penjualan_jasa_detail_load_data($no_daftar,null,'Akomodasi Kamar Inap')->result();
        $data['bayar'] = $this->m_billing->total_pembayaran($no_daftar);
        $data['sisa'] = $this->m_billing->get_sisa_tagihan($no_daftar);
        $data['nama'] = get_safe('nama');
        $data['no_daftar'] = $no_daftar;
        $this->load->view('billing/rincian_pembayaran_kunjungan', $data);
    }

    function cek_pembayaran($no_daftar){
        $status = $this->m_billing->cek_pembayaran($no_daftar);
        die(json_encode(array('status'=>$status)));
    }
    
    function load_data_asuransi_by_nodaftar($no_daftar) {
        $data = $this->m_billing->load_data_asuransi_by_nodaftar($no_daftar)->result();
        die(json_encode($data));
    }
    
    function pembayaran_salin_resep() {
        $data['title'] = 'Pembayaran Salin Resep';
        $this->load->view('billing/billing-salin-resep', $data);
    }

}

?>