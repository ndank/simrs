<div id="result_cetak" style="display: none"></div>
<?php $this->load->view('message') ?>
<script type="text/javascript">
$(function() {
    $('button').button();
    $('#salinresep').click(function() {
        var id = $('#id_resep').html();
        location.href='<?= base_url('laporan/salin_resep') ?>/'+id;
    });
    $('#print').click(function() {
        var id = $('#id_resep').html();
        $.get('<?= base_url('pelayanan/kitir_cetak_nota') ?>/'+id, function(data) {
            $('#result_cetak').html(data);
            $('#result_cetak').dialog({
                autoOpen: true,
                modal: true,
                width: 700,
                height: 400
            });
        });
    });
    $('#hapusresep').click(function() {
        var ok = confirm('Anda yakin akan menghapus resep dengan nomor: '+$('#id_resep').html()+' ?');
        if (ok) {
            $.ajax({
                url: '<?= base_url('pelayanan/delete_resep') ?>/'+$('#id_resep').html(),
                cache: false,
                success: function(data) {
                    alert_delete();
                }
            });
            $('#forminforesep').submit();
            $(this).closest("#result_detail").dialog().remove();
        }
    });
})
function cetak_etiket(i) {
    var no_resep = $('#id_resep').html();
    var no_r = i;
    $.ajax({
        url: '<?= base_url('pelayanan/cetak_etiket') ?>',
        data: 'no_resep='+no_resep+'&no_r='+no_r,
        cache: false,
        success: function(data) {
            $('#data_cetak').html(data);
            $('#data_cetak').dialog({
                autoOpen: true,
                modal: true,
                width: 450,
                height: 400
            })
        }
    })
}
</script>
<div id="data_cetak"></div>
<div class="circle">
    <?php
    foreach ($list_data as $rows);
    ?>
    <h1 class="title"><?= $title ?></h1>
    <table width="100%" style="border-bottom: 1px solid #ccc; padding: 10px 10px; margin-bottom: 10px;">
        <tr><td width="20%">No.:</td><td id="id_resep"><?= $rows->id ?></td> </tr>
        <tr><td>Waktu:</td><td><?= datetime($rows->waktu) ?> </td> </tr>
        <tr><td>Dokter: </td><td><?= $rows->dokter ?></td> </tr>
        <tr><td>No. RM: </td><td><?= $rows->no_rm ?></td> </tr>
        <tr><td>Pasien: </td><td><?= $rows->pasien ?></td> </tr>
        <tr><td>Absah:</td><td><?= $rows->sah ?></td> </tr>
        <tr><td>Keterangan:</td><td><?= $rows->keterangan ?></td> </tr>
    </table>
    <?php
    foreach ($list_data as $key => $data) { ?>
    <div style="display: inline-block; width: 97%" class="tr_row data-input">
        <div class="masterresep" style="border-bottom: 1px solid #ccc; margin-left: 20px; padding-bottom: 10px;">
            <table width="100%">
                <tr><td width="15%">Nomor R/:</td><td><b><?= $data->r_no ?></b></td></tr>
                <tr><td>Jumlah R:</td><td><b><?= $data->resep_r_jumlah ?></b></td></tr>
                <tr><td>Jumlah Tebus:</td><td><b><?= $data->tebus_r_jumlah ?></b></td></tr>
                <tr><td>Aturan Pakai:</td><td><b><?= $data->pakai_aturan ?></b></td></tr>
                <tr><td>Iterasi:</td><td><b><?= $data->iter ?></b></td></tr>
                <tr><td>Bia. Apoteker:</td><td><b><?= rupiah($data->nominal) ?></b></td></tr>
                <tr><td></td><td><?= form_button(null, 'Cetak Etiket', 'id=etiket onclick="cetak_etiket('.++$key.')"') ?></td></tr>
            </table>
        </div>
    </div>
    
    <?php 
    $detail = $this->m_resep->detail_data_resep_load_data($data->id_rr)->result();
    foreach ($detail as $num => $val) { ?>
        <table width=92% style="border-bottom: 1px solid #ccc; margin-bottom: 10px; padding-bottom: 10px; margin: 0 40px;">
        <tr><td width=15%>Barcode:</td><td><?= $val->barcode ?></td></tr>
        <tr><td>Packing Barang:</td><td><?= $val->barang ?> <?= ($val->kekuatan == '1')?'':$val->kekuatan ?>  <?= $val->satuan ?> <?= $val->sediaan ?> <?= $val->pabrik ?> <?= ($val->isi==1)?'':'@'.$val->isi ?> <?= $val->satuan_terkecil ?></td></tr>
        <tr><td>Kekuatan:</td><td><?= ($val->kekuatan != '')?$val->kekuatan:'-' ?></td></tr>
        <tr><td>Dosis Racik:</td><td><?= $val->dosis_racik ?></td></tr>
        <tr><td>Jumlah Pakai:</td><td><?= ($val->kekuatan != '')?(($val->dosis_racik*$val->tebus_r_jumlah)/$val->kekuatan):$val->pakai_jumlah ?></td></tr>
        </table>
    <?php } ?>
    
    <?php } ?>
</div>
<?= form_button(null, 'Hapus', 'id=hapusresep') ?>
<!--<?= form_button(null, 'Cetak', 'id=salinresep') ?>
<?= form_button(null, 'Cetak Kitir', 'id=print') ?>-->