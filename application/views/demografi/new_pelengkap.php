<script>
    $(function(){
        $('.enter').live("keydown", function(e) {
            var n = $(".enter").length;
            if (e.keyCode === 13) {
                var nextIndex = $('.enter').index(this) + 1;
                if (nextIndex < n) {                        
                    $('.enter')[nextIndex].focus();
                } else {
                    $('#simpan').focus();
                }
            }
        });
        $('button[id=addnewrow]').button({icons: {secondary: 'ui-icon-circle-plus'}});
        $('#lahir_tempat').focus();
        $('#simpan').button({icons: {secondary: 'ui-icon-circle-check'}}).click(function(){
            if ($('input[name=hd_lahir_tempat]').val() == ''){
                custom_message('Peringatan','Tempat lahir tidak boleh kosong !','#lahir_tempat');
            }else{
                $("<div title='Konfirmasi Simpan'>Anda yakin akan menyimpan data transaksi ini ?</div>").dialog({
                modal: true,
                autoOpen: true,
                width: 320,
                buttons: { 
                        "Ya": function() { 
                            $('#form_new').submit();
                            $(this).dialog('close');
                        },
                        "Tidak": function() {
                            $(this).dialog('close');
                            return false;
                        }
                    }, close: function() {
                        $(this).dialog('close');
                        return false;
                    }
              });
            }
        });

        
        $('#lahir_tempat').autocomplete("<?= base_url('demografi/get_kabupaten') ?>",
        {
            parse: function(data){
                var parsed = [];
                for (var i=0; i < data.length; i++) {
                    parsed[i] = {
                        data: data[i],
                        value: data[i].kabupaten // nama field yang dicari
                    };
                }
                return parsed;
            },
            formatItem: function(data,i,max){
                var str = '<div class=result> Kab: '+data.kabupaten+', <br/>Prov: '+data.provinsi+'</div>';
                return str;
            },
            width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
            dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
        }).result(
        function(event,data,formated){
            $(this).val(data.kabupaten);
            $("input[name=hd_lahir_tempat]").val(data.kabupaten_id);
          
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
            $("input[name=hd_kelurahan]").val(data.id);
            $('#addr_kec').html(data.kecamatan); 
            $('#addr_kab').html(data.kabupaten); 
            $('#addr_prop').html(data.provinsi);
        });
        
        
        $('#form_new').submit(function() {                          
            $.ajax({
                type : 'POST',
                url: '<?= base_url('demografi/new_post') ?>',               
                data: $(this).serialize(),
                cache: false,
                success: function(data) {
                    alert_tambah();
                    $('#loaddata').html(data);
                }
            });         
            
            return false;
        })
        
    });
   
</script>
<title><?= $title ?></title>

