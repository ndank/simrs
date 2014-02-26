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
    function edit_gol_sebab_sakit(str) {
        var val = str.split('#');
        $('input[name=id_gol]').val(val[0]);
        $('#no_dtd').val(val[1]);
        $('#no_daftar').val(val[2]);
        $('#nama').val(val[3]);
    }
</script>
<div class="data-list">
    <table class="list-data" width="100%">
        <thead>
            <tr>
                <th width="5%">ID</th>
                <th width="10%">No. DTD</th>
                <th width="10%">Kode ICD X</th>
                <th width="20%">Nama</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($gol_sebab_sakit as $key => $data) { 
        $str = $data->id."#".$data->no_dtd."#".$data->no_daftar_terperinci."#".$data->nama;
        ?>
        <tr class="<?= ($key%2==0)?'even':'odd' ?>">
            <td align="center"><span style="border-bottom: 1px dotted #000; cursor: pointer;" href="" onclick="edit_gol_sebab_sakit('<?= $str ?>')"><?= $data->id ?></span></td>
            <td><?= $data->no_dtd ?></td>
            <td><?= $data->no_daftar_terperinci ?></td>
            <td><?= $data->nama ?></td>
        </tr>
        <?php } ?>
        </tbody>
    </table>
    <br/>
    <?= $paging ?>
</div>