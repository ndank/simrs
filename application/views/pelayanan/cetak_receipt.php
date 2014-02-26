<title>RESEP</title>
<link rel="stylesheet" href="<?= base_url('assets/css/print-struk.css') ?>" media="print" />
<script type="text/javascript">
    window.print();
    setTimeout(function(){ window.close();},300);
</script>
<div class="space"></div>
<table class="resep_dokter">
    <tr valign="top"><td>No. Resep:</td><td><?= $detail->no_resep ?></td></tr>
    <tr valign="top"><td>Pasien:</td><td>(<?= $detail->no_rm ?>) <?= $detail->pasien ?></td></tr>
    <tr valign="top"><td>Dokter:</td><td><?= $detail->nama ?></td></tr>
    <tr valign="top"><td>Pelayanan / Unit:</td><td><?= $detail->jenis_pelayanan ?> / <?= $detail->unit ?></td></tr>
</table>