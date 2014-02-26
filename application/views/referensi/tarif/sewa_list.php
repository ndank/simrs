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
<div id="resume">
    <h3>
        Halaman <?= $page ?> dari <?= (ceil($jumlah / $limit) == 0) ? 1 : ceil($jumlah / $limit) ?> (Total <?= $jumlah ?> data )
    </h3>
</div>
<table class="list-data" width="100%">
    <thead>
        <tr>
            <th width="10%">ID</th>
            <th>Nama Layanan</th>
            <th>Nominal</th>
            <th width="10%">Aksi</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($tarif as $key => $data): ?>
        <tr class="<?= ($key % 2 == 0) ? 'odd' : 'even' ?>">
            <td align="center"><?= $data->id ?></td>
            <td><?= $data->barang.' '.$data->jenis_pelayanan_kunjungan.' '.$data->unit.' '.$data->kelas ?></td>
            <td align="right"><?= rupiah($data->nominal) ?></td>            
            <td class="aksi">
                <a title="Edit tarif" class="edit" onclick="edit_sewa('<?= $data->id ?>')"></a>
                <a title="Hapus tarif" class="delete" onclick="delete_sewa('<?= $data->id ?>')"></a>
            </td>   
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<br/>
<div id="paging"><?= $paging ?></div>
</div>