<?php $this->load->view('message') ?>
<title><?= $title ?></title>
<div class="titling"><h1><?= $title ?></h1></div>
<script type="text/javascript">
    $(function() {
        $('#tabs').tabs();
        $('#awal, #akhir').datepicker({
            changeYear: true,
            changeMonth: true
        });
        $('#print_lap').button({icons: {secondary: 'ui-icon-print'}}).click(function() {
            print_rekap_pasien();
        });

        $('#reset').button({icons: {secondary: 'ui-icon-refresh'}}).click(function() {
            $('#loaddata').empty();
            $('#loaddata').load($.cookie('url'));
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

    function print_rekap_pasien(){
        var url = $('#form').attr('action');
        var awal = date2mysql($('#awal').val());
        var akhir = date2mysql($('#akhir').val());
        if (awal == ''){
            custom_message('Peringatan','Range Tanggal harus diisi !', '#awal');
        }else if(akhir == ''){
            custom_message('Peringatan','Range Tanggal harus diisi !', '#akhir');
        }else{
            location.href = url+'/'+awal+'/'+akhir;
        }
       
    }
    
</script>
<div class="kegiatan">
    <div id="tabs">
        <ul>
            <li><a href="#tabs-1">Parameter</a></li>
        </ul>
        <div id="tabs-1">
            <?= form_open('pelayanan/cetak_rekap_profil_pasien','id=form') ?>
            <table width="100%" class="inputan">
                <tr><td>Tanggal Kunjungan:</td><td><?= form_input('awal', date("d/m/Y"), 'id=awal style="width: 75px;"') ?> s.d <?= form_input('akhir', date("d/m/Y"), 'id=akhir style="width: 75px;"') ?>
                <tr><td></td><td><?= form_button(NULL, 'Cetak Excel', 'id=print_lap') ?><?= form_button(NULL, 'Reset', 'id=reset') ?>
            </table>
            <?= form_close() ?>    
            <div id="result"></div>
        </div>
</div>