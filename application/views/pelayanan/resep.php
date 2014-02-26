<?php $array = array(
    '1' => array('1','1'),
    '2' => array('1/4','0.25'),
    '3' => array('1/3','0.33'),
    '4' => array('1/2','0.5'),
    '5' => array('1,5','1.5'),
    '6' => array('2','2'),
    '7' => array('3','3'),
    '8' => array('4','4'),
    '9' => array('5','5'));
?>
<script type="text/javascript">
    //$(document).tooltip();
    $('#nor').click(function() {
        
    });
    $.cookie('session', 'false');
    $(document).keydown(function(e) {
        if (e.keyCode === 120) { 
            //alert($.cookie('session'));
            if ($.cookie('session') === 'false') {
                $('#button').click();
            }
        }
    });
    load_data_resep();
    $('#button').button({
        icons: {
            secondary: 'ui-icon-newwin'
        }
    }).click(function() {
        form_receipt();
    });
    $('#reset').button({
        icons: {
            secondary: 'ui-icon-refresh'
        }
    }).click(function() {
        load_data_resep();
    });
    $('#addnewrows').click(function() {
        var row = $('.masterresep').length;
        addnoresep(row);
    });

function entri_field(id_pasien) {
    $.ajax({
        url: '<?= base_url('pelayanan/get_kunjungan_pelayanan') ?>/'+id_pasien,
        dataType: 'json',
        success: function(data) {
            $('#id_kp').val(data.id_kp);
        }
    });
}

function subTotal() {
    
    var jumlah = $('.tr_row').length-1;
    var total_jasa = 0;
    for(i = 0; i<= jumlah; i++) {
        var valjasa  = $('#ja'+i).val();
        var n=valjasa.split("-");
        var jasa = parseInt(n[1]);
        var total_jasa = total_jasa + jasa;
    }
    $('#totalbiaya').html(numberToCurrency(total_jasa));
}

function eliminate(el) {
    $('<div id=alert>Anda yakin akan menghapus data ini?</div>').dialog({
        title: 'Konfirmasi Penghapusan',
        autoOpen: true,
        modal: true,
        buttons: {
            "OK": function() {
                $('#alert').dialog().remove();
                var parent = el.parentNode.parentNode.parentNode.parentNode;
                parent.parentNode.removeChild(parent);
                var jumlah = $('.tr_row').length-1;
                for (i = 0; i <= jumlah; i++) {

                    $('.tr_row:eq('+i+')').children('.masterresep:eq(0)').children('.nr').attr('value',(i+1));
                    $('.tr_row:eq('+i+')').children('.masterresep:eq(0)').children('.nr').attr('id','nr'+i);
                    $('.tr_row:eq('+i+')').children('.psdg-right:eq(0)').children('.jr').attr('id','jr'+i);
                    $('.tr_row:eq('+i+')').children('.psdg-right:eq(0)').children('.jt').attr('id','jt'+i);
                    $('.tr_row:eq('+i+')').children('.psdg-right:eq(0)').children('.ap').attr('id','ap'+i);
                    $('.tr_row:eq('+i+')').children('.psdg-right:eq(0)').children('.it').attr('id','it'+i);
                    $('.tr_row:eq('+i+')').children('.psdg-right:eq(0)').children('.ja').attr('id','ja'+i);
                    $('.tr_row:eq('+i+')').children('.psdg-right:eq(0)').children('.ad').attr('id','ad'+i);
                    $('.tr_row:eq('+i+')').children('.psdg-right:eq(0)').children('.de').attr('id','de'+i);
                }
            },
            "Cancel": function() {
                $(this).dialog().remove();
            }
        }
    });
    
}

function eliminatechild(el,x,y) {
    ok=confirm('Anda yakin akan menghapus data ini ?');
    if (ok) {

        var parent = el.parentNode.parentNode.parentNode.parentNode.parentNode;
        parent.parentNode.removeChild(parent);
        var jumlah = $('.tr_rows').length-1;
    } else {
        return false;
    }
}

