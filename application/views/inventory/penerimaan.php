<title><?= $title ?></title>
<?= $this->load->view('message') ?>
<script type="text/javascript">
load_data_penerimaan();
    $.cookie('session', 'false');
    $(document).keydown(function(e) {
        if (e.keyCode === 120) {
            if ($.cookie('session') === 'false') {
                $('#button').click();
            }
        }
    });

/*FORM Barang*/
function hitung_hja() {
    var hna     = parseInt(currencyToNumber($('#hna').val()));
    var mar_nr  = $('#margin_nr').val();
    var mar_r   = $('#margin_r').val();
    var rp_nr   = hna+(hna*(mar_nr/100));
    var rp_r    = hna+(hna*(mar_r/100));
    $('#margin_nr_rp').val(numberToCurrency(parseInt(rp_nr)));
    $('#margin_r_rp').val(numberToCurrency(parseInt(rp_r)));
}

function form_add_barang() {
var str = '<div id=form_add_barang>'+
            '<form id="form_barang" action="models/update-masterdata.php?method=save_barang" enctype="multipart/form-data">'+
                '<div id="tabs">'+
                    '<ul>'+
                        '<li><a href="#tabs-1">Data Utama</a></li>'+
                        '<li><a href="#tabs-2">Pelengkap</a></li>'+
                        '<li><a href="#tabs-3">Kemasan Produk</a></li>'+
                    '</ul>'+
                    '<div id="tabs-1"><?= form_hidden('id_barang', NULL, 'id=id_barang') ?>'+
                            '<table width="100%"><tr valign=top><td width="55%"><table width=100% class=data-input cellspacing=0 cellpadding=0>'+
                                '<tr><td>Barcode:</td><td><?= form_input('barcode', NULL, 'id=barcode style="min-width: 147px;"') ?></td></tr>'+
                                '<tr><td width=30%>Nama Barang:</td><td><?= form_input('nama', NULL, 'id=nama size=50 onBlur="javascript:this.value=this.value.toUpperCase();"') ?></td></tr>'+
                                '<tr><td>Pabrik:</td><td><?= form_input('pabrik', NULL, 'id=pabrik size=50') ?><?= form_hidden('id_pabrik', NULL, 'id=id_pabrik') ?></td></tr>'+
                                '<tr><td>Kekuatan:</td><td><?= form_input('kekuatan', NULL, 'id=kekuatan style="min-width: 147px;"') ?></td></tr>'+
                                '<tr><td>Satuan Kekuatan:</td><td><select name=s_sediaan id=s_sediaan><option value="">Pilih ...</option><?php foreach ($satuan_kekuatan as $data) { echo '<option value="'.$data->id.'">'.$data->nama.'</option>'; } ?></select></td></tr>'+
                                '<tr><td>Sediaan:</td><td><select name=sediaan id=sediaan><option value="">Pilih ...</option><?php foreach ($sediaan as $data) { echo '<option value="'.$data->id.'">'.$data->nama.'</option>'; } ?></select></td></tr>'+
                                '<tr><td>Golongan:</td><td><select name=golongan id=golongan><option value="">Pilih ...</option><?php foreach ($golongan as $data) { echo '<option value="'.$data->id.'">'.$data->nama.'</option>'; } ?></select></td></tr>'+
                                '<tr><td>Formularium:</td><td><?= form_radio('formularium', 'Ya', 'yes', 'Ya', FALSE) ?> <?= form_radio('formularium', 'Tidak', 'no', 'Tidak', TRUE) ?></td></tr>'+
                                '<tr><td>Rute Pemberian:</td><td><select name=admr id=admr><option value="">Pilih ...</option><?php foreach ($admr as $data) { echo '<option value="'.$data.'">'.$data.'</option>'; } ?></select></td></tr>'+
                                '<tr><td></td><td><?= form_radio('generik', '1', 'ya', 'Generik', TRUE) ?> <?= form_radio('generik', '0', 'tidak', 'Non Generik', FALSE) ?></td></tr>'+
                                '<tr><td>Golongan Obat:</td><td><select name="perundangan" id="perundangan"><?php foreach ($perundangan as $data) { echo '<option value="'.$data.'">'.$data.'</option>'; } ?></select></td></tr>'+
                                '<tr><td>Kategori:</td><td><select style="max-width: 147px;" name=farmakoterapi id=farmakoterapi><option value="">Pilih ...</option><?php foreach ($farmakoterapi as $data) { echo '<option value="'.$data->id.'">'.$data->nama.'</option>'; } ?></select></td></tr>'+
                                '<tr><td>Sub Kategori:</td><td><select name=kls_terapi id=kls_terapi style="max-width: 147px;"><option value="">Pilih ...</option></select></td></tr>'+
                                '<tr><td>Keterangan Khusus:</td><td><select name=status id=status><option value="">Pilih ...</option><?php foreach ($status as $data) { ?><option value="<?= $data ?>"><?= $data ?></option><?php } ?></select></td></tr>'+
                                    '<tr><td>Status Obat:</td><td><select name=range_terapi id=range_terapi><option value="">Pilih ...</option><?php foreach ($range_terapi as $data) { ?><option value="<?= $data ?>"><?= $data ?></option><?php } ?></select></td></tr>'+
                                    '<tr><td>Pengawasan:</td><td><select name=pengawasan id=pengawasan><option value="">Pilih ...</option><?php foreach ($pengawasan as $data) { ?><option value="<?= $data ?>"><?= $data ?></option><?php } ?></select></td></tr>'+
                                    '<tr><td>Fornas:</td><td><select name=fornas id=fornas><option value="">Pilih ...</option><?php foreach ($fornas as $data) { ?><option value="<?= $data ?>"><?= $data ?></option><?php } ?></select></td></tr></table>'+
                                '</td><td width=1%>&nbsp;</td><td width=40%>'+
                                '<table width=100% class=data-input cellspacing=0 cellpadding=0>'+
                                    '<tr><td>Rak:</td><td><?= form_input('rak', NULL, 'id=rak style="width: 70px;"') ?></td></tr>'+
                                    '<tr><td width=40%>Stok Minimal:</td><td><?= form_input('stok_min', NULL, 'id=stok_min style="width: 70px;"') ?></td></tr>'+
                                    '<tr><td>HNA:</td><td><?= form_input('hna', NULL, 'id=hna onblur="FormNum(this)" onkeyup=hitung_hja() style="width: 70px;"') ?></td></tr>'+
                                    '<tr><td>Margin Non Resep:</td><td><?= form_input('margin_nr', NULL, 'id=margin_nr style="width: 70px;" onkeyup=hitung_hja()') ?> %, H. Jual <?= form_input('margin_nr_rp', NULL, 'id=margin_nr_rp style="width: 63px;"') ?></td></tr>'+
                                    '<tr><td>Margin Resep:</td><td><?= form_input('margin_r', NULL, 'id=margin_r style="width: 70px;" onkeyup=hitung_hja()') ?> %, H. Jual <?= form_input('margin_r_rp', NULL, 'id=margin_r_rp style="width: 63px;" onblur=FormNum(this)') ?></td></tr>'+
                                    '<tr><td>Image:</td><td><?= form_upload('mFile',null,'id=mFile') ?></td></tr>'+
                                    '<tr><td></td><td><img id=image src="" /></td></tr>'+
                                    '<tr><td></td><td><?= form_checkbox('aktifasi', '1', 'aktifasi', 'Aktifasi') ?></td></tr>'+
                                '</table>'+
                            '</td></tr></table>'+
                    '</div>'+
                    '<div id="tabs-2"><table width=100% class=data-input>'+
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
                        '<img src="img/icons/add-kemasan.png" id=add_kemasan align=left style="margin-bottom: 3px;" /><br/>'+
                            '<table width=100% class="data-input" id="input-packing">'+
                                '<span class="packing" style="display: block; width: 100%; border: 1px solid #999; background: #f4f4f4; padding: 10px 5px; font-size: 11px;"></span>'+
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
        $.getJSON('models/autocomplete.php?method=farmakoterapi&id='+id, function(data){
            $('#kls_terapi').html('');
            $.each(data, function (index, value) {
                $('#kls_terapi').append("<option value='"+value.id+"'>"+value.nama+"</option>");
            });
        });
    });
    
    $('#golongan').change(function() {
        var id = $(this).val();
        $.ajax({
            url: 'models/autocomplete.php?method=golongan_load_data&id='+id,
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
    });
    $('#form_add_barang').dialog({
        title: 'Tambah Barang',
        autoOpen: true,
        modal: true,
        width: 950,
        height: 580,
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
    
    
    $('#form_barang').on('submit', function(e)
    {
        e.preventDefault();
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
        $(this).ajaxSubmit({
            target: '#output',
            dataType: 'json',
            success:  function(data) {
                if (data.status === true) {
                    if (cek_id === '') {
                        alert_tambah('#barcode');
                        $('input[type=text], textarea, select').val('');
                        $('#input-packing').html('');
                        $('#form_add_barang').dialog('close');
                        //load_data_barang('1','',data.id_barang);
                    } else {
                        alert_edit();
                        $('#form_add_barang').dialog().remove();
                        var page = $('.noblock').html();
                        $('#form_add_barang').dialog('close');
                        //load_data_barang(page,$('#search').val());
                    }
                    
                }
            }
        });
        //return false;
    });
}

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
    
    var str = '<tr class="mother" id="mother'+i+'"><td width=15%><input type=radio name=default value="'+i+'" title="Kemasan jual default" /> Barcode:</td><td width=70%><input type=hidden name=id_kemasan'+i+' id=id_kemasan'+i+' /><input type=text name=barcode'+i+' id=barcode'+i+' class=barcode size=10 />'+
                '&nbsp;Kemasan: <select name=kemasan'+i+' id="kemasan'+i+'" onchange="isi_satuan_terkecil('+i+')" style="min-width: 100px;"><option value="">Pilih ...</option><?php foreach ($kemasan as $data) { echo '<option value="'.$data->id.'">'.$data->nama.'</option>'; } ?></select> '+
                'Isi: <input type=text name=isi'+i+' id=isi'+i+' onblur="isi_satuan_terkecil('+i+')" size=5 />&nbsp;'+
                'Satuan: <select name=satuan'+i+' id="satuan'+i+'" onchange="config_auto_suggest('+i+')" style="min-width: 100px;"><option value="">Pilih ...</option><?php foreach ($kemasan as $data) { echo '<option value="'.$data->id.'">'.$data->nama.'</option>'; } ?></select>&nbsp;'+
                '&nbsp;<input type=checkbox name=is_bertingkat'+i+' id=checkbox'+i+' value="1" /> <input type=hidden name=isi_kecil'+i+' id=isi_kecil'+i+' value="1" size=5 />'+
            '<img onclick=add_setting_harga('+i+'); title="Klik untuk setting harga" src="img/icons/add.png" class=add_kemasan align=right />'+
            '<img onclick=delete_setting_harga('+i+'); title="Klik untuk delete" src="img/icons/delete.png" class=delete_kemasan align=right style="margin: 0 5px;" />'+
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
                    '<tr><td colspan=2>&nbsp;</td></tr>'+
                    '<tr><td>Range Jual:</td><td><input type=text name=awal'+i+'[] id=awal'+i+''+j+' size=5 /> <div class="space"> s.d </div> <input type=text name=akhir'+i+'[] id=akhir'+i+''+j+' size=5 /></td><td>Diskon:</td><td><input type=text name=d_persen'+i+'[] id=d_persen'+i+''+j+' value="0" size=5 /> <div class="space"> (%) </div> <input type=text name=d_rupiah'+i+'[] value="0" id=d_rupiah'+i+''+j+' onblur="FormNum(this);" onfocus="javascript:this.value=currencyToNumber(this.value);" size=5 /></td></tr>'+
                    '<tr><td>Margin Non Resep:</td><td><input type=text name=margin_nr'+i+'[] id=margin_nr'+i+''+j+' size=5 /> <div class="space"> (%) </div> <input type=text id=margin_nr_rp'+i+''+j+' size=5 disabled /></td><td>Harga Jual Non Resep (Rp.):</td><td><span id="hj_nonresep'+i+''+j+'">-</span><input type=hidden name="hj_nonresep'+i+'[]" id="hj_nonresep_f'+i+''+j+'" /></td></tr>'+
                    '<tr><td>Margin Resep:</td><td><input type=text name=margin_r'+i+'[] id=margin_r'+i+''+j+' size=5 /> <div class="space"> (%) </div> <input type=text id=margin_r_rp'+i+''+j+' size=5 disabled /></td><td>Harga Jual Resep (Rp.):</td><td><span id="hj_resep'+i+''+j+'">-</span><input type=hidden name="hj_resep'+i+'[]" id="hj_resep_f'+i+''+j+'" /><img src="img/icons/delete.png" onclick="removeMe(this)" align=right /></td></tr>'+
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

/*END Of Barang Form*/
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
                    $('.tr_rows:eq('+col+')').children('td:eq(1)').children('.barang').attr('id','barang'+i);
                    $('.tr_rows:eq('+col+')').children('td:eq(1)').children('.id_barang').attr('id','id_barang'+i);
                    $('.tr_rows:eq('+col+')').children('td:eq(2)').children('.satuan').attr('id','satuan'+i);
                    $('.tr_rows:eq('+col+')').children('td:eq(3)').children('.jumlah').attr('id','jumlah'+i);
                    $('.tr_rows:eq('+col+')').children('td:eq(4)').children('.nobatch').attr('id','nobatch'+i);
                    $('.tr_rows:eq('+col+')').children('td:eq(5)').children('.ed').attr('id','ed'+i);
                    $('.tr_rows:eq('+col+')').children('td:eq(6)').children('.harga').attr('id','harga'+i);
                    $('.tr_rows:eq('+col+')').children('td:eq(7)').children('.diskon_pr').attr('id','diskon_pr'+i);
                    $('.tr_rows:eq('+col+')').children('td:eq(8)').children('.diskon_rp').attr('id','diskon_rp'+i);
                    $('.tr_rows:eq('+col+')').children('td:eq(9)').children('.subtotal').attr('id','subtotal'+i);
                    $('.tr_rows:eq('+col+')').children('td:eq(9)').children('.existing_hna').attr('id','existing_hna'+i);
                    $('.tr_rows:eq('+col+')').children('td:eq(9)').children('.hna').attr('id','hna'+i);
                    $('.tr_rows:eq('+col+')').children('td:eq(9)').children('.isi').attr('id','isi'+i);
                    $('.tr_rows:eq('+col+')').children('td:eq(9)').children('.isi_satuan').attr('id','isi_satuan'+i);
                    hitung_sub_total(i);
                    col++;
                }
                if (jumlah === 0) {
                    $('#total').val('0');
                }
            }
        }
    });
}

