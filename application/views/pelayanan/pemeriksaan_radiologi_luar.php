<?php $this->load->view('message') ?>
<title><?= $title ?></title>
<div class="kegiatan">
    <script type="text/javascript">
    function form_edit(id_pl, id_pk) {
        var id_pelayanan = id_pk;
        var str = '<div id="form_hasil_rad" class=data-input>'+
                    '<form id=save_hasil_rad>'+
                    '<input type=hidden name=id_pemeriksaan_lab value="'+id_pl+'" />'+
                    '<table width="100%" style="background: #f4f4f4; border: 1px solid #ccc; margin-top: 7px;"><tr valign=top><td width="50%">'+
                            '<table width=100% style="line-height: 22px; border-spacing: 0;">'+
                                '<tr><td width=40%>No.:</td><td id=arv_no_pemeriksaan></td></tr>'+
                                '<tr><td>Waktu Order:</td><td id=arv_waktu_order></td></tr>'+
                                '<tr><td>Waktu Hasil:</td><td><?= form_input("waktu_hasil", NULL, "id=arv_waktu_hasil size=15") ?></td></tr>'+
                                '<tr><td>Nama Radiografer.:</td><td><input type="text" name="radiografer" value="" id=arv_radiografer /><input type="hidden" name="id_radiografer" id="id_radiografer" /></td></tr>'+
                                '<tr><td>Nama Layanan:</td><td id=arv_layanan></td></tr>'+
                            '</table>'+
                        '</td><td width=50%>'+
                            '<table width=100% style="line-height: 22px; border-spacing: 0;">'+
                                '<tr><td width=40%>kv:</td><td><?= form_input("kv", NULL, "id=arv_kv size=10 autofocus ") ?></td></tr>'+
                                '<tr><td>ma:</td><td><?= form_input("ma", NULL, "id=arv_ma size=10") ?></td></tr>'+
                                '<tr><td>s:</td><td><?= form_input("s", NULL, "id=arv_s size=10") ?></td></tr>'+
                                '<tr><td>p:</td><td><?= form_input("p", NULL, "id=arv_p size=10") ?></td></tr>'+
                                '<tr><td>fr:</td><td><?= form_input("fr", NULL, "id=arv_fr size=10") ?></td></tr>'+
                            '</table>'+
                        '</td></tr>'+
                    '</table>'+
                    '</form>'+
                  '</div>';


            $('#dialog_edit_rad').html('').append(str);
            $('#form_hasil_rad').dialog({
                autoOpen: true,
                modal: true,
                width: 700,
                height: 250,
                title: 'Entry Hasil Pemeriksaan Radiologi',
                buttons: {
                    "Simpan": function() {
                        $('#save_hasil_rad').submit();
                        $(this).dialog().remove();
                    },
                    "Cancel": function() {
                        $(this).dialog().remove();
                    }
                }, open: function() {
                    $('#arv_kv').focus();
                    $.ajax({
                        url: '<?= base_url("pelayanan/radiologi_luar_load_data_pemeriksaan") ?>/'+id_pl,
                        cache: false,
                        dataType: 'json',
                        success: function(data) {
                            $('#arv_kv').val(data.kv);
                            $('#arv_ma').val(data.ma);
                            $('#arv_s').val(data.s);
                            $('#arv_p').val(data.p);
                            $('#arv_fr').val(data.fr);

                            $('#arv_no_pemeriksaan').html(data.id);
                            $('#arv_waktu_order').html((data.waktu_order != null)?datetimefmysql(data.waktu_order):'');
                            $('#arv_waktu_hasil').val((data.waktu_hasil != null)?datetimefmysql(data.waktu_hasil):'');
                            $('#arv_radiografer').val(data.radiografer);
                            $('input[name=id_radiografer]').val(data.id_kepegawaian_radiografer);
                            $('#arv_layanan').html(data.layanan);
                        }
                    });
                }, close: function() {
                    $(this).dialog().remove();
                }
            });

            $('#arv_waktu_hasil').datetimepicker({
                changeYear : true,
                changeMonth : true
            });

            $('#arv_radiografer').autocomplete("<?= base_url('inv_autocomplete/load_data_pegawai_profesi') ?>",
            {
                extraParams :{ 
                    profesi : function(){
                        return 'Radiologi';
                    }
                },
                parse: function(data){
                    var parsed = [];
                    for (var i=0; i < data.length; i++) {
                        parsed[i] = {
                            data: data[i],
                            value: data[i].nama // nama field yang dicari
                        };
                    }
                    $('input[name=id_analis_lab]').val('');
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
                $('input[name=id_radiografer]').val(data.id);
            });

            $('#save_hasil_rad').submit(function() {
               
                $.ajax({
                    url: '<?= base_url("laboratorium/edit_hasil_rad") ?>/'+id_pl,
                    data: $('#save_hasil_rad').serialize(),
                    dataType: 'json',
                    type: 'POST',
                    success: function(data) {
                        if (data.status === true) {
                            custom_message('Informasi', 'Data berhasil di simpan');
                            $('#form_hasil_rad').dialog().remove();
                            get_rad_list(id_pelayanan);
                        }
                    }
                });
                return false;
            });
    }

       
        $(function(){
            $(document).unbind().on("keydown", function (e) {
                if (e.keyCode === 119) {
                    e.preventDefault();
                    $('#bt_rad').click();
                }
            });
             $('#waktu_order_rad, #waktu_hasil_rad').datetimepicker({
                changeYear : true,
                changeMonth : true
            });

             $('.datetimehasil').datetimepicker({
                changeYear : true,
                changeMonth : true
            });

            $('#formnew').submit(function(){
                var url = $(this).attr('action');

                var Dnama;
                    var Dkelamin = $("#kelamin option:selected").val();
                    var Dusia = $("#usia option:selected").val();
                    var Dtgl_lahir;
                    var Dtelp = $("#telp").val();
                    var Dalamat = $("#alamat").val();
                    var Dkelurahan = $("input[name=id_kelurahan]").val();
                    var id_penduduk = $('input[name=id_penduduk]').val();
                    var id_pelayanan = $('input[name=id_pelayanan_kunjungan]').val();

                    if($('#nama').val()!=''){
                        Dnama = $('#nama').val();
                    }else{
                        Dnama = $('#nama_pdd').val();
                    }

                    if (Dnama == '') {
                        custom_message('Peringatan','Nama pasien tidak boleh kosong !','#nama');
                        return false;
                    }

                    if (Dalamat == '') {
                        custom_message('Peringatan','Alamat jalan tidak boleh kosong !','#alamat');
                        return false;
                    }
                    
                    if (Dusia == 'umur'){
                        Dtgl_lahir = birthByAge($("#umur").val());
                        
                    }else{
                    
                        if($('#tgl_lahir').val() == '00/00/0000'){
                            custom_message('Peringatan','Tanggal lahir tidak valid !','#tgl_lahir');
                            return false;
                        }else{
                            Dtgl_lahir = $("#tgl_lahir").val()
                        }
                    }

                var param = "&id_pelayanan="+id_pelayanan+"&id_penduduk="+id_penduduk+"&nama="+Dnama+"&kelamin="+Dkelamin+"&alamat="+Dalamat+"&tgl_lahir="+Dtgl_lahir+"&id_kelurahan="+Dkelurahan;
                $.ajax({
                    type : 'POST',
                    url: url,
                    data: $(this).serialize()+param,
                    cache: false,
                    dataType: 'json',
                    success: function(data) {
                        if (data.status == true) {
                            $('input[name=id_pelayanan_kunjungan]').val(data.id_pelayanan);
                            get_rad_list(data.id_pelayanan);
                            alert_tambah();
                        }else{
                            alert_tambah_failed();
                        }
                    },
                    error: function(){
                        alert_tambah_failed();
                    }
                });
                return false;
            });

            $('#bt_rad').button({icons: {secondary: 'ui-icon-circle-plus'}}).click(function() {
                    var id_dokter   = $('#dokter_rad').val();
                    var id_radio   = $('input[name=id_radio_rad]').val();
                    var id_layanan  = $('input[name=id_layanan_rad]').val();
                    var waktu_order = $('#waktu_order_rad').val();

                    if (id_dokter === '') {
                        custom_message('Alert !', 'Nama dokter tidak boleh kosong !', '#dokter_rad'); return false;
                    }

                    if (waktu_order_rad === '') {
                        custom_message('Alert !', 'Waktu order tidak boleh kosong !', '#waktu_order_rad'); return false;
                    }

                    if (id_layanan === '') {
                        custom_message('Alert !', 'Nama layanan tidak boleh kosong', '#layanan_rad'); return false;
                    }
                    add_new_rad();
                });


            $('.angka').keyup(function(){Angka(this);});  
            $('#nama').focus(); 
       
            $('#tgl_lahir').datepicker({
                changeYear : true,
                changeMonth : true,
                maxDate : +0
            });
            $('#clear, #reset_cari').button({icons: {secondary: 'ui-icon-refresh'}});
            $('button[id=simpanutama],button[id=cari_pdd]').button({icons: {secondary: 'ui-icon-search'}});
            $('#reset, #reset_cari').button({icons: {secondary: 'ui-icon-refresh'}});
            $('input[type=submit]').each(function(){ $(this).replaceWith('<button type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'">' + $(this).val() + '</button>');});
            $('button[type=submit], #simpanpdd').button({icons: {secondary: 'ui-icon-circle-check'}});

            $('#reset').click(function(){
                $('#loaddata').empty();
                $('#loaddata').load($.cookie('url'));
            
            });

            $('#umur').hide();  
            $('#usia').change(function() {
                if ($('#usia').val() == 'umur') {
                    $('#umur').show();
                    $('#tgl_lahir').hide();
                } else {
                    $('#umur').hide();
                    $('#tgl_lahir').show();
                }
            });

            $('#kelurahan').autocomplete("<?= base_url('demografi/get_kelurahan') ?>",
            {
                parse: function(data){
                    var parsed = [];
                    for (var i=0; i < data.length; i++) {
                        parsed[i] = {
                            data: data[i],
                            value: data[i].nama // nama field yang dicari
                        };
                    }
                    $("input[name=id_kelurahan]").val('');
                    $('#addr').html('');
                    return parsed;
                },
                formatItem: function(data,i,max){
                    var str = '<div class=result>'+data.nama+'<br/>Kec: '+data.kecamatan+', Kab: '+data.kabupaten+', Prov: '+data.provinsi+'</div>';
                    return str;
                },
                width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
            }).result(
            function(event,data,formated){
                $(this).val(data.nama);
                $("input[name=id_kelurahan]").val(data.id);
                $('#addr').html("Kec: "+data.kecamatan+", Kab: "+data.kabupaten+", Prov: "+data.provinsi);
            });

            $('#layanan_rad').autocomplete("<?= base_url('inv_autocomplete/layanan_jasa_load_data_radiologi') ?>",
            {
                parse: function(data){
                    var parsed = [];
                    for (var i=0; i < data.length; i++) {
                        parsed[i] = {
                            data: data[i],
                            value: data[i].nama // nama field yang dicari
                        };
                    }
                    //$('#tindakan_tindak'+i).val('');
                    return parsed;
                },
                formatItem: function(data,i,max){
                    var str = '<div class=result>'+data.nama+'</div>';
                    return str;
                },
                width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                dataType: 'json', // tipe data yang diterima oleh library ini disetup sebagai JSON
                cacheLength: 0
            }).result(
            function(event,data,formated){
                $(this).val(data.nama);
                $('input[name=id_layanan_rad]').val(data.id);
            });

           
            $('#radio_rad').autocomplete("<?= base_url('inv_autocomplete/load_data_pegawai_profesi') ?>",
            {
                extraParams :{ 
                    profesi : function(){
                        return 'Radiologi';
                    }
                },
                parse: function(data){
                    var parsed = [];
                    for (var i=0; i < data.length; i++) {
                        parsed[i] = {
                            data: data[i],
                            value: data[i].nama // nama field yang dicari
                        };
                    }
                    $('input[name=id_analis_lab]').val('');
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
                $('input[name=id_radio_rad]').val(data.id);
            });
        
        });
        

        function removeMe(el) {
            var parent = el.parentNode.parentNode;
            parent.parentNode.removeChild(parent);
        }

        function add_new_rad(){
            var nama_dokter = $('#dokter_rad').val();
            var nama_radio = $('#radio_rad').val();
            var id_radio   = $('input[name=id_radio_rad]').val();
            var nama_layanan  = $('#layanan_rad').val();
            var id_layanan  = $('input[name=id_layanan_rad]').val();
            var wkt_order = $('#waktu_order_rad').val();
            var wkt_hasil = $('#waktu_hasil_rad').val();
            var kv_rad = $('#kv_rad').val(); 
            var ma_rad = $('#ma_rad').val(); 
            var s_rad = $('#s_rad').val();
            var p_rad = $('#p_rad').val(); 
            var fr_rad = $('#fr_rad').val(); 
            
            var str = '<tr class=add_rads>'+
                        '<td>'+nama_dokter+' <input type=hidden name="dokter_radio[]" value="'+nama_dokter+'" /></td>'+
                        '<td>'+nama_radio+' <input type=hidden name="id_radio_radio[]" value="'+id_radio+'" /></td>'+
                        '<td>'+nama_layanan+' <input type=hidden name="id_layanan_radio[]" value="'+id_layanan+'" /></td>'+
                        '<td>'+wkt_order+' <input type="hidden" name="waktu_order_radio[]" value="'+wkt_order+'" /></td>'+
                        '<td>'+wkt_hasil+' <input type="hidden" name="waktu_hasil_radio[]" value="'+wkt_hasil+'" />'+
                        '<input type="hidden" name="kv_radio[]" value="'+kv_rad+'" />'+
                        '<input type="hidden" name="ma_radio[]" value="'+ma_rad+'" />'+
                        '<input type="hidden" name="s_radio[]" value="'+s_rad+'" />'+
                        '<input type="hidden" name="p_radio[]" value="'+p_rad+'" />'+
                        '<input type="hidden" name="fr_radio[]" value="'+fr_rad+'" /></td>'+
                        '<td></td>'+
                        '<td align=center class=aksi><a class="delete" onClick="removeMe(this)"></a></td>'+
                      '</tr>';
            if($('.empty_rad').length > 0){
                $('#add_data_rad tbody').html('');
            }
            $('#add_data_rad tbody').append(str);
            $('.input_rad, input[name=id_radio_rad], input[name=id_layanan_rad]').val('');
            $('#layanan_rad').focus();
        }

        function get_rad_list(id_pk){
            $('#add_data_rad tbody').html('');
             var str_empty = '<tr class="empty_rad"><td></td><td></td><td></td><td></td><td></td><td></td></tr>';
             
            $.ajax({
                type : 'GET',
                url: '<?= base_url('laboratorium/pemeriksaan_radiologi_list') ?>/'+id_pk, 
                cache: false,
                dataType: 'json',
                success: function(data) {
                    $('#add_data_rad').hide();
                    var str ='';
                    var ket = '';
                    var count = 0;
                                    
                    $.each(data, function(i, v){
                        count += 1;
                        append_rad_table(v);
                    });

                    $('#add_data_rad').fadeIn('slow');
                    if(count == 0){
                        $('#add_data_rad tbody').append(str_empty);
                        $('#add_data_rad tbody').append(str_empty);
                    }

                }
            });
        }

        function insert_empty(){
            var str_empty = '<tr class="empty_rad"><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td></tr>';
            $('#add_data_rad tbody').append(str_empty);
            $('#add_data_rad tbody').append(str_empty);
        }


        function append_rad_table(v){
            var waktu_hasil = ((v.waktu_hasil !== null)?datetimefmysql(v.waktu_hasil):'');

            var str = '<tr class=add_rads>'+
                '<td>'+((v.dokter_pemesan != null)?v.dokter_pemesan:'-')+'</td>'+
                '<td>'+((v.radiografer != null)?v.radiografer:'-')+'</td>'+
                '<td>'+((v.layanan != null)?v.layanan:'-')+'</td>'+
                '<td>'+datetimefmysql(v.waktu_order)+'</td>'+
                '<td>'+waktu_hasil+'</td>'+
                '<td align="center"><span class="link_button" onclick="form_edit('+v.id+','+v.id_pelayanan_kunjungan+')" >Edit</span></td>'+
                '<td class=aksi><a class="delete" onClick="delete_rad('+v.id+', this)"></a></td></tr>';      
            $('#add_data_rad tbody').append(str);

            $('.datetimehasil').datetimepicker({
                changeYear : true,
                changeMonth : true
            });
        }

        function edit_hasil_rad(id){
            $('input[name=id_pemeriksaan_radiologi]').val(id);

            $.ajax({
                type : 'GET',
                url: '<?= base_url("laboratorium/get_hasil_radiologi") ?>/'+id, 
                cache: false,
                dataType: 'json',
                success: function(data) {
                    $('#kv_rad_edit').val(data.kv);
                    $('#ma_rad_edit').val(data.ma);
                    $('#s_rad_edit').val(data.s);
                    $('#p_rad_edit').val(data.p);
                    $('#fr_rad_edit').val(data.fr);
                }
            });
            
            $('#form_rad_edit').dialog('open');
        }

    function edit_waktu_hasil_rad(id, hasil){
        $.ajax({
            type : 'GET',
            url: '<?= base_url('laboratorium/edit_waktu_hasil_rad') ?>/'+id, 
            cache: false,
            data : 'hasil='+hasil,
            dataType: 'json',
            success: function(data) {
                
            }
        });
    }

        
    
        function birthByAge(umur){
            var today = new Date();
            var birth = today.getDate()+"/"+(today.getMonth()+1)+"/"+(today.getYear()+1900-umur);
    
            return birth;
        }

        function delete_rad(id_rad, obj){
            $('<div></div>')
              .html("Anda yakin akan menghapus data ini ?")
              .dialog({
                 title : "Hapus Data",
                 modal: true,
                 buttons: [ 
                    { 
                        text: "Ok", 
                        click: function() { 
                           eliminate(obj);
                            $.ajax({
                                url: '<?= base_url("pelayanan/delete_pemeriksaan_radiologi") ?>/'+id_rad,
                                cache: false,
                                success: function(data) {
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

        function eliminate(el) {
            var parent = el.parentNode.parentNode;
            parent.parentNode.removeChild(parent);      
        }

    </script>
    <div class="titling"><h1><?= $title ?></h1></div>
    <div class="data-input">
        <table width="100%" class="inputan">Parameter</legend>
            <?= form_hidden('id_pelayanan_kunjungan', isset($customer)?$customer->id:NULL) ?>
            <tr><td>Nama Customer:</td><td>
                <?= form_input('nama', isset($customer)?$customer->pasien:NULL, 'id =nama size=30 class=input-text') ?>
                <div class="search_pdd" id="bt_cari" title="Klik untuk mencari data di database kependudukan"></div>
                <?= form_hidden('id_penduduk') ?>
                <?= form_hidden('alamat')?>
            <tr><td>Alamat Jalan:</td><td><?= form_textarea('alamat',isset($customer)?$customer->alamat:NULL,'id=alamat class=standar')?>
            <tr><td>Kelurahan:</td><td><?= form_input('kelurahan', isset($customer)?$customer->kelurahan:NULL, 'id=kelurahan size=30 class=input-text') ?> 
            <?= form_hidden('id_kelurahan') ?>    
            <tr><td></td><td>
            <span class="label" id="addr">
                <?php 
                    if(isset($customer)){
                        echo "Kec: ".$customer->kecamatan.", Kab: ".$customer->kabupaten.", Prov: ".$customer->provinsi;
                    }
                ?>
            </span> 
            <tr><td>Jenis Kelamin:</td><td><?= form_dropdown('kelamin', $kelamin, isset($customer)?$customer->gender:NULL, 'id = kelamin class=standar') ?>        
            <tr><td><?= form_dropdown('usia', $tgl_lahir, '', 'id=usia style=width:120px;') ?></td><td>
            <span class="label"><?= form_input('tgl_lahir', isset($customer)?datefmysql($customer->lahir_tanggal):NULL, 'id=tgl_lahir class=special size=10') ?>
            <?= form_input('tgl_lahir', null, 'id=umur class=angka size=10') ?></span>            
        </table>
    </div>

    <?= form_open('laboratorium/pemeriksaan_radiologi_luar_save','id=formnew') ?>
    <div class="data-input">
        <table width="100%" class="inputan">Summary</legend>
            <div class="left_side_60">
                <tr><td>Nama Dokter:</td><td><?= form_input('dokter_rad', NULL, 'size=30 id=dokter_rad class=input_rad') ?> 
                <tr><td>Nama Radiografer:</td><td><?= form_input('radio_rad', NULL, 'size=30 id=radio_rad class=input_rad') ?> <?= form_hidden('id_radio_rad') ?>
                <tr><td>Waktu Order:</td><td><?= form_input('', date("d/m/Y H:i"), 'id=waktu_order_rad size=15') ?>
                <tr><td>Waktu Hasil:</td><td><?= form_input('', '', 'id=waktu_hasil_rad size=15') ?>
                <tr><td>Nama Layanan</td><td><?= form_input('layanan_rad', NULL, 'size=30 id=layanan_rad class=input_rad') ?> <?= form_hidden('id_layanan_rad') ?>
                <tr><td></td><td><?= form_button(NULL, 'Tambah (F8)', 'id=bt_rad') ?>
            </div>
            <div class="right_side_40">
                <tr><td>kv:</td><td><?= form_input('','','id="kv_rad" class=input_rad onkeyup=Angka(this)') ?>
                <tr><td>ma:</td><td><?= form_input('','','id="ma_rad" class=input_rad onkeyup=Angka(this)') ?>
                <tr><td>s:</td><td><?= form_input('','','id="s_rad" class=input_rad onkeyup=Angka(this)') ?>
                <tr><td>p:</td><td><?= form_input('','','id="p_rad" class=input_rad onkeyup=Angka(this)') ?>
                <tr><td>fr:</td><td><?= form_input('','','id="fr_rad" class=input_rad') ?>
            </div>
        </table>
    </div>
        
    <div class="data-list">
        <table class="tabel" id="add_data_rad" width="100%">
            <thead>
            <tr>
                <th width="20%">Nama Dokter</th>
                <th width="20%">Nama Radiografer</th>
                <th width="15%">Nama Layanan</th>
                <th width="15%">Waktu Order</th>
                <th width="15%">Waktu Hasil</th>
                <th width="10%">Edit Hasil</th>
                <th width="5%">#</th>
            </tr>
            </thead>
            <tbody>
            <?php if(isset($radiologi)): ?>
                    <?php foreach ($radiologi as $key => $value): ?>
                    <tr>
                        <td><?= ($value->dokter_pemesan != '')?$value->dokter_pemesan:'-' ?></td>
                        <td><?= ($value->radiografer != '')?$value->radiografer:'-' ?></td>
                        <td><?= ($value->layanan != '')?$value->layanan:'-' ?></td>
                        <td><?= ($value->waktu_order != '')?datetimefmysql($value->waktu_order, true):'-' ?></td>
                        <td><?= ($value->waktu_hasil != '')?datetimefmysql($value->waktu_hasil, true):'' ?></td>
                        <td align="center"><span class="link_button" onclick="form_edit('<?= $value->id ?>','<?= $customer->id ?>')" >Edit</span></td>
                        <td class=aksi>
                            <a class="delete" onClick="delete_rad('<?= $value->id ?>', this)"></a>
                        </td>
                    </tr> 
                    <?php endforeach; ?>
                <?php else: ?>
                    <script type="text/javascript">
                        $(function(){
                            insert_empty();
                        });
                    </script>
                <?php endif; ?>                
            </tbody>
        </table>
        <br/>
        <?= form_submit('','Simpan','id=save') ?> <?= form_button('' ,'Reset' ,'id=reset')?>
    </div>

    <?= form_close() ?>

     <script type="text/javascript">
        $(function(){
            $('#form_rad_edit').dialog({
                autoOpen: false,
                height: 230,
                width: 300,
                title : 'Edit Hasil Pemeriksaan Radiologi',
                modal: true,
                resizable : false
            });

            $('#formradedit').submit(function(){
                var id = $('input[name=id_pemeriksaan_radiologi]').val();

                $.ajax({
                    type : 'POST',
                    url: '<?= base_url("laboratorium/edit_hasil_rad") ?>/'+id,
                    data : $(this).serialize(),
                    cache: false,
                    success: function(data) {
                        $('#form_rad_edit').dialog('close');
                    }
                });

                return false;
            });
        });
    </script>

<div id="dialog_edit_rad"></div>
</div><br/><br/>


<div id="form_cari" style="display: none;position: static; background: #fff; padding: 10px;">
        <div class="data-input">
            <?= form_open('','id=formcari')?>
            <fieldset>
                <tr><td>Nama Penduduk</td><td><?= form_input('nama', null, 'id=nama_cari size=30 class="input-text"') ?>
                <tr><td>Alamat</td><td><?= form_textarea('alamat','','id=alamat_cari class="standar"')?>
                <tr><td></td><td><?= form_button('', 'Cari', 'id=cari_pdd onclick=cari_penduduk(1)'); ?>
            <?= form_button('reset', 'Reset', 'id=reset_cari onclick=reset_pencarian()') ?>
            </table>
            <?= form_close() ?>

            <div class="list_penduduk"></div>
        </div>
    </div>

    <script type="text/javascript">
        $(function(){
            $('#bt_cari').click(function(){
                $('#form_cari').dialog('open');
            });
            $('#formcari').submit(function(){
                cari_penduduk(1);
                return false;
            });

            $('#form_cari').dialog({
                autoOpen: false,
                height: 550,
                width: 700,
                title : 'Pencarian Penduduk',
                modal: true,
                resizable : false,
                open: function(){
                    cari_penduduk(1)
                },
                close : function(){
                    reset_pencarian();
                }
            });
        });

        function cari_penduduk(page){
            $.ajax({
                url: '<?= base_url("demografi/search_penduduk") ?>/'+page,
                cache: false,
                data : $('#formcari').serialize(),
                success: function(data) {
                   $('.list_penduduk').html(data);
                }
            });
        }

        function reset_pencarian(){
            $('#nama_cari, #alamat_cari').val('');
            $('.list_penduduk').html('');
        }

    

        function pilih_penduduk(id, id_daftar){
            $.ajax({
                url: '<?= base_url("demografi/get_penduduk") ?>/'+id,
                cache: false,
                dataType :'json',
                success: function(data) {
                    console.log()
                    $('input[name=id_penduduk]').val(data.penduduk_id);
                    $('#nama').val(data.nama);
                    
                    $('#kelamin').val(data.gender);
                    $('#tgl_lahir').val(datefmysql(data.lahir_tanggal));
                    $('#alamat').val(data.alamat);
                    $('#darah_gol').val(data.darah_gol);
                    $('#telp').val(data.telp);
                    get_kelurahan(data.kelurahan_id);

                }
            });
           
            $('#form_cari').dialog('close');
                
        }

        function get_kelurahan(kel_id){
            if(kel_id != ''){
                $.ajax({
                    url: '<?= base_url("demografi/detail_kelurahan") ?>/'+kel_id,
                    cache: false,
                    dataType :'json',
                    success: function(data) {
                        $('#kelurahan').val(data.nama);
                        $('input[name=id_kelurahan]').val(data.id);
                        $('#addr').html("Kec: "+data.kecamatan+", Kab: "+data.kabupaten+", Prov: "+data.provinsi);
                    }
                });
            }
        }

        function paging(page, tab, cari){
            cari_penduduk(page);
        }

    </script>
