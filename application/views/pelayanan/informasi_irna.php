<title><?= $title ?></title>
<script type="text/javascript">
    
    function paging(page,tab,search) {
        var pg = page;
        if (page === null) {
            var pg = '';
        }
        $.ajax({
            url: '<?= base_url('pelayanan/irna_load_data') ?>/'+pg,
            type: 'POST',
            data: $('#form_klinis').serialize(),
            cache: false,
            success: function(data) {
                $('#result').html(data);
            }
        });
    }
    $(function() {
        
        $('#awal, #akhir').datetimepicker({
            changeYear: true,
            changeMonth: true
        });
        $('#cari').button({
            icons: {
                secondary: 'ui-icon-search'
            }
        }).click(function() {
            $('#form_klinis').submit();
            $('#pelayanan_kunjungan').html('');
        });
        $('#reset').button({
            icons: {
                secondary: 'ui-icon-refresh'
            }
        }).click(function() {
            $('#loaddata').empty().load('<?= base_url('pelayanan/informasi_irna') ?>');
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
        $('.detail_kunjungan').live('click', function() {
            var url = $(this).attr('href');
            $.ajax({
                url: url,
                cache: false,
                success: function(data) {
                    $('#pelayanan_kunjungan').html(data);
                    $('#detail_diagnosis').html('');
                    $('#detail_tindakan').html('');
                }
            });
            return false;
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
        $('.detail_tindakan').live('click', function() {
            var url = $(this).attr('href');
            $.ajax({
                url: url,
                cache: false,
                success: function(data) {
                    $('#detail_tindakan').html(data);
                }
            });
            return false;
            //$('#detail_tindakan').
        });
        $('.detail_kunj,.pelayanan,.add_kunjungan, #rekap_morbiditas').live('click', function() {
            var url = $(this).attr('href');
            $('#loaddata').load(url);
            return false;
        });
    });
</script>
<div class="kegiatan">
    <div id="preview_kunjungan"></div>
    <div class="titling"><h1><?= $title ?></h1></div>
    <div class="data-input">
        <?= form_open('','id=form_klinis') ?>
        <table width="100%" class="inputan">Parameter Pencarian Kunjungan</legend>
            <tr><td>Range Waktu:</td><td><?= form_input('awal', date("d/m/Y 00:00"), 'id=awal size=15') ?><span class="label"> s . d</span><?= form_input('akhir', date("d/m/Y H:i"), 'id=akhir size=15') ?>
            <tr><td>No.:</td><td><?= form_input('no', NULL, 'id=no size=40') ?>
            <tr><td>No. RM:</td><td><?= form_input('no_rm', NULL, 'id=no_rm size=40') ?>
            <tr><td>Nama Pasien:</td><td><?= form_input('nama', NULL, 'id=nama size=40') ?>
            <tr><td>Alamat:</td><td><?= form_textarea('alamat', NULL, 'id=alamat') ?>
            <tr><td>Kelurahan:</td><td><?= form_input('kelurahan', NULL, 'id=kelurahan size=40') ?><?= form_hidden('id_kelurahan') ?>
            <tr><td></td><td><?= form_button(NULL, 'Cari', 'id=cari') ?><?= form_button(NULL, 'Reset', 'id=reset') ?>
        </table>
        <?= form_close() ?>
    </div>
    <div id="result">
            <div class="data-list">
            <h2>Hasil Cari Kunjungan</h2>
            <table width="100%" class="tabel">
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
    <div id="pelayanan_kunjungan">
        <div class="data-list">
            <h2>Pelayanan Kunjungan</h2>
            <table width="100%" class="tabel">
                <thead>
                    <tr>
                        <th width="10%">No.</th>
                        <th width="15%">Unit</th>
                        <th width="5%">Kelas</th>
                        <th width="5%">T.T</th>
                        <th width="30%">Dokter Penanggung Jawab</th>
                        <th width="10%">Kondisi</th>
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
                        <td align="center">-</td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
            <br/>

        </div>
    </div>
</div>