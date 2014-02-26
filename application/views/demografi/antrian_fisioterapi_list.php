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


    function confirm(id){
        $.ajax({
            type : 'GET',
            url: '<?= base_url("demografi/antrian_fisioterapi_detail") ?>/'+id,
            cache: false,
            success: function(data) {
                $('#loaddata').html(data);
            }
        });
    }

    function entry_penduduk(id){
        $.ajax({
            type : 'GET',
            url: '<?= base_url("demografi/antrian_fisioterapi_entry_penduduk") ?>/'+id,
            cache: false,
            success: function(data) {
                $('#loaddata').html(data);
            }
        });
    }
   
</script>

<div class="data-list">
<?php
if ($customer != null) {
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
                <th width="10%">No. Antrian</th>
                <th width="30%">Nama Pasien</th>
                <th width="30%">Alamat Jalan</th>
                <th>Kelurahan</th>
                <th>Konfirm</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($customer as $key => $row): ?>
            
            <tr class="<?= ($key % 2 == 0) ? 'even' : 'odd' ?>">
                <td align="center"><?= (++$key + (($page - 1) * $limit)) ?></td>
                <td align="center"><?= $row->no_antri ?></td>
                <td><?= $row->nama_calon_pasien ?></td>
                <td><?= $row->alamat_jalan_calon_pasien ?></td>  
                <td><?= $row->kelurahan ?></td>  
                <td align="center" class="aksi">
                    <?php if($row->penduduk_id != null):?>
                        <span class="link_button" onclick="confirm('<?= $row->id ?>')">Konfirm</span>
                    <?php else: ?>
                        <span class="link_button" onclick="entry_penduduk('<?= $row->id ?>')">Entry Penduduk</span>
                    <?php endif; ?>
                </td> 

            </tr>
            <?php
        endforeach;
        ?>
        </tbody>
    </table>
    <br/>
    <div id="paging"><?= $paging ?></div>

    <?php
} else {
    echo "Data tidak ada";
}
?>
</div>
<?php die ?>
