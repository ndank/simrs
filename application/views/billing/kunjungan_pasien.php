<title><?= $title ?></title>
<script type="text/javascript">
    $(function() {
        $('#tabs').tabs();
        $('#nomor').focus();
        $('#awal,#akhir').datepicker({
            changeYear: true,
            changeMonth: true
        });
        $('#search').button({ icons: { secondary: 'ui-icon-search' } });
        $('#reset').button({ icons: { secondary: 'ui-icon-refresh' } });
        $('#reset').click(function() {
            $('#loaddata').empty();
            $('#loaddata').load('<?= base_url('billing/kunjungan_pasien') ?>');
        });
        $('#search').click(function() {
            $('#billing').submit();
        });
        $('#billing').submit(function() {
           get_list(1);
            return false;
        });
        $('#kelurahan').autocomplete("<?= base_url('referensi/get_kelurahan') ?>",
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
                var str = '<div class=result>'+data.nama+'<br/>Kec: '+data.kecamatan+', Kab: '+data.kabupaten+', Prov: '+data.provinsi+'</div>';
                return str;
            },
            width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
            dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
        }).result(
        function(event,data,formated){
            $(this).val(data.nama);
            $('input[name=id_kelurahan]').val(data.id);
            // $('.id_kabupaten').val(data.id_kabupaten);
        });
    });

    function get_list(page){
         $.ajax({
                type: 'POST',
                url: $('#billing').attr('action')+'/'+page,
                data: $('#billing').serialize(),
                cache: false,
                success: function(data) {
                    $('#result').html(data);
                }
            });
    }

    function paging(page, tab, cari){
            get_list(page);
        }
</script>
<div class="titling"><h1><?= $title ?></h1></div>
<div class="kegiatan">
<div id="tabs">
        <ul>
            <li><a href="#tabs-1">Laporan</a></li>
        </ul>
    <div id="tabs-1">
        <?= form_open('billing/kunjungan_pasien_load_data', 'id=billing') ?>
        <table width="100%" class="inputan">
            <tr><td style="width: 150px;">Range Waktu Pendaftaran:</td><td>
            <?= form_input('awal', date("d/m/Y"), 'id=awal style="width: 75px;" size=10') ?> s . d <?= form_input('akhir', date("d/m/Y"), 'id=akhir style="width: 75px;" size=10') ?></td></tr>
            <tr><td>No.:</td><td><?= form_input('nomor', NULL, 'id=nomor size=40') ?></td></tr>
            <tr><td>No. RM:</td><td><?= form_input('no_rm', NULL, 'id=no_rm size=40') ?></td></tr>
            <tr><td>Nama Pasien:</td><td><?= form_input('pasien', NULL, 'id=pasien size=40') ?></td></tr>
            <tr><td>Alamat:</td><td><?= form_textarea('alamat', NULL, 'id=alamat') ?></td></tr>
            <tr><td>Kelurahan:</td><td><?= form_input('kelurahan', NULL, 'id=kelurahan size=40') ?><?= form_hidden('id_kelurahan') ?></td></tr>
            <tr><td></td><td><?= form_button(null, 'Cari', 'id=search') ?> <?= form_button(null, 'Reset', 'id=reset') ?></td></tr>
        </table>
        <?= form_close() ?>
        <div id="result"></div>
    </div>
    </div>
</div>