<title><?= $title ?></title>
<div id="data_cetak" style="display: none"></div>
<div id="result_cetak" style="display: none"></div>
<div class="kegiatan">
<?php $this->load->view('message'); ?>
<script type="text/javascript">
function cetakEtiket(val) {
    $(function() {
        $('.cetaketiket').click(function() {
            window.open('<?= base_url('cetak/transaksi/etiket') ?>?id=&no_r='+val,'mywindow','location=1,status=1,scrollbars=1,width=430px,height=300px');
        });
    });
}

function print_resep(id) {
    var wWidth = $(window).width();
    var dWidth = wWidth * 1;
    var wHeight= $(window).height();
    var dHeight= wHeight * 1;
    var x = screen.width/2 - dWidth/2;
    var y = screen.height/2 - dHeight/2;
    window.open('<?= base_url('pelayanan/print_no_receipt') ?>/'+id, 'Print Resep', 'width='+dWidth+', height='+dHeight+', left='+x+',top='+y);
}
$(function() {
    $('#id_penduduk').focus();
    //$('#copyresep, #print').hide();
    $('input[type=submit]').each(function(){
        $(this).replaceWith('<button type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'">' + $(this).val() + '</button>');
    });
    $('#submit').button({
        icons: {
            secondary: 'ui-icon-circle-check'
        }
    }).click(function() {
        $('#form_resep').submit();
    });
    $('button[id=addnewrow]').button({
        icons: {
            secondary: 'ui-icon-circle-plus'
        }
    });
    $('button[id=reset]').button({
        icons: {
            secondary: 'ui-icon-refresh'
        }
    });
    $('#print, #copyresep').button({
        icons: {
            secondary: 'ui-icon-print'
        }
    }).click(function() {
        var id_resep = $('input[name=id_hidden]').val();
        if (id_resep === '') {
            custom_message('Peringatan','Cetak gagal, transaksi belum dientrikan!','#id_penduduk'); return false;
        }
        print_resep(id_resep);
    });
    $('input:text').live("keydown", function(e) {
        var n = $("input:text").length;
        if (e.keyCode === 13) {
            var nextIndex = $('input:text').index(this) + 1;
            if (nextIndex < n) {
                $('input:text')[nextIndex].focus().select();
            } else {
                $('#submit').focus();
            }
        }
    });
    $('#penyerahan').button({
        icons: {
            secondary: 'ui-icon-circle-minus'
        }
    }).click(function() {
        var str = '<div id=alert_delete>Anda yakin akan menghapus data resep ini ?</div>';
        $('#loaddata').append(str);
        $('#alert_delete').dialog({
            autoOpen: true,
            modal: true,
            buttons: {
                "Ok": function() {
                    $.ajax({
                        url: '<?= base_url('pelayanan/cek_ketersediaan_penjualan') ?>/'+$('#id_receipt').val(),
                        cache: false,
                        dataType: 'json',
                        success: function(data) {
                            $('#alert_delete').dialog().remove();
                            if (data === true) {
                                alert_delete();
                                $('#loaddata').load('<?= base_url('laporan/rekap_resep') ?>');
                            } else {
                                alert_delete_failed();
                            }
                        }
                    });
                },
                "Cancel": function() {
                    $('#alert_delete').dialog().remove();
                }
            },
            close: function() {
                $('#alert_delete').dialog().remove();
            }
        });
    });
    
    $('#form_resep').submit(function() {
        $("<div title='Konfirmasi Simpan'>Anda yakin akan menyimpan data transaksi ini ?</div>").dialog({
                modal: true,
                autoOpen: true,
                width: 320,
                buttons: { 
                    "Ya": function() { 
                        $(this).dialog('close');
                        if ($('input[name=id_dokter]').val() === '') {
                            custom_message('Peringatan','Nama dokter tidak boleh kosong !');
                            $('#dokter').focus();
                            return false;
                        }
                        if ($('input[name=id_pasien]').val() === '') {
                            custom_message('Peringatan','Nama pasien tidak boleh kosong !');
                            $('#pasien').focus();
                            return false;
                        }
                        var jumlah = $('.tr_row').length-1;
                        var sub_jumlah = $('.tr_rows').length-1;

                        for (i = 0; i <= jumlah; i++) {
                            if ($('#jr'+i).val() === '') {
                                custom_message('Peringatan','Jumlah R tidak boleh kosong !');
                                $('#jr'+i).focus();
                                return false;
                            }
                            if ($('#ap'+i).val() === '') {
                                custom_message('Peringatan','Aturan pakai tidak boleh kosong !');
                                $('#ap'+i).focus();
                                return false;
                            }
                            if ($('#it'+i).val() === '') {
                                custom_message('Peringatan','Iter tidak boleh kosong !');
                                $('#it'+i).focus();
                                return false;
                            }
                            //var jumlahsub = $('.tr_rows').length-1;
                            //custom_message('Peringatan',sub_jumlah);
                            for(k = 0; k <= sub_jumlah; k++) {

                                if ($('#id_pb'+i+''+k).val() === '') {
                                    custom_message('Peringatan','Obat tidak boleh kosong !');
                                    $('#pb'+i+''+k).focus();
                                    return false;
                                }
                            }
                        }
                        var post = $('#form_resep').attr('action');
                        $.ajax({
                            type: 'POST',
                            url: post,
                            dataType: 'json',
                            data: $('#form_resep').serialize(),
                            success: function(data) {
                                if (data.status === true) {
                                    $('input[type=text], select, input[type=radio], textarea').attr('disabled','disabled');
                                    $('#submit, #addnewrow').hide();
                                    $('#id_receipt, input[name=id_hidden]').val(data.id_resep);
                                    $('#id_receipt').removeAttr('disabled').attr('readonly','readonly');
                                    $('.etiket,#copyresep, #print').show();
                                    if ($('#id_receipt').val() === $('input[name=id_resep]').val()) {
                                        alert_edit();
                                    } else {
                                        alert_tambah();
                                    }
                                }
                            }
                        });
                        },
                    "Tidak": function() {
                        $(this).dialog('close');
                        return false;
                    }
                }, close: function() {
                    $(this).dialog('close');
                    return false;
                }
          });
        return false;
    });
    $('#cetakcsr').click(function() {
        var awal = $('#awal').val();
        var akhir= $('#akhir').val();
        var hambatan = $('#hambatan').val();
        window.open('<?= base_url('cetak/transaksi/statistika-resep') ?>?awal='+awal+'&akhir='+akhir+'&hambatan='+hambatan,'mywindow','location=1,status=1,scrollbars=1,width=730px,height=500px');
    });
    $('#copyresep').click(function() {
        var id = $('#id_receipt').val();
        location.href='<?= base_url('laporan/salin_resep') ?>/'+id;
    });
    
    $('#csr').click(function() {
        $('.csr').fadeIn('fast',function() {
            
            $('#tanggals').datepicker({
                changeYear: true,
                changeMonth: true
            });
        });
        $('#hambatan').focus();
    });
    $('#closehambatan').click(function() {
        $('.csr').fadeOut('fast');
    });
    $('#dokter').autocomplete("<?= base_url('inv_autocomplete/load_data_penduduk_dokter') ?>",
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
            var str = '<div class=result>'+data.nama+'<br/>'+data.alamat+'</div>';
            return str;
        },
        width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
        dataType: 'json', // tipe data yang diterima oleh library ini disetup sebagai JSON
        cacheLength: 0
    }).result(
    function(event,data,formated){
        $(this).val(data.nama);
        $('input[name=id_dokter]').val(data.penduduk_id);
    });
    $('#pasien, #id_penduduk').autocomplete("<?= base_url('inv_autocomplete/load_data_penduduk_pasien_form_resep') ?>",
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
            var kelurahan = '-';
            if (data.kelurahan !== null) { var kelurahan = data.kelurahan; }
            var str = '<div class=result>'+data.no_rm+' - '+data.nama+'<br/>'+data.alamat+'</div>';
            return str;
        },
        width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
        dataType: 'json', // tipe data yang diterima oleh library ini disetup sebagai JSON
        cacheLength: 0
    }).result(
    function(event,data,formated){
        $('#pasien').val(data.nama);
        $('input[name=id_pasien]').val(data.penduduk_id);
        $('#id_penduduk').val(data.no_rm);
        
        var id_pasien = data.penduduk_id;
        var no_rm = data.no_rm;
        
        $.ajax({
            url: '<?= base_url('pelayanan/cek_data_pasien_on_pel_kunjungan') ?>/'+no_rm,
            cache: false,
            dataType: 'json',
            success: function(msg) {
                //$('#jenis').html(data);
                if (msg.jumlah === "0") {
                    custom_message('Peringatan','Data belum terdaftar pada layanan Poli maupun IGD');
                    $('#pasien').val('');
                    $('input[name=id_pasien]').val('');
                    $('#id_penduduk').val('');
                    $('#jenis').html('');
                    $('input[name=jenis]').val('');
                    $('#unit').html('');
                    $('#kelas').html('');
                    $('#id_penduduk').focus();
                    $('input[name=id_pelayanan]').val('');
                    return false;
                }
                //$('input[name=jenis]').val(data);
            }
        });
        $.ajax({
            url: '<?= base_url('inv_autocomplete/load_data_pelayanan_kunjungan_by_id_penduduk') ?>/'+id_pasien,
            cache: false,
            dataType: 'json',
            success: function(data) {
                $('input[name=id_pelayanan]').val(data.id_pelayanan);
                $('#jenis').html(data.jenis);
                $('input[name=jenis]').val(data.jenis);
                $('#unit').html(data.unit);
                $('#kelas').html((data.kelas !== null)?data.kelas:'-');
                $('#asuransi').html(data.asuransi);
            }
        });
    });
});
$(function() {
    
    i = 0;
    <?php if (!isset($id_resep)) { ?>
    for(x = 0; x <= i; x++) {
        addnoresep(x);
        //add(x);
    }
    <?php } ?>
    $('#addnewrow').click(function() {
        row = $('.masterresep').length;
        //custom_message('Peringatan',row)
        addnoresep(row);
        i++;
    });
});

