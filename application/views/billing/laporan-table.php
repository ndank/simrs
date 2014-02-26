<script type="text/javascript">
$("table").tablesorter();
</script>
<div class="data-list">
    <div id="resume">
        <h3>
            Halaman <?= $page ?> dari <?= (ceil($jumlah / $limit) == 0) ? 1 : ceil($jumlah / $limit) ?> (Total <?= $jumlah ?> data )
        </h3>
    </div>
    <table class="list-data" id="table" width="100%">
        <thead>
        <tr>
            <th width="5%">No.<br/> Kunjungan</th>
            <th width="10%">Tanggal</th>
            <th width="5%">No. RM</th>
            <th width="20%">Nama Pasien</th>
            <th width="30%">Alamat Jalan</th>
            <th width="7%">Total <br/> Tagihan</th>
            <th width="7%">No.<br/> Pembayaran</th>
            <th width="7%">Jumlah<br/> Bayar</th>
            <th width="10%">Sisa</th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($list_data as $key => $data) {
            $tb = $this->m_billing->data_kunjungan_muat_data_total_barang($data->no_daftar);
            $tj = $this->m_billing->data_kunjungan_muat_data_total_jasa($data->no_daftar);
            ?>
        <tr class="<?= ($key%2==0)?'even':'odd' ?>">
            <td align="center"><span class="link_button" onclick="pembayaran(<?= $data->no_daftar ?>)"><?= $data->no_daftar ?></span></td>
            <td align="center"><?= datetimefmysql($data->tgl_daftar) ?></td>
            <td align="center"><?= ($data->no_rm != '-')?str_pad($data->no_rm, 6,"0",STR_PAD_LEFT):$data->no_rm ?></td>
            <td><?= $data->pasien ?></td>
            <td><?= $data->alamat ?></td>
            <td align="right"><?= rupiah($tj->total_jasa+$tb->total_barang) ?></td>
            <td align="center"><?= $data->no_pembayaran ?></td>
            <td align="right"><?= ($data->bayar != null)?rupiah($data->bayar):rupiah(0) ?></td>
            <td align="right"><?= ($data->sisa == NULL)?rupiah($tj->total_jasa+$tb->total_barang):rupiah($data->sisa) ?></td>
        </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
<br/>
<div id="paging"><?= $paging ?></div>
<?php die; ?>