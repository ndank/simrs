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
                    <td width="50%" style="text-align:center;">PENGAMBILAN KEPUTUSAN TINDAKAN MEDIS PASIEN TIDAK SADAR DAN TANPA PENGANTAR KELUARGA</td>
                    <td>NO. RM : <?= $detail->no_rm ?><br/>NAMA:<?= $detail->pasien ?></td>
                </tr>
            </table>
            <br/>
            <div class="letter_layout">
                
        <p>Dengan memperhatikan semua tanda dan gejala yang dijumpai pada saat ini atas semua pasien dengan data sbb:</p>
        <table width="100%">
            <tr><td width="20%">Nama:</td><td><?= $detail->nama_pjwb ?></td></tr>
            <tr><td>Umur:</td><td>.......................</td></tr>
            <tr><td>Alamat:</td><td><?= $detail->alamat_pjwb ?></td></tr>
        </table>
        <p>Serta setelah melalui pertimbangan ilmu kedokteran, etik profesi kedokteran dan mengingat sumpah dokter, kami menyatakan dengan sesungguhnya pada hari/tanggal <?= indo_tgl(date("Y-m-d")) ?>:</p>
        <ol>
            <li>Bahwa pasien dalam keadaan gawat darurat yang perlu memperoleh tindakan medis segera untuk upaya penyelamatan jiwa dimana penundaan tindak medis akan membahayakan pasien.</li>
            <li>Bahwa pasien dalam keadaan tidak sadar dan tidak mampu menerima penjelasan tentang keadaan medis yang dihadapi ataupun untuk memutuskan sesuatu terhadap dirinya.</li>
            <li>Bahwa pasien tidak disertai pendamping yang mempunyai pertalian urusan keluarga terdekat yang berwenang memberi persetujuan/ijin tindak medis atas dirinya.</li>
        </ol>
    

    <p>Maka diputuskan untuk melakukan segala tindak medis yang dianggap perlu tanpa menunggu persetujuan pasien mengingat hal tersebut diatas (butir 1,2,3) seperti ketentuan dalam standar profesi.<br/>
    Pernyataan ini dibuat untuk melengkapi dokumen medis dan sebagai bukti kesungguhan kami menunaikan tugas melakukan upaya terbaik dalam pertolongan pasien dengan:
    </p>
        
    <table width="100%" style="margin-bottom:6em">
        <tr><td width="20%">Diagnostik Medis:</td><td></td></tr>
        <tr><td>Tindakan Medis</td><td></td></tr>
    </table>

    <p style="float:right;margin-bottom:6em"><?= $apt->kabupaten ?>, <?= indo_tgl(date("Y-m-d")) ?></p>
    <table width="100%" style="margin-top: 50px;" align="center">
        <tr>
            <td width="50%" align="center">
                 <div style="width:70%;border-bottom: 1px solid #000;">&nbsp;</div>
            </td>
            <td width="50%" align="center">
                 <div style="width:70%;border-bottom: 1px solid #000;"><?= $detail->nama_pjwb ?></div>
             </td>
        </tr>
        <tr>
            <td width="50%" align="center">Mengetahui</td>
            <td width="50%" align="center">yang memberikan pernyataan</td>
        </tr>
    </table>
     <table width="100%" style="margin-top: 6em">
         <tr>
            <td width="50%" align="center">
                 <div style="width:70%;border-bottom: 1px solid #000;"><?= $detail->nama_dpjp  ?></div>
            </td>
            <td width="50%" align="center">
                 <div style="width:70%;border-bottom: 1px solid #000;"><?= (isset($tindakan->operator))?$tindakan->operator:$_GET['operator'] ?></div>
            </td>
        </tr>
         <tr><td width="50%" align="center">Petugas Rumah Sakit</td><td width="50%" align="center">Dokter Operator</td></tr>
     </table>
    </div>


        </div>
    </body>
</html>