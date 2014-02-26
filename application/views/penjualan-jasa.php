<?php $this->load->view('message'); ?>
<script type="text/javascript">
var request;
var no_penduduk;
$(function() {
    $('#tabs').tabs();
    $('button[id=reset], #reset_cari').button({icons: {secondary: 'ui-icon-refresh'}});
    $('button[id=retur]').button({icons: {secondary: 'ui-icon-transferthick-e-w'}});
    $('button[id=addnewrow]').button({icons: {secondary: 'ui-icon-circle-plus'}});
    $('button[id=cari_pdd]').button({icons: {secondary: 'ui-icon-search'}});
    
    $('input[type=submit]').each(function(){
        $(this).replaceWith('<button type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'">' + $(this).val() + '</button>');
    });
    $('button[type=submit]').button({icons: {secondary: 'ui-icon-circle-check'}});
    $('button[id=deletion]').button({icons: {secondary: 'ui-icon-circle-close'}});
    $('#noresep').focus();
    $('#bayar').keyup(function() {
        FormNum(this);
        setKembali();
    });    
    
    $('#id_penduduk').autocomplete("<?= base_url('pelayanan/pasien_load_data') ?>",
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
            var kelurahan = '';
            if (data.kelurahan!==null) { var kelurahan = data.kelurahan; }
            var str = '<div class=result>'+data.nama+' - '+data.no_rm+'<br/>'+data.alamat+'</div>';
            return str;
        },
        max: 20,
        width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
        dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
    }).result(
    function(event,data,formated){
        fill(data);
        $(this).val(data.no_rm);
        $('input[name=id_penduduk_hide]').val(data.id);
        $('input[name=no_daftar]').val(data.no_daftar);
        get_list_pelayanan_kunjungan(data.no_daftar);
        list_penjualan(data.no_daftar, '');
        no_penduduk = data.id;
    });
});

$(function() {
    var r = $('.tr_row').length;
    if(r < 1){
        for(x = r; x <= (r+1); x++) {
            add(x);
        }
    }
    $(document).unbind().on("keydown", function (e) {
        if (e.keyCode === 119) {
            e.preventDefault();
            $('#addnewrow').click();
        }
    });
    $('#addnewrow').click(function() {
        var row = $('.tr_row').length;
        add(row);
        $('#nakes'+row).focus();
    });

    $('#pelayanan').change(function(){
        var id_pk = $('#pelayanan :selected').val();
        var no_daftar = $('input[name=no_daftar]').val();
        //list_penjualan(no_daftar, id_pk);
    });
});

