<script type="text/javascript">
    function cetak() {
        setTimeout(function(){ window.close();},300);
        window.print();    
    }
</script>
<title><?= $title ?></title>
<style>
    *{ font-size: 10px; font-family: "Times New Roman"}
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
    <table width="100%" cellspacing="0" style="border-bottom: 1px solid #000;">
        <tr>
            <td width="50%" style="border-right: 1px solid #000;"><span style="font-size: 30px;"><?= $title ?></span></td>
            <td width="50%">
                <table width="100%">
                    <tr><td><span style="font-size: 30px;">No. RM.:<?= $rows->no_rm ?></td></tr>
                    <tr><td><span style="font-size: 20px;">Tanggal : <?= date('d/m/Y')?></span></td></tr>
                </table>
            </span></td>
        </tr>
    </table><br/>
    <table width="100%" style="border-bottom: 1px solid #000; padding-bottom: 10px">
        <tr><td width="20%">NAMA LENGKAP:</td><td><?= $rows->nama ?></td></tr>
        <tr><td>TANGGAL LAHIR/UMUR:</td><td><?= datefmysql($rows->lahir_tanggal) . ' / ' . createUmur($rows->lahir_tanggal) ?></td></tr>
        <tr><td>AGAMA:</td><td><?= $rows->agama ?></td></tr>
        <tr><td>PEKERJAAN:</td><td><?= $rows->pekerjaan ?></td></tr>
        <tr><td>ALAMAT JALAN:</td><td><?= $rows->alamat ?></td></tr>
        <tr><td>KELURAHAN:</td><td><?= $rows->kelurahan . ' ' . $rows->kecamatan . ' ' . $rows->provinsi ?></td></tr>
        <tr><td>NO. TELP</td><td><?= $rows->telp ?></td></tr>
        <tr><td>Bangsal/Kelas</td><td><?= $pk->unit." / ".$pk->kelas ?></td></tr>
        <tr><td>No. TT</td><td><?= $pk->no_tt ?></td></tr>
    </table>
    <br/>
    <table width="100%" style="border-bottom: 1px solid #000; padding-bottom: 10px">
        <tr><td colspan="2">DALAM KEADAAN PENTING HARAP MENGHUBUNGI</td></tr>
        <tr><td width="20%">NAMA</td><td><?= $rows->nama_pjwb ?></td></tr>
        <tr><td>ALAMAT</td><td><?= $rows->alamat_pjwb ?></td></tr>
        <tr><td>NO. TELP</td><td><?= $rows->telp_pjwb ?></td></tr>
    </table>

    <table width="100%" class="list-data inap">
        <tr style="height:40px">
            <td width="25%">Dirawat di Kelas</td>
            <td><table width="100%" class="table"><tr><td>Pindah Ruang</td><td>Tanggal</td></tr></table></td>
        </tr>

        <tr style="height:40px;">
            <td>Bagian Penyakit</td>
            <td>&nbsp;</td>
        </tr>
        
        <tr>
            <td>Dirawat Oleh</td>
            <td>
                <ol class="li-special"><li></li><li></li><li></li></ol>
            </td>
        </tr>

        <tr>
            <td>Diagnosis</td>
            <td>
                <ol class="li-special"><li></li><li></li><li></li></ol>
            </td>
        </tr>

        <tr style="height:40px;">
            <td>Nama dan Tanggal Operasi</td>
            <td></td>
        </tr>

        <tr>
            <td><table width="100%" class="table"><tr><td>Masuk Tanggal</td></tr><tr><td>Keluar Tanggal</td></tr></table></td>
            <td>
                <table width="100%" class="table">
                    <tr><td width="20%"></td><td>jam</td></tr>
                    <tr><td></td><td>jam</td></tr></table>
            </td>
        </tr>


        <tr>
            <td>Keadaan Keluar</td>
            <td>
                <ul style="list-style-type: none;padding-left:2px;">
                    <li><input type="checkbox" /><span>Sembuh</span></li>
                    <li><input type="checkbox" />Belum sembuh</li>
                    <li><input type="checkbox" />Dirujuk</li>
                    <li><input type="checkbox" />Pulang Paksa</li>
                    <li><input type="checkbox" />Lari</li>
                    <li><input type="checkbox" />Meninggal</li>
                </ul>
            </td>
        </tr>


        <tr>
            <td>Biaya Ditanggung</td>
            <td>
                <ul style="list-style-type: none;padding-left:2px;">
                    <li><input type="checkbox" />Sendiri</li>
                    <li><input type="checkbox" />Instansi</li>
                    <li><input type="checkbox" />Asuransi</li>
                    <li><input type="checkbox" />DSM</li>
                </ul>
            </td>
        </tr>
    </table>
    <br/><br/><br/>
</body>