function check_hna() {
    var jml_baris = $('.tr_rows').length;
    for (i = 1; i <= jml_baris; i++) {
        var barang      = $('#barang'+i).val();
        var ppn         = $('#ppn').val()/100;
        var isi         = $('#isi'+i).val();
        var isi_satuan  = $('#isi_satuan'+i).val();
        var hna         = Math.ceil($('#existing_hna'+i).val()); // existing HNA
        var hrg_beli    = parseFloat(currencyToNumber($('#harga'+i).val()));
        var new_var     = hrg_beli/(isi*isi_satuan); // pengali
        var new_hna     = Math.ceil((ppn*new_var)+new_var);
        //alert(hrg_beli+' - '+isi+' - '+isi_satuan);
        if (hna > new_hna) {
            $('<div>HNA untuk barang '+barang+' mengalami perubahan dari Rp. '+numberToCurrency(hna)+' menjadi Rp. '+numberToCurrency(new_hna)+'</br> Apakah anda akan melakukan perubahan?</div>').dialog({
                title: 'Konfirmasi Perubahan HNA',
                autoOpen: true,
                modal: true,
                width: 400,
                buttons: {
                    "Ya": function() {
                        $('#hna'+i).val(new_hna);
                        
                        $(this).dialog().remove();
                        $('#diskon_pr'+i).focus().select();
                    },
                    "Tidak": function() {
                        $('#hna'+i).val(hna);
                        $(this).dialog().remove();
                        $('#diskon_pr'+i).focus().select();
                    }
                }, close: function() {
                    $('#hna'+i).val(hna);
                    $(this).dialog().remove();
                    $('#diskon_pr'+i).focus().select();
                }
            });
        }
        if (hna < new_hna) {
            $('#hna'+i).val(new_hna);
        }
    }
}

