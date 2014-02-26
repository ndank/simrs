<title><?= $title ?></title>
<script type="text/javascript">
function swallow_my_load(url, element) {
    if ($(element).html() === '') {
        $.ajax({
            url: url,
            success: function(data) {
                $(element).html(data);
            }
        });
    }
}
$(function() {
    swallow_my_load('<?= base_url('billing/pembayaran_total_kunjungan') ?>','#kunjungan');
    $('#tabs').tabs();
    $('#tabs-1').click(function() {
        swallow_my_load('<?= base_url("billing/pembayaran_total_kunjungan") ?>','#kunjungan');
    });
    $('#tabs-2').click(function() {
        swallow_my_load('<?= base_url("pelayanan/pembayaran_penjualan_nr") ?>','#nonresep');
    });
    $('#tabs-3').click(function() {
        swallow_my_load('<?= base_url("billing/pembayaran_salin_resep") ?>','#sisatebusresep');
    });
}); 
</script>
<div class="titling"><h1><?= $title ?></h1></div>
<div class="kegiatan">
    <div id="tabs">
        <ul>
            <li><a href="#kunjungan" id="tabs-1">Pembayaran Total Kunjungan</a></li>
            <li><a href="#nonresep" id="tabs-2">Pembayaran Non Resep</a></li>
            <!--<li><a href="#sisatebusresep" id="tabs-3">Pembayaran Sisa Copy Resep</a></li>-->
        </ul>
        <div id="kunjungan"></div>
        <div id="nonresep"></div>
        <!--<div id="sisatebusresep"></div>-->
    </div>
</div>