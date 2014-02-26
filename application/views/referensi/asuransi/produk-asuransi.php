<?php $this->load->view('message') ?>
<title><?= $title ?></title>
<div class="titling"><h1><?= $title ?></h1></div>
<div class="kegiatan">
    <script type="text/javascript">
        var Url = '';
        var request;
        $(function() {
            //initial
            $('#tabs').tabs();
            get_last_id();
            get_produk_asuransi_list(1);
            $('#suplier').focus();
            
            $('input[type=submit]').each(function(){$(this).replaceWith('<button type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'">' + $(this).val() + '</button>');});
            $('button[type=submit]').button({icons: {secondary: 'ui-icon-circle-check'}});
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
        
            $('#suplier').autocomplete("<?= base_url('referensi/get_relasi_instansi') ?>",
            {
                parse: function(data){
                    var parsed = [];
                    for (var i=0; i < data.length; i++) {
                        parsed[i] = {
                            data: data[i],
                            value: data[i].nama // nama field yang dicari
                        };
                        $('input[name=id_ap]').val('');
                    }
                    return parsed;
            
                },
        
                formatItem: function(data,i,max){
                    var str = '<div class=result>'+data.nama+'<br/>'+data.alamat+'</div>';
                    return str;
                },
                width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
            }).result(
            function(event,data,formated){
                $(this).attr('value',data.nama);
                $('input[name=id_ap]').val(data.id);
            });

            $('#formadd').submit(function(){
                form_submit();
                return false;
            });
            
        });

        function form_submit(){
            
            var nama = $('#nama').val();
            var relasi = $('input[name=id_ap]').val();
            var tipe = $('input[name=id_produk]').val();

            
            if(($('#suplier').val()!='')&($('#nama').val()!='')){
                if(!request) {
                    request = $.ajax({
                    url: '<?= base_url('referensi/produk_asuransi_cek') ?>',
                    data:'relasi='+relasi+"&nama="+nama,
                        cache: false,
                        dataType: 'json',
                        success: function(msg){
                            request = null;
                            if (tipe == ''){
                                if (msg.status == false){
                                    $('#text_konfirmasi').html('Nama Instansi <b>"'+nama+'"</b> sudah ada<br/> Apakah anda yakin akan menambahkannya lagi?');            
                                    $('#konfirmasi').dialog("open");
                                } else {
                                    save();
                                }
                            }else{
                                save();
                            }                      
                           
                        }
                    });
                }
            }else{
                custom_message('Peringatan','Silahkan lengkapi data !','#suplier');
                return false;
            }                
                
            
        } 
        
        function save(){
            var tipe = $('input[name=id_produk]').val();
            if(tipe == ''){
                Url = '<?= base_url("referensi/produk_asuransi_add") ?>/1';
            }else{
                Url = '<?= base_url("referensi/produk_asuransi_edit") ?>/1';
            }
            
            if(!request) {
                request = $.ajax({
                type : 'POST',
                url: Url,               
                data: $('#formadd').serialize(),
                cache: false,
                dataType : 'json',
                success: function(data) {
                    $('input[name=id_produk]').val(data.id);
                    var id = data.id;
                    generate_msg('ok',tipe);
                    $.ajax({
                        type : 'GET',
                        url: '<?= base_url('referensi/produk_asuransi_get_data') ?>',
                        data :'id='+id,
                        cache: false,
                        success: function(data) {
                            $('#asu_list').html(data);
                            
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
    
    
        function get_produk_asuransi_list(p){
            $.ajax({
                type : 'GET',
                url: '<?= base_url('referensi/produk_asuransi_list') ?>/'+p, 
                cache: false,
                data: $('#formadd').serialize(),
                success: function(data) {
                    $('#asu_list').html(data);
                }
            });
        }
    
        function delete_produk_asuransi(id){
            var page = ($('.noblock').html()=='')?'1':$('.noblock').html();
            
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
                                url: '<?= base_url('referensi/produk_asuransi_delete') ?>/'+page,
                                data :'id='+id,
                                cache: false,
                                success: function(data) {
                                    get_produk_asuransi_list(page);
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

        function get_last_id(){
            $.ajax({
                type : 'GET',
                url: '<?= base_url('referensi/get_last_id') ?>/asuransi_produk/id',
                cache: false,
                dataType : 'json',
                success: function(data) {
                    $('#no_produk').html(data.last_id);
                }
            });
        }
    
        function edit_produk_asuransi(id, nama,id_ap,suplier,reim, rp){
            $('input[name=tipe]').val('edit');
            $('input[name=id_produk]').val(id);
            $('input[name=id_ap]').val(id_ap);
            $('#suplier').val(suplier);
            $('#no_produk').html(id);  
            $('#nama').val(nama);    
            $('#reim').val(reim);
            $('#reim_rp').val(numberToCurrency(rp));
        
        }
        function paging(page, tab, cari){
            get_produk_asuransi_list(page);
        }
    
    </script>
    
    <div id="tabs">
        <ul>
            <li><a href="#tabs-1">Form Asuransi</a>
        </ul>
        <div id="tabs-1">
            <?= form_open('', 'id=formadd') ?>
                <table width="100%" class="inputan">
                <tr><td>No.</td><td><span id="no_produk"></span><?= form_hidden('id_produk') ?>
                <tr><td>Nama Perusahaan</td><td><?= form_input('perusahaan', '', 'id=suplier size=40') ?>
                            <?= form_hidden('id_ap') ?>
                <tr><td>Nama Produk</td><td><?= form_input('nama', '', 'id=nama size=30') ?>
                <tr><td>Reimbursement (%)</td><td><?= form_input('reimbursement','', 'id=reim size=5 onkeyup=Angka(this)') ?>
                <tr><td>Reimbursement (Rp.)</td><td><?= form_input('reimbursement_rp','', 'onkeyup=FormNum(this) id=reim_rp') ?>
                <tr><td></td><td><?= form_submit('', 'Simpan', 'id=simpan') ?>
                    <?= form_button('','Cari','id=cari onclick=get_produk_asuransi_list(1)') ?>
                    <?= form_button('', 'Reset', 'id=reset') ?>
                </table>
            <?= form_close() ?>
            <div id="asu_list"></div>
        </div>
    </div>

    <div id="konfirmasi" style="display: none; padding: 20px;">
        <div id="text_konfirmasi"></div>
    </div>
    

    
</div>