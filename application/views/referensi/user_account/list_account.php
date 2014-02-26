<div id="resume">
    <h3>
        Halaman <?= $page ?> dari <?= ceil($jumlah / $limit) ?> (Total <?= $jumlah ?> data )
    </h3>
</div>
<table class="list-data" width="100%">
    <thead>
    <tr>
        <th width="3%">No.</th>
        <th width="15%">ID / Username</th>
        <th width="30%">Nama</th>
        <th width="20%">Unit</th>
        <th width="15%">User Group</th>
        <th width="15%">Status</th>
        <th width="5%">Aksi</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($user as $key => $rows) : 
        $str = $rows->id.'#'.$rows->username.'#'.$rows->nama.'#'.$rows->unit_id.'#'.$rows->unit.'#'.$rows->status.'#'.$rows->group_id;
        ?>
        <tr class="<?= ($key % 2 == 1) ? 'even' : 'odd' ?>" ondblclick="edit_user('<?= $rows->id ?>')">
            <td align="center"><?= ++$key ?></td>
            <td><?= $rows->username ?></td>
            <td><?= $rows->nama ?></td>
            <td><?= $rows->unit ?></td>
            <td><?= $rows->user_group ?></td>
            <td><?= $rows->status ?></td>
            <td align="center" class="aksi"> 
                <a title="Ubah user account" class="edition" onclick="resetpassword('<?= $str ?>');"></a>
                <a title="Hapus user account" class="deletion" onclick="delete_user('<?= $rows->id ?>')"></a>
            </td>  
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<br/>
<div id="paging"><?= $paging ?></div>