function check_perubahan_hna(i) {
    //var jml_baris = $('.tr_rows').length;
    //for (i = 1; i <= jml_baris; i++) {
        var barang      = $('#barang'+i).val();
        var ppn         = $('#ppn').val()/100;
        var isi         = $('#isi'+i).val();
        var isi_satuan  = $('#isi_satuan'+i).val();
        var hna         = Math.ceil($('#existing_hna'+i).val()); // existing HNA
        var hrg_beli    = parseFloat(currencyToNumber($('#harga'+i).val()));
        var new_var     = hrg_beli/(isi*isi_satuan); // pengali
        var new_hna     = Math.ceil((ppn*new_var)+new_var);
        //alert(hrg_beli+' - '+isi+' - '+isi_satuan);
        if (hna > new_hna) {
            $('<div>HNA untuk barang '+barang+' mengalami perubahan dari Rp. '+numberToCurrency(hna)+' menjadi Rp. '+numberToCurrency(new_hna)+'</br> Apakah anda akan melakukan perubahan?</div>').dialog({
                title: 'Konfirmasi Perubahan HNA',
                autoOpen: true,
                modal: true,
                width: 400,
                buttons: {
                    "Ya": function() {
                        $('#hna'+i).val(new_hna);
                        
                        $(this).dialog().remove();
                        $('#diskon_pr'+i).focus().select();
                    },
                    "Tidak": function() {
                        $('#hna'+i).val(hna);
                        $(this).dialog().remove();
                        $('#diskon_pr'+i).focus().select();
                    }
                }, close: function() {
                    $('#hna'+i).val(hna);
                    $(this).dialog().remove();
                    $('#diskon_pr'+i).focus().select();
                }
            });
        }
        if (hna < new_hna) {
            $('#hna'+i).val(new_hna);
        }
    //}
}
function load_list_data(id_barang, nama_barang, id_satuan_beli, jumlah, hna, isi, isi_satuan) {
    var no   = $('.tr_rows').length+1;
    var list = '<tr class=tr_rows>'+
                    '<td align=center>'+no+'</td>'+
                    '<td><input type=text name=barang value="'+nama_barang+'" class=barang id=barang'+no+' size=50 /> <input type=hidden name=id_barang[] id=id_barang'+no+' value="'+id_barang+'" class=id_barang /></td>'+
                    '<td><select name=satuan[] id=satuan'+no+' class=satuan></select></td>'+
                    /*3*/'<td><input type=text name=jumlah[] id=jumlah'+no+' class=jumlah value="'+jumlah+'" size=10 /></td>'+
                    '<td><input type=text name=nobatch[] id=nobatch'+no+' class=nobatch size=10 /></td>'+
                    '<td><input type=text name=ed[] id=ed'+no+' size=10 class=ed /></td>'+
                    /*6*/'<td><input type=text name=harga[] id=harga'+no+' class=harga onblur=javascript:this.value=currencyToNumber(this.value); size=10 /></td>'+
                    '<td><input type=text name=diskon_pr[] id=diskon_pr'+no+' class=diskon_pr value="0" onblur="hitung_sub_total('+no+');" size=10 maxlength=5 /></td>'+
                    '<td><input type=text name=diskon_rp[] id=diskon_rp'+no+' class=diskon_rp value="0" onblur=FormNum(this); size=10 onfocus=javascript:this.value=currencyToNumber(this.value); /></td>'+
                    /*9*/'<td><input type=text name=subtotal[] id=subtotal'+no+' class=subtotal size=10 />'+
                    '<input type=hidden name=existing_hna[] id=existing_hna'+no+' class=existing_hna value="'+hna+'" />'+
                    '<input type=hidden name=hna[] id=hna'+no+' value="'+hna+'" class=hna />'+
                    /*12*/'<input type=hidden name=isi[] id=isi'+no+' value="'+isi+'" class=isi />'+
                    '<input type=hidden name=isi_satuan[] id=isi_satuan'+no+' value="'+isi_satuan+'" class=isi_satuan /></td>'+
                    '<td align=center class=aksi><img src="<?= base_url('assets/images/delete.png') ?>" align=left title="Klik untuk hapus" onclick="removeMe(this);" /></td>'+
               '</tr>';
    $('#penerimaan-list tbody').append(list);
    $('#harga'+no).blur(function() {
        FormNum(this);
        check_perubahan_hna(no);
    });
    $('#ed'+no).datepicker({
        changeMonth: true,
        changeYear: true,
        minDate: 0
    });
    $('#harga'+no+', #diskon_rp'+no+', #subtotal'+no+', #diskon_pr'+no+', #jumlah'+no+', #disc_pr, #disc_rp, #materai, #ppn').keyup(function() {
        hitung_sub_total(no);
    });
    $('#satuan'+no).change(function() {
        var id        = $(this).val(); // id_satuan
        var id_barang = $('#id_barang'+no).val();
        var jum       = $('#jumlah'+no).val();
        $.ajax({
            url: '<?= base_url('autocomplete/get_detail_harga_barang_penerimaan') ?>?id_kemasan='+id+'&id_barang='+id_barang+'&jumlah='+jum,
            dataType: 'json',
            cache: false,
            success: function(data) {
                $('#isi'+no).val(data.isi);
                $('#isi_satuan'+no).val(data.isi_sat);
            }
        });
    });
    $('#harga'+no+', #diskon_rp'+no+', #subtotal'+no).css('text-align','right');
    $('#diskon_pr'+no+', #jumlah'+no).css('text-align','center');
    $('#diskon_pr'+no).keyup(function() {
        var jumlah  = $('#jumlah'+no).val();
        var harga   = parseInt(currencyToNumber($('#harga'+no).val()));
        var subtotal= jumlah*harga;
        var disc_pr = ($('#diskon_pr'+no).val()/100);
        $('#diskon_rp'+no).val(numberToCurrency(parseInt(subtotal*disc_pr)));
    });
    $.getJSON('<?= base_url('autocomplete/get_data_kemasan') ?>/'+id_barang, function(data){
        $.each(data, function (index, value) {
            if (value.kemasan !== value.satuan_kecil) {
                label = value.kemasan+' isi: '+value.isi+' '+value.satuan_kecil;
            } else {
                label = value.kemasan;
            }
            $('#satuan'+no).append('<option value="'+value.id+'">'+label+'</option>');
        });
        $('#satuan'+no).val(id_satuan_beli);
    });
    $('#barang,#id_barang,#jumlah,#hna,#isi,#isi_satuan').val('');
    $('#kemasan').html(''); $('#faktur').focus().select();
}

