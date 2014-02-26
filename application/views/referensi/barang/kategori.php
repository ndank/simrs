<?php $this->load->view('message') ?>
<title><?= $title ?></title>
<div class="kegiatan">
<script type="text/javascript">
    $(function() {
        $('#nama').focus();
        get_data(1);
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
        });

        $('#simpan').click(function(){
            $('#formkategori').submit();
        });
       
        $('#formkategori').submit(function() {
            var url = $(this).attr('action');
            var tipe = $('input[name=id_kategori]').val();
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
                data: $('#formkategori').serialize(),
                success: function(data) {
                     var view = data.split('##');
                    $('input[name=id_kategori]').val($.trim(view[0]));
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
            url: '<?= base_url("referensi/manage_kategori") ?>/list/'+page,
            data: $('#formkategori').serialize(),
            success: function(data) {
                $('#list').html(data);
            }
        });
        return false;
    }

    function edit_kategori(id, nama, jenis){
        $('input[name=id_kategori]').val(id);
        $('#id').html(id);
        $('#nama').val(nama);
        if (jenis == 'Farmasi') {
            $('#farmasi').attr('checked','checked');
        }else if(jenis == 'Rumah Tangga'){
            $('#rt').attr('checked','checked');
        }else{
            $('#gizi').attr('checked','checked');
        }
    }

    function paging(page, tab, cari){
        get_data(page);
    }

    function delete_kategori(id){
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
                            url: '<?= base_url('referensi/manage_kategori') ?>/delete/'+page,
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
<div class="titling"><h1><?= $title ?></h1></div>
    <div class="data-input">
        <?= form_open('referensi/manage_kategori', 'id=formkategori') ?>
        <table width="100%" class="inputan">Parameter</legend>
            <?= form_hidden('id_kategori') ?>
            <tr><td>ID.:</td><td><span class="label" id="id"><?= get_last_id('barang_kategori', 'id')?></span>
            <tr><td>Nama:</td><td><?= form_input('nama','','id=nama size=30') ?>
            <tr><td>Jenis:</td><td><span class="label"><?= form_radio('jenis','Farmasi',false,'id=farmasi') ?>Farmasi</span>
             <span class="label"><?= form_radio('jenis','Rumah Tangga',false,'id=rt') ?>Rumah Tangga</span>
             <span class="label"><?= form_radio('jenis','Gizi',false,'id=gizi') ?>Gizi</span>
            <tr><td></td><td><?= form_button('', 'Simpan', 'id=simpan') ?>
            <?= form_button('','Cari','id=cari onclick=get_data(1)') ?>
            <?= form_button(null, 'Reset', 'id=reset') ?>
        </table>
        <?= form_close() ?>
    </div>
    <div id="list"></div>
</div>