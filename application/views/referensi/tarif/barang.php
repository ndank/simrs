<?php $this->load->view('message') ?>
<title><?= $title ?></title>
<div class="kegiatan">
    <script type="text/javascript">
        var request;
        $(function() {        
            $('input[type=submit]').each(function(){ $(this).replaceWith('<button type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'">' + $(this).val() + '</button>');});
            $('button[type=submit]').button({icons: {secondary: 'ui-icon-circle-check'}});
            $('#reset_sewa,.resetan').button({icons: {secondary: 'ui-icon-refresh'}});
            $('#cari').button({icons:{secondary:'ui-icon-search'}});
            get_sewa_list(1,'null');

             $('#konfirmasi_sewa').dialog({
                autoOpen: false,title :'Konfirmasi',height: 200,width: 300,
                modal: true,resizable : false,
                buttons: [ 
                    { text: "Ok", click: function() { 
                            save_sewa();
                            $( this ).dialog( "close" ); 
                        } 
                    }, 
                    { text: "Batal", click: function() { 
                            $( this ).dialog( "close" ); 
                        } 
                    } 
                ]
            });

            $('#reset_sewa').click(function(){
                 my_ajax('<?= base_url() ?>referensi/tarif_barang/<?= isset($id_tarif)?$id_tarif:'' ?>','#barang');
            });
            $('#formsewa').submit(function(){ 
                var Url = '<?= base_url('referensi/manage_sewa') ?>/cek/1';
                var tipe = $('input[name=id_sewa]').val();
                

                if($('input[name=id_barang_sewa]').val()===''){
                    custom_message('Peringatan','Barang tidak boleh kosong !','#barang_sewa');
                    return false;
                }
                
                if($('#nominal_sewa').val()===''){
                    custom_message('Peringatan','Nominal tidak boleh kosong !','#nominal_sewa');
                    return false;
                }
            
               
                $.ajax({
                    type : 'GET',
                    url: Url,               
                    data: $('#formsewa').serialize(),
                    dataType: 'json',
                    cache: false,
                    success: function(data) {
                        if (tipe === ''){
                            if (data.status === false){
                                $('#text_konfirmasi_sewa').html('Tarif sudah ada<br/> Apakah anda yakin akan menambahkannya lagi?');            
                                $('#konfirmasi_sewa').dialog("open");
                            } else {
                                save_sewa();
                            }                        
                        }else{
                             save_sewa();
                        }
                            
                    }
                });        
                
                return false;
            });
            
            $('#jurusan').autocomplete("<?= base_url('kepegawaian/get_jurusan') ?>",
            {
                parse: function(data)
                {
                    var parsed = [];
                    for (var i=0; i < data.length; i++)
                    {
                        parsed[i] =
                            {
                            data: data[i],
                            value: data[i].nama // nama field yang dicari
                        };
                    }
                    $('input[name=id_jurusan]').val('');
                    return parsed;
                },
                formatItem: function(data,i,max)
                {
                    var str = '<div class=result>'+data.nama+'<br/>'+data.jenis +'</div>';
                    return str;
                },
                width: 270, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
            }).result(
            function(event,data,formated){
                $(this).attr('value',data.nama);
                $('input[name=id_jurusan]').val(data.id);
            });
            
            
            $('#barang_sewa').autocomplete("<?= base_url('inv_autocomplete/load_data_packing_barang') ?>",
            {
                parse: function(data){
                    var parsed = [];
                    for (var i=0; i < data.length; i++) {
                        parsed[i] = {
                            data: data[i],
                            value: data[i].nama // nama field yang dicari
                        };
                    }
                    $('input[name=id_barang_sewa]').val('');
                    return parsed;
                },
                formatItem: function(data,i,max){
                    if (data.id_obat !== null) {
                        if (data.kekuatan !== null && data.satuan !== null && data.sediaan !== null) {
                            var str = '<div class=result>'+data.nama+' '+((data.kekuatan === '1')?'':data.kekuatan)+' '+data.satuan+' '+data.sediaan+'  <i> '+ ((data.pabrik!==null)?data.pabrik:'') +'</i></div>';
                        } 
                        else if (data.kekuatan !== null && data.satuan !== null && data.sediaan === null) {
                            var str = '<div class=result>'+data.nama+' '+((data.kekuatan === '1')?'':data.kekuatan)+' '+data.satuan+' '+data.sediaan+' <i> '+ ((data.pabrik!==null)?data.pabrik:'') +'</i></div>';
                        } else {
                            var str = '<div class=result>'+data.nama+'</div>';
                        }   
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
                dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
            }).result(
            function(event,data,formated){
                if (data.id_obat !== null) {
                        if (data.kekuatan !== null && data.satuan !== null && data.sediaan !== null) {
                            var str = data.nama+' '+((data.kekuatan === '1')?'':data.kekuatan)+' '+data.satuan+' '+data.sediaan+' '+ ((data.pabrik!==null)?data.pabrik:'') ;
                        } 
                        else if (data.kekuatan !== null && data.satuan !== null && data.sediaan === null) {
                            var str = data.nama+' '+((data.kekuatan === '1')?'':data.kekuatan)+' '+data.satuan+' '+data.sediaan+' '+ ((data.pabrik!==null)?data.pabrik:'');
                        } else {
                            var str = data.nama;
                        }   
                    } else {
                        if (data.pabrik != null) {
                            var str = data.nama+' '+data.pabrik;
                        } else {
                            var str = data.nama;
                        }
                    }
                $(this).val(str);
                $('input[name=id_barang_sewa]').val(data.id);
                $('#hna').html(data.hna);
                $.ajax({
                    url: '<?= base_url('inv_autocomplete/get_hna_packing') ?>/'+data.id,
                    dataType: 'json',
                    cache: false,
                    success: function(data) {
                        $('#hna').html(data.hna);
                    }
                });
            });
        });
        
        function save_sewa(){
            var tipe = $('input[name=id_sewa_hide]').val();
            var Url = '';

            if(tipe === ''){
                Url = '<?= base_url('referensi/manage_sewa') ?>/add/1';
            }else{
                Url = '<?= base_url('referensi/manage_sewa') ?>/edit/1';
            }
            var last = $('#id').html();
            
             if(!request) {
                    request = $.ajax({
                    type : 'POST',
                    url: Url,               
                    data: $('#formsewa').serialize(),
                    cache: false,
                    success: function(data) {
                        request = null;
                        $('#sewa_list').html(data);                            
                        if(tipe == ''){
                            alert_tambah();
                            $('input[name=id_sewa]').val(parseInt(last));
                        }else{
                            alert_edit();
                        }
                    }
                });
            }
        }
        
        
        function get_sewa_last_id(){
            $.ajax({
                type : 'GET',
                url: '<?= base_url('referensi/get_last_id') ?>/tarif/id',
                cache: false,
                dataType : 'json',
                success: function(data) {
                    $('#id_sewa').val(data.last_id);
                }
            });
        }
        
        function get_sewa_list(p,cari){
            $.ajax({
                type : 'GET',
                url: '<?= base_url('referensi/manage_sewa') ?>/list/'+p, 
                data : 'search='+cari+'&id='+$('input[name=id_sewa_hide]').val(),
                cache: false,
                success: function(data) {
                    $('#sewa_list').html(data);
                }
            });
        }
        
        function delete_sewa(id){
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
                                url: '<?= base_url('referensi/manage_sewa') ?>/delete/'+$('.noblock').html(),
                                data :'id='+id,
                                cache: false,
                                success: function(data) {
                                    $('#sewa_list').html(data);
                                    alert_delete();
                                }
                            });
                            
                            $(this).dialog("close"); 
                        } 
                    }, 
                    { text: "Batal", click: function() { $( this ).dialog( "close" );}} 
                ]
            });
        }
        
        function edit_sewa(id){
             $.ajax({
                type : 'GET',
                url: '<?= base_url('referensi/manage_sewa') ?>/detail/1', 
                cache: false,
                data : 'id='+id,
                dataType : 'json',
                success: function(data) {
                    fill_fieldset_sewa(data);
                    $('#layanan').focus();
                }
            });
            
        }

        function fill_fieldset_sewa(data){
            
            $('input[name=id_sewa]').val(data.id);
            $('#id_sewa,input[name=id_sewa_hide]').val(data.id);
            $('#layanan_sewa').val(data.layanan);
            $('#barang_sewa').val(data.barang);
            $('input[name=id_barang_sewa]').val(data.id_barang);
            $('input[name=id_layanan_sewa]').val(data.id_layanan);
            $('#jenis_layan_sewa').val(data.jenis_pelayanan_kunjungan);
            $('#unit_sewa').val(data.id_unit);
            $('#kelas_sewa').val(data.kelas);            
            $('#nominal_sewa').val(numberToCurrency(data.nominal));
        }

    </script>
    <div class="data-input">
        <fieldset>
            <?= form_open('', 'id = formsewa') ?>
            <?= form_hidden('id_layanan_sewa', $id_layanan) ?>
            <table width="100%" class="inputan">
                <tr><td style="width: 120px;">ID.:</td><td><?= form_hidden('id_sewa_hide',isset($edit)?$edit->id:null) ?>
                <?= form_input('id_sewa', isset($edit)?$edit->id:get_last_id('tarif', 'id'), 'id=id_sewa size=40') ?></td></tr>
                <tr><td>Layanan:</td><td><span class="label">Sewa Barang</span></td></tr>
                <tr><td>Kemasan Barang:</td><td><?= form_input('barang_sewa',isset($edit)?$edit->barang:null,'id=barang_sewa size=40')?>
                <?= form_hidden('id_barang_sewa', isset($edit)?$edit->id_barang_sewa:null ) ?></td></tr>
                <tr><td>HNA Barang:</td><td><span id="hna" class="wrap label"></span></td></tr>
                <tr><td>Jenis Pelayanan:</td><td><?= form_dropdown('jenis_layan',$jenis_layan,isset($edit)?$edit->jenis_pelayanan_kunjungan:null,'id=jenis_layan_sewa') ?></td></tr>
                <tr><td>Unit:</td><td><?= form_dropdown('unit',$unit,isset($edit)?$edit->id_unit:null, 'id=unit_sewa')?></td></tr>
                <tr><td>Kelas:</td><td><?= form_dropdown('kelas', $kelas, isset($edit)?$edit->kelas:null, 'id=kelas_sewa')?></td></tr>
                <tr><td>Nominal Akhir:</td><td><?= form_input('nominal',isset($edit)?$edit->nominal:'0','id=nominal_sewa onkeyup=FormNum(this)')?></td></tr>
                <tr><td></td><td>
                <?= form_submit('simpan', "Simpan", 'id=simpan_sewa') ?>
                <?= form_button('reset', 'Reset', 'id=reset_sewa') ?>
                </td></tr>
            </table>
            <?= form_close() ?>
        </table>
    </div>
    <div id="sewa_list"></div>

    <div id="konfirmasi_sewa" style="display: none; padding: 20px;">
        <div id="text_konfirmasi_sewa"></div>
    </div>

</div>