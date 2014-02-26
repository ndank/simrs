<script type="text/javascript">
    var request;
    $(function()
    {
        get_kelurahan_list(1);
        $('#kelurahan').focus();
        $('.save').button({icons: {secondary: 'ui-icon-circle-check'}});
        $('#reset_kel').button({icons: {secondary: 'ui-icon-refresh'}});
        $('#cari_kel').button({icons: {secondary: 'ui-icon-search'}});
        
        $('#konfirmasi_kel').dialog({
            autoOpen: false,
            title :'Konfirmasi',
            height: 200,
            width: 300,
            modal: true,
            resizable : false,
            close : function(){
                
            },
            open : function(){
                
            },
            buttons: [ 
                { text: "Ok", click: function() { 
                        save_kel();
                        $( this ).dialog( "close" ); 
                    } 
                }, 
                { text: "Batal", click: function() { 
                        $( this ).dialog( "close" ); 
                    } 
                } 
            ]
        });
        
        $('#reset_kel').click(function(){
            reset_all_kel();
            get_kelurahan_list(1);
        });
        
        $('.kecamatan-kel').autocomplete("<?= base_url('referensi/get_kecamatan') ?>",
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
               $('input[name=idkecamatankel]').val("");
                return parsed;
            },
            formatItem: function(data,i,max)
            {
                var str = '<div class=result><b style="text-transform:capitalize">'+data.nama+'</b><br />Kab: '+data.kabupaten+' - Prov: '+data.provinsi+'</div>';
                return str;
            },
            width: 270, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
            dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
        }).result(
        function(event,data,formated)
            {
                $(this).val(data.nama);
                $('input[name=idkecamatankel]').val(data.id);
            }
        );

        $('#formkel').submit(function(){
            form_kel_submit();
            return false;
        });
       
    });


    function form_kel_submit(){
            var kelurahan = $('#kelurahan').val();
            var kec = $('.kecamatan-kel').val();
            var kecid=  $('input[name=idkecamatankel]').val();
            var tipe = $('input[name=id_kel]').val();
            
            if($('#kelurahan').val()==''){
                custom_message('Peringatan','Nama kelurahan tidak boleh kosong !','#kelurahan');
                return false;
            }else if($('input[name=idkecamatankel]').val() == ''){
                custom_message('Peringatan','Data Kecamatan tidak boleh kosong !','.kecamatan-kel');
                return false;
            }else{               
                if(!request) {
                    request = $.ajax({
                        url: '<?= base_url('referensi/manage_kelurahan') ?>/cek',
                        data:'kelurahan='+kelurahan+'&kecid='+kecid,
                        cache: false,
                        dataType: 'json',
                        success: function(msg){
                            request = null;
                            if (tipe == ''){
                                if (!msg.status){
                                    $('#text_konfirmasi_kel').html('Nama Kelurahan <b>"'+kelurahan+'"</b> dengan Kecamatan <b>"'+kec+'"</b> sudah ada<br/> Apakah anda yakin akan menambahkannya lagi?');            
                                    $('#konfirmasi_kel').dialog("open");
                                } else {
                                    save_kel();
                                }                                
                            }else{
                                save_kel();
                            }
                            }
                        });
                }
            }
        }
    
    function save_kel(){
        var Url = '';
        var tipe = $('input[name=id_kel]').val();        
        if(tipe == ''){
            Url = '<?= base_url('referensi/manage_kelurahan') ?>/add/';
        }else{
            Url = '<?= base_url('referensi/manage_kelurahan') ?>/edit/';
        }
        
        if(!request) {
            request = $.ajax({
            type : 'POST',
            url: Url+$('.noblock').html(),               
            data: $('#formkel').serialize(),
            cache: false,
            dataType: 'json',
            success: function(data) {
                $('input[name=id_kel]').val(data.id);
                var id = data.id;
                generate_msg('ok',tipe);
                $.ajax({
                    type : 'GET',
                    url: '<?= base_url('referensi/manage_kelurahan') ?>/get_data/1',
                    data :'id='+id,
                    cache: false,
                    success: function(data) {
                        $('#kel_list').html(data);
                        
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
    
    function reset_all_kel(){
        $('input[name=idkecamatankel]').val('');
        $('input[name=id_kel]').val('');
        $('.kecamatan-kel').val('');
        $('#kelurahan').val('');
        $('#kodekel').val('');
    }
    
    function get_kelurahan_list(p){
        $.ajax({
            type : 'GET',
            url: '<?= base_url('referensi/manage_kelurahan') ?>/list/'+p,
            cache: false,
            data: $('#formkel').serialize(),
            success: function(data) {
                $('#kel_list').html(data);
            }
        });
    }
    
    function delete_kelurahan(id){
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
                            url: '<?= base_url('referensi/manage_kelurahan') ?>/delete/'+$('.noblock').html(),
                            data :'id='+id,
                            cache: false,
                            success: function(data) {
                                $('#kel_list').html(data);
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
    
    function edit_kelurahan(id, nama,kec_id,kec_nama,kode){
        $('input[name=id_kel]').val(id);
        $('#kelurahan').val(nama);
        $('.kecamatan-kel').val(kec_nama);
        $('input[name=idkecamatankel]').val(kec_id);
        $('#kodekel').val(kode);
        $('#form-kel').dialog("option", "title", "Edit Data Kelurahan");
        $('#form-kel').dialog("open");   
        $('#savekel').focus();
        $('#resetkel').attr('disabled','disabled');
    }
    
   

</script>
<div class="data-input">
    <?= form_open('', 'id=formkel') ?>
    <table width="100%" class="inputan">
        <?= form_hidden('id_kel') ?>
        <tr><td>Nama Kecamatan</td><td><?= form_input('kecamatan', null, 'class=kecamatan-kel size=30') ?> <?= form_hidden('idkecamatankel') ?></td></tr>
        <tr><td>Nama Kelurahan</td><td><?= form_input('kelurahan', null, 'id=kelurahan size=30') ?></td></tr>
        <tr><td>Kode Kelurahan</td><td><?= form_input('kodekel', null, 'id=kodekel size=10') ?></td></tr>
        <tr><td></td><td><?= form_button('', 'Simpan', 'class=save onclick=form_kel_submit()') ?>
        <?= form_button('','Cari','id=cari_kel onclick=get_kelurahan_list(1)') ?>
        <?= form_button(null, 'Reset', 'id=reset_kel') ?></td></tr>
    </table>
    <?= form_close() ?>
</div>
<div id="konfirmasi_kel" style="display: none; padding: 20px;">
    <div id="text_konfirmasi_kel"></div>
</div>
<div id="kel_list"></div>