function hitung_sub_total(i) {
    var jumlah      = $('#jumlah'+i).val();
    var harga       = parseInt(currencyToNumber($('#harga'+i).val()));
    var diskon      = parseInt(currencyToNumber($('#diskon_rp'+i).val())); // diskon rupiah yg diambil
    var subtotal    = (harga*jumlah) - diskon;
    $('#subtotal'+i).val(numberToCurrency(subtotal));
    var jml_baris   = $('.tr_rows').length;
    var total       = 0;
    
    for (j = 1; j <= jml_baris; j++) {
        var subttl      = parseInt(currencyToNumber($('#subtotal'+j).val()));
        total = total + subttl;
    }
    
    var ppn         = $('#ppn').val()/100;
    var materai     = parseInt(currencyToNumber($('#materai').val()));
    
    var disc_percent= $('#disc_pr').val()/100; // persentase diskon per faktur
    if (disc_percent !== 0) {
        var dp_total    = total*disc_percent;
        $('#disc_rp').val(numberToCurrency(parseInt(Math.ceil(dp_total))));
        diskon_ttl  = parseInt(currencyToNumber($('#disc_rp').val()));
    }
    else {
        diskon_ttl  = parseInt(currencyToNumber($('#disc_rp').val()));
    }
    var ppn_total   = (total-diskon_ttl)+((total-diskon_ttl)*ppn); // total PPN faktur setelah ditambah dengan total barang
    var disc_ppn_ttl= ppn_total;
    var general_ttl = disc_ppn_ttl+materai;
    
    $('#total').val(numberToCurrency(parseInt(general_ttl)));
}

