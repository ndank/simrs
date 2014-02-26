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
            get_last_id();
            get_jenis_list(1,'null');
            $('#reset').click(function(){
                $('#loaddata').empty();
                $('#loaddata').load($.cookie('url'));
            });
            $('#formjenis').submit(function(){ 
                var Url = '<?= base_url('kepegawaian/manage_jenis') ?>/cek/1';
                if($('#nama').val()==''){
                    custom_message('Peringatan','Nama tidak boleh kosong !','#nama');
                }else{
                    $.ajax({
                        type : 'POST',
                        url: Url,               
                        data: $('#formjenis').serialize(),
                        dataType: 'json',
                        cache: false,
                        success: function(data) {
                            if($('input[name=id]').val() == ''){       
                                if (data.status == false){
                                    custom_message('Peringatan','Jenis jurusan kualifikasi pendidikan sudah terdaftar !');
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
            var Url = '<?= base_url('kepegawaian/manage_jenis') ?>/post/1';
            var last = $('#id_jenis').html();
            
            $.ajax({
                type : 'POST',
                url: Url,               
                data: $('#formjenis').serialize(),
                cache: false,
                success: function(data) {
                    $('#jenis_list').html(data);                            
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
            $('input[name=id]').val('');
            $('#id_jenis').html('');
            $('#nama').val('');
            $('#tidak').removeAttr('checked');
            $('#ya').attr('checked', true);
        }
        
        function get_last_id(){
            $.ajax({
                type : 'GET',
                url: '<?= base_url('kepegawaian/get_last_id') ?>/jenis_jurusan_kualifikasi_pendidikan/id',
                cache: false,
                dataType : 'json',
                success: function(data) {
                    $('#id_jenis').html(data.last_id);
                }
            });
        }
        
        function get_jenis_list(p){
            $.ajax({
                type : 'GET',
                url: '<?= base_url('kepegawaian/manage_jenis') ?>/list/'+p, 
                data : $('#formjenis').serialize(),
                cache: false,
                success: function(data) {
                    $('#jenis_list').html(data);
                }
            });
        }
        
        function delete_jenis(id){
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
                                url: '<?= base_url('kepegawaian/manage_jenis') ?>/delete/'+$('.noblock').html(),
                                data :'id='+id,
                                cache: false,
                                success: function(data) {
                                    get_jenis_list($('.noblock').html());
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
        
        function edit_jenis(id, nama, nakes){
            $('input[name=id]').val(id);
            $('#id_jenis').html(id);
            $('#nama').val(nama);
            if(nakes == 'Ya'){
                $('#tidak').removeAttr('checked');
                $('#ya').attr('checked', true);
            }else{
                $('#ya').removeAttr('checked');
                $('#tidak').attr('checked', true);
            }
            
        }
        
        function paging(page, tab, cari){
            get_jenis_list(page,cari);
        }
    </script>
        <div class='msg'></div>
        <div id="tabs">
            <ul>
                <li><a href="#tabs-1">Parameter</a></li>
            </ul>
            <div id="tabs-1">
                <?= form_open('', 'id = formjenis') ?>
                <table width="100%" class="inputan">
                    <tr><td>Nakes</td><td><span class="label"><?= form_radio('nakes', 'Ya', false, 'id=ya') ?>Ya
                    </span> <span class="label"><?= form_radio('nakes', 'Tidak', false, 'id=tidak') ?>Tidak</span>
                    <tr><td>Nama</td><td><span><?= form_input('nama', '', 'id=nama size=30') ?></span>
                    <tr><td></td><td><?= form_submit('simpan', "Simpan", 'id=simpan') ?>
                    <?= form_button('cari', "Cari", 'id=cari onclick=get_jenis_list(1)') ?>
                    <?= form_button('reset', 'Reset', 'id=reset') ?>
                    <?= form_close() ?>
                </table>
                <div id="jenis_list"></div>
            </div>
        </div>
</div>