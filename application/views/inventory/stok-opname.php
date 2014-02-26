<title><?= $title ?></title>
<?= $this->load->view('message') ?>
<script type="text/javascript">
$(function() {
    $('#search').keyup(function() {
        var value = $(this).val();
        load_data_stokopname('undefined',value,'');
    });
});
$(document).tooltip();
load_data_stokopname();
function removeMe(el) {
    var parent = el.parentNode.parentNode;
    parent.parentNode.removeChild(parent);
}
function add_new_rows(id_brg, nama_brg, batch, ed, masuk, keluar) {
    var jml = $('.tr_rows').length+1;
    
    var str = '<tr class="tr_rows">'+
                '<td align=center>'+jml+'</td>'+
                '<td>&nbsp;'+nama_brg+' <input type=hidden name=id_barang[] value="'+id_brg+'" class=id_barang id=id_barang'+jml+' /></td>'+
                '<td><input type=text name=nobatch[] id=nobatch'+jml+' value="'+batch+'" /></td>'+
                '<td><input type=text name=ed[] id=ed'+jml+' value="'+ed+'" /></td>'+
                '<td align=center id=sisa'+jml+'></td>'+
                '<td><input type=text name=masuk[] id=masuk'+jml+' value="'+masuk+'" /></td>'+
                '<td><input type=text name=keluar[] id=keluar'+jml+' value="'+keluar+'" /></td>'+
                '<td align=center><img onclick=removeMe(this); title="Klik untuk hapus" src="<?= base_url('assets/images/delete.png') ?>" class=add_kemasan align=left /></td>'+
              '</tr>';
    $('#pesanan-list tbody').append(str);
    $('#ed'+jml).datepicker({
        changeYear: true,
        changeMonth: true,
        minDate: 0
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
    $('#id_barang, #barang, #batch, #ed, #masuk, #pilih').val('');
}

function form_add() {
    var str = '<div id="form_stokopname">'+
            '<form id="save_stokopname">'+
            '<table width=100% class=data-input><tr valign=top><td width=50%><table width=100% cellspacing=0>'+
                '<tr><td>Tanggal:</td><td><?= form_input('tanggal', date("d/m/Y"), 'id=tanggal size=10') ?></td></tr>'+
                '<tr><td>Alasan:</td><td><?= form_input('alasan', NULL, 'id=alasan size=40') ?></td></tr>'+
                '<tr><td width=20%>Nama Barang:</td><td><?= form_input('barang', NULL, 'id=barang size=40') ?><?= form_hidden('id_barang', NULL, 'id=id_barang') ?></td></tr>'+
                '<tr><td>No. Batch:</td><td><?= form_input('batch', NULL, 'id=batch size=10') ?></td></tr>'+
            '</table></td><td width=50%>'+
            '<table width=100% cellspacing=0>'+
                '<tr><td>Expired Date:</td><td><?= form_input('ed', NULL, 'id=ed size=10') ?></td></tr>'+
                '<tr><td>Masuk:</td><td><?= form_input('masuk', NULL, 'id=masuk size=10') ?></td></tr>'+
                '<tr><td>Keluar:</td><td><?= form_input('keluar', NULL, 'id=pilih size=10') ?></td></tr>'+
            '</table>'+
            '</td></tr></table>'+
            '<table width=100% cellspacing="0" class="list-data-input" id="pesanan-list"><thead>'+
                '<tr><th width=5%>No.</th>'+
                    '<th width=43%>Nama Barang</th>'+
                    '<th width=10%>No. Batch</th>'+
                    '<th width=10%>ED</th>'+
                    '<th width=10%>Sisa</th>'+
                    '<th width=10%>Masuk</th>'+
                    '<th width=10%>Keluar</th>'+
                    '<th width=2%>#</th>'+
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
            var batch       = $('#batch').val();
            var ed          = $('#ed').val();
            var masuk       = $('#masuk').val();
            var keluar      = $('#pilih').val();
            if (id_barang !== '') {
                add_new_rows(id_barang, nama, batch, ed, masuk, keluar);
            }
            $('#id_barang').val('');
            $('#barang').val('').focus();
        }
    });
    $('#ed').datepicker({
        changeYear: true,
        changeMonth: true,
        onSelect: function() {
            $('#masuk').focus();
        }
    });
    $('#barang').keydown(function(e) {
        if (e.keyCode === 13) {
            $('#batch').focus();
        }
    });
    $('#batch').keydown(function(e) {
        if (e.keyCode === 13) {
            $('#ed').focus();
        }
    });
    $('#ed').keydown(function(e) {
        if (e.keyCode === 13) {
            $('#masuk').focus();
        }
    });
    $('#masuk').keydown(function(e) {
        if (e.keyCode === 13) {
            $('#pilih').focus();
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
        width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
        dataType: 'json', // tipe data yang diterima oleh library ini disetup sebagai JSON
        cacheLength: 0,
        max: 100
    }).result(
    function(event,data,formated){
        $(this).val(data.nama_barang);
        $('#id_barang').val(data.id);
    });
    var wWidth = $(window).width();
    var dWidth = wWidth * 1;
    
    var wHeight= $(window).height();
    var dHeight= wHeight * 1;
    $('#form_stokopname').dialog({
        title: 'Stok Opname',
        autoOpen: true,
        modal: true,
        width: dWidth,
        height: dHeight,
        hide: 'clip',
        show: 'blind',
        buttons: {
            "Simpan": function() {
                $('#save_stokopname').submit();
            }, 
            "Cancel": function() {    
                $(this).dialog().remove();
            }
        }, close: function() {
            $(this).dialog().remove();
        }, open: function() {
            $('#alasan').focus();
        }
    });
    $('#tanggal').datepicker({
        changeYear: true,
        changeMonth: true
    });
    $('#save_stokopname').submit(function() {
        var jumlah = $('.tr_rows').length;
        
        if (jumlah === 0) {
            alert_empty('Barang', '#barang');
            return false;
        }
        if (jumlah > 0) {
            for (i = 1; i <= jumlah; i++) {
                if ($('#id_barang'+i).val() === '') {
                    alert_empty('Barang','#barang'+i);
                    return false;
                }
                if ($('#nobatch'+i).val() === '') {
                    alert_empty('No. batch','#nobatch'+i);
                    return false;
                }
                if ($('#ed'+i).val() === '') {
                    alert_empty('expired date','#nobatch'+i);
                    return false;
                }
                if ($('#masuk'+i).val() === '' || $('#keluar'+i).val() === '') {
                    alert_empty('masuk & keluar','#masuk'+i);
                    return false;
                }
            }
        }
        $.ajax({
            url: '<?= base_url('inventory/manage_stok_opname/save')?>',
            data: $(this).serialize(),
            dataType: 'json',
            type: 'POST',
            success: function(data) {
                if (data.status === true) {
                    alert_refresh('Stok opname berhasil di tambahkan');
                    $('#pesanan-list tbody').html('');
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
    load_data_stokopname();
});
function load_data_stokopname(page, search, id) {
    pg = page; src = search; id_barg = id;
    if (page === undefined) { var pg = ''; }
    if (search === undefined) { var src = ''; }
    if (id === undefined) { var id_barg = ''; }
    $.ajax({
        url: '<?= base_url('inventory/manage_stok_opname/list') ?>/'+page,
        cache: false,
        data: 'search='+src+'&id='+id_barg,
        success: function(data) {
            $('#result-stokopname').html(data);
        }
    });
}

function paging(page, tab, search) {
    load_data_stokopname(page, search);
}

function edit_stokopname(str) {
    
    var arr = str.split('#');
    form_add();
    $('#form_add').dialog({ title: 'Edit stokopname' });
    $('#id_stokopname').val(arr[0]);
    $('#nama').val(arr[1]);
    if (arr[2] === 'P') { $('#prm').attr('checked','checked'); }
    if (arr[2] === 'L') { $('#l').attr('checked','checked'); }
    $('#alamat').val(arr[3]);
    $('#telp').val(arr[4]);
    $('#email').val(arr[5]);
    $('#nostr').val(arr[6]);
    $('#spesialis').val(arr[7]);
    $('#tglmulai').val(arr[8]);
    
}

function delete_stokopname(id, page) {
    $('<div id=alert>Anda yakin akan menghapus data ini?</div>').dialog({
        title: 'Konfirmasi Penghapusan',
        autoOpen: true,
        modal: true,
        buttons: {
            "OK": function() {
                $.ajax({
                    url: 'models/update-transaksi.php?method=delete_stokopname&id='+id,
                    cache: false,
                    success: function() {
                        load_data_stokopname(page);
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
<div class="titling"><h1><?= $title ?></h1></div>
<div class="kegiatan">
    <button id="button">Tambah Data</button>
    <button id="reset">Reset</button>
    <?= form_input('search', NULL, 'id=search placeholder="Search ..." class=search') ?>
    <div id="result-stokopname">

    </div>
</div>