function form_add_supplier() {
var str = '<div id=form_add>'+
            '<form action="" method=post id="save_barang">'+
            '<?= form_hidden('id_supplier', NULL, 'id=id_supplier') ?>'+
            '<table width=100% class=data-input cellpadding=0 cellspacing=0>'+
                '<tr><td width=40%>Nama Supplier:</td><td><?= form_input('nama', NULL, 'id=nama size=40') ?></td></tr>'+
                '<tr><td>Alamat:</td><td><?= form_input('alamat', NULL, 'id=alamat size=40') ?></td></tr>'+
                '<tr><td width=40%>Email:</td><td><?= form_input('email', NULL, 'id=email size=40') ?></td></tr>'+
                '<tr><td>No. Telp:</td><td><?= form_input('telp', NULL, 'id=telp size=40') ?></td></tr>'+
            '</table>'+
            '</form>'+
            '</div>';
    $('body').append(str);
    $('#form_add').dialog({
        title: 'Tambah Supplier',
        autoOpen: true,
        width: 480,
        height: 220,
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
    $('#save_barang').submit(function() {
        if ($('#nama').val() === '') {
            alert('Nama barang tidak boleh kosong !');
            $('#nama').focus(); return false;
        }
        var cek_id = $('#id_supplier').val();
        $.ajax({
            url: '<?= base_url('referensi/manage_supplier/save') ?>',
            type: 'POST',
            dataType: 'json',
            data: $(this).serialize(),
            cache: false,
            success: function(data) {
                if (data.status === true) {
                    if (cek_id === '') {
                        $('#form_add').dialog('close');
                        $('#id_supplier').val(data.id_supplier);
                        $('#supplier').val(data.nama);
                        $('#tempo').focus();
                    }
                }
            }
        });
        return false;
    });
}

function form_edit_barang() {
    var id_barang = $('#id_barang').val();
    if (id_barang === '') {
        alert_dinamic('Barang belum dipilih', '#barang'); return false;
    }
    form_add_barang();
    $.ajax({
        url: 'models/autocomplete.php?method=get_detail_barang',
        data: 'id='+id_barang,
        dataType: 'json',
        success: function(data) {
            
        }
    });
    $('#barcode').val(arr[30]);
    $('#image').attr('src','img/barang/'+arr[31]);
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
    hitung_hja();
}

function form_add() {
    var str = '<div id="penerimaan"><form id="save_penerimaan">'+
                '<input type=hidden name=id_penerimaan id=id_penerimaan />'+
                '<table width=100% class=data-input><tr valign=top><td width=50%>'+
                    '<table width=100% cellpadding=0 cellspacing=0>'+
                        '<tr><td>No. SP:</td><td><input type=text name=no_sp id=no_sp size=40 /></td></tr>'+
                        '<tr><td>Jenis Penerimaan:</td><td><select name=status id=status style="min-width: 86px;"><option value="Tempo">Tempo</option><option value="Cash">Cash</option><option value="Konsinyasi">Konsinyasi</option></select></td></tr>'+
                        '<tr><td>Faktur:</td><td><input type=text name=faktur id=faktur size=10 /></td></tr>'+
                        '<tr><td>Tanggal:</td><td><input type=text value="<?= date("d/m/Y") ?>" name=tanggal id=tanggal size=10 /></td></tr>'+
                        '<tr><td>Supplier:</td><td><input type=text name=supplier id=supplier size=40 /><input type=hidden name=id_supplier id=id_supplier /> <a class="addition" onclick="form_add_supplier();" title="Klik untuk tambah supplier, jika supplier belum ada">&nbsp;</a></td></tr>'+
                        '<tr><td>Jatuh Tempo:</td><td><input type=text name=tempo id=tempo size=10 value="<?= date("d/m/Y") ?>" /></td></tr>'+
                    '</table>'+
                    '</td><td width=50%>'+
                    '<table width=100% cellpadding=0 cellspacing=0>'+
                        '<tr><td>Diskon:</td><td><input type=text name=disc_pr id=disc_pr value="0" size=10 style="width: 105px;" /> %, Rp. <input type=text name=disc_rp id=disc_rp onblur=FormNum(this); onfocus=javascript:this.value=currencyToNumber(this.value); size=10 value="0" style="width: 108px;" /></td></tr>'+
                        '<tr><td>Materai (Rp.):</td><td><input type=text name=materai onblur=FormNum(this); id=materai size=10 value="0" /></td></tr>'+
                        '<tr><td>PPN:</td><td><input type=text name=ppn id=ppn onblur="check_hna();" size=10 value="0" /> %</td></tr>'+
                        '<tr><td>Total (Rp.):</td><td><input type=text name=total id=total size=10 readonly /></td></tr>'+
                        '<tr><td width=20%>Nama Barang:</td><td width=50%><?= form_input('barang', NULL, 'id=barang size=40') ?><a class="addition" onclick="form_add_barang();" title="Klik untuk tambah barang">&nbsp;</a><?= form_hidden('id_barang', NULL, 'id=id_barang') ?><?= form_hidden(NULL, NULL, 'id=hna') ?><?= form_hidden(NULL, NULL, 'id=isi') ?><?= form_hidden(NULL, NULL, 'id=isi_satuan') ?></td></tr>'+
                        '<tr><td>Kemasan & Jumlah:</td><td><select name=id_kemasan id=kemasan style="min-width: 86px;"><option value="">Pilih ...</option></select> & <?= form_input('jumlah', NULL, 'id=jumlah size=10') ?> <a class="edition" onclick="form_edit_barang();" title="Klik untuk edit barang">&nbsp;</a></td></tr>'+
                    '</table>'+
                '</td></tr></table>'+
                '<table width=100% cellspacing="0" class="list-data-input" id="penerimaan-list"><thead>'+
                    '<tr>'+
                        '<th width=5%>No.</th>'+
                        '<th width=25%>Nama Barang</th>'+
                        '<th width=10%>Kemasan</th>'+
                        '<th width=5%>Jumlah</th>'+
                        '<th width=10%>No. Batch</th>'+
                        '<th width=10%>ED</th>'+
                        '<th width=10%>Harga @</th>'+
                        '<th width=5%>Diskon (%)</th>'+
                        '<th width=10%>Diskon Rp.</th>'+
                        '<th width=10%>SubTotal Rp.</th>'+
                        '<th width=10%>#&nbsp;#</th>'+
                    '</tr></thead>'+
                    '<tbody></tbody>'+
                '</table>'+
              '</form></div>';
    $('body').append(str);
    $('#tempo,#tanggal').datepicker({
        changeYear: true,
        changeMonth: true
    });
    $('input:text').on("keydown", function(e) {
        var n = $("input:text").length;
        if (e.keyCode === 13) {
            var nextIndex = $('input:text').index(this) + 1;
            if (nextIndex < n) {
                $('input:text')[nextIndex].focus();
            }
        }
    });
    $('#jumlah').keydown(function(e) {
        if (e.keyCode === 13) {
            var id_barang       = $('#id_barang').val();
            var nama_barang     = $('#barang').val();
            var id_satuan_beli  = $('#kemasan').val().split('-');
            var jumlah          = $('#jumlah').val();
            var hna             = $('#hna').val();
            var isi             = id_satuan_beli[1];
            var isi_satuan      = id_satuan_beli[2];
            load_list_data(id_barang, nama_barang, id_satuan_beli[0], jumlah, hna, isi, isi_satuan);
        }
    });
    $('#kemasan').keydown(function(e) {
        if (e.keyCode === 13) {
            $('#jumlah').focus();
        }
    });
    $('#kemasan').change(function() {
        var id        = $(this).val(); // id_satuan
        var id_barang = $('#id_barang').val();
        var jum       = $('#jumlah').val();
        $.ajax({
            url: 'models/autocomplete.php?method=get_detail_harga_barang_penerimaan&id_kemasan='+id+'&id_barang='+id_barang+'&jumlah='+jum,
            dataType: 'json',
            cache: false,
            success: function(data) {
                $('#isi').val(data.isi);
                $('#isi_satuan').val(data.isi_sat);
            }
        });
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
        $('#hna').val(data.hna);
        $('#kemasan').html('').focus();
        $.getJSON('models/autocomplete.php?method=get_kemasan_barang&id='+data.id, function(data){
            if (data === null) {
                alert('Kemasan barang tidak tersedia !');
            } else {
                $.each(data, function (index, value) {
                    $('#kemasan').append("<option value='"+value.id_kemasan+"-"+value.isi+"-"+value.isi_satuan+"'>"+value.nama+"</option>");
                });
            }
        });
    });
    var wWidth = $(window).width();
    var dWidth = wWidth * 1;
    
    var wHeight= $(window).height();
    var dHeight= wHeight * 1;
    $('#penerimaan').dialog({
        title: 'Penerimaan Barang',
        autoOpen: true,
        modal: true,
        width: dWidth,
        height: dHeight,
        hide: 'clip',
        show: 'blind',
        buttons: {
            "Simpan": function() {
                $('#save_penerimaan').submit();
            }, 
            "Cancel": function() {    
                $(this).dialog().remove();
                $.cookie('session', 'false');
            }, "Reset": function() {
                $('#no_sp, #supplier, #id_suppplier, #tempo, #total').val('');
                $('#penerimaan-list tbody').html('');
            }
        }, close: function() {
            $(this).dialog().remove();
            $.cookie('session', 'false');
        }, open: function() {
            $('#no_sp').focus();
            $.cookie('session', 'true');
            /*$.ajax({
                url: 'models/autocomplete.php?method=get_attr_penerimaan',
                cache: false,
                dataType: 'json',
                success: function(msg) {
                    $('#faktur').val(msg.faktur);
                }
            });*/
        }
    });
    var lebar = $('#supplier').width();
    $('#no_sp').autocomplete("<?= base_url('autocomplete/get_nomor_sp') ?>",
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
            var str = '<div class=result>'+data.id+'<br/> '+data.supplier+'</div>';
            return str;
        },
        width: lebar, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
        dataType: 'json', // tipe data yang diterima oleh library ini disetup sebagai JSON
        cacheLength: 0
    }).result(
    function(event,data,formated){
        $(this).val(data.id);
        $('#supplier').val(data.supplier);
        $('#id_supplier').val(data.id_supplier);
        $('#penerimaan-list tbody').html('');
        /*$.ajax({
            url: 'models/autocomplete.php?method=get_attr_penerimaan',
            cache: false,
            dataType: 'json',
            success: function(msg) {
                $('#faktur').val(msg.faktur);
                $('#tempo').val(msg.tempo);
                $('#ppn,#materai,#disc_rp, #disc_pr').val('0');
            }
        });*/
        $.getJSON('<?= base_url('autocomplete/get_data_pemesanan_penerimaan') ?>?id='+data.id, function(data){
            $.each(data, function (index, value) {
                // function here
                load_list_data(value.id_barang, value.nama+' '+value.kekuatan+' '+value.satuan_kekuatan, value.id_kemasan, value.jumlah, value.hna, value.isi, value.isi_satuan);
            });
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
    });
    $('#save_penerimaan').submit(function() {
        if ($('#faktur').val() === '') {
            alert_empty('Faktur','#faktur'); return false;
        }
        if ($('#id_supplier').val() === '') {
            alert_empty('Supplier','#supplier'); return false;
        }
        if ($('#status').val() === 'Tempo') {
            if ($('#tempo').val() === '') {
                alert_empty('Jatuh tempo','#tempo'); return false;
            }
        }
        var jml_baris = $('.tr_rows').length;
        for (i = 1; i <= jml_baris; i++) {
            if ($('#satuan'+i).val() === '') {
                alert_empty('Kemasan','#satuan'+i);
                return false;
            }
            if ($('#jumlah'+i).val() === '') {
                alert_empty('Jumlah', '#jumlah'+i);
                return false;
            }
            if ($('#ed'+i).val() === '') {
                alert_empty('Expired date','#ed'+i);
                return false;
            }
            if ($('#harga'+i).val() === '') {
                alert_empty('Harga','#harga'+i);
                return false;
            }
        }
        $('<div id=alert>Anda yakin akan melakukan transaksi penerimaan ini?</div>').dialog({
            title: 'Konfirmasi',
            autoOpen: true,
            modal: true,
            buttons: {
                "OK": function() {
                    $.ajax({
                        url: '<?= base_url('inventory/manage_penerimaan/save') ?>',
                        type: 'POST',
                        dataType: 'json',
                        data: $('#save_penerimaan').serialize(),
                        cache: false,
                        success: function(data) {
                            $('#alert').dialog().remove();
                            if (data.status === true) {
                                if (data.action === 'add') {
                                    alert_refresh('Data berhasil disimpan');
                                    load_data_penerimaan();
                                } else {
                                    alert_refresh('Data berhasil diupdate');
                                    load_data_penerimaan();
                                }
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
    load_data_penerimaan();
});
function load_data_penerimaan(page, search, id) {
    pg = page; src = search; id_barg = id;
    if (page === undefined) { var pg = ''; }
    if (search === undefined) { var src = ''; }
    if (id === undefined) { var id_barg = ''; }
    $.ajax({
        url: '<?= base_url('inventory/manage_penerimaan/list') ?>/'+page,
        cache: false,
        data: 'search='+src+'&id='+id_barg,
        success: function(data) {
            $('#result-penerimaan').html(data);
        }
    });
}

function paging(page, tab, search) {
    load_data_penerimaan(page, search);
}
function delete_penerimaan(id, page) {
    $('<div id=alert>Anda yakin akan menghapus data ini?</div>').dialog({
        title: 'Konfirmasi Penghapusan',
        autoOpen: true,
        modal: true,
        buttons: {
            "OK": function() {
                
                $.ajax({
                    url: '<?= base_url('inventory/manage_penerimaan/delete') ?>?id='+id,
                    cache: false,
                    success: function() {
                        load_data_penerimaan(page);
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

function edit_penerimaan(data) {
    var arr = data.split('#');
    form_add();
    $('#id_penerimaan').val(arr[0]);
    $('#no_sp').val(arr[5]);
    $('#faktur').val(arr[1]);
    $('#status').val(arr[12]);
    $('#tanggal').val(arr[2]);
    $('#supplier').val(arr[4]);
    $('#id_supplier').val(arr[3]);
    $('#disc_pr').val(arr[9]);
    $('#disc_rp').val(arr[10]);
    $('#materai').val(numberToCurrency(arr[7]));
    $('#ppn').val(arr[6]);
    $('#total').val(arr[11]);
    $('#tempo').val(arr[13]);
    $.getJSON('<?= base_url('autocomplete/get_data_penerimaan') ?>/'+arr[0], function(data){
        $.each(data, function (index, value) {
            // function here
            load_list_data(value.id_barang, value.nama_barang+' '+value.kekuatan+' '+value.satuan_kekuatan, value.id_kemasan, value.jumlah, value.hna, value.isi, value.isi_satuan);
            $('#nobatch'+(index+1)).val(value.nobatch);
            $('#ed'+(index+1)).val(datefmysql(value.expired));
            $('#harga'+(index+1)).val(value.harga);
            $('#diskon_pr'+(index+1)).val(value.disc_pr);
            $('#diskon_rp'+(index+1)).val(numberToCurrency(value.disc_rp));
            hitung_sub_total((index+1));
        });
    });
}
</script>
<div class="titling"><h1><?= $title ?></h1></div>
<div class="kegiatan">
    <button id="button">Tambah Penerimaan (F9)</button>
    <button id="reset">Reset</button>
    <div id="result-penerimaan">

    </div> 
</div>