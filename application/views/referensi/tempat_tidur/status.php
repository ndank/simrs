<title><?= $title ?></title>
<div class="kegiatan">
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
    
    $(function() {
        $('#cari').each(function(){
            $(this).replaceWith('<button type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'">' + $(this).val() + '</button>');
        });
        $('#cari').button({icons: {secondary: 'ui-icon-search'}});
        $('#reset').button({icons: {secondary: 'ui-icon-refresh'}});
        $('button[id=cetak]').button({icons: {secondary: 'ui-icon-print'}});
        $('#awal,#akhir').datepicker({
            changeYear: true,
            changeMonth: true
        })
        $('#reset').click(function() {
            $('#loaddata').empty();
            $('#loaddata').load($.cookie('url'));
        })
       
        $('#statusbed').submit(function() {
            var url = $(this).attr('action');
            if ($('#awal').val() == '') {
                $('.msg').fadeIn('fast').html("Range tanggal tidak boleh kosong!");
                return false;
            };


            get_data(url, 1);
            return false;
        })
    });

    function get_data(url, page){
        $.ajax({
                type: 'GET',
                url: url,
                data: $('#statusbed').serialize()+'&page='+page,
                success: function(data) {
                    $('#loaddata').html(data);
                }
            })
            return false;
        
    }

    function paging(page, tab, cari){
        get_data('<?= base_url('referensi/status_tt') ?>?', page);
    }

    
</script>
<div class="titling"><h1><?= $title ?></h1></div>
<div class="data-input">
    <?= form_open('referensi/status_tt', 'id=statusbed') ?>
    <table width="100%" class="inputan">Parameter</legend>
        <div class="msg"></div>
        <tr><td>Nama Bangsal</td><td><?= form_dropdown('bangsal', $bangsal, isset($bangsal_cek)?$bangsal_cek:NULL, 'id=bangsal') ?>
        <tr><td>Kelas</td><td><?= form_dropdown('kelas', $kelas, isset($kelas_cek)?$kelas_cek:NULL, 'id=kelas') ?>
        <tr><td></td><td><?= form_submit('submit', 'Cari', 'id=cari') ?> <?= form_button(null, 'Reset', 'id=reset') ?>
    </table>
    <?= form_close() ?>
</div>
<div class="data-list">
    <?php if (isset($page)): ?>
        <div id="pencarian">
            <h3>
                <?= (($bangsal_cek !='')|($kelas_cek != ''))?"Pencarian Berdasarkan":"" ?> <?= ($bangsal_cek != '')?"Unit ".$bangsal[$bangsal_cek]:''?>
                <?= ($kelas_cek != '')?"Kelas ".$kelas_cek:''?>
            </h3>
        </div>
    <?php endif; ?>

    <?php if (isset($page)):?>
     <div id="resume">
        <h3>
            Halaman <?= $page ?> dari <?= (ceil($jumlah / $limit) == 0) ? 1 : ceil($jumlah / $limit) ?> (Total <?= $jumlah ?> data )
        </h3>
    </div>
    <?php endif; ?>
    <table class="tabel" width="100%">
        <thead>
            <tr>
                <th width="10">No.</th>
                <th width="25%">Bangsal</th>
                <th width="20%">Kelas</th>
                <th width="15%">Nomor Bed</th>
                <th width="15%">Status</th>
            </tr>
        </thead>
        <tbody>
        <?php if (isset($list_data)&&($list_data != null)) {
            $total = 0;
            foreach ($list_data as $key => $data) { ?>
            <tr class="<?= ($key%2==1)?'even':'odd' ?>">
                <td align="center"><?= (++$key + (($page - 1) * $limit)) ?></td>
                <td><?= $data->unit ?></td>
                <td align="center"><?= $data->kelas ?></td>
                <td align="center"><?= $data->nomor ?></td>
                <td align="center"><?= $data->jumlah ?></td>
            </tr>
        <?php } ?>
        
        <?php
        } else { ?>
        <?php for($i = 0; $i <= 1; $i++)  { ?>
            <tr class="<?= ($i%2==1)?'even':'odd' ?>">
                <td>&nbsp;</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        <?php } 
        }?>
        </tbody>
    </table>
    <br/>
    <?= isset($paging)?$paging:'' ?>
    
</div>
</div>