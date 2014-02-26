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

load_data_pemesanan();

function generate_new_sp() {
    $.ajax({
        url: '<?= base_url('autocomplete/generate_new_sp') ?>',
        dataType: 'json',
        data: 'tanggal='+$('#tanggal').val(),
        success: function(data) {
            $('#no_sp').val(data.sp);
        }
    });
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
}

function cetak_sp(id_pemesanan, perundangan) {
    var wWidth = $(window).width();
    var dWidth = wWidth * 0.4;
    var wHeight= $(window).height();
    var dHeight= wHeight * 1;
    var x = screen.width/2 - dWidth/2;
    var y = screen.height/2 - dHeight/2;
    window.open('<?= base_url('inventory/cetak_sp') ?>?id='+id_pemesanan+'&perundangan='+perundangan, 'Pemesanan Cetak', 'width='+dWidth+', height='+dHeight+', left='+x+',top='+y);
}

function form_add() {
    var str = '<div id="form_pemesanan">'+
            '<form id="save_pemesanan">'+
            '<input type=hidden name=id id=id />'+
            '<table width=100% class=data-input cellpadding=0 cellspacing=0><tr valign=top><td width=50%><table width=100% cellpadding=0 cellspacing=0>'+
                '<tr><td width=15%>No. SP:</td><td width=30%><?= form_input('no_sp', NULL, 'id=no_sp size=15 readonly') ?></td></tr>'+
                '<tr><td>Tanggal Pembuatan SP:</td><td><?= form_input('tanggal', date("d/m/Y"), 'id=tanggal size=15') ?></td></tr>'+
                '<tr><td>Tanggal Diharapkan Datang:</td><td><?= form_input('tanggal_datang', date("d/m/Y"), 'id=tanggal_datang size=15') ?></td></tr>'+
                '<tr><td>Supplier:</td><td width=20%><?= form_input('supplier', NULL, 'id=supplier size=40') ?><?= form_hidden('id_supplier', NULL, 'id=id_supplier') ?></td></tr>'+
            '</table></td><td width=50%>'+
                '<table cellpadding=0 cellspacing=0><tr><td width=20%>Nama Barang:</td><td width=50%><?= form_input('barang', NULL, 'id=barang size=40') ?><input type=hidden name=id_barang id=id_barang /></td></tr>'+
                '<tr><td>Kemasan:</td><td><select name=id_kemasan id=kemasan><option value="">Pilih Kemasan...</option></select></td></tr>'+
                '<tr><td>Jumlah:</td><td><?= form_input('jumlah', NULL, 'id=jumlah size=10 onblur="Angka(this);"') ?></td></tr>'+
                '<tr><td>Estimasi Harga:</td><td style="font-size: 40px;"><span>Rp</span> <span id=estimasi style="font-size: 40px;">0</span>, 00</td></tr>'+
            '</table>'+
            '</td></tr></table>'+
            '<table width=100% cellspacing="0" class="list-data-input" id="pesanan-list"><thead>'+
                '<tr><th width=5%>No.</th>'+
                    '<th width=43%>Nama Barang</th>'+
                    '<th width=20%>Kemasan</th>'+
                    '<th width=10%>Jumlah</th>'+
                    '<th width=10%>HNA</th>'+
                    '<th width=10%>Subtotal</th>'+
                    '<th width=2%>#</th></tr></thead>'+
                '<tbody></tbody>'+
            '</table>'+
            '</form></div>';
    $('body').append(str);
    $(document).keydown(function(e) {
        if (e.keyCode === 119) {
            $('#save_pemesanan').submit();
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
    $('#supplier').autocomplete("<?= base_url('autocomplete/supplier') ?>",
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
            var str = '<div class=result>'+data.nama+'<br/> '+data.alamat+'</div>';
            return str;
        },
        width: lebar, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
        dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
    }).result(
    function(event,data,formated){
        $(this).val(data.nama);
        $('#id_supplier').val(data.id);
        $('#barang').focus();
    });
    $('#tanggal,#tanggal_datang').datepicker({
        changeYear: true,
        changeMonth: true,
        onSelect: function() {
            generate_new_sp();
        }
    });
    var wWidth = $(window).width();
    var dWidth = wWidth * 1;
    
    var wHeight= $(window).height();
    var dHeight= wHeight * 1;
    $('#form_pemesanan').dialog({
        title: 'Tambah Pemesanan Barang',
        autoOpen: true,
        modal: true,
        width: dWidth,
        height: dHeight,
        hide: 'clip',
        show: 'blind',
        buttons: {
            "Simpan (F8)": function() {
                $('#save_pemesanan').submit();
            }, 
            "Cancel": function() {    
                $(this).dialog().remove();
                $.cookie('session', 'false');
            }
        }, close: function() {
            $(this).dialog().remove();
            $.cookie('session', 'false');
        }, open: function() {
            $('#supplier').focus();
            $.cookie('session', 'true');
            generate_new_sp();
        }
    });
    $('#save_pemesanan').submit(function() {
        var jumlah = $('.tr_rows').length;
        if ($('#id_supplier').val() === '') {
            alert('Nama supplier tidak boleh kosong !');
            $('#supplier').focus(); return false;
        }
        if (jumlah === 0) {
            alert('Pilih salah satu barang!');
            $('#barang').focus(); return false;
        }
        $('<div id=alert>Anda yakin akan melakukan transaksi pemesanan ini?</div>').dialog({
            title: 'Konfirmasi',
            autoOpen: true,
            modal: true,
            buttons: {
                "OK": function() {
                    $.ajax({
                        url: '<?= base_url('inventory/manage_pemesanan/save') ?>',
                        data: $('#save_pemesanan').serialize(),
                        dataType: 'json',
                        type: 'POST',
                        success: function(data) {
                            if (data.status === true) {
                                alert_refresh('Data pemesanan berhasil di tambahkan !');
                                $('#supplier, #id_supplier').val('');
                                $('#no_sp').val(data.id_pemesanan);
                                $('#pesanan-list tbody').html('');
                                $('#estimasi').html('0');
                                load_data_pemesanan('','',data.id_pemesanan);
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
    load_data_pemesanan();
});

function load_data_pemesanan(page, search, id) {
    pg = page; src = search; id_barg = id;
    if (page === undefined) { var pg = ''; }
    if (search === undefined) { var src = ''; }
    if (id === undefined) { var id_barg = ''; }
    $.ajax({
        url: '<?= base_url('inventory/manage_pemesanan') ?>/list/'+page,
        cache: false,
        data: 'search='+src+'&id='+id_barg,
        success: function(data) {
            $('#result-pemesanan').html(data);
        }
    });
}

function paging(page, tab, search) {
    load_data_pemesanan(page, search);
}
function delete_pemesanan(id, page) {
    $('<div id=alert>Anda yakin akan menghapus data ini?</div>').dialog({
        title: 'Konfirmasi Penghapusan',
        autoOpen: true,
        modal: true,
        buttons: {
            "OK": function() {
                
                $.ajax({
                    url: 'models/update-transaksi.php?method=delete_pemesanan&id='+id,
                    cache: false,
                    success: function() {
                        load_data_pemesanan(page);
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

<button id="button">SP Baru (F9)</button>
<button id="reset">Reset</button>
<div id="result-pemesanan">

</div>
