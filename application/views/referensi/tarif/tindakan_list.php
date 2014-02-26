 <div id="pencarian">
    <h3>
        <?php if (isset($nama) && ($nama != '')): ?>
        Nama Layanan "<?= $nama ?>"
        <?php endif; ?>

        <?php if (isset($profesi) && ($profesi != '')): ?>
            <br/>Profesi "<?= $profesi ?>"   
        <?php endif; ?>

        <?php if (isset($jurusan) && ($jurusan != '')): ?>
            <br/>Jurusan "<?= $jurusan ?>"   
        <?php endif; ?>

        <?php if (isset($jenis_layan) && ($jenis_layan != '')): ?>
            <br/>Jenis Pelayanan Kunjungan "<?= $jenis_layan ?>"   
        <?php endif; ?>

        <?php if (isset($key) && ($key != 'Pilih unit')): ?>
            <br/>Unit "<?= $nama_unit ?>"   
        <?php endif; ?>           

        <?php if (isset($bobot) && ($bobot != '')): ?>
            <br/>Bobot "<?= $bobot ?>"   
        <?php endif; ?>

        <?php if (isset($kelas) && ($kelas != '')): ?>
            <br/>Kelas "<?= $kelas ?>"   
        <?php endif; ?>
    </h3>
</div>
<div class="resume">
    <h3>
        Halaman <?= $page ?> dari <?= (ceil($jumlah / $limit) == 0) ? 1 : ceil($jumlah / $limit) ?> (Total <?= $jumlah ?> data )
    </h3>
</div>
<table class="list-data" width="100%">
    <thead>
    <tr>
        <th width="3%">ID</th>
        <th width="37%">Nama Layanan</th>
        <th width="10%">Profesi</th>
        <th width="10%">Kualifikasi</th>
        <th width="10%">Jenis Pelayanan</th>
        <th width="5%">Bobot</th>
        <th width="5%">Kelas</th>
        <th width="15%">Nominal</th>
        <th width="5%">Aksi</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($tarif as $key => $data): ?>
        <tr class="<?= ($key % 2 == 0) ? 'odd' : 'even' ?>">
            <td align="center"><?= $data->id ?></td>
            <td><?= $data->layanan ?></td>
            <td><?= $data->profesi ?></td>
            <td><?= $data->jurusan ?></td>
            <td align="center"><?= $data->jenis_pelayanan_kunjungan ?></td>
            <td align="center"><?= $data->bobot ?></td>
            <td align="center"><?= $data->kelas ?></td>
            <td align="right"><?= rupiah($data->nominal) ?></td>
            <td class="aksi">
                <a title="Edit tarif" class="edition" onclick="edit_tindakan('<?= $data->id ?>')"></a>
                <a title="Hapus tarif" class="deletion" onclick="delete_tindakan('<?= $data->id ?>')"></a>
            </td>   
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<br/>
<div id="paging"><?= $paging ?></div><br/><br/>