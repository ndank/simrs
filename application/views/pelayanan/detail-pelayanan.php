<script type="text/javascript">
    $('table').tablesorter();
    $('.kunjungan').click(function() {
        var url = $(this).attr('href');
        $('#loaddata').load(url);
        return false;
    });

    $('.detail_pelayanan').click(function() {
        $('.tr_layan').removeClass('selected');
        var no = $(this).attr('id');
        $('#tr_layan'+no).addClass('selected');
    });
</script>

    <h2>Pelayanan Kunjungan</h2>
    
    <h2>
        <table>
            <tr><td>No. RM  </td><td> : </td><td><?= $no_rm ?></td></tr>
            <tr><td>Nama Pasien  </td><td> : </td><td><?= $nama ?></td></tr>
        </table>
    </h2>
    
    <table width="100%" class="list-data">
        <thead>
            <tr>
                <th width="3%">ID</th>
                <th width="10%">Waktu</th>
                <th width="20%">Jenis Pelayanan</th>
                <th width="15%">Unit</th>
                <th width="5%">Kelas</th>
                <th width="5%">No. Bed</th>
                <th width="20%">Dokter Penanggung Jawab</th>
                <th width="18%">Asuransi</th>
                <?php if(!isset($irna)):?>
                <th width="3%">#</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data_list as $key => $data) { 
                    //echo print_r($data);
                          
                    if ($data->jenis == 'Rawat Jalan' and $data->jenis_rawat == 'Poliklinik') {
                        $jenis = "poliklinik";
                    }
                    if ($data->jenis == 'Rawat Jalan' and $data->jenis_rawat == 'IGD') {
                        $jenis = "igd";
                    }
                    if ($data->jenis == 'Rawat Inap' and $data->jenis_rawat == 'Poliklinik') {
                        $jenis = "rawat_inap";
                    }
                    if ($data->jenis == 'Rawat Inap' and $data->jenis_rawat == 'IGD') {
                        $jenis = "rawat_inap";
                    }

                    if ($data->jenis == 'Rawat Jalan' and $data->jenis_rawat == '' and $data->unit == 'Radiologi') {
                        $jenis = "radiologi_non_pasien";
                    }

                    if ($data->jenis == 'Rawat Jalan' and $data->jenis_rawat == '' and $data->unit == 'Laboratorium') {
                        $jenis = "laboratorium_non_pasien";
                    }

                    if ($data->jenis == 'Rawat Jalan' and $data->jenis_rawat == '' and $data->unit == 'Fisioterapi') {
                        $jenis = "pelayanan_fisioterapi_luar";
                    }

                   
                ?>
                <?php
                    if ($jenis == 'pelayanan_fisioterapi_luar') {
                        $no_dft = $data->no_daftar;
                    }else{
                        $no_dft = 'null';
                    }
                ?>

            <tr id="tr_layan<?= $data->id ?>" class="tr_layan">
                <td align="center"><?= $data->id ?></td>
                <td align="center" style="white-space: nowrap;"><?= ($data->waktu != null)?datetimefmysql($data->waktu, true):'-' ?></td>
                <td>
                    <?php if($data->pasien !== null): ?>
                        <?= $data->jenis ?>
                        <?php 
                            if(($data->no_antri === null) & ($data->jenis === 'Rawat Jalan')){
                                echo " (IGD)";
                            }else if(($data->no_antri !== null) & ($data->jenis === 'Rawat Jalan')){
                                echo " (Poliklinik)";
                            }
                        ?>
                    <?php else: ?>
                        Pasien luar
                    <?php endif; ?>
                </td>
                <td><?= $data->unit ?></td>
                <td align="center"><?= ($data->kelas == NULL)?'-':$data->kelas ?></td>
                <td align="center"><?= ($data->nomor_bed == null)?'-':$data->nomor_bed ?></td>
                <td><?= $data->nama ?></td>
                <td><?= $data->asuransi ?></td>
                <?php if(!isset($irna)):?>
                <td align="center">
                    <?php if(($jenis !== 'radiologi_non_pasien') and ($jenis !== 'laboratorium_non_pasien')):?>
                        
                        <a id="<?= $data->id ?>" class="link_button detail_pelayanan" onclick="detail_pelayanan_pasien(<?= $data->id ?>)">Detail</a>
                    <?php endif; ?>
                </td>
                 <?php endif; ?>
            </tr>
            <?php } ?>
        </tbody>
    </table>
    <br/>
    <div id="detail_pelayanan"></div>