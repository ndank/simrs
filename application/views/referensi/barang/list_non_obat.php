<script type="text/javascript">
$(function() {
//    $("table").tablesorter();
//    var onSampleResized = function(e){
//            var columns = $(e.currentTarget).find("th");
//            var msg = "columns widths: ";
//            columns.each(function(){ msg += $(this).width() + "px; "; });
//    };
//    $(".tabel").colResizable({
//        liveDrag:true,
//        gripInnerHtml:"<div class='grip'></div>", 
//        draggingClass:"dragging", 
//        onResize:onSampleResized
//    });
});
</script>
<?php if (isset($key)): ?>
    <div id="pencarian">
        <h3>
            <?php if ($key!=""): ?>
            Nama Barang "<?= $key ?>"
            <?php endif; ?>

            <?php if ($kategori!=""): ?>
                <br/>kategori "<?= isset($kategori_gizi)?$kategori_gizi[$kategori]:$kategori ?>"   
            <?php endif; ?>

            <?php if ($pabrik!=""): ?>
                <br/>pabrik "<?= $pabrik ?>"   
            <?php endif; ?>
        </h3>
    </div>
<?php endif; ?>
<div id="resume">
    <h3>
        Halaman <?= $page ?> dari <?= (ceil($jumlah / $limit) == 0) ? 1 : ceil($jumlah / $limit) ?> (Total <?= $jumlah ?> data )
    </h3>
</div>
<table class="tabel" id="nonobat" width="100%">
    <thead>
    <tr>
        <th width="3%">No</th>
        <th width="20%">Nama</th>
        <th width="20%">Kategori</th>
        <th width="20%">Pabrik</th>
        <th width="5%">Aksi</th>
    </tr>
    </thead>
    <tbody>
    <?php if (count($barang) == 0) : ?>

        <?php for ($i = 1; $i <= 2; $i++) : ?>
            <tr class="<?= ($i % 2 == 1) ? 'even' : 'odd' ?>">
                <td>&nbsp;</td>
                <td></td>
                <td></td>
                <td></td>
                <td class="aksi">

                </td>
            </tr>
        <?php endfor; ?>
    <?php else: ?>
        <?php foreach ($barang as $key => $rowA): 
            if (isset($_GET['search'])) {
                $cari = $_GET['search'];
            } else if (isset($_POST['nama'])) {
                $cari = $_POST['nama'].'-'.(isset($_POST['id_pabriks'])?$_POST['id_pabriks']:'').'-';
            } else {
                $cari = NULL;
            }
            ?>
            <?php $str = $rowA->id . "#" . htmlentities($rowA->nama) . "#" . $rowA->barang_kategori_id . "#" . $rowA->id_pabrik . "#" . $rowA->pabrik ."#".$rowA->kategori; ?>
            <tr class="<?= ($key % 2 == 0) ? 'odd' : 'even' ?>" ondblclick="edit_non('<?= $str ?>')">
                <td align="center"><?= (++$auto) ?></td>
                <td><?= $rowA->nama ?></td>
                <td><?= isset($rowA->kategori) ? $rowA->kategori : '-' ?></td>
                <td><?= $rowA->pabrik ?></td>
                <td class="aksi"> 
                    <a title="Edit barang" class="edit" onclick="edit_non('<?= $str ?>')"></a>
                    <a title="Hapus barang" class="delete" onclick="delete_non('<?= $rowA->id ?>', '<?= $cari ?>')"></a>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>
<br/>
<div id="paging" class="paging"><?= $paging ?></div>