<?php
header_excel("Rekap Profil Pasien -".indo_tgl($awal)." s.d ".indo_tgl($akhir).".xls");

?>
<style>
    * { font-size: 16px; font-family: calibri;}
    table td {font-size: 16px;}
    .list-data-excel { border-top: 1px solid #000; border-left: 1px solid #000; border-spacing: 0; border-bottom: 1px solid #000; }
    .list-data-excel th { border-bottom: 1px solid #000; border-right: 1px solid #000;  }
    .list-data-excel td { border-bottom: 1px solid #f4f4f4; border-right: 1px solid #000;  }
    .no_border{border:none; }
</style>

<?php $buf_resep = null; $run = 0; $run_resep = false; ?>
<table width="100%" cellpadding="0" cellspacing="0">
    <tr style="color: #ffffff" bgcolor="#31849b">
    <td rowspan="3" style="width: 70px" class="no_border"><img src="<?= base_url('assets/images/company/'.$apt->logo_file_nama) ?>" width="70px" height="70px" /></td>    
    <td colspan="16" align="center" class="no_border"><b><?= strtoupper($apt->nama) ?></b></td> </tr>
    <tr bgcolor="#31849b"><td colspan="16" align="center"><b><?= strtoupper($apt->alamat) ?> <?= strtoupper($apt->kelurahan)  ?> <?= strtoupper($apt->kecamatan) ?> <?= strtoupper($apt->kabupaten) ?></b></td> </tr>
    <tr bgcolor="#31849b"><td colspan="16" align="center" style="padding-right: 70px"><b>TELP. <?= $apt->telp ?>,  FAX. <?= $apt->fax ?>, EMAIL <?= $apt->email ?></b></td> </tr>
</table>
<table class="list-data-excel" width="100%">
    <tr align="center">
        <td colspan="17">
            <b>
            Rekap Profil Pasien<br/>
            <?= indo_tgl($awal) ?> s.d <?= indo_tgl($akhir) ?>
            </b>
        </td>
    </tr>
    <tr bgcolor="#bdb76b">
        <th rowspan="2">No</th>
        <th rowspan="2">Tanggal</th>
        <th rowspan="2">No. RM</th>
        <th rowspan="2">Nama Pasien</th>
        <th rowspan="2">Status Pembiayaan Pasien</th>
        <th rowspan="2">Diagnosis ICD X</th>
        <th rowspan="2">ICD IX - CM</th>
        <th colspan="3">INA-CBGs</th>
        <th rowspan="2">LOS RS<br/>(Hari)</th>
        <th colspan="5">Rekap Biaya Perbekalan Farmasi</th>
        <th rowspan="2">Biaya Riil RS</th>        
    </tr>
    <tr bgcolor="#bdb76b">
        <th>Level Severity</th>
        <th>LOS<br/>(Hari)</th>
        <th>Biaya</th>
        <th>Nama Obat</th>
        <th>BHP</th>
        <th>Jumlah</th>
        <th>Dosis</th>
        <th>Harga</th>
    </tr>
    <?php if($profil != null): ?>    

    <?php foreach ($profil as $key => $data): ?>
    <?php ($key%2==0)?$bgcolor='#ffffb0':$bgcolor='#ffffff'; ?>
    <?php awal: ?>
    <?php if($run_resep): ?>
        <?php if(is_array($buf_resep)): ?>
        <?php foreach ($buf_resep as $key => $resep): ?>
            <tr bgcolor="<?= $bgcolor ?>">
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td><?= $resep->barang ?></td>
                <td></td>
                <td><?= $resep->resep_r_jumlah ?></td>
                <td><?= $resep->dosis_racik ?></td>
                <td align="right"><?= $resep->jual_harga ?></td>

                <td></td>
            </tr>
        <?php endforeach; $run_resep = false;?>
        
        <?php endif; ?>

    <?php else: ?>

        <tr bgcolor="<?= $bgcolor ?>">
            <td align="center" valign="top"><?= ++$key ?></td>
            <td align="center" valign="top"><?= datefmysql($data->tgl_layan)?></td>
            <td align="center" valign="top"><?= $data->no_rm ?></td>
            <td valign="top"><?= $data->nama ?></td>
            <td valign="top">
                <table width="100%">
                    <?php if(is_array($data->asuransi)): ?>
                        <?php foreach ($data->asuransi as $key => $asu): ?>                        
                                <tr><td style="border:none;"><?= $asu ?></td></tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </table>
            </td>
            <td valign="top">
                <table width="100%">
                    <?php if(is_array($data->diagnosis)): ?>
                        <?php foreach ($data->diagnosis as $key => $diag): ?>                        
                                <tr><td style="border:none;"><?= $diag->no_daftar_terperinci ." : ". $diag->golongan_sebab?></td></tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </table>
            </td>
            <td valign="top">
                <table width="100%">
                    <?php if(is_array($data->tindakan)): ?>
                        <?php foreach ($data->tindakan as $key => $tindak): ?>                        
                                <tr><td style="border:none;"><?= $tindak->kode_icdixcm ." : ". $tindak->tindakan ?></td></tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </table>
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td><?= $data->los ?></td>
             <?php if(is_array($data->resep) & (sizeof($data->resep) > 0) ): ?>
                <?php $buf_resep = $data->resep; ?>
                <?php foreach ($data->resep as $key => $r): ?>

                    <?php if($key == 0): ?>
                        <td><?= $r->barang ?></td>
                        <td><?= '' ?></td>
                        <td><?= $r->resep_r_jumlah ?></td>
                        <td><?= $r->dosis_racik ?></td>
                        <td align="right"><?= $r->jual_harga ?></td>
                        <td align="right"><?= $data->total_biaya ?></td>
                    <?php else: ?>
                    <?php $run_resep = true; goto awal; ?>

                    <?php endif; ?>

                <?php endforeach; ?>
            <?php else: ?>
             <td></td>
             <td></td>
             <td></td>
             <td></td>
             <td></td>
             <td align="right"><?= $data->total_biaya ?></td>
            <?php endif; ?>
        </tr>
    <?php endif; ?>
   
    <?php endforeach; ?>
    <?php endif; ?>


</table>

</td>
    </tr>
</table>
