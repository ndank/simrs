<script type="text/javascript" src="<?= base_url('assets/js/colResizable-1.3.min.js') ?>"></script>
<script type="text/javascript">
    $('table').tablesorter();
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

    $('.detail_kunjungan').click(function() {
        $('.tr').removeClass('selected');
        var no = $(this).attr('id');
        $('#tr'+no).addClass('selected');
    });
 
    
</script>
<div class="data-list">
    <b>Hasil Cari Pelayanan Kunjungan</b>
    <table width="100%" class="list-data">
        <thead>
            <tr>
                <th width="10%">ID</th>
                <th width="8%">No. RM</th>
                <th width="20%">Nama Pasien</th>
                <th width="15%">Unit</th>
                <th width="15%">Kelas</th>
                <th width="15%">No. TT</th>
                <th width="20%">DPJP</th>
                <th width="10%">#</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data_list as $key => $data) {
                if ($data->jenis_rawat == 'IGD') {
                    $jenis = "igd";
                } else {
                    $jenis = "poliklinik";
                }
                ?>
            <tr id="tr<?= $data->id_pk ?>" class="tr">
                <?php if($data->no_rm !== 'Pasien Luar'): ?>
                    <td align="center"><?= anchor('pendaftaran/detail/'.$data->no_daftar, $data->no_daftar, 'class=detail_kunj') ?></td>
                <?php else: ?>
                    <td align="center"><?= $data->no_daftar ?></td>
                <?php endif; ?>
                
                <td align="center"><?= str_pad($data->no_rm, 6, "0", STR_PAD_LEFT);  ?></td>
                <td><?= $data->nama ?></td>
                <td align="center"><?= $data->unit ?></td>
                <td align="center"><?= $data->kelas ?></td>
                <td align="center"><?= $data->no_tt ?></td>
                <td><?= $data->dpjp ?></td>
                <td align="center"><span id="<?= $data->id_pk ?>" class="link_button detail_kunjungan" onclick="detail_pemeriksaan('<?= $data->id_pk ?>','<?= $pemeriksaan ?>')">detail</span></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
    <br/>
    <?= $paging ?>
</div>