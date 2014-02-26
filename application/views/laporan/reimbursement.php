<script type="text/javascript">
$(function() {
    $('#awal,#akhir').datepicker({
        changeYear: true,
        changeMonth: true
    })
    $('button[id=reset]').button({
        icons: {
            secondary: 'ui-icon-refresh'
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
    $('button[id=cetak]').button({
        icons: {
            secondary: 'ui-icon-print'
        }
    });
    $('#reset').click(function() {
        var url = '<?= base_url('laporan/reimbursement') ?>';
        $('#loaddata').load(url);
    })
    $('#suplier').autocomplete("<?= base_url('inv_autocomplete/load_data_instansi_relasi/Asuransi') ?>",
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
        $('input[name=id_asuransi]').attr('value',data.id);
    });
    $('#cetak').click(function() {
        var awal = '<?= isset($_GET['awal'])?$_GET['awal']:null ?>';
        var akhir = '<?= isset($_GET['awal'])?$_GET['akhir']:null ?>';
        var id_as = '<?= isset($_GET['awal'])?$_GET['id_asuransi']:null ?>';
        var asu = '<?= isset($_GET['awal'])?$_GET['asuransi']:null ?>';
        location.href='<?= base_url('transaksi/laporan-reimbursement') ?>?awal='+awal+'&akhir='+akhir+'&asuransi='+asu+'&id_asuransi='+id_as+'&do=cetak';
    })
    $('#form_reimbursement').submit(function() {
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
})
</script>
<title><?= $title ?></title>
<?php
$border = null;
if (isset($_GET['do'])) {
    echo "<table width='100%'><tr><td colspan=5 align=center><b>LAPORAN REIMBURSEMENT <br/>TANGGAL $_GET[awal] s/d $_GET[akhir] <br/> ".strtoupper($_GET['asuransi'])."</b></td></tr></table>";
    header_excel("reimbursement-".$_GET['awal']." sd".$_GET['akhir'].".xls");
    $border = "border=1";
}
?>
<div class="kegiatan">
    <div class="titling"><h1><?= $title ?></h1></div>
    <?php
    if (!isset($_GET['do'])) {
    ?>
    <div class="data-input">
        <?= form_open('laporan/reimbursement', 'id=form_reimbursement') ?>
        <table width="100%" class="inputan">Parameter</legend>
            <tr><td>Tanggal</td><td> <?= form_input('awal', isset($_GET['awal'])?$_GET['awal']:date("d/m/Y"), 'id=awal size=10') ?> <span class="label">s . d </span> <?= form_input('akhir', isset($_GET['awal'])?$_GET['akhir']:date("d/m/Y"), 'id=akhir size=10') ?>
            <tr><td> Produk Asuransi</td><td><?= form_input('asuransi', isset($_GET['asuransi'])?$_GET['asuransi']:null, 'id=suplier size=40') ?><?= form_hidden('id_asuransi', isset($_GET['asuransi'])?$_GET['id_asuransi']:null, 'id=id_asuransi') ?>
            <tr><td></td><td><?= form_submit(null, 'Cari', 'id=search') ?> <?= form_button('Reset', 'Reset','id=reset') ?> <?= form_button(null, 'Cetak Excel', 'id=cetak') ?>
        </table>
        <?= form_close() ?>
    </div>
    <?php } ?>
    <div class="data-list">
        <table class="tabel" width="100%" <?= $border ?>>
            <thead>
            <tr>
                <th width="10%">No. Nota</th>
                <th width="10%">Waktu</th>
                <th width="20%">Pasien</th>
                <th width="10%">No. Polis</th>
                <th width="10%">Total Tagihan</th>
                <th width="10%">Nominal Tereimburse</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if (isset($_GET['awal'])) {
            $total = 0;
            foreach ($list_data as $key => $data) { 
            $subtotal = $data->nominal_tereimburse;
            //$total_faktur = ($data->total+$data->materai)+($data->total*($data->ppn/100));    
            ?>
            <tr class="<?= ($key%2==1)?'even':'odd' ?>">
                <td align="center"><?= $data->id ?></td>
                <td align="center"><?= datetimefmysql($data->waktu) ?></td>
                <td><?= $data->pasien ?></td>
                <td><?= ($data->no_polis == '0')?'-':$data->no_polis ?></td>
                <td align="right"><?= rupiah($data->total) ?></td>
                <td align="right"><?= rupiah($subtotal) ?></td>
            </tr>
            <?php 
            $total = $total+$subtotal;
                } 
            } ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" align="right">Total Reimburse</td>
                    <td align="right" colspan="3" style="font-weight: bold"><?= (isset($_GET['awal'])?rupiah($total):null) ?></td>
                </tr>
            </tfoot>
        </table>
        
    </div>
</div>