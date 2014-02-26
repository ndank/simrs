<script type="text/javascript">
    $("table").tablesorter();
    var onSampleResized = function(e){
            var columns = $(e.currentTarget).find("th");
            var msg = "columns widths: ";
            columns.each(function(){ msg += $(this).width() + "px; "; });
    };
    $(".list-data").colResizable({
        liveDrag:true,
        gripInnerHtml:"<div class='grip'></div>", 
        draggingClass:"dragging", 
        onResize:onSampleResized
    });
</script>
<div class="data-list">
    <div id="pencarian">
      
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
                <th>Tarif</th>
                <th width="15%">Jenis Pelayanan</th>
                <th width="20%">Kode Debet</th>
                <th width="20%">Kode Kredit</th>
                <th width="6%">Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php
        //$asuransi = asuransi_produk_muat_data();
        if (count($rekening) == 0) {
            for ($key = 1; $key <= 2; $key++) {
                ?>
                <tr class="<?= ($key % 2) ? "even" : "odd" ?>">
                    <td align="center">&nbsp;</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="aksi">

                    </td>
                </tr>    
                <?php
            }
        } else {
            foreach ($rekening as $key => $val) {
                ?>
                <tr class="<?= ($key % 2) ? "even" : "odd" ?>" ondblclick="edit_kode_rekening('<?= $val->id ?>')">
                    <td align="center"><?= (++$key + (($page - 1) * $limit)) ?></td>
                    <td><?= $val->tarif." ".$val->profesi." ".$val->jurusan." ".$val->jenis_pelayanan_kunjungan." ".$val->unit." ".$val->bobot." ".$val->kelas ?></td>
                    <td><?= $val->jenis_pelayanan ?></td>
                    <td><?= $val->debet ?></td>
                    <td><?= $val->kredit ?></td>
                    <td class="aksi">
                        <a title="Edit kode rekening" class="edit" onclick="edit_kode_rekening('<?= $val->id_kode ?>')"></a>
                        <a title="Hapus kode rekening" class="delete" onclick="delete_kode_rekening('<?= $val->id_kode ?>')"></a>
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