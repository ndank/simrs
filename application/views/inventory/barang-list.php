<table cellspacing="0" width="100%" class="list-data">
<thead>
<tr class="italic">
    <th width="5%">No.</th>
    <th width="20%">Nama Barang</th>
    <th width="15%">Pabrik</th>
    <th width="5%">Kekuatan</th>
    <th width="5%">Satuan<br/> Kekuatan</th>
    <th width="7%">Gol.</th>
    <th width="5%">Sediaan</th>
    <th width="5%">Generik</th>
    <th width="5%">Adm R</th>
    <th width="7%">Formularium</th>
    <th width="7%">Perundangan</th>
    <th width="7%">Lokasi<br/>Rak</th>
    <th width="3%">Stok<br/>Min</th>
    <th width="10%">Hna</th>
    <th width="4%">#</th>
</tr>
</thead>
<tbody>
    <?php
    foreach ($list_data as $key => $data) { 
    $str = $data->id.'#'.$data->nama.'#'.$data->kekuatan.'#'.$data->satuan_kekuatan.'#'.$data->id_sediaan.'#'. // 0 - 4
            $data->id_golongan.'#'.$data->adm_r.'#'.$data->id_pabrik.'#'.$data->pabrik.'#'. // 5 - 8
            $data->rak.'#'.$data->formularium.'#'.$data->generik.'#'.

            $data->indikasi.'#'.$data->dosis.'#'.$data->kandungan.'#'.$data->perhatian.'#'.$data->kontra_indikasi.'#'.
            $data->efek_samping.'#'.
            $data->stok_minimal.'#'.$data->margin_non_resep.'#'.$data->margin_resep.'#'.$data->plus_ppn.'#'.$data->hna.'#'.$data->aktif.'#'.
            $data->aturan_pakai.'#'.$data->id_farmakoterapi.'#'.$data->id_kelas_terapi.'#'.$data->fda_pregnancy.'#'.$data->fda_lactacy.'#'.$data->perundangan.'#'.
            $data->barcode.'#'.$data->image.'#'.$data->status.'#'.$data->range_terapi.'#'.$data->pengawasan.'#'.$data->fornas;
        ?>
    <tr class="<?= ($key%2==0)?'even':'odd' ?>">
        <td align="center"><?= $auto++ ?></td>
        <td><span title="<img src='img/barang/<?= $data->image ?>' />"><?= $data->nama.' '.$data->kekuatan.' '.$data->satuan ?></span></td>
        <td><?= $data->pabrik ?></td>
        <td align="center"><?= $data->kekuatan ?></td>
        <td align="center"><?= $data->satuan ?></td>
        <td align="center"><?= $data->golongan ?></td>
        <td><?= $data->sediaan ?></td>
        <td align="center"><?= ($data->generik === '1')?'Ya':'Tidak' ?></td>
        <td><?= $data->adm_r ?></td>
        <td align="center"><?= $data->formularium ?></td>
        <td><?= $data->perundangan ?></td>
        <td><?= $data->rak ?></td>
        <td align="center"><?= $data->stok_minimal ?></td>
        <td align="right"><?= rupiah($data->hna) ?></td>
        <td class='aksi' align='center'>
            <a class='edition' onclick="edit_barang('<?= $str ?>');" title="Klik untuk edit barang">&nbsp;</a>
            <a class='deletion' onclick="delete_barang('<?= $data->id ?>','<?= $page ?>');" title="Klik untuk hapus barang">&nbsp;</a>
        </td>
    </tr>
    <?php } ?>
</tbody>
</table>
<?= $paging ?>
<br/><br/>