<?php
header_excel("RL 3.13.xls");
//foreach ($pemesanan as $rows);
/*if ($apt['logo_file_nama'] != '') {
    $img = "<img src='".app_base_url('assets/images/company/'.$apt['logo_file_nama'])."' width='100px' />";
} else {
    $img = "<img src='".app_base_url('assets/images/company')."/apotek.jpg' width='100px' />";
}*/
?>
<style>
    * { font-size: 14px; }
    table td {font-size: 14px;}
    .list-data-excel { border-top: 1px solid #000; border-left: 1px solid #000; border-spacing: 0; border-bottom: 1px solid #000; }
    .list-data-excel th { border-bottom: 1px solid #000; border-right: 1px solid #000;  }
    .list-data-excel td { border-bottom: 1px solid #f4f4f4; border-right: 1px solid #000;  }
</style>
<table border="1" bgcolor="#e6faff">
<tr>
<td>

<table width="100%" style="color: #000; border-bottom: 1px solid #000;" bgcolor="#fff">
    <tr>
    <td rowspan="5" style="width: 70px;"><img src="<?= base_url('assets/images/company/bakti-husada.png') ?>" width="70px" height="88px" /></td>    
    <td colspan="3" style="line-height: 50px;"><b>Formulir RL 3.13</b></td><td rowspan="2"><span style="font-size: 10px;"><i>Dirjen Bina Upaya Kesehatan<br/>Kementrian Kesehatan R.I</span></td> </tr>
    <tr valign="top"><td colspan="3"><b>PENGADAAN OBAT, PENULISAN DAN PELAYANAN RESEP</b></td> </tr>
</table>
<table width="100%">
    <tr><td width="15%">Kode R.S.:</td><td align="left"></td></tr>
    <tr><td>Nama R.S.:</td><td align="left"></td></tr>
    <tr><td>Tahun:</td><td align="left"><?= ($_GET['akhir'] != '')?substr($_GET['akhir'], 6,4):NULL ?></td></tr>
</table><br/>
<b>A. Pengadaan Obat</b>
<table width="100%" border="1">
    <tr valign="top">
        <th width="5%">No</th>
        <th width="24%">GOLONGAN OBAT</th>
        <th width="24%">JUMLAH ITEM OBAT</th>
        <th width="22%">JUMLAH ITEM OBAT<br/>YANG TERSEDIA DI<br/>RUMAH SAKIT</th>
        <th width="30%">JUMLAH ITEM OBAT<br/>FORMULARIUM YANG <br/> TERSEDIA DI RUMAH SAKIT</th>
    </tr>
    <tr>
        <td align="center">1</td>
        <td>Generik</td>
        <td align="center"><?= $og ?></td>
        <td align="center"><?= $og_2 ?></td>
        <td align="center"><?= $og_3 ?></td>
    </tr>
    <tr>
        <td align="center">2</td>
        <td>Non Generik <br/>Formularium</td>
        <td align="center"><?= $ng_form ?></td>
        <td align="center"><?= $ng_form_2 ?></td>
        <td align="center"><?= $ng_form_3 ?></td>
    </tr>
    <tr>
        <td align="center">3</td>
        <td>Non Generik</td>
        <td align="center"><?= $ng ?></td>
        <td align="center"><?= $ng_2 ?></td>
        <td align="center"><?= $ng_3 ?></td>
    </tr>
    <tr>
        <td colspan="2" align="center">TOTAL</td>
        <td align="center"><?= $og+$ng_form+$ng ?></td>
        <td align="center"><?= $og_2+$ng_form_2+$ng_2 ?></td>
        <td align="center"><?= $og_3+$ng_form_3+$ng_3 ?></td>
    </tr>
</table><br/>
<b>B. Penulisan dan Pelayanan Resep</b>
<table width="100%" border="1">
    <tr valign="top">
        <th width="5%">No</th>
        <th width="24%">GOLONGAN OBAT</th>
        <th width="24%">RAWAT JALAN</th>
        <th width="22%">IGD</th>
        <th width="30%">RAWAT INAP</th>
    </tr>
    <tr>
        <td align="center">1</td>
        <td>Generik</td>
        <td align="center"><?= $g_rj ?></td>
        <td align="center"><?= $g_igd ?></td>
        <td align="center"><?= $g_ri ?></td>
    </tr>
    <tr>
        <td align="center">2</td>
        <td>Non Generik <br/>Formularium</td>
        <td align="center"><?= $ngf_rj ?></td>
        <td align="center"><?= $ngf_igd ?></td>
        <td align="center"><?= $ngf_ri ?></td>
    </tr>
    <tr>
        <td align="center">3</td>
        <td>Non Generik</td>
        <td align="center"><?= $ng_rj ?></td>
        <td align="center"><?= $ng_igd ?></td>
        <td align="center"><?= $ng_ri ?></td>
    </tr>
    <tr>
        <td colspan="2" align="center">TOTAL</td>
        <td align="center"><?= $g_rj+$ngf_rj+$ng_rj ?></td>
        <td align="center"><?= $g_igd+$ngf_igd+$ng_igd ?></td>
        <td align="center"><?= $g_ri+$ngf_ri+$ng_ri ?></td>
    </tr>
</table><br/>
</td></tr></table>