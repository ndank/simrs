<title><?= $title ?></title>
<div class="titling"><h1><?= $title ?></h1></div>
<div class="kegiatan">
<script type="text/javascript">
    $(function() {
        $('#tabs').tabs();
        $('#cari').each(function(){
            $(this).replaceWith('<button type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'">' + $(this).val() + '</button>');
        });
        $('#cari').button({
            icons: {
                secondary: 'ui-icon-circle-check'
            }
        });
        $('#reset').button({
            icons: {
                secondary: 'ui-icon-refresh'
            }
        });
        $('#awal,#akhir').datepicker({
            changeYear: true,
            changeMonth: true
        })
        $('#reset').click(function() {
            $('#loaddata').empty();
            $('#loaddata').load('<?= base_url('laporan/penjualan_jasa') ?>?_='+Math.random());
        })
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
        $('#penjualan_jasa').submit(function() {
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
</script>
<div id="tabs">
        <ul>
            <li><a href="#tabs-1">Laporan</a></li>
        </ul>
        <div id="tabs-1">
            <?= form_open('laporan/penjualan_jasa', 'id=penjualan_jasa') ?>
            <table width="100%" class="inputan">
                <tr><td>Waktu</td><td><?= form_input('awal', isset($_GET['awal'])?$_GET['awal']:date("d/m/Y"), 'id=awal size=10 style="width: 75px;"') ?> <span class="label"> s.d </span><?= form_input('akhir', isset($_GET['akhir'])?$_GET['akhir']:date("d/m/Y"), 'id=akhir size=10 style="width: 75px;"') ?>
                <tr><td>Nama Pegawai (Nakes)</td><td><?= form_input('nakes', isset($_GET['nakes'])?$_GET['nakes']:null, 'id=nakes size=32') ?> <?= form_hidden('id_nakes', isset($_GET['id_nakes'])?$_GET['id_nakes']:null) ?>
                <tr><td></td><td>
                <?= form_submit('submit', 'Cari', 'id=cari') ?> <?= form_button(null, 'Reset', 'id=reset') ?>
            </table>
            <?= form_close() ?>

            <div class="data-list">
                <table class="list-data" width="100%">
                    <tr>
                        <th width="5%">ID. Kunj.</th>
                        <th width="7%">No. RM</th>
                        <th width="20%">Nakes</th>
                        <th width="10%">Waktu</th>
                        <th width="40%">Nama Tarif</th>
                        <th width="5%">Nominal</th>
                        <th width="5%">Freq</th>
                        <th width="5%">Sub Total</th>
                    </tr>
                    <?php if (isset($_GET['awal'])) {
                        $total = 0;
                        $no = "";
                        foreach ($list_data as $key => $data) {?>
                    <tr class="<?= ($key%2==1)?'even':'odd' ?>">
                        <td align="center"><?= ($no !== $data->no_daftar)?$data->no_daftar:NULL ?></td>
                        <td align="center"><?= $data->no_rm ?></td>
                        <td><?= $data->pegawai ?></td>
                        <td align="center"><?= datetime($data->waktu) ?></td>
                        <td><?= $data->layanan ?></td>
                        <td align="right"><?= rupiah($data->jasa_nakes) ?></td>
                        <td align="center"><?= $data->frekuensi ?></td>
                        <td align="right"><?= rupiah($data->jasa_nakes*$data->frekuensi) ?></td>
                    </tr>
                    <?php 
                        $no = $data->no_daftar;
                        $total = $total + ($data->jasa_nakes*$data->frekuensi);
                        } ?>
                    <tr>
                        <td colspan="7" align="right"><b>Total Jasa Nakes<b/></td><td align="right"><b><?= rupiah($total) ?></b></td>
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
                            <td></td>
                            <td></td>
                        </tr>
                    <?php } 
                    }?>
                </table>
            </div>
        </div>
</div>
</div>