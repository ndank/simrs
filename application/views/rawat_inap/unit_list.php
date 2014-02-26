<link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/jquery-ui-timepicker-addon.css') ?>" media="all" />
<script type="text/javascript" src="<?= base_url('assets/js/jquery-ui-timepicker-addon.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/js/jquery-ui-sliderAccess.js') ?>"></script>
<script>
    
    $(function(){
        $('.keluar').datetimepicker({
            changeYear : true,
            changeMonth : true,
            minDate : +0
        });

        $('#deletion').button({ icons: {secondary: 'ui-icon-circle-close'}});
        $('input[type=submit]').each(function(){ $(this).replaceWith('<button type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'">' + $(this).val() + '</button>');});
        $('button[type=submit]').button({icons: {secondary: 'ui-icon-circle-check'}});
        $('#reset').button({icons: {secondary: 'ui-icon-refresh'}});
       
        $('#nopasien').autocomplete("<?= base_url('rawatinap/get_data_unit/') ?>",
        {
            parse: function(data){
                var parsed = [];
                for (var i=0; i < data.length; i++) {
                    parsed[i] = {
                        data: data[i],
                        value: data[i].no_daftar // nama field yang dicari
                    };
                }
                return parsed;
                
            },
            formatItem: function(data,i,max){
                var str = '<div class=result>'+data.no_daftar+' - '+data.nama+'</div>';
                return str;
            },
            width: 370, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
            dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
        }).result(
        function(event,data,formated){
            $(this).val(data.no_daftar);
           
        });
        
        $('#myform').submit(function() { 
      
            if($("#out_time").val() == ''){
                custom_message('Peringatan','Waktu keluar tidak boleh kosong !','#out_time');
                return false;
            }
           

            $.ajax({
                type : 'POST',
                url: '<?= base_url("rawatinap/save_rawatinap") ?>',               
                data: $(this).serialize(),
                cache: false,
                success: function(data) {
                    $('#result').html(data);
                    alert_edit();
                },
                error: function(){
                    custom_message('Kesalahan Koneksi','Koneksi jaringan bermasalah, mohon cek!');
                }
            });
            
            return false;
        })
        
       
    });
    
    function eliminate(el) {
        var parent = el.parentNode.parentNode;
        parent.parentNode.removeChild(parent);      
    }
    
    function delete_bed(id, id_pk, id_tarif, waktu){  
    var id_kunjungan = $('#no_daftar').html();      
        $('<div></div>')
          .html("Anda yakin akan menghapus data ini ?<br/><b>Peringatan!!</b> Saat anda menghapus data ini, beberapa data terkait juga akan terhapus")
          .dialog({
             title : "Hapus Data",
             modal: true,
             buttons: [ 
                { 
                    text: "Ok", 
                    click: function() { 
                        $.ajax({
                            type : 'GET',
                            url: '<?= base_url("rawatinap/delete_data_bed") ?>/'+id,
                            cache: false,
                            data: 'id_tarif='+id_tarif+'&waktu='+waktu+'&no_daftar='+id_kunjungan,
                            dataType: 'json',
                            success: function(data) {
                                if (data.result == true) {
                                    alert_delete();
                                    get_list_bed(data.no_daftar);
                                }else{
                                    alert_delete_failed();
                                }   
                            }
                        });
                        $( this ).dialog( "close" ); 
                    } 
                }, 
                { text: "Batal", click: function() { $( this ).dialog( "close" );}} 
            ]
        });     
    }

    
    function reset_data(){
        var url = '<?= base_url('rawatinap/billing_rawat_inap') ?>';
        $('#loaddata').load(url);
        $('#search').attr('disabled', 'disabled');
        $('.row_inap').html('');
        $('tr').removeClass('row_inap');
    }
</script>
<div class="data-list">
    <?= form_open('', 'id = myform') ?>
    <?= form_hidden('no_daftar', $no_daftar) ?>
    <?= form_hidden('count', sizeof($bed)) ?>
    
    <br/>
    <table class="list-data" id="inap_add" width="100%">
        <tbody>
            <tr>
                <th width="37%">Tarif</th>
                <th width="15%">Waktu Masuk</th>
                <th width="15%">Waktu Keluar</th>
                <th width="10%">Durasi (Hari)</th>                
                <th width="10%">Tarif(Rp)</th>
                <th width="10%">Sub Total(Rp)</th>
                <th width="3%">Aksi</th>
            </tr>
            <?php if(sizeof($bed) > 0):?>
            <?php foreach ($bed as $k => $val): ?>
                <tr class="row_inaps <?= ($k % 2 == 0) ? 'even' : 'odd' ?>">
                    <td width="25%"><?= $val->unit ?>, <?= $val->kelas ?></td>
                    <td width="15%" align="center"><?= datetime($val->masuk_waktu) ?></td>
                    <td width="15%" align="center">
                        <?php
                        if ($val->keluar_waktu != null) {
                            echo datetime($val->keluar_waktu);
                        } else {
                            echo form_hidden('id', $val->id);
                            echo form_hidden('id_pk', $val->id_pelayanan_kunjungan);
                            echo form_hidden('no_tt', $val->no_tt);
                            echo form_hidden('masuk', $val->masuk_waktu);
                            echo '<input type="text" name="out" class="keluar" id=out_time />';
                        }
                        ?>
                    </td>
                    <td width="10%" align="center">
                        <?php
                        if ($val->keluar_waktu != null) {
                            $durasi = get_duration($val->masuk_waktu, $val->keluar_waktu);
                        } else {
                            $durasi = get_duration($val->masuk_waktu, date('Y-m-d H:i:s'));
                        }

                        if ($durasi['day'] == 0) {
                            $durasi['day']++;
                        } else if ($durasi['hour'] > 0) {
                            $durasi['day']++;
                        }
                        echo $durasi['day'];
                        ?>
                    </td>
                    <td width="15%" style="text-align:  right"><?= rupiah($val->nominal) ?><?= form_hidden('id_tarif', $val->id_tarif) ?></td>
                    <td width="10%" style="text-align: right">
                        <?php
                        if ($val->keluar_waktu != null) {
                            echo rupiah($durasi['day'] * $val->nominal);
                        } else {
                            echo rupiah($durasi['day'] * $val->nominal);
                        }
                        ?> 

                    </td>
                    <td class="aksi" align="center">
                        <a class="deletion" onclick="delete_bed(<?= $val->id ?>,<?= $val->id_pelayanan_kunjungan ?>, <?= $val->id_tarif ?>,'<?= $val->masuk_waktu ?>')"></a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
            <tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
        <?php endif;?>


        </tbody>

    </table>
    <div>
        <br/>
        <?= form_submit('Simpan', 'Simpan', 'id=search') ?>
        <?= form_button('Reset', 'Reset', 'id=reset  onClick=reset_data()') ?>
    </div>
    <?= form_close() ?>
</div>
