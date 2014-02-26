<?php $this->load->helper('functions_helper'); ?> 

<div class="data-list">
   <h2><?= $pencarian ?></h2>
    <?php
    $total = 0; $total_kasus = 0; $total_all_kasus = 0;
    $baru = 0; $lama = 0; $kasus_baru = 0; $kasus_lama = 0;
    ?>
    <?php if ($hasil != null) : ?>
        <table class="list-data" width="100%">
            <tr>
                <th width="20%">Tanggal</th>
                <th width="10%">Pasien Lama</th>
                <th width="10%">Pasien Baru</th>
                <th width="20%">Total Pengunjung</th>
                <th width="10%">Kasus Lama</th>
                <th width="10%">Kasus Baru</th>
                <th width="20%">Total Kasus</th>
            </tr>
            <?php foreach ($hasil as $key => $row): 
                $total = $total + ($pasienbaru[$key]+$pasienlama[$key]);
                $baru += $pasienbaru[$key];
                $lama += $pasienlama[$key];
                $kasus_baru += $kasus['kasus_baru'][$key];
                $kasus_lama += $kasus['kasus_lama'][$key];
                $total_kasus += ($kasus['kasus_lama'][$key]+$kasus['kasus_baru'][$key]);
                $total_all_kasus += $total_kasus;
                ?>
                <tr>
                    <td style="text-align: center"><?= tanggal_format($row->tgl_layan) ?></td>
                    <td style="text-align: center"><?= $pasienlama[$key] ?></td>
                    <td style="text-align: center"><?= $pasienbaru[$key] ?></td>
                    <td style="text-align: center" class="total"><b><?= ($pasienbaru[$key]+$pasienlama[$key]) ?></b></td>
                    <td style="text-align: center"><?= $kasus['kasus_lama'][$key] ?></td>
                    <td style="text-align: center"><?= $kasus['kasus_baru'][$key] ?></td>
                    <td style="text-align: center" class="total"><b><?= $total_kasus ?></b></td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td style="text-align: center"></td>
                <td style="text-align: center"><b><?= $lama ?></b></td>
                <td style="text-align: center"><b><?= $baru ?></b></td>
                <td style="text-align: center" class="total"><b><?= $total ?></b></td>
                <td style="text-align: center"><b><?= $kasus_lama ?></b></td>
                <td style="text-align: center"><b><?= $kasus_baru ?></b></td>
                <td style="text-align: center" class="total"><b><?= $total_all_kasus ?></b></td>
            </tr>

        </table>
    <?php endif; ?>
    <?php die ?>