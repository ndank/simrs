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
            <td rowspan="3" style="width: 70px"><img src="<?= base_url('assets/images/company/' . $apt->logo_file_nama) ?>" width="70px" height="70px" /></td>    
            <td colspan="3" align="center"><b><?= strtoupper($apt->nama) ?></b></td> <td rowspan="3" style="width: 70px">&nbsp;</td>
        </tr>
        <tr><td colspan="3" align="center"><b><?= strtoupper($apt->alamat) ?> <?= strtoupper($apt->kelurahan) ?></b></td> </tr>
        <tr><td colspan="3" align="center"><b>TELP. <?= $apt->telp ?>,  FAX. <?= $apt->fax ?>, EMAIL <?= $apt->email ?></b></td> </tr>
    </table>
    <table width="100%" cellspacing="0" style="border-bottom: 1px solid #000;">
        <tr>
            <td width="50%" style="border-right: 1px solid #000;"><span style="font-size: 30px;"><?= $title ?></span></td>
            <td width="50%"><span style="font-size: 30px;">No. RM.:<?= $pasien->no_rm ?></span></td>
        </tr>
    </table><br/>
    <table width="100%" style="border-bottom: 1px solid #000; padding-bottom: 10px">
        <tr><td width="20%">NAMA LENGKAP:</td><td><?= $pasien->nama ?></td></tr>
        <tr><td>TANGGAL LAHIR/UMUR:</td><td><?= datefmysql($pasien->lahir_tanggal) . ' / ' . hitungUmur($pasien->lahir_tanggal) ?></td></tr>
        <tr><td>NO. TELP</td><td><?= $pasien->telp ?></td></tr>
        <tr><td>AGAMA:</td><td><?= $pasien->agama ?></td></tr>
        <tr><td>PENDIDIKAN</td><td><?= ($pasien->pendidikan != '') ? $pasien->pendidikan : '-' ?></td></tr>
        <tr><td>PEKERJAAN:</td><td><?= $pasien->pekerjaan ?></td></tr>
        <tr><td>ALAMAT JALAN:</td><td><?= $pasien->alamat ?></td></tr>
        <tr><td>KELURAHAN:</td><td><?= $pasien->kelurahan . ' ' . $pasien->kecamatan . ' ' . $pasien->provinsi ?></td></tr>
        <tr><td>STATUS PERNIKAHAN</td><td><?= $pasien->pernikahan ?></td></tr>
    </table>
    <br/>
     <div style="color: #000; border-bottom: 1px solid #000; width: 100%;">
        <?php $jml_kunj = sizeof($kunjungan) ?>
        <?php foreach ($kunjungan as $key => $value): ?>
        <h3><b>Kunjungan <?= ($jml_kunj - $key)." : ".indo_tgl($value->tgl_layan) ?></b></h3>
                <table width="100%">
                    <tr><td width="20%">Waktu Kedatangan:</td><td><?= ($value->arrive_time != '') ? datetime($value->arrive_time) : $value->arrive_time ?></td></tr>
                    <tr><td>Kebutuhan Perawatan:</td><td><?= $value->keb_rawat ?></td></tr>
                    <tr><td>Jenis Layanan:</td><td><?= $value->jenis_layan ?></td></tr>
                    <tr><td>Kriteria Layanan:</td><td><?= $value->krit_layan ?></td></tr>
                </table>        

                <tr><td><h2>Pelayanan Kunjungan</h2></td><td>
                <?php foreach ($value->pelayanan_kunjungan as $key2 => $pk): ?>
                <tr><td>
                    <h4>
                        &nbsp;&nbsp;<?= ($key2+1).". ". $pk->jenis ?>
                        <?php 
                            if(($pk->no_antri === null) & ($pk->jenis === 'Rawat Jalan')){
                                echo " (IGD)";
                            }else if(($pk->no_antri !== null) & ($pk->jenis === 'Rawat Jalan')){
                                echo " (Poliklinik)";
                            }
                        ?>
                    </h4>
                </td><td>
                <table width="100%" style="margin-left:25px;">
                    <tr><td>Dokter Penanggung Jwb:</td><td><?= $pk->nama_pegawai ?></td></tr>
                    <tr><td>Anamnesis:</td><td><span><?= $pk->anamnesis ?></span></td></tr>
                    <?php if(($pk->no_antri === null) & ($pk->jenis === 'Rawat Jalan')):?>
                       <tr><td>Pemeriksan Umum:</td><td></td></tr>
                       <tr>
                            <td colspan="2" style="padding-left:130px;">
                                <table width="100%"> 
                                    <tr><td width="80px">Tensi</td><td><?= ($pk->p_tensi != '')?$pk->p_tensi:'-'." mm/Hg" ?></td></tr>
                                    <tr><td>Nadi</td><td><?= ($pk->p_nadi != '')?$pk->p_nadi:'-'." bpm"?></td></tr>
                                    <tr><td>Suhu</td><td><?= ($pk->p_suhu != '')?$pk->p_suhu:'-'." <sup>&deg;</sup> C" ?></td></tr>
                                    <tr><td>Nafas</td><td><?= ($pk->p_nafas != '')?$pk->p_nafas:'-'." " ?></td></tr>
                                    <tr><td>Berat Badan</td><td><?= ($pk->p_bb != '')?$pk->p_bb:'-'." Kg" ?></td></tr>
                                </table>
                            </td>
                        </tr>
                    <?php else: ?>
                         <tr><td>Pemeriksaan Umum:</td><td><span><?= $pk->pemeriksaan_umum ?></span></td></tr>
                    <?php endif; ?>

                    <tr><td>Diagnosa:</td><td></td></tr>
                    <tr>
                            <td colspan="2">
                                <ul style="margin-left:100px;">
                                   <?php foreach ($pk->diagnosis as $key3 => $diag):?>
                                    <li><?= $diag->no_daftar_terperinci." . ".$diag->golongan_sebab ?>  (<?= "Kasus ".$diag->kasus ?>)</li>
                                   <?php endforeach; ?>
                                </ul>
                            </td>
                    </tr>

                    <tr><td>Tindakan:</td><td></td></tr>
                    <tr>
                            <td colspan="2">
                                <ul style="margin-left:100px;">
                                   <?php foreach ($pk->tindakan as $key4 => $td):?>
                                    <li><?= $td->kode_icdixcm." . ".$td->tindakan ?></li>
                                   <?php endforeach; ?>
                                </ul>
                            </td>
                    </tr>

                    <tr><td>Resep:</td><td></td></tr>
                    <tr>
                            <td colspan="2">
                                <ul style="margin-left:100px;">
                                   <?php foreach ($pk->resep as $key5 => $obat):?>
                                    <li><?= $obat->barang."  ".$obat->kekuatan." ".$obat->satuan."  ".$obat->sediaan ?></li>
                                   <?php endforeach; ?>
                                </ul>
                            </td>
                    </tr>

                    <tr><td>Labratorium:</td><td></td></tr>
                    <tr>
                            <td colspan="2">
                                <ul style="margin-left:100px;">
                                   <?php foreach ($pk->lab as $key6 => $lab):?>
                                    <li><?= $lab->layanan ?></li>
                                   <?php endforeach; ?>
                                </ul>
                            </td>
                    </tr>

                    <tr><td>Radiologi:</td><td></td></tr>
                    <tr>
                            <td colspan="2">
                                <ul style="margin-left:100px;">
                                   <?php foreach ($pk->rad as $key7 => $rad):?>
                                    <li><?= $rad->layanan ?></li>
                                   <?php endforeach; ?>
                                </ul>
                            </td>
                    </tr>
                </table>
                <?php endforeach; ?>
        <?php endforeach; ?>
    </div>
    
</body>