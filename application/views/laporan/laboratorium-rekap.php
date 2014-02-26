<div class="data-list">
    <table class="tabel" width="100%">
        <thead>
            <tr>
                <th width="5%">No.</th>
                <th width="10%">Waktu Order</th>
                <th width="20%">Nama Dokter</th>
                <th width="20%">Nama Analis Lab.</th>
                <th width="5%">No. RM</th>
                <th width="20%">Nama Pasien</th>
                <th width="10%">#</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($list_data as $key => $data) { ?>
            <tr>
                <td align="center"><?= ++$key ?></td>
                <td align="center"><?= datetimefmysql($data->waktu_order) ?></td>
                <td><?= $data->dokter ?></td>
                <td><?= $data->analis ?></td>
                <td align="center"><?= $data->no_rm ?></td>
                <td><?= $data->pasien ?></td>
                <td align="center" style="white-space: nowrap;">
                    <span style="border: none;" onclick="form_add('<?= $data->id ?>')">Entri Hasil</span> | <span onclick="delete_this('<?= $data->id ?>')" style="border: none;">Hapus</span>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>