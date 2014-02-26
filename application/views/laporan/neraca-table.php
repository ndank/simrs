<?php
$border = "";
if (isset($_GET['do'])) { 
    $border = "border=0";
    echo "<center>LAPORAN NERACA<br/>Tanggal ".indo_tgl(date2mysql($_GET['awal']))." s.d ".indo_tgl(date2mysql($_GET['akhir']))."</center>";
    echo "<style>";
    echo "table, td, tr { border: 1px solid #000; empty-cells: show; border-spacing: 0; font-size: 9px; }";
    echo "</style>";
    echo "<script>
        window.print();    
        setTimeout(function(){ window.close();},300);
    </script>";
}
?>
<style>
    * { font-family: Calibri; } 
</style>
<div class="data-list">
    <table class="tabel aset" width="100%" <?= $border ?>>
        <?php 
        $total_aktiva = 0;
        foreach ($aktiva as $data) { ?>
        <tr>
            <td width="10%"><?= $data->nama ?></td>
            <td width="10%">&nbsp;</td>
            <td width="30%">&nbsp;</td>
            <td width="40%">&nbsp;</td>
            <td width="10%">&nbsp;</td>
        </tr>
        <?php 
            
            $sub_rekening = $this->m_akuntansi->data_subrekening_load_data(NULL, $data->id)->result();
            foreach ($sub_rekening as $r2 => $rows) { ?>
                <tr>
                    <td>&nbsp;</td>
                    <td><?= $rows->nama ?></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr> 
                <?php
                $total_ss_aktiva = 0;
                $sub_sub_rekening = $this->m_akuntansi->data_subsubrekening_load_data(isset($id_sub_sub)?$id_sub_sub:NULL, $rows->id)->result();
                    foreach ($sub_sub_rekening as $r3 => $rowx) { 
                        
                        ?>
                    <tr>
                        <td></td>
                        <td></td>
                        <td><?= $rowx->nama ?></td>
                        <td align="right"></td>
                        <td></td>
                    </tr>
                <?php 
                
                //$total_ss_aktiva = $total_ss_aktiva + $value;
                    $sub_sub_sub_rekening = $this->m_akuntansi->data_subsubsub_rekening_load_data(null, $rowx->id)->result();
                        foreach ($sub_sub_sub_rekening as $r4 => $rowz) {
                            $totallica = $this->m_akuntansi->get_total_jurnal_by_subsub_aset($rowz->id)->row();
                            $penyusutan = $this->m_akuntansi->get_total_penyusutan_by_subsub_aset($rowz->id)->row();
                            $value = $totallica->total; 
                            if (cek_karakter($rowx->nama)) {
                                $nilai = '('.rupiah(abs($value)).')';
                            } else if (cek_karakter($rowz->nama)) { 
                                $nilai = '('.rupiah(abs($value)).')';
                            } else {
                                $nilai = rupiah($value);
                            }
                            ?>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td><?= $rowz->nama ?></td>
                                <td align="right"><?= $nilai  ?></td>
                            </tr>
                    <?php    
                        $total_ss_aktiva = ($total_ss_aktiva+$value)-$penyusutan->total;
                        }
                    } ?>
                <tr style="background: #f4f4f4;">
                    <td></td>
                    <td></td>
                    <td>&nbsp;</td>
                    <td align="right">Subtotal: </td>
                    <td align="right"><?= rupiah($total_ss_aktiva) ?></td>
                </tr>
                <?php 
                $total_aktiva = $total_aktiva + $total_ss_aktiva;
                } ?>
    <?php } ?>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td align="right"><b>TOTAL</b></td>
            <td align="right"><b><?= rupiah($total_aktiva) ?></b></td>
        </tr>
    </table>
    <table class="tabel kewajiban" width="100%" <?= $border ?>>
        <?php 
        $total_pasiva = 0;
        foreach ($pasiva as $data) { ?>
        <tr>
            <td width="10%"><?= $data->nama ?></td>
            <td width="10%">&nbsp;</td>
            <td width="30%">&nbsp;</td>
            <td width="40%">&nbsp;</td>
            <td width="10%">&nbsp;</td>
        </tr>
        <?php 
            $sub_rekening = $this->m_akuntansi->data_subrekening_load_data(NULL, $data->id)->result();
            foreach ($sub_rekening as $r2 => $rows) { ?>
                <tr>
                    <td>&nbsp;</td>
                    <td><?= $rows->nama ?></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr> 
                <?php
                $total_ss_pasiva = 0;
                $sub_sub_rekening = $this->m_akuntansi->data_subsubrekening_load_data(isset($id_sub_sub)?$id_sub_sub:NULL, $rows->id)->result();
                    foreach ($sub_sub_rekening as $r3 => $rowx) { 
                        
                        ?>
                    <tr>
                        <td></td>
                        <td></td>
                        <td><?= $rowx->nama ?></td>
                        <td align="right"></td>
                        <td>&nbsp;</td>
                    </tr>
                <?php
                //$total_ss_aktiva = $total_ss_aktiva + $value;
                    $sub_sub_sub_rekening = $this->m_akuntansi->data_subsubsub_rekening_load_data(null, $rowx->id)->result();
                        foreach ($sub_sub_sub_rekening as $r4 => $rowz) {
                            $totallica = $this->m_akuntansi->get_total_jurnal_by_subsub_aset($rowz->id)->row();
                            $penyusutan = $this->m_akuntansi->get_total_penyusutan_by_subsub_aset($rowz->id)->row();
                            $total_ss_pasiva = ($total_ss_pasiva+$totallica->total)-$penyusutan->total;
                            $value = $totallica->total; 
                            if (cek_karakter($rowx->nama)) {
                                $nilai = '('.rupiah(abs($value)).')';
                            } else if (cek_karakter($rowz->nama)) { 
                                $nilai = '('.rupiah(abs($value)).')';
                            } else {
                                $nilai = rupiah(abs($value));
                            }
                            ?>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td><?= $rowz->nama ?></td>
                                <td align="right"><?= $nilai  ?></td>
                            </tr>
                    <?php    } 
                    
                    } ?>
                <tr style="background: #f4f4f4;">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td align="right">Subtotal</td>
                    <td align="right"><?= ($total_ss_pasiva < 0)?rupiah(abs($total_ss_pasiva)):rupiah($total_ss_pasiva) ?></td>
                </tr>
            <?php 
            $total_pasiva = $total_pasiva + $total_ss_pasiva;
            } ?>
        <?php } ?>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td align="right"><b>TOTAL</b></td>
            <td align="right"><b><?= ($total_pasiva < 0)?rupiah(abs($total_pasiva)):rupiah($total_pasiva) ?></b></td>
        </tr>
    <!-- EKUITAS Begin Here -->
        <?php 
        $total_ekuitas = 0;
        foreach ($ekuitas as $data) { ?>
        <tr>
            <td width="10%"><?= $data->nama ?></td>
            <td width="10%">&nbsp;</td>
            <td width="30%">&nbsp;</td>
            <td width="40%">&nbsp;</td>
            <td width="10%">&nbsp;</td>
        </tr>
        <?php 
            $sub_rekening = $this->m_akuntansi->data_subrekening_load_data(NULL, $data->id)->result();
            foreach ($sub_rekening as $r2 => $rows) { ?>
                <tr>
                    <td>&nbsp;</td>
                    <td><?= $rows->nama ?></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr> 
                <?php
                $total_ss_ekuitas = 0;
                $sub_sub_rekening = $this->m_akuntansi->data_subsubrekening_load_data(isset($id_sub_sub)?$id_sub_sub:NULL, $rows->id)->result();
                    foreach ($sub_sub_rekening as $r3 => $rowx) { 
                        
                        ?>
                    <tr>
                        <td></td>
                        <td></td>
                        <td><?= $rowx->nama ?></td>
                        <td align="right"></td>
                        <td></td>
                    </tr>
                    <?php 
                
                    //$total_ss_aktiva = $total_ss_aktiva + $value;
                    $sub_sub_sub_rekening = $this->m_akuntansi->data_subsubsub_rekening_load_data(null, $rowx->id)->result();
                        foreach ($sub_sub_sub_rekening as $r4 => $rowz) {
                            $totallica = $this->m_akuntansi->get_total_jurnal_by_subsub_aset($rowz->id)->row();
                            $total_ss_ekuitas = $total_ss_ekuitas+$totallica->total;
                            $value = $totallica->total; 
                            if (cek_karakter($rowx->nama)) {
                                $nilai = '('.rupiah(abs($value)).')';
                            } else if (cek_karakter($rowz->nama)) { 
                                $nilai = '('.rupiah(abs($value)).')';
                            } else {
                                $nilai = rupiah(abs($value));
                            }
                            ?>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td><?= $rowz->nama ?></td>
                                <td align="right"><?= $nilai  ?></td>
                            </tr>
                <?php    }
                    } ?>
                <tr style="background: #f4f4f4;">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td align="right">Subtotal</td>
                    <td align="right"><?= ($total_ss_ekuitas < 0)?rupiah(abs($total_ss_ekuitas)):rupiah($total_ss_ekuitas) ?></td>
                </tr>
            <?php 
            $total_ekuitas = $total_ekuitas + $total_ss_ekuitas;
            } ?>
        <?php } ?>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td align="right"><b>TOTAL</b></td>
            <td align="right"><b><?= ($total_ekuitas < 0)?rupiah(abs($total_ekuitas)):rupiah($total_ekuitas) ?></b></td>
        </tr>
        
        <!-- SHU SHU SHU SHU SHU SHU SHU SHU SHU SHU SHU SHU SHU SHU SHU -->
    
        
        
            <?php
            
            // pendapatan //
            $total1 = 0;
            foreach ($pendapatan_operasional as $r1 => $data) { ?>
            
           <?php 
           if (isset($id_sub)) { $id_sub = $id_sub;
            } else if (isset($data->id_sub_rekening)) { $id_sub = $data->id_sub_rekening;
            } else { $id_sub = NULL; }
                $sub_rekening = $this->m_akuntansi->data_subrekening_load_data($id_sub, $data->id)->result();
                foreach ($sub_rekening as $r2 => $rows) { ?>
                     <?php 
                    
                    $sub_sub_rekening = $this->m_akuntansi->data_subsubrekening_load_data(NULL, $rows->id)->result();
                    foreach ($sub_sub_rekening as $r3 => $rowx) { 
                        $total_po = $this->m_akuntansi->total_jurnal_by_sub_sub($rowx->id)->row(); ?>
                        
                        <?php
                        $total1 = $total1 + $total_po->total_kredit;
                    }
               } 
           } 
           // pendapatan
           
            // beban //
            $total2 = 0;
            foreach ($beban_operasional as $r1 => $data) { ?>
           <?php 
            if (isset($id_sub)) { $id_sub = $id_sub;
            } else if (isset($data->id_sub_rekening)) { $id_sub = $data->id_sub_rekening;
            } else { $id_sub = NULL; }
                $sub_rekening = $this->m_akuntansi->data_subrekening_load_data($id_sub, $data->id)->result();
                foreach ($sub_rekening as $r2 => $rows) { ?>
                    <?php 
                    $sub_sub_rekening = $this->m_akuntansi->data_subsubrekening_load_data(NULL, $rows->id)->result();
                    
                    foreach ($sub_sub_rekening as $r3 => $rowx) { 
                        $total_bo = $this->m_akuntansi->total_jurnal_by_sub_sub($rowx->id)->row();
                        
                        $total2 = $total2 + $total_bo->total_debet;
                    }
               } 
           } 
           
           // beban //
           ?>
           
        
        <?php
            $jml_laba_bersih = $total1-$total2;
        ?>
        <tr><td></td><td></td><td></td><td align="right"><b>TOTAL SHU</b></td><td align="right"><b><?= rupiah($jml_laba_bersih) ?></b></td></tr>
    </table>
    <div style="font-weight: bold; float: right; margin-top: 10px; color: #000; font-size: 20px;">
        <table>
            <tr><td>TOTAL AKTIVA:</td><td align="right"> <?= rupiah($total_aktiva) ?></td></tr>
            <tr><td>TOTAL PASIVA:</td><td align="right"> <?= rupiah(abs($total_pasiva+$total_ekuitas)+$jml_laba_bersih) ?></td></tr>
        </table>
    </div>
</div>

