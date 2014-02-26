<?php
class Akuntansi extends CI_Controller {
    
    function __construct() {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
        $this->load->model('m_akuntansi');
        $this->load->model(array('m_user'));
        $this->load->helper(array('functions'));
        
    }
    
    function rekening() {
        $data['title'] = 'Rekening';
        
        $rekening = $this->m_akuntansi->data_rekening_load_data();
        $ddmenu = array();
        $ddmenu[''] = 'Pilih Rekening ..';
        foreach ($rekening->result_array() as $rows) {
            $ddmenu[$rows['id']] = $rows['nama'];
        }
        $data['rekening'] = $ddmenu;
        
        $subrekening = $this->m_akuntansi->data_subrekening_load_data();
        $ddmenusub = array();
        $ddmenusub[''] = 'Pilih subRekening ..';
        foreach ($subrekening->result_array() as $rows) {
            $ddmenusub[$rows['id']] = $rows['nama'];
        }
        $data['subrekening'] = $ddmenusub;
        $this->load->view('akuntansi/rekening', $data);
    }
    
    function get_sub_rekening_dropdown($id_rekening = NULL) {
        $q = "where id_rekening IS NULL";
        if ($id_rekening != NULL) {
            $q = " where id_rekening = '$id_rekening'";
        }
        $sql = "select * from sub_rekening $q";
        $result = $this->db->query($sql)->result();
        foreach ($result as $rows) {
            echo "<option value='".$rows->id."'>".$rows->nama."</option>";
        }
    }
    
    function get_sub_sub_rekening_dropdown($id_sub_rekening = NULL) {
        $q = "where id_sub_rekening IS NULL";
        if ($id_sub_rekening != NULL) {
            $q = " where id_sub_rekening = '$id_sub_rekening'";
        }
        $sql = "select * from sub_sub_rekening $q";
        $result = $this->db->query($sql)->result();
        foreach ($result as $rows) {
            echo "<option value='".$rows->id."'>".$rows->nama."</option>";
        }
    }
    
    function get_sub_sub_sub_rekening_dropdown($id_sub_sub_rekening = NULL) {
        $q = "where id_sub_sub_rekening IS NULL";
        if ($id_sub_sub_rekening != NULL) {
            $q = " where id_sub_sub_rekening = '$id_sub_sub_rekening'";
        }
        $sql = "select * from sub_sub_sub_rekening $q";
        $result = $this->db->query($sql)->result();
        foreach ($result as $rows) {
            echo "<option value='".$rows->id."'>".$rows->nama."</option>";
        }
    }
    
    
    function save_sub_sub_rekening() {
        //$data = $this->m_akuntansi->save_sub_sub_rekening();
        $this->m_akuntansi->save_sub_sub_rekening();
        $data['id_sub'] = post_safe('id_subrekening');
        $data['nama_sub_sub'] = post_safe('sub_sub_rekening');
        $data['jenis'] = post_safe('jenis');
        $data['list_data'] = $this->m_akuntansi->data_rekening_load_data(post_safe('rekening'))->result();
        $data['list_rekening'] = $this->m_akuntansi->data_rekening_load_data()->result();
        $this->load->view('akuntansi/list_rekening',$data);
    }
    
    function save_sub_sub_sub_sub_rekening() {
        $data['list_rekening'] = $this->m_akuntansi->data_rekening_load_data()->result();
        $data['srekening'] = $this->m_akuntansi->data_subrekening_load_data()->result();
        $data['ssrekening'] = $this->m_akuntansi->data_subsubrekening_load_data()->result();
        $rows = $this->m_akuntansi->save_sub_sub_sub_sub_rekening();
        $data['id_sub'] = $rows->id_sub_rekening;
        $data['id_sub_sub'] = $rows->id_sub_sub_rekening;
        $data['id_sub_sub_sub'] = $rows->id_sub_sub_sub_rekening;
        $data['id_sub_sub_sub_sub'] = $rows->id_sub_sub_sub_sub_rekening;
        $data['list_data'] = $this->m_akuntansi->data_rekening_load_data($rows->id_rekening)->result();
        $this->load->view('akuntansi/list_rekening',$data);
    }
    
