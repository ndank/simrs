<?php $this->load->view('message') ?>
<title><?= $title ?></title>
<div class="kegiatan">
    <script type="text/javascript">
        var data = '';
        $(function() {
 
            $('#tabs').tabs();
            my_ajax('<?= base_url() ?>referensi/barang_obat','#obat');
        
            $('.nonobat').click(function(){
                if($('#nonobat').html()=== ''){
                    my_ajax('<?= base_url() ?>referensi/barang_non_obat','#nonobat');
                }
                $('#nama_brg').focus();
            });
            $('.obat').click(function(){
                if($('#obat').html()=== ''){
                    my_ajax('<?= base_url() ?>referensi/barang_obat','#obat');
                }
                $('#namaobat').focus();
            });
            $('.kemasan_barang').click(function(){
                if($('#kemasan_barang').html()=== ''){
                    my_ajax('<?= base_url() ?>referensi/packing_barang','#kemasan_barang');
                }
                $('#namaobat').focus();
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
        function paging(page,mytab,search){
            var active = $("#tabs").tabs('option', 'active');
            if(active === 0){            
                get_obat_list(page,search);
            }else if(active === 1){
                get_nonobat_list(page,search);
            }else if(active === 2){
                get_packing_list(page,search);
            }
       
        }
    </script>
    <div class="titling"><h1><?= $title ?></h1></div>
    <div class="data-input">
        <div id="tabs">
            <ul>
                <li><a class="obat" href="#obat">Obat</a></li>
                <li><a class="nonobat" href="#nonobat">Non Obat</a></li>
                <li><a class="kemasan_barang" href="#kemasan_barang">Kemasan</a></li>
            </ul>
            <div id="obat"></div>
            <div id="nonobat"></div>
            <div id="kemasan_barang"></div>
        </div>
    </div>
</div>