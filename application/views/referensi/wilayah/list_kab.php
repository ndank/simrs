<div class="data-list">

<div id="pencarian">
    <h3>
        <?php if (isset($provinsi) && ($provinsi!="")): ?>
        Provinsi "<?= $provinsi ?>"
        <?php endif; ?>

         <?php if (isset($nama) && ($nama!="")): ?>
        <br/>Kabupaten "<?= $nama ?>"
        <?php endif; ?>

        <?php if (isset($kode) && ($kode!="")): ?>
            <br/>Kode "<?= $kode ?>"   
        <?php endif; ?>
    </h3>
</div>
<div id="resume">
    <h3>
        Halaman <?= $page ?> dari <?= (ceil($jumlah / $limit)==0)?1:ceil($jumlah / $limit)?> (Total <?= $jumlah ?> data )
    </h3>

</div>
<table cellpadding="0" cellspacing="0" class="list-data" id="tbl_kab" width="80%">
    <thead>
        <tr>
            <th width="10%">No.</th>
            <th width="40%">Nama</th>
            <th width="30%">Provinsi</th>
            <th width="10%">Kode</th>
            <th width="5%">Aksi</th>
        </tr>
    </thead>
    <tbody>
    <?php if ($kabupaten != null): ?>
        <?php foreach ($kabupaten as $key => $kab) : ?>
            <tr class="<?= ($key % 2) ? "even" : "odd" ?>" ondblclick="edit_kabupaten('<?= $kab->id ?>','<?= $kab->nama ?>','<?= $kab->provinsi_id ?>','<?= $kab->provinsi ?>','<?= $kab->kode ?>')">
                <td align="center"><?= (++$key + (($page - 1) * $limit)) ?></td>
                <td><?= $kab->nama ?></td>
                <td><?= $kab->provinsi ?></td>
                <td><?= $kab->kode ?></td>
                <td class="aksi" align="center"> 
                    <a title="Edit kabupaten" class="edition" onclick="edit_kabupaten('<?= $kab->id ?>','<?= $kab->nama ?>','<?= $kab->provinsi_id ?>','<?= $kab->provinsi ?>','<?= $kab->kode ?>')"></a>
                    <a title="Hapus kabupaten" class="deletion" onclick="delete_kabupaten('<?= $kab->id ?>')"></a>
                </td>   
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>
<br/>
<div id="paging"><?= $paging ?></div>
</div>