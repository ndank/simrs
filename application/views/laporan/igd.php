<title><?= $title ?></title>
<script type="text/javascript">
$(function() {
    $('#tabs').tabs();
    $('#awal,#akhir').datepicker({
        changeYear: true,
        changeMonth: true
    });
    $('#cari').button({icons: {secondary: 'ui-icon-search'}}).click(function() {
        $.ajax({
            url: '<?= base_url('pelayanan/rekap_igd_load_data') ?>',
            type: 'GET',
            data: $('#form_rekap_igd').serialize(),
            cache: false,
            success: function(data) {
                $('#result').html(data)
            }
        });
    });
    $('#cetak').button({icons: {secondary: 'ui-icon-print'}}).click(function() {
        location.href='<?= base_url("pelayanan/rekap_igd_load_data") ?>?'+$('#form_rekap_igd').serialize()+'&do=cetak';
    });
    $('#reset').button({
        icons: {
            secondary: 'ui-icon-refresh'
        }
    }).click(function() {
        $('#loaddata').empty().load('<?= base_url('laporan/igd') ?>');
    });
});
</script>
<div class="titling"><h1><?= $title ?></h1></div>
<div class="kegiatan">
    <div id="tabs">
        <ul>
            <li><a href="#tabs-1">Parameter</a></li>
        </ul>
        <div id="tabs-1">
            <?= form_open('', 'id=form_rekap_igd') ?>
            <table width="100%" class="inputan">
                <tr><td>Range Tanggal:</td><td><?= form_input('awal', date("d/m/Y"), 'id=awal size=10 style="width: 75px;"') ?> s.d <?= form_input('akhir', date("d/m/Y"), 'id=akhir size=10 style="width: 75px;"') ?>
                <tr><td>Rujukan:</td><td><span class="label"><?= form_radio('rujukan', '', FALSE, 'id=tidak') ?> Tidak</span>  <span class="label"><?= form_radio('rujukan', 'Ya', FALSE, 'id=ya') ?> Iya</span>
                <tr><td>Tindak Lanjut:</td><td><?= form_dropdown('tindak_lanjut', array('' => 'Pilih ...', 'dirawat' => 'Dirawat', 'dirujuk'=> 'Dirujuk','pulang' => 'Pulang'), isset($_GET['rujukan'])?$_GET['rujukan']:NULL) ?>
                <tr><td>Mati Di IGD:</td><td><span class="label"><?= form_radio('matiigd', 'Hidup', false, 'id=tidak') ?> Tidak</span>  <span class="label"><?= form_radio('matiigd', 'Meninggal', FALSE, 'id=ya') ?> Iya</span>
                <tr><td>D.O.A</td><td><span class="label"><?= form_radio('doa', 'Tidak', false, 'id=tidak') ?> Tidak</span>  <span class="label"><?= form_radio('doa', 'Ya', FALSE, 'id=ya') ?> Iya</span>
                <tr><td></td><td><?= form_button(NULL, 'Cari', 'id=cari') ?> <?= form_button(NULL, 'Cetak', 'id=cetak') ?> <?= form_button(NULL, 'Reset', 'id=reset') ?>
            </table>
            <?= form_close() ?>

            <div id="result">

            </div>
        </div>
    </div>
</div>