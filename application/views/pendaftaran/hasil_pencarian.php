<script>
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
    $(function(){
        $("button").button();
        $("#nama_unit").hide();  
    });
    
    function detail(no, id_antri){
        $.ajax({
            type : "GET",
            url: '<?= base_url('demografi/detail/') ?>/'+no+'/'+id_antri,
            cache: false,
            success: function(msg) {
                $('#loaddata').html(msg);                       
            }
        })
    }

    function pelengkap(data){
        var ps = data.split('#');
        $.post('<?= base_url() ?>demografi/new_pelengkap', {nama: ps[0], kelamin : ps[1], tgl_lahir : ps[2],telp : ps[3],id_antri : ps[4],id : ps[5], id_kelurahan : ps[6]},
        function(data){
            // $("#page").html(data);
            $("#loaddata").html('<div class=kegiatan>'+data+'</div>');
        }, '');
    }


</script>

<div class="data-list">
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
    <table class="tabel form-inputan" width="100%">
        <thead>
            <tr>
                <th width="5%">No.</th>
                <th>Jenis Layanan</th>
                <th width="10%">No. Antrian</th>
                <th width="20%">Nama Pasien</th>
                <th width="10%">No. RM</th>
                <th width="20%">Alamat Jalan</th>
                <th>Kelurahan</th>
                <th>#</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($pasien as $key => $row): ?>
            
            <tr class="<?= ($key % 2 == 0) ? 'even' : 'odd' ?>">
                <td align="center">
                    <?= $row->no_daftar ?>
                </td>
                <td align="center"><?= $row->nama_layanan ?></td>
                <td align="center"><?= $row->no_antri ?></td>
                <td><?= $row->nama_pasien ?></td>
                <td align="center"><?= $row->no_rm ?></td> 
                <td><?= $row->alamat_jalan_calon_pasien ?></td>  
                <td><?= $row->kelurahan ?></td>  
                <td align="center" class="aksi">
                    <?php if($row->no_rm !=''):?>
                    <span class="link_button" onclick="detail('<?= $row->no_rm ?>',<?= $row->no_daftar?>)">Konfirm</span>
                    <?php else:?>
                        <?php
                            $id_pdd = (isset($row->penduduk_id))?$row->penduduk_id:"";
                            $str = $row->nama_pasien."#".
                                    $row->gender."#".
                                    datefrompg($row->lahir_tanggal)."#".
                                    $row->telp_no."#".
                                    $row->no_daftar."#".
                                    $id_pdd."#".
                                    $row->id_kelurahan;
                        ?>

                    <span class="link_button" onclick="pelengkap('<?= $str?>')">Konfirm</span>
                    <?php endif; ?>
                </td> 

            </tr>
            <?php
        endforeach;
        if (isset($unit)) {
            echo '<div id="nama_unit">' . $unit . '</div>';
        }
        ?>
        </tbody>
    </table>
    <br/>
    <div id="paging"><?= $paging ?></div>

    <?php
} else {
    echo "Data pasien tidak ada";
}
?>
</div>
<?php die ?>
