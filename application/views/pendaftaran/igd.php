<?php $this->load->view('message') ?>
<title><?= $title ?></title>
<div class="titling"><h1><?= $title ?></h1></div>
<script type="text/javascript">
    var cetak_kartu;
    var request = null;
    var Dtgl_lahir = '';
    var dWidth = $(window).width();
    var dHeight= $(window).height();
    var x = screen.width/2 - dWidth/2;
    var y = screen.height/2 - dHeight/2;

    function fill_data(data){
        $.ajax({
            type : 'POST',
            url: '<?= base_url() ?>pendaftaran/cek_pendaftaran_terakhir/'+data.no_rm,
            data : $('#formtindak').serialize(),
            dataType : 'json',
            success: function(msg) {
//                if (msg.status == 'inap') {
//                    custom_message('Peringatan', 'Pasien bersangkutan masih dalam pelayanan rawat inap !');
//                }else if(msg.status == 'bayar'){
//                    custom_message('Peringatan', 'Pasien bersangkutan belum melunasi tagihan pelayanan rumah sakit !');
//                }else{
                    $('#no,input[name=norm], #no_barcode').val(data.no_rm);
                    $('input[name=id_penduduk]').val(data.penduduk_id);
                    $('#nama').val(data.nama);
                    $('#alamat').val(data.alamat);
                    $('#telp').val(data.telp);
                    $('#kelamin').val(data.gender);
                    $('#darah_gol').val(data.darah_gol);
                    $('#tgl').val(datefmysql(data.lahir_tanggal));
                    get_kelurahan(data.kelurahan_id);
                //}
            }
        });
    }

    function print_out() {
        var perawatan = 'IGD';
        var no_daftar = $("input[name=no_daftar]").val();
        window.open('<?= base_url("pendaftaran/cetak_lembar_pertama") ?>/'+no_daftar+'/'+perawatan,'Myprint','width='+dWidth+', height='+dHeight+', left='+x+',top='+y);
    }
    function alert_and_cetak() {
        var str = '<div id=confirm>'+
            'Data berhasil di tambahkan, <br/>Anda akan mencetak lembar pertama RM'+
            '</div>';
        $('#loaddata').append(str);
        $('#confirm').dialog({
            autoOpen: true,
            modal: true,
            title: 'Informasi',
            buttons: {
                "OK": function() {
                    print_out();
                    $('#lembarrm').show();
                    $(this).dialog().remove();
                }
            }
        });
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
        $('#bt_cari').click(function(){
            $('#form_cari').dialog('open');
        });
        $('#no_barcode').focus();
        $('button[id=cari_pdd]').button({icons: {secondary: 'ui-icon-search'}});
        $('#cetak_kartu').button({icons: {secondary: 'ui-icon-print'}}).hide();
        $('#simpan, #simpanpdd').button({icons: {secondary: 'ui-icon-circle-check'}})

        $('#simpan').click(function() {
             var Dusia = $("#usia option:selected").val();
            
            if($('#from_pdd').attr('checked') != 'checked'){
                if ($('#nama').val() === '') {
                    custom_message('Peringatan', 'Nama pasien tidak boleh kosong !', '#nama');
                    return false;
                }
            }
            if ($('#alamat').val() === '') {
                custom_message('Peringatan', 'Alamat jalan tidak boleh kosong !', '#alamat');
                return false;
            }
            if (Dusia === 'umur'){         
                if ($('#umur').val() === '') {
                    custom_message('Peringatan', 'Umur tidak boleh kosong !', '#umur');
                    return false;
                }else{
                    Dtgl_lahir = birthByAge($("#umur").val());
                }
                
            }else{
            
                if ($('#tgl').val() == '') {
                    custom_message('Peringatan', 'Tanggal lahir tidak boleh kosong !', '#tgl');
                    return false;
                }else if($('#tgl').val() == '00/00/0000'){
                    custom_message('Peringatan', 'Tanggal lahir tidak valid !', '#tgl');
                    return false;
                }else{
                    Dtgl_lahir = $("#tgl").val()
                }            
            }
           $("<div title='Konfirmasi Simpan'>Anda yakin akan menyimpan data transaksi ini ?</div>").dialog({
                modal: true,
                autoOpen: true,
                width: 320,
                buttons: { 
                        "Ya": function() { 
                            $('#form_igd').submit();
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
            $('#loaddata').empty().load('<?= base_url('pendaftaran/igd_new') ?>');
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
            maxDate : +0
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

         $('#no_barcode').keydown(function(e){
                if ((e.keyCode == 13) && (IsNumeric($(this).val()))) {
                    $.ajax({
                        url: '<?= base_url("demografi/pasien_get_detail") ?>/'+$(this).val(),
                        cache: false,
                        dataType:'json',
                        success: function(data) {
                            if(data.length !== 0){
                                fill_data(data);
                            }else{
                                custom_message('Peringatan!', 'No. Rekam Medik tidak ada atau pasien belum melakukan pendaftaran kunjungan !', '#no_barcode');
                                $('#no, .enter').val('');
                                $('#addr').html('');
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
            dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
        }).result(
        function(event,data,formated){
            $(this).val(data.no_rm);
            fill_data(data);            
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


        $('#form_igd').submit(function() {
            if(!request) {
                    request = $.ajax({
                    url: '<?= base_url("pendaftaran/igd_save") ?>',
                    cache: false,
                    dataType: 'json',
                    type: 'POST',
                    data: $(this).serialize()+'&tgl_lahir='+Dtgl_lahir,
                    success: function(data) {
                        request = null;
                        //$('input[type=text], select, textarea').attr('disabled','disabled');
                        alert_and_cetak();
                        $('#cetak_kartu').show();
                        $('input[name=norm]').val(data.no_rm);
                        $('input[name=no_daftar]').val(data.no_daftar);
                        $('input[name=id_pelayanan]').val(data.id_pk);
                        $('#simpan').hide();
                    },
                    error: function() {
                        custom_message('Transaksi Gagal','Data gagal di masukkan silahkan cek kembali data yg dimasukkan');
                    }
                });
            }
            return false;
        });
        $('#cetak_kartu').click(function(){
            var no_daftar = $("input[name=no_daftar]").val();
            var no_rm = $("input[name=no_rm]").val();
            var id_pk = $('input[name=id_pelayanan]').val();

            window.open('<?= base_url() ?>pendaftaran/cetak_kartu_get/'+no_rm+'/'+no_daftar+'/'+id_pk+'/igd','Cetak Kartu Pasien','width='+dWidth+', height='+dHeight+', left='+x+',top='+y);
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
            
    });

    function birthByAge(umur){
        var today = new Date();
        var birth = today.getDate()+"/"+(today.getMonth()+1)+"/"+(today.getYear()+1900-umur);

        return birth;
    }

    function get_kelurahan(kelurahan_id){
        if(kelurahan_id != null){
            $.ajax({
                url: '<?= base_url('demografi/detail_kelurahan') ?>/'+kelurahan_id,
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
</script>
<div class="kegiatan">
    <div id="tabs">
    <ul>
        <li><a href="#tabs-1">Data Pasien &rightarrow;</a></li>
        <li><a href="#tabs-2">Rujukan &rightarrow;</a></li>
        <li><a href="#tabs-3">Penanggung Jawab &downarrow;</a></li>
    </ul>   
    

        <?= form_open('', 'id=form_igd') ?>
        <?= form_hidden('no_daftar') ?>
        <?= form_hidden('id_pelayanan') ?>
        <?= form_hidden('id_penduduk') ?>
        <?= form_hidden('alamat')?>
    <div id="tabs-1">
        <table width="100%" class="inputan">
            <tr><td style="width: 150px;">No. RM (Barcode):</td><td><?= form_input('', (isset($no_rm))?$no_rm:'', 'id=no_barcode size=40 class="input-text enter"') ?></td></tr>
            <tr><td>No. RM:</td><td><?= form_input('norm_tampil', (isset($no_rm))?$no_rm:'', 'id=no size=40 class="input-text enter"') ?><?= form_hidden('norm') ?><div class="search_pdd" id="bt_cari" title="Klik untuk mencari data di database kependudukan"></div></td></tr>
            <tr><td>Nama Pasien:</td><td><?= form_input('nama', (isset($nama))?$nama:'', 'id=nama size=40 class="input-text enter"') ?></td></tr>
            <tr><td>Keterangan:</td><td><?= form_dropdown('ket', array('Tn' => 'Tn', 'Ny' => 'Ny', 'Nn' => 'Nn', 'Sdr' => 'Sdr', 'An' => 'An'), NULL, 'id=ket') ?></td></tr>
            <tr><td>Jenis kelamin:</td><td><?= form_dropdown('gender', $kelamin, (isset($gender))?$gender:'', 'id=kelamin class="standar enter"') ?></td></tr>
            <tr><td>Tanggal Lahir:</td><td><?= form_input('', (isset($lahir_tanggal))?$lahir_tanggal:'', 'id=tgl class="special enter" size=10') ?></td></tr>
            <tr><td>Usia:</td><td><?= form_input('', null, 'id=umur class=angka size=10') ?>&nbsp;Thn</td></tr>
            <tr><td valign="top">Alamat Jalan:</td><td><?= form_textarea('alamat', (isset($alamat))?$alamat:'', 'id=alamat class="standar enter"') ?></td></tr>
            <tr><td>Kelurahan</td><td><?= form_input('kelurahan', '', 'id=kelurahan size=40 class="input-text enter"') ?><?= form_hidden('id_kelurahan') ?></td></tr>
            <tr><td></td><td id="addr"></td></tr>
            <tr><td>Telepon:</td><td><?= form_input('telp', (isset($telp))?$telp:'', 'id=telp size=34 class="input-text enter"') ?> </td></tr>
            
            <tr><td>Golongan Darah:</td><td><?= form_dropdown('gol_darah', $darah, (isset($gol_darah))?$gol_darah:'','id=darah_gol class="standar enter"') ?></td></tr>
            <tr><td>Kebutuhan Perawatan:</td><td><?= form_dropdown('keb_rawat', $keb_rawat, 'Darurat', 'id=keb_rawat class="standar enter"') ?></td></tr>
            <!--<tr><td>Keb. Pelayanan:</td><td><?= form_dropdown('jenis_layan', $jenis_layan, null, 'id=jenis_layan class="standar enter"') ?></td></tr>
            <tr><td>Kriteria Layanan:</td><td><?= form_dropdown('krit_layan', $krit_layan, null, 'id=krit_layan class="standar enter"') ?></td></tr>-->
            <?= form_hidden('jenis_layan', 'Kuratif', 'id=jenis_layan') ?>
            <?= form_hidden('krit_layan', 'Biasa','id=krit_layan') ?>
            <tr><td>Meninggal Saat Tiba di RS:</td><td><span class="label"><?= form_radio('doa', 'Tidak', TRUE, 'id=tidak class="standar enter"') ?>Tidak </span> <span class="label"><?= form_radio('doa', 'Ya', FALSE, 'id=ya class="enter"') ?>Ya</span></td></tr>
        </table>
    </div>
    <div id="tabs-2">
        <table width="100%" class="inputan">
            <tr><td style="width: 150px;">Nama Instansi Perujuk:</td><td><?= form_input('instansi', '', 'id=instansi size=40 class="enter"') ?><?= form_hidden('id_instansi') ?></td></tr>
            <tr><td>Nama Tenaga Perujuk:</td><td><?= form_input('nakes', '', 'id=nakes size=30 class="input-text enter"') ?><?= form_hidden('id_nakes') ?></td></tr>
            <tr><td>Alasan Datang:</td><td><?= form_dropdown('alasan', $alasan_datang,array(), 'id=alasan class="enter"') ?><?= form_hidden('id_alasan') ?></td></tr>
            <tr><td>Keterangan Kecelakaan:</td><td><?= form_textarea('keterangan_kecelakaan','','row=3 class="standar enter"')?></td></tr>
        </table>
    </div>
    <div id="tabs-3">
        <?= form_hidden('id_kelurahan_pjawab') ?>
        <table width="100%" class="inputan">
            <tr><td style="width: 150px;">Nama:</td><td><?= form_input('pjawab', '', 'size=30 id=pjawab class="input-text enter"') ?></td></tr>
            <tr><td>Telepon:</td><td><?= form_input('telppjawab', '', 'size=30 id=telppjawab class="input-text enter"') ?></td></tr>
            <tr><td>Detail Alamat Jalan:</td><td><?= form_textarea('alamatpjawab', '', 'row=3 class="standar enter"') ?></td></tr>
            <tr><td>Desa/Kelurahan:</td><td><?= form_input('kelpjawab', '', 'size=30 id=kelpjawab class="input-text enter"') ?></td></tr>
            <tr><td></td><td><span id="addr_pjawab"></span></td></tr>
            <tr><td></td><td><?= form_button(NULL, 'Simpan', 'id=simpan') ?> <?= form_button(null, 'Reset', 'id=reset') ?><?= form_button(NULL, 'Cetak Lembar RM', 'id=lembarrm') ?> <?= form_button(NULL, 'Cetak Kartu', 'id=cetak_kartu') ?></td></tr>
        </table>
    </div>
    <?= form_close() ?>
    </div>
</div>

<div id="formpenduduk" style="display: none;position: static; background: #fff; padding: 10px;">
    <div class="data-input">
        <?= form_open('','id=form_penduduk')?>
        <?= form_hidden('kelurahan_id_pdd') ?>
        <fieldset>
            <tr><td>Nama Penduduk</td><td><?= form_input('nama', null, 'id=namapdd size=40 class="input-text"') ?>
            <tr><td>Telepon</td><td><?= form_input('telp', null, 'id=telppdd size=40 class="input-text"') ?>
            <tr><td>Alamat</td><td><?= form_textarea('alamat','','id=alamatpdd class=standar')?>
            <tr><td>Desa/Kelurahan</td><td><?= form_input('kelurahan', null, 'id=kelpdd size=40 class="input-text"') ?>
            <tr><td></td><td><span class="label" id="addr_pdd">&nbsp;</span>
        </table>
        <?= form_close() ?>

        
    </div>
</div>

<div id="form_cari" style="display: none;position: static; background: #fff; padding: 10px;">
    <div class="data-input">
        <form id="formcari">
        <table width="100%" class="inputan">
            <tr><td style="width: 150px;">Nama Pasien:</td><td><?= form_input('nama', null, 'id=nama_cari size=30 class="input-text"') ?></td></tr>
            <tr><td>Alamat:</td><td><?= form_textarea('alamat','','id=alamat_cari class="standar"')?></td></tr>
            <tr><td></td><td><?= form_button('', 'Cari', 'id=cari_pdd onclick=cari_penduduk(1)'); ?>
            <?= form_button('reset', 'Reset', 'id=reset_cari onclick=reset_pencarian()') ?></td></tr>
        </table>
        </form>
        <div class="list_penduduk"></div>
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