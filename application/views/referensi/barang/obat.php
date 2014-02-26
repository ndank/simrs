<script type="text/javascript">
    var request;
    $(function(){
        $('#namaobat').focus();
        $('#simpan').button({icons: {secondary: 'ui-icon-circle-check'}});
        $('#resetobat').button({icons: {secondary: 'ui-icon-refresh'}});
        $('#cariobat').button({icons: {secondary: 'ui-icon-search'}});
        get_obat_list(1,'');
        $('#resetobat').click(function(){
            $('#loaddata').empty();
            $('#loaddata').load('<?= base_url('referensi/barang') ?>');
        });

        $('#formobat').submit(function(){
            form_submit();
            return false;
        });
       
        
        $('.pabrik').autocomplete("<?= base_url('inv_autocomplete/load_data_pabrik') ?>",
        {
            parse: function(data){
                var parsed = [];
                for (var i=0; i < data.length; i++) {
                    parsed[i] = {
                        data: data[i],
                        value: data[i].nama // nama field yang dicari
                    };
                }
                $('input[name=id_pabrik_obat]').val('');
                $('input[name=id_pabriks_obat]').val(data.id);
                return parsed;
            },
            formatItem: function(data,i,max){
                var str = '<div class=result>'+data.nama+'</div>';
                return str;
            },
            width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
            dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
        }).result(
        function(event,data,formated){
            $(this).attr('value',data.nama);
            $('input[name=id_pabrik_obat]').val(data.id);
            $('input[name=id_pabriks_obat]').val(data.id);
        });
        
        
        $('#konfirmasi_obat').dialog({
            autoOpen: false,title :'Konfirmasi',height: 200,width: 300,
            modal: true,resizable : false,
            buttons: [ 
                { text: "Ok", click: function() { 
                        save_obat();
                        $( this ).dialog( "close" ); 
                    } 
                }, 
                { text: "Batal", click: function() { 
                        $( this ).dialog( "close" ); 
                    } 
                } 
            ]
        });        
               
    });


    function form_submit(){
            var Url = '<?= base_url('referensi/manage_barang_obat') ?>/cek/1';
            var namaobat = $('#namaobat').val();
            var tipe = $('input[name=id_obat]').val();

            if($('#namaobat').val() === ''){
                custom_message('Peringatan','Nama obat tidak boleh kosong !', '#namaobat');
            }else{    
                $.ajax({
                    type : 'GET',
                    url: Url,               
                    data: 'nama='+namaobat,
                    dataType: 'json',
                    cache: false,
                    success: function(data) {
                        if (tipe === ''){
                            if (data.status === false){
                                $('#text_konfirmasi_obat').html('Nama Obat <b>"'+namaobat+'"</b> sudah ada<br/> Apakah anda yakin akan menambahkannya lagi?');            
                                $('#konfirmasi_obat').dialog("open");
                            } else {
                                save_obat();
                            }                        
                        }else{
                            save_obat();
                        }
                            
                    }
                });        
                
            }
        }
    
    function save_obat(){
        var Url = '';       
        var tipe = $('input[name=id_obat]').val();
        if( tipe=== ''){
            Url = '<?= base_url('referensi/manage_barang_obat') ?>/add/';
        }else{
            Url = '<?= base_url('referensi/manage_barang_obat') ?>/edit/';
        }            
         
        if(!request) {
            request =  $.ajax({
                type : 'POST',
                url: Url+$('.noblock').html(),               
                data: $('#formobat').serialize(),
                cache: false,
                dataType: 'json',
                success: function(data) {
                    $('input[name=id_obat]').val(data.id);
                    var id = data.id;
                    generate_msg('ok',tipe);
                    if (tipe === '') {
                        $.ajax({
                            type : 'GET',
                            url: '<?= base_url('referensi/manage_barang_obat') ?>/get_data/1',
                            data :'id='+id,
                            cache: false,
                            success: function(data) {
                                $('#obat_list').html(data);

                            }
                        });
                    } else {
                        $.ajax({
                            type : 'GET',
                            url: '<?= base_url('referensi/manage_barang_obat') ?>/list/'+$('.noblock').html(),
                            cache: false,
                            success: function(data) {
                                $('input[type=text], input[type=hidden], select, textarea').val('');
                                $('input[type=radio]').removeAttr('checked');
                                $('#obat_list').html(data);
                            }
                        });
                    }
                    request = null;                            
                },
                error : function(){
                    generate_msg('fail',tipe);
                }
            });
        }   
    }
    
    function reset_all(){
        
        $('.nama, .pabrik, #ven, #ddd').val('');
        
        $('#kekuatan').val(1);
        $('#admr').val(0);
        $('#a').attr('checked','checked');
        $('#c').removeAttr('checked');
        $('#j').attr('checked','checked');
        $('#k').removeAttr('checked');

        $('#aa').attr('checked','checked');
        $('#cc').removeAttr('checked');
        $('#jj').attr('checked','checked');
        $('#kk').removeAttr('checked');
        
        $('#perundangan').val('');
        $('select[name=satuan]').val('');
        $('select[name=sediaan]').val('');
        $('input[name=id_pabrik_obat]').val('');
        $('input[name=id_pabriks_obat]').val('');
        $('#id_barang').val('');
    }
    
    function get_obat_list(p,search){
        
        $.ajax({
            type : 'GET',
            url: '<?= base_url('referensi/manage_barang_obat') ?>/list/'+p,
            data : $('#formobat').serialize(),
            cache: false,
            success: function(data) {
                $('#obat_list').html(data);
            }
        });
    }
    
    function delete_obat(id, search){
        $('<div></div>')
          .html("Anda yakin akan menghapus data ini ?")
          .dialog({
             title : "Hapus Data",
             modal: true,
             buttons: [ 
                { 
                    text: "Ok", 
                    click: function() { 
                        $.ajax({
                            type : 'GET',
                            url: '<?= base_url('referensi/manage_barang_obat') ?>/delete/'+$('.noblock').html(),
                            data :'id='+id+'&search='+search,
                            cache: false,
                            success: function(data) {
                                get_obat_list($('.noblock').html(),'');
                                alert_delete();
                            },
                            error: function(){
                                alert_delete_failed();
                            }
                        });
                        $( this ).dialog( "close" ); 
                    } 
                }, 
                { text: "Batal", click: function() { $( this ).dialog( "close" );}} 
            ]
        });     
    }
    
    function edit_obat(arr){
        var data = arr.split("#");
        $('input[name=id_obat]').val(data[0]);
        $('#namaobat').val(data[1]);
        $('input[name=id_pabrik_obat]').val(data[2]);
        $('.pabrik').val(data[3]);
        $('#kekuatan').val(data[4]);
        $('select[name=satuan]').val(data[5]);
        $('select[name=sediaan]').val(data[6]);
        $('#ven').val(data[7]);
        if (data[8] === 'Ya') {
            $('#ya').attr('checked','checked');
        } else if(data[8] === 'Tidak') {
            $('#tidak').attr('checked','checked');
        }else{
            $('#ya, #tidak').removeAttr('checked');
        }
        $('#admr').val(data[9]);
        $('#perundangan').val(data[10]);
          
        
        if(data[11] ==='Generik'){
            $('#a').attr('checked','checked');
        }else if(data[11] ==='Non Generik'){
            $('#c').attr('checked','checked');
        }else{
            $('#a, #c').removeAttr('checked');
        }
        
        if(data[12] ==='Ya'){
            $('#j').attr('checked','checked');
        }else if (data[12] ==='Tidak') {
            $('#k').attr('checked','checked');
        }else{
            $('#j, #k').removeAttr('checked');
        }
        $('#kandungan').val(data[13]);
        $('#aturan_pakai').val(data[14]);
        $('#efek_samping').val(data[15]);
        $('#konsinyasi').val(data[16]);
        
    }
    
   