    function save_edit_sub_sub_sub_sub_rekening() {
        $data['list_rekening'] = $this->m_akuntansi->data_rekening_load_data()->result();
        $data['srekening'] = $this->m_akuntansi->data_subrekening_load_data()->result();
        $data['ssrekening'] = $this->m_akuntansi->data_subsubrekening_load_data()->result();
        $rows = $this->m_akuntansi->save_edit_sub_sub_sub_sub_rekening();
        $data['id_sub'] = $rows->id_sub_rekening;
        $data['id_sub_sub'] = $rows->id_sub_sub_rekening;
        $data['id_sub_sub_sub'] = $rows->id_sub_sub_sub_rekening;
        $data['id_sub_sub_sub_sub'] = $rows->id_sub_sub_sub_sub_rekening;
        $data['list_data'] = $this->m_akuntansi->data_rekening_load_data($rows->id_rekening)->result();
        $this->load->view('akuntansi/list_rekening',$data);
    }
    
    function save_edit_sub_sub_rekening() {
        //$data = $this->m_akuntansi->save_sub_sub_rekening();
        $this->m_akuntansi->save_edit_sub_sub_rekening();
        $data['id_sub'] = post_safe('id_subrekening');
        $data['nama_sub_sub'] = post_safe('sub_sub_rekening');
        $data['jenis'] = post_safe('jenis');
        $data['list_data'] = $this->m_akuntansi->data_rekening_load_data(post_safe('rekening'))->result();
        $data['list_rekening'] = $this->m_akuntansi->data_rekening_load_data()->result();
        $this->load->view('akuntansi/list_rekening',$data);
    }
    
    function delete_rekening($id) {
        $this->m_akuntansi->delete_rekening($id);
        $data['list_data'] = $this->m_akuntansi->data_rekening_load_data()->result();
        $this->load->view('akuntansi/list_rekening', $data);
    }
    
    function delete_subsubrekening($id) {
        $this->m_akuntansi->delete_subsubrekening($id);
        $data['list_data'] = $this->m_akuntansi->data_rekening_load_data()->result();
        $this->load->view('akuntansi/list_rekening', $data);
    }
    
    function delete_subrekening($id) {
        $this->m_akuntansi->delete_subrekening($id);
        $data['list_data'] = $this->m_akuntansi->data_rekening_load_data()->result();
        $this->load->view('akuntansi/list_rekening', $data);
    }
    
