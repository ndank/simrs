<table cellspacing="0" width="100%" class="list-data">
<thead>
    <tr class="italic">
        <th width="3%">No.</th>
        <th width="5%">Tanggal</th>
        <th width="5%">Faktur</th>
        <th width="15%">Nama Supplier</th>
        <th width="3%">PPN</th>
        <th width="4%">Materai</th>
        <th width="5%">Tempo</th>
        <th width="3%">Disc (%)</th>
        <th width="3%">Disc Rp.</th>
        <th width="5%">Total RP.</th>
        <th width="3%">Secara</th>
        <th width="15%">Nama Barang</th>
        <th width="5%">Jumlah</th>
        <th width="5%">ED</th>
        <th width="5%">No. Batch</th>
        <th width="5%">Harga RP.</th>
        <th width="3%">Diskon<br/> (%)</th>
        
        <th width="2%">#</th>
    </tr>
</thead>
<tbody>
    <?php
    $id = "";
    $no = 1;
    foreach ($list_data as $key => $data) { 
        $str = $data->id.'#'.$data->faktur.'#'.datefmysql($data->tanggal).'#'.$data->id_supplier.'#'.$data->supplier.'#'.
               $data->id_pemesanan.'#'.$data->ppn.'#'.$data->materai.'#'.$data->jatuh_tempo.'#'.$data->diskon_persen.'#'.
               $data->diskon_rupiah.'#'.$data->total.'#'.$data->status.'#'.datefmysql($data->jatuh_tempo);
        ?>
        <tr class="<?= ($key%2==0)?'even':'odd' ?>">
            <td align="center"><?= ($id !== $data->id)?($no):NULL ?></td>
            <td align="center"><?= ($id !== $data->id)?datefmysql($data->tanggal):NULL ?></td>
            <td align="center"><?= ($id !== $data->id)?$data->faktur:NULL ?></td>
            <td><?= ($id !== $data->id)?$data->supplier:NULL ?></td>
            <td align="center"><?= ($id !== $data->id)?$data->ppn:NULL ?></td>
            <td align="center"><?= ($id !== $data->id)?rupiah($data->materai):NULL ?></td>
            <td align="center"><?= ($id !== $data->id)?datefmysql($data->jatuh_tempo):NULL ?></td>
            <td align="center"><?= ($id !== $data->id)?$data->diskon_persen:NULL ?></td>
            <td align="right"><?= ($id !== $data->id)?rupiah($data->diskon_rupiah):NULL ?></td>
            <td align="right"><?= ($id !== $data->id)?rupiah($data->total):NULL ?></td>
            <td align="center"><?= ($id !== $data->id)?$data->status:NULL ?></td>
            <td><?= $data->nama_barang ?></td>
            <td align="center"><?= $data->jumlah ?></td>
            <td align="center"><?= datefmysql($data->expired) ?></td>
            <td align="center"><?= $data->nobatch ?></td>
            <td align="right"><?= rupiah($data->harga) ?></td>
            <td align="center"><?= $data->disc_pr ?></td>
            <td class='aksi' align='center'>
                <?php
                if ($id !== $data->id) { ?>
                    <a class='edition' onclick="edit_penerimaan('<?= $str ?>');" title="Klik untuk edit penerimaan">&nbsp;</a>
                    <a class='deletion' onclick="delete_penerimaan('<?= $data->id ?>','<?= $page ?>');" title="Klik untuk hapus penerimaan">&nbsp;</a>
                <?php } ?>
            </td>
        </tr>
    <?php 
    if ($id !== $data->id) {
        $no++;
    }
    $id = $data->id;
    }
    ?>
</tbody>
</table>
<?= $paging ?><br/><br/>