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
    <body onload="printit()" >
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
                    <td width="50%" style="text-align:center;">PERSETUJUAN TINDAKAN STERILISASI</td>
                    <td>NO. RM : <?= $detail->no_rm ?><br/>NAMA:<?= $detail->pasien ?></td>
                </tr>
            </table>
            <br/>
            <div class="letter_layout">
                
        <p>Kami yang bertandatangan dibawah ini:</p>
        <table width="100%">
            <tr><td width="20%">Nama:</td><td><?= $detail->nama_pjwb ?></td></tr>
            <tr><td>Umur:</td><td>.......................</td></tr>
            <tr><td>Alamat:</td><td><?= $detail->alamat_pjwb ?></td></tr>
        </table>
        <p>Dengan ini kami menyatakan sesungguhnya bahwa kami telah mendapat penjelasan dan mengerti sepenuhnya hal yang berkaitan
            dengan alat kontrasepsi serta setelah kami menyepakati berdua (suami-istri), bersama ini kami menyatakan memberikan 
            persetujuan untuk diberi tindakan [nama.layanan]:</p>
    

    <p>Yang sifat dan tujuan tindakan sterilisasi serta kemungkinan akibat-akibatnya telah dijelaskan sepenuhnya oleh dokter dan telah
    saya mengerti seluruhnya.</p>

    <p style="float:right;margin-bottom:5em"><?= $apt->kabupaten ?>, <?= indo_tgl(date("Y-m-d")) ?></p>
    <table width="100%"  align="center">
        <tr>
            <td width="30%" style="border-bottom: 1px dotted #000;"></td>
            <td></td>
            <td width="30%" align="center" style="border-bottom: 1px solid #000;"><?= $detail->nama_pjwb ?></td>
        </tr>
        <tr>
            <td width="30%" align="center">Nama Jelas</td>
            <td></td>
            <td width="30%" align="center">Nama Jelas</td>
        </tr>
    </table>
    <p>
        Saya menyatakan bahwa saya telah menambahkan sifat dan tujuan serta kemungkinan akibat yang timbul dari penghentian tindakan ...............
        /perawatan ini kepada yang menandatangani surat pernyataan diatas.
    </p>
    <table width="100%" style="margin-top: 4em;" align="center">
        <tr>
            <td width="30%"></td>
            <td>&nbsp;</td>
            <td width="30%" align="center" style="border-bottom: 1px solid #000;"></td>
        </tr>
        <tr>
            <td width="30%" align="center">&nbsp;</td>
            <td>&nbsp;</td>
            <td width="30%" align="center"><?= $detail->nama_pjwb ?></td>
        </tr>
    </table>
    </div>
        </div>
    </body>
</html>