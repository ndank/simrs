<?php 
if (!isset($_GET['do'])) {
    $this->load->view('message');
}
?>
<title><?= $title ?></title>
<div class="titling"><h1><?= $title ?></h1></div>
<script>
    $(function() {
        $('#tabs').tabs();
        $('#suplier').autocomplete("<?= base_url('inv_autocomplete/load_data_instansi_relasi/supplier') ?>",
        {
            parse: function(data){
                var parsed = [];
                for (var i=0; i < data.length; i++) {
                    parsed[i] = {
                        data: data[i],
                        value: data[i].nama // nama field yang dicari
                    };
                }
                return parsed;
            },
            formatItem: function(data,i,max){
                var str = '<div class=result>'+data.nama+'<br/>'+data.alamat+'</div>';
                return str;
            },
            width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
            dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
        }).result(
        function(event,data,formated){
            $(this).attr('value',data.nama);
            $('input[name=id_suplier]').val(data.id);
        });
        $('#reset').click(function() {
            var url = $('#formlaporanhutang').attr('action');
            $('#loaddata').load(url);
        });
        $('#formlaporanhutang').submit(function() {
            var url = $(this).attr('action');
            $.ajax({
                type: 'GET',
                url: url,
                data: $(this).serialize(),
                success: function(data) {
                    $('#loaddata').html(data);
                }
            });
            return false;
        });
        $('#excel').click(function() {
            $('#loaddata').load(url);
        });
        $('button[id=reset]').button({
            icons: {
                secondary: 'ui-icon-refresh'
            }
        });
        $('button[id=cetak]').button({
            icons: {
                secondary: 'ui-icon-print'
            }
        });
        $('input[type=submit]').each(function(){
            $(this).replaceWith('<button type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'">' + $(this).val() + '</button>');
        });
        $('button[type=submit]').button({
            icons: {
                secondary: 'ui-icon-circle-check'
            }
        });
        $('#cetak').click(function() {
            var awal = '<?= isset($_GET['awal']) ? $_GET['awal'] : null ?>';
            var akhir = '<?= isset($_GET['awal']) ? $_GET['akhir'] : null ?>';
            location.href='<?= base_url('laporan/hutang') ?>?awal='+awal+'&akhir='+akhir+'&do=cetak';
        });
        $('#awal,#akhir').datetimepicker({
            changeYear: true,
            changeMonth: true
        });
    });
</script>
<div class="kegiatan">
    <?php
    $border = 0;
    if (isset($_GET['do'])) {
        header_excel("laporan-hutang_" . date("d-m-Y") . ".xls");
        $border = 1;
    }
    
        ?>
        <div id="tabs">
            <ul>
                <li><a href="#tabs-1">Parameter</a></li>
            </ul>
            <div id="tabs-1">
            <?= form_open('laporan/hutang', 'id=formlaporanhutang') ?>
            <table width="100%" class="inputan">
    
                <tr><td>Supplier:</td><td><?= form_input('supplier', isset($_GET['supplier'])?$_GET['supplier']:NULL, 'id=suplier size=40') ?>
                <?= form_hidden('id_suplier', isset($_GET['id_suplier'])?$_GET['id_suplier']:NULL) ?>
                <tr><td>Jatuh Tempo:</td><td><?= form_dropdown('tempo', array('Semua' => 'Semua tempo...', 'Ya' => 'Ya', 'Belum' => 'Belum'), isset($_GET['tempo'])?$_GET['tempo']:NULL) ?>
                <tr><td></td><td><?= form_submit(null, 'Cari', 'id=search') ?> <?= form_button('Reset', 'Reset', 'id=reset') ?> <!--<?= form_button(null, 'Cetak Excel', 'id=cetak') ?>-->
            </table>
            <?= form_close() ?>
            <table class="list-data" border="<?= $border ?>" width="100%">
                <thead>
                    <tr>
                        <th width="3%">No.</th>
                        <th width="10%">Tanggal</th>
                        <th width="10%">Jatuh Tempo</th>
                        <?php if (isset($_GET['tempo']) and $_GET['tempo'] === 'Belum') { ?>
                        <th width="10%">Tenggat Waktu</th>
                        <?php } ?>
                        <?php if (isset($_GET['tempo']) and $_GET['tempo'] === 'Ya') { ?>
                        <th width="10%">Terlambat</th>
                        <?php } ?>
                        <th width="35%">Supplier</th>
                        <th width="10%">No. Faktur</th>
                        <th width="10%">Total Nilai (Rp.)</th>
                        <th width="10%">Inkaso (Rp.)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $t_faktur = 0;
                    $t_inkaso = 0;
                    if (isset($_GET['id_suplier'])) {
                        //$utang = utang_get_data($_GET['awal'], $_GET['akhir']);
                        foreach ($list_data as $key => $data) {
                            $inkaso = $this->m_inventory->get_data_inkaso($data->id)->row();
                            $total_faktur = $data->total + ($data->total * ($data->ppn / 100));
                            $alert = "";
                            if ($data->jatuh_tempo <= date("Y-m-d") and ($total_faktur > $inkaso->inkaso)) {
                                $alert = "style='background: red; color: #fff;'";
                            }
                            ?>
                            <tr class="<?= ($key % 2 == 1) ? 'even' : 'odd' ?>">
                                <td align="center" <?= $alert ?>><?= ++$key ?></td>
                                <td align="center" <?= $alert ?>><?= datefmysql($data->tanggal) ?></td>
                                <td align="center" <?= $alert ?>><?= datefmysql($data->jatuh_tempo) ?></td>
                                <?php if (isset($_GET['tempo']) and $_GET['tempo'] === 'Belum') { ?>
                                <td><?= $data->tenggat ?> Hari lagi</td>
                                <?php } ?>
                                <?php if (isset($_GET['tempo']) and $_GET['tempo'] === 'Ya') { ?>
                                <td <?= $alert ?>><?= abs($data->tenggat) ?> Hari</td>
                                <?php } ?>
                                <td <?= $alert ?>><?= $data->nama ?></td>
                                <td align="center" <?= $alert ?>><?= $data->faktur ?></td>
                                <td align="right" <?= $alert ?>><?= rupiah($total_faktur) ?></td>
                                <td align="right" <?= $alert ?>><?= rupiah($inkaso->inkaso) ?></td>
                            </tr>
                            <?php
                            $t_faktur = $t_faktur + $total_faktur;
                            $t_inkaso = $t_inkaso + $inkaso->inkaso;
                        }
                    }
                    ?>
                </tbody>
                <tfoot>
                    <?php
                    $colspan = 5;
                    if (isset($_GET['tempo'])) {
                        if (($_GET['tempo'] === 'Ya') or $_GET['tempo'] === 'Belum') {
                            $colspan = 6;
                        } else {
                            $colspan = 5;
                        }
                    }
                    ?>
                    <tr class="odd">
                        <td colspan="<?= $colspan ?>" align="right"><b>Total</b></td>
                        <td align="right" style="font-weight: bold"><?= rupiah($t_faktur) ?></td>
                        <td align="right" style="font-weight: bold"><?= rupiah($t_inkaso) ?></td>
                    </tr>
                </tfoot>
            </table>
            <div style="font-weight: bold; font-size: 13px;">
                <table>
                    <tr><td>SISA HUTANG (Rp.) </td><td>:</td> <td><?= rupiah($t_faktur - $t_inkaso) ?></td></tr>
                    <tr><td>TOTAL TERBAYAR (Rp.)</td><td>:</td> <td><?= rupiah($t_inkaso) ?></td></tr>
                </table>

            </div>
    </div>
    </div>
</div>
<?php
if (isset($_GET['do'])) {
    die;
}
?>