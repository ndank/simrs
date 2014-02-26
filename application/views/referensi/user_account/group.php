<?php $this->load->view('message') ?>
<title><?= $title ?></title>
<div class="titling"><h1><?= $title ?></h1></div>
<div class="kegiatan">
    <style type="text/css">
        #tabs{
            z-index: 0;
        }
    </style>
    <script type="text/javascript" src="<?= base_url('assets/js/colResizable-1.3.min.js') ?>"></script>
    <script type="text/javascript">
        var data = '';
        $(function() {
 
            $('#tabs').tabs();
            my_ajax('<?= base_url() ?>referensi/user_group','#group');
        
            $('.group').click(function(){
                if($('#group').html()== ''){
                    my_ajax('<?= base_url() ?>referensi/user_group','#group');
                }
                
            });
            $('.user').click(function(){
                if($('#user').html()== ''){
                    my_ajax('<?= base_url() ?>referensi/user_account','#user');
                }
            });
        });
    
        function my_ajax(url,element){
            $.ajax({
                url: url,
                dataType: '',
                success: function( response ) {
                    $(element).html(response);
                }
            });
        }
        function paging(page,mytab){
            if(mytab == 1){
                get_group_list(page);
            }else if(mytab == 2){
               get_user_list(page);
            }
       
        }
    </script>
    
    <div id="tabs">
        <ul>
            <li><a class="group" href="#group">User Group</a></li>
            <li><a class="user" href="#user">User Account</a></li>
        </ul>

        <div id="group"></div>
        <div id="user"></div>
    </div>


</div>