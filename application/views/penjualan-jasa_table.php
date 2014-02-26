<?php 
$totallica = 0;
foreach ($list_data as $noo => $rowz) {
?>
     <tr class="tr_row <?= ($noo%2==0)?'even':'odd' ?>">
         <?php
                if (($rowz->layanan == 'Sewa Kamar') && ($rowz->frekuensi === null) ) {
                   $durasi = get_duration($rowz->waktu, date('Y-m-d H:i:s'));
                   if ($durasi['day'] == 0) {
                    $durasi['day']++;
                    } else if ($durasi['hour'] > 0) {
                        $durasi['day']++;
                    }
                    $frekuensi = $durasi['day'];
                    $subtotal = $rowz->nominal * $frekuensi;
                }else{
                    $frekuensi = $rowz->frekuensi;
                    $subtotal = $rowz->subtotal;
                }
                
            ?>
         <td align="center"><?= ($noo+1) ?></td>
         <td><?= datetime($rowz->waktu) ?></td>
         <td><?= $rowz->nakes ?></td>
         <td><?= $rowz->barang.' '.$rowz->layanan ?></td>
         <td align="right"><?= rupiah($rowz->nominal) ?></td>
         <td align="center"><?= $frekuensi ?></td>
         <td align="right" id="subtotal<?= $noo ?>">
            <?= rupiah($subtotal) ?>
        </td>
         <td align="center" class="aksi">
             <?php if ( (strpos($rowz->layanan, 'Pendaftaran Kunjungan') === FALSE) and $rowz->layanan !== 'Sewa Kamar') { ?>
             <a class=deletion title="Hapus" id="<?= $rowz->id ?>" onclick=eliminate(this,'<?= $rowz->id ?>')></a>
             <?php } else { ?>
             -
             <?php } ?>
         </td>
     </tr>
     <?php 
     $totallica = $totallica + $subtotal;
} ?>
<script>
    $('#totals, #total').html(numberToCurrency(<?= $totallica ?>));
</script>