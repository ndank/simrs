<?php 
    $this->load->view('message'); 
    $this->load->helper('html');
?>
<title><?= $title ?></title>
<div class="titling"><h1><?= $title ?></h1></div>
<div class="kegiatan">
    <script type="text/javascript">
        $('#no_barcode').focus();
        var request;       
        var dWidth = $(window).width();
        var dHeight= $(window).height();
        var x = screen.width/2 - dWidth/2;
        var y = screen.height/2 - dHeight/2;

        $(function() {
            $('#fromdate, #todate').datepicker({
                changeYear: true,
                changeMonth: true
            });
            $('#tabs-utama').tabs();
            $('#waktu_vital, #waktu_order_lab, #waktu_hasil_lab, #waktu_order_rad, #waktu_hasil_rad, #waktu_pelayanan').datetimepicker({
                changeYear : true,
                changeMonth : true
            });
            $('#riwayat_rm').hide();
            $(document).unbind().on("keydown", function (e) {
                if (e.keyCode === 115) {
                    e.preventDefault();
                    $('#add_lab').click();
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

            $('#bt_diag, #bt_tindak, #add_diag, #add_tindak,.plus, #bt_rad, #bt_vital').button({icons: {secondary: 'ui-icon-circle-plus'}});
            $('button[type=submit], #save_vital, #simpan, #simpan_tindak, #simpan_diag, #simpan_lab, #simpan_rad').button({icons: {secondary: 'ui-icon-circle-check'}});
            $('.resetan, #reset_lab, #reset-search').button({icons: {secondary: 'ui-icon-refresh'}});
            $('.print').button({icons: {secondary: 'ui-icon-print'}});
            $('#cari_ava_bed').button({icons: {secondary: 'ui-icon-circle-check'}});

            $('#simpan').click(function(){
                $('#formrawatinap').submit();
            });
           
            $('#formrawatinap').submit(function(){
                var url = $(this).attr('action');

                if($('input[name=no_rm]').val() === ''){
                    custom_message('Peringatan', 'No. Rekam Medik tidak boleh kosong !', '#no');
                    $('#tabs-utama').tabs({ selected: 1});
                    return false;
                }
                if($('input[name=id_dpjp]').val() === ''){
                    custom_message('Peringatan','DPJP tidak boleh kosong !','#dpjp');
                    return false;
                }

                if($('#jenis_layanan').val() === ''){
                    custom_message('Peringatan','Jenis layanan belum dipilih !','#bangsal');
                    return false;
                }

                if($('input[name=id_tt]').val() === ''){
                    custom_message('Peringatan','Tempat tidur belum dipilih !','#bangsal');
                    return false;
                }

              
                if(!request) {
                    request = $.ajax({
                        type : 'POST',
                        url: url,
                        data:$(this).serialize(),
                        cache: false,
                        dataType: 'json',
                        success: function(data){
                            request = null;
                            if (data.status == true){
                                if($('input[name=id_pelayanan_kunjungan]').val() != ''){
                                    alert_edit();
                                }else{
                                    alert_tambah();
                                }
                                $('input[name=id_pelayanan_kunjungan]').val(data.id_pelayanan);
                                $('input[name=can_print]').val('print');
                                get_rawat_inap_list($('input[name=id_kunjungan]').val());
                            }else{
                                custom_message('Peringatan',"Gagal tambah data");
                            }
                            
                           return false;
                        }
                    });
                }

                return false;
            });

            $('#no_barcode').keydown(function(e){
                if ((e.keyCode == 13) && (IsNumeric($(this).val()))) {
                    $.ajax({
                        url: '<?= base_url("pelayanan/pasien_load_detail") ?>/'+$(this).val(),
                        data : $(this).serialize(),
                        cache: false,
                        dataType:'json',
                        success: function(data) {
                            if(data.length !== 0){
                                $('#msg_detail').html('');
                                $('input[name=no_rm], #no, #no_barcode').val(data.no_rm);
                                fill_field(data);
                                get_rawat_inap_list(data.no_daftar);
                                load_data_pelayanan(data.no_daftar);
                            }else{
                                custom_message('Peringatan!', 'No. Rekam Medik tidak ada atau pasien belum melakukan pendaftaran kunjungan !', '#no_barcode');
                                $('#no').val('');
                            }
                           
                        }
                    });
                    return false;
                };

            });

             $('#no').autocomplete("<?= base_url('pelayanan/pasien_load_data') ?>",
                {
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
                    formatItem: function(data,i,max){
                        if (data.no_daftar !== null) {
                            var str = '<div class=result>'+data.no_rm+' - '+data.nama+'<br/>'+data.alamat+'</div>';
                        }
                        return str;
                    },
                    width: 370, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                    dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
                }).result(
                function(event,data,formated){
                    $(this).val(data.no_rm);
                    $('input[name=no_rm]').val(data.no_rm);
                    get_rawat_inap_list(data.no_daftar);
                    load_data_pelayanan(data.no_daftar);
                });

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
                    width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                    dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
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
                    var str = '<div class=result>'+data.nama+'</div>';
                    return str;
                },
                width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
            }).result(
            function(event,data,formated){
                $(this).val(data.nama);
                $('input[name=id_dpjp]').val(data.id);
                $('input[name=id_jurusan]').val(data.id_jurusan_kualifikasi_pendidikan);
                $('#dpjp_diag, #dpjp_tindak').html(data.nama);
            });            
        
        });



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
                    height: $(window).height(),
                    width: $(window).width(),
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
                    type : 'GET',
                    cache: false,
                    success: function(data) {
                        $('#rekam_medis').html(data);
                        $('#rm').dialog('open');
                    }
                }); 
            }
        }


        function removeRow(kelas){
            $(kelas+' tbody').empty();
        }

        function fill_field(id_pk, jenis){
            //reset_all_data();
            $.ajax({
                url: '<?= base_url('inv_autocomplete/get_data_dinamis_penduduk') ?>/'+id_pk,
                dataType: 'json',
                success: function(data) {
                    $('#no, #no_barcode, input[name=no_rm]').val(data.no_rm);
                    $('#riwayat_rm').show();
                    $("input[name=id_kunjungan]").val(data.no_daftar);
                    $("input[name=id_pelayanan_kunjungan]").val(''); // diempty dulu PK nya
                    if (jenis === 'Rawat Inap') {
                        $("input[name=id_pelayanan_kunjungan]").val(id_pk);
                    }
                    $("#norm_diag, #norm_tindak, #norm_lab, #norm_rad, #norm_vital").html(data.no_rm);
                    $("#nama").html(data.nama);
                    $("#nama, #nama_diag, #nama_tindak, #nama_lab, #nama_lab, #nama_rad, #nama_vital").html(data.nama);
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
                    get_rawat_inap_list(data.no_daftar);
                }
            });
        }

        function edit_pelayanan_irna(id){
            $('#no').focus();
            $.ajax({
                url: '<?= base_url("pelayanan/detail_pelayanan_kunjungan") ?>/'+id,
                cache: false,
                dataType : 'json',
                success: function(data) {
                    
                    if (!jQuery.isEmptyObject(data)) {
                        var waktu = '';
                        if(data.waktu != ''){
                            waktu = datetimefmysql(data.waktu);
                        }

                        $('#waktu_pelayanan').val(waktu);
                        $('input[name=id_pelayanan_kunjungan]').val(data.id);
                        $('input[name=can_print]').val('print');
                        $('#dpjp').val(data.nama);
                        $('input[name=id_dpjp]').val(data.id_kepegawaian_dpjp);
                        $('input[name=id_bangsal]').val(data.id_unit);
                        $('input[name=kelas]').val(data.kelas);
                        $('#no_polis').val(data.no_polis);
                        $('input[name=id_tt]').val(data.no_tt);
                        $('#asuransi').val(data.asuransi);
                        $('input[name=id_asuransi]').val(data.id_produk_asuransi);
                        $('input[name=id_jurusan]').val(data.id_jurusan_kualifikasi_pendidikan);
                        $('#anamnesis').val(data.anamnesis);
                        $('#pemeriksaan').val(data.pemeriksaan_umum);
                        $('#jenis_layanan').val(data.id_jurusan_kualifikasi_pendidikan);

                        $('#no_bed').html(data.unit+' '+data.kelas+' '+data.nomor_bed);
                    }
                }
            });
        }

        function get_rawat_inap_list(id){
            $.ajax({
                type : 'GET',
                url: '<?= base_url("pelayanan/rawat_inap_list") ?>/'+id, 
                cache: false,
                success: function(data) {
                    $('#inap_list').html(data);
                }
            });
        }

        function load_data_pelayanan(no_kunjungan){
            $.ajax({
                url: '<?= base_url("pelayanan/load_data_pelayanan_kunjungan") ?>/'+no_kunjungan,
                cache: false,
                dataType : 'json',
                success: function(data) {
                    if (!jQuery.isEmptyObject(data)) {
                        $('#asuransi').val(data.produk_asuransi);
                        fill_field(data.id_pelayanan_kunjungan);
                        $('input[name=id_asuransi]').val(data.id_produk_asuransi);
                        $('#no_polis').val(data.no_polis);
                        $('#dpjp').val(data.nama_dpjp);
                        $('input[name=id_dpjp]').val(data.id_dpjp);
                        $('input[name=id_jurusan]').val(data.id_jurusan_kualifikasi_pendidikan);
                        $('#jenis_layanan').val(data.id_jurusan_kualifikasi_pendidikan);
                        
                        $('#anamnesis').val(data.anamnesis);
                        $('#pemeriksaan').val(data.pemeriksaan);

                
                    }
                }
            });
        }

        function reset_all(){
            $('#loaddata').empty();
            $('#loaddata').load('<?= base_url("pelayanan/rawat_inap") ?>');
        }

        function add_diagnosis(id){
            $.ajax({
                type : 'GET',
                url: '<?= base_url('pelayanan/inap_kunjungan_get_data') ?>/'+id, 
                cache: false,
                dataType: 'json',
                success: function(data) {
                    $('#diag_add tbody').empty();
                    var act = $("#form_diag").attr('action');
                    $("#form_diag").attr('action', act+'/'+data.inap.id);
                    $('#bangsal_diag').html(data.inap.nama_unit);
                    $('#kelas_diag').html(data.inap.kelas);
                    $('#nott_diag').html(data.inap.no_tt);
                    $('#dpjp_diag').html(data.inap.nama_pegawai);
                    $('#asu_diag').html(data.inap.nama_asuransi);                    

                    add_diagnosis_value(data.diagnosis);
                    if ($('.row_diag_value').length < 1) {
                        add(0);
                        add(1);
                    };
                    
                }
            });
            
        }

        function cetak_ic_inap(){
            if($('input[name=id_kunjungan]').val() == ''){
                custom_message('Peringatan','No RM harus diisi !','#no');
            }else if($('input[name=can_print]').val() == ""){
                custom_message('Peringatan','Data rawat inap belum disimpan atau <br/>pilih terlebih dahulu daftar pelayanan rawat inap di bawah !','#no');
            }else{
                window.open('<?= base_url("pelayanan/ic_persetujuan_rawat_inap/") ?>/'+$('input[name=id_pelayanan_kunjungan]').val(), 'mywindow', 'width='+dWidth+', height='+dHeight+', left='+x+',top='+y);
            }
        }

        function cetak_gelang(){
            if($('input[name=id_kunjungan]').val() == ''){
                custom_message('Peringatan', 'No RM harus diisi !', '#no');
            }else{
                window.open('<?= base_url("pelayanan/cetak_gelang_rawat_inap/") ?>/'+$('#no').val(), 'mywindow', 'width='+dWidth+', height='+dHeight+', left='+x+',top='+y);
            }
        }

        function diagnosis(id){
            add_diagnosis(id);
            $('#diag_add').hide();
            $('#form_diagnosis').dialog('open');
        }

        function tindakan(id){
            add_tindakan(id);
            $('#form_tindakan').dialog('open');
        }

        function add_diagnosis_value(data) {
            var str ='';
            $('#diag_add').hide();
            $.each(data, function(i, v){
                str = '<tr class="row_diag_value">'+
                '<td>'+datetimefmysql(v.waktu)+'</td>'+
                '<td>'+(v.nama_dokter!=null?v.nama_dokter:'')+'</td>'+
                '<td>'+v.nama_unit+'</td>'+ 
                '<td>'+(v.golongan_sebab == null?' ':v.golongan_sebab)+'</td>'+
                '<td>'+(v.no_daftar_terperinci == null?' ':v.no_daftar_terperinci)+'</td>'+
                '<td align="center">'+v.kasus+'</td>'+
                '<td class=aksi align=center><a class="deletion" onClick="delete_diagnosis('+v.id+', this)"></a></td></tr>';      
                $('#diag_add tbody').append(str);
            });
            $('#diag_add').fadeIn('slow');                
        }

        function add_tindakan_value(data){
            var str ='';
            $('#tindak_add').hide();
            $.each(data, function(i, v){
                str = '<tr class="row_tindak_value">'+
                    '<td align=center>'+datetimefmysql(v.waktu)+'</td>'+
                    '<td>'+(v.nama_ope == null?' ':v.nama_ope)+'</td>'+
                    '<td>'+(v.nama_anes==null?' ':v.nama_anes)+'</td>'+
                    '<td>'+v.nama_unit+'</td>'+
                    '<td>'+(v.tindakan==null?' ':v.tindakan)+'</td>'+
                    '<td class=aksi align=center><a class="deletion" onClick="delete_tindakan('+v.id+', this)"></a></td></tr>';      
                $('#tindak_add tbody').append(str);
            });

            $('#tindak_add').fadeIn('slow');
        }

        function add_tindakan(id){
             $.ajax({
                type : 'GET',
                url: '<?= base_url('pelayanan/inap_kunjungan_get_data') ?>/'+id, 
                cache: false,
                dataType: 'json',
                success: function(data) {
                    $('#tindak_add tbody').empty();
                    var act = $("#form_tindak").attr('action');
                    $("#form_tindak").attr('action', act+'/'+data.inap.id);
                    $('#bangsal_tindak').html(data.inap.nama_unit);
                    $('#kelas_tindak').html(data.inap.kelas);
                    $('#nott_tindak').html(data.inap.no_tt);
                    $('#dpjp_tindak').html(data.inap.nama_pegawai);
                    $('#asu_tindak').html(data.inap.nama_asuransi);
                    $('input[name=id_pk_tindak]').val(id);
                    add_tindakan_value(data.tindakan);

                    if ($('.row_tindak_value').length < 1) {
                        add_tindak(0);
                        add_tindak(1);
                    };
                }
            });
        }

        function cetak_lembar_rm(){
            var id = $('input[name=id_pelayanan_kunjungan]').val();
            if(id != ''){
                var url = '<?= base_url("pelayanan/cetak_lembar_rm_inap")?>/'+id+'/'+$('input[name=id_kunjungan]').val();
                window.open(url, 'Lembar Rawat Inap', 'width='+dWidth+', height='+dHeight+', left='+x+',top='+y);
            }else{
                custom_message('Peringatan', 'Belum melakukan entry data pelayanan kunjungan!');
            }
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
        
        function get_pendaftar_list(p) {
            $.ajax({
                type : 'GET',
                url: '<?= base_url("pendaftaran/list_data_pendaftar_ranap") ?>/'+p,
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

        function pemeriksaan(id_pk, jenis) {
            $('#tabs-utama').tabs({ selected: 1});
            fill_field(id_pk, jenis);
            //edit_pelayanan_irna(id_pk);
        }
    </script>

        <?= form_open('pelayanan/rawat_inap_save','id=formrawatinap')?>
        <?= form_hidden('can_print',isset($print)?$print:null) ?>
        <div id="tabs-utama">
            <ul>
                <li><a href="#tabs-utama-0">Data Pasien</a></li>
                <li><a href="#tabs-utama-1">Data Pendaftaran</a></li>
                <li><a href="#tabs-utama-2">Pemeriksaan</a></li>
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
                    <?= form_hidden('id_kunjungan',isset($data->id_kunjungan)?$data->id_kunjungan:NULL) ?>
                    <?= form_hidden('id_jurusan',isset($data->id_jurusan_kualifikasi_pendidikan)?$data->id_jurusan_kualifikasi_pendidikan:NULL) ?>
                    <?= form_hidden('id_pelayanan_kunjungan',isset($data->id)?$data->id:NULL) ?>
                    <?= form_hidden('waktu_keluar',isset($data->waktu_keluar)?$data->waktu_keluar:NULL) ?>
                    <table width="100%" class="inputan">
                        <tr><td style="width: 150px;">No. RM (Barcode):</td><td><?= form_input('', '', 'id=no_barcode size=40') ?></td></tr>
                        <tr><td>No. RM:</td><td><?= form_input('',isset($data->no_rm)?$data->no_rm:NULL,'id=no size=40')?></td></tr>
                        <tr><td>Nama Pasien:</td><td id="nama"><?= isset($data)?$data->pasien:NULL ?></td></tr>
                        <tr><td></td><td><span id="riwayat_rm" class="link_button" onclick="detail_rm()">Riwayat Rekam Medis</span></td></tr>
                        <tr><td>Alamat Jalan:</td><td id="alamat"><?= isset($data)?$data->alamat:NULL ?></td></tr>
                        <tr><td>Wilayah:</td><td id="wilayah"><?= isset($data)?$data->kelurahan." ".$data->kecamatan:NULL ?></td></tr>
                        <tr><td>Gender:</td><td id="gender"><?= isset($data)?(($data->gender=="L")?"Laki-Laki":"Perempuan"):NULL ?></td></tr>
                        <tr><td>Umur:</td><td id="umur"><?= isset($data)?hitungUmur($data->lahir_tanggal):NULL ?></td></tr>
                        <tr><td>Pekerjaan:</td><td id="pekerjaan"><?= isset($data)?$data->pekerjaan:NULL ?></td></tr>
                        <tr><td>Pendidikan:</td><td id="pendidikan"><?= isset($data)?$data->pendidikan:NULL ?></td></tr>
                        <tr><td colspan="2"><h2>Rujukan</h2></td></tr>
                        <tr><td>Waktu Datang:</td><td id="waktu_datang"><?= isset($rujuk)?datetime($rujuk->arrive_time):NULL ?><?= isset($data->arrive_time)?datetime($data->arrive_time):NULL ?></td></tr>
                        <tr><td>Instansi Kesehatan:</td><td id="ins_kes"><?= isset($rujuk)?$rujuk->rujukan:NULL ?><?= isset($data->instansi_rujukan)?$data->instansi_rujukan:NULL ?></td></tr>
                        <tr><td>Nama Nakes:</td><td id="nakes"><?= isset($rujuk)?$rujuk->nakes:NULL ?><?= isset($data->nama_nakes)?$data->nama_nakes:NULL ?></td></tr>
                        <tr><td colspan="2"><h2>Penanggung Jawab</h2></td></tr>
                        <tr><td>Nama:</td><td id="nama_pj"><?= isset($pjwb)?$pjwb->penanggung_jawab:(isset($data->nama_pj)?$data->nama_pj:NULL ) ?></td></tr>
                        <tr><td>Alamat Jalan:</td><td id="alamat_pj"><?= isset($pjwb)?$pjwb->alamat:(isset($data->alamat_pj)?$data->alamat_pj:NULL) ?></td></tr>
                        <tr><td>Wilayah:</td><td id="wilayah_pj"><?= isset($pjwb)?$pjwb->kelurahan." ".$pjwb->kecamatan:NULL ?><?= (isset($data->kelurahan_pj)&(isset($data->kecamatan_pj)))?$data->kelurahan_pj." ".$data->kecamatan_pj:NULL ?></td></tr>
                        <tr><td>No. Telp:</td><td id="telp_pj"><?= isset($pjwb)?$pjwb->telp:NULL ?><?= isset($data->telp_pj)?$data->telp_pj:NULL ?></td></tr>
                    </table>
            </div>

            <div id="tabs-utama-2">
                <table width="100%" cellpadding="0" cellspacing="0"><tr valign="top"><td width="50%">
                <table width="100%" class="inputan" >
                    <tr><td style="width: 150px;">Waktu:</td><td><?= form_input('waktu', date('d/m/Y H:i'), 'id=waktu_pelayanan size=18') ?></td></tr>
                    <tr><td>Dokter Penanggung Jwb:</td><td>
                    <?= form_input('',isset($data->nama)?$data->nama:$this->session->userdata('nama'),'id=dpjp size=40')?>
                    <?= form_hidden('id_dpjp',isset($data->id_kepegawaian_dpjp)?$data->id_kepegawaian_dpjp:$this->session->userdata('id_pegawai'))?></td></tr>
                    <tr><td>Jenis Layanan:</td><td><?= form_dropdown('id_layanan', $layanan, null, 'id=jenis_layanan class=standar') ?></td></tr>


                    <tr><td>No. Bed:</td><td><span id="no_bed"></span></td></tr>
                    <tr><td></td><td><span style="cursor: pointer;" class="link_button" id="bt_ava_bed" title="Klik untuk memilih bed yg tersedia"><u>Daftar Bed Tersedia</u></span></td></tr>
                    <tr><td></td><td>
                        <?= form_button('','Simpan','id=simpan') ?>
                        <!--<?= form_button('','Cetak Gelang','id=bt_gelang class=print onclick=cetak_gelang()') ?>
                        <?= form_button('','Cetak Inform Concent','id=bt_ic class=print onclick=cetak_ic_inap()') ?>
                        <?= form_button('','Cetak Lembar RM','id=bt_rm class=print onclick=cetak_lembar_rm()') ?>
                        <?= form_button('','Cetak Surat Kontrol','id=bt_kontrol class=print onclick=cetak_surat_kontrol()') ?>-->
                        <?= form_button('','Reset','id=reset class=resetan onClick=reset_all()')?>
                    </td></tr>
                </table>
                <?= form_hidden('id_bangsal',isset($data->id_unit)?$data->id_unit:NULL) ?>
                <?= form_hidden('kelas') ?>
                <?= form_hidden('id_tt') ?>
                <?= form_hidden('id_tarif') ?>
                </td><td width="50%">
                <table width="100%" class="inputan">
                    <tr><td width="30%">Nama Asuransi:</td><td>
                        <?= form_input('',isset($data->asuransi)?$data->asuransi:NULL,'id=asuransi class=input-text size=40')?> 
                    <tr><td>No. Polish Asuransi:</td><td> <?= form_input('no_polis',isset($data->no_polis)?$data->no_polis:NULL,'id=no_polis class=input-text size=40')?>
                    <?= form_hidden('id_asuransi',isset($data->id_produk_asuransi)?$data->id_produk_asuransi:NULL)?></td></tr>

                    <tr><td valign="top" width="15%">Anamnesis:</td><td><?= form_textarea('anamnesis',isset($data->anamnesis)?$data->anamnesis:'','id=anamnesis class=standar row=3')?></td></tr>
                    <tr><td valign="top">Pemeriksaan Umum:</td><td><?= form_textarea('pemeriksaan',isset($data->pemeriksaan)?$data->pemeriksaan:'','id=pemeriksaan class=standar row=3')?></td></tr>
                </table>
                </td></tr></table>
                <div id="inap_list">
                    <?php
                        if (isset($inap_list)) {
                            $inap['inap'] = $inap_list; 
                            $this->load->view('pelayanan/inap_list', $inap);
                        }
                     ?>
                </div>
            </div>
        </div>
        <?= form_close()?>
    <script type="text/javascript">
        $(function(){
            $('#form_ava_bed').dialog({
                autoOpen: false,
                height: 500,
                width: 600,
                title : 'Daftar Bed Tersedia',
                modal: true,
                resizable : false,
                close : function(){

                }
            });

            $('#bt_ava_bed').click(function(){
                if($('input[name=no_rm]').val() !== ''){
                    get_available_bed();
                }else{
                    custom_message('Peringatan', 'Anda belum memasukkan data pasien !', '#no');
                }
            });

             $('#bangsal_cari').autocomplete("<?= base_url('rawatinap/get_data_unit') ?>",
            {
                parse: function(data){
                    var parsed = [];
                    for (var i=0; i < data.length; i++) {
                        parsed[i] = {
                            data: data[i],
                            value: data[i].nama// nama field yang dicari
                        };
                    }
                    $('input[name=id_bangsal_cari]').val('');
                    return parsed;
                },
                formatItem: function(data,i,max){
                    var str = '<div class=result>'+data.nama +'</div>';
                    return str;
                },
                width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
            }).result(
            function(event,data,formated){
                $(this).val(data.nama);
                $('input[name=id_bangsal_cari]').val(data.id);

            });
        });

        function get_available_bed(){
            $.ajax({
                url: '<?= base_url("pelayanan/get_available_bed")?>',
                data: $('#formavabed').serialize(),
                cache: false,
                success: function(data){
                    $('#list-data_ava_bed').html(data);
                    $('#form_ava_bed').dialog('open');
                   return false;
                }
            });
        }

        function reset_ava_cari(){
            $('input[name=id_bangsal_cari], #bangsal_cari, #kelas_cari').val('');
            get_available_bed();
        }

        function pilih_bed(id, nama, kelas, no, id_tarif, id_unit){
            $('input[name=id_tt]').val(id);
            $('input[name=id_bangsal]').val(id_unit);
            $('input[name=kelas]').val(kelas);
            $('input[name=id_tarif]').val(id_tarif);
            $('#no_bed').html(nama+' '+kelas+' '+no);
            $('#form_ava_bed').dialog('close');
        }
    </script>

    <div id="form_ava_bed" style="display: none;position: static; background: #fff; padding: 10px;">
        <?= form_open('pelayanan/get_available_bed','id=formavabed')?>
        <table width="100%" class="inputan">
            <?= form_hidden('id_bangsal_cari') ?>
            <tr><td style="width: 150px;">Bangsal:</td><td><?= form_input('bangsal','','id=bangsal_cari size=34') ?></td></tr>
            <tr><td>Kelas:</td><td><?= form_dropdown('kelas',$kelas, array(), 'id=kelas_cari') ?></td></tr>
            <tr><td></td><td><?= form_button('', 'Cari', 'id=cari_ava_bed onclick=get_available_bed()') ?> <?= form_button('','Reset','class=resetan onclick=reset_ava_cari()') ?></td></tr>
        </table>
        <?= form_close() ?>
        <div id="list-data_ava_bed"></div>
    </div>
    
    <script type="text/javascript">
        var request_diag;
        $(function(){
            
            $('#form_diagnosis').dialog({
                autoOpen: false,
                height: $(window).height(),
                width: $(window).width(),
                title : 'Diagnosis Rawat Inap',
                modal: true,
                resizable : false,
                close : function(){
                    removeRow('.diag_add');
                },
                open : function(){
                    
                }
            });

             $('#add_diag').click(function() {
                var rows = $('.row_diag').length;
                add(rows);                
            });


            $('#bt_diag').click(function(){
                if($('input[name=no_rm]').val() != '' ){
                    $('#form_diagnosis').dialog('open');    
                }else{
                    custom_message('Peringatan','No RM harus diisi !','#no');
                }                
            });

            $('#simpan_diag').click(function(){
                $('#form_diag').submit();
            });

            $('#form_diag').submit(function(){
                var url = $(this).attr('action');
                if($('.row_diag').length > 0){
                     var rows = $('.row_diag').length-1;
                     for (var i = 0; i < rows; i++) {
                        if($('#waktu_diag'+i).val() == ""){
                            custom_message('Peringatan','Waktu tidak boleh kosong !','#waktu_diag'+i);
                            return false;
                        }                        
                     }
                }
                if(!request_diag) {
                    request_diag = $.ajax({
                        type : 'POST',
                        url: url,
                        data:$(this).serialize(),
                        cache: false,
                        dataType: 'json',
                        success: function(msg){
                            request_diag = null;
                            if (msg.status == true){
                                add_diagnosis(msg.id_pelayanan);
                                alert_tambah();
                            }else{
                                custom_message('Peringatan',"Gagal tambah data");
                            }
                            
                           return false;
                        }
                    });
                }

                return false;
            });
        });


        function add(i) {
                str = '<tr class="row_diag">'+
                    '<td><input type="text" name="waktu_diag[]" id=waktu_diag'+i+' value="<?= date('d/m/Y H:i') ?>" /></td>'+
                    '<td><input type="text" name="dok_diag" id=dokter_diag'+i+' /><input type="hidden" name="dokter_diag[]" id=id_dokter_diag'+i+' /></td>'+
                    '<td><select name="unit_diag[]" style="width:100%" id="unit_diag'+i+'"><option value="">Pilih</option></select></td>'+ 
                    '<td><input type="text" name="sbb_diag"'+i+' id=sebab_diag'+i+' /><input type="hidden" name="sebab_diag[]" id=id_sebab_diag'+i+' /></td>'+
                    '<td><input type="text" name="icd_diag[]" id=icd_diag'+i+' /></td>'+
                    '<td align="center"><span id="kasus_diag'+i+'"></span><input type="hidden" name="kasus[]" id="val_kasus_diag'+i+'" /></td>'+
                    '<td class=aksi align=center><a class="deletion" onClick="eliminate(this)"></a></td></tr>';      
                $('#diag_add tbody').append(str);
                $('#waktu_diag'+i).datetimepicker({
                    changeYear : true,
                    changeMonth : true
                });
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
                            var str = '<div class=result>'+data.nama+'</div>';
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
                                $('#unit_diag'+i).append('<option value="'+val.id+'" '+((val.id === '<?= $this->session->userdata('id_unit') ?>')?'selected':null)+'>'+val.nama+'</option>');
                            });
                        }
                    });

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
                        dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
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

    </script>
    <div id="form_diagnosis" style="display: none;position: static; background: #fff; padding: 10px;">
        <div class="data-input">
        <?= form_open('pelayanan/diagnosis_rawat_inap', 'id=form_diag') ?>
            <table width="100%" class="inputan">
                <tr><td>No. RM:</td><td class="label" id="norm_diag"></td></tr>
                <tr><td>Nama Pasien:</td><td class="label" id="nama_diag"></td></tr>
                <tr><td>Bangsal:</td><td class="label" id="bangsal_diag">fds</td></tr>
                <tr><td>Kelas:</td><td class="label" id="kelas_diag"></td></tr>
                <tr><td>No. Bed:</td><td class="label" id="nott_diag"></td></tr>
                <tr><td>Dokter Penanggung Jwb:</td><td class="label" id="dpjp_diag"></td></tr>
                <tr><td>Produk Asuransi:</td><td class="label" id="asu_diag"></td></tr>
                <tr><td><?= form_button('', 'Tambah (F8)', 'id=add_diag class=tombol') ?></td><td></td></tr>
            </table>
           
                <div class="data-list">
                    <table class="list-data" id="diag_add" width="100%">
                        <thead>
                            <tr>
                                <th width="15%">Waktu</th>
                                <th width="20%">Nama Dokter</th>
                                <th width="15%">Unit</th>
                                <th width="25%">Diagnosis</th>                
                                <th width="10%">ICDX</th>
                                <th width="10%">Kasus</th>
                                <th width="5%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>             
                    </table>
                </div>
      
            <?= form_button('','Simpan','id=simpan_diag')?>
        <?= form_close() ?>
        </div>
    </div>


    <script type="text/javascript">
        var request_tindak;
        $(function(){
            $('#form_tindakan').dialog({
                autoOpen: false,
                height: $(window).height(),
                width: $(window).width(),
                title : 'Tindakan Rawat Inap',
                modal: true,
                resizable : false,
                close : function(){
                    removeRow('.tindak_add');
                },
                open : function(){
                    
                }
            });

             $('#add_tindak').click(function() {
                var rows = $('.row_tindak').length;
                add_tindak(rows);                
            });
            
            $('#simpan_tindak').click(function() {
                $('#form_tindak').submit();             
            });

            $('#form_tindak').submit(function(){
                var url = $(this).attr('action');
                if($('.row_tindak').length > 0){
                     var rows = $('.row_tindak').length;
                     for (var i = 0; i < rows; i++) {
                        if($('#waktu_tindak'+i).val() == ""){
                            custom_message('Peringatan','Waktu tidak boleh kosong !','#waktu_tindak'+i);
                            return false;
                        }
                    
                        if($('#tindakan_tindak'+i).val() == ""){
                            custom_message('Peringatan','Tindakan tidak boleh kosong !','#tindakan_tindak'+i);
                            return false;
                        }                        
                     }
                }
                
                if(!request_tindak) {
                    request_tindak = $.ajax({
                        type : 'POST',
                        url: url+'/'+$('input[name=id_kunjungan]').val(),
                        data:$(this).serialize(),
                        cache: false,
                        dataType: 'json',
                        success: function(msg){
                            request_tindak = null;
                            if (msg.status == true){
                                add_tindakan(msg.id_pelayanan);
                                alert_tambah();
                            }else{
                                custom_message('Peringatan',"Gagal tambah data");
                            }
                            
                           return false;
                        }
                    });
                }

                return false;
            });
        });

        function cetak_ic(id_tindakan, baris){
            if($('input[name=no_rm]').val() != ''){
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
            }else{
                custom_message('Peringatan','No. Rekam Medik tidak boleh kosong !','#no');
                $('#tabs-utama').tabs({ selected: 1});
                return false;
                    
            }
            
        }

        function print_informed_consent(val, id_kunjungan, id_tindakan, baris) {
            var url = '';
            var operator = '';
            var anestesi = '';
            if (val === 'setuju') {
                url = '<?= base_url('pelayanan/ic_persetujuan_tindakan') ?>/'+id_kunjungan+'/'+id_tindakan;
                if(id_tindakan == 'null'){
                    operator = $('#nakes_tindak'+baris).val();
                    anestesi = $('#anes_tindak'+baris).val();
                    url += '?operator='+operator+'&anestesi='+anestesi;
                }
                window.open(url, 'form persetujuan', 'width='+dWidth+', height='+dHeight+', left='+x+',top='+y);
            }
            if (val === 'tolak') {
                url = '<?= base_url('pelayanan/ic_penolakan_tindakan') ?>/'+id_kunjungan+'/'+id_tindakan;
                if(id_tindakan == 'null'){
                    operator = $('#nakes_tindak'+baris).val();
                    anestesi = $('#anes_tindak'+baris).val();
                    url += '?operator='+operator+'&anestesi='+anestesi;
                }
                window.open(url, 'form penolakan tindakan', 'width='+dWidth+', height='+dHeight+', left='+x+',top='+y);
            }
            if (val === 'pasien_tidak_sadar') {
                url = '<?= base_url('pelayanan/ic_pasien_tidak_sadar') ?>/'+id_kunjungan+'/'+id_tindakan;
                if(id_tindakan == 'null'){
                    operator = $('#nakes_tindak'+baris).val();
                    url += '?operator='+operator;
                }
                window.open(url, 'form pk pasien tidak sadar', 'width='+dWidth+', height='+dHeight+', left='+x+',top='+y);
            }
            if (val === 'penghentian_tindakan') {
                url = '<?= base_url('pelayanan/ic_penghentian_tindakan') ?>/'+id_kunjungan+'/'+id_tindakan;
                if(id_tindakan == 'null'){
                    operator = $('#nakes_tindak'+baris).val();
                    url += '?operator='+operator;
                }
                window.open(url, 'form penghentian tindakan', 'width='+dWidth+', height='+dHeight+', left='+x+',top='+y);
            }
            if (val === 'setuju_rawat_inap') {
                window.open('<?= base_url("pelayanan/ic_persetujuan_rawat_inap") ?>/'+id_kunjungan, 'form persetujuan rawat inap', 'width='+dWidth+', height='+dHeight+', left='+x+',top='+y);
            }
            if (val === 'setuju_tindakan_sterilisasi') {
                window.open('<?= base_url("pelayanan/ic_persetujuan_tindakan_sterilisasi") ?>/'+id_kunjungan, 'form tindakan sterilisasi', 'width='+dWidth+', height='+dHeight+', left='+x+',top='+y);
            }
            
        }

        function cetak_fisio(){
            window.open('<?= base_url("laboratorium/cetak_sp_pemeriksaan_fisioterapi") ?>/'+ $('input[name=id_pk_tindak]').val(), 'mywindow', 'width='+dWidth+', height='+dHeight+', left='+x+',top='+y);
        }

        function add_tindak(i) {
            str = '<tr class="row_tindak">'+
                '<td><input type="text" name="waktu_tindak[]" id=waktu_tindak'+i+' value="<?= date('d/m/Y H:i') ?>" /></td>'+
                '<td><input type="text" name="nakes_tindak[]" id=nakes_tindak'+i+' /><input type="hidden" name="id_nakes_tindak[]" id=id_nakes_tindak'+i+' /></td>'+
                '<td><input type="text" name="anes_tindak[]" id=anes_tindak'+i+' /><input type="hidden" name="id_anes_tindak[]" id=id_anes_tindak'+i+' /></td>'+
                '<td><select name="unit_tindak[]" style="width:100%" id="unit_tindak'+i+'"><option value="">Pilih</option></select></td>'+ 
                '<td><input type="text" name="tindakan_tindak[]" id=tindakan_tindak'+i+' /><input type="hidden" name="tindakan[]" id=id_tindakan'+i+' /><input type="hidden" name="id_tarif[]" id="id_tarif'+i+'" /></td>'+
                '<td class=aksi align=center><a class="deletion" onClick="eliminate(this)"></a></td></tr>';      
            $('#tindak_add tbody').append(str);
            $('#waktu_tindak'+i).datetimepicker({
                changeYear : true,
                changeMonth : true
            });

            $('#ic'+i).click(function(){
                cetak_ic('null', i);
            });
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
                        var str = '<div class=result>'+data.nama+'</div>';
                        return str;
                    },
                    width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                    dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
                }).result(
                function(event,data,formated){
                    $(this).val(data.nama);
                    $('#id_nakes_tindak'+i).val(data.id);
                });

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
                        var str = '<div class=result>'+data.nama+'</div>';add_tindakan
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
                            $('#unit_tindak'+i).append('<option value="'+val.id+'" '+((val.id === '<?= $this->session->userdata('id_unit') ?>')?'selected':null)+'>'+val.nama+'</option>');
                        });
                    }
                });           
            
        }

    </script>


    <div id="form_tindakan" style="display: none;position: static; background: #fff; padding: 10px;">
        <div class="data-input">
        <?= form_open('pelayanan/tindakan_rawat_inap', 'id=form_tindak') ?>
        <?= form_hidden('id_pk_tindak')?>
            <table width="100%" class="inputan">
                <tr><td>No. RM:</td><td id="norm_tindak"></td></tr>
                <tr><td>Nama Pasien:</td><td id="nama_tindak"></td></tr>
                <tr><td>Bangsal:</td><td id="bangsal_tindak"></td></tr>
                <tr><td>Kelas:</td><td id="kelas_tindak"></td></tr>
                <tr><td>No. Bed:</td><td id="nott_tindak"></td></tr>
                <tr><td>Dokter Penanggung Jwb:</td><td id="dpjp_tindak"></td></tr>
                <tr><td>Produk Asuransi:</td><td id="asu_tindak"></td></tr>
                <tr><td></td><td><?= form_button('', 'Tambah (F9)', 'id=add_tindak class=tombol') ?><?= form_button('','Cetak S.P. Fisioterapi', 'class=print onclick=cetak_fisio()') ?></td></tr>
            </table>
                <div class="data-list">
                    <table class="list-data" id="tindak_add" width="100%">
                        <thead>
                            <tr>
                                <th width="10%">Waktu</th>
                                <th width="18%">Nama Nakes</th>
                                <th width="18%">Nama Nakes Anestesi</th>
                                <th width="15%">Unit</th>
                                <th width="26%">Tarif</th>
                                <th width="2%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>            
                    </table>
                </div>
                <table><tr><td><?= form_button('','Simpan','id=simpan_tindak')?> </td></tr></table>
           
        <?= form_close() ?>
        </div>
    </div>
   
    <div id="form_laboratorium" style="display: none;position: static; background: #fff; padding: 10px;">
        <div class="data-input">
        <?= form_open('laboratorium/pemeriksaan_lab_add', 'id=formlab') ?>
            <table width="100%" class="inputan">
                <tr><td>No. RM:</td><td id="norm_lab"></td></tr>
                <tr><td>Nama Pasien:</td><td id="nama_lab"></td></tr>
                <tr><td>Bangsal:</td><td id="bangsal_lab"></td></tr>
                <tr><td>Kelas:</td><td id="kelas_lab"></td></tr>
                <tr><td>No. Bed:</td><td id="nott_lab"></td></tr>
                <tr><td>Dokter Penanggung Jwb:</td><td id="dpjp_lab"></td></tr>
                <tr><td>Produk Asuransi:</td><td id="asu_lab"></td></tr>
            </table>
            <table width="100%" class="inputan">
                <div class="msg" id="msg_lab"></div>
                <?= form_hidden('id_pk_lab') ?>
                <?= form_hidden('id_dokter_lab') ?>
                <?= form_hidden('id_analis_lab') ?>
                <?= form_hidden('id_layanan_lab') ?>
                <?= form_hidden('id_satuan_lab') ?>
                <?php $ket = array(''=>'Pilih...','L'=>'Low','N'=>'Netral', 'H'=>'High' ) ?>
                <tr><td>Nama Dokter:</td><td><?= form_input('dokter_lab','','id=dokter_lab size=30')?></td></tr>
                <tr><td>Nama Analis Lab:</td><td><?= form_input('analis_lab','','id=analis_lab size=30')?></td></tr>
                <tr><td>Waktu Order:</td><td><?= form_input('waktu_order_lab', date('d/m/Y H:i'),'id=waktu_order_lab size=30')?></td></tr>
                <tr><td>Waktu Hasil:</td><td><?= form_input('waktu_hasil_lab', '','id=waktu_hasil_lab size=30')?></td></tr>
                <tr><td>Layanan:</td><td><?= form_input('layanan_lab','','id=layanan_lab size=30')?></td></tr>
                <tr><td>Hasil:</td><td><?= form_input('hasil_lab','','id=hasil_lab size=30')?></td></tr>
                <tr><td>Keterangan:</td><td><?= form_dropdown('ket_lab', $ket ,null,'id=ket_lab')?></td></tr>
                <tr><td>Satuan:</td><td><?= form_input('satuan','','id=satuan_lab size=30')?></td></tr>
                <tr><td></td><td></td></tr>
                <tr><td></td><td><?= form_button('' ,'Tambah (F4)' ,'id=add_lab onclick=save_pemeriksaan_temp()')?>
                    <?= form_button('','Cetak S.P. Laboratorium', 'class=print onclick=cetak_sp()') ?> 
                    <?= form_button('','Cetak Hasil Laboratorium', 'class=print onclick=cetak_hasil_lab()') ?> 
                    <?= form_reset('','Reset','id=reset_lab')?></td></tr>
            </table>
            <div class="data-list">
                <table class="list-data" id="lab_table" width="100%">
                    <thead>
                        <tr>
                            <th width="15%">Nama Dokter</th>
                            <th width="15%">Nama Analis Lab</th>
                            <th width="10%">Waktu Order</th>
                            <th width="17%">Waktu Hasil</th>
                            <th width="15%">Layanan</th>
                            <th width="5%">Hasil</th>                
                            <th width="5%">Ket</th>
                            <th width="8%">Satuan</th>
                            <th width="5%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>             
                </table>
            </div>
                <?= form_button('','Simpan', 'id=simpan_lab') ?>
        <?= form_close() ?>
        </div>
    </div>


