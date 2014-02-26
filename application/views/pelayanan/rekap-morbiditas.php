<title><?= $title ?></title>
<script type="text/javascript">
$(function() {
    $('#tabs').tabs();
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
            url: '<?= base_url('pelayanan/rekap_morbiditas_load_data') ?>',
            type: 'GET',
            data: $('#form_morbiditas').serialize(),
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
        location.href='<?= base_url('pelayanan/rekap_morbiditas_load_data') ?>?'+$('#form_morbiditas').serialize()+'&do=cetak';
    });
    $('#reset').button({
        icons: {
            secondary: 'ui-icon-refresh'
        }
    }).click(function() {
        $('#loaddata').empty().load('<?= base_url('pelayanan/rekap_morbiditas') ?>');
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
        <form action="" id="form_morbiditas">
        <table width="100%" class="inputan">
            <tr><td>Range Tanggal:</td><td><?= form_input('awal', date("d/m/Y"), 'id=awal size=10 style="width: 75px;"') ?> s.d <?= form_input('akhir', date("d/m/Y"), 'id=akhir size=10 style="width: 75px;"') ?>
            <tr><td>Kondisi:</td><td><?= form_dropdown('kondisi', array('' => 'Pilih ...', 'Tidak' => 'Hidup', 'Ya' => 'Mati'), isset($_GET['kondisi'])?$_GET['kondisi']:NULL) ?>
            <tr><td>Sudah Keluar:</td><td><?= form_dropdown('keluar', array('' => 'Pilih ...', 'Sudah' => 'Sudah', 'Belum' => 'Belum'), isset($_GET['keluar'])?$_GET['keluar']:NULL) ?>
            <tr><td>Sex:</td><td><span class="label"><?= form_radio('sex', 'L', false, 'id=laki') ?> Laki-laki</span>  <span class="label"><?= form_radio('sex', 'P', FALSE, 'id=perempuan') ?> Perempuan</span>
            <tr><td>Kelompok Umur:</td><td><?= form_dropdown('klpumur', $klp_umur, '', 'id=klpumur') ?>
            <tr><td></td><td><?= form_button(NULL, 'Cari', 'id=cari') ?> <?= form_button(NULL, 'Cetak', 'id=cetak') ?> <?= form_button(NULL, 'Reset', 'id=reset') ?>
        </table>
        </form>
        <div id="result">

        </div>
        </div>
    </div>
</div>