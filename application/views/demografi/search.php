
<?php $this->load->view('message') ?>
<title><?= $title ?></title>
<div class="titling"><h1><?= $title ?></h1></div>
<div class="kegiatan">
    <script type="text/javascript" src="<?= base_url() ?>/assets/js/jquery.cookies.js"></script>
    <script type="text/javascript">  
        function reset_all(){
            $('#loaddata').load('<?= base_url('demografi/search') ?>');
        } 
    
        $(function() {
            $( '#tabs' ).tabs();
            if('<?= $tab ?>' == '2'){
                my_ajax('<?= base_url() ?>index.php/demografi/advance_search_get','#tab2');
                $("#tabs").tabs('select', '#tab2');                
            }else{                
                my_ajax('<?= base_url() ?>index.php/demografi/advance_search_get','#tab2');
                $("#tabs").tabs('select', '#tab1');
            }
            

            $('#tb1').click(function(){
                if ($('#tab1').html() == '') {
                     my_ajax('<?= base_url() ?>index.php/demografi/search_by_no_rm_get','#tab1');
                }
            });
           
           $('#tb2').click(function(){
                if ($('#tab2').html() == '') {
                    my_ajax('<?= base_url() ?>index.php/demografi/advance_search_get','#tab2');
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

        function paging(page, tab,search){
            if(tab == 1){
                get_list(page);
            }else{
                get_list_no_rm(page);
            }
            
             //
        }
    </script>

    <div id="tab2"></div>
</div>
<?php die; ?>