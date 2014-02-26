<?php
header_excel("laporan-abc.xls");
?>
<style>
    * { font-size: 16px; }
    table td {font-size: 16px;}
    .list-data-excel { border-top: 1px solid #000; border-left: 1px solid #000; border-spacing: 0; border-bottom: 1px solid #000; }
    .list-data-excel th { border-bottom: 1px solid #000; border-right: 1px solid #000;  }
    .list-data-excel td { border-bottom: 1px solid #f4f4f4; border-right: 1px solid #000;  }
</style>
<table border="1" bgcolor="#e6faff" width="100%">
<tr>
<td>
    <table width="100%" style="color: #ffffff" bgcolor="#31849b">
        <tr>
        <td rowspan="3" style="width: 70px"><img src="<?= base_url('assets/images/company/'.$apt->logo_file_nama) ?>" width="70px" height="70px" /></td>    
        <td colspan="6" align="center"><b><?= strtoupper($apt->nama) ?></b></td><td rowspan="3" style="width: 70px"></td> </tr>
        <tr><td colspan="6" align="center"><b><?= strtoupper($apt->alamat) ?> <?= strtoupper($apt->kelurahan) ?></b></td> </tr>
        <tr><td colspan="6" align="center" style="padding-right: 70px"><b>TELP. <?= $apt->telp ?>,  FAX. <?= $apt->fax ?>, EMAIL <?= $apt->email ?></b></td> </tr>
    </table>
    <br/>
    <table width="100%" border="1" class="tabel">
        <tr>
            <th>No.</th>
            <th>Packing Barang</th>
            <th>Jumlah Terjual</th>
            <th>Harga Obat</th>
            <th>Total</th>
            <th>%(n)</th>
            <th>% Kumulatif (n)</th>
            <th>Golongan</th>
        </tr>
        <?php 
        $total = 0;
        foreach ($list_data as $rows) {
            $total = $total + $rows->jml_keluar;
        }
        $kum = 0;
        foreach ($list_data as $key => $data) { ?>
        <tr>
            <td align="center"><?= ++$key ?></td>
            <td><?= $data->barang ?> <?= ($data->kekuatan != '1') ? $data->kekuatan : null ?>  <?= $data->satuan ?> <?= $data->sediaan ?> <?= (($data->generik == 'Non Generik') ? '' : $data->pabrik) ?> @ <?= ($data->isi == 1) ? '' : $data->isi ?> <?= $data->satuan_terkecil ?></td>
            <td align="center"><?= $data->jml_keluar ?></td>
            <td align="right"><?= ($data->harga_obat) ?></td>
            <td align="right"><?= ($data->harga_obat*$data->jml_keluar) ?></td>
            <td align="center"><?= ($data->jml_keluar/$total)*100 ?></td>
            <td align="center"><?= $kum = $kum+(($data->jml_keluar/$total)*100) ?></td>
            <td align="center"><?php if ($kum >= '70' and $kum <= '80') { echo "A"; } if ($kum >= '80' and $kum <= '90') { echo "B"; } if ($kum >= '90' and $kum <= '100') { echo "C"; } ?></td>
        </tr>
        <?php } ?>
    </table>
    
</td>
</tr>
</table>