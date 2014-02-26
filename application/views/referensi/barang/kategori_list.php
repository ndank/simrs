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
    <?php if(isset($nama) & isset($jenis)):?>
        <div id="pencarian">
            <h3>
                <?php 
                    if (($nama != '')|($jenis!= '')) {
                        echo "Pencarian dengan ";
                    }
                ?>
               <?= ($nama != '')?'nama kategori "'. $nama .'"':''?> <?= ($jenis != '')?'Jenis "'.$jenis.'"':'' ?>
            </h3>
        </div>
    <?php endif; ?>

    <?php if (isset($page)):?>
     <div id="resume">
        <h3>
            Halaman <?= $page ?> dari <?= (ceil($jumlah / $limit) == 0) ? 1 : ceil($jumlah / $limit) ?> (Total <?= $jumlah ?> data )
        </h3>
    </div>
    <?php endif; ?>
    <table class="tabel" width="100%">
        <thead>
            <tr>
                <th width="8%">ID</th>
                <th>Nama</th>
                <th width="25%">Jenis</th>
                <th width="10%">#</th>
            </tr>
        </thead>
        <tbody>
        <?php if (isset($list_data)&&($list_data != null)) {
            $total = 0;
            foreach ($list_data as $key => $data) { ?>
            <tr class="<?= ($key%2==1)?'even':'odd' ?>" ondblclick="edit_kategori('<?= $data->id ?>','<?= $data->nama ?>','<?= $data->jenis ?>')">
                <td align="center"><?= $data->id ?></td>
                <td><?= $data->nama ?></td>
                <td align="center"><?= $data->jenis ?></td>
                <td class="aksi">
                    <a title="Edit kategori" class="edit" onclick="edit_kategori('<?= $data->id ?>','<?= $data->nama ?>','<?= $data->jenis ?>')"></a>
                    <a title="Hapus kategori" class="delete" onclick="delete_kategori('<?= $data->id ?>')"></a>
                </td>
            </tr>
        <?php } ?>
        
        <?php
        } else { ?>
        <?php for($i = 0; $i <= 1; $i++)  { ?>
            <tr class="<?= ($i%2==1)?'even':'odd' ?>">
                <td>&nbsp;</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        <?php } 
        }?>
        </tbody>
    </table>
    <br/>
    <?= $paging ?>
    
</div>