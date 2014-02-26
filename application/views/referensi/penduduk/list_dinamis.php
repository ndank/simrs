<div class="data-list">
<table class="list-data" width="100%">
    <tr>
        <th width="5%">ID</th>
        <th width="15%">Tanggal</th>
        <th width="30%">Alamat</th>
        <th width="10%">Kelurahan</th>
        <th width="10%">Pekerjaan</th>
        <th width="5%">#</th>
    </tr>
    <?php foreach ($dinamis as $key => $rows) : ?>
        <tr class="<?= ($key % 2 == 1) ? 'even' : 'odd' ?>">
            <td align="center"><?= $rows->id ?></td>
            <td align="center"><?= datefmysql($rows->tanggal) ?></td>
            <td><?= $rows->alamat ?></td>
            <td><?= $rows->kelurahan ?></td>
            <td><?= $rows->pekerjaan ?></td>
            <td class="aksi">
                <a title="Edit dinamis penduduk" class="edition" onclick="edit_dinamis_penduduk('<?= $rows->penduduk_id ?>','<?= $rows->id ?>')"></a>
                <a title="Hapus dinamis penduduk" class="deletion" onclick="delete_dinamis_penduduk('<?= $rows->id ?>', this)"></a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
</div>