<?php

class Autocomplete extends CI_Controller {
    
    function __construct() {
        parent::__construct();
        $this->load->model(array('m_autocomplete'));
    }
    
    function generate_new_sp() {
        $data = $this->m_autocomplete->generate_new_sp();
        die(json_encode($data));
    }
    
    function pasien() {
        $q = $_GET['q'];
        $data = $this->m_autocomplete->pasien($q)->result();
        die(json_encode($data));
    }
    
    function kabupaten() {
        $q = $_GET['q'];
        $data = $this->m_autocomplete->kabupaten($q)->result();
        die(json_encode($data));
    }
    
    function kelurahan() {
        $q = $_GET['q'];
        $data = $this->m_autocomplete->kelurahan($q)->result();
        die(json_encode($data));
    }
    
    function get_no_resep() {
        $data = $this->m_autocomplete->get_no_resep();
        die(json_encode($data));
    }
    
    function resep() {
        $q = $_GET['q'];
        $data = $this->m_autocomplete->load_no_resep($q)->result();
        die(json_encode($data));
    }
    
    function get_layanan() {
        $data = $this->m_autocomplete->get_layanan()->result();
        die(json_encode($data));
    }
    
    function supplier() {
        $q = $_GET['q'];
        $data = $this->m_autocomplete->supplier($q)->result();
        die(json_encode($data));
    }
    
    function apoteker() {
        $q = $_GET['q'];
        $data = $this->m_autocomplete->get_data_apoteker($q)->result();
        die(json_encode($data));
    }
    
    function bpom() {
        $q = $_GET['q'];
        $data = $this->m_autocomplete->get_data_bpom($q)->result();
        die(json_encode($data));
    }
    
    function dokter() {
        $q = $_GET['q'];
        $data = $this->m_autocomplete->dokter($q)->result();
        die(json_encode($data));
    }
    
    function barang() {
        $q = $_GET['q'];
        $data = $this->m_autocomplete->barang($q)->result();
        die(json_encode($data));
    }
    
//    function get_data_kemasan($id_barang) {
//        $sql = "select k.id, k.default_kemasan, k.id_kemasan, s.nama, k.isi, k.isi_satuan, (k.isi*k.isi_satuan) as jml_isi
//            from kemasan k 
//            join satuan s on (k.id_kemasan = s.id) 
//            where k.id_barang = '$id_barang' order by k.id desc";
//        return $this->db->query($sql);
//    }
    
    function farmakoterapi() {
        $id = $_GET['id'];
        $data = $this->m_autocomplete->farmakoterapi($id)->result();
        die(json_encode($data));
    }
    
    function golongan_load_data() {
        $id = $_GET['id'];
        $data = $this->m_autocomplete->golongan_load_data($id)->result();
        die(json_encode($data));
    }
    
    function pabrik() {
        $q = $_GET['q'];
        $data = $this->m_autocomplete->pabrik($q)->result();
        die(json_encode($data));
    }
    
    function instansi() {
        $q = $_GET['q'];
        $data = $this->m_autocomplete->instansi($q)->result();
        die(json_encode($data));
    }
    
    /*PEMESANAN BARANG*/
    function get_nomor_sp() {
        $q = $_GET['q'];
        $data = $this->m_autocomplete->get_nomor_sp($q)->result();
        die(json_encode($data));
    }
    
    function get_data_pemesanan_penerimaan() {
        $id = $_GET['id'];
        $data = $this->m_autocomplete->get_data_pemesanan_penerimaan($id)->result();
        die(json_encode($data));
    }
    
    function get_detail_harga_barang_penerimaan() {
        $id_kemasan = $_GET['id_kemasan']; // kemasan
        $id_barang  = $_GET['id_barang']; // id barang
        $jml= $_GET['jumlah'];
        $data = $this->m_autocomplete->get_detail_harga_barang_penerimaan($id_kemasan, $id_barang, $jml)->result();
        die(json_encode($data));
    }
    
    function get_data_kemasan($id) {
        $data = $this->m_autocomplete->get_data_kemasan($id)->result();
        die(json_encode($data));
    }
    
    function get_data_penerimaan($id) {
        $data = $this->m_autocomplete->get_data_penerimaan($id)->result();
        die(json_encode($data));
    }
    
    function get_stok_sisa($id_barang) {
        $data = $this->m_autocomplete->get_stok_sisa($id_barang)->row();
        die(json_encode($data));
    }
    
    function get_detail_harga_barang_resep() {
        $id = $_GET['id'];
        $jml= $_GET['jumlah'];
        $data = $this->m_autocomplete->get_detail_harga_barang_resep($id, $jml)->result();
        die(json_encode($data));
    }
    
    function get_barang_barcode() {
        $barcode = $_GET['barcode'];
        $data = $this->m_autocomplete->get_barang_barcode($barcode)->row();
        die(json_encode($data));
    }
    
    function get_detail_harga_barang() {
        $id         = $_GET['id'];
        $kemasan = $_GET['kemasan'];
        $jml        = $_GET['jumlah'];
        $data = $this->m_autocomplete->get_detail_harga_barang($id, $kemasan, $jml);
        die(json_encode($data));
    }
    
    function get_expiry_barang() {
        $id = $_GET['id']; // ID barang
        $data = $this->m_autocomplete->get_expiry_barang($id)->result();
        die(json_encode($data));
    }
    
    function check_alergi_obat_pasien() {
        $id_barang = $_GET['id_barang'];
        $id_pasien = $_GET['id_pasien'];
        $data = $this->m_autocomplete->check_alergi_obat_pasien($id_barang, $id_pasien)->result();
        die(json_encode($data));
    }
    
    function get_nomor_antri() {
        $id_spesialis = $_GET['id_layanan'];
        $tanggal      = date2mysql($_GET['tanggal']);
        $data = $this->m_autocomplete->get_nomor_antri($id_spesialis, $tanggal)->row();
        die(json_encode($data));
    }
    
    /*PEMERIKSAAN*/
    function diagnosis() {
        $q = get_param('q');
        $data = $this->m_autocomplete->diagnosis($q)->result();
        die(json_encode($data));
    }
    
    function tindakan() {
        $q = get_param('q');
        $data = $this->m_autocomplete->tindakan($q)->result();
        die(json_encode($data));
    }
    
    function tindakan_komponen_tarif() {
        $q = get_param('q');
        $data = $this->m_autocomplete->tindakan_komponen_tarif($q)->result();
        die(json_encode($data));
    }
    
    function layanan() {
        $q = get_param('q');
        $data = $this->m_autocomplete->layanan($q)->result();
        die(json_encode($data));
    }
    
    function unit() {
        $q = get_param('q');
        $data = $this->m_autocomplete->unit($q)->result();
        die(json_encode($data));
    }
    
    function nomor_distribusi() {
        $q = get_param('q');
        $data = $this->m_autocomplete->nomor_distribusi($q)->result();
        die(json_encode($data));
    }
}
?>
