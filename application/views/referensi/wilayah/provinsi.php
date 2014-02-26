<script type="text/javascript">
    var request;
    $(function() {
        //initial
        get_provinsi_list(1);
        //initial
        $('#provinsi').focus();
        $('.save').button({icons: {secondary: 'ui-icon-circle-check'}});
        $('#reset_pro').button({icons: {secondary: 'ui-icon-refresh'}});
        $('#cari_pro').button({icons: {secondary: 'ui-icon-search'}});
        
        $('#konfirmasi_prov').dialog({
            autoOpen: false,
            title :'Konfirmasi',
            height: 200,
            width: 300,
            modal: true,
            resizable : false,
            buttons: [ 
                { text: "Ok", click: function() { 
                        save_prov();
                        $( this ).dialog( "close" ); 
                    } 
                }, 
                { text: "Batal", click: function() { 
                        $( this ).dialog( "close" ); 
                    } 
                } 
            ]
        });
        
        $('#reset_pro').click(function(){
            reset_all_prov();
            get_provinsi_list(1);
        });
               
        $('#formpro').submit(function(){
            form_submit();
            return false;
        });
    
    });
    
    function form_submit(){
            var provinsi = $('#provinsi').val();
            var tipe = $('input[name=id]').val();
            
            if($('#provinsi').val()!=''){
                if(!request) {
                    request = $.ajax({
                        url: '<?= base_url('referensi/manage_provinsi') ?>/cek',
                        data:'provinsi='+provinsi,
                        cache: false,
                        dataType: 'json',
                        success: function(msg){
                            request = null;
                            if (tipe == ''){
                                if (msg.status == false){
                                    $('#text_konfirmasi_prov').html('Nama Provinsi <b>"'+provinsi+'"</b> sudah ada<br/> Apakah anda yakin akan menambahkannya lagi?');            
                                    $('#konfirmasi_prov').dialog("open");
                                } else {
                                    save_prov();
                                }                        
                            }else{
                                save_prov();
                            }
                        }
                    });
                }
            }else{
                custom_message('Peringatan','Nama provinsi tidak boleh kosong !','#provinsi');
            }
        }
    
    function save_prov(){
        var Url = '';           
        var tipe = $('input[name=id]').val();
        if(tipe == ''){
            Url = '<?= base_url('referensi/manage_provinsi') ?>/add/';
        }else{
            Url = '<?= base_url('referensi/manage_provinsi') ?>/edit/';
        }
        
        if(!request) {
            request = $.ajax({
            type : 'POST',
            url: Url+$('.noblock').html(),               
            data: $('#formpro').serialize(),
            cache: false,
            dataType : 'json',
            success: function(data) {
                $('input[name=id]').val(data.id);
                var id = data.id;
                generate_msg('ok',tipe);
                $.ajax({
                    type : 'GET',
                    url: '<?= base_url('referensi/manage_provinsi') ?>/get_data/1',
                    data :'id='+id,
                    cache: false,
                    success: function(data) {
                        $('#pro_list').html(data);
                        
                    }
                });
                request = null;                            
            },
            error : function(){
                generate_msg('fail',tipe);
            }
            });
        }
    }
    
 
    function get_provinsi_list(p){
        $.ajax({
            type : 'GET',
            url: '<?= base_url('referensi/manage_provinsi') ?>/list/'+p,
            data: $('#formpro').serialize(),
            cache: false,
            success: function(data) {
                $('#pro_list').html(data);
            }
        });
    }
    
    function delete_provinsi(id){
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
                            url: '<?= base_url('referensi/manage_provinsi') ?>/delete/'+$('.noblock').html(),
                            data :'id='+id,
                            cache: false,
                            success: function(data) {
                                $('#pro_list').html(data);
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
    
    function edit_provinsi(id, nama,kode){
        $('input[name=id]').val(id);
        $('#provinsi').val(nama);
        $('#kode').val(kode);
    }

    function reset_all_prov(){
        $('#provinsi').val('');
        $('#kode').val('');
    }
    
    
</script>
    
        <?= form_open('', 'id=formpro') ?>
        <table width="100%" class="inputan">
            <?= form_hidden('id') ?>
            <tr><td>Nama Provinsi</td><td><?= form_input('provinsi', null, 'id=provinsi size=30') ?>
            <tr><td>Kode Provinsi</td><td><?= form_input('kode', null, 'id=kode size=10') ?>
            <tr><td></td><td><?= form_button('', 'Simpan', 'class=save onclick=form_submit()') ?>
            <?= form_button('','Cari','id=cari_pro onclick=get_provinsi_list(1)') ?>
            <?= form_button(null, 'Reset', 'id=reset_pro') ?>
        </table>
        <?= form_close() ?>
    
    <div id="konfirmasi_prov" style="padding: 20px;">
        <div id="text_konfirmasi_prov"></div>
    </div>

    <div id="pro_list"></div>
