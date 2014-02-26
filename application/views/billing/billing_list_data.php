<div class="data-list">
    <h3>Penjualan Barang</h3>
    <table class="list-data" width="100%">
        <tr>
            <th width="15%">No. Penjualan Barang</th>
            <th width="40%">Resep/Non</th>
            <th width="10%">Total</th>
            <th width="10%">&nbsp;</th>
            <th width="10%">&nbsp;</th>
            <th width="10%">&nbsp;</th>
        </tr>
        <?php
        $totals = 0;
        foreach ($list_data as $key => $data) {
            ?>
            <tr>
                <td align="center"><?= ++$key ?></td>
                <td align="center"><?= ($data->status == NULL) ? 'Non Resep' : 'Resep' ?></td>
                <td align="right" id="total<?= $key ?>"></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr class="odd">
                <td align="center"><b>Waktu</b></td>
                <td align="left"><b>Packing Barang</b></td>
                <td align="right"><b>Harga Jual</b></td>
                <td align="center"><b>Diskon</b></td>
                <td align="center"><b>Jumlah</b></td>
                <td align="right"><b>Subtotal</b></td>
            </tr>
            <?php
            $total = 0;
            $list_data_detail = $this->m_billing->penjualan_barang_detail_load_data($data->no_penjualan)->result();
            if (count($list_data_detail) > 0) {
                foreach ($list_data_detail as $no => $rows) {
                    $harga_jual = $rows->hna + ($rows->hna * $rows->margin / 100) - ($rows->hna * ($rows->diskon / 100));
                    $subtotal = $rows->keluar * $harga_jual;
                    ?>
                    <tr>
                        <td align="center"><?= datetime($rows->waktu) ?></td>
                        <td><?= $rows->barang ?> <?= ($rows->kekuatan == '1') ? '' : $rows->kekuatan ?> <?= $rows->satuan ?> <?= $rows->sediaan ?> <?= (($rows->generik == 'Non Generik') ? '' : $rows->pabrik) ?> <?= (($rows->isi == '1') ? '' : $rows->isi) ?> <?= $rows->satuan_terkecil ?></td>
                        <td align="right"><?= rupiah($harga_jual) ?></td>
                        <td align="center"><?= $rows->diskon ?></td>
                        <td align="center"><?= $rows->keluar ?></td>
                        <td align="right"><?= rupiah($subtotal) ?></td>
                    </tr>
                    <?php
                    $total = $total + $subtotal;
                }
            }
            ?>
            <script>
                $(function() {
                    $('#total<?= $key ?>').html(numberToCurrency(Math.ceil(<?= $total ?>)));
                })
            </script>
            <?php
            $totals = $totals + $total;
        }
        ?>
    </table>
    <br/>
    <h3>Penjualan Jasa</h3>
    <table class="tabel" width="100%">
        <tr class="even">
            <th align="center">No</th>
            <th align="center">Layanan</th>
            <th align="right">Tarif&nbsp;</th>
            <th align="center">Frequensi</th>
            <th align="right">Subtotal&nbsp;</th>
        </tr>
        <?php
        $totallica = 0;
        foreach ($jasa_list_data as $noo => $rowz) {
            ?>
            <tr class="<?= ($noo % 2 == 0) ? 'even' : 'odd' ?>">
                <td align="center"><?= ++$noo ?></td>
                <td><?= $rowz->layanan ?></td>
                <td align="right"><?= rupiah($rowz->nominal) ?></td>
                <td align="center"><?= $rowz->frekuensi ?></td>
                <td align="right"><?= rupiah($rowz->subtotal) ?></td>
            </tr>
            <?php
            $totallica = $totallica + $rowz->total;
        }
        ?>
            <tr class="even"><td></td><td colspan="3"><b>Total</b></td><td align="right"><?= rupiah($totallica) ?></td></tr>
    </table>
    <script>
        $(function() {
            //$('#total, #total-pembayaran').html(numberToCurrency(Math.ceil(<?= $totals + $totallica ?>)));
        })
    </script><br/>
    <h3>Jasa Rawat Inap </h3>
    <table class="tabel" width="100%">
        <tr>
            <th align="center">No</th>
            <th align="center">Bangsal</th>
            <th align="center">Kelas</th>
            <th align="center">No. TT</th>
            <th align="center">Durasi</th>
            <th align="right">Subtotal&nbsp;</th>
        </tr>
        <?php
        //$arrays = $this->m_billing->penjualan_jasa_detail_load_data($data->pasien_id)->result();
        $totallicas = 0;
        foreach ($rawat_inap as $nooo => $rowy) {
            ?>
            <tr class="<?= ($noo % 2 == 0) ? 'even' : 'odd' ?>">
                <td align="center"><?= ++$nooo ?></td>
                <td><?= $rowy->unit ?></td>
                <td align="center"><?= $rowy->kelas ?></td>
                <td align="center"><?= $rowy->no ?></td>
                <td align="center">
                    <?php
                    if ($rowy->keluar_waktu != null) {
                        $durasi = get_duration($rowy->masuk_waktu, $rowy->keluar_waktu);
                    } else {
                        $durasi = get_duration($rowy->masuk_waktu, date('Y-m-d H:i:s'));
                    }

                    if ($durasi['day'] == 0) {
                        $durasi['day']++;
                    } else if ($durasi['hour'] > 0) {
                        $durasi['day']++;
                    }
                    echo $durasi['day'];
                    ?>
                </td>
                <td align="right">
                    <?php
                    if ($rowy->keluar_waktu != null) {
                        $subttl = $rowy->subtotal;
                        echo rupiah($rowy->subtotal);
                    } else {
                        $subttl = ($durasi['day'] * $rowy->tarif);
                        echo rupiah($durasi['day'] * $rowy->tarif);
                    }
                    ?> 
                </td>
            </tr>
            <?php
            $totallicas = $totallicas + $subttl;
        }
        ?>
            <tr class="even"><td></td><td colspan="4"><b>Total</b></td><td align="right"><?= rupiah($totallicas) ?></td></tr>
    </table>
    <script>
        $(function() {
            $('#total, #total-pembayaran').html(numberToCurrency(Math.ceil(<?= $totals + $totallica + $totallicas ?>)));
        })
    </script>
</div>