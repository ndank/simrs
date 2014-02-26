<title><?= $title ?></title>
<script type="text/javascript">
$(function() {
    $('#awal,#akhir').datepicker({
        changeYear: true,
        changeMonth: true
    });
    $('#tampil').button({
        icons: {
            secondary: 'ui-icon-search'
        }
    });
    $('#cetak').button({
        icons: {
            secondary: 'ui-icon-print'
        }
    }).click(function() {
        var wWidth = $(window).width();
        var dWidth = wWidth * 1;
        var wHeight= $(window).height();
        var dHeight= wHeight * 1;
        var x = screen.width/2 - dWidth/2;
        var y = screen.height/2 - dHeight/2;
        window.open('<?= base_url('laporan/neraca_load_data') ?>?awal='+$('#awal').val()+'&akhir='+$('#akhir').val()+'&do=cetak', 'Penerimaan', 'width='+dWidth+', height='+dHeight+', left='+x+',top='+y);
    });
    $('#reset').button({
        icons: {
            secondary: 'ui-icon-refresh'
        }
    }).click(function() {
        $('#loaddata').empty().load('<?= base_url('laporan/neraca') ?>');
    });
    $('#tampil').click(function() {
        $.ajax({
            url: '<?= base_url('laporan/neraca_load_data') ?>',
            data: 'awal='+$('#awal').val()+'&akhir='+$('#akhir').val(),
            cache: false,
            beforeSend: function() {
                $('#loading').show();
            },
            success: function(data) {
                $('#result').html(data);
            }
        });
    });
});
</script>
<div class="kegiatan">
    <div class="titling"><h1><?= $title ?></h1></div>
    <div class="data-input">
        <table width="100%" class="inputan">Parameter</legend>
            <tr><td>Range Tanggal Jurnal:</td><td><?= form_input('awal', date("d/m/Y"), 'id=awal') ?><span class="label">s . d</span><?= form_input('akhir', date("d/m/Y"), 'id=akhir') ?>
            <tr><td></td><td><?= form_button(null, 'Tampil', 'id=tampil') ?> <?= form_button(null, 'Cetak', 'id=cetak') ?> <?= form_button(null, 'Reset', 'id=reset') ?>
        </table>
    </div>
    <div id="result">
        
    </div>
</div>