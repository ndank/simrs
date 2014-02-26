<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv='expires' content='-1' />
        <meta http-equiv='pragma' content='no-cache' />
        <link rel="shortcut icon" href="<?= base_url('assets/images/fav.png') ?>" />
        <link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/base.css') ?>" media="screen" /> 
        <link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/jquery-ui-1.9.2.custom.css') ?>" media="all" />
        <link rel="stylesheet" type="text/css" href="<?= base_url('assets/js/jquery-ui.css') ?>" media="all" />
        <link rel="stylesheet" type="text/css" href="<?= base_url('assets/js/styles.css') ?>" media="all" />
        <link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/jquery.autocomplete.css') ?>" media="all" />
        <link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/jquery.treetable.css') ?>" media="all" />
        <link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/jquery.treetable.theme.default.css') ?>" media="all" />
        
        <script type="text/javascript" src="<?= base_url('assets/js/jquery-1.8.3.js') ?>"></script>
        <script type="text/javascript" src="<?= base_url('assets/js/jquery-ui-1.9.2.custom.js') ?>"></script>
        <script type="text/javascript" src="<?= base_url('assets/js/jquery-ui-timepicker-addon.js') ?>"></script>
        <script type="text/javascript" src="<?= base_url('assets/js/jquery-print.min.js') ?>"></script>
        <script type="text/javascript" src="<?= base_url('assets/js/jquery.form.js') ?>"></script>
        
        <script type="text/javascript" src="<?= base_url('assets/js/colResizable-1.3.min.js') ?>"></script>
        <script type="text/javascript" src="<?= base_url('assets/js/jquery.tablesorter.js') ?>"></script>
        <script type="text/javascript" src="<?= base_url('assets/js/jquery.autocomplete.js') ?>"></script>
        <script type="text/javascript" src="<?= base_url('assets/js/library.js') ?>"></script>
        <script type="text/javascript" src="<?= base_url('assets/js/workspace.js') ?>"></script>
        <script type="text/javascript" src="<?= base_url('assets/js/jquery.watermark.js') ?>"></script>
        <script type="text/javascript" src="<?= base_url('assets/js/jquery.treetable.js') ?>"></script>
        <script type="text/javascript" src="<?= base_url('assets/js/jquery.contextmenu.js') ?>"></script>
        <script type="text/javascript" src="<?= base_url('assets/highchart/highcharts.js') ?>"></script>
        <script type="text/javascript" src="<?= base_url('assets/highchart/themes/grid.js') ?>"></script>
        <script type="text/javascript" src="<?= base_url('assets/highchart/modules/exporting.js') ?>"></script>
        <script type="text/javascript" src="<?= base_url('assets/js/mousetrap.min.js') ?>"></script>
        <script type="text/javascript" src="<?= base_url('assets/js/jquery.nicescroll.js') ?>"></script>
        <script type="text/javascript" src="<?= base_url('assets/js/jquery.cookies.js') ?>"></script>

        <script type="text/javascript">
            
            function ganti_pwd() {
                $('.logoutbutton').toggle();
                $.ajax({
                    type : 'GET',
                    url: '<?= base_url('referensi/ganti_password') ?>',
                    cache: false,
                    success: function(data) {
                        $('#loaddata').html(data);
                    }
                });
            }
            function load_menu(val){
                $.cookie('url', val);
                $.ajax({
                    url: val,
                    cache: false
                }).done(function( data ) {
                    $('#loaddata').html(data);
                });
                return false;
            }
            $(function() {
                $('.home').click(function() {
                    $.cookie('url',null);
                });
                if ($.cookie('url') !== null) {
                    load_menu($.cookie('url'));
                }
                $(window).bind("load", function() {
                    $(".menu-detail").niceScroll({touchbehavior:true,cursorcolor:"#666",cursoropacitymax:1,cursorwidth:0,cursorborder:"1px solid #919295",cursorborderradius:"0px",background:"#ccc",autohidemode:"scroll"}).cursor.css({"background":"#ccc"}); 
                });
                
                $(document).ready(function(){
                    
                    $('#cssmenu > ul > li ul').each(function(index, e){
                        var count = $(e).find('li').length;
                        var content = '<span class="cnt">' + count + '</span>';
                        $(e).closest('li').children('a').append(content);
                    });
                    $('#cssmenu ul ul li:odd').addClass('even');
                    $('#cssmenu ul ul li:even').addClass('even');
                    $('#cssmenu > ul > li > a').click(function() {
                        
                        $('#cssmenu li').removeClass('active');
                        $(this).closest('li').addClass('active');	
                        var checkElement = $(this).next();
                        if((checkElement.is('ul')) && (checkElement.is(':visible'))) { // tutup
                            $(this).closest('li').removeClass('active');
                            checkElement.slideUp('normal');
                            //$.cookie('status', 'close');
                        }
                        if((checkElement.is('ul')) && (!checkElement.is(':visible'))) { // buka
                            $('#cssmenu ul ul:visible').slideUp('normal');
                            checkElement.slideDown('normal');
                            //$.cookie('status', 'open');
                            //alert('you');
                        }
                        if($(this).closest('li').find('ul').children().length === 0) {
                            return true;
                        } else {
                            return false;	
                        }		
                    });

                });
                
                
                $('.fixed').fadeOut(15000);
                $('#hide').click(function() {
                    $('#hide').hide();
                    $('#show').show();
                    $(".menu-detail").hide("slide", { direction: "left" }, 500);
                    $('#loaddata').css('width','100%');
                });
                $('#show').click(function() {
                    $('#show').hide();
                    $('#hide').show();
                    $(".menu-detail").show("slide", { direction: "left" }, 500);
                    $('#loaddata').css('width','80%');
                });
            });

        </script>
    </head>
        <body>
            <?= form_hidden('menu_url') ?>
            <?= form_hidden('menu_name') ?>
            <div style="height: 100%">
                <div id="mainribbon-min" class="shadow-box">
                    <div class="logo-apotek">&nbsp;</div>
                    <div class="info-user">
                        <img src="<?= base_url('assets/images/user-aktif.png') ?>" align="left" /> 
                        <div class="welcome">
                            Welcome, <b><?= $this->session->userdata('nama') ?></b>
                        </div>
                        <div>
                            <?= anchor('user/logout', 'Logout') ?>
                        </div>
                    </div>
                </div>
                <div class="menu-detail">
                    
                    <div class="space"></div>
                    <div id='cssmenu'>
                    <ul>
                        <li class='active home'><a href='<?= base_url() ?>'><span>Home</span><div class="small">INFORMASI UMUM</div></a></li>
                    </ul>
                    <?php foreach ($master_menu as $key => $menu) { ?>
                    <ul>
                        <li class='has-sub'><a href='#' id="<?= $key ?>"><span><?= $menu->nama ?><br/></span><div class="small"><?= $menu->keterangan ?></div></a>
                          <ul>
                                <?php $detail = $this->m_user->menu_user_load_data($this->session->userdata('id_user'), $menu->id)->result(); 
                                foreach ($detail as $rows) { ?>
                                <li><a class="submenu" onclick="load_menu('<?= base_url($rows->url) ?>'); return false;" href='<?= base_url($rows->url) ?>'><span><?= $rows->form_nama ?></span></a></li>
                                <?php } ?>
                          </ul>
                       </li>
                    </ul>
                    <?php } ?>
                    </div>
                </div>
                <!--            <div class="arrow-left">&nbsp;</div>
                            <div class="arrow-right">&nbsp;</div>-->

                <div id="loading"></div>
                <div id="loaddata">
                    <?php $this->load->view('registrasi') ?>
                </div>
                
            </div>
        </body>
</html>
<noscript><div class="windowsjavascript"><div>Maaf Javascript pada browser anda tidak aktif.<br/>mohon aktifkan untuk menggunakan sistem ini.</div></div></noscript>