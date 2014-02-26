<link rel="stylesheet" href="<?= base_url('assets/css/print-struk.css') ?>" />
<script type="text/javascript">
window.onunload = refreshParent;
function refreshParent() {
    //window.opener.location.reload();
}
function cetak() {  		
    window.print();
    setTimeout(function(){ window.close();},300);
}
</script>
<body onload="cetak();" class="default-printing">
<?= header_surat() ?>
<?php
$label = get_bottom_label();
$apa   = get_apa_from_karyawan();
foreach ($list_data as $rows);
?>
<table width="100%" style="border-bottom: 1px solid #000;">
    <tr><td>No. Resep: </td><td colspan="3" align="left"><?= $rows->id_resep ?></td> </tr>
    <tr><td>Tanggal: </td><td colspan="3" align="left"><?= datetimefmysql($rows->waktu) ?></td> </tr>
    <tr><td>Pasien: </td><td colspan="3"><?= $rows->pasien ?></td> </tr>
</table>
<table width="100%" style="border-bottom: 1px solid #000;">
<?php
    $total = 0;
    foreach ($list_data as $key => $data) { ?>
        <tr>
            <td colspan="4"><?= $data->nama_barang ?></td>
        </tr>
        <tr>
            <td><?= $data->qty ?></td>
            <td><?= $data->kemasan ?></td>
            <td align="right"><?= rupiah($data->harga_jual) ?></td>
            <td align="right"><?= rupiah($data->subtotal) ?></td>
        </tr>
    <?php 
    $total = $total+$data->subtotal;
    } ?>
</table>
<table width="100%">
    <tr>
        <td colspan="3">Total:</td>
        <td align="right">Rp. <?= rupiah($total) ?>,00</td>
    </tr>
</table>
</body>