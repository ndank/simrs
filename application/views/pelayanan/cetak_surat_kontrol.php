<script type="text/javascript">
    function cetak() {
        setTimeout(function(){ window.close();},300);
        window.print();    
    }
</script>
<title><?= $title ?></title>
<style>
    *{ font-size: 12px; font-family: "Times New Roman"}
    .list-data { border-spacing: 0; }
    .list-data th,.list-data td { border-right: 1px solid #000; height: 20px; }
    .list-data th:last-child, .list-data td:last-child { border-right: none; }
    .inap td{border-bottom: 1px solid #000; }
    .table td {border: none;}
    .li-special{padding-left:20px;}
    .li-special li{padding-bottom: 5px;padding-top: 5px }
</style>
<body onload="cetak()" style="height:auto; width:21.5cm">
    <table width="100%" style="color: #000; border-bottom: 1px solid #000;">
        <tr>
            <td rowspan="3" style="width: 70px"><img src="<?= base_url('assets/images/company/' . $apt->logo_file_nama) ?>" width="70px" height="70px" /></td>    
            <td colspan="3" align="center"><b><?= strtoupper($apt->nama) ?></b></td> <td rowspan="3" style="width: 70px">&nbsp;</td>
        </tr>
        <tr><td colspan="3" align="center"><b><?= strtoupper($apt->alamat) ?> <?= strtoupper($apt->kelurahan) ?></b></td> </tr>
        <tr><td colspan="3" align="center"><b>TELP. <?= $apt->telp ?>,  FAX. <?= $apt->fax ?>, EMAIL <?= $apt->email ?></b></td> </tr>
    </table>
    <br/><br/>
    <p style="text-align:center;font-size:14px;margin-bottom:0px; ">
        <span style="border-bottom:1px solid #000;font-weight:bold;">SURAT KONTROL</span>
    </p>
    <p style="margin-top:5px;text-align:center;">No. ..............................</p>
    <br/>
     <p>Yang bertandatangan di bawah ini, saya dokter <?= $this->session->userdata('user') ?>, memberitahukan bahwa pasien: </p>
     <table width="100%" style="margin-left:50px;">
        <tr><td width="20%">NO. RM</td><td><?= $rows->no_rm ?></td></tr>
        <tr><td>NAMA PASIEN:</td><td><?= $rows->nama ?></td></tr>
        <tr><td>ALAMAT JALAN:</td><td><?= $rows->alamat ?></td></tr>
        <tr><td>WILAYAH:</td><td><?= $rows->kelurahan . ' ' . $rows->kecamatan . ' ' . $rows->provinsi ?></td></tr>
    </table>
    <?php  if(strpos($pk->unit, 'angsal') !== false):?>
        <p>Opname Tanggal: <?= get_date_format($rows->arrive_time) ?>  s.d.  <?= ($rows->waktu_keluar != '')?get_date_format($rows->waktu_keluar):'' ?></p>
    <?php endif; ?>

    <p>Diharap kontrol lagi pada:</p>
    <table width="100%" style="margin-left:50px;">
        <tr><td width="20%">HARI/TANGGAL</td><td><?= get_day($rows->waktu_kontrol) ?> / <?= get_date_format($rows->waktu_kontrol)?></td></tr>
    </table>
    <table width="100%" style="margin-top: 7em">
         <tr>
            <td width="50%" align="center"></td>
            <td width="50%" align="right">Hormat kami,</td>
        </tr>
     </table>
    <table width="100%" style="margin-top: 5em">
         <tr>
            <td width="50%" align="center"></td>
            <td width="50%" align="right"><?= $this->session->userdata('user') ?></td>
        </tr>
     </table>
</body>