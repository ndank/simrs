<?php $this->load->view('message') ?>
<title><?= $title ?></title>
<div class="titling"><h1><?= $title ?></h1></div>
<div class="kegiatan">
    <script type="text/javascript">
        var data = '';
        $(function() {
        
           $('#tabs').tabs();
           if('<?= $tipe ?>' == 'tindakan'){
                $("#tabs").tabs('select', '#tindakan');
                my_ajax('<?= base_url() ?>referensi/tarif_tindakan/<?= isset($id_tarif)?$id_tarif:'' ?>','#tindakan');
           }else if('<?= $tipe ?>' == 'barang'){
                $("#tabs").tabs('select', '#barang');
                my_ajax('<?= base_url() ?>referensi/tarif_barang/<?= isset($id_tarif)?$id_tarif:'' ?>','#barang');
           }else if('<?= $tipe ?>' == 'jenislayanan'){
                $("#tabs").tabs('select', '#jenislayanan');
                my_ajax('<?= base_url() ?>referensi/pelayanan/<?= isset($id_tarif)?$id_tarif:'' ?>','#jenislayanan');
           }else if('<?= $tipe ?>' == 'layanan'){
                $("#tabs").tabs('select', '#layanan');
                my_ajax('<?= base_url() ?>referensi/layanan/<?= isset($id_tarif)?$id_tarif:'' ?>','#layanan');
           }
           else{
                $("#tabs").tabs('select', '#kamar');
                my_ajax('<?= base_url() ?>referensi/tarif_kamar/<?= isset($id_tarif)?$id_tarif:'' ?>','#kamar');
           }

            
            $('.jenislayanan').click(function(){
                if($('#jenislayanan').html()== ''){
                    my_ajax('<?= base_url() ?>pelayanan/jenis_layanan','#barang');
                }
            });
            $('.layanan').click(function(){
                if($('#layanan').html()== ''){
                    my_ajax('<?= base_url() ?>referensi/layanan','#barang');
                }
            });
            $('.barang').click(function(){
                if($('#barang').html()== ''){
                    my_ajax('<?= base_url() ?>referensi/tarif_barang','#barang');
                }
            });
            $('.tindakan').click(function(){
                if($('#tindakan').html()== ''){
                    my_ajax('<?= base_url() ?>referensi/tarif_tindakan','#tindakan');
                }
            });

            $('.kamar').click(function(){
                if($('#kamar').html()== ''){
                    my_ajax('<?= base_url() ?>referensi/tarif_kamar','#kamar');
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
        function paging(page,mytab,search){
            if(mytab == 1){            
                get_tindakan_list(page, search);
            }else if(mytab == 2){
                get_barang_list(page, search);
            }else{
                get_kamar_list(page, search);
            }
       
        }
    </script>
    <div class="data-input">
        <div id="tabs">
            <ul>
                <li><a class="tindakan" href="#tindakan">Tarif Pemeriksaan dan Tindakan</a></li>
                <!--<li><a class="barang" href="#barang">Tarif Sewa Barang</a></li>-->
                <li><a class="kamar" href="#kamar">Tarif Sewa Kamar</a></li>

            </ul>
            <div id="tindakan"></div>
            <!--<div id="barang"></div>-->
            <div id="kamar"></div>

        </div>
    </div>
</div>