<table class="list-data" id="table" width="100%">
    <thead>
    <tr>
        <th width="3%">No</th>
        <th width="15%">Modul</th>
        <th width="40%">Nama Form</th>
        <th width="40%">URL</th>
        <th width="5%">Aksi</th>
    </tr>
    </thead>
    <tbody>
    <?php 
    $modul = "";
    $no = 1;
    foreach ($privilege as $key => $rows) : ?>
        <tr class="<?= ($key % 2 == 1) ? 'even' : 'odd' ?>">
            <td align="center"><?= ($modul !== $rows->modul)?$no:NULL ?></td>
            <td><?= ($modul !== $rows->modul)?'<b>'.$rows->modul.'</b>':NULL ?></a></td>
            <td><?= $rows->form_nama ?></td>
            <td><?= $rows->url ?></td>
            <td class="aksi">
                <?php
                $check = false;
                $check = in_array($rows->id, $user_priv);
                echo form_checkbox('data[]', $rows->id, $check,'class=check');
                ?>
            </td>
        </tr>
    <?php
    if ($modul !== $rows->modul) {
        $no++;
    }
    $modul = $rows->modul;
    endforeach; ?>
    </tbody>
</table>

</div>
<?= form_close() ?>