function addnoresep() {
    var jasa_apt    = $('#ja').val().split('-');
    var i           = $('.tr_rows').length+1;
    var barang      = $('#pb').val();
    var id_barang   = $('#id_pb').val();
    var no_r        = $('#nr').val();
    var permintaan  = $('#jr').val();
    var tebus       = $('#jt').val();
    //var aturan_pakai= $('#a').val()+'DD'+$('#p').val();
    var a           = $('#a').val();
    var p           = $('#p').val();
    var iterasi     = $('#it').val();
    var jasa        = jasa_apt[1];
    var kekuatan    = $('#kekuatan').html();
    var dosis_racik = $('#dr').val();
    var jml_pakai   = $('#jmlpakai').val();
    var str = '<tr class="tr_rows">'+
                '<td align=center>'+i+'</td>'+
                '<td><input type=text name=no_r[] id=no_r'+i+' class=no_r value="'+no_r+'" style="text-align: center;" /></td>'+
                '<td>'+barang+' <input type=hidden name=id_barang[] id=id_barang'+i+' value="'+id_barang+'" class=id_barang style="text-align: center;" /></td>'+
        /*3*/   '<td><input type=text name=jp[] id=jp'+i+' class=jp value="'+permintaan+'" style="text-align: center;" /></td>'+
                '<td><input type=text name=jt[] id=jt'+i+' class=jt value="'+tebus+'" style="text-align: center;" /></td>'+
                '<td align=center id=sisa'+i+'></td>'+
                '<td><input type=text name=a[] id=a'+i+' value="'+a+'" class=a style="text-align: right; width: 40%" /> X <input type=text name=p[] id=p'+i+' value="'+p+'" class=p style="text-align: left; width: 40%" /></td>'+
                '<td><input type=text name=it[] id=it'+i+' value="'+iterasi+'" class=it style="text-align: center;" /></td>'+
                '<td><input type=text name=dr[] id=dr'+i+' value="'+dosis_racik+'" class=dr style="text-align: center;" /></td>'+
                '<td><input type=text name=jpi[] id=jpi'+i+' value="'+jml_pakai+'" class=jpi style="text-align: center;" /></td>'+
                '<td><input type=hidden name=id_tarif[] id=id_tarif'+i+' value="'+jasa_apt[0]+'" class=jasa_apt /> <input type=text name=jasa[] id=jasa'+i+' class=jasa onkeyup=FormNum(this) value="'+numberToCurrency(jasa)+'" style="text-align: right;" /></td>'+
                '<td><input type=text name=hrg_barang[] id=hrg_barang'+i+' readonly class=hrg_barang style="text-align: right;" /></td>'+
                '<td class=aksi><img onclick=removeThis(this); title="Klik untuk hapus" src="<?= base_url('assets/images/delete.png') ?>" class=add_kemasan align=left /></td>'+
              '</tr>';
        $('#resep-list tbody').append(str);
        $.ajax({
            url: '<?= base_url('autocomplete/get_detail_harga_barang_resep') ?>?id='+id_barang+'&jumlah='+jml_pakai,
            dataType: 'json',
            cache: false,
            success: function(data) {
                //hitung_detail_total(jml, jum, data.diskon_rupiah, data.diskon_persen, data.harga_jual);
                $('#hrg_barang'+i).val(numberToCurrency(parseInt(data.harga_jual*jml_pakai)));
                total_perkiraan_resep();
            }
        });
        $.ajax({
            url: '<?= base_url('autocomplete/get_stok_sisa') ?>/'+id_barang,
            dataType: 'json',
            cache: false,
            success: function(data) {
                if (data.sisa === null) {
                    sisa = '0';
                } else {
                    sisa = data.sisa;
                }
                $('#sisa'+i).html(sisa);
            }
        });
        check_alergi_obat_pasien(i, $('#id_pasien').val(), id_barang);
}

function check_alergi_obat_pasien(i, id_pasien, id_barang) {
    if ((id_pasien !== '') && (id_barang !== '')) {
        $.ajax({
            url: '<?= base_url('autocomplete/check_alergi_obat_pasien') ?>?id_barang='+id_barang+'&id_pasien='+id_pasien,
            cache: false,
            dataType: 'json',
            success: function(data) {
                if (data.jumlah === '1') {
                    alert_dinamic('HATI-HATI!, Pasien ini memiliki alergi terhadap '+data.nama+'!','');
                    $('.tr_rows:eq('+(i-1)+')').css({background: "purple", color: "white"});
                }
            }
        });
    }
}

