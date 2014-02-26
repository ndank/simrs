<?php
header('Cache-Control: max-age=0');
?>
<title><?= $title ?></title>
<div id="result_cetak" style="display: none"></div>
<div class="kegiatan">
<?php $this->load->view('message'); ?>

<script type="text/javascript">
    function PrintElem(elem) {
        $('#cetak').hide();
        Popup($(elem).printElement());
        
    }

    function Popup(data) {
        //var mywindow = window.open('<?= $title ?>', 'Print', 'height=400,width=800');
        mywindow.document.write('<html><head><title> <?= $title ?> </title>');
        /*optional stylesheet*/ //mywindow.document.write('<link rel="stylesheet" href="main.css" type="text/css" />');
        mywindow.document.write('</head><body >');
        mywindow.document.write(data);
        mywindow.document.write('</body></html>');

        mywindow.print();
        setTimeout(function(){ window.close();},300);
        $('#cetak').show();
        return true;
    }

</script>
<script type="text/javascript">
$(function() {
    $(document).unbind().on("keydown", function (e) {
        if (e.keyCode === 119) {
            e.preventDefault();
            $('#addnewrow').click();
        }
    });
    $('#reset').button({
        icons: {
            secondary: 'ui-icon-refresh'
        }
    });
    $('#print').button({
        icons: {
            secondary: 'ui-icon-print'
        }
    }).click(function() {
        cetak_kitir();
    });
    $('#retur').button({
        icons: {
            secondary: 'ui-icon-transferthick-e-w'
        }
    });
    $('#addnewrow').button({icons: {secondary: 'ui-icon-circle-plus'}});
    $('#save').button({icons: {secondary: 'ui-icon-circle-check'}}).click(function() {
        $("#form_penjualan_non_resep").submit();
    });
    $('#deletion').button({icons: {secondary: 'ui-icon-circle-close'}});
    $('#noresep').focus();
    $('#bayar').keyup(function() {
        FormNum(this);
        setKembali();
    });
    $('#noresep').autocomplete("<?= base_url('common/autocomplete?opsi=noresep') ?>",
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
                var str = '<div class=result>'+data.id+' - '+data.pasien+'<br/>Dokter: '+data.dokter+'</div>';
            }
            return str;
        },
        width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
        dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
    }).result(
    function(event,data,formated){
        $(this).val(data.id);
        $('#display-apt').show();
        $('#id_resep').val(data.id);
        $('#pasien').html(data.pasien);
        $('#id_pasien').val(data.pasien_penduduk_id);
        $('#jasa-apt').html(numberToCurrency(data.jasa_apoteker));
        var id = data.id;
        $.ajax({
            url: '<?= base_url('inventory/penjualan-table') ?>',
            data: 'id='+id,
            cache: false,
            success: function(msg) {
                $('.form-inputan tbody').html(msg);
                $('#total-tagihan').html($('#tagihan').val());
            }
        });
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
                subTotal();
            }
        });
    });
});
$(function() {
    <?php if (!isset($list_data)) { ?>
    for(x = 0; x <= 1; x++) {
        add(x);
    }
    <?php } ?>
    $('#pb0').focus();
    $('#addnewrow').click(function() {
        row = $('.tr_row').length;
        
        add(row);
        i++;
    });
    
});

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
    //var ppn_hasil = (ppn/100)*total;
    $('#total_barang').html(numberToCurrency(Math.ceil(total)));
    $('#total_tagihan').html(numberToCurrency(Math.ceil(total)));
    $('#cetak_kitir').dialog({
        autoOpen: true,
        modal: true,
        width: 700,
        height: 400,
        open: function() {
            $(this).dialog('close');
            PrintElem('#cetak_kitir');
        }
    });
}

