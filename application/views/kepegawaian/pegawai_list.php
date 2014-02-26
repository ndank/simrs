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
    <?php
//    if (isset($gender) || isset($fromdate) || isset($todate) || isset($jenjang) || isset($jurusan)) {
//        echo "<h3>";
//        if ((isset($fromdate)&isset($todate))&&($fromdate != '' & $todate != '')) {
//            echo 'Tanggal Masuk antara "' . datefmysql($fromdate) . '" s.d "' . datefmysql($todate) . '"  <br/>';
//        }
//
//        if ($jenjang != '') {
//            echo 'Kualifikasi pendidikan "' . $pendidikan[$jenjang] . '" ';
//        }
//
//         if ( $nm_jurusan != '') {
//            echo '  "' . $nm_jurusan . '" <br/>';
//        }
//
//        if ($gender != '') {
//            echo 'Jenis kelamin "' . $gender . '" <br/>';
//        }
//
//        echo "</h3>";
//    }
    ?>

</h3>
<div id="resume">
    <h3>
        Halaman <?= $page ?> dari <?= (ceil($jumlah / $limit) == 0) ? 1 : ceil($jumlah / $limit) ?> (Total <?= $jumlah ?> data )
    </h3>
</div>
<table cellpadding="0" cellspacing="0" class="list-data" width="100%">
    <thead>
        <tr>
            <th width="10%">Tanggal</th>
            <th width="65%">Nama</th>
            <th width="15%">Jenjang Pendidikan</th>
            <th width="20%">Jabatan</th>
            <th width="3%">Aksi</th>
        </tr>
    </thead>
    <tbody>
    <?php
//$asuransi = asuransi_produk_muat_data();
    if (count($pegawai) == 0) {
        for ($key = 1; $key <= 2; $key++) {
            ?>
            <tr class="<?= ($key % 2) ? "even" : "odd" ?>">
                <td align="center">&nbsp;</td>
                <td></td>
                <td></td>
                <td></td>
                <td class="aksi">

                </td>
            </tr>    
            <?php
        }
    } else {
        foreach ($pegawai as $key => $prov) {
            $str = $prov->id
                    . "#" . $prov->nip
                    . "#" . datetimetomysql($prov->waktu)
                    . "#" . $prov->nama
                    . "#" . $prov->gender
                    . "#" . $prov->id_kualifikasi
                    . "#" . $prov->id_jurusan
                    . "#" . $prov->jurusan
                    . "#" . $prov->jabatan;

            if ($prov->gender == 'Pria') {
                $str .= "#" . $prov->jumlah_kebutuhan_per_jenjang_pendidikan_pria;
            } else {
                $str .= "#" . $prov->jumlah_kebutuhan_per_jenjang_pendidikan_wanita;
            }

            $str .= "#".$prov->id_penduduk;
            ?>
            <tr class="<?= ($key % 2) ? "even" : "odd" ?>" ondblclick="edit_pegawai('<?= $str ?>')">
                <td align="center"><?= datetimefmysql($prov->waktu) ?></td>
                <td><?= $prov->nama ?></td>
                <td align="center"><?= $prov->kualifikasi ?></td>
                <td><?= $prov->jabatan ?></td>
                <td class="aksi">
                    <a title="Edit pegawai" class="edition" onclick="edit_pegawai('<?= $str ?>')"></a>
                    <a title="Hapus pegawai" class="deletion" onclick="delete_pegawai('<?= $prov->id ?>')"></a>
                </td>
            </tr>
            <?php
        }
    }
    ?>
    </tbody>
</table>

<div id="paging"><?= $paging ?></div>
<br/><br/>
</div>