function removeThis(el) {
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
                    $('.tr_rows:eq('+col+')').children('td:eq(1)').children('.no_r').attr('id','no_r'+i);
                    $('.tr_rows:eq('+col+')').children('td:eq(2)').children('.id_barang').attr('id','id_barang'+i);
                    $('.tr_rows:eq('+col+')').children('td:eq(3)').children('.jp').attr('id','jp'+i);
                    $('.tr_rows:eq('+col+')').children('td:eq(4)').children('.jt').attr('id','jt'+i);
                    $('.tr_rows:eq('+col+')').children('td:eq(5)').attr('id','sisa'+i);
                    $('.tr_rows:eq('+col+')').children('td:eq(6)').children('.a').attr('id','a'+i);
                    $('.tr_rows:eq('+col+')').children('td:eq(6)').children('.p').attr('id','p'+i);
                    $('.tr_rows:eq('+col+')').children('td:eq(7)').children('.it').attr('id','it'+i);
                    $('.tr_rows:eq('+col+')').children('td:eq(8)').children('.dr').attr('id','dr'+i);
                    $('.tr_rows:eq('+col+')').children('td:eq(9)').children('.jpi').attr('id','jpi'+i);
                    $('.tr_rows:eq('+col+')').children('td:eq(10)').children('.jasa_apt').attr('id','id_tarif'+i);
                    $('.tr_rows:eq('+col+')').children('td:eq(10)').children('.jasa').attr('id','jasa'+i);
                    $('.tr_rows:eq('+col+')').children('td:eq(11)').children('.hrg_barang').attr('id','hrg_barang'+i);
                    col++;
                }
                total_perkiraan_resep();
            }
        }
});
}

function total_perkiraan_resep() {
    var jumlah  = $('.tr_rows').length;
    var total   = 0;
    //var jasa    = 0;
    for (i = 1; i <= jumlah; i++) {
        var subtotal = parseInt(currencyToNumber($('#hrg_barang'+i).val()));
        var jasa     = parseInt(currencyToNumber($('#jasa'+i).val()));
        total   = total + subtotal + jasa;
    }
    $('#total').html(numberToCurrency(parseInt(total)));
    $('#totallica').val(total);
}

function hitung_jml_pakai() {
    var dosis_racik = ($('#dr').val())*1;
    var jumlah_tbs  = parseInt($('#jt').val());
    var kekuatan    = ($('#kekuatan').html())*1;
    //alert(dosis_racik+' '+jumlah_tbs+' '+kekuatan);
    
    var jumlah_pakai= (dosis_racik*jumlah_tbs)/kekuatan;
    var jml_pakai = isNaN(jumlah_pakai)?'':jumlah_pakai;
    $('#jmlpakai').val(jml_pakai);
}

function cetak_copy_resep(id_resep) {
    var wWidth = $(window).width();
    var dWidth = wWidth * 0.3;
    var wHeight= $(window).height();
    var dHeight= wHeight * 1;
    var x = screen.width/2 - dWidth/2;
    var y = screen.height/2 - dHeight/2;
    window.open('<?= base_url('pelayanan/manage_resep/cetak_copy') ?>?id='+id_resep,'Resep Cetak','width='+dWidth+', height='+dHeight+', left='+x+',top='+y);
}

function cetak_kitir(id_resep) {
    var wWidth = $(window).width();
    var dWidth = wWidth * 0.3;
    var wHeight= $(window).height();
    var dHeight= wHeight * 1;
    var x = screen.width/2 - dWidth/2;
    var y = screen.height/2 - dHeight/2;
    window.open('<?= base_url('pelayanan/manage_resep/cetak_kitir') ?>?id='+id_resep,'Resep Cetak','width='+dWidth+', height='+dHeight+', left='+x+',top='+y);
}

