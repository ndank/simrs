<?php
require 'app/lib/common/master-data.php';
require 'app/lib/partial/message.php';
echo isset($_GET['msg'])?$mess:null;
?>
<script type="text/javascript">
$(function() {
    $('button[type=submit]').click(function() {
        if ($('#id_layanan').val() == '') {
            custom_message('Peringatan','Nama layanan tidak boleh kosong !');
            $('#nama').focus();
            return false;
        }
        if ($('#id_kategori').val() == '') {
            custom_message('Peringatan','Nama Kategori tidak boleh kosong !');
            $('#kategori').focus();
            return false;
        }
    })
    $('#nama').autocomplete("<?= app_base_url('common/autocomplete?opsi=layanan') ?>",
        {
        parse: function(data)
        {
            var parsed = [];
            for (var i=0; i < data.length; i++)
            {
                parsed[i] =
                {
                    data: data[i],
                    value: data[i].nama // nama field yang dicari
                };
            }
            return parsed;
        },
        formatItem: function(data,i,max)
        {
            var str = '<div class=result>'+data.nama+' <br/>Kode: '+data.code+'</div>';
            return str;
        },
        width: 270, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
        dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
    }).result(
        function(event,data,formated) {
            $(this).val(data.nama);
            $('#id_layanan').val(data.id);
        }
    );
    $('#kategori').autocomplete("<?= app_base_url('common/autocomplete?opsi=kategori') ?>",
        {
        parse: function(data)
        {
            var parsed = [];
            for (var i=0; i < data.length; i++)
            {
                parsed[i] =
                {
                    data: data[i],
                    value: data[i].nama // nama field yang dicari
                };
            }
            return parsed;
        },
        formatItem: function(data,i,max)
        {
            var str = '<div class=result>'+data.nama+'</div>';
            return str;
        },
        width: 270, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
        dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
    }).result(
        function(event,data,formated) {
            $(this).val(data.nama);
            $('#id_kategori').val(data.id);
        }
    );
    $('#TanpaBobot, #Ringan, #Sedang, #Berat').click(function() {
        cekSameData();
    })
})
function cekSameData() {
        var layanan = $('#id_layanan').val();
        var kategori= $('#id_kategori').val();
        var bobot = '';
        if($('#Tanpa Bobot').is(':checked') == true) { var bobot = 'Tanpa Bobot'; }
        if($('#Ringan').is(':checked') == true) { var bobot = 'Ringan'; }
        if($('#Sedang').is(':checked') == true) { var bobot = 'Sedang'; }
        if($('#Berat').is(':checked') == true) { var bobot = 'Berat'; }
        var nominal = $('#nominal').val();
        $.ajax({
            url: '<?= app_base_url('common/search')?>',
            data:'tarif=tarif&layanan='+layanan+'&kategori='+kategori+'&bobot='+bobot+'&nominal='+nominal,
            cache: false,
            dataType: 'json',
            success: function(msg){
                if (msg.status == true){
                    $('.msg').fadeIn('fast').html('Data packing barang sudah terdaftar !');
                    $('#simpan').attr('disabled', 'disabled');
                    return false;
                } else {
                    $('.msg').fadeOut('fast');
                    $('#simpan').removeAttr('disabled');
                    return false;
                }
            }
        });
}
</script>
<div class="kegiatan" title="Administrasi Tarif Jasa">
    <table width="100%" class="inputan">Master Layanan</legend>
        <div class="msg"></div>
        <?php
        if (isset($_GET['do']) and $_GET['do'] == 'edit') {
            $serv = tarif_muat_data($_GET['id']);
            foreach ($serv as $rows);
        }
        ?>
        <?= Form('controller/transaksi/tarif', 'post', null) ?>
        <?= InputHidden('id_tarif', isset($rows['id'])?$rows['id']:null, null) ?>
        <table width="100%">
            <tr><td width="15%">No.</td><td><?= get_last_id('tarif', 'id') ?></td> </tr>
            <tr><td>Nama Layanan</td><td><?= InputText('nama', isset($rows['nama'])?$rows['nama']:null, 'id=nama size=30 onBlur=cekSameData()') ?><?= InputHidden('id_layanan', isset($rows['layanan_id'])?$rows['layanan_id']:null, 'id=id_layanan') ?></td> </tr>
            <tr><td>Kategori</td><td><?= InputText('kategori', isset($rows['kategori'])?$rows['kategori']:null, 'id=kategori size=30 onBlur=cekSameData()') ?><?= InputHidden('id_kategori', isset($rows['tarif_kategori_id'])?$rows['tarif_kategori_id']:null, 'id=id_kategori') ?></td> </tr>
            <tr><td>Bobot</td><td><?= InputRadio('bobot', 'Tanpa Bobot', 'Tanpa Bobot', 'TanpaBobot', isset($rows['bobot'])?$rows['bobot']:NULL) ?>
                <?= InputRadio('bobot', 'Ringan', 'Ringan', 'Ringan', isset($rows['bobot'])?$rows['bobot']:NULL)   ?>
                <?= InputRadio('bobot', 'Sedang', 'Sedang', 'Sedang', isset($rows['bobot'])?$rows['bobot']:NULL) ?>
                <?= InputRadio('bobot', 'Berat', 'Berat', 'Berat', isset($rows['bobot'])?$rows['bobot']:NULL) ?></td> </tr>
            <tr><td>Nominal (Rp.)</td><td><?= InputText('nominal', rupiah(isset($rows['nominal'])?$rows['nominal']:NULL), 'id=nominal onkeyup=FormNum(this) onBlur=cekSameData() size=10') ?></td> </tr>
            <tr><td></td><td><?= ButtonSubmit('simpan', 'Simpan', 'id=simpan') ?> <?= ButtonCancel('Reset', 'transaksi/tarif','id=reset') ?></td> </tr>
        </table>
        <?= EndForm() ?>
    </table>
    <div class="data-list">
        <table class="tabel" width="70%">
            <tr>
                <th width="10%">No</th>
                <th>Nama</th>
                <th>Kategori</th>
                <th>Bobot</th>
                <th>Nominal</th>
                <th width="10%">Aksi</th>
            </tr>
            <?php
            $tarif = tarif_muat_data();
            foreach ($tarif as $key => $data) {
            ?>
            <tr class="<?= ($key%2==0)?'odd':'even' ?>">
                <td align="center"><?= ++$key ?></td>
                <td><?= $data['nama'] ?></td>
                <td><?= $data['kategori'] ?></td>
                <td><?= $data['bobot'] ?></td>
                <td><?= $data['nominal'] ?></td>
                <td class="aksi">
                    <a class="edit" href="?do=edit&id=<?= $data['id'] ?>"></a>
                    <a class="delete" href="<?= app_base_url('controller/transaksi/tarif') ?>?do=delete&id=<?= $data['id'] ?>"></a>
                </td>
            </tr>
            <?php
            } ?>
        </table>
    </div>
</div>