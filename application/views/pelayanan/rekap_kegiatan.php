<title><?= $title ?></title>
<script type="text/javascript">
    var waktu_awal;
    $(function() {
        $('#tabs').tabs();
        $('#awal').datepicker({
            changeYear: true,
            changeMonth: true
        });

        $('#akhir').datepicker({
            changeYear: true,
            changeMonth: true
        });
        $('#cari').button({icons: {secondary: 'ui-icon-search'}}).click(function() {
            $.ajax({
                url: '<?= base_url('pelayanan/rekap_kegiatan_load_data') ?>',
                type: 'GET',
                data: $('#form_kegiatan').serialize(),
                cache: false,
                dataType :'json',
                success: function(data) {
                    write_list_rekap(data);
                }
            });
        });
        $('#cetak').button({icons: {secondary: 'ui-icon-print'}});
        $('#reset').button({icons: {secondary: 'ui-icon-refresh'}}).click(function() {
            $('#loaddata').empty();
            $('#loaddata').load($.cookie('url'));
        });

        $('#subsub').autocomplete("<?= base_url('pelayanan/load_data_sub_sub_jenis_layanan') ?>",
            {   
                extraParams :{ 
                    id_sub : function(){
                        return $('input[name=id_sub]').val();
                    }
                },
                parse: function(data){
                    var parsed = [];
                    for (var i=0; i < data.length; i++) {
                        parsed[i] = {
                            data: data[i],
                            value: data[i].subsub_jenis // nama field yang dicari
                        };
                    }
                    return parsed;
                },
                formatItem: function(data,i,max){
                    var str = '<div class=result>'+data.subsub_jenis+'<br/>'+data.sub_jenis+'<br/>'+data.jenis+'</div>';
                    return str;
                },
                width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
            }).result(
            function(event,data,formated){
                $(this).val(data.subsub_jenis);
                $('input[name=id_sub_sub]').val(data.id_subsub);
                $('#sub').val(data.sub_jenis);
                $('input[name=id_sub]').val(data.id_sub);
                $('#jenis').val(data.jenis);
                $('input[name=id_jenis]').val(data.id_jenis);

            });

            $('#sub').autocomplete("<?= base_url('pelayanan/load_data_sub_jenis_layanan') ?>",
            {
                extraParams :{ 
                    id_jenis : function(){
                        return $('input[name=id_jenis]').val();
                    }
                },
                parse: function(data){
                    var parsed = [];
                    for (var i=0; i < data.length; i++) {
                        parsed[i] = {
                            data: data[i],
                            value: data[i].sub_jenis // nama field yang dicari
                        };
                    }
                    return parsed;
                },
                formatItem: function(data,i,max){
                    var str = '<div class=result>'+data.sub_jenis+'<br/>'+data.jenis+'</div>';
                    return str;
                },
                width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
            }).result(
            function(event,data,formated){
                subsub_reset();
                $(this).val(data.sub_jenis);
                $('input[name=id_sub]').val(data.id_sub);
                $('#jenis').val(data.jenis);
                $('input[name=id_jenis]').val(data.id_jenis);

            });

            $('#jenis').autocomplete("<?= base_url('pelayanan/load_data_jenis_layanan') ?>",
            {
                parse: function(data){
                    var parsed = [];
                    for (var i=0; i < data.length; i++) {
                        parsed[i] = {
                            data: data[i],
                            value: data[i].jenis // nama field yang dicari
                        };
                    }
                    return parsed;
                },
                formatItem: function(data,i,max){
                    var str = '<div class=result>'+data.jenis+'</div>';
                    return str;
                },
                width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
            }).result(
            function(event,data,formated){
                subsub_reset();
                $(this).val(data.jenis);
                $('input[name=id_jenis]').val(data.id_jenis);

            });


    });

    function subsub_reset(){
        $('#subsub').val('');
        $('input[name=id_sub_sub]').val('');
        $('#sub').val('');
        $('input[name=id_sub]').val('');
    }

    function write_list_rekap(data){
        var str = '<div class="data-list"><table class="list-data" id="rekap_table" width="100%">'+
                '<thead><tr><th width="10%">No.</th><th width="80%">Nama Layanan</th><th width="10%">Jumlah</th></tr></thead>'+
                '<tbody></tbody></table></div>';
        $('#result').html(str);

        $.each(data, function(i, v){
            str = '<tr class="row_diag '+((i%2==1)?'even':'odd')+'">'+
            '<td align="center">'+(i+1)+'</td>'+
            '<td>'+v.nama_layanan+'</td>'+
            '<td align="center">'+v.jumlah+'</td>';
            $('#rekap_table tbody').append(str);
        });

        $("table").tablesorter();
    var onSampleResized = function(e){
            var columns = $(e.currentTarget).find("th");
            var msg = "columns widths: ";
            columns.each(function(){ msg += $(this).width() + "px; "; });
    };
    $(".tabel").colResizable({
        liveDrag:true,
        gripInnerHtml:"<div class='grip'></div>", 
        draggingClass:"dragging", 
        onResize:onSampleResized
    });

    }

    function cetak_rl3_3(){
        var dWidth = $(window).width();
        var dHeight= $(window).height();
        var x = screen.width/2 - dWidth/2;
        var y = screen.height/2 - dHeight/2;
        window.open('<?= base_url("pelayanan/cetak_rl_kegiatan_rs/")."/".date("Y") ?>/','Myprint','width='+dWidth+', height='+dHeight+', left='+x+',top='+y);
        
    }
</script>
<div class="titling"><h1><?= $title ?></h1></div>
<div class="kegiatan">
    
    <div id="tabs">
        <ul>
            <li><a href="#tabs-1">Form Rekap</a></li>
        </ul>
        <div id="tabs-1">
            <?= form_open('','id=form_kegiatan') ?>
            <table class="inputan" width="100%">
                <tr><td>Range Tanggal:</td><td><?= form_input('awal', date("d/m/Y"), 'id=awal style="width: 75px;" size=10') ?> s.d <?= form_input('akhir', date("d/m/Y"), 'id=akhir size=10 style="width: 75px;"') ?></td></tr>
                <tr><td>Jenis Layanan</td><td><?= form_input('jenis','','id=jenis size=40') ?><?= form_hidden('id_jenis') ?></td></tr>
                <tr><td>Sub Jenis Layanan</td><td><?= form_input('sub','','id=sub size=40') ?><?= form_hidden('id_sub') ?></td></tr>
                <tr><td>Sub Sub Jenis Layanan</td><td><?= form_input('subsub','','id=subsub size=40') ?><?= form_hidden('id_sub_sub') ?></td></tr>
                <tr><td></td><td><?= form_button(NULL, 'Cari', 'id=cari') ?>
                <!--<?= form_button(NULL, 'Cetak', 'id=cetak onclick=cetak_rl3_3()') ?>-->
                <?= form_button(NULL, 'Reset', 'id=reset') ?></td></tr>
            </table>
            <?= form_close() ?>
            <br/>
            <div id="result"></div>
        </div>
    </div>
</div>