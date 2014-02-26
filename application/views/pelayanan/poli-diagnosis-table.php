<?php
if ($total_list > 0) {
foreach ($diag_list as $key => $data) { ?>
    <tr class="row_diag <?= ($key%2==0)?'even':'odd' ?>">
        <td align="center"><?= datetime($data->waktu) ?></td>
        <td><?= $data->dokter ?></td>
        <td><?= $data->unit ?></td> 
        <td><?= $data->golongan ?></td>
        <td align="center"><?= $data->kode ?></td>
        <td align="center"><?= $data->kasus ?></td>
        <td class=aksi align="center"><a class="deletion" onClick="delete_diagnosis('<?= $data->id ?>', this)"></a></td>
    </tr>
<?php }
} ?>

