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
  
        $(function(){
            $('#tabs').tabs();
            $('#container').hide();
            $('#reset').click(function() {
                var url = '<?= base_url("pelayanan/laporan_morbiditas") ?>';
                $('#loaddata').load(url);
            })
            
            $('#reset').button({icons: { secondary: 'ui-icon-refresh'}});
            $('#cari_range').button({icons: {secondary: 'ui-icon-circle-check'}});

        
        
            $("#fromdate").datepicker({
                changeYear : true,
                changeMonth : true 
            });
            $("#fromdate").change(function(){
                if($('#fromdate').val() == ""){
                    $("#todate").attr('readonly', 'readonly');
                    $("#todate").val(null);
                }else{
                
                    $("#todate").removeAttr('readonly');
                    $("#todate").datepicker({
                        changeYear : true,
                        changeMonth : true,
                        minDate : $('#fromdate').val()
                    })
                }
            });
        
           
        });
    
        
        function search(){
           $.ajax({
                url: '<?= base_url("pelayanan/load_data_morbiditas") ?>',
                data: $('#form').serialize(),
                dataType: 'json',
                success: function(data) {
                    console.log(data);
                    draw_bar_chart(data);
                }
            });            
        }
    
    
    
       
    </script>
    <div id="tabs">
        <?php $unit = array(''=>'Semua Pelayanan','Poliklinik'=>'Poliklinik', 'IGD'=>'IGD', 'Rawat Inap'=>'Rawat Inap');?>
        <ul>
            <li><a href="#tabs-1">Parameter</a></li>
        </ul>
        <div id="tabs-1">
            <?= form_open('','id=form') ?>
            <div class="msg"></div>
            <table width="100%" class="inputan">
                <tr><td class="tb_harian">Range Tanggal</td><td><?= form_input('fromdate', date('d/m/Y'), 'id = fromdate style="width: 75px;"') ?><span class="label"> s.d </span><?= form_input('todate', date('d/m/Y'), 'id = todate style="width: 75px;"') ?></td></tr>
                <tr><td>Pelayanan</td><td><?= form_dropdown('unit', $unit, null, 'id=unit') ?></td></tr>
                <tr><td></td><td><?= form_button('cari', 'Tampil', 'id=cari_range onClick=search() ') ?> <?= form_button(null, 'Reset', 'id=reset') ?></td></tr>
            </table>
            <?= form_close() ?>
            <br/>
            <div class="charting_full" id="container"></div>
        </div>
    </div>

    <br/><br/><br/>
    <script type="text/javascript">

        function draw_bar_chart(data){
            $('#container').show();
            $('#container').highcharts({
                chart: {
                    type: 'bar'
                },
                title: {
                    text: data.title
                },
                xAxis: {
                    categories: data.nama
                },
                yAxis: {
                    title: {
                        text: 'Jumlah'
                    }
                },
                series: [{
                    name : 'Jumlah Pasien',
                    data: data.jumlah
                }]
            });
        }
    </script>

</div>
<?php die ?>