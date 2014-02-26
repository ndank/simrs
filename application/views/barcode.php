<script src="<?= base_url() ?>assets/js/jquery-1.8.3.js"></script>
<script src="<?= base_url() ?>assets/js/jquery-barcode-2.0.2.min.js"></script>
<script type="text/javascript">
    $(function(){
        $('#text-barcode').barcode('<?= $barcode ?>', "code128",{barWidth:2, barHeight:40});
    });
    function cetak() {  		
        SCETAK.innerHTML = '';
        window.print();
        if (confirm('Apakah menu print ini akan ditutup?')) {
            show_form2();
        }
        SCETAK.innerHTML = '<br /><input onClick=\'cetak()\' type=\'submit\' name=\'Submit\' value=\'Cetak\' class=\'tombol\'>';
    }
</script> 
<?php
$jml = $_GET['jumlah'];
$barcode = $_GET['barcode'];
for ($i = 1; $i <= $jml; $i++) {
    ?>
    <span style="font-size: 40px;font-family: 'barcode','free 3 of 9';" id="text-barcode"><?= $barcode ?></span><br/>
    <span style="letter-spacing:8px; font: 15px arial,tahoma; line-height: 18px" id="real-text"><?= $barcode ?></span><br/><br/>
<?php } ?>
<p><span id='SCETAK'><input type='button' class='tombol' value='Cetak' onClick='cetak()'></span></p>
<?php die;
?>
