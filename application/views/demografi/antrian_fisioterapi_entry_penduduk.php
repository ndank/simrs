<div class="kegiatan">
<script>
    $(function(){
        $('button[id=addnewrow]').button({
            icons: {
                secondary: 'ui-icon-circle-plus'
            }
        });
        $('input[type=submit]').each(function(){ $(this).replaceWith('<button type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'">' + $(this).val() + '</button>');});
        $('button[type=submit]').button({icons: {secondary: 'ui-icon-circle-check'}});
        
        $("#simpan").button(); 
        
         
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
                $("input[name=hd_kelurahan]").val('');
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
            $('#addr_kec').html(data.kecamatan); 
            $('#addr_kab').html(data.kabupaten); 
            $('#addr_prop').html(data.provinsi);
        });
        
        
        $('#form_new').submit(function() { 
            var id = $('input[name=id]').val();
            if ($('input[name=id_lahir_tempat]').val() == ''){
                $('#msg_pelengkap').fadeIn('fast').html('Tempat lahir tidak boleh kosong !');
                $('#lahir_tempat').focus();     
            }else{
                
                $.ajax({
                    type : 'POST',
                    url: '<?= base_url("demografi/antrian_fisioterapi_penduduk_save") ?>/'+id,               
                    data: $(this).serialize(),
                    cache: false,
                    success: function(data) {
                        $('#loaddata').html(data);
                    }
                }); 
                return false;
            }
            return false;
        })
        
    });
   
</script>
<title><?= $title ?></title>

<?= form_open('', 'id = form_new') ?>
<?= form_hidden('id', $pasien->id) ?>
<?= form_hidden('nama', $pasien->nama_calon_pasien) ?>
<?= form_hidden('lahir_tanggal', $pasien->lahir_tanggal) ?>
<?= form_hidden('gender', $pasien->gender) ?>

<div class="titling"><h1><?= $title ?></h1></div>
<div class="data-input">
    <fieldset>
        <div class="msg" id="msg_pelengkap"></div>
        <tr><td>Nama</td><td><span class="label"><?= $pasien->nama_calon_pasien ?></span>          
        <tr><td>Jenis Kelamin</td><td><span class="label"><?= ($pasien->gender == "L") ? "Laki-laki" : "" ?><?= ($pasien->gender == 'P') ? 'Perempuan' : '' ?></span>
        <tr><td>Tempat Kelahiran/Umur</td><td><span class="label">  / <?= ($pasien->lahir_tanggal != '')?hitungUmur($pasien->lahir_tanggal):'' ?></span>
        <tr><td>Telepon</td><td><?= form_hidden('telp', $pasien->telp_no) ?><span class="label"><?= $pasien->telp_no ?></span>

        <tr><td>Golongan Darah</td><td><?= form_dropdown('darah_gol', $gol_darah, null, 'id = darah') ?>
        


        <tr><td>Alamat Jalan</td><td><?= form_textarea('alamat', $pasien->alamat_jalan_calon_pasien, 'rows=3') ?>
        <tr><td>Desa/Kelurahan</td><td><?= form_input('kelurahan', (isset($kelurahan)?$kelurahan:''), 'id=kelurahan size=30') ?> 
        <?= form_hidden('id_kelurahan', ($pasien->id_kelurahan != null)?$pasien->id_kelurahan:'') ?>
        <tr><td>Kecamatan</td><td><span class="label" id="addr_kec"><?= (isset($kecamatan)?$kecamatan:'') ?></span>
        <tr><td>Kabupaten/Kota</td><td><span class="label" id="addr_kab"><?= (isset($kabupaten)?$kabupaten:'') ?></span>
        <tr><td>Provinsi</td><td><span class="label" id="addr_prop"><?= (isset($provinsi)?$provinsi:'') ?></span>       
        <tr><td></td><td>

        <tr><td></td><td><?= form_submit('simpanpelengkap', 'Simpan', 'id=simpan') ?>
    </table>
</div>

<?= form_close() ?>

<?php die; ?>
</div>