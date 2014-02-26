<?php $this->load->view('message') ?>
<title><?= $title ?></title>
<div class="titling"><h1><?= $title ?></h1></div>
<div class="kegiatan">
    <script type="text/javascript">
        var cetak_kartu;
        var cetak_antri;
        var tindak;
        var dWidth = $(window).width();
        var dHeight= $(window).height();
        var x = screen.width/2 - dWidth/2;
        var y = screen.height/2 - dHeight/2;

        $(function(){
            $('#tabs').tabs();
            $('#reset, #label_keluar').hide();
            $('input[type=submit]').each(function(){ $(this).replaceWith('<button type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'">' + $(this).val() + '</button>');});
            $('button[type=submit]').button({icons: {secondary: 'ui-icon-circle-check'}});
            $('#reset').button({icons: {secondary: 'ui-icon-refresh'}});
            $('.print').button({icons: {secondary: 'ui-icon-print'}});
            $('.plus').button({icons: {secondary: 'ui-icon-circle-plus'}});
            $('.cetak_kartu, .cetak_antri, #cetak_rm, #lembarpertama').button({icons: {secondary: 'ui-icon-print'}});
            $('#lembarpertama').click(function() {
                var perawatan = 'Biasa';//$('#perawatan').html();
                var no_daftar = $("input[name=no_daftar]").val();
                window.open('<?= base_url("pendaftaran/cetak_lembar_pertama") ?>/'+no_daftar+'/'+perawatan,'Myprint','width='+dWidth+', height='+dHeight+', left='+x+',top='+y);
            });

            $('.cetak_kartu').click(function(){
                var no_daftar = $("input[name=no_daftar]").val();
                var no_rm = $("input[name=no_rm]").val();
                var id_pk = $('input[name=id_pelayanan]').val();
                
                window.open('<?= base_url() ?>pendaftaran/cetak_kartu_get/'+no_rm+'/'+no_daftar+'/'+id_pk+'/poliklinik','Myprint','width='+dWidth+', height='+dHeight+', left='+x+',top='+y);
            });
            $("#waktu_keluar").datetimepicker({
                changeYear : true,
                changeMonth : true
            });   

            $('#search').button({icons: {secondary: 'ui-icon-circle-check'}});
           
            $('#edit_demografi').button({icons: {secondary: 'ui-icon-pencil'}});
            
            
            // pilihan konfirmasi
            $('#edit_demografi').click(function(){
                var norm = $('input[name=no_rm]').val();
                $('#dialog_konfirmasi').dialog('close');
                $.ajax({
                    url: '<?= base_url('demografi/edit') ?>/'+norm,
                    data: '',
                    cache: false,
                    success: function(msg) {
                        $('.kegiatan').html(msg);
                        
                    }
                })
            });



             $('#instansi_rujuk').autocomplete("<?= base_url('pendaftaran/load_data_instansi_relasi/') ?>",
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
                $('input[name=id_instansi_rujuk]').val(data.id);
            });
        
            
            $('#formtindak').submit(function(){
                $("<div title='Konfirmasi'>Apakah pasien <b><?= $pasien->nama ?></b> sudah meyelesaikan proses pemeriksaan?</div>").dialog({
                modal: true,
                autoOpen: true,
                width: 320,
                buttons: { 
                        "Ya": function() { 
                            discharge();
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
                return false;
            });
        
    
        });

        function discharge(){
            var no_daftar = $("input[name=no_daftar]").val();
            if(!tindak) {
                tindak = $.ajax({
                    type : 'POST',
                    url: '<?= base_url() ?>pendaftaran/discharge/'+no_daftar,
                    data : $('#formtindak').serialize(),
                    dataType : 'json',
                    success: function(data) {
                        if (data.status == 'inap') {
                            custom_message('Peringatan', 'Pasien bersangkutan masih dalam pelayanan rawat inap !');
                        }else if(data.status == 'bayar'){
                            custom_message('Peringatan', 'Pasien bersangkutan belum melunasi tagihan pelayanan rumah sakit !');
                        }else{
                            alert_edit();
                            reset_all();
                            $('#label_keluar').show();
                            tindak = null;
                        }
                    }
                });
            }
        }
        function reset_all(){
            $('input[type=text], input[name=id_instansi_rujuk]').attr('disabled', 'disabled');
            $('#formtindak').find("input:radio").attr('disabled', 'disabled');
            $('#reset').show();
            $('#save').hide();
        }

        function reset_page(){
            $('#loaddata').empty();
            $('#loaddata').load($.cookie('url'));
        }

        function cetak_jawaban_rujukan(no_daftar){
            window.open('<?= base_url("pendaftaran/cetak_surat_jawaban_rujukan") ?>/'+no_daftar,'Myprint','width='+dWidth+', height='+dHeight+', left='+x+',top='+y);
        }

        function cetak_rujukan(no_daftar){
            window.open('<?= base_url("pendaftaran/cetak_surat_rujukan") ?>/'+no_daftar,'Myprint','width='+dWidth+', height='+dHeight+', left='+x+',top='+y);
        }

        function cetak_antrian(id_pk, arrive){
            var no_daftar = $("input[name=no_daftar]").val();
            if(arrive == 1) {
                $.ajax({
                    url: '<?= base_url() ?>pendaftaran/set_arrive_time/'+no_daftar,
                    dataType: '',
                    timeout : 10000,
                    success: function( response ) {
                       $('#loaddata').html(response);
                    }
                });
            }
            window.open('<?= base_url() ?>pendaftaran/cetak_no_antri_get/'+id_pk,'Cetak Nomor Antri Pendaftaran','width='+dWidth+', height='+dHeight+', left='+x+',top='+y);
        }

       

    </script>
    

    <div id="tabs">
        <ul>
            <li><a href="#tabs-1">Detail Kunjungan Pasien</a></li>
        </ul>
        <div id="tabs-1">
        <?php if ($pasien != null): ?>
            <?php 
                $row = $pasien;
                $cetak_kartu = $row->is_cetak_kartu; 
                $alert = '';     
                if ($row->arrive_time != null) {
                   $alert =  '<span class="status status-success">Terdaftar</span>';
                } else {
                   $alert = '<span class="status status-warning">Belum Antri</span>';
                }
           ?>
        <?= form_hidden('no_rm', $row->no_rm); ?>
        <table width="100%" class="inputan">            
            <tr><td>No. Pendaftaran:</td><td><?= $row->no_daftar ?><?= form_hidden('no_daftar', $row->no_daftar) ?> <?= $alert?></td></tr>
            <tr><td style="width: 150px;">Tgl&nbsp;Pendaftaran:</td><td><?= datetimefmysql($row->tgl_daftar) ?></td></tr>
                <!--<tr><td></td><td><?= form_button("cetak_kartu", "Cetak Kartu Pasien", "class=cetak_kartu"); ?> -->
            <tr><td></td><td><?= form_button(null, "Lembar Pertama RM", "id=lembarpertama") ?></td></tr>
            <!--<?= form_button('','Cetak Surat Rujukan', 'class=print onclick=cetak_rujukan('.$row->no_daftar.')') ?> 
            <?= form_button('','Cetak Surat Balasan Rujukan', 'class=print onclick=cetak_jawaban_rujukan('.$row->no_daftar.')') ?> -->
        </table>
        
        <div class="data-list">
            
                <h2>Detail Kunjungan Pasien</h2>
                <?php 
                    $id_pelayanan = '';
                    if($row->jenis_rawat != 'IGD'){
                        echo form_button('','Tambah Antrian Kunjungan','class=plus onclick=tambah_antrian_pelayanan()') ;
                    }
                ?>
                <br/>
                <table class="list-data" width="100%">
                    <tr>
                        <th width="4%">No.</th>
                        <th width="13%">Waktu Pelayanan</th>
                        <th width="30%">Unit Layanan</th>
                        <th width="15%">Jenis Pelayanan</th>
                        <th width="30%">DPJP</th>
                        <th width="5%">No.Antrian</th>
                        <th width="5%">#</th>
                    </tr>
                    <?php foreach ($pelayanan as $key => $value): ?>
                        <?php 
                            if ($key === 0) {
                                $id_pelayanan = $value->id;
                            }
                        ?>
                        <tr>
                            <td align="center"><?= $key+1 ?></td>
                            <td><?= ($value->waktu != null)?datetimefmysql($value->waktu, true):'' ?></td>
                            <td>
                                <?php
                                    if(($value->no_antri !== null) & ($value->jenis === 'Rawat Jalan')){
                                        echo $value->unit_layanan.", ".$value->jenis_jurusan;    
                                    } 
                                    
                                ?>
                            </td>
                            <td>
                                <?= $value->jenis ?>
                                <?php 
                                    if(($value->no_antri === null) & ($value->jenis === 'Rawat Jalan')){
                                        echo " (IGD)";
                                    }else if(($value->no_antri !== null) & ($value->jenis === 'Rawat Jalan')){
                                        echo " (Poliklinik)";
                                    }
                                ?>
                            </td>
                            <td><?= $value->nama_pegawai ?></td>
                            <td align="center"><?= $value->no_antri ?></td>
                            <?php
                                $arrive = 0; 
                                if ($key == 0) {
                                    $arrive = 1;
                                }
                            ?>                            
                            <td align="center">
                                <?php
                                    if(($value->no_antri != null) ){ ?>
                                        <img style="cursor:pointer;" src="<?= base_url('assets/images/icons/print.png') ?>" onclick="cetak_antrian('<?= $value->id ?>','<?= $arrive ?>');" title="Klik untuk cetak nomor antrian" />
                                    <?php }else{
                                        echo "&checkmark;";
                                    }
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?= form_hidden('id_pelayanan', $id_pelayanan)?>
                </table>
            
        </div>
                    
        <table width="100%" cellpadding="0" cellspacing="0">
            <tr valign="top"><td width="50%">
            <table width="100%" class="inputan">
                <tr><td colspan="2"><h2>Data Pasien</h2></td></tr>
                <tr><td style="width: 150px;">Nama Pasien:</td><td><?= $row->nama ?></td></tr>
                <tr><td>No.&nbsp;RM:</td><td><?= $row->no_rm ?></td></tr>
                <tr><td>Umur:</td><td><?= hitungUmur($row->lahir_tanggal) ?> (<?= datefrompg($row->lahir_tanggal) ?>)</td></tr>
                <tr><td>Jenis Kelamin:</td><td><?= ($row->gender == 'L') ? 'Laki-laki' : '' ?><?= ($row->gender == 'P') ? 'Perempuan' : '' ?></td></tr>
                <tr><td>Golongan Darah:</td><td><?= $row->darah_gol ?></td></tr>
                <tr><td>Telepon:</td><td><?= $row->telp ?></td></tr>


                <tr><td>Waktu Datang:</td><td><?= ($row->arrive_time != '') ? datetime($row->arrive_time) : $row->arrive_time ?> </td></tr>
                <tr><td>Status Perawatan:</td><td id="perawatan"><?= $row->keb_rawat ?></td></tr>
                <!--<tr><td>Jenis Layanan:</td><td><?= $row->jenis_layan ?></td></tr>
                <tr><td>Kriteria Layanan:</td><td><?= $row->krit_layan ?></td></tr>-->
                <tr><td>Staff Pendaftar:</td><td><?= $row->petugas_daftar ?></td></tr>
                <!--<tr><td>Staff Konfirmasi:</td><td><?= $row->petugas_confirm ?></td></tr>-->
            </table>
            </td><td width="50%">
            <table width="100%" class="inputan">
                <tr><td colspan="2"><h2>Penanggung Jawab</h2></td></tr>
                <tr><td style="width: 150px;">Nama:</td><td><?= $row->nama_pjwb ?></td></tr>
                <tr><td>Alamat:</td><td><?= $row->alamat_pjwb ?></td></tr>
                <tr><td>No. Telp:</td><td><?=  $row->telp_pjwb?></td></tr>
                <tr><td colspan="2"><h2>Rujukan</h2></td></tr>
                <tr><td>Instansi Perujuk:</td><td><?= $row->rs_perujuk ?></td></tr>
                <tr><td>Nakes Perujuk:</td><td><?= $row->nakes_perujuk ?></td></tr>  
                <tr><td>Alasan Pasien Datang:</td><td><?= $row->alasan_datang ?></td></tr>
                <tr><td>Keterangan Tambahan:</td><td><?= $row->keterangan_kecelakaan ?></td></tr>
                <tr><td colspan="2"></td></tr>
            </table>
            </td></tr>
        </table>

        <?php if($row->arrive_time != null):?>
        <span id="label_keluar"><h2>SUDAH KELUAR</h2></span>
        <?= form_open('','id=formtindak')?>
        <table width="100%" class="inputan">
            <?php                
                if ($row->waktu_keluar != null) {
                    echo "<tr><td></td><td><h2>SUDAH KELUAR</h2>";
                }
            ?>
            <tr><td style="width: 150px;"><h2>Tindak Lanjut</h2></td><td></td></tr>
            <tr><td>Waktu Keluar:</td><td><span class="label"><?= form_input('waktu_keluar', ($row->waktu_keluar != null)?datetimefmysql($row->waktu_keluar, true):date('d/m/Y H:i'), 'id=waktu_keluar size=15') ?></span></td></tr>
            <tr><td>Kondisi Keluar:</td><td>
            <?=form_dropdown('kondisi_keluar', array('Hidup'=>'Hidup','Mati'=>'Mati'), 'Hidup', 'id=kondisi_keluar')?></td></tr>
            <tr><td>Penolakan Perawatan</td><td>
            <?=form_dropdown('menolak', array('Tidak'=>'Tidak','Ya'=>'Ya'), 'Tidak', 'id=menolak')?>
            <?= form_hidden('diterima', 'Tidak', 'id=diterima') ?></td></tr>
            <?php
                if ($row->waktu_keluar == null) {
                    echo "<tr><td></td><td>".form_submit('simpan', 'Discharge Pasien', 'id=save title="Klik untuk discharge pasien"');
                    echo form_button('', 'Reset', 'id=reset onclick=reset_page()')."</td></tr>";
                }
            ?>
        </table>
            <?= form_close() ?>
            <?php endif; ?>
        </div>   
        </div>
    </div>

    <?php else: ?>

        <div class="circle">
            <center>
                <h1>Data Tidak Ditemukan</h1>
            </center>
        </div>
    <?php endif; ?>
    <div id="formantri" style="display:none;">
        <fieldset>
            <?= form_open('','id=form_antri') ?>
            <?= form_hidden('id_dokter')?>
            <?= form_hidden('antrian')?>
            <table width="100%" class="inputan" cellpadding="0" cellspacing="0">
                <tr><td style="width: 150px;">Tanggal:</td><td><span id="tanggal"><?= date('d/m/Y') ?></span></td></tr>
                <tr><td>Nama Dokter:</td><td><?= form_input('dokter','','id=dokter size=30 class="input-text enter"')?></td></tr>
                <tr><td>Jenis Layanan:</td><td><?= form_dropdown('id_layanan', $layanan, array(), 'id=layanan class="standar enter"') ?></td></tr>
                <tr><td>No. Antrian:</td><td><span id="antri"></span></td></tr>
            </table>            
            <?= form_close() ?>
        </table>
    </div>

<script type="text/javascript">
    $(function(){
        $('#formantri').dialog({
                autoOpen: false,
                title :'Tambah Antrian Kunjungan',
                height: 200,
                width: 400,
                modal: true,
                resizable : false,
                buttons: [ 
                    { text: "Simpan", click: function() { 
                            save_antrian();
                        } 
                    }, 
                    { text: "Batal", click: function() {
                            $('#antri').html('');
                            $('#dokter, #layanan, input[name=id_dokter],input[name=id_layanan]').val('');
                            $( this ).dialog( "close" ); 
                        } 
                    } 
                ],
                close : function(){
                    clear_antrian();
                }
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
    });

    function tambah_antrian_pelayanan(){
        $('#formantri').dialog('open');
    }

    function save_antrian(){
        var no_daftar = $("input[name=no_daftar]").val();
         if($('#layanan').val() == ''){
            custom_message("Peringatan", "Jenis Layanan harus dipilih !", "#layanan");
         }else{
            $.ajax({
                type : 'POST',
                url: '<?= base_url("pendaftaran/antrian_pelayanan") ?>/'+no_daftar,               
                data: $('#form_antri').serialize(),
                cache: false,
                success: function(data) {
                    $('#loaddata').html(data);
                     $('#formantri').dialog( "close" );
                }
            });
         } 

    }

    function clear_antrian(){
        $('input[name=id_dokter], input[name=antrian], #dokter, #layanan').val('');
        $('#antri').html('');
    }

    function save_kunjungan(layanan_id, tgl){
        if(layanan_id !== 'igd'){
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
        }else{
            $('#antri').html('');
            $('input[name=antrian]').val('');
        }
        
        return false;
    }
</script>



    <?php die ?>
</div>
