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

function resep_load(id) {
    if (id !== undefined) {
        $.ajax({
            url: '<?= base_url('pelayanan/resep_load') ?>/'+id,
            cache: false,
            success: function(data) {
                $('#psdg-middle').html(data);
                $('#jt0').focus();
            }
        });
    }
}
$(function() {
    $('#id_receipt').focus();
    $('#copyresep, #print').hide();
    $('#submit').button({
        icons: {
            secondary: 'ui-icon-circle-check'
        }
    }).click(function() {
        $('#form_resep').submit();
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
                                $('#loaddata').load('<?= base_url('laporan/resep') ?>');
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
    $('#addnewrow').button({
        icons: {
            secondary: 'ui-icon-circle-plus'
        }
    });
    $('#reset').button({
        icons: {
            secondary: 'ui-icon-refresh'
        }
    });
    $('button[id=print], button[id=copyresep]').button({
        icons: {
            secondary: 'ui-icon-print'
        }
    });
    $('#print').click(function() {
        var id = $('#id_receipt').val();
        window.open('<?= base_url('pelayanan/kitir_cetak_nota') ?>/'+id,'mywindow', 'width=300px, height=400px, resizable=yes, scrollable=yes');
    });
    $('#form_resep').submit(function() {
        $("<div title='Konfirmasi Simpan'>Anda yakin akan menyimpan data transaksi ini ?</div>").dialog({
                modal: true,
                autoOpen: true,
                width: 320,
                buttons: { 
                    "Ya": function() { 
                        $(this).dialog('close');
                        if ($('input[name=id_resep]').val() === '') {
                            custom_message('Peringatan','No. resep harus dipilih !','#id_receipt');
                            return false;
                        }
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
                        if($('.id_pb').val()===''){
                             custom_message('Peringatan','Obat tidak boleh kosong !');
                             return false;
                        }
                        var jumlah = $('.tr_row').length-1;
                        for (i = 0; i <= jumlah; i++) {
                            if ($('#jr'+i).val() === '') {
                                custom_message('Peringatan','Jumlah R tidak boleh kosong !','#jr'+i);
                                return false;
                            }
                            if ($('#jt'+i).val() === '') {
                                custom_message('Peringatan','Jumlah tebus tidak boleh kosong !','#jt'+i);
                                return false;
                            }
                            if ($('#ap'+i).val() === '') {
                                custom_message('Peringatan','Aturan pakai tidak boleh kosong !','#ap'+i);
                                return false;
                            }
                            if ($('#it'+i).val() === '') {
                                custom_message('Peringatan','Iter tidak boleh kosong !','#it'+i);
                                return false;
                            }
                            if ($('#ja'+i).val() === '0-0') {
                                custom_message('Peringatan','Jasa apoteker tidak boleh kosong !','#ja'+i);
                                return false;
                            }
                            //var jumlahsub = $('.tr_rows').length-1;
                        }
                        var post = $('#form_resep').attr('action');
                        $.ajax({
                            type: 'POST',
                            url: post,
                            dataType: 'json',
                            data: $('#form_resep').serialize(),
                            success: function(data) {
                                if (data.status === true) {
                                    if ($('#id_receipt').val() === $('input[name=id_resep]').val()) {
                                        alert_edit();
                                        $.ajax({
                                            url: '<?= base_url('pelayanan/resep_load') ?>/'+data.id_resep,
                                            cache: false,
                                            success: function(data) {
                                                $('#psdg-middle').html(data);
                                                $('#jt0').focus();
                                                $('input[type=text], select, input[type=radio], textarea').attr('disabled','disabled');
                                                $('#submit, #addnewrow').hide();
                                                //$('#id_receipt').val(data.id_resep);
                                                $('#id_receipt').removeAttr('disabled').attr('readonly','readonly');
                                                $('.etiket,#copyresep, #print').show();
                                            }
                                        });
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
    $('input:text').live("keydown", function(e) {
        var n = $("input:text").length;
        if (e.keyCode === 13) {
            var nextIndex = $('input:text').index(this) + 1;
            if (nextIndex < n) {
                $('input:text')[nextIndex].focus();
            } else {
                $('#submit').focus();
            }
        }
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
    $('#id_receipt').autocomplete("<?= base_url('inv_autocomplete/load_data_no_resep') ?>",
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
            if (data.id !== null) {
                var str = '<div class=result>'+data.id+' - '+data.pasien+'<br/>Dokter: '+data.dokter+'</div>';
            }
            return str;
        },
        width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
        dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
    }).result(
    function(event,data,formated){
        $(this).val(data.id);
        $('input[name=id_resep]').val(data.id);
        $('#tanggal').val(datetimefmysql(data.waktu));
        $('#dokter').val(data.dokter);
        $('input[name=id_dokter]').val(data.dokter_penduduk_id);
        $('#id_penduduk').val(data.no_rm);
        $('#pasien').val(data.pasien);
        $('input[name=id_pasien]').val(data.pasien_penduduk_id);
        $('#jenis').html(data.jenis);
        $('input[name=jenis]').val(data.jenis);
        //if (data.sah === 'Sah') {
        $('#sah').attr('checked','checked');
//        } else {
//            $('#tidaksah').attr('checked','checked');
//        }
        $('#ket').val(data.keterangan);
        resep_load(data.id);
        $.ajax({
            url: '<?= base_url('inv_autocomplete/load_data_pelayanan_kunjungan_by_id_penduduk') ?>/'+data.pasien_penduduk_id,
            cache: false,
            dataType: 'json',
            success: function(data) {
                $('#jenis').html(data.jenis);
                $('#unit').html(data.unit);
                $('#kelas').html((data.kelas !== null)?data.kelas:'-');
                $('#asuransi').html(data.asuransi);
            }
        });
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
        dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
    }).result(
    function(event,data,formated){
        $(this).val(data.nama);
        $('input[name=id_dokter]').val(data.penduduk_id);
    });
    $('#pasien, #id_penduduk').autocomplete("<?= base_url('inv_autocomplete/load_data_penduduk_pasien') ?>",
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
        dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
    }).result(
    function(event,data,formated){
        $('#pasien').val(data.nama);
        $('input[name=id_pasien]').val(data.penduduk_id);
        $('#id_penduduk').val(data.no_rm);
        var id_pasien = data.penduduk_id;
        var no_rm = data.no_rm;
        $.ajax({
            url: '<?= base_url('pelayanan/get_jenis_rawat_by_pasien') ?>/'+id_pasien+'/'+no_rm,
            cache: false,
            dataType: 'json',
            success: function(data) {
                $('#jenis').html(data);
                $('input[name=jenis]').val(data);
            }
        });
    });
});
$(function() {
    
    i = 0;
    <?php if (!isset($id_resep)) { ?>
    for(x = 0; x <= i; x++) {
        addnoresep(x);
        for (j = 0; j <= 1; j++) {
            add(x);
        }
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
    var ok = confirm('Anda yakin akan menghapus data ini ?');
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
    $('<div>Anda yakin akan menghapus data ini ?</div>').dialog({
        autoOpen: true,
        title: 'Konfirmasi',
        modal: true,
        buttons: {
            "Ya": function() {
                var parent = el.parentNode.parentNode.parentNode.parentNode.parentNode;
                parent.parentNode.removeChild(parent);
                $(this).dialog('close');
            },
            "Tidak": function() {
                $(this).dialog('close');
            }
        }
    });
}

function cetak_etiket(i) {
    var no_resep = $('#id_receipt').val();
    var no_r = i;
    $.ajax({
        url: '<?= base_url('pelayanan/cetak_etiket_pelayanan_farmasi') ?>',
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
    
    str = ' <div style="display: inline-block; width: 100%; padding: 3px;" class=tr_row>'+
                '<table class="masterresep" style="line-height: 18px;" width="100%" cellpadding=0 cellspacing=0>'+
                    '<tr><td width="17%">No. R/:</td><td><input style="background: #f9f9f9;" type=text name=nr[] id=nr'+i+' value='+(i+1)+' class=nr size=40 onkeyup=Angka(this) readonly maxlength=2 /></td></tr>'+
                    '<tr><td>Jumlah Permintaan:</td><td><input type=text name=jr[] id=jr'+i+' class=jr size=40 onkeyup=Angka(this) /></td></tr>'+
                    '<tr><td>Jumlah Tebus:</td><td><input type=text name=jt[] id=jt'+i+' class=jt onkeyup=Angka(this) size=40 /></td></tr>'+
                    '<tr><td>Aturan Pakai:</td><td><input type=text name=ap[] id=ap'+i+' class=ap size=40 /></td></tr>'+
                    '<tr><td>Iterasi:</td><td><input type=text name=it[] id=it'+i+' class=it size=10 value="0" onkeyup=Angka(this) /></td></tr>'+
                    '<tr><td>Biaya Apoteker:</td><td><select onchange="subTotal()" name=ja[] id=ja'+i+'><option value="0-0">Pilih biaya ..</option><?php foreach ($biaya_apoteker as $value) { echo '<option value="'.$value->id.'-'.$value->nominal.'">'.$value->layanan.' '.$value->profesi.' '.$value->nama_jkp.' '.$value->jenis.' '.$value->bobot.' '.$value->kelas.' Rp. '.rupiah($value->nominal).'</option>'; } ?></select></td></tr>'+
                    '<tr><td><i>Subscriptio (BSO)</i></td><td><span class=label>-</span></td></tr>'+
                    '<tr><td>Total Tertebus:</td><td id="total_tebus">-</td></tr>'+
                    '<tr><td></td><td><input type=button value="Tambah Kemasan Barang" onclick=add('+i+') id="addition'+i+'" />'+
                    '<input type=button value="Hapus R/" id="deletion'+i+'" onclick=eliminate(this) /> <input type=button value="Etiket" id="etiket'+i+'" style="display: none" class="etiket" onclick=cetak_etiket('+(i+1)+') /></td></tr>'+
                '</table>'+
                '<div id=resepno'+i+' style="display: inline-block;width: 100%"></div>'+
            '</div>';
    
    $('#psdg-middle').append(str);
    
}

function add(i) {
    var j = $('.detailobat'+i).length;
    var str = ' <div class=tr_rows>'+
                '<table align=right width=100% cellpadding="0" cellspacing="0" style="border-bottom: 1px solid #f1f1f1; padding-bottom: 5px; margin-bottom: 5px;" class="detailobat'+i+'">'+
                //'<tr><td width=15%>Barcode:</td><td> <input type=text value="<?= isset($val->barcode)?$val->barcode:NULL ?>" name=bc'+i+'[] id=bc'+i+''+j+' class=bc size=30 readonly /></td></tr>'+
                '<tr><td width=17%>Kemasan Barang:</td><td>  <input type=text name=pb'+i+'[] id=pb'+i+''+j+' class=pb size=40 />'+
                    '<input type=hidden name=id_pb'+i+'[] id=id_pb'+i+''+j+' class=id_pb />'+
                    '<input type=hidden name=kr'+i+'[] id=kr'+i+''+j+' class=kr />'+
                    '<input type=hidden name=jp'+i+'[] id=jp'+i+''+j+' class=jp /></td></tr>'+
                '<tr><td>Dosis Racik:</td><td> <input type=text name=dr'+i+'[] id=dr'+i+''+j+' class=dr onkeyup=jmlPakai('+i+','+j+') size=10 value="" /></td></tr>'+
                '<tr><td>Kekuatan:</td><td><span class=label id=kekuatan'+i+''+j+'>-</span></td></tr>'+
                '<tr><td>Jumlah Pakai:</td><td><span class=label id=jmlpakai'+i+''+j+'>-</span></td></tr>'+
                '<tr><td></td><td><span class=label><span class="link_button orange" id="deleting'+i+''+j+'" onclick="eliminatechild(this,'+i+','+j+')">Hapus</span></td></tr></table>'+
            '</div>';
    
    $('#resepno'+i).after(str);
    $('input[type=button]').button();
    $('.pb').watermark('Nama Kemasan Barang');
    $('#pb'+i+''+j).autocomplete("<?= base_url('inv_autocomplete/load_data_packing_barang_resep') ?>",
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
            var isi = ''; var satuan = ''; var sediaan = ''; var pabrik = ''; var satuan_terkecil = ''; var kekuatan = '';
            if (data.isi !== '1') { var isi = '@ '+data.isi; }
            if (data.kekuatan !== null && data.kekuatan !== '0') { var kekuatan = data.kekuatan; }
            if (data.satuan !== null) { var satuan = data.satuan; }
            if (data.sediaan !== null) { var sediaan = data.sediaan; }
            if (data.pabrik !== null) { var pabrik = data.pabrik; }
            if (data.satuan_terkecil !== null) { var satuan_terkecil = data.satuan_terkecil; }
            if (data.id_obat === null) {
                var str = '<div class=result>'+data.nama+' '+pabrik+' '+isi+' '+satuan_terkecil+'</div>';
            } else {
                if (data.generik === 'Non Generik') {
                    var str = '<div class=result>'+data.nama+' '+((kekuatan === '1')?'':kekuatan)+' '+satuan+' '+sediaan+' '+isi+' '+satuan_terkecil+'</div>';
                } else {
                    var str = '<div class=result>'+data.nama+' '+((kekuatan === '1')?'':kekuatan)+' '+satuan+' '+sediaan+' '+pabrik+' '+isi+' '+satuan_terkecil+'</div>';
                }
            }
            return str;
        },
        width: 400, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
        dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
    }).result(
    function(event,data,formated){
        if (data.kekuatan === null) {
            var ok=confirm('Kekuatan untuk Kemasan Barang yang dipilih = NULL, Anda yakin akan menambahkan dalam form resep?');
            if (!ok) {
                $(this).val('');
                $('#id_pb'+i+''+j).val('');
                $('#bc'+i+''+j).val('');
                $('#kekuatan'+i+''+j).html('');
                $('#dr'+i+''+j).val('');
                return false;
            } else {
                $('#kekuatan'+i+''+j).html('1');
                $('#jmlpakai'+i+''+j).html($('#jt'+i).val());
                $('#dr'+i+''+j).val('1');
                $('#jp'+i+''+j).val($('#jt'+i).val());
            }
        } 
        if (data.high_alert === 'Ya') {
            custom_message('Peringatan','Obat High Alert');
        }
        var isi = ''; var satuan = ''; var sediaan = ''; var pabrik = ''; var satuan_terkecil = ''; var kekuatan = '';
        if (data.isi !== '1') { var isi = '@ '+data.isi; }
        if (data.kekuatan !== null && data.kekuatan !== '0') { var kekuatan = data.kekuatan; }
        if (data.satuan !== null) { var satuan = data.satuan; }
        if (data.sediaan !== null) { var sediaan = data.sediaan; }
        if (data.pabrik !== null) { var pabrik = data.pabrik; }
        if (data.satuan_terkecil !== null) { var satuan_terkecil = data.satuan_terkecil; }
        if (data.id_obat === null) {
            $(this).val(data.nama+' '+pabrik+' '+isi+' '+satuan_terkecil);
        } else {
            if (data.generik === 'Non Generik') {
                $(this).val(data.nama+' '+((kekuatan === '1')?'':kekuatan)+' '+satuan+' '+sediaan+' '+isi+' '+satuan_terkecil);
            } else {
                $(this).val(data.nama+' '+((kekuatan === '1')?'':kekuatan)+' '+satuan+' '+sediaan+' '+pabrik+' '+isi+' '+satuan_terkecil);
            }
        }
        
        $('#id_pb'+i+''+j).val(data.id);
        $('#bc'+i+''+j).val(data.barcode);
        if (data.kategori === 'Obat') {
            $('#kekuatan'+i+''+j).html(data.kekuatan);
            $('#dr'+i+''+j).val(data.kekuatan);
            jmlPakai(i, j);
        }
    });
}
function jmlPakai(i,j) {
        var dosis_racik = parseFloat($('#dr'+i+''+j).val());
        var jumlah_tbs  = parseFloat($('#jt'+i).val());
        var kekuatan    = parseFloat($('#kekuatan'+i+''+j).html());
        var jumlah_pakai= (dosis_racik*jumlah_tbs)/kekuatan;
        if (isNaN(kekuatan) || kekuatan === '0') {
            custom_message('Peringatan','Kekuatan obat tidak boleh bernilai nol, silahkan diubah pada master data obat !');
            $('#pb'+i+''+j).val('');
            $('#id_pb'+i+''+j).val('');
            $('#bc'+i+''+j).val('');
            $('#kekuatan'+i+''+j).html('');
            $('#dr'+i+''+j).val('');
            return false;
        }
        $('#jmlpakai'+i+''+j).html(jumlah_pakai);
        $('#kr'+i+''+j).val(kekuatan);
        $('#jp'+i+''+j).val(jumlah_pakai);
        
}

function jmlPakaiDua(i,j) {
        var dosis_racik = parseInt($('#dr'+i+''+j).val());
        var jumlah_tbs  = parseInt($('#jt'+i).val());
        var kekuatan    = parseInt($('#kekuatan'+i+''+j).html());
        var jumlah_pakai= (dosis_racik*jumlah_tbs)/kekuatan;
        if (isNaN(kekuatan) || kekuatan === 0) {
            custom_message('Peringatan','Kekuatan obat tidak boleh bernilai nol, silahkan diubah pada master data obat !');
            $('#pb'+i+''+j).val('');
            $('#id_pb'+i+''+j).val('');
            $('#bc'+i+''+j).val('');
            $('#kekuatan'+i+''+j).html('');
            $('#dr'+i+''+j).val('');
            return false;
        }
        $('#jmlpakai'+i+''+j).html(jumlah_pakai);
        $('#kr'+i+''+j).val(kekuatan);
        $('#jp'+i+''+j).val(jumlah_pakai);
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
        var url = $('#form_resep').attr('action');
        $('#loaddata').load(url);
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
    <?= form_open('pelayanan/resep', 'id=form_resep') ?>
    
    <div class="data-input">
    <?php if (isset($list_data)) { foreach ($list_data as $key => $rows); } ?>
    <?= form_hidden('id_resep', isset($id_resep)?$id_resep:NULL) ?>
    <table width="100%" class="inputan">Summary</legend>
        <table width="100%" cellpadding="0" cellspacing="0"><tr valign="top"><td width="50%">
            <table width="100%" cellpadding="0" cellspacing="0">
                <tr><td>Waktu:</td><td><?= form_input('tanggal', isset($id_resep)?datetimefmysql($rows->waktu,'true'):date("d/m/Y H:i"), 'id=tanggal size=15') ?></td></tr>
                <tr><td width="32%">No. Resep:</td><td><?= form_input('no_resep', isset($id_resep)?$id_resep:NULL, 'id=id_receipt size=40') ?></td></tr>
                <tr><td>Dokter:</td><td><?= form_input('', isset($id_resep)?$rows->dokter:NULL, 'id=dokter size=40') ?> <?= form_hidden('id_dokter', isset($id_resep)?$rows->dokter_penduduk_id:NULL) ?></td></tr>
                <tr><td>No. RM:</td><td><?= form_input('id_penduduk', isset($id_resep)?$rows->no_rm:NULL, 'id=id_penduduk size=40') ?></td></tr>
                <tr><td>Pasien: </td><td><?= form_input('', isset($id_resep)?$rows->pasien:NULL, 'id=pasien size=40') ?> <?= form_hidden('id_pasien', isset($id_resep)?$rows->pasien_penduduk_id:NULL) ?></td></tr>
                <tr><td>Keterangan:</td><td><?= form_textarea('ket', isset($id_resep)?$rows->keterangan:NULL, 'id=ket style="height: 20px;"') ?></td></tr>
            </table></td><td width="50%">
            <table width="100%" cellpadding="0" cellspacing="0" style="line-height: 19px;">
                <tr><td width="30%">Asuransi:</td><td id="asuransi"></td></tr>
                <tr><td>Unit:</td><td id="unit"><?= isset($list_data)?$rows->unit:NULL ?></td></tr>
                <tr><td>Kelas:</td><td id="kelas"><?= isset($list_data)?$rows->kelas:NULL ?></td></tr>
                <tr><td>Jenis Kunjungan:</td><td><?= form_hidden('jenis', isset($id_resep)?$rows->jenis:NULL) ?><span class="label" id="jenis"><?= isset($id_resep)?$rows->jenis:'-' ?></span></td></tr>
            </table>
        </td></tr>
        </table>
        </div>
    </table>
    <script type="text/javascript">
        resep_load(<?= isset($id_resep)?$id_resep:null ?>);
    </script>
    <div class="data-list">
        <div id="psdgraphics-com-table">
            
            <div id="psdg-middle" class="data-input">
                <?php if (isset($id_resep)) { 
                    $noo = 1;
                    $nom = 0;
                    foreach ($list_data as $key => $data) { 
                    $t_tebus = isset($data->t_tebus)?$data->t_tebus:'0';
                    $data_nominal = isset($data->nominal)?$data->nominal:'0';
                        ?>
                    <div style="display: inline-block; width: 100%" class=tr_row>
                        <div class="masterresep" style="display: inline-block; width: 100%; border-bottom: 1px solid #f1f1f1; padding: 10px 0; ">
                                <tr><td>No. R/:</td><td><input style="border: none;" type=text name=nr[] id=nr<?= $key ?> value='<?= $noo ?>' class=nr size=20 onkeyup="Angka(this);" readonly maxlength=2 />
                                <tr><td>Jumlah Permintaan:</td><td><input type=text name=jr[] value="<?= $data->resep_r_jumlah ?>" id=jr<?= $key ?> class=jr size=20 onkeyup="Angka(this);" />
                                <tr><td>Jumlah Tebus:</td><td><input type=text name=jt[] value="<?= $data->resep_r_jumlah-$t_tebus ?>" id=jt<?= $key ?> class=jt onkeyup="Angka(this);" size=20 /> <span class="label">&nbsp;Total Tebus:</span><span class="label" id="total_tebus"><?= $t_tebus ?></span>
                                <tr><td>Aturan Pakai:</td><td><input type=text name=ap[] value="<?= $data->pakai_aturan ?>" id=ap<?= $key ?> class=ap size=20 />
                                <tr><td>Iterasi:</td><td><input type=text name=it[] value="<?= $data->iter ?>" id=it<?= $key ?> class=it size=10 value="0" onkeyup="Angka(this);" />
                                <tr><td>Biaya Apoteker:</td><td><select onchange="subTotal();" name=ja[] id=ja<?= $key ?>><option value="0-0">Pilih biaya ..</option><?php foreach ($biaya_apoteker as $value) { echo '<option '; if ($value->id == $data->tarif_id) echo 'selected'; echo ' value="'.$value->id.'-'.$value->nominal.'">'.$value->layanan.' '.$value->profesi.' '.$value->nama_jkp.' '.$value->jenis.' '.$value->bobot.' '.$value->kelas.' Rp. '.$value->nominal.'</option>'; } ?></select>
                                <tr><td></td><td><input type=button value="Tambah Kemasan Barang" onclick="add(<?= $key ?>);" id="addition<?= $key ?>" />
                                <input type=button value="Hapus R/" id="deletion<?= $key ?>" onclick="eliminate(this);" /> <input type=button value="Etiket" id="etiket<?= $noo ?>" class="etiket" onclick="cetak_etiket(<?= $noo ?>);" />
                        </div>
                        <div id=resepno<?= $key ?> style="display: inline-block;width: 100%"></div>
                    </div>
                    <script>
                            $('#addition<?= $key ?>, #deletion<?= $key ?>, #etiket<?= $noo ?>').button();
                    </script>
                <?php 
                    $detail = $this->m_resep->detail_data_resep_muat_data($data->id_rr)->result();
                    foreach($detail as $no => $val) { ?>
                         <div class=tr_rows style="width: 100%; display: block;">
                                <table align=right width=95% style="border-bottom: 1px solid #f4f4f4" class="detailobat<?= $key ?>">
                                <tr><td width=15%>Barcode:</td><td> <input type=text value="<?= isset($val->barcode)?$val->barcode:NULL ?>" name=bc<?= $key ?>[] id=bc<?= $key ?><?= $no ?> class=bc size=30 readonly /></td></tr>
                                <tr><td>Kemasan Barang:</td><td>  <input type=text name=pb<?= $key ?>[] value="<?= $val->barang ?> <?= ($val->kekuatan == '1')?'':$val->kekuatan ?>  <?= $val->satuan ?> <?= $val->sediaan ?> <?= $val->pabrik ?> <?= ($val->isi==1)?'':'@'.$val->isi ?> <?= $val->satuan_terkecil ?>" id=pb<?= $key ?><?= $no ?> class=pb size=60 />
                                        <input type=hidden name=id_pb<?= $key ?>[] value="<?= $val->id_packing ?>" id=id_pb<?= $key ?><?= $no ?> class=id_pb />
                                        <input type=hidden name=kr<?= $key ?>[] value="<?= $val->kekuatan ?>" id=kr<?= $key ?><?= $no ?> class=kr />
                                        <input type=hidden name=jp<?= $key ?>[] value="<?= $val->pakai_jumlah ?>" id=jp<?= $key ?><?= $no ?> class=jp /></td></tr>
                                <tr><td>Kekuatan:</td><td><span class=label id=kekuatan<?= $key ?><?= $no ?>><?= $val->kekuatan ?></span></td></tr>
                                <tr><td>Dosis Racik:</td><td> <input type=text name=dr<?= $key ?>[] value="<?= $val->dosis_racik ?>" id=dr<?= $key ?><?= $no ?> class=dr onkeyup="jmlPakai(<?= $key ?>,<?= $no ?>);" size=10 value="" /></td></tr>
                                <tr><td>Jumlah Pakai:</td><td><span class=label id=jmlpakai<?= $key ?><?= $no ?>><?= $val->pakai_jumlah ?></span></td></tr>
                                <tr><td></td><td><span class=label><input type=button value="Hapus" id="deleting<?= $key ?><?= $no ?>" onclick="eliminatechild(this,<?= $key ?>,<?= $no ?>);" /></span></td></tr></table>
                        </div>
                        <script>
                            $('#addition<?= $key ?>, #deletion<?= $key ?>').button();
                            $('#pb<?= $key ?><?= $no ?>').autocomplete("<?= base_url('inv_autocomplete/load_data_packing_barang') ?>",
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
                                        var isi = ''; var satuan = ''; var sediaan = ''; var pabrik = ''; var satuan_terkecil = ''; var kekuatan = '';
                                        if (data.isi !== '1') { var isi = '@ '+data.isi; }
                                        if (data.kekuatan !== null && data.kekuatan !== '0') { var kekuatan = data.kekuatan; }
                                        if (data.satuan !== null) { var satuan = data.satuan; }
                                        if (data.sediaan !== null) { var sediaan = data.sediaan; }
                                        if (data.pabrik !== null) { var pabrik = data.pabrik; }
                                        if (data.satuan_terkecil !== null) { var satuan_terkecil = data.satuan_terkecil; }
                                        if (data.id_obat === null) {
                                            var str = '<div class=result>'+data.nama+' '+pabrik+' '+isi+' '+satuan_terkecil+'</div>';
                                        } else {
                                            if (data.generik === 'Non Generik') {
                                                var str = '<div class=result>'+data.nama+' '+((kekuatan === '1')?'':kekuatan)+' '+satuan+' '+sediaan+' '+isi+' '+satuan_terkecil+'</div>';
                                            } else {
                                                var str = '<div class=result>'+data.nama+' '+((kekuatan === '1')?'':kekuatan)+' '+satuan+' '+sediaan+' '+pabrik+' '+isi+' '+satuan_terkecil+'</div>';
                                            }
                                        }
                                        return str;
                                    },
                                    width: 400, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                                    dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
                                }).result(
                                function(event,data,formated){
                                    if (data.kekuatan === null) {
                                        custom_message('Peringatan','Kekuatan untuk Obat yang dipilih tidak boleh null, silahkan ubah pada bagian master data obat');
                                        $(this).val('');
                                        $('#id_pb<?= $key ?><?= $no ?>').val('');
                                        $('#bc<?= $key ?><?= $no ?>').val('');
                                        $('#kekuatan<?= $key ?><?= $no ?>').html('');
                                        $('#dr<?= $key ?><?= $no ?>').val('');
                                        return false;
                                    }
                                    var isi = ''; var satuan = ''; var sediaan = ''; var pabrik = ''; var satuan_terkecil = ''; var kekuatan = '';
                                    if (data.isi !== '1') { var isi = '@ '+data.isi; }
                                    if (data.kekuatan !== null && data.kekuatan !== '0') { var kekuatan = data.kekuatan; }
                                    if (data.satuan !== null) { var satuan = data.satuan; }
                                    if (data.sediaan !== null) { var sediaan = data.sediaan; }
                                    if (data.pabrik !== null) { var pabrik = data.pabrik; }
                                    if (data.satuan_terkecil !== null) { var satuan_terkecil = data.satuan_terkecil; }
                                    if (data.id_obat === null) {
                                        $(this).val(data.nama+' '+pabrik+' '+isi+' '+satuan_terkecil);
                                    } else {
                                        if (data.generik === 'Non Generik') {
                                            $(this).val(data.nama+' '+((kekuatan === '1')?'':kekuatan)+' '+satuan+' '+sediaan+' '+isi+' '+satuan_terkecil);
                                        } else {
                                            $(this).val(data.nama+' '+((kekuatan === '1')?'':kekuatan)+' '+satuan+' '+sediaan+' '+pabrik+' '+isi+' '+satuan_terkecil);
                                        }
                                    }
                                    $('#id_pb<?= $key ?><?= $no ?>').val(data.id);
                                    $('#bc<?= $key ?><?= $no ?>').val(data.barcode);
                                    $('#kekuatan<?= $key ?><?= $no ?>').html(data.kekuatan);
                                    $('#dr<?= $key ?><?= $no ?>').val(data.kekuatan);

                                    jmlPakai(<?= $key ?>, <?= $no ?>);

                                });
                        </script>
                    <?php }
                    $noo++;
                    $nom = $nom + $data_nominal;
                    }
                } ?>
            </div>
            <?= form_button(null,'Tambah R /', 'id=addnewrow') ?>
<!--            <table width="100%">
                <tr><td>
                * Total Biaya Apoteker: <b id="totalbiaya"><?= isset($id_resep)?rupiah($nom):NULL ?></b>
                    </td></tr>
            </table>-->
            </div>
    </div>
        <?= form_button('save', 'Simpan', 'id=submit') ?>
        <?= form_button('copyresep', 'Salinan Resep', 'id=copyresep') ?>
        <?= form_button(null, 'Cetak Kitir', 'id=print') ?>
        <?php if (isset($id_resep)) { ?>
        <script type="text/javascript">
            $('#copyresep, #print').show();
        </script>
        <?= form_button(null, 'Delete', 'id=penyerahan') ?>
        
        <?php } ?>
        <?= form_button('Reset', 'Reset', 'id=reset') ?>
    <?= form_close() ?>
</div>