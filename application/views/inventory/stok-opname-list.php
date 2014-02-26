<table cellspacing="0" width="100%" class="list-data">
<thead>
<tr class="italic">
    <th width="5%">No.</th>
    <th width="20%">Nama Barang</th>
    <th width="10%">No. Batch</th>
    <th width="10%">ED</th>
    <th width="10%">Masuk</th>
    <th width="10%">Keluar</th>
    <th width="10%">Sisa</th>
    <!--<th width="4%">#</th>-->
</tr>
</thead>
<tbody>
    <?php
    foreach ($list_data as $key => $data) { 
        $str = "";
        ?>
    <tr class="<?= ($key%2==0)?'even':'odd' ?>">
        <td align="center"><?= ($auto++) ?></td>
        <td><?= $data->nama.' '.$data->kekuatan.' '.$data->satuan_kekuatan ?></td>
        <td align="center"><?= $data->nobatch ?></td>
        <td align="center"><?= datefmysql($data->ed) ?></td>
        <td align="center"><?= $data->masuk ?></td>
        <td align="center"><?= $data->keluar ?></td>
        <td align="center"><?= $data->sisa ?></td>
        <!--<td class='aksi' align='center'>
            <a class='edition' onclick="edit_stokopname('<?= $str ?>');" title="Klik untuk edit stok_opname">&nbsp;</a>
            <a class='deletion' onclick="delete_stokopname('<?= $data->id ?>', '<?= $page ?>');" title="Klik untuk hapus">&nbsp;</a>
        </td>-->
    </tr>
    <?php } ?>
</tbody>
</table>