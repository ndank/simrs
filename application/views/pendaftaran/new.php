<?php $this->load->view('message') ?>
<title><?= $title ?></title>
    <script>
        $(function(){
            $('#telp').focus();
            $('.enter').live("keydown", function(e) {
                var n = $(".enter").length;
                if (e.keyCode === 13) {
                    var nextIndex = $('.enter').index(this) + 1;
                    if (nextIndex < n) {                        
                        $('.enter')[nextIndex].focus();
                    } else {
                        $('#save').focus();
                    }
                }
            });

            $('#save').button({icons: {secondary: 'ui-icon-circle-check'}}).click(function(){
                if($('input[name=unit_layan]').val() == ''){
                    $('.msg').fadeIn('fast').html('Layanan harus diisi<br/> atau pilih layanan yang ada !');
                    $('#lay').focus();
                }else{
                    $("<div title='Konfirmasi Simpan'>Anda yakin akan menyimpan data transaksi ini ?</div>").dialog({
                        modal: true,
                        autoOpen: true,
                        width: 320,
                        buttons: { 
                                "Ya": function() { 
                                    $('#formdaftar').submit();
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

            });
            $('#reset, #reset_cari').button({icons: {secondary: 'ui-icon-refresh'}});

            $('#reset').click(function() {
                $('#loaddata').empty().load('<?= base_url("pendaftaran/search/1") ?>');
            });

            $("#tgl").datepicker({
                changeYear : true,
                changeMonth : true,
                minDate : +0
            });   
        
            $('#formdaftar').submit(function(){
                var Url = '<?= base_url("pendaftaran/new_post") ?>';         
                              
                $.ajax({
                    type : 'POST',
                    url: Url,               
                    data: $(this).serialize(),
                    cache: false,
                    success: function(data) {
                        $('#loaddata').html(data); 
                        alert_tambah();
                                                                    
                    }
                });
               
                return false;
                
             
            });

            $('#lay').autocomplete("<?= base_url('pendaftaran/get_unit_layanan') ?>",
            {
                parse: function(data){
                    var parsed = [];
                    for (var i=0; i < data.length; i++) {
                        parsed[i] = {
                            data: data[i],
                            value: data[i].nama // nama field yang dicari
                        };
                    }
                    $('input[name=unit_layan]').val('');
                    return parsed;
                },
                formatItem: function(data,i,max){
                    var str = '<div class=result><b>'+data.nama+'</b></div>';
                    return str;
                },
                width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
            }).result(
            function(event,data,formated){
                $(this).val(data.nama);
                $('input[name=unit_layan]').val(data.id); 
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


        });

    </script>
    <div class="titling"><h1><?= $title ?></h1></div>
    <?= form_open('', 'id=formdaftar') ?>
    <?php foreach ($pasien as $row): ?>
        <div class="data-input">
            <table width="100%" class="inputan">Pasien</legend>
                <div class='msg' id="msg_daftar"></div>
                <?= form_hidden('dinamis', $row->dinamis_penduduk_id) ?>
                <?= form_hidden('id_antri', $id_antri)?>

                <table width="100%" class="inputan">
                    <tr><td style="width: 150px;">Nama Pasien:</td><td><span class="label"><?= $row->nama ?><?= form_hidden('id_penduduk', $row->id) ?></span></td></tr>
                    <tr><td>No. Rekam Medik:</td><td><span class="label"><?= $row->no_rm ?><?= form_hidden('no_rm', $row->no_rm) ?></span></td></tr>
                    <tr><td>Umur:</td><td><span class="label"><?= hitungUmur($row->lahir_tanggal) ?></span></td></tr>
                    <tr><td>Jenis Kelamin:</td><td><span class="label"><?= ($row->gender == "L") ? "Laki-laki" : "Perempuan" ?></span></td></tr>
                    <tr><td>Golongan Darah:</td><td><span class="label"><?= ($row->darah_gol == '') ? '-' : $row->darah_gol ?></span></td></tr>
                    <tr><td>Telepon:</td><td><span class="label"><?= form_input('tlpn', $row->telp, 'id=telp class="enter"') ?></span></td></tr>
                    <tr><td>Tgl Pelayanan:</td><td><span class="label"><?= $tgl_layan ?><?= form_hidden('tgl_layan', $tgl_layan)?></span></td></tr>
                </table>
            </table>

            <table width="100%" class="inputan">Antrian</legend>
                <table width="100%" class="inputan">
                     <tr><td style="width: 150px;">Nama Dokter:</td><td><span><?= $dokter ?><?= form_hidden('id_dokter',$id_dokter) ?></td></tr>
                    <tr><td style="width: 150px;">Jenis Layanan:</td><td><span><?= $layanan ?><?= form_hidden('unit_layan',$layanan_id) ?></td></tr>
                    <tr><td>Antri:</td><td><span><?= $no_antri ?></span><?= form_hidden('no_antri', $no_antri)?></td></tr>
                </table>
            </table>

            <table width="100%" class="inputan">Data Pelengkap</legend>
                <table width="100%" class="inputan">
                    <tr><td style="width: 150px;">Keb. Pelayanan:</td><td><?= form_dropdown('jenis_layan', $jenis_layan, null, 'id=jenis_layan class="standar enter"') ?></td></tr>
                    <tr><td>Kriteria Layanan:</td><td><?= form_dropdown('krit_layan', $krit_layan, null, 'id=krit_layan class="standar enter"') ?></td></tr>
                </table>
                
                <tr><td><h2>Rujukan</h2></td><td>
                <table width="100%" class="inputan">
                    <tr><td style="width: 150px;">Nama Instansi Perujuk:</td><td><?= form_input('instansi', '', 'id=instansi size=30 class="input-text enter"') ?><?= form_hidden('id_instansi') ?></td></tr>
                    <tr><td>Nama Tenaga Perujuk:</td><td><?= form_input('nakes', '', 'id=nakes size=30 class="input-text enter"') ?></td></tr>
                    <tr><td>Alasan Datang:</td><td><?= form_dropdown('alasan', $alasan_datang,array(), 'id=alasan class="standar enter"') ?><?= form_hidden('id_alasan') ?></td></tr>
                    <tr><td>Keterangan Kecelakaan:</td><td><?= form_textarea('keterangan_kecelakaan','','row=3 class="standar enter"')?></td></tr>
                </table>

                <tr><td><h2>Penanggung Jawab</h2></td><td>
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
                    <tr><td style="width: 150px;"></td><td><?= form_button(NULL, 'Simpan', 'id=save') ?> <?= form_button(null, 'Reset', 'id=reset') ?></td></tr>
                </table>
            </table>
            <?php endforeach; ?>
            <?= form_close() ?>
        </table>
    </div> <br/><br/>