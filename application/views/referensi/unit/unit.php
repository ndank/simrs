<?php $this->load->view('message') ?>
<script type="text/javascript">
var request;
$(function() {
        // initial
        $('#tabs').tabs();
        $('#unit').focus();
        get_unit_list(1);
        $('input[type=submit]').each(function(){
            $(this).replaceWith('<button type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'">' + $(this).val() + '</button>');
        });
        $('button[type=submit]').button({
            icons: {
                secondary: 'ui-icon-circle-check'
            }
        });
        $('#formsave').submit(function(){
            form_add_submit();
            return false;
        });
             

        $('#reset').button({icons: {secondary: 'ui-icon-refresh'}}).click(function(){
            $('#loaddata').empty();
            $('#loaddata').load($.cookie('url'));
        });


        $('#unit').blur(function() {
            var unit = $('#unit').val();
            $.ajax({
                url: '<?= base_url('referensi/master_unit_search') ?>',
                data:'unit='+unit,
                cache: false,
                dataType: 'json',
                success: function(msg){
                    if (msg.status == false){
                        custom_message('Peringatan', 'Nama unit sudah terdaftar !', '#unit');
                        $('#simpan').attr('disabled', 'disabled');
                        return false;
                    } else {
                        $('#simpan').removeAttr('disabled');
                        return false;
                    }
                }
            });
        })
    });

function form_add_submit(){
    var tipe = $('input[name=id]').val();
    var url = '';

    if (tipe == '') {
        url = '<?= base_url('referensi/master_unit_save') ?>';
    }else{
        url = '<?= base_url('referensi/master_unit_edit') ?>'
    }

    if($('#unit').val() != ''){
        if(!request) {
            request = $.ajax({
                type : 'POST',
                url: url,               
                data: $('#formsave').serialize(),
                cache: false,
                dataType : 'json',
                success: function(data) {
                    $('input[name=id]').val(data.id);
                    var id = data.id;
                    pesan('ok',tipe);
                    $.ajax({
                        type : 'GET',
                        url: '<?= base_url('referensi/master_unit_get_data') ?>',
                        data :'id='+id,
                        cache: false,
                        success: function(data) {
                            $('#unit_list').html(data);
                            
                        }
                    });
                    request = null;                            
                },
                error : function(){
                    pesan('fail',tipe);
                }
            });
        }
    }else{
        custom_message('Peringatan', 'Nama unit tidak boleh kosong !', '#unit')
    }

}

function pesan(status,tipe){
    if (status == 'ok') {
        if(tipe == ''){
            alert_tambah();                                    
        }else{
            alert_edit();
        }
    }else{
        if(tipe == ''){
            alert_tambah_failed();                                    
        }else{
            alert_edit_failed();
        }
    }
    
}
        


function get_unit_list(p){
    $.ajax({
        type : 'GET',
        url: '<?= base_url('referensi/master_unit_list') ?>/'+p, 
        cache: false,
        success: function(data) {
            $('#unit_list').html(data);
        }
    });
}

function delete_unit(id){
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
                        url: '<?= base_url('referensi/master_unit_delete') ?>/'+$('.noblock').html(),
                        data :'id='+id,
                        cache: false,
                        success: function(data) {
                            $('#unit_list').html(data);
                            alert_delete();
                        }
                    });
                    $( this ).dialog( "close" ); 
                } 
            }, 
            { text: "Batal", click: function() { $( this ).dialog( "close" );}} 
        ]
    });         
}

function edit_unit(id, nama){
    $('#unit').val(nama);
    $('input[name=id]').val(id);

}

function paging(page, tab,search){
    get_unit_list(page);
}   

</script>
<title><?= $title ?></title>
<div class="titling"><h1><?= $title ?></h1></div>
<div class="kegiatan">
    <div id="tabs">
        <ul>
            <li><a href="#tabs-1">Unit</a></li>
        </ul>
        <div id="tabs-1">
            <?= form_open('', 'id = formsave') ?>
            <?= form_hidden('id') ?>
            <table class="inputan" width="100%">
                <tr><td>Nama unit:</td><td><?= form_input('unit', null, 'id=unit size=30') ?></td></tr>
                <tr><td></td><td><?= form_submit('', 'Simpan', 'id=simpan') ?><?= form_button(null, 'Reset', 'id=reset') ?></td></tr>
            </table>
            <?= form_close() ?>
            <div class="data-list">
                <div id="unit_list"></div>
            </div>
        </div>
    </div>
</div>
