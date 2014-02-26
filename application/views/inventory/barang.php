<title><?= $title ?></title>
<div class="titling"><h1><?= $title ?></h1></div>
<?= $this->load->view('message') ?>
<script type="text/javascript">
$(function() {
    $('#tabs-utama').tabs();
    load_data_barang();
    load_data_barang_pelengkap();
    $('#search').keyup(function() {
        var value = $(this).val();
        load_data_barang('undefined',value,'');
    });
    $('#search-pelengkap').keyup(function() {
        var value = $(this).val();
        load_data_barang_pelengkap('undefined',value,'');
    });
});

function create_new_packing(i) {
    var kemasan = $('#kemasan'+i).val();
    var satuan  = $('#satuan'+i).val();
    var jumlah  = $('.mother').length-1;
    
    
    if (kemasan !== satuan) {
        kemasan_add(jumlah+1);
        $('#kemasan'+(i+1)).val(satuan);
        $('#satuan'+(i+1)).val(satuan);
        $('.mother:eq('+(i+1)+')').attr('id', 'mother'+(i+1));
    }
}

function kemasan_add(i) {
    
    var str = '<tr class="mother" id="mother'+i+'"><td width=15%><input type=radio name=default value="'+i+'" title="Kemasan jual default" checked /> Barcode:</td><td width=70%><input type=hidden name=id_kemasan'+i+' id=id_kemasan'+i+' /><input type=text name=barcode'+i+' id=barcode'+i+' class=barcode style="width: 75px;" size=10 />'+
                '&nbsp;Kemasan: <select name=kemasan'+i+' id="kemasan'+i+'" onchange="isi_satuan_terkecil('+i+')" style="width: 75px;"><option value="">Pilih ...</option><?php foreach ($kemasan as $data) { echo '<option value="'.$data->id.'">'.$data->nama.'</option>'; } ?></select> '+
                'Isi: <input type=text name=isi'+i+' id=isi'+i+' onblur="isi_satuan_terkecil('+i+')" size=5 style="width: 75px;" />&nbsp;'+
                'Satuan: <select name=satuan'+i+' id="satuan'+i+'" onchange="config_auto_suggest('+i+')" style="width: 75px;"><option value="">Pilih ...</option><?php foreach ($kemasan as $data) { echo '<option value="'.$data->id.'">'.$data->nama.'</option>'; } ?></select>&nbsp;'+
                '&nbsp;<input type=checkbox name=is_bertingkat'+i+' id=checkbox'+i+' value="1" /> <input type=hidden name=isi_kecil'+i+' id=isi_kecil'+i+' value="1" size=5 />'+
            '<img onclick=add_setting_harga('+i+'); title="Klik untuk setting harga" src="<?= base_url('assets/images/add.png') ?>" class=add_kemasan align=right />'+
            '<img onclick=delete_setting_harga('+i+'); title="Klik untuk delete" src="<?= base_url('assets/images/delete.png') ?>" class=delete_kemasan align=right style="margin: 0 5px;" />'+
            '<input type=hidden name=jumlah value="'+i+'" /></td></tr>';
    $('.packing').append(str);
    $('#barcode'+i).val($('#barcode').val());
    config_auto_suggest(i);
    $('#checkbox'+i).mouseover(function() {
        if ($(this).is(':checked') === false) {
            $('#checkbox'+i).attr('title', 'Check, jika menggunakan harga bertingkat');
        } else {
            $('#checkbox'+i).attr('title', 'Uncheck jika TIDAK menggunakan harga bertingkat');
        }
    });
}

function isi_satuan_terkecil(i) {
    var jml_baris = $('.barcode').length-1;
    for (j = 0; j <= jml_baris; j++) {
        /*var kemasan = $('#kemasan'+i).val();
        var isi     = $('#isi'+i).val();
        var satuan  = $('#satuan'+i).val();*/
        if ($('#kemasan'+i).val() === $('#satuan'+j).val()) {
            $('#isi_kecil'+j).val($('#isi'+i).val());
        }
    }
}

