<script type="text/javascript">
    function form_add(id_pl, id_pk) {
        var id_pelayanan = id_pk;
        var str = '<div id="form_hasil_fisio" class=data-input>'+
                    '<form id=save_hasil_fisio>'+
                    '<input type=hidden name=id_pemeriksaan_lab value="'+id_pl+'" />'+
                        '</td><td>'+
                            '<table width=100% style="line-height: 22px; border-spacing: 0;">'+
                                '<tr><td width=40%>No.:</td><td id=arv_no_pemeriksaan></td></tr>'+
                                '<tr><td>Waktu:</td><td><input name="waktu" id="arv_waktu" size="15" autofocus /></td></tr>'+
                                '<tr><td>Nama Nakes Operator:</td><td id=arv_operator></td></tr>'+
                                '<tr><td>Nama Nakes Anestesi:</td><td id=arv_anestesi></td></tr>'+
                                '<tr><td>Unit:</td><td id=arv_unit></td></tr>'+
                                '<tr><td>Tindakan:</td><td id=arv_tindakan></td></tr>'+
                                '<tr><td>ICDIX:</td><td id=arv_icd></td></tr>'+
                            '</table>'+
                        '</td></tr>'+
                    '</table>'+
                    '</form>'+
                  '</div>';
            $('#dialog_edit_fisio').html('').append(str);

            $('#form_hasil_fisio').dialog({
                autoOpen: true,
                modal: true,
                width: 450,
                height: 330,
                title: 'Entry Waktu Pelayanan Fisioterapi',
                buttons: {
                    "Simpan": function() {
                        $('#save_hasil_fisio').submit();
                        $(this).dialog().remove();
                    },
                    "Cancel": function() {
                        $(this).dialog().remove();
                    }
                }, open: function() {
                    $('.ui-dialog-buttonset').focus();
                     $('#arv_waktu').datetimepicker({
                        changeYear : true,
                        changeMonth : true
                    });
                    $.ajax({
                        url: '<?= base_url("pelayanan/fisioterapi_load_data_pemeriksaan") ?>/'+id_pl,
                        cache: false,
                        dataType: 'json',
                        success: function(data) {

                            $('#arv_no_pemeriksaan').html(data.id);
                            $('#arv_waktu').val((data.waktu != null)?datetimefmysql(data.waktu):'');
                            $('#arv_operator').html(data.operator);
                            $('#arv_anestesi').html(data.anestesi);
                            $('#arv_tindakan').html(data.layanan);
                            $('#arv_unit').val(data.unit);
                            $('#arv_icd').val(data.kode_icdixcm);
                        }
                    });
                }, close: function() {
                    $(this).dialog().remove();
                }
            });

           
            $('#save_hasil_fisio').submit(function() {
           
                $.ajax({
                    url: '<?= base_url("laboratorium/edit_waktu_fisioterapi") ?>/'+id_pl,
                    data: $('#save_hasil_fisio').serialize(),
                    dataType: 'json',
                    type: 'POST',
                    success: function(data) {
                        if (data.status === true) {
                            custom_message('Informasi', 'Data berhasil di simpan');
                            $('#form_hasil_fisio').dialog().remove();
                            detail_pemeriksaan(id_pelayanan,'fisioterapi');
                        }
                    }
                });
                return false;
            });
    }

</script>
<div id="dialog_edit_fisio"></div>
<div class="data-input">
<fieldset>
    <h1><?= $subtitle?></h1>
    <br/><br/>
    <table width="100%" style="border-spacing:0;">
        <tr valign="top">
            <td width="50%">
                <h2>Kunjungan</h2>
                <div class="msg" id="msg_detail"></div>
                               

                <table width="100%" style="line-height: 22px;">
                    <tr><td style="width: 165px;">No. RM:</td><td><?= isset($pasien->no_rm)?$pasien->no_rm:NULL ?></td></tr>
                    <tr><td>Nama Pasien:</td><td id="nama"><?= isset($pasien)?$pasien->pasien:NULL ?></td></tr>
                    <tr><td>Alamat Jalan:</td><td id="alamat"><?= isset($pasien)?$pasien->alamat:NULL ?></td></tr>
                    <tr><td>Wilayah:</td><td id="wilayah"><?= isset($pasien)?$pasien->kelurahan." ".$pasien->kecamatan:NULL ?></td></tr>
                    <tr><td>Gender:</td><td id="gender"><?= isset($pasien)?(($pasien->gender=="L")?"Laki-Laki":"Perempuan"):NULL ?></td></tr>
                    <tr><td>Umur:</td><td id="umur"><?= isset($pasien)?createUmur($pasien->lahir_tanggal):NULL ?></td></tr>
                    <tr><td>Pekerjaan:</td><td id="pekerjaan"><?= isset($pasien)?$pasien->pekerjaan:NULL ?></td></tr>
                    <tr><td>Pendidikan:</td><td id="pendidikan"><?= isset($pasien)?$pasien->pendidikan:NULL ?></td></tr>
                </table> 
            </td>
        </tr>
    </table>
</table>

<fieldset>
    <table class="tabel" width="100%">
            <thead>
                <tr>
                    <th width="5%">No.</th>
                    <th width="10%">Waktu</th>
                    <th width="20%">Nama Nakes</th>
                    <th width="20%">Nama Nakes Anestesi</th>
                    <th width="10%">Unit</th>
                    <th width="20%">Tindakan</th>
                    <th width="5%">ICDIX - CM</th>
                    <th width="10%">#</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($list as $key => $data) { ?>
                <tr>
                    <td align="center"><?= ++$key ?></td>
                    <td align="center"><?= ($data->waktu != '')?datetimefmysql($data->waktu):'-' ?></td>
                    <td><?= $data->nama_ope ?></td>
                    <td><?= $data->nama_anes ?></td>
                    <td><?= $data->nama_unit ?></td>
                    <td><?= $data->tindakan ?></td>
                    <td><?= $data->kode_icdixcm ?></td>
                    <td align="center" style="white-space: nowrap;">
                        <span class="link_button" onclick="form_add('<?= $data->id ?>','<?= $pasien->id_pk ?>')">Edit Waktu</span>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
</table>
</div>