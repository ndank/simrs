<script type="text/javascript">
$(function() {
    $("table").tablesorter();
    var onSampleResized = function(e){
            var columns = $(e.currentTarget).find("th");
            var msg = "columns widths: ";
            columns.each(function(){ msg += $(this).width() + "px; "; });
    };
});
</script>
    <div id="pencarian">
        <h3>
            <?php if (isset($nama)): ?>
            Nama Obat "<?= $nama ?>"
            <?php endif; ?>

            <?php if (isset($kekuatan)): ?>
                <br/>Kekuatan "<?= $kekuatan ?>"   
            <?php endif; ?>

            <?php if (isset($satuan)): ?>
                <br/>Satuan "<?= $satuan_list[$satuan] ?>"   
            <?php endif; ?>

            <?php if (isset($sediaan)): ?>
                <br/>Macam Sediaan "<?= $sediaan_list[$sediaan] ?>"   
            <?php endif; ?>

            <?php if (isset($ven)): ?>
                <br/>Ven "<?= $ven ?>"   
            <?php endif; ?>

            <?php if (isset($ha)): ?>
                <br/>High Alert "<?= $ha ?>"   
            <?php endif; ?>

            <?php if (isset($perundangan)): ?>
                <br/>Perundangan "<?= $perundangan ?>"   
            <?php endif; ?>

            <?php if (isset($generik)): ?>
                <br/>Generik "<?= $generik ?>"   
            <?php endif; ?>

            <?php if (isset($formularium)): ?>
                <br/>Formularium "<?= $formularium ?>"   
            <?php endif; ?>

            <?php if (isset($pabrik_obat)): ?>
                <br/>Pabrik "<?= $pabrik_obat ?>"   
            <?php endif; ?>
        </h3>
    </div>
<div id="resume">
    <h3>
        Halaman <?= $page ?> dari <?= (ceil($jumlah / $limit) == 0) ? 1 : ceil($jumlah / $limit) ?> (Total <?= $jumlah ?> data )
    </h3>
</div>
<div  class="data-list">
<table class="tabel" width="100%">
    <thead>
    <tr>
        <th width="3%">No</th>
        <th width="20%">Nama</th>
        <th width="3%">Kekuatan</th>
        <th width="3%">Satuan</th>
        <th width="5%">Macam Sediaan</th>
        <th width="20%">Pabrik</th>
        <th width="5%">VEN</th>
        <th width="5%">High Alert</th>
        <th width="7%">Perundangan</th>
        <th width="10%">Generik</th>
        <th width="5%">Form</th>
        <th width="5%">Konsi</th>
        <th width="3%">Aksi</th>
    </tr>
    </thead>
    <tbody>
    <?php if (count($barang) == 0) : ?>

        <?php for ($i = 1; $i <= 2; $i++) : ?>
            <tr class="<?= ($i % 2 == 1) ? 'even' : 'odd' ?>">
                <td>&nbsp;</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="aksi">

                </td>
            </tr>
        <?php endfor; ?>
    <?php else: ?>
        <?php foreach ($barang as $key => $rows): 
            $alert = "";
            if ($rows->high_alert == 'Ya') {
                $alert = "style='color: red;'";
            }
            if (isset($_GET['search'])) {
                $cari = $_GET['search'];
            } else if (isset($_POST['nama'])) {
                $cari = $_POST['nama'].'-'.$_POST['id_pabrik_obat'];
            } else {
                $cari = NULL;
            }
            ?>

            <?php
            $str = $rows->id . "#" . $rows->nama
                    . "#" . $rows->id_pabrik . "#" . $rows->pabrik
                    . "#" . $rows->kekuatan . "#" . $rows->satuan_id
                    . "#" . $rows->sediaan_id . "#" . $rows->ven . "#" . $rows->high_alert . "#" . $rows->adm_r
                    . "#" . $rows->perundangan . "#" . $rows->generik . "#" . $rows->formularium. "#" . $rows->kandungan
                    . "#" . $rows->aturan_pakai. "#" . $rows->efek_samping . "#" . $rows->konsinyasi;
            ?>

            <tr class="<?= ($key % 2 == 0) ? 'odd' : 'even' ?>" <?= $alert ?> ondblclick="edit_obat('<?= $str ?>')">
                <td align="center"><?= (++$auto) ?></td>
                <td><?= $rows->nama ?></td>
                <td><?= ($rows->kekuatan != '1')?$rows->kekuatan:NULL ?></td>
                <td align="center"><?= $rows->satuan ?></td>
                <td><?= $rows->sediaan ?></td>
                <td><?= $rows->pabrik ?></td>
                <td align="center"><?= $rows->ven ?></td>
                <td align="center"><?= $rows->high_alert ?></td>
                <td align="center"><?= $rows->perundangan ?></td>
                <td align="center"><?= $rows->generik ?></td>
                <td align="center"><?= $rows->formularium ?></td>
                <td align="center"><?= $rows->konsinyasi ?></td>
                <td class="aksi"> 
                    <a title="Edit barang" class="edit" onclick="edit_obat('<?= $str ?>')"></a>
                    <a title="Hapus barang" class="delete" onclick="delete_obat('<?= $rows->id ?>', '<?= $cari ?>')"></a>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>
</div>
<br/>
<div id="paging"><?= $paging ?></div>
<br/>
<div>
   <table>
        <tr>
            <td><b>Total Item : </b></td>
            <td><b><?= $jumlah ?> item</b></td>
        </tr>
    </table>
</div>