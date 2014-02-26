<?php $this->load->view('message') ?>
<title><?= $title ?></title>
<div class="kegiatan">
    <script type="text/javascript">
        var request;
        $(function() {        
            $('#simpan_kamar').button({icons: {secondary: 'ui-icon-circle-check'}});
            $('#reset_kamar,.resetan').button({icons: {secondary: 'ui-icon-refresh'}});
            $('#cari_kamar').button({icons:{secondary:'ui-icon-search'}});
            get_kamar_list(1,'');

             $('#konfirmasi_kamar').dialog({
                autoOpen: false,title :'Konfirmasi',height: 200,width: 300,
                modal: true,resizable : false,
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

            $('#reset_kamar').click(function(){
                my_ajax('<?= base_url() ?>referensi/tarif_kamar/<?= isset($id_tarif)?$id_tarif:'' ?>','#kamar');            
            });
            $('#simpan_kamar').click(function() {
                $('#formkamar').submit();
            });
            $('#formkamar').submit(function(){
                var Url = '<?= base_url('referensi/manage_kamar') ?>/cek/1';
                var tipe = $('input[name=id]').val();
                
                if($('#js_kamar').val()===''){
                    custom_message('Peringatan','Jasa Sarana tidak boleh kosong !','#js_kamar');
                    return false;
                }
                
                if($('#bhp_kamar').val()===''){
                    custom_message('Peringatan','B.H.P tidak boleh kosong !','#bhp_kamar');
                    return false;
                }
                if($('#bia_adm_kamar').val()===''){
                    custom_message('Peringatan','Biaya administrasi tidak boleh kosong !','#bia_adm_kamar');
                    return false;
                }

                if($('#margin_kamar').val()===''){
                    custom_message('Peringatan','Margin tidak boleh kosong !','#margin_kamar');
                    return false;
                }
                $.ajax({
                    type : 'GET',
                    url: Url,               
                    data: 'unit='+$('#unit_kamar').val()+'&kelas='+$('#kelas_kamar').val()+'&nama_unit='+$('#unit_kamar option:selected').text(),
                    dataType: 'json',
                    cache: false,
                    success: function(data) {
                        if (tipe === ''){
                            if (data.status === false){
                                $('#text_konfirmasi_kamar').html('Tarif sudah ada<br/> Apakah anda yakin akan menambahkannya lagi?');            
                                $('#konfirmasi_kamar').dialog("open");
                            } else {
                                alert('fuck');
                                save();
                            }                        
                        }else{
                             save();
                        }
                            
                    }
                });        
                
                return false;
            });
            
            
        });
        
        function save(){
            var tipe = $('input[name=id_hide_kamar]').val();
            var Url = '';

            if(tipe == ''){
                Url = '<?= base_url('referensi/manage_kamar') ?>/add/1';
            }else{
                Url = '<?= base_url('referensi/manage_kamar') ?>/edit/1';
            }
            var last = $('#id_kamar').val();
            
             if(!request) {
                    request = $.ajax({
                    type : 'POST',
                    url: Url,               
                    data: $('#formkamar').serialize(),
                    cache: false,
                    success: function(data) {
                        request = null;
                        $('#kamar_list').html(data);                            
                        if(tipe == ''){
                            alert_tambah();
                            $('input[name=id_hide_kamar]').val(parseInt(last));
                        }else{
                            alert_edit();
                        }
                    }
                });
            }
        }
        
        
        
        function get_kamar_list(p){
            $.ajax({
                type : 'GET',
                url: '<?= base_url('referensi/manage_kamar') ?>/list/'+p, 
                data : 'unit='+$('#unit_kamar').val()+'&kelas='+$('#kelas_kamar').val()+'&nama_unit='+$('#unit_kamar option:selected').text(),
                cache: false,
                success: function(data) {
                    $('#kamar_list').html(data);
                }
            });
        }
        
        function delete_kamar(id){
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
                                url: '<?= base_url('referensi/manage_kamar') ?>/delete/'+$('.noblock').html(),
                                data :'id='+id,
                                cache: false,
                                success: function(data) {
                                    get_kamar_list($('.noblock').html());
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
        
        function edit_kamar(id){
             $.ajax({
                type : 'GET',
                url: '<?= base_url('referensi/manage_kamar') ?>/detail/1', 
                cache: false,
                data : 'id='+id,
                dataType : 'json',
                success: function(data) {
                    fill_kamar(data);
                    $('#unit_kamar').focus();
                }
            });
            
        }

        function get_kamar_last_id(){
            $.ajax({
                type : 'GET',
                url: '<?= base_url('referensi/get_last_id') ?>/tarif/id',
                cache: false,
                dataType : 'json',
                success: function(data) {
                    $('#id_kamar').val(data.last_id);
                }
            });
        }

        function fill_kamar(data){
            $('#id_kamar,input[name=id_hide_kamar]').val(data.id);
            $('#unit_kamar').val(data.id_unit);
            $('#kelas_kamar').val(data.kelas);
            $('#js_kamar').val(numberToCurrency(data.jasa_sarana));
            $('#bhp_kamar').val(numberToCurrency(data.bhp));
            $('#bia_adm_kamar').val(numberToCurrency(data.biaya_administrasi));
            $('#total_kamar').html(numberToCurrency(data.total));
            $('input[name=total_kamar]').val(data.total);
            $('#margin_kamar').val(data.persentase_profit);
            $('#nominal_akhir_kamar').val(numberToCurrency(data.nominal));
            
        }

        function subtotal(){
            $('#js_kamar').val(numberToCurrency($('#js_kamar').val()));
           
            $('#bhp_kamar').val(numberToCurrency($('#bhp_kamar').val()));
            $('#bia_adm_kamar').val(numberToCurrency($('#bia_adm_kamar').val()));
            
            var js = currencyToNumber($('#js_kamar').val());
            var bhp= currencyToNumber($('#bhp_kamar').val());
            var bia = currencyToNumber($('#bia_adm_kamar').val());
            if (isNaN(js)) { js = 0;}
            if (isNaN(bhp)) { bhp = 0; }
            if (isNaN(bia)) { bia = 0; }
            var val = js+bhp+bia;
            $('#total_kamar').html(numberToCurrency(val));
            $('input[name=total_kamar]').val(val);
            var margin = $('#margin_kamar').val()/100;
            var nominal= val+parseInt(margin*val);
            $('#nominal_akhir_kamar').val(numberToCurrency(nominal));
            $('input[name=nominal_akhir_kamar]').val(numberToCurrency(Math.ceil(nominal)));
        } 

        function get_margin_kamar(nominal){
            var total = currencyToNumber($('#total_kamar').html());
            var margin = currencyToNumber(nominal) - total;
            var persentase = (margin / total) * 100;
            $('#margin_kamar').val(persentase);
        }
        
      
           
    </script>
    <table width="100%" class="inputan">
        <?= form_open('', 'id = formkamar') ?>
        <tr><td>ID.:</td><td><?= form_hidden('id_hide_kamar',isset($edit)?$edit->id:null) ?>
            <?= form_input('id', isset($edit)?$edit->id:get_last_id('tarif', 'id'), 'id=id_kamar size=40') ?></td></tr>
            <tr><td>Unit:</td><td><?= form_dropdown('unit',$unit, isset($edit)?$edit->id_unit:null, 'id=unit_kamar')?></td></tr>
            <tr><td>Kelas:</td><td><?= form_dropdown('kelas', $kelas, isset($edit)?$edit->kelas:null, 'id=kelas_kamar')?></td></tr>
            <tr><td>Jasa Sarana:</td><td><?= form_input('js',isset($edit)?rupiah($edit->jasa_sarana):'0','id=js_kamar onblur=subtotal()')?></td></tr>
            <tr><td>B.H.P:</td><td><?= form_input('bhp',isset($edit)?rupiah($edit->bhp):'0','id=bhp_kamar onblur=subtotal()')?></td></tr>
            <tr><td>Bia. Adm:</td><td><?= form_input('bia_adm',isset($edit)?rupiah($edit->biaya_administrasi):'0','id=bia_adm_kamar onblur=subtotal()') ?></td></tr>
            <tr><td>Total: </td><td><span class="label" id="total_kamar" class="wrap"><?= isset($edit)?rupiah($edit->total):''?></span></td></tr>
            <?= form_hidden('total_kamar',isset($edit)?rupiah($edit->total):'') ?>
            <tr><td>Margin (%):</td><td><?= form_input('margin',isset($edit)?rupiah($edit->persentase_profit):'0','id=margin_kamar size=5 onblur=subtotal()') ?></td></tr>
            <tr><td>Nominal Akhir:</td><td><?= form_input('nominal_akhir',isset($edit)?rupiah($edit->nominal):'0','onkeyup="FormNum(this)" id=nominal_akhir_kamar onblur="get_margin_kamar(this.value)" ') ?></td></tr>
        <tr><td></td><td>
            <?= form_button('simpan', "Simpan", 'id=simpan_kamar') ?>
            <?= form_button('cari', 'Cari', 'id=cari_kamar onclick=get_kamar_list(1)') ?>
            <?= form_button('reset', 'Reset', 'id=reset_kamar') ?></td></tr>
        <?= form_close() ?>
    </table>
    <div id="kamar_list"></div>

    <div id="konfirmasi_kamar" style="display: none; padding: 20px;">
        <div id="text_konfirmasi_kamar"></div>
    </div>

</div>