<title><?= $title ?></title>
<?= $this->load->view('message') ?>
<div class="titling"><h1><?= $title ?></h1></div>
<div class="kegiatan">
    <script type="text/javascript">
    
    $(function() {
        get_list_jenis(1);
        $('#tabs').tabs();
        $('input[type=submit]').each(function(){
            $(this).replaceWith('<button type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'">' + $(this).val() + '</button>');
        });
        $('#simpan').button({icons: {secondary: 'ui-icon-circle-check'}});
        $('#cari').button({icons: {secondary: 'ui-icon-search'}});
        $('#reset').button({icons: {secondary: 'ui-icon-refresh'}}).click(function() {
            $('#loaddata').load('<?= base_url('pelayanan/jenis_layanan') ?>');
            //get_list_jenis(1, null);
        });

        $('#simpan').attr('disabled','disabled');
        $('#form_ss_jenis').submit(function(){
            var url = $(this).attr('action');
            if ($('input[name=hd_id_sub_sub_jenis]').val() === '') {
                url += "/add_sub_sub";
            } else {
                url += "/edit_sub_sub";
            }


            $.ajax({
                url: url,
                type: 'POST',
                data: $('#form_ss_jenis').serialize(),
                success: function(data) {
                    if ($('input[name=hd_id_sub_sub_jenis]').val() == '') {
                        alert_tambah();
                    } else {
                        alert_edit();
                    }
                    $('#list_jenis').html(data);
                    $("#example-advanced").treetable('expandAll');
                }
            });

            return false;
        });

       });

        function get_list_jenis(page) {
            $.ajax({
                url: '<?= base_url("pelayanan/manage_jenis_layanan") ?>/list/'+page,
                cache: false,
                data : $('#form_ss_jenis').serialize(),
                success: function(data) {
                    $('#list_jenis').html(data);
                }
            });
        }
        function paging(page, tab, search){
            get_list_jenis(page);
        }

    </script>
    <div id="tabs">
        <ul>
            <li><a href="#tabs-1">Entri Hasil</a></li>
        </ul>
        <div id="tabs-1">
            <?= form_open('pelayanan/manage_jenis_layanan/', 'id=form_ss_jenis') ?>
                <table width="100%" class="inputan">
                    <?= form_hidden("hd_id_sub_sub_jenis") ?>
                    <tr><td style="width: 150px;">Kode Sub Sub:</td><td><?= form_input('id_sub_sub_jenis', null, 'size=30 onKeyup="Angka(this)" id=id_sub_sub_jenis') ?></td></tr>
                    <tr><td>Jenis Layanan:</td><td><span class="label" id="nama_jenis_layanan">-</span></td></tr>
                    <tr><td>Sub Jenis Layanan:</td><td><span class="label" id="nama_sub_jenis_layanan">-</span></td></tr>
                    <tr><td>Sub Sub Jenis Layanan:</td><td><?= form_input('nama_sub_sub_jenis','','id=nama_sub_sub_jenis size=40') ?></td></tr>
                    <tr><td></td><td><?= form_submit(null, 'Simpan', 'id=simpan') ?> 
                    <?= form_button(null, 'Reset', 'id=reset') ?></td></tr>
                </table>
            <?= form_close() ?>
        
        <div id="list_jenis"></div>
        </div>
    </div>
</div>