    function manage_rekening($act, $page = null) {
        
        $data['list_rekening'] = $this->m_akuntansi->data_rekening_load_data()->result();
        $data['srekening'] = $this->m_akuntansi->data_subrekening_load_data()->result();
        $data['ssrekening'] = $this->m_akuntansi->data_subsubrekening_load_data()->result();
        switch ($act) {
            case 'list':
                $data['list_data'] = $this->m_akuntansi->data_rekening_load_data()->result();
                $data['list_rekening'] = $this->m_akuntansi->data_rekening_load_data()->result();
                $this->load->view('akuntansi/list_rekening',$data);
            break;
            case 'add':
                $data['id'] = $this->m_akuntansi->rekening_save();
                $data['id_sub'] = post_safe('sub_rekening');
                $data['nama_sub_sub'] = post_safe('sub_sub_rekening');
                $data['jenis'] = post_safe('jenis');
                $data['list_data'] = $this->m_akuntansi->data_rekening_load_data($data['id'])->result();
                $data['list_rekening'] = $this->m_akuntansi->data_rekening_load_data()->result();
                $this->load->view('akuntansi/list_rekening',$data);
            break;
            case 'edit_rek':
                $this->m_akuntansi->rekening_update();
                $data['id_sub'] = post_safe('sub_rekening');
                $data['nama_sub_sub'] = post_safe('sub_sub_rekening');
                $data['jenis'] = post_safe('jenis');
                $data['list_data'] = $this->m_akuntansi->data_rekening_load_data(post_safe('kode_rek'))->result();
                $data['list_rekening'] = $this->m_akuntansi->data_rekening_load_data()->result();
                $this->load->view('akuntansi/list_rekening',$data);
            break;
            case 'add_sub':
                $data['id_sub'] = $this->m_akuntansi->subrekening_save();
                $data['list_data'] = $this->m_akuntansi->data_rekening_load_data(post_safe('rekening_id'))->result();
                $data['srekening'] = $this->m_akuntansi->data_subrekening_load_data()->result();
                $data['list_rekening'] = $this->m_akuntansi->data_rekening_load_data()->result();
                $this->load->view('akuntansi/list_rekening',$data);
            break;
            case 'edit_sub':
                $data['id_sub'] = $this->m_akuntansi->subrekening_edit();
                $data['list_rekening'] = $this->m_akuntansi->data_rekening_load_data()->result();
                $data['list_data'] = $this->m_akuntansi->data_rekening_load_data(post_safe('rekening_id'))->result();
                $this->load->view('akuntansi/list_rekening',$data);
            break;
            case 'search':
                $id_rek = post_safe('nama_ssss');
                $data['id_sub'] = post_safe('sub_rekening');
                $data['nama_sub_sub'] = post_safe('sub_sub_rekening');
                $data['jenis'] = post_safe('jenis');
                $data['list_data'] = $this->m_akuntansi->data_subsubsubsub_rekening_load_data(null, null, $id_rek)->result();
                $this->load->view('akuntansi/list_rekening',$data);
            break;
            case 'add_sub_sub_rek':
                $rows = $this->m_akuntansi->sub_sub_rek_save();
                $data['id_sub'] = $rows->id_subrekening;
                $data['id_sub_sub'] = $rows->id;
                $data['id_sub_sub_sub'] = '';
                $data['id_sub_sub_sub_sub'] = '';
                $data['srekening'] = $this->m_akuntansi->data_subrekening_load_data()->result();
                $data['ssrekening'] = $this->m_akuntansi->data_subsubrekening_load_data()->result();
                $data['list_data'] = $this->m_akuntansi->data_rekening_load_data($rows->id_rekening)->result();
                $this->load->view('akuntansi/list_rekening',$data);
            break;
            case 'edit_sub_sub_rek':
                $rows = $this->m_akuntansi->sub_sub_rek_save();
                $data['id_sub'] = $rows->id_subrekening;
                $data['id_sub_sub'] = $rows->id;
                $data['id_sub_sub_sub'] = '';
                $data['id_sub_sub_sub_sub'] = '';
                $data['ssrekening'] = $this->m_akuntansi->data_subsubrekening_load_data()->result();
                $data['list_data'] = $this->m_akuntansi->data_rekening_load_data($rows->id_rekening)->result();
                $this->load->view('akuntansi/list_rekening',$data);
            break;
            case 'add_sub_sub_sub_rek':
                $rows = $this->m_akuntansi->sub_sub_sub_rek_save();
                $data['id_sub'] = $rows->id_sub_rekening;
                $data['id_sub_sub'] = $rows->id_sub_sub_rekening;
                $data['id_sub_sub_sub'] = $rows->id;
                $data['id_sub_sub_sub_sub'] = '';
                $data['list_data'] = $this->m_akuntansi->data_rekening_load_data($rows->id_rekening)->result();
                $this->load->view('akuntansi/list_rekening',$data);
            break;
        }
    }
    
    function jurnal() {
        $data['title'] = 'Jurnal';
        $data['reference'] = $this->m_akuntansi->referensi_load_data()->result();
        $this->load->view('akuntansi/jurnal', $data);
    }
    
    function jurnal_save() {
        $array = array(
            'waktu' => date("Y-m-d H:i:s"),
            'id_transaksi' => post_safe('id_transaksi'),
            'jenis_transaksi' => post_safe('jenis_transaksi'),
            'id_sub_sub_sub_sub_rekening' => post_safe('reference'),
            'debet' => currencyToNumber(post_safe('nilai')),
            'kredit' => '0',
            'ket_transaksi' => post_safe('keterangan')
        );
        $this->db->insert('jurnal', $array);
        $arrays = array(
            'waktu' => date("Y-m-d H:i:s"),
            'id_transaksi' => post_safe('id_transaksi'),
            'jenis_transaksi' => post_safe('jenis_transaksi'),
            'id_sub_sub_sub_sub_rekening' => post_safe('reference2'),
            'debet' => '0',
            'kredit' => currencyToNumber(post_safe('nilai')),
            'ket_transaksi' => post_safe('keterangan')
        );
        $this->db->insert('jurnal', $arrays);
    }
    
