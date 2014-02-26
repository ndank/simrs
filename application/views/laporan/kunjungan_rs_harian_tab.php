<script>  
    var tgl_from;
    var tgl_to;
    $(function(){

        $("#todate").attr('disabled', 'disabled');
        $("#fromdate").datepicker({
            changeYear : true,
            changeMonth : true 
        });
        $("#fromdate").change(function(){
            if($('#fromdate').val() == ""){
                $("#todate").attr('disabled', 'disabled');
                $("#todate").val(null);
            }else{
                
                $("#todate").removeAttr('disabled');
                $("#todate").datepicker({
                    changeYear : true,
                    changeMonth : true,
                    minDate : $('#fromdate').val()
                })
            }
        });
        
        $("#todate").change(function(){
            $("#cari_range").removeAttr('disabled');  
        });

    });
    
    function search(){
        var from = $("#fromdate").val().split("/");
        tgl_from = from[2]+"-"+from[1]+"-"+from[0];

        var to = $("#todate").val().split("/");
        tgl_to = to[2]+"-"+to[1]+"-"+to[0];
         $.ajax({
            url: '<?= base_url() ?>index.php/laporan/kunjungan_rs_harian_pasien/'+tgl_from+"/"+tgl_to,
            dataType: '',
            success: function( response ) {
                $("#tab1").html(response);
            }
        });

        $.ajax({
            url: '<?= base_url() ?>index.php/laporan/kunjungan_rs_harian_unit/'+tgl_from+"/"+tgl_to,
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
        var from = $("#fromdate").val().split("/");
        tgl_from = from[2]+"-"+from[1]+"-"+from[0];

        var to = $("#todate").val().split("/");
        tgl_to = to[2]+"-"+to[1]+"-"+to[0];
       
        $.ajax({
            url: '<?= base_url() ?>index.php/laporan/kunjungan_rs_harian_pasien/'+tgl_from+"/"+tgl_to,
            dataType: '',
            success: function( response ) {
                $("#tab1").html(response);
            }
        });

        $.ajax({
            url: '<?= base_url() ?>index.php/laporan/kunjungan_rs_harian_unit/'+tgl_from+"/"+tgl_to,
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