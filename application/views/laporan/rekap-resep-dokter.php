<script type="text/javascript">
    $(function() {
        $('#reset').click(function() {
            var url = '<?= base_url('laporan/rekap_resep_dokter') ?>';
            $('#loaddata').empty().load(url);
        })
        $('button[type=pmr_open], #csr').each(function(){
            $(this).replaceWith('<button type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'" onclick="'+$(this).attr('onclick')+'">' + $(this).val() + '</button>');
        });
        $('button[type=pmr_open], #csr').button({
            icons: {
                secondary: 'ui-icon-print'
            }
        });
        $('button[id=reset]').button({
            icons: {
                secondary: 'ui-icon-refresh'
            }
        });
        $('button[id=pmr_open]').button({
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
        $('.tanggal').datepicker({
            changeYear: true,
            changeMonth: true
        })
        
        $('#csr').click(function() {
            var awal = $('#awal').val();
            var akhir= $('#akhir').val();
            var hambatan = $('#hambatan').val();
            var url = '<?= base_url('laporan/statistika_resep') ?>?awal='+awal+'&akhir='+akhir;
            $.get(url, function(data) {
                $('#result_detail').html(data);
                $('#result_detail').dialog({
                    autoOpen: true,
                    height: 500,
                    width: 900,
                    modal: true
                })
            });
            return false;
            //window.open('<?= base_url('laporan/statistika_resep') ?>?awal='+awal+'&akhir='+akhir+'&hambatan='+hambatan,'mywindow','location=1,status=1,scrollbars=1,width=840.48px,height=500px');
        })
        $('#closehambatan').click(function() {
            $('.csr').fadeOut('fast');
        })
        $('#pmr_open').click(function() {
            var pasien = $('input[name=id_pasien]').val();
            var nama   = $('input[name=pasien]').val();
            if (pasien == '') {
                custom_message('Peringatan','Silahkan isikan data pasien terlebih dahulu!');
                $('#pasien').focus();
            } else {
                location.href='<?= base_url('pelayanan/cetak_pmr') ?>?id_pasien='+pasien+'&nama='+nama;
            }
        })
        $('.noresep').click(function() {
            var url = $(this).attr('href');
            $.get(url, function(data) {
                $('#result_detail').html(data);
                $('#result_detail').dialog({
                    autoOpen: true,
                    height: 500,
                    width: 900,
                    modal: true
                })
            });
            return false;
        })
        $('.salinresep').click(function() {
            var url = $(this).attr('href');
            $('#loaddata').load(url+'?_'+Math.random());
            return false;
        })
        $('#apoteker').autocomplete("<?= base_url('inv_autocomplete/load_data_penduduk_apoteker') ?>",
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
                var str = '<div class=result>'+data.nama+' - '+data.sip_no+'</div>';
                return str;
            },
            width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
            dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
        }).result(
        function(event,data,formated){
            $(this).val(data.nama+' - '+data.sip_no);
            $('input[name=id_apoteker]').val(data.id);
            
        });
        $('#pasien').autocomplete("<?= base_url('inv_autocomplete/load_data_penduduk_pasien') ?>",
        {
            parse: function(data){
                var parsed = [];
                for (var i=0; i < data.length; i++) {
                    parsed[i] = {
                        data: data[i],
                        value: data[i].nama // nama field yang dicari
                    };
                }
                $('input[name=id_pasien]').val('');
                return parsed;
            },
            formatItem: function(data,i,max){
                var str = '<div class=result>'+data.nama+' - '+data.no_rm+' <br/> '+data.alamat+'</div>';
                return str;
            },
            width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
            dataType: 'json', // tipe data yang diterima oleh library ini disetup sebagai JSON
            cacheLength: 0
        }).result(
        function(event,data,formated){
            $(this).val(data.nama+' - '+data.no_rm);
            $('input[name=id_pasien]').val(data.penduduk_id);
            
        });
        $('#dokter').autocomplete("<?= base_url('inv_autocomplete/load_data_penduduk_dokter') ?>",
        {
            parse: function(data){
                var parsed = [];
                for (var i=0; i < data.length; i++) {
                    parsed[i] = {
                        data: data[i],
                        value: data[i].nama // nama field yang dicari
                    };
                }
                $('input[name=id_dokter]').val('');
                return parsed;
            },
            formatItem: function(data,i,max){
                var str = '<div class=result>'+data.nama+' - '+data.kerja_izin_surat_no+'</div>';
                return str;
            },
            width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
            dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
        }).result(
        function(event,data,formated){
            $(this).val(data.nama+' - '+data.kerja_izin_surat_no);
            $('input[name=id_dokter]').val(data.penduduk_id);
        });
        
        $('#forminforesep').submit(function(){
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
        $('#awal, #akhir').datepicker({
            changeMonth: true,
            changeYear: true
        })
    })
</script>
<title><?= $title ?></title>
<div id="result_detail" style="display: none"></div>
<div class="kegiatan">
    <div class="titling"><h1><?= $title ?></h1></div>
    <div class="data-input">
        <table width="100%" class="inputan">Parameter</legend>

            <?= form_open('laporan/rekap_resep_dokter', 'id=forminforesep') ?>
                <tr><td>Range Tanggal:</td><td> <?= form_input('awal',isset($_GET['awal'])?$_GET['awal']:date("d/m/Y"),'size=10 id=awal') ?> <span class="label">s/d </span><?= form_input('akhir',isset($_GET['akhir'])?$_GET['akhir']:date("d/m/Y"),'size=10 id=akhir') ?>
                <tr><td>Nama Dokter:</td><td><?= form_input('dokter',isset($_GET['awal'])?$_GET['dokter']:NULL,'size=40 id=dokter') ?> <?= form_hidden('id_dokter', isset($_GET['awal'])?$_GET['id_dokter']:NULL) ?>
                <tr><td>Nama Pasien:</td><td><?= form_input('pasien',isset($_GET['awal'])?$_GET['pasien']:NULL,'size=40 id=pasien') ?> <?= form_hidden('id_pasien', isset($_GET['awal'])?$_GET['id_pasien']:NULL) ?>
                <tr><td></td><td>
                <?= form_submit(null, 'Cari', 'id=search') ?> 
                <?= form_button('Reset', 'Reset','id=reset') ?>
            <?= form_close() ?>
        </table>
    </div>
    <div class="data-list">
        <table class="tabel form-inputan" width="100%">
        <tr>
            <th width="3%">No.</th>
            <th width="5%">Tanggal</th>
            <th width="5%">No. RM</th>
            <th width="20%">Pasien</th>
            <th width="20%">Dokter</th>
            <th width="10%">Unit</th>
            <th width="5%">No. R/</th>
            <th width="10%">Jml <br/>Permintaan</th>
            <th width="10%">Aturan <br/>Pakai</th>
            <th width="5%">Iterasi</th>
        </tr>
        <?php
        if (isset($_GET['awal'])) {
        $total = 0;
        $no = "";
        foreach ($list_data as $key => $data) { 
        //$total = $total + $data->profesi_layanan_tindakan_jasa_total;
        ?>
        <tr class="<?= ($key%2==1)?'even':'odd' ?>">
            <td align="center"><?= ($no !== $data->id)?anchor('pelayanan/salin_receipt/'.$data->id, $data->id, 'class=salinresep title="Klik untuk salin resep"'):NULL ?></td>
            <td align="center"><?= ($no !== $data->id)?datetimefmysql($data->waktu):NULL ?></td>
            <td align="center"><?= ($no !== $data->id)?$data->no_rm:NULL ?></td>
            <td><?= ($no !== $data->id)?$data->pasien:NULL ?></td>
            <td><?= ($no !== $data->id)?$data->dokter:NULL ?></td>
            <td><?= ($no !== $data->id)?$data->nama_unit:NULL ?></td>
            <td align="center"><?= $data->r_no ?></td>
            <td align="center"><?= $data->resep_r_jumlah ?></td>
            <td><?= $data->pakai_aturan ?></td>
            <td align="center"><?= $data->iter ?></td>
        </tr>
        <?php 
        $no = $data->id;
        } ?>
<!--        <tr>
            <td colspan="6" align="right">Total</td>
            <td align="right"><?= rupiah($total) ?></td>
        </tr>-->
        <?php } else { 
        for ($i = 1; $i <= 2; $i++) {
        ?>
        <tr class="<?= ($i%2==1)?'even':'odd' ?>">
            <td align="center">&nbsp;</td>
            <td align="center"></td>
            <td align="center"></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <?php } }?>
    </table><br/>
    </div>
</div>
