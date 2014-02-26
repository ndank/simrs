<title><?= $title ?></title>
<div class="titling"><h1><?= $title ?></h1></div>
<div class="kegiatan">
<script type="text/javascript">
    $("table").tablesorter();
    var onSampleResized = function(e){
            var columns = $(e.currentTarget).find("th");
            var msg = "columns widths: ";
            columns.each(function(){ msg += $(this).width() + "px; "; });
    };
    $(".list-data").colResizable({
        liveDrag:true,
        gripInnerHtml:"<div class='grip'></div>", 
        draggingClass:"dragging", 
        onResize:onSampleResized
    });
    
    $(function() {
        $('#tabs').tabs();
        $('#cari').each(function(){
            $(this).replaceWith('<button type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'">' + $(this).val() + '</button>');
        });
        $('#cari').button({icons: {secondary: 'ui-icon-circle-check'}});
        $('#reset').button({icons: {secondary: 'ui-icon-refresh'}});
        $('button[id=cetak]').button({icons: {secondary: 'ui-icon-print'}});
        $('#awal,#akhir').datepicker({
            changeYear: true,
            changeMonth: true
        })
        $('#reset').click(function() {
            $('#loaddata').empty();
            $('#loaddata').load('<?= base_url('laporan/laporan_penjualan') ?>?_='+Math.random());
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
            $('#profesi').html(data.profesi);
            $('input[name=profesi]').val(data.profesi);
        });
        $('#penjualan_jasa').submit(function() {
            var url = $(this).attr('action');
            if ($('#awal').val() == '') {
                $('.msg').fadeIn('fast').html("Range tanggal tidak boleh kosong!");
                return false;
            };

            if ($('#akhir').val() == '') {
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
                data: $('#penjualan_jasa').serialize()+'&page='+page,
                success: function(data) {
                    $('#loaddata').html(data);
                }
            })
            return false;
        
    }

    function paging(page, tab, cari){
        get_data('<?= base_url('laporan/laporan_penjualan') ?>?', page);
    }

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

    function load_tarif(id_tarif, tipe){
         $.ajax({
                type: 'GET',
                url: '<?= base_url ()?>referensi/tarif/'+id_tarif+'/?tipe='+tipe,
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
    <?= form_open('laporan/laporan_penjualan', 'id=penjualan_jasa') ?>
        <table width="100%" class="inputan">
            <div class="msg"></div>
            <tr><td style="width: 150px;">Waktu</td><td><?= form_input('awal', isset($_GET['awal'])?$_GET['awal']:date("d/m/Y"), 'style="width: 75px;" id=awal size=10') ?> <span class="label"> s.d </span><?= form_input('akhir', isset($_GET['akhir'])?$_GET['akhir']:date("d/m/Y"), 'id=akhir size=10 style="width: 75px;"') ?></td></tr>
            <tr><td>Nama Nakes</td><td><?= form_input('nakes', isset($_GET['nakes'])?$_GET['nakes']:null, 'id=nakes size=32') ?> <?= form_hidden('id_nakes', isset($_GET['id_nakes'])?$_GET['id_nakes']:null) ?>
            <tr><td>&nbsp;</td><td><span class="label" id="profesi"><?= isset($_GET['profesi'])?$_GET['profesi']:null ?></span></td></tr>
            <tr><td></td><td>
            <?= form_hidden('profesi', isset($_GET['profesi'])?$_GET['profesi']:null) ?>
            <?= form_submit('submit', 'Cari', 'id=cari') ?> <?= form_button(null, 'Reset', 'id=reset') ?>
            <?= form_button(null, 'Cetak Excel', 'id=cetak') ?></td></tr>
        </table>
    </table>
    <?= form_close() ?>

    <?php if (isset($page)):?>
     <div id="resume">
        <h3>
            Halaman <?= $page ?> dari <?= (ceil($jumlah / $limit) == 0) ? 1 : ceil($jumlah / $limit) ?> (Total <?= $jumlah ?> data )
        </h3>
    </div>
    <?php endif; ?>
        <table class="list-data" width="100%">
            <thead>
                <tr>
                    <th width="3%">No.</th>
                    <th width="5%">Tanggal</th>
                    <th width="5%">No. Daftar</th>
                    <th width="25%">Nama Nakes</th>
                    <th width="30%">ID Tarif</th>
                    <th width="10%">Nominal</th>
                    <th width="5%">Frekuensi</th>
                    <th width="10%">Subtotal</th>
                </tr>
            </thead>
            <tbody>
            <?php if (isset($list_data)&&($list_data != null)) {
                $total = 0;
                foreach ($list_data as $key => $data) { ?>
                <tr class="<?= ($key%2==1)?'even':'odd' ?>">
                    <td align="center"><?= (++$key + (($page - 1) * $limit)) ?></td>
                    <td align="center"><?= datetimefmysql($data->waktu) ?></td>
                    <td align="center"><span class="link_button" onclick="load_penjualan_jasa('<?= $data->no_daftar ?>')"><?= $data->no_daftar ?></span></td>
                    <td><?= $data->pegawai ?></td>
                    <td>
                        <?php 
                            $tipe = '';
                            if($data->id_barang_sewa != null){
                                // barang
                                $tipe = 'barang';
                            }else if($data->layanan == 'Sewa Kamar'){
                                $tipe = 'kamar';
                            }else{
                                $tipe = 'tindakan';
                            }
                        ?>
                        <span class="link_button"  onclick="load_tarif('<?= $data->tarif_id ?>','<?= $tipe ?>')">
                            <?= $data->nama_tarif ?>
                        </span>
                    </td>
                    <td align="right"><?= rupiah($data->nominal) ?></td>
                    <td align="center"><?= $data->frekuensi ?></td>
                    <td align="right"><?= rupiah($data->nominal * $data->frekuensi) ?></td>
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
         <?php if (isset($list_data)) :?>
        <table width="100%">
            <tr>
                <td align="right" ><b>Total Jasa Sarana</b></td>
                <td align="right" width="15%"><b><?= isset($total_tarif)?rupiah($total_tarif->jasa_sarana):"0" ?></b></td>
            </tr>
             <tr>
                <td align="right" ><b>Total Jasa Nakes</b></td>
                <td align="right" width="15%"><b><?= isset($total_tarif)?rupiah($total_tarif->jasa_nakes):"0" ?></b></td>
            </tr>
             <tr>
                <td align="right" ><b>Total Jasa Tindakan R.S.</b></td>
                <td align="right" width="15%"><b><?= isset($total_tarif)?rupiah($total_tarif->jasa_tindakan):"0" ?></b></td>
            </tr>
             <tr>
                <td align="right" ><b>Total B.H.P</b></td>
                <td align="right" width="15%"><b><?= isset($total_tarif)?rupiah($total_tarif->bhp):"0" ?></b></td>
            </tr>
             <tr>
                <td align="right" ><b>Total Biaya Administrasi</b></td>
                <td align="right" width="15%"><b><?= isset($total_tarif)?rupiah($total_tarif->biaya_administrasi):"0" ?></b></td>
            </tr>
             <tr>
                <td align="right" ><b>Total (Subtotal)</b></td>
                <td align="right" width="15%"><b><?= isset($total_tarif)?rupiah($total_tarif->total_biaya):"0" ?></b></td>
            </tr>
        </table>
        <?php endif;?>
    </div>
    </div>
</div>