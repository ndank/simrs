<?php

class Inv_autocomplete extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model(array('m_inv_autocomplete','m_billing','configuration','m_akuntansi','m_demografi'));
    }

    public function load_data_instansi_relasi($jenis=null) {
        $q = get_safe('q');
        $data = $this->m_inv_autocomplete->load_data_instansi_relasi($jenis, $q)->result();
        die(json_encode($data));
    }

    function load_data_user_system() {
        $q = get_safe('q');
        $data = $this->m_inv_autocomplete->load_data_user_system($q)->result();
        die(json_encode($data));
    }

    function load_data_produk_asuransi() {
        $q = get_safe('q');
        $data = $this->m_inv_autocomplete->load_data_produk_asuransi($q)->result();
        die(json_encode($data));
    }

    function load_data_pegawai() {
        $q = get_safe('q');
        $data = $this->m_inv_autocomplete->load_data_pegawai($q)->result();
        die(json_encode($data));
    }

    function load_data_pegawai_nakes() {
        $q = get_safe('q');
        $data = $this->m_inv_autocomplete->load_data_pegawai_nakes($q)->result();
        die(json_encode($data));
    }

    function load_data_pegawai_profesi() {
        $q = get_safe('q');
        $profesi = '';
        if(isset($_GET['profesi'])){
            $profesi = get_safe('profesi');
        }
        $data = $this->m_inv_autocomplete->load_data_pegawai_profesi($q, $profesi)->result();
        die(json_encode($data));
    }

    function load_data_penduduk($jenis = null) {
        $q = get_safe('q');
        $data = $this->m_inv_autocomplete->load_data_penduduk($jenis, $q)->result();
        die(json_encode($data));
    }
    
    function load_data_penduduk_profesi() {
        $q = get_safe('q');
        $data = $this->m_inv_autocomplete->load_data_penduduk_profesi('Nakes', $q)->result();
        die(json_encode($data));
    }
    
    function load_data_profesi_by_nakes() {
        $q = get_safe('q');
        $data = $this->m_inv_autocomplete->load_data_nakes($q)->result();
        die(json_encode($data));
    }

    function load_penduduk() {
        $q = get_safe('q');
        $data = $this->m_inv_autocomplete->load_penduduk($q)->result();
        die(json_encode($data));
    }
    
    function load_data_penjualan_jasa($no_daftar, $id_pk = null) {
        $data['list_data'] = $this->m_billing->penjualan_jasa_detail_load_data($no_daftar, $id_pk)->result();
        $this->load->view('penjualan-jasa_table', $data);
    }
    
    
    function load_data_packing_barang_per_ed() {
        $q = get_safe('q');
        $extra_param = isset($_GET['id_barang']) ? get_safe('id_barang') : NULL;
        $data = $this->m_inv_autocomplete->load_data_packing_barang_per_ed($q, $extra_param)->result();
        die(json_encode($data));
    }
    
    function load_data_packing_barang_pemusnahan_per_ed() {
        $q = get_safe('q');
        $extra_param = isset($_GET['id_barang']) ? get_safe('id_barang') : NULL;
        $data = $this->m_inv_autocomplete->load_data_packing_barang_pemusnahan_per_ed($q, $extra_param)->result();
        die(json_encode($data));
    }

    function load_data_penduduk_pasien($id_penduduk = null) {
        $id = null;
        $q = null;
        if ($id_penduduk != null) {
            $id = $id_penduduk;
            $data = $this->m_inv_autocomplete->load_data_penduduk_pasien(null, $id)->row();
        }
        if ($id_penduduk == null) {
            $q = get_safe('q');
            $data = $this->m_inv_autocomplete->load_data_penduduk_pasien($q)->result();
        }
        die(json_encode($data));
    }
    
    function load_data_penduduk_pasien_form_resep() {
        $q = get_safe('q');
        $data = $this->m_inv_autocomplete->load_data_penduduk_pasien_form_resep($q)->result();
        die(json_encode($data));
    }

    function load_data_penduduk_dokter() {
        $q = get_safe('q');
        $data = $this->m_inv_autocomplete->load_data_penduduk_dokter($q)->result();
        die(json_encode($data));
    }

    function load_data_penduduk_asuransi($id_penduduk = null) {
        $id = null;
        if ($id_penduduk != null) {
            $id = $id_penduduk;
        }
        $data = $this->m_inv_autocomplete->load_data_penduduk_asuransi($id);
        die(json_encode($data));
    }

    function get_layanan_jasa($param = null){
        $q = get_safe('q');
        $extraParam = null;
        if ($param != null) {
            $extraParam['layanan'] = 'Sewa Kamar';
        }
        if (isset($_GET['id_unit'])) {
           $extraParam['id_unit'] = get_safe('id_unit');
        }
        $data = $this->m_inv_autocomplete->get_layanan_jasa($q, $extraParam)->result();
        die(json_encode($data));
    }

    function get_layanan_laboratorium(){
        $q = get_safe('q');
        $data = $this->m_inv_autocomplete->get_layanan_laboratorium($q)->result();
        die(json_encode($data));
    }

    function get_satuan(){
        $q = get_safe('q');
        $data = $this->m_inv_autocomplete->get_satuan($q)->result();
        die(json_encode($data));
    }
    
    
    function get_last_id_tarif() {
        $data['id_layanan'] = post_safe('id_layanan');
        $data['id_profesi'] = post_safe('id_profesi');
        $data['id_jurusan'] = post_safe('id_jurusan');
        $data['jpk'] = post_safe('jpk');
        $data['id_unit'] = post_safe('id_unit');
        $data['bobot'] = post_safe('bobot');
        $data['id_barang_sewa'] = post_safe('id_barang_sewa');
        $data['kelas'] = post_safe('kelas');
        $result = $this->m_inv_autocomplete->get_last_id_tarif($data)->row();
        die(json_encode($result));
    }
    
    function layanan_jasa_load_data() {
        $q = get_safe('q');
        $data = $this->m_inv_autocomplete->layanan_jasa_load_data($q)->result();
        die(json_encode($data));
    }

    function layanan_jasa_load_data_radiologi() {
        $q = get_safe('q');
        $data = $this->m_inv_autocomplete->layanan_jasa_load_data_radiologi($q)->result();
        die(json_encode($data));
    }
    
    function tindakan_tarif_load_data() {
        $q = get_safe('q');
        $data = $this->m_inv_autocomplete->tindakan_tarif_load_data($q)->result();
        die(json_encode($data));
    }

    function load_data_packing_barang() {
        $q = get_safe('q');
        $extra_param = isset($_GET['id_barang']) ? get_safe('id_barang') : NULL;
        $data = $this->m_inv_autocomplete->load_data_packing_barang($q, $extra_param)->result();
        die(json_encode($data));
    }
    
    function get_hna_packing($id_packing) {
        $data = $this->m_inv_autocomplete->get_hna_packing($id_packing)->row();
        die(json_encode($data));
    }
    
    function load_data_packing_barang_resep() {
        $q = get_safe('q');
        $extra_param = isset($_GET['id_barang']) ? get_safe('id_barang') : NULL;
        $data = $this->m_inv_autocomplete->load_data_packing_barang_resep($q, $extra_param)->result();
        die(json_encode($data));
    }

    function load_data_kategori_barang() {
        $data = $this->m_inv_autocomplete->load_data_kategori_barang(get_safe('q'))->result();
        die(json_encode($data));
    }
    
    function load_data_rop() {
        $id = get_safe('id');
        $data = $this->m_inv_autocomplete->load_data_rop($id);
        die(json_encode($data));
    }
    
    function get_data_sisa() {
        $id = get_safe('id');
        $data = $this->m_inv_autocomplete->get_data_sisa($id);
        die(json_encode($data));
    }

    function get_nomor_pemesanan() {
        $q = get_safe('q');
        $data = $this->m_inv_autocomplete->get_nomor_pemesanan($q)->result();
        die(json_encode($data));
    }
    
    function hitung_detail_pemesanan($id_pb, $biaya) {
        $data = $this->m_inv_autocomplete->hitung_detail_pemesanan($id_pb, $biaya);
        die(json_encode($data));
    }
    
    function get_harga_jual() {
        $data = $this->m_inv_autocomplete->get_harga_jual(get_safe('id'))->row();
        die(json_encode($data));
    }
    
    function load_data_penduduk_apoteker() {
        $data = $this->configuration->get_apoteker(get_safe('q'))->result();
        die(json_encode($data));
    }

    function get_nomor_pembelian() {
        $q = get_safe('q');
        $row = $this->m_inv_autocomplete->get_nomor_pembelian($q)->row();
        $data = null;
        if (isset($row->id)) {
            $data = $this->m_inv_autocomplete->get_nomor_pembelian($q)->result();
        }
        die(json_encode($data));
    }

    function get_last_transaction() {
        $id_pb = get_safe('id_pb');
        $ed = datetopg(get_safe('ed'));
        $data = $this->m_inv_autocomplete->get_last_transaction($id_pb, $ed)->row();
        die(json_encode($data));
    }

    function get_nomor_distribusi() {
        $q = get_safe('q');
        $data = $this->m_inv_autocomplete->get_nomor_distribusi($q)->result();
        die(json_encode($data));
    }

    function get_diskon_instansi_relasi($id_instansi_relasi = null) {
        $id = null;
        if ($id_instansi_relasi != null) {
            $id = $id_instansi_relasi;
        }
        $data = $this->m_inv_autocomplete->get_diskon_instansi_relasi($id)->row();
        die(json_encode($data));
    }

    function get_harga_barang_penjualan($id_packing) {
        $data = $this->m_inv_autocomplete->get_harga_barang_penjualan($id_packing)->row();
        die(json_encode($data));
    }

    function get_penjualan_field($barcode) {
        $data = $this->m_inv_autocomplete->get_penjualan_field($barcode)->row();
        die(json_encode($data));
    }

    function load_data_pabrik() {
        $q = get_safe('q');
        $data = $this->m_inv_autocomplete->load_data_pabrik($q)->result();
        die(json_encode($data));
    }

    function load_data_no_resep() {
        $q = get_safe('q');
        $data = $this->m_inv_autocomplete->load_data_no_resep($q)->result();
        die(json_encode($data));
    
    }
    
    function load_jasa_apoteker($id_resep) {
        $data = $this->m_inv_autocomplete->load_jasa_apoteker($id_resep)->row();
        die(json_encode($data));
    }
    
    function load_unit_kelas_by_no_rm($no_rm) {
        $data = $this->m_inv_autocomplete->load_unit_kelas_by_no_rm($no_rm)->row();
        die(json_encode($data));
    }

    function load_penjualan_by_no_resep($no_resep) {
        $data['list_data'] = $this->m_inv_autocomplete->load_penjualan_by_no_resep($no_resep)->result();
        $this->load->view('inventory/penjualan-table', $data);
    }

    function reretur_pembelian_load_id() {
        $q = get_safe('q');
        $data = $this->m_inv_autocomplete->reretur_pembelian_load_id($q)->result();
        die(json_encode($data));
    }

    function reretur_pembelian_load_data() {
        $no_retur_pembelian = get_safe('id');
        $data['list_data'] = $this->m_inv_autocomplete->reretur_pembelian_load_data($no_retur_pembelian)->result();
        $this->load->view('inventory/reretur-pembelian-table', $data);
    }

    function reretur_penjualan_get_nomor() {
        $id = get_safe('q');
        $data = $this->m_inv_autocomplete->reretur_penjualan_get_nomor($id)->result();
        die(json_encode($data));
    }

    function reretur_penjualan_table() {
        $id = get_safe('id');
        $data['list_data'] = $this->m_inv_autocomplete->reretur_penjualan_table($id)->result();
        $this->load->view('inventory/reretur-penjualan-table', $data);
    }

    function get_layanan($tindakan = null) {
        $q = get_safe('q');
        $data = $this->m_inv_autocomplete->get_layanan($q, $tindakan)->result();
        die(json_encode($data));
    }

    function get_tarif_kategori() {
        $q = get_safe('q');
        $data = $this->m_inv_autocomplete->get_tarif_kategori($q)->result();
        die(json_encode($data));
    }

    function load_data_barang() {
        $q = get_safe('q');
        $data = $this->m_inv_autocomplete->get_barang($q)->result();
        die(json_encode($data));
    }
    
    function load_data_layanan_profesi() {
        $q = get_safe('q');
        $data = $this->m_inv_autocomplete->load_data_layanan_profesi($q)->result();
        die(json_encode($data));
    }
    
    function get_no_retur_distribusi() {
        $q =  get_safe('q');
        $data = $this->m_inv_autocomplete->get_no_retur_distribusi($q)->result();
        die(json_encode($data));
    }
    
    function load_data_retur_unit($id) {
        $data['list_data'] = $this->m_inv_autocomplete->load_data_retur_unit($id)->result();
        $this->load->view('inventory/penerimaan-retur-distribusi-table', $data);
    }

    function pasien_load_data() {
        $q = get_safe('q');
        $data = $this->m_inv_autocomplete->data_pasien_muat_data($q);
        return die(json_encode($data));
    }

    function load_data_pelayanan_kunjungan_by_id_penduduk($id) {
        $data = $this->m_inv_autocomplete->load_data_pelayanan_kunjungan_by_id_penduduk($id)->row();
        die(json_encode($data));
    }
    
    function delete_penjualan_jasa($id) {
        $data = $this->m_inv_autocomplete->delete_penjualan_jasa($id);
        die(json_encode($data));
    }

    function get_tindakan_jasa() {
        $q = get_safe('q');
        $data = $this->m_inv_autocomplete->tindakan_load_data($q);
        return die(json_encode($data));
    }

    function get_rekening(){
        $q = get_safe('q');
        $data = $this->m_akuntansi->auto_rekening_load_data($q)->result();
        return die(json_encode($data));
    }
    
    function get_subrekening() {
        $q = get_safe('q');
        $id_rekening = get_safe('id_rekening');
        $data = $this->m_akuntansi->auto_subrekening_load_data($q, $id_rekening)->result();
        return die(json_encode($data));
    }

    function get_subsubrekening() {
        $q = get_safe('q');
        $id_sub = get_safe('id_sub');
        $data = $this->m_akuntansi->auto_subsubrekening_load_data($q, $id_sub)->result();
        return die(json_encode($data));
    }

    function get_subsubsubrekening() {
        $q = get_safe('q');
        $id_sub_sub = get_safe('id_sub_sub');
        $data = $this->m_akuntansi->auto_subsubsubrekening_load_data($q, $id_sub_sub)->result();
        return die(json_encode($data));
    }

    function get_sub_sub_sub_subrekening() {
        $q = get_safe('q');
        $id_sub_sub_sub = isset($_GET['id_sub_sub_sub'])?$_GET['id_sub_sub_sub']:'';
        $data = $this->m_akuntansi->data_subsubsubsubrekening_load_data($q, $id_sub_sub_sub)->result();
        return die(json_encode($data));
    }
    
    function load_data_pemesanan() {
        $data['list_data'] = $this->m_inv_autocomplete->load_data_pemesanan()->result();
        $this->load->view('inventory/pemesanan-table', $data);
    }
    
    function get_data_pemesanan($id) {
        $data = $this->m_inv_autocomplete->load_data_pemesanan($id)->row();
        die(json_encode($data));
    }
    
    function get_sisa_stok($id_packing, $ed) {
        $data = $this->m_inv_autocomplete->get_sisa_stok($id_packing, $ed)->row();
        die(json_encode($data));
    }
    
    function get_sisa_stok_kemasan($id_packing) {
        $data = $this->m_inv_autocomplete->get_sisa_stok_kemasan($id_packing)->row();
        die(json_encode($data));
    }
    
    function get_detail_kemasan($id_packing) {
        $data = $this->m_inv_autocomplete->get_detail_kemasan($id_packing)->row();
        die(json_encode($data));
    }
    
    function get_data_dinamis_penduduk($id_pk) {
        $data = $this->m_demografi->get_data_dinamis_penduduk($id_pk)->row();
        die(json_encode($data));
    }

}

?>