</script>


<div class="data-input">
   <table width="100%" class="inputan">Parameter</legend>
        <?= form_open('', 'id=formobat') ?>
        <?= form_hidden('id_obat') ?>
       <table width="100%" cellpadding="0" cellspacing="0"><tr valign="top"><td width="50%">
            <tr><td>Nama:</td><td><?= form_input('nama', '', 'id=namaobat class=nama size=40') ?> 
            <tr><td>Pabrik:</td><td><?= form_input('pabrik_obat', '', 'class=pabrik size=40') ?>
                    <?= form_hidden('id_pabrik_obat', '', 'class=id_pabrik id=pabrik_id') ?>
            <tr><td>Kekuatan:</td><td><?= form_input('kekuatan', '', 'id=kekuatan size=10') ?> 
            <tr><td>Satuan:</td><td><?= form_dropdown('satuan', $satuan, null) ?>
            <tr><td>Macam Sediaan:</td><td><?= form_dropdown('sediaan', $sediaan, null) ?>
            <tr><td>Ven:</td><td><?= form_dropdown('ven', array(''=>'Pilih','Vital' => 'Vital','Esensial' => 'Esensial','Non' => 'Non'),'','id=ven') ?> 
            <tr><td>High Alert:</td><td>
            <span class="label"><?= form_radio('ha', 'Ya', false, 'id=ya') ?> Ya </span>
            <span class="label"><?= form_radio('ha', 'Tidak', False, 'id=tidak') ?> Tidak</span>
            <tr><td>Adm. R:</td><td><?= form_dropdown('admr', $admr, null, 'id=admr') ?>
            <tr><td>Perundangan:</td><td><?= form_dropdown('perundangan', $perundangan, null, 'id=perundangan') ?>
            <tr><td>Generik:</td><td>
            <span class="label"><?= form_radio('generik', 'Generik', false, 'id=a') ?>Ya</span>
            <span class="label"><?= form_radio('generik', 'Non Generik', false, 'id=c') ?>Tidak</span>
            <tr><td>Formularium</td><td>
            <span class="label"><?= form_radio('formularium', 'Ya', false, 'id=j') ?>Ya</span>
            <span class="label"><?= form_radio('formularium', 'Tidak', false, 'id=k') ?>Tidak</span>
            </td><td width="50%">
            <tr><td>Kandungan:</td><td><?= form_textarea('kandungan', NULL, 'id=kandungan') ?>
            <tr><td>Aturan Pakai:</td><td><?= form_textarea('aturan_pakai', NULL, 'id=aturan_pakai') ?>
            <tr><td>Efek Samping:</td><td><?= form_textarea('efek_samping', NULL, 'id=efek_samping') ?>
            <tr><td>Konsinyasi:</td><td><?= form_dropdown('konsinyasi', array('Tidak' => 'Tidak', 'Ya' => 'Ya'), NULL, 'id=konsinyasi') ?>
            </td></tr>
        </table>
        <tr><td></td><td><?= form_button('', 'Simpan', 'id=simpan onclick=form_submit()') ?>
        <?= form_button('','Cari','id=cariobat onclick=get_obat_list(1)') ?>
        <?= form_button(null, 'Reset', 'id=resetobat') ?>

        <?= form_close() ?>
    </table>
    <?= form_close() ?>

</div>

<div id="konfirmasi_obat" style="display: none; padding: 20px;">
    <div id="text_konfirmasi_obat"></div>
</div>

<div id="obat_list" style="padding: 0;"></div>

