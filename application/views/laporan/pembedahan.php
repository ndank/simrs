<title><?= $title ?></title>
<script type="text/javascript">
$(function() {
    $('#awal,#akhir').datepicker({
        changeYear: true,
        changeMonth: true
    });
    $('#cari').button({icons: {secondary: 'ui-icon-search'}}).click(function() {
        $.ajax({
            url: '<?= base_url('laporan/pembedahan_load_data') ?>',
            type: 'GET',
            data: $('#form_rekap_perinatori').serialize(),
            cache: false,
            success: function(data) {
                $('#result').html(data)
            }
        });
    });
    $('#cetak').button({icons: {secondary: 'ui-icon-print'}}).click(function() {
        var awal = $('#awal').val();
        var akhir = $('#akhir').val();
        var dWidth = $(window).width();
        var dHeight= $(window).height();
        var x = screen.width/2 - dWidth/2;
        var y = screen.height/2 - dHeight/2;
        window.open('<?= base_url("laporan/pembedahan_load_data") ?>?'+$('#kegiatan_rujukan').serialize()+'&do=cetak&awal='+awal+'&akhir='+akhir,'Myprint','width='+dWidth+', height='+dHeight+', left='+x+',top='+y);
    });
    $('#reset').button({icons: {secondary: 'ui-icon-refresh'}}).click(function() {
        $('#loaddata').empty().load('<?= base_url("laporan/rekap_pembedahan") ?>');
    });
});
</script>
<div class="kegiatan">
    <div class="titling"><h1><?= $title ?></h1></div>
    <div class="data-input">
        <table width="100%" class="inputan">Parameter</legend>
            <tr><td>Range Tanggal:</td><td><?= form_input('awal', date("d/m/Y"), 'id=awal size=10') ?> <span class="label">s . d</span><?= form_input('akhir', date("d/m/Y"), 'id=akhir size=10') ?>
            <tr><td>Bobot:</td><td><?= form_dropdown('bobot', array('' => 'Pilih ...', 'Khusus' => 'Khusus', 'Besar' => 'Besar', 'Sedang' => 'Sedang', 'Kecil' => 'Kecil')) ?>
            <tr><td></td><td><?= form_button(NULL, 'Cari', 'id=cari') ?> <?= form_button(NULL, 'Cetak', 'id=cetak') ?> <?= form_button(NULL, 'Reset', 'id=reset') ?>
        </table>
    </div>
    <div id="result">
        
    </div>
</div>