function get_list_pelayanan_kunjungan(no_daftar){
    $.ajax({
        url: '<?= base_url("pelayanan/get_list_pelayanan_kunjungan") ?>/'+no_daftar,
        cache: false,
        dataType : 'json',
        success: function(data) {
            var opt = '';
            var bed = '';
            $('#pelayanan').html('');
            //opt = "<option value=''>Pilih Semua Pelayanan Kunjungan</option>";
            $('#pelayanan').append(opt);
            $.each(data, function(i, v){
                if (v.jenis === 'Rawat Inap') {
                    bed = v.nama_unit+" "+v.kelas+" "+v.nomor_bed;
                };
                var jenis_pl = (v.jenis_pelayanan !== null)?v.jenis_pelayanan:'Pasien Luar';
                var unit_layan = (v.unit_layanan !== null)?v.unit_layanan:'-';
                opt = "<option value='"+v.id+"'>"+jenis_pl+" ("+unit_layan+") "+" "+ bed +"</option>";
                $('#pelayanan').append(opt);
            });
        }
    });
}
function eliminate(el, id) {
    if (id !== undefined) {
        $('<div id="alert">Anda yakin akan menghapus data ini</div>').dialog({
            autoOpen: true,
            modal: true,
            buttons: {
                "OK": function() {
                    $.ajax({
                        url: '<?= base_url("inv_autocomplete/delete_penjualan_jasa") ?>/'+id,
                        cache: false,
                        dataType: 'json',
                        success: function(data) {
                            if (data === true) {
                                $('#alert').dialog().remove();
                                alert_delete();
                                var id_pk = $('#pelayanan :selected').val();
                                var no_daftar = $('input[name=no_daftar]').val();
                                list_penjualan(no_daftar, id_pk);
                            }
                        }
                    });
                    var parent = el.parentNode.parentNode;
                    parent.parentNode.removeChild(parent);
                    var jumlah = $('.tr_row').length-1;

                    for (i = 0; i <= jumlah; i++) {
                        $('.tr_row:eq('+i+')').children('td:eq(0)').children('.layanan').attr('id','layanan'+i);
                        $('.tr_row:eq('+i+')').children('td:eq(0)').children('.id_layanan').attr('id','id_layanan'+i);
                        $('.tr_row:eq('+i+')').children('td:eq(1)').attr('id','tarif'+i);
                        $('.tr_row:eq('+i+')').children('td:eq(2)').children('.jumlah').attr('id','jumlah'+i);
                        $('.tr_row:eq('+i+')').children('td:eq(3)').attr('id','subtotal'+i);
                    }
                    subtotal();


                },
                "Cancel": function() {
                    $(this).dialog().remove();
                }
            }
        });
    } else {
        $('<div id="alert">Anda yakin akan menghapus data ini</div>').dialog({
            autoOpen: true,
            modal: true,
            buttons: {
                "OK": function() {
                    var parent = el.parentNode.parentNode;
                    parent.parentNode.removeChild(parent);
                    $(this).dialog().remove();
                },
                "Cancel": function() {
                    $(this).dialog().remove();
                }
            }
        });
    }
}

function list_penjualan(no_daftar, id_pk){
    var pk = '';
    if(id_pk !== ''){
        pk = '/'+id_pk
    }
    $.ajax({
        url: '<?= base_url("inv_autocomplete/load_data_penjualan_jasa") ?>/'+no_daftar+pk,
        cache: false,
        success: function(data) {
            $('.form-inputan tbody').html(data);
        }
    });
}


function fill(data){
    //$('#id_kunjungan').html(data.no_daftar);
    $('#nama').html(data.nama);
    $('#alamat').html(data.alamat);
    $('#wilayah').html(data.kelurahan);
    $('#gender').html((data.gender=="L")?"Laki - laki":"Perempuan");
    $('#umur').html(hitungUmur(data.lahir_tanggal));
    $('#bangsal').html(data.bangsal);
    $('#kelas').html(data.kelas);
    $('#nott').html(data.no_bed);

    $("#nama_pj").html(data.nama_pjwb);
    $("#alamat_pj").html(data.alamat_pjwb);
    $("#wilayah_pj").html(data.kelurahan_pj);
    $("#wilayah_pj").append(" ");
    $("#wilayah_pj").append(data.kecamatan_pj);
    $("#telp_pj").html(data.telp_pjwb);

}

function subtotal(i) {
    var tarif = currencyToNumber($('#nominal'+i).html());
    var jumlah  = $('#jumlah'+i).val();
    var subtotal = tarif * jumlah;
    $('#subtotal'+i).html(numberToCurrency(subtotal));
    total_jual_jasa();
}

function total_jual_jasa() {
    var jumlah = $('.tr_row').length-1;
    var total = 0;
    for (i = 0; i <= jumlah; i++) {
        var subtotal = currencyToNumber($('#subtotal'+i).html());
        if (!isNaN(subtotal)) {
            var total = total + subtotal;
        }
    }
    $('#total, #totals').html(numberToCurrency(total));
}

