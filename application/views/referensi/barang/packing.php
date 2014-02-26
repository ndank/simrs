    <script type="text/javascript">
        var request;
        $(function(){
            $('#barang').focus();
            $('input[type=submit]').each(function(){ $(this).replaceWith('<button type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'">' + $(this).val() + '</button>');});
            $('button[type=submit]').button({icons: {secondary: 'ui-icon-circle-check'}});
            $('#reset_packing, #cetak_batal').button({icons: {secondary: 'ui-icon-refresh'}}); 
            $('#cari_packing').button({icons: {secondary: 'ui-icon-search'}});
            $('#cetak-jumlah').button({icons: {secondary: 'ui-icon-print'}});
            get_packing_list(1, '');
            $('#reset_packing').click(function(){
                $('#kemasan').load('<?= base_url('referensi/packing_barang') ?>');
            });

            $('#formpacking').submit(function(){
                form_submit();
                return false;
            });
             
            
            $('#konfirmasi').dialog({
                autoOpen: false,
                title :'Konfirmasi',
                height: 200,
                width: 300,
                modal: true,
                resizable : false,
                buttons: [ 
                    { text: "Ok", click: function() { 
                            save();
                            $( this ).dialog( "close" ); 
                        } 
                    }, 
                    { text: "Batal", click: function() { 
                            $( this ).dialog( "close" ); 
                        } 
                    } 
                ]
            });
            
            $('#barang_cari').blur(function(){
                if($('#barang_cari').val()=== ''){
                    $('input[name=barang_cari]').val(''); 
                }               
            });
        
            $('.barang').autocomplete("<?= base_url('inv_autocomplete/load_data_barang') ?>",
            {
                extraParams :{ 
                    jenis : function(){
                        return 'Obat';
                    }
                },
                parse: function(data){
                    var parsed = [];
                    for (var i=0; i < data.length; i++) {
                        parsed[i] = {
                            data: data[i],
                            value: data[i].nama // nama field yang dicari
                        };
                    }
                    $('input[name=id_barang]').val('');
                    return parsed;
                },
                formatItem: function(data,i,max){
                    if (data.pabrik !== null) {var pab = data.pabrik;} else {var pab = '';}
                    if (data.satuan !== null) {var satuan = data.satuan;} else {var satuan = '';}
                    if (data.sediaan !== null) {var sediaan = data.sediaan;} else {var sediaan = '';}
                    if (data.kekuatan !== null) {var kekuatan = data.kekuatan;} else {var kekuatan = '';}

                    if (data.id_obat !== null) {
                        var str = '<div class=result>'+data.nama+' '+((kekuatan !== '1')?kekuatan:'')+' '+satuan+' '+sediaan+'  <i> '+pab+'</i></div>';
                        
                    } else {
                        if (data.pabrik !== null) {
                            var str = '<div class=result>'+data.nama+'<i> '+data.pabrik+'</i></div>';
                        } else {
                            var str = '<div class=result>'+data.nama+'</div>';
                        }
                    }
                    return str;
                },
                width: 320, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                dataType: 'json', // tipe data yang diterima oleh library ini disetup sebagai JSON
                cacheLength: 0,
                max: 100
            }).result(
            function(event,data,formated){
                if (data.pabrik !== null) {
                        var pab = data.pabrik;
                    } else {
                        var pab = '';
                    }
                if (data.id_obat !== null) {
                    if (data.kekuatan !== null && data.satuan !== null && data.sediaan !== null) {
                        var str = data.nama+' '+data.kekuatan+' '+data.satuan+' '+data.sediaan+' '+pab;
                    } 
                    else if (data.kekuatan !== null && data.satuan !== null && data.sediaan === null) {
                        var str = data.nama+' '+data.kekuatan+' '+data.satuan+' '+pab+'';
                    } else {
                        var str = data.nama;
                    }   
                } else {
                    var str = data.nama+' '+pab;
                }
                $(this).val(str);
                $('input[name=id_barang]').val(data.id_barang);
            });
        
            $('#barcode').live('keydown', function(e) {
                if (e.keyCode===13) {
                    $('input[name=barcode]').val($('#barcode').val());
                }
            });
            $('#barcode').keyup(function() {
                $('input[name=barcode]').val($('#barcode').val());
            });
                    
        
            $('#cetak-jumlah').click(function() {
                var barcode = $('#real-text').html();
                var jumlah  = $('#jml').val();
                window.open('<?= base_url('referensi/cetak_barcode') ?>?barcode='+barcode+'&jumlah='+jumlah, 'MyWindow', 'width=500px,height=400px,scrollbars=1');
            });
        
        
            $('#cetak_batal').click(function() {
                $('#cetak-barcode').fadeOut('fast');
            });
        
        
        });


        function form_submit(){
            var barcode = $('#barcode').val();
            var id_barang = $('input[name=id_barang]').val();
            var nama = $('#barang').val();
            var kemasan = $('#kemasan').val();
            var isi = $('#isi').val();
            var satuan = $('#satuan').val();
            var tipe = $('input[name=id]').val();
            
            if($('input[name=id_barang]').val()===''){
                custom_message('Peringatan','Nama barang tidak boleh kosong atau pilih barang yang ada !','#barang');
            } else if($('#kemasan').val()===''){
                custom_message('Peringatan','Jenis kemasan harus dipilih !','#kemasan');
            }else if($('#isi').val()===''){
                custom_message('Peringatan','Isi tidak boleh kosong !','#isi');
            }else if($('#satuan').val() ===''){
                custom_message('Peringatan','Jenis satuan harus dipilih !','#satuan');
            }else{    
                $.ajax({
                    url: '<?= base_url('referensi/manage_packing') ?>/cek/'+null+'/Farmasi/',
                    data:'barcode='+barcode+'&id_barang='+id_barang+'&kemasan='+kemasan+'&isi='+isi+'&satuan='+satuan,
                    cache: false,
                    dataType: 'json',
                    success: function(msg){
                         if (tipe === ''){
                            if (msg.status === false){
                                $('#text_konfirmasi').html('Nama Kemasan <b>"'+nama+'"</b> sudah ada<br/> Apakah anda yakin akan menambahkannya lagi?');            
                                $('#konfirmasi').dialog("open");
                            } else {
                                save();                                    
                            }                        
                        }else{
                            save();
                        }
                    }
                });                    
            }
        }
    
        function save(){
            var Url = '';     
            var tip = $('input[name=id]').val();      
            if(tip === ''){
                Url = '<?= base_url('referensi/manage_packing') ?>/add/'+null+'/Farmasi/';
            }else{
                Url = '<?= base_url('referensi/manage_packing') ?>/edit/'+null+'/Farmasi/';
            }
                      
            
            if(!request) {
                request =$.ajax({
                    type : 'POST',
                    url: Url+$('.noblock').html(),               
                    data: $('#formpacking').serialize(),
                    cache: false,
                    dataType: 'json',
                    success: function(data) {
                        $('input[name=id], #barcode').val(data.id);
                        var id = data.id;
                        pesan('ok',tip);
                        if (tip === '') {
                            $.ajax({
                                type : 'GET',
                                url: '<?= base_url('referensi/manage_packing') ?>/get_data/1',
                                data :'id='+id,
                                cache: false,
                                success: function(data) {
                                    $('#packing_list').html(data);
                                }
                            });
                        } else {
                            $('input[type=text], input[type=hidden], select').val('');
                            $.ajax({
                                type : 'GET',
                                url: '<?= base_url('referensi/manage_packing') ?>/list/'+$('.noblock').html()+'/Farmasi/',
                                cache: false,
                                success: function(data) {
                                    $('#packing_list').html(data);
                                }
                            });
                        }
                        request = null;                            
                    },
                    error : function(){
                        pesan('fail',tip);
                    }
                });
            }
        }

        function pesan(status,tipe){
            if (status === 'ok') {
                if(tipe === ''){
                    alert_tambah();                                    
                }else{
                    alert_edit();
                }
            }else{
                if(tipe === ''){
                    alert_tambah_failed();                                    
                }else{
                    alert_edit_failed();
                }
            }
            
        }
    
    
        function get_packing_list(p, search){
            $.ajax({
                type : 'GET',
                url: '<?= base_url('referensi/manage_packing') ?>/list/'+p+'/Farmasi/',
                data: $('#formpacking').serialize(),
                cache: false,
                success: function(data) {
                    $('#packing_list').html(data);
                }
            });
        }
    
        function delete_packing(id, search){    
            
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
                                url: '<?= base_url('referensi/manage_packing') ?>/delete/'+$('.noblock').html()+'/Farmasi/',
                                data :'id='+id+'&search='+search,
                                cache: false,
                                success: function(data) {
                                    get_packing_list($('.noblock').html(),'');
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
    
        function edit_packing(arr){
            var data = arr.split("#");
        
            $('input[name=id]').val(data[0]);
            $('#barcode').val(data[1]);
            $('input[name=barcode]').val(data[1]);
            $('input[name=id_barang]').val(data[2]);
            $('#barang').val(data[3]);
            $('#kemasan').val(data[4]);
            $('#isi').val(data[5]);
            $('#satuan').val(data[6]);

        
        }
    
        function cetak_barcode(barcode){
            $('#cetak-barcode').fadeIn('fast');
            $('#real-text').html(barcode);
            $('#text-barcode').barcode(barcode, "code128",{barWidth:2, barHeight:40});
        }
       
    </script>
    <div class="data-input">
        <table width="100%" class="inputan">Parameter</legend>
        <tr><td>Barcode:</td><td><?= form_input('barcode', get_last_id('barang_packing','id') , 'id=barcode style="width: 258px;"') ?>
        <?= form_open('', 'id=formpacking') ?>        
        <?= form_hidden('id') ?>
        <tr><td>Barang:</td><td><?= form_hidden('barcode', '') ?>
                    <?= form_input('barang', '', 'class=barang id=barang style="width: 258px;"') ?>
                    <?= form_hidden('id_barang') ?>
        <tr><td>Satuan Terbesar:</td><td><?= form_dropdown('kemasan', $kemasan, null, 'id=kemasan') ?>
        <tr><td>Isi @:</td><td><?= form_input('isi', '', 'id=isi style="width: 258px;" onkeyup=Angka(this)') ?>
        <tr><td>Satuan:</td><td><?= form_dropdown('satuan', $satuan, null, 'id=satuan') ?>
        <tr><td></td><td><?= form_submit('', 'Simpan', 'id=simpan') ?>
            <?= form_button('','Cari','id=cari_packing onclick=get_packing_list(1)') ?>
            <?= form_button(null, 'Reset', 'id=reset_packing') ?>
        <?= form_close() ?>
        </table>
    </div>
    


    <div id="cetak-barcode" style="z-index: 2;display: none;position: absolute;background: #fff;" class="popup">
        <span style="font-size: 40px;font-family: 'barcode','free 3 of 9'; display: block" id="text-barcode"></span>
        <span style="letter-spacing:8px; font: 15px arial,tahoma; line-height: 18px" id="real-text"></span><br/>
        Jumlah cetak: <?= form_input('jml', null, 'id=jml size=5') ?> 
        <?= form_button('', 'Cetak', 'id=cetak-jumlah') ?>
        <?= form_button('', 'Reset', 'id=cetak_batal') ?>
    </div>

    <div id="konfirmasi" style="display: none; padding: 20px;padding-top: 30px">
        <div id="text_konfirmasi"></div>
    </div>
    <div id="list" class="data-list">
        <div id="packing_list"></div>
    </div>
