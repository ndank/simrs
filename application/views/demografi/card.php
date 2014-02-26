<link rel="stylesheet" href="<?= base_url() ?>assets/css/base.css" />
<script src="<?= base_url() ?>assets/js/jquery-1.8.3.js"></script>
<script src="<?= base_url() ?>assets/js/jquery-barcode-2.0.2.min.js"></script>
<script type="text/javascript">
    $(function(){
        $("#barcode").barcode("<?= $pasien->no_rm ?>", "code128",{barWidth:2, barHeight:40});
    });
    
    function cetak() {
        window.print();    
        setTimeout(function(){ window.close();},300);
    }
</script>
<style type="text/css" media="print">
    @page 
    {
        size: auto;   /* auto is the initial value */
        margin: 0mm;  /* this affects the margin in the printer settings */
    }
</style>
<title><?= $title.$pasien->no_rm ?></title>
<body onload="cetak();" style="height: 4cm">
    <div class="print-card">

<!--        <div style="width: 100px">
            <center><?= $apt->nama ?>
                <img src="<?= base_url() ?>assets/images/company/<?= $apt->logo_file_nama ?>" width="80px" /></center>

        </div>-->
        <table width="100%">
            <tr valign="top"><td width="15%"></td><td width="85%">&nbsp;</td></tr>
        </table>
        <div style="width: 4.5cm; float: right; margin-right: 10px; position: absolute; bottom: 0.2cm; right: 0">
            <center>
                <div id="barcode" style="margin-right: 100px;"></div>
                <?= $pasien->nama ?><br/>
            </center>
        </div>
    </div>
</body>