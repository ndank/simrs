<script type="text/javascript">
    var request;
    $(function() {
        //initial
        get_kabupaten_list(1);
        $('#kabupaten').focus();
        $('.save').button({icons: {secondary: 'ui-icon-circle-check'}});
        $('#reset_kab').button({icons: {secondary: 'ui-icon-refresh'}});
        $('#cari_kab').button({icons: {secondary: 'ui-icon-search'}});
        
        $('#konfirmasi_kab').dialog({
            autoOpen: false,
            title :'Konfirmasi',
            height: 200,
            width: 300,
            modal: true,
            resizable : false,
            buttons: [ 
                { text: "Ok", click: function() { 
                        save_kab();
                        $( this ).dialog( "close" ); 
                    } 
                }, 
                { text: "Batal", click: function() { 
                        $( this ).dialog( "close" ); 
                    } 
                } 
            ]
        });
        
        $('#reset_kab').click(function() {
            reset_all_kab();
            get_kabupaten_list(1);
        });
        
        $('.provinsi-kab').autocomplete("<?= base_url('referensi/get_provinsi') ?>",
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
                $('input[name=idprovinsikab]').val('');
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
        function(event,data,formated)
        {
            $(this).attr('value',data.nama);
            $('input[name=idprovinsikab]').val(data.id);
        }
    );
        $('#formkab').submit(function(){
            form_kab_submit();
            return false;
        });
       
    });

    
    
    function form_kab_submit(){    
        var kabupaten = $('#kabupaten').val();
        var provid= $('input[name=idprovinsikab]').val();
        var prov = $('.provinsi-kab').val();
        var tipe = $('input[name=id_kab]').val();
        
        if($('#kabupaten').val()==''){
            custom_message('Peringatan','Nama kabupaten tidak boleh kosong !','#kabupaten');
            return false;
        }else if($('input[name=idprovinsikab]').val() == ''){
            custom_message('Peringatan','Data Provinsi tidak boleh kosong !','.provinsi-kab');
            return false;
        }else{               
            if(!request) {
                request = $.ajax({
                    url: '<?= base_url("referensi/manage_kabupaten") ?>/cek',
                    data:'kabupaten='+kabupaten+'&provid='+provid,
                    cache: false,
                    dataType: 'json',
                    success: function(msg){
                        request = null;
                        if (tipe == ''){
                            if (!msg.status){
                                $('#text_konfirmasi_kab').html('Nama Kabupaten <b>"'+kabupaten+'"</b> dengan Provinsi <b>"'+prov+'"</b> sudah ada<br/> Apakah anda yakin akan menambahkannya lagi?');            
                                $('#konfirmasi_kab').dialog("open");
                            } else {
                                save_kab();
                            }                        
                        }else{
                            save_kab();
                        }
                    }
                });
            }       
        }
    }
    
    function save_kab(){
        var Url = '';           
        var tipe = $('input[name=id_kab]').val();
        if(tipe == ''){
            Url = '<?= base_url('referensi/manage_kabupaten') ?>/add/';
        }else{
            Url = '<?= base_url('referensi/manage_kabupaten') ?>/edit/';
        }

        if(!request) {
            request = $.ajax({
                type : 'POST',
                url: Url+$('.noblock').html(),               
                data: $('#formkab').serialize(),
                cache: false,
                dataType:'json',
                success: function(data) {
                    $('input[name=id_kab]').val(data.id);
                    var id = data.id;
                    generate_msg('ok',tipe);
                    $.ajax({
                        type : 'GET',
                        url: '<?= base_url('referensi/manage_kabupaten') ?>/get_data/1',
                        data :'id='+id,
                        cache: false,
                        success: function(data) {
                            $('#kab_list').html(data);
                            
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
    
    function reset_all_kab(){
        $('input[name=id_kab]').val('');
        $('#kabupaten').val('');
        $('input[name=idprovinsikab]').val('');
        $('.provinsi-kab').val('');
        $('#kodekab').val('');
    }
    
    function get_kabupaten_list(p){
        $.ajax({
            type : 'GET',
            url: '<?= base_url('referensi/manage_kabupaten') ?>/list/'+p,
            cache: false,
            data: $('#formkab').serialize(),
            success: function(data) {
                $('#kab_list').html(data);
            }
        });
    }
    
    function delete_kabupaten(id){
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
                            url: '<?= base_url('referensi/manage_kabupaten') ?>/delete/'+$('.noblock').html(),
                            data :'id='+id,
                            cache: false,
                            success: function(data) {
                                $('#kab_list').html(data);
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
    
    function edit_kabupaten(id, nama,p_id,p_nama,kode){
        $('input[name=id_kab]').val(id);
        $('#kabupaten').val(nama);
        $('.provinsi-kab').val(p_nama);
        $('input[name=idprovinsikab]').val(p_id);
        $('#kodekab').val(kode);
    }
   
</script>

<div class="data-input">
    <table width="100%" class="inputan">
        <?= form_open('', 'id=formkab') ?>
        <?= form_hidden('id_kab') ?>
        <tr><td>Nama Provinsi</td><td><?= form_input('provinsi', null, 'class=provinsi-kab size=30') ?> <?= form_hidden('idprovinsikab', null) ?></td></tr>
        <tr><td>Nama Kabupaten</td><td><?= form_input('kabupaten', null, 'id=kabupaten size=30') ?></td></tr>
        <tr><td>Kode Kabupaten</td><td><?= form_input('kodekab', null, 'id=kodekab size=10') ?></td></tr>
        <tr><td></td><td><?= form_button('', 'Simpan', 'class=save onclick=form_kab_submit()') ?>
            <?= form_button('','Cari','id=cari_kab onclick=get_kabupaten_list(1)') ?>
            <?= form_button(null, 'Reset', 'id=reset_kab') ?></td></tr>

        <?= form_close() ?>
    </table>
</div>

<div id="konfirmasi_kab" style="display: none; padding: 20px;">
    <div id="text_konfirmasi_kab"></div>
</div>
<div id="kab_list"></div>

