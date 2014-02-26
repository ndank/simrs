<script type="text/javascript">
$(function() {
    $(".tabel").tablesorter();
    var onSampleResized = function(e){
            var columns = $(e.currentTarget).find("th");
            var msg = "columns widths: ";
            columns.each(function(){ msg += $(this).width() + "px; "; });

    };
    $(".tabel").colResizable({
        liveDrag:true,
        gripInnerHtml:"<div class='grip'></div>", 
        draggingClass:"dragging", 
        onResize:onSampleResized
    });
});
</script>
    <div id="pencarian">
        <h3>
            <?php if (isset($barcode)): ?>
                Barcode "<?= $barcode ?>"
            <?php endif; ?>

            <?php if (isset($id_barang)): ?>
                <br/>Barang "<?= $barang ?>"   
            <?php endif; ?>

             <?php if (isset($kemasan)): ?>
                <br/>Kemasan "<?= $sediaan_list[$kemasan] ?>"   
            <?php endif; ?>

            <?php if (isset($isi)): ?>
                <br/>Isi "<?= $isi ?>"   
            <?php endif; ?>

            <?php if (isset($satuan)): ?>
                <br/>Satuan "<?= $satuan_list[$satuan] ?>"   
            <?php endif; ?>
        </h3>
    </div>

<div id="resume">
    <h3>
        Halaman <?= $page ?> dari <?= (ceil($jumlah / $limit) == 0) ? 1 : ceil($jumlah / $limit) ?> (Total <?= $jumlah ?> data )
    </h3>
</div>
<table class="tabel" width="100%">
    <thead>
    <tr>
        <th width="45%">Barang</th>
        <th width="10%">Barcode</th>
        <th width="15%">Satuan Terbesar</th>
        <th width="10%">Isi @</th>
        <th width="10%">Satuan</th>
        <th width="10%">Aksi</th>
    </tr>
    </thead>
    <tbody>
    <?php if (count($packing) == 0) : ?>

        <?php
        for ($i = 1; $i <= 2; $i++) :
            ?>
            <tr class="<?= ($i % 2 == 1) ? 'even' : 'odd' ?>">
                <td>&nbsp;</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="aksi"></td>
            </tr>
        <?php endfor; ?>
    <?php else: ?>
        <?php 
        $id = "";
        foreach ($packing as $key => $rows): 
            if (isset($_POST['barang'])) {
                $cari = $_POST['barang'];
            } else if (isset($_GET['search'])) {
                $cari = $_GET['search'];
            } else {
                $cari = NULL;
            }
            ?>
            <?php
            $str = $rows->id
                    . "#" . $rows->barcode
                    . "#" . $rows->barang_id
                    . "#" . $rows->nama." ".$rows->kekuatan." ".$rows->satuan_obat." ".$rows->sediaan_obat." ".$rows->pabrik." "
                    . "#" . $rows->terbesar_satuan_id
                    . "#" . $rows->isi
                    . "#" . $rows->terkecil_satuan_id;
            ?>
            <tr class="<?= ($key % 2 == 0) ? 'odd' : 'even' ?>" ondblclick="edit_packing('<?= $str ?>')">
                <td><?= ($id !== $rows->id_barang)?($rows->nama.' '.((($rows->kekuatan == '0')|$rows->kekuatan == '1') ? '' : $rows->kekuatan).' '.$rows->satuan_obat.' '.$rows->sediaan.' '.(($rows->generik !== 'Non Generik')?$rows->pabrik:NULL)):NULL ?></a></td>
                <td><a id="<?= $rows->barcode ?>" class="barcode" style="cursor: pointer;" onclick="cetak_barcode('<?= $rows->barcode ?>')"><?= $rows->barcode ?></a></td>
                <td><?= $rows->s_besar ?></td>
                <td><?= $rows->isi ?></td>
                <td><?= $rows->s_kecil ?></td>
                <td class="aksi">
                    <a title="Edit kemasan barang" class="edit" onclick="edit_packing('<?= $str ?>')"></a>
                    <a title="Hapus kemasan barang" class="delete" onclick="delete_packing('<?= $rows->id ?>', '<?= $cari ?>')"></a>
                </td>   

            </tr>
        <?php 
        $id = $rows->id_barang;
        endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>
<br/>
<div id="paging"><?= $paging ?></div>