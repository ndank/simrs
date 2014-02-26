<title><?= $title ?></title>
<div class="titling"><h1><?= $title ?></h1></div>
<div class="kegiatan">
<script type="text/javascript">
    $(function() {
        $('#tabs').tabs();
        $('#cari').button({icons: {secondary: 'ui-icon-circle-check'}});
        $('#reset').button({icons: {secondary: 'ui-icon-refresh'}});
        $("a[href='#top']").click(function() {
            $("#loaddata").animate({ scrollTop: 0 }, "slow");
            return false;
        });
        $('#awal,#akhir').datepicker({
            changeYear: true,
            changeMonth: true
        });
        $('#reset').click(function() {
            $('#loaddata').empty();
            $('#loaddata').load('<?= base_url("laporan/rekap_pendapatan") ?>?_='+Math.random());
        });
        $('#nakes').autocomplete("<?= base_url('inv_autocomplete/load_data_profesi_by_nakes') ?>",
        {
            parse: function(data){
                var parsed = [];
                for (var i=0; i < data.length; i++) {
                    parsed[i] = {
                        data: data[i],
                        value: data[i].nama // nama field yang dicari
                    };
                }
                $('input[name=id_nakes]').val('');
                return parsed;
            },
            formatItem: function(data,i,max){
                var str = '<div class=result>'+data.nama+' - '+data.nip+'</div>';
                return str;
            },
            width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
            dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
        }).result(
        function(event,data,formated){
            $(this).val(data.nama+' - '+data.nip);
            $('input[name=id_nakes]').val(data.id);
        });
        
    });

    function load_penjualan_jasa(no_daftar){
         $.ajax({
                type: 'GET',
                url: '<?= base_url ()?>pelayanan/penjualan_jasa/'+no_daftar,
                cache : false,
                success: function(data) {
                    $('#loaddata').html(data);
                }
            })
    }

    function get_pendapatan_list(p){
        $.ajax({
            type: 'GET',
            url:'<?= base_url ()?>laporan/rekap_pendapatan/',
            data: $('#penjualan_jasa').serialize(),
            success: function(data) {
                $('#loaddata').html(data);
            }
        });
    }

    function paging(page, tab, cari){
            get_pendapatan_list(page);
        }
</script>

<div id="tabs">
    <ul>
        <li><a href="#tabs-1">Laporan</a></li>
    </ul>
    <div id="tabs-1">
    <?= form_open('laporan/rekap_pendapatan', 'id=penjualan_jasa') ?>
        <table width="100%" class="inputan">
            <tr><td>Tanggal:</td><td><?= form_input('awal', isset($_GET['awal'])?$_GET['awal']:date("d/m/Y"), ' style="width: 90px;" id=awal size=10') ?> <span class="label"> s.d </span><?= form_input('akhir', isset($_GET['akhir'])?$_GET['akhir']:date("d/m/Y"), 'id=akhir size=10 style="width: 90px;"') ?></td></tr>
            <tr><td>Unit Layanan:</td><td><?= form_dropdown('id_layanan', $layanan, isset($_GET['id_layanan'])?$_GET['id_layanan']:NULL, 'id=layanan class="standar enter"') ?></td></tr>
            <tr><td></td><td><?= form_button('submit', 'Cari', 'id=cari onclick=get_pendapatan_list(1)') ?> <?= form_button(null, 'Reset', 'id=reset') ?></td></tr>
        </table>
    <?= form_close() ?>

        <div class="data-list">
            <table class="inputan" width="100%">
                <tr>
                    <td>Total Jasa Sarana:</td>
                    <td><?= isset($sum)?rupiah($sum->sum_jasa_sarana):'' ?></td>
                </tr>
                <tr>
                    <td>Total Jasa Tindakan RS:</td>
                    <td><?= isset($sum)?rupiah($sum->sum_tindakan_rs):'' ?></td>
                </tr>
                <tr>
                    <td>Total BHP:</td>
                    <td><?= isset($sum)?rupiah($sum->sum_bhp):'' ?></td>
                </tr>
            </table>
            <table class="list-data" width="100%">
                <tr>
                    <th width="5%">ID. Kunj.</th>
                    <th width="10%">Waktu</th>
                    <th width="30%">Nama Tarif</th>
                    <th width="5%">Jasa Sarana</th>
                    <th width="5%">Jasa RS</th>
                    <th width="5%">BHP</th>
                    <!-- <th>Sub Total</th> -->
                </tr>
                <?php if (isset($_GET['awal'])) {
                    $total = 0;
                    $total_sarana = 0;
                    $total_tindakan = 0;
                    $total_bhp = 0;
                    foreach ($list_data as $key => $data) {
                            $total_sarana += $data->jasa_sarana;
                            $total_tindakan += $data->jasa_tindakan_rs;
                            $total_bhp += $data->bhp;
                        ?>
                <tr class="<?= ($key%2==1)?'even':'odd' ?>">
                    <td align="center">
                        <?php if($data->no_rm !== null): ?>
                        <span class="link_button" onclick="load_penjualan_jasa('<?= $data->no_daftar ?>')"><?= $data->no_daftar ?></span>
                        <?php else:?>
                        <span><?= $data->no_daftar ?></span>
                        <?php endif; ?>
                    </td>
                    <td align="center"><?= datetime($data->waktu) ?></td>
                    <td><?= $data->nama_tarif ?></td>
                    <td align="right"><?= rupiah($data->jasa_sarana) ?></td>
                    <td align="right"><?= rupiah($data->jasa_tindakan_rs) ?></td>
                    <td align="right"><?= rupiah($data->bhp) ?></td>
                    <!-- <td align="right"><?= rupiah($data->jasa_nakes*$data->frekuensi) ?></td> -->
                </tr>
                <?php 

                    //$total = $total + ($data->jasa_nakes*$data->frekuensi);
                    } ?>
                <tr>
                    <td colspan="3" align="right"><b>Total<b/></td>
                    <td align="right"><b><?= rupiah($total_sarana) ?></b></td>
                    <td align="right"><b><?= rupiah($total_tindakan) ?></b></td>
                    <td align="right"><b><?= rupiah($total_bhp) ?></b></td>
                    <!-- <td align="right"><b><?= rupiah($total) ?></b></td> -->
                </tr>
                <?php
                } else { ?>
                <?php for($i = 0; $i <= 1; $i++)  { ?>
                    <tr class="<?= ($i%2==1)?'even':'odd' ?>">
                        <td>&nbsp;</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                <?php } 
                }?>
            </table>
            <br/><br/>
            <a href="#top" title="Back to top">Kembali ke atas</a>

        </div>
    </div>
</div>