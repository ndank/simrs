<title><?= $title ?></title>
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

load_data_pemusnahan();

function form_add_karyawan() {
var str = '<div id=form_add_karyawan>'+
            '<form action="" method=post id="save_barang">'+
            '<?= form_hidden('id_karyawan', NULL, 'id=id_karyawan') ?>'+
            '<table width=100% class=data-input>'+
                '<tr><td width=30%>Nama:</td><td width=70%><?= form_input('nama', NULL, 'id=nama size=40 onBlur="javascript:this.value=this.value.toUpperCase();"') ?></td></tr>'+
                '<tr><td>Kelamin:</td><td><input type="radio" name=kelamin value="P" checked id="prm" /> <label for="prm">Perempuan</td><td> <input type="radio" name=kelamin value="L" id="l" /> <label for="l">Laki-laki</td><td></td></tr>'+
                '<tr><td>Tempat / Tgl Lahir:</td><td><input type=text name=tmp_lahir size=5 style="min-width: 200px;" id=tmp_lahir /> / <input type=text name=tgl_lahir style="min-width: 80px;" size=5 id="tgl_lahir" /></td></tr>'+
                '<tr><td>Alamat:</td><td><?= form_input('alamat', NULL, 'id=alamat size=40 onBlur="javascript:this.value=this.value.toUpperCase();"') ?><input type=hidden name="id_pabrik" /></td></tr>'+
                '<tr><td>Kab. / Kodya:</td><td><?= form_input('kabupaten', '', 'id=kabupaten size=40') ?></td></tr>'+
                '<tr><td>Provinsi:</td><td><?= form_input('provinsi', '', 'id=provinsi size=40') ?></td></tr>'+
                '<tr><td>Telp:</td><td><?= form_input('telp', '', 'id=telp size=40') ?></td></tr>'+
                '<tr><td>Email:</td><td><?= form_input('email','', 'id=email size=40') ?></td></tr>'+
                '<tr><td>Jabatan:</td><td><select name=jabatan id=jabatan><option value="APA">APA</option><option value="Kasir">Kasir</option><option value="Staff">Staff</option></select></td></tr>'+
                '<tr><td>No. SIPA:</td><td><?= form_input('sipa', '', 'id=sipa size=40') ?></td></tr>'+
            '</table>'+
            '</form>'+
            '</div>';
    $('body').append(str);
    $('input[type=text]').blur(function() {
        this.value=this.value.toUpperCase();
    });
    $('#form_add_karyawan').dialog({
        title: 'Tambah karyawan',
        autoOpen: true,
        width: 480,
        height: 370,
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
    $('#tgl_lahir').datepicker({
        maxDate: 0,
        changeYear: true,
        changeMonth: true
    });
    $('#save_barang').submit(function() {
        if ($('#nama').val() === '') {
            alert('Nama karyawan tidak boleh kosong !');
            $('#nama').focus(); return false;
        }
        var cek_id = $('#id_karyawan').val();
        $.ajax({
            url: 'models/update-masterdata.php?method=save_karyawan',
            type: 'POST',
            dataType: 'json',
            data: $(this).serialize(),
            cache: false,
            success: function(data) {
                if (data.status === true) {
                    if (cek_id === '') {
                        $('#form_add_karyawan').dialog('close');
                        $('#apoteker').val(data.nama);
                        $('#id_apoteker').val(data.id_karyawan);
                    } 
                }
            }
        });
        return false;
    });
}

function removeMe(el) {
    var parent = el.parentNode.parentNode;
    parent.parentNode.removeChild(parent);
    hitung_estimasi();
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
                '<td align=center>'+kemasan+'<input type=hidden name=kemasan[] id=kemasan'+jml+' value="'+id_kemasan+'" /></td>'+
                '<td align=center><select name=ed[] id=ed'+jml+' class=ed></select></td>'+
                '<td><input type=text name=jumlah[] id=jumlah'+jml+' value="'+jumlah+'" size=10 style="text-align: center;" /><input type=hidden name=hpp[] id="hpp'+jml+'" class=hpp /></td>'+
                '<td align=right id=subtotal'+jml+'></td>'+
                '<td align=center><input type=hidden id=perundangan'+jml+' /><img onclick=removeMe(this); title="Klik untuk hapus" src="img/icons/delete.png" class=add_kemasan align=left /></td>'+
              '</tr>';
    $('#pesanan-list tbody').append(str);
    $.getJSON('models/autocomplete.php?method=get_expiry_barang&id='+id_brg, function(data){
        $('#ed'+jml).html('');
        $.each(data, function (index, value) {
            $('#ed'+jml).append("<option value='"+value.ed+"'>"+datefmysql(value.ed)+"</option>");
        });
    });
    $.ajax({
        url: 'models/autocomplete.php?method=get_detail_hpp&id='+id_brg+'&id_kemasan='+id_kemasan,
        dataType: 'json',
        cache: false,
        success: function(data) {
            $('#subtotal'+jml).html(numberToCurrency(parseInt(data.total_hpp)));
            $('#hpp'+jml).val(parseInt(data.total_hpp));
        }
    });
    
}

function cetak_sp(id_pemusnahan) {
    var perundangan = $('#perundangan1').val();
    var wWidth = $(window).width();
    var dWidth = wWidth * 0.3;
    var wHeight= $(window).height();
    var dHeight= wHeight * 1;
    var x = screen.width/2 - dWidth/2;
    var y = screen.height/2 - dHeight/2;
    window.open('pages/pemusnahan-print.php?id='+id_pemusnahan+'&perundangan='+perundangan, 'pemusnahan Cetak', 'width='+dWidth+', height='+dHeight+', left='+x+',top='+y);
}

function form_add() {
    var str = '<div id="form_pemusnahan">'+
            '<form id="save_pemusnahan">'+
            '<table width=100% class=data-input><tr valign=top><td width=50%><table width=100% cellspacing=0>'+
                '<tr><td width=10%>Tanggal:</td><td><?= form_input('tanggal', date("d/m/Y"), 'id=tanggal size=10') ?></td></tr>'+
                '<tr><td>Saksi Pihak RS:</td><td><?= form_input('apoteker', $this->session->userdata('nama'), 'id=apoteker size=40') ?><?= form_hidden('id_apoteker', $this->session->userdata('id_user'), 'id=id_apoteker') ?> <a class="addition" onclick="form_add_karyawan();" title="Klik untuk tambah apoteker, jika apoteker belum ada">&nbsp;</a></td></tr>'+
                '<tr><td>Saksi BPOM:</td><td width=20%><?= form_input('bpom', NULL, 'id=bpom size=40 placeholder="Jika belum ada tuliskan langsung disini"') ?></td></tr>'+
            '</table></td><td width=50%>'+
                '<table cellspacing=0><tr><td width=20%>Nama Barang:</td><td width=50%><?= form_input('barang', NULL, 'id=barang size=40') ?><?= form_hidden('id_barang', NULL, 'id=id_barang') ?></td></tr>'+
                '<tr><td>Kemasan:</td><td><select name=id_kemasan id=kemasan style="min-width: 86px;"><option value="">Pilih ...</option><?php foreach ($kemasan as $data) { echo '<option value="'.$data->id.'">'.$data->nama.'</option>'; } ?></select></td></tr>'+
                '<tr><td>Jumlah:</td><td><?= form_input('jumlah', NULL, 'id=jumlah size=10') ?></td></tr>'+
            '</table>'+
            '</td></tr></table>'+
            '<table width=100% cellspacing="0" class="list-data-input" id="pesanan-list"><thead>'+
                '<tr><th width=5%>No.</th><th width=40%>Nama Barang</th><th width=20%>Kemasan</th><th width=10%>ED</th><th width=10%>Jumlah</th><th width=10%>HPP</th><th width=1%>#</th></tr></thead>'+
                '<tbody></tbody>'+
            '</table>'+
            '</form></div>';
    $('body').append(str);
    $(document).keydown(function(e) {
        if (e.keyCode === 119) {
            $('#save_pemusnahan').submit();
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
        $.getJSON('<?= base_url('autocomplete/get_kemasan_barang') ?>/'+data.id, function(data){
            if (data === null) {
                alert('Kemasan tidak barang tidak tersedia !');
            } else {
                $.each(data, function (index, value) {
                    $('#kemasan').append("<option value='"+value.id_kemasan+"'>"+value.nama+"</option>");
                });
            }
        });
    });
    $('#apoteker').autocomplete("<?= base_url('autocomplete/apoteker') ?>",
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
        $('#id_apoteker').val(data.id);
    });
    $('#bpom').autocomplete("<?= base_url('autocomplete/bpom') ?>",
    {
        parse: function(data){
            var parsed = [];
            for (var i=0; i < data.length; i++) {
                parsed[i] = {
                    data: data[i],
                    value: data[i].saksi_bpom // nama field yang dicari
                };
            }
            return parsed;
        },
        formatItem: function(data,i,max){
            var str = '<div class=result>'+data.saksi_bpom+'</div>';
            return str;
        },
        width: lebar, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
        dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
    }).result(
    function(event,data,formated){
        $(this).val(data.saksi_bpom);
    });
    var wWidth = $(window).width();
    var dWidth = wWidth * 1;
    
    var wHeight= $(window).height();
    var dHeight= wHeight * 1;
    $('#form_pemusnahan').dialog({
        title: 'Tambah pemusnahan Barang',
        autoOpen: true,
        modal: true,
        width: dWidth,
        height: dHeight,
        hide: 'clip',
        show: 'blind',
        buttons: {
            "Simpan (F8)": function() {
                $('#save_pemusnahan').submit();
            }, 
            "Cancel": function() {    
                $(this).dialog().remove();
                $.cookie('session', 'false');
            }
        }, close: function() {
            $(this).dialog().remove();
            $.cookie('session', 'false');
        }, open: function() {
            $('#apoteker').focus();
            $.cookie('session', 'true');
        }
    });
    $('#tanggal').datepicker({
        changeYear: true,
        changeMonth: true
    });
    $('#save_pemusnahan').submit(function() {
        var jumlah = $('.tr_rows').length;
        if ($('#id_apoteker').val() === '') {
            alert_empty('Apoteker','#apoteker'); return false;
        }
        if ($('#bpom').val() === '') {
            alert_empty('BPOM','#bpom'); return false;
        }
        if (jumlah === 0) {
            alert('Pilih salah satu barang!');
            $('#barang').focus(); return false;
        }
        $.ajax({
            url: 'models/update-transaksi.php?method=save_pemusnahan',
            data: $(this).serialize(),
            dataType: 'json',
            type: 'POST',
            success: function(data) {
                if (data.status === true) {
                    alert_refresh('Pemusnahan barang berhasil dilakukan');
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
    load_data_pemusnahan();
});

function form_pemusnahan() {
    var str = ''+
            
            '';
    $('body').append(str);
}

function load_data_pemusnahan(page, search, id) {
    pg = page; src = search; id_barg = id;
    if (page === undefined) { var pg = ''; }
    if (search === undefined) { var src = ''; }
    if (id === undefined) { var id_barg = ''; }
    $.ajax({
        url: '<?= base_url('inventory/manage_pemusnahan/list') ?>/'+page,
        cache: false,
        data: 'search='+src+'&id='+id_barg,
        success: function(data) {
            $('#result-pemusnahan').html(data);
        }
    });
}

function paging(page, tab, search) {
    load_data_pemusnahan(page, search);
}
function delete_pemusnahan(id, page) {
    $('<div id=alert>Anda yakin akan menghapus data ini?</div>').dialog({
        title: 'Konfirmasi Penghapusan',
        autoOpen: true,
        modal: true,
        buttons: {
            "OK": function() {
                $.ajax({
                    url: '<?= base_url('inventory/manage_pemusnahan/delete') ?>?id='+id,
                    cache: false,
                    success: function() {
                        load_data_pemusnahan(page);
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
    
    <button id="button">Tambah Transaksi (F9)</button>
    <button id="reset">Reset</button>
    <div id="result-pemusnahan">

    </div>
</div>