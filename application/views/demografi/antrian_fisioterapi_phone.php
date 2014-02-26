<?php $this->load->view('message') ?>
<title><?= $title ?></title>
<div class="kegiatan">
    <script>
        var Dtgl_lahir;
        $('.angka').keyup(function(){Angka(this);});
        $('#nama_pdd').hide();   
        $('#nama').focus(); 
        $(function(){
            $('.enter').live("keydown", function(e) {
                var n = $(".enter").length;
                if (e.keyCode === 13) {
                    var nextIndex = $('.enter').index(this) + 1;
                    if (nextIndex < n) {                        
                        $('.enter')[nextIndex].focus();
                    } else {
                        $('#simpan').focus();
                    }
                }
            });  
            $('#bt_cari').click(function(){
                $('#form_cari').dialog('open');
            });
       
            $('#tgl_lahir').datepicker({
                changeYear : true,
                changeMonth : true,
                maxDate : +0
            });
        
            $('button[id=cari_pdd]').button({icons: {secondary: 'ui-icon-search'}});
            $('input[type=submit]').each(function(){$(this).replaceWith('<button type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'">' + $(this).val() + '</button>');});
             $('#simpan').button({icons: {secondary: 'ui-icon-circle-check'}});
            $('#reset, #reset_cari').button({icons: {secondary: 'ui-icon-refresh'}});
            $('#reset').click(function(){
                $('#loaddata').empty();
                $('#loaddata').load($.cookie('url'));
            
            });

            $('#nama_pdd').autocomplete("<?= base_url('inv_autocomplete/load_penduduk') ?>",
            {
                parse: function(data){
                    var parsed = [];
                    for (var i=0; i < data.length; i++) {
                        parsed[i] = {
                            data: data[i],
                            value: data[i].nama // nama field yang dicari
                        };
                    }
                    $('#telp,#tgl_lahir,#kelamin').val('');
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
                $('#kelamin').val(data.gender);
                $('#tgl_lahir').val(datefmysql(data.lahir_tanggal));
                $('#telp').val(data.telp);
                $('#alamat').val(data.alamat);
                if(data.kelurahan_id != null){
                    $.ajax({
                        url: '<?= base_url('demografi/detail_kelurahan') ?>/'+data.kelurahan_id,
                        cache: false,
                        dataType :'json',
                        success: function(data) {
                            $('#kelurahan').val(data.nama);
                            $('input[name=id_kelurahan]').val(data.id);
                            $('#addr').html("Kec: "+data.kecamatan+", Kab: "+data.kabupaten+", Prov: "+data.provinsi);
                        }
                    });
                }
                 
            });

            $("#formnew").submit(function(){       
                $.ajax({
                    type: 'POST',
                    url: '<?= base_url("demografi/antrian_fisioterapi_phone_save") ?>/',
                    cache: false,
                    data: $('#formnew').serialize()+'&tgl_lahir='+Dtgl_lahir,
                    dataType :'json',
                    success: function(data) {
                       if(data.status == true){
                            custom_message('Berhasil','Transaksi Berhasil, Silahkan dikonfirmasi');
                            $('#simpan').hide();
                       }else{
                            custom_message('Transaksi Gagal','Data gagal di masukkan silahkan cek kembali data yg dimasukkan');
                       }
                    },
                    error: function() {
                        custom_message('Transaksi Gagal','Data gagal di masukkan silahkan cek kembali data yg dimasukkan');
                    }
                });
                return false;
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

            $('#tgl_layan').change(function(){
                get_antrian($(this).val());
            });
        
        });
    
        function birthByAge(umur){
            var today = new Date();
            var birth = today.getDate()+"/"+(today.getMonth()+1)+"/"+(today.getYear()+1900-umur);
    
            return birth;
        }

        function cetak_antrian(no_daftar){
            window.open('<?= base_url() ?>pendaftaran/cetak_no_antri/'+no_daftar, 'Cetak Nomor Antri Pendaftaran', 'location=1,status=1, scrollbars=1 width=600px, height=400px ')           
        }

        function get_antrian( tgl){
            $.ajax({
                type : 'GET',
                url: '<?= base_url('demografi/get_antrian_non') ?>',               
                data: "tgl_layan="+tgl,
                dataType : 'json',
                cache: false,
                success: function(data) {
                    $('#antri').html(data.antrian);
                    $('input[name=antrian]').val(data.antrian);
                }
            }); 
            return false;
        }

        function konfirmasi(){
             var Dusia = $("#usia option:selected").val();             

            if ($('#nama').val() == '') {
                custom_message('Peringatan', 'Nama pasien tidak boleh kosong !', '#nama');
                return false;
            }

            if ($('#alamat').val() == '') {
                custom_message('Peringatan', 'Alamat jalan tidak boleh kosong !', '#alamat');
                return false;
            }
            
            if (Dusia == 'umur'){
                Dtgl_lahir = birthByAge($("#umur").val());
                
            }else{
            
                if($('#tgl_lahir').val() == '00/00/0000'){
                    custom_message('Peringatan', 'Tanggal lahir tidak valid !', '#tgl_lahir');
                    return false;
                }else{
                    Dtgl_lahir = $("#tgl_lahir").val();
                }
            }

            $("<div title='Konfirmasi Simpan'>Anda yakin akan menyimpan data transaksi ini ?</div>").dialog({
                modal: true,
                autoOpen: true,
                width: 320,
                buttons: { 
                        "Ya": function() { 
                            $('#formnew').submit();
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
        }

    </script>
    <div class="titling"><h1><?= $title ?></h1></div>
    <?= form_open('','id = formnew') ?>
    <div class="data-input">

        <table width="100%" class="inputan">Pasien</legend>
            <div class='msg' id="msg_new"></div>
            
            <tr><td>Nama Penduduk*:</td><td>
            <?= form_input('nama', null, 'id =nama size=40 class="enter"') ?>
            <?= form_hidden('id_penduduk') ?>
            <?= form_hidden('jenis','phone') ?>
            <div class="search_pdd" id="bt_cari" title="Klik untuk mencari data di database kependudukan"></div>
            
            <tr><td>Alamat Jalan*:</td><td><?= form_textarea('alamat','','id=alamat class="enter"')?>
            <tr><td>Kelurahan:</td><td><?= form_input('kelurahan', '', 'id=kelurahan size=40 class="enter"') ?> 
            <?= form_hidden('id_kelurahan') ?>    
            <tr><td></td><td><span class="label" id="addr"></span> 
            <tr><td>Jenis Kelamin:</td><td><?= form_dropdown('kelamin', $kelamin, '', 'id = kelamin class="enter"') ?>        
            <tr><td><?= form_dropdown('usia', $tgl_lahir, '', 'id=usia style=width:100px') ?></td><td>
            <span class="label" style="margin-top:6px;">
            <?= form_input('', null, 'id=tgl_lahir class="special enter" size=10') ?>
            <?= form_input('', null, 'id=umur class=angka size=10') ?>
            </span>
            <tr><td>Telepon:</td><td><?= form_input('tlpn', null, 'id=telp class="angka enter" size=40') ?>       
        </table>
        <table width="100%" class="inputan">Antrian</legend>
            <tr><td>Tanggal Antri:</td><td><?= form_input('tgl_layan',date('d/m/Y'),'id=tgl_layan size=15 class=enter')?>
            <tr><td>Nama Layanan:</td><td><span class="label">Fisioterapist</span><?= form_hidden('id_jurusan', $id_jurusan) ?>
            <tr><td>No. Antrian:</td><td><span class="label" id="antri"><?= $antri?></span><?= form_hidden('antrian', $antri)?>
            <tr><td></td><td>
            <tr><td></td><td><?= form_button('','Simpan','id=simpan onclick=konfirmasi()')?><?= form_button('','Reset', 'id=reset') ?>
        </table>
    </div>

    <?= form_close() ?>


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

             $('#tgl_layan').datepicker({
                changeYear : true,
                changeMonth : true,
                minDate : +0
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
            if(kel_id != null){
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

</div>
<?php die; ?>