<?php $this->load->view('message') ?>
<title><?= $title ?></title>
<div class="titling"><h1><?= $title ?></h1></div>
<script type="text/javascript">
    
    function paging(page,tab,search) {
        var pg = page;
        if (page === null) {
            var pg = '';
        }
        $.ajax({
            url: '<?= base_url("pelayanan/klinis_load_data") ?>/'+pg,
            type: 'POST',
            data: $('#form_klinis').serialize(),
            cache: false,
            success: function(data) {
                $('#result').html(data);
            }
        });
    }
    $(function() {
        
        $('#tabs').tabs();
        $('#awal, #akhir').datetimepicker({
            changeYear: true,
            changeMonth: true
        });
        $('#cari').button({icons: {secondary: 'ui-icon-search'}}).click(function() {
            $('#form_klinis').submit();
            $('#pelayanan_kunjungan').html('');
        });
        $('#reset').button({icons: {secondary: 'ui-icon-refresh'}}).click(function() {
            $('#loaddata').empty().load('<?= base_url("pelayanan/klinis") ?>');
        });
        $('#form_klinis').submit(function() {
            paging();
            return false;
        }); 
        var lebar = $('#kelurahan').width();
        $('#kelurahan').autocomplete("<?= base_url('demografi/get_kelurahan') ?>",
        {
            parse: function(data){
                var parsed = [];
                for (var i=0; i < data.length; i++) {
                    parsed[i] = {
                        data: data[i],
                        value: data[i].nama // nama field yang dicari
                    };
                }
                $('input[name=id_kelurahan]').val('');
                return parsed;
            },
            formatItem: function(data,i,max){
                var str = '<div class=result>'+data.nama+'<br/>Kec: '+data.kecamatan+', Kab: '+data.kabupaten+', Prov: '+data.provinsi+'</div>';
                return str;
            },
            width: lebar, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
            dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
        }).result(
        function(event,data,formated){
            $(this).val(data.nama);
            $('input[name=id_kelurahan]').val(data.id);
        });
       
        $('.detail_diagnosis').live('click', function() {
            var url = $(this).attr('href');
            $.ajax({
                url: url,
                cache: false,
                success: function(data) {
                    $('#detail_diagnosis').html(data);
                }
            });
            return false;
        });


        $('.detail_kunj,.pelayanan,.add_kunjungan, #rekap_morbiditas').live('click', function() {
            var url = $(this).attr('href');
            $('#loaddata').load(url);
            $('.pelayanan_kunjungan').dialog('close');
            return false;
        });
    });

    function detail_tindakan(id){
         $.ajax({
                url: '<?= base_url("pelayanan/detail_tindakan") ?>/'+id,
                cache: false,
                success: function(data) {
                    $('#detail_tindakan').html(data);
                }
            });
    }

    function detail_pelayanan_pasien(id){
         $.ajax({
            url: '<?= base_url("pelayanan/detail_pelayanan") ?>/'+id,
            cache: false,
            success: function(data) {
                $('#detail_pelayanan').html(data);
            }
        });
    }

    
</script>
<div class="kegiatan">
    <div id="preview_kunjungan"></div>
    <div id="tabs">
        <ul>
            <li><a href="#tabs-1">Parameter</a></li>
        </ul>
        <div id="tabs-1">
        <?= form_open('','id=form_klinis') ?>
        <table width="100%" class="inputan">
            <tr><td style="width: 150px;">Range Waktu Pendaftaran:</td><td><?= form_input('awal', date("d/m/Y 00:00"), 'style="width: 90px;" id=awal size=14') ?><span class="label"> s . d</span><?= form_input('akhir', date("d/m/Y H:i"), 'style="width: 90px;" id=akhir size=14') ?></td></tr>
            <tr><td>No.:</td><td><?= form_input('no', NULL, 'id=no size=40') ?></td></tr>
            <tr><td>No. RM:</td><td><?= form_input('no_rm', NULL, 'id=no_rm size=40') ?></td></tr>
            <tr><td>Nama Pasien:</td><td><?= form_input('nama', NULL, 'id=nama size=40') ?></td></tr>
            <tr><td>Alamat:</td><td><?= form_textarea('alamat', NULL, 'id=alamat') ?></td></tr>
            <tr><td>Kelurahan:</td><td><?= form_input('kelurahan', NULL, 'id=kelurahan size=40') ?><?= form_hidden('id_kelurahan') ?></td></tr>
            <tr><td></td><td><?= form_button(NULL, 'Cari', 'id=cari') ?><?= form_button(NULL, 'Reset', 'id=reset') ?></td></tr>
        </table>
        <?= form_close() ?>
        
        <div id="result">
                <div class="data-list">
                <b>Hasil Cari Kunjungan</b>
                <table width="100%" class="list-data">
                    <thead>
                        <tr>
                            <th width="10%">No.</th>
                            <th width="15%">Masuk</th>
                            <th width="15%">Keluar</th>
                            <th width="25%">Nama Pasien</th>
                            <th width="30%">Wilayah</th>
                            <th width="10%">#</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php for($i = 0; $i <= 1; $i++) { ?>
                        <tr>
                            <td align="center">-</td>
                            <td align="center">-</td>
                            <td align="center">-</td>
                            <td>-</td>
                            <td>-</td>
                            <td align="center">-</td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
        </div>
    </div>
</div>