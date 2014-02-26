<?php $this->load->view('message') ?>
<title><?= $title ?></title>
<div class="kegiatan">
    <script type="text/javascript">
        $(function() {
            get_user_list(1);
            $('#cari').button({icons: {secondary: 'ui-icon-refresh'}}).click(function() {
                $.ajax({
                    type : 'POST',
                    url: '<?= base_url('referensi/manage_user') ?>/search/1',
                    data: $('#form').serialize(),
                    cache: false,
                    success: function(data) {
                        $('#user_list').html(data);
                    }
                });
            });
            $('input[type=submit]').each(function(){ $(this).replaceWith('<button type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'">' + $(this).val() + '</button>');});
            $('button[type=submit]').button({icons: {secondary: 'ui-icon-circle-check'}});
            $('#reset,.resetan').button({icons: {secondary: 'ui-icon-refresh'}});
            $('#showAllUser').click(function(){
                reset_user();
                get_user_list(1);
            });
            
            $('#nama').autocomplete("<?= base_url('inv_autocomplete/load_data_user_system') ?>",
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
                    var kelurahan = '';
                    if (data.kelurahan!=null) { var kelurahan = data.kelurahan; }
                    var str = '<div class=result>'+data.nama+' <br/> '+data.alamat+'</div>';
                    return str;
                },
                width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
            }).result(
            function(event,data,formated){
                $(this).val(data.nama);
                $('input[name=id_penduduk]').val(data.id);
        
            });

            $('#unit').autocomplete("<?= base_url('referensi/get_unit') ?>",
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
                    var str = '<div class=result>'+data.nama+'</div>';
                    return str;
                },
                width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
            }).result(
            function(event,data,formated){
                $(this).val(data.nama);
                $('input[name=id_unit]').val(data.id);
        
            });
            $('#reset').click(function(){
                reset_user();
            });
      
            $('#privform').dialog({
                autoOpen: false,
                height: 500,
                width: 800,
                modal: true,
                resizable : false,
                close : function(){
                    reset_user();
                },
                buttons: [ 
                { text: "Simpan", click: function() { 
                        form_submit(); 
                    } 
                } 
            ]
            });
        
            $('#form').submit(function(){
                var Url = '<?= base_url('referensi/manage_user') ?>/add/';
                var id = $('input[name=id]').val();
                if($('input[name=id_penduduk]').val()==''){
                    custom_message('Peringatan','Nama tidak boleh kosong !','#nama');
                } else if($('#username').val()==''){
                    custom_message('Peringatan','Username tidak boleh kosong !','#username');
                }else if($('input[name=id_unit]').val() == ''){
                    custom_message('Peringatan','Unit tidak boleh kosong !','#unit');
                }else{    
                
                    $.ajax({
                        type : 'POST',
                        url: Url+$('.noblock').html(),               
                        data: $(this).serialize(),
                        cache: false,
                        success: function(data) {
                            $('#user_list').html(data);
                            reset_user();
                            if (id === '') {
                                alert_tambah();
                            } else {
                                alert_edit();
                            }
                            $('#nama').removeAttr('disabled');
                        }
                    });
              
                    return false;
                }
                return false;
            });
        });
    
        function reset_user(){
            $('input[name=id_penduduk]').val('');
            $('input[name=id]').val('');
            $('#nama, #username, #user_group, #status, #unit, input[name=id_unit]').val('');
        }
    
        function get_user_list(p){
            $.ajax({
                type : 'POST',
                url: '<?= base_url('referensi/manage_user') ?>/list/'+p,
                data: $('#form').serialize(),
                cache: false,
                success: function(data) {
                
                    $('#user_list').html(data);
                    reset_user();
                }
            });
        }
    
        function get_privileges_list(){
            $.ajax({
                type : 'GET',
                url: '<?= base_url('referensi/manage_privileges') ?>/list',
                data :'id='+$('input[name=id]').val(),
                cache: false,
                success: function(data) {
                
                    $('#list').html(data);
                    reset_user();
                }
            });
        }
        
    
        function edit_user(id){
            $('input[name=id]').val(id);
            $.ajax({
                type : 'GET',
                url: '<?= base_url('referensi/manage_user') ?>/edit/'+1,
                data :'id='+id,
                cache: false,
                success: function(data) {                
                    $('#privileges').html(data);             
                }
            });
            $('#privform').dialog("option",  "title", "Edit User Privileges");
            $('#privform').dialog("open");
        }
    
        function delete_user(id){
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
                                    url: '<?= base_url('referensi/manage_user') ?>/delete/'+$('.noblock').html(),
                                    data :'id='+id,
                                    cache: false,
                                    success: function(data) {
                                        $('#user_list').html(data);
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
        
        function resetpassword(str) {
            var arr = str.split('#');
            $('input[name=id],input[name=id_penduduk]').val(arr[0]);
            $('#nama').val(arr[2]).attr('disabled','disabled');
            $('#unit').val(arr[4]);
            $('input[name=id_unit]').val(arr[3]);
            $('#username').val(arr[1]).focus();
            $('#status').val(arr[5]);
            $('#user_group').val(arr[6]);
            return false;
        }
        
        
    </script>
        
    <?php $status = array('pilih'=>'Pilih...', 'Staff'=>'Staff', 'Manajer'=>'Manajer', 'Direktur' => 'Direktur');?>
    <?= form_open('', 'id = form') ?>
    <table width="100%" class="inputan">
        <?= form_hidden('id') ?>
        <tr><td width="15%">Nama Penduduk</td><td><?= form_input('nama', null, 'id=nama size=40') ?> <?= form_hidden('id_penduduk') ?></td>  </tr>
        <tr><td>Username</td><td><?= form_input('username', '', 'id=username size=40') ?></td></tr>
        <tr><td>Password</td><td>Sementara password di set standar 1234</td></tr>
        <tr><td>Unit</td><td><?= form_input('unit', '', 'id=unit size=40') ?><?= form_hidden('id_unit') ?></td></tr>
        <tr><td>User Group</td><td><?= form_dropdown('user_group',$user_group, array(),'id=user_group') ?></td></tr>
        <tr><td>Status</td><td><?= form_dropdown('status',$status, array(),'id=status') ?></td></tr>
        <tr><td></td><td>
                <?= form_submit('ubahpassword', 'Simpan', 'class=tombol') ?>
                <?= form_button(null, 'Cari', 'id=cari') ?>
                <?= form_button('', 'Reset', 'class=resetan id=showAllUser') ?>
            </td></tr>
    </table>
    <?= form_close() ?>
    <div id="user_list"></div>
    <div id="privform" style="display: none;position: relative; background: #fff; padding: 10px;">
        <div id="privileges"></div>
    </div>
</div>