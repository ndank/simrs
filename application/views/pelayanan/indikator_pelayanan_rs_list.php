<div class="data-list">
    <center>
        <h2>Indikator Pelayanan Rumah Sakit<br>
            <?= ($awal != '')?indo_tgl(date2mysql($awal)):'-' ?> s.d <?= ($akhir != '')?indo_tgl(date2mysql($akhir)):'-' ?> <br></h2>
        <br/>
    </center>
    <table class="list-data" width="50%">
        <!-- BOR -->
        <tr>
            <td width="20%" colspan="3"><b>BOR (<i>Bed Occupancy Rate</i>)</b></td>
        </tr>

        <tr>
            <td width="20%">&nbsp;</td>
            <td width="50%">Jumlah Hari Perawatan</td>
            <td width="30%"><?= $bor->hari_perawatan ?></td>
        </tr>

        <tr>
            <td width="20%">&nbsp;</td>
            <td width="50%">Jumlah Tempat Tidur</td>
            <td width="30%"><?= $bor->bed ?></td>
        </tr>

         <tr>
            <td width="20%">&nbsp;</td>
            <td width="50%">Nilai</td>
            <td width="30%"><?= round($bor->nilai, 5) ?> %</td>
        </tr>
        <tr><td width="20%">&nbsp;</td><td width="50%">&nbsp;</td><td width="30%">&nbsp;</td></tr>

        <!-- BOR -->

        <!-- ALOS -->
        
        <tr>
            <td width="20%" colspan="3"><b>ALOS (<i>Average Length of Stay</i>)</b></td>
        </tr>

         <tr>
            <td width="20%">&nbsp;</td>
            <td width="50%">Jumlah Total Lama dirawat inap:</td>
            <td width="30%"><?= ($alos->lama_inap != null)?$alos->lama_inap." hari":'-' ?></td>
        </tr>

        <tr>
            <td width="20%">&nbsp;</td>
            <td width="50%">Jumlah Pasien Rawat Inap:</td>
            <td width="30%"><?= ($alos->jumlah != null)?$alos->jumlah." pasien":'-' ?></td>
        </tr>
        <tr>
            <td width="20%">&nbsp;</td>
            <td width="50%">Nilai</td>
            <td width="30%"><?= (($alos->jumlah != null) && ($alos->lama_inap != null)) ? round($alos->lama_inap / $alos->jumlah, 5):'-' ?></td>
        </tr>
        <tr><td width="20%">&nbsp;</td><td width="50%">&nbsp;</td><td width="30%">&nbsp;</td></tr>

        <!-- ALOS -->

        <!-- BTO -->
        
        <tr>
            <td width="20%" colspan="3"><b>BTO (<i>Bed Turn Over</i>)</b></td>
        </tr>

         <tr>
            <td width="20%">&nbsp;</td>
            <td width="50%">Jumlah Pasien Rawat Inap:</td>
            <td width="30"><?= ($bto->jumlah != null)?$bto->jumlah." pasien":'-' ?></td>
        </tr>

        <tr>
            <td width="20%">&nbsp;</td>
            <td width="50%">Jumlah TT</td>
            <td width="30%"><?= ($bto->bed != null)?$bto->bed:'-' ?></td>
        </tr>

         <tr>
            <td width="20%">&nbsp;</td>
            <td width="50%">Nilai:</td>
            <td width="30%"><?= (($bto->jumlah != null) && ($bto->bed != null)) ? round($bto->jumlah / $bto->bed, 5):'-' ?></td>
        </tr>

        <tr><td width="20%">&nbsp;</td><td width="50%">&nbsp;</td><td width="30%">&nbsp;</td></tr>
        <!-- BTO -->
    </table>
    <table class="list-data" width="50%">
         <!-- TOI -->
        
        <tr>
            <td width="20%" colspan="3"><b>TOI (<i>Turn Over Interval</i>)</b></td>
        </tr>

         <tr>
            <td width="20%">&nbsp;</td>
            <td width="50%">Jumlah TT</td>
            <td width="30%"><?= ($toi->bed != null)?$toi->bed:'-' ?></td>
        </tr>

        <tr>
            <td width="20%">&nbsp;</td>
            <td width="50%">Jumlah Periode</td>
            <td width="30%"><?= ($toi->periode != null)?$toi->periode." hari":'-' ?></td>
        </tr>

        <tr>
            <td width="20%">&nbsp;</td>
            <td width="50%">Jumlah Pasien Rawat Inap</td>
            <td width="30%"><?= ($toi->jumlah != null)?$toi->jumlah." pasien":'-' ?></td>
        </tr>        

        <tr>
            <td width="20%">&nbsp;</td>
            <td width="50%">Nilai</td>
            <td width="30%"><?= round($toi->nilai, 5) ?></td>
        </tr>

        <tr><td width="20%">&nbsp;</td><td width="50%">&nbsp;</td><td width="30%">&nbsp;</td></tr>
        <!-- TOI -->

         <!-- NDR -->
        
        <tr>
            <td width="20%" colspan="3"><b>NDR (<i>Net Death Rate</i>)</b></td>
        </tr>

         <tr>
            <td width="20%">&nbsp;</td>
            <td width="50%">Jumlah Pasien Meninggal:</td>
            <td width="30%"><?= ($ndr->jumlah_mati != null)?$ndr->jumlah_mati." pasien":'-' ?></td>

        <tr>
            <td width="20%">&nbsp;</td>
            <td width="50%">Jumlah Pasien Meninggal dan Hidup:</td>
            <td width="30%"><?= ($ndr->jumlah != null)?$ndr->jumlah." pasien":'-' ?></td>
        </tr>

        <tr>
            <td width="20%">&nbsp;</td>
            <td width="50%">Nilai:</td>
            <td width="30%"><?= (($ndr->jumlah != null) & ($ndr->jumlah != 0) && ($ndr->jumlah_mati != null)) ? round($ndr->jumlah_mati / $ndr->jumlah, 5):'-' ?></td>
        </tr>

        <tr><td width="20%">&nbsp;</td><td width="50%">&nbsp;</td><td width="30%">&nbsp;</td></tr>
        <!-- NDR -->

         <!-- GDR -->
        
        <tr>
            <td width="20%" colspan="3"><b>GDR (<i>Gross Death Rate</i>)</b></td>
        </tr>

          <tr>
            <td width="20%">&nbsp;</td>
            <td width="50%">Jumlah Pasien Meninggal:</td>
            <td width="30%"><?= ($gdr->jumlah_mati != null)?$gdr->jumlah_mati." pasien":'-' ?></td>

        <tr>
            <td width="20%">&nbsp;</td>
            <td width="50%">Jumlah Pasien Meninggal dan Hidup:</td>
            <td width="30%"><?= ($gdr->jumlah != null)?$gdr->jumlah." pasien":'-' ?></td>
        </tr>

        <tr>
            <td width="20%">&nbsp;</td>
            <td width="50%">Nilai:</td>
            <td width="30%"><?= (($gdr->jumlah != null) & ($gdr->jumlah != 0) && ($gdr->jumlah_mati != null)) ? round($gdr->jumlah_mati / $gdr->jumlah, 5):'-' ?></td>
        </tr>

         <tr><td width="20%">&nbsp;</td><td width="50%">&nbsp;</td><td width="30%">&nbsp;</td></tr>

        <!-- GDR -->
    </table>    
</div>