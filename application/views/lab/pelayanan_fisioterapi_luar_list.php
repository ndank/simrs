<title><?= $title ?></title>
<script type="text/javascript">
    
    function paging(page,tab,search) {
        var pg = page;
        if (page === null) {
            var pg = '';
        }
        $.ajax({
            url: '<?= base_url("laboratorium/fisioterapi_load_data") ?>/'+pg,
            data: $('#formfisio').serialize(),
            cache: false,
            success: function(data) {
                $('#result').html(data);
            }
        });
    }
    $(function() {
        
        $('#awal, #akhir').datepicker({
            changeYear: true,
            changeMonth: true
        });
        $('#cari').button({icons: {secondary: 'ui-icon-search'}}).click(function() {
            $('#formfisio').submit();
            $('#pelayanan_kunjungan').html('');
        });
        $('#reset').button({icons: {secondary: 'ui-icon-refresh'}}).click(function() {
            $('#loaddata').load($.cookie('url'));
        });
        $('#formfisio').submit(function() {
            paging(1);
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
       
    });

    function entry_fisioterapi(no_daftar){
        $.ajax({
            type : 'GET',
            url: '<?= base_url("laboratorium/pelayanan_fisioterapi_luar") ?>/'+no_daftar,
            cache: false,
            success: function(data) {
                $('#loaddata').html(data);
            }
        });
    }

    function edit_fisioterapi(no_daftar, id_pk){
        $.ajax({
            type : 'GET',
            url: '<?= base_url("pelayanan/pelayanan_fisioterapi_luar") ?>/'+no_daftar+'/'+id_pk,
            cache: false,
            success: function(data) {
                $('#loaddata').html(data);
            }
        });
    }

</script>
<div class="kegiatan">
    <div class="titling"><h1><?= $title ?></h1></div>
    <div class="data-input">
        <?= form_open('','id=formfisio') ?>
        <table width="100%" class="inputan">Parameter Pencarian Kunjungan</legend>
            <tr><td>Range Waktu:</td><td><?= form_input('awal', date("d/m/Y"), 'id=awal size=12') ?><span class="label"> s . d</span><?= form_input('akhir', date("d/m/Y"), 'id=akhir size=12') ?>
            <tr><td>ID Kunjungan:</td><td><?= form_input('no', NULL, 'id=no size=40') ?>
            <tr><td>Nama Pasien:</td><td><?= form_input('nama', NULL, 'id=nama size=40') ?>
            <tr><td>Alamat:</td><td><?= form_textarea('alamat', NULL, 'id=alamat style="width: 293px;"') ?>
            <tr><td>Kelurahan:</td><td><?= form_input('kelurahan', NULL, 'id=kelurahan size=40') ?><?= form_hidden('id_kelurahan') ?>
            <tr><td></td><td><?= form_button(NULL, 'Cari', 'id=cari') ?><?= form_button(NULL, 'Reset', 'id=reset') ?>
        </table>
        <?= form_close() ?>
    </div>

    <div id="result"></div>   

</div>