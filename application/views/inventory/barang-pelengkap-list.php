<table cellspacing="0" width="100%" class="list-data">
<thead>
<tr class="italic">
    <th width="5%">No.</th>
    <th width="20%">Nama Barang</th>
    <th width="15%">Indikasi</th>
    <th width="10%">Dosis</th>
    <th width="20%">Kandungan</th>
    <th width="10%">Perhatian</th>
    <th width="10%">Kontra Indikasi</th>
    <th width="10%">Efek Samping</th>
    <!--<th width="4%">#</th>-->
</tr>
</thead>
<tbody>
    <?php
    foreach ($list_data as $key => $data) {
        ?>
    <tr class="<?= ($key%2==0)?'even':'odd' ?>">
        <td align="center"><?= ($auto++) ?></td>
        <td><?= $data->nama.' '.$data->kekuatan.' '.$data->satuan ?></td>
        <td><?= $data->indikasi ?></td>
        <td><?= $data->dosis ?></td>
        <td><?= $data->kandungan ?></td>
        <td><?= $data->perhatian ?></td>
        <td><?= $data->kontra_indikasi ?></td>
        <td><?= $data->efek_samping ?></td>
<!--        <td class='aksi' align='center'>
            <a class='edition' onclick="edit_barang('<?= $str ?>');" title="Klik untuk edit barang">&nbsp;</a>
            <a class='deletion' onclick="delete_barang('<?= $data->id ?>','<?= $page ?>');" title="Klik untuk hapus barang">&nbsp;</a>
        </td>-->
    </tr>
    <?php } ?>
</tbody>
</table>
<?= $paging ?>
<br/><br/>