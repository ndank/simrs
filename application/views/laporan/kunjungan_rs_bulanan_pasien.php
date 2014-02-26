<?php $this->load->helper('functions_helper'); ?>
<div class="data-list">
    <h2><?= $pencarian ?></h2>

    <?php
    $total = 0; $total_kasus = 0; $total_all_kasus = 0;
    foreach ($hasil as $key => $row) {
        $jumlah[$key] = $row->jumlah;
        $total += $row->jumlah;
    }
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
                $total_kasus += ($kasus['kasus_lama'][$key]+$kasus['kasus_baru'][$key]);
                $total_all_kasus += $total_kasus;
                ?>
                <tr class="<?= ($key % 2===0) ? "even" : "odd" ?>">
                    <td style="text-align: center"><?= tampil_bulan('0' . '-' . $row->bulan . '-' . '') . " " . $row->tahun ?></td>
                    <td style="text-align: center"><?= $pasienlama[$key] ?></td>
                    <td style="text-align: center"><?= $pasienbaru[$key] ?></td>
                    <td style="text-align: center" class="total"><b><?= $jumlah[$key] ?></b></td>
                    <td style="text-align: center"><?= $kasus['kasus_lama'][$key] ?></td>
                    <td style="text-align: center"><?= $kasus['kasus_baru'][$key] ?></td>
                    <td style="text-align: center" class="total"><b><?= $total_kasus ?></b></td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="3" style="text-align: center"><b>Total</b></td>
                <td style="text-align: center" class="total"><b><?= $total ?></b></td>
                <td colspan="2"></td>
                <td style="text-align: center" class="total"><b><?= $total_all_kasus ?></b></td>
            </tr>

        </table>
    <?php endif; ?>
</div>
<?php die ?>