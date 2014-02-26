<?php $this->load->view('message') ?>
<title><?= $title ?></title>
<div class="kegiatan">
    <script type="text/javascript">
        var Url = '';
        var request;
        $(function() {
            //initial/
            get_kode_rekening_list(1);
            $('#tarif').focus();
            
            $('#simpan').button({icons: {secondary: 'ui-icon-circle-check'}}).click(function(){
                if($('input[name=id_tarif]').val() === ''){
                    custom_message('Peringatan', 'Tarif harus dipilih','#tarif');
                    return false;
                }

                if($('#jenis_layan').val() === ''){
                    custom_message('Peringatan', 'Jenis pelayanan harus dipilih','#jenis_layan');
                    return false;
                }

                save();
            });
            $('#cari').button({icons: {secondary: 'ui-icon-search'}});
            $('#reset').button({icons: {secondary: 'ui-icon-refresh'}});

            $('#reset').click(function(){
                $('#loaddata').empty();
                $('#loaddata').load($.cookie('url'));
            });
            
            
            $('#konfirmasi').dialog({
                autoOpen: false,
                title :'Konfirmasi',
                height: 200,
                width: 300,
                modal: true,
                resizable : false,
                buttons: [ 
                    { text: "Ok", click: function() { 
                            save();
                            $( this ).dialog( "close" ); 
                        } 
                    }, 
                    { text: "Batal", click: function() { 
                            $( this ).dialog( "close" ); 
                        } 
                    } 
                ]
            });
        
            $('#tarif').autocomplete("<?= base_url('inv_autocomplete/get_layanan_jasa') ?>/param/",
                {
                    parse: function(data){
                        var parsed = [];
                        for (var i=0; i < data.length; i++) {
                            parsed[i] = {
                                data: data[i],
                                value: data[i].layanan // nama field yang dicari
                            };
                        }
                        return parsed;
                    },
                    formatItem: function(data,i,max){
                        var profesi = data.profesi;
                        if (data.profesi == null) {
                            var profesi = '';
                        }
                        var jurusan = data.jurusan;
                        if (data.jurusan == null) {
                            var jurusan = '';
                        }
                        var jenis = data.jenis_pelayanan_kunjungan;
                        if (data.jenis_pelayanan_kunjungan == null) {
                            var jenis = '';
                        }
                        var unit = data.unit;
                        if (data.unit == null) {
                            var unit = '';
                        }
                        var bobot = data.bobot;
                        if (data.bobot == null) {
                            var bobot = '';
                        }
                        var kelas = data.kelas;
                        if (data.kelas == null) {
                            var kelas = '';
                        }

                        var barang = data.barang;
                        if(data.barang == null){
                            var barang = '';
                        }
                        var str = '<div class=result>'+data.id_tarif+' ,'+barang+' '+data.layanan+' '+profesi+' '+jurusan+' '+/*jenis*/''+' '+unit+' '+bobot+' '+kelas+'</div>';
                        return str;
                    },
                    max: 200,
                    width: 350, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                    dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
                }).result(function(event,data,formated){

                    var barang = data.barang;
                    if(data.barang == null){
                        var barang = '';
                    }
                    var sp = ' ';
                    var tarif = data.layanan+' '+((data.profesi != null)?data.profesi:'')+((data.jurusan != null)?data.jurusan+sp:'')+((data.jenis_pelayanan_kunjungan != null)?data.jenis_pelayanan_kunjungan+sp:'')+((data.unit != null)?data.unit+sp:'')+((data.bobot != null)?data.bobot+sp:'')+((data.kelas != null)?data.kelas+sp:'');
                    $(this).val(((barang !='')?barang:'')+tarif);
                    $('input[name=id_tarif]').val(data.id_tarif);
                });

            $('.rekening').autocomplete("<?= base_url('inv_autocomplete/get_sub_sub_sub_subrekening') ?>",
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
                    var str = '<div class=result>'+data.id_rekening+', '+data.id_sub_rekening+'.'+data.id_sub_sub_rekening+'.'+data.id_sub_sub_sub_rekening+'.'+data.id_sub_sub_sub_sub_rekening+'<br/>'+data.sub_sub_sub_sub_rekening+'</div>';
                    return str;
                },
                max: 500,
                cacheLength: 0,
                width:350, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
            }).result(
            function(event,data,formated){
                $(this).flushCache();
                var id = $(this).attr('id');

                if(id == 'debet'){
                    $('#debet').val(data.sub_sub_sub_sub_rekening);
                    $('input[name=id_debet]').val(data.id_sub_sub_sub_sub_rekening);
                }else{
                    $('#kredit').val(data.sub_sub_sub_sub_rekening);
                    $('input[name=id_kredit]').val(data.id_sub_sub_sub_sub_rekening);
                }
                
            });

            
        });

        
        function save(){
            var tipe = $('input[name=id_kode_rekening]').val();
            
            Url = '<?= base_url("akuntansi/kode_rekening_update") ?>/1';
            
            if(!request) {
                request = $.ajax({
                type : 'POST',
                url: Url,               
                data: $('#formadd').serialize(),
                cache: false,
                dataType : 'json',
                success: function(data) {
                    $('input[name=id_kode_rekening]').val(data.id);
                    var id = data.id;
                    generate_msg('ok',tipe);
                    get_kode_rekening_list(1);
                    request = null;                            
                },
                error : function(){
                    generate_msg('fail',tipe);
                }
                });
            }
        }
    
    
        function get_kode_rekening_list(p){
            $.ajax({
                type : 'GET',
                url: '<?= base_url("akuntansi/kode_rekening_list") ?>/'+p, 
                cache: false,
                data: $('#formadd').serialize(),
                success: function(data) {
                    $('#asu_list').html(data);
                }
            });
        }
    
        function delete_kode_rekening(id){
            
            var page = isNaN($('.noblock').html())?'1':$('.noblock').html();
            
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
                                url: '<?= base_url("akuntansi/kode_rekening_delete") ?>/'+id,
                                cache: false,
                                success: function(data) {
                                    get_kode_rekening_list(page);
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
    
        function edit_kode_rekening(id){
            $.ajax({
                type : 'GET',
                url: '<?= base_url("akuntansi/get_kode_rekening") ?>/'+id,
                cache: false,
                dataType : 'json',
                success: function(data) {
                    console.log(data);
                    $('input[name=id_kode_rekening]').val(data.id_kode);
                    var sp = ' ';
                    var tarif = data.tarif+' '+((data.profesi != null)?data.profesi:'')+((data.jurusan != null)?data.jurusan+sp:'')+((data.jenis_pelayanan_kunjungan != null)?data.jenis_pelayanan_kunjungan+sp:'')+((data.unit != null)?data.unit+sp:'')+((data.bobot != null)?data.bobot+sp:'')+((data.kelas != null)?data.kelas+sp:'');

                    $('#tarif').val(tarif);
                    $('input[name=id_tarif]').val(data.tarif_id);
                    $('#jenis_layan').val(data.jenis_pelayanan);
                    $('#debet').val(data.debet);
                    $('input[name=id_debet]').val(data.kode_debet);
                    $('#kredit').val(data.kredit);
                    $('input[name=id_kredit]').val(data.kode_kredit);
                }
            });

            $('.msg').html('');
        
        }
        function paging(page, tab, cari){
            get_kode_rekening_list(page);
        }
    
    </script>
    <div class="titling"><h1><?= $title ?></h1></div>
    <div class="data-input">
        <table width="100%" class="inputan">Parameter</legend>
        <div class="msg"></div>
        <?= form_open('', 'id=formadd') ?>
        <?= form_hidden('id_kode_rekening') ?>

        <tr><td>Tarif</td><td><?= form_input('tarif', '', 'id=tarif size=50 class=mousetrap') ?>
                    <?= form_hidden('id_tarif') ?>
        <tr><td></td><td><span class="label">Untuk pencarian, cukup masukkan nama layanan</span>
        <tr><td>Jenis Pelayanan:</td><td><?= form_dropdown('jenis_layan',$jenis_layan,null,'id=jenis_layan class=mousetrap') ?>
        <tr><td>Kode Debet</td><td><?= form_input('debet', '', 'id=debet class="rekening mousetrap" size=50') ?>
                    <?= form_hidden('id_debet') ?>
        <tr><td>Kode Kredit</td><td><?= form_input('kredit', '', 'id=kredit class="rekening mousetrap" size=50') ?>
                    <?= form_hidden('id_kredit') ?>
        <tr><td></td><td><?= form_button('', 'Simpan', 'id=simpan class=save') ?>
            <?= form_button('','Cari','id=cari onclick=get_kode_rekening_list(1) class=search') ?>
            <?= form_button('', 'Reset', 'id=reset class=reset') ?>
        <?= form_close() ?>
        </table>
    </div>

    <div id="konfirmasi" style="display: none; padding: 20px;">
        <div id="text_konfirmasi"></div>
    </div>
    <div id="asu_list"></div>

    
</div>