function eliminate(el) {
    ok=confirm('Anda yakin akan menghapus data ini ?');
    if (ok) {
        var parent = el.parentNode.parentNode;
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
    } else {
        return false;
    }
    
}

function eliminatechild(el,x,y) {
    //ok=confirm('Anda yakin akan menghapus data ini ?');
    //if (ok) {
        
        var parent = el.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode;
        
        parent.parentNode.removeChild(parent);
        var jumlah = $('.detailobat'+x).length-1;
        /*str = ' <div class=tr_rows style="width: 100%; display: inline-block;">'+
                '<table align=right width=95% style="border-bottom: 1px solid #f1f1f1" class="details_obat detailobat'+i+'">'+
                '<tr><td width=10%>Obat:</td><td width=30%>  <input type=text name=pb'+i+'[] id=pb'+i+''+j+' class=pb size=60 />'+
                    '<input type=hidden name=id_pb'+i+'[] id=id_pb'+i+''+j+' class=id_pb />'+
                    '<input type=hidden name=kr'+i+'[] id=kr'+i+''+j+' class=kr />'+
                    '<input type=hidden name=jp'+i+'[] id=jp'+i+''+j+' class=jp />'+
                    '<input type=hidden id=kekuatan'+i+''+j+' /></td>'+
                '<td width=50%><input type=hidden name=dr'+i+'[] id=dr'+i+''+j+' class=dr onkeyup=jmlPakai('+i+','+j+') size=10 value="" />'+
                '<span class=label><input type=button value="Hapus" style="padding: 2px 10px; margin: 0;" id="deleting'+i+''+j+'" onclick=eliminatechild(this,'+i+','+j+') /></span></td></tr></table>'+
            '</div>';*/
        //custom_message('Peringatan',x);
        for (i = 0; i <= jumlah; i++) {
            
            $('.tr_rows:eq('+x+')').children('table.detailobat'+x).find('.pb').attr('id','pb'+x+''+i);
//            $('.tr_rows:eq('+i+')').find('.id_pb').attr('id','id_pb'+x+''+i);
//            $('.tr_rows:eq('+i+')').find('.kr').attr('id','kr'+x+''+i);
//            $('.tr_rows:eq('+i+')').find('.jp').attr('id','jp'+x+''+i);
        }
//    } else {
//        return false;
//    }
}

