<?php $this->load->view('message') ?>
<title><?= $title ?></title>
<div class="kegiatan">
    <script >
        var request;
        $('#dokter').focus();
        function remove_data(){
            $('#layanan, #dokter, input[name=id_layanan], input[name=id_dokter], input[name=antrian]').val('');
            $('#antri').html('');
        }
        
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
            $('.angka').keyup(function(){
                Angka(this);
            });

            $('#tgl_layan').datepicker({
                changeYear : true,
                changeMonth : true,
                minDate : +0
            });

            $('#simpan').button({icons: {secondary: 'ui-icon-circle-check'}});
            $('.resetan').button({icons: {secondary: 'ui-icon-refresh'}});
           

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
                     save_kunjungan(data.id_jurusan,$("#tgl_layan").val());
                }
                
            });

            $('#layanan').change(function(){
                if($(this).val() != ''){
                    save_kunjungan($(this).val() ,$("#tgl_layan").val());
                }
            });            

            $('#formantri').submit(function(){
                if(!request) {
                    request = $.ajax({
                        type : 'POST',
                        url: '<?= base_url("demografi/antrian_save") ?>',               
                        data: $(this).serialize(),
                        cache: false,
                        success: function(data) {
                            alert_sukses();
                            $('#simpan').attr('disabled', 'disabled');
                            $('#simpan').hide();
                            request = null;
                        }
                    });
                }             
                return false;
            });
        });

        function alert_sukses(){
            $( "#sukses" ).dialog({
                modal: true,
                buttons: {
                  Ok: function() {
                    $( this ).dialog( "close" );
                    $('.resetan').focus();
                  }
                }
            });
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

        function reset_all(){
            $('#loaddata').empty();
            $('#loaddata').load($.cookie('url'));
        }

        function action_save(){
            if($('#tgl_layan').val() == ''){
                custom_message('Peringatan', 'Tanggal Layanan tidak boleh kosong !', '#tgl_layan');
            }else if($('#layanan').val() == ''){
                custom_message('Peringatan','Layanan tidak boleh kosong !','#layanan');
            }else{
                $("<div title='Konfirmasi Simpan'>Anda yakin akan menyimpan data transaksi ini ?</div>").dialog({
                    modal: true,
                    autoOpen: true,
                    width: 320,
                    buttons: { 
                            "Ya": function() { 
                                $('#formantri').submit();
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
        }

    </script>
    <div class="titling"><h1><?= $title ?></h1></div>
    <?= form_open('','id = formantri') ?>
    <div class="data-input">
        <table width="100%" class="inputan">Pasien</legend>
            <?= form_hidden('id_penduduk', (isset($id_penduduk))?$id_penduduk:"")?>
            <?= form_hidden('alamat', isset($alamat)?$alamat:"") ?>
            <tr><td>Nomor RM.</td><td><?= form_hidden('no_rm', $no_rm) ?><span class="label"><?= $no_rm ?><?= isset($norm)?str_pad($norm, 6, '0', STR_PAD_LEFT):'' ?></span>
            <tr><td>Nama</td><td><?= form_hidden('nama', $nama) ?><span class="label"><?= $nama ?></span>
            <tr><td>Jenis Kelamin</td><td><?= form_hidden('kelamin', $gender) ?><span class="label"><?= ($gender == 'L')?'Laki-laki':'Perempuan' ?></span>          
            <tr><td>Umur</td><td><?= form_hidden('tgl_lahir', $lahir_tanggal) ?>
            <span class="label">
                 <?php
                    $a = explode("/", $lahir_tanggal);
                    if (sizeof($a) === 3) {
                        echo hitungUmur(date2mysql($lahir_tanggal));
                    }
                    
                ?>
                
            </span> 
            <tr><td>Telepon</td><td><?= form_hidden('tlpn', isset($telp_no)?$telp_no:"") ?><span class="label"><?= isset($telp_no)?$telp_no:"" ?></span>
            <tr><td>Alamat Jalan</td><td><span class="label"><?= isset($alamat)?$alamat:"" ?></span>
            <tr><td>Kelurahan</td><td><span class="label"><?= isset($kelurahan)?$kelurahan:'' ?></span>
            <?= form_hidden('id_kelurahan', isset($id_kelurahan)?$id_kelurahan:'') ?>    
            <?php if(isset($id_kelurahan)):?>
            <tr><td></td><td><span class="label">Kec: <?= isset($kecamatan)?$kecamatan:'' ?>, Kab: <?= isset($kabupaten)?$kabupaten:'' ?>, Prov: <?= isset($provinsi)?$provinsi:'' ?></span>
            <?php endif; ?>
            <tr><td>Tanggal</td><td><?= form_input('tgl_layan',date('d/m/Y'),'id=tgl_layan size=10')?>
            <tr><td>Nama Dokter</td><td><?= form_input('dokter','','id=dokter size=30 class=enter')?>
            <?= form_hidden('id_dokter')?>
            <tr><td>Jenis Layanan*</td><td><?= form_dropdown('id_layanan', $layanan, null, 'id=layanan class="standar enter"') ?>
            <tr><td>No. Antrian</td><td><span class="label" id="antri"></span>
            <?= form_hidden('antrian')?>
            <tr><td></td><td><?= form_button('simpanutama', 'Simpan', 'id=simpan onclick=action_save()'); ?>
            <?= form_button('','Reset','onclick=reset_all() class=resetan')?>
        </table>
    </div>


    <?= form_close() ?>

</div>
<?php die; ?>