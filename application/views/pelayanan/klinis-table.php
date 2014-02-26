<script type="text/javascript">
    $('#rl51').click(function() {
        window.open('<?= base_url("pelayanan/cetak_rl5_1") ?>','print RL 51','width=500px, height=300px, resizable=yes, scrollable=yes');
        return false;
    });
    $('.detail_kunjungan').click(function() {
        $('.tr').removeClass('selected');
        var no = $(this).attr('id');
        $('#tr'+no).addClass('selected');
    });
    function detail_pelayanan(no_daftar, no_rm, nama) {
        var str = '<div class="pelayanan_kunjungan">'+
                    '<div class="pelayanan_kunjungan_content"></div>'+
                  '</div>';
        $(str).dialog({
            autoOpen: true,
            title :'Detail Pelayanan Pasien',
            height: $(window).height() - 10,
            width: $(window).width() - 20,
            modal: true,
            buttons: { 
                "Ok": function() { 
                    $( this ).dialog('close');
                }
            },
            open: function() {
                var url = '<?= base_url("pelayanan/detail") ?>/'+no_daftar;
                $.ajax({
                    url: url,
                    data: 'no_rm='+no_rm+'&nama='+nama,
                    cache: false,
                    success: function(data) {
                        $('.pelayanan_kunjungan_content').html(data);
                    }
                });
            }
        });
    }
</script>
<div class="data-list">
    <b>Hasil Cari Kunjungan</b>
    <table width="100%" class="list-data">
        <thead>
            <tr>
                <th width="3%">ID</th>
                <th width="7%">No. RM</th>
                <th width="13%">Masuk</th>
                <th width="13%">Keluar</th>
                <th width="20%">Nama Pasien</th>
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
                <?php if($data->no_rm !== 'Pasien Luar'): ?>
                    <td align="center"><?= anchor('pendaftaran/detail/'.$data->no_daftar, $data->no_daftar, 'class=detail_kunj title="Klik untuk melihat detail kunjungan"') ?></td>
                <?php else: ?>
                    <td align="center"><?= $data->no_daftar ?></td>
                <?php endif; ?>
                
                <td align="center"><?= str_pad($data->no_rm, 6, "0", STR_PAD_LEFT);  ?></td>
                <td align="center"><?= datetime($data->arrive_time) ?></td>
                <td align="center"><?= datetime($data->waktu_keluar) ?></td>
                <td><?= $data->nama ?></td>
                <td><?= $data->kelurahan ?> <?= $data->kecamatan ?> <?= $data->kabupaten ?></td>
                <td align="center">
                    <span onclick="detail_pelayanan('<?= $data->no_daftar ?>', '<?= $data->no_rm?>', '<?= $data->nama ?>')" class="link_button detail_kunjungan" title="Klik untuk melihat detail pelayanan" id="<?= $data->no_daftar ?>">Detail</span>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
    <br/>
    <?= $paging ?>
</div>