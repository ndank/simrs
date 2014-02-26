<?php $this->load->view('message') ?>
<title><?= $title ?></title>
<div class="titling"><h1><?= $title ?></h1></div>
<div class="kegiatan">
    <script>
        $(function(){
            $( '#tabs' ).tabs();
            $('#reset').button({icons: {secondary: 'ui-icon-refresh'}}).click(function() {
                $('#loaddata').empty().load('<?= base_url("pendaftaran/list_pendaftar") ?>');
            });
            $('#cari_range').button({icons: {secondary: 'ui-icon-circle-check'}});
            
            $('#cari_range').focus();
            
            
            $("#fromdate").datepicker({
                changeYear : true,
                changeMonth : true 
            });
            $("#fromdate").change(function(){ 
                if($('#fromdate').val() == ""){
                    $("#todate").attr('readonly', 'readonly');
                    $("#todate").val(null);
                }else{
                    $("#cari_range").removeAttr('disabled');
                    $("#todate").removeAttr('readonly');
                    $("#todate").datepicker({
                        changeYear : true,
                        changeMonth : true,
                        minDate : $('#fromdate').val()
                    })
                }
            });
        

        });

        function detail(no){
            $.ajax({
                url: '<?= base_url("pendaftaran/detail/") ?>/'+no,
                cache: false,
                success: function(msg) {
                    $('#loaddata').html(msg);
                       
                }
            })
        }
    
        function get_pendaftar_list(p){
            $.ajax({
                type : 'GET',
                url: '<?= base_url("pendaftaran/list_data_pendaftar") ?>/'+p,
                data : $('#form').serialize(),
                cache: false,
                success: function(data) {
                    $('#daftar_list').html(data);
             
                }
            });
        }    
        function paging(page, tab,search){
            get_pendaftar_list(page);
        }

        function delete_pendaftaran(id){
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
                                url: '<?= base_url('pendaftaran/delete_kunjungan') ?>/'+id,
                                data :'id='+id,
                                cache: false,
                                dataType:'json',
                                success: function(data) {
                                    if(data.status == true){
                                        paging(page, '', null);
                                        alert_delete();
                                    }else{
                                        alert_delete_failed();
                                    }
                                    
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
        <li><a href="#tabs-1">Rekap Pasien</a></li>
    </ul>
    <div id="tabs-1">
        <?= form_open('', 'id=form') ?>
        <table class="inputan" width="100%">
            <tr><td style="width: 150px;">Range Tanggal:</td><td><?= form_input('fromdate', date("d/m/Y"), 'id = fromdate style="width: 70px;" size=10') ?> <span class="label"> s.d </span> <?= form_input('todate', date("d/m/Y"), 'id = todate style="width: 70px;" size=10') ?></td></tr>
            <tr><td>Nama:</td><td><?= form_input('nama',null,'id=nama class=input-text')?></td></tr>
            <tr><td>Jenis Layanan:</td><td><?= form_dropdown('id_layanan', $layanan, null, 'id=layanan') ?></td></tr>
            <tr><td>Alamat:</td><td><?= form_textarea('alamat','','id=alamat class=standar')?></td></tr>
            <tr><td></td><td><?= form_button('cari', 'Cari', 'id=cari_range onClick=get_pendaftar_list(1) ') ?> <?= form_button(null, 'Reset', 'id=reset') ?></td></tr>
        </table>
        <?= form_close() ?>
    <div id="daftar_list"></div>
    </div>
    
</div>
    <?php die ?>
</div>