function add(i) {
     str = '<tr class=tr_row>'+
                '<td align="center">'+(i+1)+'</td>'+
                '<td><input type=text name=waktu[] id=waktu'+i+' value="<?= date("d/m/Y H:i") ?>" /></td>'+
                '<td><input type=text name=nakes[] id=nakes'+i+' /><input type=hidden value="" name=id_nakes[] id=id_nakes'+i+' /></td>'+
                '<td><input type=text name=layanan[] id=layanan'+i+' class=layanan size=60 /><input type=hidden name=id_tarif[] id=id_tarif'+i+' class=id_tarif /></td>'+
                '<td align=right id="nominal'+i+'"></td><input type=hidden name=tarifs[] id=tarifs'+i+'/>'+
                '<td><input type=text name=jumlah[] id=jumlah'+i+' size=10 onkeyup="subtotal('+i+')" />'+
                '<input type=hidden name=jenis_pelayanan_kunjungan[] id=jenis_pelayanan_kunjungan'+i+' class=jenis_pelayanan_kunjungan />'+
                '<input type=hidden name=id_unit[] id=id_unit'+i+' class=id_unit /></td>'+
                '<td align=right id=subtotal'+i+'></td>'+
                '<td class=aksi align=center><a class=deletion onclick=eliminate(this)></a><input type=hidden name=disc[] id=disc'+i+' />'+
            '</tr>';

    $('.form-inputan tbody').append(str);
    $('#waktu'+i).datetimepicker({
        changeYear : true,
        changeMonth : true
    });
     $('#nakes'+i).autocomplete("<?= base_url('inv_autocomplete/load_data_pegawai_nakes') ?>",
            {
                parse: function(data){
                    var parsed = [];
                    for (var i=0; i < data.length; i++) {
                        parsed[i] = {
                            data: data[i],
                            value: data[i].nama // nama field yang dicari
                        };
                    }
                    $('#id_nakes'+i).val('');
                    return parsed;
                },
                formatItem: function(data,i,max){
                    var str = '<div class=result>'+data.nama+'<br/>'+data.nip+'</div>';
                    return str;
                },
                width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
            }).result(
            function(event,data,formated){
                $(this).val(data.nama);
                $('#id_nakes'+i).val(data.id);
            });
    $('#layanan'+i).autocomplete("<?= base_url('inv_autocomplete/get_layanan_jasa') ?>/param/",
    {
        extraParams :{ 
            id_unit : function(){
                return $('input[name=id_unit_pegawai]').val();
            }
        },
        parse: function(data){
            var parsed = [];
            for (var i=0; i < data.length; i++) {
                parsed[i] = {
                    data: data[i],
                    value: data[i].layanan // nama field yang dicari
                };
            }
            return parsed;
        },
        formatItem: function(data,i,max){
            var profesi = data.profesi;
            if (data.profesi == null) {
                var profesi = '';
            }
            var jurusan = data.jurusan;
            if (data.jurusan == null) {
                var jurusan = '';
            }
            var jenis = data.jenis_pelayanan_kunjungan;
            if (data.jenis_pelayanan_kunjungan == null) {
                var jenis = '';
            }
            var unit = data.unit;
            if (data.unit == null) {
                var unit = '';
            }
            var bobot = data.bobot;
            if (data.bobot == null) {
                var bobot = '';
            }
            var kelas = data.kelas;
            if (data.kelas == null) {
                var kelas = '';
            }

            var barang = data.barang;
            if(data.barang == null){
                var barang = '';
            }
            var str = '<div class=result>'+barang+' '+data.layanan+' '+profesi+' '+jurusan+' '+/*jenis*/''+' '+unit+' '+bobot+' '+kelas+'</div>';
            return str;
        },
        max: 20,
        width: 350, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
        dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
    }).result(
    function(event,data,formated){
        var profesi = data.profesi;
        if (data.profesi == null) {
            var profesi = '';
        }
        var jurusan = data.jurusan;
        if (data.jurusan == null) {
            var jurusan = '';
        }
        var jenis = data.jenis_pelayanan_kunjungan;
        if (data.jenis_pelayanan_kunjungan == null) {
            var jenis = '';
        }
        var unit = data.unit;
        if (data.unit == null) {
            var unit = '';
        }
        var bobot = data.bobot;
        if (data.bobot == null) {
            var bobot = '';
        }
        var kelas = data.kelas;
        if (data.kelas == null) {
            var kelas = '';
        }

        var barang = data.barang;
            if(data.barang == null){
                var barang = '';
            }

        if(jenis == ''){
            custom_message('Peringatan','Jenis pelayanan pada data tarif yang dipilih belum ditentukan','#layanan'+i);
        }else{
            $(this).val(barang+' '+data.layanan+' '+profesi+' '+jurusan+' '+jenis+' '+unit+' '+bobot+' '+kelas);
            $('#id_tarif'+i).val(data.id_tarif);
            $('#nominal'+i).html(numberToCurrency(data.nominal));
            $('#tarifs'+i).val(data.nominal);
            $('#jumlah'+i).val('1').focus();
            $('#tindakan_jasa'+i).val(data.profesi_layanan_tindakan_jasa_total);
            $('#jenis_pelayanan_kunjungan'+i).val(jenis);
            $('#id_unit'+i).val(data.id_unit);
            subtotal(i);
        }
        
    });
}

