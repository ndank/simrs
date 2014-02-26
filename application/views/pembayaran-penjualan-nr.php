<?php
header('Cache-Control: max-age=0');
?>
<title><?= $title ?></title>
<div id="result_cetak" style="display: none"></div>
<div class="kegiatan">
<?php $this->load->view('message'); ?>
<script type="text/javascript">
$(function() {
    $('#nopenjualan').focus();
    $('#resetnr').button({
        icons: {
            secondary: 'ui-icon-refresh'
        }
    }).click(function() {
        $('#nonresep').load('<?= base_url('pelayanan/pembayaran_penjualan_nr') ?>');
    });
    $('#save').button({
        icons: {
            secondary: 'ui-icon-circle-check'
        }
    }).click(function() {
        $("#form_byr_penjualan_non_resep").submit();
    });
    $('#print').button({
        icons: {
            secondary: 'ui-icon-print'
        }
    }).click(function() {
        var id = $('#nopenjualan').val();
        var wWidth = $(window).width();
        var dWidth = wWidth * 1;
        var wHeight= $(window).height();
        var dHeight= wHeight * 1;
        var x = screen.width/2 - dWidth/2;
        var y = screen.height/2 - dHeight/2;
        window.open('<?= base_url('pelayanan/penjualan_cetak_nota') ?>/'+id+'/nonresep','Myprint','width='+dWidth+', height='+dHeight+', left='+x+',top='+y);
    });
    var lebar = $('#nopenjualan').width();
    $('#nopenjualan').autocomplete("<?= base_url('pelayanan/get_no_penjualan_bebas') ?>",
    {
        parse: function(data){
            var parsed = [];
            for (var i=0; i < data.length; i++) {
                parsed[i] = {
                    data: data[i],
                    value: data[i].id // nama field yang dicari
                };
            }
            $('#id_resep').val('');
            return parsed;
            
        },
        formatItem: function(data,i,max){
            if (data.id !== null) {
                var str = '<div class=result>'+data.id+' <br/> '+datetimefmysql(data.waktu)+'</div>';
            }
            return str;
        },
        width: lebar, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
        dataType: 'json', // tipe data yang diterima oleh library ini disetup sebagai JSON
        cacheLength: 0
    }).result(
    function(event,data,formated){
        $(this).val(data.id);
        $('#ppn_ppnr').html(data.ppn);
        $('#total_harga_barang').html(numberToCurrency(data.total));
        $('#tanggal').val(datetimefmysql(data.waktu));
        var disc_bank = $('#disc_bank').html();
        var disc  = (data.total*(disc_bank/100));
        var hasil = parseInt(data.total) - disc;
        $('#total-tagihan').html(numberToCurrency(Math.ceil(hasil)));
        $('input[name=bulat]').val(numberToCurrency(pembulatan_seratus(hasil)));
        $('input[name=total]').val(numberToCurrency(pembulatan_seratus(hasil)));
        $('#bulat').html(numberToCurrency(pembulatan_seratus(hasil)));
    });
    
    $('#pembayaran').change(function() {
        var id = $('#pembayaran').val();
        $.ajax({
            url: '<?= base_url('inv_autocomplete/get_diskon_instansi_relasi') ?>/'+id,
            cache: false,
            dataType: 'json',
            success: function(msg) {
                //custom_message('Peringatan',msg.diskon_penjualan)
                $('#disc_bank').html((msg.diskon_penjualan === null)?'0':msg.diskon_penjualan);
                $('#diskon_bank').val((msg.diskon_penjualan === null)?'0':msg.diskon_penjualan);
                //subTotal()
                var disc_bank = $('#disc_bank').html()/100;
                var total_hb  = parseInt(currencyToNumber($('#total_harga_barang').html()));
                var ppn       = $('#ppn_ppnr').html()/100;
                var hasil = (total_hb+(total_hb*(ppn/100))) - (disc_bank*total_hb);
                
            $('#total-tagihan').html(numberToCurrency(parseInt(hasil)));
            $('#bulat').html(numberToCurrency(parseInt(pembulatan_seratus(hasil))));
            $('input[name=bulat]').val(numberToCurrency(pembulatan_seratus(parseInt(hasil))));
            }
        });
    });
});

function total_this() {
    
}

