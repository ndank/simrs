<title><?= $title ?></title>
<script type="text/javascript">
function load_data_probabilitas(awal, akhir) {
    $.ajax({
        url: '<?= base_url('laporan/load_data_lap_probabilitas') ?>',
        data: 'awal='+awal+'&akhir='+akhir,
        cache: false,
        success: function(data) {
            $('#result').html(data);
        }
    });
}
$(function() {
    $('#tampil').button({
        icons: {
            secondary: 'ui-icon-search'
        }
    }).click(function() {
        var awal = $('#awal').val();
        var akhir= $('#akhir').val();
        var jenis= $('input:checked').val();
        load_data_probabilitas(awal,akhir,jenis);
    });
    $('#cancel').button({
        icons: {
            secondary: 'ui-icon-refresh'
        }
    }).click(function() {
        $('#loaddata').load('<?= base_url('laporan/probabilitas') ?>');
    });
    $('#awal,#akhir').datepicker({
        changeYear: true,
        changeMonth: true
    });
});
</script>
<div class="kegiatan">
    <div class="titling"><h1><?= $title ?></h1></div>  
    <table width="100%" class="inputan">Parameter</legend>
    <div class="data-input">
        <tr><td>Range Tanggal:</td><td><?= form_input('awal', date("d/m/Y"), 'id=awal size=10') ?> <span class="label">s . d</span> <?= form_input('akhir', date("d/m/Y"), 'id=akhir size=10') ?>
        <tr><td></td><td><?= form_button(NULL, 'Tampilkan', 'id=tampil') ?> <?= form_button(NULL, 'Reset', 'id=cancel') ?>
    </div>
    </table>
    <div id="result"></div>
</div>