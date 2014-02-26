<title><?= $title ?></title>
<?php $this->load->view('message') ?>
<script type="text/javascript">
$(function() {
    $('#tabs').tabs();
    $('#awal,#akhir').datepicker({
        changeYear: true,
        changeMonth: true
    });
    $('#search').button({icons: {secondary: 'ui-icon-search'}});
    $('#set').button({icons: {secondary: 'ui-icon-newwin'}}).click(function() {
        create_win_set_awal();
    });
    $('#addjurnal').button({icons: {secondary: 'ui-icon-newwin'}}).click(function() {
        create_win_jurnal();
    });
    $('#setneracaawal').button({icons: {secondary: 'ui-icon-wrench'}});
    $('#reset').button({icons: {secondary: 'ui-icon-refresh'}});
    $('#reset').click(function() {
        $('#loaddata').empty();
        $('#loaddata').load('<?= base_url('akuntansi/bukubesar') ?>');
    }); 
    $('#search').click(function() {
        get_list_data();
    });
    var lebar = $('#rekening').width();
    

    $('#reke').autocomplete("<?= base_url('inv_autocomplete/get_rekening') ?>",
    {
        parse: function(data){
            var parsed = [];
            for (var i=0; i < data.length; i++) {
                parsed[i] = {
                    data: data[i],
                    value: data[i].nama // nama field yang dicari
                };
            }
            $('input[name=id_reke]').val('');
            $('#sub, #sub_sub, #sub_sub_sub').val('');
            $('input[name=id_sub], input[name=id_subsub], input[name=id_subsubsub]').val('');
            return parsed;
        },
        formatItem: function(data,i,max){
            var str = '<div class=result>'+data.nama+'<div>';
            return str;
        },
        max: 500,
        cacheLength: 0,
        width: lebar, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
        dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
    }).result(
    function(event,data,formated){
        $(this).val(data.nama);
        $('input[name=id_reke]').val(data.id);
    });

    $('#sub').autocomplete("<?= base_url('inv_autocomplete/get_subrekening') ?>",
    {
        extraParams :{ 
            id_rekening : function(){
                return $('input[name=id_reke]').val();
            }
        },
        parse: function(data){
            var parsed = [];
            for (var i=0; i < data.length; i++) {
                parsed[i] = {
                    data: data[i],
                    value: data[i].nama // nama field yang dicari
                };
            }
            $('input[name=id_sub]').val('');

            $('#sub_sub, #sub_sub_sub').val('');
            $('input[name=id_subsub], input[name=id_subsubsub]').val('');
            return parsed;
        },
        formatItem: function(data,i,max){
            var str = '<div class=result>'+data.subrekening+'<div>';
            return str;
        },
        max: 500,
        cacheLength: 0,
        width: lebar, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
        dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
    }).result(
    function(event,data,formated){
        $(this).val(data.subrekening);
        $(this).flushCache();
        $('input[name=id_sub]').val(data.id_subrekening);
    });

    $('#sub_sub').autocomplete("<?= base_url('inv_autocomplete/get_subsubrekening') ?>",
    {
        extraParams :{ 
            id_sub : function(){
                return $('input[name=id_sub]').val();
            }
        },
        parse: function(data){
            var parsed = [];
            for (var i=0; i < data.length; i++) {
                parsed[i] = {
                    data: data[i],
                    value: data[i].nama // nama field yang dicari
                };
            }
            $('input[name=id_subsub]').val('');
            $('#sub_sub_sub').val('');
            $('input[name=id_subsubsub]').val('');
            return parsed;
        },
        formatItem: function(data,i,max){
            var str = '<div class=result>'+data.nama+'<div>';
            return str;
        },
        max: 500,
        cacheLength: 0,
        width: lebar, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
        dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
    }).result(
    function(event,data,formated){
        $(this).val(data.nama);
        $(this).flushCache();
        $('input[name=id_subsub]').val(data.id);
    });

    $('#sub_sub_sub').autocomplete("<?= base_url('inv_autocomplete/get_subsubsubrekening') ?>",
    {
        extraParams :{ 
            id_sub_sub : function(){
                return $('input[name=id_subsub]').val();
            }
        },
        parse: function(data){
            var parsed = [];
            for (var i=0; i < data.length; i++) {
                parsed[i] = {
                    data: data[i],
                    value: data[i].nama // nama field yang dicari
                };
            }
            $('input[name=id_subsubsub]').val('');
            $('#kode, #rekening').val('');
            return parsed;
        },
        formatItem: function(data,i,max){
            var str = '<div class=result>'+data.nama+'<div>';
            return str;
        },
        max: 500,
        cacheLength: 0,
        width: lebar, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
        dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
    }).result(
    function(event,data,formated){
        $(this).val(data.nama);
        $(this).flushCache();
        $('input[name=id_subsubsub]').val(data.id);
    });

    $('#rekening, #kode').autocomplete("<?= base_url('inv_autocomplete/get_sub_sub_sub_subrekening') ?>",
    {
        extraParams :{ 
            id_sub_sub_sub : function(){
                return $('input[name=id_subsubsub]').val();
            }
        },
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
            var str = '<div class=result>'+data.id_rekening+'.'+data.id_sub_rekening+'.'+data.id_sub_sub_rekening+'.'+data.id_sub_sub_sub_rekening+'.'+data.id_sub_sub_sub_sub_rekening+'<br/>'+data.sub_sub_sub_sub_rekening+'</div>';
            return str;
        },
        max: 500,
        cacheLength: 0,
        width: lebar, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
        dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
    }).result(
    function(event,data,formated){
        $(this).flushCache();
        $('#kode').val(data.id_rekening+'.'+data.id_sub_rekening+'.'+data.id_sub_sub_rekening+'.'+data.id_sub_sub_sub_rekening+'.'+data.id_sub_sub_sub_sub_rekening);
        $('#rekening').val(data.sub_sub_sub_sub_rekening);
    });


});