function print_etiket(id_resep, no_r) {
    var wWidth = $(window).width();
    var dWidth = wWidth * 0.3;
    var wHeight= $(window).height();
    var dHeight= wHeight * 1;
    var x = screen.width/2 - dWidth/2;
    var y = screen.height/2 - dHeight/2;
    window.open('<?= base_url('pelayanan/manage_resep/cetak_etiket') ?>?id_resep='+id_resep+'&no_r='+no_r,'Etiket','width='+dWidth+', height='+dHeight+', left='+x+',top='+y);
}

function form_add_pelanggan() {
var str = '<div id=form_add>'+
            '<form action="models/update-masterdata.php?method=save_pelanggan" enctype=multipart/form-data method=post id="save_barang">'+
            '<input type=hidden name=id_pelanggan id=id_pelanggan />'+
            '<table width=100% class=data-input cellpadding=0 cellspacing=0>'+
                '<tr><td width=30%>Nama:</td><td width=70%><?= form_input('nama', NULL, 'id=nama size=40 onBlur="javascript:this.value=this.value.toUpperCase();"') ?></td></tr>'+
                '<tr><td>Jenis:</td><td><input type="radio" name=jenis value="Personal" checked id="p" /> <label for="p">Personal</label> <input type="radio" name=jenis value="Perusahaan" id="pr" /> <label for="pr">Perusahaan</label></td></tr>'+
                '<tr><td>Kelamin:</td><td><input type="radio" name=kelamin value="P" checked id="prm" /> <label for="prm">Perempuan</label> <input type="radio" name=kelamin value="L" id="l" /> <label for="l">Laki-laki</label></td></tr>'+
                '<tr><td>Tempat / Tgl Lahir:</td><td><input type=text name=tmp_lahir size=5 style="min-width: 200px;" id=tmp_lahir /> / <input type=text name=tgl_lahir style="min-width: 80px;" size=5 id="tgl_lahir" /></td></tr>'+
                '<tr><td>Alamat:</td><td><?= form_input('alamat', NULL, 'id=alamat size=40 onBlur="javascript:this.value=this.value.toUpperCase();"') ?><input type=hidden name="id_pabrik" /></td></tr>'+
                '<tr><td>Kota / Kodya:</td><td><?= form_input('kota', '', 'id=kota size=40') ?></td></tr>'+
                '<tr><td>Provinsi:</td><td><?= form_input('provinsi', '', 'id=provinsi size=40') ?></td></tr>'+
                '<tr><td>Telp:</td><td><?= form_input('telp', '', 'id=telp size=40') ?></td></tr>'+
                '<tr><td>Email:</td><td><?= form_input('email','', 'id=email size=40') ?></td></tr>'+
                '<tr><td>Diskon:</td><td><?= form_input('diskon', '', 'id=diskon size=40') ?></td></tr>'+
                '<tr><td>Catatan:</td><td><?= form_input('catatan', '', 'id=catatan size=40') ?></td></tr>'+
                '<tr><td>Asuransi:</td><td><select name="asuransi" id="asuransi"><option value="">Pilih asuransi ...</option><?php foreach ($asuransi as $data) { echo '<option value="'.$data->id.'">'.$data->nama.'</option>'; } ?></select></td></tr>'+
                '<tr><td>No. Polish:</td><td><?= form_input('nopolish', '', 'id=nopolish size=40') ?></td></tr>'+
                '<tr><td>Foto:</td><td><?= form_upload('mFile',null,'id=mFile') ?></td></tr>'+
            '</table>'+
            '</form>'+
            '</div>';
    $('body').append(str);
    $('input[type=text]').blur(function() {
        this.value=this.value.toUpperCase();
    });
    $('#form_add').dialog({
        title: 'Tambah Customer',
        autoOpen: true,
        width: 480,
        height: 430,
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
            alert('Nama pelanggan tidak boleh kosong !');
            $('#nama').focus(); return false;
        }
        var cek_id = $('#id_pelanggan').val();
        $(this).ajaxSubmit({
            target: '#output',
            dataType: 'json',
            success:  function(data) {
                if (data.status === true) {
                    if (cek_id === '') {
                        $('#form_add').dialog('close');
                        $('#pasien').val(data.nama);
                        $('#id_pasien').val(data.id_pelanggan);
                    }
                }
            }
        });
        return false;
    });
}

