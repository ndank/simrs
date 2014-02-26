<title><?= $title ?></title>
<script type="text/javascript">
$(function() {
    $('#awal,#akhir').datepicker({
        changeYear: true,
        changeMonth: true
    });
    $('#cari').button({
        icons: {
            secondary: 'ui-icon-search'
        }
    }).click(function() {
        $.ajax({
            url: '<?= base_url('laporan/kebidanan_load_data') ?>',
            type: 'GET',
            data: $('#form_rekap_kebidanan').serialize(),
            cache: false,
            success: function(data) {
                $('#result').html(data)
            }
        });
    });
    $('#cetak').button({
        icons: {
            secondary: 'ui-icon-print'
        }
    }).click(function() {
        location.href='<?= base_url('laporan/kebidanan_load_data') ?>?'+$('#kegiatan_rujukan').serialize()+'&do=cetak';
    });
    $('#reset').button({
        icons: {
            secondary: 'ui-icon-refresh'
        }
    }).click(function() {
        $('#loaddata').empty().load('<?= base_url('laporan/kebidanan') ?>');
    });
});
</script>
<div class="kegiatan">
    <div class="titling"><h1><?= $title ?></h1></div>
    <div class="data-input">
        <?= form_open('laporan/kebidanan_load_data', 'id=form_rekap_kebidanan') ?>
        <table width="100%" class="inputan">Parameter</legend>
            <tr><td>Range Tanggal:</td><td><?= form_input('awal', date("d/m/Y"), 'id=awal size=10') ?> <span class="label">s . d</span><?= form_input('akhir', date("d/m/Y"), 'id=akhir size=10') ?>
            <tr><td>Rujukan:</td><td><span class="label"><?= form_radio('rujukan', 'Tidak', TRUE, 'id=tidak') ?> Tidak</span>  <span class="label"><?= form_radio('rujukan', 'Ya', FALSE, 'id=ya') ?> Iya</span>
            <tr><td>Jenis Instansi Perujuk:</td><td><?= form_dropdown('jenis', array('' => 'Pilih ...', 'Puskesmas' => 'Puskesmas', 'R.S' => 'R.S', 'lain' => 'Faskes Lain')) ?>
            <tr><td>jenis Nakes Perujuk:</td><td><?= form_dropdown('jnakes', array('' => 'Pilih ...', 'Medis' => 'Medis', 'Non Medis' => 'Non Medis')) ?>
            <tr><td>Dirujuk:</td><td><span class="label"><?= form_radio('dirujuk', 'Tidak', TRUE, 'id=tidak') ?> Tidak</span>  <span class="label"><?= form_radio('dirujuk', 'Ya', FALSE, 'id=ya') ?> Iya</span>
            <tr><td></td><td><?= form_button(NULL, 'Cari', 'id=cari') ?> <?= form_button(NULL, 'Cetak', 'id=cetak') ?> <?= form_button(NULL, 'Reset', 'id=reset') ?>
        </table>
        <?= form_close() ?>
    </div>
    <div id="result">
        
    </div>
</div>