function cetak_kitir() {
    var jumlah = $('.tr_row').length-1;
    $('#loaddatapenjualan tbody').html('');
    var total  = 0;
    var ppn    = $('#ppn').val();
    for (i = 0; i <= jumlah; i++) {
        if ($('#subtotal'+i).html() !== '') {
            str ='<tr>'+
                    '<td>'+$('#pb'+i).val()+'</td>'+
                    '<td align="right">'+$('#hj'+i).html()+'</td>'+
                    '<td align="center">'+$('#diskon'+i).html()+'</td>'+
                    '<td align="center">'+$('#jl'+i).val()+'</td>'+
                    '<td align="right">'+$('#subtotal'+i).html()+'</td>'+
                 '</tr>';
            var total = total + currencyToNumber($('#subtotal'+i).html());
            $('#loaddatapenjualan tbody').append(str);
         }
    }
    $('#ppn_kitir').html(ppn);
    var ppn_hasil = (ppn/100)*total;
    $('#total_barang').html(numberToCurrency(total));
    $('#total_tagihan').html(numberToCurrency(total+ppn_hasil));
    $('#cetak_kitir').dialog({
        autoOpen: true,
        modal: true,
        width: 700,
        height: 400
    });
}

function pembulatan_seratus(angka) {
    var kelipatan = 100;
    var sisa = angka % kelipatan;
    if (sisa !== 0) {
        var kekurangan = kelipatan - sisa;
        var hasilBulat = angka + kekurangan;
        return Math.ceil(hasilBulat);
    } else {
        return Math.ceil(angka);
    }
    
    
}
function subTotal() {
    
        var jumlah = $('.tr_row').length-1;
        var tagihan = 0;
        var disc = 0;
        //custom_message('Peringatan',jumlah)
        var jasa_apt = 0;
        var ppn = $('#ppn').val()/100;
        for (i = 0; i <= jumlah; i++) {
            
            var harga = currencyToNumber($('#hj'+i).html());
            var diskon= parseInt($('#diskon'+i).html())/100;
            
            <?php 
            if (isset($_GET['id'])) { ?>
            var jml= parseInt($('#jl'+i).html());                
            <?php } else { ?>
            var jml= parseInt($('#jl'+i).val());
            <?php } ?>
            var subtotal = numberToCurrency(Math.ceil((harga - (harga*diskon))*jml));
            //custom_message('Peringatan',subtotal);
            $('#subtotal'+i).html(subtotal);
            $('#subttl'+i).val(subtotal);
            //custom_message('Peringatan',harga)
            //custom_message('Peringatan',disc)
            
            var harga = parseInt(currencyToNumber($('#hj'+i).html()));
            var diskon= parseInt($('#diskon'+i).html())/100;
            //var jumlah= parseInt($('#jl'+i).val());
            var subtotall = 0;
            //custom_message('Peringatan',harga); custom_message('Peringatan',diskon); custom_message('Peringatan',jumlah);
            if (!isNaN(harga) && !isNaN(diskon) && !isNaN(jml)) {
                if (parseInt($('#subttl'+i).val()) !== '') {
                    //var subtotall = parseInt($('#subttl'+i).val());
                    var subtotall = harga*jml;
                }
                var disc = disc + ((diskon*harga)*jml);
                var tagihan = tagihan + subtotall;
            }
            
        }
        
        //$('#jasa_total_apotek').val(ja);
        $('#total-diskon').html(numberToCurrency(Math.ceil(disc)));
        $('#total-tagihan').html(numberToCurrency(tagihan));
        var totallica = (tagihan - disc)+jasa_apt;
        var diskon_bank   = (totallica * ($('#disc_bank').html()/100));
        var pajak = ppn*tagihan;
        var new_totallica = (totallica - diskon_bank)+pajak;
        $('#total').html(numberToCurrency(Math.ceil(new_totallica)));
        if (tagihan !== 0) {
            $('input[name=bulat]').val(numberToCurrency(pembulatan_seratus(hasil)));
            $('#bulat').html(numberToCurrency(pembulatan_seratus(hasil)));
        }
}
function setKembali() {
    //var apoteker = currencyToNumber($('#jasa-apt').html());
    var total = currencyToNumber($('#total-tagihan').html());
    var bayar = currencyToNumber($('#bayar_penjualan').val());
    var bulat = currencyToNumber($('#bulat').html());
    var kembali = bayar - bulat;
    if (isNaN(bayar)) {var kembali = 0;} else {var kembali = kembali;}
    //$('#bulat').val(numberToCurrency(total));
    if (kembali < 0) {
        var kembali = kembali;
    } else {
        var kembali = numberToCurrency(kembali);
    }
    $('#kembalian_pr').html(kembali);
    $('input[name=total]').val(total);
}
$(function() {
    date_time('tanggal');
    $('#bulat').focus(function() {
        var kembalian = $('#kembalian_pr').html();
        $('#kembalian_pr').html(numberToCurrency(kembalian));
        var jumlah = $('.tr_row').length;
        for (i = 1; i <= jumlah; i++) {
            subTotal(i);
        }
    });
    $('#bayar_penjualan').focus(function() {
        var jumlah = $('.tr_row').length;
        for (i = 1; i <= jumlah; i++) {
            subTotal(i);
        }
    });
    $('input:text').live("keydown", function(e) {
        var n = $("input:text").length;
        if (e.keyCode === 13) {
            var nextIndex = $('input:text').index(this) + 1;
            if (nextIndex < n) {
                $('input:text')[nextIndex].focus();
            } else {
                $('#save').focus();
            }
        }
    });
    $("#form_byr_penjualan_non_resep").submit(function() {
        $("<div title='Konfirmasi Simpan'>Anda yakin akan menyimpan data transaksi ini ?</div>").dialog({
                modal: true,
                autoOpen: true,
                width: 320,
                buttons: { 
                    "Ya": function() { 
                        $(this).dialog('close');
                        if ($('#nopenjualan').val() === '') {
                            custom_message('Peringatan','Nomor penjualan tidak boleh kosong !','#nopenjualan');
                            return false;
                        }
                        if ($('#bayar_penjualan').val() === '') {
                            custom_message('Peringatan','Jumlah bayar tidak boleh kosong !','#bayar_penjualan');
                            return false;
                        }
                        var post = $("#form_byr_penjualan_non_resep").attr('action');
                        $.ajax({
                            type: 'POST',
                            url: post,
                            dataType: 'json',
                            data: $("#form_byr_penjualan_non_resep").serialize(),
                            success: function(data) {
                                if (data.status === true) {
                                    $('#save').hide();
                                    $('#print').show();
                                    $('#id_penjualan').html(data.id_penjualan);
                                    $('button[type=submit]').hide();
                                    alert_tambah();
                                } else {

                                }
                            }
                        });
                        },
                    "Tidak": function() {
                        $(this).dialog('close');
                        return false;
                    }
                }, close: function() {
                    $(this).dialog('close');
                    return false;
                }
          });
        return false;
    });
});
</script>
    <?= form_open('pelayanan/pembayaran_penjualan_nr_save', 'id=form_byr_penjualan_non_resep') ?>
    <?= form_hidden('total') ?>
        <table width="100%" class="inputan">
            <tr><td width="20%">Tanggal:</td><td><?= indo_tgl(date("Y-m-d")) ?></td></tr>
            <tr><td>No. Penjualan:</td><td> <?= form_input('nopenjualan', NULL, 'id=nopenjualan size=40') ?></td></tr>
            <tr><td>PPN (%):</td><td id="ppn_ppnr"></td></tr>
            <tr><td>Total Harga Barang:</td><td id="total_harga_barang"><?= isset($data['subtotal'])?rupiah($data['subtotal']):null ?></td></tr>
            <tr><td>Pembayaran Bank:</td><td><?= form_dropdown('cara_bayar', $list_bank, NULL, 'id=pembayaran') ?></td></tr>
            <tr><td>Diskon Bank (%):</td><td><span class="label" id="disc_bank"></span><?= isset($data['diskon_bank'])?$data['diskon_bank']:'0' ?><?= form_hidden('diskon_bank', isset($data['diskon_bank'])?$data['diskon_bank']:'0', 'id=diskon_bank size=10 ') ?></td></tr>
            <tr><td>Total Tagihan:</td><td id="total-tagihan"><?= isset($data['total'])?rupiah($data['total']):null ?></td></tr>
            <tr><td>Pembulatan Total</td><td><span class="label" id="bulat"></span><?= form_hidden('bulat', isset($data['total'])?rupiah($data['total']):NULL) ?></td></tr>
            <tr><td>Uang diserahkan (Rp)</td><td><?= form_input('bayar', isset($data['bayar'])?rupiah($data['bayar']):null, 'id=bayar_penjualan onblur="setKembali();" size=40 onkeyup="FormNum(this)"') ?></td></tr>
            <tr><td>Kembalian (Rp)</td><td id="kembalian_pr"><?= rupiah(isset($kembali)?$kembali:null) ?></td></tr>
            <tr><td></td><td>
            <?= form_button('save', 'Simpan', 'id=save') ?>
            <?= form_button('Reset', 'Reset', 'id=resetnr') ?>
            <?= form_button(null, 'Cetak Nota', 'id=print') ?>
            </td></tr>
        </table>
    <?= form_close() ?>
</div>