function form_add_dokter() {
var str = '<div id=form_add_dokter>'+
            '<form action="" method=post id="save_dokter">'+
            '<?= form_hidden('id_dokter', NULL, 'id=id_dokter') ?>'+
            '<table width=100% class=data-input cellpadding=0 cellspacing=0>'+
                '<tr><td width=30%>Nama:</td><td width=70%><?= form_input('nama', NULL, 'id=nama size=40 onBlur="javascript:this.value=this.value.toUpperCase();"') ?></td></tr>'+
                '<tr><td>Kelamin:</td><td><input type="radio" name=kelamin value="P" checked id="prm" /> <label for="prm">Perempuan</label> <input type="radio" name=kelamin value="L" id="l" /> <label for="l">Laki-laki</label></td></tr>'+
                '<tr><td>Alamat:</td><td><?= form_input('alamat', NULL, 'id=alamat size=40 onBlur="javascript:this.value=this.value.toUpperCase();"') ?><input type=hidden name="id_pabrik" /></td></tr>'+
                '<tr><td>Telp:</td><td><?= form_input('telp', '', 'id=telp size=40') ?></td></tr>'+
                '<tr><td>Email:</td><td><?= form_input('email','', 'id=email size=40') ?></td></tr>'+
                '<tr><td>No. STR:</td><td><?= form_input('nostr', '', 'id=nostr size=40') ?></td></tr>'+
                '<tr><td>Spesialis:</td><td><?= form_input('spesialis', '', 'id=spesialis size=40') ?></td></tr>'+
                '<tr><td>Tgl Mulai Praktek:</td><td><?= form_input('tglmulai', '', 'id=tglmulai size=40') ?></td></tr>'+
                '<tr><td>Fee:</td><td><?= form_input('fee', '', 'id=fee size=10') ?> %</td></tr>'+
            '</table>'+
            '</form>'+
            '</div>';
    $('body').append(str);
    $('input[type=text]').blur(function() {
        this.value=this.value.toUpperCase();
    });
    $('#form_add_dokter').dialog({
        title: 'Tambah Dokter',
        autoOpen: true,
        width: 480,
        height: 350,
        modal: true,
        hide: 'clip',
        show: 'blind',
        buttons: {
            "Simpan": function() {
                $('#save_dokter').submit();
            }, "Cancel": function() {
                $(this).dialog().remove();
            }
        }, close: function() {
            $(this).dialog().remove();
        }
    });
    $('#tglmulai').datepicker({
        maxDate: 0,
        changeYear: true,
        changeMonth: true
    });
    
    $('#save_dokter').submit(function() {
        if ($('#nama').val() === '') {
            alert('Nama dokter tidak boleh kosong !');
            $('#nama').focus(); return false;
        }
        var cek_id = $('#id_dokter').val();
        $.ajax({
            url: '<?= base_url('referensi/manage_dokter/save') ?>',
            type: 'POST',
            dataType: 'json',
            data: $(this).serialize(),
            cache: false,
            success: function(data) {
                if (data.status === true) {
                    if (cek_id === '') {
                        $('#form_add_dokter').dialog('close');
                        $('#dokter').val(data.nama);
                        $('#id_dokter').val(data.id_dokter);
                    }
                }
            }
        });
        return false;
    });
}

