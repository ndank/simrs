<?php if (!isset($_GET['do'])) { ?>
<script type="text/javascript" src="<?= base_url('assets/js/colResizable-1.3.min.js') ?>"></script>
<script type="text/javascript">
$(function() {
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
});
</script>
<?php 
}
$border = "";
if (isset($_GET['do'])) { 
    $border = "border=1";
    header_excel('rekap_kegiatan_rujukan.xls');
    echo "<center>Rekap Kegiatan Rujukan<br/>Tanggal ".indo_tgl(date2mysql($_GET['awal']))." s.d ".indo_tgl(date2mysql($_GET['akhir']))."</center>";
}
?>
<div class="data-list">
    <table class="tabel" width="100%" <?= $border ?>>
        <thead>
            <tr>
                <th width="10%">No.</th>
                <th width="80%">Jenis Spesialisasi</th>
                <th width="10%">Jumlah</th>
            </tr>
        </thead>
        <tbody>
        <?php if($list_data != null){?>
            <?php foreach ($list_data as $key => $data) {
                if ($data->jumlah > 0) {
                ?>
                <tr>
                    <td align="center"><?= ++$key ?></td>
                    <td><?= $data->nama ?></td>
                    <td align="center"><?= $data->jumlah ?></td>
                </tr>
            <?php } 
            } ?>
        <?php }else{?>
                 <tr>
                    <td align="center">&nbsp;</td>
                    <td></td>
                    <td align="center"></td>
                </tr>
                <tr>
                    <td align="center">&nbsp;</td>
                    <td></td>
                    <td align="center"></td>
                </tr>
        <?php } ?>
        </tbody>
    </table>
</div>