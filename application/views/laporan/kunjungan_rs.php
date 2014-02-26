<?php $this->load->view('message') ?>
<title><?= $title ?></title>
<div class="titling"><h1><?= $title ?></h1></div>
<div class="kegiatan">
    <script>
        var from = null;
        var to = null;
    
        var bulan_from;
        var tahun_from;
    
        var bulan_to;
        var tahun_to;
    
        $(function(){
            $('#tabs').tabs();
            $('#reset, .resetan').button({icons: {secondary: 'ui-icon-refresh'}});
            $('#cari_range').button({icons: {secondary: 'ui-icon-circle-check'}});
            $(".tb_bulanan").hide();
            $(".tb_harian").hide();
            $(".jenis").hide();
            $("#tipe_lp").change(function(){
                $('#laporan').html('');
                if($("#tipe_lp").val() == "harian"){
                    $(".tb_bulanan").hide();
                    $(".tb_harian").show();
                    $(".jenis").show();
                }else if($("#tipe_lp").val() == "bulanan"){
                    $(".tb_bulanan").show();
                    $(".tb_harian").hide();
                    $(".jenis").show();
                }else{
                    $(".tb_bulanan").hide();
                    $(".tb_harian").hide();
                    $(".jenis").hide();
                }
            }); 
        
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

        });
    
        function search(){

            if($('#tipe_lp').val() == 'pilihtipe'){
                custom_message('Peringatan !','Pilih tipe laporan !','#tipe_lp');
                return false;
            }

            if($('#jenis_lp').val() == 'pilihjenis'){
                custom_message('Peringatan !','Pilih jenis laporan','#jenis_lp');
                return false;
            }

            if($("#tipe_lp").val() == "harian"){
                search_harian();
            }else if($("#tipe_lp").val() == "bulanan"){
                search_bulanan();
            }
        }
    
        function search_harian(){
            if(($("#fromdate").val() != '') & ($("#todate").val() == '') ){
                custom_message('Peringatan!','Range tanggal harus lengkap !', '#todate');
            }else if(($("#fromdate").val() == '') & ($("#todate").val() != '')){
                custom_message('Peringatan!', 'Range tanggal harus lengkap !', '#fromdate');
            }else{
                from = $("#fromdate").val().split("/");
                to = $("#todate").val().split("/");
            
         
                tgl_from = from[2]+"-"+from[1]+"-"+from[0];
                tgl_to = to[2]+"-"+to[1]+"-"+to[0];
           
            
                if($("#jenis_lp").val() == "pasien"){ 
                    $.ajax({
                        url: '<?= base_url() ?>index.php/laporan/kunjungan_rs_harian_pasien/'+tgl_from+"/"+tgl_to,
                        dataType: '',
                        success: function( response ) {
                            $("#laporan").html(response);
                        }
                    });
            
                }else if($("#jenis_lp").val() == "unit"){
                    $.ajax({
                        url: '<?= base_url() ?>index.php/laporan/kunjungan_rs_harian_unit/'+tgl_from+"/"+tgl_to,
                        dataType: '',
                        success: function( response ) {
                            $("#laporan").html(response);
                        }
                    });
                }
            }
        
        }
        function search_bulanan(){

            bulan_from = $("#bulan_from").val();
            tahun_from = $("#tahun_from").val();
            
            
            bulan_to = $("#bulan_to").val();
            tahun_to = $("#tahun_to").val();
            if($("#jenis_lp").val() == "pasien"){
                $.ajax({
                    url: '<?= base_url() ?>index.php/laporan/kunjungan_rs_bulanan_pasien/'+bulan_from+"/"+tahun_from+"/"+bulan_to+"/"+tahun_to,
                    dataType: '',
                    success: function( response ) {
                        $("#laporan").html(response);
                    }
                });
        
            }else if($("#jenis_lp").val() == "unit"){
                $.ajax({
                    url: '<?= base_url() ?>index.php/laporan/kunjungan_rs_bulanan_unit/'+bulan_from+"/"+tahun_from+"/"+bulan_to+"/"+tahun_to,
                    dataType: '',
                    success: function( response ) {
                        $("#laporan").html(response);
                    }
                });
            }
        }

        function reset_all(){
            $('#loaddata').empty();
            $('#loaddata').load($.cookie('url'));
        }
    </script>
    <div id="tabs">
        <ul>
            <li><a href="#tabs-1">Parameter</a></li>
        </ul>
        <div id="tabs-1">
        <table width="100%" class="inputan">
            <tr><td style="width: 150px;">Tipe Laporan:</td><td><?= form_dropdown('tipe_lp', $tipe, '', 'id=tipe_lp') ?></td>
            <tr class="tb_bulanan"><td>Bulan:</td><td>
            <span class="tb_bulanan"><?= form_dropdown('frombulan', $bulan, $bulan_now, 'id=bulan_from style=width:120px') ?>
            <?= form_dropdown('fromtahun', $tahun, $tahun_now, 'id=tahun_from style=width:120px') ?> 
            <span class="label"> s.d </span><?= form_dropdown('tobulan', $bulan, $bulan_now, 'id=bulan_to style=width:120px') ?>
            <?= form_dropdown('fromtahun', $tahun, $tahun_now, 'id=tahun_to style=width:120px') ?></span>
            </td></tr>
            <tr class="tb_harian"><td>Tanggal:</td><td class="tb_harian"><?= form_input('fromdate', '', 'id = fromdate size=10 style="width: 75px;"') ?><span class="label"> s.d </span><?= form_input('todate', '', 'id = todate size=10 style="width: 75px;"') ?></td></tr>
            <tr class="jenis"><td>Jenis Laporan</td><td class="jenis"><?= form_dropdown('jenis_lp', $jenis, '', 'id=jenis_lp') ?></td></tr>
            <tr><td></td><td><?= form_button('cari', 'Cari', 'id=cari_range onClick=search() ') ?>
            <?= form_button('', 'Reset', 'class=resetan onClick=reset_all()') ?></td></tr>
        </table>
        <div id="laporan"></div>
        </div>
    </div>


</div>
<?php die ?>