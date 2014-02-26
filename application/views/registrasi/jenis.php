<?php $this->load->view('message') ?>
<title><?= $title ?></title>
<div class="kegiatan">
    <script type="text/javascript">
        $(function() {        
            $('#simpan').button({icons: {secondary: 'ui-icon-circle-check'}}).click(function(){
                $('#formjenis').submit();
            });
            $('#reset,.resetan').button({icons: {secondary: 'ui-icon-refresh'}});
            $('#cari').button({icons:{secondary:'ui-icon-search'}});
            get_jenis_list(1,'null');
            
            get_last_id();
            $('#reset').click(function(){
                $('#loaddata').empty();
                $('#loaddata').load($.cookie('url'));
            });
            $('#formjenis').submit(function(){ 
                var Url = '<?= base_url('registrasi_rs/manage_jenis') ?>/cek/1';
               
                if($('#nama').val()==''){
                    custom_message('Peringatan','Nama tidak boleh kosong !','#nama');
                }else if($('#ket').val()==''){
                    custom_message('Peringatan','Keterangan tidak boleh kosong !','#ket');
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
                                    custom_message('Peringatan','Jenis RS sudah terdaftar !');
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
            var Url = '<?= base_url("registrasi_rs/manage_jenis") ?>/post/1';
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
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    if(confirm("Error, Reload??")){      
                    }
                }
            });
        }
        
        function reset_all(){
            $('input[name=id], #id_jenis, #nama, #ket').val('');
        }
        
        function get_last_id(){
            $.ajax({
                type : 'GET',
                url: '<?= base_url('registrasi_rs/get_last_id') ?>/jenis_rs/id',
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
                url: '<?= base_url('registrasi_rs/manage_jenis') ?>/list/'+p,
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
                                url: '<?= base_url('registrasi_rs/manage_jenis') ?>/delete/'+$('.noblock').html(),
                                data :'id='+id,
                                cache: false,
                                success: function(data) {
                                    $('#jenis_list').html(data);
                                    reset_all();
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
        
        function edit_jenis(id, nama, ket){
            $('input[name=id]').val(id);
            $('#id_jenis').html(id);
            $('#nama').val(nama);
            $('#ket').val(ket);
            
        }
        
        function paging(page, tab, cari){
            get_jenis_list(page,cari);
        }
    </script>
    <div class="titling"><h1><?= $title ?></h1></div>
    <div class="data-input">
        <fieldset>
            <?= form_open('', 'id = formjenis') ?>
            <tr><td>ID.:</td><td><span class="label"><?= form_hidden('id') ?><span id="id_jenis"></span></span>
            <tr><td>Nama:</td><td><span><?= form_input('nama', '', 'id=nama size=30') ?></span>
            <tr><td>Keterangan:</td><td><span><?= form_textarea('ket', '', 'id=ket size=30') ?></span>
            <tr><td></td><td><?= form_button('simpan', "Simpan", 'id=simpan') ?>
            <?= form_button('reset', 'Reset', 'id=reset') ?>
            <?= form_close() ?>
        </table>
    </div>
    <div id="jenis_list"></div>
</div>