<table cellspacing="0" width="100%" class="list-data">
<thead>
    <tr class="italic">
        <th width="5%">No.</th>
        <th width="5%">Tanggal</th>
        <th width="15%">Saksi Apotek</th>
        <th width="15%">Saksi BPOM</th>
        <th width="25%">Nama Barang</th>
        <th width="5%">Kemasan</th>
        <th width="5%">ED</th>
        <th width="5%">Jumlah</th>
        <th width="1%">#</th>
    </tr>
</thead>
<tbody>
    <?php
    foreach ($list_data as $key => $data) { ?>
        <tr class="<?= ($key%2==0)?'even':'odd' ?>">
            <td align="center"><?= ($sp !== $data->id)?($auto++):NULL ?></td>
            <td align="center"><?= ($sp !== $data->id)?datetimefmysql($data->tanggal):NULL ?></td>
            <td><?= ($sp !== $data->id)?$data->apoteker:NULL ?></td>
            <td><?= ($sp !== $data->id)?$data->saksi_bpom:NULL ?></td>
            <td><?= $data->nama_barang ?></td>
            <td align="center"><?= $data->kemasan ?></td>
            <td align="center"><?= datefmysql($data->ed) ?></td>
            <td align="center"><?= $data->jumlah ?></td>
            <td class='aksi' align='center'>
                <!--<a class='edition' onclick="edit_pemusnahan('<?= $str ?>');" title="Klik untuk edit pemusnahan">&nbsp;</a>-->
                <?php
                if ($sp !== $data->id) { ?>
                    <a class='deletion' onclick="delete_pemusnahan('<?= $data->id ?>','<?= $page ?>');" title="Klik untuk hapus pemusnahan">&nbsp;</a>
                <?php } ?>
            </td>
        </tr>
    <?php 
    if ($sp !== $data->id) {
        $no++;
    }
    $sp = $data->id;
    }
    ?>
</tbody>
</table>