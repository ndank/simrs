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
        <?php if (isset($nama) && ($nama != '')): ?>
            <br/>Nama "<?= $nama ?>"   
        <?php endif; ?>

        <?php if (isset($nm_jenis) && ($nm_jenis != '')): ?>
            <br/>Jenis "<?= $nm_jenis ?>"   
        <?php endif; ?>           
    </h3>
</div>

    <div id="resume">
        <h3>
            Halaman <?= $page ?> dari <?= (ceil($jumlah / $limit) == 0) ? 1 : ceil($jumlah / $limit) ?> (Total <?= $jumlah ?> data )
        </h3>
    </div>
    <table cellpadding="0" cellspacing="0" class="list-data" width="100%">
        <thead>
            <tr>
                <th width="3%">No.</th>
                <th>Nama</th>
                <th width="15%">Titel</th>
                <th width="30%">Jenis</th>
                <th width="10%">Admission</th>
                <th width="3%">Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php
        //$asuransi = asuransi_produk_muat_data();
        if (count($jurusan) == 0) {
            for ($key = 1; $key <= 2; $key++) {
                ?>
                <tr class="<?= ($key % 2) ? "even" : "odd" ?>">
                    <td align="center">&nbsp;</td>
                    <td></td>
                    <td></td>
                    <td class="aksi">

                    </td>
                </tr>    
                <?php
            }
        } else {
            foreach ($jurusan as $key => $prov) {
                ?>
                <tr class="<?= ($key % 2) ? "even" : "odd" ?>" ondblclick="edit_jurusan('<?= $prov->id ?>','<?= $prov->nama ?>','<?= $prov->id_jenis ?>','<?= $prov->jenis ?>')">
                    <td align="center"><?= (++$key + (($page - 1) * $limit)) ?></td>
                    <td><?= $prov->nama ?></td>
                    <td><?= $prov->titel ?></td>
                    <td><?= $prov->jenis ?></td>
                    <td><?= $prov->admission ?></td>
                    <td class="aksi">
                        <a title="Edit jurusan" class="edition" onclick="edit_jurusan('<?= $prov->id ?>','<?= $prov->nama ?>','<?= $prov->titel ?>','<?= $prov->id_jenis ?>','<?= $prov->jenis ?>','<?= $prov->admission ?>')"></a>
                        <a title="Hapus jurusan" class="deletion" onclick="delete_jurusan('<?= $prov->id ?>')"></a>
                    </td>
                </tr>
                <?php
            }
        }
        ?>
        </tbody>
    </table>
    <div id="paging"><?= $paging ?></div>
    <br/><br/>
</div>