<script type="text/javascript">
    var dokter_arr = new Array();
    var analis_arr = new Array();
    var waktu_order_arr = new Array();
    var waktu_hasil_arr = new Array();
    var layanan_arr = new Array();
    var hasil_arr = new Array();
    var ket_arr = new Array();
    var satuan_arr = new Array();


    function save_pemeriksaan_temp(){
        var dokter = $('input[name=id_dokter_lab]').val();
        var analis = $('input[name=id_analis_lab]').val();
        var wkt_order = ($('#waktu_order_lab').val() !== '')?datetime2mysql($('#waktu_order_lab').val()):'';
        var wkt_hasil = ($('#waktu_hasil_lab').val() !== '')?datetime2mysql($('#waktu_hasil_lab').val()):'';
        var layanan_id = $('input[name=id_layanan_lab]').val();
        var layanan = $('#layanan_lab').val();
        var hasil = $('#hasil_lab').val();
        var keterangan = $('#ket_lab option:selected').val();
        var satuan = $('input[name=id_satuan_lab]').val();

        if (dokter === '') {
            custom_message('Alert !', 'Nama Dokter tidak boleh kosong', '#dokter_lab'); return false;
        }

        if (wkt_order === '') {
            custom_message('Alert !', 'Waktu order tidak boleh kosong !', '#waktu_order_lab'); return false;
        }
        if (layanan_id === '') {
            custom_message('Alert !', 'Nama layanan tidak boleh kosong atau pilih layanan yang tersedia !', '#layanan_lab'); return false;
        }

        dokter_arr.push(dokter);
        analis_arr.push(analis);
        waktu_order_arr.push(wkt_order);
        waktu_hasil_arr.push(wkt_hasil);
        layanan_arr.push(layanan_id);
        hasil_arr.push(hasil);
        ket_arr.push(keterangan);
        satuan_arr.push(satuan);
        append_lab_table(null, $('#dokter_lab').val(), $('#analis_lab').val(), wkt_order, wkt_hasil, layanan, hasil, keterangan, $('#satuan_lab').val(), dokter.length);
        clear_lab_field();
    }

    function clear_lab_field(){
        $("input[name=id_dokter_lab], input[name=id_analis_lab], input[name=id_satuan_lab], input[name=id_layanan_lab]").val("");
        $('#waktu_lab, #dokter_lab, #analis_lab, #layanan_lab,#nilai_lab, #ket_lab, #hasil_lab, #satuan_lab').val('');
    }

    function cetak_sp(){
        window.open('<?= base_url("laboratorium/cetak_sp_pemeriksaan_lab") ?>/'+$('input[name=id_pk_lab]').val(), 'mywindow', 'width='+dWidth+', height='+dHeight+', left='+x+',top='+y);
    }

    function cetak_hasil_lab(){
        window.open('<?= base_url("laboratorium/cetak_hasil_pemeriksaan_lab") ?>/'+ $('input[name=id_pk_lab]').val(), 'mywindow', 'width='+dWidth+', height='+dHeight+', left='+x+',top='+y);
    }

    function append_lab_table(id, dokter, analis, waktu_order, waktu_hasil, layanan, hasil, keterangan, satuan, index){
        var ket = '';
       if(keterangan == 'L'){
            ket = 'Low';
        }else if(keterangan == 'N'){
            ket = 'Netral';
        }else if(keterangan == 'H'){
            ket = 'High';
        }

        var str = '<tr>'+
            '<td>'+((dokter != null)?dokter:'-')+'</td>'+
            '<td>'+((analis != null)?analis:'-') +'</td>'+
            '<td>'+waktu_order+'</td>'+
            '<td>'+waktu_hasil+'</td>'+
            '<td>'+layanan+'</td>'+
            '<td align="center">'+hasil+'</td>'+
            '<td>'+ket+'</td>'+
            '<td>'+((satuan != null)?satuan:'-')+'</td>'+
            '<td class=aksi align=center><a class="deletion" onClick="delete_lab('+id+', this, '+index+')"></a></td></tr>';
        if($('.empty_lab').length > 0){
            $('#lab_table tbody').html('');
        }     
        $('#lab_table tbody').append(str);

    }

   

    function get_lab_list(id_pk){
        $('#lab_table tbody').html('');
         var str_empty = '<tr class="empty_lab"><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>';
         
        $.ajax({
            type : 'GET',
            url: '<?= base_url('laboratorium/pemeriksaan_lab_list') ?>/'+id_pk, 
            cache: false,
            dataType: 'json',
            success: function(data) {
                $('#lab_table').hide();
                var str ='';
                var ket = '';
                var count = 0;
                
                $.each(data, function(i, v){
                    count += 1;
                    append_lab_table(v.id, v.dokter, v.laboran, datetimefmysql(v.waktu_order), (v.waktu_hasil !== null)?datetimefmysql(v.waktu_hasil):'', v.layanan, v.hasil, v.ket_nilai_rujukan, v.satuan );
                });


                $('#lab_table').fadeIn('slow');
                if(count == 0){
                    $('#lab_table tbody').append(str_empty);
                    $('#lab_table tbody').append(str_empty);
                }

            }
        });
    }
    
    function emptying_array(){
        dokter_arr = [];
        analis_arr = [];
        waktu_order_arr = [];
        waktu_hasil_arr = [];
        layanan_arr = [];
        hasil_arr = [];
        ket_arr = [];
        satuan_arr = [];
    }

    $(function(){
        $('#add_lab').button({icons: {secondary: 'ui-icon-circle-plus'}});
        $('#simpan_lab').click(function(){
            $('#formlab').submit();
        });

        $('#formlab').submit(function(){
            var id_pk = $('input[name=id_pk_lab]').val();
            var data_form = 'dokter='+JSON.stringify(dokter_arr)+'&analis='+JSON.stringify(analis_arr)+'&waktu_order='+JSON.stringify(waktu_order_arr)+'&waktu_hasil='+JSON.stringify(waktu_hasil_arr)+'&layanan='+JSON.stringify(layanan_arr)+'&hasil='+JSON.stringify(hasil_arr)+'&ket='+JSON.stringify(ket_arr)+'&satuan='+JSON.stringify(satuan_arr);
                        
            $.ajax({
                type : 'POST',
                url: '<?= base_url("laboratorium/pemeriksaan_lab_add") ?>/',
                data: data_form+'&id_pelayanan_kunjungan='+id_pk,
                cache: false,
                dataType: 'json',
                success: function(data) {
                    if (data.status == true) {
                        alert_tambah();
                        clear_lab_field();
                        get_lab_list(id_pk);
                        emptying_array();
                    }else{
                        clear_lab_field();
                        alert_tambah_failed();
                    }
                },
                error: function(){
                    alert_tambah_failed();
                }
            });
            
            return false;
        });

        $('#form_laboratorium').dialog({
            autoOpen: false,
            height: $(window).height(),
            width: $(window).width(),
            title : 'Pemeriksaan Laboratorium',
            modal: true,
            resizable : false,
            close : function(){
            },
            open: function(){
            }
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
                    //$('input[name=id_analis_lab]').val('');
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
    })

    function laboratorium(id){
        $('input[name=id_pk_lab]').val(id);
        $('#dokter_lab').focus();
        add_laboratorium(id);
        get_lab_list(id);
        $('#form_laboratorium').dialog('open');
    }

    function add_laboratorium(id){
        $.ajax({
            type : 'GET',
            url: '<?= base_url('pelayanan/inap_kunjungan_get_data') ?>/'+id, 
            cache: false,
            dataType: 'json',
            success: function(data) {
                $('#lab_add tbody').empty();
                var act = $("#formlab").attr('action');
                $("#formlab").attr('action', act+'/'+data.inap.id);
                $('#bangsal_lab').html(data.inap.nama_unit);
                $('#kelas_lab').html(data.inap.kelas);
                $('#nott_lab').html(data.inap.no_tt);
                $('#dpjp_lab').html(data.inap.nama_pegawai);
                $('#asu_lab').html(data.inap.nama_asuransi);
            }
        });
    }

    function delete_lab(id_lab, obj, index_arr){
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
                       if(id_lab != null){
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
                        }else{
                            dokter_arr.splice(index_arr, 1);
                            analis_arr.splice(index_arr, 1);
                            waktu_order_arr.splice(index_arr, 1);
                            waktu_hasil_arr.splice(index_arr, 1);
                            layanan_arr.splice(index_arr, 1);
                            hasil_arr.splice(index_arr, 1);
                            ket_arr.splice(index_arr, 1);
                            satuan_arr.splice(index_arr, 1);
                        }
                        $( this ).dialog( "close" ); 
                    } 
                }, 
                { text: "Batal", click: function() { $( this ).dialog( "close" );}} 
            ]
        });
    }

