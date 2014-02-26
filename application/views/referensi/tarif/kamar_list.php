<script type="text/javascript">
    $("table").tablesorter();
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
</script>
<div class="data-list">
 <div id="pencarian">
    <h3>
        <?php if (isset($key) && ($key != 'Pilih unit')): ?>
            <br/>Unit "<?= $key ?>"   
        <?php endif; ?>

        <?php if (isset($kelas) && ($kelas != '')): ?>
            <br/>Kelas "<?= $kelas ?>"   
        <?php endif; ?>           
    </h3>
</div>
<div id="resume">
    <h3>
        Halaman <?= $page ?> dari <?= (ceil($jumlah / $limit) == 0) ? 1 : ceil($jumlah / $limit) ?> (Total <?= $jumlah ?> data )
    </h3>
</div>
<table class="list-data" width="100%">
    <thead>
    <tr>
        <th width="5%">ID</th>
        <th width="62%">Nama Layanan</th>
        <th width="10%">Unit</th>
        <th width="10%">Kelas</th>
        <th width="10%">Nominal</th>
        <th width="3%">Aksi</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($tarif as $key => $data): ?>
        <tr class="<?= ($key % 2 == 0) ? 'odd' : 'even' ?>">
            <td align="center"><?= $data->id ?></td>
            <td><?= $data->layanan ?></td>
            <td><?= $data->unit ?></td>
            <td><?= $data->kelas ?></td>
            <td align="right"><?= rupiah($data->nominal) ?></td>
            <td class="aksi" align="center">
                <a title="Edit tarif" class="edition" onclick="edit_kamar('<?= $data->id ?>')"></a>
                <a title="Hapus tarif" class="deletion" onclick="delete_kamar('<?= $data->id ?>')"></a>
            </td>   
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<br/>
<div id="paging"><?= $paging ?></div>
</div>