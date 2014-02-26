<?php $this->load->view('message') ?>
<title><?= $title ?></title>
<div class="kegiatan">
    <script type="text/javascript">
        var dWidth = $(window).width();
        var dHeight= $(window).height();
        var x = screen.width/2 - dWidth/2;
        var y = screen.height/2 - dHeight/2;
        $(function() {        
            $('#simpan').button({icons: {secondary: 'ui-icon-circle-check'}}).click(function(){
                $('#formregister').submit();
            });
            $('#reset,.resetan').button({icons: {secondary: 'ui-icon-refresh'}});
            $('#cari').button({icons:{secondary:'ui-icon-search'}});
            get_register_list(1,'null');
            get_last_id();
            $('.tanggal').datepicker({
                changeYear : true,
                changeMonth : true
            });
            
            $('.tanggaltime').datetimepicker({
                changeYear : true,
                changeMonth : true
            });
     
            $('#reset').click(function(){
                $('#loaddata').empty();
                $('#loaddata').load($.cookie('url'));
            });
            
            
            $('#direktur').autocomplete("<?= base_url('registrasi_rs/load_data_pegawai') ?>",
            {
                parse: function(data)
                {
                    var parsed = [];
                    for (var i=0; i < data.length; i++)
                    {
                        parsed[i] =
                            {
                            data: data[i],
                            value: data[i].nama // nama field yang dicari
                        };
                    }
                    $('input[name=id_direktur]').val('');
                    return parsed;
                },
                formatItem: function(data,i,max)
                {
                    var str = '<div class=result>'+data.nama+'<br/>'+data.nip +'</div>';
                    return str;
                },
                width: 270, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
            }).result(
            function(event,data,formated){
                $(this).attr('value',data.nama);
                $('input[name=id_direktur]').val(data.id);
            });
            
            $('#penyelenggara').autocomplete("<?= base_url('inv_autocomplete/load_data_instansi_relasi') ?>/",
            {
                parse: function(data)
                {
                    var parsed = [];
                    for (var i=0; i < data.length; i++)
                    {
                        parsed[i] =
                            {
                            data: data[i],
                            value: data[i].nama // nama field yang dicari
                        };
                    }
                    $('input[name=id_penyelenggara]').val('');
                    return parsed;
                },
                formatItem: function(data,i,max)
                {
                    var str = '<div class=result>'+data.nama+'<br/>'+data.alamat +'</div>';
                    return str;
                },
                width: 270, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
            }).result(
            function(event,data,formated){
                $(this).attr('value',data.nama);
                $('input[name=id_penyelenggara]').val(data.id);              
            });
            
            $('#penetap').autocomplete("<?= base_url('inv_autocomplete/load_data_instansi_relasi') ?>/",
            {
                parse: function(data)
                {
                    var parsed = [];
                    for (var i=0; i < data.length; i++)
                    {
                        parsed[i] =
                            {
                            data: data[i],
                            value: data[i].nama // nama field yang dicari
                        };
                    }
                    $('input[name=id_penetap]').val('');
                    return parsed;
                },
                formatItem: function(data,i,max)
                {
                    var str = '<div class=result>'+data.nama+'<br/>'+data.alamat +'</div>';
                    return str;
                },
                width: 270, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
            }).result(
            function(event,data,formated){
                $(this).attr('value',data.nama);
                $('input[name=id_penetap]').val(data.id);              
            });
            
            $('#kelurahan').autocomplete("<?= base_url('referensi/get_kelurahan') ?>/",
            {
                parse: function(data)
                {
                    var parsed = [];
                    for (var i=0; i < data.length; i++)
                    {
                        parsed[i] =
                            {
                            data: data[i],
                            value: data[i].nama // nama field yang dicari
                        };
                    }
                    $('input[name=id_kelurahan]').val('');
                    return parsed;
                },
                formatItem: function(data,i,max)
                {
                    var str = '<div class=result>'+data.nama+'<br/>'+data.kecamatan +', '+data.kabupaten+', '+data.provinsi+'</div>';
                    return str;
                },
                width: 270, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
            }).result(
            function(event,data,formated){
                $(this).attr('value',data.nama);
                $('input[name=id_kelurahan]').val(data.id);              
            });
            
            $('#formregister').submit(function(){ 
                if($('#wkt_reg').val()==''){
                    custom_message('Peringatan','Waktu Registrasi tidak boleh kosong  !','#wkt_reg');            
                }else  if($('#jenis_reg').val()==''){
                    custom_message('Peringatan','Jenis registrasi tidak boleh kosong !','#jenis_reg');
                }else  if($('#nama').val()==''){
                    custom_message('Peringatan','Nama tidak boleh kosong !','#nama');
                }else  if($('input[name=id_direktur]').val()==''){
                    custom_message('Peringatan','Direktur tidak boleh kosong !','#direktur');
                }else  if($('#alamat_jln').val()==''){
                    custom_message('Peringatan','Alamat tidak boleh kosong !','#alamat_jln');
                }else  if($('#telp_no').val()==''){
                    custom_message('Peringatan','Nomor telepon tidak boleh kosong !','#telp_no');
                }else  if($('#ex_telp_no').val()==''){
                    custom_message('Peringatan','Nomor telepon ekstensi tidak boleh kosong !','#ex_telp_no');
                }else  if($('#fax').val()==''){
                    custom_message('Peringatan','Alamat fax tidak boleh kosong !','#fax');
                }else  if($('#ex_fax_no').val()==''){
                    custom_message('Peringatan','Nomor fax ekstensi tidak boleh kosong !','#ex_fax_no');
                }else  if($('#email').val()==''){
                    custom_message('Peringatan','Alamat email tidak boleh kosong !','#email');
                }else  if($('#tanah').val()==''){
                    custom_message('Peringatan','Luas tanah tidak boleh kosong !','#tanah');
                }else  if($('#bangunan').val()==''){
                    custom_message('Peringatan','Luas bangunan tidak boleh kosong !','#bangunan');
                }else  if($('#tgl').val()==''){
                    custom_message('Peringatan','Tanggal penetapan tidak boleh kosong !','#tgl');
                }else  if($('#no_surat').val()==''){
                    custom_message('Peringatan','Nomor surat izin tidak boleh kosong !','#no_surat');
                }else  if($('#sifat').val()==''){
                    custom_message('Peringatan','Sifat tidak boleh kosong !','#sifat'); 
                }else  if($('#masa').val()==''){
                    custom_message('Peringatan','Tanggal masa berlaku tidak boleh kosong !','#masa');
                }else  if($('#sps').val()==''){
                    custom_message('Peringatan','Status penyelenggara tidak boleh kosong !','#sps');
                }else  if($('#tgl_ak').val()==''){
                    custom_message('Peringatan','Tanggal akreditasi tidak boleh kosong !','#tgl_ak'); 
                }else  if($('#total').val()==''){
                    custom_message('Peringatan','Total nilak akreditasi tidak boleh kosong !','#total');
                }else{
                    save();                       
                    return false;
                }
                return false;
            });
            
        });
        
        function save(){
            var Url = '<?= base_url('registrasi_rs/manage_register') ?>/post/1';
            var last = $('#id_register').html();
            
            $.ajax({
                type : 'POST',
                url: Url,               
                data: $('#formregister').serialize(),
                cache: false,
                success: function(data) {
                    $('#register_list').html(data);                            
                    if($('input[name=id]').val() == ''){
                        alert_tambah();
                        $('input[name=id]').val(parseInt(last));
                    }else{
                        alert_edit();
                    }
                }
            });
        }
        
        function get_last_id(){
            $.ajax({
                type : 'GET',
                url: '<?= base_url('registrasi_rs/get_last_id') ?>/reg_rs/id',
                cache: false,
                dataType : 'json',
                success: function(data) {
                    $('#id_register').html(data.last_id);
                }
            });
        }
        
        function reset_all(){
            $('#alamat_jln, select, input[name=id], #id_register, input[type=hidden], input[type=text]').val('');
        }
        
        function get_register_list(p){
            $.ajax({
                type : 'GET',
                url: '<?= base_url("registrasi_rs/manage_register") ?>/list/'+p,
                cache: false,
                success: function(data) {
                    $('#register_list').html(data);
                }
            });
        }
        
        function delete_register(id){
            $('<div></div>')
              .html("Anda yakin akan menghapus data ini ?")
              .dialog({
                 title : "Hapus Data",
                 modal: true,
                 buttons: [ 
                    { 
                        text: "Ok", 
                        click: function() { 
                            $.ajax({
                                type : 'GET',
                                url: '<?= base_url("registrasi_rs/manage_register") ?>/delete/'+$('.noblock').html(),
                                data :'id='+id,
                                cache: false,
                                success: function(data) {
                                    $('#register_list').html(data);
                                    alert_delete();
                                }
                            });
                            $( this ).dialog( "close" ); 
                        } 
                    }, 
                    { text: "Batal", click: function() { $( this ).dialog( "close" );}} 
                ]
            });
        }
        
        function edit_register(id){
            
            $.ajax({
                type : 'GET',
                url: '<?= base_url("registrasi_rs/get_registrasi_rs") ?>/'+id,
                cache: false,
                dataType: 'json',
                success: function(data) {
                    console.log(data);
                    $('input[name=id]').val(data.id_reg);
                    $('#id_register').html(data.id_reg);
                    $('#wkt_reg').val((data.waktu != null)?datetimefmysql(data.waktu):null);
                    $('#jenis_reg').val(data.jenis);
                    $('#kode_h').val(data.kode_rs);
                    $('#nama').val(data.nama);
                    $('#jenis_rs').val(data.id_jenis_rs);
                    $('#kelas').val(data.kelas);
                    $('#direktur').val(data.direktur);
                    $('input[name=id_direktur]').val(data.id_kepegawaian_direktur);
                    $('#penyelenggara').val(data.penyelenggara);
                    $('input[name=id_penyelenggara]').val(data.id_instansi_relasi_penyelenggara);
                    $('#alamat_jln').val(data.alamat_jalan);
                    $('#kelurahan').val(data.kelurahan);
                    $('input[name=id_kelurahan]').val(data.id_kelurahan);
                    $('#telp_no').val(data.telp_no);
                    $('#ex_telp_no').val(data.extension_telp_no);
                    $('#fax').val(data.fax_no);
                    $('#ex_fax_no').val(data.extension_fax_no);
                    $('#email').val(data.alamat_email);
                    $('#website').val(data.url_website);
                    $('#tanah').val(data.luas_tanah);
                    $('#bangunan').val(data.luas_bangunan);
                    $('#tgl').val(data.tanggal_surat_izin_penetapan);
                    $('#no_surat').val(data.no_surat_izin_penetapan);
                    $('#penetap').val(data.penetap);
                    $('input[name=id_penetap]').val(data.id_instansi_relasi_penetap);
                    $('#sifat').val(data.sifat_penetapan);
                    $('#masa').val((data.tanggal_batas_masa_berlaku!=null)?datefmysql(data.tanggal_batas_masa_berlaku):null);
                    $('#sps').val(data.status_penyelenggara_swasta);
                    $('#tgl_ak').val((data.tanggal_akreditasi!=null)?datefmysql(data.tanggal_akreditasi):null);
                    $('#total').val(data.total_nilai_akreditasi);                    
                }
            }); 
        }

        function cetak_rl(tahun){
             window.open('<?= base_url("rekap_laporan/cetak_rl1_1") ?>/'+tahun, 'mywindow', 'width='+dWidth+', height='+dHeight+', left='+x+',top='+y);
        }
    </script>
    <div class="titling"><h1><?= $title ?></h1></div>
    <div class="data-input">
        <?= form_open('', 'id = formregister') ?>
        <?= form_hidden('id') ?>
        <fieldset style="border: transparent">
            <tr><td>ID.:</td><td><span class="label"><?= form_hidden('id') ?><span id="id_register"></span></span>
            <tr><td>Waktu Reg.:</td><td><span><?= form_input('wkt_reg', '', 'id=wkt_reg class=tanggaltime size=15') ?></span>
            <tr><td>Jenis Reg.:</td><td><span><?= form_dropdown('jenis', $jenis, null, "id=jenis_reg") ?></span>

        </table>
        <fieldset style="border: transparent">
            <legend></legend>
            <tr><td>Kode:</td><td><span><?= form_input('kode','','id=kode_h size=20')?></span>
            <tr><td>Nama:</td><td><span><?= form_input('nama', '', 'id=nama size=40') ?></span>
            <tr><td>Jenis RS:</td><td><span><?= form_dropdown('jenis_rs', $jenisrs, null, 'id=jenis_rs') ?></span>
            <tr><td>Kelas:</td><td><span><?= form_dropdown('kelas', $kelas, null, 'id=kelas') ?></span>
            <tr><td>Direktur:</td><td><span><?= form_input('direktur', '', 'id=direktur size=40') ?></span>
            <?= form_hidden("id_direktur") ?>
            <tr><td>Penyelenggara:</td><td><span><?= form_input('penyelenggara', '', 'id=penyelenggara size=40') ?></span>
            <?= form_hidden("id_penyelenggara") ?>
        </table>

        <fieldset>
            <legend>Lokasi</legend>
            <tr><td>Alamat Jalan:</td><td><span><?= form_textarea('alamat_jln', '', 'id=alamat_jln size=40') ?></span>
            <tr><td>Kelurahan:</td><td><span><?= form_input('kelurahan', '', 'id=kelurahan size=40') ?></span>
            <?= form_hidden("id_kelurahan") ?>
            <tr><td>No. Telp:</td><td><span><?= form_input('telp_no', '', 'id=telp_no size=40') ?><?= form_input('ex_telp_no', '', 'id=ex_telp_no size=10') ?></span>
            <tr><td>No. Fax:</td><td><span><?= form_input('fax', '', 'id=fax size=40') ?><?= form_input('ex_fax_no', '', 'id=ex_fax_no size=10') ?></span>
            <tr><td>Email:</td><td><span><?= form_input('email', '', 'id=email size=40') ?></span>
            <tr><td>Website:</td><td><span><?= form_input('website', '', 'id=website size=40') ?></span>
        </table>

        <fieldset>
            <legend>Fisik</legend>
            <tr><td>Tanah (m<sup>3</sup>):</td><td><span><?= form_input('tanah', '', 'id=tanah size=40') ?></span>
            <tr><td>Bangunan (m<sup>3</sup>):</td><td><span><?= form_input('bangunan', '', 'id=bangunan size=40') ?></span>
        </table>

        <fieldset>
            <legend>Penetapan</legend>          
            <tr><td>Tanggal:</td><td><span><?= form_input('tgl', '', 'id=tgl class=tanggal size=10') ?></span>
            <tr><td>No. Surat Izin:</td><td><span><?= form_input('no_surat', '', 'id=no_surat size=40') ?></span>
            <tr><td>Oleh:</td><td><span><?= form_input('penetap', '', 'id=penetap size=40') ?></span>
            <?= form_hidden("id_penetap") ?>
            <tr><td>Sifat:</td><td><span><?= form_dropdown('sifat', $sifat, null, 'id=sifat') ?></span>
            <tr><td>Masa Berlaku:</td><td><span><?= form_input('masa', '', 'id=masa class=tanggal size=10') ?></span>

        </table>

        <fieldset style="border: transparent">
            <legend></legend>
            <tr><td>S.P Swasta:</td><td><span><?= form_dropdown('sps', $status, null, 'id=sps'); ?></span>
        </table>
        <fieldset>
            <legend>Akreditasi</legend>
            <tr><td>Tanggal:</td><td><span><?= form_input('tgl_ak', '', 'id=tgl_ak class=tanggal size=10') ?></span>
            <tr><td>Total Nilai:</td><td><span><?= form_input('total', '', 'id=total size=40') ?></span>
        </table>
        <fieldset style="border: transparent">
            <tr><td></td><td>
            <?= form_button('simpan', "Simpan", 'id=simpan') ?>
            <?= form_button('reset', 'Reset', 'id=reset') ?>
        </table>
        <?= form_close() ?>
    </div>
    <div id="register_list"></div>
</div>