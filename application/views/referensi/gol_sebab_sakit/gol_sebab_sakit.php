<?php $this->load->view('message') ?>
<title><?= $title ?></title>
<script type="text/javascript">
$(function() {
    $('#tabs').tabs();
    $('#no_dtd').focus();
    $('button').button();
    get_list_data(1, null);
    $('#simpan').click(function() {
        save();
    });
    $('#hapus').click(function() {
        var id_gol = $('input[name=id_gol]').val();
        if (id_gol === '') {
            custom_message('Peringatan','Data belum ada yang terpilih !');
            return false;
        }
        else {
            var ok = confirm('Anda yakin akan menghapus data ini?');
            if (ok) {
                $.ajax({
                    url: '<?= base_url('referensi/manage_gol_sebab_sakit') ?>/delete/'+$('.noblock').html(),
                    cache: false,
                    data: 'id='+id_gol,
                    success: function(data) {
                        alert_delete();
                        $('#result').html(data);
                    }
                });
            }
        }
    });
    $('#reset').click(function() {
        $('#loaddata').load('<?= base_url('referensi/gol_sebab_sakit') ?>');
    });
    $('#cari').click(function() {
        $.ajax({
            url: '<?= base_url('referensi/manage_gol_sebab_sakit') ?>/search/',
            cache: false,
            type: 'POST',
            data: $('#save_gol_sebab_sakit').serialize(),
            success: function(data) {
                $('#result').html(data);
            }
        });
    });
});
function get_list_data(page, search) {
    $.ajax({
        url: '<?= base_url('referensi/manage_gol_sebab_sakit') ?>/list/'+page,
        data: 'search='+search,
        cache: false,
        success: function(data) {
            $('#result').html(data);
        }
    });
}

function paging(page, tab, search) {
    get_list_data(page, search);
}

function reset_form() {
    $('input[name=id_gol]').val('');
    $('#no_dtd').val('');
    $('#no_daftar').val('');
    $('#icdx').val('');
    $('#nama').val('');
}

function save() {
    var cek_id = $('input[name=id_gol]').val();
    if (cek_id === '') {
        var url = '<?= base_url('referensi/manage_gol_sebab_sakit/add') ?>';
    } else {
        var url = '<?= base_url('referensi/manage_gol_sebab_sakit/edit') ?>';
    }
    $.ajax({
        url: url,
        data: $('#save_gol_sebab_sakit').serialize(),
        cache: false,
        type: 'POST',
        success: function(data) {
            if (cek_id === '') {
                $('input[type=text]').attr('disabled','disabled');
                $('#simpan').hide();
                alert_tambah();
            } else {
                reset_form();
                alert_edit();
            }
            $('#result').html(data);
        }
    });
}


</script>
<div class="titling"><h1><?= $title ?></h1></div>
<div class="kegiatan">
    
    <div id="tabs">
        <ul>
            <li><a href="#tabs-1">Daftar Diagnosa</a></li>
        </ul>
        <div id="tabs-1">
        <?= form_open('', 'id=save_gol_sebab_sakit') ?>
        <table class="inputan" widht="100%">
            <tr><td>ID.:</td><td><?= get_last_id('golongan_sebab_sakit', 'id') ?><?= form_hidden('id_gol', NULL) ?></td></tr>
            <tr><td>No. DTD:</td><td><?= form_input('no_dtd', NULL, 'id=no_dtd size=40') ?></td></tr>
            <tr><td>Kode ICD X:</td><td><?= form_input('no_daftar', NULL, 'id=no_daftar size=40') ?></td></tr>
            <tr><td>Nama:</td><td><?= form_input('nama', NULL, 'id=nama size=40') ?></td></tr>
            <tr><td></td><td><?= form_button(null, 'Simpan', 'id=simpan') ?><?= form_button(null, 'Cari', 'id=cari') ?><?= form_button(null, 'Hapus', 'id=hapus') ?><?= form_button(null, 'Reset', 'id=reset') ?></td></tr>
        </table>
         <?= form_close() ?>
    <div id="result">
        
    </div>
        </div>
</div>
</div>