<script>  
    var bulan_from;
    var tahun_from;
    
    var bulan_to;
    var tahun_to;
  
    
    function search_bulan(){
        bulan_from = $("#bulan_from").val();
        tahun_from = $("#tahun_from").val();
            
            
        bulan_to = $("#bulan_to").val();
        tahun_to = $("#tahun_to").val();
        
        $.ajax({
            url: '<?= base_url() ?>index.php/laporan/kunjungan_rs_bulanan_pasien/'+bulan_from+"/"+tahun_from+"/"+bulan_to+"/"+tahun_to,
            dataType: '',
            success: function( response ) {
                $("#tab1").html(response);
            }
        });

        $.ajax({
            url: '<?= base_url() ?>index.php/laporan/kunjungan_rs_bulanan_unit/'+bulan_from+"/"+tahun_from+"/"+bulan_to+"/"+tahun_to,
            dataType: '',
            success: function( response ) {
                $("#tab2").html(response);
            }
        });
      
    }
    
    $(document).ready(function() {
        $( '#tabs' ).tabs({
            fx: { height: 'toggle', opacity: 'toggle' }
        });
        
        bulan_from = $("#bulan_from").val();
        tahun_from = $("#tahun_from").val();
            
            
        bulan_to = $("#bulan_to").val();
        tahun_to = $("#tahun_to").val();
            
       
       
        $.ajax({
            url: '<?= base_url() ?>index.php/laporan/kunjungan_rs_bulanan_pasien/'+bulan_from+"/"+tahun_from+"/"+bulan_to+"/"+tahun_to,
            dataType: '',
            success: function( response ) {
                $("#tab1").html(response);
            }
        });

        $.ajax({
            url: '<?= base_url() ?>index.php/laporan/kunjungan_rs_bulanan_unit/'+bulan_from+"/"+tahun_from+"/"+bulan_to+"/"+tahun_to,
            dataType: '',
            success: function( response ) {
                $("#tab2").html(response);
            }
        });
        
 
        
        $("a").click(function(){
            $("#pesan").html("");
        });

        $("button").button();

    });
</script>





<div id="tabs">
    <ul>
        <li><a href="#tab1"><span>Laporan Kunjungan Pasien</span></a></li>
        <li><a href="#tab2"><span>Laporan Kunjungan Pasein per Unit</span></a></li>


    </ul> 
    <div id="tab1" ></div>
    <div id="tab2" ></div>    

</div>
<br/>
<div id="result">

</div>
<?php die ?>