function config_auto_suggest(i) {
    var jml_baris = $('.barcode').length-1;
    if (i === jml_baris) {
        create_new_packing(i);
    } else {
        $('#kemasan'+(i+1)).val($('#satuan'+i).val());
        $('#satuan'+(i+1)).val($('#satuan'+i).val());
    }
}

function add_setting_harga(i) {
    var j   = $('.child'+i).length;
    var row = '<tr class="child'+i+'" id="child'+i+''+j+'"><td></td><td>'+
                '<table class="data-input" width="100%" style="border: none; border-bottom: 1px solid #ccc; margin-bottom: 10px;">'+
                    '<tr><td colspan=4>&nbsp;</td></tr>'+
                    '<tr><td>Range Jual:</td><td><input type=text name=awal'+i+'[] id=awal'+i+''+j+' size=5 style="width: 75px;" /> s.d <input type=text name=akhir'+i+'[] id=akhir'+i+''+j+' size=5 style="width: 75px;" /></td><td>Diskon:</td><td><input type=text name=d_persen'+i+'[] id=d_persen'+i+''+j+' value="0" size=5 style="width: 75px;" /> (%) <input type=text name=d_rupiah'+i+'[] value="0" id=d_rupiah'+i+''+j+' onblur="FormNum(this);" onfocus="javascript:this.value=currencyToNumber(this.value);" size=5 style="width: 75px;" /></td></tr>'+
                    '<tr><td>Margin Non Resep:</td><td><input type=text name=margin_nr'+i+'[] id=margin_nr'+i+''+j+' size=5 style="width: 75px;" /> (%) <input type=text id=margin_nr_rp'+i+''+j+' size=5 disabled style="width: 75px;" /></td><td>Harga Jual Non Resep (Rp.):</td><td><span id="hj_nonresep'+i+''+j+'">-</span><input type=hidden name="hj_nonresep'+i+'[]" id="hj_nonresep_f'+i+''+j+' /></td></tr>'+
                    '<tr><td>Margin Resep:</td><td><input type=text name=margin_r'+i+'[] id=margin_r'+i+''+j+' size=5 style="width: 75px;" /> (%) <input type=text id=margin_r_rp'+i+''+j+' size=5 disabled style="width: 75px;" /></td><td>Harga Jual Resep (Rp.):</td><td><span id="hj_resep'+i+''+j+'">-</span><input type=hidden name="hj_resep'+i+'[]" id="hj_resep_f'+i+''+j+'" /><img src="<?= base_url('assets/images/delete.png') ?>" onclick="removeMe(this)" align=right /></td></tr>'+
                '</table>'+
            '</td></tr>';
    $(row).insertAfter('#mother'+i);
    detail_hitung_dinamic(i,j);
}