    function bukubesar() {
        $data['title'] = 'Transaksi Buku Besar';
        $data['jenis_transaksi'] = array('Pilih ...', 'Stok Opname','Pembelian','Retur Pembelian','Penerimaan Retur Pembelian','Pemusnahan','Penjualan','Retur Penjualan','Pengeluaran Retur Penjualan','Inkaso','Pembayaran Billing Pasien','Pengeluaran Retur Penjualan','Penerimaan Retur Pembelian','Penerimaan dan Pengeluaran');
        $data['jenis_trx'] = array(
                '' => 'Pilih ...',
                'Stok Opname' => 'Stok Opname',
                'Pembelian' => 'Pembelian',
                'Retur Pembelian' => 'Retur Pembelian',
                'Penerimaan Retur Pembelian' => 'Penerimaan Retur Pembelian',
                'Pemusnahan' => 'Pemusnahan',
                'Pemakaian' => 'Pemakaian',
                'Penjualan' => 'Penjualan',
                'Retur Penjualan' => 'Retur Penjualan',
                'Pengeluaran Retur Penjualan' => 'Pengeluaran Retur Penjualan',
                'Pembayaran Billing Pasien' => 'Pembayaran Billing Pasien',
                'Penerimaan dan Pengeluaran' => 'Penerimaan dan Pengeluaran',
                'Inkaso' => 'Inkaso'
                
            );
        $this->load->view('akuntansi/bukubesar',$data);
    }
    
    function list_bukubesar($id = null) {
        $search['id'] = isset($id)?$id:NULL;
        $search['awal'] = date2mysql(get_safe('awal'));
        $search['akhir']= date2mysql(get_safe('akhir'));
        $search['rekening'] = get_safe('rekening');
        $search['kode'] = get_safe('kode');
        $search['last'] = isset($_GET['last'])?get_safe('last'):NULL;
        $search['batas']= isset($_GET['batas'])?get_safe('batas'):NULL;

        $search['id_rek'] = get_safe('id_reke');
        $search['id_sub'] = get_safe('id_sub');
        $search['id_subsub'] = get_safe('id_subsub');
        $search['id_subsubsub'] = get_safe('id_subsubsub');
        $search['jenis_transaksi'] = get_safe('jenis_transaksi');

        $page = get_safe('page');
        $limit = 10;
        if ($page == 'undefined') {
            $page = 1;
        }
        
        $start = ($page - 1) * $limit;
        
        $jumData = $this->m_akuntansi->list_bukubesar($search)->num_rows();
        $search['start'] = $start;
        $search['limit'] = $limit;
        
        $data['paging'] = paging_ajax($jumData, $limit, $page, 1, '');
        $data['list_data'] = $this->m_akuntansi->list_bukubesar($search)->result();
        $data['page'] = $page;
        $data['limit'] = $limit;
        $data['jumlah'] = $jumData;
        $this->load->view('akuntansi/list_bukubesar', $data);
    }
    
    function delete_bukubesar($id) {
        $this->db->delete('jurnal', array('id' => $id));
        die(json_encode(TRUE));
    }
    
    function lap_shu() {
        $data['title'] = 'SHU';
        $data['pendapatan_operasional'] = $this->m_akuntansi->data_rekening_load_data(4)->result();
        $data['beban_operasional'] = $this->m_akuntansi->data_rekening_load_data(5)->result();
        $data['pendapatan_non_operasional'] = $this->m_akuntansi->data_rekening_load_data(3)->result();
        $data['beban_non_operasional'] = $this->m_akuntansi->data_rekening_load_data(4)->result();
        $data['taksir_pajak'] = $this->m_akuntansi->data_rekening_load_data(5)->result();
        //$data['dakwah'] = $this->m_akuntansi->data_rekening_dakwah('Dana Pengembangan Dakwah')->row();
        //$data['premi'] = $this->m_akuntansi->data_rekening_dakwah('Premi')->row();
        $this->load->view('akuntansi/shu', $data);
    }
    
    function get_sub_sub_sub_sub_rekening() {
        $q = get_safe('q');
        $data = $this->m_akuntansi->get_sub_sub_sub_sub_rekening($q)->result();
        die(json_encode($data));
    }
    
