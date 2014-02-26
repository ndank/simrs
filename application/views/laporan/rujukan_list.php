<div class="data-list">
    <div>
        <center>
            <h1>Laporan Rujukan</h1>
            <h1>
                <?php
                if (isset($from)) {
                    echo datefmysql($from) . " s.d " . datefmysql($to);
                } else {
                    echo 'Semua Data';
                }
                ?>
            </h1>
        </center>
    </div>

    <div id="resume">
        <br/>
        <h3>
            Halaman <?= $page ?> dari <?= (ceil($jumlah / $limit) == 0) ? 1 : ceil($jumlah / $limit) ?> (Total <?= $jumlah ?> data )
        </h3>
    </div>
    <table class="tabel" width="100%">
        <tr>
            <th width="10%">No.</th>
            <th width="15%">Tanggal Pendaftaran</th>
            <th width="25%">Nama Instansi Perujuk</th>
            <th width="25%">Nama Tenaga Kesehatan</th>
            <th width="25%">Nama Pasien</th>
        </tr>
        <?php if ($hasil != null): ?>
            <?php foreach ($hasil as $key => $rows): ?>
                <tr class="<?= ($key % 2 == 1) ? 'even' : 'odd' ?>">
                    <td align="center"><?= (++$key + (($page - 1) * $limit)) ?></td>
                    <td><?= datetimefmysql($rows->tgl_daftar) ?></td>
                    <td><?= $rows->nama_instansi ?></td>
                    <td><?= $rows->nama_nakes ?></td>
                    <td><?= $rows->nama_pasien ?></td>   
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>
    <br/>
    <div id="paging"><?= $paging ?></div>
</div>