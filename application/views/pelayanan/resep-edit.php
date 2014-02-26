<?php

foreach ($list_data as $key => $data) {
?>
<tr class="tr_rows">
    <td align=center><?= ++$key ?></td>
    <td><input type=text name=no_r[] value="<?= $data->r_no ?>" id=no_r<?= $key ?> value="" style="text-align: center;" /></td>
    <td><input type=hidden name=id_barang[] id=id_barang<?= $key ?> value="<?= $data->id_barang ?>" style="text-align: center;" /><?= $data->nama_barang ?></td>
    <td><input type=text name=jp[] id=jp<?= $key ?> value="<?= $data->resep_r_jumlah ?>" style="text-align: center;" /></td>
    <td><input type=text name=jt[] id=jt<?= $key ?> value="<?= $data->tebus_r_jumlah ?>" style="text-align: center;" /></td>
    <td align="center" id="sisa<?= $key ?>"></td>
    <td align="center"><input type=text name=a[] id="a<?= $key ?>" value="<?= $data->aturan ?>" style="text-align: right; width: 40%" />DD<input type=text name=p[] id="p<?= $key ?>" value="<?= $data->pakai ?>" style="text-align: left; width: 40%" /></td>
    <td><input type=text name=it[] id=it<?= $key ?> value="<?= $data->iter ?>" style="text-align: center;" /></td>
    <td><input type=text name=dr[] id=dr<?= $key ?> value="<?= $data->dosis_racik ?>" style="text-align: center;" /></td>
    <td><input type=text name=jpi[] id=jpi<?= $key ?> value="<?= $data->jumlah_pakai ?>" style="text-align: center;" /></td>
    <td><input type=hidden name=id_tarif[] id=id_tarif<?= $key ?> value="<?= $data->id_tarif ?>" /> <input type=text name=jasa[] id=jasa<?= $key ?> onkeyup="FormNum(this);" value="<?= rupiah($data->nominal) ?>" style="text-align: right;" /></td>
    <td><input type=text name=hrg_barang[] id=hrg_barang<?= $key ?> value="<?= rupiah($data->jual_harga) ?>" style="text-align: right;" /></td>
    <td class=aksi><img onclick="removeMe(this);" title="Klik untuk hapus" src="<?= base_url('assets/images/delete.png') ?>" class=add_kemasan align=left /></td>
  </tr>
  <script>
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
