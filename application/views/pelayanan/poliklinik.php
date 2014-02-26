<?php $this->load->view('message');$this->load->helper('html'); ?>
<title><?= $title ?></title>
<div class="titling"><h1><?= $title ?></h1></div>

    <script type="text/javascript">
    $('#tabs, #tabs-utama').tabs();
    $('#no_barcode').focus();
    var request;
    var data_pasien;
    var dWidth = $(window).width();
    var dHeight= $(window).height();
    var x = screen.width/2 - dWidth/2;
    var y = screen.height/2 - dHeight/2;
        $(function() {
            $("#fromdate, #todate").datepicker({
                changeYear : true,
                changeMonth : true 
            });
            insert_empty_lab();
            insert_empty_rad();
            $('#waktu, #waktu_order_rad, #waktu_hasil_rad, #waktu_order_lab, #waktu_hasil_lab, #waktu_pelayanan').datetimepicker({
                changeYear : true,
                changeMonth : true
            });
            $('#riwayat_rm').hide();
            $('#add_diag, #add_tindak').button({icons: {secondary: 'ui-icon-circle-plus'}});
            $('input[type=submit]').each(function(){ $(this).replaceWith('<button type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'">' + $(this).val() + '</button>');});
            $('button[type=submit]').button({icons: {secondary: 'ui-icon-circle-check'}});
            $('#reset, #reset-search').button({icons: {secondary: 'ui-icon-refresh'}});
            $('.print').button({icons: {secondary: 'ui-icon-print'}});
            $('#fuckyeah').button({icons: {secondary: 'ui-icon-circle-plus'}}).click(function() {
                var dokter = $('input[name=id_dokter_lab]').val();
                var analis = $('input[name=id_analis_lab]').val();
                var waktu_order = ($('#waktu_order_lab').val() !== '')?datetime2mysql($('#waktu_order_lab').val()):'';
                var waktu_hasil = ($('#waktu_hasil_lab').val() !== '')?datetime2mysql($('#waktu_hasil_lab').val()):'';
                var layanan_id = $('input[name=id_layanan_lab]').val();
                var layanan = $('#layanan_lab').val();
                var hasil = $('#hasil_lab').val();
                var keterangan = $('#ket_lab option:selected').val();
                var satuan = $('input[name=id_satuan_lab]').val();

                if (dokter === '') {
                    custom_message('Alert !', 'Nama Dokter tidak boleh kosong', '#dokter_lab'); return false;
                }
                if (waktu_order === '') {
                    custom_message('Alert !', 'Waktu order tidak boleh kosong !', '#waktu_order_lab'); return false;
                }
                if (layanan_id === '') {
                    custom_message('Alert !', 'Nama layanan tidak boleh kosong atau pilih layanan yang tersedia !', '#layanan_lab'); return false;
                }
                
             
                add_new_rows(dokter, $('#dokter_lab').val() , analis, $('#analis_lab').val(), waktu_order, waktu_hasil, layanan_id,layanan, hasil, keterangan, satuan, $('#satuan_lab').val());
                clear_lab_field();
            });


            $('#bt_rad').button({icons: {secondary: 'ui-icon-circle-plus'}}).click(function() {
                var id_dokter   = $('input[name=id_dokter_rad]').val();
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

            $('#formpoli').submit(function(){

                if($('input[name=no_rm]').val() == ''){
                    custom_message('Peringatan','Anda belum memasukkan data pasien !','#no');
                    return false;
                }

                
                if($('input[name=id_dpjp]').val() == ''){
                    custom_message('Peringatan','DPJP tidak boleh kosong !','#dpjp');
                    return false;
                }
                
                if( ($('#tensi').val() !== '') & ($('#tensi').val().indexOf('/') === -1) ){
                    custom_message('Peringatan', 'Format nilai tekanan darah salah!', '#tensi');
                    return false;
                }

                if(!request) {
                    request = $.ajax({
                    type : 'POST',
                    url: '<?= base_url("pelayanan/poliklinik_save") ?>',
                    data: $(this).serialize(),
                    cache: false,
                    dataType: 'json',
                    success: function(msg){
                        request = null;
                        if (msg.status == true){
                            if($('input[name=id_pelayanan_kunjungan]').val() != ''){
                                alert_edit();
                            }else{
                                alert_tambah();
                            }
                            $('input[name=id_pelayanan_kunjungan]').val(msg.id_pelayanan)
                            disable();
                        }else{
                            custom_message('Peringatan',"Gagal tambah data");
                        }
                       return false;
                    }
                    });
                }
                return false;
            });
            var lebar = $('#dokter').width();
            $('#layanan').autocomplete("<?= base_url('inv_autocomplete/layanan_jasa_load_data') ?>",
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
                $('input[name=id_layanan]').val(data.id);
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
                    $('input[name=id_dokter]').val('');
                    return parsed;
                },
                formatItem: function(data,i,max){
                    var str = '<div class=result>'+data.nama+'<br/>'+data.nip+'</div>';
                    return str;
                },
                width: lebar, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                dataType: 'json', // tipe data yang diterima oleh library ini disetup sebagai JSON
                cacheLength: 0
            }).result(
            function(event,data,formated){
                $(this).val(data.nama+' - '+data.nip);
                $('input[name=id_dokter]').val(data.id);
            });

             $('#dokter_rad').autocomplete("<?= base_url('inv_autocomplete/load_data_penduduk_dokter') ?>",
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
                    var str = '<div class=result>'+data.nama+'<br/>'+data.kerja_izin_surat_no+'</div>';
                    return str;
                },
                width: lebar, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                dataType: 'json', // tipe data yang diterima oleh library ini disetup sebagai JSON
                cacheLength: 0
            }).result(
            function(event,data,formated){
                $(this).val(data.nama);
                $('input[name=id_dokter_rad]').val(data.penduduk_id);
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

            $('#no_barcode').keydown(function(e){
                if ((e.keyCode == 13) && (IsNumeric($(this).val()))) {
                    $.ajax({
                        url: '<?= base_url("pelayanan/pasien_load_detail") ?>/'+$(this).val(),
                        data : 'jenis=Poliklinik',
                        cache: false,
                        dataType:'json',
                        success: function(data) {
                            if(data.length !== 0){
                                reset_all_data();
                                $('#msg_detail').html('');
                                $('input[name=no_rm], #no, #no_barcode').val(data.no_rm);
                                fill_field(data.id_pk);
                                load_data_pelayanan(data.no_daftar);
                            }else{
                                custom_message('Peringatan!', 'Pasien tidak mendaftar pada layanan Poliklinik', '#no_barcode');
                                $('#no').val('');
                            }
                           
                        }
                    });
                    return false;
                };

            });
            var lebar = $('#no').width();
            $('#no').autocomplete("<?= base_url('pelayanan/pasien_load_data') ?>",
            {
                extraParams :{ 
                    jenis : function(){
                        return 'Poliklinik';
                    }
                },
                parse: function(data){
                    var parsed = [];
                    for (var i=0; i < data.length; i++) {
                        parsed[i] = {
                            data: data[i],
                            value: data[i].no_rm // nama field yang dicari
                        };
                    }
                    //$('input[name=no_rm]').val('');
                    return parsed;
                
                },
                formatItem: function(data,i,max) {
                    if (data.no_daftar != null) {
                        var str = '<div class=result>'+data.no_rm+' - '+data.nama+'<br/>'+data.alamat+'</div>';
                    }
                    return str;
                },
                max: 100,
                width: lebar, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
            }).result(
            function(event,data,formated){
                reset_all_data();
                $(this).val(data.no_rm);
                $('#msg_detail').html('');
                $('input[name=no_rm]').val(data.no_rm);
                data_pasien = data;
                load_data_pelayanan(data.no_daftar);
                
            });
            var lebar = $('#asuransi').width();
            $('#asuransi').autocomplete("<?= base_url('inv_autocomplete/load_data_produk_asuransi') ?>",
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
                    var str = '<div class=result>'+data.nama+' <br/>'+data.instansi+'</div>';
                    return str;
                },
                width: lebar, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                dataType: 'json', // tipe data yang diterima oleh library ini disetup sebagai JSON
                cacheLength:0
            }).result(
            function(event,data,formated){
        
                $(this).val(data.nama);
                $('input[name=id_asuransi]').val(data.id);
            });

            $('#dpjp').autocomplete("<?= base_url('inv_autocomplete/load_data_pegawai') ?>",
            {
                parse: function(data){
                    var parsed = [];
                    for (var i=0; i < data.length; i++) {
                        parsed[i] = {
                            data: data[i],
                            value: data[i].nama // nama field yang dicari
                        };
                    }
                    $('input[name=id_dpjp]').val('');
                    return parsed;
                },
                formatItem: function(data,i,max){
                    var str = '<div class=result>'+data.nama+'<br/>'+data.nip+'</div>';
                    return str;
                },
                width: lebar, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                dataType: 'json', // tipe data yang diterima oleh library ini disetup sebagai JSON
                cacheLength:0
            }).result(
            function(event,data,formated){
                $(this).val(data.nama);
                $('input[name=id_dpjp]').val(data.id);
                //$("input[name=id_jurusan]").val(data.id_jurusan_kualifikasi_pendidikan);
            });

             $('#add_diag').click(function() {
                var rows = $('.row_diag').length;
                add(rows);                
            });

            $(document).unbind().on("keydown", function (e) {
                if (e.keyCode === 115) {
                    e.preventDefault();
                    $('#fuckyeah').click();
                }
                if (e.keyCode === 118) {
                    e.preventDefault();
                    $('#bt_rad').click();
                }

                if (e.keyCode === 119) {
                    e.preventDefault();
                    $('#add_diag').click();
                }
                if (e.keyCode === 120) {
                    e.preventDefault();
                    $('#add_tindak').click();
                }
            });

             $('#add_tindak').click(function() {
                var rows = $('.row_tindak').length;
                add_tindak(rows);                
            });

            $('#dokter_lab').autocomplete("<?= base_url('inv_autocomplete/load_data_penduduk_dokter') ?>",
            {
                parse: function(data){
                    var parsed = [];
                    for (var i=0; i < data.length; i++) {
                        parsed[i] = {
                            data: data[i],
                            value: data[i].nama // nama field yang dicari
                        };
                    }
                    //$('input[name=id_dokter_lab]').val('');
                    return parsed;
                },
                formatItem: function(data,i,max){
                    var str = '<div class=result>'+data.nama+'<br/>'+data.kerja_izin_surat_no+'</div>';
                    return str;
                },
                width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
            }).result(
            function(event,data,formated){
                $(this).val(data.nama);
                $('input[name=id_dokter_lab]').val(data.penduduk_id);

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
                   // $('input[name=id_analis_lab]').val('');
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
        
        function removeMe(el) {
            var parent = el.parentNode.parentNode;
            parent.parentNode.removeChild(parent);
        }

        function reset_all_data(){
            $('#add_data_lab tbody').html('');
            $('#add_data_rad tbody').html('');
             $('#diag_add tbody').html('');
             $('#tindak_add tbody').html('');

            $('input[name=id_pelayanan_kunjungan]').val('');
            $('#asuransi').val('');
            $('input[name=id_asuransi]').val('');
            $('#no_polis').val('');
            //$('#dpjp').val('');
            //$('input[name=id_dpjp]').val('');
            $('input[name=id_jurusan]').val('');

            $('#tensi').val('');
            $('#nadi').val('');
            $('#suhu').val('');
            $('#nafas').val('');
            $('#bb').val('');
        }

        function insert_empty_lab(){
            var str_empty = '<tr class="empty_lab"><td>&nbsp</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>';
            $('#add_data_lab tbody').append(str_empty);
            $('#add_data_lab tbody').append(str_empty);
        }

        function get_lab_list(id_pk){
            $('#add_data_lab tbody').html('');
             var str_empty = '<tr class="empty_lab"><td>&nbsp</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>';
             
            $.ajax({
                type : 'GET',
                url: '<?= base_url('laboratorium/pemeriksaan_lab_list') ?>/'+id_pk, 
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
                    if(count === 0){
                        $('#add_data_lab tbody').html('');
                        $('#add_data_lab tbody').append(str_empty);
                        $('#add_data_lab tbody').append(str_empty);
                    }

                }
            });
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
                '<td>'+((v.dokter != null)?v.dokter:'-')+'</td>'+
                '<td>'+((v.laboran != null)?v.laboran:'-') +'</td>'+
                '<td>'+datetimefmysql(v.waktu_order)+'</td>'+
                '<td>'+waktu_hasil+'</td>'+
                '<td>'+v.layanan+'</td>'+
                '<td align="center">'+v.hasil+'</td>'+
                '<td>'+ket+'</td>'+
                '<td>'+((v.satuan != null)?v.satuan:'-')+'</td>'+
                '<td class=aksi align="center"><a class="deletion" onClick="delete_lab('+v.id+', this)"></a></td></tr>'; 
            $('#add_data_lab tbody').append(str);
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

        
        function add_new_rows(dokter,nama_dokter, analis, nama_analis, waktu_order, waktu_hasil,layanan_id, layanan, hasil, keterangan, satuan, nama_satuan) {
            var ket = '';
            if(keterangan == 'L'){
                ket = 'Low';
            }else if(keterangan == 'N'){
                ket = 'Netral';
            }else if(keterangan == 'H'){
                ket = 'High';
            }

           var str = '<tr>'+
                '<td>'+nama_dokter+' <input type="hidden" name="dokter_lab[]" value="'+dokter+'" /></td>'+
                '<td>'+nama_analis+' <input type="hidden" name="analis_lab[]" value="'+analis+'" /></td>'+
                '<td>'+waktu_order+' <input type="hidden" name="waktu_order_lab[]" value="'+waktu_order+'" /></td>'+
                '<td>'+waktu_hasil+' <input type="hidden" name="waktu_hasil_lab[]" value="'+waktu_hasil+'" /></td>'+
                '<td>'+layanan+' <input type="hidden" name="layanan_lab[]" value="'+layanan_id+'" /></td>'+
                '<td>'+hasil+' <input type="hidden" name="hasil_lab[]" value="'+hasil+'" /></td>'+
                '<td>'+ket+' <input type="hidden" name="ket_lab[]" value="'+keterangan+'" /></td>'+
                '<td>'+nama_satuan+' <input type="hidden" name="satuan_lab[]" value="'+satuan+'" /></td>'+
                '<td class=aksi align="center"><a class="deletion" onClick="removeMe(this)"></a></td></tr>';  
            if($('.empty_lab').length > 0){
                $('#add_data_lab tbody').html('');
            }

            $('#add_data_lab tbody').append(str);
            $('#layanan').focus();
        }

        function add_new_rad(){
            var nama_dokter = $('#dokter_rad').val();
            var id_dokter   = $('input[name=id_dokter_rad]').val();
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
                        '<td>'+nama_dokter+' <input type=hidden name="id_dokter_radio[]" value="'+id_dokter+'" /></td>'+
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
                        '<td align=center class=aksi><a class="deletion" onClick="removeMe(this)"></a></td>'+
                      '</tr>';
            if($('.empty_rad').length > 0){
                $('#add_data_rad tbody').html('');
            }
            $('#add_data_rad tbody').append(str);
            $('input[name=id_radio_rad], input[name=id_layanan_rad]').val('');
            $('#layanan_rad').val('').focus();
        }


        function load_data_pelayanan(no_kunjungan){
            $.ajax({
                url: '<?= base_url("pelayanan/load_data_pelayanan_kunjungan") ?>/'+no_kunjungan+'/poli',
                cache: false,
                dataType : 'json',
                success: function(data) {
                    //console.log(data);
                    if (!jQuery.isEmptyObject(data)) {
                        var waktu = '';
                        if(data.waktu !== null){
                            waktu = datetimefmysql(data.waktu);
                            $('#waktu_pelayanan').val(waktu);
                        }
                        fill_field(data.id_pelayanan_kunjungan);
                        $('input[name=id_pelayanan_kunjungan]').val(data.id_pelayanan_kunjungan);
                        $('#asuransi').val(data.produk_asuransi);
                        $('input[name=id_asuransi]').val(data.id_produk_asuransi);
                        $('#no_polis').val(data.no_polis);
                        $('#dpjp, #dokter_lab, #dokter_rad').val(data.nama_dpjp);
                        $('input[name=id_dpjp], #id_dokter_lab, #id_dokter_rad').val(data.id_dpjp);
                        $('input[name=id_jurusan]').val(data.id_jurusan_kualifikasi_pendidikan);
                        

                        $('#tensi').val(data.p_tensi);
                        $('#nadi').val(data.p_nadi);
                        $('#suhu').val(data.p_suhu);
                        $('#nafas').val(data.p_nafas);
                        $('#bb').val(data.p_bb);
                        
                        $('#anamnesis').val(data.anamnesis);
                        $('#pemeriksaan').val(data.pemeriksaan);

                        get_lab_list(data.id_pelayanan_kunjungan);
                        get_rad_list(data.id_pelayanan_kunjungan);

                        load_data_diagnosis(data.id_pelayanan_kunjungan);
                        load_data_tindakan(data.id_pelayanan_kunjungan);
                    }
                }
            });
        }
        
        function load_data_pelayanan2(no_kunjungan){
            $.ajax({
                url: '<?= base_url("pelayanan/load_data_pelayanan_kunjungan") ?>/'+no_kunjungan+'/poli',
                cache: false,
                dataType : 'json',
                success: function(data) {
                    //console.log(data);
                    if (!jQuery.isEmptyObject(data)) {
                        var waktu = '';
                        if(data.waktu !== null){
                            waktu = datetimefmysql(data.waktu);
                            $('#waktu_pelayanan').val(waktu);
                        }
                        $('input[name=id_pelayanan_kunjungan]').val(data.id_pelayanan_kunjungan);
                        $('#asuransi').val(data.produk_asuransi);
                        $('input[name=id_asuransi]').val(data.id_produk_asuransi);
                        $('#no_polis').val(data.no_polis);
                        $('#dpjp, #dokter_lab, #dokter_rad').val(data.nama_dpjp);
                        $('input[name=id_dpjp], #id_dokter_lab, #id_dokter_rad').val(data.id_dpjp);
                        $('input[name=id_jurusan]').val(data.id_jurusan_kualifikasi_pendidikan);
                        

                        $('#tensi').val(data.p_tensi);
                        $('#nadi').val(data.p_nadi);
                        $('#suhu').val(data.p_suhu);
                        $('#nafas').val(data.p_nafas);
                        $('#bb').val(data.p_bb);
                        
                        $('#anamnesis').val(data.anamnesis);
                        $('#pemeriksaan').val(data.pemeriksaan);
                    }
                }
            });
        }
        
        function load_data_diagnosis(no_kunjungan) {
            $.ajax({
                url: '<?= base_url("pelayanan/load_data_diagnosis") ?>/'+no_kunjungan+'/poli',
                cache: false,
                success: function(data) {
                    $('#diag_add tbody').html(data);
                }
            });
        }
        function load_data_tindakan(no_kunjungan) {
            $.ajax({
                url: '<?= base_url("pelayanan/load_data_tindakan") ?>/'+no_kunjungan+'/poli',
                cache: false,
                success: function(data) {
                    $('#tindak_add tbody').html(data);
                }
            });
        }
     function add(i) {
        var nama_dpjp   = $('#dpjp').val();
        var id_dpjp     = $('#id_dpjp').val();
        str = '<tr class="row_diag">'+
            '<td><input type="text" name="waktu_diag[]" id=waktu_diag'+i+' value="<?= date('d/m/Y H:i') ?>" style="text-align: center;" /></td>'+
            '<td><input type="text" name="dok_diag" value="'+nama_dpjp+'" id=dokter_diag'+i+' /><input type="hidden" name="dokter_diag[]" value="'+id_dpjp+'" id=id_dokter_diag'+i+' /></td>'+
            '<td><select name="unit_diag[]" style="width:100%" id="unit_diag'+i+'"><option value="">Pilih</option></select></td>'+ 
            '<td><input type="text" name="sbb_diag'+i+'" id=sebab_diag'+i+' /><input type="hidden" name="sebab_diag[]" id=id_sebab_diag'+i+' /></td>'+
            '<td><input type="text" name="icd_diag[]" id=icd_diag'+i+' /></td>'+
            '<td align="center"><span id="kasus_diag'+i+'"></span><input type="hidden" name="kasus[]" id="val_kasus_diag'+i+'" /></td>'+
            '<td class=aksi align="center"><a class="deletion" onClick="eliminate(this)"></a></td></tr>';      
        $('#diag_add tbody').append(str);
        $('#waktu_diag'+i).datetimepicker({
            changeYear : true,
            changeMonth : true
        });
        var lebar = $('#dokter_diag'+i).width();
        $('#dokter_diag'+i).autocomplete("<?= base_url('inv_autocomplete/load_data_pegawai') ?>",
            {
                parse: function(data){
                    var parsed = [];
                    for (var i=0; i < data.length; i++) {
                        parsed[i] = {
                            data: data[i],
                            value: data[i].nama // nama field yang dicari
                        };
                    }
                    $('#id_dokter_diag'+i).val('');
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
                $('#id_dokter_diag'+i).val(data.id);

            });
            $.ajax({
                type : 'POST',
                url: '<?= base_url("pelayanan/load_data_unit_layanan") ?>',
                cache: false,
                success: function(data) {
                    var obj = jQuery.parseJSON(data);
                    $.each(obj, function ( index, val) {
                        $('#unit_diag'+i).append('<option value="'+val.id+'" '+((val.id === '<?= $this->session->userdata('id_unit') ?>')?"selected":null)+'>'+val.nama+'</option>');
                    });
                }
            });
            var lebar_diag = $('#icd_diag'+i).width();
            $('#icd_diag'+i).autocomplete("<?= base_url('pelayanan/gol_sebab_sakit_load_data2') ?>",
            {
                parse: function(data){
                    var parsed = [];
                    for (var i=0; i < data.length; i++) {
                        parsed[i] = {
                            data: data[i],
                            value: data[i].nama // nama field yang dicari
                        };
                    }
                    $('#id_sebab_diag'+i).val('');
                    return parsed;
                },
                formatItem: function(data,i,max){
                    var str = '<div class=result>'+data.no_daftar_terperinci+'</div>';
                    return str;
                },
                width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
            }).result(
            function(event,data,formated){
                if($('input[name=no_rm]').val() !== ''){
                    $(this).val(data.no_daftar_terperinci);
                    $('#sebab_diag'+i).val(data.nama);
                    $('#id_sebab_diag'+i).val(data.id);
                    get_kasus($('input[name=no_rm]').val(), data.id, i);
                }else{
                    custom_message('Peringatan', 'Anda belum memasukkan data pasien !', '#no');
                }                
            });

            var sebab_diag = $('#sebab_diag'+i).width();
            $('#sebab_diag'+i).autocomplete("<?= base_url('pelayanan/gol_sebab_sakit_load_data') ?>",
            {
                parse: function(data){
                    var parsed = [];
                    for (var i=0; i < data.length; i++) {
                        parsed[i] = {
                            data: data[i],
                            value: data[i].nama // nama field yang dicari
                        };
                    }
                    $('#id_sebab_diag'+i).val('');
                    return parsed;
                },
                formatItem: function(data,i,max){
                    var str = '<div class=result>'+data.nama+'</div>';
                    return str;
                },
                width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                dataType: 'json', // tipe data yang diterima oleh library ini disetup sebagai JSON
                max: 100,
                cacheLength: 0
            }).result(
            function(event,data,formated){
                if($('input[name=no_rm]').val() !== ''){
                    $(this).val(data.nama);
                    $('#icd_diag'+i).val(data.no_daftar_terperinci);
                    $('#id_sebab_diag'+i).val(data.id);
                    get_kasus($('input[name=no_rm]').val(), data.id, i);
                }else{
                    custom_message('Peringatan', 'Anda belum memasukkan data pasien !', '#no');
                }
            });
    }

    function get_kasus(no_rm, id_gol_sebab, i){
        $.ajax({
            url: '<?= base_url("pelayanan/get_no_kasus") ?>/'+no_rm+'/'+id_gol_sebab,
            cache: false,
            dataType : 'json',
            success: function(data) {
                $('#kasus_diag'+i).html(data.jenis_kasus);
                $('#val_kasus_diag'+i).val(data.jenis_kasus);
            }
        });
    }
    
    function print_informed_consent(val, id_pk, id_tindakan, baris) {
        var url = '';
        var operator = '';
        var anestesi = '';
        if (val === 'setuju') {
            url = '<?= base_url('pelayanan/ic_persetujuan_tindakan') ?>/'+id_pk+'/'+id_tindakan;
            if(id_tindakan == 'null'){
                operator = $('#nakes_tindak'+baris).val();
                anestesi = $('#anes_tindak'+baris).val();
                url += '?operator='+operator+'&anestesi='+anestesi;
            }
            window.open(url, 'form persetujuan','width='+dWidth+', height='+dHeight+', left='+x+',top='+y);
        }
        if (val === 'tolak') {
            url = '<?= base_url('pelayanan/ic_penolakan_tindakan') ?>/'+id_pk+'/'+id_tindakan;
            if(id_tindakan == 'null'){
                operator = $('#nakes_tindak'+baris).val();
                anestesi = $('#anes_tindak'+baris).val();
                url += '?operator='+operator+'&anestesi='+anestesi;
            }
            window.open(url, 'form penolakan tindakan', 'width='+dWidth+', height='+dHeight+', left='+x+',top='+y);
        }
        if (val === 'pasien_tidak_sadar') {
            url = '<?= base_url('pelayanan/ic_pasien_tidak_sadar') ?>/'+id_pk+'/'+id_tindakan;
            if(id_tindakan == 'null'){
                operator = $('#nakes_tindak'+baris).val();
                url += '?operator='+operator;
            }
            window.open(url, 'form pk pasien tidak sadar', 'width='+dWidth+', height='+dHeight+', left='+x+',top='+y);
        }
        if (val === 'penghentian_tindakan') {
            url = '<?= base_url('pelayanan/ic_penghentian_tindakan') ?>/'+id_pk+'/'+id_tindakan;
            if(id_tindakan == 'null'){
                operator = $('#nakes_tindak'+baris).val();
                url += '?operator='+operator;
            }
            window.open(url, 'form penghentian tindakan', 'width='+dWidth+', height='+dHeight+', left='+x+',top='+y);
        }
        if (val === 'setuju_rawat_inap') {
            window.open('<?= base_url("pelayanan/ic_persetujuan_rawat_inap") ?>/'+id_pk, 'form persetujuan rawat inap','width='+dWidth+', height='+dHeight+', left='+x+',top='+y);
        }
        if (val === 'setuju_tindakan_sterilisasi') {
            window.open('<?= base_url("pelayanan/ic_persetujuan_tindakan_sterilisasi") ?>/'+id_pk, 'form tindakan sterilisasi', 'width='+dWidth+', height='+dHeight+', left='+x+',top='+y);
        }
        
    }

    function cetak_ic(id_tindakan, baris){
        if($('input[name=no_rm]').val() == ''){
            custom_message('Peringatan','Anda belum memasukkan data pasien !','#no');
            return false;
        }else if($('input[name=id_pelayanan_kunjungan]').val() == ''){
            custom_message('Peringatan','Data pelayanan kunjungan pasien belum tersimpan !','#no');
            return false;
        }else{
            var str = '<div id=create_ic>'+
            'Informed Consent:<br/>'+
            '<ul style="list-style-type: none;">'+
            '<li><?= form_radio('ic', 'setuju', TRUE, 'id=setuju') ?> Persetujuan Tindakan</li>'+
            '<li><?= form_radio('ic', 'tolak', FALSE, 'id=total') ?> Penolakan Tindakan</li>'+
            '<li><?= form_radio('ic', 'pasien_tidak_sadar', FALSE, 'id=pasien_tidak_sadar') ?> P.K Pasien Tidak Sadar</li>'+
            '<li><?= form_radio('ic', 'penghentian_tindakan', FALSE, 'id=penghentian_tindakan') ?> Penghentian Tindakan</li>'+
            '<li><?= form_radio('ic', 'setuju_rawat_inap', FALSE, 'id=setuju_rawat_inap') ?> Persetujuan Rawat Inap</li>'+
            '<li><?= form_radio('ic', 'setuju_tindakan_sterilisasi', FALSE, 'id=setuju_tindakan_sterilisasi') ?> Persetujuan Tindakan Sterilisasi</li>'+
            '</ul>'+
            '</div>';
            $('#loaddata').append(str);
            $('#create_ic').dialog({
                autoOpen: true,
                width: 400,
                height: 300,
                modal: true,
                title: 'Persetujuan Tindakan Kedokteran (Informed Consent)',
                buttons: {
                    "Cetak": function() {
                        print_informed_consent($('input[name=ic]:checked').val(),$('input[name=id_pelayanan_kunjungan]').val(),id_tindakan,baris);
                    },
                    "Batal": function() {
                        $(this).dialog().remove();
                    }
                }
            });
                
        }
        
    }

    function add_tindak(i) {
        var nama_dpjp   = $('#dpjp').val();
        var id_dpjp     = $('#id_dpjp').val();
        str = '<tr class="row_tindak">'+
            '<td><input type="text" name="waktu_tindak[]" id=waktu_tindak'+i+' value="<?= date('d/m/Y H:i') ?>" style="text-align: center;" /></td>'+
            '<td><input type="text" name="nakes_tindak[]" value="'+nama_dpjp+'" id=nakes_tindak'+i+' /><input type="hidden" name="id_nakes_tindak[]" value="'+id_dpjp+'" id=id_nakes_tindak'+i+' /></td>'+
            '<td><input type="text" name="anes_tindak[]" id=anes_tindak'+i+' /><input type="hidden" name="id_anes_tindak[]" id=id_anes_tindak'+i+' /></td>'+
            '<td><select name="unit_tindak[]" style="width:100%" id="unit_tindak'+i+'"><option value="">Pilih</option></select></td>'+ 
            '<td><input type="text" name="tindakan_tindak[]" id=tindakan_tindak'+i+' /><input type="hidden" name="tindakan[]" id=id_tindakan'+i+' />'+
            '<input type=hidden name="id_tarif[]" id="id_tarif'+i+'" /></td>'+
            //'<td align=center><span class="ic link_button" id=ic'+i+'>I.C.</span></td>'+
            '<td class=aksi align="center"><a class="deletion" onClick="eliminate(this);"></a></td></tr>';      
        $('#tindak_add tbody').append(str);
        $('#waktu_tindak'+i).datetimepicker({
            changeYear : true,
            changeMonth : true
        });
        $('#ic'+i).click(function(){
            cetak_ic('null', i);
        });
        var nakes_tindak = $('#nakes_tindak'+i).width();
        $('#nakes_tindak'+i).autocomplete("<?= base_url('inv_autocomplete/load_data_pegawai') ?>",
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
                $('#id_nakes_tindak'+i).val(data.id);
            });
            var tindakan_tindak = $('#tindakan_tindak'+i).width();
            $('#tindakan_tindak'+i).autocomplete("<?= base_url('inv_autocomplete/tindakan_tarif_load_data') ?>",
            {
                parse: function(data){
                    var parsed = [];
                    for (var i=0; i < data.length; i++) {
                        parsed[i] = {
                            data: data[i],
                            value: data[i].nama_tarif // nama field yang dicari
                        };
                    }
                    $('#id_tindakan'+i).val('');
                    $('#icd_tindak'+i).val('');
                    //$('#tindakan_tindak'+i).val('');
                    return parsed;
                },
                formatItem: function(data,i,max){
                    var str = '<div class=result>'+data.nama_tarif+'</div>';
                    return str;
                },
                width: 380, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                dataType: 'json', // tipe data yang diterima oleh library ini disetup sebagai JSON
                max: 50,
                cacheLength: 0
            }).result(
            function(event,data,formated){
                $(this).val(data.nama_tarif);
                $('#id_tindakan'+i).val(data.id_layanan);
                $('#id_tarif'+i).val(data.id);
            });
            var icd_tindak = $('#icd_tindak'+i).width();
            $('#icd_tindak'+i).autocomplete("<?= base_url('inv_autocomplete/get_tindakan_jasa') ?>",
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
                $('#id_tindakan'+i).val(data.id);
                $('#tindakan_tindak'+i).val(data.nama);
            });
            var anes_tindak = $('#anes_tindak'+i).width();
            $('#anes_tindak'+i).autocomplete("<?= base_url('inv_autocomplete/load_data_pegawai') ?>",
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
                $('#id_anes_tindak'+i).val(data.id);
            });
            $.ajax({
               type : 'POST',
               url: '<?= base_url("pelayanan/load_data_unit_layanan") ?>',
               cache: false,
               success: function(data) {
                   var obj = jQuery.parseJSON(data);
                   $.each(obj, function ( index, val) {
                       $('#unit_tindak'+i).append('<option value="'+val.id+'" '+((val.id === '<?= $this->session->userdata('id_unit') ?>')?"selected":null)+'>'+val.nama+'</option>');
                   });
               }
           });
    }

    function eliminate(el) {
        var parent = el.parentNode.parentNode;
        parent.parentNode.removeChild(parent);      
    }

    function disable(){
        //$('input[type=text], select,.tombol').attr('disabled','disabled');
        $('#simpan').hide();
    }

    function detail_rm(){
        if($('#no').val() !== ''){
            $('#rm').dialog({
                autoOpen: false,
                title :'Riwayat Rekam Medis Pasien ',
                height: $(window).height() - 10,
                width: $(window).width() - 10,
                modal: true,
                resizable : false,
                buttons: [ 
                    { text: "Ok", click: function() { 
                            $( this ).dialog( "close" ); 
                        } 
                    }
                ]
            });
            $.ajax({
                url: '<?= base_url("demografi/get_rekam_medis_pasien") ?>/'+$('#no').val()+'/show',
                cache: false,
                success: function(data) {
                    $('#rekam_medis').html(data);
                    $('#rm').dialog('open');
                }
            });
        }
    }

    function fill_field(id_pk){
        reset_all_data();
        $.ajax({
            url: '<?= base_url('inv_autocomplete/get_data_dinamis_penduduk') ?>/'+id_pk,
            dataType: 'json',
            success: function(data) {
                load_data_pelayanan2(data.no_daftar);
                $('#riwayat_rm').show();
                $('#no, #no_barcode').val(data.no_rm);
                $('#msg_detail').html('');
                $('input[name=no_rm]').val(data.no_rm);
                $("input[name=id_kunjungan]").val(data.no_daftar);
                $('input[name=id_pelayanan_kunjungan]').val(data.id_pelayanan_kunjungan);
                $('input[name=id_jurusan]').val(data.id_jurusan_kualifikasi_pendidikan);
                $("#nama").html(data.nama);
                $("#alamat").html(data.alamat);
                $("#wilayah").html(data.kelurahan);
                $("#wilayah").append(" ");
                $("#wilayah").append(data.kecamatan);
                $("#gender").html((data.gender==="L")?'Laki-laki':((data.gender === "P")?'Perempuan':''));
                $("#umur").html(hitungUmur(data.lahir_tanggal));
                $("#pekerjaan").html(data.pekerjaan);
                $("#pendidikan").html(data.pendidikan);
                $("#waktu_datang").html((data.arrive_time === null)?'':datetimefmysql(data.arrive_time));
                $("input[name=waktu_keluar]").val(data.waktu_keluar);
                $("#ins_kes").html(data.instansi_rujukan);
                $("#nakes").html(data.nakes_perujuk);
                $("#nama_pj").html(data.nama_pjwb);
                $("#alamat_pj").html(data.alamat_pjwb);
                $("#wilayah_pj").html(data.kelurahan_pj);
                $("#wilayah_pj").append(" ");
                $("#wilayah_pj").append(data.kecamatan_pj);
                $("#telp_pj").html(data.telp_pjwb);
                $('#jenis_layanan').val(data.jenis_layanan);
            }
        });
    }

    function reset_all(){
        $('#loaddata').empty();
        $('#loaddata').load('<?= base_url("pelayanan/poliklinik") ?>');
    }

    function delete_tindakan(id_tindakan, obj){
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
                                url: '<?= base_url("pelayanan/delete_tindakan") ?>/'+id_tindakan,
                                cache: false,
                                success: function(data) {
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

    function clear_lab_field(){
        $("input[name=id_satuan_lab], input[name=id_layanan_lab]").val("");
        $('#layanan_lab,#nilai_lab, #ket_lab, #hasil_lab, #satuan_lab').val('');
        $('#layanan_lab').focus();
    }

    function delete_diagnosis(id_diag, obj){
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
                                url: '<?= base_url("pelayanan/delete_diagnosis") ?>/'+id_diag,
                                cache: false,
                                success: function(data) {
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

    function cetak_surat_kontrol(){
        var id = $('input[name=id_pelayanan_kunjungan]').val();
        var out = $('input[name=waktu_keluar]').val();
        if(id == ''){
            custom_message('Peringatan', 'Belum melakukan entry data pelayanan kunjungan!');
            $('#no').focus();
        }else if(out == ''){
            custom_message('Peringatan', 'Pasien belum keluar!');
        }else{
            var url = '<?= base_url("pelayanan/cetak_surat_kontrol")?>/'+id+'/'+$('input[name=id_kunjungan]').val();
            window.open(url, 'Surat Kontrol', 'width='+dWidth+', height='+dHeight+', left='+x+',top='+y);
        }
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

    function insert_empty_rad(){
        var str_empty = '<tr class="empty_rad"><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td></tr>';
        $('#add_data_rad tbody').append(str_empty);
        $('#add_data_rad tbody').append(str_empty);

    }

    function get_rad_list(id_pk){
        $('#add_data_rad tbody').html('');
        var str_empty = '<tr class="empty_rad"><td>&nbsp</td><td></td><td></td><td></td><td></td><td></td><td></td></tr>';
         
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
                    append_rad_table(v)
                });

                $('#add_data_rad').fadeIn('slow');
                if(count == 0){
                    $('#add_data_rad tbody').html('');
                    $('#add_data_rad tbody').append(str_empty);
                    $('#add_data_rad tbody').append(str_empty);
                }

            }
        });
    }

    function append_rad_table(v){
        var waktu_hasil = ((v.waktu_hasil !== null)?datetimefmysql(v.waktu_hasil):'');

        var str = '<tr class=add_rads>'+
            '<td>'+((v.dokter != null)?v.dokter:'-')+'</td>'+
            '<td>'+((v.radiografer != null)?v.radiografer:'-')+'</td>'+
            '<td>'+((v.layanan != null)?v.layanan:'-')+'</td>'+
            '<td>'+datetimefmysql(v.waktu_order)+'</td>'+
            '<td>'+waktu_hasil+'</td>'+
            '<td align="center"><span class="link_button" onclick="edit_hasil_rad('+v.id+')" >detail</span></td>'+
            '<td align="center" class=aksi><a class="deletion" onClick="delete_rad('+v.id+', this)"></a></td></tr>';      
        $('#add_data_rad tbody').append(str);
    }

    function edit_hasil_rad(id){
        $.ajax({
            type : 'GET',
            url: '<?= base_url("laboratorium/get_hasil_radiologi") ?>/'+id, 
            cache: false,
            dataType: 'json',
            success: function(data) {
                $('#kv_rad_edit').html(data.kv);
                $('#ma_rad_edit').html(data.ma);
                $('#s_rad_edit').html(data.s);
                $('#p_rad_edit').html(data.p);
                $('#fr_rad_edit').html(data.fr);
            }
        });
        
        $('#form_rad_edit').dialog('open');
    }


    function cetak_lab(){
        var id = $('input[name=id_pelayanan_kunjungan]').val();

        if(id !== ''){
            window.open('<?= base_url("laboratorium/cetak_sp_pemeriksaan_lab") ?>/'+ id, 'mywindow', 'width='+dWidth+', height='+dHeight+', left='+x+',top='+y);
        }else{
            custom_message('Alert !', 'Belum memasukkan data pasien', '#no');
        }        
    }

    function cetak_radio(){
        var id = $('input[name=id_pelayanan_kunjungan]').val();

        if(id !== ''){
            window.open('<?= base_url("laboratorium/cetak_sp_pemeriksaan_radiologi") ?>/'+id, 'mywindow', 'width='+dWidth+', height='+dHeight+', left='+x+',top='+y);
        }else{
            custom_message('Alert !', 'Belum memasukkan data pasien', '#no');
        }
    }

    function cetak_fisio(){
        var id = $('input[name=id_pelayanan_kunjungan]').val();
        if (id !== '') {
            window.open('<?= base_url("laboratorium/cetak_sp_pemeriksaan_fisioterapi") ?>/'+ id, 'mywindow', 'width='+dWidth+', height='+dHeight+', left='+x+',top='+y);
        } else {
            custom_message('Alert !', 'Belum memasukkan data pasien', '#no');
        }
    }

    function cetak_hasil_lab(){
        var id = $('input[name=id_pelayanan_kunjungan]').val();

        if(id !== ''){
            window.open('<?= base_url("laboratorium/cetak_hasil_pemeriksaan_lab") ?>/'+ id, 'mywindow', 'width='+dWidth+', height='+dHeight+', left='+x+',top='+y);
        }else{
            custom_message('Alert !', 'Belum memasukkan data pasien', '#no');
        }        
    }

    function get_pendaftar_list(p) {
        $.ajax({
            type : 'GET',
            url: '<?= base_url("pendaftaran/list_data_pendaftar_poliklinik") ?>/'+p,
            data : 'fromdate='+$('#fromdate').val()+'&todate='+$('#todate').val()+'&nama='+$('#nama').val()+'&id_layanan='+$('#idLayanan').val()+'&alamat='+$('#salamat').val(),
            cache: false,
            success: function(data) {
                $('#daftar_list').html(data);
            }
        });
    }    
    
    function paging(page, tab,search){
        get_pendaftar_list(page);
    }
    $('#cari_range').button({icons: {secondary: 'ui-icon-circle-check'}}).click(function() {
        get_pendaftar_list(1);
    });
    
    function pemeriksaan(id_pk) {
        $('#tabs-utama').tabs({ selected: 1});
        fill_field(id_pk);
        get_lab_list(id_pk);
        get_rad_list(id_pk);
        load_data_diagnosis(id_pk);
        load_data_tindakan(id_pk);
    }
    </script>
<div class="kegiatan">
    <?php if(isset($data->id)): ?>
        <script type="text/javascript">
            $(function(){{
                get_rad_list('<?= $data->id ?>');
                get_lab_list('<?= $data->id ?>');
            }});
        </script>
    <?php endif; ?>
    <?= form_open('','id=formpoli')?>
    <div id="tabs-utama">
            <ul>
                <li><a href="#tabs-utama-0">Data Pasien</a></li>
                <li><a href="#tabs-utama-1">Data Pendaftaran</a></li>
                <li><a href="#tabs-utama-2">Pemeriksaan</a></li>
                <li><a href="#tabs-utama-3">Pemeriksaan Penunjang</a></li>
                <li><a href="#tabs-utama-4">Diagnosis</a></li>
                <li><a href="#tabs-utama-5">Tindakan</a></li>
            </ul>
            <div id="tabs-utama-0">
                <?= form_open('', 'id=formcaripasien') ?>
                <table class="inputan" width="100%">
                    <tr><td style="width: 150px;">Range Tanggal:</td><td><?= form_input('fromdate', date("d/m/Y"), 'id = fromdate style="width: 70px;" size=10') ?> <span class="label"> s.d </span> <?= form_input('todate', date("d/m/Y"), 'id = todate style="width: 70px;" size=10') ?></td></tr>
                    <tr><td>Nama:</td><td><?= form_input('nama',null,'id=snama class=input-text')?></td></tr>
                    <tr><td>Jenis Layanan:</td><td><?= form_dropdown('id_layanan', $layanan, NULL,'id=idLayanan') ?></td></tr>
                    <tr><td>Alamat:</td><td><?= form_textarea('alamat','','id=salamat class=standar')?></td></tr>
                    <tr><td></td><td><?= form_button('cari', 'Cari', 'id=cari_range onClick=get_pendaftar_list(1) ') ?> <?= form_button(null, 'Reset', 'id=reset-search onClick=reset_all();') ?></td></tr>
                </table>
                <?= form_close() ?>
                <div id="daftar_list"></div>
            </div>
            <div id="tabs-utama-1">
                             
                    <?= form_hidden('no_rm',isset($data->no_rm)?$data->no_rm:NULL) ?>
                    <?= form_hidden('id_jurusan',isset($data->id_jurusan_kualifikasi_pendidikan)?$data->id_jurusan_kualifikasi_pendidikan:NULL) ?>
                    <?= form_hidden('id_kunjungan',isset($data->id_kunjungan)?$data->id_kunjungan:NULL) ?>
                    <?= form_hidden('id_pelayanan_kunjungan',isset($data->id)?$data->id:NULL) ?>
                    <?= form_hidden('waktu_keluar',isset($data->waktu_keluar)?$data->waktu_keluar:NULL) ?>
                    <table width="100%" class="inputan">
                        <tr><td style="width: 150px;">No. RM (Barcode):</td><td><?= form_input('', '', 'id=no_barcode class="input-text" size=40') ?></td></tr>
                        <tr><td>No. RM:</td><td><?= form_input('',isset($data->no_rm)?$data->no_rm:NULL,'id=no class="input-text" size=40')?></td></tr>
                        <tr><td>Nama Pasien:</td><td id="nama"><?= isset($data)?$data->pasien:NULL ?></td></tr>
                        <tr><td></td><td><span id="riwayat_rm" class="link_button" onclick="detail_rm()">Riwayat Rekam Medis</span></td></tr>
                        <tr><td>Alamat Jalan:</td><td id="alamat"><?= isset($data)?$data->alamat:NULL ?></td></tr>
                        <tr><td>Wilayah:</td><td id="wilayah"><?= isset($data)?$data->kelurahan." ".$data->kecamatan:NULL ?></td></tr>
                        <tr><td>Gender:</td><td id="gender"><?= isset($data)?(($data->gender=="L")?"Laki-Laki":"Perempuan"):NULL ?></td></tr>
                        <tr><td>Umur:</td><td id="umur"><?= isset($data)?hitungUmur($data->lahir_tanggal):NULL ?></td></tr>
                        <tr><td>Pekerjaan:</td><td id="pekerjaan"><?= isset($data)?$data->pekerjaan:NULL ?></td></tr>
                        <tr><td>Pendidikan:</td><td id="pendidikan"><?= isset($data)?$data->pendidikan:NULL ?></td></tr>
                        <tr><td colspan="2"><h2>Rujukan</h2></td></tr>
                        <tr><td>Waktu Datang:</td><td><span class="label" id="waktu_datang"><?= isset($rujuk)?datetime($rujuk->arrive_time):NULL ?><?= isset($data->arrive_time)?datetime($data->arrive_time):NULL ?></span></td></tr>
                        <tr><td>Instansi Kesehatan:</td><td><span class="label" id="ins_kes"><?= isset($rujuk)?$rujuk->rujukan:NULL ?><?= isset($data->instansi_rujukan)?$data->instansi_rujukan:NULL ?></span></td></tr>
                        <tr><td>Nama Nakes:</td><td><span class="label" id="nakes"><?= isset($rujuk)?$rujuk->nakes_perujuk:NULL ?><?= isset($data->nakes_perujuk)?$data->nakes_perujuk:NULL ?></span></td></tr>
                        <tr><td colspan="2"><h2>Penanggung Jawab</h2></td></tr>
                        <tr><td>Nama:</td><td id="nama_pj"><?= isset($pjwb)?$pjwb->penanggung_jawab:(isset($data->nama_pj)?$data->nama_pj:NULL) ?></td></tr>
                        <tr><td>Alamat Jalan:</td><td id="alamat_pj"><?= isset($pjwb)?$pjwb->alamat:(isset($data->alamat_pj)?$data->alamat_pj:NULL) ?></td></tr>
                        <tr><td>Wilayah:</td><td id="wilayah_pj"><?= isset($pjwb)?$pjwb->kelurahan." ".$pjwb->kecamatan:NULL ?><?= (isset($data->kelurahan_pj)&(isset($data->kecamatan_pj)))?$data->kelurahan_pj." ".$data->kecamatan_pj:NULL ?></td></tr>
                        <tr><td>No. Telp:</td><td id="telp_pj"><?= isset($pjwb)?$pjwb->telp:NULL ?><?= isset($data->telp_pj)?$data->telp_pj:NULL ?></span></td></tr>
                    </table>
            </div>
        
        <div id="tabs-utama-2">
            <table width="100%" cellspacing="0" cellpadding="0"><tr valign="top"><td width="50%">
                    <table width="100%" class="inputan">
                        <tr><td style="width: 150px;">Jenis Layanan</td><td><?= form_input('', NULL, 'id="jenis_layanan"') ?></td></tr>
                        <tr><td>Waktu:</td><td><?= form_input('waktu', date('d/m/Y H:i'), 'id=waktu_pelayanan size=18') ?></td></tr>
                        <tr><td>Nama Asuransi:</td><td><?= form_input('',isset($data->asuransi)?$data->asuransi:NULL,'id=asuransi size=40 class="input-text"')?></td></tr>
                        <tr><td>No. Polish Asuransi: </td><td><?= form_input('no_polis',isset($data->no_polis)?$data->no_polis:NULL,'id=no_polis size=40 class="input-text"')?></td></tr>
                        <tr><td>Dokter Penanggung Jwb:</td><td><?= form_input('',isset($data->nama)?$data->nama:NULL,'id=dpjp size=40 class="input-text"')?></td></tr>
                        <tr valign="top"><td width="125px;">Anamnesis:</td><td><?= form_textarea('anamnesis',isset($data->anamnesis)?$data->anamnesis:'','id=anamnesis class=standar row=3')?></td></tr>
                        <tr><td valign="top">Pemeriksaan Umum:</td><td><?= form_textarea('pemeriksaan',isset($data->pemeriksaan)?$data->pemeriksaan:'','id=pemeriksaan row=3')?>
                            <?= form_hidden('id_asuransi',isset($data->id_produk_asuransi)?$data->id_produk_asuransi:NULL)?>
                            <?= form_hidden('id_dpjp',isset($data->id_kepegawaian_dpjp)?$data->id_kepegawaian_dpjp:$this->session->userdata('id_pegawai'),'id=id_dpjp')?>
                        </td></tr>
                    </table>
                </td><td width="50%">
                    <table width="100%" class="inputan">
                        <tr><td width="30%">Tensi:</td><td><?= form_input('tensi',isset($data->p_tensi)?$data->p_tensi:NULL,'id=tensi size=15')?> mm/Hg</td></tr>
                        <tr><td>Nadi:</td><td><?= form_input('nadi',isset($data->p_nadi)?$data->p_nadi:NULL,'id=nadi size=15')?> ppm</td></tr>
                        <tr><td>Suhu:</td><td><?= form_input('suhu',isset($data->p_suhu)?$data->p_suhu:NULL,'id=suhu size=15')?> <sup>&deg;</sup> C</td></tr>
                        <tr><td>Nafas:</td><td><?= form_input('nafas',isset($data->p_nafas)?$data->p_nafas:NULL,'id=nafas size=15')?> bpm</td></tr>
                        <tr><td>BB:</td><td><?= form_input('bb',isset($data->p_bb)?$data->p_bb:NULL,'id=bb size=15')?> Kg</td></tr>
                    </table>
                </td></tr>
            </table>
        </div>
        <div id="tabs-utama-3">
            <div id="tabs">
                <ul>
                    <li><a href="#tabs-1">Laboratorium</a></li>
                    <li><a href="#tabs-2">Radiologi</a></li>
                </ul>
                <div id="tabs-1">
                    <table width="100%" class="inputan">
                        <?= form_hidden('id_dokter_lab', NULL, 'id=id_dokter_lab') ?>
                        <?= form_hidden('id_analis_lab') ?>
                        <?= form_hidden('id_layanan_lab') ?>
                        <?= form_hidden('id_satuan_lab') ?>
                        <?php $ket = array(''=>'Pilih...','L'=>'Low','N'=>'Netral', 'H'=>'High' ) ?>
                        <tr><td style="width: 150px;">Nama Dokter</td><td><?= form_input('',NULL,'id=dokter_lab size=40')?></td></tr>
                        <tr><td>Nama Analis Lab</td><td><?= form_input('','','id=analis_lab size=40')?></td></tr>
                        <tr><td>Waktu Order</td><td><?= form_input('', date('d/m/Y H:i'),'id=waktu_order_lab size=15')?></td></tr>
                        <tr><td>Waktu Hasil</td><td><?= form_input('', '','id=waktu_hasil_lab size=15')?></td></tr>
                        <tr><td>Layanan</td><td><?= form_input('','','id=layanan_lab size=40')?></td></tr>
                        <tr><td>Hasil</td><td><?= form_input('','','id=hasil_lab size=40')?></td></tr>
                        <tr><td>Keterangan</td><td><?= form_dropdown('', $ket ,null,'id=ket_lab')?></td></tr>
                        <tr><td>Satuan</td><td><?= form_input('','','id=satuan_lab size=40')?></td></tr>
                        <tr><td></td><td><?= form_button('' ,'Tambah (F4)' ,'id=fuckyeah')?> 
                        <?= form_button('','Cetak S.P. Laboratorium', 'class=print onclick=cetak_lab()') ?> 
                        <?= form_button('','Cetak Hasil Laboratorium', 'class=print onclick=cetak_hasil_lab()') ?> </td></tr>
                    </table>
                    <div class="data-list">
                        <table class="list-data" id="add_data_lab" width="100%">
                            <thead>
                            <tr>
                                <th width="15%">Nama Dokter</th>
                                <th width="15%">Nama Analis Lab</th>
                                <th width="10%">Waktu Order</th>
                                <th width="15%">Waktu Hasil</th>
                                <th width="15%">Layanan</th>
                                <th width="5%">Hasil</th>                
                                <th width="5%">Ket</th>
                                <th width="10%">Satuan</th>
                                <th width="5%">Aksi</th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

                <div class="data-input" id="tabs-2">
                    <table width="100%" class="inputan">
                        <tr><td style="width: 150px;">Nama Dokter:</td><td><?= form_input('dokter_rad', $this->session->userdata('nama'), 'size=40 id=dokter_rad class=input_rad') ?> <?= form_hidden('id_dokter_rad',$this->session->userdata('id_pegawai')) ?></td></tr>
                        <tr><td>Nama Radiografer:</td><td><?= form_input('radio_rad', NULL, 'size=40 id=radio_rad class=input_rad') ?> <?= form_hidden('id_radio_rad') ?></td></tr>
                        <tr><td>Waktu Order:</td><td><?= form_input('', date("d/m/Y H:i"), 'id=waktu_order_rad size=15') ?></td></tr>
                        <tr><td>Waktu Hasil:</td><td><?= form_input('', '', 'id=waktu_hasil_rad size=15') ?></td></tr>
                        <tr><td>Nama Layanan</td><td><?= form_input('layanan_rad', NULL, 'size=40 id=layanan_rad class=input_rad') ?> <?= form_hidden('id_layanan_rad') ?></td></tr>
                        <tr><td>kv:</td><td><?= form_input('','','id="kv_rad" class=input_rad onkeyup=Angka(this)') ?></td></tr>
                        <tr><td>ma:</td><td><?= form_input('','','id="ma_rad" class=input_rad onkeyup=Angka(this)') ?></td></tr>
                        <tr><td>s:</td><td><?= form_input('','','id="s_rad" class=input_rad onkeyup=Angka(this)') ?></td></tr>
                        <tr><td>p:</td><td><?= form_input('','','id="p_rad" class=input_rad onkeyup=Angka(this)') ?></td></tr>
                        <tr><td>fr:</td><td><?= form_input('','','id="fr_rad" class=input_rad') ?></td></tr>
                        <tr><td></td><td><?= form_button(NULL, 'Tambah (F7)', 'id=bt_rad') ?><?= form_button('','Cetak S.P. Radiologi', 'class=print onclick=cetak_radio()') ?> </td></tr>
                    </table>
                   
                    <div class="data-list">
                        <table class="list-data" id="add_data_rad" width="100%">
                            <thead>
                            <tr>
                                <th width="20%">Nama Dokter</th>
                                <th width="20%">Nama Radiografer</th>
                                <th width="15%">Nama Layanan</th>
                                <th width="15%">Waktu Order</th>
                                <th width="15%">Waktu Hasil</th>
                                <th width="5%">Hasil</th>
                                <th width="5%">#</th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
        
        <div id="tabs-utama-4">
            <?= form_button('', 'Tambah (F8)', 'id=add_diag class=tombol') ?>             
            <table class="list-data" id="diag_add" width="100%">
                <thead>
                    <tr>
                        <th width="10%">Waktu</th>
                        <th width="30%">Nama Dokter</th>
                        <th width="15%">Unit</th>
                        <th width="25%">Golongan Sebab Sakit</th>                
                        <th width="7%">ICDX</th>
                        <th width="10%">Kasus</th>
                        <th width="3%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (isset($diagnosis)) { 
                    foreach ($diagnosis as $key => $rows) {
                    ?>
                    <tr>
                        <td align="center"><?= datetime($rows->waktu) ?></td>
                        <td><?= $rows->nama_dokter ?></td>
                        <td><?= $rows->nama_unit ?></td>
                        <td><?= $rows->golongan_sebab ?></td>
                        <td align="center"><?= $rows->no_dtd ?></td>
                        <td align="center"><?= $rows->kasus ?></td>
                        <td align="center">-</td>
                    </tr>    
                <?php } 
                }else{ ?>
                    <script type="text/javascript">
                        $(function(){
                            //add(0);
                            //add(1);
                        });
                    </script>
                <?php } ?>
                </tbody>             
            </table>
        </div>
        
        <div id="tabs-utama-5">
            <?= form_button('', 'Tambah (F9)', 'id=add_tindak class=tombol') ?> <?= form_button('','Cetak S.P. Fisioterapi', 'class=print onclick=cetak_fisio()') ?>  
            <table class="list-data" id="tindak_add" width="100%">
                <thead>
                    <tr>
                        <th width="10%">Waktu</th>
                        <th width="20%">Nama Nakes</th>
                        <th width="20%">Nama Nakes Anestesi</th>
                        <th width="10%">Unit</th>
                        <th width="35%">Tindakan</th>
                        <th width="5%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($tindakan)) { 
                    foreach ($tindakan as $key => $rows) {
                        ?>
                        <tr>
                            <td align="center"><?= datetime($rows->waktu) ?></td>
                            <td><?= $rows->nama_ope ?></td>
                            <td><?= $rows->nama_anes ?></td>
                            <td><?= $rows->nama_unit ?></td>
                            <td><?= $rows->tindakan ?></td>
                            <td align="center">-</td>
                        </tr>    
                    <?php } 
                    } else{ ?>
                    <script type="text/javascript">
                        $(function(){
                            add_tindak(0);
                            add_tindak(1);
                        });
                    </script>
                <?php } ?>
                </tbody>             
            </table>
        <?= form_submit('','Simpan','id=simpan') ?>
        <?= form_button('','Cetak Surat Kontrol','id=bt_kontrol class=print onclick=cetak_surat_kontrol()') ?>
        <?= form_button('','Reset','id="reset" onClick=reset_all();')?>
        </div>
        
        
    </div>
    <?= form_close()?>

    <script type="text/javascript">
        $(function(){
            $('#form_rad_edit').dialog({
                autoOpen: false,
                height: 210,
                width: 300,
                title : 'Hasil Pemeriksaan Radiologi',
                modal: true,
                resizable : false,
                buttons: [ 
                      { text: "Ok", click: function() { $( this ).dialog( "close" );} } 
                  ]
            });
        });
    </script>

    <div id="form_rad_edit" style="display: none;position: static; background: #fff; padding: 10px;">
        <div class="data-input">            
            <table class="inputan" width="100%">
                <tr><td width="15%">kv:</td><td id="kv_rad_edit"></td></tr>
                <tr><td>ma:</td><td id="ma_rad_edit"></td></tr>
                <tr><td>s:</td><td id="s_rad_edit"></td></tr>
                <tr><td>p:</td><td id="p_rad_edit"></td></tr>
                <tr><td>fr:</td><td id="fr_rad_edit"></td></tr>
            </table>
        </div>
    </div>
</div>

<div id="rm">
    <div id="rekam_medis"></div>
</div>
