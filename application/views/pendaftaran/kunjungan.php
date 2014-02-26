<?php $this->load->view('message') ?>
<title><?= $title ?></title>
<div class="titling"><h1><?= $title ?></h1></div>
<div class="kegiatan">
<script type="text/javascript">
    var cetak_kartu;
    var Dtgl_lahir = '';
    var request;
    
    function fill_data(data){
        $.ajax({
            type : 'POST',
            url: '<?= base_url() ?>pendaftaran/cek_pendaftaran_terakhir/'+data.no_rm,
            data : $('#formtindak').serialize(),
            dataType : 'json',
            success: function(msg) {
                /*if (msg.status == 'inap') {
                    custom_message('Peringatan', 'Pasien bersangkutan masih dalam pelayanan rawat inap !');
                }else if(msg.status == 'bayar'){
                    custom_message('Peringatan', 'Pasien bersangkutan belum melunasi tagihan pelayanan rumah sakit !');
                }else{*/
                    $('input[name=norm], #no_barcode, #no').val(data.no_rm);
                    $('input[name=id_penduduk]').val(data.penduduk_id);
                    $('#nama').val(data.nama);
                    $('#ket').val(data.keterangan);
                    $('#alamat').val(data.alamat);
                    $('#kelamin').val(data.gender);
                    $('#telp').val(data.telp);
                    $('#darah_gol').val(data.darah_gol);
                    $('#tgl').val(datefmysql(data.lahir_tanggal));
                    $('#umur').val(hitungOnlyTahun(data.lahir_tanggal));
                    $('#lahir_tempat').val(data.tempat_lahir);
                    $('input[name=hd_lahir_tempat]').val(data.lahir_kabupaten_id);
                    $('#agama').val(data.agama);
                    $('#pendidikan').val(data.pendidikan_id);
                    $('#pekerjaan').val(data.pekerjaan_id);
                    $('#pernikahan').val(data.pernikahan);
                    $('input[name=profesi]').val(data.profesi_id);
                    $('#dokter').focus();
                    get_kelurahan(data.kelurahan_id);
                //}
            }
        });
    }
    
    function fn_cari_penduduk() {
        var str = '<div id="form_cari" style="display: none;position: static; background: #fff; padding: 10px;">'+
                    '<form id="formcari">'+
                    '<table width="100%" class="inputan">'+
                        '<tr><td style="width: 150px;">Nama Pasien:</td><td><?= form_input('nama', null, 'id=nama_cari size=30 class="input-text"') ?></td></tr>'+
                        '<tr><td>Alamat:</td><td><?= form_textarea('alamat','','id=alamat_cari class="standar"')?></td></tr>'+
                        '<tr><td></td><td><?= form_button('', 'Cari', 'id=cari_pdd onclick=cari_penduduk(1)'); ?>'+
                        '<?= form_button('reset', 'Reset', 'id=reset_cari onclick=reset_pencarian()') ?></td></tr>'+
                    '</table>'+
                    '</form>'+
                    '<div id="list_pasien"></div>'+
        '</div>';
        $(str).dialog({
            title: 'Form Cari Pasien',
            autoOpen: true,
            modal: true,
            width: 700,
            height: $(window).height(),
            close: function() {
                $(this).dialog('close').remove();
            },
            open: function() {
                cari_penduduk(1);
            }
        });
        $('#reset_cari').button({icons: {secondary: 'ui-icon-refresh'}});
        $('#cari_pdd').button({icons: {secondary: 'ui-icon-search'}});
    }
    $(function() {
        $('#tabs').tabs();
        $('.enter').live("keydown", function(e) {
            var n = $(".enter").length;
            if (e.keyCode === 13) {
                var nextIndex = $('.enter').index(this) + 1;
                if (nextIndex < n) {                        
                    $('.enter')[nextIndex].focus();

                    var center = $('#loaddata').height()/2;
                    var top = $(this).offset().top;
                    if (top > center){
                        $('#loaddata').scrollTop(top-center);
                    }
                } else {
                    $('#simpan').focus();
                }
            }
        });
        $('#bt_cari').click(function() {
            fn_cari_penduduk();
        });

        $('#no_barcode').focus();
        $('button[id=cari_pdd]').button({icons: {secondary: 'ui-icon-search'}});
        $('#cetak_kartu').button({icons: {secondary: 'ui-icon-print'}}).hide();
        $('#simpan, #simpanpdd').button({icons: {secondary: 'ui-icon-circle-check'}});
        $('#simpan').click(function() {
            var Dusia = $("#usia option:selected").val();
            
            if (Dusia == 'umur'){         
                if ($('#umur').val() == '') {
                    custom_message('Peringatan', 'Umur tidak boleh kosong !', '#umur');
                    return false;
                }else{
                    Dtgl_lahir = birthByAge($("#umur").val());
                }
                
            }else{
            
                if ($('#tgl').val() == '') {
                    custom_message('Peringatan','Tanggal lahir tidak boleh kosong !','#tgl');
                    return false;
                }else if($('#tgl').val() == '00/00/0000'){
                    custom_message('Peringatan', 'Tanggal lahir tidak valid !', '#tgl');
                    return false;
                }else{
                    Dtgl_lahir = $("#tgl").val()
                }            
            }

            if($('#layanan').val() == ''){
                custom_message('Peringatan', 'Layanan harus dipilih !', '#layanan');
                return false
            }
            if ($('#jenis_layan').val() == '') {
                custom_message('Peringatan','Jenis layanan harus dipilih !','#jenis_layan');
                return false;
            }
            if ($('#krit_layan').val() == '') {
                custom_message('Peringatan','Kriteria layanan harus dipilih !','#krit_layan');
                return false;
            }    

            $("<div title='Konfirmasi Simpan'>Anda yakin akan menyimpan data transaksi ini ?</div>").dialog({
                modal: true,
                autoOpen: true,
                width: 320,
                buttons: { 
                        "Ya": function() { 
                            $('#form_kunjungan').submit();
                            $(this).dialog('close');
                        },
                        "Tidak": function() {
                            $(this).dialog('close');
                            return false;
                        }
                    }, close: function() {
                        $(this).dialog('close');
                        return false;
                    }
              });
        });
        $('#reset, #reset_cari').button({icons: {secondary: 'ui-icon-refresh'}});

        $('#reset').click(function() {
            $('#loaddata').empty().load('<?= base_url("pendaftaran/kunjungan") ?>');
        });
        $('#lembarrm').button({icons: {secondary: 'ui-icon-print'}}).click(function() {
            print_out();
        }).hide();

        $('.angka').keyup(function(){
            Angka(this);
        });

        $("#tgl").datepicker({
            changeYear : true,
            changeMonth : true,
            maxDate : +0,
            onSelect: function() {
                var tahun = date2mysql($(this).val());
                $('#umur').val(hitungOnlyTahun(tahun));
            }
        });

        
        $('#usia').change(function() {
            if ($('#usia').val() == 'umur') {
                $('#umur').show();
                $('#tgl').hide();
                 $('#tgl').val('');
            } else {
                $('#umur').hide();
                $('#tgl').show();
                $('#umur').val('');

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
            $('#addr').html("Kec : "+data.kecamatan+", Kab : "+data.kabupaten+", Prov : "+data.provinsi);
        });

        $('#kelpjawab').autocomplete("<?= base_url('demografi/get_kelurahan') ?>",
        {
            parse: function(data){
                var parsed = [];
                for (var i=0; i < data.length; i++) {
                    parsed[i] = {
                        data: data[i],
                        value: data[i].nama // nama field yang dicari
                    };
                }
                $("input[name=id_kelurahan_pjawab]").val('');
                $('#addr_pjawab').html('');
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
            $("input[name=id_kelurahan_pjawab]").val(data.id);
            $('#addr_pjawab').html("Kec : "+data.kecamatan+", Kab : "+data.kabupaten+", Prov : "+data.provinsi);
        });

     

        $('#no_barcode').keydown(function(e){

                if ((e.keyCode == 13) && (IsNumeric($(this).val()))) {
                    //alert($(this).val());
                    $.ajax({
                        url: '<?= base_url("demografi/pasien_get_detail") ?>/'+$(this).val(),
                        cache: false,
                        dataType:'json',
                        success: function(data) {
                            if(data.length !== 0){
                                fill_data(data);
                            }else{
                                custom_message('Peringatan!', 'No. Rekam Medik tidak ada atau pasien belum melakukan pendaftaran kunjungan !');
                                $('#addr').html(' ');
                                $('.enter').val('');
                            }
                           
                        }
                    });
                    return false;
                };

            });

        $('#no').autocomplete("<?= base_url('inv_autocomplete/load_data_penduduk_pasien') ?>",
        {
            parse: function(data){
                var parsed = [];
                for (var i=0; i < data.length; i++) {
                    parsed[i] = {
                        data: data[i],
                        value: data[i].no_rm // nama field yang dicari
                    };
                }
                
                return parsed;

            },
            formatItem: function(data,i,max){
                var str = '<div class=result>'+data.no_rm+' - '+data.nama+'<br/>'+data.alamat+'</div>';
                return str;
            },
            width: 370, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
            dataType: 'json', // tipe data yang diterima oleh library ini disetup sebagai JSON
            cacheLength: 0,
            max: 10
        }).result(
        function(event,data,formated){
            $(this).val(data.no_rm);
            fill_data(data);
        });

        $('#form_kunjungan').submit(function() {
            if(!request) {
                request = $.ajax({
                    url: '<?= base_url("pendaftaran/kunjungan_save") ?>',
                    cache: false,
                    type: 'POST',
                    data: $(this).serialize()+'&tgl_lahir='+Dtgl_lahir,
                    success: function(data) {
                        request = null;
                       $('#loaddata').html(data); 
                    },
                    error: function() {
                        custom_message('Transaksi Gagal','Data gagal di masukkan silahkan cek kembali data yg dimasukkan');
                    }
                });
            }
            return false;
        });
       

        $('#instansi').autocomplete("<?= base_url('pendaftaran/load_data_instansi_relasi/') ?>",
        {
            parse: function(data){
                var parsed = [];
                for (var i=0; i < data.length; i++) {
                    parsed[i] = {
                        data: data[i],
                        value: data[i].nama // nama field yang dicari
                    };
                }
                $('input[name=id_instansi]').val('');
                return parsed;
            },
            formatItem: function(data,i,max){
                var str = '<div class=result>'+data.nama+'<br/>Jenis : '+data.jenis+'<br/>Alamat :  '+data.alamat+'</div>';
                return str;
            },
            width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
            dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
        }).result(
        function(event,data,formated){
            $(this).val(data.nama);
            $('input[name=id_instansi]').val(data.id);
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
                    var str = '<div class=result><b>'+data.nama+'</b><br/>'+data.jurusan_kualifikasi+'</div>';
                    return str;
                },
                width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
            }).result(
            function(event,data,formated){
                $(this).val(data.nama);
                $('input[name=id_dokter]').val(data.id);
                $('#layanan').val(data.id_jurusan);
               

                if($('#layanan').val() == ''){
                    $(this).val('');
                    $('input[name=id_dokter], input[name=antrian]').val('');
                    $('#antri').html('');
                    custom_message('Peringatan', 'Layanan tidak tersedia di bagian pendaftaran kunjungan !', '#layanan');
                }else{
                     save_kunjungan(data.id_jurusan,$("#tanggal").html());
                }
                
            });

            $('#layanan').change(function(){
                if($(this).val() != ''){
                    save_kunjungan($(this).val() ,$("#tanggal").html());
                }
            });

            $('#lahir_tempat').autocomplete("<?= base_url('demografi/get_kabupaten') ?>",
            {
                parse: function(data){
                    var parsed = [];
                    for (var i=0; i < data.length; i++) {
                        parsed[i] = {
                            data: data[i],
                            value: data[i].kabupaten // nama field yang dicari
                        };
                    }
                    return parsed;
                },
                formatItem: function(data,i,max){
                    var str = '<div class=result> Kab: '+data.kabupaten+', <br/>Prov: '+data.provinsi+'</div>';
                    return str;
                },
                width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
            }).result(
            function(event,data,formated){
                $(this).val(data.kabupaten);
                $("input[name=hd_lahir_tempat]").val(data.kabupaten_id);
              
            });
            
    });

    function birthByAge(umur){
        var today = new Date();
        var birth = today.getDate()+"/"+(today.getMonth()+1)+"/"+(today.getYear()+1900-umur);

        return birth;
    }

    function save_kunjungan(layanan_id, tgl){
        $.ajax({
            type : 'GET',
            url: '<?= base_url("demografi/get_antrian") ?>',               
            data: "id_layanan="+layanan_id+"&tgl_layan="+tgl,
            dataType : 'json',
            cache: false,
            success: function(data) {
                $('#antri').html(data.antrian);
                $('input[name=antrian]').val(data.antrian);
            }
        }); 
        return false;
    }

    function get_kelurahan(kelurahan_id){
        if(kelurahan_id !== null){
            $.ajax({
                url: '<?= base_url('demografi/detail_kelurahan') ?>/'+kelurahan_id,
                dataType :'json',
                success: function(data) {
                    $('#kelurahan').val(data.nama);
                    $('input[name=id_kelurahan]').val(data.id);
                    $('#addr').html("Kec: "+data.kecamatan+", Kab: "+data.kabupaten+", Prov: "+data.provinsi);
                }
            });
        }
    }

</script>
    
    
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">Data Pasien &rightarrow;</a></li>
        <li><a href="#tabs-2">Rujukan &rightarrow;</a></li>
        <li><a href="#tabs-3">Antrian &rightarrow;</a></li>
        <li><a href="#tabs-4">Penanggung Jawab &downarrow;</a></li>
    </ul>
        <?= form_open('', 'id=form_kunjungan') ?>
        <?= form_hidden('no_daftar') ?>
        <div id="tabs-1">
            <div class="msg"></div>
            <table width="100%" class="inputan">
                <tr><td style="width: 150px;">No. RM (Barcode):</td><td><?= form_input('', (isset($no_rm))?$no_rm:'', 'id=no_barcode size=34 class="input-text enter"') ?></td></tr>
                <tr><td>No. RM:</td><td><?= form_input('no_rm', (isset($no_rm))?$no_rm:'', 'id=no size=34 class="input-text enter"') ?><?= form_hidden('norm',isset($no_rm)?$no_rm:'') ?><div class="search-dialog" id="bt_cari" title="Klik untuk cari data pasien"></div></td></tr>
                <tr><td>Nama Pasien:</td><td><?= form_input('nama', (isset($nama))?$nama:'', 'id=nama size=34 class="input-text enter"') ?>
                        
                <?= form_hidden('id_penduduk', (isset($id_penduduk))?$id_penduduk:'') ?>
                <?= form_hidden('hd_lahir_tempat', (isset($lahir_kabupaten_id)) ? $lahir_kabupaten_id: '') ?></td></tr>
                <tr><td>Keterangan:</td><td><?= form_dropdown('ket', array('Tn' => 'Tn', 'Ny' => 'Ny', 'Nn' => 'Nn', 'Sdr' => 'Sdr', 'An' => 'An'), NULL, 'id=ket class=enter') ?></td></tr>
                <tr><td>Jenis kelamin:</td><td><?= form_dropdown('gender', $kelamin, (isset($gender))?$gender:'', 'id=kelamin class="standar enter"') ?></td></tr>
                <tr><td>Tempat Kelahiran:</td><td><?= form_input('lahir_tempat', (isset($lahir_tempat) ) ? $lahir_tempat : null, 'id=lahir_tempat size=40 class="enter input-text"') ?></td><tr>
                <tr><td>Detail Alamat:</td><td id="addr"></td></tr>
                <tr><td>Tanggal Lahir:</td><td><?= form_input('tgl_lahir', (isset($lahir_tanggal))?$lahir_tanggal:'', 'id=tgl class="special enter" size=10') ?></td></tr>
                <tr><td>Umur</td><td><?= form_input('umur', null, 'id=umur class=angka size=10') ?>&nbsp;Thn</td></tr>
                <tr><td>Agama:</td><td><?= form_dropdown('agama', $agama, (isset($agama_id) ) ? $agama_id : null, 'id=agama class="standar enter"') ?></td></tr>
                <tr><td valign="top">Alamat Jalan:</td><td><?= form_textarea('alamat', (isset($alamat))?$alamat:'', 'id=alamat class="standar enter"') ?></td></tr>
                <tr><td>Desa / Kelurahan:</td><td><?= form_input('kelurahan', (isset($kelurahan))?$kelurahan:'', 'id=kelurahan size=34 class="input-text enter"') ?> </td></tr>
                <?= form_hidden('id_kelurahan',(isset($kelurahan_id))?$kelurahan_id:'') ?>    
                <tr><td>Pendidikan:</td><td><?= form_dropdown('pendidikan', $pendidikan, (isset($pendidikan_id) ) ? $pendidikan_id : null, 'id=pendidikan class="standar enter"') ?></td></tr>
                <tr><td>Pekerjaan:</td><td><?= form_dropdown('pekerjaan', $pekerjaan, (isset($pekerjaan_id) ) ? $pekerjaan_id : null, 'id=pekerjaan class="standar enter"') ?></td></tr>
                <tr><td>Golongan Darah:</td><td><?= form_dropdown('gol_darah', $darah, (isset($gol_darah))?$gol_darah:'','id=darah_gol class="standar enter"') ?></td></tr>
                <tr><td>Status Pernikahan:</td><td><?= form_dropdown('pernikahan', $pernikahan, (isset($pernikahan_id) ) ? $pernikahan_id : null, 'id=pernikahan class="standar enter"') ?></td></tr>  
                <tr><td>Telepon:</td><td><?= form_input('telp', (isset($telp))?$telp:'', 'id=telp size=34 class="input-text enter"') ?> </td></tr>
                <?= form_hidden('profesi', (isset($profesi_id) ) ? $profesi_id : null )?>
                <?= form_hidden('jenis_layan', 'Kuratif', 'id=jenis_layan') ?>
                <?= form_hidden('krit_layan', 'Biasa','id=krit_layan') ?>
                <!--<tr><td style="width: 150px;">Keb. Pelayanan:</td><td><?= form_dropdown('jenis_layan', $jenis_layan, null, 'id=jenis_layan class="standar enter"') ?></td></tr>
                <tr><td>Kriteria Layanan:</td><td><?= form_dropdown('krit_layan', $krit_layan, null, 'id=krit_layan class="standar enter"') ?></td></tr>-->
            </table>
        </div>
        <div id="tabs-3">
            <?= form_hidden('id_dokter')?>
            <?= form_hidden('antrian')?>
            <table width="100%" class="inputan">
                <tr><td style="width: 150px;">Tanggal:</td><td><span id="tanggal"><?= date('d/m/Y') ?></span><?= form_hidden('tgl_layan',date('Y-m-d')) ?></td></tr>
                <tr><td>Nama Dokter:</td><td><?= form_input('dokter','','id=dokter size=30 class="input-text enter"')?></td></tr>
                <tr><td>Jenis Layanan:</td><td><?= form_dropdown('id_layanan', $layanan, null, 'id=layanan class="standar enter"') ?></td></tr>
                <tr><td>No. Antrian:</td><td><span id="antri"></span></td></tr>
            </table>            
        </div>
            
        <div id="tabs-2">
            <table width="100%" class="inputan">
                <tr><td style="width: 150px;">Nama Instansi Perujuk:</td><td><?= form_input('instansi', '', 'id=instansi size=30 class="input-text enter"') ?><?= form_hidden('id_instansi') ?></td></tr>
                <tr><td>Nama Tenaga Perujuk:</td><td><?= form_input('nakes', '', 'id=nakes size=30 class="input-text enter"') ?><?= form_hidden('id_nakes') ?></td></tr>
                <tr><td>Alasan Datang:</td><td><?= form_dropdown('alasan', $alasan_datang,array(), 'id=alasan class="standar enter"') ?><?= form_hidden('id_alasan') ?></td></tr>
                <tr><td>Keterangan Kecelakaan:</td><td><?= form_textarea('keterangan_kecelakaan','','row=3 class="standar enter"')?></td></tr>
            </table>
        </div>
        <div id="tabs-4">
            <?= form_hidden('id_kelurahan_pjawab') ?>
            <table width="100%" class="inputan">
                <tr><td style="width: 150px;">Nama:</td><td><?= form_input('pjawab', '', 'size=30 id=pjawab class="input-text enter"') ?></td></tr>
                <tr><td>Telepon:</td><td><?= form_input('telppjawab', '', 'size=30 id=telppjawab class="input-text enter"') ?></td></tr>
                <tr><td>Detail Alamat Jalan:</td><td><?= form_textarea('alamatpjawab', '', 'row=3 class="standar enter"') ?></td></tr>
                <tr><td>Desa/Kelurahan:</td><td><?= form_input('kelpjawab', '', 'size=30 id=kelpjawab class="input-text enter"') ?></td></tr>
                <tr><td></td><td><span id="addr_pjawab"></span></td></tr>
            </table>
            <br/>
            <table width="100%" class="inputan">
                <tr><td style="width: 150px;"></td><td><?= form_button(NULL, 'Simpan', 'id=simpan') ?> <?= form_button(null, 'Reset', 'id=reset') ?></td></tr>
            </table>
        </div>
        <?= form_close() ?>
</div>
</div>
<script type="text/javascript">
    $(function(){
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
                cari_penduduk(1);
            },
            close : function(){
                reset_pencarian();
            }
        });
    });

    function cari_penduduk(page){
        $.ajax({
            url: '<?= base_url("demografi/search_penduduk") ?>/'+page,
            data : $('#formcari').serialize(),
            success: function(data) {
               $('#list_pasien').html(data);
            }
        });
    }

    function reset_pencarian(){
        $('#nama_cari, #alamat_cari').val('');
        $('.list_penduduk').html('');
    }



    function pilih_penduduk(id,id_daftar){
 
         $.ajax({
            url: '<?= base_url("demografi/get_penduduk") ?>/'+id,
            cache: false,
            dataType :'json',
            success: function(data) {
                if(data.no_rm !== null){
                    $('input[name=norm], #no, #no_barcode').val(data.no_rm);
                }
                $('input[name=id_penduduk]').val(data.penduduk_id);
                

                $('#nama').val(data.nama);
                
                $('#kelamin').val(data.gender);
                $('#tgl').val(datefmysql(data.lahir_tanggal));
                $('#alamat').val(data.alamat);
                $('#telp').val(data.telp);
                $('#darah_gol').val(data.darah_gol);
                $('#lahir_tempat').val(data.tempat_lahir);
                $('input[name=hd_lahir_tempat]').val(data.lahir_kabupaten_id);
                $('#agama').val(data.agama);
                $('#pendidikan').val(data.pendidikan_id);
                $('#pekerjaan').val(data.pekerjaan_id);
                $('#pernikahan').val(data.pernikahan);
                $('input[name=profesi]').val(data.profesi_id);
                get_kelurahan(data.kelurahan_id);

                $('#nama, #no').attr('readonly', 'readonly');
            }
        });
    
        $('#form_cari').dialog('close');
            
    }

    function paging(page, tab, cari){
        cari_penduduk(page);
    }

</script>
