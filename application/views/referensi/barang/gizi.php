<title><?= $title ?></title>
<script type="text/javascript">
$(function() {
    $('#tabs').tabs();
    load_my_page('<?= base_url('referensi/perbekalan_gizi') ?>','#tabs-1');
});

function load_my_page(url, el) {
    //if ($(el).html() === '') {
        $.ajax({
            url: url,
            success: function(data) {
                $(el).html(data);
            }
        });
    //}
}
function paging(page, tab, search){
    var active = $("#tabs").tabs('option', 'active');
    if (active === 0) {
        get_nonobat_list(page,search);
    } else {
        get_packing_list(page);
    }
}

</script>
<div class="kegiatan">
    <div class="titling"><h1><?= $title ?></h1></div>
    <div id="tabs">
        <ul>
            <li><a href="#tabs-1" onclick="load_my_page('<?= base_url('referensi/perbekalan_gizi') ?>','#tabs-1');">Barang Gizi</a></li>
            <li><a href="#tabs-2" onclick="load_my_page('<?= base_url('referensi/packing_barang_gizi') ?>','#tabs-2');">Kemasan</a></li>
        </ul>
        <div id="tabs-1"></div>
        <div id="tabs-2"></div>
    </div>
</div>