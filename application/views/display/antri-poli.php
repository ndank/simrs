<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv='cache-control' content='no-cache'>
        <meta http-equiv='expires' content='0'>
        <meta http-equiv='pragma' content='no-cache'>
        <link rel="shortcut icon" href="<?= base_url('assets/images/fav.ico') ?>" />
        <link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/workspace.css') ?>" media="all" />
        <link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/clock.css') ?>" media="all" />
        <script type="text/javascript" src="<?= base_url('assets/js/jquery-1.8.3.js') ?>"></script>
        <link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/jquery-ui-1.9.2.custom.css') ?>" media="all" />
  
        <style type="text/css">
            body{
                background-color: #000000;
            }

            .dg-table{
                color: white;
                margin: 20px;
                padding: 10px;
            }

            .dg-table th{
                font-size: 30px;
            }

             .dg-table td{
                font-size: 20px;
                border-bottom-style: solid;
                border-width:1px;
                border-color: white;
                padding: 5px;
            }


        </style>
       <title><?= $title ?></title>
       <script type="text/javascript">
            $(function() {
            reload();
            var monthNames = [ "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember" ]; 
            var dayNames= ["Minggu","Senin","Selasa","Rabu","Kamis","Jumat","Sabtu"]

            var newDate = new Date();
            newDate.setDate(newDate.getDate());  
            $('#Date').html(dayNames[newDate.getDay()] + ", " + newDate.getDate() + ' ' + monthNames[newDate.getMonth()] + ' ' + newDate.getFullYear());

            setInterval( function() {                
                var seconds = new Date().getSeconds();                
                $("#sec").html(( seconds < 10 ? "0" : "" ) + seconds);
                },1000);
                
            setInterval( function() {                
                var minutes = new Date().getMinutes();                
                $("#min").html(( minutes < 10 ? "0" : "" ) + minutes);
                },1000);
                
            setInterval( function() {                
                var hours = new Date().getHours();                
                $("#hours").html(( hours < 10 ? "0" : "" ) + hours);
                }, 1000); 

             setInterval(function(){
               reload();
             } , 10000); 

            });

        function reload(){
            $.ajax({
                url: '<?= base_url('display/reload_antrian/') ?>/',
                cache: false,
                success: function(msg) {
                    $('#antrian').html(msg);                       
                }
            });
        }

            
        </script>
    </head>
        <body>
            

             
                 <div class="clock">
                    <div id="judul"><?= $title ?></div>
                    <div id="Date"></div>
                    <ul>
                      <li id="hours"></li>
                      <li id="point">:</li>
                      <li id="min"></li>
                      <li id="point">:</li>
                      <li id="sec"></li>
                    </ul>
                </div>
                <br/>
                <div id="antrian"></div>
                <marquee>Selamat Datang</marquee>
        </body>
</html>
<noscript><div class="windowsjavascript"><div>Maaf Javascript pada browser anda tidak aktif.<br/>mohon aktifkan untuk menggunakan sistem ini.</div></div></noscript>