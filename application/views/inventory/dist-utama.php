<title><?= $title ?></title>
<?= $this->load->view('message') ?>
<script type="text/javascript">
    $(function() {
        $('#tabs').tabs();
        load_my_fucking_page('<?= base_url('inventory/distributed') ?>','#tabs-1');
    });
    function load_my_fucking_page(url, el) {
        if ($(el).html() === '') {
            $.ajax({
                url: url,
                success: function(data) {
                    $(el).html(data);
                }
            });
        }
    }
    function paging(page, tab, search) {
        var active = $('#tabs').tabs('option','active');
        paginate(page, tab, search, active);
        //load_data_barang(page, search);
    }

    function paginate(page, tab, search, active) {
        if (active === 0) {
            load_data_distribusi(page, search);
        }
        if (active === 1) {
            load_data_distribusi(page, search);
        }
    }
</script>
<div class="titling"><h1><?= $title ?></h1></div>
<div class="kegiatan">
    <div id="tabs">
        <ul>
            <li><a href="#tabs-1" onclick="load_my_fucking_page('<?= base_url('inventory/distribusi') ?>','#tabs-1');">Distribusi</a></li>
            <li><a href="#tabs-2" onclick="load_my_fucking_page('<?= base_url('inventory/penerimaan_distribusi') ?>','#tabs-2');">Penerimaan Distribusi</a></li>
        </ul>
        <div id="tabs-1"></div>
        <div id="tabs-2"></div>
        
    </div>
</div>