function reset_form() {
    $('#rekening').val('');
    $('#pairing').val('');
    $('input[name=reference]').val('');
    $('#debet,#kredit').removeAttr('checked');
    $('#debet').attr('checked','checked');
    $('#nilai').val('');
}

function create_win_set_awal() {
    var str = '<div class="data-input" title="Set Saldo Neraca Awal" id="opennewwind_set">'+
            '<form action="akuntansi/set_awal_neraca" id="metallica" method=post>'+
            '<input type=hidden name=id_jurnal id=id_jurnal />'+
            '<tr><td>Waktu:</td><td><span class=label id=time><?= date("d/m/Y H:i:s") ?></span>'+
            '<tr><td>Nama Rekening:</td><td><?= form_input('', NULL, 'id=rek size=80') ?><input type=hidden name=id_rek />'+
            '<tr><td>Kode:</td><td><?= form_input('kodes', NULL, 'id=kodes size=80') ?>'+
            '<tr><td>Jenis Transaksi:</td><td><select name=jenis_trans id="jenis_trans"><option value="Neraca Awal Aset">Neraca Awal Aset</option><option value="Neraca Awal Modal">Neraca Awal Modal</option><option value="Neraca Awal Kewajiban">Neraca Awal Kewajiban</option></select>'+
            '<tr><td>Nilai:</td><td><?= form_input('nilai', NULL, 'id=nilai onblur=FormNum(this) size=20') ?>'+
            '<tr><td>Jenis:</td><td><span class=label><?= form_radio('jeniss', 'debet', TRUE, 'id=debet') ?> Debet</span> <span class=label><?= form_radio('jeniss', 'kredit', FALSE, 'id=kredit') ?> Kredit</span>'+
            '</form>'+
            '</div>';
        $('#loaddata').append(str);
        
        $('#opennewwind_set').dialog({
            autoOpen: true,
            modal: true,
            width: 850,
            height: 310,
            open: function() {
                $('#rek').focus();
                $('#tanggal').datetimepicker();
            },
            close: function() {
                $('#tanggal').remove();
                $('#opennewwind_set').dialog().remove();
            },
            buttons: {
                "Simpan": function() {
                    $.ajax({
                        type: 'POST',
                        url: $('#metallica').attr('action'),
                        cache: false,
                        dataType: 'json',
                        data: $('#metallica').serialize(),
                        success: function(data) {
                            if (data.status === true) {
                                if ($("#id_jurnal").val() === '') {
                                    alert_tambah();
                                    $('#opennewwind_set').dialog().remove();
                                    get_list_data('undefined', 1);
                                } else {
                                    alert_edit();
                                    $('#opennewwind_set').dialog().remove();
                                    get_list_data('undefined', 1, data.id);
                                }
                            }
                        },
                        error: function() {
                            alert_tambah_failed();
                        }
                    });
                },
                "Batal": function() {
                    $('#tanggal').remove();
                    $('#opennewwind_set').dialog().remove();
                }
            }
        });
        var lebar = $('#rek').width();
        $('#rek, #kodes').autocomplete("<?= base_url('inv_autocomplete/get_sub_sub_sub_subrekening') ?>",
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
                //'+data.rekening+' - '+data.sub_rekening+' - '+data.sub_sub_rekening+' - '+data.sub_sub_sub_rekening+' - 
                var str = '<div class=result>'+data.id_rekening+'.'+data.id_sub_rekening+'.'+data.id_sub_sub_rekening+'.'+data.id_sub_sub_sub_rekening+'.'+data.id_sub_sub_sub_sub_rekening+'<br/>'+data.sub_sub_sub_sub_rekening+'</div>';
                return str;
            },
            max: 500,
            width: lebar, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
            dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
        }).result(
        function(event,data,formated){
            $('#kodes').val(data.id_rekening+'.'+data.id_sub_rekening+'.'+data.id_sub_sub_rekening+'.'+data.id_sub_sub_sub_rekening+'.'+data.id_sub_sub_sub_sub_rekening);
            $('input[name=id_rek]').val(data.id_sub_sub_sub_sub_rekening);
            $('#rek').val(data.sub_sub_sub_sub_rekening);
        });
}

