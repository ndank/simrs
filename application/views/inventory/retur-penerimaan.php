<script type="text/javascript">
$(function() {
    load_data_retur_penerimaan();
    $(document).keydown(function(e) {
        if (e.keyCode === 120) {
            form_add();
        }
    });
});

function removeMe(el) {
    var parent = el.parentNode.parentNode;
    parent.parentNode.removeChild(parent);
}
function load_list_data() {
    var val_kemasan = $('#kemasan').val().split('-');
    var nama_barang = $('#barang').val();
    var id_barang   = $('#id_barang').val();
    var kemasan     = $('#kemasan option:selected').text();
    var id_kemasan  = val_kemasan[1];
    var isi_jumlah  = val_kemasan[0];
    var ed          = $('#ed').val();
    var jumlah      = $('#pilih').val();
    var no   = $('.tr_rows').length+1;
    var list = '<tr class=tr_rows>'+
                    '<td align=center>'+no+'</td>'+
                    '<td><input type=text name=barang value="'+nama_barang+'" id=barang'+no+' size=50 /> <input type=hidden name=id_barang[] id=id_barang'+no+' value="'+id_barang+'" /></td>'+
                    '<td><span id=kemasan'+no+'>'+kemasan+'</span><input type=hidden name=id_kemasan[] id=id_kemasan'+no+' value="'+id_kemasan+'" /></td>'+
                    '<td><input type=text name=ed[] id=ed'+no+' value="'+ed+'" size=10 style="text-align: center;" /></td>'+
                    '<td><input type=hidden name=jml_isi[] id=jml_isi'+no+' value="'+isi_jumlah+'" /><input type=text name=jumlah[] id=jumlah'+no+' value="'+jumlah+'" size=10 style="text-align: center;" /></td>'+
                    '<td align=center class=aksi><img src="img/icons/delete.png" align=left title="Klik untuk hapus" onclick="removeMe(this);" /></td>'+
               '</tr>';
    $('#retur_penerimaan-list tbody').append(list);
    $('input:text').on('keydown', function(e) {
        var n = $("input:text").length;
        if (e.keyCode === 13) {
            var nextIndex = $('input:text').index(this) + 1;
            if (nextIndex < n) {
                $('input:text')[nextIndex].focus();
            } else {
                $('input:text')[nextIndex-n].focus();
            }
        }
    });
    $('#barang,#id_barang,#ed,#pilih').val('');
    $('#kemasan').html('').append('<option value="">Pilih ...</option>');
    $('#barang').focus();
    $('#ed'+no).datepicker({
        changeMonth: true,
        changeYear: true
    });
}

