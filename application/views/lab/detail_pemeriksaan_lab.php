<?php
$nilai_rujukan = array(''=>'Pilih...','L' => 'Low', 'N' => 'Neutral', 'H' => 'High');
?>
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
                                '<tr><td>Hasil:</td><td><?= form_input('hasil', NULL, 'id=hasil size=10') ?></td></tr>'+
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
                        $(this).dialog().remove();
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
                        url: '<?= base_url("pelayanan/laboratorium_load_data_pemeriksaan") ?>/'+id_pl,
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
                }
                $.ajax({
                    url: '<?= base_url('pelayanan/pemeriksaan_lab_save') ?>',
                    data: $('#save_hasil_lab').serialize(),
                    dataType: 'json',
                    type: 'POST',
                    success: function(data) {
                        if (data === true) {
                            custom_message('Informasi', 'Data berhasil di simpan');
                            $('#form_hasil_lab').dialog().remove();
                            detail_pemeriksaan(id_pelayanan,'laboratorium');
                        }
                    }
                });
                return false;
            });
    }

</script>
<div id="dialog_edit_lab"></div>
<div class="data-input">
<fieldset>
    <h1><?= $subtitle?></h1>
    <table width="100%" style="border-spacing:0;">
        <tr valign="top">
            <td width="50%">
                <div class="msg" id="msg_detail"></div>
                <table width="100%"><tr><td width="50%">
                    <table width="100%" style="line-height: 18px;">
                        <tr><td style="width: 165px;">No. RM:</td><td><?= isset($pasien->no_rm)?$pasien->no_rm:NULL ?></td></tr>
                        <tr><td>Nama Pasien:</td><td id="nama"><?= isset($pasien)?$pasien->pasien:NULL ?></td></tr>
                        <tr><td>Alamat Jalan:</td><td id="alamat"><?= isset($pasien)?$pasien->alamat:NULL ?></td></tr>
                        <tr><td>Wilayah:</td><td id="wilayah"><?= isset($pasien)?$pasien->kelurahan." ".$pasien->kecamatan:NULL ?></td></tr>
                    </table> 
                </td>
                <td width="50%">
                    <table width="100%" style="line-height: 18px;">    
                        <tr><td>Gender:</td><td id="gender"><?= isset($pasien)?(($pasien->gender=="L")?"Laki-Laki":"Perempuan"):NULL ?></td></tr>
                        <tr><td>Umur:</td><td id="umur"><?= isset($pasien)?hitungUmur($pasien->lahir_tanggal):NULL ?></td></tr>
                        <tr><td>Pekerjaan:</td><td id="pekerjaan"><?= isset($pasien)?$pasien->pekerjaan:NULL ?></td></tr>
                        <tr><td>Pendidikan:</td><td id="pendidikan"><?= isset($pasien)?$pasien->pendidikan:NULL ?></td></tr>
                    </table>
                    </td>
                </tr>
                </table>
            </td>
        </tr>
    </table>
</table>

<fieldset>
    <table class="list-data" width="100%">
            <thead>
                <tr>
                    <th width="5%">No.</th>
                    <th width="20%">Nama Dokter</th>
                    <th width="20%">Nama Analis Lab.</th>
                    <th width="20%">Nama Layanan</th>
                    <th width="10%">Waktu Order</th>
                    <th width="10%">Waktu Hasil</th>
                    <th width="5%">Hasil</th>
                    <th width="5%">Ket</th>
                    <th width="5%">Satuan</th>
                    <th width="10%">#</th>
                </tr>
            </thead>
            <tbody>
                <?php if(sizeof($list_lab) > 0): ?>
                    <?php foreach ($list_lab as $key => $data) { ?>
                    <?php 
                        if ($data->ket_nilai_rujukan === 'L') {
                            $ket = "Low";
                        }else if($data->ket_nilai_rujukan === 'N'){
                            $ket = "Netral";
                        }else if($data->ket_nilai_rujukan === 'H'){
                            $ket = "High";
                        }else{
                            $ket = "";
                        }

                    ?>
                    <tr>
                        <td align="center"><?= ++$key ?></td>
                        <td><?= $data->dokter ?></td>
                        <td><?= $data->laboran ?></td>
                        <td><?= $data->layanan ?></td>
                        <td align="center"><?= ($data->waktu_order != '')?datetimefmysql($data->waktu_order):'-' ?></td>
                        <td align="center"><?= ($data->waktu_hasil != '')?datetimefmysql($data->waktu_hasil):'-' ?></td>
                        <td align="center"><?= ($data->hasil !== '0')?$data->hasil:'-' ?></td>
                        <td align="center"><?= $ket ?></td>
                        <td align="center"><?= ($data->satuan !== NULL)?$data->satuan:'-' ?></td>
                        <td align="center" style="white-space: nowrap;">
                            <span class="link_button" onclick="form_add('<?= $data->id ?>','<?= $pasien->id_pk ?>')">Entri Hasil</span>
                        </td>
                    </tr>
                    <?php } ?>
                 <?php else: ?>
                    <tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
                    <tr><td>&nbsp;</td><td></td><td><td></td><td></td><td></td></td><td></td><td></td><td></td><td></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
</table>
</div>
<br/><br/><br/>