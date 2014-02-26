<?php $this->load->view('message') ?>
<title><?= $title ?></title>
<div class="titling"><h1><?= $title ?></h1></div>
<div class="kegiatan">
    <script type="text/javascript">
        var request;
        $(function() {
            $('#tabs').tabs();
            $('#nama').focus();
            $('input[type=submit]').each(function(){ $(this).replaceWith('<button type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'">' + $(this).val() + '</button>');});
            $('button[type=submit]').button({icons: {secondary: 'ui-icon-circle-check'}});
            $('#reset').button({icons: {secondary: 'ui-icon-refresh'}});
            $('#cari').button({icons: {secondary: 'ui-icon-search'}});
            
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
                  
           
            //initial
            get_instansi_list(1,'null');
            //initial
            $('.kelurahan').autocomplete("<?= base_url('referensi/get_kelurahan') ?>",
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
                    var str = '<div class=result>'+data.nama+'<br/>Kec: '+data.kecamatan+', Kab: '+data.kabupaten+', Pro: '+data.provinsi+'</div>';
                    return str;
                },
                width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
            }).result(
            function(event,data,formated){
                $(this).attr('value',data.nama);
                $('input[name=id_kelurahan]').val(data.id);
                $('#ket').html(data.kecamatan+', '+data.kabupaten+', '+data.provinsi);
                
            });
            
            $('#reset').click(function(){
                $('#loaddata').empty();
                $('#loaddata').load($.cookie('url'));
            });

            $('#formadd').submit(function(){
                form_submit();
                return false;
            });
        
           
            
                                
        });

        function form_submit(){                          
                var nama = $('#nama').val();
                var tipe = $('input[name=id]').val();
                
                if($('#nama').val()==''){
                    custom_message('Peringatan','Nama instansi tidak boleh kosong !','#nama');
                } else if($('#alamat').val()==''){
                    custom_message('Peringatan','Alamat tidak boleh kosong !','#alamat');
                }else if($('#jenis').val()==''){
                    custom_message('Peringatan','Pilih jenis instansi !','#jenis');
                }else{    
                    if(!request) {
                        request = $.ajax({
                        url: '<?= base_url("referensi/manage_instansi") ?>/cek',
                        data:'instansi='+nama,
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
                    return false;
                }
                return false;
            }

        
        function save(){
            var Url = ''; 
            var id = $('input[name=id]').val();
            var nama = $('#nama').val();
            var alamat = $('#alamat').val();
            var id_kelurahan = $('input[name=id_kelurahan]').val();
            var telp = $('#telp').val();
            var fax = $('#fax').val();
            var email = $('#email').val();
            var website = $('#website').val();
            var jenis = $('#jenis').val();
            var diskon_penjualan = $('#diskon_penjualan').val();
            var tipe = $('input[name=id]').val();
            if(tipe  == ''){
                Url = '<?= base_url('referensi/manage_instansi') ?>/add/';
            }else{
                Url = '<?= base_url('referensi/manage_instansi') ?>/edit/';
            }
            
            if(!request) {
                request = $.ajax({
                    type : 'POST',
                    url: Url+$('.noblock').html(),               
                    data: 'id='+id+'&nama='+nama+'&alamat='+alamat+'&id_kelurahan='+id_kelurahan+'&telp='+telp+'&fax='+fax+'&email='+email+'&website='+website+'&jenis='+jenis+'&disk_penjualan='+diskon_penjualan,
                    cache: false,
                    dataType : 'json',
                    success: function(data) {
                        $('input[name=id]').val(data.id);
                        var id = data.id;
                        generate_msg('ok',tipe);
                        $.ajax({
                            type : 'GET',
                            url: '<?= base_url('referensi/manage_instansi') ?>/get_data/1',
                            data :'id='+id,
                            cache: false,
                            success: function(data) {
                                $('#ins_list').html(data);
                                
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
    
        function get_instansi_list(p,search){
            $.ajax({
                type : 'GET',
                url: '<?= base_url('referensi/manage_instansi') ?>/list/'+p,
                data : $('#formadd').serialize(),
                cache: false,
                success: function(data) {
                    $('#ins_list').html(data);
                }
            });
        }
    
        function delete_instansi(id, param){
        
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
                                url: '<?= base_url('referensi/manage_instansi') ?>/delete/'+$('.noblock').html(),
                                data :'id='+id+'&search='+param,
                                cache: false,
                                success: function(data) {
                                    get_instansi_list($('.noblock').html(), '');
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
    
        function edit_instansi(arr){
            var data = arr.split("#");
        
            $('input[name=id]').val(data[0]);
            $('#nama').val(data[1]);
            $('#alamat').val(data[2]);
            $('.kelurahan').val(data[4]);
            $('input[name=id_kelurahan]').val(data[3]);
            $('#telp').val(data[5]);
            $('#fax').val(data[6]);
            $('#email').val(data[7]);
            $('#website').val(data[8]);
            $('#jenis').val(data[9]);
            $('#diskon_penjualan').val(data[10]);

      
        }
        function paging(page, tab, cari){
            get_instansi_list(page,cari);
        }
        
       
    </script>
    <div id="tabs">
            <ul>
                <li><a href="#tabs-1">Parameter</a></li>
            </ul>
            <div id="tabs-1">
                <?= form_open('', 'id=formadd') ?>
                <?= form_hidden('id') ?>
                <table width="100%" class="inputan">    
                    <tr><td>Nama:</td><td><?= form_input('nama', '', 'id=nama class="input-text"') ?>
                    <tr><td>Alamat:</td><td><?= form_textarea('alamat', '', 'class="standar" rows=2 id=alamat class="minitextarea"') ?>
                    <tr><td>Kelurahan:</td><td><?= form_input('kelurahan', '', 'class=kelurahan class="input-text"') ?>
                    <?= form_hidden('id_kelurahan') ?>
                    <tr><td></td><td><span id="ket" class="label"></span>
                    <tr><td>Telpon:</td><td><?= form_input('telp', '', 'id=telp class="input-text"') ?>
                    <tr><td>Fax:</td><td><?= form_input('fax', '', 'id=fax class="input-text"') ?>
                    <tr><td>Email:</td><td><?= form_input('email', '', 'id=email class="input-text"') ?>
                    <tr><td>Website:</td><td><?= form_input('website', '', 'id=website class="input-text"') ?>
                    <tr><td>Jenis:</td><td><?= form_dropdown('jenis', $jenis, array(), 'id=jenis') ?>
                    <tr><td>Diskon Penjualan (%):</td><td><?= form_input('disk_penjualan', '', 'id=diskon_penjualan size=5 maxlength=3') ?>
                     <tr><td></td><td><?= form_submit('', 'Simpan', 'id=simpan') ?>
                    <?= form_button('','Cari','id=cari onclick=get_instansi_list(1)') ?>
                    <?= form_button(null, 'Reset', 'id=reset') ?>
                </table>
                <?= form_close(); ?>
                <div id="konfirmasi" style="display: none; padding: 20px;">
                    <div id="text_konfirmasi"></div>
                </div>

                <div id="list" class="data-list">
                    <div id="ins_list"></div>
                </div>
            </div>
    </div>
</div>