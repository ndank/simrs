<?php $this->load->view('message') ?>
<title><?= $title ?></title>
<div class="kegiatan">
    <script type="text/javascript">
        $(function() {
            $('#pb').focus();
            $('input[type=submit]').each(function(){
                $(this).replaceWith('<button type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'">' + $(this).val() + '</button>');
            });
            $('button[type=submit]').button({
                icons: {
                    secondary: 'ui-icon-circle-check'
                }
            });
            $('button[id=reset]').button({
                icons: {
                    secondary: 'ui-icon-refresh'
                }
            })
            $('button[id=update]').button({
                icons: {
                    secondary: 'ui-icon-pencil'
                }
            })
            $('#akhir').datepicker({
                changeYear: true,
                changeMonth: true
            })
        
            $('#update').hide();
            $('#reset').click(function() {
                var url = '<?= base_url('referensi/harga_jual') ?>';
                $('#loaddata').load(url);
            })
            $('#searchs').click(function() {
                $('#form-update').fadeIn('fast').draggable();
            })
        
            $('#form_harga_jual').submit(function() {
                if ($('#id_pb').val() == '') {
                    custom_message('Peringatan','Packing barang tidak boleh kosong !');
                    $('#pb').focus();
                    return false;
                }
                if ($('#akhir').val() == '') {
                    custom_message('Peringatan','Tanggal batas akhir tidak boleh kosong !');
                    $('#akhir').focus();
                    return false;
                }
                $('#searchs').fadeIn('fast');
                var id_pb= $('#pb').val();
                $.ajax({
                    url: '<?= base_url('referensi/harga_jual_load') ?>',
                    data: 'pb='+id_pb,
                    cache: false,
                    success: function(msg) {
                        $('#result').html(msg);
                    }
                })
                return false;
            })
        
        })
    </script>
    <div class="titling"><h1><?= $title ?></h1></div>
    <div class="data-input">
        <table width="100%" class="inputan">Parameter</legend>
            <?= form_open('referensi/harga_jual', 'id=form_harga_jual') ?>
            <tr><td>Nama Barang</td><td> <?= form_input('pb', isset($_GET['id']) ? $packing : null, 'id=pb size=50') ?> <?= form_hidden('id_pb') ?>
            <tr><td></td><td>
            <tr><td></td><td>
            <?= form_submit(null, 'Cari', 'id=search') ?>
            <?= form_button('Reset', 'Reset', 'id=reset') ?>
            <?= form_button('updatemargin', 'Update Margin', 'id=update') ?>
            <?= form_close() ?>
        </table>
    </div>
    <div id="result">

    </div>

</div>