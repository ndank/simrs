<?php $this->load->view('message') ?>
<title><?= $title ?></title>
<div class="titling"><h1><?= $title ?></h1></div>
<div class="kegiatan">
    <script type="text/javascript">
        var request;
        $(function(){
            $('#tabs').tabs();
            $( "#addpenduduk" ).button({icons: {secondary: "ui-icon-circle-plus"}});
            $('#simpan, #save_dinamis').button({icons: {secondary: 'ui-icon-circle-check'}});
            $('.resetan').button({icons: {secondary: 'ui-icon-refresh'}});
            $('.cari').button({icons: {secondary: 'ui-icon-search'}});
            get_penduduk_list(1,'');
            $('#form_penduduk').dialog({
                autoOpen: false,
                height: 550,
                width: 1024,
                modal: true,
                resizable : false,
                close : function(){
                    reset_all();
                }
            });
            
            $('#konfirmasi').dialog({
                autoOpen: false,
                title :'Konfirmasi',
                height: 200,
                width: 300,
                modal: true,
                resizable : false,
                buttons: [ 
                    { text: "Ok", click: function() { 
                            save();
                            $( this ).dialog( "close" ); 
                        } 
                    }, 
                    { text: "Batal", click: function() { 
                            $( this ).dialog( "close" ); 
                        } 
                    } 
                ]
            });
            
            $('#showAll').click(function(){
                $('#loaddata').empty();
                $('#loaddata').load('<?= base_url('referensi/penduduk') ?>');
            });
        
            $('.tgl').datepicker({
                changeMonth: true,
                changeYear: true,
                dateFormat : 'dd/mm/yy',
                maxDate: 0
            });
        
            $('#form_cari_pdd').dialog({
                autoOpen: false,
                height: 380,
                width: 400,
                modal: true,
                resizable : false,
                buttons: [ 
                    { text: "Cari", click: function() { 
                            form_cari_submit(); 
                        } 
                    }, 
                    { text: "Reset", click: function() { 
                            reset_all();
                        } 
                    } 
                ]
            });
            $( "#tab" ).tabs({selected: 0 });
            $('#addpenduduk').click(function() {
                get_last_id();
                $('input[name=tipe]').val('add');
                $('#form_penduduk').dialog("option",  "title", "Tambah Penduduk");
                $('#form_penduduk').dialog("open");
                $( "#tab" ).tabs({selected: 0 });
                $(".dinamis").hide();
                $('#reset').removeAttr('disabled');
                $('#nama').focus();
            });
        
            $('.resetan').click(function() {
                reset_all();
            });
            
            
            
            $('#showAll').click(function() {
                get_penduduk_list(1,'');
            });
        
            $('#searching').button({
                icons: {
                    secondary: 'ui-icon-search'
                }
            }).click(function() {
                $('#form_cari_pdd').dialog("option",  "title", "Pencarian Penduduk");
                $('#form_cari_pdd').dialog("open");
                reset_all();
            });

            $('#save_dinamis').click(function(){
                $('#formdinamis').submit();
            });
        
            $('#formdinamis').submit(function(){     
                Url = '<?= base_url('referensi/manage_penduduk') ?>/edit_dinamis/';              
                if(!request) {
                    request =  $.ajax({
                        type : 'POST',
                        url: Url,               
                        data: $(this).serialize(),
                        cache: false,
                        success: function(res) {
                            var data = $.parseJSON(res);
                            get_penduduk_list($('.noblock').html(), '')
                            get_dinamis_penduduk_list(data.id);
                            $('input[name=hd_pdd_dinamis]').val('');
                            alert_edit();                            
                           // reset_all();
                            request = null;                            
                        }
                    });
                }  
                return false;
            });
        
            $('.kabupaten').autocomplete("<?= base_url('referensi/get_kabupaten') ?>",
            {
                parse: function(data){
                    var parsed = [];
                    for (var i=0; i < data.length; i++) {
                        parsed[i] = {
                            data: data[i],
                            value: data[i].nama // nama field yang dicari
                        };
                    }
                    return parsed;
                },
                formatItem: function(data,i,max){
                    var str = '<div class=result>'+data.nama+'<br/>Provinsi: '+data.provinsi+'</div>';
                    return str;
                },
                width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
            }).result(
            function(event,data,formated){
                $(this).val(data.nama);
                $('input[name=id_kabupaten]').val(data.id);
                $('input[name=id_kabupaten_cari]').val(data.id);
            });
        
            $('.kelurahan').autocomplete("<?= base_url('referensi/get_kelurahan') ?>",
            {
                parse: function(data){
                    var parsed = [];
                    for (var i=0; i < data.length; i++) {
                        parsed[i] = {
                            data: data[i],
                            value: data[i].nama // nama field yang dicari
                        };
                    }
                    return parsed;
                },
                formatItem: function(data,i,max){
                    var str = '<div class=result>'+data.nama+'<br/>Kec: '+data.kecamatan+', Kab: '+data.kabupaten+', Prov: '+data.provinsi+'</div>';
                    return str;
                },
                width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
            }).result(
            function(event,data,formated){
                $(this).val(data.nama);
                $('input[name=id_kelurahan]').val(data.id);
                // $('.id_kabupaten').val(data.id_kabupaten);
            });

            $('#simpan').click(function(){
                 $('#formpenduduk').submit();
            });
            
            $('#formpenduduk').submit(function(){
                var nama = $('#nama').val();
                var tipe = $('input[name=tipe]').val();
                if(nama==''){
                    custom_message('Peringatan','Nama penduduk tidak boleh kosong !','#nama');
                    return false;
                }else{    
                    if(!request) {
                        request = $.ajax({
                            url: '<?= base_url('referensi/manage_penduduk') ?>/cek',
                            data:'nama='+nama,
                            cache: false,
                            dataType: 'json',
                            success: function(msg){
                                request = null;
                                if (tipe == 'add'){
                                    if (msg.status == false){
                                        $('#text_konfirmasi').html('Nama penduduk <b>"'+nama+'"</b> sudah ada<br/> Apakah anda yakin akan menambahkannya lagi?');            
                                        $('#konfirmasi').dialog("open");
                                    } else {
                                        save();
                                    }                            
                                }else{
                                    save();
                                }
                            }
                        });
                    }
                }
                return false;
            });
        });
        
        function form_cari_submit(){
            var Url = '<?= base_url('referensi/manage_penduduk') ?>/list/1';
           
            if(!request) {
                request =  $.ajax({
                    url: Url,               
                    data: $('#formcaripenduduk').serialize(),
                    cache: false,
                    success: function(data) {
                        $('#penduduk_list').html(data);                           
                        $('#form_cari_pdd').dialog('close');
                        request = null;                            
                    }
                });
            }
        
        }

        function save(){
            var Url = '';       
            var tipe = $('input[name=tipe]').val();
            if( tipe== 'add'){
                Url = '<?= base_url('referensi/manage_penduduk') ?>/add/';
            }else{
                Url = '<?= base_url('referensi/manage_penduduk') ?>/edit/';
            }   
            
            if(!request) {
                request =  $.ajax({
                    type : 'POST',
                    url: Url+1,               
                    data: $("#formpenduduk").serialize(),
                    cache: false,
                    success: function(data) {
                        $('#penduduk_list').html(data);
                        $('#form_penduduk').dialog("close");
                        if(tipe == 'add'){
                            alert_tambah();
                        }else{
                            alert_edit();
                        }
                        reset_all(); 
                        request = null;                            
                    }
                });
            }  
        }
    
        function reset_all(){
            $('input[name=tipe]').val('');
            $('#nama').val('');
            $('#nama_cari').val('');
            $('#alamat').val('');
            $('#alamat_cari').val('');
            $('#telp').val('');
            $('#telp_cari').val('');
            $('.kabupaten').val('');
            $('.l').removeAttr('checked');
            $('.p').removeAttr('checked');
            $('#gol_darah').val('');
            $('#gol_darah_cari').val('');
            $('.tgl').val('');
        
        
            $('input[name=id_kabupaten]').val('');
            $('input[name=id_kabupaten_cari]').val('');
      
        }
    
        function get_last_id(){
            $.ajax({
                type : 'GET',
                url: '<?= base_url('referensi/get_last_id') ?>/penduduk/id',
                cache: false,
                dataType : 'json',
                success: function(data) {
                    $('#nomor_penduduk').html(data.last_id);
                    $('input[name=id_penduduk]').val(data.last_id);
                }
            });
        }
    
        function get_penduduk_list(p){
            $.ajax({
                type : 'GET',
                url: '<?= base_url('referensi/manage_penduduk') ?>/list/'+p,
                data : $('#formcaripenduduk').serialize() ,
                cache: false,
                success: function(data) {
                    $('#penduduk_list').html(data);
                }
            });
        }    
     
        function delete_penduduk(id){
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
                                url: '<?= base_url('referensi/manage_penduduk') ?>/use/'+$('.noblock').html(),
                                data :'id='+id,
                                dataType : 'json',
                                cache: false,
                                success: function(data) {
                                    if(data.status == true){
                                        delete_user_penduduk(id);
                                    }else{
                                         $('<div></div>')
                                            .html("Data penduduk tidak bisa dihapus karena digunakan sebagai user")
                                            .dialog({
                                                title : "Hapus Data",
                                                modal: true,
                                                buttons : [{
                                                    text : "Ok",
                                                    click : function(){
                                                        $( this ).dialog( "close" ); 
                                                    }
                                                }]
                                            });
                                    }
                                }
                            });
                            $( this ).dialog( "close" ); 
                        } 
                    }, 
                    { text: "Batal", click: function() { $( this ).dialog( "close" );}} 
                ]
            });     
        }
    

        function delete_user_penduduk(id){
            $.ajax({
                type : 'GET',
                url: '<?= base_url('referensi/manage_penduduk') ?>/delete/'+$('.noblock').html(),
                data :'id='+id,
                cache: false,
                success: function(data) {
                    get_penduduk_list($('.noblock').html());
                    alert_delete();
                }
            });
        }
        function edit_penduduk(arr){
            var data = arr.split("#");
            $(".dinamis").show();
            $('input[name=tipe]').val('edit');
        
            $('#nomor_penduduk').html(data[0]);
            $('#id_pdd_dinamis').html(data[0]);
            $('input[name=id_penduduk]').val(data[0]);       
            $('input[name=id_pdd_dinamis]').val(data[0]);
            $('#nama').val(data[1]);
            $('#nama_pdd').html(data[1]);
            $('#alamat').val(data[2]);  
            $('input[name=alamat_lama]').val(data[2]);
            $('#telp').val(data[3]);
            $('input[name=id_kabupaten]').val(data[4]);
            $('#kabupaten').val(data[5]);
            if(data[6] == 'L'){
                $('.l').attr('checked','checked');
            }else if(data[6] == 'P'){
                $('.p').attr('checked','checked');
            }
      
            $('#gol_darah').val(data[7]);
            $('#awal').val(datefmysql(data[8]));
            get_last_dinamis_penduduk(data[0], data[9]);
            get_dinamis_penduduk_list(data[0]);
            $( "#tab" ).tabs({selected: 0 });
    
        
            $('#form_penduduk').dialog("option",  "title", "Edit Kependudukan Pasien");
            $('#form_penduduk').dialog("open");
            $('#reset').attr('disabled','disabled');
        }
    
        function get_last_dinamis_penduduk(id, id_dp){
            $.ajax({
                type : 'GET',
                url: '<?= base_url("referensi/dinamis_penduduk_get_data") ?>',
                data : 'id='+id+'&id_dp='+id_dp,
                cache: false,
                dataType : 'json',
                success: function(msg) {
                    var data = msg[0];
                    $('#tgl_dinamis').html(datefmysql(data.tanggal));
                    $('input[name=id_pdd]').val(data.penduduk_id);
                    $('#noid').val(data.identitas_no);
                    $('#alamat_dinamis').val(data.alamat);
                    $('#kelurahan_dinamis').val(data.kelurahan);
                    $('input[name=id_kelurahan]').val(data.kelurahan_id);
                    $('#pernikahan').val(data.pernikahan);
                    $('#nokk').val(data.kk_no);
                    $('#posisi').val(data.posisi);
                    $('#pendidikan').val(data.pendidikan_id);
                    $('#profesi').val(data.profesi_id);
                    $('#nostr').val(data.str_no);
                    $('#nosip').val(data.sip_no);
                    $('#pekerjaan').val(data.pekerjaan_id);
                    $('#nosik').val(data.kerja_izin_surat_no);
                    $('#jabatan').val(data.jabatan);
                    $('#agama').val(data.agama);
                                
                }
            });
        }
    
        function get_dinamis_penduduk_list(id){
            $.ajax({
                type : 'GET',
                url: '<?= base_url('referensi/dinamis_penduduk_get_list') ?>',
                data :'id='+id,
                cache: false,
                success: function(data) {
                    $('#dinamis_list').html(data);
                }
            });
        }
    
        function paging(page, tab,search){
            get_penduduk_list(page);
        }
    </script>
    <div id="tabs">
        <ul>
            <li><a href="#tabs-1">Parameter</a></li>
        </ul>
        <div id="tabs-1">
            <?= form_button('', 'Tambah', 'id=addpenduduk') ?>
            <?= form_button('', 'Cari', 'id=searching') ?>
            <?= form_button('', 'Reset', 'class=resetan id=showAll') ?>
            <div id="list" class="data-list">
                <div id="form_penduduk" style="display: none;position: relative; background: #fff; padding: 10px;">


                    <div id="tab">
                        <ul>
                            <li><a class="pdd" href="#pdd">Penduduk</a>  </li>
                            <li><a class="dinamis" href="#dinamis">Sejarah Penduduk</a>  </li>
                        </ul>

                        <div id="pdd">
                            <?= form_open('', 'id=formpenduduk') ?>
                            <?= form_hidden('tipe') ?>
                            <?= form_hidden('id_penduduk') ?>
                            <table width="100%"class="inputan">
                                <tr>
                                    <td width="15%">ID</td>
                                    <td><span id="nomor_penduduk"></span></td>
                                </tr>
                                <tr>
                                    <td width="15%">Nama</td>
                                    <td><?= form_input('nama', '', 'id=nama size=40') ?> </td>
                                </tr>
                                <tr>
                                    <td width="15%">Alamat</td>
                                    <td><?= form_textarea('alamat', '', 'id=alamat rows=2 cols=41 style="height: 50px"') ?>
                                        <?= form_hidden('alamat_lama') ?></td>
                                </tr>
                                <tr>
                                    <td width="15%">Telepon</td>
                                    <td>
                                        <?= form_input('telp', '', 'id=telp size=40') ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="15%">Tempat Lahir</td>
                                    <td>
                                        <?= form_input('', '', 'class=kabupaten id=kabupaten size=40') ?>
                                        <?= form_hidden('id_kabupaten') ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="15%">Gender</td>
                                    <td>
                                        <?= form_radio('kelamin', 'L', true, 'class=l') ?>Laki -laki
                                        <?= form_radio('kelamin', 'P', false, 'class=p') ?>Perempuan
                                    </td>
                                </tr>
                                <tr>
                                    <td width="15%">Golongan Darah</td>
                                    <td><?= form_dropdown('gol_darah', $gol_darah, null, 'id=gol_darah') ?></td>
                                </tr>
                                <tr>
                                    <td width="15%">Tanggal Lahir</td>
                                    <td><?= form_input('tgl_lahir', '', 'id=awal class=tgl size=10 placeholder=dd/mm/yyyy') ?> </td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>
                                        <?= form_button('save', 'Simpan', 'id=simpan') ?>
                                        <?= form_button('', 'Reset', 'id=reset class=resetan') ?>
                                    </td>
                                </tr>
                            </table>
                            <?= form_close() ?>
                        </div>

                        <script type="text/javascript">
                            function edit_dinamis_penduduk(id, id_dp){
                                $('input[name=hd_pdd_dinamis]').val(id_dp);
                                get_last_dinamis_penduduk(id, id_dp);
                            }

                            function delete_dinamis_penduduk(id, obj){

                                $('<div></div>')
                                  .html("Anda yakin akan menghapus data ini ?")
                                  .dialog({
                                     title : "Hapus Data",
                                     modal: true,
                                     buttons: [ 
                                        { 
                                            text: "Ok", 
                                            click: function() { 
                                                hapus(id, obj);
                                                $( this ).dialog( "close" ); 
                                            } 
                                        }, 
                                        { text: "Batal", click: function() { $( this ).dialog( "close" );}} 
                                    ]
                                });


                            }
                             function eliminate(el) {
                                var parent = el.parentNode.parentNode;
                                parent.parentNode.removeChild(parent);      
                            }

                            function hapus(id,obj){
                                 $.ajax({
                                    type : 'GET',
                                    url: '<?= base_url('referensi/dinamis_penduduk_delete_data') ?>/'+id,
                                    cache: false,
                                    success: function(data) {
                                        eliminate(obj);
                                       alert_delete();
                                    },
                                    error :function(){

                                    }
                                });
                            }

                        </script>

                        <div id="dinamis">
                            <table width="100%"class="inputan">
                                  <tr>
                                    <td width="15%">Tanggal</td>
                                    <td><span id="tgl_dinamis"></span></td>
                                </tr>
                            </table>
                            <?= form_open('', 'id=formdinamis') ?>
                            <?= form_hidden('id_pdd') ?>
                            <?= form_hidden('hd_pdd_dinamis') ?>

                            <table width="100%">
                                <tr>
                                    <td width="15%">No. Identitas</td>
                                    <td><?= form_input('noid', '', 'id=noid size=60') ?> </td>
                                </tr>
                                <tr>
                                    <td width="15%">Alamat</td>
                                    <td><?= form_textarea('alamat', '', 'id=alamat_dinamis cols=30 style="height: 50px"') ?> </td>
                                </tr>
                                <tr valign="top">
                                    <td width="15%">Kelurahan</td>
                                    <td>
                                        <?= form_input('', '', 'id=kelurahan_dinamis class=kelurahan size=60') ?><br/>
                                        <?= form_hidden('id_kelurahan', '', 'class=id_kelurahan') ?>

                                    </td>
                                </tr>
                                <tr>
                                    <td width="15%">Agama</td>
                                    <td><?= form_dropdown('agama', $agama, null, 'id=agama class=standar') ?></td>
                                </tr>
                                <tr>
                                    <td width="15%">Pernikahan</td>
                                    <td><?= form_dropdown('pernikahan', $pernikahan, null, 'id=pernikahan  class=standar') ?></td>
                                </tr>
                                <tr>
                                    <td width="15%">No. KK</td>
                                    <td><?= form_input('nokk', '', 'id=nokk size=60') ?> </td>
                                </tr>
                                <tr>
                                    <td width="15%">Posisi Keluarga</td>
                                    <td><?= form_dropdown('posisi', $posisi, null, 'id=posisi class=standar') ?></td>
                                </tr>
                                <tr>
                                    <td width="15%">Pendidikan</td>
                                    <td><?= form_dropdown('pendidikan', $pendidikan, null, 'id=pendidikan class=standar') ?></td>
                                </tr>
                                <tr>
                                    <td width="15%">Profesi</td>
                                    <td><?= form_dropdown('profesi', $profesi, null, 'id=profesi class=standar') ?></td>
                                </tr>
                                <tr>
                                    <td width="15%">No. STR </td>
                                    <td><?= form_input('nostr', '', 'id=nostr size=60') ?> </td>
                                </tr>
                                <tr>
                                    <td width="15%">No. SIP</td>
                                    <td><?= form_input('nosip', '', 'id=nosip size=60') ?> </td>
                                </tr>
                                <tr>
                                    <td width="15%">Pekerjaan</td>
                                    <td><?= form_dropdown('pekerjaan', $pekerjaan, null, 'id=pekerjaan class=standar') ?></td>
                                </tr>
                                <tr>
                                    <td width="15%">No. Surat Ijin Kerja</td>
                                    <td><?= form_input('nosik', '', 'id=nosik size=60') ?> </td>
                                </tr>
                                <tr>
                                    <td width="15%">Jabatan</td>
                                    <td><?= form_dropdown('jabatan', $jabatan, null, 'id=jabatan class=standar') ?></td>
                                </tr>

                                <tr>
                                    <td></td>
                                    <td>
                                        <?= form_button('savedinamis', 'Simpan', 'id=save_dinamis') ?>
                                    </td>
                                </tr>
                            </table>
                            <?= form_close() ?>
                            <br/>
                            <hr/>
                            <h2>Riwayat dinamis penduduk</h2>
                            <div id="dinamis_list"></div>
                        </div>

                    </div>
                </div>

                <div id="konfirmasi" style="display: none; padding: 20px;">
                    <div id="text_konfirmasi"></div>
                </div>
                <div id="penduduk_list"></div>
                <div id="form_cari_pdd" style="display: none;position: relative; background: #fff; padding: 10px;">
                    <?= form_open('', 'id=formcaripenduduk') ?>

                    <table width="100%" class="inputan">
                        <tr>
                            <td width="15%">Nama:</td>
                            <td><?= form_input('nama_cari', '', 'id=nama_cari size=40') ?> </td>
                        </tr>
                        <tr>
                            <td width="15%">Alamat:</td>
                            <td><?= form_textarea('alamat_cari', '', 'id=alamat_cari rows=2 cols=41 style="height: 50px"') ?></td>
                        </tr>
                        <tr>
                            <td width="15%">Telepon:</td>
                            <td>
                                <?= form_input('telp_cari', '', 'id=telp_cari size=40') ?>
                            </td>
                        </tr>
                        <tr>
                            <td width="15%">Tempat Lahir:</td>
                            <td>
                                <?= form_input('kabupaten_cari', '', 'class=kabupaten size=40') ?>
                                <?= form_hidden('id_kabupaten_cari') ?>
                            </td>
                        </tr>
                        <tr>
                            <td width="15%">Gender</td>
                            <td>
                                <?= form_radio('kelamin_cari', 'L', false, 'class=l') ?>Laki -laki
                                <?= form_radio('kelamin_cari', 'P', false, 'class=p') ?>Perempuan
                            </td>
                        </tr>
                        <tr>
                            <td width="15%">Golongan Darah</td>
                            <td><?= form_dropdown('gol_darah_cari', $gol_darah, null, 'id=gol_darah_cari') ?></td>
                        </tr>
                        <tr>
                            <td width="15%">Tanggal Lahir</td>
                            <td><?= form_input('tgl_lahir_cari', '', 'id=awal_cari class=tgl size=10 placeholder=dd/mm/yyyy') ?> </td>
                        </tr>
                    </table>
                    <?= form_close() ?>
                </div>
            </div>
        </div>
    </div>
</div>