<?php $this->load->view('message') ?>
<title><?= $title ?></title>
<div class="kegiatan">
    <script type="text/javascript">
        $(function(){
            $('input[type=submit]').each(function(){ $(this).replaceWith('<button type="' + $(this).attr('type') + '" name="'+$(this).attr('name')+'" id="'+$(this).attr('id')+'">' + $(this).val() + '</button>');});
            $('button[type=submit]').button({icons: {secondary: 'ui-icon-pencil'}});
            $('#pwd_baru').attr('disabled', 'disabled');
            $('#pwd_konf').attr('disabled', 'disabled');    
            $('#change').attr('disabled', 'disabled');   
            $('#pwd_baru').blur(function(){
                $('.msg_ok').fadeIn('fast').html('Masukkan lagi password baru');
                $('#pwd_konf').focus();
            });
            
            $('#formpwd').submit(function(){
                var baru = $('#pwd_baru').val();
                $.ajax({
                    type : 'POST',
                    url: '<?= base_url('referensi/simpan_password') ?>',
                    data : 'password='+baru,
                    cache: false,
                    success: function(data) {
                        $('input').val('');
                        alert_edit();
                        $('#pwd_baru').attr('disabled', 'disabled');
                        $('#pwd_konf').attr('disabled', 'disabled');
                    }
                });
                return false;
            });
        });
                
        function cek_pwd(val){
            $.ajax({
                type : 'POST',
                url: '<?= base_url('referensi/cek_password') ?>',
                data : 'password='+val,
                dataType :'json',
                cache: false,
                success: function(data) {
                    if (data.status){
                        $('#pwd_baru').removeAttr('disabled');
                        $('#pwd_konf').removeAttr('disabled');
                        $('#msg_pwd').fadeOut('fast');
                        $('.msg_ok').fadeIn('fast').html('Masukkan password baru');
                    }else{
                        $('#msg_pwd').fadeIn('fast').html('Pasword yang anda masukkan tidak cocok !');
                        $('#pwd_baru,#pwd_konf ').attr('disabled', 'disabled');
                        $('#pwd_baru,#pwd_konf ').val('');
                        $('#change').attr('disabled', 'disabled');
                    }
                }
            });
        }
    
        
        function retype(konf){
            var pwd_baru = $('#pwd_baru').val();
            if (konf != pwd_baru){
                $('#msg_pwd').fadeIn('fast').html('Konfirmasi pasword baru tidak sama!');
                $('.msg_ok').fadeOut('fast');
                $('#change').attr('disabled', 'disabled');
            }else{
                $('#msg_pwd').fadeOut('fast');
                $('.msg_ok').fadeOut('fast');
                $('#change').removeAttr('disabled');
            }
        }
    </script>
    <div class="titling"><h1><?= $title ?></h1></div>
    <?= form_open('', 'id=formpwd') ?>
    
    
    <div class="data-input">
        <fieldset>
            <div class="msg_ok"></div>
            <div class='msg' id="msg_pwd"></div>
            <tr><td>Username</td><td><span class="label"><?= $user ?></span>
<!--            <tr><td></td><td><span class="label"></span>-->
            <tr><td>Password Lama</td><td><span class="label"><?= form_password('pwd_lama', '', 'id=pwd_lama onblur=cek_pwd(this.value) size=30') ?></span>
            <tr><td>Password Baru</td><td><span class="label"><?= form_password('pwd_baru', '', 'id=pwd_baru  size=30') ?></span>
            <tr><td>Password Baru (Ulangi)</td><td><span class="label"><?= form_password('pwd_konf', '', 'id=pwd_konf onblur=retype(this.value) size=30') ?></span>
            <tr><td></td><td><span class="label"><?= form_submit('', 'Simpan', 'id=change') ?></span>
            
        </table>
    </div>
    <?= form_close() ?>


</div>