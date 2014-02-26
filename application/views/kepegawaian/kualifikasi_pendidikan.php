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
            get_pendidikan_list(1,'null');
            get_last_id();
            $('#reset').click(function(){
                $('#loaddata').empty();
                $('#loaddata').load($.cookie('url'));
            });
            $('#formpendidikan').submit(function(){
                var Url = '<?= base_url('kepegawaian/manage_pendidikan') ?>/cek/1';
    
                if($('#nama').val()==''){
                    custom_message('Peringatan','Nama tidak boleh kosong !','#nama');
                }else{
                    $.ajax({
                        type : 'POST',
                        url: Url,               
                        data: $('#formpendidikan').serialize(),
                        dataType: 'json',
                        cache: false,
                        success: function(data) {     
                            if($('input[name=id]').val() == ''){                                
                                if (data.status == false){
                                    custom_message('Peringatan','Kualifikasi pendidikan sudah terdaftar !');     
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
            
        });
        
        function save(){
            var Url = '<?= base_url('kepegawaian/manage_pendidikan') ?>/post/1';
            var last = $('#id_pendidikan').html();
            $.ajax({
                type : 'POST',
                url: Url,               
                data: $('#formpendidikan').serialize(),
                cache: false,
                success: function(data) {
                    $('#pendidikan_list').html(data);                            
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
            $('.msg').fadeOut('fast');
            $('input[name=id]').val('');
            $('#id_pendidikan').html('');
            $('#nama').val('');
            $('#tidak').removeAttr('checked');
            $('#ya').attr('checked', true);
        }
        
        function get_last_id(){
            $.ajax({
                type : 'GET',
                url: '<?= base_url('kepegawaian/get_last_id') ?>/kualifikasi_pendidikan/id',
                cache: false,
                dataType : 'json',
                success: function(data) {
                    $('#id_pendidikan').html(data.last_id);
                   
                }
            });
        }
        
        function get_pendidikan_list(p){
            $.ajax({
                type : 'GET',
                url: '<?= base_url('kepegawaian/manage_pendidikan') ?>/list/'+p, 
                data : $('#formpendidikan').serialize(),
                cache: false,
                success: function(data) {
                    $('#pendidikan_list').html(data);
                }
            });
        }
        
        function delete_pendidikan(id){            
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
                                url: '<?= base_url('kepegawaian/manage_pendidikan') ?>/delete/'+$('.noblock').html(),
                                data :'id='+id,
                                cache: false,
                                success: function(data) {
                                    get_pendidikan_list($('.noblock').html());
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
        
        function edit_pendidikan(id, nama){
            $('input[name=id]').val(id);
            $('#id_pendidikan').html(id);
            $('#nama').val(nama);
           
            
        }
        
        function paging(page, tab, cari){
            get_pendidikan_list(page);
        }
    </script>
    <div id="tabs">
        <ul>
            <li><a href="#tabs-1">Parameter</a></li>
        </ul>
        <div id="tabs-1">
            <table class="inputan" widht="100%">
                <?= form_open('', 'id = formpendidikan') ?>
                <tr><td>ID.</td><td><span class="label"><?= form_hidden('id') ?><span id="id_pendidikan"></span></span>
                <tr><td>Nama</td><td><span><?= form_input('nama', '', 'id=nama size=30') ?></span>
                <tr><td></td><td><?= form_submit('simpan', "Simpan", 'id=simpan') ?>
                <?= form_button('cari', "Cari", 'id=cari onclick=get_pendidikan_list(1)') ?>
                <?= form_button('reset', 'Reset', 'id=reset') ?>
                <?= form_close() ?>
            </table>
            <div id="pendidikan_list"></div>
        </div>
    </div>
    

</div>