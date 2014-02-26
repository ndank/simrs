<?php
$total = 0;
if (count($list_data) == 0) {
    for($i = 0; $i <= 1; $i++) { ?>
    <script>
        add(<?= $i ?>);
    </script>
    <?php }
} else {
    foreach ($list_data as $key => $rows) { ?>
    <tr class="<?= ($key%2==1)?'even':'odd' ?> tr_row">
        <td><?= $rows->nama ?></td>
        <td><?= $rows->posisi ?></td>
        <td align="center"><?= rupiah($rows->nominal) ?></td>+
        <td class=aksi><a style="cursor: pointer" id="<?= $rows->id ?>" title="<?= $rows->nama ?> <?= $rows->posisi ?>" class="delete drop"></a></td>
    </tr>
    <?php 
    $total = $total + $rows->nominal;
    } 
}
?>
<script>
    $('#total').html(numberToCurrency(<?= $total ?>));
    $('.drop').click(function() {
        var title = $(this).attr('title');
        var ok = confirm('Anda yakin akan menghapus data '+title+' ?');
        if (!ok) {
            return false;
        } else {
            var id = $(this).attr('id');
            var id_layanan = $('input[name=id_layanan]').val();
            $.ajax({
                url: '<?= base_url('referensi/layanan_profesi_delete') ?>/'+id+'/'+id_layanan,
                cache: false,
                success: function(msg) {
                    $('.form-inputan tbody').html(msg);
                }
            })
        }
    })
</script>