function detail_hitung_dinamic(i,j,status) {
    var isi = ($('#isi'+i).val()*$('#isi_kecil'+i).val());
    var mar_nr  = $('#margin_nr').val();
    var hna     = parseInt(currencyToNumber($('#hna').val()))*isi;
    var rp_nr   = parseInt(currencyToNumber($('#margin_nr_rp').val()))*isi;
    var rp_r    = parseInt(currencyToNumber($('#margin_r_rp').val()))*isi;
    if (status !== 'edit') {
        $('#margin_nr'+i+''+j).val(mar_nr);
        $('#margin_nr_rp'+i+''+j).val(numberToCurrency(parseInt(rp_nr)));
    } else {
        var margin_nr_pr = ($('#margin_nr'+i+''+j).val()/100);
        var margin_nr_rp = hna+(hna*margin_nr_pr);
        //alert(rp_nr+' '+rp_nr+' '+margin_nr_pr);
        $('#margin_nr_rp'+i+''+j).val(numberToCurrency(parseInt(margin_nr_rp)));
    }
    var mar_r   = $('#margin_r').val();
    var rp_r    = parseInt(currencyToNumber($('#margin_r_rp').val()))*isi;
    if (status !== 'edit') {
        $('#margin_r'+i+''+j).val(mar_r);
        $('#margin_r_rp'+i+''+j).val(numberToCurrency(parseInt(rp_r)));
    } else {
        var margin_r_pr = ($('#margin_r'+i+''+j).val()/100);
        var margin_r_rp = hna+(hna*margin_r_pr);
        //alert(rp_r+' '+rp_r+' '+margin_r_pr);
        $('#margin_r_rp'+i+''+j).val(numberToCurrency(parseInt(margin_r_rp)));
    }
    $('#d_persen'+i+''+j).keyup(function() {
        if ($(this).val() > 0) {
            $('#d_rupiah'+i+''+j).val('0');
        }
        hitung_dinamic_hja(i,j);
    });
    $('#awal'+i+''+j+', #akhir'+i+''+j).keyup(function() {
        hitung_dinamic_hja(i,j);
    });
    $('#d_rupiah'+i+''+j).keyup(function() {
        if ($(this).val() > 0) {
            $('#d_persen'+i+''+j).val('0');
        }
        hitung_dinamic_hja(i,j);
    });
    $('#margin_nr'+i+''+j).keyup(function() {
        var margin_nr_pr= ($(this).val()/100);
        var margin_nr_rp= hna+(hna*margin_nr_pr);
        $('#margin_nr_rp'+i+''+j).val(numberToCurrency(parseInt(margin_nr_rp)));
        hitung_dinamic_hja(i,j);
    });
    $('#margin_r'+i+''+j).keyup(function() {
        var margin_r_pr= ($(this).val()/100);
        var margin_r_rp= hna+(hna*margin_r_pr);
        $('#margin_r_rp'+i+''+j).val(numberToCurrency(parseInt(margin_r_rp)));
        hitung_dinamic_hja(i,j);
    });
}

function hitung_dinamic_hja(i,j) {
    var diskon_pr  = $('#d_persen'+i+''+j).val()/100;
    var diskon_rp  = parseInt(currencyToNumber($('#d_rupiah'+i+''+j).val()));
    
    var margin_nr_rp  = parseInt(currencyToNumber($('#margin_nr_rp'+i+''+j).val()));
    var margin_r_rp   = parseInt(currencyToNumber($('#margin_r_rp'+i+''+j).val()));
    
    if (diskon_pr !== 0) {
        hja_nr  = margin_nr_rp-(diskon_pr*margin_nr_rp);
        hja_r   = margin_r_rp-(diskon_pr*margin_r_rp);
    } else {
        hja_nr  = margin_nr_rp-diskon_rp;
        hja_r   = margin_r_rp-diskon_rp;
    }
    $('#hj_nonresep'+i+''+j).html(numberToCurrency(parseInt(hja_nr)));
    $('#hj_nonresep_f'+i+''+j).val(parseInt(hja_nr));
    
    $('#hj_resep'+i+''+j).html(numberToCurrency(parseInt(hja_r)));
    $('#hj_resep_f'+i+''+j).val(parseInt(hja_r));
    //var hja_nr        = 
}

function delete_setting_harga(i) {
    $('<div>Anda yakin akan menghapus baris ini?</div>').dialog({
        autoOpen: true,
        modal: true,
        title: 'Information Alert',
        buttons: {
            "OK": function() {
                $('tr#mother'+i).remove();
                $('tr.child'+i).remove();
                $(this).dialog().remove();
            }, "Cancel": function() {
                $(this).dialog().remove();
            }
        }
    });
    
}

function removeMe(el) {
    $('<div>Anda yakin akan menghapus baris ini?</div>').dialog({
        autoOpen: true,
        modal: true,
        title: 'Information Alert',
        buttons: {
            "OK": function() {
                $(this).dialog().remove();
                var parent = el.parentNode.parentNode.parentNode.parentNode;
                parent.parentNode.removeChild(parent);
            }, "Cancel": function() {
                $(this).dialog().remove();
            }
        }
    });
    
}

