<link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/jquery-ui-timepicker-addon.css') ?>" media="all" />
<script type="text/javascript" src="<?= base_url('assets/js/jquery-ui-timepicker-addon.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/js/jquery-ui-sliderAccess.js') ?>"></script>
<script>
    
    $(function(){
        $('.keluar').datetimepicker({
            changeYear : true,
            changeMonth : true
        });
        $('button[id=tambahrow]').button({icons: {secondary: 'ui-icon-circle-plus'}});
        $('#deletion').button({ icons: {secondary: 'ui-icon-circle-close'}});
        $('#search, button[type=submit]').button({icons: {secondary: 'ui-icon-circle-check'}});
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
        
        $('#tambahrow').click(function() {
          
            if($('.keluar').length == 0){
                var rows = $('.row_inap').length;
                add(rows);
                
            }else{
                custom_message('Peringatan',"Tidak dapat menambah data");
            }
            
        })
        
        $('#myform').submit(function() {            
            $.ajax({
                type : 'POST',
                url: '<?= base_url('rawatinap/save_rawatinap') ?>',               
                data: $(this).serialize(),
                cache: false,
                success: function(data) {
                    $('#result').html(data);
                }
            });
            return false;
        })
        
        
       
    });
    
    
    function add(i) {
        str = '<tr class="row_inap">'+
            '<td><input type="hidden" name="unit" id="hd_unit'+i+'" /><input type="text" id=unit'+i+' style="width:30%" placeholder="Unit" />&nbsp;'+
            '<select name="kelas" style="width:30%" id="kelas'+i+'"><option value="pilih">Pilih</option><option value="VIP">VIP</option><option value="III">III</option></select>&nbsp;'+
            '<select name="no[]" style="width:20%" id="no'+i+'"><option value="">Pilih</option></select>'+
            '<td><span id=tarif'+i+'></span></td>'+            
            '<td><input type="text" name="in_time[]" id=in_time'+i+' /></td>'+
            '<td></td>'+
            '<td></td>'+
            '<td>Sub </td>'+
            '<td>#</td></tr>';      
        $('#inap_add tbody').append(str);
        $('#in_time'+i).datetimepicker({
            changeYear : true,
            changeMonth : true,
            minDate : +0
        });
        
        $('#unit'+i).autocomplete("<?= base_url('rawatinap/get_data_unit') ?>",
        {
            parse: function(data){
                var parsed = [];
                for (var i=0; i < data.length; i++) {
                    parsed[i] = {
                        data: data[i],
                        value: data[i].nama// nama field yang dicari
                    };
                }
                return parsed;
            },
            formatItem: function(data,i,max){
                var str = '<div class=result>'+data.nama +'</div>';
                return str;
            },
            width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
            dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
        }).result(
        function(event,data,formated){
            $(this).val(data.nama);
            $('#hd_unit'+i).val(data.id);
          
        });
        
        $('#no'+i).change(function(){
           
            $('#tarif'+i).html( $('#no'+i).val().split("-")[1]);
        });
        
        $('#kelas'+i).change(function(){
            var unit =   $('#hd_unit'+i).val();
            var kelas =  $('#kelas'+i).val();
        
            $.ajax({
                type : 'POST',
                url: '<?= base_url('rawatinap/get_data_bed') ?>/'+unit+'/'+kelas,
                cache: false,
                success: function(data) {
                    var obj = jQuery.parseJSON(data);
                    $.each(obj, function ( index, val) {
                        $('#no'+i).append('<option value="'+val.id+'-'+val.tarif+'">'+val.no+'</option>');
                    });
                }
            });
        })
        
        
       
       
    }
</script>
<div class="circle">
    <?= form_open('', 'id = myform') ?>
    <?= form_hidden('no_daftar', $no_daftar) ?>
    <?= form_button('', 'Tambah', 'id = tambahrow') ?>
    <table class="tabel" id="inap_add" width="100%">
        <tbody>
            <tr>
                <th width="30%">Bed</th>
                <th width="10%">Tarif</th>
                <th width="15%">Waktu Masuk</th>
                <th width="15%">Waktu Keluar</th>
                <th width="10%">Durasi</th>
                <th width="10%">Sub Total(Rp)</th>
                <th width="10%">#</th>
            </tr>
            <?php foreach ($bed as $k => $val): ?>
                <tr>
                    <td width="25%"><?= $val->unit ?>, <?= $val->kelas ?>, <?= $val->no ?></td>
                    <td width="15%"><?= $val->tarif ?></td>
                    <td width="15%"><?= datetime($val->masuk_waktu) ?></td>
                    <td width="15%">
                        <?php
                        if ($val->keluar_waktu != null) {
                            echo datetime($val->keluar_waktu);
                        } else {
                            echo form_hidden('id[]', $val->id);
                            echo form_hidden('tarif[]', $val->tarif);
                            echo form_hidden('masuk[]', $val->masuk_waktu);
                            echo '<input type="text" name="out[]" class="keluar" id=out_time' . $k . ' />';
                        }
                        ?>
                    </td>
                    <td width="10%"><?= get_duration($val->masuk_waktu, $val->keluar_waktu) ?></td>
                    <td width="10%"><?= $val->sub_total ?> </td>
                    <td width="10%">#</td>
                </tr>
            <?php endforeach; ?>



        </tbody>

    </table>
    <div>
        <br/>
        <?= form_submit('Simpan', 'Simpan', 'id=search') ?>
        <?= form_button('Hapus', 'Hapus', 'id=deletion') ?>
        <?= form_button('Reset', 'Reset', 'id=reset') ?>
    </div>
    <?= form_close() ?>
</div>
