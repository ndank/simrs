
<script type="text/javascript">
$(function() {
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
});
</script>
<div id="resume">
    <div id="pencarian">
        <h3>
            <?php if (isset($nama) && ($nama != "")): ?>
            Nama "<?= $nama ?>"
            <?php endif; ?>

             <?php if (isset($icd) && ($icd != "")): ?>
            <br/>ICD IX CM "<?= $icd ?>"
            <?php endif; ?>

            <?php if (isset($id_sub_sub) && ($id_sub_sub != "")): ?>
                <br/>Jenis Layanan "<?= $jenis ?>"   
            <?php endif; ?>
        </h3>
    </div>
    <h3>
        Halaman <?= $page ?> dari <?= (ceil($jumlah / $limit) == 0) ? 1 : ceil($jumlah / $limit) ?> (Total <?= $jumlah ?> data )
    </h3>
</div>
<table class="list-data" width="100%">
    <thead>
    <tr>
        <th width="3%">No.</th>
        <th width="40%">Nama</th>
        <th width="5%">Kode</th>
        <th width="40%">Jenis Layanan</th>
        <th width="5%">Aksi</th>
    </tr>
    </thead>
    <tbody>
    <?php if (count($layanan) == 0) : ?>

        <?php for ($i = 1; $i <= 2; $i++) : ?>
            <tr class="<?= ($i % 2 == 1) ? 'even' : 'odd' ?>">
                <td>&nbsp;</td>
                <td></td>
                <td></td>
                <td></td>
                <td class="aksi"></td>
            </tr>
        <?php endfor; ?>
    <?php else: ?>
        <?php foreach ($layanan as $key => $data): 
            if (isset($_GET['nama'])) {
                $cari = $_GET['nama'];
            }
            else if (isset($_GET['key'])) {
                $cari = $_GET['key'];
            } else {
                $cari = NULL;
            }
            ?>
            <?php
            $str = $data->id
                    . "#" . $data->nama
                    . "#" . $data->kode_icdixcm
                    ."#".$data->id_sub_sub_jenis_layanan
                    ."#".$data->sub_jenis;
            ?>
            <tr class="<?= ($key % 2 == 0) ? 'odd' : 'even' ?>" ondblclick="edit_layanan('<?= $str ?>')">
                <td align="center"><?= (++$key + (($page - 1) * $limit)) ?></td>
                <td><?= $data->nama ?></td>
                <td align="center"><?= ($data->kode_icdixcm=='NULL')?'':$data->kode_icdixcm ?></td>
                <td><?= $data->jenis." ".$data->sub_jenis." ".$data->sub_sub_jenis ?></td>
                <td align="center">
                    <a title="Edit layanan" class="edition" onclick="edit_layanan('<?= $str ?>')"></a>
                    <a title="Hapus layanan" class="deletion" onclick="delete_layanan('<?= $data->id ?>', '<?= $cari ?>')"></a>
                </td>   
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>
<div id="paging"><?= $paging ?></div>
<br/>
<br/>