function eliminate(el) {
    var parent = el.parentNode.parentNode;
    parent.parentNode.removeChild(parent);
    var jumlah = $('.tr_row').length-1;
    
    for (i = 0; i <= jumlah; i++) {
        $('.tr_row:eq('+i+')').children('td:eq(0)').children('.bc').attr('id','bc'+i);
        $('.tr_row:eq('+i+')').children('td:eq(1)').children('.pb').attr('id','pb'+i);
        $('.tr_row:eq('+i+')').children('td:eq(1)').children('.id_pb').attr('id','id_pb'+i);
        $('.tr_row:eq('+i+')').children('td:eq(2).ed').attr('id','ed'+i);
        $('.tr_row:eq('+i+')').children('td:eq(3).hj').attr('id','hj'+i);
        $('.tr_row:eq('+i+')').children('td:eq(4).diskon').attr('id','diskon'+i);
        $('.tr_row:eq('+i+')').children('td:eq(5).ppn').attr('id','ppn'+i);
        $('.tr_row:eq('+i+')').children('td:eq(6)').children('.ed').attr('id','exp'+i);
        $('.tr_row:eq('+i+')').children('td:eq(6)').children('.jl').attr('id','jl'+i);
        $('.tr_row:eq('+i+')').children('td:eq(7)').attr('id','subtotal'+i);
//        if ($('.tr_row:eq('+i+')').children('td:eq(1)').children('.id_pb').val() === '') {
//            $('.tr_row:eq('+i+')').remove();
//        }
    }
    subTotal();
    
}
function add(i) {
     str = '<tr class=tr_row>'+
                '<td><input type=text name=nr[] id=bc'+i+' class=bc size=10 /></td>'+
                '<td><input type=text name=dr[] id=pb'+i+' class=pb size=60 /><input type=hidden name=id_pb[] id=id_pb'+i+' class=id_pb />'+
                '<input type="hidden" name="id_kategori[]" id="id_kategori'+i+'" class="id_kategori" /></td>'+
                '<td id=ed'+i+' class=ed align=center></td>'+
                '<td id=hj'+i+' class=hj align=right></td>'+
                '<td align=center class=diskon id=diskon'+i+'></td>'+
//                '<td align=center class=ppn id=ppn'+i+'></td>'+
                '<td><input type=hidden name=ed[] id=exp'+i+' /> <input type=text name=jl[] id=jl'+i+' class=jl size=20 style="width: 100px;" onKeyup=subTotal() /><input type=hidden name=subtotal[] id=subttl'+i+' class=subttl /></td>'+
                '<td id=subtotal'+i+' align=right></td>'+
                '<td class=aksi><a class=delete onclick=eliminate(this)></a><input type=hidden name=disc[] id=disc'+i+' />'+
                '<input type=hidden name=harga_jual[] id=harga_jual'+i+' /></td>'+
            '</tr>';

    $('.form-inputan tbody').append(str);
    $('#bc'+i).live('keydown', function(e) {
        if (e.keyCode===13) {
            var bc = $('#bc'+i).val();
            if (bc !== '') {
                $.ajax({
                    url: '<?= base_url('inv_autocomplete/get_penjualan_field') ?>/'+bc,
                    cache: false,
                    dataType: 'json',
                    success: function(data) {
                        if (data.nama !== null) {
                            var isi = ''; var satuan = ''; var sediaan = ''; var pabrik = ''; var satuan_terkecil = ''; var kekuatan = ''; var kemasan = '';
                            if (data.isi !== '1') { var isi = '@ '+data.isi; }
                            if (data.kekuatan !== null && data.kekuatan !== '0') { var kekuatan = data.kekuatan; }
                            if (data.satuan !== null) { var satuan = data.satuan; }
                            if (data.sediaan !== null) { var sediaan = data.sediaan; }
                            if (data.pabrik !== null) { var pabrik = data.pabrik; }
                            if (data.satuan_terkecil !== null) { var satuan_terkecil = data.satuan_terkecil; }
                            if ((data.satuan_terbesar !== null) && (data.satuan_terbesar !== data.satuan_terkecil)) { kemasan = data.satuan_terbesar; }
                            if (data.id_obat === null) {
                                $('#pb'+i).val(data.nama+' '+pabrik+' '+kemasan+' '+isi+' '+satuan_terkecil);
                            } else {
                                if (data.generik === 'Non Generik') {
                                    $('#pb'+i).val(data.nama+' '+((kekuatan === '1')?'':kekuatan)+' '+satuan+' '+sediaan+' '+kemasan+' '+isi+' '+satuan_terkecil);
                                } else {
                                    $('#pb'+i).val(data.nama+' '+((kekuatan === '1')?'':kekuatan)+' '+satuan+' '+sediaan+' '+pabrik+' '+kemasan+' '+isi+' '+satuan_terkecil);
                                }
                            }
                            var terdiskon = (data.harga-data.diskon);
                            var harga_jual= terdiskon+(terdiskon*(data.ppn_jual/100));
                            $('#id_pb'+i).val(data.id);
                            $('#kekuatan'+i).html(data.kekuatan);
                            $('#hj'+i).html(numberToCurrency(Math.ceil(data.harga))); // text asli
                            $('#harga_jual'+i).val(harga_jual);

                            $('#disc'+i).val(data.diskon);
                            $('#ppn'+i).html(data.ppn_jual);
                            $('#ed'+i).html(datefmysql(data.ed));
                            $('#exp'+i).val(data.ed);
                            $('#diskon'+i).html(data.diskon);
                            $('#jl'+i).val('1');
                            subTotal(i);
                            var jml = $('.tr_row').length;
                            //custom_message('Peringatan',jml+' - '+i)
                            if (jml - i == 1) {
                                add(jml);
                            }
                            $('#bc'+(i+1)).focus();
                        } else {
                            custom_message('Peringatan','Barang yang diinputkan tidak ada !');
                            $('#bc'+i).val('');
                            $('#pb'+i).val('');
                            $('#id_pb'+i).val('');
                            $('#kekuatan'+i).html('');
                            $('#ed'+i).html('');
                            $('#exp'+i).val('');
                            $('#hj'+i).html(''); // text asli
                            $('#harga_jual'+i).val('');
                            $('#disc'+i).val('');
                            $('#diskon'+i).html('');
                        }
                    }
                });
            }
            return false;
        }
    });
    $('#pb'+i).autocomplete("<?= base_url('inv_autocomplete/load_data_packing_barang_per_ed') ?>",
    {
        parse: function(data){
            var parsed = [];
            for (var j=0; j < data.length; j++) {
                parsed[j] = {
                    data: data[j],
                    value: data[j].nama // nama field yang dicari
                };
            }
            return parsed;
        },
        formatItem: function(data,i,max){
            var isi = ''; var satuan = ''; var sediaan = ''; var pabrik = ''; var satuan_terkecil = ''; var kekuatan = ''; var kemasan = '';
            if (data.isi !== '1') { var isi = '@ '+data.isi; }
            if (data.kekuatan !== null && data.kekuatan !== '0') { var kekuatan = data.kekuatan; }
            if (data.satuan !== null) { var satuan = data.satuan; }
            if (data.sediaan !== null) { var sediaan = data.sediaan; }
            if (data.pabrik !== null) { var pabrik = data.pabrik; }
            if (data.satuan_terkecil !== null) { var satuan_terkecil = data.satuan_terkecil; }
            if ((data.satuan_terbesar !== null) && (data.satuan_terbesar !== data.satuan_terkecil)) { kemasan = data.satuan_terbesar; }
            if (data.id_obat === null) {
                var str = '<div class=result>'+data.nama+' '+pabrik+' '+kemasan+' '+isi+' '+satuan_terkecil+'<br/>ED: '+datefmysql(data.ed)+'</div>';
            } else {
                if (data.generik === 'Non Generik') {
                    var str = '<div class=result>'+data.nama+' '+((kekuatan === '1')?'':kekuatan)+' '+satuan+' '+sediaan+' '+kemasan+' '+isi+' '+satuan_terkecil+'<br/>ED: '+datefmysql(data.ed)+'</div>';
                } else {
                    var str = '<div class=result>'+data.nama+' '+((kekuatan === '1')?'':kekuatan)+' '+satuan+' '+sediaan+' '+pabrik+' '+kemasan+' '+isi+' '+satuan_terkecil+'<br/>ED: '+datefmysql(data.ed)+'</div>';
                }
            }
            return str;
        },
        width: 440, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
        dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
    }).result(
    function(event,data,formated){
        var isi = ''; var satuan = ''; var sediaan = ''; var pabrik = ''; var satuan_terkecil = ''; var kekuatan = '';
        if (data.isi !== '1') { var isi = '@ '+data.isi; }
        if (data.kekuatan !== null && data.kekuatan !== '0') { var kekuatan = data.kekuatan; }
        if (data.satuan !== null) { var satuan = data.satuan; }
        if (data.sediaan !== null) { var sediaan = data.sediaan; }
        if (data.pabrik !== null) { var pabrik = data.pabrik; }
        if (data.satuan_terkecil !== null) { var satuan_terkecil = data.satuan_terkecil; }
        if (data.id_obat === null) {
            $(this).val(data.nama+' '+pabrik+' '+isi+' '+satuan_terkecil);
        } else {
            if (data.generik === 'Non Generik') {
                $(this).val(data.nama+' '+((kekuatan === '1')?'':kekuatan)+' '+satuan+' '+sediaan+' '+isi+' '+satuan_terkecil);
            } else {
                $(this).val(data.nama+' '+((kekuatan === '1')?'':kekuatan)+' '+satuan+' '+sediaan+' '+pabrik+' '+isi+' '+satuan_terkecil);
            }
        }
        
        $('#id_pb'+i).val(data.id);
        $('#bc'+i).val(data.barcode);
        $('#kekuatan'+i).html(data.kekuatan);
        $('#ed'+i).html(datefmysql(data.ed));
        $('#exp'+i).val(data.ed);
        $('#ppn'+i).html(data.ppn_jual);
        $('#id_kategori'+i).val(data.id_kategori);
        var id_packing = data.id;
        $.ajax({
            url: '<?= base_url("inv_autocomplete/get_harga_barang_penjualan") ?>/'+id_packing,
            cache: false,
            dataType: 'json',
            success: function(msg) {
                var diskon = msg.harga*(msg.diskon/100);
                var terdiskon = (msg.harga-diskon);
                var harga_jual= terdiskon+(terdiskon*(msg.ppn_jual/100));
                $('#hj'+i).html(numberToCurrency(Math.ceil(harga_jual))); // text asli
                $('#harga_jual'+i).val(harga_jual);
                $('#disc'+i).val(msg.diskon);
                $('#diskon'+i).html(msg.diskon);
                subTotal(i);
            }
        });
        $('#jl'+i).val('1').focus().select();
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
    
    var ppn = 0; //$('#ppn').val()/100;
    for (i = 0; i <= jumlah; i++) {
        if ($('#id_pb'+i).val() !== '') {
            var harga = currencyToNumber($('#hj'+i).html());
            var diskon= parseInt($('#diskon'+i).html())/100;

            <?php 
            if (isset($_GET['id'])) { ?>
            var jml= parseInt($('#jl'+i).html());                
            <?php } else { ?>
            var jml= parseInt($('#jl'+i).val());
            <?php } ?>

            var subtotal = Math.ceil(harga*jml);

            $('#subtotal'+i).html(numberToCurrency(subtotal));
            $('#subttl'+i).val(numberToCurrency(subtotal));


            var harga = parseInt(currencyToNumber($('#hj'+i).html()));
            var diskon= parseInt($('#diskon'+i).html())/100;


            var tagihan = tagihan + subtotal;

            //custom_message('Peringatan',subtotal);
        }
    }
    
    //$('#jasa_total_apotek').val(ja);
    //$('#total-diskon').html(numberToCurrency(Math.ceil(disc)));
    $('#total-tagihan').html(numberToCurrency(tagihan));
    var totallica = tagihan;
    var diskon_bank   = 0; //(totallica * ($('#disc_bank').html()/100));
    var pajak = ppn*tagihan;
    var new_totallica = (totallica - diskon_bank)+pajak;
    $('#total').html(numberToCurrency(Math.ceil(new_totallica)));
    $('input[name=total]').val(Math.ceil(new_totallica));
    if (tagihan !== 0) {
        $('#bulat').val(numberToCurrency(pembulatan_seratus(new_totallica)));
    }
}
function setKembali() {
    $(function() {
        //var apoteker = currencyToNumber($('#jasa-apt').html());
        var total = currencyToNumber($('#total').html());
        var bayar = currencyToNumber($('#bayar').val());
        var bulat = currencyToNumber($('#bulat').val());
        var kembali = bayar - bulat;
        if (isNaN(bayar)) {var kembali = 0;} else {var kembali = kembali;}
        //$('#bulat').val(numberToCurrency(total));
        $('#kembalian').html(numberToCurrency(kembali));
        $('input[name=total]').val(total);
    })
}
$(function() {
    $('#bulat').focus(function() {
        var kembalian = $('#kembalian').html();
        $('#kembalian').html(numberToCurrency(kembalian));
        var jumlah = $('.tr_row').length;
        for (i = 1; i <= jumlah; i++) {
            subTotal(i);
        }
    });
    $('#retur').button({icons: {secondary: 'ui-icon-transfer-e-w'}}).click(function() {
        var id = $('#id_penjualan').html();
        $.get('<?= base_url('inventory/retur_penjualan') ?>/'+id+'?_'+Math.random(), function(data) {
            $("#result_detail").dialog().remove();
            $('#loaddata').html(data);
        });
        $("#result_detail").dialog().remove();
    });
    $('#bayar').focus(function() {
        var jumlah = $('.tr_row').length;
        for (i = 1; i <= jumlah; i++) {
            subTotal(i);
        }
    });
    $('#tanggal').datetimepicker();
    $('#reset').click(function() {
        $('#loaddata').empty();
        var url = '<?= base_url('pelayanan/penjualan_nr') ?>';
        $('#loaddata').load(url);
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
    $("#form_penjualan_non_resep").submit(function() {
        $("<div title='Konfirmasi Simpan'>Anda yakin akan menyimpan data transaksi ini ?</div>").dialog({
                modal: true,
                autoOpen: true,
                width: 320,
                buttons: { 
                    "Ya": function() { 
                        $(this).dialog('close');
                        if ($('#id_pembeli').val() === '') {
                            custom_message('Peringatan','Nama pembeli tidak boleh kosong !');
                            $('#pasien').focus();
                            return false;
                        }
                        if ($('#bayar').val() === '') {
                            custom_message('Peringatan','Jumlah bayar tidak boleh kosong !');
                            $('#bayar').focus();
                            return false;
                        }
                        if ($('#bulat').val() === '') {
                            custom_message('Peringatan','Jumlah pembulatan tidak boleh kosong !');
                            $('#bulat').focus();
                            return false;
                        }
                        var vEmptyTextBox = $(".id_pb").filter(function(){
                            return $.trim($(this).val()) !== '';
                        }).length;
                        if (vEmptyTextBox === 0) {
                            custom_message('Peringatan','Silahkan isikan barang yang akan ditransaksikan !','#pb0');
                            return false;
                        }
                        var jumlah = $('.tr_row').length-1;
                        var jml = $('.pb[value=""]').length;
                        for(i = 0; i <= jumlah; i++) {
                            if ($('#id_pb'+i).val() !== '') {
                                if ($('#jl'+i).val() === '') {
                                    custom_message('Peringatan','Jumlah tidak boleh kosong !');
                                    $('#jl'+i).focus();
                                    return false;
                                }
                            }
                        }
                        var post = $("#form_penjualan_non_resep").attr('action');
                        $.ajax({
                            type: 'POST',
                            url: post,
                            dataType: 'json',
                            data: $("#form_penjualan_non_resep").serialize(),
                            success: function(data) {
                                if (data.status === true) {
                                    //$('input,select').attr('disabled','disabled');
                                    $('#print').show();
                                    $('#id_penjualan').html(data.id_penjualan);
                                    $('#save').hide();
                                    if (data.action === 'add') {
                                        alert_tambah();
                                    } else {
                                        alert_edit();
                                    }
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
<div id="cetak_kitir" style="display: none; border-color: #fff;">
    <table style="border-bottom: 1px solid #000; font-size: 16px;" width="100%">
    <tr><td colspan="4" align="center" style="text-transform: uppercase; font-size: 16px;"><?= $apt->nama ?></td> </tr>
    <tr><td colspan="4" align="center" style="font-size: 16px;"><?= $apt->alamat ?> <?= $apt->kelurahan ?></td> </tr>
    <tr><td colspan="4" align="center" style="font-size: 16px;">Telp. <?= $apt->telp ?>,  Fax. <?= $apt->fax ?>, Email <?= $apt->email ?></td> </tr>
</table>
    <center><h1>KITIR PENJUALAN BEBAS</h1></center>
<table class="content-printer" style="border-bottom: 1px solid #000; width: 100%">
    <tr><td>No.:</td><td><?= get_last_id('penjualan', 'id') ?></td></tr>
    <tr><td>Petugas:</td><td><?= $this->session->userdata('nama') ?></td></tr>
</table>

<table width="100%" id="loaddatapenjualan" style="border-bottom: 1px solid #000">
    <thead>
    <tr>
        <th align="left" width="40%">Barang</th>
        <th align="right" width="15%">Harga</th>
        <th align="center" width="15%">Diskon(%)</th>
        <th width="15%">Jumlah</th>
        <th align="right" width="15%">Subtotal</th>
    </tr>
    </thead>
    <tbody>
        
    </tbody>
</table>
    <p>
        SUM(Sub Total): <span id="total_barang"></span><br/>
        Total: <span id="total_tagihan"></span>
    </p>
    <p align="center">
        <span id="SCETAK"><input type="button" class="tombol" value="Cetak" id="cetak" onClick="PrintElem('#cetak_kitir')"/></span>
    </p>
</div>
    <div class="titling"><h1><?= $title ?></h1></div>
    <?php
    if (isset($list_data)) {
        foreach ($list_data as $rows);
    }
    ?>
    <?= form_open('pelayanan/penjualan_nr', 'id=form_penjualan_non_resep') ?>
    <div class="data-input">
        <table width="100%" class="inputan">Summary</legend>
        <?= form_hidden('total') ?>
        <?= form_hidden('id_penjualan', isset($rows)?$rows->transaksi_id:NULL) ?>
                <tr><td>No.:</td><td> <span class="label" id="id_penjualan"><?= isset($rows)?$rows->transaksi_id:get_last_id('penjualan', 'id') ?> </span>
                <tr><td>Waktu:</td><td><?= form_input('tanggal', isset($rows)?datetime($rows->waktu):date("d/m/Y H:i"), 'id=tanggal') ?>
                <!--<tr><td>Pembayaran Bank:</td><td><?= form_dropdown('cara_bayar', $list_bank, NULL, 'id=pembayaran') ?>
                <tr><td>SUM(Sub Total):</td><td><span class="label" id="total-tagihan"><?= isset($data['total'])?rupiah($data['total']):null ?> </span>-->
                <!--<tr><td>PPN (%):</td><td><?= form_input('ppn', '10', 'id=ppn size=10 onkeyup=subTotal()') ?>-->
                <tr><td></td><td><?= form_button(null, 'Tambah Baris (F8)', 'id=addnewrow') ?>
            
                <!--<tr><td>Total Diskon Barang:</td><td><span class="label" id="total-diskon"><?= isset($data['subtotal'])?rupiah($data['subtotal']):null ?></span>
                <tr><td>Diskon Bank (%):</td><td><span id="disc_bank" class="label"><?= isset($data['diskon_bank'])?$data['diskon_bank']:'0' ?></span><?= form_hidden('diskon_bank') ?>
                <tr><td>Total:</td><td><span id="total" class="label"><?= isset($data['total'])?rupiah($data['total']):null ?></span>-->
<!--                <tr><td>Pembulatan Total</td><td><?= form_input('bulat', isset($data['total'])?rupiah($data['total']):NULL, 'id=bulat size=30 onkeyup=FormNum(this) ') ?>
                <tr><td>Bayar (Rp)</td><td><?= form_input('bayar', isset($data['bayar'])?rupiah($data['bayar']):null, 'id=bayar size=30 ') ?>
                <tr><td>Kembalian (Rp)</td><td><span id="kembalian" class="label"><?= rupiah(isset($kembali)?$kembali:null) ?></span>-->
                
            
        </table>
    </div>
    <div class="data-list">
        
        <table class="tabel form-inputan" width="100%">
            <thead>
            <tr>
                <th width="10%">Barcode</th>
                <th width="40%">Packing Barang</th>
                <th width="10%">ED</th>
                <th width="15%">Harga Jual</th>
                <th width="7%">Diskon %</th>
                <!--<th width="7%">PPN</th>-->
                <th width="10%">Jumlah</th>
                <th width="10%">Sub Total</th>
                <th>#</th>
            </tr>
            </thead>
            <tbody>
                <?php
                if (isset($list_data)) { 
                $no = 0;
                foreach ($list_data as $key => $data) {
                    if ($data->id_obat == null) {
                        $packing = $data->barang.' '.$data->pabrik.' @ '.(($data->isi==1)?'':$data->isi).' '.$data->satuan_terkecil;
                    } else {
                        $packing = $data->barang .' '.(($data->kekuatan != '1')?$data->kekuatan:null).' '. $data->satuan.' '. $data->sediaan .' '.(($data->generik == 'Non Generik')?'':$data->pabrik).' '.(($data->isi==1)?'':'@ '.$data->isi).' '. $data->satuan_terkecil;
                    }  
                    $param = ($data->hna)+($data->hna*($data->margin/100));
                    $diskon= ($param*($data->diskon/100));
                    $harga_jual = $param - $diskon;
                ?>
                <tr class=tr_row>
                    <td><input type=text name=nr[] id=bc<?= $key ?> class=bc size=10 value="<?= $data->barcode ?>" /></td>
                    <td><input type=text name=dr[] id=pb<?= $key ?> class=pb value="<?= $packing ?>" size=60 /><input type=hidden name=id_pb[] id=id_pb<?= $key ?> class=id_pb value="<?= $data->barang_packing_id ?>" />
                        <input type="hidden" name="id_kategori[]" id="id_kategori<?= $key ?>" value="<?= $data->id_kategori ?>" class="id_kategori" /></td>
                    <td id=ed<?= $key ?> class=ed align=center><?= datefmysql($data->ed) ?></td>
                    <td id=hj<?= $key ?> class=hj align=right><?= rupiah($param) ?></td>
                    <td align=center class=diskon id=diskon<?= $key ?>><?= $diskon ?></td>
                    <!--<td align=center class=ppn id=ppn<?= $key ?>><?= $data->ppn ?></td>-->
                    <td><input type=hidden name=ed[] id=exp<?= $key ?> value="<?= $data->ed ?>" /> <input type=text name=jl[] id=jl<?= $key ?> class=jl size=20 style="width: 100px;" value="<?= $data->keluar ?>" onKeyup=subTotal() /><input type=hidden name=subtotal[] id=subttl<?= $key ?> class=subttl value="<?= ($harga_jual*$data->keluar) ?>" /></td>
                    <td id=subtotal<?= $key ?> align=right><?= rupiah($harga_jual*$data->keluar) ?></td>
                    <td class=aksi><a class=delete onclick=eliminate(this)></a><input type=hidden name=disc[] id=disc<?= $key ?> />
                        <input type=hidden name=harga_jual[] value="<?= $harga_jual ?>" id=harga_jual<?= $key ?> /></td>
                </tr>
                <script type="text/javascript">
                    $('#pb<?= $key ?>').autocomplete("<?= base_url('inv_autocomplete/load_data_packing_barang_per_ed') ?>",
                    {
                        parse: function(data){
                            var parsed = [];
                            for (var i=0; i < data.length; i++) {
                                parsed[i] = {
                                    data: data[i],
                                    value: data[i].nama // nama field yang dicari
                                };
                            }
                            return parsed;
                        },
                        formatItem: function(data,i,max){
                            var isi = ''; var satuan = ''; var sediaan = ''; var pabrik = ''; var satuan_terkecil = ''; var kekuatan = '';
                            if (data.isi !== '1') { var isi = '@ '+data.isi; }
                            if (data.kekuatan !== null && data.kekuatan !== '0') { var kekuatan = data.kekuatan; }
                            if (data.satuan !== null) { var satuan = data.satuan; }
                            if (data.sediaan !== null) { var sediaan = data.sediaan; }
                            if (data.pabrik !== null) { var pabrik = data.pabrik; }
                            if (data.satuan_terkecil !== null) { var satuan_terkecil = data.satuan_terkecil; }
                            if (data.id_obat === null) {
                                var str = '<div class=result>'+data.nama+' '+pabrik+' '+isi+' '+satuan_terkecil+'<br/>ED: '+datefmysql(data.ed)+'</div>';
                            } else {
                                if (data.generik === 'Non Generik') {
                                    var str = '<div class=result>'+data.nama+' '+((kekuatan === '1')?'':kekuatan)+' '+satuan+' '+sediaan+' '+isi+' '+satuan_terkecil+'<br/>ED: '+datefmysql(data.ed)+'</div>';
                                } else {
                                    var str = '<div class=result>'+data.nama+' '+((kekuatan === '1')?'':kekuatan)+' '+satuan+' '+sediaan+' '+pabrik+' '+isi+' '+satuan_terkecil+'<br/>ED: '+datefmysql(data.ed)+'</div>';
                                }
                            }
                            return str;
                        },
                        width: 440, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                        dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
                    }).result(
                    function(event,data,formated){
                        var isi = ''; var satuan = ''; var sediaan = ''; var pabrik = ''; var satuan_terkecil = ''; var kekuatan = '';
                        if (data.isi !== '1') { var isi = '@ '+data.isi; }
                        if (data.kekuatan !== null && data.kekuatan !== '0') { var kekuatan = data.kekuatan; }
                        if (data.satuan !== null) { var satuan = data.satuan; }
                        if (data.sediaan !== null) { var sediaan = data.sediaan; }
                        if (data.pabrik !== null) { var pabrik = data.pabrik; }
                        if (data.satuan_terkecil !== null) { var satuan_terkecil = data.satuan_terkecil; }
                        if (data.id_obat === null) {
                            $(this).val(data.nama+' '+pabrik+' '+isi+' '+satuan_terkecil);
                        } else {
                            if (data.generik === 'Non Generik') {
                                $(this).val(data.nama+' '+((kekuatan === '1')?'':kekuatan)+' '+satuan+' '+sediaan+' '+isi+' '+satuan_terkecil);
                            } else {
                                $(this).val(data.nama+' '+((kekuatan === '1')?'':kekuatan)+' '+satuan+' '+sediaan+' '+pabrik+' '+isi+' '+satuan_terkecil);
                            }
                        }

                        $('#id_pb<?= $key ?>').val(data.id);
                        $('#bc<?= $key ?>').val(data.barcode);
                        $('#kekuatan<?= $key ?>').html(data.kekuatan);
                        $('#ed<?= $key ?>').html(datefmysql(data.ed));
                        $('#exp<?= $key ?>').val(data.ed);
                        $('#ppn<?= $key ?>').html(data.ppn_jual);
                        $('#id_kategori<?= $key ?>').val(data.id_kategori);
                        var id_packing = data.id;
                        $.ajax({
                            url: '<?= base_url('inv_autocomplete/get_harga_barang_penjualan') ?>/'+id_packing,
                            cache: false,
                            dataType: 'json',
                            success: function(msg) {
                                var diskon = msg.harga*(msg.diskon/100);
                                var terdiskon = (msg.harga-diskon);
                                var harga_jual= terdiskon+(terdiskon*(msg.ppn_jual/100));
                                $('#hj<?= $key ?>').html(numberToCurrency(Math.ceil(harga_jual))); // text asli
                                $('#harga_jual<?= $key ?>').val(harga_jual);
                                $('#disc<?= $key ?>').val(msg.diskon);
                                $('#diskon<?= $key ?>').html(msg.diskon);
                                subTotal(i);
                            }
                        });
                        $('#jl<?= $key ?>').val('1');
                        //subTotal(i);
                        var jml = $('.tr_row').length;
                        //custom_message('Peringatan',jml+' - <?= $key ?>')
                        if (jml - i === 1) {
                            add(jml);
                        }
                        $('#bc'+(i+1)).focus();
                    });
                </script>
                <?php $no++; 
                
                } 
                } ?>
                
            </tbody>
            <tfoot>
            <tr>
                <td colspan="6" align="right"><b>Sum(Total):</b></td>
                <td align="right" id="total"></td>
                <td></td>
            </tr>
            </tfoot>
        </table> 
    </div>
   
    <?= form_button('save', 'Simpan', 'id=save') ?>
    <!--<?= form_button(null, 'Cetak Nota', 'id=print') ?>-->
    <?= form_button(null, 'Cetak Kitir', 'id=print') ?>
    <?php if (isset($list_data)) { ?>
    <?= form_button(NULL, 'Retur', 'id=retur') ?>
    <?php } ?>
    <?= form_button('Reset', 'Reset', 'id=reset') ?>
    <?= form_close() ?>
</div>