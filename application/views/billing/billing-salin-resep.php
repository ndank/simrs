<script type="text/javascript">
function form_cari_resep() {
    var str = '<div id=cari_resep_dialog class="data-input"><fieldset><table width=100% cellpadding=0 cellspacing=0>'+
            '<tr><td width=20%>Range Tanggal:</td><td><?= form_input('awal',NULL,'size=10 id=awal') ?> <span class="label" style="padding-left: 5px;"> s/d </span> <?= form_input('akhir',NULL,'size=10 id=akhir') ?></td></tr>'+
            '<tr><td>Nama Apoteker:</td><td><?= form_input('apoteker',NULL,'size=40 id=apoteker') ?><input type=hidden name=id_apoteker id=id_apoteker /></td></tr>'+
            '<tr><td>Nama Dokter:</td><td><?= form_input('dokter',NULL,'size=40 id=dokter') ?><input type=hidden name=id_adokter id=id_dokter /> </td></tr>'+
            '<tr><td>Nomor RM / Nama Pasien:</td><td><?= form_input('pasien',NULL,'size=40 id=pasien') ?><input type=hidden name=id_pasien id=id_pasien /> </td></tr>'+
            '</table></table></div>';
    var wWidth = $(window).width();
    var dWidth = wWidth * 0.7;

    var wHeight= $(window).height();
    var dHeight= wHeight * 1;
    $(str).dialog({
        title: 'Pencarian Data Resep Pasien',
        autoOpen: true,
        modal: true,
        width: dWidth,
        height: dHeight,
        open: function() {
            $('#pasien').focus();
        },
        close: function() {
            $('#pasien').focus();
            $(this).dialog().remove();
        }
    });
    $('#awal, #akhir').datepicker({
        changeYear: true,
        changeMonth: true
    });
    $('#apoteker').autocomplete("<?= base_url('inv_autocomplete/load_data_penduduk_apoteker') ?>",
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
            var str = '<div class=result>'+data.nama+' - '+data.sip_no+'</div>';
            return str;
        },
        width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
        dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
    }).result(
    function(event,data,formated){
        $(this).val(data.nama+' - '+data.sip_no);
        $('input[name=id_apoteker]').val(data.id);

    });
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
        width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
        dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
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
        width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
        dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
    }).result(
    function(event,data,formated){
        $(this).val(data.nama+' - '+data.kerja_izin_surat_no);
        $('input[name=id_dokter]').val(data.penduduk_id);
    });
    
}
$(function() {
    $('#cari_resep').click(function() {
        form_cari_resep();
    });
    $('#noresep').autocomplete("<?= base_url('inv_autocomplete/load_data_no_resep') ?>",
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
    }).result(function(event,data,formated){
        $(this).val(data.id);
    });
});
</script>
<div class="kegiatan">
    <div class="data-input">
        <table width="100%" class="inputan">Entri Pembayaran</legend>
            <table width="100%" cellpadding="0" cellspacing="0" style="line-height: 18px;">
                <tr><td width="20%">Tanggal:</td><td><?= indo_tgl(date("Y-m-d")) ?></td></tr>
                <tr><td>No. Resep:</td><td><?= form_input('noresep', NULL, 'id=noresep size=40') ?>
                        <?= form_hidden('id_resep') ?>
                        <div class="search_pdd" id="cari_resep" title="Klik untuk mencari data resep pasien"/>
                </td></tr>
                <tr><td>Total:</td><td><?= form_input('total', NULL, 'id=total size=40') ?></td></tr>
                <tr><td>Tunai:</td><td><?= form_input('tunai', NULL, 'id=tunai size=40') ?></td></tr>
                <tr><td>Kembalian:</td><td id="kembalian"></td></tr>
            </table>
        </table>
    </div>
</div>