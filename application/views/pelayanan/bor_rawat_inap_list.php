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
                <th width="10%">Tanggal</th>
                <th width="15%">Bangsal</th>
                <th width="14%">VVIP</th>
                <th width="14%">VIP</th>
                <th width="14%">I</th>
                <th width="14%">II</th>
                <th width="14%">III</th>
            </tr>
        </thead>
        <tbody>
        <?php
        //$bor = asuransi_produk_muat_data();
        if (count($bor) == 0) {
            for ($key = 1; $key <= 2; $key++) {
                ?>
                <tr class="<?= ($key % 2) ? "even" : "odd" ?>">
                    <td align="center">&nbsp;</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>    
                <?php
            }
        } else {
            foreach ($bor as $key => $val) {
                ?>
                <tr class="<?= ($key % 2) ? "even" : "odd" ?>">
                    <td align="center"><?= (++$key + (($page - 1) * $limit)) ?></td>
                    <td align="center"><?= datefmysql($val->tanggal) ?></td>
                    <td><?= $val->nama ?></td>
                    <td align="center"><?= $val->hari_vvip ?> Hari (<?= round(((int)$val->hari_vvip / ($bed*1)) * 100,2 )?> %)</td>
                    <td align="center"><?= $val->hari_vip ?> Hari (<?= round(((int)$val->hari_vip / ($bed*1)) * 100,2 )?> %)</td>
                    <td align="center"><?= $val->hari_i ?> Hari (<?= round(((int)$val->hari_i / ($bed*1)) * 100,2 )?> %)</td>
                    <td align="center"><?= $val->hari_ii ?> Hari (<?= round(((int)$val->hari_ii / ($bed*1)) * 100,2 )?> %)</td>
                    <td align="center"><?= $val->hari_iii ?> Hari (<?= round(((int)$val->hari_iii / ($bed*1)) * 100,2 )?> %)</td>                 
                </tr>
                <?php
            }
        }
        ?>
        </tbody>
    </table>
    <br/>
    <div id="paging"><?= $paging ?></div>
    <br/><br/>
</div>