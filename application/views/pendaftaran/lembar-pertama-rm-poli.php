<script type="text/javascript">
    function cetak() {
        setTimeout(function(){ window.close();},300);
        window.print();    
    }
</script>
<title><?= $title ?></title>
<link rel="stylesheet" href="<?= base_url('assets/css/print-A4.css') ?>" />
<style>
    
    .list-data { border-spacing: 0; }
    .list-data th,.list-data td { border-right: 1px solid #000; height: 20px; }
    .list-data th:last-child, .list-data td:last-child { border: none; }
</style>
<body onload="cetak();">
    <table width="100%" style="color: #000; border-bottom: 1px solid #000;">
        <tr>
            <td rowspan="3" style="width: 70px"><img src="<?= base_url('assets/images/company/' . $apt->logo_file_nama) ?>" width="70px" height="70px" /></td>    
            <td colspan="3" align="center"><b><?= strtoupper($apt->nama) ?></b></td> <td rowspan="3" style="width: 70px">&nbsp;</td>
        </tr>
        <tr><td colspan="3" align="center"><b><?= strtoupper($apt->alamat) ?> <?= strtoupper($apt->kelurahan) ?></b></td> </tr>
        <tr><td colspan="3" align="center"><b>TELP. <?= $apt->telp ?>,  FAX. <?= $apt->fax ?>, EMAIL <?= $apt->email ?></b></td> </tr>
    </table>
    <table width="100%" cellspacing="0" style="border-bottom: 1px solid #000;">
        <tr>
            <td width="50%" style="border-right: 1px solid #000;"><span style="font-size: 30px;"><?= $title ?></span></td>
            <td width="50%"><span style="font-size: 30px;">No. RM.:<?= $rows->no_rm ?></span></td>
        </tr>
    </table><br/>
    <table width="100%" style="border-bottom: 1px solid #000; padding-bottom: 10px">
        <tr><td width="20%">NAMA LENGKAP:</td><td><?= $rows->nama ?>, <?= $rows->keterangan ?></td></tr>
        <tr><td>TANGGAL LAHIR/UMUR:</td><td><?= datefmysql($rows->lahir_tanggal) . ' / ' . hitungUmur($rows->lahir_tanggal) ?></td></tr>
        <tr><td>AGAMA:</td><td><?= $rows->agama ?></td></tr>
        <tr><td>PEKERJAAN:</td><td><?= $rows->pekerjaan ?></td></tr>
        <tr><td>ALAMAT JALAN:</td><td><?= $rows->alamat ?></td></tr>
        <tr><td>KELURAHAN:</td><td><?= $rows->kelurahan . ' ' . $rows->kecamatan . ' ' . $rows->provinsi ?></td></tr>
        <tr><td>NO. TELP</td><td><?= $rows->telp ?></td></tr>
    </table>
    <table width="100%" style="border-bottom: 1px solid #000; padding-bottom: 10px">
        <tr><td width="20%">RUJUKAN DARI:</td><td><?= strtoupper($rows->rs_perujuk) ?></td></tr>
        <tr><td>NAKES PERUJUK:</td><td><?= strtoupper($rows->nakes_perujuk) ?></td></tr>
    </table>
    <table width="100%" style="border-bottom: 1px solid #000; padding-bottom: 10px">
        <tr><td colspan="2">DALAM KEADAAN PENTING HARAP MENGHUBUNGI</td></tr>
        <tr><td width="20%">NAMA</td><td><?= $rows->nama_pjwb ?></td></tr>
        <tr><td>ALAMAT</td><td><?= $rows->alamat_pjwb ?></td></tr>
        <tr><td>NO. TELP</td><td><?= $rows->telp_pjwb ?></td></tr>
    </table>
    <table width="100%" style="border-bottom: 1px solid #000; padding-bottom: 10px">
        <tr></tr>
        <tr valign="top"><td width="20%">KETERANGAN</td><td></td></tr>
    </table>
    <table width="100%" class="list-data">
        <tr>
            <th width="10%">Tanggal/Jam</th>
            <th width="35%">Anamnesis & Pemeriksaan Fisik</th>
            <th width="30%">Pemeriksaan Penunjang</th>
            <th width="10%">Diagnosis & Terapi</th>
            <th width="15%">TTD DOKTER</th>
        </tr>
        <?php foreach ($rm as $key => $val):?>
            <?php foreach ($val->pelayanan_kunjungan as $key => $pk):?>
                <tr>
                    <td style="vertical-align:top;"><?= ($pk->waktu !== null)?datetimefmysql($pk->waktu, true):'' ?></td>
                    <td style="vertical-align:top;"><?= "<b></b>".$pk->anamnesis."<br/>"."<b></b>".$pk->pemeriksaan_umum ?></td>
                    <td style="vertical-align:top;">
                        <b></b>
                        <ul>
                        <?php foreach ($pk->diagnosis as $key => $diag):?>
                            <li><?= $diag->golongan_sebab ?></li>
                        <?php endforeach;?>
                        </ul>

                        <b></b>
                        <ul>
                        <?php foreach ($pk->tindakan as $key => $tind):?>
                            <li><?= $tind->tindakan ?></li>
                        <?php endforeach;?>
                        </ul>
                    </td>
                    <td>&nbsp;</td>
                    <td style="vertical-align:top;"><?= $pk->nama_pegawai ?></td>
                </tr>
                 <tr><td><hr/></td><td><hr/></td><td><hr/></td><td><hr/></td><td><hr/></td></tr>
            <?php endforeach; ?>            
        <?php endforeach; ?>
        
        <?php for ($i = 0; $i <= 21; $i++) { ?>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
        <?php } ?>
    
    </table>
</body>