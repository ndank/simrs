<div class="data-list">

<div id="pencarian">
    <h3>
        <?php if (isset($nama)&&($nama!="")): ?>
        Provinsi "<?= $nama ?>"
        <?php endif; ?>

        <?php if (isset($kode)&&($kode!="")): ?>
            <br/>Kode "<?= $kode ?>"   
        <?php endif; ?>
    </h3>
</div>
<div id="resume">
    <h3>
        Halaman <?= $page ?> dari <?= (ceil($jumlah / $limit)==0)?1:ceil($jumlah / $limit)?> (Total <?= $jumlah ?> data )
    </h3>

</div>
<table cellpadding="0" cellspacing="0" class="list-data" width="60%">
    <thead>
        <tr>
            <th width="10%">No.</th>
            <th width="70">Nama</th>
            <th width="15%">Kode</th>
            <th width="5%">Aksi</th>
        </tr>
    </thead>
    <tbody>
    <?php if ($provinsi != null): ?>
        <?php foreach ($provinsi as $key => $prov): ?>
            <tr class="<?= ($key % 2) ? "even" : "odd" ?>" ondblclick="edit_provinsi('<?= $prov->id ?>','<?= $prov->nama ?>','<?= $prov->kode ?>')">
                <td align="center"><?= (++$key + (($page - 1) * $limit)) ?></td>
                <td><?= $prov->nama ?></td>
                <td align="center"><?= $prov->kode ?></td>
                <td class="aksi"> 
                    <a title="Edit provinsi" class="edition" onclick="edit_provinsi('<?= $prov->id ?>','<?= $prov->nama ?>','<?= $prov->kode ?>')"></a>
                    <a title="Hapus provinsi" class="deletion" onclick="delete_provinsi('<?= $prov->id ?>')"></a>
                </td>        
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>
<br/>
<div id="paging"><?= $paging ?></div>
</div>