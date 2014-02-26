<?php $this->load->view('message') ?>
<title><?= $title ?></title>
<div class="kegiatan">
    <script type="text/javascript">
        $(function() {
            get_group_list(1);            
            $('#simpan').button({icons: {secondary: 'ui-icon-circle-check'}});
            $('#reset,.resetan').button({icons: {secondary: 'ui-icon-refresh'}});
                        
            var wWidth = $(window).width();
            var dWidth = wWidth * 0.8;

            var wHeight= $(window).height();
            var dHeight= wHeight * 1;
            $('#privform').dialog({
                autoOpen: false,
                height: dHeight,
                width: dWidth,
                modal: true,
                resizable : false,
                buttons: {
                    "Simpan": function() { 
                        form_submit(); 
                    } 
                },
                close: function() {
                    location.reload();
                }
            });

            $('#simpan').click(function(){
                $('#form_group').submit();
            });
        
            $('#form_group').submit(function(){
                var Url = '<?= base_url("referensi/manage_group") ?>/post/';
                var id = $('input[name=id]').val();
                if($('#nama_group').val()==''){
                    custom_message('Peringatan','Nama tidak boleh kosong !','#nama_group');
                }else{    
                
                    $.ajax({
                        type : 'POST',
                        url: Url+$('.noblock').html(),               
                        data: $(this).serialize(),
                        cache: false,
                        success: function(data) {
                            $('#group_list').html(data);
                            
                            if (id === '') {
                                alert_tambah();
                                $('#nama_group').val('');
                            } else {
                                alert_edit();
                            }
                        }
                    });
              
                    return false;
                }
                return false;
            });
        });
    
        function reset_group(){
            $('#loaddata').empty();
            $('#loaddata').load($.cookie('url'));
        }
    
        function get_group_list(p){
            $.ajax({
                type : 'POST',
                url: '<?= base_url("referensi/manage_group") ?>/list/'+p,
                data: $('#form_group').serialize(),
                cache: false,
                success: function(data) {
                    $('#group_list').html(data);
                }
            });
        }
    
        function get_privileges_list(){
            $.ajax({
                type : 'GET',
                url: '<?= base_url("referensi/manage_privileges") ?>/list',
                data :'id='+$('input[name=id_group]').val(),
                cache: false,
                success: function(data) {
                
                    $('#list').html(data);
                    reset_group();
                }
            });
        }
        
    
        function edit_privileges(id, nama){
            $('input[name=id]').val(id);
            $.ajax({
                type : 'GET',
                url: '<?= base_url("referensi/manage_group") ?>/edit/'+1,
                data :'id='+id+'&nama='+nama,
                cache: false,
                success: function(data) {                
                    $('#privileges').html(data);             
                }
            });
            $('#privform').dialog("option",  "title", "Edit User Privileges");
            $('#privform').dialog("open");
        }

        function edit_group(id,nama){
            $('input[name=id]').val(id);
            $('#nama_group').val(nama);
        }
    
        function delete_group(id){
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
                                    url: '<?= base_url("referensi/manage_group") ?>/delete/'+$('.noblock').html(),
                                    data :'id='+id,
                                    cache: false,
                                    success: function(data) {
                                        $('#group_list').html(data);
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
        
        
        
        
    </script>
    
            <?= form_open('', 'id=form_group')?>
            <?= form_hidden('id') ?>
            <table width="100%" class="inputan">
                <tr><td width="15%">Nama Group</td><td><?= form_input('nama', '','id=nama_group size=40') ?></td></tr>
                <tr><td></td>
                    <td>
                        <?= form_button('','Simpan','id=simpan') ?>
                        <?= form_button('', 'Reset', 'class=resetan onclick=reset_group()') ?>
                    </td>
                </tr>
            </table>
            <?= form_close() ?>
    <div id="group_list"></div>
    <div id="privform" style="display: none;position: relative; background: #fff; padding: 10px;">
        <div id="privileges"></div>
    </div>
</div>