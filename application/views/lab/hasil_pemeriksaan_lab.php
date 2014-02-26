<script type="text/javascript">
    function cetak() {
        window.print();
        setTimeout(function(){ window.close();},300);
            
    }
</script>
<title><?= $title ?></title>
<style>
    *{ font-size: 10px; font-family: "Times New Roman"}
    .list-data { border-spacing: 0; }
    .list-data th,.list-data td { border-right: 1px solid #000; height: 20px; }
    .list-data th:last-child, .list-data td:last-child { border: none; }
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
    <table width="100%" cellspacing="0" style="margin-top:30px">
        <tr>
            <td width="50%" align="center"><span style="font-size: 12px;font-weight:bold;">HASIL PEMERIKSAAN LABORATORIUM KLINIK</span></td>
        </tr>
    </table><br/><br/>
    <?php $nama_dokter = ''; ?>
    <?php foreach ($laboratorium as $key => $value):?>
        <?php 
            if($key == 0){
                $nama_dokter = $value->dokter;
            }
        ?>
    <?php endforeach; ?>
    <table width="100%" style="padding: 10px">       
        <tr><td width="20%">NO. RM:</td><td><?= $pasien->no_rm ?></td></tr>
        <tr><td>NAMA PASIEN:</td><td><?= $pasien->pasien ?></td></tr>
        <tr><td>ALAMAT JALAN:</td><td><?= $pasien->alamat ?></td></tr>
        <tr><td>WILAYAH:</td><td><?= $pasien->kelurahan.', '.$pasien->kecamatan.', '.$pasien->kabupaten.', '.$pasien->provinsi ?></td></tr>
        <tr><td>UNIT/BANGSAL:</td><td><?= isset($pasien->nama_unit)?$pasien->nama_unit:$pasien->unit ?></td></tr>
        <tr><td>KELAS:</td><td><?= $pasien->kelas ?></td></tr>
        <tr><td>NO. TT:</td><td><?= $pasien->no_tt ?></td></tr>
        <tr><td>NAMA DOKTER PEMESAN:</td><td><?=  $nama_dokter ?></td></tr>
    </table>
    <div style="padding:10px;">
        <table width="100%"   border="1" cellspacing="0" cellpadding="0" >
            <thead>
                <th width="5%">NO.</th>
                <th width="12%">WAKTU ORDER</th>
                <th width="12%">WAKTU HASIL</th>
                <th width="20%">NAMA LABORAN</th>
                <th width="18%">NAMA LAYANAN</th>
                <th width="10%">HASIL</th>
                <th width="15%">NILAI RUJUKAN</th>
                <th width="10%">SATUAN</th>
            </thead>
            <tbody>
                <?php foreach ($laboratorium as $key => $value): ?>
                    <tr>
                        <td align="center"><?= $key+1 ?></td>
                        <td align="center"><?= ($value->waktu_order != '')?datetimefmysql($value->waktu_order, true):'' ?></td>
                        <td align="center"><?= ($value->waktu_hasil != '')?datetimefmysql($value->waktu_hasil, true):'' ?></td>
                        <td><?= $value->laboran ?></td>
                        <td><?= $value->layanan ?></td>
                        <td align="center"><?= $value->hasil ?></td>
                        <td align="center"><?= $value->ket_nilai_rujukan ?></td>
                        <td align="center"><?= $value->satuan ?></td>
                    </tr>
                <?php endforeach;?>

            </tbody>
        </table>
    </div>
    <table width="100%" style="margin-top: 6em">
        <tr style="margin-bottom:20px">
            <td width="50%" align="center">PENANGGUNG JAWAB</td>
            <td width="50%" align="center">PETUGAS PEMERIKSA</td>
        </tr>
    </table>
    <table width="100%" style="margin-top: 8em">
        <tr>
            <td width="50%" align="center"><div style="width:50%;border-bottom: 1px solid #000;"><?= '' ?></div></td>
            <td width="50%" align="center"><div style="width:50%;border-bottom: 1px solid #000;"><?= '' ?></div></td>
            
        </tr>
         <tr>
            <td width="50%" align="center">AHLI PATOLOGI KLINIK</td>
            <td width="50%" align="center"></td>
        </tr>
    </table>

</body>