$(function() {
    $('#id_penduduk').focus();
    $('#reset').click(function() {
        var url = $('#form_penjualan_jasa').attr('action');
        $('#loaddata').load(url);
    });
    $('#form_penjualan_jasa').submit(function() {
        var jumlah = $('.tr_row').length-1;
        
        if(jumlah < 0){
            custom_message('Peringatan','Tidak ada data !','#no_rm');
            return false;
        }

        if($('#pelayanan:selected').val() === ''){
            custom_message('Peringatan','Pelayanan kunjungan belum dipilih !','#pelayanan');
            return false;
        }

        for (i = 0; i <= jumlah; i++) {
            
            if ($('#waktu'+i).val() === '') {
                custom_message('Peringatan','Waktu harus diisi !','#waktu'+i);
                return false;
            }

            if ($('#layanan'+i).val() === '') {
                custom_message('Peringatan','Layanan harus diisi !','#layanan'+i);
                return false;
            }

            if ($('#jumlah'+i).val() === '') {
                custom_message('Peringatan','Jumlah harus diisi !','#jumlah'+i);                
                return false;
            }
            
        }
        var post = $(this).attr('action');
         if(!request) {
            request = $.ajax({
            type: 'POST',
            url: post+'/'+$('input[name=no_daftar]').val(),
            dataType: 'json',
            data: $(this).serialize(),
            success: function(data) {
                if (data.status === true) {
                    $('button[type=submit]').hide();
                    
                    list_penjualan(data.no_daftar, $('#pelayanan :selected').val());
                   
                    alert_tambah();
                } else {
                    custom_message('Peringatan','kode rekening tarif belum ditentukan');
                }
                request = null;
            }
            });
        }
        return false;
    });
   
});
</script>
<title><?= $title ?></title>
<div class="titling"><h1><?= $title ?></h1></div>
<div class="kegiatan">
    <?= form_open('pelayanan/penjualan_jasa', 'id=form_penjualan_jasa') ?>
    <div id="tabs">
        <ul>
            <li><a href="#tabs-1">Jasa Tindakan & Pemeriksaan</a></li>
        </ul>
        <div id="tabs-1">
        <?= form_hidden('total', null, 'id=total_tagihan') ?>
        <?= form_hidden('non_pasien') ?>
        <?= form_hidden('id_unit_pegawai', $id_unit) ?>
        <?= form_hidden('no_daftar', isset($no_daftar)?$no_daftar:'') ?>
            <table width="100%" cellpadding="0" cellspacing="0">
            <tr valign="top"><td width="50%">
                    <table width="100%" class="inputan">
                    <tr><td>No. RM:</td><td><?= form_input('id_penduduk', (isset($_GET['id'])?$rows['id']:null).(isset($data->no_rm)?$data->no_rm:null), 'id=id_penduduk size=40') ?><span class="label link_button" id="bt_cari" title="Klik untuk mencari data kunjungan pasien non poliklinik atau igd"></span>
                    <?= form_hidden('id_penduduk_hide',(isset($data->no_rm)?$data->id_penduduk:null)) ?></td></tr>
                    <tr><td>Nama:</td><td id="nama"><span class="label"><?= isset($data)?$data->pasien:NULL ?></span></td></tr>
                    <tr><td>Pelayanan Kunjungan</td><td><?= form_dropdown('pelayanan', isset($list_pk)?$list_pk:array(),array(), 'id=pelayanan class=standar')?></td></tr>
                    <tr><td>Alamat Jalan:</td><td id="alamat"><?= isset($data)?$data->alamat:NULL ?></td></tr>
                    <tr><td>Wilayah:</td><td id="wilayah"><?= isset($data)?$data->kelurahan." ".$data->kecamatan:NULL ?></td></tr>
                    <tr><td>Sex:</td><td id="gender"><?= isset($data)?(($data->gender=="L")?"Laki-Laki":"Perempuan"):NULL ?></td></tr>
                    <tr><td>Umur:</td><td id="umur"><?= isset($data)?hitungUmur($data->lahir_tanggal):NULL ?></td></tr>
                    <!--<tr><td>ID Kunjungan</td><td><span class="label" id="id_kunjungan"><?= isset($data)?$data->no_daftar:'' ?></span> -->
                   
                    </table>
            </td><td>
                <table width="100%" class="inputan">
                    <tr><td colspan="2"><h2>Penanggung Jawab</h2></td></tr>
                    <tr><td>Nama:</td><td id="nama_pj"><?= isset($data->nama_pjwb)?$data->nama_pjwb:NULL ?></td></tr>
                    <tr><td>Alamat:</td><td id="alamat_pj"><?= isset($data->alamat_pjwb)?$data->alamat_pjwb:NULL ?></td></tr>
                    <tr><td>Wilayah:</td><td id="wilayah_pj"><?= (isset($data->kelurahan_pj)&(isset($data->kecamatan_pj)))?$data->kelurahan_pj." ".$data->kecamatan_pj:NULL ?></td></tr>
                    <tr><td>No. Telp:</td><td id="telp_pj"><?= isset($data->telp_pj)?$data->telp_pj:NULL ?></td></tr>
                    <tr><td colspan="2"><h2>Rencana Pembayaran</h2></td></tr>
                    <tr><td>Asuransi:</td><td id="asuransi"></td></tr>
                </table>
                </td></tr>
            </table>
            
        

        <div class="data-list">
            <?= form_button(null, 'Tambah Baris (F8)', 'id=addnewrow') ?>
            <?php if (isset($_GET['id'])) { ?>
            <h3>Jasa yang pernah di terima pasien :</h3>
            <?php } ?>
            <table class="list-data form-inputan" width="100%">
                <thead>
                <tr>
                    <th width="5%">No.</th>
                    <th width="15%">Waktu</th>
                    <th width="20%">Nakes</th>
                    <th width="28%">Tarif</th>
                    <th width="10%">Nominal</th>
                    <th width="5%">Jumlah</th>
                    <th width="10%">Sub Total</th>
                    <th width="2%">#</th>
                </tr>
                </thead>
                <tbody>
                    <?php
                    if (isset($_GET['msg'])) { 
                    $penjualan = penjualan_jasa_muat_data($_GET['id']);
                    $no = 0;
                    $total = 0;
                    foreach ($penjualan as $key => $data) {
                        $layanan = _select_unique_result("select * from tarif t join layanan l on (t.layanan_id = l.id) where t.id = '$data[tarif_id]'");
                        //$hjual = ($data['hna']*($data['margin']/100))+$data['hna'];
                    ?>
                    <tr class="<?= ($key%2==0)?'odd':'even' ?> tr_row">
                        <td><?= $layanan['nama'] ?></td>
                        <td align="right"><?= rupiah($data['tarif']) ?></td>
                        <td align="center"><?= $data['frekuensi'] ?></td>
                        <td align="right">
                            <?= rupiah($data['tarif']*$data['frekuensi']) ?>
                        </td>
                        <td align="center">-</td>
                    </tr>
                    <?php $no++; 
                        $total = $total + ($data['tarif']*$data['frekuensi']);
                        } 
                    } ?>

                    <?php
                    if (isset($list_data)) { 
                        $list['list_data'] = $list_data;
                        $this->load->view('penjualan-jasa_table', $list);
                    } 
                    ?>
                </tbody>
                <tfoot>
                    <tr class="odd">
                        <td align="right" colspan="6">Total</td>
                        <td align="right" id="totals"></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table> 
        </div>
    
    <?= form_submit('save', 'Simpan', 'id=save') ?>
    <?= form_button('Reset', 'Reset', 'id=reset') ?>
    
    <?= form_close() ?>
    </div>