function cetak_etiket(i) {
    var no_resep = $('#id_receipt').val();
    var no_r = i;
    $.ajax({
        url: '<?= base_url('pelayanan/cetak_etiket') ?>',
        data: 'no_resep='+no_resep+'&no_r='+no_r,
        cache: false,
        success: function(data) {
            $('#data_cetak').html(data);
            $('#data_cetak').dialog({
                autoOpen: true,
                modal: true,
                width: 450,
                height: 400
            });
        }
    });
}
function addnoresep(i) {
    var str = ' <div style="display: inline-block; width: 100%" class=tr_row>'+
                '<div class="masterresep" style="display: inline-block; width: 100%; border-bottom: 1px solid #f1f1f1; padding: 10px 0; ">'+
                    '<tr><td>No. R/:</td><td><input style="border: none; background: #f9f9f9" type=text name=nr[] id=nr'+i+' value='+(i+1)+' class=nr size=40 onkeyup=Angka(this) readonly maxlength=2 />'+
                    '<tr><td>Jumlah Permintaan:</td><td><input type=text name=jr[] id=jr'+i+' class=jr size=40 onkeyup=Angka(this) />'+
                    '<tr><td>Aturan Pakai:</td><td><input type=text name=ap[] id=ap'+i+' class=ap size=40 />'+
                    '<tr><td><i>Subscriptio</i> (BSO):</td><td><input type=text name=pr[] id=pr'+i+' class=pr size=40 />'+
                    '<tr><td>Iterasi:</td><td><input type=text name=it[] id=it'+i+' class=it size=10 value="0" onkeyup=Angka(this) />'+
                    '<tr><td></td><td><input type=button value="Tambah Obat" id="addition'+i+'" />'+
                    '<input type=button value="Hapus R/" id="deletion'+i+'" onclick=eliminate(this) /> <input type=button value="Etiket" id="etiket'+i+'" style="display: none" class="etiket" onclick=cetak_etiket('+(i+1)+') />'+
                '</div>'+
                '<div id=resepno'+i+' style="display: inline-block;width: 100%"></div>'+
            '</div>';
    $('#psdg-middle').append(str);
    add(i);
    $('input[type=button]').button();
    $('#addition'+i).click(function() {
        add(i, 'focus');
    });
}

