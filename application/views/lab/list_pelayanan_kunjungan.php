<?php $this->load->view('message') ?>
<title><?= $title ?></title>
<div class="titling"><h1><?= $title ?></h1></div>
<script type="text/javascript">
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
        $('#reset').button({icons: {secondary: 'ui-icon-refresh'}
        }).click(function() {
            $('#loaddata').load('<?= base_url('laboratorium/hasil_pemeriksaan_lab_list') ?>');
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


         $('.detail_kunj,.pelayanan,.add_kunjungan, #rekap_morbiditas').live('click', function() {
            var url = $(this).attr('href');
            $('#loaddata').load(url);
            return false;
        });
       
    });

    function paging(page,tab,search) {
        var pg = page;
        if (page === null) {
            var pg = '';
        }
        $.ajax({
            url: '<?= base_url("laboratorium/list_pelayanan_kunjungan") ?>/'+pg+'/'+$('input[name=jenis]').val(),
            type: 'POST',
            data: $('#form_klinis').serialize(),
            cache: false,
            success: function(data) {
                $('#result2').html(data);
                $('#detail_periksa').html('');
            }
        });
    }

    function paging(page,tab,search) {
        var pg = page;
        if (page === null) {
            var pg = '';
        }
        $.ajax({
            url: '<?= base_url("laboratorium/list_pelayanan_kunjungan") ?>/'+pg+'/'+$('input[name=jenis]').val(),
            type: 'POST',
            data: $('#form_klinis').serialize(),
            cache: false,
            success: function(data) {
                $('#result2').html(data);
                $('#detail_periksa').html('');
            }
        });
    }

    function detail_pemeriksaan(id_pk, jenis){
        if(jenis == 'laboratorium'){
            var url = '<?= base_url("laboratorium/detail_pemeriksaan_lab")?>/'+id_pk;
        }else if(jenis == 'radiologi'){
            var url = '<?= base_url("laboratorium/detail_pemeriksaan_rad")?>/'+id_pk;
        }else{
            var url = '<?= base_url("laboratorium/detail_pelayanan_fisioterapi")?>/'+id_pk;
        }
        

        $.ajax({
            url: url,
            cache: false,
            success: function(data) {
                $('#detail_periksa').html(data);
                
            }
        });
    }

    
</script>
<div class="kegiatan">
    <div id="preview_kunjungan"></div>
    <div id="tabs">
        <ul>
            <li><a href="#tabs-1">Entri Hasil</a></li>
        </ul>
        <div id="tabs-1">
        <?= form_open('','id=form_klinis') ?>
        <?= form_hidden('jenis', $jenis) ?>
        <table width="100%" class="inputan">
            <tr><td style="width: 150px;">Range Waktu:</td><td><?= form_input('awal', date("d/m/Y 00:00"), 'id=awal style="width: 90px;" size=12') ?><span class="label"> s . d</span><?= form_input('akhir', date("d/m/Y H:i"), 'id=akhir style="width: 90px;" size=12') ?></td></tr>
            <tr><td>No.:</td><td><?= form_input('no', NULL, 'id=no size=40') ?></td></tr>
            <tr><td>No. RM:</td><td><?= form_input('no_rm', NULL, 'id=no_rm size=40') ?></td></tr>
            <tr><td>Nama Pasien:</td><td><?= form_input('nama', NULL, 'id=nama size=40') ?></td></tr>
            <tr><td>Alamat:</td><td><?= form_input('alamat', NULL, 'id=alamat size=40') ?></td></tr>
            <tr><td>Kelurahan:</td><td><?= form_input('kelurahan', NULL, 'id=kelurahan size=40') ?><?= form_hidden('id_kelurahan') ?></td></tr>
            <tr><td></td><td><?= form_button(NULL, 'Cari', 'id=cari') ?><?= form_button(NULL, 'Reset', 'id=reset') ?></td></tr>
        </table>
        <?= form_close() ?>
        
        <div id="result2">
                <div class="data-list">
                <b>Hasil Cari Pelayanan Kunjungan</b>
                <table width="100%" class="list-data">
                    <thead>
                        <tr>
                            <th width="10%">ID</th>
                            <th width="8%">No. RM</th>
                            <th width="20%">Nama Pasien</th>
                            <th width="15%">Unit</th>
                            <th width="15%">Kelas</th>
                            <th width="15%">No. Bed</th>
                            <th width="20%">DPJP</th>
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
                            <td>-</td>
                            <td>-</td>
                            <td align="center">-</td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div id="detail_periksa"></div>
        </div>
    </div>
</div>