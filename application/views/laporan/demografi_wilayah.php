<?php $this->load->view('message') ?>
<title><?= $title ?></title>
<div class="titling"><h1><?= $title ?></h1></div>
<div class="kegiatan">
    <script type="text/javascript">
        var area = 'provinsi';
        $(function(){
            $('#tabs').tabs();
            $('.print').button({icons: {secondary: 'ui-icon-print'}});
            $('#reset').click(function() {
                $('#loaddata').empty();
                $('#loaddata').load($.cookie('url'));
            });
            hide_all();
            $('#dinamic_label').html('Provinsi');

            multi_show($('input:radio[name=kategori]').val(), '');
            $('input:radio[name=kategori]').change(function(){
                empty();
                hide_all();
                area = $(this).val();
                if (area == 'provinsi') {
                    multi_show(area, '');
                    $('#dinamic_label').html('Provinsi');
                }else {
                    prov_show();
                    if(area == 'kabupaten'){
                        $('#prop_sp').show();
                        $('#dinamic_label').html('Kabupaten');
                    }else if(area == 'kecamatan'){
                        $('#prop_sp').show();
                        $('#kab_sp').show();
                        $('#dinamic_label').html('Kecamatan');
                    }else if(area == 'kelurahan'){
                        $('#prop_sp').show();
                        $('#kab_sp').show();
                        $('#kec_sp').show();
                        $('#dinamic_label').html('Kelurahan');
                    }
                }
            });

            $('#prop_choice').change(function(){
                var prov = $('#prop_choice').val();
               
                if(area != 'kabupaten'){
                    kab_show(prov);
                }else{
                    multi_show(area, $(this).val());
                }
            });

            $('#kab_choice').change(function(){
                var kab = $('#kab_choice').val();
                if (area=='kelurahan'){
                    kec_show(kab); 
                }else if(area =='kecamatan'){
                    multi_show(area, kab);         
                }   
            });

            $('#kec_choice').change(function(){
                var kec = $('#kec_choice').val();
                multi_show(area,kec);
            
            });

            $("#multiAll").click(function(){
                $('option.multi').each(function() {
                    this.selected = true;                        
                });
            });
        
            $("#multiNone").click(function(){
                $('option.multi').each(function() {
                    this.selected = false;                        
                });
           
            });

            $('#reset').button({icons: { secondary: 'ui-icon-refresh'}});
            $('#cari_range').button({icons: {secondary: 'ui-icon-circle-check'}});
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
        });

        function prov_show(){
            $('#prop_choice').empty();
            $('#prop_choice').append("<option value='pilih'>Pilih Provinsi ...</option>");
            $.getJSON("<?= base_url() ?>laporan/get_provinsi", function(data){
                $.each(data, function (index, val) {
                    $('#prop_choice').append("<option value='"+val.id+"' >"+val.nama+"</option>");
                });
            });
        }
    
        function kab_show(prov){
            $('#kab_choice').empty();
            $('#kab_choice').append("<option value='pilih'>Pilih Kabupaten ...</option>");
            $.getJSON("<?= base_url() ?>laporan/get_kabupaten/"+prov, function(data){
                $.each(data, function (index, val) {
                    $('#kab_choice').append("<option value='"+val.id+"'>"+val.nama+"</option>");
                });
            });
        }
    
        function kec_show(kab){
       
            $('#kec_choice').empty();
            $('#kec_choice').append("<option value='pilih'>Pilih Kecamatan ...</option>");
            $.getJSON("<?= base_url() ?>laporan/get_kecamatan/"+kab, function(data){
                $.each(data, function (index, val) {
                    $('#kec_choice').append("<option value='"+val.id+"' >"+val.nama+"</option>");
                });
            });
        }

        

        function multi_show(area,param){
            var fungsi = '';
            $('#multiSelect').empty();
               
            $.getJSON("<?= base_url() ?>laporan/get_"+area+"/"+param, function(data){
                $.each(data, function (index, val) {
                    $('#multiSelect').append("<option value='"+val.id+"' class='multi'>"+val.nama+"</option>");
                });
            });
        }

        function empty(){
            $('#multiSelect, #prop_choice, #kab_choice, #kec_choice').empty();
            $('#container').html('');
        }

        function hide_all(){
            $("#prop_sp, #kab_sp, #kec_sp").hide();
            $('.print').hide();
        }

        function get_data(){
           $('.print').show();
            $.ajax({
                type : 'POST',
                url: '<?= base_url() ?>laporan/laporan_demografi_wilayah/',
                cache: false,
                dataType: 'json',
                data: $('#form').serialize(),
                success: function(data) {
                     generate_chart(data);
                }
            });
        }

        function cetak_grafik(){
            window.open($('#container').html(), 'Grafik Demografi Berdasarkan Wilayah', 'location=1,status=1, scrollbars=1 width=600px, height=400px ')
        }
    </script>
    <div id="tabs">
        <ul>
            <li><a href="#tabs-1">Parameter</a></li>
        </ul>
        <div id="tabs-1">
            <?= form_open('','id=form') ?>
            <table width="100%" class="inputan">
                <tr><td>Range Tanggal Reg.</td><td><?= form_input('fromdate', date('d/m/Y'), 'id = fromdate size=10 style="width: 75px;"') ?> s.d <?= form_input('todate', date('d/m/Y'), 'id = todate size=10 style="width: 75px;"') ?></td></tr>
                <tr><td>Range Area:</td><td>
                    <span class="label"><?= form_radio('kategori', 'provinsi', true, 'id=rd_prov') ?> <label for="rd-prov">Provinsi</label></span>
                    <span class="label"><?= form_radio('kategori', 'kabupaten', false, 'id=rd_kab') ?> <label for="rd-kab">Kabupaten</label></span>
                    <span class="label"><?= form_radio('kategori', 'kecamatan', false, 'id=rd_kec') ?> <label for="rd-kec">Kecamatan</label></span>
                    <span class="label"><?= form_radio('kategori', 'kelurahan', false, 'id=rd_kel') ?> <label for="rd-kel">Kelurahan</label></span>

                <tr id="prop_sp"><td class="area">Provinsi</td><td><?= form_dropdown('prop_choice', array(), '', 'id=prop_choice class=drop') ?><td></tr>
                <tr id="kab_sp"><td class="area">Kabupaten</td><td><?= form_dropdown('kab_choice', array(), '', 'id=kab_choice class=drop') ?><td></tr>
                <tr id="kec_sp"><td class="area">Kecamatan</td><td><?= form_dropdown('kec_choice', array(), '', 'id=kec_choice class=drop') ?><td></tr>
                <tr><td><span id="dinamic_label"></span></td><td><span class="item"><?= form_multiselect('multi[]', array(), null, 'id = multiSelect size=10 style="width:260px; height: 100px;"') ?></span><td></tr>

                <tr><td></td><td><span class="item"><?= form_button('semua', 'Pilih semua', 'id=multiAll') ?><?= form_button('batal', 'Batalkan semua', 'id = multiNone') ?></span><td></tr>
                <tr><td></td><td><?= form_button('cari', 'Cari', 'id=cari_range onclick=get_data()') ?><?= form_button(null, 'Reset', 'id=reset') ?></td></tr>
            </table>
            <?= form_close() ?>
            <div id="container"></div>
        </div>
    </div>
    <br/><br/><br/>
    <script type="text/javascript">
        function generate_chart(data){
            $('#container').highcharts({
                chart: {
                    type: 'bar'
                },
                title: {
                    text: data.title
                },
                xAxis: {
                    categories: data.area
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
