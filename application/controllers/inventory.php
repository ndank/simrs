<?php

class Inventory extends CI_Controller {
    
    function __construct() {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
        is_logged_in();
    }
    
    /*MANAGE DATA BARANG*/
    function barang() {
        $data['title'] = 'Data Barang';
        $data['golongan'] = $this->m_referensi->golongan_load_data()->result();
        $data['satuan_kekuatan'] = $this->m_referensi->satuans_load_data()->result();
        $data['kemasan'] = $this->m_referensi->satuans_load_data()->result();
        $data['sediaan'] = $this->m_inventory->sediaan_load_data()->result();
        $data['admr'] = $this->m_referensi->admr_load_data();
        $data['perundangan'] = $this->m_referensi->perundangan_load_data();
        $data['farmakoterapi'] = $this->m_referensi->farmakoterapi_load_data()->result();
        $data['fda'] = $this->m_referensi->fda_load_data();
        $data['status'] = array('Hibah','Program Pemerintah','Program Khusus');
        $data['range_terapi'] = array('Obat High Alert','Non-High Alert');
        $data['pengawasan'] = array('Dalam Pengawasan','Tidak Dalam Pengawasan');
        $data['fornas'] = array('Ya','Tidak');
        $this->load->view('inventory/barang', $data);
    }
    
