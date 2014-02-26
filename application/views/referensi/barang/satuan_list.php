<div class="data-list">
    <?php if(isset($nama)):?>
        <div id="pencarian">
            <h3>
                <?php 
                    if (($nama != '')) {
                        echo "Pencarian dengan ";
                    }
                ?>
               <?= ($nama != '')?'nama satuan "'. $nama .'"':''?>
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

    <table class="list-data" width="50%">
        <thead>
            <tr>
                <th width="10%">No.</th>
                <th width="80%">Nama</th>
                <th width="10%">#</th>
            </tr>
        </thead>
        <tbody>
        <?php if (isset($list_data)&&($list_data != null)) {
            $total = 0;
            foreach ($list_data as $key => $data) { ?>
            <tr class="<?= ($key%2==1)?'even':'odd' ?>" ondblclick="edit_satuan('<?= $data->id ?>','<?= $data->nama ?>')">
                <td align="center"><?= (++$key + (($page - 1) * $limit)) ?></td>
                <td><?= $data->nama ?></td>
                <td class="aksi" align="center">
                    <a title="Edit sediaan" class="edition" onclick="edit_satuan('<?= $data->id ?>','<?= $data->nama ?>')"></a>
                    <a title="Hapus sediaan" class="deletion" onclick="delete_satuan('<?= $data->id ?>')"></a>
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
            </tr>
        <?php } 
        }?>
        </tbody>
    </table>
    
    <?= $paging ?>
    <br/><br/>
</div>