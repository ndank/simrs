<link rel="stylesheet" href="<?= base_url('/assets/css/print-struk.css') ?>" />
<script type="text/javascript">
    function cetak() {
        window.print();    
        setTimeout(function(){ window.close();},300);
    }
</script>
<style>
            .tabel-laporan{
                border-left: 1px solid #ccc; border-top: 1px solid #ccc; border-spacing: 0;
            }
            .tabel-laporan th, .tabel-laporan td{           
                border-right: 1px solid #ccc; border-bottom: 1px solid #ccc;
                padding: 1px;
                height: 16px;
            }
            .tabel-laporan .number{
                text-align: center;
            }

            .tabel-laporan th rowspan, td rowspan{
                vertical-align: middle;
            }
            .letter_layout{
                border-style: solid;
                border-width: 2px;
                padding: 15px;
            }
        </style>
<title><?= $title ?></title>
<body onload="cetak()">
<div class="layout-printer">
<table class="header-printer" style="border-bottom: 1px solid #000;" width="100%">
    <tr><td colspan="4" align="center" style="text-transform: uppercase">Rumah Sakit <?= $apt->nama ?></td> </tr>
    <tr><td colspan="4" align="center"><?= $apt->alamat ?> <?= $apt->kelurahan ?></td> </tr>
    <tr><td colspan="4" align="center">Telp. <?= $apt->telp ?>,  Fax. <?= $apt->fax ?>, Email <?= $apt->email ?></td> </tr>
</table>
    <center><h2>NOTA</h2></center>
<?php
foreach ($attribute as $rows);
$detail = $this->m_billing->detail_atribute_penduduk_by_norm($rows->no_rm)->row();
?>
    <table width="100%">
        <tr><td width="20%">Waktu:</td><td><?= datefrompg($rows->tgl_layan) ?></td></tr>
        <tr><td>No. RM:</td><td><?= str_pad($rows->no_rm, 6,"0",STR_PAD_LEFT) ?></td></tr>
        <tr><td>Nama:</td><td><?= $rows->nama ?></td></tr>
        <tr><td>Alamat Jalan:</td><td><?= isset($detail->alamat)?$detail->alamat:'' ?></td></tr>
        <tr><td>Wilayah:</td><td><?= isset($detail->kelurahan)?$detail->kelurahan:'' ?> <?= isset($detail->kecamatan)?$detail->kecamatan:'' ?> <?= isset($detail->kabupaten)?$detail->kabupaten:'' ?></td></tr>
        <tr><td>No. Kunjungan</td><td><?= $rows->no_daftar ?></td></tr>
    </table>
    <br/>
    <table width="100%" border="0">
        <tr>
            <td colspan="2"><?= $daftar_kunjungan->nama ?></td>
            
        </tr>
        <tr>
            <td colspan="2" align="right">
                <?= rupiah($daftar_kunjungan->subtotal) ?>
            </td>
        </tr>
        <!-- end 1 -->
        <?php 
        $akomodasi_kamar = 0;
        if (isset($akomodasi_kamar_inap->nama)) {
            
            $akomodasi_kamar = $akomodasi_kamar_inap->subtotal; ?>
        <tr>
            <td colspan="2"><?= $akomodasi_kamar_inap->nama ?></td>
        </tr>
        <tr>
            <td colspan="2" align="right">
                <?= rupiah($akomodasi_kamar_inap->subtotal) ?>
            </td>
        </tr>
        <?php } ?>
        <!-- end 2 -->
        <?php
        $selain_total = 0;
        foreach ($selain as $key => $data) {
        ?>
        <tr>
            <td colspan="2"><?= $data->nama ?></td>
        </tr>
        <tr>
            <td colspan="2" align="right">
                <?= rupiah($data->subtotal) ?>
            </td>
        </tr>
        <?php 
        $selain_total = $selain_total+$data->subtotal;
        } ?>
        <!-- end 3 -->
        <tr>
            <td colspan="2">Pemakaian Obat / Barang</td>
        </tr>
        <tr>
            <td colspan="2" align="right">
                <?= rupiah($total_barang->total_barang) ?>
            </td>
        </tr>
        <?php
        $total = $daftar_kunjungan->subtotal + $akomodasi_kamar + $selain_total + $total_barang->total_barang;
        ?>
        <tr>
            <td align="right"><b>Total</b></td>
            <td align="right"><b><?= rupiah($total) ?></b></td>
        </tr>
    </table>
<br/>
<p style="float: right; text-align: right;">
    Total: <?= rupiah($total) ?> <br />
    Untuk Pembayaran ke: <?= $bayar_ke ?> <br/>
    Jumlah Terbayar: <?= rupiah($pembayaran->bayar) ?>, Sisa Tagihan: 
    <?php 
        $sisa = $total-$pembayaran->bayar;
        if ($sisa < 0) {
            echo rupiah(0);
        }else{
            echo rupiah($sisa);
        }
        
    ?>
</p>
</div>
</body>