<script type="text/javascript">
    $(function() {
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
});
</script>

    <div id="pencarian">
        <h3>
            <?php if (isset($nama) && ($nama != '')): ?>
            Nama Instansi "<?= $nama ?>"
            <?php endif; ?>

            <?php if (isset($alamat) && ($alamat != '')): ?>
                <br/>Alamat "<?= $alamat ?>"   
            <?php endif; ?>

            <?php if (isset($id_kelurahan) && ($id_kelurahan != '')): ?>
                <br/>Kelurahan "<?= $kelurahan ?>"   
            <?php endif; ?>

            <?php if (isset($jenis) && ($jenis != '')): ?>
                <br/>Jenis "<?= $jenis_list[$jenis] ?>"   
            <?php endif; ?>
        </h3>
    </div>
<div id="resume">
    <h3>
        Halaman <?= $page ?> dari <?= (ceil($jumlah / $limit) == 0) ? 1 : ceil($jumlah / $limit) ?> (Total <?= $jumlah ?> data )
    </h3>
</div>
<table class="list-data" width="100%">
    <thead>
    <tr>
        <th width="3%">No.</th>
        <th width="27%">Nama</th>
        <th width="47%">Alamat Jalan</th>
        <th width="13%">Kelurahan</th>
        <th width="7%">Jenis</th>
        <th width="3%">Aksi</th>
    </tr>
    </thead>
    <tbody>
    <?php if ($instansi != null): ?>
        <?php foreach ($instansi as $key => $rows): 
        if (isset($_GET['search'])) {
            $cari = $_GET['search'];
        } else if (isset($_POST['nama'])) {
            $cari = $_POST['nama'];
        } else {
            $cari = NULL;
        }    
        ?>
        <?php $str = $rows->id . "#" . $rows->nama . "#" . trim(str_replace("\n", " ",$rows->alamat)) . "#" . $rows->kelurahan_id . "#" . $rows->kelurahan . "#" . $rows->telp . "#" . $rows->fax . "#" . $rows->email . "#" . $rows->website . "#" . $rows->relasi_instansi_jenis_id . "#" . $rows->diskon_penjualan; ?>
            <tr class="<?= ($key % 2 == 1) ? 'even' : 'odd' ?>" ondblclick="edit_instansi('<?= $str ?>')">
                <td align="center"><?= $rows->nomor ?></td>
                <td><?= $rows->nama ?></td>
                <td><?= $rows->alamat ?></td>
                <td><?= $rows->kelurahan ?></td>
                <td><?= $rows->jenis ?></td>
                <td class="aksi" align="center"> 
                    <a title="Edit instansi" class="edition" onclick="edit_instansi('<?= $str ?>')"></a>
                    <a title="Hapus instansi" class="deletion" onclick="delete_instansi('<?= $rows->id ?>', '<?= $cari ?>')"></a>
                </td>   
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>

<div id="paging"><?= $paging ?></div>
<br/><br/>