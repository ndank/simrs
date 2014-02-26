<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
    <head>
        <title><?= $title ?></title>
      
        <style>
            
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

            .tab1{
                margin-left:10px
            }

            .tab2{
                margin-left:20px
            }

            .tab3{
                margin-left:30px
            }
        </style>
        <script type="text/javascript">
            function cetak() {
                setTimeout(function(){ window.close();},300);
                window.print();    
            }
        </script>
    </head> 
    <body onload="cetak()">
        <?php
            // header_excel("RL 3.3 Kegiatan RS tahun".$tahun.".xls"); 
        ?>
        <div style="padding: 10px">
            <table width="100%" style="color: #000; border-bottom: 4px solid #000;">
                <tr>
                    <td rowspan="3" style="width: 70px"><img src="<?= base_url('assets/images/company/bakti-husada.png') ?>" width="70px" height="95px" /></td>    
                    <td rowspan="3"><h3><b>Formulir RL 3.3<br/><?= $title?></b></h3></td>
                    <td rowspan="3" style="text-align: right; "><i>Ditjen Bina Upaya Kesahatan<br/>Kementrian Kesahatan RI</i></td>
                </tr>
            </table>

            <br/>
            <?php 

            $A = ''; 
            $th = explode("-", $rs->waktu);
            ?>
            <table class="tabel-laporan">
                <tr>
                    <td><b>Kode R.S.</b></td><td> : </td><td><b><?= $rs->kode_rs?></b></td>
                </tr>
                 <tr>
                    <td><b>Nama R.S.</b></td><td> : </td><td><b><?= $rs->nama?></b></td>
                </tr>
                 <tr>
                    <td><b>Tahun</b></td><td> : </td><td><b><?= $tahun?></b></td>
                </tr>
            </table>
            <br/><br/>

            <table border="1" cellspacing="0" cellpadding="0" class="tabel-laporan" width="100%">
                <tr>
                    <th>No.</th>
                    <th>Nama Layanan</th>
                    <th width="10%">Jumlah</th>
                </tr>
                <?php 
                    $j = 0; $k = 0; $l = 0; $m = 0;
                    $id_jenis = 0; $id_sub = 0; $id_sub_sub = 0; $id_layanan= 0;
                    foreach ($laporan as $key => $value): 
                ?>
                    <tr>
                        <td>
                            <?php
                                if($id_jenis != $value['id_jenis']){
                                    $id_jenis = $value['id_jenis'];
                                    echo ++$j;
                                    $k = 0;
                                }else{
                                    echo $j;
                                }
                            ?>
                        </td>
                        <td><?= $value['jenis'] ?></td>
                        <td></td>
                    </tr>

                    <tr>
                        <td>
                            <?php
                                if($id_sub != $value['id_sub']){
                                    $id_sub = $value['id_sub'];
                                    echo $j.".".++$k;
                                    $l = 0;
                                }else{
                                    echo $j.".".$k;
                                }
                            ?>
                        </td>
                        <td><span class="tab1"><?= $value['sub'] ?></span></td>
                        <td></td>
                    </tr>

                    <tr>
                        <td>
                            <?php
                                if($id_sub_sub != $value['id_sub_sub']){
                                    $id_sub_sub = $value['id_sub_sub'];
                                    echo $j.".".$k.".".++$l;
                                    $m = 0;
                                }else{
                                    echo $j.".".$k.".".$l;
                                }
                            ?>
                        </td>
                        <td><span class="tab2"><?= $value['sub_sub'] ?></span></td>
                        <td></td>
                    </tr>

                    <?php foreach ($value['layanan'] as $key2 => $val): ?>
                         <tr>
                            <td>
                                <?php
                                if($id_layanan != $val->id){
                                    $id_layanan = $val->id;
                                     echo $j.".".$k.".".$l.".".++$m;
                                }else{
                                    echo $j.".".$k.".".$l.".".$m;
                                }
                            ?>
                            </td>
                            <td><span class="tab3"><?= $val->nama_layanan ?></span></td>
                            <td align="center"><?= $val->jumlah ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="3"><br/></td>
                    </tr>

                <?php endforeach; ?>
            </table>
            
        </div>
    </body>
</html>