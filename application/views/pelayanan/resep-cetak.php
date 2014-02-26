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
<h2 style="text-align: center">SALINAN RESEP</h2>
<table width="100%" style="border-bottom: 1px solid #000;">
    <tr><td>No. Resep: </td><td colspan="3" align="left"><?= $rows->id ?></td> </tr>
    <tr><td>Dari Dokter: </td><td colspan="3"><?= $rows->dokter ?></td> </tr>
    <tr><td>Tanggal: </td><td colspan="3" align="left"><?= datetimefmysql($rows->waktu) ?></td> </tr>
    <tr><td>Pro: </td><td colspan="3"><?= $rows->pasien ?></td> </tr>
    <tr><td>Usia:</td><td colspan="3"><?= ($rows->tanggal_lahir=='0000-00-00')?'':hitungUmur($rows->tanggal_lahir) ?></td> </tr>
</table>
<table width="100%" style="border-bottom: 1px solid #000;">
<?php
    $id_resep = "";
    $jasa = "";
    
    $no = 1;
    foreach ($list_data as $key => $data) {
        if ($jasa !== $data->r_no) { 
            $then = NULL;
            if (($data->resep_r_jumlah - $data->tebus_r_jumlah) === 0) {
                $then = "Detur Originale";
            }
            else if (($data->resep_r_jumlah - $data->tebus_r_jumlah) == $data->resep_r_jumlah) {
                $then = "Nedet";
            }
            else if (($data->resep_r_jumlah - $data->tebus_r_jumlah) > 0) {
                $then = "Det ".$data->tebus_r_jumlah;
            }
            ?>
        <?php }
        if (($data->id_resep !== $id_resep) or ($data->r_no !== $jasa)) { ?>
        <tr>
            <td>R /: <?= ($data->r_no !== $jasa)?$data->r_no:NULL ?></td>
        </tr>
        <?php }
        if (($data->id_resep !== $id_resep) or ($data->r_no !== $jasa)) { ?>
        <tr>
            <td style="padding-left: 20px">No. <?= $data->resep_r_jumlah ?> <?= $then ?><br/></td>
        </tr>
        <?php } ?>
        <tr class="<?= ($data->id_resep !== $id_resep)?'odd':'even' ?>">
            <td style="padding-left: 20px"><?= $data->nama_barang ?></td>
        </tr>
        <tr>
            <td style="padding-left: 20px"><?= $data->aturan ?> x <?= $data->pakai ?></td>
        </tr>
        
        
    <?php 
    $jasa = $data->r_no;
    if ($data->id_resep !== $id_resep) {
        $no++;
    }
    $id_resep = $data->id_resep;
    } ?>
</table>
<table width="100%" align="right">
    <tr><td align="right">Yogyakarta, <?= indo_tgl(date("Y-m-d")) ?></td></tr>
    <tr><td colspan="4" align="right">PCC</td> </tr>
    <tr><td colspan="4" align="right">APA</td> </tr>
    <tr><td align="right">&nbsp;</td></tr>
    <tr><td align="right">&nbsp;</td></tr>
    <tr><td align="right"><?= isset($apa->nama)?$apa->nama:NULL ?></td></tr>
    <tr><td align="right"><?= isset($apa->no_sipa)?$apa->no_sipa:NULL ?></td></tr>
</table>
</body>