<table cellspacing="0" width="100%" class="list-data">
<thead>
<tr class="italic">
    <th width="3%">No.</th>
    <th width="40%">Nama Barang</th>
    <th width="30%">Nama Distributor</th>
    <th width="10%">Stok Min.</th>
    <th width="10%">Sisa</th>
    <th width="2%">#</th>
</tr>
</thead>
<tbody>
    <?php
    foreach ($list_data as $key => $data) { 
        $rows = $this->m_inventory->get_distributor_by_barang($data->id_barang);
        ?>
    <tr class="<?= ($key%2==0)?'even':'odd' ?>">
        <td align="center"><?= ($auto++) ?></td>
        <td><?= $data->nama.' '.$data->kekuatan.' '.$data->satuan_kekuatan ?></td>
        <td><?= isset($rows->nama)?$rows->nama:NULL ?></td>
        <td align="center"><?= $data->stok_minimal ?></td>
        <td align="center"><?= $data->sisa ?></td>
        <td class='aksi' align='center'>
            <a class='planning' onclick="add_to_planning('<?= $data->id_barang ?>','<?= $page ?>','<?= $data->nama.' '.$data->kekuatan.' '.$data->satuan_kekuatan ?>');" title="Klik untuk entri ke rencana pemesanan">&nbsp;</a>
        </td>
        <!--<td class='aksi' align='center'>
            <a class='edition' onclick="edit_stokopname('<?= $str ?>');" title="Klik untuk edit defecta">&nbsp;</a>
            <a class='deletion' onclick="delete_stokopname('<?= $data->id ?>', '<?= $page ?>');" title="Klik untuk hapus">&nbsp;</a>
        </td>-->
    </tr>
    <?php } ?>
</tbody>
</table>
<?= $paging ?><br/><br/>