function add(i,status) {
    var j = $('.detailobat'+i).length;
    var str = ' <div class=tr_rows style="width: 100%; display: inline-block;">'+
                '<table cellspacing=0 cellpadding=0 align=right width=95% style="border-bottom: 1px solid #f1f1f1" class="details_obat detailobat'+i+'">'+
                '<tr><td width="15%">Obat:</td><td>  <input type=text name=pb'+i+'[] id=pb'+i+''+j+' class=pb size=40 />'+
                    '<input type=hidden name=id_pb'+i+'[] id=id_pb'+i+''+j+' class=id_pb />'+
                    '<input type=hidden name=kr'+i+'[] id=kr'+i+''+j+' class=kr />'+
                    '<input type=hidden name=jp'+i+'[] id=jp'+i+''+j+' class=jp />'+
                    '<input type=hidden id=kekuatan'+i+''+j+' /></td></tr>'+
                '<tr><td>Dosis Racik:</td><td> <input type=text name=dr'+i+'[] id=dr'+i+''+j+' class=dr onkeyup=jmlPakai('+i+','+j+') size=10 value="" /></td></tr>'+
                '<tr><td>Jumlah Pakai:</td><td><input type=text name=jmlpakai[] onkeyup=hitungJmlRacik('+i+','+j+') id="jmlpakai'+i+''+j+'" size=10 /></td></tr>'+
                '<td></td><td>'+
                '<span class=label><input type=button value="Hapus" style="padding: 2px 10px; margin: 0;" id="deleting'+i+''+j+'" onclick=eliminatechild(this,'+i+','+j+') /></span></td></tr></table>'+
            '</div>';

        
    $('#resepno'+i).append(str);
    $('.pb').watermark('Nama Obat');
    if (status !== undefined) {
        $('#pb'+i+''+j).focus();
    }
    $('input[type=button]').button();
    var lebar = $('#pb'+i+''+j).width();
    $('#pb'+i+''+j).autocomplete("<?= base_url('inv_autocomplete/load_data_barang') ?>",
    {
        extraParams :{ 
            jenis : function(){
                return 'Obat';
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
            if (data.pabrik !== null) {var pab = data.pabrik;} else {var pab = '';}

            if (data.id_obat !== null) {
                if (data.kekuatan !== null && data.satuan !== null && data.sediaan !== null) {
                    var str = '<div class=result>'+data.nama+' '+((data.kekuatan === '1')?'':data.kekuatan)+' '+data.satuan+' '+data.sediaan+'  <i> '+pab+'</i></div>';
                } 
                else if (data.kekuatan !== null && data.satuan !== null && data.sediaan === null) {
                    var str = '<div class=result>'+data.nama+' '+((data.kekuatan === '1')?'':data.kekuatan)+' '+data.satuan+' <i> '+pab+'</i></div>';
                } else {
                    var str = '<div class=result>'+data.nama+'</div>';
                }   
            } else {
                if (data.pabrik !== null) {
                    var str = '<div class=result>'+data.nama+'<i> '+data.pabrik+'</i></div>';
                } else {
                    var str = '<div class=result>'+data.nama+'</div>';
                }
            }
            return str;
        },
        width: lebar, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
        dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
    }).result(
    function(event,data,formated){
        
        if (data.pabrik !== null) {
                var pab = data.pabrik;
            } else {
                var pab = '';
            }
        if (data.id_obat !== null) {
            if (data.kekuatan !== null && data.satuan !== null && data.sediaan !== null) {
                var str = data.nama+' '+data.kekuatan+' '+data.satuan+' '+data.sediaan+' '+pab;
            } 
            else if (data.kekuatan !== null && data.satuan !== null && data.sediaan === null) {
                var str = data.nama+' '+data.kekuatan+' '+data.satuan+' '+pab+'';
            } else {
                var str = data.nama;
            }   
        } else {
            var str = data.nama+' '+pab;
        }
        $(this).val(str);
        $('input[name=id_barang]').val(data.id_barang);
        
        $('#id_pb'+i+''+j).val(data.id_barang);
        $('#bc'+i+''+j).val(data.barcode);
        if (data.kategori === 'Obat') {
            $('#kr'+i+''+j).val(data.kekuatan);
            $('#dr'+i+''+j).val(data.kekuatan);
            jmlPakai(i, j);
        }
    });
}
function hitungJmlRacik(i,j) {
    var jumlah_tbs  = parseFloat($('#jr'+i).val());
    var kekuatan    = parseFloat($('#kr'+i+''+j).val());
    var jumlah_pakai= parseFloat($('#jmlpakai'+i+''+j).val()); //(dosis_racik*jumlah_tbs)/kekuatan; 10 = (5*4)/2
    var dosis_racik = (kekuatan*jumlah_pakai)/jumlah_tbs;
    
    if (isNaN(kekuatan) || kekuatan === '0') {
        custom_message('Peringatan','Kekuatan obat tidak boleh bernilai nol, silahkan diubah pada master data obat !');
        $('#pb'+i+''+j).val('');
        $('#id_pb'+i+''+j).val('');
        $('#bc'+i+''+j).val('');
        $('#kekuatan'+i+''+j).val('');
        $('#dr'+i+''+j).val('');
        
        return false;
    }
    $('#dr'+i+''+j).val(Math.round(dosis_racik));
    $('#jmlpakai'+i+''+j).val(jumlah_pakai);
    $('#kr'+i+''+j).val(kekuatan);
    $('#jp'+i+''+j).val(jumlah_pakai);
}
function jmlPakai(i,j) {
    var dosis_racik = parseFloat($('#dr'+i+''+j).val());
    var jumlah_tbs  = parseFloat($('#jr'+i).val());
    var kekuatan    = parseFloat($('#kr'+i+''+j).val());
    var jumlah_pakai= (dosis_racik*jumlah_tbs)/kekuatan;
    
    if (isNaN(kekuatan) || kekuatan === 0) {
        custom_message('Peringatan','Kekuatan obat tidak boleh bernilai nol, silahkan diubah pada master data obat !');
        $('#pb'+i+''+j).val('');
        $('#id_pb'+i+''+j).val('');
        $('#bc'+i+''+j).val('');
        $('#kekuatan'+i+''+j).val('');
        $('#dr'+i+''+j).val('');
        return false;
    }
    $('#jmlpakai'+i+''+j).val(Math.round(jumlah_pakai));
    $('#kr'+i+''+j).val(kekuatan);
    $('#jp'+i+''+j).val(Math.round(jumlah_pakai));
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
</script>
<script type="text/javascript">
$(function() {
    $('#tanggal').datetimepicker();
    $('#reset').click(function() {
        
        $('#loaddata').empty().load('<?= base_url('pelayanan/receipt') ?>');
    });
    var jml = $('.masterresep').length-1;
    for(i = 0; i <= jml; i++) {
        $('#cetak'+i).each(function(){
            $(this).replaceWith('<button class="'+$(this).attr('class')+'" title="'+$(this).attr('title')+'" type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'" onclick="'+$(this).attr('onclick')+'">' + $(this).val() + '</button>');
        });
        $('#cetak'+i).button({
            icons: {
                secondary: 'ui-icon-print'
            }
        });
    }
    $('#pmr_open').click(function() {
        var pasien = $('#id_pasien').val();
        var nama   = $('#pasien').val();
        if (pasien === '') {
            custom_message('Peringatan','Silahkan isikan data pasien terlebih dahulu!');
            $('#pasien').focus();
        } else {
            location.href='<?= base_url('cetak/transaksi/pmr') ?>?id_pasien='+pasien+'&nama='+nama;
        }
    });
    $('#id_penduduk').blur(function() {
        if ($('#id_penduduk').val() !== '') {
            var id = $('#id_penduduk').val();
            $.ajax({
                url: '<?= base_url('inv_autocomplete/load_data_penduduk_pasien') ?>/'+id,
                dataType: 'json',
                success: function(val) {
                    if (val.id === null) {
                        $('#id_penduduk, #pasien, #id_pasien').val('');
                        $('#id_penduduk').focus();
                    } else {
                        $('#pasien').val(val.nama);
                        $('#id_pasien').val(val.id);
                    }

                }
            });
        }
    });
});
</script>
    <div class="titling"><h1><?= $title ?></h1></div>
    <?= form_open('pelayanan/receipt', 'id=form_resep') ?>
    
    <div class="data-input">
    <?php if (isset($list_data)) { foreach ($list_data as $key => $rows); } ?>
    <?= form_hidden('id_resep', isset($id_resep)?$id_resep:NULL) ?>
    <?= form_hidden('id_pelayanan',isset($id_resep)?$rows->id_pelayanan_kunjungan:'') ?>
    <table width="100%" class="inputan">Summary</legend>
        <table width="100%" cellspacing="0" cellpadding="0"><tr valign="top"><td width="50%">
            <tr><td>No.:</td><td><?= form_input('no_resep', isset($id_resep)?$id_resep:get_last_id('resep', 'id'), 'id=id_receipt readonly size=10') ?><?= form_hidden('id_hidden',isset($id_resep)?$id_resep:NULL) ?>
            <tr><td>Waktu:</td><td><?= form_input('tanggal', isset($id_resep)?datetimefmysql($rows->waktu,'true'):date("d/m/Y H:i"), 'id=tanggal') ?>
            <tr><td>Dokter:</td><td><?= form_input('', isset($id_resep)?$rows->dokter:$this->session->userdata('nama'), 'id=dokter size=40') ?> <?= form_hidden('id_dokter', isset($id_resep)?$rows->dokter_penduduk_id:$this->session->userdata('id_user')) ?>
            <tr><td>No. RM:</td><td><?= form_input('id_penduduk', isset($id_resep)?$rows->no_rm:NULL, 'id=id_penduduk size=40') ?>
            <tr><td>Pasien: </td><td><?= form_input('', isset($id_resep)?$rows->pasien:NULL, 'id=pasien size=40') ?> <?= form_hidden('id_pasien', isset($id_resep)?$rows->pasien_penduduk_id:NULL) ?>
            </td><td width="50%">
            <tr><td>Jenis Pelayanan:</td><td><?= form_hidden('jenis', isset($id_resep)?$rows->jenis:NULL) ?><span class="label" id="jenis"><?= isset($id_resep)?$rows->jenis:'-' ?></span>
            <tr><td>Asuransi:</td><td><span  class="label" id="asuransi"><?= isset($id_resep)?$rows->asuransi:NULL ?></span>
            <tr><td>Unit:</td><td><span class="label" id="unit"><?= isset($id_resep)?$rows->unit:NULL ?></span>
            <tr><td>Kelas:</td><td><span class="label" id="kelas"><?= (isset($rows->kelas) and $rows->kelas !== NULL)?$rows->kelas:'-' ?></span>
            <tr><td>Keterangan:</td><td><?= form_input('ket', isset($id_resep)?$rows->keterangan:NULL, 'id=ket size=40') ?>
            </td></tr>
        </table>
    </table>
    </div>
    <div class="data-list">
        <div id="psdgraphics-com-table">
            
            <div id="psdg-middle" class="data-input">
                <?php if (isset($id_resep)) { 
                    $noo = 1;
                    $nom = 0;
                    foreach ($list_data as $key => $data) { ?>
                    <div style="display: inline-block; width: 100%" class=tr_row>
                        <div class="masterresep" style="display: inline-block; width: 100%; border-bottom: 1px solid #f1f1f1; padding: 10px 0; ">
                                <tr><td>No. R/:</td><td><input style="border: none;" type=text name=nr[] id=nr<?= $key ?> value='<?= $noo ?>' class=nr size=20 onkeyup="Angka(this);" readonly maxlength=2 />
                                <tr><td>Jumlah Permintaan:</td><td><input type=text name=jr[] value="<?= $data->resep_r_jumlah ?>" id=jr<?= $key ?> class=jr size=20 onkeyup="Angka(this);" />
                                <tr><td>Aturan Pakai:</td><td><input type=text name=ap[] value="<?= $data->pakai_aturan ?>" id=ap<?= $key ?> class=ap size=20 />
                                <tr><td><i>Subscriptio</i> (BSO):</td><td><input type=text name="pr[]" value="<?= $data->perintah_resep ?>" id="pr<?= $key ?>" class=pr size=40 />
                                <tr><td>Iterasi:</td><td><input type=text name=it[] value="<?= $data->iter ?>" id=it<?= $key ?> class=it size=10 value="0" onkeyup="Angka(this);" />
                                <tr><td></td><td><input type=button value="Tambah Obat" onclick="add(<?= $key ?>);" id="addition<?= $key ?>" />
                                <input type=button value="Hapus R/" id="deletion<?= $key ?>" onclick="eliminate(this);" /> <input type=button value="Etiket" id="etiket<?= $noo ?>" style="display: none" class="etiket" onclick="cetak_etiket(<?= $noo ?>)" />
                        </div>
                        <div id=resepno<?= $key ?> style="display: inline-block;width: 100%"></div>
                    </div>
                <?php 
                    $detail = $this->m_resep->detail_data_resep_dokter_muat_data($data->id_rr)->result();
                    foreach($detail as $no => $val) { ?>
                         <div class=tr_rows style="width: 100%; display: block;">
                                <table align=right width=95% style="border-bottom: 1px solid #f4f4f4" class="detailobat<?= $key ?>">
                                <tr><td>Obat:</td><td>  <input type=text name=pb<?= $key ?>[] value="<?= $val->barang ?> <?= ($val->kekuatan == '1')?'':$val->kekuatan ?>  <?= $val->satuan ?> <?= $val->sediaan ?>" id=pb<?= $key ?><?= $no ?> class=pb size=60 />
                                        <input type=hidden name=id_pb<?= $key ?>[] value="<?= $val->id_barang ?>" id=id_pb<?= $key ?><?= $no ?> class=id_pb />
                                        <input type=hidden name=kr<?= $key ?>[] value="<?= $val->kekuatan ?>" id=kr<?= $key ?><?= $no ?> class=kr />
                                        <input type=hidden name=jp<?= $key ?>[] value="<?= $val->pakai_jumlah ?>" id=jp<?= $key ?><?= $no ?> class=jp />
                                        <input type=hidden id="kekuatan<?= $key ?><?= $no ?>" /></td></tr>
                                <tr><td>Dosis Racik:</td><td> <input type=text name=dr<?= $key ?>[] id=dr<?= $key ?><?= $no ?> class=dr onkeyup="jmlPakai('<?= $key ?>','<?= $no ?>');" size=10 value="<?= $val->dosis_racik ?>" /></td></tr>
                                <tr><td>Jumlah Pakai:</td><td><input type=text name=jmlpakai[] onkeyup="hitungJmlRacik('<?= $key ?>','<?= $no ?>');" id="jmlpakai<?= $key ?><?= $no ?>;" size=10 value="<?= $val->pakai_jumlah ?>" /></td></tr>
                                <tr><td></td><td><span class=label><input type=button value="Hapus" id="deleting<?= $key ?><?= $no ?>" onclick="eliminatechild(this,'<?= $key ?>','<?= $no ?>');" /></span></td></tr></table>
                        </div>
                        <script>
                            $('input[type=button]').button();
                            var lebar = $('#pb<?= $key ?><?= $no ?>').width();
                            $('#pb<?= $key ?><?= $no ?>').autocomplete("<?= base_url('inv_autocomplete/load_data_barang') ?>",
                            {
                                extraParams :{ 
                                    jenis : function(){
                                        return 'Obat';
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
                                    if (data.pabrik !== null) {var pab = data.pabrik;} else {var pab = '';}

                                    if (data.id_obat !== null) {
                                        if (data.kekuatan !== null && data.satuan !== null && data.sediaan !== null) {
                                            var str = '<div class=result>'+data.nama+' '+((data.kekuatan === '1')?'':data.kekuatan)+' '+data.satuan+' '+data.sediaan+'  <i> '+pab+'</i></div>';
                                        } 
                                        else if (data.kekuatan !== null && data.satuan !== null && data.sediaan === null) {
                                            var str = '<div class=result>'+data.nama+' '+((data.kekuatan === '1')?'':data.kekuatan)+' '+data.satuan+' <i> '+pab+'</i></div>';
                                        } else {
                                            var str = '<div class=result>'+data.nama+'</div>';
                                        }   
                                    } else {
                                        if (data.pabrik !== null) {
                                            var str = '<div class=result>'+data.nama+'<i> '+data.pabrik+'</i></div>';
                                        } else {
                                            var str = '<div class=result>'+data.nama+'</div>';
                                        }
                                    }
                                    return str;
                                },
                                width: lebar, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                                dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
                            }).result(
                            function(event,data,formated){

                                if (data.pabrik !== null) {
                                        var pab = data.pabrik;
                                    } else {
                                        var pab = '';
                                    }
                                if (data.id_obat !== null) {
                                    if (data.kekuatan !== null && data.satuan !== null && data.sediaan !== null) {
                                        var str = data.nama+' '+data.kekuatan+' '+data.satuan+' '+data.sediaan+' '+pab;
                                    } 
                                    else if (data.kekuatan !== null && data.satuan !== null && data.sediaan === null) {
                                        var str = data.nama+' '+data.kekuatan+' '+data.satuan+' '+pab+'';
                                    } else {
                                        var str = data.nama;
                                    }   
                                } else {
                                    var str = data.nama+' '+pab;
                                }
                                $(this).val(str);
                                $('input[name=id_barang]').val(data.id_barang);

                                $('#id_pb<?= $key ?><?= $no ?>').val(data.id_barang);
                                $('#bc<?= $key ?><?= $no ?>').val(data.barcode);
                                if (data.kategori === 'Obat') {
                                    $('#kr<?= $key ?><?= $no ?>').val(data.kekuatan);
                                    $('#dr<?= $key ?><?= $no ?>').val(data.kekuatan);
                                    jmlPakai(<?= $key ?>, <?= $no ?>);
                                }
                            });
                        </script>
                    <?php }
                    $noo++;
                    //$nom = $nom + $data->nominal;
                    }
                } ?>
            </div>
            <?= form_button(null,'Tambah R', 'id=addnewrow') ?>
<!--            <table width="100%">
                <tr><td>
                * Total Biaya Apoteker: <b id="totalbiaya"><?= isset($id_resep)?rupiah($nom):NULL ?></b>
                    </td></tr>
            </table>-->
            </div>
    </div>
        <?= form_button('save', 'Simpan', 'id=submit') ?>
        <?= form_button('Reset', 'Reset', 'id=reset') ?>
        <?= form_button('Cetak', 'Cetak', 'id=print') ?>
        <?php if (isset($id_resep)) { ?>
        <?= form_button(null, 'Delete', 'id=penyerahan') ?>
        <?php } ?>
    <?= form_close() ?>
</div>