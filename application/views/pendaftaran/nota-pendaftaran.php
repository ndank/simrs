<link rel="stylesheet" href="<?= base_url() ?>assets/css/styles.css" />
<link href="<?= base_url() ?>/assets/js/css/start/jquery-ui-1.8.20.custom.css" rel="stylesheet" type="text/css"/>
<script src="<?= base_url() ?>/assets/js/js/jquery-1.7.2.min.js"></script>
<script src="<?= base_url() ?>/assets/js/js/jquery-ui-1.8.20.custom.min.js"></script>

<script type="text/javascript">
    
     
    
    function cetak() {        
       
        SCETAK.innerHTML = '';
        window.print();
        setTimeout(function(){ window.close();},300);
        SCETAK.innerHTML = '<br /><input onClick=\'cetak()\' type=\'submit\' name=\'Submit\' value=\'Cetak\' class=\'tombol\'>';
    }
</script>
<div class="print-area">
    <h2 class="heading">Nota Pendaftaran</h2>
    <table width="100%">
        <tr><td width="40%">Nama Pasien</td><td>:</td><td><?= $pasien->nama ?></td></tr>
        <tr><td>No. Rekam Medik</td><td>:</td><td><?= $pasien->no_rm ?></td></tr>
        <tr><td>No. Pendaftaran</td><td>:</td><td><?= $pasien->no_daftar ?></td></tr>
        <tr><td>Unit Layanan</td><td>:</td><td><?= $pasien->nama_unit ?></td></tr>
        <tr><td>Tanggal Pelayanan</td><td>:</td><td><?= dateconvert($pasien->tgl_layan) ?></td></tr>
        <tr><td>No Antrian</td><td>:</td><td><?= $pasien->no_antri ?></td></tr>
    </table>
    <table width="100%">
        <tr><td width="40%"><b>Uraian</b></td><td>:</td><td><b>Harga</b></td></tr>
        <tr><td width="40%">Pendaftaran</td><td>:</td><td align="right"><?= rupiah($biaya_kunjungan) ?></td></tr>
        <tr><td width="40%">Pembuatan Kartu</td><td>:</td><td class="border" align="right"><?= rupiah($biaya_kartu) ?></td></tr>
        <tr><td width="40%"><b>Total</b></td><td>:</td><td align="right"><?= rupiah($biaya_kunjungan + $biaya_kartu) ?></td></tr>
    </table>
</div>
<center><span id="SCETAK"><button onclick="cetak()">Cetak</button></span></center>
<?php die ?>