<?php
    $nilai_rujukan = array('Low' => 'Low', 'Neutral' => 'Neutral', 'High' => 'High');
    
?>
<title><?= $title ?></title>
<script type="text/javascript">
$(function() {
    $('#awal,#akhir').datetimepicker({
        changeYear: true,
        changeMonth: true
    });
    $('#cari').button({
        icons: {
            secondary: 'ui-icon-search'
        }
    }).click(function() {
        load_pemeriksaan_lab();
    });
    $('#reset').button({
        icons: {
            secondary: 'ui-icon-refresh'
        }
    }).click(function() {
        $('#loaddata').load('<?= base_url('pelayanan/laboratorium') ?>');
    });
    var lebar = $('#pasien').width();
    $('#pasien').autocomplete("<?= base_url('inv_autocomplete/load_data_penduduk_pasien') ?>",
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
            var str = '<div class=result>'+data.nama+' - '+data.no_rm+' <br/> '+data.alamat+'</div>';
            return str;
        },
        width: lebar, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
        dataType: 'json', // tipe data yang diterima oleh library ini disetup sebagai JSON
        cacheLength: 0
    }).result(
    function(event,data,formated){
        $(this).val(data.nama+' - '+data.no_rm);
        $('input[name=id_pasien]').val(data.penduduk_id);

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
            var str = '<div class=result>'+data.nama+' - '+data.kerja_izin_surat_no+'</div>';
            return str;
        },
        width: lebar, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
        dataType: 'json', // tipe data yang diterima oleh library ini disetup sebagai JSON
        cacheLength: 0
    }).result(
    function(event,data,formated){
        $(this).val(data.nama+' - '+data.kerja_izin_surat_no);
        $('input[name=id_dokter]').val(data.penduduk_id);
    });
    $('#pemeriksa').autocomplete("<?= base_url('inv_autocomplete/load_data_penduduk_dokter') ?>",
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
            var str = '<div class=result>'+data.nama+' - '+data.kerja_izin_surat_no+'</div>';
            return str;
        },
        width: lebar, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
        dataType: 'json', // tipe data yang diterima oleh library ini disetup sebagai JSON
        cacheLength: 0
    }).result(
    function(event,data,formated){
        $(this).val(data.nama);
        $('input[name=id_pemeriksar]').val(data.penduduk_id);
    });
    $('#layanan').autocomplete("<?= base_url('inv_autocomplete/layanan_jasa_load_data') ?>",
    {
        parse: function(data){
            var parsed = [];
            for (var i=0; i < data.length; i++) {
                parsed[i] = {
                    data: data[i],
                    value: data[i].nama // nama field yang dicari
                };
            }
            $('#id_tindakan'+i).val('');
            $('#icd_tindak'+i).val('');
            //$('#tindakan_tindak'+i).val('');
            return parsed;
        },
        formatItem: function(data,i,max){
            var str = '<div class=result>'+data.kode_icdixcm+' - '+data.nama+'</div>';
            return str;
        },
        width: lebar, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
        dataType: 'json', // tipe data yang diterima oleh library ini disetup sebagai JSON
        cacheLength: 0,
        max: 100
    }).result(
    function(event,data,formated){
        $(this).val(data.nama);
        $('#id_layanan').val(data.id);
    });
});

