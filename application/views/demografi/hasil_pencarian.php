<div class="data-list">
<script type="text/javascript"> 

    $("table").tablesorter();
    var onSampleResized = function(e){
            var columns = $(e.currentTarget).find("th");
            var msg = "columns widths: ";
            columns.each(function(){ msg += $(this).width() + "px; "; });
    };
    $(".tabel").colResizable({
        liveDrag:true,
        gripInnerHtml:"<div class='grip'></div>", 
        draggingClass:"dragging", 
        onResize:onSampleResized
    });

    function detail_rm(no_rm){
        $('#rm').dialog({
            autoOpen: false,
            title :'Riwayat Rekam Medis Pasien ',
            height: $(window).height() - 10,
            width: $(window).width() - 10,
            modal: true,
            resizable : false,
            buttons: [ 
                { text: "Ok", click: function() { 
                        $( this ).dialog( "close" ); 
                    } 
                }
            ]
        });
        $.ajax({
            url: '<?= base_url("demografi/get_rekam_medis_pasien") ?>/'+no_rm+'/show',
            type : 'GET',
            cache: false,
            success: function(data) {
                $('#rekam_medis').html(data);
                $('#rm').dialog('open');
            }
        });
        
    }

    function cek_pendaftaran(no_rm, jenis){
        $.ajax({
            type : 'POST',
            url: '<?= base_url() ?>pendaftaran/cek_pendaftaran_terakhir/'+no_rm,
            data : $('#formtindak').serialize(),
            dataType : 'json',
            success: function(msg) {
                if (msg.status == 'inap') {
                    custom_message('Peringatan', 'Pasien bersangkutan masih dalam pelayanan rawat inap !');
                }else if(msg.status == 'bayar'){
                    custom_message('Peringatan', 'Pasien bersangkutan belum melunasi tagihan pelayanan rumah sakit !');
                }else{
                    if(jenis == 'IGD'){
                        kunjungan_igd(no_rm);
                    }else if(jenis == 'Jalan'){
                        kunjungan_poliklinik(no_rm);
                    }else{
                        antri(no_rm);
                    }
                }
            }
        });
    }

    
    function antri(no_rm){
         $.ajax({
            url: '<?= base_url("demografi/antrian_kunjungan") ?>',
            type : 'POST',
            data : "no_rm="+no_rm,
            cache: false,
            success: function(data) {
                $('#loaddata').html(data);
            
            }
        });
    }

    function kunjungan_igd(no_rm){
        $.ajax({
            url: '<?= base_url("pendaftaran/igd_new") ?>',
            data : "no_rm="+no_rm,
            cache: false,
            success: function(data) {
                $('#loaddata').html(data);
            
            }
        });
    }

    function kunjungan_poliklinik(no_rm){
        $.ajax({
            url: '<?= base_url("pendaftaran/kunjungan") ?>',
            data : "no_rm="+no_rm,
            cache: false,
            success: function(data) {
                $('#loaddata').html(data);
            
            }
        });
    }
</script>

<?php
if ($pasien != null) {
    ?>
    <?php if ($jumlah != ''): ?>
        <div id="resume">
            <br/>
            <h3>
                Halaman <?= $page ?> dari <?= (ceil($jumlah / $limit) == 0) ? 1 : ceil($jumlah / $limit) ?> (Total <?= $jumlah ?> data )
            </h3>
        </div>
    <?php endif; ?>
    <table class="list-data" width="100%">
        <thead>
            <tr>
                <th width="5%">No. RM</th>
                <th width="50%">Nama</th>
                <th width="5%">Jenis Kelamin</th>
                <th width="5%">Jenis</th>
                <th width="15%">Tanggal Lahir</th>
                <th width="15%">Usia</th>
                <th>#</th>
            </tr>
        </thead>
        <tbody>
        <?php 
        $baru = 0;
        $lama = 0;
        foreach ($pasien as $key => $row): ?>
            <tr class="<?= ($key % 2 == 0) ? 'even' : 'odd' ?>">
                <td align="center"><?= $row->no_rm ?></td> 
                <td><?= $row->nama ?></td>
                <td align="center"><?= $row->gender ?></td>
                <td align="center"><?= ($row->kunjungan > 1) ? 'Lama' : 'Baru' ?></td>  
                <td align="center"><?= ($row->lahir_tanggal != '0000-00-00')?datefrompg($row->lahir_tanggal):'-' ?></td>  
                <td><?= hitungUmur($row->lahir_tanggal) ?></td>
                <td align="center" class="aksi">
                    <span class="link_button" onclick="detail_rm('<?= $row->no_rm ?>')">Riwayat</span>
<!--                    <span class="link_button" onclick="cek_pendaftaran('<?= $row->no_rm ?>','Jalan')">Antri Poli</span>
                    <span class="link_button" onclick="cek_pendaftaran('<?= $row->no_rm ?>','Phone')">Antri Poli (Phone)</span>
                    <span class="link_button" onclick="cek_pendaftaran('<?= $row->no_rm ?>','IGD')">Kunj. IGD</span>-->
                </td> 
            </tr>
        <?php 
        if ($row->kunjungan > 1)  $lama++; else $baru++;
        endforeach; ?>
        </tbody>
    </table>
    <div><?= $paging ?></div>
    <?php 
    if (isset($status)) {?>

    
    <table align="right">
            <tr><td>Jumlah Pengunjung Baru :</td><td><?= $status->baru ?></td></tr>
            <tr><td>Jumlah Pengunjung Lama :</td><td><?= $status->lama ?></td></tr>
        </table>
    <br/><br/>
    <?php } else { ?>
    <div>
       <table>
            <tr>
                <td><b>Jumlah Pengunjung Baru : </b></td>
                <td><b><?= $baru ?></b></td>
            </tr>
            <tr>
                <td><b>Jumlah Pengunjung Lama : </b></td>
                <td><b><?= $lama ?></b></td>
            </tr>
        </table>
    </div>
    <?php } ?>
</div>
    <?php
} else {
    echo "Data pasien tidak ada<br/>";  
}
?>
<div id="rm">
    <div id="rekam_medis"></div>
</div>
<?php die; ?>