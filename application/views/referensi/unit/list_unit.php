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
<table cellpadding="0" cellspacing="0" class="list-data" width="40%">
    <thead>
        <tr>
            <th width="9%">ID</th>
            <th width="80%">Unit</th>
            <th width="11%">Aksi</th>
        </tr>
    </thead>
    <tbody>
    <?php if ($unit != null): ?>
        <?php foreach ($unit as $key => $prov) : ?>
            <tr class="<?= ($key % 2) ? "even" : "odd" ?>" ondblclick="edit_unit(<?= $prov->id ?>,'<?= $prov->nama ?>')">
                <td align="center"><?= (++$key + (($page - 1) * $limit)) ?></td>
                <td><?= $prov->nama ?></td>
                <td class="aksi" align="center">
                    <a title="Edit unit" class="edition" onclick="edit_unit(<?= $prov->id ?>,'<?= $prov->nama ?>')"></a>
                    <a title="Hapus unit" class="deletion" onclick="delete_unit(<?= $prov->id ?>)"></a>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>
<br/>
    <div id="paging"><?= $paging ?></div>
