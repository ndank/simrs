<script type="text/javascript">
load_data_penjualan();
$.cookie('session', 'false');
$.cookie('formbayar', 'false');
$(document).on('keydown', function(e) {
    if (e.keyCode === 120) {
        if ($.cookie('session') === 'false') {
            $('#button').click();
        }
    }
    if (e.keyCode === 119) {
        if ($.cookie('session') === 'true' && $.cookie('formbayar') === 'false') {
            form_pembayaran();
        }
    }
});
//hitung_detail_total(jml, jum, data.diskon_rupiah, data.diskon_persen, data.harga_jual);
function hitung_detail_total(jml, jum, diskon_rupiah, diskon_persen, harga_jual) {
    if (diskon_persen === undefined) {
        dp = '0';
    } else {
        dp = diskon_persen;
    }
    
    if (diskon_rupiah === undefined) {
        dr = '0';
    } else {
        dr = diskon_rupiah;
    }
    $('#hargajual'+jml).html(numberToCurrency(harga_jual));
    $('#harga_jual'+jml).val(parseInt(harga_jual));
    //$('#diskon_rupiah'+jml).val(numberToCurrency(parseInt(dr)));
    //$('#diskon_persen'+jml).val(dp);
    //alert(jum+' '+harga_jual);
    var subtotal = (jum*harga_jual);
    //alert(jum+' '+harga_jual);
    $('#subtotal'+jml).html(numberToCurrency(parseInt(subtotal)));
    hitung_total_penjualan();
}

function hitung_total_penjualan() {
    var panjang   = $('.tr_rows').length; // banyaknya baris data
    var total     = 0;
    var jasa_apt  = parseInt(currencyToNumber($('#biaya-apt').html()));
    for(i = 1; i <= panjang; i++) {
        var subtotal = parseInt(currencyToNumber($('#subtotal'+i).html()));
        total   = total + subtotal;
    }
    var totallica = total + jasa_apt;
    var diskon_pr = ($('#diskon_pr').val()/100); // diskon penjualan %
    var diskon_rp = parseInt(currencyToNumber($('#diskon_rp').val())); // diskon penjualan Rp.
    var ppn_jual  = ($('#ppn').val()/100);
    var tuslah    = parseInt(currencyToNumber($('#tuslah').val()));
    var embalage  = parseInt(currencyToNumber($('#embalage').val()));
    if (diskon_pr !== 0) {
        total_terdiskon = totallica-(totallica*diskon_pr); // total terdiskon persentase
    } else {
        total_terdiskon = totallica-diskon_rp; // total terdiskon rupiah
    }
    var total_tambah_ppn = total_terdiskon+(total_terdiskon*ppn_jual);
    var total_tambah_tuslah = total_tambah_ppn+tuslah+embalage;
    
    /*Dikurangi Reimbursement jika ada*/
    var reimburse = $('#reimburse').val();
    if (!isNaN(reimburse)) {
        if ($('#reimburse').is(':checked') === true) {
            total = total_tambah_tuslah-(total_tambah_tuslah*(reimburse/100));
        } else {
            total = total_tambah_tuslah;
        }
    } else {
        total = total_tambah_tuslah;
    }
    
    $('#total-penjualan').html(numberToCurrency(parseInt(total)));
    $('#total_penjualan').val(parseInt(total_tambah_tuslah));
}

function removeMe(el) {
    $('<div id=alert>Anda yakin akan menghapus data ini?</div>').dialog({
        title: 'Konfirmasi Penghapusan',
        autoOpen: true,
        modal: true,
        buttons: {
            "OK": function() {
                $('#alert').dialog().remove();
                var parent = el.parentNode.parentNode;
                parent.parentNode.removeChild(parent);
                var jumlah = $('.tr_rows').length;
                var col = 0;
                for (i = 1; i <= jumlah; i++) {
                    $('.tr_rows:eq('+col+')').children('td:eq(0)').html(i);
                    $('.tr_rows:eq('+col+')').children('td:eq(1)').children('.id_barang').attr('id','id_barang'+i);
                    $('.tr_rows:eq('+col+')').children('td:eq(2)').children('.jumlah').attr('id','jumlah'+i);
                    $('.tr_rows:eq('+col+')').children('td:eq(3)').children('.harga_jual').attr('id','harga_jual'+i);
                    $('.tr_rows:eq('+col+')').children('td:eq(3)').children('.kemasan').attr('id','kemasan'+i);
                    $('.tr_rows:eq('+col+')').children('td:eq(4)').children('.ed').attr('id','ed'+i);
                    $('.tr_rows:eq('+col+')').children('td:eq(5)').attr('id','sisa'+i);
                    $('.tr_rows:eq('+col+')').children('td:eq(6)').attr('id','hargajual'+i);
                    $('.tr_rows:eq('+col+')').children('td:eq(7)').children('.diskon_rupiah').attr('id','diskon_rupiah'+i);
                    $('.tr_rows:eq('+col+')').children('td:eq(8)').children('.diskon_persen').attr('id','diskon_persen'+i);
                    $('.tr_rows:eq('+col+')').children('td:eq(9)').attr('id','subtotal'+i);
                    col++;
                }
                hitung_total_penjualan();
            }
        }
    });
}

