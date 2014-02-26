<?php

$jasa = 0;
foreach ($list_data as $key => $data) { 
    $kemasan = $this->db->query("select k.*, s.nama from kemasan k join satuan s on (k.id_kemasan = s.id) where k.id_barang = '".$data->id_barang."'")->result();
    $jasa = $jasa + $data->nominal;
    ?>
<tr class="tr_rows">
    <td align=center><?= ++$key ?></td>
    <td>&nbsp;<?= $data->nama_barang ?><input type=hidden name=id_barang[] value="<?= $data->id_barang ?>" class=id_barang id=id_barang<?= $key ?> /></td>
    <td><input type=text name=jumlah[] id=jumlah<?= $key ?> value="<?= $data->jumlah_pakai ?>" class="jumlah" style="text-align: center;" /></td>
    <td><input type=hidden name=harga_jual[] id=harga_jual<?= $key ?> class="harga_jual" /> <select name=kemasan[] class="kemasan" id=kemasan<?= $key ?>><?php foreach ($kemasan as $rows) { echo '<option value="'.$rows->id.'"'; if ($rows->default_kemasan === '1') echo 'selected'; echo '>'.$rows->nama.'</option>'; } ?></select></td>
    <td align=center><select name=ed[] class=ed id=ed<?= $key ?> class=ed></select></td>
    <td align=center id=sisa<?= $key ?>></td>
    <td align=right id=hargajual<?= $key ?>></td>
    <td><input type=text name=diskon_rupiah[] style="text-align: right;" class="diskon_rupiah" id=diskon_rupiah<?= $key ?> value="0" onblur="FormNum(this)" /></td>
    <td><input type=text name=diskon_persen[] style="text-align: center;" class="diskon_persen" id=diskon_persen<?= $key ?> value="0" /></td>
    <td align=right id=subtotal<?= $key ?>></td>
    <td align=center><img onclick="removeMe(this);" title="Klik untuk hapus" src="<?= base_url('assets/images/delete.png') ?>" class=add_kemasan align=left /></td>
</tr>
<script type="text/javascript">
$.ajax({
    url: '<?= base_url('autocomplete/get_detail_harga_barang_resep') ?>?id='+<?= $data->id_barang ?>+'&jumlah='+<?= $data->jumlah_pakai ?>,
    dataType: 'json',
    cache: false,
    success: function(data) {
        hitung_detail_total(<?= $key ?>, <?= $data->jumlah_pakai ?>, data.diskon_rupiah, data.diskon_persen, Math.ceil(data.harga_jual));
        hitung_total_penjualan();
    }
});
$.getJSON('<?= base_url('autocomplete/get_expiry_barang')?>?id='+<?= $data->id_barang ?>, function(data){
    $('#ed'+<?= $key ?>).html('');
    var jmled = 0;
    $.each(data, function (index, value) {
        $('#ed'+<?= $key ?>).append("<option value='"+value.ed+"'>"+datefmysql(value.ed)+"</option>");
        jmled++;
    });
    if (jmled === 0) {
        $('#ed'+<?= $key ?>).append("<option value=''></option>");
    }
});
$('#jumlah'+<?= $key ?>).blur(function() {
    var id  = $('#kemasan'+<?= $key ?>).val();
    var jum = $('#jumlah'+<?= $key ?>).val();
    $.ajax({
        url: '<?= base_url('autocomplete/get_detail_harga_barang') ?>?id='+id+'&jumlah='+jum,
        dataType: 'json',
        cache: false,
        success: function(data) {
            hitung_detail_total(<?= $key ?>, jum*data.isi_satuan, data.diskon_rupiah, data.diskon_persen, Math.ceil(data.harga_jual_resep));
            hitung_total_penjualan();
        }
    });
});
$('#kemasan'+<?= $key ?>).change(function() {
    var id  = $('#kemasan'+<?= $key ?>).val();
    var jum = $('#jumlah'+<?= $key ?>).val();
    $.ajax({
        url: '<?= base_url('autocomplete/get_detail_harga_barang') ?>?id='+id+'&jumlah='+jum,
        dataType: 'json',
        cache: false,
        success: function(data) {
            hitung_detail_total(<?= $key ?>, jum*data.isi_satuan, data.diskon_rupiah, data.diskon_persen, Math.ceil(data.harga_jual_resep));
            hitung_total_penjualan();
        }
    });
});
$.ajax({
    url: '<?= base_url('autocomplete/get_stok_sisa') ?>/'+<?= $data->id_barang ?>,
    dataType: 'json',
    cache: false,
    success: function(data) {
        if (data.sisa === null) {
            sisa = '0';
        } else {
            sisa = data.sisa;
        }
        $('#sisa'+<?= $key ?>).html(sisa);
    }
});
</script>
<?php } ?>
<script type="text/javascript">
$('#biaya-apt').html('<?= $jasa ?>');
<?php
    if (isset($cek->id)) { 
        if ($cek->sisa < 0) {
            $kekurangan = '0';
        } else {
            $kekurangan = $cek->sisa;
        }
        ?>    
        var str = '<tr class=adding><td>Total Terbayar:</td><td>Rp <?= rupiah($cek->terbayar) ?>, 00</td></tr>'+
                  '<tr class=adding><td>Kekurangan:</td><td>Rp <span id=kekurangan><?= rupiah($kekurangan) ?></span>, 00</td></tr>';
        $('#detail_harga_jual').append(str);

<?php } ?>
</script>