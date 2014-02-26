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
            <?php if (isset($id_perusahaan) && ($id_perusahaan != "")): ?>
            Nama Perusahaan "<?= $perusahaan ?>"
            <?php endif; ?>

             <?php if (isset($nama) && ($nama != "")): ?>
            <br/>Nama Produk "<?= $nama ?>"
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
                <th width="5%">No.</th>
                <th width="45%">Perusahaan</th>
                <th width="46%">Nama produk</th>
                <th width="4%">Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php
        //$asuransi = asuransi_produk_muat_data();
        if (count($asuransi) == 0) {
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
            foreach ($asuransi as $key => $prov) {
                ?>
                <tr class="<?= ($key % 2) ? "even" : "odd" ?>" ondblclick="edit_produk_asuransi('<?= $prov->id ?>','<?= $prov->nama ?>','<?= $prov->id_ap ?>','<?= $prov->prsh ?>','<?= $prov->reimbursement ?>','<?= $prov->reimbursement_rupiah ?>')">
                    <td align="center"><?= (++$key + (($page - 1) * $limit)) ?></td>
                    <td><?= $prov->prsh ?></td>
                    <td><?= $prov->nama ?></td>
                    <td class="aksi">
                        <a title="Edit produk asuransi" class="edition" onclick="edit_produk_asuransi('<?= $prov->id ?>','<?= $prov->nama ?>','<?= $prov->id_ap ?>','<?= $prov->prsh ?>','<?= $prov->reimbursement ?>','<?= $prov->reimbursement_rupiah ?>')"></a>
                        <a title="Hapus produk asuransi" class="deletion" onclick="delete_produk_asuransi('<?= $prov->id ?>')"></a>
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