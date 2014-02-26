<?php
$nilai_rujukan = array('L' => 'Low', 'N' => 'Neutral', 'H' => 'High');
?>
<script type="text/javascript">
    $(function() {
        $('.cetak').button({icons: {secondary: 'ui-icon-print'}}).click(function() {
            var id = $(this).attr('id');
            window.open('laboratorium/cetak_hasil_pemeriksaan_lab/'+id,'Cetak','width=800px, height=400px, scrollable=yes');
        });
    });
</script>


<h2>Diagnosis Pelayanan Kunjungan</h2>
<?= anchor('pelayanan/rekap_morbiditas', 'Rekap Morbiditas', 'id=rekap_morbiditas') ?>
<table width="100%" class="list-data" id="diag_tabel">
    <thead>
        <tr>
            <th width="5%">No.</th>
            <th width="35%">Nama Dokter</th>
            <th width="15%">Unit</th>
            <th width="35%">Sebab Sakit</th>
            <th width="10%">Kode ICD X</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($list_diagnosis as $key => $data) { ?>
        <tr>
            <td align="center"><?= ++$key ?></td>
            <td><?= $data->nama ?></td>
            <td><?= $data->unit ?></td>
            <td><?= $data->golongan ?></td>
            <td align="center"><?= $data->no_daftar_terperinci ?></td>
        </tr>
        <?php } ?>
    </tbody>
</table>
<br/><br/>


<h2>Tindakan Pelayanan Kunjungan</h2>
<table width="100%" class="list-data" id="tindak_table">
    <thead>
        <tr>
            <th width="5%">No.</th>
            <th width="20%">Nakes Operator</th>
            <th width="20%">Nakes Anestesi</th>
            <th width="15%">Unit</th>
            <th width="25%">Tindakan</th>
            <th width="10%">ICD IX CM Code</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($list_tindakan as $key => $data) { ?>
        <tr>
            <td align="center"><?= ++$key ?></td>
            <td><?= $data->operator ?></td>
            <td><?= $data->anestesi ?></td>
            <td><?= $data->unit ?></td>
            <td><?= $data->layanan ?></td>
            <td align="center"><?= $data->kode_icdixcm ?></td>
        </tr>
        <?php } ?>
    </tbody>
</table>
<br/><br/>



<h2>Pelayanan Laboratorium</h2>
<table class="list-data" width="100%">
    <thead>
        <tr>
            <th width="5%">No.</th>
            <th width="10%">Waktu Order</th>
            <th width="20%">Nama Dokter</th>
            <th width="20%">Nama Analis Lab.</th>
            <th width="20%">Nama Layanan</th>
            <th width="10%">Waktu Hasil</th>
            <th width="5%">Hasil</th>
            <th width="5%">Ket</th>
            <th width="5%">Satuan</th>
            <th width="10%">#</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($list_lab as $key => $data) { ?>
        <tr>
            <td align="center"><?= ++$key ?></td>
            <td align="center"><?= datetimefmysql($data->waktu_order) ?></td>
            <td><?= $data->dokter ?></td>
            <td><?= $data->analis ?></td>
            <td><?= $data->layanan ?></td>
            <td align="center"><?= datetimefmysql($data->waktu_order) ?></td>
            <td align="center"><?= ($data->hasil !== '0')?$data->hasil:'-' ?></td>
            <td align="center"><?= ($data->ket !== NULL)?$data->ket:'-' ?></td>
            <td align="center"><?= ($data->satuan !== NULL)?$data->satuan:'-' ?></td>
            <td align="center" style="white-space: nowrap;">
                <a class="link_button" title="Klik untuk hapus" onclick="delete_this('<?= $data->id ?>')">Hapus</a>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>
<?php if($list_lab != null):?>
    <?= form_button(NULL, 'Cetak', 'class=cetak id="'.$data->id_pelayanan_kunjungan.'" style="margin-left:0"') ?>
<?php endif; ?>
<br/><br/>



<h2>Pelayanan Radiologi</h2>
<table class="list-data" width="100%">
    <thead>
        <tr>
            <th width="5%">No.</th>
            <th width="20%">Nama Dokter</th>
            <th width="20%">Nama Radiografer</th>
            <th width="20%">Nama Layanan</th>
            <th width="10%">Waktu Order</th>
            <th width="10%">Waktu Hasil</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($list_rad as $key => $data) { ?>
        <tr>
            <td align="center"><?= ++$key ?></td>
            <td><?= $data->dokter ?></td>
            <td><?= $data->radiografer ?></td>
            <td><?= $data->layanan ?></td>
            <td align="center"><?= ($data->waktu_order != '')?datetimefmysql($data->waktu_order, true):'-' ?></td>
            <td align="center"><?= ($data->waktu_hasil != '')?datetimefmysql($data->waktu_hasil, true):'-' ?></td>
        </tr>
        <?php } ?>
    </tbody>
</table>
<br/><br/>

<h2>Pelayanan Resep</h2>
<table class="list-data" width="100%">
    <thead>
        <tr>
            <th width="3%">No.</th>
            <th width="12%">Waktu</th>
            <th width="80%">Nama Barang</th>
            <th width="5%">Jumlah</th>
        </tr>
    </thead>
    <tbody>
         <?php foreach ($list_resep as $key => $data) { ?>
        <tr>
            <td align="center"><?= ++$key ?></td>
            <td align="center"><?= ($data->waktu != '')?datetimefmysql($data->waktu, true):'-' ?></td>
            <td><?= $data->nama_barang ?></td>
            <td align="center"><?= $data->qty ?></td>
            
        </tr>
        <?php } ?>
    </tbody>        
</table>
<br/><br/>