</script>

<div id="form_radiologi" style="display: none;position: static; background: #fff; padding: 10px;">
    <div class="data-input">
            <?= form_hidden('id_pk_rad') ?>
            <table width="100%" class="inputan">
                <tr><td>No. RM:</td><td id="norm_rad"></td></tr>
                <tr><td>Nama Pasien:</td><td id="nama_rad"></td></tr>
                <tr><td>Bangsal:</td><td id="bangsal_rad"></td></tr>
                <tr><td>Kelas:</td><td id="kelas_rad"></td></tr>
                <tr><td>No. Bed:</td><td id="nott_rad"></td></tr>
                <tr><td>Dokter Penanggung Jwb:</td><td id="dpjp_rad"></td></tr>
                <tr><td>Produk Asuransi:</td><td id="asu_rad"></td></tr>
            </table>
        <?= form_open('pelayanan/pemeriksaan_radiologi_save', 'id=formrad') ?>
            <table width="100%" class="inputan">
                <tr><td>Nama Dokter:</td><td><?= form_input('dokter_rad', NULL, 'size=30 id=dokter_rad class=input_rad') ?> <?= form_hidden('id_dokter_rad') ?></td></tr>
                <tr><td>Nama Radiografer:</td><td><?= form_input('radio_rad', NULL, 'size=30 id=radio_rad class=input_rad') ?> <?= form_hidden('id_radio_rad') ?></td></tr>
                <tr><td>Waktu Order:</td><td><?= form_input('', date("d/m/Y H:i"), 'id=waktu_order_rad size=15') ?></td></tr>
                <tr><td>Waktu Hasil:</td><td><?= form_input('', '', 'id=waktu_hasil_rad size=15') ?></td></tr>
                <tr><td>Nama Layanan</td><td><?= form_input('layanan_rad', NULL, 'size=30 id=layanan_rad class=input_rad') ?> <?= form_hidden('id_layanan_rad') ?></td></tr>
                <tr><td></td><td><?= form_button(NULL, 'Tambah (F9)', 'id=bt_rad') ?> <?= form_button('','Cetak S.P.', 'class=print onclick=cetak_radio()') ?> </td></tr>
                <tr><td>kv:</td><td><?= form_input('','','id="kv_rad" class=input_rad onkeyup=Angka(this)') ?></td></tr>
                <tr><td>ma:</td><td><?= form_input('','','id="ma_rad" class=input_rad onkeyup=Angka(this)') ?></td></tr>
                <tr><td>s:</td><td><?= form_input('','','id="s_rad" class=input_rad onkeyup=Angka(this)') ?></td></tr>
                <tr><td>p:</td><td><?= form_input('','','id="p_rad" class=input_rad onkeyup=Angka(this)') ?></td></tr>
                <tr><td>fr:</td><td><?= form_input('','','id="fr_rad" class=input_rad') ?></td></tr>
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
        <?= form_button('','Simpan', 'id=simpan_rad') ?>
     
    <?= form_close() ?>
    </div>
