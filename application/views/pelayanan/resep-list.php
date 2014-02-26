<table cellspacing="0" width="100%" class="list-data">
<thead>
<tr class="italic">
    <th width="3%">No.</th>
    <th width="5%">No.<br/>Resep</th>
    <th width="5%">Tanggal</th>
    <th width="3%">ID</th>
    <th width="15%">Pasien</th>
    <th width="15%">Dokter</th>
    <th width="3%">No.R</th>
    <th width="10%">Apoteker</th>
    <th width="5%">Jasa</th>
    <th width="20%">Nama Barang</th>
    <th width="3%">Dosis <br/> Racik</th>
    <th width="4%">Jumlah<br/> Pakai</th>
    <th width="5%">Harga<br/>Barang</th>
    <th width="3%">#</th>
</tr>
</thead>
<tbody>
    <?php
    $id_resep = "";
    $jasa = "";
    $no = 1;
    foreach ($list_data as $key => $data) { 
        $str = $data->id.'#'.$data->id_penduduk_dokter.'#'.$data->dokter.'#'.$data->id_pasien.'#'.$data->pasien.'#'.$data->keterangan.'#'.$data->id_kunjungan_pelayanan;
        ?>
    <tr class="<?= ($data->id_resep !== $id_resep)?'odd':'even' ?>">
        <td align="center"><?= ($data->id_resep !== $id_resep)?($auto++):NULL ?></td>
        <td align="center"><?= ($data->id_resep !== $id_resep)?$data->id_resep:NULL ?></td>
        <td align="center"><?= ($data->id_resep !== $id_resep)?datetimefmysql($data->waktu):NULL ?></td>
        <td align="center"><?= ($data->id_resep !== $id_resep)?$data->id_pasien:NULL ?></td>
        <td><?= ($data->id_resep !== $id_resep)?$data->pasien:NULL ?></td>
        <td><?= ($data->id_resep !== $id_resep)?$data->dokter:NULL ?></td>
        <td align="center"><?= (($data->id_resep !== $id_resep) or ($data->r_no !== $jasa))?'<div title="Cetak Etiket" class="etiket" onclick=print_etiket("'.$data->id_resep.'","'.$data->r_no.'");>'.$data->r_no.'</div>':NULL ?></td>
        <td><?= (($data->id_resep !== $id_resep) or ($data->r_no !== $jasa))?$data->apoteker:NULL ?></td>
        <td align="right"><?= (($data->id_resep !== $id_resep) or ($data->r_no !== $jasa))?rupiah($data->nominal):NULL ?></td>
        <td><?= $data->nama_barang ?></td>
        <td align="center"><?= $data->dosis_racik ?></td>
        <td align="center"><?= $data->jumlah_pakai ?></td>
        <td align="right"><?= rupiah($data->jual_harga) ?></td>
        <td class='aksi' align='center'>
            <?php
            if ($data->id_resep !== $id_resep) { ?>
                <!--<a class='jual-link' onclick="cetak_kitir('<?= $data->id_resep ?>');" title="Klik untuk penjualan resep">&nbsp;</a>-->
                <a class='printing' onclick="cetak_copy_resep('<?= $data->id_resep ?>');" title="Klik untuk cetak copy resep">&nbsp;</a>
                <a class='edition' onclick="edit_resep('<?= $str ?>','<?= $data->id_resep ?>');" title="Klik untuk edit resep">&nbsp;</a>
                <a class='deletion' onclick="delete_resep('<?= $data->id_resep ?>', '<?= $page ?>');" title="Klik untuk hapus">&nbsp;</a>
            <?php } ?>
        </td>
    </tr>
    <?php 
    $jasa = $data->r_no;
    if ($data->id_resep !== $id_resep) {
        $no++;
    }
    $id_resep = $data->id_resep;
    } ?>
</tbody>
</table>