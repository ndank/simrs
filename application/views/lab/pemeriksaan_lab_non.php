<?php $this->load->view('message') ?>
<?php
$nilai_rujukan = array(''=>'Pilih...','L' => 'Low', 'N' => 'Neutral', 'H' => 'High');
?>
<title><?= $title ?></title>
<div class="kegiatan">
    <script type="text/javascript">
    function form_add(id_pl, id_pk) {
        var id_pelayanan = id_pk;
        var str = '<div id="form_hasil_lab" class=data-input>'+
                    '<form id=save_hasil_lab>'+
                    '<input type=hidden name=id_pemeriksaan_lab value="'+id_pl+'" />'+
                        '</td><td>'+
                            '<table width=100% style="line-height: 22px; border-spacing: 0;">'+
                                '<tr><td width=40%>No.:</td><td id=arv_no_pemeriksaan></td></tr>'+
                                '<tr><td>Waktu Order:</td><td id=arv_waktu_order></td></tr>'+
                                '<tr><td>Waktu Hasil:</td><td><input type="text" name="waktu_hasil" id=arv_waktu_hasil /></td></tr>'+
                                '<tr><td>Nama Analis Lab.:</td><td><input type="text" name="analis" value="" id=arv_analis /><input type="hidden" name="id_analis" id="id_analis" /></td></tr>'+
                                '<tr><td>Nama Layanan:</td><td id=arv_layanan></td></tr>'+
                                '<tr><td>Hasil:</td><td><?= form_input("hasil", NULL, "id=hasil size=10") ?></td></tr>'+
                                '<tr><td>Satuan:</td><td><select name="satuan" id=satuan><?php foreach ($satuan as $data) { ?> <option value="<?= $data->id ?>"><?= $data->nama ?></option><?php } ?></select></td></tr>'+
                                '<tr><td>Nilai Rujukan:</td><td><select name=nilai id=nilai><?php foreach ($nilai_rujukan as $key => $data) { ?> <option value="<?= $key ?>"><?= $data ?></option><?php } ?></select></td></tr>'+
                            '</table>'+
                        '</td></tr>'+
                    '</table>'+
                    '</form>'+
                  '</div>';
            $('#dialog_edit_lab').html('').append(str);
           
            $('#form_hasil_lab').dialog({
                autoOpen: true,
                modal: true,
                width: 400,
                height: 330,
                title: 'Entry Hasil',
                buttons: {
                    "Simpan": function() {
                        $('#save_hasil_lab').submit();
                    },
                    "Cancel": function() {
                        $(this).dialog().remove();
                    }
                }, open: function() {
                    $('#hasil').focus();
                     $('#arv_waktu_hasil').datetimepicker({
                        changeYear : true,
                        changeMonth : true
                    });
                    $.ajax({
                        url: '<?= base_url("pelayanan/laboratorium_luar_load_data_pemeriksaan") ?>/'+id_pl,
                        cache: false,
                        dataType: 'json',
                        success: function(data) {

                            $('#arv_no_pemeriksaan').html(data.id);
                            $('#arv_waktu_order').html((data.waktu_order !== null)?datetimefmysql(data.waktu_order):'');
                            $('#arv_waktu_hasil').val((data.waktu_hasil !== null)?datetimefmysql(data.waktu_hasil):'');
                            $('#arv_analis').val(data.analis);
                            $('#id_analis').val(data.id_analis);
                            $('#arv_layanan').html(data.layanan);
                            $('#hasil').val(data.hasil);
                            $('#nilai').val(data.ket);
                            $('#satuan').val(data.id_satuan);
                        }
                    });
                }, close: function() {
                    $(this).dialog().remove();
                }
            });

           $('#arv_analis').autocomplete("<?= base_url('inv_autocomplete/load_data_pegawai_profesi') ?>",
            {
                extraParams :{ 
                    profesi : function(){
                        return 'Kimia Analist';
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
                $('input[name=id_analis]').val(data.id);
            });

            $('#save_hasil_lab').submit(function() {
                if ($('#hasil').val() === '') {
                    custom_message('Alert', 'Hasil tidak boleh kosong', '#hasil'); return false;
                }else{
                    $.ajax({
                        url: '<?= base_url("pelayanan/pemeriksaan_lab_save") ?>',
                        data: $('#save_hasil_lab').serialize(),
                        dataType: 'json',
                        type: 'POST',
                        success: function(data) {
                            if (data === true) {
                                custom_message('Informasi', 'Data berhasil di simpan');
                                $('#form_hasil_lab').dialog().remove();
                               
                               get_lab_list(id_pelayanan);
                            }
                        }
                    });
                }
                return false;
            });
    }


    $(function(){
        $('.print').button({icons: {secondary: 'ui-icon-print'}});
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
                }

                if (Dnama == '') {
                    custom_message('Peringatan','Nama pasien tidak boleh kosong !','#nama');
                    return false;
                }

                if (Dalamat == '') {
                    custom_message('Peringatan','Alamat jalan tidak boleh kosong !','#');
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
                        get_lab_list(data.id_pelayanan);
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
        $('#nama').focus(); 
        
        $(function(){            
                   
            $('#tgl_lahir').datepicker({
                changeYear : true,
                changeMonth : true,
                maxDate : +0
            });
            $('#clear').button({icons: {secondary: 'ui-icon-refresh'}});
            $('button[id=simpanutama], button[id=cari_pdd]').button({icons: {secondary: 'ui-icon-search'}});
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

            $('#fuckyeah').button({icons: {secondary: 'ui-icon-circle-plus'}}).click(function() {
                var dokter = $('#dokter_lab').val();
                var analis = $('input[name=id_analis_lab]').val();
                var waktu_order = $('#waktu_order_lab').val();
                var waktu_hasil = $('#waktu_hasil_lab').val();
                var layanan_id = $('input[name=id_layanan_lab]').val();
                var layanan = $('#layanan_lab').val();
                var hasil = $('#hasil_lab').val();
                var keterangan = $('#ket_lab option:selected').val();
                var satuan = $('input[name=id_satuan_lab]').val();
                
                if (layanan == '') {
                    custom_message('Peringatan','Layanan harus diisi!', '#layanan_lab');
                    return false;
                };
                
                add_new_rows(dokter , analis, $('#analis_lab').val(), waktu_order, waktu_hasil, layanan_id, layanan, hasil, keterangan, satuan, $('#satuan_lab').val());
                clear_lab_field();
            });

             

            $('#analis_lab').autocomplete("<?= base_url('inv_autocomplete/load_data_pegawai_profesi') ?>",
            {
                extraParams :{ 
                    profesi : function(){
                        return 'Kimia Analist';
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
                $('input[name=id_analis_lab]').val(data.id);
            });

            $('#layanan_lab').autocomplete("<?= base_url('inv_autocomplete/get_layanan_laboratorium') ?>",
            {
                parse: function(data){
                    var parsed = [];
                    for (var i=0; i < data.length; i++) {
                        parsed[i] = {
                            data: data[i],
                            value: data[i].nama // nama field yang dicari
                        };
                    }
                    $('input[name=id_layanan_lab]').val('');
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
                $('input[name=id_layanan_lab]').val(data.id);
            });

            $('#satuan_lab').autocomplete("<?= base_url('inv_autocomplete/get_satuan') ?>",
            {
                parse: function(data){
                    var parsed = [];
                    for (var i=0; i < data.length; i++) {
                        parsed[i] = {
                            data: data[i],
                            value: data[i].nama // nama field yang dicari
                        };
                    }
                    $('input[name=id_satuan_lab]').val('');
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
                $('input[name=id_satuan_lab]').val(data.id);
            });       
        });
    });

        function removeMe(el) {
            var parent = el.parentNode.parentNode;
            parent.parentNode.removeChild(parent);
        }

        function get_lab_list(id_pk){
            $('#add_data_lab tbody').html('');
             var str_empty = '<tr class="empty_lab"><td>&nbsp</td><td><td></td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>';
             
            $.ajax({
                type : 'GET',
                url: '<?= base_url("laboratorium/pemeriksaan_lab_list") ?>/'+id_pk, 
                cache: false,
                dataType: 'json',
                success: function(data) {
                    $('#add_data_lab').hide();
                    var str ='';
                    var ket = '';
                    var count = 0;
                    
                    $.each(data, function(i, v){
                        count += 1;
                        append_lab_table(v);
                    });


                    $('#add_data_lab').fadeIn('slow');
                    if(count == 0){
                        $('#add_data_lab tbody').append(str_empty);
                        $('#add_data_lab tbody').append(str_empty);
                    }

                }
            });
        }

        function insert_empty(){
             var str_empty = '<tr class="empty_lab"><td>&nbsp</td><td></td><td><td></td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>';
             $('#add_data_lab tbody').append(str_empty);
             $('#add_data_lab tbody').append(str_empty);
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
            $('#waktu_lab, #dokter_lab, #analis_lab, #layanan_lab,#nilai_lab, #ket_lab, #hasil_lab, #satuan_lab').val('');
        }

        function append_lab_table(v){
           var ket = '';
           if(v.ket_nilai_rujukan == 'L'){
                ket = 'Low';
            }else if(v.ket_nilai_rujukan == 'N'){
                ket = 'Netral';
            }else if(v.ket_nilai_rujukan == 'H'){
                ket = 'High';
            }


            var waktu_hasil = ((v.waktu_hasil !== null)?datetimefmysql(v.waktu_hasil):'');

            var str = '<tr>'+
                '<td>'+((v.dokter_pemesan != null)?v.dokter_pemesan:'-')+'</td>'+
                '<td>'+((v.laboran != null)?v.laboran:'-') +'</td>'+
                '<td>'+datetimefmysql(v.waktu_order)+'</td>'+
                '<td>'+waktu_hasil+'</td>'+
                '<td>'+v.layanan+'</td>'+
                '<td align="center">'+v.hasil+'</td>'+
                '<td>'+ket+'</td>'+
                '<td>'+((v.satuan != null)?v.satuan:'-')+'</td>'+
                '<td align="center" style="white-space: nowrap;">'+
                '<span class="link_button" onclick="form_add('+v.id+', '+v.id_pelayanan_kunjungan+')">Entri Hasil</span></td>'+
                '<td class=aksi><a class="delete" onClick="delete_lab('+v.id+', this)"></a></td></tr>'; 
            $('#add_data_lab tbody').append(str);

            $('.datetimehasil').datetimepicker({
                changeYear : true,
                changeMonth : true
            });
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
        
        function add_new_rows(nama_dokter, analis, nama_analis, waktu_order, waktu_hasil, layanan_id, layanan, hasil, keterangan, satuan, nama_satuan) {
            var ket = '';
            if(keterangan == 'L'){
                ket = 'Low';
            }else if(keterangan == 'N'){
                ket = 'Netral';
            }else if(keterangan == 'H'){
                ket = 'High';
            }

           var str = '<tr>'+
                '<td>'+nama_dokter+' <input type="hidden" name="dokter_lab[]" value="'+nama_dokter+'" /></td>'+
                '<td>'+nama_analis+' <input type="hidden" name="analis_lab[]" value="'+analis+'" /></td>'+
                '<td>'+waktu_order+' <input type="hidden" name="waktu_order_lab[]" value="'+waktu_order+'" /></td>'+
                '<td>'+waktu_hasil+' <input type="hidden" name="waktu_hasil_lab[]" value="'+waktu_hasil+'" /></td>'+
                '<td>'+layanan+' <input type="hidden" name="layanan_lab[]" value="'+layanan_id+'" /></td>'+
                '<td>'+hasil+' <input type="hidden" name="hasil_lab[]" value="'+hasil+'" /></td>'+
                '<td>'+ket+' <input type="hidden" name="ket_lab[]" value="'+keterangan+'" /></td>'+
                '<td>'+nama_satuan+' <input type="hidden" name="satuan_lab[]" value="'+satuan+'" /></td>'+
                '<td></td>'+
                '<td class=aksi><a class="delete" onClick="removeMe(this)"></a></td></tr>';  
            if($('.empty_lab').length > 0){
                $('#add_data_lab tbody').html('');
            }

            $('#add_data_lab tbody').append(str);
            $('#layanan').focus();
        }
    
        function birthByAge(umur){
            var today = new Date();
            var birth = today.getDate()+"/"+(today.getMonth()+1)+"/"+(today.getYear()+1900-umur);
    
            return birth;
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

        function eliminate(el) {
            var parent = el.parentNode.parentNode;
            parent.parentNode.removeChild(parent);      
        }

        function cetak_hasil_lab(){
            var id = $('input[name=id_pelayanan_kunjungan]').val();

            if(id !== ''){
                window.open('<?= base_url("laboratorium/cetak_hasil_pemeriksaan_lab_luar") ?>/'+ id, 'mywindow', 'width=820px, height=300px, scrollable=1, resizable=0');
            }else{
                custom_message('Alert !', 'Belum memasukkan data pasien', '#no');
            }        
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
            <span class="label" style="margin-top:7px;"><?= form_input('tgl_lahir', isset($customer)?datefmysql($customer->lahir_tanggal):NULL, 'id=tgl_lahir class=special size=10') ?>
            <?= form_input('tgl_lahir', null, 'id=umur class=angka size=10') ?></span>            
            
        </table>
    </div>


    <?= form_open('laboratorium/pemeriksaan_lab_non_save','id=formnew') ?>
    <div class="data-input">
        <table width="100%" class="inputan">Pelayanan Lab</legend>
            <?= form_hidden('id_analis_lab') ?>
            <?= form_hidden('id_layanan_lab') ?>
            <?= form_hidden('id_satuan_lab') ?>
            <?php $ket = array(''=>'Pilih...','L'=>'Low','N'=>'Netral', 'H'=>'High' ) ?>
            <tr><td>Nama Dokter</td><td><?= form_input('dokter_lab','','id=dokter_lab size=30')?>
            <tr><td>Nama Analis Lab</td><td><?= form_input('analis_lab','','id=analis_lab size=30')?>
            <tr><td>Waktu Order</td><td><?= form_input('waktu_order_lab', date('d/m/Y H:i'),'id=waktu_order_lab size=30')?>
            <tr><td>Waktu Hasil</td><td><?= form_input('waktu_hasil_lab', '','id=waktu_hasil_lab size=30')?>
            <tr><td>Layanan</td><td><?= form_input('layanan_lab','','id=layanan_lab size=30')?>
            <tr><td>Hasil</td><td><?= form_input('hasil_lab','','id=hasil_lab size=30)')?>
            <tr><td>Keterangan</td><td><?= form_dropdown('ket_lab', $ket ,null,'id=ket_lab')?>
            <tr><td>Satuan</td><td><?= form_input('satuan_lab','','id=satuan_lab size=30')?>
            <tr><td></td><td>
            <tr><td></td><td><?= form_button('' ,'Tambah (F8)' ,'id=fuckyeah')?> <?= form_button('','Cetak Hasil Laboratorium', 'class=print onclick=cetak_hasil_lab()') ?> 
        </table>
    </div>
        
    <div class="data-list">
        <table class="tabel" id="add_data_lab" width="100%">
            <thead>
            <tr>
                <th width="15%">Nama Dokter</th>
                <th width="15%">Nama Analis Lab</th>
                <th width="10%">Waktu Order</th>
                <th width="10%">Waktu Hasil</th>
                <th width="20%">Layanan</th>
                <th width="5%">Hasil</th>                
                <th width="5%">Ket</th>
                <th width="10%">Satuan</th>
                <th>Hasil</th>
                <th width="5%">Aksi</th>
            </tr>
            </thead>
            <tbody>
            <?php if(isset($laboratorium) && (sizeof($laboratorium) > 0)): ?>
                    <?php foreach ($laboratorium as $key => $value): ?>
                    <?php 
                        if ($value->ket_nilai_rujukan === 'L') {
                            $ket = "Low";
                        }else if($value->ket_nilai_rujukan === 'N'){
                            $ket = "Netral";
                        }else if($value->ket_nilai_rujukan === 'H'){
                            $ket = "High";
                        }else{
                            $ket = "";
                        }

                    ?>
                    <tr>
                        <td><?= ($value->dokter_pemesan != '')?$value->dokter_pemesan:'-' ?></td>
                        <td><?= ($value->laboran != '')?$value->laboran:'-' ?></td>
                        <td><?= ($value->waktu_order != '')?datetimefmysql($value->waktu_order, true):'-' ?></td>
                        <td><?= ($value->waktu_hasil != '')?datetimefmysql($value->waktu_hasil, true):'' ?></td>
                        <td><?= ($value->layanan != '')?$value->layanan:'-' ?></td>
                        <td><?= $value->hasil ?></td>
                        <td><?= $ket ?></td>
                        <td><?= ($value->satuan != '')?$value->satuan:'-' ?></td>
                        <td align="center" style="white-space: nowrap;">
                            <span class="link_button" onclick="form_add('<?= $value->id ?>','<?= $customer->id ?>')">Entri Hasil</span>
                        </td>
                        <td class=aksi>
                            <a class="delete" onClick="delete_lab('<?= $value->id ?>', this)"></a>
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

</div><br/><br/>
<div id="dialog_edit_lab"></div>


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
