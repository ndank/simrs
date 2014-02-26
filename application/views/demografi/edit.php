<?php $this->load->view('message') ?>
<div class="kegiatan">
    <div class="titling"><h1><?= $title ?></h1></div>
    <script>
        $(function(){
            $('#nama').focus();
            $('button[id=addnewrow]').button({icons: {secondary: 'ui-icon-circle-plus'}});
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
            $('#simpan').button({icons: {secondary: 'ui-icon-circle-check'}}).click(function(){
                if ($('input[name=hd_lahir_tempat]').val() == ''){
                    custom_message('Peringatan','Tempat lahir tidak boleh kosong !','#lahir_tempat');
                }else{
                    $("<div title='Konfirmasi Simpan'>Anda yakin akan menyimpan data transaksi ini ?</div>").dialog({
                        modal: true,
                        autoOpen: true,
                        width: 320,
                        buttons: { 
                                "Ya": function() { 
                                    $('#form_edit').submit();
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
        
            $('#tgl_lahir').datepicker({
                changeYear : true,
                changeMonth : true
            });
        
            $('.angka').keyup(function(){
                Angka(this);
            });
        
            $('#nm_bapak').autocomplete("<?= base_url('demografi/get_penanggungjawab') ?>",
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
                    var str = '<div class=result>'+data.nama+'<br/>Kec: '+data.kecamatan+', Kab: '+data.kabupaten+', Prov: '+data.provinsi+'</div>';
                    return str;
                },
                width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
            }).result(
            function(event,data,formated){
                $(this).val(data.nama);
                $("input[name=hd_bapak]").val(data.id);
          
            });
        
            $('#nm_ibu').autocomplete("<?= base_url('demografi/get_penanggungjawab') ?>",
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
                    var str = '<div class=result>'+data.nama+'<br/>Kec: '+data.kecamatan+', Kab: '+data.kabupaten+', Prov: '+data.provinsi+'</div>';
                    return str;
                },
                width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
            }).result(
            function(event,data,formated){
                $(this).val(data.nama);
                $("input[name=hd_ibu]").val(data.id);
          
            });
        
        
            $('#lahir_tempat').autocomplete("<?= base_url('demografi/get_kabupaten') ?>",
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
                $("input[name=hd_kelurahan]").val(data.id);
                $('input[name=addr_kec]').val(data.kecamatan); 
                $('input[name=addr_kab]').val(data.kabupaten); 
                $('input[name=addr_prop]').val(data.provinsi);
            });
        
        
            $('#form_edit').submit(function() {                
                var id_antri = $('input[name=id_antri]').val();
                $.ajax({
                    type : 'POST',
                    url: '<?= base_url('demografi/edit_put') ?>/'+id_antri,               
                    data: $(this).serialize(),
                    cache: false,
                    success: function(data) {
                        alert_edit();
                        $('.kegiatan').html(data);
                    },
                    error: function(){
                        custom_message('Kesalahan Koneksi','Koneksi jaringan bermasalah, mohon cek!');
                    }
                }); 
                return false;
             
            })
        
        });
    
    
    </script>
    <div class="data-input">
        <fieldset>
            <?= form_open('', 'id=form_edit') ?>
            <?php foreach ($pasien as $row): ?>
                <?= form_hidden('id', $row->id) ?>
                <?= form_hidden('no_rm', $row->no_rm) ?>
                <?= form_hidden('hd_kelurahan', $row->kelurahan_id) ?>    
                <?= form_hidden('hd_lahir_tempat', $row->tempat_lahir_id) ?>
                <?= form_hidden('is_edited', "") ?>

                <?= form_hidden('bf_identitas_no', $row->identitas_no) ?>
                <?= form_hidden('bf_agama', $row->agama) ?>
                <?= form_hidden('bf_alamat', $row->alamat) ?>
                <?= form_hidden('bf_hd_kelurahan', $row->kelurahan_id) ?>
                <?= form_hidden('bf_pernikahan', $row->pernikahan) ?>
                <?= form_hidden('bf_pendidikan', $row->pendidikan_id) ?>
                <?= form_hidden('bf_pekerjaan', $row->pekerjaan_id) ?>
                <?= form_hidden('id_antri', $id_antri)?>


                <tr><td>No. rekam medik</td><td><span class="label"><?= $row->no_rm ?></span>
                <tr><td>Nama pasien*</td><td><?= form_input('nama', $row->nama ,'class="input-text enter" id=nama') ?>
                <tr><td>Jenis kelamin*</td><td><?= form_dropdown('gender', $kelamin, $row->gender,'class=enter') ?>
                <tr><td>Tanggal lahir*</td><td><?= form_input('lahir_tanggal', datefrompg($row->lahir_tanggal), 'id = tgl_lahir class="enter" size=10') ?>
                <tr><td>Tempat kelahiran</td><td><?= form_input('lahir_tempat', $row->tempat_lahir, 'id=lahir_tempat class="input-text enter"') ?>
                <tr><td>Golongan darah</td><td><?= form_dropdown('darah_gol', $darah, $row->darah_gol, 'class=enter') ?>
                <tr><td>Agama</td><td><?= form_dropdown('agama', $agama, $row->agama, 'class=enter') ?>
                <tr><td>Pendidikan</td><td><?= form_dropdown('pendidikan', $pendidikan, $row->pendidikan_id, 'class=enter') ?>
                <tr><td>Pekerjaan</td><td><?= form_dropdown('pekerjaan', $pekerjaan, $row->pekerjaan_id, 'class=enter') ?>
                <tr><td>Status pernikahan</td><td><?= form_dropdown('pernikahan', $stat_nikah, $row->pernikahan, 'class=enter') ?>
                <tr><td>Telepon</td><td><?= form_input('telp', $row->telp, 'class="angka enter"') ?>

                <tr><td></td><td>
                <tr><td><h2>Alamat</h2></td><td>

                <tr><td>Detail Alamat</td><td><?= form_textarea('alamat', $row->alamat, 'rows=3 class=enter') ?>
                <tr><td>Desa/kelurahan</td><td><?= form_input('addr_desa', $row->kelurahan, 'id=kelurahan class="input-text enter"') ?> <?= form_hidden('id_addr_desa') ?>
                <tr><td></td><td>
                <span class="label" id="addr">
                    <?php 
                        if(isset($row)){
                            echo "Kec: ".$row->kecamatan.", Kab: ".$row->kabupaten.", Prov: ".$row->provinsi;
                        }
                    ?>
                </span>   


                <tr><td></td><td>
                <tr><td><h2>Nomor Identitas</h2></td><td>
                <tr><td>No. KTP/SIM/<br/>Passport</td><td><?= form_input('identitas_no', $row->identitas_no, 'id = identitas_no class="enter"') ?>            
                <tr><td><?= form_button('edit', 'Simpan' ,'id=simpan'); ?></td><td>
            </table>
        </div>

    <?php endforeach; ?>
    <?= form_close() ?>
</div>


<?php die; ?>