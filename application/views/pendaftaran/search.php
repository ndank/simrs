<?php $this->load->view('message') ?>
<title><?= $title ?></title>
<div class="titling"><h1><?= $title ?></h1></div>
<div class="kegiatan">
    <script>       
        function reset_all(tab){
            $('#loaddata').empty();
            $('#loaddata').load($.cookie('url')+'/'+tab);
        }

        $(function() {
            $( '#tabs' ).tabs();
            if('<?= $tab ?>' === '3'){
                my_ajax('<?= base_url() ?>index.php/pendaftaran/search_by_nama_get',"#tab3");
                $("#tabs").tabs('select', '#tab3');
            }else if('<?= $tab ?>' === '2'){
                my_ajax('<?= base_url() ?>index.php/pendaftaran/search_by_no_rm_get','#tab2');
                $("#tabs").tabs('select', '#tab2');                
            }else{                
                my_ajax('<?= base_url() ?>index.php/pendaftaran/search_by_no_antri_get','#tab1');
                $("#tabs").tabs('select', '#tab1');
            }


           $('#tb1').click(function(){
                if ($('#tab1').html() == '') {
                    my_ajax('<?= base_url() ?>index.php/pendaftaran/search_by_no_antri_get','#tab1');
                }
           });
           
           $('#tb2').click(function(){
                if ($('#tab2').html() == '') {
                    my_ajax('<?= base_url() ?>index.php/pendaftaran/search_by_no_rm_get','#tab2');
                }
            });


           $('#tb3').click(function(){
                if ($('#tab3').html() == '') {
                    my_ajax('<?= base_url() ?>index.php/pendaftaran/search_by_nama_get',"#tab3");
                }
            });
                      
                       
            

        

            $("button").button();

            function my_ajax(url,element){
                $.ajax({
                    url: url,
                    dataType: '',
                    success: function( response ) {
                        $(element).html(response);
                    }
                });
            }
        
        });
    </script>
    
    <div id="tabs">
        <ul>
            <li><a id="tb1" href="#tab1"><span>Cari Pasien</span></a></li>            
<!--            <li><a id="tb2" href="#tab2"><span>Cari Berdasarkan No. RM</span></a></li>
            <li><a id="tb3" href="#tab3"><span>Cari Berdasarkan Nama & Alamat</span></a></li>-->

        </ul> 
        <div id="tab1"></div>
<!--        <div id="tab2" ></div>    
        <div id="tab3" ></div>-->
    </div>
    <br/>

</div>
<?php die ?>