function edit_jurnal(data) {
    var arr = data.split('#');
    create_win_set_awal();
    $('#id_jurnal').val(arr[7]);
    $('#time').html(arr[0]);
    $('#rek').val(arr[1]);
    $('input[name=id_rek]').val(arr[5]);
    $('#kodes').val(arr[2]);
    
    var option = 
        '<option value="Neraca Awal Aset">Neraca Awal Aset</option>'+
        '<option value="Neraca Awal Modal">Neraca Awal Modal</option>'+
        '<option value="Neraca Awal Kewajiban">Neraca Awal Kewajiban</option>'+
        '<option value="Stok Opname">Stok Opname</option>'+
        '<option value="Pembelian">Pembelian</option>'+
        '<option value="Retur Pembelian">Retur Pembelian</option>'+
        '<option value="Penerimaan Retur Pembelian">Penerimaan Retur Pembelian</option>'+
        '<option value="Pemusnahan">Pemusnahan</option>'+
        '<option value="Penjualan">Penjualan</option>'+
        '<option value="Retur Penjualan">Retur Penjualan</option>'+
        '<option value="Pengeluaran Retur Penjualan">Pengeluaran Retur Penjualan</option>'+
        '<option value="Inkaso">Inkaso</option>'+
        '<option value="Pembayaran Billing Pasien">Pembayaran Billing Pasien</option>'+
        '<option value="Pengeluaran Retur Penjualan">Pengeluaran Retur Penjualan</option>'+
        '<option value="Penerimaan Retur Pembelian">Penerimaan Retur Pembelian</option>'+
        '<option value="Penerimaan dan Pengeluaran">Penerimaan dan Pengeluaran</option>';
    $('#jenis_trans').html(option);
    $('#jenis_trans').val(arr[6]);
    if (arr[3] === '0') {
        $('#nilai').val(arr[4]);
        $('#kredit').attr('checked', 'checked');
    }
    if (arr[4] === '0') {
        $('#nilai').val(arr[3]);
        $('#debet').attr('checked', 'checked');
    }
    
}

