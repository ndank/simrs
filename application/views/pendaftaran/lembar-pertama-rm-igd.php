<script type="text/javascript">
    function cetak() {
       setTimeout(function(){ window.close();},300);
       window.print();
    }
</script>
<link rel="stylesheet" href="<?= base_url('assets/css/print-A4.css') ?>" media="print" />
<title><?= $title ?></title>
<style>
    .list-data { border-spacing: 0; }
    .list-data th,.list-data td { border-right: 1px solid #000; height: 20px; }
    .list-data th:last-child, .list-data td:last-child { border: none; }
</style>
<body onload="cetak()" style="height:auto; width:21.5cm">
<table width="100%" style="color: #000; border-bottom: 1px solid #000;">
    <tr>
    <td rowspan="3" style="width: 70px"><img src="<?= base_url('assets/images/company/'.$apt->logo_file_nama) ?>" width="70px" height="70px" /></td>    
    <td colspan="3" align="center"><b><?= strtoupper($apt->nama) ?></b></td> <td rowspan="3" style="width: 70px">&nbsp;</td></tr>
    <tr><td colspan="3" align="center"><b><?= strtoupper($apt->alamat) ?> <?= strtoupper($apt->kelurahan) ?></b></td> </tr>
    <tr><td colspan="3" align="center"><b>TELP. <?= $apt->telp ?>,  FAX. <?= $apt->fax ?>, EMAIL <?= $apt->email ?></b></td> </tr>
</table>
<table width="100%" cellspacing="0" style="border-bottom: 1px solid #000;">
    <tr>
        <td width="50%" style="border-right: 1px solid #000;"><span style="font-size: 30px;"><?= $title ?></span></td>
        <td width="50%"><span style="font-size: 30px;">No. RM.:<?= $rows->no_rm ?></span></td>
    </tr>
</table>
<table width="100%" style="border-bottom: 1px solid #000; padding-bottom: 5px">
    <tr><td width="20%">NAMA LENGKAP:</td><td><?= $rows->nama ?></td></tr>
    <tr><td>TANGGAL LAHIR/UMUR:</td><td><?= datefmysql($rows->lahir_tanggal).' / '.  hitungUmur($rows->lahir_tanggal) ?></td></tr>
    <tr><td>AGAMA:</td><td><?= $rows->agama ?></td></tr>
    <tr><td>PEKERJAAN:</td><td><?= $rows->pekerjaan ?></td></tr>
    <tr><td>ALAMAT JALAN:</td><td><?= $rows->alamat ?></td></tr>
    <tr><td>KELURAHAN:</td><td><?= $rows->kelurahan.' '.$rows->kecamatan.' '.$rows->provinsi ?></td></tr>
    <tr><td>NAMA P.J.</td><td><?= $rows->nama_pjwb ?></td></tr>
    <tr><td>ALAMAT P.J.</td><td><?= $rows->alamat_pjwb ?></td></tr>
    <tr><td>No. Telp</td><td><?= $rows->telp_pjwb ?></td></tr>
</table>
<table width="100%" style="border-bottom: 1px solid #000; padding-bottom: 5px">
    <tr><td width="20%">KEBUTUHAN PERAWATAN:</td><td><?= $rows->keb_rawat ?></td></tr>
    <tr><td>JENIS LAYANAN:</td><td><?= $rows->jenis_layan ?></td></tr>
    <tr><td>KRITERIA LAYANAN:</td><td><?= $rows->krit_layan ?></td></tr>
    <tr><td>D.O.A.:</td><td><?= $rows->doa ?></td></tr>
</table>
<table width="100%" style="border-bottom: 1px solid #000; padding-bottom: 5px">
    <tr><td width="20%">RUJUKAN:</td><td></td></tr>
    <tr><td>INSTANSI PERUJUK:</td><td><?= $rows->relasi ?></td></tr>
    <tr><td>TENAGA KESEHATAN PERUJUK:</td><td><?= $rows->nakes_perujuk ?></td></tr>
    <tr><td>ALASAN DATANG:</td><td><?= $rows->alasan_datang ?></td></tr>
    <tr><td>KETERANGAN KECELAKAAN</td><td><?= $rows->keterangan_kecelakaan ?></td></tr>
    <tr><td>TIBA PUKUL:</td><td>..........</td></tr>
    <tr><td>TANGGAL KEJADIAN:</td><td>.......... PUKUL KEJADIAN: ........</td></tr>
    <tr><td>TEMPAT KEJADIAN:</td><td>..........</td></tr>
    <tr><td>PENYEBAB CIDERA:</td><td>..........</td></tr>
</table>
<table width="100%" style="border-bottom: 1px solid #000; padding-bottom: 5px">
    <tr></tr>
    <tr><td width="20%">JENIS KASUS</td><td></td></tr>
    <tr><td colspan="2"><?= form_radio() ?> Bedah <?= form_radio() ?> Non Bedah <?= form_radio() ?> Anak <?= form_radio() ?> Psikiatrik <?= form_radio() ?> Trauma <?= form_radio() ?> Lainnya.................</td></tr>
    <tr><td width="20%">TINDAKAN RESUSITASI</td><td><?= form_radio() ?> Ya <?= form_radio() ?> Tidak</td></tr>
</table><br/>
<table width="100%" style="padding-bottom: 5px; height: 20px;">
    <tr valign="top"><td width="20%">A. ANAMNESA</td><td></td></tr>
</table>
<table width="100%" style="padding-bottom: 5px;">
    <tr valign="top"><td width="20%">B. PEMERIKSAAN</td><td></td></tr>
    <tr><td colspan="2" style="padding-left: 50px;">KU: ..........</td></tr>
    <tr><td colspan="2" style="padding-left: 50px;">TENSI: ..........mmHg NADI: .......... Suhu: ..........<sup>o</sup>C NAFAS: ..........</td></tr>
    <tr><td colspan="2" style="padding-left: 50px;">BB: ..........</td></tr>
    <tr></tr>
</table>
<center><img src="<?= base_url('assets/images/company/man-body.png') ?>" align="center" height="320px" /></center>
</body>