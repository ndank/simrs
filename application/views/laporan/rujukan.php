<?php $this->load->view('message') ?>
<title><?= $title ?></title>
<div class="kegiatan">
    <style>
        .drop{
            width: 200px;
        }
    </style>
    <script>  
        var tgl_from;
        var tgl_to;
        var ar;
        $(function(){
            $('#reset').click(function() {
                $('#lap_rujukan').html('');
                $('#todate, #fromdate, #nakes, #instansi, input[name=id_nakes], input[name=id_instansi]').val('');
                $('.msg').fadeOut('fast');
                $("#todate").attr('disabled', 'disabled');
            })
            
            $('#reset').button({icons: { secondary: 'ui-icon-refresh'}})
            $('#cari_range').button({icons: {secondary: 'ui-icon-circle-check'}}); 
        
            $("#todate").attr('disabled', 'disabled');
            $("#fromdate").datepicker({
                changeYear : true,
                changeMonth : true 
            });
            $("#fromdate").change(function(){
                if($('#fromdate').val() == ""){
                    $("#todate").attr('disabled', 'disabled');
                    $("#todate").val(null);
                }else{
                
                    $("#todate").removeAttr('disabled');
                    $("#todate").datepicker({
                        changeYear : true,
                        changeMonth : true,
                        minDate : $('#fromdate').val()
                    })
                }
            });
        
            $("#todate").change(function(){
                $("#cari_range").removeAttr('disabled');  
            });
            
            $('#instansi').autocomplete("<?= base_url('pendaftaran/load_data_instansi_relasi/') ?>",
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
                    var str = '<div class=result>'+data.nama+'<br/>Jenis : '+data.jenis+'<br/>Alamat :  '+data.alamat+'</div>';
                    return str;
                },
                width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
            }).result(
            function(event,data,formated){
                $(this).val(data.nama);
                $('input[name=id_instansi]').val(data.id);
            });
            
            $('#nakes').autocomplete("<?= base_url('pendaftaran/load_data_penduduk_profesi') ?>/",
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
                    var str = '<div class=result>'+data.nama+'<br/> '+data.profesi+'</div>';
                    return str;
                },
                width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
            }).result(
            function(event,data,formated){
                $(this).val(data.nama);
                $('input[name=id_nakes]').val(data.id_penduduk);
            });
            
            $('#formrujukan').submit(function(){
               
                if(($('#fromdate').val() != "")&($('#todate').val() == "")){
                    $('.msg').fadeIn('fast').html('Lengkapi range tanggal !');
                    $('#todate').focus();
                    return false;
                }else if(($('input[name=id_instansi]').val() != "")&($('input[name=id_nakes]').val() == "")){
                    $('.msg').fadeIn('fast').html('Tenaga Kesahatan harus diisi !');
                    $('#nakes').focus();
                    return false;
                }else{
                    $.ajax({
                        type : 'POST',
                        url: '<?= base_url('laporan/rujukan_data') ?>/',
                        data:$(this).serialize()+"&p=1",
                        cache: false,
                        success: function(data){
                            $('#lap_rujukan').html(data);
                            $('.msg').fadeOut('fast');
                        }
                    });
                    return false;
                }
                return false;
            });

        });

        function paging(page, tab, cari){
            $.ajax({
                type : 'POST',
                url: '<?= base_url('laporan/rujukan_data') ?>/',
                data:$('#formrujukan').serialize()+"&p="+page,
                cache: false,
                success: function(data){
                    $('#lap_rujukan').html(data);
                }
            });
        }
       
    </script>
    <div class="titling"><h1><?= $title ?></h1></div>
    <div class="data-input">

        <fieldset>
            <div class="msg"></div>
            <?= form_open('', 'id=formrujukan') ?>
            <label class="tb_harian">Tanggal</td><td>
            <?= form_input('fromdate', '', 'id = fromdate size=10') ?><span class="label"> s.d </span><?= form_input('todate', '', 'id = todate size=10') ?>

            <tr><td>Nama Instansi<br/>Perujuk</td><td><?= form_input('instansi', '', 'id=instansi size=30') ?>
            <?= form_hidden('id_instansi') ?>
            <tr><td>Nama Tenaga<br/>Kesehatan</td><td><?= form_input('nakes', '', 'id=nakes size=30') ?>
            <?= form_hidden('id_nakes') ?>
            <tr><td></td><td><?= form_submit('cari', 'Cari', 'id=cari_range ') ?> <?= form_button(null, 'Reset', 'id=reset') ?>
            <?= form_close() ?>
        </table>

        <div id="lap_rujukan"></div>

    </div>

</div>