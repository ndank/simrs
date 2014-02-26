<table cellspacing="0" width="100%" class="list-data">
<thead>
    <tr class="italic">
        <th width="3%">No.</th>
        <th width="10%">No. SP</th>
        <th width="5%">Tanggal</th>
        <th width="20%">Nama Supplier</th>
        <th width="15%">Karyawan</th>
        <th width="20%">Nama Barang</th>
        <th width="5%">Kemasan</th>
        <th width="5%">Jumlah</th>
        <th width="2%">#</th>
    </tr>
</thead>
<tbody>
    <?php
    
    $no = 1;
    $sp = "";
    foreach ($list_data as $key => $data) { 
        
        ?>
        <tr class="<?= ($key%2==0)?'even':'odd' ?>">
            <td align="center"><?= ($sp !== $data->id)?($no):NULL ?></td>
            <td><?= ($sp !== $data->id)?$data->id:NULL ?></td>
            <td align="center"><?= ($sp !== $data->id)?datetimefmysql($data->tanggal):NULL ?></td>
            <td><?= ($sp !== $data->id)?$data->supplier:NULL ?></td>
            <td><?= ($sp !== $data->id)?$data->karyawan:NULL ?></td>
            <td><?= $data->nama_barang ?></td>
            <td align="center"><?= $data->kemasan ?></td>
            <td align="center"><?= $data->jumlah ?></td>
            <td class='aksi' align='center'>
                <!--<a class='edition' onclick="edit_pemesanan('<?= $str ?>');" title="Klik untuk edit pemesanan">&nbsp;</a>-->
                <?php
                if ($sp !== $data->id) { ?>
                    <a class='printing' onclick="cetak_sp('<?= $data->id ?>','<?= $data->perundangan ?>');" title="Klik untuk cetak SP">&nbsp;</a>
                    <a class='deletion' onclick="delete_pemesanan('<?= $data->id ?>','<?= $page ?>');" title="Klik untuk hapus pemesanan">&nbsp;</a>
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