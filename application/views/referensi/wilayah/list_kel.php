<div class="data-list">

<div id="pencarian">
    <h3>
        <?php if (isset($kecamatan) && ($kecamatan!="")): ?>
        Kecamatan "<?= $kecamatan ?>"
        <?php endif; ?>

         <?php if (isset($nama) && ($nama!="")): ?>
        <br/>Kelurahan "<?= $nama ?>"
        <?php endif; ?>

        <?php if (isset($kode) && ($kode!="")): ?>
            <br/>Kode "<?= $kode ?>"   
        <?php endif; ?>
    </h3>
</div>
<div id="resume">
    <h3>
        Halaman <?= $page ?> dari <?= (ceil($jumlah / $limit)==0)?1:ceil($jumlah / $limit) ?> (Total <?= $jumlah ?> data )
    </h3>

</div>
<table cellpadding="0" cellspacing="0" class="list-data" width="60%">
    <thead>
        <tr>
            <th width="10%">No.</th>
            <th width="30%">Nama</th>
            <th width="20%">Kecamatan</th>
            <th width="20%">Kode Wilayah</th>
            <th width="5%">Aksi</th>
        </tr>
    </thead>
    <tbody>
    <?php if ($kelurahan != null): ?>
        <?php foreach ($kelurahan as $key => $data): ?>
            <tr class="<?php echo ($key % 2) ? "even" : "odd" ?>" ondblclick="edit_kelurahan('<?= $data->id ?>','<?= $data->nama ?>','<?= $data->kecamatan_id ?>','<?= $data->kecamatan ?>','<?= $data->kode ?>')">
                <td align="center"><?= (++$key + (($page - 1) * $limit)) ?></td>
                <td><?php echo $data->nama ?></td>
                <td><?php echo $data->kecamatan ?></td>
                <td><?= $data->kode ?></td>
                <td class="aksi" align="center"> 
                    <a title="Edit kelurahan" class="edition" onclick="edit_kelurahan('<?= $data->id ?>','<?= $data->nama ?>','<?= $data->kecamatan_id ?>','<?= $data->kecamatan ?>','<?= $data->kode ?>')"></a>
                    <a title="Hapus kelurahan" class="deletion" onclick="delete_kelurahan('<?= $data->id ?>')"></a>
                </td> 
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>
<br/>
<div id="paging"><?= $paging ?></div>
</div>