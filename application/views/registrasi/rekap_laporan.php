<?php $this->load->view('message') ?>
<title><?= $title ?></title>
<div class="kegiatan">
    <script type="text/javascript">
        var dWidth = $(window).width();
        var dHeight= $(window).height();
        var x = screen.width/2 - dWidth/2;
        var y = screen.height/2 - dHeight/2;

        function cetak_rl(nama, url){
            var url_rep = url.replace('{tahun}', $('#tahun').val());
            url_rep = url_rep.replace('{bulan}', $('#bulan').val());
            window.open('<?= base_url("'+url_rep+'") ?>/',nama,'width='+dWidth+', height='+dHeight+', left='+x+',top='+y);
        }
    </script>
    <div class="titling"><h1><?= $title ?></h1></div>
    <div class="data-input">
        <table width="100%" class="inputan">Parameter</legend>
        <?= form_open('', 'id=form') ?>
        <tr><td>Bulan</td><td><?= form_dropdown('bulan', $bulan, $bulan_now, 'id=bulan style=width:120px') ?>
        <tr><td>Tahun</td><td><?= form_dropdown('tahun', $tahun, $tahun_now, 'id=tahun style=width:120px') ?>
        <?= form_close() ?>
        </table>
    </div>

    <div class="data-list">
        <table cellpadding="0" cellspacing="0" class="tabel" width="100%">
            <thead>
                <tr>
                    <th width="5%">No.</th>
                    <th width="20%">Kode RL</th>
                    <th>Nama RL</th>
                    <th width="15%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rl as $key => $val): ?>
                <tr>
                    <td align="center"><?= (++$key) ?></td>
                    <td align="center"><?= $val->kode_rl ?></td>
                    <td><?= $val->nama ?></td>
                    <td align="center"><span class="link_button" onclick="cetak_rl('<?= $val->nama ?>','<?= $val->url ?>')">Cetak</span></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    
</div>