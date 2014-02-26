<?php $this->load->view('message'); ?>
<script>
$(function() {
    $('button').button();
})
</script>
<title><?= $title ?></title>
    <h1 class="informasi"><?= $title ?></h1>
    <?php foreach ($list_data as $rows); ?>
    <div class="data-input">
            <tr><td>No. Dokumen</td><td><span class="label"><?= $rows->dokumen_no ?></span>
            <tr><td>Tanggal</td><td><span class="label"><?= indo_tgl($rows->tanggal) ?></span>
            <tr><td>Jenis Transaksi</td><td><span class="label"><?= $rows->jenis ?></span>
    </div>
        <table class="tabel form-inputan" width="100%">
            <thead>
            <tr>
                <th width="70%">Nama Transaksi</th>
                <th width="20%">Jumlah (Rp.)</th>
            </tr>
            </thead>
            <tbody>
                <?php
                $total = 0;
                    foreach ($list_data as $key => $data) { ?>
                        <tr class="tr_row <?= ($key%2==1)?'even':'odd' ?>">
                            <td><?= $data->penerimaan_pengeluaran_nama ?></td>
                            <td align="right"><?= ($data->jenis == 'Penerimaan')?rupiah($data->penerimaan):rupiah($data->pengeluaran) ?></td>
                        </tr>
                    <?php 
                    if ($data->jenis == 'Penerimaan') {
                        $jml = $data->penerimaan;
                    } else {
                        $jml = $data->pengeluaran;
                    }
                    $total = $total+$jml;
                    }
                ?>
            </tbody>
            <tfoot>
                <tr class="odd">
                    <td align="right">Total</td>
                    <td id="total" align="right"><?= $total ?></td>
                </tr>
            </tfoot>
        </table><br/>
        <?= form_button('delete', 'Delete', 'id=deletion') ?>