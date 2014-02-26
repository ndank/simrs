<?php $this->load->view('message') ?>
<title><?= $title ?></title>
<div class="kegiatan">
    <script type="text/javascript">
    $(function(){
        $(document).unbind().on("keydown", function (e) {
            if (e.keyCode === 119) {
                e.preventDefault();
                $('#fuckyeah').click();
            }
        });
        $('#waktu_order_lab, #waktu_hasil_lab').datetimepicker({
            changeYear : true,
            changeMonth : true
        });

        $('.datetimehasil, #waktu').datetimepicker({
            changeYear : true,
            changeMonth : true
        });
    
        $('#formnew').submit(function(){
            var url = $(this).attr('action');

            
            var id_pelayanan = $('input[name=id_pelayanan_kunjungan]').val();
            var no_daftar = $('input[name=no_daftar]').val();
            var param = "&id_pelayanan="+id_pelayanan+'&no_daftar='+no_daftar;
            $.ajax({
                type : 'POST',
                url: url,
                data: $(this).serialize()+param,
                cache: false,
                dataType: 'json',
                success: function(data) {
                    if (data.status == true) {
                        $('input[name=id_pelayanan_kunjungan]').val(data.id_pelayanan);
                        get_fisio_list(data.id_pelayanan);
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


        $('.angka').keyup(function(){Angka(this);});

        
        $(function(){            
            
       
            $('#clear').button({icons: {secondary: 'ui-icon-refresh'}});
            $('button[id=simpanutama]').button({icons: {secondary: 'ui-icon-search'}});
            $('#reset').button({icons: {secondary: 'ui-icon-refresh'}});
            $('input[type=submit]').each(function(){ $(this).replaceWith('<button type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'">' + $(this).val() + '</button>');});
            $('button[type=submit]').button({icons: {secondary: 'ui-icon-circle-check'}});

            $('#reset').click(function(){
                $('#loaddata').empty();
                $('#loaddata').load($.cookie('url'));
            
            });

            $('#fuckyeah').button({icons: {secondary: 'ui-icon-circle-plus'}}).click(function() {
                var dokter = $('input[name=id_dokter]').val();
                var anestesi = $('input[name=id_anestesi]').val();
                var waktu = $('#waktu').val();
                var tindakan = $('input[name=id_tindakan]').val();
                var icd = $('#icd').val();

                if (waktu == '') {
                    custom_message('Peringatan!','Waktu harus diisi!');
                    $('#waktu').focus();
                    return false;
                };

                if (dokter == '') {
                    custom_message('Peringatan!','Nama nakes harus diisi!');
                    $('#dokter').focus();
                    return false;
                };

                if (tindakan == '') {
                    custom_message('Peringatan!','Tindakan harus diisi!');
                    $('#tindakan').focus();
                    return false;
                };
                add_new_rows(dokter, $('#dokter').val() , anestesi, $('#anestesi').val(), waktu, $('#unit option:selected').val(), $('#unit option:selected').text(), tindakan, $('#tindakan').val(), $('#icd').val() );
                clear_lab_field();
            });

            $('#dokter').autocomplete("<?= base_url('inv_autocomplete/load_data_pegawai') ?>",
            {
                parse: function(data){
                    var parsed = [];
                    for (var i=0; i < data.length; i++) {
                        parsed[i] = {
                            data: data[i],
                            value: data[i].nama // nama field yang dicari
                        };
                    }
                    $('#id_nakes_tindak'+i).val('');
                    return parsed;
                },
                formatItem: function(data,i,max){
                    var str = '<div class=result>'+data.nama+'<br/>'+data.nip+'</div>';
                    return str;
                },
                width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
            }).result(
            function(event,data,formated){
                $(this).val(data.nama);
                $('input[name=id_dokter]').val(data.id);
            });

            $('#tindakan').autocomplete("<?= base_url('inv_autocomplete/layanan_jasa_load_data') ?>",
            {
                parse: function(data){
                    var parsed = [];
                    for (var i=0; i < data.length; i++) {
                        parsed[i] = {
                            data: data[i],
                            value: data[i].nama // nama field yang dicari
                        };
                    }
                    $('#id_tindakan'+i).val('');
                    $('#icd_tindak'+i).val('');
                    //$('#tindakan_tindak'+i).val('');
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
                $('input[name=id_tindakan]').val(data.id);
                $('#icd').val(data.kode_icdixcm);
            });

            $('#icd').autocomplete("<?= base_url('inv_autocomplete/get_tindakan_jasa') ?>",
            {
                parse: function(data){
                    var parsed = [];
                    for (var i=0; i < data.length; i++) {
                        parsed[i] = {
                            data: data[i],
                            value: data[i].nama // nama field yang dicari
                        };
                    }
                    $('#id_tindakan'+i).val('');
                    $('#icd_tindak'+i).val('');
                    $('#tindakan_tindak'+i).val('');
                    return parsed;
                },
                formatItem: function(data,i,max){
                    var str = '<div class=result>'+data.kode_icdixcm+'</div>';
                    return str;
                },
                width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
            }).result(
            function(event,data,formated){
                $(this).val(data.kode_icdixcm);
                $('input[name=id_tindakan]').val(data.id);
                $('#tindakan').val(data.nama);
            });

            $('#anestesi').autocomplete("<?= base_url('inv_autocomplete/load_data_pegawai') ?>",
            {
                parse: function(data){
                    var parsed = [];
                    for (var i=0; i < data.length; i++) {
                        parsed[i] = {
                            data: data[i],
                            value: data[i].nama // nama field yang dicari
                        };
                    }
                    $('#id_anes_tindak'+i).val('');
                    return parsed;
                },
                formatItem: function(data,i,max){
                    var str = '<div class=result>'+data.nama+'<br/>'+data.nip+'</div>';
                    return str;
                },
                width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
            }).result(
            function(event,data,formated){
                $(this).val(data.nama);
                $('input[name=id_anestesi]').val(data.id);
            });

             
        
        });

    });

        function removeMe(el) {
            var parent = el.parentNode.parentNode;
            parent.parentNode.removeChild(parent);
        }

        function get_fisio_list(id_pk){
            $('#add_data_fisio tbody').html('');
             var str_empty = '<tr class="empty_tindakan"><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td></tr>';
             
            $.ajax({
                type : 'GET',
                url: '<?= base_url("laboratorium/pelayanan_fisioterapi_list") ?>/'+id_pk, 
                cache: false,
                dataType: 'json',
                success: function(data) {
                    $('#add_data_fisio').hide();
                    var str ='';
                    var ket = '';
                    var count = 0;
                    
                    $.each(data, function(i, v){
                        count += 1;
                        append_fisio_table(v);
                    });


                    $('#add_data_fisio').fadeIn('slow');
                    if(count == 0){
                        $('#add_data_fisio tbody').append(str_empty);
                        $('#add_data_fisio tbody').append(str_empty);
                    }

                }
            });
        }

        function insert_empty(){
             var str_empty = '<tr class="empty_tindakan"><td>&nbsp</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>';
             $('#add_data_fisio tbody').append(str_empty);
             $('#add_data_fisio tbody').append(str_empty);
        }

        function delete_lab(id_lab, obj){
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
                                url: '<?= base_url("laboratorium/delete_pemeriksaan_lab") ?>/'+id_lab,
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

        function clear_lab_field(){
            $("input[name=id_dokter], input[name=id_anestesi], input[name=id_tindakan]").val("");
            $('#waktu, #dokter, #anestesi, #tindakan, #icd').val('');
        }

        function append_fisio_table(v){

            var str = '<tr>'+
                '<td>'+datetimefmysql(v.waktu)+'</td>'+
                '<td>'+((v.operator != null)?v.operator:'-')+'</td>'+
                '<td>'+((v.anestesi != null)?v.anestesi:'-') +'</td>'+
                '<td>'+v.unit+'</td>'+
                '<td>'+v.tindakan+'</td>'+
                '<td>'+v.kode+'</td>'+
                '<td class=aksi><a class="delete" onClick="delete_fisio('+v.id+', this)"></a></td></tr>'; 
            $('#add_data_fisio tbody').append(str);
        }

        function edit_hasil_lab(id, hasil){
            $.ajax({
                type : 'GET',
                url: '<?= base_url('laboratorium/edit_hasil_lab') ?>/'+id, 
                cache: false,
                data : 'hasil='+hasil,
                dataType: 'json',
                success: function(data) {
                    
                }
            });
        }

        function edit_waktu_hasil_lab(id, hasil){
            $.ajax({
                type : 'GET',
                url: '<?= base_url('laboratorium/edit_waktu_hasil_lab') ?>/'+id, 
                cache: false,
                data : 'hasil='+hasil,
                dataType: 'json',
                success: function(data) {
                    
                }
            });
        }
        
        function add_new_rows(id_dokter, dokter , id_anestesi, anestesi, waktu, id_unit, unit, id_tindakan, tindakan, icd ) {

           var str = '<tr>'+
                '<td>'+waktu+' <input type="hidden" name="waktu[]" value="'+waktu+'" /></td>'+
                '<td>'+dokter+' <input type="hidden" name="dokter[]" value="'+id_dokter+'" /></td>'+
                '<td>'+anestesi+' <input type="hidden" name="anestesi[]" value="'+id_anestesi+'" /></td>'+
                '<td>'+unit+' <input type="hidden" name="unit[]" value="'+id_unit+'" /></td>'+
                '<td>'+tindakan+' <input type="hidden" name="tindakan[]" value="'+id_tindakan+'" /></td>'+
                '<td>'+icd+' <input type="hidden" name="icd[]" value="'+icd+'" /></td>'+
                '<td class=aksi><a class="delete" onClick="removeMe(this)"></a></td></tr>';  
            if($('.empty_tindakan').length > 0){
                $('#add_data_fisio tbody').html('');
            }

            $('#add_data_fisio tbody').append(str);
            $('#tindakan').focus();
        }
    
        function birthByAge(umur){
            var today = new Date();
            var birth = today.getDate()+"/"+(today.getMonth()+1)+"/"+(today.getYear()+1900-umur);
    
            return birth;
        }

        function delete_fisio(id_lab, obj){
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
                                url: '<?= base_url("pelayanan/delete_tindakan") ?>/'+id_lab,
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
            <div class='msg' id="msg_new"></div>
            <?= form_hidden('id_pelayanan_kunjungan', isset($id_pk)?$id_pk:'') ?>
            <?= form_hidden('no_daftar', $customer->no_daftar) ?>

            <tr><td>Nama Customer:</td><td><span class="label"><?= $customer->pasien ?></span>
            <tr><td>Alamat Jalan:</td><td><span class="label"><?= $customer->alamat ?></span>
            <tr><td>Kelurahan:</td><td><span class="label"><?= $customer->kelurahan ?>
                <?php 
                    if(isset($customer)){
                        echo ", ".$customer->kecamatan.", ".$customer->kabupaten.", ".$customer->provinsi;
                    }
                ?>
            </span>
            <tr><td>Jenis Kelamin:</td><td><span class="label"><?= ($customer->gender == 'L')?'Laki - Laki':(($customer->gender == 'P')?'Perempuan':'') ?></span>
            <tr><td>Tanggal Lahir</td><td><span class="label"><?= isset($customer)?datefmysql($customer->lahir_tanggal):'' ?></span>
        </table>
    </div>


    <?= form_open('laboratorium/pelayanan_fisioterapi_luar_save','id=formnew') ?>
    <div class="data-input">
        <table width="100%" class="inputan">Pelayanan Lab</legend>
            <?= form_hidden('id_dokter') ?>
            <?= form_hidden('id_anestesi') ?>
            <?= form_hidden('id_tindakan') ?>

            <tr><td>Waktu</td><td><?= form_input('waktu', date('d/m/Y H:i'),'id=waktu size=30')?>
            <tr><td>Nama Nakes</td><td><?= form_input('dokter','','id=dokter size=30')?>
            <tr><td>Nama Nakes Anestesi</td><td><?= form_input('anestesi','','id=anestesi size=30')?>
            <tr><td>Unit</td><td><?= form_dropdown('unit', $unit ,null,'id=unit style=width:218px')?>
            <tr><td>Tindakan</td><td><?= form_input('tindakan','','id=tindakan size=30')?>
            <tr><td>ICDIX - CM</td><td><?= form_input('icd','','id=icd size=30')?>
            <tr><td></td><td>
            <tr><td></td><td><?= form_button('' ,'Tambah (F8)' ,'id=fuckyeah')?>
            
        </table>
    </div>
        
    <div class="data-list">
        <table class="tabel" id="add_data_fisio" width="100%">
            <thead>
            <tr>
                <th width="15%">Waktu</th>
                <th width="20%">Nama Nakes</th>
                <th width="20%">Nama Nakes Anestesi</th>
                <th width="15%">Unit</th>
                <th width="20%">Tindakan</th>
                <th width="10%">ICDIX - CM</th> 
                <th width="5%">Aksi</th>
            </tr>
            </thead>
            <tbody>
            <?php if(isset($fisioterapi) && (sizeof($fisioterapi) > 0)): ?>
                    <?php foreach ($fisioterapi as $key => $value): ?>
                    
                    <tr>
                        <td><?= ($value->waktu != '')?datetimefmysql($value->waktu, true):'-' ?></td>
                        <td><?= ($value->operator != '')?$value->operator:'-' ?></td>
                        <td><?= ($value->anestesi != '')?$value->anestesi:'-' ?></td>
                        <td><?= ($value->unit != '')?$value->unit:'-' ?></td>
                        <td><?= ($value->layanan != '')?$value->layanan:'-' ?></td>
                        <td><?= ($value->kode_icdixcm != '')?$value->kode_icdixcm:'-' ?></td>
                        <td class=aksi>
                            <a class="delete" onClick="delete_fisio('<?= $value->id ?>', this)"></a>
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

</div>
