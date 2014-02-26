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
                Nama "<?= $nama ?>"
            <?php endif; ?>
        </h3>
    </div>
    <div id="resume">
        <h3>
            Halaman <?= $page ?> dari <?= (ceil($jumlah / $limit) == 0) ? 1 : ceil($jumlah / $limit) ?> (Total <?= $jumlah ?> data )
        </h3>
    </div>
    <table cellpadding="0" cellspacing="0" class="list-data" width="40%">
        <thead>
            <tr>
                <th width="10%">No.</th>
                <th width="80%">Nama</th>
                <th width="10%">Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php
        //$asuransi = asuransi_produk_muat_data();
        if (count($pendidikan) == 0) {
            for ($key = 1; $key <= 2; $key++) {
                ?>
                <tr class="<?= ($key % 2) ? "even" : "odd" ?>">
                    <td align="center">&nbsp;</td>
                    <td></td>
                    <td class="aksi">
                    </td>
                </tr>    
                <?php
            }
        } else {
            foreach ($pendidikan as $key => $prov) {
                ?>
                <tr class="<?= ($key % 2) ? "even" : "odd" ?>" ondblclick="edit_pendidikan('<?= $prov->id ?>','<?= $prov->nama ?>')">
                    <td align="center"><?= (++$key + (($page - 1) * $limit)) ?></td>
                    <td><?= $prov->nama ?></td>
                    <td class="aksi" align="center">
                        <a title="Edit kualifikasi pendidikan" class="edition" onclick="edit_pendidikan('<?= $prov->id ?>','<?= $prov->nama ?>')"></a>
                        <a title="Hapus kualifikasi pendidikan" class="deletion" onclick="delete_pendidikan('<?= $prov->id ?>')"></a>
                    </td>
                </tr>
                <?php
            }
        }
        ?>
        </tbody>
    </table>
    <br/>
    <div id="paging"><?= $paging ?></div>
</div>