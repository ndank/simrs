<?php $this->load->view('message') ?>
<title><?= $title ?></title>
<div class="titling"><h1><?= $title ?></h1></div>
<div class="kegiatan">
    <script type="text/javascript">
        var request;
        $(function() {
            $('#tabs').tabs();
            $('input[type=submit]').each(function(){ $(this).replaceWith('<button type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'">' + $(this).val() + '</button>');});
            $('button[type=submit]').button({icons: {secondary: 'ui-icon-circle-check'}});
            $('#reset, #reset_baru').button({icons: {secondary: 'ui-icon-refresh'}});
            $('#peg').button({icons:{secondary:'ui-icon-circle-plus'}});
            $('#cari').button({icons:{secondary:'ui-icon-search'}});
            $('#rl2').button({icons:{secondary:'ui-icon-print'}});
            
            get_pegawai_list(1,'null');
            $('.angka').keyup(function(){
                Angka(this);
            });
            
            $('#rl2').click(function(){
                location.href='<?= base_url('kepegawaian/cetak_rl2') ?>';
            });
            
            $('#form_baru').dialog({
                autoOpen: false,
                height: 450,
                width: 800,
                modal: true,
                resizable : false,
                close : function(){
                    reset_all();
                },
                buttons: [ 
                    { text: "Simpan", click: function() { 
                            save();
                        } 
                    }, 
                    { text: "Reset", click: function() { 
                             reset_all();
                        } 
                    } 
                ]
            });
            
            $('#waktu').datetimepicker({
                changeYear : true,
                changeMonth : true
            });
        
            $("#fromdate").datepicker({
                changeYear : true,
                changeMonth : true 
            });
            $("#todate").datepicker({
                changeYear : true,
                changeMonth : true,
                minDate : $('#fromdate').val()
            });
            
            $('#peg').click(function(){
                get_last_id();
                $('#form_baru').dialog("option",  "title", "Tambah Pegawai");
                $('#form_baru').dialog("open");
                $('#reset_baru').removeAttr('disabled');
               
            });
            
      
            $('#nama').autocomplete("<?= base_url('inv_autocomplete/load_penduduk') ?>",
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
                    var str = '<div class=result>'+data.nama+'<br/>'+data.alamat+'</div>';
                    return str;
                },
                width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
            }).result(
            function(event,data,formated){
                $(this).val(data.nama);
                $('input[name=id_penduduk]').val(data.id);                 
                $('input[name=gender]').val(data.gender);
                $('#gender_val').html((data.gender == 'L')?'Laki-laki':'Perempuan');
            });
            
            
            $('#reset').click(function(){
                $('#loaddata').empty();
                $('#loaddata').load('<?= base_url('kepegawaian/pegawai') ?>');
            });
            $('#reset_baru').click(function(){
                reset_all();
            });
            $('#formbaru').submit(function(){ 
                save();             
                return false;
            });
            
            $('#jurusan').autocomplete("<?= base_url('kepegawaian/get_jurusan') ?>",
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
                    $('input[name=id_jurusan]').val('');
                    return parsed;
                },
                formatItem: function(data,i,max)
                {
                    var str = '<div class=result>'+data.nama+'<br/>'+data.jenis +'</div>';
                    return str;
                },
                width: 270, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
            }).result(
            function(event,data,formated){
                $(this).attr('value',data.nama);
                $('input[name=id_jurusan]').val(data.id);
            });
            
            $('#jurusan_baru').autocomplete("<?= base_url('kepegawaian/get_jurusan') ?>",
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
                    $('input[name=id_jurusan_baru]').val('');
                    return parsed;
                },
                formatItem: function(data,i,max)
                {
                    var str = '<div class=result>'+data.nama+'<br/>'+data.jenis +'</div>';
                    return str;
                },
                width: 270, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
            }).result(
            function(event,data,formated){
                $(this).attr('value',data.nama);
                $('input[name=id_jurusan_baru]').val(data.id);
            });
            
            $('#cari').click(function(){
                
                if (($('#fromdate').val() !='')&($('#todate').val()=='')) {
                    custom_messagge('Peringatan','Range waktu harus lengkap !','#todate');
                } else {                      
                   get_pegawai_list(1);
                }
              
                
            });
            
        });
        
        function save(){
            var Url = '<?= base_url('kepegawaian/manage_pegawai') ?>/post/1';
            
            if($('#waktu').val()==''){
                custom_messagge('Peringatan','Waktu tidak boleh kosong !','#waktu');
                return false;
            }

            if($('input[name=id_penduduk]').val()==''){
                custom_messagge('Peringatan','Nama tidak boleh kosong atau pilih data yang ada !','#nama');
                return false;
            }

            if($('#jenjang_baru').val()==''){
                custom_messagge('Peringatan','Jenjang Pendidikan tidak boleh kosong !','#jenjang_baru');
                return false;
            }

            if($('input[name=id_jurusan_baru]').val()==''){
                custom_messagge('Peringatan','Jurusan kualifikasi pendidikan tidak boleh kosong !','#jurusan_baru');
                return false;
            }

            if(!request) {
                request = $.ajax({
                    type : 'POST',
                    url: Url,               
                    data: $('#formbaru').serialize(),
                    cache: false,
                    success: function(data) {
                        $('#pegawai_list').html(data);                       
                        if($('input[name=id_baru]').val() == ''){
                            alert_tambah();
                            
                        }else{
                            alert_edit();
                        }
                        reset_all();
                        request = null;
                        $('#form_baru').dialog('close');
                    }
                });
            }
            
        }
        
        function reset_all(){
            $('#fromdate, #todate, #jenjang, #jurusan, #nama, #nip, #waktu, #jenjang_baru, #jurusan_baru, #jumlah, #jabatan').val('');
            $('input[type=hidden]').val('');
            $('input[name=id_penduduk]').val('');
            $('#P').removeAttr('checked');
            $('#L').removeAttr('checked');
            $('#PB').removeAttr('checked');
            $('#LB').attr('checked', true);
        }
        
        function get_last_id(){
            $.ajax({
                type : 'GET',
                url: '<?= base_url('kepegawaian/get_last_id') ?>/kepegawaian/id',
                cache: false,
                dataType : 'json',
                success: function(data) {
                    $('#h_id_baru').html(data.last_id);
                   
                }
            });
        }
        
        function get_pegawai_list(p){
            $.ajax({
                type : 'GET',
                url: '<?= base_url('kepegawaian/manage_pegawai') ?>/list/'+p, 
                data : $('#formcari').serialize(),
                cache: false,
                success: function(data) {
                    $('#pegawai_list').html(data);
                }
            });
        }
        
        function delete_pegawai(id){
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
                                url: '<?= base_url('kepegawaian/manage_pegawai') ?>/delete/'+$('.noblock').html(),
                                data :'id='+id,
                                cache: false,
                                success: function(data) {
                                    get_pegawai_list($('.noblock').html());
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
        
        function edit_pegawai(str){
            var data = str.split("#");
            $('input[name=id_baru]').val(data[0]);
            $('#h_id_baru').html(data[0]);
            $('#nip').val(data[1]);
            $('#waktu').val(data[2]);
            $('#nama').val(data[3]);
            
            $('#gender_val').html((data[4] == 'L')?'Laki-laki':'Perempuan' );
            $('input[name=gender]').val(data.gender);
                        
            $('#reset_baru').attr('disabled', 'disabled');

            $('#jenjang_baru').val(data[5]);
            $('input[name=id_jurusan_baru]').val(data[6]);
            $('#jurusan_baru').val(data[7]);
            $('#jabatan').val(data[8]);
            $('#jumlah').val(data[9]);
            $('input[name=id_penduduk]').val(data[10]);
            
        
            $('#form_baru').dialog("option",  "title", "Edit Pegawai");
            $('#form_baru').dialog("open");            
        }
        
        function paging(page, tab, cari){
            get_pegawai_list(page);
        }
    </script>
    
    <div id="tabs">
        <ul>
            <li><a href="#tabs-1">Parameter</a></li>
        </ul>
        <div id="tabs-1">
            <table width="100%" class="inputan">
                <?= form_open('', 'id = formcari') ?>
                <tr><td>Tanggal Masuk:</td><td><?= form_input('fromdate', date('d/m/Y'), 'id = fromdate size=10 style="width: 75px"') ?>
                <span class="label"> s.d </span><?= form_input('todate', date('d/m/Y'), 'id = todate size=10 style="width: 75px"') ?>
                <tr><td>Kualifikasi Pendidikan:</td><td>
                <?= form_dropdown('jenjang', $pendidikan, null, 'id=jenjang class=standar') ?>
                <tr><td></td><td>
                <?= form_input('jurusan', '', 'id=jurusan size=30 class=input-text') ?>
                <?= form_hidden('id_jurusan') ?>
                <tr><td>Jenis Kelamin:</td><td>
                <span class="label"><?= form_radio('gender', 'Pria', false, 'id=L') ?>Laki-laki</span>
                <span class="label"><?= form_radio('gender', 'Wanita', false, 'id=P') ?>Perempuan</span>
                <tr><td></td><td><?= form_button('peg', "Pegawai Baru", 'id=peg') ?>
                <?= form_button('cari', "Cari", 'id=cari') ?>
                <?= form_button('reset', 'Reset', 'id=reset') ?>
                <!--<?= form_button('rl2', 'RL. 2', 'id=rl2') ?>-->
                <?= form_close() ?>
            </table>
            <div id="pegawai_list"></div>
        </div>
    </div>
    

    <div id="form_baru" class="data-input" style="display: none;position: relative; background: #fff; padding: 10px;">
        <?= form_open('', 'id=formbaru') ?>
        <?= form_hidden('tipe') ?>
        <?= form_hidden('id_baru') ?>
        <?= form_hidden('id_jurusan_baru') ?>
        <table width="100%" class="inputan">
            <tr><td>ID.:</td><td><span id="h_id_baru"></span>
            <tr><td>NIP.:</td><td><?= form_input('nip', '', 'id=nip size=30 class=input-text') ?>
            <tr><td>Waktu:</td><td><?= form_input('waktu', date('d/m/Y H:i'), 'id=waktu size=15') ?>
            <tr><td>Nama:</td><td><?= form_input('nama', '', 'id=nama size=30 class=input-text') ?>
            <?= form_hidden('id_penduduk') ?>
            <?= form_hidden('gender') ?>
            <tr><td>Jenis Kelamin</td><td><span id="gender_val" class="label"></span>
        </table>

        <table width="100%" class="inputan">
            <tr><td>Kualifikasi Pendidikan:</td><td>
            <?= form_dropdown('jenjang_baru', $pendidikan, null, 'id=jenjang_baru') ?>
            <tr><td></td><td><?= form_input('jurusan_baru', '', 'id=jurusan_baru size=30 class=input-text') ?>
            <tr><td>Jumlah Kebutuhan:</td><td><?= form_input('jumlah', '', 'id=jumlah class=angka') ?>
        </table>
        <fieldset>
            <tr><td>Jabatan:</td><td><?= form_dropdown('jabatan', $jabatan, null, 'id=jabatan') ?>

            <?= form_close() ?>
        </table>
    </div>

</div>