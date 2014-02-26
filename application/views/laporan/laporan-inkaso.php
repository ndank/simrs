<?php 
if (!isset($_GET['do'])) {
    $this->load->view('message');
}
?>
<title><?= $title ?></title>
<div class="kegiatan">
    <script>
        $(function() {
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
            })
            $('#formlaporanhutang').submit(function() {
                var url = $(this).attr('action');
                $.ajax({
                    type: 'GET',
                    url: url,
                    data: $(this).serialize(),
                    success: function(data) {
                        $('#loaddata').html(data);
                    }
                })
                return false;
            })
            $('#excel').click(function() {
                $('#loaddata').load(url);
            })
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
            })
            $('#awal,#akhir').datepicker({
                changeYear: true,
                changeMonth: true
            })
        })
    </script>
    <div class="titling"><h1><?= $title ?></h1></div>
    <?php
    $border = 0;
    if (isset($_GET['do'])) {
        header_excel("laporan-hutang_" . date("d-m-Y") . ".xls");
        $border = 1;
    }
    if (!isset($_GET['do'])) {
        ?>
        <div class="data-input">
            <table width="100%" class="inputan">Parameter Pencarian</legend>
    <?= form_open('laporan/inkaso', 'id=formlaporanhutang') ?>
                <tr><td>Tanggal Pembayaran:</td><td> <?= form_input('awal', isset($_GET['awal']) ? $_GET['awal'] : NULL, 'id=awal size=15') ?> <span class="label"> s . d </span> <?= form_input('akhir', isset($_GET['awal']) ? $_GET['akhir'] : NULL, 'id=akhir size=15') ?>
                <tr><td>No. Faktur:</td><td><?= form_input('nofaktur', isset($_GET['supplier'])?$_GET['nofaktur']:NULL, 'id=nofaktur size=40') ?>
                <tr><td>Supplier:</td><td><?= form_input('supplier', isset($_GET['supplier'])?$_GET['supplier']:NULL, 'id=suplier size=40') ?>
                <?= form_hidden('id_suplier', isset($_GET['id_suplier'])?$_GET['id_suplier']:NULL) ?>
                <tr><td></td><td><?= form_submit(null, 'Cari', 'id=search') ?> <?= form_button('Reset', 'Reset', 'id=reset') ?> <!--<?= form_button(null, 'Cetak Excel', 'id=cetak') ?>-->
    <?= form_close() ?>
            </table>
        </div>
<?php } ?>
    <div class="data-list">
        <table class="tabel" border="<?= $border ?>" width="100%">
            <thead>
                <tr>
                    <th width="3%">No.</th>
                    <th width="10%">Jatuh Tempo</th>
                    <th width="44%">Supplier</th>
                    <th width="3%">No. Faktur</th>
                    <th width="10%">Total Faktur (Rp.)</th>
                    <th width="10%">Tanggal<br/> Pembayaran</th>
                    <th width="10%">Inkaso (Rp.)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $t_faktur = 0;
                $t_inkaso = 0;
                $faktur = "";
                $no = 1;
                if (isset($_GET['id_suplier'])) {
                    //$utang = utang_get_data($_GET['awal'], $_GET['akhir']);
                    foreach ($list_data as $key => $data) {
                        ?>
                        <tr class="<?= ($key % 2 == 1) ? 'even' : 'odd' ?>">
                            <td align="center"><?= ($faktur !== $data->dokumen_no)?$no:NULL ?></td>
                            <td align="center"><?= ($faktur !== $data->dokumen_no)?datefmysql($data->tempo):NULL ?></td>
                            <td><?= ($faktur !== $data->dokumen_no)?$data->nama:NULL ?></td>
                            <td><?= ($faktur !== $data->dokumen_no)?$data->dokumen_no:NULL ?></td>
                            <td align="right"><?= ($faktur !== $data->dokumen_no)?rupiah($data->total):NULL ?></td>
                            <td align="center"><?= datetimefmysql($data->waktu) ?></td>
                            <td align="right"><?= rupiah($data->jumlah_bayar) ?></td>
                        </tr>
                        <?php
                        
                        $t_inkaso = $t_inkaso + $data->jumlah_bayar;
                        if ($faktur !== $data->dokumen_no) {
                            $t_faktur = $t_faktur + $data->total;
                            $no++;
                        }
                        $faktur = $data->dokumen_no;
                    }
                }
                ?>
            </tbody>
            <tfoot>
                <tr class="odd">
                    <td colspan="4" align="right"><b>Total</b></td>
                    <td align="right" style="font-weight: bold"><?= rupiah($t_faktur) ?></td>
                    <td></td>
                    <td align="right" style="font-weight: bold"><?= rupiah($t_inkaso) ?></td>
                </tr>
            </tfoot>
        </table>
        <div style="font-weight: bold; font-size: 13px;">
<!--            <table>
                <tr><td>SISA HUTANG (Rp.) </td><td>:</td> <td><?= rupiah($t_faktur - $t_inkaso) ?></td></tr>
                <tr><td>TOTAL TERBAYAR (Rp.)</td><td>:</td> <td><?= rupiah($t_inkaso) ?></td></tr>
            </table>-->

        </div>
    </div>
</div>
<?php
if (isset($_GET['do'])) {
    die;
}
?>