function create_win_jurnal() {
    var str = '<div class="data-input" id="opennewwind">'+
        '<form action="akuntansi/jurnal_save" id="form_jurnal">'+
        '<tr><td>Waktu:</td><td><span class="label"><?= date("d/m/Y H:i:s") ?></span>'+
        '<tr><td><b>Transaksi</b></td><td>'+
        '<tr><td>ID. Transaksi:</td><td><?= form_input('id_transaksi', get_last_id('jurnal', 'id_transaksi'), 'id=id_transaksi') ?>'+
        '<tr><td>Jenis Transaksi:</td><td><select name="jenis_transaksi" id="jenis_transaksi"><?php foreach ($jenis_transaksi as $rows) { echo '<option value="'.$rows.'">'.$rows.'</option>'; } ?></select>'+
        '<tr><td>Ket. Transaksi:</td><td><?= form_textarea('keterangan','','style="width: 580px;"') ?>'+
        '<tr><td><b>Kolom Debet</b></td><td>'+
        '<tr><td>Nama Rekening:</td><td><?= form_input('rekening', NULL, 'id=rekening_s size=80') ?>'+
        '<tr><td>Kode Rek.:</td><td><?= form_input('ref', null, 'id=pairing size=80') ?><input type=hidden name=reference />'+
        '<tr><td>Nilai (Rp.):</td><td><?= form_input('nilai', NULL, 'id=nilai onkeyup="FormNum(this)"') ?>'+
        '<tr><td><b>Kolom Kredit</b></td><td>'+
        '<tr><td>Nama Rekening:</td><td><?= form_input('rekening2', NULL, 'id=rekening_s2 size=80') ?>'+
        '<tr><td>Kode Rek.:</td><td><?= form_input('ref2', null, 'id=pairing2 size=80') ?><input type=hidden name=reference2 />'+
        '<tr><td>Nilai (Rp.):</td><td><span class="label" id="nilai_label"></span>'+
        '</form>'+
    '</div>';
    $('#loaddata').append(str);
    $('#nilai').blur(function() {
        $('#nilai_label').html($(this).val());
    });
    $('#opennewwind').dialog({
        autoOpen: true,
        width: 850,
        height: 550,
        title: 'Tambah Transaksi Jurnal',
        modal: true,
        open: function() {
            $('#jenis_transaksi').focus();
        },
        close: function() {
            $('#tanggal').remove();
            $(this).dialog().remove();
        },
        buttons: {
            "Simpan": function() {
                $('#form_jurnal').submit();
            },
            "Reset": function() {
                
                $('#form_jurnal').each(function(){
                    this.reset();
                });
            }
        }
    });
    $('#form_jurnal').submit(function() {
        if ($('#jenis_transaksi').val() === 'Pilih ...') {
            custom_message('Peringatan','Jenis transaksi harus dipilih !');
            $('#jenis_transaksi').focus();
            return false;
        }
        if ($('input[name=reference]').val() === '') {
            custom_message('Peringatan','Rekening debet tidak boleh kosong !');
            $('#rekening_s').focus();
            return false;
        }
        if ($('#nilai').val() === '') {
            custom_message('Peringatan','Nilai tidak boleh kosong !');
            $('#nilai').focus();
            return false;
        }
        if ($('input[name=reference2]').val() === '') {
            custom_message('Peringatan','Rekening kredit tidak boleh kosong !');
            $('#rekening_s2').focus();
            return false;
        }
        $.ajax({
            url: $(this).attr('action'),
            data: $(this).serialize(),
            type: 'POST',
            cache: false,
            dataType: 'json',
            success: function() {
               alert_tambah();
               get_list_data(true, 2);
               $('#opennewwind').dialog().remove();
            }
        });
        return false;
    });
    var lebar = $('#pairing').width();
    $('#pairing, #rekening_s').autocomplete("<?= base_url('inv_autocomplete/get_sub_sub_sub_subrekening') ?>",
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
            //'+data.rekening+' - '+data.sub_rekening+' - '+data.sub_sub_rekening+' - '+data.sub_sub_sub_rekening+' - 
            var str = '<div class=result>'+data.id_rekening+'.'+data.id_sub_rekening+'.'+data.id_sub_sub_rekening+'.'+data.id_sub_sub_sub_rekening+'.'+data.id_sub_sub_sub_sub_rekening+'<br/>'+data.sub_sub_sub_sub_rekening+'</div>';
            return str;
        },
        max: 500,
        width: lebar, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
        dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
    }).result(
    function(event,data,formated){
        $('#pairing').val(data.id_rekening+'.'+data.id_sub_rekening+'.'+data.id_sub_sub_rekening+'.'+data.id_sub_sub_sub_rekening+'.'+data.id_sub_sub_sub_sub_rekening);
        $('input[name=reference]').val(data.id_sub_sub_sub_sub_rekening);
        $('#rekening_s').val(data.sub_sub_sub_sub_rekening);
    });
    $('#pairing2, #rekening_s2').autocomplete("<?= base_url('inv_autocomplete/get_sub_sub_sub_subrekening') ?>",
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
            var str = '<div class=result>'+data.id_rekening+'.'+data.id_sub_rekening+'.'+data.id_sub_sub_rekening+'.'+data.id_sub_sub_sub_rekening+'.'+data.id_sub_sub_sub_sub_rekening+'<br/>'+data.sub_sub_sub_sub_rekening+'</div>';
            return str;
        },
        max: 500,
        width: lebar, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
        dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
    }).result(
    function(event,data,formated){
        $('#pairing2').val(data.id_rekening+'.'+data.id_sub_rekening+'.'+data.id_sub_sub_rekening+'.'+data.id_sub_sub_sub_rekening+'.'+data.id_sub_sub_sub_sub_rekening);
        $('input[name=reference2]').val(data.id_sub_sub_sub_sub_rekening);
        $('#rekening_s2').val(data.sub_sub_sub_sub_rekening);
        
        
    });
    
    $('#tanggal').datetimepicker();
}

