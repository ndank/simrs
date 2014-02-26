<?php
if ($total_list_tindakan > 0) {
foreach ($tind_list as $key => $data) { ?>
    <tr class="row_diag <?= ($key%2==0)?'even':'odd' ?>">
        <td align="center"><?= datetime($data->waktu) ?></td>
        <td><?= $data->operator ?></td>
        <td><?= $data->anestesi ?></td> 
        <td><?= $data->unit ?></td>
        <td><?= $data->tindakan ?></td>
        <td class="aksi" align="center"><a class="deletion" onClick="delete_tindakan('<?= $data->id ?>', this);"></a></td>
    </tr>
<?php } 
} else { ?>
    <script>
        //add_tindak(0);
    </script>
<?php } ?>

