<script>
    window.print();    
    setTimeout(function(){ window.close();},300);
</script>
<style>
    table { border-top: 1px solid #000; border-left: 1px solid #000; border-spacing: 0; }
    table td { border-bottom: 1px solid #000; border-right: 1px solid #000; }
</style>
<h1>Laporan SHU</h1>
<div class="data-list">
        
        <table class="list-data" width="50%" style="float: left; clear: right;">
            <?php
            $total1 = 0;
            foreach ($pendapatan_operasional as $r1 => $data) { ?>
            <tr data-tt-id='<?= $r1 ?>' class="even" style="font-weight: bold;">
                <td width="10%"><?= $data->rekening ?></td>
                <td></td>
                <td align="center"></td>
                <td align="center"></td>
            </tr>
           <?php 
           if (isset($id_sub)) { $id_sub = $id_sub;
            } else if (isset($data->id_sub_rekening)) { $id_sub = $data->id_sub_rekening;
            } else { $id_sub = NULL; }
                $sub_rekening = $this->m_akuntansi->data_subrekening_load_data($id_sub, $data->id)->result();
                foreach ($sub_rekening as $r2 => $rows) { ?>
                    <tr class="even">
                        <td align="center"></td>
                        <td><?= $rows->nama ?></td>
                        <td align="center"></td>
                        <td align="center"></td>
                    </tr> <?php 
                    
                    $sub_sub_rekening = $this->m_akuntansi->data_subsubrekening_load_data(NULL, $rows->id)->result();
                    foreach ($sub_sub_rekening as $r3 => $rowx) { 
                        $total_po = $this->m_akuntansi->total_jurnal_by_sub_sub($rowx->id)->row(); ?>
                        <tr class="even">
                            <td align="center"></td>
                            <td></td>
                            <td><?= $rowx->nama ?></td>
                            <td align="right"><?= rupiah($total_po->total_kredit) ?></td>
                        </tr>
                        <?php
                        $total1 = $total1 + $total_po->total_kredit;
                    }
               } 
           } ?>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td align="right"><b><?= rupiah($total1) ?></b></td>
            </tr>
        </table>
        
        <table class="list-data" width="50%" style="float: left; clear: right;">
            <?php
            $total2 = 0;
            foreach ($beban_operasional as $r1 => $data) { ?>
            <tr data-tt-id='<?= $r1 ?>' class="even" style="font-weight: bold;">
                <td><?= $data->rekening ?></td>
                <td></td>
                <td align="center"></td>
                <td align="center"></td>
            </tr>
           <?php 
            if (isset($id_sub)) { $id_sub = $id_sub;
            } else if (isset($data->id_sub_rekening)) { $id_sub = $data->id_sub_rekening;
            } else { $id_sub = NULL; }
                $sub_rekening = $this->m_akuntansi->data_subrekening_load_data($id_sub, $data->id)->result();
                foreach ($sub_rekening as $r2 => $rows) { ?>
                    <tr data-tt-id='<?= $r1 ?>-<?= $r2 ?>' data-tt-parent-id='<?= $r1 ?>' class="even">
                        <td align="center"></td>
                        <td><?= $rows->nama ?></td>
                        <td align="center"></td>
                        <td align="center"></td>    
                    </tr> <?php 
                    $sub_sub_rekening = $this->m_akuntansi->data_subsubrekening_load_data(NULL, $rows->id)->result();
                    
                    foreach ($sub_sub_rekening as $r3 => $rowx) { 
                        $total_bo = $this->m_akuntansi->total_jurnal_by_sub_sub($rowx->id)->row();
                        ?>
                        <tr data-tt-id='<?= $r1 ?>-<?= $r2 ?>-<?= $r3 ?>' data-tt-parent-id='<?= $r1 ?>-<?= $r2 ?>' class="even">
                            <td align="center"></td>
                            <td></td>
                            <td><?= $rowx->nama ?></td>
                            <td align="right"><?= rupiah($total_bo->total_debet) ?></td>    
                        </tr>
                        <?php
                        $total2 = $total2 + $total_bo->total_debet;
                    }
               } 
           } ?>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td align="right"><b><?= rupiah($total2) ?></b></td>
            </tr>
        </table>
        <br/>
        
        <?php
            $jml_laba_bersih = $total1-$total2;
        ?>
        <table width="50%" align="right">
            <tr><td align="right" style="font-size: 18px;">LABA BERSIH:</td><td align="right" style="font-size: 18px;"><b><?= rupiah($jml_laba_bersih) ?></b></td></tr>
        </table>
    </div>