function form_receipt() {
    var str = '<div id=form_resep>'+
                '<form id=resep_save>'+
                '<input type=hidden name=id_resep id=id_resep />'+
                '<?= form_hidden('id_kp', NULL, 'id=id_kp') ?>'+
                '<table width=100% class=data-input><tr valign=top><td width=33% style="border-right: 1px solid #ccc;">'+
                    '<table width=100% cellpadding=0 cellspacing=0>'+
                        '<tr><td width=25%>Nomor Resep:</td><td><?= form_input('noresep', NULL, 'id=noresep size=10') ?></td></tr>'+
                        '<tr><td>Waktu:</td><td><?= form_input('waktu', date("d/m/Y"), 'id=waktu size=10') ?></td></tr>'+
                        '<tr><td>Dokter:</td><td><?= form_input('dokter', NULL, 'id=dokter') ?><?= form_hidden('id_dokter', NULL, 'id=id_dokter') ?> </td></tr>'+ //<a class="addition" onclick="form_add_dokter();" title="Klik untuk tambah dokter, jika dokter belum ada">&nbsp;</a>
                        '<tr><td>Pasien:</td><td><?= form_input('pasien', NULL, 'id=pasien') ?><?= form_hidden('id_pasien', NULL, 'id=id_pasien') ?> </td></tr>'+ //<a class="addition" onclick="form_add_pelanggan();" title="Klik untuk tambah pasien, jika pasiem belum ada">&nbsp;</a>
                        '<tr><td>Keterangan:</td><td><?= form_input('keterangan', NULL, 'id=keterangan') ?></td></tr>'+
                    '</table></td><td width=33% style="padding-left: 10px; border-right: 1px solid #ccc;">'+
                    '<table width=100% cellpadding=0 cellspacing=0>'+
                        '<tr><td width=25%>No. R/:</td><td><input type=text name=nr id=nr value="1" class=nr size=20 onkeyup=Angka(this) maxlength=2 /></td></tr>'+
                        '<tr><td>Permintaan:</td><td><input type=text name=jr id=jr class=jr size=20 onkeyup=Angka(this) /></td></tr>'+
                        '<tr><td>Jumlah Tebus:</td><td><input type=text name=jt id=jt class=jt onblur="hitung_jml_pakai();" onkeyup=Angka(this) size=20 /></td></tr>'+
                        '<tr><td>Aturan Pakai:</td><td><select name=a id=a style="max-width: 65px;">'+
                        '<?php for ($i = 1; $i<=10;$i++) { echo '<option value="'.$i.'">'.$i.'</option>'; } ?></select> X <select name=p id=p style="max-width: 65px;">'+
                        '<?php foreach($array as $key => $i) { echo '<option value="'.$i[1].'">'.$i[0].'</option>'; } ?></select>'+
                        '</td></tr>'+
                        '<tr><td>Iterasi:</td><td><input type=text name=it id=it class=it size=20 value="0" onkeyup=Angka(this) /></td></tr>'+
                        '<tr><td>Jasa Apoteker:</td><td><select onchange="subTotal()" name=ja id=ja><option value="0-0">Pilih biaya ..</option><?php foreach ($biaya_apoteker as $value) { echo '<option value="'.$value->id.'-'.$value->nominal.'"> Rp. '.$value->nominal.' '.$value->nama.'</option>'; } ?></select><br/> F4 = No. R/ selanjutnya</td></tr>'+
                    '</table>'+
                    '</td><td width=33% style="padding-left: 10px;">'+
                    '<table align=right width=100% cellpadding=0 cellspacing=0>'+
                        '<tr><td width=25%>Nama Produk:</td><td>  <input type=text name=pb id=pb class=pb />'+
                            '<input type=hidden name=id_pb id=id_pb class=id_pb /></td></tr>'+
                        '<tr><td>Kekuatan:</td><td><span class=label id=kekuatan>-</span></td></tr>'+
                        '<tr><td>Dosis Racik:</td><td> <input type=text name=dr id=dr class=dr size=10 onblur="hitung_jml_pakai();" /></td></tr>'+
                        '<tr><td>Jumlah Pakai:</td><td><?= form_input('jmlpakai', NULL, 'id=jmlpakai size=10') ?></td></tr>'+
                        '<tr><td>TOTAL:</td><td style="font-size: 30px;"><input type=hidden name="totallica" id="totallica" /><span>Rp </span><span id=total style="font-size: 45px;"></span>,00</td></tr>'+
                    '</table>'+
                    '</td></tr></table>'+
                '<table width=100% cellspacing="0" class="list-data-input" id="resep-list"><thead>'+
                    '<tr><th width=3%>No.</th>'+
                        '<th width=3%>No. R</th>'+
                        '<th width=25%>Nama Barang</th>'+
                        '<th width=10%>Jumlah<br/>Permintaan</th>'+
                        '<th width=10%>Jumlah<br/>Tebus</th>'+
                        '<th width=5%>Sisa<br/>Stok</th>'+
                        '<th width=10%>Aturan Pakai</th>'+
                        '<th width=5%>Iterasi</th>'+
                        '<th width=8%>Dosis Racik</th>'+
                        '<th width=8%>Jumlah Pakai</th>'+
                        '<th width=10%>Jasa Apoteker</th>'+
                        '<th width=14%>Harga Barang</th>'+
                        '<th width=2%>#</th>'+
                    '</tr></thead>'+
                    '<tbody></tbody>'+
                '</table>'+
                '</form>'+
              '</div>';
    $('body').append(str);
    $('#form_resep').keydown(function(e) {
        if (e.keyCode === 119) {
            $('#resep_save').submit();
        }
        if (e.keyCode === 115) {
            if ($.cookie('session') === 'true') {
                var prev = $.cookie('nomor_r');
                var next = parseInt(prev)+1;
                $('#nr').val(next);
                $('#jr,#jt,#ap,#ja').val('');
                $('#it').val('0');
                $('#jr').focus();
                $.cookie('nomor_r', next);
            }
        }
    });
    var lebar = $('#dokter').width();
    $('#jmlpakai').keydown(function(e) {
        if (e.keyCode === 13) {
            addnoresep();
            $('#pb,#id_pb,#dr,#jmlpakai,#ja').val('');
            $('#kekuatan').html('-');
            $('#pb').focus();
        }
    });
    $('#keterangan').keydown(function(e) {
        if (e.keyCode === 13) { $('#nr').focus().select(); }
    });
    $('#nr').keydown(function(e) {
        if (e.keyCode === 13) { $('#jr').focus().select(); }
    });
    $('#jr').keydown(function(e) {
        if (e.keyCode === 13) { $('#jt').focus().select(); }
    });
    $('#jt').keydown(function(e) {
        if (e.keyCode === 13) { $('#a').focus().select(); }
    });
    $('#a').keydown(function(e) {
        if (e.keyCode === 13) { $('#p').focus().select(); }
    });
    $('#p').keydown(function(e) {
        if (e.keyCode === 13) { $('#it').focus().select(); }
    });
    $('#it').keydown(function(e) {
        if (e.keyCode === 13) { $('#ja').focus().select(); }
    });
    $('#ja').keydown(function(e) {
        if (e.keyCode === 13) { $('#pb').focus().select(); }
    });
    $('#dr').keydown(function(e) {
        if (e.keyCode === 13) { $('#jmlpakai').focus().select(); }
    });
    $('#pb').autocomplete("<?= base_url('autocomplete/barang') ?>",
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
        $('#id_pb').val(data.id);
        $('#kekuatan').html(data.kekuatan);
        $('#dr').val(data.kekuatan);
        hitung_jml_pakai();
        $('#dr').focus().select();
    });
    $('#dokter').autocomplete("<?= base_url('autocomplete/dokter') ?>",
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
            var str = '<div class=result>'+data.nama+'<br/> '+data.str_no+'</div>';
            return str;
        },
        width: lebar, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
        dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
    }).result(
    function(event,data,formated){
        $(this).val(data.nama);
        $('#id_dokter').val(data.id);
        $('#pasien').focus().select();
    });
    $('#pasien').autocomplete("<?= base_url('autocomplete/pasien') ?>",
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
            var str = '<div class=result>'+data.no_rm+' '+data.nama+'<br/> '+data.alamat+'</div>';
            return str;
        },
        width: lebar, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
        dataType: 'json', // tipe data yang diterima oleh library ini disetup sebagai JSON
        cacheLength: 0
    }).result(
    function(event,data,formated){
        $(this).val(data.id+' '+data.nama);
        $('#id_pasien').val(data.id);
        entri_field(data.no_rm);
        $('#keterangan').focus().select();
    });
    $('#resep_save').submit(function() {
        if ($('#id_dokter').val() === '') {
            alert_empty('dokter','#dokter'); return false;
        }
        if ($('#id_pasien').val() === '') {
            alert_empty('pasien','#pasien'); return false;
        }
        if ($('.tr_rows').length === 0) {
            alert_dinamic('Barang belum ada yang dipilih !','#barang'); return false;
        }
        $.ajax({
            type: 'POST',
            url: '<?= base_url('pelayanan/manage_resep/save') ?>',
            data: $(this).serialize(),
            dataType: 'json',
            cache: false,
            success: function(data) {
                if (data.status === true) {
                    if (data.action === 'add') {
                        cetak_copy_resep(data.id);
                        alert_refresh('Data berhasil disimpan');
                        $('input:text,select').val('');
                        $('#resep-list tbody, #total').html('');
                        //load_data_resep();
                        //location.reload();
                    } else {
                        cetak_copy_resep(data.id);
                        alert_refresh('Data resep berhasil diubah');
                    }
                }
            }
        });
        return false;
    });
    var wWidth = $(window).width();
    var dWidth = wWidth * 1;
    
    var wHeight= $(window).height();
    var dHeight= wHeight * 1;
    $('#form_resep').dialog({
        title: 'Tambah Resep',
        autoOpen: true,
        modal: true,
        width: dWidth,
        height: dHeight,
        hide: 'clip',
        show: 'blind',
        buttons: {
            "Simpan (F8)": function() {
                $('#resep_save').submit();
            }, 
            "Cancel": function() {    
                $(this).dialog().remove();
                $.cookie('session', 'false');
            }
        }, close: function() {
            $(this).dialog().remove();
            $.cookie('session', 'false');
        }, open: function() {
            $.ajax({
                url: '<?= base_url('autocomplete/get_no_resep') ?>',
                cache: false,
                dataType: 'json',
                success: function(data) {
                    $('#noresep').val(data);
                }
            });
            $('#dokter').focus();
            $.cookie('session', 'true');
            $.cookie('nomor_r', $('#nr').val());
        }
    });
}

