<div class="data-list">
    <center><b><?= $sub_title ?></b></center>
    <?php if ($hasil != null): ?>
        <table class="list-data" width="100%">
            <thead>
                <tr>
                    <th width="3%">No.</th>
                    <th width="13%">Waktu Masuk</th>
                    <th width="7%">Jenis</th>                    
                    <th width="5%">No. RM</th> 
                    <th width="17%">Nama Pasien</th>
                    <th width="25%">Alamat</th>
                    <th width="10%">Waktu Keluar</th>
                    <th width="15%">Info Ranap</th>
                    <th width="5%">Status</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($hasil as $key => $row): ?>
                <tr class="<?= ($key % 2 == 0) ? 'even' : 'odd' ?>">
                    <td align="center">
                    <?= $row->no_daftar ?>
                    </td>
                    <td align="center">
                        <?= ($row->arrive_time != null)?datetimefmysql($row->arrive_time, true):'<span class="status status-warning">Belum Dikonfirmasi</span>' ?>
                    </td>                    
                    <td><?= $row->jenis_pelayanan ?></td>
                    <td align="center"><?= $row->no_rm ?></td>
                    <td><?= $row->nama ?></td>
                    <td><?= $row->alamat ?></td>  
                    <td align="center"><?= ($row->waktu_keluar != "")?datetimefmysql($row->waktu_keluar, true):'-' ?></td>
                    <td><?= $row->ranap ?></td>
                    <td align="center" class="aksi">
                         <span class="link_button" onclick="pemeriksaan('<?= $row->id_pk ?>','<?= $row->jenis ?>')">
                             <?= (($row->jenis !== 'Rawat Inap')?'<i title="Klik untuk pemeriksaan rawat inap">Periksa</i>':'<span title="Pasien sudah diperiksa, klik untuk edit pemeriksaan">&#10004;</span>') ?>
                         </span>
                    </td>
               
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <div id="resume">
        <h3>
            Halaman <?= $page ?> dari <?= (ceil($jumlah / $limit) == 0) ? 1 : ceil($jumlah / $limit) ?> (Total <?= $jumlah ?> data )
        </h3>
    </div>
        
    <div id="paging"><?= $paging ?></div>
    <br/><br/>
    <?php else: ?>
         <table class="list-data" width="100%">
            <tr>
                 <th width="10%">No. Kunjungan</th>
                <th width="10%">Masuk Waktu</th>
                <th width="10%">Jenis</th>                    
                <th width="10%">No. RM</th> 
                <th width="20%">Nama Pasien</th>
                <th>Umur</th>
                <th>Waktu Keluar</th>
            </tr>
            <tr class="odd">
                <td align="center">&nbsp;</td>
                <td></td>
                <td align="center"></td>
                <td></td>
                <td align="center"></td> 
                <td></td>  
                <td align="center"></td>  
            </tr>
            <tr class="odd">
                <td align="center">&nbsp;</td>
                <td></td>
                <td align="center"></td>
                <td></td>
                <td align="center"></td> 
                <td></td>  
                <td align="center"></td>  
            </tr>
        </table>
    <?php endif; ?>
</div>