<?= form_open('', 'id = form_new') ?>
<?= form_hidden('id_pdd', $id_pdd) ?>
<?= form_hidden('nama', $nama) ?>
<?= form_hidden('lahir_tanggal', $tgl_lahir) ?>
<?= form_hidden('gender', $kelamin) ?>
<?= form_hidden('', (isset($penduduk) && $penduduk != null && $penduduk != null) ? $penduduk->kelurahan_id : '') ?>    
<?= form_hidden('hd_lahir_tempat', (isset($penduduk) && $penduduk != null && $penduduk != null) ? $penduduk->tempat_lahir_id : '') ?>
<?= form_hidden('id_antri', $id_antri)?>
<div class="titling"><h1><?= $title ?></h1></div>
<div class="data-input">
    <fieldset>
        <div class="msg" id="msg_pelengkap"></div>
        
        <tr><td>No. Rekam Medik</td><td><span class="label"><?= $no_rm ?></span>
        <tr><td>Nama</td><td><span class="label"><?= $nama ?></span>          
        <tr><td>Jenis Kelamin</td><td><span class="label"><?= ($kelamin == "L") ? "Laki-laki" : "" ?><?= ($kelamin == 'P') ? 'Perempuan' : '' ?></span>
        <tr><td>Tempat Kelahiran/<br/>Umur</td><td><?= form_input('lahir_tempat', (isset($penduduk) && $penduduk != null&& $penduduk != null) ? $penduduk->tempat_lahir : null, 'id = lahir_tempat class=enter size=30') ?> <span class="label">  / <?= ($tgl_lahir != '')?hitungUmur($tgl_lahir):'' ?></span>
        <tr><td>Telepon</td><td><?= form_hidden('telp', $telp) ?><span class="label"><?= $telp ?></span>

        <tr><td>Golongan Darah</td><td><?= form_dropdown('darah_gol', $gol_darah, (isset($penduduk) && $penduduk != null&& $penduduk != null) ? $penduduk->darah_gol : null, 'id=darah class="standar enter"') ?>
        <tr><td>Agama</td><td><?= form_dropdown('agama', $agama, (isset($penduduk) && $penduduk != null&& $penduduk != null) ? $penduduk->agama : null, 'id=agama class="enter"') ?>
        <tr><td>Pendidikan</td><td><?= form_dropdown('pendidikan', $pendidikan, (isset($penduduk) && $penduduk != null&& $penduduk != null) ? $penduduk->pendidikan_id : null, 'id=pendidikan class=enter') ?>
        <tr><td>Pekerjaan</td><td><?= form_dropdown('pekerjaan', $pekerjaan, (isset($penduduk) && $penduduk != null&& $penduduk != null) ? $penduduk->pekerjaan_id : null, 'id=pekerjaan class="enter"') ?>
        <tr><td>Status Pernikahan</td><td><?= form_dropdown('pernikahan', $pernikahan, (isset($penduduk) && $penduduk != null&& $penduduk != null) ? $penduduk->pernikahan : null, 'id=pernikahan class="enter"') ?>       
        <?= form_hidden('profesi', (isset($penduduk) && $penduduk != null&& $penduduk != null) ? $penduduk->profesi_id : null )?>



        <tr><td>Alamat Jalan</td><td><?= form_textarea('alamat', (isset($penduduk) && $penduduk != null) ? $penduduk->alamat : $detail->alamat_jalan_calon_pasien, 'rows=3 class="enter"') ?>
        <tr><td>Desa/Kelurahan</td><td><?= form_input('kelurahan', (isset($penduduk) && $penduduk != null) ? $penduduk->kelurahan : (isset($kelurahan)?$kelurahan:''), 'id=kelurahan class="enter" size=30') ?> 
        <?= form_hidden('hd_kelurahan', (isset($penduduk) && $penduduk != null) ? $penduduk->kelurahan_id : (isset($id_kelurahan)?$id_kelurahan:'')) ?>
        <tr><td>Kecamatan</td><td><span class="label" id="addr_kec"><?= (isset($penduduk) && $penduduk != null) ? $penduduk->kecamatan : (isset($kecamatan)?$kecamatan:'') ?></span>
        <tr><td>Kabupaten/Kota</td><td><span class="label" id="addr_kab"><?= (isset($penduduk) && $penduduk != null) ? $penduduk->kabupaten : (isset($kabupaten)?$kabupaten:'') ?></span>
        <tr><td>Provinsi</td><td><span class="label" id="addr_prop"><?= (isset($penduduk) && $penduduk != null) ? $penduduk->provinsi : (isset($provinsi)?$provinsi:'') ?></span>       
        <tr><td></td><td>

        <tr><td>Nomor Identitas</td><td><?= form_input('identitas_no', (isset($penduduk) && $penduduk != null) ? $penduduk->identitas_no : '', 'size=30 class="enter"') ?>

        <tr><td></td><td><?= form_button('simpanpelengkap', 'Simpan', 'id=simpan') ?>
    </table>
</div>

<?= form_close() ?>

<?php die; ?>