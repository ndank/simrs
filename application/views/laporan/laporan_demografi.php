<?php $this->load->view('message') ?>
<title><?= $title ?></title>
<div class="titling"><h1><?= $title ?></h1></div>
<div class="kegiatan">
    <style>
        .drop{
            width: 200px;
        }
    </style>
    <script type="text/javascript">  
        var tgl_from;
        var tgl_to;

        $(function(){
            $('#tabs').tabs();
            $('#reset').click(function() {
                var url = '<?= base_url('laporan/laporan_demografi') ?>';
                $('#loaddata').load(url);
            });
            $('#reset').button({icons: { secondary: 'ui-icon-refresh'}});
            $('#cari_range').button({icons: {secondary: 'ui-icon-circle-check'}});
            $(".item").hide();
            $("#jenis_lp").focus();
        
        
        
            $("#jenis_lp").change(function(){
                $('#container').html('');          
            });        
        
        
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
            var jenis = $("#jenis_lp").val();
            if (jenis !== 0){
                $('.msg').fadeOut('fast');
                var from = $("#fromdate").val().split("/");
                tgl_from = from[2]+"-"+from[1]+"-"+from[0];

                var to = $("#todate").val().split("/");
                tgl_to = to[2]+"-"+to[1]+"-"+to[0];
        
           
        
                if(from !== ''){
                    if(to === ''){
                        $('.msg').fadeIn('fast').html('Isi dulu range tanggal !');
                    }else{
                        get_laporan(tgl_from, tgl_to, jenis);
                    }            
                }else{                 
                    get_laporan(tgl_from, tgl_to, jenis);
                }
            }else{
                $('.msg').fadeIn('fast').html('Pilih jenis laporan dulu !');
            }
        }
    
        function get_laporan(tgl_from, tgl_to, jenis){
            
            $('.msg').fadeOut('fast');
            var func = '';
            switch(jenis){
                case '0':
                
                    break;
            
                case '1':
                    func = 'laporan_demografi_kelamin/';
                    break;
            
                case '2':
                    func = 'laporan_demografi_usia/';
                    break;
            
                case '3':
                    func = 'laporan_demografi_agama/';
                    break;
            
                case '4':
                    func = 'laporan_demografi_pendidikan/';
                    break;
            
                case '5':
                    func = 'laporan_demografi_pekerjaan/';
                    break;
            
                case '6':
                    func = 'laporan_demografi_nikah/';
                    break;
            
                case '7':
                    func = 'laporan_demografi_darah/';
                    break;
    
                default:
                    break;
            }
            
            if ((jenis !== '0') & (jenis !== '8')){
                $.ajax({
                    url: '<?= base_url() ?>laporan/'+func+tgl_from+'/'+tgl_to,
                    dataType: 'json',
                    success: function(data) {
                        generate_chart(data);
                    }
                });
            }
        }
            
    
       
    </script>
    
    <div id="tabs">
            <ul>
                <li><a href="#tabs-1">Parameter</a></li>
            </ul>
        <div id="tabs-1">
            <div class="msg"></div>
            <table class="inputan" width="100%">
                <tr><td>Range Tanggal Reg.:</td><td><?= form_input('fromdate', date('d/m/Y'), 'id = fromdate size=10 style="width: 75px;"') ?> s.d <?= form_input('todate', date('d/m/Y'), 'id = todate size=10 style="width: 75px;"') ?></td></tr>
                <tr class="jenis"><td>Jenis Laporan</td><td><?= form_dropdown('jenis_lp', $jenis, '', 'id=jenis_lp') ?></td></tr>
                <tr><td></td><td><?= form_button('cari', 'Cari', 'id=cari_range onClick=search() ') ?> <?= form_button(null, 'Reset', 'id=reset') ?>
            </table>
            <div id="container"></div>
        </div>
        <script type="text/javascript">
            function generate_chart(data){
                if(data.tipe === 'pie'){
                    draw_pie_chart(data);
                }else if(data.tipe === 'bar'){
                    draw_bar_chart(data);
                }
            }

            function draw_pie_chart(data){
                $('#container').highcharts({
                    chart: {
                        plotBackgroundColor: null,
                        plotBorderWidth: null,
                        plotShadow: false
                    },
                    title: {
                        text: data.title
                    },
                    tooltip: {
                        pointFormat: '{point.y} pasien ({point.percentage:.1f} %)'
                    },
                    plotOptions: {
                        pie: {
                            allowPointSelect: true,
                            cursor: 'pointer',
                            dataLabels: {
                                enabled: true,
                                color: '#000000',
                                connectorColor: '#000000',
                                format: '<b>{point.name}</b><br/>{point.y} pasien ({point.percentage:.1f} %)'
                            },
                            showInLegend: true
                        }
                    },
                    series: [{
                        type: 'pie',
                        name: data.kategori,
                        data: data.data
                    }]
                });
            }

            function draw_bar_chart(data){
                 $('#container').highcharts({
                    chart: {
                        type: 'bar'
                    },
                    title: {
                        text: data.title
                    },
                    xAxis: {
                        categories: data.item
                    },
                    yAxis: {
                        title: {
                            text: 'Jumlah'
                        }
                    },
                    series: [{
                        name: data.kategori,
                        data: data.jumlah
                    }]
                });
            }
        </script>

</div>