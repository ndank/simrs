<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
    <head>
        <title><?= $title ?></title>
      
        <style>
            .tabel-laporan{
                font-weight: bold;
            }
            .tabel-laporan th{           
                text-align: center;
            }
            .tabel-laporan td, th{
                padding-left: 10px;
                padding-right: 5px;
            }
            .tabel-laporan .number{
                text-align: center;
            }

            .tabel-laporan th rowspan, td rowspan{
                vertical-align: middle;
            }
            .letter_layout{
                border-style: solid;
                border-width: 2px;
                padding: 15px;
            }
        </style>
        <script type="text/javascript">
            function printit() {
                window.print();
                setTimeout(function(){ window.close();},300);
            }
        </script>
    </head> 
    <body onload="printit()">
        <div style="padding: 10px">
            <table width="100%" style="color: #000; border-bottom: 1px solid #000;">
                <tr>
                    <td rowspan="3" style="width: 70px"><img src="<?= base_url('assets/images/company/' . $apt->logo_file_nama) ?>" width="70px" height="70px" /></td>    
                    <td colspan="3" align="center"><b><?= strtoupper($apt->nama) ?></b></td> <td rowspan="3" style="width: 70px">&nbsp;</td>
                </tr>
                <tr><td colspan="3" align="center"><b><?= strtoupper($apt->alamat) ?> <?= strtoupper($apt->kelurahan) ?></b></td> </tr>
                <tr><td colspan="3" align="center"><b>TELP. <?= $apt->telp ?>,  FAX. <?= $apt->fax ?>, EMAIL <?= $apt->email ?></b></td> </tr>
            </table>

            <br/>
            <?php $A = ''; ?>

            <table class="tabel-laporan" style="width:100%" border="1" cellspacing="0" cellpadding="0">
                <tr style="font-size:20px">
                    <td width="50%" style="text-align:center;">PERSETUJUAN TINDAKAN KEDOKTERAN</td>
                    <td>NO. RM : <?= $detail->no_rm ?><br/>NAMA:<?= $detail->pasien ?></td>
                </tr>
            </table>
            <br/>
            <div class="letter_layout">
                
        <p>Yang bertandatangan di bawah ini :</p>
        <table width="100%">
            <tr><td width="20%">Nama:</td><td><?= $detail->nama_pjwb ?></td></tr>
            <tr><td>Umur:</td><td>.......................</td></tr>
            <tr><td>Alamat:</td><td><?= $detail->alamat_pjwb ?></td></tr>
        </table>
        <p>Dengan ini menyatakan sesungguhnya telah memberikan persetujuan saya untuk dilakukan tindakan [nama.layanan] terhadap
    diri saya sendiri/istri/anak berikut :</p>
        <table width="100%">
            <tr><td width="20%">Nama:</td><td width="80%"><?= $detail->pasien ?></td></tr>
            <tr><td>Umur:</td><td><?= hitungUmur($detail->lahir_tanggal) ?></td></tr>
            <tr><td>Alamat:</td><td><?= $detail->alamat ?></td></tr>
            <tr><td>Bangsal/Ruang:</td><td><?= $detail->unit ?></td></tr>
        </table>
    <p>yang sifat tujuannya serta kemungkinan akibatnya telah dijelaskan sepenuhnya oleh dokter dan telah saya mengerti
        seluruhnya.</p>

    <p>Saya juga menyatakan telah memberikan persetujuan saya atas dilakukan tindakan kedokteran lebih lanjut apabila pada waktu
    pembedahan berlangsung ternyata diperlukannya demi keselamatan jiwa. Juga saya menyetujui pemberian pembiusan umum
    atau lokal untuk dapat dilakukannya tindakan/bedah ini.</p>

    <p style="float:right;margin-bottom:6em"><?= $apt->kabupaten ?>, <?= indo_tgl(date("Y-m-d")) ?></p>
    
    <table width="100%" style="margin-top: 50px;" align="center">
        <tr>
            <td width="30%" style="border-bottom: 1px dotted #000;"></td>
            <td></td>
            <td width="30%" style="border-bottom: 1px dotted #000;"></td>
            <td></td>
            <td width="30%" align="center" style="border-bottom: 1px solid #000;"><?= $detail->nama_pjwb ?></td>
        </tr>
        <tr>
            <td width="30%" align="center">SAKSI I </td>
            <td></td>
            <td width="30%" align="center">SAKSI II</td>
            <td></td>   
            <td width="30%" align="center">yang memberikan pernyataan</td>
        </tr>
    </table>

    <p>Saya menyatakan bahwa saya telah menjelaskan sifat dan tujuan serta kemungkinan akibat yang timbul dari tindakan operasi
     dan pembiusan ini kepada pasien sendiri/istri/anak</p>

     <table width="100%" style="margin-top: 6em">
        <tr>
            <td width="50%" align="center">
                <div style="width:70%;border-bottom: 1px solid #000;"><?= (isset($tindakan->anestesi))?$tindakan->anestesi:$_GET['anestesi'] ?></div>
            </td>
            <td width="50%" align="center">
                <div style="width:70%;border-bottom: 1px solid #000;"><?= (isset($tindakan->operator))?$tindakan->operator:$_GET['operator'] ?></div>
            </td>
        </tr>
         <tr>
            <td width="50%" align="center">Dokter Anesthesi</td><td width="50%" align="center">Dokter Operator</td>
        </tr>
     </table>
            </div>


        </div>
    </body>
</html>