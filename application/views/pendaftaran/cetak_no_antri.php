<link rel="stylesheet" href="<?= base_url('assets/css/print-struk.css') ?>" />
<script type="text/javascript">
    function cetak() {
        window.print();    
        setTimeout(function(){ window.close();},300);
    }
</script>
<title><?= $title.' - '.(isset($pasien->no_rm)?$pasien->no_rm:'').' - '.$pasien->nama ?></title>
<body onload="cetak()" style="height:auto">
<div class="print-area">
    <table width="100%">
        <tr>
            <td>
                <h2>R.S Queen Latifa</h2>
                <?= $antri->nama_pegawai ?><br/>
                <?= $antri->unit_layanan.", ".$antri->jenis_jurusan ?> <br/>
                <?= dateconvert($pasien->tgl_layan) ?> 
            </td>
        </tr>
        <tr>
            <td class="border">
                <b style="font-size: 25px; font-weight: bold;"><?= $antri->no_antri ?></b>
            </td>
        </tr>
        <tr>
            <td class="border">
                <?= $pasien->nama ?><br/>
                <?= isset($pasien->no_rm)?$pasien->no_rm:'' ?>
            </td>
        </tr>
    </table>
</div>
</body>
<?php die; ?>
