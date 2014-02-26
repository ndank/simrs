<?php $this->load->view('message') ?>
<title><?= $title ?></title>
<div class="titling"><h1><?= $title ?></h1></div>
<div class="kegiatan">
    <script type="text/javascript">
        var request;
        var dWidth = $(window).width();
        var dHeight= $(window).height();
        var x = screen.width/2 - dWidth/2;
        var y = screen.height/2 - dHeight/2;
        $(function() {
            $('#tabs').tabs();
            $('#bangsal').focus();
            $('#simpan').button({icons: {secondary: 'ui-icon-circle-check'}});
            $('#reset').button({icons: {secondary: 'ui-icon-refresh'}});
            $('#cari').button({icons: {secondary: 'ui-icon-search'}});
            $('.print').button({icons: {secondary: 'ui-icon-print'}});
            $('button[id=reset_bed]').button({icons: {secondary: 'ui-icon-refresh'}});
            
            $('.enter').live("keydown", function(e) {
                var n = $(".enter").length;
                if (e.keyCode === 13) {
                    var nextIndex = $('.enter').index(this) + 1;
                    console.log(nextIndex+" "+n);
                    if (nextIndex < n) {                        
                        $('.enter')[nextIndex].focus();
                    } else {
                       $('#simpan').focus();
                    }
                }
            });
            list_all_bed(1);
            
            $('#reset').click(function(){
                $('#loaddata').empty();
                $('#loaddata').load('<?= base_url('referensi/tempat_tidur') ?>');
            });

            $('#formbed').submit(function(){
                return false;
            });

            $('#simpan').click(function(){ 
                var bangsal = $('#bangsal').val();
                var kelas = $('#kelas').val();
                var jumlah = $('#nomor').val();
                if(bangsal===''){
                    custom_message('Peringatan','Pilih unit dulu !','#bangsal');
                } else if(kelas===''){
                    custom_message('Peringatan','Pilih kelas dulu !','#kelas');
                }else if(jumlah===''){
                    custom_message('Peringatan','Nomor tidak boleh kosong !','#nomor');
                }else{
                    $("<div title='Konfirmasi Simpan'>Anda yakin akan menyimpan data transaksi ini ?</div>").dialog({
                    modal: true,
                    autoOpen: true,
                    width: 320,
                    buttons: { 
                            "Ya": function() { 
                                save();
                                $(this).dialog('close');
                            },
                            "Tidak": function() {
                                $(this).dialog('close');
                                return false;
                            }
                        }, close: function() {
                            $(this).dialog('close');
                            return false;
                        }
                  });
                }
            });          
            
            $('#kelas').change(function(){
                var bangsal = $('#bangsal').val();
                var kelas = $('#kelas').val();
             
            });
            $('#bangsal').change(function(){
                var bangsal = $('#bangsal').val();
                var kelas = $('#kelas').val();               
             
            });

            $('#reset_bed').click(function(){
                reset_all();
            });        
        
        });

        function save(){
            var Url = '';           
            var tipe = $('input[name=hd_id]').val();
       
            if(tipe == ''){
                Url = '<?= base_url("referensi/tempat_tidur_manage") ?>/?act=add_bed&page='+$('.noblock').html();
            }else{
                Url = '<?= base_url("referensi/tempat_tidur_manage") ?>/?act=edit_bed&page='+$('.noblock').html();
            }                   
            
            if(!request) {
                request = $.ajax({
                    type : 'POST',
                    url: Url,               
                    data: $('#formbed').serialize(),
                    dataType : 'json',
                    cache: false,
                    success: function(data) {
                        if(data.id !== 0){
                            $('input[name=hd_id]').val(data.id);  
                        }
                        var id = data.id;

                        if(data.insert){
                            pesan('ok',tipe);
                            $.ajax({
                                url: '<?= base_url('referensi/tempat_tidur_manage/') ?>/',
                                data: 'act=get_bed&id='+id,
                                cache: false,
                                success: function(msg) {
                                    $('#result').html(msg);
                                }
                            });
                        }else{
                            custom_message("Peringatan", "Tempat tidur sudah terdaftar !", '#bangsal');
                        }                               
                       
                        request = null;
                    },
                    error : function(){
                        pesan('fail',tipe);
                    }
                });
            }
        
            return false;
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
    
        function reset_all(){
            $('#bangsal, #kelas, input[name=no_tt], #nomor').val('');
            $('#no_tt').html('');
        }
    

        function list_all_bed(page){
            $.ajax({
                url: '<?= base_url('referensi/tempat_tidur_manage/') ?>/',
                data: 'act=list_bed&page='+page+'&'+$('#formbed').serialize(),
                cache: false,
                success: function(msg) {
                    $('#result').html(msg);
                }
            });
        }
    
       
   
        function edit_bed(arr){
            var data = arr.split("#");
            $('input[name=hd_id]').val(data[0]);
            $('#bangsal').val(data[1]);
            $('#kelas').val(data[2]);
            $('#nomor').val(data[3]);
        }
    
        function delete_bed(id){
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
                                url: '<?= base_url('referensi/tempat_tidur_manage/') ?>',
                                data: 'act=delete_bed&id='+id+'&page='+$('.noblock').html(),
                                cache: false,
                                success: function(data) {
                                    $('#result').html(data);
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
        
        function paging(page, tab,search){
            list_all_bed(page);
        }

        function cetak_rl(){
            window.open('<?= base_url("rekap_laporan/cetak_rl1_3") ?>/','Myprint','width='+dWidth+', height='+dHeight+', left='+x+',top='+y);
        }
    
    </script>
    <div id="tabs">
        <ul>
            <li><a href="#tabs-1">Data Bed</a></li>
        </ul>
        <div id="tabs-1">
            <?= form_open('', 'id=formbed') ?>
            <table class="inputan" width="100%">
                    <?= form_hidden('hd_id') ?>
                <tr><td>Bangsal</td><td><?= form_dropdown('bangsal', $bangsal, '', 'id=bangsal class="enter"') ?></td></tr>
                    <tr><td>Kelas</td><td><?= form_dropdown('kelas', $kelas, '', 'id=kelas class="enter"') ?></td></tr>
                    <tr><td>Nomor Bed</td><td><?= form_input('nomor', '', 'id=nomor size=15  class="enter"') ?></td></tr>
                    <tr><td></td><td><?= form_button('simpan', 'Simpan', 'id=simpan') ?>
                    <?= form_button(null, 'Cari', 'id=cari onclick=list_all_bed(1)') ?>
                    <!--<?= form_button(null, 'Cetak RL 1.3 ', 'class=print onclick=cetak_rl()') ?>-->
                    <?= form_button(null, 'Reset', 'id=reset') ?></td></tr>
            </table>
            <?= form_close() ?>
                <div class="delete_form" style="display: none" title="Hapus Data">
                <p>
                  Data Telah Berhasil di Tambahkan
                </p>
                </div>
            <div id="result"></div>
        </div>
    </div>
</div>