</div>

 <div id="form_cari" style="display: none;position: static; background: #fff; padding: 10px;">
        <div class="data-input">
            <?= form_open('','id=formcari')?>
            <table class="inputan" width="100%">
                <tr><td>No. RM:</td><td><?= form_input('norm', null, 'id=norm size=30 class=input-text') ?>
                <tr><td>Nama Pasien:</td><td><?= form_input('nama', null, 'id=nama_cari size=30 class=input-text') ?>
                <tr><td>Alamat:</td><td><?= form_textarea('alamat','','id=alamat_cari class=standar')?>
                <tr><td></td><td><?= form_button('', 'Cari', 'id=cari_pdd onclick=cari_penduduk(1)'); ?>
                <?= form_button('reset', 'Reset', 'id=reset_cari onclick=reset_pencarian();') ?>
            </table>
            <?= form_close() ?>

            <div id="list_penduduk"></div>
        </div>
    </div>

    <script type="text/javascript">
        $(function(){
            $('#formcari').submit(function(){
                cari_penduduk(1);
                return false;
            });

            $('#bt_cari').click(function(){
                $('#form_cari').dialog('open');
            });

            $('#form_cari').dialog({
                autoOpen: false,
                height: 550,
                width: 1024,
                title : 'Pencarian Penduduk',
                modal: true,
                close : function(){
                    reset_pencarian();
                },
                open : function(){
                    cari_penduduk(1);
                }
            });
        });

        function cari_penduduk(page){
            $.ajax({
                url: '<?= base_url("pelayanan/search_pendaftaran_penduduk") ?>/'+page,
                cache: false,
                data : $('#formcari').serialize(),
                success: function(data) {
                   $('#list_penduduk').html(data);
                }
            });
        }

        function reset_pencarian(){
            $('#norm, #nama_cari, #alamat_cari').val('');
            cari_penduduk(1);
        }

    

        function pilih_penduduk(id, id_daftar){
            //$('#id_kunjungan').html(id_daftar);
            $('input[name=id_penduduk_hide]').val(id);
            $('input[name=no_daftar]').val(id_daftar);
            $('input[name=non_pasien]').val('non');
            $.ajax({
                url: '<?= base_url("demografi/get_penduduk") ?>/'+id,
                cache: false,
                dataType :'json',
                success: function(data) {
                    $('#nama').html(data.nama);
                    $('#alamat').html(data.alamat);
                    $('#gender').html((data.gender==="L")?"Laki - laki":"Perempuan");
                    $('#umur').html(hitungUmur(data.lahir_tanggal));
                    get_kelurahan(data.kelurahan_id);
                }
            });
            get_list_pelayanan_kunjungan(id_daftar);
            list_penjualan(id_daftar, '');
            $('#form_cari').dialog('close');
                
        }

        function get_kelurahan(kel_id){
            if(kel_id != ''){
                $.ajax({
                    url: '<?= base_url("demografi/detail_kelurahan") ?>/'+kel_id,
                    cache: false,
                    dataType :'json',
                    success: function(data) {
                        $('#wilayah').html(data.nama);
                    }
                });
            }
        }

        function paging(page, tab, cari){
            cari_penduduk(page);
        }

    </script>