function form_penjualan_resep(id_resep) {
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
                '<tr><td>TOTAL:</td><td style="font-size: 45px;"><span>Rp</span> <span id=total-penjualan>0</span>, 00</td></tr>'+
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
    $('#customer').autocomplete("<?= base_url('autocomplete/pasien') ?>",
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
    });
    
    $('#noresep').autocomplete("models/autocomplete.php?method=get_data_noresep",
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
    var dWidth = wWidth * 0.99;
    
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
            $.ajax({
                url: '<?= base_url('pelayanan/manage_resep/get_detail_resep') ?>',
                data: 'id='+id_resep,
                dataType: 'json',
                success: function(data) {
                    $('#noresep').val(data.id);
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
                }
            });
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
            url: 'models/update-transaksi.php?method=save_penjualan',
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
    window.open('pages/nota-penjualan.php?id='+id_penjualan, 'Penjualan Cetak', 'width='+dWidth+', height='+dHeight+', left='+x+',top='+y);
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

function load_data_resep(page, search, id) {
    pg = page; src = search; id_barg = id;
    if (page === undefined) { var pg = ''; }
    if (search === undefined) { var src = ''; }
    if (id === undefined) { var id_barg = ''; }
    $.ajax({
        url: '<?= base_url('pelayanan/manage_resep/list') ?>/'+page,
        cache: false,
        data: 'search='+src+'&id='+id_barg,
        success: function(data) {
            $('#result-resep').html(data);
        }
    });
}

function edit_resep(data, id) {
    var arr = data.split('#');
    form_receipt();
    $('#id_resep').val(arr[0]);
    $('#id_dokter').val(arr[1]);
    $('#dokter').val(arr[2]);
    $('#id_pasien').val(arr[3]);
    $('#pasien').val(arr[4]);
    $('#keterangan').val(arr[5]);
    $('#id_kp').val(arr[6]);
    $.ajax({
        url: '<?= base_url('pelayanan/manage_resep/edit') ?>?id='+id,
        cache: false,
        success: function(msg) {
            $('#resep-list tbody').html(msg);
            total_perkiraan_resep();
        }
    });
}

function delete_resep(id, page) {
    $('<div id=alert>Anda yakin akan menghapus data ini?</div>').dialog({
        title: 'Konfirmasi Penghapusan',
        autoOpen: true,
        modal: true,
        buttons: {
            "OK": function() {
                
                $.ajax({
                    url: '<?= base_url('pelayanan/manage_resep/delete') ?>?id='+id,
                    cache: false,
                    success: function() {
                        load_data_resep(page);
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
<button id="button">Resep Baru (F9)</button>
<button id="reset">Reset</button>
<button id="nor" style="display: none;"></button>
<div id="result-resep">
    
</div>