</div>

<script type="text/javascript">
    $(function(){
        $('#simpan_rad').click(function(){
            $('#formrad').submit();
        });

        $('#formrad').submit(function(){
            var url = $(this).attr('action');

             $.ajax({
                type : 'POST',
                url: url,
                data: $(this).serialize(),
                cache: false,
                dataType: 'json',
                success: function(data) {
                    if (data.status == true) {
                        alert_tambah();
                        get_rad_list(data.id_pelayanan);
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

        $('#form_radiologi').dialog({
            autoOpen: false,
            height: $(window).height(),
            width: $(window).width(),
            title : 'Pemeriksaan Radiologi',
            modal: true,
            resizable : false,
            close : function(){
            },
            open: function(){
            }
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
                    //$('input[name=id_dokter]').val('');
                    return parsed;
                },
                formatItem: function(data,i,max){
                    var str = '<div class=result>'+data.nama+'<br/>'+data.kerja_izin_surat_no+'</div>';
                    return str;
                },
                width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
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
                    //$('input[name=id_analis_lab]').val('');
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

    });


    function radiologi(id){
        $('input[name=id_pk_rad]').val(id);
        add_radiologi(id);
        get_rad_list(id);
        $('#form_radiologi').dialog('open');
        var act = $("#formrad").attr('action');
        $("#formrad").attr('action', act+'/'+id);
    }

    function cetak_radio(){
        window.open('<?= base_url("laboratorium/cetak_sp_pemeriksaan_radiologi") ?>/'+$('input[name=id_pk_rad]').val(), 'mywindow', 'width='+dWidth+', height='+dHeight+', left='+x+',top='+y);
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
            
            var str = '<tr class=add_rad>'+
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
            $('.input_rad, input[name=id_dokter_rad], input[name=id_radio_rad], input[name=id_layanan_rad]').val('');
            $('#layanan_rad').focus();
        }

    function get_rad_list(id_pk){
        $('#add_data_rad tbody').html('');
         var str_empty = '<tr class="empty_rad"><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td></tr>';
         
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
            '<td class=aksi align=center><a class="deletion" onClick="delete_rad('+v.id+', this)"></a></td></tr>';      
        if($('.empty_rad').length > 0){
            $('#add_data_rad tbody').html('');
        } 

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


    function removeMe(el) {
        var parent = el.parentNode.parentNode;
        parent.parentNode.removeChild(parent);
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

     function add_radiologi(id){
        $.ajax({
            type : 'GET',
            url: '<?= base_url('pelayanan/inap_kunjungan_get_data') ?>/'+id, 
            cache: false,
            dataType: 'json',
            success: function(data) {
                $('#bangsal_rad').html(data.inap.nama_unit);
                $('#kelas_rad').html(data.inap.kelas);
                $('#nott_rad').html(data.inap.no_tt);
                $('#dpjp_rad').html(data.inap.nama_pegawai);
                $('#asu_rad').html(data.inap.nama_asuransi);
            }
        });
    }

</script>

 <script type="text/javascript">
        $(function(){
            $('#form_rad_edit').dialog({
                autoOpen: false,
                height: 210,
                width: 300,
                title : 'Vital Sign',
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


    <div id="form_vital" style="display: none;position: static; background: #fff; padding: 10px;">
        <div class="data-input">
        
        <?= form_hidden('id_pk_vital') ?>
            <table width="100%" class="inputan">
                <tr><td>No. RM:</td><td class="label" id="norm_vital"></td></tr>
                <tr><td>Nama Pasien:</td><td class="label" id="nama_vital"></td></tr>
                <tr><td>Bangsal:</td><td class="label" id="bangsal_vital"></td></tr>
                <tr><td>Kelas:</td><td class="label" id="kelas_vital"></td></tr>
                
                <tr><td>No. Bed:</td><td class="label" id="nott_vital"></td></tr>
                <tr><td>Dokter Penanggung Jwb:</td><td class="label" id="dpjp_vital"></td></tr>
                <tr><td>Produk Asuransi:</td><td class="label" id="asu_vital"></td></tr>
                <tr><td>Tensi:</td><td class="label"><?= form_input('tensi','','id="tensi_vital" class=input_vital') ?> mm/Hg</td></tr>
                <tr><td>Nadi:</td><td class="label"><?= form_input('nadi','','id="nadi_vital" class=input_vital') ?> ppm</td></tr>
                <tr><td>Suhu:</td><td class="label"><?= form_input('suhu','','id="suhu_vital" class=input_vital') ?> <sup>&deg;</sup> C</td></tr>
                <tr><td>Nafas:</td><td class="label"><?= form_input('nafas','','id="nafas_vital" class=input_vital') ?> bpm</td></tr>
                <tr><td>Waktu:</td><td class="label"><?= form_input('waktu', date("d/m/Y H:i"), 'id=waktu_vital size=20') ?></td></tr>
                <tr><td></td><td><?= form_button(NULL, 'Tambah', 'id=bt_vital') ?>
            </table>
            <?= form_open('pelayanan/vital_sign_save', 'id=formvital') ?>    
            <div class="data-list">
                <table class="list-data" id="vital_table" width="100%">
                    <thead>
                        <tr>
                            <th width="15%">Waktu</th>
                            <th width="20%">Tensi (mm/Hg)</th>
                            <th width="20%">Nadi (ppm)</th>                
                            <th width="20%">Suhu (<sup>&deg;</sup> C)</th>
                            <th width="20%">Nafas (bpm)</th>
                            <th width="5%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>             
                </table>
                <?= form_button('','Simpan' ,'id=save_vital onclick="simpan_vital()"') ?>
            </div>
            <br/><br/><br/>
            <div id="tabs">
                <ul>
                    <li><a href="#tensi_chart">Tekanan Darah</a></li>
                    <li><a href="#nadi_chart">Denyut Nadi</a></li>
                    <li><a href="#suhu_chart">Suhu</a></li>
                    <li><a href="#nafas_chart">Nafas</a></li>
                </ul>

                 <div class="charting_popup" id="tensi_chart"></div>
                 <div class="charting_popup" id="nadi_chart"></div>
                 <div class="charting_popup" id="suhu_chart"></div>
                 <div class="charting_popup" id="nafas_chart"></div>
            </div>

            
            
            <?= form_close() ?>
        </div>
    </div>

    <script type="text/javascript">
        $(function(){
            $('#tabs').tabs();
            $('#form_vital').dialog({
                autoOpen: false,
                height: $(window).height() - 10,
                width: $(window).width() - 10,
                title : 'Vital Sign',
                modal: true,
                resizable : false,
                close : function(){
                },
                open: function(){
                    $('#tensi_vital').focus();
                }
            });

            $('#bt_vital').click(function(){
                if( ($('#tensi_vital').val() !== '') & ($('#tensi_vital').val().indexOf('/') === -1) ){
                    custom_message('Peringatan', 'Format nilai tekanan darah salah!', '#tensi_vital');
                    return false;
                }
                if($('#waktu_vital').val() === ''){
                    custom_message('Peringatan', 'Waktu tidak boleh kosong!', '#waktu_vital');
                    return false;
                }

                add_vital();
                
            });
        });

        function vital_chart(div,judul, waktu, series, satuan){
            $(div).highcharts({
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false
                },
                exporting: {
                    enabled: false
                },
                xAxis: {
                    categories: waktu
                },
                yAxis:{
                    title: {
                        text: 'Jumlah'
                    }
                },
                title: {
                    text: judul
                },
                tooltip: {
                    pointFormat: '{point.y} '+satuan
                },
                series: series
            });
        }

        function tensi_chart(data){
            $('#tensi_chart').highcharts({
    
                chart: {
                    type: 'column'
                },
                 exporting: {
                    enabled: false
                },
                title: {
                    text: data.title_tensi
                },
        
                xAxis: {
                    categories: data.waktu
                },
        
                yAxis: {
                    min: 0,
                    title: {
                        text: 'Nilai (mm/Hg)'
                    }
                },
        
                tooltip: {
                    formatter: function() {
                        return '<b>'+ this.x +'</b><br/>'+
                            this.series.name +': '+ this.y +' mm/Hg'
                    }
                },
        
                plotOptions: {
                    column: {
                        stacking: 'normal'
                    }
                },
        
                series: data.tensi
            });
  
        }

        function vital(id){
            $('input[name=id_pk_vital]').val(id);
            get_vital_list(id);
            $.ajax({
                type : 'GET',
                url: '<?= base_url("pelayanan/inap_kunjungan_get_data") ?>/'+id, 
                cache: false,
                dataType: 'json',
                success: function(data) {
                    $('#add_vital tbody').empty();
                    var act = $("#formvital").attr('action');
                    $("#formvital").attr('action', act+'/'+data.inap.id);
                    $('#bangsal_vital').html(data.inap.nama_unit);
                    $('#kelas_vital').html(data.inap.kelas);
                    $('#nott_vital').html(data.inap.no_tt);
                    $('#dpjp_vital').html(data.inap.nama_pegawai);
                    $('#asu_vital').html(data.inap.nama_asuransi);
                }
            });
            
            $('#form_vital').dialog('open');

        }

        function simpan_vital(){
           var url = $("#formvital").attr('action');

           $.ajax({
                type : 'POST',
                url: url,
                data: $('#formvital').serialize(),
                cache: false,
                dataType: 'json',
                success: function(data) {
                    if (data.status == true) {
                        alert_tambah();
                        get_vital_list(data.id_pelayanan);
                    }else{
                        alert_tambah_failed();
                    }
                },
                error: function(){
                    alert_tambah_failed();
                }
            });
            return false;
        }

        function add_vital(){
            var waktu = $('#waktu_vital').val();
            var tensi_vital = $('#tensi_vital').val(); 
            var nadi_vital = $('#nadi_vital').val(); 
            var suhu_vital = $('#suhu_vital').val(); 
            var nafas_vital = $('#nafas_vital').val(); 
            
            var str = '<tr class=add_rad>'+
                        '<td>'+waktu+' <input type="hidden" name="waktu[]" value="'+waktu+'" />'+
                        '<input type="hidden" name="tensi_data[]" value="'+tensi_vital+'" />'+
                        '<input type="hidden" name="nadi_data[]" value="'+nadi_vital+'" />'+
                        '<input type="hidden" name="suhu_data[]" value="'+suhu_vital+'" />'+
                        '<input type="hidden" name="nafas_data[]" value="'+nafas_vital+'" />'+
                        '<td align="center">'+tensi_vital+'</td>'+
                        '<td align="center">'+nadi_vital+'</td>'+
                        '<td align="center">'+suhu_vital+'</td>'+
                        '<td align="center">'+nafas_vital+'</td>'+
                        '<td align=center class=aksi><a class="deletion" onClick="removeMe(this)"></a></td>'+
                      '</tr>';

            if($('.empty_vital').length > 0){
                $('#vital_table tbody').html('');
            } 
            $('#vital_table tbody').append(str);
            $('.input_vital').val('');
            $('#tensi_vital').focus();
        }

        function get_vital_list(id_pk){
            $('#vital_table tbody').html('');
             var str_empty = '<tr class="empty_vital"><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td></tr>';
             
            $.ajax({
                type : 'GET',
                url: '<?= base_url("pelayanan/vital_sign_list") ?>/'+id_pk, 
                cache: false,
                dataType: 'json',
                success: function(data) {
                    $('#vital_table').hide();
                    var str ='';
                    var ket = '';
                    var count = 0;

                    $.each(data.data, function(i, v){
                        count += 1;
                        append_vital_table(v)
                    });

                    $('#vital_table').fadeIn('slow');
                    if(count == 0){
                        $('#vital_table tbody').append(str_empty);
                        $('#vital_table tbody').append(str_empty);
                    }else{
                        tensi_chart(data);
                        vital_chart('#nadi_chart', data.title_nadi,data.waktu,data.nadi,'ppm');
                        vital_chart('#suhu_chart', data.title_suhu,data.waktu,data.suhu,'° C');
                        vital_chart('#nafas_chart', data.title_nafas,data.waktu,data.nafas,'bpm');
                    }
                }
            });
        }

        function append_vital_table(v){
            var waktu = ((v.waktu !== null)?datetimefmysql(v.waktu):'');

            var str = '<tr class=add_vitals>'+
                '<td>'+waktu+'</td>'+
                '<td align="center">'+v.tensi+'</td>'+
                '<td align="center">'+v.nadi+'</td>'+
                '<td align="center">'+v.suhu+'</td>'+
                '<td align="center">'+v.nafas+'</td>'+
                '<td class=aksi align=center><a class="deletion" onClick="delete_vital('+v.id+', this)"></a></td></tr>';      
            if($('.empty_vital').length > 0){
                $('#vital_table tbody').html('');
            } 

            $('#vital_table tbody').append(str);
        }

        function delete_vital(id_vital, obj){
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
                                url: '<?= base_url("pelayanan/delete_vital_sign") ?>/'+id_vital,
                                cache: false,
                                success: function(data) {
                                   alert_delete();
                                   get_vital_list($('input[name=id_pk_vital]').val());
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

    </script>

    <div id="rm">
        <div id="rekam_medis"></div>
    </div>

</div>