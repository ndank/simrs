<title><?= $title ?></title>
<div class="titling"><h1><?= $title ?></h1></div>
<div class="kegiatan">
<script type="text/javascript">
    $(function() {
        $('#tabs').tabs();
        $('#cari').button({icons: {secondary: 'ui-icon-circle-check'}});
        $('#reset').button({icons: {secondary: 'ui-icon-refresh'}});
       
        $('#awal, #akhir').datepicker({
            changeYear: true,
            changeMonth: true,
            maxDate : -1
        });

      
        $('#reset').click(function() {
            $('#loaddata').empty();
            $('#loaddata').load('<?= base_url("pelayanan/bor_rawat_inap") ?>?_='+Math.random());
        });
            
    });

    function load_penjualan_jasa(no_daftar){
         $.ajax({
                type: 'GET',
                url: '<?= base_url ()?>pelayanan/penjualan_jasa/'+no_daftar,
                cache : false,
                success: function(data) {
                    $('#loaddata').html(data);
                }
            });
    }

    function get_bor_list(p){
        $.ajax({
            type: 'GET',
            url:'<?= base_url ("pelayanan/get_bor_list")?>/'+p,
            data: $('#bor_irna').serialize(),
            success: function(data) {
                $('#bor_list').html(data);
            }
        });
    }

    function paging(page, tab, cari){
            get_bor_list(page);
        }
</script>

<div id="tabs">
    <ul>
        <li><a href="#tabs-1">Parameter</a></li>
    </ul>
    <div id="tabs-1">
    <?= form_open('laporan/rekap_pendapatan', 'id=bor_irna') ?>
    <table width="100%" class="inputan">
        <tr><td style="width: 150px;">Waktu:</td><td><?= form_input('awal', date("01/m/Y"), 'id=awal size=10 style="width: 75px;" readonly') ?> <span class="label"> s.d </span><?= form_input('akhir', date("d/m/Y"), 'id=akhir size=10 style="width: 75px;" readonly') ?></td></tr>
        <tr><td>Bangsal:</td><td><?= form_dropdown('layanan', $layanan, array(), 'id=layanan class="standar enter"') ?></td></tr>
        <tr><td></td><td>
        <?= form_button('submit', 'Cari', 'id=cari onclick=get_bor_list(1)') ?> <?= form_button(null, 'Reset', 'id=reset') ?></td></tr>
    </table>
    <?= form_close() ?>
    <div id="bor_list"></div>
    </div>
</div>
</div>