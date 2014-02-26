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
                    <td width="50%" style="text-align:center;">PERSETUJUAN RAWAT INAP</td>
                    <td>NO. RM : <?= $detail->no_rm ?><br/>NAMA:<?= $detail->pasien ?></td>
                </tr>
            </table>
            <br/>
            <div class="letter_layout">
                
        <p>Saya yang bertandatangan di bawah ini :</p>
        <table width="100%" style="margin-left:10%">
            <tr><td width="20%">Nama:</td><td><?= $detail->nama_pjwb ?></td></tr>
            <tr><td>Umur:</td><td>.......................</td></tr>
            <tr><td>Alamat:</td><td><?= $detail->alamat_pjwb ?></td></tr>
        </table>
        <p>Dalam hal ini bertindak sebagai diri sendiri/suami/istri/orang tua/anak atas nama pasien yang sedang dirawat dengan keterangan:</p>
        <table width="100%" style="margin-left:10%">
            <tr><td width="20%">Yang bernama:</td><td width="80%"><?= $detail->pasien ?></td></tr>
            <tr><td>Umur:</td><td><?= hitungUmur($detail->lahir_tanggal) ?></td></tr>
            <tr><td>Alamat:</td><td><?= $detail->alamat ?></td></tr>
            <tr><td>Bangsal/Ruang:</td><td><?= $detail->unit ?> / <?= $detail->kelas ?></td></tr>
        </table>
    <p>Dengan ini menyatakan dengan sesungguhnya bahwa saya menyetujui jika pasien tersebut dirawat di <?= $apt->nama ?> dengan menggunakan hak pelayanan Cara Pembayaran:.</p>
    <div width="100%" style="margin-left:7%">
            <?php if($detail->produk_asuransi !== null){?>
            <ol>
                <li>
                    <p><?= $detail->produk_asuransi ?><p>
                </li>
            </ol>
        <?php }else{ ?>
           <ol>
                <li>
                    <p>............................................<p>
                </li>
            </ol>
        <?php } ?>

    </div>

    <p>Demi kelancaran pelayanan perawatan, pengobatan dan administrasi dengan ini menyatakan:</p>
    <div width="100%" style="margin-left:7%">
        <ul>
            <li>
                <p>Setuju dan memberi izin kepada dokter/perawat yang bersangkutan untuk merawat, mengobati dan atau melakukan
                 prosedur diagnostik yang dianggap penting dan perlu pada pasien tersebut di atas.</p>
            </li>
            <li>
                <p>Sanggup/bersedia membayar seluruh biaya perawatan dan atau mengurus administrasi sesuai dengan aturan yang
                    berlaku dan <b>jika dalam perawatan akan berganti Cara Pembayaran sanggup menyelesaikan pembayaran sebelumnya.</b></p>
            </li>
            <li>
                <p>Setuju dan bersedia mentaati segala peraturan yang berlaku di Rumah Sakit</p>
            </li>
            <li>
                <p>Memberi kuasa kepada dokter untuk memberi keterangan yang diperlukan oleh pihak penanggungjawab biaya
                    perawatan pasien tersebut di atas.</p>
            </li>
        </ul>            
    </div>
    <br/>
    <p>Demikian pernyataan saya buat dengan sepenuh kesadaran dan tanpa ada paksaan dari pihak manapun, dengan demikian saya bersedia menanggung beban
     resiko dari tindakan perawatan, pengobatan dan atau sesuai prosedur diagnostik</p>

    <p style="float:right;margin-bottom:6em"><?= $apt->kabupaten ?>, <?= indo_tgl(date("Y-m-d")) ?></p>
    <table width="100%" style="margin-top: 50px;" align="center">
        <tr>
            <td width="50%" align="center">
                 <div style="width:70%;border-bottom: 1px solid #000;"><?= $detail->nama_dpjp ?></div>
            </td>
            <td></td>
            <td width="50%" align="center">
                 <div style="width:70%;border-bottom: 1px solid #000;">&nbsp;</div>
            </td>
        </tr>
        <tr>
            <td width="50%" align="center">Petugas R.S.</td>
            <td></td>  
            <td width="50%" align="center">Yang Membuat Pernyataan</td>
        </tr>
    </table>


     
    </div>


        </div>
    </body>
</html>