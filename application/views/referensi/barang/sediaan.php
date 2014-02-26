<?php $this->load->view('message') ?>
<title><?= $title ?></title>
<div class="titling"><h1><?= $title ?></h1></div>
<div class="kegiatan">
<script type="text/javascript">
    $(function() {
        $('#tabs').tabs();
        get_data(1);
        $('#simpan').each(function(){
            $(this).replaceWith('<button type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'">' + $(this).val() + '</button>');
        });
        $('#simpan').button({icons: {secondary: 'ui-icon-circle-check'}});
        $('#cari').button({icons: {secondary: 'ui-icon-search'}});
        $('#reset').button({icons: {secondary: 'ui-icon-refresh'}});
        $('button[id=cetak]').button({icons: {secondary: 'ui-icon-print'}});
        $('#awal,#akhir').datepicker({
            changeYear: true,
            changeMonth: true
        })
        $('#reset').click(function() {
            $('#loaddata').empty();
            $('#loaddata').load($.cookie('url'));
        })
       
        $('#formsediaan').submit(function() {
            var url = $(this).attr('action');
            var tipe = $('input[name=id_sediaan]').val();
            if(tipe == ''){
                url += '/add/1';
            }else{
                url += '/edit/1';
            }

            if ($('#nama').val() == '') {
                custom_message('Peringatan', 'Nama tidak boleh kosong!', '#nama');
                return false;
            };           

            $.ajax({
                type: 'POST',
                url: url,
                data: $('#formsediaan').serialize(),
                success: function(data) {
                    var view = data.split('##');
                    $('input[name=id_sediaan]').val($.trim(view[0]));
                    $('#list').html(view[1]);
                    if(tipe == ''){
                        alert_tambah();
                    }else{
                        alert_edit();
                    }
                }
            });

            return false;
        });
    });

    function get_data(page){
        $.ajax({
            type: 'GET',
            url: '<?= base_url('referensi/manage_sediaan') ?>/list/'+page,
            data: $('#formsediaan').serialize(),
            success: function(data) {
                $('#list').html(data);
            }
        });
        return false;
    }

    function edit_sediaan(id, nama, jenis){
        $('input[name=id_sediaan]').val(id);
        $('#nama').val(nama);
    }

    function paging(page, tab, cari){
        get_data(page);
    }

    function delete_sediaan(id){
        var page = ($('.noblock').html()=='')?'1':$('.noblock').html();
        
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
                            url: '<?= base_url('referensi/manage_sediaan') ?>/delete/'+page,
                            data :'id='+id,
                            cache: false,
                            success: function(data) {
                                get_data(page);
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

    
</script>
    <div id="tabs">
        <ul>
            <li><a href="#tabs-1">Referensi Sediaan</a></li>
        </ul>
        <div id="tabs-1">
            <?= form_open('referensi/manage_sediaan', 'id=formsediaan') ?>
            <table class="inputan" width="100%">
                <?= form_hidden('id_sediaan') ?>
                <tr><td>ID.:</td><td class="label" id="id"><?= get_last_id('sediaan', 'id')?></td></tr>
                <tr><td>Nama:</td><td><?= form_input('nama','','id=nama size=30') ?></td></tr>

                <tr><td></td><td><?= form_submit('', 'Simpan', 'id=simpan') ?>
                <?= form_button('','Cari','id=cari onclick=get_data(1)') ?>
                <?= form_button(null, 'Reset', 'id=reset') ?></td></tr>
            </table>
            <?= form_close() ?>
        
        <div id="list"></div>
        </div>
</div>