function hitung_hja() {
    var hna     = parseInt(currencyToNumber($('#hna').val()));
    var mar_nr  = $('#margin_nr').val();
    var mar_r   = $('#margin_r').val();
    var rp_nr   = hna+(hna*(mar_nr/100));
    var rp_r    = hna+(hna*(mar_r/100));
    $('#margin_nr_rp').val(numberToCurrency(parseInt(rp_nr)));
    $('#margin_r_rp').val(numberToCurrency(parseInt(rp_r)));
}

function set_margin(i,j) {
    
}

function form_add() {
var str = '<div id=form_add>'+
            '<form id="form_barang" action="<?= base_url('inventory/manage_barang/save') ?>" enctype="multipart/form-data">'+
                '<div id="tabs">'+
                    '<ul>'+
                        '<li><a href="#tabs-1">Data Utama</a></li>'+
                        '<li><a href="#tabs-2">Pelengkap</a></li>'+
                        '<li><a href="#tabs-3">Kemasan Produk</a></li>'+
                    '</ul>'+
                    '<div id="tabs-1"><?= form_hidden('id_barang', NULL, 'id=id_barang') ?>'+
                            '<table cellpadding=0 cellspacing=0 width="100%"><tr valign=top><td width="55%"><table width=100% class=data-input cellpadding=0 cellspacing=0>'+
                                '<tr><td>Barcode:</td><td><?= form_input('barcode', NULL, 'id=barcode size=50') ?></td></tr>'+
                                '<tr><td width=30%>Nama Barang:</td><td><?= form_input('nama', NULL, 'id=nama size=50 onBlur="javascript:this.value=this.value.toUpperCase();"') ?></td></tr>'+
                                '<tr><td>Pabrik:</td><td><?= form_input('pabrik', NULL, 'id=pabrik size=50') ?><?= form_hidden('id_pabrik', NULL, 'id=id_pabrik') ?></td></tr>'+
                                '<tr><td>Kekuatan Obat:</td><td><?= form_input('kekuatan', NULL, 'id=kekuatan size=50') ?></td></tr>'+
                                '<tr><td>Satuan Kekuatan:</td><td><select name=s_sediaan id=s_sediaan><option value="">Pilih ...</option><?php foreach ($satuan_kekuatan as $data) { echo '<option value="'.$data->id.'">'.$data->nama.'</option>'; } ?></select></td></tr>'+
                                '<tr><td>Jenis Sediaan:</td><td><select name=sediaan id=sediaan><option value="">Pilih ...</option><?php foreach ($sediaan as $data) { echo '<option value="'.$data->id.'">'.$data->nama.'</option>'; } ?></select></td></tr>'+
                                '<tr><td>Golongan Margin:</td><td><select name=golongan id=golongan><option value="">Pilih ...</option><?php foreach ($golongan as $data) { echo '<option value="'.$data->id.'">'.$data->nama.'</option>'; } ?></select></td></tr>'+
                                '<tr><td>Formularium:</td><td><?= form_radio('formularium', 'Ya', 'yes', 'Ya', FALSE) ?> <?= form_radio('formularium', 'Tidak', 'no', 'Tidak', TRUE) ?></td></tr>'+
                                '<tr><td>Rute Pemberian:</td><td><select name=admr id=admr><option value="">Pilih ...</option><?php foreach ($admr as $data) { echo '<option value="'.$data.'">'.$data.'</option>'; } ?></select></td></tr>'+
                                '<tr><td></td><td><?= form_radio('generik', '1', 'ya', 'Generik', TRUE) ?> <?= form_radio('generik', '0', 'tidak', 'Non Generik', FALSE) ?></td></tr>'+
                                '<tr><td>Golongan Obat:</td><td><select name="perundangan" id="perundangan"><?php foreach ($perundangan as $data) { echo '<option value="'.$data.'">'.$data.'</option>'; } ?></select></td></tr>'+
                                '<tr><td>Kategori:</td><td><select style="max-width: 147px;" name=farmakoterapi id=farmakoterapi><option value="">Pilih ...</option><?php foreach ($farmakoterapi as $data) { echo '<option value="'.$data->id.'">'.$data->nama.'</option>'; } ?></select></td></tr>'+
                                '<tr><td>Sub Kategori:</td><td><select name=kls_terapi id=kls_terapi style="max-width: 147px;"><option value="">Pilih ...</option></select></td></tr>'+
                                '<tr><td>Keterangan Khusus:</td><td><select name=status id=status><option value="">Pilih ...</option><?php foreach ($status as $data) { ?><option value="<?= $data ?>"><?= $data ?></option><?php } ?></select></td></tr>'+
                                    '<tr><td>Status Obat:</td><td><select name=range_terapi id=range_terapi><option value="">Pilih ...</option><?php foreach ($range_terapi as $data) { ?><option value="<?= $data ?>"><?= $data ?></option><?php } ?></select></td></tr>'+
                                    '<tr><td>Pengawasan:</td><td><select name=pengawasan id=pengawasan><option value="">Pilih ...</option><?php foreach ($pengawasan as $data) { ?><option value="<?= $data ?>"><?= $data ?></option><?php } ?></select></td></tr>'+
                                    '<tr><td>Fornas:</td><td><select name=fornas id=fornas><option value="">Pilih ...</option><?php foreach ($fornas as $data) { ?><option value="<?= $data ?>"><?= $data ?></option><?php } ?></select></td></tr>'+
                                '</table></div>'+
                                
                                '</td><td width=1%>&nbsp;</td><td width=40%>'+
                                '<table width=100% class=data-input cellpadding=0 cellspacing=0>'+
                                    '<tr><td>Rak:</td><td><?= form_input('rak', NULL, 'id=rak style="width: 70px;"') ?></td></tr>'+
                                    '<tr><td width=40%>Stok Minimal:</td><td><?= form_input('stok_min', NULL, 'id=stok_min style="width: 70px;"') ?></td></tr>'+
                                    '<tr><td>HNA:</td><td><?= form_input('hna', NULL, 'id=hna onblur="FormNum(this)" onkeyup=hitung_hja() style="width: 70px;"') ?></td></tr>'+
                                    '<tr><td>Margin Non Resep:</td><td><?= form_input('margin_nr', NULL, 'id=margin_nr style="width: 70px;" onkeyup=hitung_hja()') ?> %, H. Jual <?= form_input('margin_nr_rp', NULL, 'id=margin_nr_rp style="width: 63px;"') ?></td></tr>'+
                                    '<tr><td>Margin Resep:</td><td><?= form_input('margin_r', NULL, 'id=margin_r style="width: 70px;" onkeyup=hitung_hja()') ?> %, H. Jual <?= form_input('margin_r_rp', NULL, 'id=margin_r_rp style="width: 63px;" onblur=FormNum(this)') ?></td></tr>'+
                                    //'<tr><td>Image:</td><td><?= form_upload('mFile',null,'id=mFile') ?></td></tr>'+
                                    //'<tr><td></td><td><img id=image src="" /></td></tr>'+
                                    '<tr><td></td><td><?= form_checkbox('aktifasi', '1', 'aktifasi', 'Aktifasi') ?></td></tr>'+
                                '</table>'+
                            '</td></tr></table>'+
                    '</div>'+
                    '<div id="tabs-2"><table width=100% class=data-input cellpadding=0 cellspacing=0>'+
                                '<tr><td width=17% valign=top>Kandungan:</td><td><?= form_textarea('kandungan', NULL, 'cols=48 id=kandungan') ?></td></tr>'+
                                '<tr><td valign=top>Indikasi:</td><td><?= form_textarea('indikasi', NULL, 'cols=48 id=indikasi') ?></td></tr>'+
                                '<tr><td valign=top>Perhatian:</td><td><?= form_textarea('perhatian', NULL, 'cols=48 id=perhatian') ?></td></tr>'+
                                '<tr><td valign=top>Kontra Indikasi:</td><td><?= form_textarea('kontra_indikasi', NULL, 'cols=48 id=kontra_indikasi') ?></td></tr>'+
                                '<tr><td valign=top>Efek Samping:</td><td><?= form_textarea('efek_samping', NULL, 'cols=48 id=efek_samping') ?></td></tr>'+
                                '<tr><td valign=top>Dosis:</td><td><?= form_textarea('dosis', NULL, 'cols=48 id=dosis') ?></td></tr>'+
                                '<tr><td valign=top>Aturan Pakai:</td><td><?= form_textarea('aturan_pakai', NULL, 'cols=48 id=aturan_pakai') ?></td></tr>'+
                                '<tr><td>FDA Pregnancy:</td><td><select name=fda_pregnan id=fda_pregnan><option value="">Pilih ...</option><?php foreach ($fda as $data) { echo '<option value="'.$data.'">'.$data.'</option>'; } ?></select></td></tr>'+
                                '<tr><td>FDA Lactacy:</td><td><select name=fda_lactacy id=fda_lactacy><option value="">Pilih ...</option><?php foreach ($fda as $data) { echo '<option value="'.$data.'">'.$data.'</option>'; } ?></select></td></tr>'+

                            '</table></div>'+
                    '<div id="tabs-3"><?= form_hidden('id_kemasan', NULL, 'id=id_kemasan') ?>'+
                        '<img src="<?= base_url('assets/images/add-kemasan.png') ?>" id=add_kemasan align=left style="margin-bottom: 3px;" /><br/>'+
                            '<table width=100% class="data-input packing" id="input-packing" cellpadding=0 cellspacing=0>'+
                                //'<span class="packing" style="display: block; width: 100%; border: 1px solid #999; background: #f4f4f4; padding: 10px 5px; font-size: 11px;"></span>'+
                            '</table>'+
                    '</div>'+
                '</div></form>'+
              '</div>';
    $('body').append(str);
    $('#tabs').tabs();
    $('textarea').focus(function() {
        $(this).select();
    });
    $('#barcode').blur(function() {
        if ($('#id_barang').val() === '') {
            var barcode = $(this).val();
            $('#barcode0').val(barcode);
        }
    });
    $('#margin_nr_rp').keyup(function() {
        var hna     = parseInt(currencyToNumber($('#hna').val()));
        var hja     = parseInt(currencyToNumber($(this).val()));
        var mar_nr  = ((hja-hna)/hna)*100;
        $('#margin_nr').val(isNaN(mar_nr)?'':(mar_nr));
    });
    $('#margin_r_rp').keyup(function() {
        var hna     = parseInt(currencyToNumber($('#hna').val()));
        var hja     = parseInt(currencyToNumber($(this).val()));
        var mar_r   = ((hja-hna)/hna)*100;
        $('#margin_r').val(isNaN(mar_r)?'':(mar_r));
    });
    $('add_kemasan').button();
    $('#farmakoterapi').change(function() {
        var id = $(this).val();
        $.getJSON('<?= base_url('autocomplete/farmakoterapi') ?>?id='+id, function(data){
            $('#kls_terapi').html('');
            $.each(data, function (index, value) {
                $('#kls_terapi').append("<option value='"+value.id+"'>"+value.nama+"</option>");
            });
        });
    });
    
    $('#golongan').change(function() {
        var id = $(this).val();
        $.ajax({
            url: '<?= base_url('autocomplete/golongan_load_data') ?>?id='+id,
            cache: false,
            dataType: 'json',
            success: function(data) {
                $('#margin_nr').val(data.margin_non_resep);
                $('#margin_r').val(data.margin_resep);
            }
        });
    });
    $('#add_kemasan').click(function() {
        var jml = $('.mother').length;
        kemasan_add(jml);
        $('.mother:eq('+jml+')').attr('id', 'mother'+jml);
    });
    var lebar = $('#pabrik').width();
    $('#pabrik').autocomplete("<?= base_url('autocomplete/pabrik') ?>",
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
        $('#id_pabrik').val(data.id);
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
    });
    $('#form_add').dialog({
        title: 'Tambah Barang',
        autoOpen: true,
        modal: true,
        width: 950,
        height: 570,
        hide: 'clip',
        show: 'blind',
        buttons: {
            "Simpan": function() {
                $('#form_barang').submit();
            }, 
            "Cancel": function() {    
                $(this).dialog().remove();
            }
        }, close: function() {
            $(this).dialog().remove();
        }, open: function() {
            var jml = $('.mother').length;
            kemasan_add(jml);
            $('.mother:eq('+jml+')').attr('id', 'mother'+jml);
            $('#barcode').focus();
        }
    });
    
    var lebar = $('#pabrik').width();
    $('#pabrik').dblclick(function() {
        $('<div title="Data pabrik" id="pabrik-data"></div>').dialog({
            autoOpen: true,
            modal: true,
            width: 500,
            height: 350,
            buttons: {
                
            }
        });
    });
    
    
    $('#form_barang').submit(function()
    {
        //e.preventDefault();
        if ($('#nama').val() === '') {
            alert('Nama produk tidak boleh kosong !');
            $('#nama').focus(); return false;
        }
        if ($('#kekuatan').val() === '') {
            alert('Kekuatan barang tidak boleh kosong !');
            $('#kekuatan').focus(); return false;
        }
        if ($('#h_pokok').val() === '') {
            alert('Harga pokok tidak boleh kosong !');
            $('#h_pokok').focus(); return false;
        }
        var cek_id = $('#id_barang').val();
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success:  function(data) {
                if (data.status === true) {
                    if (cek_id === '') {
                        alert_tambah('#barcode');
                        $('input[type=text], textarea, select').val('');
                        $('#input-packing').html('');
                        load_data_barang('1','',data.id_barang);
                    } else {
                        alert_edit();
                        $('#form_add').dialog().remove();
                        var page = $('.noblock').html();
                        load_data_barang(page,$('#search').val());
                    }
                    
                }
            }
        });
        return false;
    });
}
function load_data_barang(page, search, id) {
    src = search; id_barg = id;
    if (search === undefined) { var src = ''; }
    if (id === undefined) { var id_barg = ''; }
    $.ajax({
        url: '<?= base_url('inventory/manage_barang/list') ?>/'+page,
        cache: false,
        data: 'search='+src+'&id='+id_barg+'&aktif=1',
        success: function(data) {
            $('#result-barang').html(data);
        }
    });
}

