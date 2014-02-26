<?php
    $unit1 = array_slice($unit, 0, (sizeof($unit)/2));
    $unit2 = array_slice($unit, (sizeof($unit)/2), sizeof($unit));

?>
<div>
    <div class="left_side">
        <!-- <div class="three_d" id="nomor"></div> -->
        <table class="dg-table" width="90%">
            <tr>
                <th class="light">Jenis Layanan</th>
                <th class="light">Antrian</th>
            </tr>
            <?php foreach ($unit1 as $key => $value):?>
                <tr>
                    <td class="light"><?= $value->nama ?></td>
                    <td align="center" class="no_antri">
                        <?php 
                            if (($value->jml > 0)&($value->last == null)) {
                                echo "1";
                            }else if(($value->jml > 0)&($value->last != null)){
                                echo $value->last;
                            }else{
                                echo "-";
                            }
                        ?></td>
                </tr>
            <?php endforeach;?>

        </table>
    </div>

     <div class="right_side">
        <table class="dg-table" width="90%">
            <tr>
                <th class="light">Jenis Layanan</th>
                <th class="light">Antrian</th>
            </tr>
            <?php foreach ($unit2 as $key => $value):?>
                <tr>
                    <td class="light"><?= $value->nama ?></td>
                    <td align="center" class="no_antri">
                        <?php 
                            if (($value->jml > 0)&($value->last == null)) {
                                echo "1";
                            }else if(($value->jml > 0)&($value->last != null)){
                                echo $value->last;
                            }else{
                                echo "-";
                            }
                        ?>
                    </td>
                </tr>
            <?php endforeach;?>

        </table>
    </div>
</div>