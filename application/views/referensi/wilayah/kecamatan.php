<script type="text/javascript">
    var request;
    $(function(){
            
        get_kecamatan_list(1);
        $('#kecamatan').focus();
        $('.save').button({icons: {secondary: 'ui-icon-circle-check'}});
        $('#reset_kec').button({icons: {secondary: 'ui-icon-refresh'}});
        $('#cari_kec').button({icons: {secondary: 'ui-icon-search'}});
        
        $('#konfirmasi_kec').dialog({
            autoOpen: false,
            title :'Konfirmasi',
            height: 200,
            width: 300,
            modal: true,
            resizable : false,
            buttons: [ 
                { text: "Ok", click: function() { 
                        save_kec();
                        $( this ).dialog( "close" ); 
                    } 
                }, 
                { text: "Batal", click: function() { 
                        $( this ).dialog( "close" ); 
                    } 
                } 
            ]
        });
  
        $('#reset_kec').click(function() {
            reset_all_kec();
            get_kecamatan_list(1);
        });
        
        $('.kabupaten-kec').autocomplete("<?= base_url('referensi/get_kabupaten') ?>",
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
                $('input[name=idkabupatenkec]').val("");
                return parsed;
            },
            formatItem: function(data,i,max)
            {
                var str = '<div class=result>'+data.nama+' - '+data.provinsi+'</div>';
                return str;
            },
            width: 270, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
            dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
        }).result(
        function(event,data,formated)
            {
                $(this).val(data.nama);
                $('input[name=idkabupatenkec]').val(data.id);
            }
        );

        $('#formkec').submit(function(){
            form_kec_submit();
            return false;
        });
    });
    

    function form_kec_submit(){
            var kecamatan = $('#kecamatan').val();
            var kab = $('.kabupaten-kec').val();
            var kabid=  $('input[name=idkabupatenkec]').val();
            var tipe = $('input[name=id_kec]').val();
            
            if($('#kecamatan').val()==''){
                custom_message('Peringatan','Nama kecamatan tidak boleh kosong !','#kecamatan');
                return false;
            }else if($('input[name=idkabupatenkec]').val() == ''){
                custom_message('Peringatan','Data Kabupaten tidak boleh kosong !','.kabupaten-kec');
                return false;
            }else{  
                if(!request) {
                    request = $.ajax({
                    url: '<?= base_url('referensi/manage_kecamatan') ?>/cek',
                    data:'kecamatan='+kecamatan+'&kabid='+kabid,
                    cache: false,
                    dataType: 'json',
                    success: function(msg){
                        request = null;
                        if (tipe == ''){
                            if (!msg.status){
                                $('#text_konfirmasi_kec').html('Nama Kecamatan <b>"'+kecamatan+'"</b> dengan Kabupaten <b>"'+kab+'"</b> sudah ada<br/> Apakah anda yakin akan menambahkannya lagi?');            
                                $('#konfirmasi_kec').dialog("open");
                            } else {
                                save_kec();
                            }                        
                        }else{
                            save_kec();
                        }
                    }
                });
                }
                
            }
        }

    function save_kec(){
        var Url = '';           
        var tipe = $('input[name=id_kec]').val();
        if(tipe == ''){
            Url = '<?= base_url('referensi/manage_kecamatan') ?>/add/';
        }else{
            Url = '<?= base_url('referensi/manage_kecamatan') ?>/edit/';
        }
        if(!request) {
            request = $.ajax({
                type : 'POST',
                url: Url+$('.noblock').html(),               
                data: $('#formkec').serialize(),
                cache: false,
                dataType: 'json',
                success: function(data) {
                    $('input[name=id_kec]').val(data.id);
                    var id = data.id;
                    generate_msg('ok',tipe);
                    $.ajax({
                        type : 'GET',
                        url: '<?= base_url('referensi/manage_kecamatan') ?>/get_data/1',
                        data :'id='+id,
                        cache: false,
                        success: function(data) {
                            $('#kec_list').html(data);
                            
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
    
    function reset_all_kec(){
        $('input[name=id_kec]').val('');
        $('input[name=idkabupatenkec]').val('');
        $('.kabupaten-kec').val('');
        $('#kecamatan').val('');
        $('#kodekec').val('');
    }
    
    function get_kecamatan_list(p){
        $.ajax({
            type : 'GET',
            url: '<?= base_url('referensi/manage_kecamatan') ?>/list/'+p,
            data: $('#formkec').serialize(),
            cache: false,
            success: function(data) {
                $('#kec_list').html(data);
            }
        });
    }
    
    function delete_kecamatan(id){
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
                            url: '<?= base_url('referensi/manage_kecamatan') ?>/delete/'+$('.noblock').html(),
                            data :'id='+id,
                            cache: false,
                            success: function(data) {
                                $('#kec_list').html(data);
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
    
    function edit_kecamatan(id, nama,kab_id,kab_nama,kode){
        $('input[name=id_kec]').val(id);
        $('#kecamatan').val(nama);
        $('.kabupaten-kec').val(kab_nama);
        $('input[name=idkabupatenkec]').val(kab_id);
        $('#kodekec').val(kode);
    }
    
    
  
</script>

    <?= form_open('', 'id=formkec') ?>
    <table width="100%" class="inputan">
    <?= form_hidden('id_kec') ?>
    <tr><td>Nama Kabupaten</td><td><?= form_input('kabupaten', null, 'class=kabupaten-kec size=30') ?> <?= form_hidden('idkabupatenkec') ?></td></tr>
    <tr><td>Nama Kecamatan</td><td><?= form_input('kecamatan', null, 'id=kecamatan size=30') ?></td></tr>
    <tr><td>Kode Kecamatan</td><td><?= form_input('kodekec', null, 'id=kodekec size=10') ?></td></tr>
    <tr><td></td><td><?= form_button('', 'Simpan', 'class=save onclick=form_kec_submit()') ?>
            <?= form_button('','Cari','id=cari_kec onclick=get_kecamatan_list(1)') ?>
            <?= form_button(null, 'Reset', 'id=reset_kec') ?></td></tr>    
    </table>
    <?= form_close() ?>


<div id="konfirmasi_kec" style="display: none; padding: 20px;">
    <div id="text_konfirmasi_kec"></div>
</div>

<div id="kec_list"></div>


