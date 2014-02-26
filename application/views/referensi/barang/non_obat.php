<script type="text/javascript">
var request;
$(function(){
    $('#nama_brg').focus();
    $('#simpan_non').button({icons: {secondary: 'ui-icon-circle-check'}});
    $('#cari').button({icons: {secondary: 'ui-icon-search'}});
    $('#reset').button({icons: {secondary: 'ui-icon-refresh'}});
    get_nonobat_list(1,'null');
    

    $('#formnon').submit(function(){
        form_barang_submit();
        return false;
    });
           
    $('#konfirmasi_brg').dialog({
        autoOpen: false,
        title :'Konfirmasi',
        height: 200,
        width: 300,
        modal: true,
        resizable : false,
        buttons: [ 
        { text: "Ok", click: function() { 
            save_barang_non();
            $( this ).dialog( "close" ); 
        } 
        }, 
        { text: "Batal", click: function() { 
            $( this ).dialog( "close" ); 
        } 
    } 
    ]
    });


    $('#reset').click(function(){
        reset_barang_all();
        get_nonobat_list(1,'null');
    });


    $('.pabrik').autocomplete("<?= base_url('inv_autocomplete/load_data_pabrik') ?>",
    {
    parse: function(data){
        var parsed = [];
        for (var i=0; i < data.length; i++) {
            parsed[i] = {
                data: data[i],
                    value: data[i].nama // nama field yang dicari
                };
            }
            $('input[name=id_pabrik]').val('');
            $('input[name=id_pabriks]').val('');
            return parsed;
        },
        formatItem: function(data,i,max){
            var str = '<div class=result>'+data.nama+'</div>';
            return str;
        },
        width: 390, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
        dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
    }).result(
    function(event,data,formated){
        $(this).attr('value',data.nama);
        $('input[name=id_pabrik]').val(data.id);
        $('input[name=id_pabriks]').val(data.id);
    });
        
    $('#kategori').autocomplete("<?= base_url('inv_autocomplete/load_data_kategori_barang') ?>",
    {
    parse: function(data){
        var parsed = [];
        for (var i=0; i < data.length; i++) {
            parsed[i] = {
                data: data[i],
                    value: data[i].nama // nama field yang dicari
                };
            }
            $('input[name=id_pabrik]').val('');
            $('input[name=id_pabriks]').val('');
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
        $('input[name=kategori]').val(data.id);
    });
        
});

function form_barang_cari_submit(){
    var Url = '<?= base_url('referensi/manage_barang_non') ?>/search/';         
    
    if($('#nama_cari').val() === ''){
        custom_message('Peringatan','Nama barang tidak boleh kosong !','#nama_cari');
    }else{    
        if(!request) {
            request =  $.ajax({
                type : 'POST',
                url: Url+$('.noblock').html(),               
                data: $('#form_carinon').serialize(),
                cache: false,
                success: function(data) {
                    $('#non_list').html(data);                           
                    $('#carinonobat').dialog("close");
                    reset_barang_all(); 
                    request = null;                            
                }
            });
        }
    }
}

function form_barang_submit(){
    var nama_brg = $('#nama_brg').val();
    var tipe = $('input[name=id_barang]').val();

    if(nama_brg ===''){
        custom_message('Peringatan','Nama barang tidak boleh kosong !','#nama_brg');
    } else if($('#kategori').val()===''){
        custom_message('Peringatan','Kategori harus dipilih !','#kategori');
    }else{  
        $.ajax({
            url: '<?= base_url('referensi/manage_barang_non') ?>/cek',
            data:'nama='+nama_brg,
            cache: false,
            dataType: 'json',
            success: function(msg_non){
                if (tipe == ''){
                    if (!msg_non.status){
                        $('#text_konfirmasi_brg').html('Nama barang <b>"'+nama_brg+'"</b> sudah ada<br/> Apakah anda yakin akan menambahkannya lagi?');            
                        $('#konfirmasi_brg').dialog("open");
                    } else {
                        save_barang_non();
                    }
                }else{
                    save_barang_non();
                }
            }
        }); 

    }
}

function save_barang_non(){
    var Url = '';       
    var tipe = $('input[name=id_barang]').val();
    if( tipe === ''){
        Url = '<?= base_url('referensi/manage_barang_non') ?>/add/';
    }else{
        Url = '<?= base_url('referensi/manage_barang_non') ?>/edit/';
    }

    if(!request) {
        request =  $.ajax({
            type : 'POST',
            url: Url+$('.noblock').html(),               
            data: $('#formnon').serialize(),
            dataType : 'json',
            cache: false,
            success: function(data) {
                $('input[name=id_barang]').val(data.id);
                var id = data.id;
                generate_msg('ok',tipe);
                if( tipe === ''){
                    $.ajax({
                        type : 'GET',
                        url: '<?= base_url('referensi/manage_barang_non') ?>/get_data/1',
                        data :'id='+id,
                        cache: false,
                        success: function(data) {
                            $('#non_list').html(data);

                        }
                    });
                } else {
                    $.ajax({
                        type : 'GET',
                        url: '<?= base_url('referensi/manage_barang_non') ?>/list/'+$('.noblock').html(),
                        cache: false,
                        success: function(data) {
                            $('#non_list').html(data);
                        }
                    });
                }
                request = null;                            
            },
            error : function(){
                generate_msg('fail',tipe);
            }
        });
    }          
}


function reset_barang_all(){
    $('#nama_brg').val('');
    $('#nama_cari').val('');
    $('#kategori').val('');
    $('input[name=id_pabrik],input[name=id_pabrik], input[name=kategori], input[name=id_barang]').val('');
    $('#id_barang').val('');
    $('.pabrik').val('');
}

function get_nonobat_list(p,search){
    $.ajax({
        type : 'GET',
        url: '<?= base_url('referensi/manage_barang_non') ?>/list/'+p,
        data : $('#formnon').serialize(),
        cache: false,
        success: function(data) {
            $('#non_list').html(data);
        }
    });
}

function delete_non(id, param){
    
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
                        url: '<?= base_url('referensi/manage_barang_non') ?>/delete/'+$('.paging .noblock').html(),
                        data :'id='+id+'&search='+param,
                        cache: false,
                        success: function(data) {
                            get_nonobat_list($('.noblock').html(),'');
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

function edit_non(arr){
    var data = arr.split("#");
    $('input[name=id_barang]').val(data[0]);
    $('#nama_brg').val(data[1]);
    $('input[name=kategori]').val(data[2]);
    $('#kategori').val(data[5]);
    $('.pabrik').val(data[4]);
    $('input[name=id_pabrik]').val(data[3]);
}
</script>


<div class="data-input">
    <table width="100%" class="inputan">Parameter</legend>
        <?= form_open('', 'id=formnon') ?>
        <?= form_hidden('id_barang', '') ?>
        <tr><td>Nama</td><td><?= form_input('nama', '', 'id=nama_brg class=nama size=40') ?> </td>
        <tr><td>Kategori</td><td><?= form_input('nama_kategori', null, 'id=kategori size=40') ?>
        <?= form_hidden('kategori') ?></td>
        <tr><td>Pabrik</td><td><?= form_input('pabrik', '', 'class=pabrik size=40') ?>
        <?= form_hidden('id_pabrik') ?>
        <tr><td></td><td><?= form_button('', 'Simpan', 'id=simpan_non onclick=form_barang_submit()') ?>
        <?= form_button('','Cari','id=cari onclick=get_nonobat_list(1)') ?>
        <?= form_button(null, 'Reset', 'id=reset') ?>

        <?= form_close() ?>
    </table>
</div>

<div id="konfirmasi_brg" style="padding: 20px;">
    <div id="text_konfirmasi_brg"></div>
</div>


<div id="non_list" style="padding: 0;">
    
</div>


