<?= $this->load->view('message') ?>
<script type="text/javascript">
function set_harga_jual(i) {
    
    var hna = currencyToNumber($('#hna'+i).html());
    var margin = parseInt($('#margin'+i).val())/100;
    var diskon = parseInt($('#diskon'+i).val())/100;
    var harga_jual = (hna+(hna*margin)) - ((hna+(hna*margin))*diskon);
    $('#hj'+i).html(numberToCurrency(Math.ceil(harga_jual)));
    //($data->hna+($data->hna*$data->margin/100)) - (($data->hna+($data->hna*$data->margin/100))*($data->diskon/100));
}
$(function() {
    $('#simpan').each(function(){
        $(this).replaceWith('<button type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'">' + $(this).val() + '</button>');
    });
    $('#simpan').button({
        icons: {
            secondary: 'ui-icon-circle-check'
        }
    });
    $('#resethj').each(function(){
        $(this).replaceWith('<button type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'" onclick="'+$(this).attr('onclick')+'">' + $(this).val() + '</button>');
    });
    $('#resethj').button({
        icons: {
            secondary: 'ui-icon-refresh'
        }
    });
    $('input[type=submit]').each(function(){
        $(this).replaceWith('<button type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'">' + $(this).val() + '</button>');
    });
    $('button[type=submit]').button({
        icons: {
            secondary: 'ui-icon-pencil'
        }
    });
    $('#resethj').click(function() {
        $('#form-update').fadeOut('fast');
    })
    $('#margin,#diskons').keyup(function() {
        var hna = parseInt(currencyToNumber($('#hna').val()));
        var margin = parseInt($('#margin').val());
        var diskon = parseInt($('#diskons').val());
        var hasil  = hna + (hna*(margin/100) - (hna*(diskon/100)));
        
        $('#harga').html(numberToCurrency(parseInt(hasil)));
    })
    $('#form_harga_jual_update_save').submit(function() {
        $.ajax({
            type: 'POST',
            url: $(this).attr('action'),
            data: $(this).serialize(),
            dataType: 'json',
            cache: false,
            success: function(msg) {
                if (msg.status == true) {
                    alert_edit();
                }
            }
        })
        return false;
    })
})
</script>
<div id="result_load"></div>
<div class="data-list">
<?= form_open('referensi/harga_jual_update_save', 'id=form_harga_jual_update_save') ?>
<table class="tabel form-inputan" width="100%">
        <thead>
        <tr>
            <th>Packing Barang</th>
            <th>HNA (Rp.)</th>
            <th>Margin (%)</th>
            <th>Diskon (%)</th>
            <th>Harga Jual (Rp.)</th>
        </tr>
        </thead>
        <tbody>
<?php
$jumlah = 0;
foreach ($list_data as $key => $data) {
$harga_jual = ($data->hna+($data->hna*$data->margin/100)) - (($data->hna+($data->hna*$data->margin/100))*($data->diskon/100));
?>
    <tr class="tr_rows <?= ($key%2==0)?'odd':'even' ?>">
        <td><?= form_hidden('id_pb[]', $data->barang_packing_id) ?><?= $data->barang ?> <?= $data->kekuatan ?>  <?= $data->satuan ?> <?= $data->sediaan ?> <?= $data->pabrik ?> @ <?= ($data->isi==1)?'':$data->isi ?> <?= $data->satuan_terkecil ?></td>
        <td align="right" id="hna<?= $key ?>"><?= inttocur($data->hna) ?></td>
        <td align="center"><?= form_input('margin[]', $data->margin, 'size=5 onkeyup=set_harga_jual('.$key.') id=margin'.$key) ?></td>
        <td align="center"><?= form_input('diskon[]', $data->diskon, 'size=5 id=diskon'.$key) ?></td>
        <td align="right" id="hj<?= $key ?>"><?= inttocur($harga_jual) ?></td>
    </tr>
<?php 
$jumlah++;
} 
?>
        </tbody>
</table>
<?= form_submit('submit', 'Simpan', 'id=update') ?>
<?= form_close() ?>
</div>