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
    <table cellpadding="0" cellspacing="0" class="tabel" width="75%">
        <thead>
            <tr>
                <th width="10%">No.</th>
                <th>Nama</th>
                <th width="40%">Keterangan</th>
                <th width="15%">Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php
        //$asuransi = asuransi_produk_muat_data();
                if (count($jenis) == 0) {
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
            foreach ($jenis as $key => $prov) {
                ?>
                <tr class="<?= ($key % 2) ? "even" : "odd" ?>" ondblclick="edit_jenis('<?= $prov->id ?>','<?= $prov->nama ?>','<?= $prov->keterangan ?>')">
                    <td align="center"><?= (++$key + (($page - 1) * $limit)) ?></td>
                    <td><?= $prov->nama ?></td>
                    <td><?= $prov->keterangan ?></td>
                    <td class="aksi">
                        <a title="Edit jenis RS" class="edit" onclick="edit_jenis('<?= $prov->id ?>','<?= $prov->nama ?>','<?= $prov->keterangan ?>')"></a>
                        <a title="Hapus jenis RS" class="delete" onclick="delete_jenis('<?= $prov->id ?>')"></a>
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