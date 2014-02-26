<?php $this->load->view('message') ?>
<title><?= $title ?></title>
<div class="titling"><h1><?= $title ?></h1></div>
<div class="kegiatan">
    <script>
        $('.angka').keyup(function(){Angka(this);});
        $('#nama').focus(); 
        $(function(){     
            $('.enter').live("keydown", function(e) {
                var n = $(".enter").length;
                if (e.keyCode === 13) {
                    var nextIndex = $('.enter').index(this) + 1;
                    if (nextIndex < n) {
                        $('.enter')[nextIndex].focus();
                    } else {
                        $('#simpanutama').focus();
                    }
                }
            });
            
            
            $('#bt_cari').click(function(){
                $('#form_cari').dialog('open');
            });

       
            $('#tgl_lahir').datepicker({
                changeYear : true,
                changeMonth : true,
                maxDate : +0
            });

            $('button[id=simpanutama], button[id=cari_pdd]').button({icons: {secondary: 'ui-icon-search'}});
            $('#reset, #reset_cari').button({icons: {secondary: 'ui-icon-refresh'}});
            $('#reset').click(function(){
                $('#loaddata').empty();
                $('#loaddata').load($.cookie('url'));
            
            });

            $("#simpanutama").click(function(){
                
                var Dnama;
                var Dkelamin = $("#kelamin option:selected").val();
                var Dusia = $("#usia option:selected").val();
                var Dtgl_lahir;
                var Dtelp = $("#telp").val();
                var Dalamat = $("#alamat").val();
                var Dkelurahan = $("input[name=id_kelurahan]").val();

                if($('#nama').val() !== ''){
                    Dnama = $('#nama').val();
                }

                if (Dnama === '') {
                    custom_message('Peringatan','Nama pasien tidak boleh kosong !', '#nama');
                    return false;
                }

                if (Dalamat === '') {
                    custom_message('Peringatan', 'Alamat jalan tidak boleh kosong !', '#alamat');
                    return false;
                }
                
                if (Dusia === 'umur'){
                    Dtgl_lahir = birthByAge($("#umur").val());
                    
                }else{
                
                    if($('#tgl_lahir').val() === '00/00/0000'){
                        custom_message('Peringatan', 'Tanggal lahir tidak valid !', '#tgl_lahir');
                        return false;
                    }else{
                        Dtgl_lahir = $("#tgl_lahir").val();
                    }
                }
            
                $.post('<?= base_url() ?>demografi/find_similar_post', {nama: Dnama, kelamin : Dkelamin, tgl_lahir : Dtgl_lahir,telp : Dtelp,alamat:Dalamat, id_kelurahan: Dkelurahan},
                function(data){
                    $("#sama").html(data);
                    $('#antrian').focus();
                }, '');
            });
            
            $('#tabs').tabs();
            $('#usia').change(function() {
                if ($('#usia').val() === 'umur') {
                    $('#umur').show();
                    $('#tgl_lahir').hide();
                } else {
                    $('#umur').hide();
                    $('#tgl_lahir').show();
                }
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
                    $('#addr').html('');
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
                $('#addr').html("Kec: "+data.kecamatan+", Kab: "+data.kabupaten+", Prov: "+data.provinsi);
            });
        
        });
    
        function birthByAge(umur){
            var today = new Date();
            var birth = today.getDate()+"/"+(today.getMonth()+1)+"/"+(today.getYear()+1900-umur);
    
            return birth;
        }

    </script>
    <div id="tabs">
        <ul>
            <li><a href="#tabs-1">Form Antri</a></li>
        </ul>
        <div id="tabs-1">
        <?= form_open('demografi/find_similar_post','id = formnew') ?>
                <table class="inputan" width="100%">
                    <tr><td style="width: 150px;">Nama Penduduk*:</td><td>
                    <?= form_input('nama', null, 'id =nama size=30 class="input-text enter"') ?>
                    <div class="search_pdd" id="bt_cari" title="Klik untuk mencari data di database kependudukan"></div>
                    <?= form_hidden('id_penduduk') ?>
                    <?= form_hidden('alamat')?></td></tr>
                <tr><td>Alamat Jalan*:</td><td><?= form_textarea('alamat','','id=alamat class="standar enter"r')?></td></tr>
                <tr><td>Kelurahan:</td><td><?= form_input('kelurahan', '', 'id=kelurahan size=30 class="input-text enter"') ?> </td></tr>
                <?= form_hidden('id_kelurahan') ?>    
                <tr><td></td><td><span class="label" id="addr"></span> </td></tr>
                <tr><td>Jenis Kelamin:</td><td><?= form_dropdown('kelamin', $kelamin, '', 'id = kelamin class="standar enter"r') ?></td></tr>        
                <tr><td>Tanggal Lahir:</td><td><?= form_input('tgl_lahir', null, 'id=tgl_lahir class="special enter" size=10') ?></td></tr>
                <tr><td>Umur:</td><td><?= form_input('umur', null, 'id=umur class=angka size=10') ?></td></tr>
                <tr><td>Telepon:</td><td><?= form_input('tlpn', null, 'id=telp class="input-text angka enter" size=30') ?></td></tr>
                <tr><td></td><td><?= form_button('simpanutama', 'Cek', 'id=simpanutama'); ?>
                <?= form_button('reset', 'Reset', 'id=reset') ?></td></tr>
                </table>
                <div id="sama"></div>
        <?= form_close() ?>
        </div>
    </div>
    
    <div id="form_cari" style="display: none;position: static; background: #fff; padding: 10px;">
        
            <?= form_open('','id=formcari')?>
        <table class="inputan" width="100%">
            <tr><td>No. RM:</td><td><?= form_input('norm', null, 'id=norm size=30 class="input-text"') ?></td></tr>
            <tr><td>Nama Penduduk:</td><td><?= form_input('nama', null, 'id=nama_cari size=30 class="input-text"') ?></td></tr>
            <tr><td>Alamat:</td><td><?= form_textarea('alamat','','id=alamat_cari class="standar"')?></td></tr>
            <tr><td></td><td><?= form_button('', 'Cari', 'id=cari_pdd onclick=cari_penduduk(1)'); ?><?= form_button('reset', 'Reset', 'id=reset_cari onclick=reset_pencarian()') ?></td></tr>
        </table>
            <?= form_close() ?>

            <div class="list_penduduk"></div>
        
    </div>

    <script type="text/javascript">
        $(function(){
            $('#formcari').submit(function(){
                cari_penduduk(1);
                return false;
            });

            $('#form_cari').dialog({
                autoOpen: false,
                height: $(window).height(),
                width: 800,
                title : 'Pencarian Penduduk',
                modal: true,
                resizable : false,
                open: function(){
                    cari_penduduk(1)
                },
                close : function(){
                    reset_pencarian();
                }
            });
        });

        function cari_penduduk(page){
            $.ajax({
                url: '<?= base_url("demografi/search_penduduk") ?>/'+page,
                cache: false,
                data : $('#formcari').serialize(),
                success: function(data) {
                   $('.list_penduduk').html(data);
                }
            });
        }

        function reset_pencarian(){
            cari_penduduk(1);
        }

    

        function pilih_penduduk(id, id_daftar){
            $.ajax({
                url: '<?= base_url("demografi/get_penduduk") ?>/'+id,
                cache: false,
                dataType :'json',
                success: function(data) {
                    console.log()
                    $('input[name=id_penduduk]').val(data.penduduk_id);
                    $('#nama').val(data.nama);
                    
                    $('#kelamin').val(data.gender);
                    $('#tgl_lahir').val(datefmysql(data.lahir_tanggal));
                    $('#alamat').val(data.alamat);
                    $('#darah_gol').val(data.darah_gol);
                    $('#telp').val(data.telp);
                    get_kelurahan(data.kelurahan_id);

                }
            });
           
            $('#form_cari').dialog('close');
                
        }

        function get_kelurahan(kel_id){
            if(kel_id != ''){
                $.ajax({
                    url: '<?= base_url("demografi/detail_kelurahan") ?>/'+kel_id,
                    cache: false,
                    dataType :'json',
                    success: function(data) {
                        $('#kelurahan').val(data.nama);
                        $('input[name=id_kelurahan]').val(data.id);
                        $('#addr').html("Kec: "+data.kecamatan+", Kab: "+data.kabupaten+", Prov: "+data.provinsi);
                    }
                });
            }
        }



        function paging(page, tab, cari){
            cari_penduduk(page);
        }

    </script>

</div>
<?php die; ?>