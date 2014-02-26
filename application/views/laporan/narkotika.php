<?php
require_once 'app/lib/common/master-data.php';
require_once 'app/lib/common/functions.php';
$apt = informasi_apotek();
$array = narkotika_laporan_muat_data($_GET['awal'], $_GET['akhir']);
?>
<link rel="stylesheet" href="<?= app_base_url('assets/css/workspace.css') ?> "/>
<style type="text/css">
    * { font-family: Verdana; font-size: 12px; }
</style>
<script type="text/javascript">
	function cetak() {
		SCETAK.innerHTML = '';
		window.print();
		if (confirm('Apakah menu print ini akan ditutup?')) {
			window.close();
		}
		SCETAK.innerHTML = '<br /><input onClick=\'cetak()\' type=\'submit\' name=\'Submit\' value=\'Cetak\' class=\'tombol\'>';
	}

</script>
<table style="border-bottom: 1px solid #000;" width="100%">
    <tr><td colspan="4" align="center" style="text-transform: uppercase">Apotek <?= $apt['nama'] ?></td> </tr>
    <tr><td colspan="4" align="center"><?= $apt['alamat'] ?> <?= $apt['kelurahan'] ?></td> </tr>
    <tr><td colspan="4" align="center">Telp. <?= $apt['telp'] ?>,  Fax. <?= $apt['fax'] ?>, Email <?= $apt['email'] ?></td> </tr>
</table>
<h1 align="center">LAPORAN NARKOTIKA</h1><h3 align="center"><?= tampil_bulan(date2mysql($_GET['awal'])) ?></h3>
<?php
foreach ($array as $rows) { ?>
<p>Tanggal <?= indo_tgl($rows['tanggal']) ?></p>
<?php
if ($rows['transaksi_jenis'] == 'Pembelian') {
?>
<p><?= $rows['transaksi_jenis'] ?>, <?= $rows['transaksi_id'] ?>, <?= $rows['pabrik'] ?></p>
<?php } else { ?>
<p><?= $rows['transaksi_jenis'] ?>, <?= $rows['transaksi_id'] ?>, <?= ($rows['pasien'] != '')?$rows['pasien']:$rows['pembeli'] ?>, <?= $rows['dokter'] ?></p>
<?php } 
$arrays = detail_narkotika_laporan_muat_data($rows['tanggal'], $rows['transaksi_id'], $rows['transaksi_jenis']); ?>
<table class="tabel" width="100%">
    <tr>
        <th>Barcode</th>
        <th>Packing Barang</th>
        <th>Awal</th>
        <th>Masuk</th>
        <th>Keluar</th>
        <th>Sisa</th>
    </tr>
<?php foreach ($arrays as $key => $data) { ?>
    <tr>
        <td><?= $data['barcode'] ?></td>
        <td><?= $data['barang'] ?> <?= $data['kekuatan'] ?>  <?= $data['satuan'] ?> <?= $data['sediaan'] ?> <?= $data['pabrik'] ?> @ <?= ($data['isi']==1)?'':$data['isi'] ?> <?= $data['satuan'] ?></td>
        <td><?= $data['awal'] ?></td>
        <td><?= $data['masuk'] ?></td>
        <td><?= $data['keluar'] ?></td>
        <td><?= $data['sisa'] ?></td>
    </tr>
<?php } ?>
</table>

<?php } ?>
<p align="center">
<span id="SCETAK"><input type="button" class="tombol" value="Cetak" onClick="cetak()"/></span>
</p>
<?php die; ?>
