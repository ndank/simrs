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
    .table-tujuan td{width: 100%; text-align: right;}
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
        <span style="border-bottom:1px solid #000;font-weight:bold;">SURAT RUJUKAN</span>
    </p>
    <p style="margin-top:5px;text-align:center;">No. ..............................</p>

    <table class="table-tujuan" width="100%" style="margin-top: 5em">
        <tr><td>Kepada:</td></tr>
        <tr><td>Yth.....................................................</td></tr>
        <tr><td><?= $pasien->relasi_rujuk ?></td></tr>
        <?php
            $wil = $this->m_pendaftaran->get_wilayah_relasi_instansi($pasien->rujuk_instansi_id);
        ?>
        <tr>
            <td>Di  <?php if($wil != null){ echo $wil->kecamatan.", ".$wil->kabupaten;} ?> </td>
        </tr>
        
     </table>

     <p>Mohon pemeriksaan dan pengobatan lebih lanjut terhadap penderita,</p>
     <table width="100%" style="margin-left:50px;">
        <tr><td width="20%">NAMA PASIEN:</td><td width="80%"><?= $pasien->nama ?></td></tr>
        <tr><td>JENIS KELAMIN:</td><td><?= ($pasien->gender == 'L')?'Laki-laki':(($pasien->gender == 'P')?'Perempuan':'') ?></td></tr>
        <tr><td>UMUR:</td><td><?= hitungUmur($pasien->lahir_tanggal) ?></td></tr>
        <tr><td>NO. TELP.:</td><td><?= $pasien->telp ?></td></tr>
        <tr><td>ALAMAT JALAN:</td><td><?= $pasien->alamat ?></td></tr>
        <tr><td>WILAYAH:</td><td><?= $pasien->kelurahan.' '.$pasien->kecamatan.' '.$pasien->provinsi ?></td></tr>
        <tr><td>ANAMNESA:</td><td></td></tr>
        <tr><td colspan="2"><span style="margin-left:50px;">...........................................................................................................................................................................................................................................</span></td></tr>
        <tr><td>DIAGNOSA SEMENTARA:</td><td></td></tr>
        <tr><td colspan="2"><span style="margin-left:50px;">...........................................................................................................................................................................................................................................</span></td></tr>
        <tr><td colspan="2"><span style="margin-left:50px;">...........................................................................................................................................................................................................................................</span></td></tr>
        <tr><td>KASUS:</td><td></td></tr>
        <tr><td colspan="2"><span style="margin-left:50px;">...........................................................................................................................................................................................................................................</span></td></tr>
        <tr><td colspan="2">TERAPI/OBAT YANG SUDAH DIBERIKAN:</td></tr>
        <tr><td colspan="2"><span style="margin-left:50px;">...........................................................................................................................................................................................................................................</span></td></tr>
        <tr><td colspan="2"><span style="margin-left:50px;">...........................................................................................................................................................................................................................................</span></td></tr>
        <tr><td colspan="2"><span style="margin-left:50px;">...........................................................................................................................................................................................................................................</span></td></tr>
        <tr><td colspan="2"><span style="margin-left:50px;">...........................................................................................................................................................................................................................................</span></td></tr>
    
    </table>

    <p>Demikian surat rujukan ini kami kirim, kami mohon balasan atas surat rujukan ini. Atas Perhatian Bapak/Ibu/Sdr./i kami mengucapkan terimakasih</p>
  
    
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