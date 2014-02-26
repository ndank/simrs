<script type="text/javascript">
    $(function(){
        $( ".print" ).button({icons: {secondary: "ui-icon-print"}});
        $('#accordion').accordion({
            collapsible:true,
            clearStyle: true, 
            autoHeight: false,
            active: 0,
            beforeActivate: function(event, ui) {
                 // The accordion believes a panel is being opened
                if (ui.newHeader[0]) {
                    var currHeader  = ui.newHeader;
                    var currContent = currHeader.next('.ui-accordion-content');
                 // The accordion believes a panel is being closed
                } else {
                    var currHeader  = ui.oldHeader;
                    var currContent = currHeader.next('.ui-accordion-content');
                }
                 // Since we've changed the default behavior, this detects the actual status
                var isPanelSelected = currHeader.attr('aria-selected') == 'true';
                
                 // Toggle the panel's header
                currHeader.toggleClass('ui-corner-all',isPanelSelected).toggleClass('accordion-header-active ui-state-active ui-corner-top',!isPanelSelected).attr('aria-selected',((!isPanelSelected).toString()));
                
                // Toggle the panel's icon
                currHeader.children('.ui-icon').toggleClass('ui-icon-triangle-1-e',isPanelSelected).toggleClass('ui-icon-triangle-1-s',!isPanelSelected);
                
                 // Toggle the panel's content
                currContent.toggleClass('accordion-content-active',!isPanelSelected)    
                if (isPanelSelected) { currContent.slideUp(); }  else { currContent.slideDown(); }

                return false; // Cancels the default action
            }
        });
    });

    function cetak(no_rm){
        var dWidth = $(window).width();
        var dHeight= $(window).height();
        var x = screen.width/2 - dWidth/2;
        var y = screen.height/2 - dHeight/2;
        window.open('<?= base_url("demografi/get_rekam_medis_pasien") ?>/'+no_rm+'/print','Myprint','width='+dWidth+', height='+dHeight+', left='+x+',top='+y);
    }
</script>

<table class="inputan" width="100%">
    <tr><td>Nama pasien:</td><td><span class="label"><?= $pasien->nama ?></span>
    <tr><td>No. rekam medik:</td><td><span class="label"><?= $pasien->no_rm ?><?= form_hidden('no_rm', $pasien->no_rm) ?></span>
    <tr><td></td><td><?= form_button('', 'Cetak Riwayat Rekam Medis', 'class=print onclick=cetak("'.$pasien->no_rm.'")') ?>
</table>


<table class="inputan" width="100%">
    <tr><td>Jenis kelamin:</td><td><?= ($pasien->gender == 'L') ? 'Laki-laki' : '' ?><?= ($pasien->gender == 'P') ? 'Perempuan' : '' ?></td></tr>         
    <tr><td>Tempat kelahiran:</td><td><?= $pasien->tempat_lahir ?></td></tr>
    <tr><td>Umur:</td><td><?= ($pasien->lahir_tanggal != '')?hitungUmur($pasien->lahir_tanggal):'' ?></td></tr>
    <tr><td>Telepon:</td><td><?= $pasien->telp ?></td></tr>
    <tr><td>Golongan darah:</td><td><?= ($pasien->darah_gol == '') ? '-' : $pasien->darah_gol ?></td></tr>
    <tr><td>Agama:</td><td><?= ($pasien->agama == '') ? '-' :$pasien->agama ?></td></tr>
    <tr><td>Pendidikan:</td><td><?= ($pasien->pendidikan != '') ? $pasien->pendidikan : '-' ?></td></tr>
    <tr><td>Pekerjaan:</td><td><?= ($pasien->pekerjaan != '') ? $pasien->pekerjaan : '-' ?></td></tr>
    <tr><td>Status pernikahan:</td><td><?= $pasien->pernikahan ?></td></tr>
    <tr><td>Alamat Jalan:</td><td><?= isset($pasien)?$pasien->alamat:NULL ?></td></tr>
    <tr><td>Desa/kelurahan:</td><td><?= $pasien->kelurahan ?></td></tr>
    <tr><td>Kecamatan:</td><td><?= $pasien->kecamatan ?></td></tr>
    <tr><td>Kabupaten/kota:</td><td><?= $pasien->kabupaten ?></td></tr>
    <tr><td>Provinsi:</td><td><?= $pasien->provinsi ?></td></tr>
    <tr><td>No. Identitas:</td><td><?= $pasien->identitas_no ?></td></tr>             
