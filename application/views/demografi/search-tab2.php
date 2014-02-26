<script>
    $(function(){
        $('#tabs').tabs();
        $('.resetan').button({icons: {secondary: 'ui-icon-refresh'}});
        $("#cari_demo").button({icons: {secondary: 'ui-icon-search'}});
        $("#cari_demo").click(function(){
            get_list(1);
        });

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
                    $("input[name=id_kelurahan]").val('');
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
                $("input[name=id_kelurahan]").val(data.id);
                $('#addr').html("Kec : "+data.kecamatan+", Kab : "+data.kabupaten+", Prov : "+data.provinsi);
            });
    });
    
    function get_list(p){
        var Dnama = $("#nama").val();
        var Dkelamin = $("#kelamin option:selected").val();
        var Dumur = $("#umur").val();
        var Dalamat = $("#alamat").val();
        var Dnm_ibu = $("#nm_ibu").val();
        $.ajax({
            url: '<?= base_url('demografi/advance_search_post/') ?>/'+p,
            data: $('#form').serialize(),
            cache: false,
            success: function(msg) {
                $('#hasil_no_rm').html(msg);                       
            }
        })
    
    }
</script>
<?= form_open('demografi/search','id=form') ?>
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">Rekap Pasien</a></li>
    </ul>
    <div id="tabs-1">
        <table width="100%" class="inputan">
            <tr><td style="width: 150px;">Nomor RM:</td><td><?= form_input('no_rm','', 'id = no_rm size=40') ?></td></tr>
            <tr><td>Nama Pasien:</td><td><?= form_input('nama', null, 'id = nama size=40') ?></td></tr>
            <tr><td>Jenis Kelamin:</td><td><?= form_dropdown('kelamin', $kelamin, '', 'id = kelamin') ?></td></tr>
            <tr><td>Umur:</td><td><?= form_input('umur', null, 'id=umur onkeyup="Angka(this)"; size=40') ?></td></tr>
            <tr><td>Alamat Jalan:</td><td><?= form_textarea('alamat', null, 'id = alamat class=standar') ?></td></tr>
            <tr><td></td><td><?= form_button('cari', 'Cari', 'id = cari_demo') ?>
            <?= form_button('', 'Reset', 'class=resetan onClick=reset_all()') ?></td></tr>
        </table>
        <div id="hasil_no_rm"></div>
    </div>
    
</div>
<?= form_close() ?>

<?php die; ?>