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

load_data_penerimaan_distribusi();

function get_data_no_distribusi() {
    var str = '<div id="list-no-distribusi">'+
        '<table width="100%" class=list-data id="list-no-dist" cellpadding=0 cellspacing=0>'+
            '<thead><tr>'+
                '<th width=10%>No.</th>'+
                '<th width=20%>Waktu</th>'+
                '<th width=40%>Nomor Distribusi</th>'+
                '<th width=10%>Pilih</th>'+
            '</tr></thead>'+
            '<tbody></tbody>'+
        '</table>'+
        '</div>';
    $('body').append(str);
    $('#list-no-distribusi').dialog({
        title: 'Tambah Supplier',
        autoOpen: true,
        width: 480,
        height: $(window).height(),
        modal: true,
        hide: 'clip',
        show: 'blind',
        buttons: {
            "Simpan": function() {
                $('#save_barang').submit();
            }, "Cancel": function() {
                $(this).dialog().remove();
            }
        }, close: function() {
            $(this).dialog().remove();
        }
    });
    $.getJSON('<?= base_url('autocomplete/nomor_distribusi') ?>', function(data) {
        $('#list-no-dist tbody').html('');
        $.each(data, function (index, value) {
            var list = '<tr>'+
                    '<td align=center>'+(index+1)+'</td>'+
                    '<td align=center>'+datefmysql(value.tanggal)+'</td>'+
                    '<td align=center>'+value.id+'</td>'+
                    '<td align=center><a class="choosen" onclick="select_this('+value.id+');">&checkmark;</a></td>'+
                    '</tr>';
            $('#list-no-dist tbody').append(list);
        });
    });
}

function select_this(id) {
    $('#list-no-distribusi').dialog().remove();
    $('#no_dist').val(id);
    $.getJSON('<?= base_url('inventory/load_data_distribusi') ?>/'+id, function(data) {
        $('#pesanan-list tbody').html('');
        $.each(data, function (index, value) {
            var list = '<tr class="'+((index%2===0)?'odd':'even')+' tr_rows">'+
                    '<td align=center>'+(index+1)+'</td>'+
                    '<td>'+value.nama_barang+'</td>'+
                    '<td>'+value.kemasan+'</td>'+
                    '<td align=center>'+value.jumlah+'</td>'+
                    '</tr>';
            $('#pesanan-list tbody').append(list);
        });
    });
}

function form_add_penerimaan_dist() {
    var str = '<div id="form_penerimaan_distribusi">'+
            '<form id="save_penerimaan_distribusi">'+
            '<input type=hidden name=id id=id />'+
            '<table width=100% class=data-input cellpadding=0 cellspacing=0>'+
                '<tr><td width=15%>Tanggal:</td><td><?= form_input('tanggal', date("d/m/Y"), 'id=tanggal size=15') ?></td></tr>'+
                '<tr><td>Nomor Distribusi:</td><td><?= form_input('no_dist', NULL, 'id=no_dist size=40') ?> <a class="addition" onclick="get_data_no_distribusi();">&nbsp;</a></td></tr>'+
            '</table>'+
            '<table width=100% cellspacing="0" class="list-data-input" id="pesanan-list"><thead>'+
                '<tr><th width=5%>No.</th>'+
                    '<th width=43%>Nama Barang</th>'+
                    '<th width=10%>Kemasan</th>'+
                    '<th width=10%>Jumlah</th>'+
                    '</tr></thead>'+
                '<tbody></tbody>'+
            '</table>'+
            '</form></div>';
    $('body').append(str);
    $(document).keydown(function(e) {
        if (e.keyCode === 119) {
            $('#save_penerimaan_distribusi').submit();
        }
    });
    var lebar = $('#no_dist').width();
    $('#no_dist').autocomplete("<?= base_url('autocomplete/nomor_distribusi') ?>",
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
    $('#form_penerimaan_distribusi').dialog({
        title: 'Tambah penerimaan distribusi Barang',
        autoOpen: true,
        modal: true,
        width: 800,
        height: dHeight,
        hide: 'clip',
        show: 'blind',
        buttons: {
            "Simpan (F8)": function() {
                $('#save_penerimaan_distribusi').submit();
            }, 
            "Cancel": function() {    
                $(this).dialog().remove();
                $.cookie('session', 'false');
            }
        }, close: function() {
            $(this).dialog().remove();
            $.cookie('session', 'false');
        }, open: function() {
            $('#no_dist').focus();
            $.cookie('session', 'true');
        }
    });
    $('#tanggal').datepicker({
        changeYear: true,
        changeMonth: true
    });
    $('#save_penerimaan_distribusi').submit(function() {
        var jumlah = $('.tr_rows').length;
        if ($('#no_dist').val() === '') {
            $('#no_dist').focus(); return false;
        }
        if (jumlah === 0) {
            custom_message('Peringatan','Data barang pada distribusi ini tidak ada, Anda tidak dapat melakukan transaksi ini!','#no_dist');
            return false;
        }
        $('<div id=alert>Anda yakin akan melakukan transaksi penerimaan_distribusi ini?</div>').dialog({
            title: 'Konfirmasi',
            autoOpen: true,
            modal: true,
            buttons: {
                "OK": function() {
                    $.ajax({
                        url: '<?= base_url('inventory/manage_penerimaan_distribusi/save') ?>',
                        data: $('#save_penerimaan_distribusi').serialize(),
                        dataType: 'json',
                        type: 'POST',
                        success: function(data) {
                            if (data !== '') {
                                custom_message('Informasi','Data penerimaan_distribusi berhasil di tambahkan !');
                                load_data_penerimaan_distribusi(1,'',data);
                                $('#alert').dialog().remove();
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
$('#button-penerimaan-dist').button({
    icons: {
        secondary: 'ui-icon-newwin'
    }
}).click(function() {
    form_add_penerimaan_dist();
});
$('#reset-penerimaan-dist').button({
    icons: {
        secondary: 'ui-icon-refresh'
    }
}).click(function() {
    load_data_penerimaan_distribusi();
});

function load_data_penerimaan_distribusi(page, search, id) {
    pg = page; src = search; id_barg = id;
    if (page === undefined) { var pg = ''; }
    if (search === undefined) { var src = ''; }
    if (id === undefined) { var id_barg = ''; }
    $.ajax({
        url: '<?= base_url('inventory/manage_penerimaan_distribusi') ?>/list/'+page,
        cache: false,
        data: 'search='+src+'&id='+id_barg,
        success: function(data) {
            $('#result-penerimaan_distribusi').html(data);
        }
    });
}

function paging(page, tab, search) {
    load_data_penerimaan_distribusi(page, search);
}
function delete_penerimaan_distribusi(id, page) {
    $('<div id=alert>Anda yakin akan menghapus data ini?</div>').dialog({
        title: 'Konfirmasi Penghapusan',
        autoOpen: true,
        modal: true,
        buttons: {
            "OK": function() {
                
                $.ajax({
                    url: 'models/update-transaksi.php?method=delete_penerimaan_distribusi&id='+id,
                    cache: false,
                    success: function() {
                        load_data_penerimaan_distribusi(page);
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

<button id="button-penerimaan-dist">Tambah (F9)</button>
<button id="reset-penerimaan-dist">Reset</button>
<div id="result-penerimaan_distribusi">

</div>
