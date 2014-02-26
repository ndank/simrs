<?= $this->load->view('message') ?>
<script type="text/javascript">
function set_harga_jual(i) {
    
    var hna = currencyToNumber($('#hna'+i).html());
    var margin = parseInt($('#margin'+i).val())/100;
    var diskon = parseInt($('#diskon'+i).val())/100;
    var harga_jual = (hna+(hna*margin)) - ((hna+(hna*margin))*diskon);
    var margin_rp  = margin*hna;
    $('#margin_rp'+i).val(numberToCurrency(margin_rp));
    $('#harga_jual'+i).val(numberToCurrency(Math.ceil(harga_jual)));
    //($data->hna+($data->hna*$data->margin/100)) - (($data->hna+($data->hna*$data->margin/100))*($data->diskon/100));
}
function set_margin_persen(i) {
    
    var hna = currencyToNumber($('#hna'+i).html());
    var margin = currencyToNumber($('#margin_rp'+i).val());
    var hsl = (margin/hna)*100;
    if (isNaN(hsl)) {
        var hsl = '0';
    }
    $('#margin'+i).val(Math.floor(hsl));
}

function set_diskon_persen(i) {
    var hna = currencyToNumber($('#hna'+i).html());
    var margin = currencyToNumber($('#margin_rp'+i).val());
    var diskon_rp =  currencyToNumber($('#diskon_rp'+i).val());
    var hsl = (diskon_rp/(hna+margin))*100;
    if (isNaN(hsl)) {
        var hsl = '0';
    }
    $('#diskon'+i).val(Math.floor(hsl));
}

function set_diskon_rupiah(i) {
    var hna = currencyToNumber($('#hna'+i).html());
    var margin_rp = currencyToNumber($('#margin_rp'+i).val());
    var diskon =  currencyToNumber($('#diskon'+i).val());
    var hsl = (diskon/100)*(hna+margin_rp);
    if (isNaN(hsl)) {
        var hsl = '0';
    }
    $('#diskon_rp'+i).val(numberToCurrency(Math.floor(hsl)));
}

function set_ppn_rupiah(i) {
    var hna = currencyToNumber($('#hna'+i).html());
    var margin_rp = currencyToNumber($('#margin_rp'+i).val());
    var diskon_rp = currencyToNumber($('#diskon_rp'+i).val());
    var ppn_jual  = currencyToNumber($('#ppn_jual'+i).val());
    var hsl = ((hna+margin_rp)-diskon_rp)*(ppn_jual/100);
    if (isNaN(hsl)) {
        var hsl = '0';
    }
    $('#ppn_jual_rp'+i).val(numberToCurrency(parseInt(hsl)));
}

function set_ppn_persen(i) {
    var hna = parseInt(currencyToNumber($('#hna'+i).html()));
    var margin_rp = parseInt(currencyToNumber($('#margin_rp'+i).val()));
    var diskon_rp = parseInt(currencyToNumber($('#diskon_rp'+i).val()));
    var ppn_jual_rp  = parseInt(currencyToNumber($('#ppn_jual_rp'+i).val()));
    var hsl = (ppn_jual_rp/((hna+margin_rp)-diskon_rp))*100;
    //custom_message('Peringatan',hna+' - '+margin_rp+' - '+diskon_rp+' - '+ppn_jual_rp);
    
    if (isNaN(hsl)) {
        var hsl = '0';
    }
    $('#ppn_jual'+i).val(numberToCurrency(Math.ceil(hsl)));
}

