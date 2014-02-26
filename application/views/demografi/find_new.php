<script>
    var Dtgl_lahir;
    var Dnama;
    $(function(){
        $('#antrian').button({
            icons: {
                secondary: 'ui-icon-circle-plus'
            }
        });

        $('#antrian').click(function(){
            antri("");
        });
    });

    function cek_pendaftaran(no_rm){
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
                    antri(no_rm);
                }
            }
        });        
    }

    function antri(no_rm){
        var Dusia = $("#usia option:selected").val();
        if($('#nama').val()!=''){
                Dnama = $('#nama').val();
        }else{
                Dnama = $('#nama_pdd').val();
        }
        if (Dusia == 'umur'){               
            Dtgl_lahir = birthByAge($("#umur").val());                
        }else{               
            Dtgl_lahir = $("#tgl_lahir").val()
        }
        $.ajax({
            url: '<?= base_url("demografi/antrian_kunjungan") ?>',
            type : 'POST',
            data : $('#formnew').serialize()+"&nama_antri="+Dnama+"&tgl_lahir_antri="+Dtgl_lahir+"&no_rm="+no_rm,
            cache: false,
            success: function(data) {
                $('#loaddata').html(data);
            
            },
            error: function(){
                custom_message('Kesalahan Koneksi','Koneksi jaringan bermasalah, mohon cek!');
            }
        });
    }
</script>

<div class="data-list">
    <?php
    if ($pasien != null) {
        //echo form_button('', 'Antri Calon Pasien', 'id = antrian');
        ?>

        <table class="tabel" width="100%">
            <tr>
                <th>No. RM</th>
                <th>Nama Pasien</th>
                <th>Jenis Kelamin</th>
                <th>Tanggal Lahir</th>
                <th>#</th>
            </tr>
            <?php foreach ($pasien as $key => $row): ?>
                <tr class="<?= ($key % 2 == 0) ? 'even' : 'odd' ?>">
                    <td align="center"><?= $row->no_rm ?></td> 
                    <td><?= $row->nama ?></td>
                    <td align="center"><?= ($row->gender == 'L') ? 'Laki-laki' : 'Perempuan' ?></td>  
                    <td align="center"><?= datefrompg($row->lahir_tanggal) ?></td>  
                    <td align="center" class="aksi"><span title="Entry antrian" class="detail" onclick="cek_pendaftaran('<?= $row->no_rm ?>')">&nbsp;</span></td> 
                </tr>
            <?php endforeach; ?>
        </table>
        <?php
    } else {
        echo "Data Calon Pasien tidak ditemukan !<br/>";
        echo form_button('', 'Antri Calon Pasien', 'id = antrian');
    }
    ?>
</div>
<?php die; ?>