function form_add() {
    var str = '<div id="retur_penerimaan"><form id="save_retur_penerimaan">'+
                '<input type=hidden name=id_retur_penerimaan id=id_retur_penerimaan />'+
                '<table width=100% class=data-input><tr valign=top><td width=50%>'+
                    '<table width=100%>'+
                        '<tr><td>Tanggal:</td><td><input type=text value="<?= date("d/m/Y") ?>" name=tanggal id=tanggal size=10 /></td></tr>'+
                        '<tr><td>Supplier:</td><td><input type=text name=supplier id=supplier size=40 /><input type=hidden name=id_supplier id=id_supplier /></td></tr>'+
                        '<tr><td width=20%>Nama Barang:</td><td><?= form_input('barang', NULL, 'id=barang size=40') ?><?= form_hidden('id_barang', NULL, 'id=id_barang') ?></td></tr>'+
                        '<tr><td>Kemasan:</td><td><select name=id_kemasan id=kemasan style="min-width: 86px;"><option value="">Pilih ...</option></select></td></tr>'+
                        '<tr><td>Expired Date:</td><td><input type=text name=ed id=ed size=10 /><?= form_hidden(NULL, NULL, 'id=nobatch') ?></td></tr>'+
                        '<tr><td>Jumlah:</td><td><input type=text size=10 id=pilih /></td></tr>'+
                    '</table>'+
                    '</td><td width=50%>'+
                    
                '</td></tr></table>'+
                '<table width=100% cellspacing="0" class="list-data-input" id="retur_penerimaan-list"><thead>'+
                    '<tr>'+
                        '<th width=8%>No.</th>'+
                        '<th width=60%>Nama Barang</th>'+
                        '<th width=10%>Kemasan</th>'+
                        '<th width=10%>ED</th>'+
                        '<th width=10%>Jumlah</th>'+
                        '<th width=2%>#</th>'+
                    '</tr></thead>'+
                    '<tbody></tbody>'+
                '</table>'+
              '</form></div>';
    $('body').append(str);
    var wWidth = $(window).width();
    var dWidth = wWidth * 0.7;
    
    var wHeight= $(window).height();
    var dHeight= wHeight * 1;
    $('#retur_penerimaan').dialog({
        title: 'Retur Penerimaan',
        autoOpen: true,
        modal: true,
        width: dWidth,
        height: dHeight,
        hide: 'clip',
        show: 'blind',
        buttons: {
            "Simpan": function() {
                $('#save_retur_penerimaan').submit();
            }, 
            "Cancel": function() {    
                $(this).dialog().remove();
            }
        }, close: function() {
            $(this).dialog().remove();
        }, open: function() {
            $('#supplier').focus();
        }
    });
    
    $('#pilih').keydown(function(e) {
        if (e.keyCode === 13) {
            if ($('#id_barang').val() === '') {
                alert_empty('Barang','#barang'); return false;
            }
            if ($('#kemasan').val() === '') {
                alert_empty('Kemasan','#kemasan'); return false;
            }
            if ($('#ed').val() === '') {
                alert_empty('Expired date','#ed'); return false;
            }
            if ($('#pilih').val() === '') {
                alert_empty('Jumlah','#pilih'); return false;
            }
            load_list_data();
        }
    });
    $('#kemasan').keydown(function(e) {
        if (e.keyCode === 13) {
            if ($('#ed').val() === '') {
                $('#ed').focus();
            } else {
                $('#pilih').val('1').focus().select();
            }
        }
    });
    $('#kemasan').change(function() {
        if ($('#ed').val() === '') {
            $('#ed').focus();
        } else {
            $('#pilih').val('1').focus().select();
        }
    }); 
    var lebar = $('#supplier').width();
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
        $('#kemasan').html('');
        $.getJSON('models/autocomplete.php?method=get_kemasan_barang&id='+data.id, function(data){
            if (data === null) {
                alert('Kemasan tidak barang tidak tersedia !');
            } else {
                $.each(data, function (index, value) {
                    $('#kemasan').append("<option value='"+value.jml_isi+"-"+value.id_kemasan+"'>"+value.nama+"</option>");
                });
            }
            $('#kemasan').focus();
        });
    });
    $('#supplier').autocomplete("models/autocomplete.php?method=supplier",
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
        $('#barang').focus().select();
    });
    $('#tanggal').datepicker();
    $('#ed').datepicker({
        changeYear: true,
        changeMonth: true,
        onSelect: function() {
            $('#pilih').val('1').focus().select();
        }
    });
    $('#save_retur_penerimaan').submit(function() {
        if ($('#id_supplier').val() === '') {
            alert_empty('Supplier','#supplier'); return false;
        }
        $.ajax({
            url: 'models/update-transaksi.php?method=save_retur_penerimaan',
            type: 'POST',
            dataType: 'json',
            data: $('#save_retur_penerimaan').serialize(),
            cache: false,
            success: function(data) {
                if (data.status === true) {
                    if (data.action === 'add') {
                        alert_refresh('Transaksi retur penerimaan berhasil dilakukan!');
                        $('#supplier, #id_supplier').val('');
                        load_data_retur_penerimaan();
                        $('#retur_penerimaan-list tbody').html('');
                    } else {
                        alert_refresh('Update transaksi retur penerimaan berhasil dilakukan!');
                        load_data_retur_penerimaan();
                    }
                }
            }
        });
        return false;
    });
}
$(function() {
    $('#tabs').tabs();
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
        load_data_retur_penerimaan();
    });
});
function load_data_retur_penerimaan(page, search, id) {
    pg = page; src = search; id_barg = id;
    if (page === undefined) { var pg = ''; }
    if (search === undefined) { var src = ''; }
    if (id === undefined) { var id_barg = ''; }
    $.ajax({
        url: 'pages/retur_penerimaan-list.php',
        cache: false,
        data: 'page='+pg+'&search='+src+'&id_retur_penerimaan='+id_barg,
        success: function(data) {
            $('#result-retur_penerimaan').html(data);
        }
    });
}

function paging(page, tab, search) {
    load_data_retur_penerimaan(page, search);
}
function delete_retur_penerimaan(id, page) {
    $('<div id=alert>Anda yakin akan menghapus data ini?</div>').dialog({
        title: 'Konfirmasi Penghapusan',
        autoOpen: true,
        modal: true,
        buttons: {
            "OK": function() {
                
                $.ajax({
                    url: 'models/update-transaksi.php?method=delete_retur_penerimaan&id='+id,
                    cache: false,
                    success: function() {
                        load_data_retur_penerimaan(page);
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

function edit_retur_penerimaan(data) {
    var arr = data.split('#');
    form_add();
    $('#id_retur_penerimaan').val(arr[0]);
    $('#tanggal').val(arr[1]);
    $('#id_supplier').val(arr[2]);
    $('#supplier').val(arr[3]);
    $.getJSON('models/autocomplete.php?method=get_data_retur_penerimaan&id='+arr[0], function(data){
        $.each(data, function (index, value) {
            load_list_data();
            $('#id_barang'+(index+1)).val(value.id_barang);
            $('#barang'+(index+1)).val(value.nama_barang);
            $('#kemasan'+(index+1)).html(value.kemasan);
            $('#id_kemasan'+(index+1)).val(value.id_kemasan);
            $('#ed'+(index+1)).val(datefmysql(value.expired));
            $('#jml_isi'+(index+1)).val(value.isi);
            $('#jumlah'+(index+1)).val(value.jumlah);
        });
    });
}

</script>
<title><?= $title ?></title>
<div class="titling"><h1><?= $title ?></h1></div>
<div class="kegiatan">
    <div id="tabs">
        <ul>
            <li><a href="#tabs-1">Retur Penerimaan</a></li>
        </ul>
        <div id="tabs-1">
        <button id="button">Tambah Retur (F9)</button>
        <button id="reset">Reset</button>
            <div id="result-retur_penerimaan">
                <i>Under construction</i>
            </div>
        </div>
    </div>
</div>