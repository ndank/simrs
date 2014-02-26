<?php $this->load->view('message'); ?>
<script type="text/javascript">
function loading() {
    var url = '<?= base_url('billing/pp_uang') ?>';
    $('#loaddata').load(url);
}
function add(i) {
     str = '<tr class=tr_row>'+
                '<td><input type=text name=nama[] id=nama'+i+' class=nama style="width:100%" /></td>'+
                '<td><input type=text name=jml[] id=jml'+i+' class=jml style="width:100%" /></td>'+
                '<td class=aksi><a class=delete onclick=eliminate(this)></a></td>'+
            '</tr>';
        
    $('.form-inputan tbody').append(str);
    $('#tgl'+i).datepicker({
        changeMonth: true,
        changeYear: true
    })
    $('.jml').keyup(function() {
        FormNum(this);
        total();
    })
}
function eliminate(el) {
    var parent = el.parentNode.parentNode;
    parent.parentNode.removeChild(parent);
}

function total() {
    var jml = $('.tr_row').length-1;
    var total = 0;
    for (i = 0; i <= jml; i++) {
        var nominal = currencyToNumber($('#jml'+i).val());
        if (!isNaN(nominal)) {
            var total = total + nominal;
        }
    }
    $('#totallica').html(numberToCurrency(total));
}

$(function() {
    $('button[id=deletion]').hide();
    $('#awal').datetimepicker({
        changeYear: true,
        changeMonth: true
    })
    $('#reset').click(function() {
        var url = '<?= base_url('billing/pp_uang') ?>';
        $('#loaddata').load(url);
    })
    $('button[id=addnewrow]').button({
        icons: {
            secondary: 'ui-icon-circle-plus'
        }
    });
    $('button[id=deletion]').button({
        icons: {
            secondary: 'ui-icon-circle-close'
        }
    });
    $('button[id=reset]').button({
        icons: {
            secondary: 'ui-icon-refresh'
        }
    });
    $('input[type=submit]').each(function(){
        $(this).replaceWith('<button type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'">' + $(this).val() + '</button>');
    });
    $('button[type=submit]').button({
        icons: {
            secondary: 'ui-icon-circle-check'
        }
    });
    for (x = 0; x <= 1; x++) {
            add(x);
    }

    $('#addnewrow').click(function() {
            row = $('.tr_row').length+1;
    add(row);
    i++;
    })
    $('.tgl').datepicker({
            changeYear: true,
            changeMonth: true
    })
    $('#deletion').click(function() {
        var ok=confirm('Anda yakin akan menghapus transaksi ini ?');
        if (ok) {
            var id = $('input[name=id_pp_uang]').val();
            $.get('<?= base_url('billing/pp_uang_delete') ?>/'+id, function(data) {
                if (data.status == true) {
                    alert_delete();
                    loading();
                }
            },'json');
        } else {
            return false;
        }
    })
    $('#form_pp_uang').submit(function() {
        if ($('#nodoc').val() == '') {
            custom_message('Peringatan','Nomor dokumen tidak boleh kosong !');
            $('#nodoc').focus();
            return false;
        }
        var url = $(this).attr('action');
        $.ajax({
            type: 'POST',
            url: url,
            data: $(this).serialize(),
            dataType: 'json',
            success: function(data) {
                if (data.status == true) {
                    $('#submit').hide();
                    $('input[name=id_pp_uang]').val(data.id_pp_uang);
                    $('button[id=deletion]').show();
                    alert_tambah();
                }
            }
        })
        return false;
    })
    
})
</script>
<title><?= $title ?></title>
<div class="kegiatan">
    <div class="titling"><h1><?= $title ?></h1></div>
    <?= form_hidden('id_pp_uang') ?>
    <?= form_open('billing/pp_uang_save', 'id=form_pp_uang') ?>
    <div class="data-input">
        <table width="100%" class="inputan">Summary</legend>
            <tr><td>No. Dokumen</td><td> <?= form_input('nodoc',NULL,'id=nodoc size=30') ?>
            <tr><td>Tanggal</td><td><?= form_input('tanggal',date("d/m/Y H:i"),'id=awal size=15') ?>
            <tr><td>Jenis Transaksi</td><td>
            <span class="label"> <?= form_radio('jenis', 'Penerimaan', TRUE) ?> Penerimaan </span>
            <span class="label"> <?= form_radio('jenis', 'Pengeluaran', FALSE) ?>Pengeluaran </span>
            <?php
            if(!isset($_GET['id'])) { ?>
            <tr><td></td><td><?= form_button(null, 'Tambah Baris','id=addnewrow') ?>
            <?php } ?>
        </table>
    </div>
	<div class="data-list">
        <table class="tabel form-inputan" width="100%">
            <thead>
            <tr>
                <th width="70%">Nama Transaksi</th>
                <th width="20%">Jumlah (Rp.)</th>
                <th width="10%">#</th>
            </tr>
            </thead>
            <tbody>
                <?php
                if (isset($_GET['id'])) {
                    $total = 0;
                    foreach ($ppuang as $key => $data) { ?>
                        <tr class="tr_row <?= ($key%2==1)?'even':'odd' ?>">
                            <td><?= $data->penerimaan_pengeluaran_nama ?></td>
                            <td align="right"><?= ($data->jenis == 'Penerimaan')?rupiah($data->penerimaan):rupiah($data->pengeluaran) ?></td>
                            <td class=aksi>#</td>
                        </tr>
                    <?php 
                    if ($data->jenis == 'Penerimaan') {
                        $jml = $data->penerimaan;
                    } else {
                        $jml = $data->pengeluaran;
                    }
                    $total = $total+$jml;
                    }
                }
                ?>
            </tbody>
            <tfoot>
                <tr class="odd">
                    <td align="right">Total</td>
                    <td id="totallica" align="right"></td>
                    <td></td>
                </tr>
            </tfoot>
        </table><br/>
        <?= form_submit('submit','Simpan','class=button id=submit') ?>
        <?= form_button('Reset', 'Reset', 'id=reset') ?> 
        <?= form_button('delete', 'Delete', 'id=deletion') ?>
         
	</div>
	<?= form_close(); ?>
</div>