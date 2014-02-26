<?= $this->load->view('message') ?>
<script type="text/javascript">
    $.cookie('session', 'false');
    $.cookie('formbayar', 'false');
    $(document).keydown(function(e) {
        if (e.keyCode === 120) {
            if ($.cookie('session') === 'false') {
                $('#button').click();
            }
        }
    });

load_data_distribusi();

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
                    $('.tr_rows:eq('+col+')').children('td:eq(2)').children('.kemasan').attr('id','kemasan'+i);
                    $('.tr_rows:eq('+col+')').children('td:eq(3)').children('.jumlah').attr('id','jumlah'+i);
                    $('.tr_rows:eq('+col+')').children('td:eq(4)').attr('id','subtotal'+i);
                    $('.tr_rows:eq('+col+')').children('td:eq(5)').children('.perundangan').attr('id','perundangan'+i);
                    col++;
                }
                hitung_estimasi();
            }
        }
    });
}

function hitung_estimasi() {
    var jml_baris = $('.tr_rows').length;
    var estimasi = 0;
    for (i = 1; i <= jml_baris; i++) {
        var subtotal = parseInt(currencyToNumber($('#subtotal'+i).html()));
        estimasi = estimasi + subtotal;
    }
    $('#estimasi').html(numberToCurrency(parseInt(estimasi)));
}
function add_new_rows(id_brg, nama_brg, jumlah, id_kemasan) {
    if (id_kemasan === null) {
        alert('Kemasan tidak boleh kosong !');
        return false;
    }
    var jml     = $('.tr_rows').length+1;
    var kemasan = $('#kemasan option:selected').text();
    var str = '<tr class="tr_rows">'+
                '<td align=center>'+jml+'</td>'+
                '<td>&nbsp;'+nama_brg+' <input type=hidden name=id_barang[] value="'+id_brg+'" class=id_barang id=id_barang'+jml+' /></td>'+
                '<td align=center>'+kemasan+'<input type=hidden name=kemasan[] id=kemasan'+jml+' value="'+id_kemasan+'" class=kemasan /></td>'+
                '<td><input type=text name=jumlah[] id=jumlah'+jml+' value="'+jumlah+'" class=jumlah size=10 style="text-align: center;" /></td>'+
                '<td align=right id=hna'+jml+'></td>'+
                '<td align=right id=subtotal'+jml+'></td>'+
                '<td><select name="ed[]" id="ed'+jml+'" class="ed" style="width: 110px;"></select></td>'+
                '<td align=center><input type=hidden class=perundangan id=perundangan'+jml+' /><img onclick=removeMe(this); title="Klik untuk hapus" src="<?= base_url('assets/images/delete.png') ?>" class=add_kemasan align=left /></td>'+
              '</tr>';
    $('#pesanan-list tbody').append(str);
    $.ajax({
        url: '<?= base_url('inventory/get_detail_harga_barang_pemesanan') ?>?id='+id_brg+'&id_kemasan='+id_kemasan,
        dataType: 'json',
        cache: false,
        success: function(data) {
            var subtotal = data.esti*jumlah;
            //alert(subtotal+' '+data.esti+' '+jumlah);
            $('#hna'+jml).html(numberToCurrency(parseInt(data.esti)));
            $('#subtotal'+jml).html(numberToCurrency(parseInt(subtotal)));
            $('#perundangan'+jml).val(data.perundangan);
            hitung_estimasi();
        }
    });
    $.getJSON('<?= base_url('autocomplete/get_expiry_barang') ?>?id='+id_brg, function(data){
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
}

function cetak_sp(id_distribusi, perundangan) {
    var wWidth = $(window).width();
    var dWidth = wWidth * 0.4;
    var wHeight= $(window).height();
    var dHeight= wHeight * 1;
    var x = screen.width/2 - dWidth/2;
    var y = screen.height/2 - dHeight/2;
    window.open('<?= base_url('inventory/cetak_sp') ?>?id='+id_distribusi+'&perundangan='+perundangan, 'distribusi Cetak', 'width='+dWidth+', height='+dHeight+', left='+x+',top='+y);
}

function form_add() {
    var str = '<div id="form_distribusi">'+
            '<form id="save_distribusi">'+
            '<input type=hidden name=id id=id />'+
            '<table width=100% class=data-input cellpadding=0 cellspacing=0><tr valign=top><td width=50%><table width=100% cellpadding=0 cellspacing=0>'+
                '<tr><td width=15%>Tanggal:</td><td><?= form_input('tanggal', date("d/m/Y"), 'id=tanggal size=15') ?></td></tr>'+
                '<tr><td>Unit Tujuan:</td><td width=20%><?= form_input('unit', NULL, 'id=unit size=40') ?><?= form_hidden('id_unit', NULL, 'id=id_unit') ?></td></tr>'+
                '<tr><td width=20%>Nama Barang:</td><td width=50%><?= form_input('barang', NULL, 'id=barang size=40') ?><input type=hidden name=id_barang id=id_barang /></td></tr>'+
                '<tr><td>Kemasan:</td><td><select name=id_kemasan id=kemasan><option value="">Pilih Kemasan...</option></select></td></tr>'+
                '<tr><td>Jumlah:</td><td><?= form_input('jumlah', NULL, 'id=jumlah size=10 onblur="Angka(this);"') ?></td></tr>'+
            '</table></td><td width=50%>'+
                '<table cellpadding=0 cellspacing=0>'+
                '<tr><td>Total Rp.:</td><td style="font-size: 40px; padding-left: 10px;"> <span id=estimasi style="font-size: 40px;">0</span>, 00</td></tr>'+
            '</table>'+
            '</td></tr></table>'+
            '<table width=100% cellspacing="0" class="list-data-input" id="pesanan-list"><thead>'+
                '<tr><th width=5%>No.</th>'+
                    '<th width=43%>Nama Barang</th>'+
                    '<th width=10%>Kemasan</th>'+
                    '<th width=10%>Jumlah</th>'+
                    '<th width=10%>HNA</th>'+
                    '<th width=10%>Subtotal</th>'+
                    '<th width=10%>ED</th>'+
                    '<th width=2%>#</th></tr></thead>'+
                '<tbody></tbody>'+
            '</table>'+
            '</form></div>';
    $('body').append(str);
    $(document).keydown(function(e) {
        if (e.keyCode === 119) {
            $('#save_distribusi').submit();
        }
    });
    var lebar = $('#pabrik').width();
    $('#jumlah').keydown(function(e) {
        if (e.keyCode === 13) {
            add_new_rows($('#id_barang').val(), $('#barang').val(), $('#jumlah').val(), $('#kemasan').val());
            $('#id_barang').val('');
            $('#jumlah').val('');
            $('#kemasan').html('').append('<option value="">Pilih ...</option>');
            $('#barang').val('').focus();
            
        }
    });
    $('#barang').keydown(function(e) {
        if (e.keyCode === 13) {
            $('#kemasan').focus();
            //$('#jumlah').val('1').focus().select();
        }
    });
    $('#kemasan').keydown(function(e) {
        if (e.keyCode === 13) {
            //$('#kemasan').focus();
            $('#jumlah').val('1').focus().select();
        }
    });
    $('#barang').autocomplete("<?= base_url('autocomplete/barang') ?>",
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
        $('#kemasan').html('');
        $.getJSON('<?= base_url('inventory/get_kemasan_barang') ?>?id='+data.id, function(data){
            if (data === null) {
                alert('Kemasan tidak barang tidak tersedia !');
            } else {
                $.each(data, function (index, value) {
                    $('#kemasan').append("<option value='"+value.id_kemasan+"'>"+value.nama+"</option>");
                });
            }
        });
    });
    $('#unit').autocomplete("<?= base_url('autocomplete/unit') ?>",
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
            var str = '<div class=result>'+data.nama+'</div>';
            return str;
        },
        width: lebar, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
        dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
    }).result(
    function(event,data,formated){
        $(this).val(data.nama);
        $('#id_unit').val(data.id);
        $('#barang').focus();
    });
    var wWidth = $(window).width();
    var dWidth = wWidth * 1;
    
    var wHeight= $(window).height();
    var dHeight= wHeight * 1;
    $('#form_distribusi').dialog({
        title: 'Tambah distribusi Barang',
        autoOpen: true,
        modal: true,
        width: 1024,
        height: dHeight,
        hide: 'clip',
        show: 'blind',
        buttons: {
            "Simpan (F8)": function() {
                $('#save_distribusi').submit();
            }, 
            "Cancel": function() {    
                $(this).dialog().remove();
                $.cookie('session', 'false');
            }
        }, close: function() {
            $(this).dialog().remove();
            $.cookie('session', 'false');
        }, open: function() {
            $('#unit').focus();
            $.cookie('session', 'true');
        }
    });
    $('#tanggal').datepicker({
        changeYear: true,
        changeMonth: true
    });
    $('#save_distribusi').submit(function() {
        var jumlah = $('.tr_rows').length;
        if ($('#id_unit').val() === '') {
            $('#supplier').focus(); return false;
        }
        if (jumlah === 0) {
            $('#barang').focus(); return false;
        }
        $('<div id=alert>Anda yakin akan melakukan transaksi distribusi ini?</div>').dialog({
            title: 'Konfirmasi',
            autoOpen: true,
            modal: true,
            buttons: {
                "OK": function() {
                    $.ajax({
                        url: '<?= base_url('inventory/manage_distribusi/save') ?>',
                        data: $('#save_distribusi').serialize(),
                        dataType: 'json',
                        type: 'POST',
                        success: function(data) {
                            if (data.status === true) {
                                alert_refresh('Data distribusi berhasil di tambahkan !');
                                $('#supplier, #id_supplier').val('');
                                $('#no_sp').val(data.id_distribusi);
                                $('#pesanan-list tbody').html('');
                                $('#estimasi').html('0');
                                load_data_distribusi('','',data.id_distribusi);
                                var perundangan = $('#perundangan1').val();
                                cetak_sp(data.id, perundangan);
                            } else {
                                alert_edit();
                            }
                        }
                    });
                    },
                "Cancel": function() {
                    $(this).dialog().remove();
                }
            }
        });
        return false;
    });
}
$('#button').button({
    icons: {
        secondary: 'ui-icon-newwin'
    }
}).click(function() {
    form_add();
});
$('#reset').button({
    icons: {
        secondary: 'ui-icon-refresh'
    }
}).click(function() {
    load_data_distribusi();
});

function load_data_distribusi(page, search, id) {
    pg = page; src = search; id_barg = id;
    if (page === undefined) { var pg = ''; }
    if (search === undefined) { var src = ''; }
    if (id === undefined) { var id_barg = ''; }
    $.ajax({
        url: '<?= base_url('inventory/manage_distribusi') ?>/list/'+page,
        cache: false,
        data: 'search='+src+'&id='+id_barg,
        success: function(data) {
            $('#result-distribusi').html(data);
        }
    });
}

function paging(page, tab, search) {
    load_data_distribusi(page, search);
}
function delete_distribusi(id, page) {
    $('<div id=alert>Anda yakin akan menghapus data ini?</div>').dialog({
        title: 'Konfirmasi Penghapusan',
        autoOpen: true,
        modal: true,
        buttons: {
            "OK": function() {
                
                $.ajax({
                    url: 'models/update-transaksi.php?method=delete_distribusi&id='+id,
                    cache: false,
                    success: function() {
                        load_data_distribusi(page);
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

<button id="button">Tambah (F9)</button>
<button id="reset">Reset</button>
<div id="result-distribusi">

</div>
