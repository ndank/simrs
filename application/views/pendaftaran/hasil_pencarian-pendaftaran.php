<script>
    function detail(no){
        $.ajax({
            url: '<?= base_url('pendaftaran/detail/') ?>/'+no,
            data: '',
            cache: false,
            success: function(msg) {
                $('#loaddata').html(msg);
                       
            }
        })
    }

</script>
<?php
if ($pasien != null) {
    ?>
    <table style="text-align: center">
        <tr>
            <th>No. pendaftaran</th>
            <th>Unit layanan</th>
            <th>Tgl. pelayanan</th>
            <th>No. Antrian</th>
            <th>Nama Pasien</th>
            <th>No. RM</th>   
            <th>Kelamin</th>
            <th>Tanggal Lahir</th>
            <th>Aksi</th>
        </tr>
        <?php foreach ($pasien as $row): ?>
            <tr>
                <td><?= $row->no_daftar ?></td>
                <td><?= $row->unit_layanan ?></td>
                <td><?= $row->tgl_layan ?></td>
                <td><?= $row->no_antri ?></td>
                <td><?= $row->nama ?></td>
                <td><?= $row->no_rm ?></td> 
                <td><?= $row->kelamin ?></td>  
                <td><?= $row->tgl_lahir ?></td>  
                <td align="center"><span class="delete" onclick="detail('<?= $row->no_daftar ?>')">Detail</span></td>
            </tr>
        <?php endforeach; ?>
    </table>
    <?php
} else {
    echo "Data pasien tidak ada";
}
?>
<?php die ?>