function load_data_barang_pelengkap(page, search, id) {
    src = search; id_barg = id;
    if (search === undefined) { var src = ''; }
    if (id === undefined) { var id_barg = ''; }
    $.ajax({
        url: '<?= base_url('inventory/manage_barang/list_pelengkap') ?>/'+page,
        cache: false,
        data: 'search='+src+'&id='+id_barg+'&aktif=1',
        success: function(data) {
            $('#result-barang-pelengkap').html(data);
        }
    });
}

function paging(page, tab, search) {
    var active = $('#tabs-utama').tabs('option','active');
    paginate(page, tab, search, active);
    //load_data_barang(page, search);
}

function paginate(page, tab, search, active) {
    if (active === 0) {
        load_data_barang(page, search);
    }
    if (active === 1) {
        load_data_barang_pelengkap(page, search);
    }
}

function edit_barang(str) {
    var arr = str.split('#');
    form_add();
    $('#barcode').val(arr[30]);
    //$('#image').attr('src','<?= base_url('assets/images/barang') ?>/'+arr[31]);
    $('#image').attr('width','100px');
    $('#id_barang').val(arr[0]);
    $('#nama').val(arr[1]);
    $('#kekuatan').val(arr[2]);
    $('#s_sediaan').val(arr[3]);
    $('#sediaan').val(arr[4]);
    $('#golongan').val(arr[5]);
    $('#admr').val(arr[6]);
    $('#pabrik').val(arr[8]);
    $('#id_pabrik').val(arr[7]);
    $('#rak').val(arr[9]);
    //alert(arr[11]);
    if (arr[10] === 'Ya') { $('#yes').attr('checked','checked'); }
    if (arr[10] === 'Tidak') { $('#no').attr('checked','checked'); }
    
    if (arr[11] === '1') { $('#ya').attr('checked','checked'); }
    if (arr[11] === '0') { $('#tidak').attr('checked','checked'); }
    
    $('#indikasi').val(arr[12]);
    $('#dosis').val(arr[13]);
    $('#kandungan').val(arr[14]);
    $('#perhatian').val(arr[15]);
    $('#kontra_indikasi').val(arr[16]);
    $('#efek_samping').val(arr[17]);
    $('#stok_min').val(arr[18]);
    $('#margin_nr').val(arr[19]);
    $('#margin_r').val(arr[20]);
    if (arr[21] === '0') { $('#ppn').removeAttr('checked'); } else { $('#ppn').attr('checked','checked'); }
    $('#hna').val(numberToCurrency(arr[22]));
    if (arr[23] === '0') { $('#aktifasi').removeAttr('checked'); } else { $('#aktifasi').attr('checked','checked'); }
    $('#aturan_pakai').val(arr[24]);
    $('#farmakoterapi').val(arr[25]);
    $.getJSON('<?= base_url('autocomplete/farmakoterapi') ?>?id='+arr[25], function(data){
        $('#kls_terapi').html('');
        $.each(data, function (index, value) {
            $('#kls_terapi').append("<option value='"+value.id+"'>"+value.nama+"</option>");
        });
        $('#kls_terapi').val(arr[26]);
    });
    $.ajax({
        url: '<?= base_url('inventory/edit_kemasan') ?>/'+arr[0],
        cache: false,
        success: function(data) {
            $('.packing').html(data);
        }
    });
    $('#fda_pregnan').val(arr[27]);
    $('#fda_lactacy').val(arr[28]);
    $('#perundangan').val(arr[29]);
    $('#status').val(arr[32]);
    $('#range_terapi').val(arr[33]);
    $('#pengawasan').val(arr[34]);
    $('#fornas').val(arr[35]);
    hitung_hja();
}
function delete_barang(id, page) {
    $('<div id=alert>Anda yakin akan menghapus data ini?</div>').dialog({
        title: 'Konfirmasi Penghapusan',
        autoOpen: true,
        modal: true,
        buttons: {
            "OK": function() {
                
                $.ajax({
                    url: '<?= base_url('referensi/manage_barang/delete') ?>?id='+id,
                    cache: false,
                    success: function() {
                        load_data_barang(page);
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
    load_data_barang();
    $('#search').val('');
});
$('#reset-pelengkap').button({
    icons: {
        secondary: 'ui-icon-refresh'
    }
}).click(function() {
    load_data_barang_pelengkap();
    $('#search').val('');
});
</script>
<div class="kegiatan"
    <div id="tabs-utama">
        <ul>
            <li><a href="#tabs-1">Data Utama</a></li>
            <li><a href="#tabs-2">Data Pelengkap</a></li>
        </ul>
        <div id="tabs-1">
            <button id="button">Tambah Data</button>
            <button id="reset">Reset</button>
            <?= form_input('search', NULL, 'id=search placeholder="Search ..." class=search') ?>
            <div id="result-barang">
                
            </div>
        </div>
        <div id="tabs-2">
            <button id="reset-pelengkap">Reset</button>
            <?= form_input('search', NULL, 'id="search-pelengkap" placeholder="Search ..." class=search') ?>
            <div id="result-barang-pelengkap">

            </div>
        </div>
    </div>
</div>