    function get_sub_sub_sub_sub_rekening_dropdown($id) {
        $data = $this->m_akuntansi->data_subsubsubsub_rekening_load_data($id)->row();
        die(json_encode($data));
    }
    
    function delete_subsubsubsubrekening($id) {
        $this->db->delete('sub_sub_sub_sub_rekening', array('id' => $id));
        die(json_encode(array('status' => TRUE)));
    }
    
    function delete_subsubsubrekening($id) {
        $this->db->delete('sub_sub_sub_rekening', array('id' => $id));
        die(json_encode(array('status' => TRUE)));
    }
    
    function save_edit_sub_sub_sub_rek() {
        $data['list_rekening'] = $this->m_akuntansi->data_rekening_load_data()->result();
        $data['srekening'] = $this->m_akuntansi->data_subrekening_load_data()->result();
        $data['ssrekening'] = $this->m_akuntansi->data_subsubrekening_load_data()->result();
        $rows = $this->m_akuntansi->sub_sub_sub_rek_save();
        $data['id_sub'] = $rows->id_sub_rekening;
        $data['id_sub_sub'] = $rows->id_sub_sub_rekening;
        $data['id_sub_sub_sub'] = $rows->id;
        $data['id_sub_sub_sub_sub'] = '';
        $data['list_data'] = $this->m_akuntansi->data_rekening_load_data($rows->id_rekening)->result();
        $this->load->view('akuntansi/list_rekening',$data);
    }
    
    function get_sub_sub_rek_auto() {
        $q = get_safe('q');
        $data = $this->m_akuntansi->get_sub_sub_rek_auto($q)->result();
        die(json_encode($data));
    }
    
    function get_sub_rekening_auto() {
        $q = get_safe('q');
        $data = $this->m_akuntansi->get_sub_rek_auto($q)->result();
        die(json_encode($data));
    }
    
    function set_awal_neraca() {
        $data = $this->m_akuntansi->set_awal_neraca_save();
        die(json_encode($data));
    }
    
    function cetak_shu($awal, $akhir) {
        $data['title'] = 'SHU';
        $data['pendapatan_operasional'] = $this->m_akuntansi->data_rekening_load_data(4)->result();
        $data['beban_operasional'] = $this->m_akuntansi->data_rekening_load_data(5)->result();
        $data['pendapatan_non_operasional'] = $this->m_akuntansi->data_rekening_load_data(3)->result();
        $data['beban_non_operasional'] = $this->m_akuntansi->data_rekening_load_data(4)->result();
        $data['taksir_pajak'] = $this->m_akuntansi->data_rekening_load_data(5)->result();
        
        $this->load->view('akuntansi/shu-print', $data);
    }

    function kode_rekening(){
        $this->load->model('m_referensi');
        $data['title'] = 'Kode Rekening Tarif';
        $data['jenis_layan'] = $this->m_referensi->jenis_pelayanan_get_data();
        $this->load->view('akuntansi/kode_rekening', $data);
    }
    
    function kode_rekening_list($page){
        $limit = 10;
        $start = ($page - 1) * $limit;
        $search = 'null';
        $data['page'] = $page;
        $data['limit'] = $limit;
        $param = array(
            'id' => get_safe('id_kode_rekening'),
            'tarif' => get_safe('tarif'),
            'debet' => get_safe('id_debet'),
            'kredit' => get_safe('id_kredit'),
            'jenis_layan' =>get_safe('jenis_layan')
        );
        $query = $this->m_akuntansi->kode_rekening_load_data($limit, $start, $param);
        $data['rekening'] = $query['data'];
        $data['jumlah'] = $query['jumlah'];
        $data['paging'] = paging_ajax($data['jumlah'], $limit, $page, 1, $search);
        $this->load->view('akuntansi/kode_rekening_list', $data);
    }

    function kode_rekening_update(){
         $id = $this->m_akuntansi->kode_rekening_update();
        die(json_encode(array('id'=> $id)));
    }

    function get_kode_rekening($id){
        $data = $this->m_akuntansi->get_kode_rekening($id);
        echo json_encode($data);
    }

    function kode_rekening_delete($id){
        $this->m_akuntansi->kode_rekening_delete($id);
        
    }
}
?>