$(function() {
    $('#checkall').click(function() {
        var status = ($(this).is(':checked') === true);
        if (status === true) {
            $('.check').attr('checked', 'checked');
        } else {
            $('.check').removeAttr('checked');
        }
    });
    $('#form_harga_jual2').submit(function() {
        var status = ($('.check').is(':checked') === true);
        
        if (status === true) {
            $.ajax({
                type: 'POST',
                url: $(this).attr('action'),
                data: $(this).serialize(),
                success: function(data) {
                    $('#result_load').html(data);
                    $('#result_load').dialog({
                        autoOpen: true,
                        modal: true,
                        width: 700,
                        height: 400,
                        close: function() {
                            $('#result_load').dialog().remove();
                            var id_pb= $('#pb').val();
                            $.ajax({
                                url: '<?= base_url('referensi/harga_jual_load') ?>',
                                data: 'pb='+id_pb,
                                cache: false,
                                success: function(msg) {
                                    $('#result').html(msg);
                                }
                            })
                        }
                    })
                }
            })
        } else {
            custom_message('Peringatan','Barang belum ada yang dipilih !');
        }
        return false;
    });
    $('#resethj').each(function(){
        $(this).replaceWith('<button type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'" onclick="'+$(this).attr('onclick')+'">' + $(this).val() + '</button>');
    });
    $('#resethj').button({
        icons: {
            secondary: 'ui-icon-refresh'
        }
    });
    $('#saving').button({
        icons: {
            secondary: 'ui-icon-circle-check'
        }
    });
    $('#saving').click(function() {
        $('#form_harga_jual_update_save').submit();
    });
    $('#form_harga_jual_update_save').submit(function() {
        $.ajax({
            type: 'POST',
            url: $(this).attr('action'),
            data: $(this).serialize(),
            dataType: 'json',
            cache: false,
            success: function(msg) {
                if (msg.status == true) {
                    $('#form_harga_jual').submit();
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
            <th width="3%">No.</th>
            <th width="5%">Tanggal</th>
            <th width="30%">Packing Barang</th>
            <th width="5%">HNA <br/>(Rp.)</th>
            <th width="5%">Margin <br/> (%)</th>
            <th width="5%">Margin <br/> (Rp.)</th>
            <th width="5%">Diskon <br/> (%)</th>
            <th width="5%">Diskon <br/> (Rp.)</th>
            <th width="5%">Harga Jual <br/> (Rp.)</th>
            <th width="5%">PPN Jual <br/> (%)</th>
            <th width="5%" style="padding-left:3px;">PPN Jual <br/> (Rp.)</th>
            <th width="1%"></th>
        </tr>
        </thead>
        <tbody>
<?php
$jumlah = 0;
foreach ($list_data as $key => $data) {
$harga_jual_origin = ($data->hna+($data->hna*$data->margin/100)) - (($data->hna+($data->hna*$data->margin/100))*($data->diskon/100));
$harga_jual = $harga_jual_origin+($harga_jual_origin*($data->ppn_jual/100));
$margin_rp = $data->hna*($data->margin/100);
$diskon_rp = ($data->hna+$margin_rp)*($data->diskon/100);
$ppn_jual_rp = round($harga_jual_origin*($data->ppn_jual/100),0);
?>
    <tr class="<?= ($key%2==0)?'odd':'even' ?>">
        <td align="center"><?= ++$key ?></td>
        <td align="center"><?= datefmysql($data->tanggal) ?></td>
        <td style="white-space: nowrap;"><?= form_hidden('id_pb[]', $data->barang_packing_id) ?><?= $data->barang ?> <?= $data->kekuatan ?>  <?= $data->satuan ?> <?= $data->sediaan ?> <?= $data->pabrik ?> @ <?= ($data->isi==1)?'':$data->isi ?> <?= $data->satuan_terkecil ?></td>
        <td align="right" id="hna<?= $key ?>"><?= inttocur($data->hna) ?></td>
        <td align="center"><?= form_input('margin[]',$data->margin,'size=10 onkeyup=set_harga_jual('.$key.') id=margin'.$key) ?></td>
        <td align="center"><?= form_input('margin_rp[]',rupiah($margin_rp),'size=10 onblur=set_margin_persen('.$key.') style="text-align: right;" onkeyup=FormNum(this) id=margin_rp'.$key) ?></td>
        <td align="center"><?= form_input('diskon[]',$data->diskon,'size=10 onblur=set_diskon_rupiah('.$key.') id=diskon'.$key) ?></td>
        <td align="center"><?= form_input('diskon_rp[]',rupiah($diskon_rp),'size=10 onblur=set_diskon_persen('.$key.') style="text-align: right;" id=diskon_rp'.$key) ?></td>
        <td align="right"><?= form_input('harga_jual[]',inttocur($harga_jual),'readonly size=10 onblur=FormNum(this) style="text-align: right;" onkeyup=set_margin('.$key.') id=harga_jual'.$key.'') ?></td>
        <td align="center"><?= form_input('ppn_jual[]',$data->ppn_jual,'size=10 onblur=set_ppn_rupiah('.$key.') id=ppn_jual'.$key) ?></td>
        <td align="center"><?= form_input('ppn_jual_rp[]',rupiah($ppn_jual_rp),'size=10 onblur=set_ppn_persen('.$key.') style="text-align: right;" onkeyup=FormNum(this) id=ppn_jual_rp'.$key) ?></td>
        <td></td>
    </tr>
<?php 
$jumlah++;
} 
?>
        </tbody>
</table>
<br/>
<?= form_button('submit', 'Simpan', 'id=saving') ?>
<?= form_close() ?>
</div>