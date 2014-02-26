<script type="text/javascript">
    function cetak() {
        setTimeout(function(){ window.close();},300);
        window.print();    
    }
</script>
<title><?= $title ?></title>
<style>
    *{ font-size: 14px; font-family: "Times New Roman"}
    .list-data { border-spacing: 0; }
    .list-data th,.list-data td { border-right: 1px solid #000; height: 20px; }
    .list-data th:last-child, .list-data td:last-child { border: none; }
</style>
<body onload="cetak()" style="height:auto; width:21.5cm">
    <table width="100%" style="color: #000; border-bottom: 1px solid #000;">
        <tr>
            <td rowspan="3" style="width: 70px"><img src="<?= base_url('assets/images/company/' . $apt->logo_file_nama) ?>" width="70px" height="70px" /></td>    
            <td colspan="3" align="center"><b><?= strtoupper($apt->nama) ?></b></td> <td rowspan="3" style="width: 70px">&nbsp;</td>
        </tr>
        <tr><td colspan="3" align="center"><b><?= strtoupper($apt->alamat) ?> <?= strtoupper($apt->kelurahan) ?></b></td> </tr>
        <tr><td colspan="3" align="center"><b>TELP. <?= $apt->telp ?>,  FAX. <?= $apt->fax ?>, EMAIL <?= $apt->email ?></b></td> </tr>
    </table>
    <table width="100%" cellspacing="0" style="margin-top:30px">
        <tr>
            <td width="50%" align="center"><span style="font-size: 12px;font-weight:bold;">SURAT PEMESANAN PEMERIKSAAN FISIOTERAPI KLINIK</span></td>
        </tr>
    </table><br/><br/>
    <table width="100%" style="padding: 10px">
        <?php $nama_dokter = ''; ?>
        <?php foreach ($tindakan as $key => $value):?>
            <?php 
                if($key == 0){
                    $nama_dokter = $value->nama_ope;
                }
            ?>
        <?php endforeach; ?>
        <tr><td width="20%">TANGGAL ORDER:</td><td><?= date('d/m/Y') ?></td></tr>
        <tr><td>NAMA DOKTER PEMESAN:</td><td><?= $nama_dokter ?></td></tr>
        
        <tr><td>DIAGNOSA SEMENTARA:</td><td></td></tr>
    	<tr>
    		<td></td>
    		<td>
        		<ol style="padding-left:12px;">
			    	<?php foreach ($diagnosis as $key => $value):?>
			    	<li><?= $value->golongan_sebab ?></li>
			    	<?php endforeach; ?>
			    </ol>
        	</td>
    	</tr>
       
        <tr><td>NO. RM:</td><td><?= $pasien->no_rm ?></td></tr>
        <tr><td>NAMA PASIEN:</td><td><?= $pasien->pasien ?></td></tr>
        <tr><td>ALAMAT JALAN:</td><td><?= $pasien->alamat ?></td></tr>
        <tr><td>WILAYAH:</td><td><?= $pasien->kelurahan.', '.$pasien->kecamatan.', '.$pasien->kabupaten.', '.$pasien->provinsi ?></td></tr>
        <tr><td>UNIT/BANGSAL:</td><td><?= $pasien->nama_unit ?></td></tr>
        <tr><td>KELAS:</td><td><?= $pasien->kelas ?></td></tr>
        <tr><td>NO. TT:</td><td><?= $pasien->no_tt ?></td></tr>
    </table>
    <p style="padding: 10px; padding-bottom:0px;">ITEM PEMERIKSAAN ADALAH SBB:</p>
    <ol>
    	<?php foreach ($tindakan as $key => $value):?>
    		<li><?= $value->tindakan ?></li>
    	<?php endforeach; ?>
    </ol><br/><br/><br/>
    <p style="padding: 10px; padding-bottom:0px;">PARAF:</p>
   <table style="margin-top: 6em;padding:10px;">
        <tr>
	        <td width="50%" align="center" style="border-bottom: 1px solid #000;"><?= $nama_dokter ?></td>
		</tr>
         <tr>
            <td width="50%" align="left">Dokter Operator</td>
        </tr>
     </table>

</body>