function check_alergi_obat_pasien(i, id_pasien, id_barang) {
    if (id_pasien !== undefined && id_barang !== undefined) {
        $.ajax({
            url: '<?= base_url('autocomplete/check_alergi_obat_pasien') ?>?id_barang='+id_barang+'&id_pasien='+id_pasien,
            cache: false,
            dataType: 'json',
            success: function(data) {
                if (data.jumlah === '1') {
                    alert_dinamic('HATI-HATI!, Pasien ini memiliki alergi terhadap obat '+data.nama+'!','');
                    $('.tr_rows:eq('+(i-1)+')').css({background: "purple", color: "white"});
                }
            }
        });
    }
}

function add_new_rows(id_brg, nama_brg, jumlah, id_packing) {
    var jml = $('.tr_rows').length+1;
    
    var str = '<tr class="tr_rows">'+
                '<td align=center>'+jml+'</td>'+
                '<td>&nbsp;'+nama_brg+' <input type=hidden name=id_barang[] value="'+id_brg+'" class=id_barang id=id_barang'+jml+' /></td>'+
                '<td><input type=text name=jumlah[] class=jumlah id=jumlah'+jml+' value="'+jumlah+'" style="text-align: center;" /></td>'+
                '<td><input type=hidden name=harga_jual[] id=harga_jual'+jml+' class=harga_jual /> <select name=kemasan[] class=kemasan id=kemasan'+jml+'></select></td>'+
                '<td align=center><select name=ed[] class=ed id=ed'+jml+'></select></td>'+
                '<td align=center id=sisa'+jml+'></td>'+
                '<td align=right id=hargajual'+jml+'></td>'+
                '<td><input type=text name=diskon_rupiah[] class=diskon_rupiah style="text-align: right;" id=diskon_rupiah'+jml+' value="0" onblur="FormNum(this)" /></td>'+
                '<td><input type=text name=diskon_persen[] class=diskon_persen style="text-align: center;" id=diskon_persen'+jml+' value="0" /></td>'+
                '<td align=right id=subtotal'+jml+'></td>'+
                '<td align=center><img onclick=removeMe(this); title="Klik untuk hapus" src="img/icons/delete.png" class=add_kemasan align=left /></td>'+
              '</tr>';
    $('#penjualan-list tbody').append(str);
    $.getJSON('<?= base_url('autocomplete/get_kemasan_barang') ?>/'+id_brg, function(data){
        $('#kemasan'+jml).html('');
        $.each(data, function (index, value) {
            $('#kemasan'+jml).append("<option value='"+value.id+"'>"+value.nama+"</option>");
            if (value.default_kemasan === '1') { $('#kemasan'+jml).val(value.id); }
        });
    });
    $.getJSON('models/autocomplete.php?method=get_expiry_barang&id='+id_brg, function(data){
        $('#ed'+jml).html('');
        var jmled = 0;
        $.each(data, function (index, value) {
            $('#ed'+jml).append("<option value='"+value.ed+"'>"+datefmysql(value.ed)+"</option>");
            jmled++;
        });
        if (jmled === 0) {
            $('#ed'+jml).append("<option value=''></option>");
        }
    });
    $.ajax({
        url: 'models/autocomplete.php?method=get_detail_harga_barang_resep&id='+id_brg+'&jumlah='+jumlah,
        dataType: 'json',
        cache: false,
        success: function(data) {
            hitung_detail_total(jml, jumlah, data.diskon_rupiah, data.diskon_persen, Math.ceil(data.harga_jual));
            hitung_total_penjualan();
        }
    });
    check_alergi_obat_pasien(i, $('#id_customer').val(), id_brg);
    $('#ed'+jml).datepicker({
        changeYear: true,
        changeMonth: true,
        minDate: 0
    });
    $('#jumlah'+jml).blur(function() {
        var jumlah      = $('#jumlah'+jml).val();
        var hrg_jual    = parseInt(currencyToNumber($('#hargajual'+jml).html()));
        var diskon      = 0;
        if ($('#diskon_rupiah'+jml).val() !== '0') {
            diskon  = parseInt(currencyToNumber($('#diskon_rupiah'+jml).val()));
        }
        else if ($('#diskon_persen'+jml).val() !== '0') {
            var diskonpr= $('#diskon_persen'+jml).val()/100;
            diskon  = ((jumlah*hrg_jual)*diskonpr);
        }
        
        var subtotal    = (hrg_jual*jumlah)-diskon;
        $('#subtotal'+jml).html(numberToCurrency(parseInt(subtotal)));
        hitung_total_penjualan();
    });
    $('#diskon_rupiah'+jml).blur(function() {
        if ($(this).val() !== '0') {
            $('#diskon_persen'+jml).val('0');
        }
        var jumlah      = $('#jumlah'+jml).val();
        var hrg_jual    = parseInt(currencyToNumber($('#hargajual'+jml).html()));
        var diskon      = 0;
        if ($('#diskon_rupiah'+jml).val() !== '0') {
            diskon  = parseInt(currencyToNumber($('#diskon_rupiah'+jml).val()));
        }
        else if ($('#diskon_persen'+jml).val() !== '0') {
            var diskonpr= $('#diskon_persen'+jml).val()/100;
            diskon  = ((jumlah*hrg_jual)*diskonpr);
        }
        
        var subtotal    = (hrg_jual*jumlah)-diskon;
        $('#subtotal'+jml).html(numberToCurrency(parseInt(subtotal)));
        hitung_total_penjualan();
    });
    $('#diskon_persen'+jml).blur(function() {
        if ($(this).val() !== '0') {
            $('#diskon_rupiah'+jml).val('0');
        }
        var jumlah      = $('#jumlah'+jml).val();
        var hrg_jual    = parseInt(currencyToNumber($('#hargajual'+jml).html()));
        var diskon      = 0;
        if ($('#diskon_rupiah'+jml).val() !== '0') {
            diskon  = parseInt(currencyToNumber($('#diskon_rupiah'+jml).val()));
        }
        else if ($('#diskon_persen'+jml).val() !== '0') {
            var diskonpr= $('#diskon_persen'+jml).val()/100;
            diskon  = ((jumlah*hrg_jual)*diskonpr);
        }
        
        var subtotal    = (hrg_jual*jumlah)-diskon;
        $('#subtotal'+jml).html(numberToCurrency(parseInt(subtotal)));
        hitung_total_penjualan();
    });
    $('#kemasan'+jml).change(function() {
        var id  = $(this).val();
        var jum = $('#jumlah'+jml).val();
        $.ajax({
            url: 'models/autocomplete.php?method=get_detail_harga_barang&id='+id+'&jumlah='+jum,
            dataType: 'json',
            cache: false,
            success: function(data) {
                hitung_detail_total(jml, jum, data.diskon_rupiah, data.diskon_persen, Math.ceil(data.harga_jual_resep));
                hitung_total_penjualan();
            }
        });
    });
    $.ajax({
        url: '<?= base_url('autocomplete/get_stok_sisa')?>/'+id_brg,
        dataType: 'json',
        cache: false,
        success: function(data) {
            if (data.sisa === null) {
                sisa = '0';
            } else {
                sisa = data.sisa;
            }
            $('#sisa'+jml).html(sisa);
        }
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

function hitung_kembalian() {
    var pembulatan = parseInt(currencyToNumber($('#pembulatan').val()));
    var pembayaran = parseInt(currencyToNumber($('#pembayaran').val()));
    var kembalian  = pembayaran - pembulatan;
    //,#pembulatan_bayar,#pembayaran_bayar'
    if (kembalian < 0) {
        $('#label_kembali').html('Kekurangan:');
        $('#kembalian').html(kembalian);
    } else {
        $('#label_kembali').html('Kembalian:');
        $('#kembalian').html(numberToCurrency(parseInt(kembalian)));
    }
    $('#pembulatan_bayar').val(pembulatan);
    $('#pembayaran_bayar').val(pembayaran);
}

function form_pembayaran() {
    var str = '<div id="form-pembayaran">'+
                '<table width="100%" style="font-size: 30px;">'+
                    '<tr><td id=label-open>Total Tagihan:</td><td><?= form_input(null, null, 'id=total_tagihan readonly size=10') ?></td></tr>'+
                    '<tr><td>Pembulatan:</td><td><?= form_input('pembulatan', NULL, 'id=pembulatan size=10') ?></td></tr>'+
                    '<tr><td>Pembayaran:</td><td><?= form_input('pembayaran', NULL, 'id=pembayaran size=10') ?></td></tr>'+
                    '<tr><td id=label_kembali>Kembalian:</td><td id=kembalian></td></tr>'+
                '</table>'+
              '</div>';
      $('body').append(str);
      $('#pembayaran').keydown(function(e) {
          if (e.keyCode === 13) {
              $('button[type=button]').focus();
          }
      });
      $('#total_tagihan,#pembulatan,#pembayaran').keyup(function() {
            FormNum(this);
            hitung_kembalian();
      });
      $('#form-pembayaran').dialog({
            title: 'Form Pembayaran',
            autoOpen: true,
            modal: true,
            width: 500,
            height: 300,
            buttons: {
                "Simpan": function() {
                    $('#save_penjualan').submit();
                    $('#form-pembayaran').dialog().remove();
                    $('.adding').remove();
                }
            },
            open: function() {
                var cek_pembayaran = $('.adding').length;
                if (cek_pembayaran === 0) {
                    var total = parseInt(currencyToNumber($('#total-penjualan').html()));
                    $('#label-open').html('Total Tagihan:');
                    $('#total_tagihan').val(numberToCurrency(total));
                    $('#pembulatan,#pembayaran,#pembulatan_bayar,#pembayaran_bayar').val(numberToCurrency(pembulatan_seratus(total)));
                    $('#kembalian').html('0');
                    $('#pembayaran').focus().select();
                    $.cookie('formbayar', 'true');
                } else {
                    $('#label-open').html('Sisa Tagihan:');
                    var kekurangan = parseInt(currencyToNumber($('#kekurangan').html()));
                    $('#total_tagihan').val(numberToCurrency(kekurangan));
                    $('#pembulatan,#pembayaran,#pembulatan_bayar,#pembayaran_bayar').val(numberToCurrency(pembulatan_seratus(kekurangan)));
                    $('#kembalian').html('0');
                    $('#pembayaran').focus().select();
                }
            }
            ,close: function() {
                $('#form-pembayaran').dialog().remove();
                $.cookie('formbayar', 'false');
            }
      });
}

function cetak_struk(id_penjualan) {
    var wWidth = $(window).width();
    var dWidth = wWidth * 0.3;
    var wHeight= $(window).height();
    var dHeight= wHeight * 1;
    var x = screen.width/2 - dWidth/2;
    var y = screen.height/2 - dHeight/2;
    window.open('<?= base_url('pelayanan/manage_penjualan/cetak') ?>?id='+id_penjualan, 'Penjualan Cetak', 'width='+dWidth+', height='+dHeight+', left='+x+',top='+y);
}

function form_add() {
    var str = '<div id="form_penjualan">'+
            '<form id="save_penjualan">'+
            '<?= form_hidden('id_penjualan', NULL, 'id=id_penjualan') ?>'+
            '<?= form_hidden('pembulatan', NULL, 'id=pembulatan_bayar') ?>'+
            '<?= form_hidden('pembayaran', NULL, 'id=pembayaran_bayar') ?>'+
            '<table width=100% class=data-input><tr valign=top><td width=50%><table width=100% id="attr-utama" cellpadding=0 cellspacing=0>'+
                '<tr><td width=20%>Tanggal:</td><td><?= form_input('tanggal', date("d/m/Y"), 'id=tanggal size=10') ?></td></tr>'+
                '<tr><td width=20%>No. Resep:</td><td><?= form_input('noresep', NULL, 'id=noresep size=40') ?> <?= form_hidden('id_resep', NULL, 'id=id_resep') ?></td></tr>'+
                '<tr><td>Pasien:</td><td><?= form_input('customer', NULL, 'id=customer size=40') ?> <?= form_hidden('id_customer', NULL, 'id=id_customer') ?> <?= form_hidden('asuransi', NULL, 'id=asuransi') ?></td></tr>'+
                '<tr><td>Diskon:</td><td><?= form_input('diskon_pr', '0', 'id=diskon_pr maxlength=3 onblur="hitung_total_penjualan();" size=10') ?> %, Rp. <?= form_input('diskon_rp', '0', 'id=diskon_rp onblur="hitung_total_penjualan();" onkeyup="FormNum(this)" size=10') ?></td></tr>'+
                '<tr><td>PPN:</td><td><?= form_input('ppn', '0', 'id=ppn size=10 maxlength=5 onblur="hitung_total_penjualan();"') ?> %</td></tr>'+
                '<tr><td>Tuslah Rp.:</td><td><?= form_input('tuslah', '0', 'id=tuslah onblur=FormNum(this) onkeyup="hitung_total_penjualan();" size=10') ?></td></tr>'+
                '<tr><td>Embalage Rp.:</td><td><?= form_input('embalage', '0', 'id=embalage size=10 onblur=FormNum(this) onkeyup="hitung_total_penjualan();"') ?></td></tr>'+
            '</table></td><td width=50%><table width=100% id=detail_harga_jual cellpadding=0 cellspacing=0>'+
                '<tr><td width=20%>Barcode:</td><td><?= form_input('barcode', NULL, 'id=barcode size=40') ?></td></tr>'+
                '<tr><td width=20%>Nama Barang:</td><td><?= form_input('barang', NULL, 'id=barang size=40') ?><?= form_hidden('id_barang', NULL, 'id=id_barang') ?></td></tr>'+
                '<tr><td>Jumlah:</td><td><input type=text value="1" size=5 id=pilih /></td></tr>'+
                '<tr><td>Biaya Apoteker:</td><td><span>Rp</span> <span id=biaya-apt>0</span>, 00</td></tr>'+
                '<tr><td>TOTAL:</td><td style="font-size: 45px;"><span>Rp</span> <span id=total-penjualan style="font-size: 45px;">0</span>, 00</td></tr>'+
            '</table><input type=hidden name=total_penjualan id=total_penjualan /></td></tr></table>'+
            '<table width=100% cellspacing="0" class="list-data-input" id="penjualan-list"><thead>'+
                '<tr><th width=5%>No.</th>'+
                    '<th width=25%>Nama Barang</th>'+
                    '<th width=5%>Jumlah</th>'+
                    '<th width=10%>Kemasan</th>'+
                    '<th width=10%>ED</th>'+
                    '<th width=5%>Sisa<br/>Stok</th>'+
                    '<th width=10%>Harga Jual</th>'+
                    '<th width=10%>Diskon RP.</th>'+
                    '<th width=10%>Diskon %</th>'+
                    '<th width=10%>Subtotal</th>'+
                    '<th width=1%>#</th>'+
                '</tr></thead>'+
                '<tbody></tbody>'+
            '</table>'+
            '</form></div>';
    $('body').append(str);
    var lebar = $('#pabrik').width();
    $('#pilih').keydown(function(e) {
        if (e.keyCode === 13) {
            var id_barang   = $('#id_barang').val();
            var nama        = $('#barang').val();
            if (id_barang !== '') {
                add_new_rows(id_barang, nama, $(this).val());
            }
            $('#id_barang').val('');
            $('#barang').val('').focus();
        }
    });
    $('#barcode').keydown(function(e) {
        if (e.keyCode === 13) {
            var barcode = $('#barcode').val();
            if (barcode !== '' && barcode !== ' ') {
                $.ajax({
                    url: 'models/autocomplete.php?method=get_barang&barcode='+barcode,
                    dataType: 'json',
                    success: function(data) {
                        $('#barang').val(data.nama_barang);
                        $('#id_barang').val(data.id);
                        if (data.id !== '') {
                            add_new_rows(data.id, data.nama, '1', data.id_packing);
                        }
                        $('#id_barang').val('');
                        $('#barang').val('');
                        $('#barcode').val('').focus();
                    }
                });
            }
        }
    });
    $('#barang').keydown(function(e) {
        if (e.keyCode === 13) {
            $('#pilih').focus().select();
        }
    });
    $('#customer').autocomplete("models/autocomplete.php?method=pasien",
    {
        parse: function(data){
            var parsed = [];
            for (var i=0; i < data.length; i++) {
                parsed[i] = {
                    data: data[i],
                    value: data[i].nama // nama field yang dicari
                };
            }
            $('#id_customer').val('');
            $('#attr-utama').append('');
            return parsed;
        },
        formatItem: function(data,i,max){
            var str = '<div class=result>'+data.nama+' <br/> '+data.alamat+'</div>';
            return str;
        },
        width: lebar, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
        dataType: 'json', // tipe data yang diterima oleh library ini disetup sebagai JSON
        cacheLength: 0
    }).result(
    function(event,data,formated){
        $(this).val(data.nama);
        $('#id_customer').val(data.id);
        $('#asuransi').val(data.id_asuransi);
        $('#newrow').remove();
        if (data.id_asuransi !== null) {
            var attr = '<tr id="newrow"><td>Reimburse:</td><td><?= form_checkbox('reimburse', '', 'reimburse', '', TRUE) ?></td></tr>';
            $('#attr-utama').append(attr);
            $('#reimburse').val(data.reimburse);
            $('label').html(data.reimburse+' %');
            $('#reimburse').unbind('click');
            $('#reimburse').bind('click', function() {
                hitung_total_penjualan();
            });
        }
        hitung_total_penjualan();
    });
    
    $('#barang').autocomplete("models/autocomplete.php?method=barang",
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
            var str = '<div class=result>'+data.nama_barang+'</div>';
            return str;
        },
        width: lebar, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
        dataType: 'json', // tipe data yang diterima oleh library ini disetup sebagai JSON
        cacheLength: 0
    }).result(
    function(event,data,formated){
        $(this).val(data.nama_barang);
        $('#id_barang').val(data.id);
    });
    
    $('#noresep').autocomplete("<?= base_url('autocomplete/resep') ?>",
    {
        parse: function(data){
            var parsed = [];
            for (var i=0; i < data.length; i++) {
                parsed[i] = {
                    data: data[i],
                    value: data[i].id // nama field yang dicari
                };
            }
            return parsed;
        },
        formatItem: function(data,i,max){
            var str = '<div class=result>'+data.id+'<br/>'+data.nama+'</div>';
            return str;
        },
        width: lebar, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
        dataType: 'json', // tipe data yang diterima oleh library ini disetup sebagai JSON
        cacheLength: 0
    }).result(
    function(event,data,formated){
        $(this).val(data.id);
        $('#id_resep').val(data.id);
        $('#customer').val(data.nama);
        $('#id_customer').val(data.id_pasien);
        $('#newrow,.adding').remove();
        if (data.id_asuransi !== null) {
            var attr = '<tr id="newrow"><td>Reimburse:</td><td><?= form_checkbox('reimburse', '', 'reimburse', '', FALSE) ?></td></tr>';
            $('#attr-utama').append(attr);
            $('#reimburse').val(data.reimburse);
            $('label').html(data.reimburse+' %');
            $('#reimburse').unbind('click');
            $('#reimburse').bind('click', function() {
                hitung_total_penjualan();
            });
        }
        hitung_total_penjualan();
        $.ajax({
            url: '<?= base_url('pelayanan/manage_resep/get_detail_resep_penjualan') ?>?id='+data.id,
            cache: false,
            success: function(data) {
                $('#penjualan-list tbody').html(data);
            }   
        });
    });
    var wWidth = $(window).width();
    var dWidth = wWidth * 1;
    
    var wHeight= $(window).height();
    var dHeight= wHeight * 1;
    $('#form_penjualan').dialog({
        title: 'Penjualan Resep',
        autoOpen: true,
        modal: true,
        width: dWidth,
        height: dHeight,
        hide: 'clip',
        show: 'blind',
        buttons: {
            "Bayar (F8)": function() {
                form_pembayaran();
            }, 
            "Cancel": function() {    
                $(this).dialog().remove();
                $.cookie('session', 'false');
            }
        }, close: function() {
            $(this).dialog().remove();
            $.cookie('session', 'false');
        }, open: function() {
            $('#noresep').focus();
            $('#barcode').val('');
            $.cookie('session', 'true');
        }
    });
    $('#tanggal').datepicker({
        changeYear: true,
        changeMonth: true
    });
    $('#save_penjualan').submit(function() {
        var jumlah = $('.tr_rows').length;
        if ($('#id_resep').val() === '') {
            alert_empty('No. resep','#noresep');
        }
        if (jumlah === 0) {
            alert_empty('Barang', '#barang');
            return false;
        }
        if (jumlah > 0) {
            for (i = 1; i <= jumlah; i++) {
                // alert here
            }
        }
        $.ajax({
            url: '<?= base_url('pelayanan/manage_penjualan/save') ?>',
            data: $(this).serialize(),
            dataType: 'json',
            type: 'POST',
            success: function(data) {
                if (data.status === true) {
                    load_data_penjualan();
                    $('#penjualan-list tbody').html('');
                    $('#noresep, #total_penjualan, #customer, #id_customer, #pilih').val('');
                    $('#total-penjualan').html('0');
                    $('#biaya-apt').html('0');
                    $('#newrow,.adding').remove();
                    $('#form_penjualan').dialog().remove();
                    //location.reload();
                    cetak_struk(data.id);
                    alert_refresh('Data penjualan berhasil disimpan!');
                    //alert_tambah('#noresep');
                }
            }
            
        });
        return false;
    });
}
$('#button-penjualan').button({
    icons: {
        secondary: 'ui-icon-newwin'
    }
}).click(function() {
    form_add();
});
$('#reset-penjualan').button({
    icons: {
        secondary: 'ui-icon-refresh'
    }
}).click(function() {
    load_data_penjualan();
});
function load_data_penjualan(page, search, id) {
    pg = page; src = search; id_barg = id;
    if (page === undefined) { var pg = ''; }
    if (search === undefined) { var src = ''; }
    if (id === undefined) { var id_barg = ''; }
    $.ajax({
        url: '<?= base_url('pelayanan/manage_penjualan/list') ?>/'+page,
        cache: false,
        data: 'page='+pg+'&search='+src+'&id='+id_barg,
        success: function(data) {
            $('#result-penjualan').html(data);
        }
    });
}

function paging(page, tab, search) {
    load_data_penjualan(page, search);
}

function edit_penjualan(str) {
    var arr = str.split('#');
    form_add();
    $('#id_penjualan').val(arr[0]);
    $('#tanggal').val(arr[1]);
    $('#noresep').val(arr[2]);
    $('#id_resep').val(arr[2]);
    $('#customer').val(arr[3]);
    $('#id_customer').val(arr[4]);
    $('#dison_pr').val(arr[5]);
    $('#dison_rp').val(arr[6]);
    $('#ppn').val(arr[7]);
    $('#tuslah').val(arr[9]);
    $('#embalage').val(arr[10]);
    $('#biaya-apt').html(arr[11]);
    $('#total-penjualan').html(numberToCurrency(arr[8]));
    $('#total_penjualan').val(arr[8]);
    $.getJSON('<?= base_url('pelayanan/manage_penjualan/get_data_penjualan') ?>?id='+arr[0], function(data){
        $.each(data, function (index, value) {
            add_new_rows(value.id_barang, value.nama_barang, value.qty, value.id_kemasan);
            $('#diskon_rupiah'+(index+1)).val(value.disc_rp);
            $('#diskon_persen'+(index+1)).val(value.disc_pr);
        });
    });
    $('#form_add').dialog({ title: 'Edit penjualan' });
}

function delete_penjualan(id, page) {
    $('<div id=alert>Anda yakin akan menghapus data ini?</div>').dialog({
        title: 'Konfirmasi Penghapusan',
        autoOpen: true,
        modal: true,
        buttons: {
            "OK": function() {
                $.ajax({
                    url: '<?= base_url('pelayanan/manage_penjualan/delete') ?>?id='+id,
                    cache: false,
                    success: function() {
                        load_data_penjualan(page);
                        $('#alert').dialog().remove();
                    }
                });
            },
            "Cancel": function() {
                $(this).dialog().remove();
            }
        }
    });
}
</script>
<button id="button-penjualan">Tambah (F9)</button>
<button id="reset-penjualan">Reset</button>
<div id="result-penjualan">
    
</div>