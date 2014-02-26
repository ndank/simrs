<?php $this->load->view('message') ?>
<title><?= $title ?></title>
<div class="titling"><h1><?= $title ?></h1></div>
<div class="kegiatan">
    <script type="text/javascript">
        var request;
        $(function() {
            $('#tabs').tabs();
            get_last_id();     
            $('input[type=submit]').each(function(){ $(this).replaceWith('<button type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'">' + $(this).val() + '</button>');});
            $('button[type=submit]').button({icons: {secondary: 'ui-icon-circle-check'}});
            $('#cari').button({icons: {secondary: 'ui-icon-search'}});
            $('#reset').button({icons: {secondary: 'ui-icon-refresh'}});
            get_layanan_list(1,'');
            
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
            
            $('#reset').click(function(){
                $('#loaddata').empty();
                $('#loaddata').load('<?= base_url('referensi/layanan') ?>');
            });

            $('#jenis').autocomplete("<?= base_url('pelayanan/load_data_sub_sub_jenis_layanan') ?>",
            {
                parse: function(data){
                    var parsed = [];
                    for (var i=0; i < data.length; i++) {
                        parsed[i] = {
                            data: data[i],
                            value: data[i].subsub_jenis // nama field yang dicari
                        };
                    }
                    $('input[name=id_sub_sub]').val('');
                    return parsed;
                },
                formatItem: function(data,i,max){
                    var str = '<div class=result>'+data.jenis_layanan+'</div>';
                    return str;
                },
                width: 360, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
            }).result( 
            function(event,data,formated){
                $(this).val(data.subsub_jenis+'  '+data.sub_jenis+'  '+data.jenis);
                $('input[name=id_sub_sub]').val(data.id_subsub);
            });

            $('#formlayanan').submit(function(){
                form_submit();
                return false;
            });    
        });

        function form_submit(){
            var Url = '';           
            var nama = $('#nama_layanan').val();
            var tipe = $('input[name=id_layanan]').val();
            
            if($('#nama_layanan').val()==''){
                custom_message('Peringatan','Nama layanan tidak boleh kosong !','#nama_layanan');
            } else{    
                $.ajax({
                    url: '<?= base_url("referensi/manage_layanan") ?>/cek',
                    data:'layanan='+nama,
                    cache: false,
                    dataType: 'json',
                    success: function(msg){
                        if (tipe == ''){
                            if (msg.status == false){
                                $('#text_konfirmasi').html('Nama Layanan <b>"'+nama+'"</b> sudah ada<br/> Apakah anda yakin akan menambahkannya lagi?');            
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
        }

        function save(){
            var tipe = $('input[name=id_layanan]').val();
            
            if(tipe == ''){
                Url = '<?= base_url('referensi/manage_layanan') ?>/add/';
            }else{
                Url = '<?= base_url('referensi/manage_layanan') ?>/edit/';
            }
            if(!request) {
                request = $.ajax({
                    type : 'POST',
                    url: Url+$('.noblock').html(),               
                    data: $('#formlayanan').serialize(),
                    cache: false,
                    dataType: 'json',
                }).done(function(data){
                    $('input[name=id_layanan]').val(data.id);
                    var id = data.id;
                    generate_msg('ok',tipe);
                    $.ajax({
                        type : 'GET',
                        url: '<?= base_url('referensi/manage_layanan') ?>/get_data/1',
                        data :'id='+id,
                        cache: false,
                        success: function(data) {
                            $('#layanan_list').html(data);
                            
                        }
                    });
                    request = null;
                }).fail(function(){
                     generate_msg('fail',tipe);
                });
            }
        }
    
        function get_last_id(){
            $.ajax({
                type : 'GET',
                url: '<?= base_url('referensi/get_last_id') ?>/layanan/id',
                cache: false,
                dataType : 'json',
                success: function(data) {
                    $('#nomor_id').html(data.last_id);
                }
            });
        }
    
        function get_layanan_list(p,search){
            $.ajax({
                type : 'GET',
                url: '<?= base_url('referensi/manage_layanan') ?>/list/'+p,
                data : $('#formlayanan').serialize(),
                cache: false,
                success: function(data) {
                    $('#layanan_list').html(data);
                }
            });
        }
    
        function delete_layanan(id,search){
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
                                url: '<?= base_url('referensi/manage_layanan') ?>/delete/'+$('.noblock').html(),
                                data :'id='+id+'&search='+search,
                                cache: false,
                                success: function(data) {
                                    get_layanan_list($('.noblock').html(),'');
                                    alert_delete();
                                },
                                error: function() {
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
    
        function edit_layanan(arr){
            var data = arr.split("#");
        
            $('input[name=id_layanan]').val(data[0]);
            $('#nomor_id').html(data[0]);
            $('#nama_layanan').val(data[1]);
            $('#icd').val(data[2]);
            $('#jenis').val(data[4]);
            $('input[name=id_sub_sub]').val(data[3]);
        }
    
        function paging(page, tab,search){
            get_layanan_list(page,search);
        }
    </script>
    <div id="tabs">
        <ul>
            <li><a href="#tabs-1">Parameter</a></li>
        </ul>
        <div id="tabs-1">
        <?= form_open('', 'id=formlayanan') ?>
        <table width="100%" class="inputan">
            <?= form_hidden('id_sub_sub') ?>
            <tr><td style="width: 150px;">No.:</td><td><span id="nomor_id" class="label"></span> <?= form_hidden('id_layanan') ?></td></tr>
            <tr><td>Nama</td><td><?= form_input('nama', '', 'id=nama_layanan size=45') ?></td></tr>
            <tr><td>ICD IX CM</td><td><?= form_input('icd', '', 'id=icd') ?></td></tr>
            <tr><td>Jenis layanan</td><td><?= form_input('jenis', '', 'id=jenis size=45') ?></td></tr>
            <tr><td></td><td><?= form_submit('', 'Simpan', 'id=simpan') ?>
            <?= form_button('','Cari','id=cari onclick=get_layanan_list(1)') ?>
            <?= form_button('', 'Reset', 'id=reset') ?></td></tr>
        </table>
        <?= form_close() ?>
        <div id="konfirmasi" style="display: none; padding: 20px;">
            <div id="text_konfirmasi"></div>
        </div>

        <div id="list" class="data-list">
            <div id="layanan_list"></div>
        </div>
        </div>
    </div>

</div>