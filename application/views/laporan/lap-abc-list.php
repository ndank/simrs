<div class="data-list">
    <table width="100%" class="tabel">
        <thead>
            <tr>
                <th>No.</th>
                <th>Kemasan Barang</th>
                <th>Jumlah</th>
                <th>Harga</th>
                <th>Total Nilai</th>
                <th>%</th>
                <th>% Kumulatif</th>
                <th>Gol.</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $total = 0;
            foreach ($list_data as $key => $rows) {
                $total = $total+$rows->total_nilai;
            }
            $persen = 0;
            foreach ($list_data as $key => $data) { 
            $persen = $persen+(($data->total_nilai/$total)*100);
            
            if($persen >= 0 and $persen <= 80) {
                $gol = "A";
            }
            else if ($persen > 80 and $persen <= 95) {
                $gol = "B";
            } else {
                $gol = "C";
            }
                ?>
            <tr>
                <td align="center"><?= ++$key ?></td>
                <td><?= $data->nama_barang ?></td>
                <td align="center"><?= $data->jumlah_pemakaian ?></td>
                <td align="right"><?= rupiah($data->hja) ?></td>
                <td align="right"><?= rupiah($data->total_nilai_pemakaian) ?></td>
                <td><?= ($data->total_nilai/$total)*100 ?></td>
                <td><?= $persen ?></td>
                <td align="center"><?= $gol ?></td>
            </tr>
            <?php 
            
            } ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" align="right">TOTAL</td>
                <td align="right"><?= rupiah($total) ?></td>
                <td align="center"><?= $persen ?></td>
                <td align="right"></td>
                <td align="right"></td>
            </tr>
        </tfoot>
    </table>
</div>