function get_list_data(last, limit, id) {
    var awal = $('#awal').val();
    var akhir= $('#akhir').val();
    var rekening = $('#rekening').val();
    var kode = $('#kode').val();
    $.ajax({
        url: '<?= base_url('akuntansi/list_bukubesar') ?>/'+id,
        data: $('#form').serialize()+'&last='+last+'&page=1&batas='+limit,
        cache: false,
        success: function(data) {
            $('#result').html(data);
        }
    });
}

function paging(page) {
    
    var awal = $('#awal').val();
    var akhir= $('#akhir').val();
    var rekening = $('#rekening').val();
    var kode = $('#kode').val();
    $.ajax({
        url: '<?= base_url('akuntansi/list_bukubesar') ?>',
        data: $('#form').serialize()+'&page='+page,
        cache: false,
        success: function(data) {
            $('#result').html(data);
        }
    });
}
</script>
<div class="titling"><h1><?= $title ?></h1></div>
<div class="kegiatan">
    <div id="tabs">
        <ul>
            <li><a href="#tabs-1">Parameter Pencarian</a></li>
        </ul>
        <div id="tabs-1">
            <?= form_open('', 'id=form')?>
            <table width="100%" class="inputan">
                <tr><td>Range Waktu:</td><td><?= form_input('awal', date("d/m/Y"), 'id=awal style="width: 75px;" size=12') ?> s . d <?= form_input('akhir', date("d/m/Y"), ' style="width: 75px;" id=akhir size=12') ?></td></tr>
                <tr><td>Nama Transaksi</td><td><?= form_dropdown('jenis_transaksi',$jenis_trx,null, 'id=jenis_trx') ?></td></tr>
                <tr><td>Jenis:</td><td><?= form_input('reke','','id=reke size=40') ?><?= form_hidden('id_reke') ?></td></tr>
                <tr><td>Sub Jenis:</td><td><?= form_input('sub','','id=sub size=40') ?><?= form_hidden('id_sub') ?></td></tr>
                <tr><td>Sub Sub Jenis:</td><td><?= form_input('sub_sub','','id=sub_sub size=40') ?><?= form_hidden('id_subsub') ?></td></tr>
                <tr><td>Sub Sub Sub Jenis:</td><td><?= form_input('sub_sub_sub','','id=sub_sub_sub size=40') ?><?= form_hidden('id_subsubsub') ?></td></tr>
                <tr><td>Nama:</td><td><?= form_input('rekening', NULL, 'id=rekening size=40') ?></td></tr>
                <tr><td>Kode Rekening:</td><td><?= form_input('kode', NULL, 'id=kode size=40') ?></td></tr>
                <tr><td></td><td>
                    <?= form_button(NULL, 'Cari', 'id=search') ?> 
                    <?= form_button(null, 'Reset', 'id=reset') ?>
                    <?= form_button('', 'Add Jurnal Transaction', 'id=addjurnal') ?>
                    <?= form_button(null, 'Setting Awal Neraca', 'id=set') ?>
                    </td></tr>
            </table>
            <?= form_close() ?>
        </div>
    </div>
    <div id="result">
        
    </div>
</div>