<div class="data-list">
    <div id="resume">
        <h3>
            Halaman <?= $page ?> dari <?= (ceil($jumlah / $limit) == 0) ? 1 : ceil($jumlah / $limit) ?> (Total <?= $jumlah ?> data )
        </h3>
    </div>
    <table class="list-data" width="100%">
        <thead>
            <tr>
                <th align="center" width="3%">No.</th>
                <th align="center" width="50%">Bangsal</th>
                <th align="center" width="15%">Kelas</th>
                <th align="center" width="10%">Nomor</th>
                <th align="center" width="15%">Status</th>
                <th align="center" width="3%">Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($hasil != null): ?>
            <?php foreach ($hasil as $key => $val): ?>
                <tr class="<?= ($key % 2) ? "even" : "odd" ?>">
                    <td align="center"><?= (++$key + (($page - 1) * $limit)) ?></td>
                    <td><?= $val->nama ?></td>
                    <td align="center"><?= $val->kelas ?></td>
                    <td align="center"><?= $val->nomor ?></td>
                    <td align="center">
                        <?php 
                            if($val->status === 'Tersedia'){
                                echo '<span class="status status-success">Tersedia</span>';
                            }else{
                                echo '<span class="status status-warning">Sudah terisi</span>';
                            }
                        ?>
                    </td>
                    <td class="aksi" align="center">
                        <?php
                        $str = $val->id
                                . "#" . $val->unit_id
                                . "#" . $val->kelas
                                . "#" . $val->nomor;
                        ?>
                        <a title="Edit tempat tidur" class="edition" onclick="edit_bed('<?= $str ?>')"></a>
                        <a title="Hapus tempat tidur" class="deletion" onclick="delete_bed('<?= $val->id ?>')"></a>
                    </td>   
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
    <br/>
    <div id="paging"><?= $paging ?></div>
    <br/><br/>
</div>
