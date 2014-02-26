<table cellspacing="0" width="100%" class="list-data">
<thead>
    <tr class="italic">
        <th width="3%">No.</th>
        <th width="5%">Tanggal</th>
        <th width="15%">Customer</th>
        <th width="5%">Disc Rp.</th>
        <th width="5%">Disc %</th>
        <th width="5%">PPN %</th>
        <th width="5%">Tuslah <br/>RP.</th>
        <th width="5%">Embalage RP.</th>
        <th width="5%">Jasa Apt<br/>RP.</th>
        <th width="5%">Total</th>
        <th width="5%">Terbayar</th>
        <th width="20%">Nama Barang</th>
        <th width="5%">Kemasan</th>
        <th width="5%">Jumlah</th>
        <th width="5%">Harga</th>
        <th width="10%">Subtotal</th>
        <th width="5%">#</th>
    </tr>
</thead>
<tbody>
    <?php
    $id = "";
    $no = 1;
    
    foreach ($list_data as $key => $data) {
        $jasa = $this->db->query("select sum(nominal) as jasa_apoteker from resep_r where id_resep = '".$data->id_resep."'")->row();
        $str = $data->id.'#'.datetimefmysql($data->waktu).'#'.$data->id_resep.'#'.$data->customer.'#'.$data->id_customer.'#'.
               $data->diskon_persen.'#'.rupiah($data->diskon_rupiah).'#'.$data->ppn.'#'.$data->total.'#'.
               $data->tuslah.'#'.$data->embalage.'#'.$jasa->jasa_apoteker;
        
        if ($data->total > $data->terbayar) {
            $alert="warning";
        } else {
            $alert = "";
        }
        ?>
        <tr id="<?= $data->id ?>" class="detail <?= ($id !== $data->id)?'odd':NULL ?> <?= $alert ?>">
            <td align="center"><?= ($id !== $data->id)?($auto++):NULL ?></td>
            <td align="center"><?= ($id !== $data->id)?datetimefmysql($data->waktu):NULL ?></td>
            <td><?= ($id !== $data->id)?$data->customer:NULL ?></td>
            <td align="right"><?= ($id !== $data->id)?rupiah($data->diskon_rupiah):NULL ?></td>
            <td align="center"><?= ($id !== $data->id)?$data->diskon_persen:NULL ?></td>
            <td align="center"><?= ($id !== $data->id)?$data->ppn:NULL ?></td>
            <td align="right"><?= ($id !== $data->id)?rupiah($data->tuslah):NULL ?></td>
            <td align="right"><?= ($id !== $data->id)?rupiah($data->embalage):NULL ?></td>
            <td align="right"><?= ($id !== $data->id)?rupiah($jasa->jasa_apoteker):NULL ?></td>
            <td align="right"><?= ($id !== $data->id)?rupiah($data->total):NULL ?></td>
            <td align="right"><?= ($id !== $data->id)?rupiah($data->terbayar):NULL ?></td>
            <td><?= $data->nama_barang ?></td>
            <td align="center"><?= $data->kemasan ?></td>
            <td align="center"><?= $data->qty ?></td>
            <td align="right"><?= rupiah($data->harga_jual) ?></td>
            <td align="right"><?= rupiah($data->subtotal) ?></td>
            <td class='aksi' align='center'>
                <?php
                if ($id !== $data->id) { ?>
                    <a class='printing' onclick="cetak_struk('<?= $data->id ?>');" title="Klik untuk cetak struk">&nbsp;</a>
                    <a class='edition' onclick="edit_penjualan('<?= $str ?>');" title="Klik untuk edit">&nbsp;</a>
                    <a class='deletion' onclick="delete_penjualan('<?= $data->id ?>','<?= $page ?>');" title="Klik untuk hapus">&nbsp;</a>
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