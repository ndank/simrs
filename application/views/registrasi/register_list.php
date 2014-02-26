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
        <br/>
        <h3>
            Halaman <?= $page ?> dari <?= (ceil($jumlah / $limit) == 0) ? 1 : ceil($jumlah / $limit) ?> (Total <?= $jumlah ?> data )
        </h3>
    </div>
    <table cellpadding="0" cellspacing="0" class="tabel" width="100%">
        <thead>
            <tr>
                <th width="5%">No.</th>
                <th width="8%">Tahun</th>
                <th width="12%">Tanggal Reg.</th>
                <th width="13%">Jenis</th>
                <th width="8%">Kelas</th>
                <th width="17%">Sifat</th>
                <th width="10%">Masa Berlaku</th>
                <th>Rekap Laporan</th>
                <th width="10%">Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php
        //$asuransi = asuransi_produk_muat_data();
        if (count($register) == 0) {
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
                    <td></td>
                </tr>    
                <?php
            }
        } else {
            foreach ($register as $key => $prov) {
                $date = new Datetime($prov->waktu);

              ?>
                <tr class="<?= ($key % 2) ? "even" : "odd" ?>">
                    <td align="center"><?= $prov->id ?></td>
                    <td align="center"><?= $date->format('Y') ?></td>
                    <td align="center"><?= datetimetomysql($prov->waktu) ?></td>
                    <td align="center"><?= $prov->jenis ?></td>
                    <td align="center"><?= $prov->kelas ?></td>
                    <td align="center"><?= $prov->sifat_penetapan ?></td>
                    <td align="center"><?= datefmysql($prov->tanggal_batas_masa_berlaku) ?></td>
                    <td align="center"><span class="link_button" onclick="cetak_rl('<?= $date->format('Y') ?>')">Cetak RL 1.1</span></td>
                    <td class="aksi">
                        <a title="Edit Registrasi RS" class="edit" onclick="edit_register('<?= $prov->id ?>')"></a>
                        <a title="Hapus Registrasi RS" class="delete" onclick="delete_register('<?= $prov->id ?>')"></a>
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