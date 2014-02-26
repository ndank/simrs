<?php $this->load->view('message') ?>
<title><?= $title ?></title>
<script type="text/javascript">
    var dWidth = $(window).width();
    var dHeight= $(window).height();
    var x = screen.width/2 - dWidth/2;
    var y = screen.height/2 - dHeight/2;

    $(function() {
        $('#tabs').tabs();
        load_indikator();
        $('#awal, #akhir').datepicker({
            changeYear: true,
            changeMonth: true
        });
        $('#cari').button({icons: {secondary: 'ui-icon-search'}}).click(function() {
            load_indikator();
        });
        $('.print').button({icons: {secondary: 'ui-icon-print'}});
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

    function load_indikator(){
        var url = $('#form').attr('action');
        $.ajax({
            url: url,
            data: $('#form').serialize(),
            cache: false,
            success: function(data) {
                $('#result').html(data);
            }
        });
    }

    function cetak_rl(){
        window.open('<?= base_url("rekap_laporan/cetak_rl1_2") ?>/','Myprint','width='+dWidth+', height='+dHeight+', left='+x+',top='+y);
    }
    
</script>
<div class="titling"><h1><?= $title ?></h1></div>
<div class="kegiatan">
    <div id="tabs">
        <ul>
            <li><a href="#tabs-1">Parameter</a></li>
        </ul>
        <div id="tabs-1">
            <?= form_open('pelayanan/load_indikator_pelayanan_rs','id=form') ?>
            <table width="100%" class="inputan">
                <tr><td>Tanggal Kunjungan:</td><td><?= form_input('awal', isset($_GET['awal'])?$_GET['awal']:date("d/m/Y"), 'id=awal size=12') ?>
                <span class="label"> s . d</span><?= form_input('akhir', isset($_GET['akhir'])?$_GET['akhir']:date("d/m/Y"), 'id=akhir size=12') ?>
                <tr><td></td><td><?= form_button(NULL, 'Cari', 'id=cari') ?><?= form_button(NULL, 'Reset', 'id=reset') ?>
                <!--<?= form_button(null, 'Cetak RL 1.2 ', 'class=print onclick=cetak_rl()') ?>-->
            </table>
            <?= form_close() ?>
            <div id="result"></div>
        </div>  
    </div>
</div>