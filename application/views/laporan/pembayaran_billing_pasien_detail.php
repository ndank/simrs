<link rel="stylesheet" href="<?= base_url('/assets/css/workspace.css') ?>" />
<h1 class="informasi">Pembayaran Pasien</h1>
<div class="data-input">
        <tr><td>Tanggal</td><td><span class="label"><?= datetime($pasien->arrive_time) ?></span>
        <tr><td>No. RM</td><td><span class="label"><?= $pasien->no_rm ?></span>
        <tr><td>No. Kunjungan</td><td><span class="label" id="kunjungan"><?= $pasien->no_daftar ?></span>
        <tr><td>Nama Pasien</td><td><span class="label" id="pasien"><?= $pasien->pasien ?></span>
        <tr><td>Produk Asuransi</td><td>
        <span class="label" id="produk">
            <?php
            /*if (count($asuransi) > 0) {
                foreach ($asuransi as $data) {
                    echo $data->nama . ' ' . $data->polis_no . '<br/>';
                }
            } else {
                echo "-";
            }*/
            ?>
        </span>

        <tr><td>Total (Rp.)</td><td><span class="label" id="total"></span>
</div>

<div style="margin-top: 40px;">
    <table width="100%" class="tabel-laporan">
    <tr>
        <th width="5%">No.</th>
        <th align="left" width="45%">Nama Layanan</th>
        <th width="15%">Nominal Tarif</th>
        <th width="10%">Frekuensi</th>
        <th align="right" width="10%">Subtotal</th>
    </tr>
    <?php
        $total_kunj = 0;
        foreach ($daftar_kunjungan as $key => $data) { 
    ?>
    <tr>
        <td align="center"><?= ++$key ?></td>
        <td><?= $data->nama ?></td>
        <td align="right"><?= rupiah($data->nominal) ?></td>
        <td align="center"><?= $data->frekuensi ?></td>
        <td align="right"><?= rupiah($data->subtotal) ?></td>
    </tr>
    <?php 
    $total_kunj = $total_kunj+$data->subtotal;
    } ?>
    <tr>
        <td colspan="4" align="right"><b>Total</b></td>
        <td align="right"><b><?= rupiah($total_kunj) ?></b></td>
    </tr>
</table>
<br/>

<table width="100%" class="tabel-laporan">
    <tr>
        <th width="5%">No.</th>
        <th align="left" width="45%">Nama Layanan</th>
        <th width="15%">Nominal Tarif</th>
        <th width="10%">Frekuensi</th>
        <th align="right" width="10%">Subtotal</th>
    </tr>
    <?php 
        $total_akomodasi_kamar = 0;
        foreach ($akomodasi_kamar_inap as $key => $data) { 
    ?>
    <tr>
        <td align="center"><?= ++$key ?></td>
        <td><?= $data->nama ?></td>
        <td align="right"><?= rupiah($data->nominal) ?></td>
        <td align="center"><?= $data->frekuensi ?></td>
        <td align="right"><?= rupiah($data->subtotal) ?></td>
    </tr>
    
        <?php 
        $total_akomodasi_kamar = $total_akomodasi_kamar+$data->subtotal;
        } ?>
   
    <tr>
        <td align="right" colspan="4"><b>Total</b></td>
        <td align="right"><b><?= rupiah($total_akomodasi_kamar) ?></b></td>
    </tr>
</table>
    <br/>
<table width="100%" class="tabel-laporan">
    <tr>
        <th width="5%">No.</th>
        <th align="left" width="45%">Nama Layanan</th>
        <th width="15%">Nominal Tarif</th>
        <th width="10%">Frekuensi</th>
        <th align="right" width="10%">Subtotal</th>
    </tr>
    <?php 
        $selain_kunj_akomodasi_kamar = 0;
        foreach ($selain as $key => $data) { 
    ?>
    <tr>
        <td align="center"><?= ++$key ?></td>
        <td><?= $data->nama ?></td>
        <td align="right"><?= rupiah($data->nominal) ?></td>
        <td align="center"><?= $data->frekuensi ?></td>
        <td align="right"><?= rupiah($data->subtotal) ?></td>
    </tr>
    
    <?php 
        $selain_kunj_akomodasi_kamar = $selain_kunj_akomodasi_kamar+$data->subtotal;
    } ?>
   
    <tr>
        <td align="right" colspan="4"><b>Total</b></td>
        <td align="right"><b><?= rupiah($selain_kunj_akomodasi_kamar) ?></b></td>
    </tr>
</table>
    <br/>
<table width="100%" class="tabel-laporan">
    <tr>
        <th width="5%">No.</th>
        <th align="left" width="45%">Nama Layanan</th>
        <th width="15%">Nominal Tarif</th>
        <th width="10%">Frekuensi</th>
        <th align="right" width="10%">Subtotal</th>
    </tr>
    <?php 
        $total_barang = 0;
        foreach ($barang_list_data as $key => $data) { 
            $harga_jual = $data->hna + ($data->hna * $data->margin / 100) - ($data->hna * ($data->diskon / 100));
            //$subtotal = $harga_jual;
    ?>
    <tr>
        <td align="center"><?= ++$key ?></td>
        <td><?= $data->barang ?> <?= ($data->kekuatan == '1') ? '' : $data->kekuatan ?> <?= $data->satuan ?> <?= $data->sediaan ?> <?= (($data->generik == 'Non Generik') ? '' : $data->pabrik) ?> <?= (($data->isi == '1') ? '' : $data->isi) ?> <?= $data->satuan_terkecil ?></td>
        <td align="right"><?= rupiah($harga_jual) ?></td>
        <td align="center"><?= $data->keluar ?></td>
        <td align="right">
            <?php 
                $total_barang += $data->subtotal;
                echo rupiah($data->subtotal);
            ?>
        </td>
    </tr>
    
    <?php 
    } ?>
   
    <tr>
        <td align="right" colspan="4"><b>Total</b></td>
        <td align="right"><b><?= rupiah($total_barang) ?></b></td>
    </tr>
</table>
    <script>
        $(function() {
            $('#total, #total-pembayaran').html(numberToCurrency(Math.ceil(<?= rupiah($total_kunj+$total_akomodasi_kamar+$selain_kunj_akomodasi_kamar+$total_barang) ?>)));
        })
    </script>
<?php die; ?>