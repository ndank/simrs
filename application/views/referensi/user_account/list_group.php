 
<div id="resume">
    <h3>
        Halaman <?= $page ?> dari <?= ceil($jumlah / $limit) ?> (Total <?= $jumlah ?> data )
    </h3>
</div>
<table class="list-data" width="50%">
    <thead>
    <tr>
        <th width="8%">No.</th>
        <th width="77%">Nama</th>
        <th width="10%">Aksi</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($user as $key => $rows) : ?>
        <tr class="<?= ($key % 2 == 1) ? 'even' : 'odd' ?>" ondblclick="edit_user('<?= $rows->id ?>')">
            <td align="center"><?= (++$key + (($page - 1) * $limit)) ?></td>
            <td><?= $rows->nama ?></td>
            <td align="center" class="aksi"> 
                <a title="Ubah group privileges" class="edition" onclick="edit_privileges('<?= $rows->id ?>', '<?= $rows->nama ?>')"></a>
                <a title="Ubah user group" class="edition" onclick="edit_group('<?= $rows->id ?>', '<?= $rows->nama ?>');"></a>
                <a title="Hapus user group" class="deletion" onclick="delete_group('<?= $rows->id ?>')"></a>
            </td>  
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<br/>
<div id="paging"><?= $paging ?></div>
