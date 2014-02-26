<script type="text/javascript">
    $("table").tablesorter();
</script>
    <div id="pencarian">
        <h3>
            <?php if (isset($nama) && $nama != ''): ?>
            Nama "<?= $nama ?>"<br/>
            <?php endif; ?>

            <?php if (isset($alamat) && $alamat != ''): ?>
            alamat "<?= $alamat ?>"<br/> 
            <?php endif; ?>

            <?php if (isset($telp) && $telp != ''): ?>
            telepon "<?= $telp ?>"<br/> 
            <?php endif; ?>

            <?php if (isset($kabupaten) && $kabupaten != ''): ?>
            kabupaten "<?= $kabupaten ?>"<br/>
            <?php endif; ?>

            <?php if (isset($gender) && $gender != ''): ?>
            jenis kelamin "<?= ($gender == 'L') ? 'Laki - laki' : 'Perempuan' ?>"<br/>
            <?php endif; ?>

            <?php if (isset($gol_darah) && $gol_darah != ''): ?>
            golongan darah "<?= $gol_darah ?>"<br/>
            <?php endif; ?>

            <?php if (isset($tgl_lahir) && $tgl_lahir != ''): ?>
            tanggal lahir "<?= $tgl_lahir ?>"<br/>
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
            <th width="5%">No.</th>
            <th width="10%">No. Identitas</th>
            <th width="25%">Nama</th>
            <th width="40%">Alamat</th>
            <th width="15%">No. Telp</th>
            <th width="5%">Aksi</th>
        </tr>
    </thead>
    <tbody>
    <?php if (count($penduduk) == 0) : ?>

        <?php
        for ($i = 1; $i <= 2; $i++) :
            ?>
            <tr class="<?= ($i % 2 == 1) ? 'even' : 'odd' ?>">
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td></td>
                <td></td>
                <td></td>
                <td class="aksi">

                </td>
            </tr>
        <?php endfor; ?>
    <?php else: ?>
        <?php foreach ($penduduk as $key => $rows): ?>
            <?php
            $str = $rows->penduduk_id
                    . "#" . $rows->nama
                    . "#" . str_replace("\n", " ",$rows->alamat)
                    . "#" . $rows->telp
                    . "#" . $rows->lahir_kabupaten_id
                    . "#" . $rows->kabupaten
                    . "#" . $rows->gender
                    . "#" . $rows->darah_gol
                    . "#" . $rows->lahir_tanggal
                    . "#" . $rows->id_dp;
            ?>
            <tr class="tr_row <?= ($key % 2 == 1) ? 'even' : 'odd' ?>" ondblclick="edit_penduduk('<?= $str ?>')">
                <td align="center"><?= (++$key + (($page - 1) * $limit)) ?></td>
                <td align="center"><?= ($rows->identitas_no =='')?'-':$rows->identitas_no ?></td>
                <td><?= $rows->nama ?></td>
                <td><?= !empty($rows->alamat) ? $rows->alamat : '-' ?></td>
                <td><?= $rows->telp ?></td>
                <td align="center">
                    <a title="Edit penduduk" class="edition" onclick="edit_penduduk('<?= $str ?>')"></a>
                    <a title="Hapus penduduk" class="deletion" onclick="delete_penduduk('<?= $rows->penduduk_id ?>')"></a>
                </td>   
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>

<div id="paging"><?= $paging ?></div>
<br/><br/>