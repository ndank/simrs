
<div class="data-list">
<div id="resume">
    <h3>
        Halaman <?= $page ?> dari <?= (ceil($jumlah / $limit) == 0) ? 1 : ceil($jumlah / $limit) ?> (Total <?= $jumlah ?> data )
    </h3>
</div>
<table class="list-data" width="100%">
    <tr>
        <th width="3%">ID</th>
        <th width="70%">Nama Layanan</th>
        <th width="10%">Total</th>
        <th width="10%">Profit (%)</th>
        <th width="5%">Aksi</th>
    </tr>
    <?php foreach ($tarif as $key => $data): ?>
        <tr class="<?= ($key % 2 == 0) ? 'odd' : 'even' ?>">
            <td align="center"><?= $data->id ?></td>
            <td><?= $data->layanan ?></td>
            <td><?= rupiah($data->total) ?></td>
            <td><?= $data->persentase_profit ?></td>            
            <td class="aksi">
                <a title="Edit tarif" class="edit" onclick="edit_tindakan('<?= $data->id ?>')"></a>
                <a title="Hapus tarif" class="delete" onclick="delete_tindakan('<?= $data->id ?>')"></a>
            </td>   
        </tr>
    <?php endforeach; ?>
</table>
<br/>
<div id="paging"><?= $paging ?></div>
</div>