function form_add(id_pl) {
    var str = '<div id="form_hasil_lab" class=data-input>'+
                '<table width="100%" style="background: #f4f4f4; border: 1px solid #ccc; margin-top: 7px;"><tr valign=top><td width="50%">'+
                        '<table width=100% style="line-height: 22px; border-spacing: 0;">'+
                            '<tr><td width=40%>No. RM:</td><td id=arv_norm></td></tr>'+
                            '<tr><td>Nama Pasien:</td><td id=arv_nama></td></tr>'+
                            '<tr><td>Alamat Jalan:</td><td id=arv_alamat></td></tr>'+
                            '<tr><td>Wilayah:</td><td id=arv_wilayah></td></tr>'+
                            '<tr><td>Unit / Bangsal:</td><td id=arv_unit></td></tr>'+
                            '<tr><td>Kelas:</td><td id=arv_kelas></td></tr>'+
                            '<tr><td>No. TT:</td><td id=arv_no_tt></td></tr>'+
                            '<tr><td>Nama Dokter Pemesan:</td><td id=arv_dokter></td></tr>'+
                        '</table>'+
                    '</td><td width=50%>'+
                        '<table width=100% style="line-height: 22px; border-spacing: 0;">'+
                            '<tr><td width=40%>No.:</td><td id=arv_no_pemeriksaan></td></tr>'+
                            '<tr><td>Waktu Order:</td><td id=arv_waktu_order></td></tr>'+
                            '<tr><td>Waktu Hasil:</td><td id=arv_waktu_hasil></td></tr>'+
                            '<tr><td>Nama Analis Lab.:</td><td id=arv_analis></td></tr>'+
                            '<tr><td>Nama Layanan:</td><td id=arv_layanan></td></tr>'+
                            '<tr><td>Hasil:</td><td><?= form_input('hasil', NULL, 'id=hasil size=10') ?></td></tr>'+
                            '<tr><td>Nilai Rujukan:</td><td><select name=nilai id=nilai><?php foreach ($nilai_rujukan as $data) { ?> <option value="<?= $data ?>"><?= $data ?></option><?php } ?></select></td></tr>'+
                            '<tr><td>Satuan:</td><td><select name="satuan" id=satuan><?php foreach ($satuan as $data) { ?> <option value="<?= $data->id ?>"><?= $data->nama ?></option><?php } ?></select></td></tr>'+
                        '</table>'+
                    '</td></tr>'+
                '</table>'+
              '</div>';
        $('body').append(str);
        $('#form_hasil_lab').dialog({
            autoOpen: true,
            modal: true,
            width: 800,
            height: 330,
            title: 'Entry Hasil Pemeriksaan Lab',
            buttons: {
                "Simpan": function() {
                    
                },
                "Cancel": function() {
                    $(this).dialog().remove();
                }
            }, open: function() {
                $.ajax({
                    url: '<?= base_url('pelayanan/laboratorium_load_data_pemeriksaan') ?>/'+id_pl,
                    cache: false,
                    dataType: 'json',
                    success: function(data) {
                        $('#arv_norm').html(data.no_rm);
                        $('#arv_nama').html(data.pasien);
                        $('#arv_alamat').html(data.alamat);
                        $('#arv_wilayah').html((data.kelurahan !== null)?data.kelurahan:''+' '+(data.kabupaten !== null)?data.kabupaten:'');
                        $('#arv_unit').html(data.unit);
                        $('#arv_kelas').html(data.kelas);
                        $('#arv_no_tt').html(data.no_tt);
                        $('#arv_dokter').html(data.dokter);
                        
                        $('#arv_no_pemeriksaan').html(data.id);
                        $('#arv_waktu_order').html(data.waktu_order);
                        $('#arv_waktu_hasil').html(data.waktu_hasil);
                        $('#arv_analis').html(data.analis);
                        $('#arv_layanan').html(data.layanan);
                    }
                });
            }, close: function() {
                $(this).dialog().remove();
            }
        });
}

function load_pemeriksaan_lab() {
    $.ajax({
        url: '<?= base_url('pelayanan/laboratorium_load_data') ?>',
        cache: false,
        data: $('#form_laboratorium').serialize(),
        type: 'GET',
        success: function(data) {
            $('#result').html(data);
        }
    });
}

function delete_this(id_pl) {
    $('<div>Anda yakin akan menghapus data lab. ini?</div>').dialog({
        title: 'Information',
        modal: true,
        autoOpen: true,
        buttons: {
            "OK": function() {
                $.ajax({
                    url: '<?= base_url('pelayanan/delete_data_lab') ?>/'+id_pl,
                    dataType: 'json',
                    cache: false,
                    success: function() {
                        alert_delete();
                        load_pemeriksaan_lab();
                    }
                });
                $(this).dialog().remove();
            },
            "Batal": function() {
                $(this).dialog().remove();
            }
        },
        close: function() {
            $(this).dialog().remove();
        }
    });
    return false;
}
</script>
<div class="kegiatan">
<div class="titling"><h1><?= $title ?></h1></div>

<div class="data-input">
    <?= form_open('', 'id=form_laboratorium') ?>
    <table width="100%" class="inputan">Parameter Pelayanan Kunjungan</legend>
        <label for="awal">Range Waktu Order:</td><td><?= form_input('awal', date("d/m/Y H:i"), 'id=awal size=15') ?> <span class="label"> s . d</span> <?= form_input('akhir', date("d/m/Y H:i"), 'id=akhir size=15') ?>
        <label for="pasien">Nama Pasien:</td><td><?= form_input('pasien', NULL, 'id=pasien size=40') ?><?= form_hidden('id_pasien') ?>
        <label for="dokter">Nama Dokter:</td><td><?= form_input('dokter', NULL, 'size=40 id=dokter') ?> <?= form_hidden('id_dokter') ?>
        <label for="pemeriksa">Nama Analis Lab.:</td><td><?= form_input('pemeriksa', NULL, 'size=40 id=pemeriksa') ?> <?= form_hidden('id_pemeriksa') ?>
        <label for="layanan">Layanan:</td><td><?= form_input('layanan', NULL, 'size=40 id=layanan') ?> <?= form_hidden('id_layanan') ?>
        <tr><td></td><td><?= form_button(NULL, 'Cari', 'id=cari') ?> <?= form_button(NULL, 'Reset', 'id=reset') ?>
    </table>
    <?= form_close() ?>
</div>
<div id="result">
    
</div>
</div>