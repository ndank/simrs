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
    $('#rl51').click(function() {
        window.open('<?= base_url('pelayanan/cetak_rl51') ?>','print RL 51','width=500px, height=300px, resizable=yes, scrollable=yes');
        return false;
    });

    $('.detail_kunjungan').click(function() {
        $('.tr').removeClass('selected');
        var no = $(this).attr('id');
        $('#tr'+no).addClass('selected');
    });
</script>
<div class="data-list">
    <h2>Hasil Cari Kunjungan</h2>
    <?= anchor('', 'RL 51', 'id=rl51') ?>
    <table width="100%" class="tabel">
        <thead>
            <tr>
                <th width="5%">No.</th>
                <th width="15%">Masuk</th>
                <th width="15%">Keluar</th>
                <th width="25%">Nama Pasien</th>
                <th width="35%">Wilayah</th>
                <th width="5%">#</th>
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
            <tr id="tr<?= $data->no_daftar ?>" class="tr">
                <td align="center"><?= anchor('pendaftaran/detail/'.$data->no_daftar, $data->no_daftar, 'class=detail_kunj title="Klik untuk melihat detail kunjungan"') ?></td>
                <td align="center"><?= datetime($data->arrive_time) ?></td>
                <td align="center"><?= datetime($data->waktu_keluar) ?></td>
                <td><?= $data->nama ?></td>
                <td><?= $data->kelurahan ?> <?= $data->kecamatan ?> <?= $data->kabupaten ?></td>
                <td align="center"><?= anchor('pelayanan/detail/'.$data->no_daftar.'/irna', 'Detail', 'id='.$data->no_daftar.' class=detail_kunjungan title="Klik untuk melihat detail pelayanan"') ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
    <br/>
    <?= $paging ?>
</div>