</table>

    <div id="accordion">

        <?php foreach ($kunjungan as $key => $value): ?>
        <h3><b>Kunjungan <?= ($key+1)." : ".indo_tgl($value->tgl_layan) ?></b></h3>
            <div class="data-input" style="overflow:hidden">     
                <table class="inputan" width="100%">
                <tr><td>Waktu Kedatangan:</td><td><?= ($value->arrive_time != '') ? datetime($value->arrive_time) : $value->arrive_time ?></td></tr> 
                <tr><td>Kebutuhan   Perawatan:</td><td id="perawatan"><?= $value->keb_rawat ?></td></tr>
                <tr><td>Jenis Layanan:</td><td><?= $value->jenis_layan ?></td></tr>
                <tr><td>Kriteria Layanan:</td><td><?= $value->krit_layan ?></td></tr>
                <tr><td></td><td><tr><td></td><td>
                <tr><td><h2>Pelayanan Kunjungan</h2></td><td></td></tr>
                <?php foreach ($value->pelayanan_kunjungan as $key2 => $pk): ?>
                <tr><td>
                    <h4>
                        &nbsp;&nbsp;<?= ($key2+1).". ". $pk->jenis ?>
                        <?php 
                            if(($pk->no_antri === null) & ($pk->jenis === 'Rawat Jalan')){
                                echo " (IGD)";
                            }else if(($pk->no_antri !== null) & ($pk->jenis === 'Rawat Jalan')){
                                echo " (Poliklinik)";
                            }
                        ?>
                    </h4>
                </td><td></td></tr>
                </table>
                <table width="100%" style="margin-left:25px;">
                    <tr><td width="120px">D.P.J.P:</td><td><?= $pk->nama_pegawai ?></td></tr>
                    <tr><td>Anamnesis:</td><td><span><?= $pk->anamnesis ?></span></td></tr>
                    <?php if(($pk->no_antri === null) & ($pk->jenis === 'Rawat Jalan')):?>
                       <tr><td>Pemeriksan Umum:</td><td></td></tr>
                       <tr>
                            <td colspan="2">
                                <table width="100%" style="margin-left:130px;"> 
                                    <tr><td width="80px">Tensi</td><td><?= ($pk->p_tensi != '')?$pk->p_tensi:'-'." mm/Hg" ?></td></tr>
                                    <tr><td>Nadi</td><td><?= ($pk->p_nadi != '')?$pk->p_nadi:'-'." bpm"?></td></tr>
                                    <tr><td>Suhu</td><td><?= ($pk->p_suhu != '')?$pk->p_suhu:'-'." <sup>&deg;</sup> C" ?></td></tr>
                                    <tr><td>Nafas</td><td><?= ($pk->p_nafas != '')?$pk->p_nafas:'-'." " ?></td></tr>
                                    <tr><td>Berat Badan</td><td><?= ($pk->p_bb != '')?$pk->p_bb:'-'." Kg" ?></td></tr>
                                </table>
                            </td>
                        </tr>
                    <?php else: ?>
                         <tr><td>Pemeriksaan Umum:</td><td><span><?= $pk->pemeriksaan_umum ?></span></td></tr>
                    <?php endif; ?>

                    <tr><td>Diagnosa:</td><td></td></tr>
                    <tr>
                            <td colspan="2">
                                <ul style="margin-left:100px;">
                                   <?php foreach ($pk->diagnosis as $key3 => $diag):?>
                                    <li><?= $diag->no_daftar_terperinci." . ".$diag->golongan_sebab ?></li>
                                   <?php endforeach; ?>
                                </ul>
                            </td>
                    </tr>

                    <tr><td>Tindakan:</td><td></td></tr>
                    <tr>
                            <td colspan="2">
                                <ul style="margin-left:100px;">
                                   <?php foreach ($pk->tindakan as $key4 => $td):?>
                                    <li><?= $td->kode_icdixcm." . ".$td->tindakan ?></li>
                                   <?php endforeach; ?>
                                </ul>
                            </td>
                    </tr>

                    <tr><td>Resep:</td><td></td></tr>
                    <tr>
                            <td colspan="2">
                                <ul style="margin-left:100px;">
                                   <?php foreach ($pk->resep as $key5 => $obat):?>
                                    <li><?= $obat->barang."  ".$obat->kekuatan." ".$obat->satuan."  ".$obat->sediaan ?></li>
                                   <?php endforeach; ?>
                                </ul>
                            </td>
                    </tr>

                    <tr><td>Labratorium:</td><td></td></tr>
                    <tr>
                            <td colspan="2">
                                <ul style="margin-left:100px;">
                                   <?php foreach ($pk->lab as $key6 => $lab):?>
                                    <li><?= $lab->layanan ?></li>
                                   <?php endforeach; ?>
                                </ul>
                            </td>
                    </tr>

                    <tr><td>Radiologi:</td><td></td></tr>
                    <tr>
                            <td colspan="2">
                                <ul style="margin-left:100px;">
                                   <?php foreach ($pk->rad as $key7 => $rad):?>
                                    <li><?= $rad->layanan ?></li>
                                   <?php endforeach; ?>
                                </ul>
                            </td>
                    </tr>
                </table>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>

<br/>
<br/>
