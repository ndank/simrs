<title><?= $title ?></title>
<script type="text/javascript">
$(function() {
    $('#awal,#akhir').datepicker({
        changeYear: true,
        changeMonth: true
    });
    $('#tampil').button({
        icons: {
            secondary: 'ui-icon-search'
        }
    });
    $('#cetak').button({
        icons: {
            secondary: 'ui-icon-print'
        }
    });
    $('#reset').button({
        icons: {
            secondary: 'ui-icon-refresh'
        }
    });
});
</script>
<div class="kegiatan">
    <div class="titling"><h1><?= $title ?></h1></div>
    <div class="data-input">
        <table width="100%" class="inputan">Parameter</legend>
            <tr><td>Range Tanggal Jurnal:</td><td><?= form_input('awal', date("d/m/Y"), 'id=awal') ?><span class="label">s . d</span><?= form_input('akhir', date("d/m/Y"), 'id=akhir') ?>
            <tr><td></td><td><?= form_button(null, 'Tampil', 'id=tampil') ?> <?= form_button(null, 'Cetak', 'id=cetak') ?> <?= form_button(null, 'Reset', 'id=reset') ?>
        </table>
    </div>
    <div class="data-list">
        <h2>Laporan SHU Tahun <?= date("Y")-1 ?></h2>
        <table class="list-data" width="100%">
            <tr>
                <th>Kategori</th>
                <th>Nama</th>
                <th>Nilai</th>
            </tr>
            <?php foreach ($list_data as $data) { ?>
            <tr>
                <td><?= $data->nama ?></td>
                <td></td>
                <td></td>
            </tr> 
            <?php 
            // Sub Rekening
            $total_pend_layan  = $this->m_akuntansi->jurnal_load_data_by_subrekening(null, 1)->row()->debet;
            $total_pend_lainnya  = $this->m_akuntansi->jurnal_load_data_by_subrekening(null, 2)->row()->debet;
            $total_bbn_pelayanan = $this->m_akuntansi->jurnal_load_data_by_subrekening(null, 3)->row()->debet;
            $total_beban_lain = $this->m_akuntansi->jurnal_load_data_by_subrekening(null, 4)->row()->debet;
            $total_bagi_shu = $this->m_akuntansi->jurnal_load_data_by_subrekening(null, 5)->row()->debet;
            $total_taksir_pajak = $this->m_akuntansi->jurnal_load_data_by_subrekening(null, 4)->row()->debet;
            $sub_rekening = $this->m_akuntansi->data_subrekening_load_data(isset($id_sub)?$id_sub:NULL, $data->id)->result();
            foreach ($sub_rekening as $rows) { 
                $subtotal = $this->m_akuntansi->jurnal_load_data_by_subrekening($rows->id)->row();
                ?>
                <tr style="background: #f9f9f9;">
                    <td></td>
                    <td><?= $rows->nama ?></td>
                    <td align="right"><?= rupiah($subtotal->debet) ?></td>
                </tr>
                <?php 
                
                } ?>
                <?php if ($data->id == '1') { ?>
                    <tr>
                        <td></td><td></td><td align="right">JUMLAH PENDAPATAN PELAYANAN: <?= rupiah($total_pend_layan) ?></td>
                    </tr>
                <?php } ?>
                <?php if ($data->id == '2') { ?>
                    <tr>
                        <td></td><td></td><td align="right">JUMLAH PENDAPATAN LAINNYA: <?= rupiah($total_pend_lainnya) ?></td>
                    </tr>
                <?php } ?>
                <?php if ($data->id == '3') { 
                    ?>
                    <tr>
                        <td></td><td></td><td align="right">JUMLAH BEBAN PELAYANAN: <?= rupiah($total_bbn_pelayanan) ?></td>
                    </tr>
                    <tr>
                        <td></td><td></td><td align="right">JUMLAH LABA KOTOR PELAYANAN: <?= rupiah($total_pend_lainnya-$total_bbn_pelayanan) ?></td>
                    </tr>
                <?php } ?>
                <?php if ($data->id == '4') { 
                    ?>
                    <tr>
                        <td></td><td></td><td align="right">BEBAN LAINNYA: <?= rupiah($total_beban_lain) ?></td>
                    </tr>
                    <tr>
                        <td></td><td></td><td align="right">JUMLAH LABA KOTOR LAINNYA: <?= rupiah($total_pend_lainnya-$total_beban_lain) ?></td>
                    </tr>
                    <tr>
                        <td></td><td></td><td align="right">JUMLAH LABA: <?= rupiah(($total_pend_lainnya-$total_bbn_pelayanan)+($total_pend_lainnya-$total_beban_lain)) ?></td>
                    </tr>
                <?php } ?>
                <?php if ($data->id == '5') { 
                    $jml_laba = (($total_pend_lainnya-$total_bbn_pelayanan)+($total_pend_lainnya-$total_beban_lain));
                    ?>
                    <tr>
                        <td></td><td></td><td align="right">JUMLAH PEMBAGIAN SHU: <?= rupiah($total_bagi_shu) ?></td>
                    </tr>
                    <tr>
                        <td></td><td></td><td align="right">LABA (RUGI) BERSIH: <?= rupiah((($jml_laba - $total_taksir_pajak)-$total_taksir_pajak) - $total_bagi_shu) ?></td>
                    </tr>
                <?php } ?>
                <?php if ($data->id == '7') { 
                    $jml_laba = (($total_pend_lainnya-$total_bbn_pelayanan)+($total_pend_lainnya-$total_beban_lain));
                    ?>
                    <tr>
                        <td></td><td></td><td align="right">TAKSIRAN PAJAK: <?= rupiah($total_taksir_pajak) ?></td>
                    </tr>
                    <tr>
                        <td></td><td></td><td align="right">LABA (RUGI) SEBELUM PAJAK TAHUN BERJALAN: <?= rupiah($jml_laba - $total_taksir_pajak) ?></td>
                    </tr>
                    <tr>
                        <td></td><td></td><td align="right">LABA (RUGI) SESUDAH PAJAK TAHUN BERJALAN: <?= rupiah(($jml_laba - $total_taksir_pajak)-$total_taksir_pajak) ?></td>
                    </tr>
                <?php } ?>
                    
            <?php 
            } ?>
        </table>
    </div>
</div>