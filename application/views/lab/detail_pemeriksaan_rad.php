<script type="text/javascript">
    function form_edit(id_pl, id_pk) {
        var id_pelayanan = id_pk;
        var str = '<div id="form_hasil_rad" class=data-input>'+
                    '<form id=save_hasil_rad>'+
                    '<input type=hidden name=id_pemeriksaan_lab value="'+id_pl+'" />'+
                    '<table width="100%" style="background: #f4f4f4; border: 1px solid #ccc; margin-top: 7px;"><tr valign=top><td width="50%">'+
                            '<table width=100% style="line-height: 22px; border-spacing: 0;">'+
                                '<tr><td width=40%>No.:</td><td id=arv_no_pemeriksaan></td></tr>'+
                                '<tr><td>Waktu Order:</td><td id=arv_waktu_order></td></tr>'+
                                '<tr><td>Waktu Hasil:</td><td><?= form_input("waktu_hasil", NULL, "id=arv_waktu_hasil size=15") ?></td></tr>'+
                                '<tr><td>Nama Radiografer.:</td><td><input type="text" name="radiografer" value="" id=arv_radiografer /><input type="hidden" name="id_radiografer" id="id_radiografer" /></td></tr>'+
                                '<tr><td>Nama Layanan:</td><td id=arv_layanan></td></tr>'+
                            '</table>'+
                        '</td><td width=50%>'+
                            '<table width=100% style="line-height: 22px; border-spacing: 0;">'+
                                '<tr><td width=40%>kv:</td><td><?= form_input("kv", NULL, "id=arv_kv size=10 autofocus ") ?></td></tr>'+
                                '<tr><td>ma:</td><td><?= form_input("ma", NULL, "id=arv_ma size=10") ?></td></tr>'+
                                '<tr><td>s:</td><td><?= form_input("s", NULL, "id=arv_s size=10") ?></td></tr>'+
                                '<tr><td>p:</td><td><?= form_input("p", NULL, "id=arv_p size=10") ?></td></tr>'+
                                '<tr><td>fr:</td><td><?= form_input("fr", NULL, "id=arv_fr size=10") ?></td></tr>'+
                            '</table>'+
                        '</td></tr>'+
                    '</table>'+
                    '</form>'+
                  '</div>';


            $('#dialog_edit_rad').html('').append(str);
            $('#form_hasil_rad').dialog({
                autoOpen: true,
                modal: true,
                width: 700,
                height: 250,
                title: 'Entry Hasil Pemeriksaan Radiologi',
                buttons: {
                    "Simpan": function() {
                        $('#save_hasil_rad').submit();
                        $(this).dialog().remove();
                    },
                    "Cancel": function() {
                        $(this).dialog().remove();
                    }
                }, open: function() {
                    $('#arv_kv').focus();
                    $.ajax({
                        url: '<?= base_url("pelayanan/radiologi_load_data_pemeriksaan") ?>/'+id_pl,
                        cache: false,
                        dataType: 'json',
                        success: function(data) {
                            $('#arv_kv').val(data.kv);
                            $('#arv_ma').val(data.ma);
                            $('#arv_s').val(data.s);
                            $('#arv_p').val(data.p);
                            $('#arv_fr').val(data.fr);

                            $('#arv_no_pemeriksaan').html(data.id);
                            $('#arv_waktu_order').html((data.waktu_order != null)?datetimefmysql(data.waktu_order):'');
                            $('#arv_waktu_hasil').val((data.waktu_hasil != null)?datetimefmysql(data.waktu_hasil):'');
                            $('#arv_radiografer').val(data.radiografer);
                            $('#arv_layanan').html(data.layanan);
                            $('input[name=id_radiografer]').val(data.id_kepegawaian_radiografer);
                        }
                    });
                }, close: function() {
                    $(this).dialog().remove();
                }
            });

            $('#arv_waktu_hasil').datetimepicker({
                changeYear : true,
                changeMonth : true
            });

            $('#arv_radiografer').autocomplete("<?= base_url('inv_autocomplete/load_data_pegawai_profesi') ?>",
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
                $('input[name=id_radiografer]').val(data.id);
            });

            $('#save_hasil_rad').submit(function() {
               
                $.ajax({
                    url: '<?= base_url("laboratorium/edit_hasil_rad") ?>/'+id_pl,
                    data: $('#save_hasil_rad').serialize(),
                    dataType: 'json',
                    type: 'POST',
                    success: function(data) {
                        if (data.status === true) {
                            custom_message('Informasi', 'Data berhasil di simpan');
                            $('#form_hasil_rad').dialog().remove();
                            detail_pemeriksaan(id_pelayanan,'radiologi');
                        }
                    }
                });
                return false;
            });
    }

</script>
<div id="dialog_edit_rad"></div>
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
                    <table width="100%" style="line-height: 22px;">
                        <tr><td>Gender:</td><td id="gender"><?= isset($pasien)?(($pasien->gender=="L")?"Laki-Laki":"Perempuan"):NULL ?></td></tr>
                        <tr><td>Umur:</td><td id="umur"><?= isset($pasien)?createUmur($pasien->lahir_tanggal):NULL ?></td></tr>
                        <tr><td>Pekerjaan:</td><td id="pekerjaan"><?= isset($pasien)?$pasien->pekerjaan:NULL ?></td></tr>
                        <tr><td>Pendidikan:</td><td id="pendidikan"><?= isset($pasien)?$pasien->pendidikan:NULL ?></td></tr>
                    </table>
                </td>
                </tr>
                </table>
        </tr>
    </table>
</table>

<fieldset>
    <table class="list-data" width="100%">
            <thead>
                <tr>
                    <th width="5%">No.</th>
                    <th width="20%">Nama Dokter</th>
                    <th width="20%">Nama Radiografer</th>
                    <th width="20%">Nama Layanan</th>
                    <th width="10%">Waktu Order</th>
                    <th width="10%">Waktu Hasil</th>
                    <th width="10%">#</th>
                </tr>
            </thead>
            <tbody>
                <?php if(sizeof($list_rad) > 0):?>
                    <?php foreach ($list_rad as $key => $data) { ?>
                    <tr>
                        <td align="center"><?= ++$key ?></td>
                        <td><?= $data->dokter ?></td>
                        <td><?= $data->radiografer ?></td>
                        <td><?= $data->layanan ?></td>
                        <td align="center"><?= ($data->waktu_order != '')?datetimefmysql($data->waktu_order):'-' ?></td>
                        <td align="center"><?= ($data->waktu_hasil != '')?datetimefmysql($data->waktu_hasil):'-' ?></td>
                        <td align="center" style="white-space: nowrap;">
                            <span class="link_button" onclick="form_edit('<?= $data->id ?>','<?= $pasien->id_pk ?>')">Entri Hasil</span>
                        </td>
                    </tr>
                    <?php } ?>
                <?php else: ?>
                    <tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
                    <tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
</table>
</div>
<br/><br/><br/>