    function get_list_data_barang($limit, $page, $search) {
        if ($page == 'undefined' or $page === '') {
            $page = 1;
        }
        //$str = 'null';
        $start = ($page - 1) * $limit;
        $data['page'] = $page;
        $data['limit'] = $limit;
        $data['auto'] = $start+1;
        $query = $this->m_inventory->get_data_barang($limit, $start, $search);
        $data['list_data'] = $query['data'];
        $data['jumlah'] = $query['jumlah'];
        
        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, 1, $search['search']);
        return $data;
    }
    
    function manage_barang($status, $page = null) {
        $limit = 15;
        switch ($status) {
            case 'list':
                $search['search'] = $_GET['search'];
                $search['id']  = $_GET['id'];
                $data = $this->get_list_data_barang($limit, $page, $search);
                $this->load->view('inventory/barang-list', $data);
                break;
            case 'list_pelengkap':
                $search['search'] = $_GET['search'];
                $search['id']  = $_GET['id'];
                $data = $this->get_list_data_barang($limit, $page, $search);
                $this->load->view('inventory/barang-pelengkap-list', $data);
                break;
            case 'save': 
                $this->m_inventory->save_barang();
                $data = $this->get_list_data_barang($limit, 1, null);
                $this->load->view('transaksi/barang-list', $data);
                break;
            case 'delete': 
                $this->m_inventory->delete_barang($_GET['id']);
                break;
            
        }
    }
    
    function get_kemasan_barang() {
        $id = $_GET['id'];
        $data = $this->m_inventory->kemasan_load_data($id)->result();
        die(json_encode($data));
    }
    
    function edit_kemasan($id) {
        $data['barang_packing'] = $this->m_inventory->kemasan_load_data($id)->result();
        $result = $this->m_referensi->satuan_load_data();
        $data['kemasan']= $result['data'];
        $this->load->view('inventory/edit-kemasan',$data);
    }
    
    /*MANAGE PEMESANAN BARANG*/
    function sp() {
        $data['title'] = 'Defecta dan Pemesanan';
        $this->load->view('inventory/sp', $data);
    }
    
    function pemesanan($id = null) {
        $data['id_pemesanan'] = NULL;
        //$data['kemasan'] = satuan_load_data();
        if ($id != NULL) {
            $data['id_pemesanan'] = $id;
            $data['list_data'] = $this->m_inventory->pemesanan_muat_data($id)->result();
        }
        $this->load->view('inventory/pemesanan', $data);
        //echo $this->waktu;
    }
    
    function get_list_data_pemesanan($limit, $page, $search) {
        if ($page == 'undefined') {
            $page = 1;
        }
        //$str = 'null';
        $start = ($page - 1) * $limit;
        $data['page'] = $page;
        $data['limit'] = $limit;
        $data['auto'] = $start+1;
        $query = $this->m_inventory->get_data_pemesanan($limit, $start, $search);
        $data['list_data'] = $query['data'];
        $data['jumlah'] = $query['jumlah'];
        
        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, 1, null);
        return $data;
    }
    
    function manage_pemesanan($status, $page = null) {
        $limit = 15;
        switch ($status) {
            case 'list':
                $search['key'] = $_GET['search'];
                $search['id']  = $_GET['id'];
                $data = $this->get_list_data_pemesanan($limit, $page, $search);
                $this->load->view('inventory/pemesanan-list', $data);
                break;
            case 'save': 
                $this->m_inventory->save_pemesanan();
                $data = $this->get_list_data_pemesanan($limit, 1, null);
                $this->load->view('transaksi/pemesanan-list', $data);
                break;
            case 'delete': 
                $this->m_inventory->delete_pemesanan($_GET['id']);
                break;
            
        }
    }
    
    function cetak_sp() {
        $id             = get_safe('id');
        $perundangan    = get_safe('perundangan');
        $data['head']   = $this->m_referensi->get_attribute_rs()->row();
        $data['apt']    = $this->m_referensi->get_apoteker()->row();
        $data['list_data'] = $this->m_inventory->cetak_sp($id)->result();
        if ($perundangan === 'Bebas') {
            $this->load->view('inventory/cetak-sp', $data);
        } else {
            $this->load->view('inventory/cetak-sp-unusual', $data);
        }
    }
    
    function defecta() {
        $data['kemasan'] = $this->m_referensi->satuans_load_data()->result();
        $this->load->view('inventory/defecta', $data);
    }
    
    function get_list_data_defecta($limit, $page, $search) {
        if ($page == 'undefined') {
            $page = 1;
        }
        //$str = 'null';
        $start = ($page - 1) * $limit;
        $data['page'] = $page;
        $data['limit'] = $limit;
        $data['auto'] = $start+1;
        $query = $this->m_inventory->get_data_defecta($limit, $start, $search);
        $data['list_data'] = $query['data'];
        $data['jumlah'] = $query['jumlah'];
        
        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, 1, null);
        return $data;
    }
    
    function manage_defecta($status, $page = null) {
        $limit = 15;
        switch ($status) {
            case 'list':
                $search['key'] = $_GET['search'];
                $search['id']  = $_GET['id'];
                $data = $this->get_list_data_defecta($limit, $page, $search);
                $this->load->view('inventory/defecta-list', $data);
                break;
            case 'save': 
                $this->m_inventory->save_defecta();
                $data = $this->get_list_data_defecta($limit, 1, null);
                $this->load->view('transaksi/defecta-list', $data);
                break;
            case 'add_rencana_pemesanan':
                $id = get_param('id');
                $data = $this->m_inventory->add_rencana_pemesanan($id);
                die(json_encode($data));
                break;
            case 'delete': 
                $this->m_inventory->delete_defecta($_GET['id']);
                break;
            
        }
    }
    
    function get_detail_harga_barang_pemesanan() {
        $data = $this->m_inventory->get_detail_harga_barang_pemesanan()->row();
        die(json_encode($data));
    }
    
    /*PENERIMAAN BARANG*/
    function penerimaan($id = null) {
        $data['id_penerimaan'] = NULL;
        $data['title'] = 'Penerimaan Barang';
        $data['golongan'] = $this->m_referensi->golongan_load_data()->result();
        $data['satuan_kekuatan'] = $this->m_referensi->satuans_load_data()->result();
        $data['kemasan'] = $this->m_referensi->satuans_load_data()->result();
        $data['sediaan'] = $this->m_inventory->sediaan_load_data()->result();
        $data['admr'] = $this->m_referensi->admr_load_data();
        $data['perundangan'] = $this->m_referensi->perundangan_load_data();
        $data['farmakoterapi'] = $this->m_referensi->farmakoterapi_load_data()->result();
        $data['fda'] = $this->m_referensi->fda_load_data();
        $data['status'] = array('Hibah','Program Pemerintah','Program Khusus');
        $data['range_terapi'] = array('Obat High Alert','Non-High Alert');
        $data['pengawasan'] = array('Dalam Pengawasan','Tidak Dalam Pengawasan');
        $data['fornas'] = array('Ya','Tidak');
        //$data['kemasan'] = satuan_load_data();
        if ($id != NULL) {
            $data['id_penerimaan'] = $id;
            $data['list_data'] = $this->m_inventory->penerimaan_muat_data($id)->result();
        }
        $this->load->view('inventory/penerimaan', $data);
        //echo $this->waktu;
    }
    
    function get_list_data_penerimaan($limit, $page, $search) {
        if ($page == 'undefined') {
            $page = 1;
        }
        //$str = 'null';
        $start = ($page - 1) * $limit;
        $data['page'] = $page;
        $data['limit'] = $limit;
        $data['auto'] = $start+1;
        $query = $this->m_inventory->get_data_penerimaan($limit, $start, $search);
        $data['list_data'] = $query['data'];
        $data['jumlah'] = $query['jumlah'];
        
        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, 1, null);
        return $data;
    }
    
    function manage_penerimaan($status, $page = null) {
        $limit = 15;
        switch ($status) {
            case 'list':
                $search['key'] = $_GET['search'];
                $search['id']  = $_GET['id'];
                $data = $this->get_list_data_penerimaan($limit, $page, $search);
                $this->load->view('inventory/penerimaan-list', $data);
                break;
            case 'save': 
                $this->m_inventory->save_penerimaan();
                $data = $this->get_list_data_penerimaan($limit, 1, null);
                $this->load->view('transaksi/penerimaan-list', $data);
                break;
            case 'delete': 
                $this->m_inventory->delete_penerimaan($_GET['id']);
                break;
        }
    }
    
    /*RETUR PENERIMAAN*/
    function retur_penerimaan() {
        $data['title'] = 'Retur Penerimaan';
        $this->load->view('inventory/retur-penerimaan', $data);
    }
    
    /*STOCK OPNAME*/
    function stok_opname() {
        $data['title'] = 'Stok Opname';
        $this->load->view('inventory/stok-opname', $data);
    }
    
    function get_list_data_stok_opname($limit, $page, $search) {
        if ($page == 'undefined') {
            $page = 1;
        }
        //$str = 'null';
        $start = ($page - 1) * $limit;
        $data['page'] = $page;
        $data['limit'] = $limit;
        $data['auto'] = $start+1;
        $query = $this->m_inventory->get_data_stok_opname($limit, $start, $search);
        $data['list_data'] = $query['data'];
        $data['jumlah'] = $query['jumlah'];
        
        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, 1, null);
        return $data;
    }
    
    function manage_stok_opname($status, $page = null) {
        $limit = 15;
        switch ($status) {
            case 'list':
                $search['key'] = $_GET['search'];
                $search['id']  = $_GET['id'];
                $data = $this->get_list_data_stok_opname($limit, $page, $search);
                $this->load->view('inventory/stok-opname-list', $data);
                break;
            case 'save': 
                $data = $this->m_inventory->save_stok_opname();
                die(json_encode($data));
                break;
            case 'delete': 
                $this->m_inventory->delete_stok_opname($_GET['id']);
                break;
        }
    }
    
    /*Pemusnahan*/
    function pemusnahan() {
        $data['title'] = 'Pemusnahan';
        $data['kemasan'] = $this->m_referensi->satuans_load_data()->result();
        $this->load->view('inventory/pemusnahan', $data);
    }
    
    function get_list_data_pemusnahan($limit, $page, $search) {
        if ($page == 'undefined') {
            $page = 1;
        }
        //$str = 'null';
        $start = ($page - 1) * $limit;
        $data['page'] = $page;
        $data['limit'] = $limit;
        $data['auto'] = $start+1;
        $query = $this->m_inventory->get_data_pemusnahan($limit, $start, $search);
        $data['list_data'] = $query['data'];
        $data['jumlah'] = $query['jumlah'];
        
        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, 1, null);
        return $data;
    }
    
    function manage_pemusnahan($status, $page = null) {
        $limit = 15;
        switch ($status) {
            case 'list':
                $search['key'] = $_GET['search'];
                $search['id']  = $_GET['id'];
                $data = $this->get_list_data_pemusnahan($limit, $page, $search);
                $this->load->view('inventory/pemusnahan-list', $data);
                break;
            case 'save': 
                $data = $this->m_inventory->save_pemusnahan();
                die(json_encode($data));
                break;
            case 'delete': 
                $this->m_inventory->delete_pemusnahan($_GET['id']);
                break;
        }
    }
    
    /*DISTRIBUSI*/
    function distribusi() {
        $data['title'] = 'Distribusi';
        $this->load->view('inventory/dist-utama', $data);
    }
    
    function distributed($id = null) {
        $data['id_distribusi'] = NULL;
        //$data['kemasan'] = satuan_load_data();
        if ($id != NULL) {
            $data['id_distribusi'] = $id;
            $data['list_data'] = $this->m_inventory->distribusi_muat_data($id)->result();
        }
        $this->load->view('inventory/distribusi', $data);
        //echo $this->waktu;
    }
    
    function get_list_data_distribusi($limit, $page, $search) {
        if ($page == 'undefined') {
            $page = 1;
        }
        //$str = 'null';
        $start = ($page - 1) * $limit;
        $data['page'] = $page;
        $data['limit'] = $limit;
        $data['auto'] = $start+1;
        $query = $this->m_inventory->get_data_distribusi($limit, $start, $search);
        $data['list_data'] = $query['data'];
        $data['jumlah'] = $query['jumlah'];
        
        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, 1, null);
        return $data;
    }
    
    function manage_distribusi($status, $page = null) {
        $limit = 15;
        switch ($status) {
            case 'list':
                $search['key'] = $_GET['search'];
                $search['id']  = $_GET['id'];
                $data = $this->get_list_data_distribusi($limit, $page, $search);
                $this->load->view('inventory/distribusi-list', $data);
                break;
            case 'save': 
                $this->m_inventory->save_distribusi();
                $data = $this->get_list_data_distribusi($limit, 1, null);
                $this->load->view('transaksi/distribusi-list', $data);
                break;
            case 'delete': 
                $this->m_inventory->delete_distribusi($_GET['id']);
                break;
            
        }
    }
    
    function load_data_distribusi($id) {
        $data = $this->m_inventory->load_data_distribusi($id)->result();
        die(json_encode($data));
    }
    
    function penerimaan_distribusi() {
        $this->load->view('inventory/penerimaan-distribusi');
    }
    
    function get_list_data_penerimaan_distribusi($limit, $page, $search) {
        if ($page == 'undefined') {
            $page = 1;
        }
        //$str = 'null';
        $start = ($page - 1) * $limit;
        $data['page'] = $page;
        $data['limit'] = $limit;
        $data['auto'] = $start+1;
        $query = $this->m_inventory->get_data_penerimaan_distribusi($limit, $start, $search);
        $data['list_data'] = $query['data'];
        $data['jumlah'] = $query['jumlah'];
        
        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, 1, null);
        return $data;
    }
    
    function manage_penerimaan_distribusi($status, $page = null) {
        $limit = 15;
        switch ($status) {
            case 'list':
                $search['key'] = $_GET['search'];
                $search['id']  = $_GET['id'];
                $data = $this->get_list_data_penerimaan_distribusi($limit, $page, $search);
                $this->load->view('inventory/penerimaan-distribusi-list', $data);
                break;
            case 'save': 
                $this->m_inventory->save_penerimaan_distribusi();
                $data = $this->get_list_data_penerimaan_distribusi($limit, 1, null);
                die(json_encode($data));
                break;
            case 'delete': 
                $this->m_inventory->delete_penerimaan_distribusi($_GET['id']);
                break;
            
        }
    }
}