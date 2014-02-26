<?php $this->load->view('message') ?>
<title><?= $title ?></title>
<div class="titling"><h1><?= $title ?></h1></div>
<div class="kegiatan">
    <script type="text/javascript">
        $(function() {        
            $('#tabs').tabs();
            $('input[type=submit]').each(function(){ $(this).replaceWith('<button type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'">' + $(this).val() + '</button>');});
            $('button[type=submit]').button({icons: {secondary: 'ui-icon-circle-check'}});
            $('#reset,.resetan').button({icons: {secondary: 'ui-icon-refresh'}});
            $('#cari').button({icons:{secondary:'ui-icon-search'}});
            get_jurusan_list(1,'null');
            get_last_id();
            $('#reset').click(function(){
                $('#loaddata').empty();
                $('#loaddata').load($.cookie('url'));
            });
            $('#formjurusan').submit(function(){ 
                var Url = '<?= base_url('kepegawaian/manage_jurusan') ?>/cek/1';
                
                if($('input[name=id_jenis]').val()==''){
                    custom_message('Peringatan','Jenis tidak boleh kosong !','#jenis');
                }else if($('#nama').val()==''){
                    custom_message('Peringatan','Nama tidak boleh kosong !','#nama');
                }else{  
                    $.ajax({
                        type : 'POST',
                        url: Url,               
                        data: $('#formjurusan').serialize(),
                        dataType: 'json',
                        cache: false,
                        success: function(data) {
                            if($('input[name=id]').val() == ''){      
                                if (data.status == false){
                                    custom_message('Peringatan','Jurusan kualifikasi pendidikan sudah terdaftar !');
                                } else {
                                    save();                                    
                                }
                            }else{
                                save();
                            }
                        }
                    });
                    
              
                    return false;
                }
                return false;
            });
            
            $('#jenis').autocomplete("<?= base_url('kepegawaian/get_jenis') ?>",
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
                    $('input[name=id_jenis]').val('');
                    return parsed;
                },
                formatItem: function(data,i,max)
                {
                    var str = '<div class=result>'+data.nama+'</div>';
                    return str;
                },
                width: 270, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
            }).result(
            function(event,data,formated){
                $(this).attr('value',data.nama);
                $('input[name=id_jenis]').val(data.id);
            });
            
        });
        
        function save(){
            var Url = '<?= base_url('kepegawaian/manage_jurusan') ?>/post/1';
            var last = $('#id_jurusan').html();
            
            $.ajax({
                type : 'POST',
                url: Url,               
                data: $('#formjurusan').serialize(),
                cache: false,
                success: function(data) {
                    $('#jurusan_list').html(data);                            
                    if($('input[name=id]').val() == ''){
                        alert_tambah();
                        $('input[name=id]').val(parseInt(last));
                    }else{
                        alert_edit();
                    }
                }
            });
        }
        
        function reset_all(){
            $('input[name=id], #id_jurusan, #nama, #jenis, input[name=id_jenis]').val('');
        }
        
        function get_last_id(){
            $.ajax({
                type : 'GET',
                url: '<?= base_url('kepegawaian/get_last_id') ?>/jurusan_kualifikasi_pendidikan/id',
                cache: false,
                dataType : 'json',
                success: function(data) {
                    $('#id_jurusan').html(data.last_id);
                }
            });
        }
        
        function get_jurusan_list(p){
            $.ajax({
                type : 'GET',
                url: '<?= base_url('kepegawaian/manage_jurusan') ?>/list/'+p, 
                data : $('#formjurusan').serialize(),
                cache: false,
                success: function(data) {
                    $('#jurusan_list').html(data);
                }
            });
        }
        
        function delete_jurusan(id){
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
                                url: '<?= base_url('kepegawaian/manage_jurusan') ?>/delete/'+$('.noblock').html(),
                                data :'id='+id,
                                cache: false,
                                success: function(data) {
                                    get_jurusan_list($('.noblock').html());
                                    alert_delete();
                                },
                                error: function(){
                                    alert_delete_failed();
                                }
                            });
                            $( this ).dialog( "close" ); 
                        } 
                    }, 
                    { text: "Batal", click: function() { $( this ).dialog( "close" );}} 
                ]
            });
        }
        
        function edit_jurusan(id, nama, titel, id_jenis, jenis, adm){
            $('input[name=id]').val(id);
            $('#id_jurusan').html(id);
            $('#nama').val(nama);
            $('#titel').val(titel);
            $('#jenis').val(jenis);
            $('input[name=id_jenis]').val(id_jenis);
            if (adm == 'Ya') {
                $('#ya').attr('checked','checked');
            }else{
                $('#tidak').attr('checked','checked');
            }            
        }
        
        function paging(page, tab, cari){
            get_jurusan_list(page);
        }
    </script>
        <div id="tabs">
            <ul>
                <li><a href="#tabs-1">Parameter</a></li>
            </ul>
            <div id="tabs-1">
                <?= form_open('', 'id = formjurusan') ?>
                <table class="inputan" widht="100%">
                    <tr><td>ID.:</td><td><span class="label"><?= form_hidden('id') ?><span id="id_jurusan"></span></span>
                    <tr><td>Jenis:</td><td><?= form_input('jenis', '', 'id=jenis size=40') ?>
                    <?= form_hidden('id_jenis') ?>
                    <tr><td>Nama:</td><td><span><?= form_input('nama', '', 'id=nama size=40') ?></span>
                    <tr><td>Titel:</td><td><?= form_input('titel', '', 'id=titel size=40') ?>
                    <tr><td>Digunakan Pendaftaran:</td><td><span class="label"><?= form_radio('admission', 'Tidak', false, 'id=tidak') ?> Tidak</span>  <span class="label"><?= form_radio('admission', 'Ya', FALSE, 'id=ya') ?> Ya</span>
                    <tr><td></td><td><?= form_submit('simpan', "Simpan", 'id=simpan') ?>
                    <?= form_button('cari', "Cari", 'id=cari onclick=get_jurusan_list(1)') ?>
                    <?= form_button('reset', 'Reset', 'id=reset') ?>
                </table>
                 <?= form_close() ?>
                <div id="jurusan_list"></div>
            </div>
        </div>
</div>