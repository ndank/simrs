<?php $this->load->helper('functions_helper'); ?>
<div class="data-list"  style="overflow-x: scroll;">
    <h2><?= $pencarian ?></h2>
    <?php
    $key = 1;
    $total = 0;
    $total_pelayanan = 0;
    $tot = array();
    ?>
    <?php if ($hasil != null) : ?>
        <table class="list-data" width="100%">
            <tr>
                <th>Tanggal/Unit</th>
                <?php foreach ($hasil as $key => $row): ?>
                    <th><?= tampil_bulan('0' . '-' . $row->bulan . '-' . '') . " " . $row->tahun ?></th>
                 <?php $tot[$key] = 0; ; endforeach; ?>
                 <th>Total</th>
            </tr>
            <?php foreach ($semua_unit as $unit): $tot_unit = 0; ?>
                <tr class="<?= ($key++ % 2 == 1) ? 'even' : 'odd' ?>">

                    <td style="text-align: left"><?= $unit ?></td>
                    <?php foreach ($hasil_unit[$unit] as $key => $row): ?>

                        <td style="text-align: center"><?= ($row != '0') ? $row : '-' ?></td>

                    <?php  $tot_unit+=$row;$tot[$key] += $row; $total_pelayanan += $row; endforeach; ?>
                     <td align="center" class="total"><b><?= ($tot_unit !== 0)?$tot_unit:'-'; ?></b></td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td></td>
                <?php foreach ($hasil as $key => $row): ?>

                    <td style="text-align: center"><b><?= $tot[$key] ?></b></td>
                    <?php 
                        $total += $row->jumlah
                    ?>

                <?php endforeach; ?>
                <td align="center" class="total"> <?= $total_pelayanan ?></td>
            </tr>
        </table>
        <br/>
        <h2>Total Pengunjung : <?= $total ?></h2>
        <h2>Total Pelayanan Kunjungan : <?= $total_pelayanan ?></h2>
    <?php endif; ?>
</div>
<?php die ?>