<?php
foreach ($list_data as $attr);
?>
<link rel="stylesheet" href="<?= base_url('assets/css/theme-print.css') ?>" />
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
<table width="100%" style="color: #000; border-bottom: 1px solid #000;">
    <tr>
        <td rowspan="3" style="width: 70px"><img src="<?= base_url('assets/images/company/' . $head->logo_file_nama) ?>" width="70px" height="70px" /></td>    
        <td colspan="3" align="center"><b><?= strtoupper($head->nama) ?></b></td> <td rowspan="3" style="width: 70px">&nbsp;</td>
    </tr>
    <tr><td colspan="3" align="center"><b><?= strtoupper($head->alamat) ?></b></td> </tr>
    <tr><td colspan="3" align="center"><b>TELP. <?= $head->telp ?>,  FAX. <?= $head->fax ?>, EMAIL <?= $head->email ?></b></td> </tr>
</table>
<?php if ($_GET['perundangan'] !== 'Psikotropika') { ?>
<br/>
<table>
    <tr><td><?= $attr->id ?></td></tr>
    <tr><td>Kepada:</td></tr>
    <tr><td>Yth. <?= $attr->supplier ?></td></tr>
    <tr><td><?= $attr->alamat_supplier ?></td></tr>
    <tr><td></td></tr>
</table>
<?php } else { ?>
<h1 style="text-align: center;">SURAT PESANAN PSIKOTROPIKA</h1>
<table width="100%">
    <tr valign="top"><td width="10%">Nama:</td><td width="50%"><?= $apa->nama ?></td><td colspan="2" width="40%">Kepada: </td></tr>
    <tr valign="top"><td>Alamat:</td><td><?= isset($apa->alamat)?$apa->alamat:null ?></td><td colspan="2">Yth. <?= $attr->supplier ?></td></tr>
    <tr valign="top"><td>Jabatan:</td><td><?= isset($apa->jabatan)?$apa->jabatan:null ?></td><td colspan="2">Di <?= $attr->alamat_supplier ?></td></tr>
</table>
<?php } ?>
<br/>
Dengan Hormat,<br/>
Mohon dikirim obat-obatan untuk keperluan Rumah Sakit kami sebagai berikut:
<br/><br/>
<table width="100%" class="list-data-print" cellspacing="0">
    <tr><th width="5%">No.</th><th width="55%" align="left">Nama Barang</th><th width="15%">Kemasan</th><th width="25%">Jumlah</th></tr>
<?php foreach ($list_data as $key => $data) { ?>
    <tr><td align="center"><?= ++$key ?></td><td><?= $data->nama_barang ?></td><td><?= $data->kemasan ?></td><td align="center"><?= $data->jumlah ?></td></tr>
<?php } ?>
</table>
<br/>
<br/>
<table width="100%" align="right">
    <tr><td align="right">Yogyakarta, <?= indo_tgl(date("Y-m-d")) ?></td></tr>
    <tr><td align="right">&nbsp;</td></tr>
    <tr><td align="right">&nbsp;</td></tr>
    <tr><td align="right"><?= $this->session->userdata('nama') ?></td></tr>
    <tr><td align="right"></td></tr>
</table>
</body>